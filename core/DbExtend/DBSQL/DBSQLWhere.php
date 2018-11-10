<?php
require_once get_path("core/DbExtend/DBSQL", "DBSQLWhereOperator.php");

class DBSQLWhere
{
	protected $_items=[];
	public $and;

	/**
	 * @return mixed
	 */
	public function getItems()
	{
		return $this->_items;
	}
	public function addItem(array $item){
		$this->_items[] = $item;
	}
	public function __construct()
	{
		$this->and = new DBSQLWhereOperator($this);
	}


	public function group($left, $operator, $right): SqlWhere
	{
		$where = new SqlWhere();
		$this->_items[] = $where;
		return $where;
	}

}