<?php

namespace dbWorks\lib;


class DbOpenHelper {


	private $connection = null;

	private $dbHost = "";
	private $dbUser = "";
	private $dbPass = "";
	private $dbName = "";

	private $schemaResource = null;
	private $columns = null;

	private $error = "";


	public function __construct(){

	}


	public function setHost($host){
		$this->dbHost = $host;
		return $this;
	}


	public function setUser($user){
		$this->dbUser = $user;
		return $this;
	}

	public function setPass($pass){
		$this->dbPass = $pass;
		return $this;
	}

	public function setName($name){
		$this->dbName = $name;
		return $this;
	}

	public function getError(){
		return $this->error;
	}

	public function connect(){
		$this->connection = mysqli_connect($this->dbHost,$this->dbUser,$this->dbPass);
	}

	public function connectDb(){
		$this->connection = mysqli_connect($this->dbHost,$this->dbUser,$this->dbPass,$this->dbName);
	}

	public function getTableSchema($dbName = ""){
		if($dbName == ""){
			$dbName = $this->dbName;
		}

		$sql = "select * from information_schema.columns 
		where table_schema = '".$dbName."' 
		order by table_name,ordinal_position";

		$this->schemaResource = mysqli_query($this->connection,$sql);

		$this->columns = mysqli_fetch_all($this->schemaResource,MYSQLI_ASSOC);
		return $this->columns;
	}

	public function getTables(){

		$tables = array();

		foreach($this->columns as $column){

		    if(!array_key_exists($column['TABLE_NAME'],$tables)){
		        $tables[$column['TABLE_NAME']] = array();
		    }
		    $tables[$column['TABLE_NAME']][$column['COLUMN_NAME']] = array($column['COLUMN_NAME'],$column['COLUMN_TYPE'],$column['COLUMN_KEY']);
		}

		return $tables;
	}

	public function getCreateTableDDL($tableName){

		$sql = "Show Create Table ".$tableName;

		$resource = mysqli_query($this->connection,$sql);

		return mysqli_fetch_all($resource,MYSQLI_ASSOC)[0]['Create Table'];
	}


	public function executeQueries(array $queries){
		$this->error = "";	
		if($this->connection != null){
			foreach ($queries as $key => $value) {
				$result = mysqli_query($this->connection,$value);
				if(!$result){
					$this->error = mysqli_error($this->connection)."\n".nl2br($value);
					return false;
				}
				return true;
			}
		}
	}
}