<?php
/**
 * Created by yogesh on 18 01, 2018.
 *
 */

$conn = mysqli_connect("192.168.1.66","root","");

$resource = mysqli_query($conn,"select * from information_schema.columns
where table_schema = 'db_robertkime_v5'
order by table_name,ordinal_position");

$columns = mysqli_fetch_all($resource,MYSQLI_ASSOC);

$tables = array();

foreach($columns as $column){
    if(!array_key_exists($column['TABLE_NAME'],$tables)){
        $tables[$column['TABLE_NAME']] = array();
    }
    $tables[$column['TABLE_NAME']][] = array($column['COLUMN_NAME'],$column['COLUMN_TYPE']);
}

//echo "<pre>",print_r($tables),"</pre>";

include __DIR__."/../views/db-table.php";

