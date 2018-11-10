<?php
require_once get_path("core/DbExtend/DBSQL", "DBSQLWhere.php");
require_once get_path("core/DbExtend/DBSQL", "DBSQLColumns.php");

class DBSQL
{
	public $tableName;
	public $tableKey;
	public $tableAlias;
	private $tableStructure;
	private $_sql = "";
	public $columns;
	private $_from;
	private $_join = [];
	private $_group = [];
	public $where;
	private $orderBy = [];
	private $_distinct = false;
	private $limit;
	private $tables = [];
	public $service;

	public function __construct($service)
	{
		$this->service = $service;
		$this->tableName = $service->tableName;
		$this->tableAlias = $service->tableAlias;
		$this->tableKey = $service->tableKey;
		$this->where = new DBSQLWhere();
		$this->columns = new DBSQLColumns();
	}

	public function text()
	{
		$sql = "SELECT";
		if ($this->isDistinct()) $sql .= " DISTINCT";
		$sql .= "\n " . $this->getColumns();
		$sql .= "\n FROM `" . $this->tableName . "` AS " . $this->tableAlias;
		$sql .= "\n " . $this->getWhere();
		return $sql;
	}

	public function exec()
	{
		$cmd = $this->text();
		$queryResult = $this->service->query($cmd);
		return $queryResult;
}

	/**
	 * @param bool $distinct
	 */
	public function setDistinct(bool $distinct): void
	{
		$this->_distinct = $distinct;
	}

	/**
	 * @return bool
	 */
	public function isDistinct(): bool
	{
		return $this->_distinct;
	}


	public function join($joinType, $table, $tableAlias)
	{
		$join = new SqlJoin($joinType, $table, $tableAlias);
		$this->_join[] = $join;
		return $join;
	}

}





