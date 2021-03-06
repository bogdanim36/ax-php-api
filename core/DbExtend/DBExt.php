<?php
require_once get_path('vendor', "DB.php");
require_once get_path('core/DbExtend/DBSQL', "DBSQLSelect.php");


class DBExt extends DB
{
	private $driver;
	private $service;
	public $select;

	public function __construct($driver, $hostname, $username, $password, $database, $service)
	{
		parent::__construct($driver, $hostname, $username, $password, $database);
		$this->driver = $driver;
		$this->service = $service;
		$this->select = new DBSQLSelect($service);
	}

//	public function sql(): DBSQL
//	{
//		$sqlClass = "DBSQL" . $this->driver;
//		require_once get_path("core/DBExtend", $sqlClass . ".php");
//		return new $sqlClass($this->service);
//	}

}