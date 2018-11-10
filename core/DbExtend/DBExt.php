<?php

class DBExt extends DB
{
	public $tableName;
	public $tableKey;
	public $structure;

	public function __construct($driver, $hostname, $username, $password, $database, $tableName, $tableKey = "id")
	{
		parent::__construct($driver, $hostname, $username, $password, $database);

		$this->tableName = $tableName;
		$this->tableKey = $tableKey;
	}

	public function sql()
	{
		return new DBSQL($this);
	}

}