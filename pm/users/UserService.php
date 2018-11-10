<?php
require_once get_path("core", "Service.php");

class UserService extends Service
{
	public $tableName = "users";
	public $modelClass = "UserItem";

	public function __construct()
	{
		parent::__construct();
		global $dbConfig;
		$dbAccessClass = "DB" . $dbConfig["driver"] . "Ext";
		require_once get_path("core/DBExtend", $dbAccessClass . ".php");
		$this->db = new $dbAccessClass($dbConfig["host"], $dbConfig["user"], $dbConfig["password"], $dbConfig["dbName"], $this->tableName, $this->tableKey);

	}

	public function getUserByEmailAndPassword($email, $password)
	{
		$cmd = 'SELECT a.* FROM `' . $this->tableName . '` a ';
		$cmd .= "LEFT JOIN `users-pw` b on b.userId = a.id ";
		$cmd .= "WHERE a.email = '{$this->db->escape($email)}' AND b.password = '{$this->db->escape($password)}'";
		$queryResult = $this->db->query($cmd);
		$response = array();
		$response["data"] = $queryResult->rows;
		$response["status"] = true;
		$sql = $this->db->sql();
		$sql->columns->addAll();
		return $response;
	}

	public function savePassword($resetLink, $password)
	{
		$userData = $this->getItemAction(array("resetLink" => $resetLink));
		if (!$userData["status"]) return $userData;
		if (count($userData["data"]) == 0) {
			$userData["status"] = false;
			return $userData;
		}
		try {
			$user = $userData["data"];
			$user["parola"] = $password;
			$cmd = "DELETE FROM `users-pw` WHERE userId={$this->db->escape($user["id"])}";
			$queryResult = $this->db->query($cmd);
			$cmd = "INSERT INTO `users-pw` SET userId={$this->db->escape($user["id"])}, password='{$this->db->escape($password)}'";
			$queryResult = $queryResult && $this->db->query($cmd);
			$cmd = "UPDATE `users` SET resetLink='' WHERE id = {$this->db->escape($user["id"])}";
			$queryResult = $queryResult && $this->db->query($cmd);

			$response = array();
			$response["data"] = $user;
			$response["status"] = $queryResult;
			return $response;
		} catch (Exception $error) {
			return $this->rollback("save-password");
		}

	}

	public function validate(&$data = null)
	{
		$itemName = "Utilizator";
		$item = $this->getDataItem($data);
		if (!isset($item->email)) {
			$exception = new Exception("Email $itemName  este obligatoriu");
			$exception->field = "email";
			throw $exception;
		}
		if (!isset($item->nume)) {
			$exception = new Exception("Nume $itemName  este obligatoriu");
			$exception->field = "nume";
			throw $exception;
		}
		if (!isset($item->prenume)) {
			$exception = new Exception("Prenume $itemName  este obligatoriu");
			$exception->field = "prenume";
			throw $exception;
		}
		return true;
	}

}
