<?php
require_once get_path("core/DBExtend/DBSQL", "DBSQLJoinType.php");

class DBSQLJoin
{
	/**
	 * @var DBSQLJoinType[]
	 */
	private $items = [];

	public function __construct()
	{
		return $this;
	}
	public function __get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		} else throw new \Exception("Property " . $property . " doesn't exist on DBSQLJoinType class!");
	}

	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		} else throw new \Exception("Property " . $property . " doesn't exist on DBSQLJoinType class!");

		return $this;
	}

	private function join($type)
	{
		$item = new DBSQLJoinType($type);
		$this->items[] = $item;
		return $item;

	}


	public function left()
	{
		return $this->join("left");
	}

	public function inner()
	{
		return $this->join("inner");
	}

	public function right()
	{
		return $this->join("right");
	}

}