<?php
class Model
{
	public function __construct($item)
	{
		foreach ($this as $key => $value) {
			$this->$key = isset($item->$key) ? $item->$key : null;
		}
	}
}
