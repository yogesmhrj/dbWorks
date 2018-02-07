<?php
/**
 * Created by yogesh on 18 01, 2018.
 *
 */
include __DIR__."/../app.php";

use dbWorks\lib\DbOpenHelper;

$dbHelper = new DbOpenHelper();

$database1 = "dd_2017_robertkime_db";

$dbHelper->setHost("localhost")
        ->setUser("root")
        ->setPass("root")
        ->setName($database1)
        ->connect();

$columns = $dbHelper->getTableSchema();

$tables = $dbHelper->getTables();

$message = count($tables)." tables.";

include __DIR__."/../views/show.php";

