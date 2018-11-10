<?php
require_once get_path("core", "Model.php");

class Service
{
	public $item;
	public $modelClass;
	public $tableName;
	public $tableKey = "id";
	/**
	 * @var DBMySQLiExt
	 */
	public $db;
	public $postData;
	public $childrenItems = null;
	public $children = null;
	public $validateOnWrite = true;

	public function __construct()
	{
	}


	public function validate(&$data = null)
	{
		return true;
	}


	public function getOne($id)
	{
		$where[$this->tableKey] = $id;
		$queryResult = $this->getAll($where);
		return $queryResult->num_rows == 1 ? $queryResult->row : false;
	}

	public function getAll($where, $order = array())
	{
		$sql = $this->db->sql();
		$sql->columns->addAll();
		foreach ($where as $field => $value) {
			if (!is_array($value)) $sql->where->and->equal($field, $value);
			else {
				$operator = $value["operator"];
				if (method_exists($sql->where->and, $operator)) $sql->where->and->$operator($field, $value["value"]);
				else throw new Exception("Operator $operator doesn't exist on where sql class!");
			}
		}
		$queryResult = $sql->exec();
		return $queryResult ->rows;
	}


	protected function getFilterSqlForField($fieldName, $value, $operator)
	{
		return "";
	}

	public function childrenInsert($id)
	{
		$response["status"] = true;
		if (!isset($this->childrenObjects)) return $response;
		foreach ($this->childrenObjects as $className => $data) {
			foreach ($data as $classObj) {
				$classObj->item->{$classObj->foreignKey} = $id;
				$response = $classObj->createAction(true);
				if (!$response["status"]) return $response;
			}
		}
		return $response;
	}

	public function setChildrenItems($childrenClass)
	{
		$postDataName = $this->children[$childrenClass]["postData"];
		$items = $this->postData->extraArgs->children->$postDataName;
		if (!isset($this->childrenItems)) $this->childrenItems = array();
		$this->childrenItems[$childrenClass] = $items;
	}

	public function childrenDelete($id)
	{
		$response["status"] = true;
		if (!$this->children) return $response;
		foreach ($this->children as $class => $child) {
			$classObj = new $class($this->db, json_encode(array()));
			$response = $classObj->getListAction(array($classObj->foreignKey => $id));
			if (!$response["status"]) return $response;
			$items = $response["data"];
			foreach ($items as $item) {
				$itemClassObj = new $class($this->db, json_encode(array("item" => $item)));
				$response = $itemClassObj->deleteAction($item[$itemClassObj->tableKey], true);
				if (!$response["status"]) return $response;
			}
		}
		return $response;
	}

	public function childrenValidate()
	{
		if (!isset($this->children)) return true;
		foreach ($this->children as $key => $value) {
			$this->setChildrenItems($key);
		}

		$this->childrenErrors = null;
		$this->childrenObjects = [];
		$hasErrors = false;
		$errors = [];
		foreach ($this->childrenItems as $class => $data) {
			$childErrors = array();
			$childHasErrors = false;
			$this->childrenObjects[$class] = [];
			foreach ($data as $item) {
				$classObj = new $class($this->db, json_encode(array("item" => $item)));
				$classObj->item->{$classObj->foreignKey} = $this->item->{$this->tableKey};
				$classObj->errors = [];
				$this->childrenObjects[$class][] = $classObj;
				$errItem = !$classObj->validate($item);
				$childErrors[] = $classObj->errors;
				if ($errItem) $childHasErrors = true;
			}

			if ($childHasErrors) {
				$hasErrors = true;
				$errors[$this->children[$class]["postData"]] = $childErrors;
			}
		}
		if ($hasErrors) $this->childrenErrors = $errors;
		return !$hasErrors;
	}

}