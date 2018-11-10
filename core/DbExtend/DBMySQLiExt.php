<?php
require_once get_path("core/DbExtend", "DBExt.php");
require_once get_path("core/DbExtend", "DBSQLMySQLi.php");

class DBMySQLiExt extends DBExt
{
	public $tableName;
	public $tableAlias;
	public $tableKey;
	public $structure;

	public function __construct($hostname, $username, $password, $database, $tableName, $tableKey = "id")
	{
		parent::__construct("Mysqli", $hostname, $username, $password, $database, $tableName, $tableKey);
	}

	/**
	 * @param $tableName
	 * @param $tableAlias
	 * @param $tableKey
	 * @return DBSQL|DBSQLMySQLi
	 */
	public function sql($tableName=null, $tableKey=null, $tableAlias = null)
	{
		$this->tableName = isset($tableName)?$tableName: $this->tableName;
		$this->tableAlias = isset($tableAlias) ? $tableAlias : $this->tableName;
		$this->tableKey = isset($tableKey)?$tableKey:$this->tableKey;
		return new DBSQLMySQLi($this);
	}

}