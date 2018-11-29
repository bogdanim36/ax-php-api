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
		$this->where->items[] = array("type" => "and", "field" => $field, "operator" => "=", "value" => $value);
		return $this->where;
	}

	public function like($field, $value)
	{
		$this->where->items[] = array("type" => "and", "field" => $field, "operator" => "like", "value" => $value);
		return $this->where;
	}

	public function in($field, $value)
	{
		$this->where->items[] = array("type" => "or", "field" => $field, "operator" => "in", "value" => $value);
		return $this->where;
	}


}