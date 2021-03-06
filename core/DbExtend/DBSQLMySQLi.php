<?php
require_once get_path("core/DbExtend/DBSQL", "DBSQL.php");

class DBSQLMySQLi extends DBSQL
{
	public function __construct($service)
	{
		parent::__construct($service);
	}

	protected function getDistinct()
	{
		return ($this->distinct) ? " DISTINCT" : "";

	}

	protected function getFrom()
	{
		$this->tables[$this->tableAlias] = $this->tableName;
		return "FROM `" . $this->tableName . "` AS " . $this->tableAlias;
	}

	protected function getColumns()
	{
		$columns = $this->columns->getItems();
		$list = "";
		foreach ($columns as $columnName => $expression) {
			$list .= $list == "" ? "" : ", ";
			if ($columnName == "*") $list .= "`" . $this->tableAlias . "`.*";
			else $list .= $expression . " AS " . $columnName;
		}
		return $list;
	}

	protected function getWhere()
	{
		$items = $this->where->items;
		$list = "";
		foreach ($items as $item) {
			$list .= $list == "" ? "" : ("\n " . $item["type"] . " ");
			$field = $item["field"];
			$operator = $item["operator"];
			$value = $item["value"];
			if (!strpos($field, ".")) $field = "`" . $this->tableAlias . "`." . $field;
			$list .= $field;
			$list .= $this->getOperatorFor($operator);
			$list .= $this->getFieldValue($field, $value, $operator);
		}
		$list = "WHERE " . $list;
		return $list;
	}

	protected function getJoin()
	{

		$joins = $this->join->items;
		$list = "";
		foreach ($joins as $join) {
			$list .= ($list == "" ? "" : ("\n ")) . strtoupper($join->type) . " " . "JOIN ";
			$this->tables[$join->tableAlias] = $join->tableName;
			$list .=  "`" . $join->tableName . "` AS " . $join->tableAlias . " ON ";
			$left = $join->onLeft;
			$operator = $join->onOperator;
			$right = $join->onRight;
			$list .= $left;
			$list .= $this->getOperatorFor($operator);
			$list .= $right;
		}
		return $list;

	}

	protected function getOperatorFor($operator)
	{
		return strtoupper($operator);
	}

	protected function getFieldValue($key, $value, $operator)
	{
		$tableAlias = explode('.', $key)[0];
		$table = isset($this->tables[$tableAlias]) ? $this->tables[$tableAlias] : $tableAlias;
		$fieldName = explode('.', $key)[1];
		$structure = $this->getTablesStructure($table);
		$index = array_search(strtolower($fieldName), array_column($structure, 'FieldName'));
		if (!$index) throw new \Exception("Field $fieldName , not found in $table structure");
		$field = $structure[$index];
		$fieldType = $field["ValueType"];
		$fieldNullable = $field["IS_NULLABLE"];
		switch ($fieldType) {
			case "date":
			case "datetime":
				if (is_null($value) && $fieldNullable == 'YES') {
					$line = "NULL";
				} else {
					$value = $this->service->db->escape($value);
					$line = $value == '' ? "" : "'$value'";
				}
				break;
			case "text":
			case "tinytext":
			case "mediumtext":
			case "longtext":
			case "varchar":
			case "char":
				if (is_null($value) && $fieldNullable == 'YES') {
					$line = "NULL";
				} else {
					$value = $this->service->db->escape($value);
					if ($operator == "like") $value = "%$value%";
					$line = "'$value'";
				}
				break;
			case "real":
			case "double":
			case "decimal":
			case "float":
				if (is_null($value)) $line = "NULL";
				else $line = $this->service->db->escape($value);
				break;
			case "smallint":
			case "tinyint":
			case "bigint":
			case "mediumint":
			case "int":
				if (is_null($value) && $fieldNullable == 'YES') {
					$line = "NULL";
				} else {
					if (is_null($value)) $line = "0";
					else $line = (int)$value;
				}
				break;

			default:
				if (!is_null($value)) {
					throw new Exception("DBSQLMySQLi class: unknown data type: " . $fieldType . "<br/>");
				} else $line = "";
				break;
		}
		if ($operator == "in") $line = "(" . $line . ")";
		return $line;
	}

	protected function getOrder()
	{

		$orders = $this->order->items;
		$list = "";
		foreach ($orders as $order) {
			$list .= $list == "" ? "ORDER BY " : ", ";
			$list .= $order["by"] . " " . strtoupper($order["direction"]);
		}
		return $list;

	}

	protected function getTablesStructure($tableName)
	{
		if (!$this->tablesStructure || !isset($this->tablesStructure[$tableName])) {
			$tableNameParts = explode('`', $tableName);
			if (count($tableNameParts) > 1) $tableName = $tableNameParts[1];
			$sql = sprintf("
					SELECT
						lower( Column_name) AS FieldName,
						Column_name AS FieldCaption,
						Data_type AS ValueType,
						Extra AS Extra,
						if( isnull( Character_Maximum_Length),Numeric_Precision, Character_Maximum_Length) AS Length,
						if( isnull(Numeric_Scale), 000, Numeric_Scale) AS DecimalPosition,
						IS_NULLABLE
					FROM information_schema.COLUMNS
					WHERE Table_Schema = '%s'
						AND LOWER(Table_Name) = '%s'", DB_DATABASE, $tableName);
			$query = $this->service->db->query($sql);
			if (count($query->rows) == 0) throw new Exception("Cannot retrive structure for table: $tableName !");
			$this->tablesStructure[$tableName] = $query->rows;
		}
		return $this->tablesStructure[$tableName];
	}

}