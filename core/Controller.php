<?php

class Controller
{
	public $serviceClass;
	/**
	 * @var Service
	 */
	public $service;
	public $postData;

	public function __construct()
	{
		if (isset($this->serviceClass)) $this->service = new $this->serviceClass();
		$postData = file_get_contents('php://input');
		$data = isset($postData) && $postData != "" ? $postData : (isset($_POST) ? json_encode($_POST) : null);

		$this->postData = json_decode($data, false);
	}

	public function getServerRoot()
	{
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off" ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
	}

	public function action($action)
	{
		$action .= "";
		return $this->$action();
	}

	public function getItem($where = null)
	{
		try {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
			$response = array();
			$response["data"] = $this->service->getItem($id);
			$response["status"] = true;
			return $response;
		} catch (Exception  $error) {
			return $this->catchError($error);
		}

	}

	public function getList($where = null, $order = null, $limit = null)
	{
		try {
			$queryResult = $this->db->query($this->sqlBuildGetAllItems($where, $order, $limit));
			$response = array();
			$response["data"] = $queryResult->rows;
			$response["status"] = true;
			return $response;
		} catch (Exception  $error) {
			return $this->catchError($error);
		}

	}

	public function new()
	{
		$response = array();
		$item = array("id" => 0);
		$object = new $this->modelItem(null);
		foreach ($object as $key => $value) {
			$item[$key] = null;
		}
		$response["data"] = $item;
		$response["status"] = true;
		return $response;
	}


	public function create($fromParent = false, $data = null)
	{
		try {
			$this->db->begin_transaction();

			$response = array();
			$response["status"] = false;
			if (!$fromParent && !$this->validate($data)) {
				$response["errors"] = array("children" => $this->childrenErrors);
				return $response;
			} else $this->validate($data);
			$queryResult = $this->db->query($this->sqlBuildInsert($data));
			if (is_null($data)) $item = $this->item;
			else $item = json_decode(json_encode($data), FALSE);
			$item->id = $this->db->getLastId();
			$response["status"] = $queryResult;
			$children = $this->childrenInsert($item->id);
			if (!$children["status"]) return $children;
			if (method_exists($this, "createActionCallback")) {
				$response = $this->createActionCallback($response, json_encode($this->postData));
				if (!$response["status"]) return $this->rollback("create", null, $response);;
			}
			$response["data"] = $this->getItemQuery($item->id);
			$this->db->commit();
			return $response;
		} catch (Exception  $error) {
			$this->db->rollback();
			return $this->catchError($error);
		}
	}

	public function update($fromParent = false, $data = null)
	{
		try {
			$this->db->begin_transaction();
			$response = array();
			$response["status"] = false;
			if (!$fromParent) {
				if (!$this->validate($data)) {
					if ($this->children) $response["errors"] = array("children" => $this->childrenErrors);
					return $response;
				}
			} else $this->validate($data);

			if (is_null($data)) $item = $this->item;
			else $item = json_decode(json_encode($data), FALSE);
			$deleted = $this->childrenDelete($item->id);
			if (!$deleted["status"]) return $deleted;
			$queryResult = $this->db->query($this->sqlBuildUpdate($data));
			$response["status"] = $queryResult;
			$children = $this->childrenInsert($item->id);
			if (!$children["status"]) return $children;
			if (method_exists($this, "updateActionCallback")) {
				$response = $this->updateActionCallback($response, json_encode($this->postData));
				if (!$response["status"]) return $this->rollback("update", null, $response);;
			}
			$response["data"] = $this->getItemQuery($item->id);
			$this->db->commit();
			return $response;
		} catch (Exception  $error) {
			$this->db->rollback();
			return $this->catchError($error);
		}
	}

	public function delete($id = null, $fromParent = false)
	{
		try {
			$this->db->begin_transaction();
			$id = isset($id) ? $id : (isset($_REQUEST['id']) ? $_REQUEST['id'] : -1);
			$response = array();
			$deleted = $this->childrenDelete($id);
			if (!$deleted["status"]) return $deleted;

			$response["data"] = $this->getItemQuery($id);
			$response["status"] = $this->db->query($this->sqlBuildDeleteItem($id));
			if (method_exists($this, "deleteActionCallback")) {
				$response = $this->deleteActionCallback($response, json_encode($this->postData));
				if (!$response["status"]) return $this->rollback("delete", null, $response);
			}
			$this->db->commit();
			return $response;
		} catch (Exception  $error) {
			return $this->rollback("", $error);
		}
	}

	public function catchError(Exception $error)
	{
		throw $error;
	}

	public function setOkResponse($data)
	{
		$response = array();
		$response["data"] = $data;
		$response["status"] = true;
		return $response;
	}

	/**
	 * @param $class
	 * @param $method
	 * @param $ex {Exception}
	 * @return array
	 */
	public function errorResponse($class, $method, $ex)
	{
		return array("status" => false, "error" => array("class" => $class, "method" => $method, "message" => $ex->getMessage()));
	}

	public function rollback($action, Exception $exception= null, $response = null)
	{
		$this->db->rollback();
		if (method_exists($this, "rollbackCallback")) {
			$this->rollbackCallback($action, $exception, $response);
		}
		if (isset($exception)) throw $exception;
		else if (isset($response)) return $response;
	}

}
