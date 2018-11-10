<?php

class BaseDb
{
	public $item;
	public $modelItem;
	public $tableName;
	public $tableKey = "id";
	public $db;
	public $postData;
	public $structure;

	public function __construct(DB $db, $postData = "[]")
	{
		$this->db = $db;
		if (isset($postData)) $this->postData = json_decode($postData, false);
		if (isset($this->postData) && isset($this->postData->item)) $this->item = new $this->modelItem($this->postData->item);
	}


	public function getDataItem($data = null)
	{
		return $item = isset($data) ? json_decode(json_encode($data)) : $this->item;
	}

	public function getTableStructure()
	{
		if (!$this->structure) {
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
						AND LOWER(Table_Name) = '%s'", DB_DATABASE, $this->tableName);
			$query = $this->db->query($sql);

			$this->structure = $query->rows;
		}
		return $this->structure;
	}

	protected function getFilterSqlForField($fieldName, $value, $operator)
	{
		return "";
	}


	public function getItemQuery($id)
	{
		$queryResult = $this->db->query($this->sqlGetItem($id));
		return $queryResult->num_rows == 1 ? $queryResult->row : [];
	}

	public function catchError(Exception $error)
	{
		$response = array();
		$field = isset($error->field) ? $error->field : "";
		$response["errors"] = [$field => [$error->getMessage()]];
		$response["status"] = false;
		return $response;
	}

	public function setOkResponse($data)
	{
		$response = array();
		$response["data"] = $data;
		$response["status"] = true;
		return $response;
	}

	public function getLastId($where)
	{
		$cmd = "SELECT a.{$this->tableKey} FROM `" . $this->tableName . "` a ";
		if ($where) $cmd .= $this->sqlBuildWhere($where);
		$cmd .= " order by {$this->tableKey} DESC";
		$result = $this->db->query($cmd);
		if (count($result->row)) return $lastId = $result->row[$this->tableKey];
		else return null;

	}
}