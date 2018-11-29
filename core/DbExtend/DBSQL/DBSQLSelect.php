<?php
require_once get_path("core/DbExtend/DBSQL", "DBSQL.php");
require_once get_path("core/DbExtend/DBSQL", "DBSQLWhere.php");
require_once get_path("core/DbExtend/DBSQL", "DBSQLColumns.php");
require_once get_path("core/DbExtend/DBSQL", "DBSQLJoin.php");
require_once get_path("core/DbExtend/DBSQL", "DBSQLOrder.php");

class DBSQLSelect extends DBSQL
{
	private $tableName;
	private $tableKey;
	private $tableAlias;
	protected $tablesStructure;
	public $columns;
	public $where;
	public $order ;
	private $distinct = false;
	private $limit;
	protected $tables = [];
	private $service;
	public $join;

	public function __construct($service)
	{
		$this->service = $service;
		$this->tableName = $service->tableName;
		$this->tableAlias = $service->tableAlias;
		$this->tableKey = $service->tableKey;
		$this->where = new DBSQLWhere();
		$this->columns = new DBSQLColumns();
		$this->join = new DBSQLJoin();
		$this->order = new DBSQLOrder();
	}

	public function &__get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		} else throw new \Exception("Property " . $property . " doesn't exist on DBSQL class!");
	}

	public function &__set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		} else throw new \Exception("Property " . $property . " doesn't exist on DBSQL class!");

		return $this;
	}

	public function text()
	{
		try {
			$sql = "SELECT";
			$sql .= $this->getDistinct();
			$sql .= "\n " . $this->getColumns();
			$sql .= "\n " . $this->getFrom();
			$sql .= "\n " . $this->getJoin();
			$sql .= "\n " . $this->getWhere();
			$sql .= "\n " . $this->getOrder();
			return $sql;
		} catch (Exception $err) {
			throw $err;
		}
	}

	public function exec()
	{
		$cmd = $this->text();
		exit($cmd);
		$queryResult = $this->service->db->query($cmd);
		return $queryResult;
	}

	public function setDistinct(): void
	{
		$this->distinct = true;
	}


}





