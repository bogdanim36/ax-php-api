<?php

class DBSQLOrder
{

	private $items = [];

	public function __construct()
	{
	}

	public function &__get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		} else throw new \Exception("Property " . $property . " doesn't exist on " . __CLASS__ . " class!");
	}

	public function &__set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		} else throw new \Exception("Property " . $property . " doesn't exist on " . __CLASS__ . " class!");

		return $this;
	}


	public function by($expression, $direction = "asc")
	{
		if (!in_array($direction, ["asc", "desc"])) throw new \Exception("Order direction can by asc or desc");
		$this->items[] = array("by" => $expression, "direction" => $direction);
		return $this;
	}

}