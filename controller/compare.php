<?php
/**
 * Created by yogesh on 18 01, 2018.
 *
 */
include __DIR__."/../app.php";

use dbWorks\lib\DbOpenHelper;

$dbHelper = new DbOpenHelper();

$database1 = "dd_2017_robertkime_db";
$database2 = "dd_2018_robertkime_website_backup";

/*
 * First database
 */
$dbHelper->setHost("localhost")
        ->setUser("root")
        ->setPass("root")
        ->setName($database1)
        ->connect();

$firstDatabaseColumns = $dbHelper->getTableSchema();

$firstTables = $dbHelper->getTables();

/*
 * Second database
 */
$dbHelper2 = new DbOpenHelper();
$dbHelper2->setHost("localhost")
        ->setUser("root")
        ->setPass("root")
        ->setName($database2)
        ->connect();

$secondDatabaseColumns = $dbHelper2->getTableSchema();
$secondTables = $dbHelper2->getTables();

/*
 * Start the database comparision
 */
$skippedTables = array();
$tableDifferences = array();

$columnChangeCount = 0;

$dbHelper->connectDb();

//compare the second table based on the first table
foreach ($firstTables as $tableName => $columns) {
	if(strpos($tableName,"temp") > 0 || strpos($tableName,"-") > 0 || strpos($tableName,"tmp") > 0){
		//skipped table
		$skippedTables[] = $tableName;
	}
	else{
		//check if the second database has the table
		if(array_key_exists($tableName,$secondTables)){

			$secondTableColumns = $secondTables[$tableName];
			foreach ($columns as $columnName => $value) {
				if(array_key_exists($columnName,$secondTableColumns)){
					//field is present, check for the column types	
					if($value[1] != $secondTableColumns[$columnName][1]){
						$columnChangeCount++;
						$value['decsp'] = "Altered";
						$tableDifferences[$tableName][$columnName] = $value;
						$tableDifferences[$tableName]['descp'] = "Altered table";
					}
				}
				else{
					//field is not present
					$value['decsp'] = "New";
					$tableDifferences[$tableName][$columnName] = $value;
					$tableDifferences[$tableName]['descp'] = "Altered table";
				}
			}

			if(array_key_exists($tableName,$tableDifferences)){
				$tableDifferences[$tableName]['ddl'] = createAlterDDL($tableName,$tableDifferences[$tableName]);		
			}

		}else{
			//table doesn't exist
			$tableDifferences[$tableName] = $columns; 	
			$tableDifferences[$tableName]['descp'] = "New table";
			$tableDifferences[$tableName]['ddl'] = $dbHelper->getCreateTableDDL($tableName);
		}
	}
}


$message = "";
if(count($tableDifferences) > 0){
	$message = count($tableDifferences)." tables, ".$columnChangeCount." columns differences found.";
}else{
	$message = "No table differences were found between the provided databases.";
}


include __DIR__."/../views/compare.php";


function createAlterDDL($tableName, $columns, $requireDrop = false){

	$dropQuery  = "ALTER TABLE `$tableName` \n";
	$alterQuery = "ALTER TABLE `$tableName` \n";

	foreach ($columns as $key => $value) {

		if(is_array($value)){

			$default = " DEFAULT NULL ";

			if(strpos($value[1],"int") > -1){
				$default = "DEFAULT 0 ";
			}else if(strpos($value[1],"var") > -1){
				$default = "DEFAULT '' ";
			}else if(strpos($value[1],"dou") > -1){
				$default = "DEFAULT 0 ";
			}

			$modifier = " ADD COLUMN ";

			if(is_array($value)){
				if(array_key_exists('decsp',$value)){
					if($value['decsp'] == 'New'){
						$dropQuery .= " DROP COLUMN `".$value[0]."` ,\n";
						$modifier = " ADD COLUMN ";

					}else{
						$modifier = " MODIFY COLUMN ";
					}
				}
			}

			$alterQuery .= $modifier." `".$value[0]."` ".$value[1]." ".$default.",\n";
		}
	}

	$dropQuery = trim(rtrim(trim($dropQuery),",\n").";");
	$alterQuery = trim(rtrim(trim($alterQuery),",\n").";");

	return $requireDrop?$dropQuery."\n".$alterQuery:$alterQuery;
}