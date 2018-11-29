<?php

class DBSQLJoinType
{
	private $type = "inner";
	private $tableName;
	private $tableAlias;
	private $onLeft;
	private $onRight;
	private $onOperator;
	public $where;

	public function __construct($type)
	{
		$this->type = $type;
		$this->where = new DBSQLWhere();
		return $this;
	}

	public function &__get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		} else throw new \Exception("Property " . $property . " doesn't exist on DBSQLJoinType class!");
	}

	public function &__set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		} else throw new \Exception("Property " . $property . " doesn't exist on DBSQLJoinType class!");

		return $this;
	}


	public function table($table, $alias)
	{
		$this->tableName = $table;
		$this->tableAlias = isset($alias) ? $alias : $this->tableName;
		return $this;
	}

	public function on($left, $operator, $right)
	{
		$this->onLeft = $left;
		$this->onOperator = $operator;
		$this->onRight = $right;
		return $this;
	}

}