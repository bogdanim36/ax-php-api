<?php

class DBSQLWhereOperator
{
	/**
	 * @var DBSQLWhere
	 */
	private $where;

	public function __construct($where)
	{
		$this->where = $where;
	}

	public function equal($field, $value)
	{
		$this->where->addItem(array("type" => "and", "field" => $field, "operator" => "=", "value" => $value));
		return $this;
	}

	public function like($field, $value)
	{
		$this->where->addItem(array("type" => "and", "field" => $field, "operator" => "like", "value" => $value));
		return $this;
	}

	public function in($field, $value)
	{
		$this->_where->addItem(array("type" => "or", "field" => $field, "operator" => "in", "value" => $value));
		return $this;
	}


}