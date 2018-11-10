<?php

class DBSQLJoin
{
	private $joinType;
	private $table;
	private $alias;
	private $onLeft;
	private $onRight;
	private $onOperator;
	private $_where = [];

	public function __construct($joinType, $table, $alias)
	{
		$this->joinType = $joinType;
		$this->table = $table;
		$this->alias = $alias;
	}

	public function on($left, $operator, $right)
	{
		$this->onLeft = $left;
		$this->onOperator = $operator;
		$this->onRight = $right;
	}

}