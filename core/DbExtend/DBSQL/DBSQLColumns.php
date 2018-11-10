<?php

class DBSQLColumns
{

	private $_items = [];

	public function __construct()
	{
	}

	public function addAll()
	{
		$this->_items["*"] = "";
		return $this;
	}

	public function addOne($expression, $name)
	{
		$this->_items[$name] = $expression;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getItems(): array
	{
		return $this->_items;
	}

}