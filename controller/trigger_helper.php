<?php
/**
 * Created by yogesh on 18 01, 2018.
 *
 */
include __DIR__."/../app.php";

use dbWorks\lib\DbOpenHelper;

// $database = "dd_jmims_test_db";
$database = "dd_furnishingfile_jmims_db";

if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){

	$dbHelper = new DbOpenHelper();

	$dbHelper->setHost("localhost")
	        ->setUser("root")
	        ->setPass("root")
	        ->setName($database)
	        ->connectDb();

	$MODULE = $_REQUEST['m'];

	$PRIMARY_KEY = $_REQUEST['pk'];

	$TABLE_NAME = $_REQUEST['t'];
	if(array_key_exists('t',$_REQUEST)){
		$TABLE_NAME = $_REQUEST['t'];	
	}

	$SOFT_DELETE = false;
	if(array_key_exists('sd',$_REQUEST)){
		$SOFT_DELETE = $_REQUEST['sd'] == "true";	
	}

	$TRIGGER_ALIAS = substr($TABLE_NAME,4);

	function isColumnRequired($column){

		global $PRIMARY_KEY;

		if($column == 'created_at'){
			return false;
		}

		if($column == 'deleted_at'){
			return false;
		}

		if($column == 'updated_at'){
			return false;
		}

		if($column == 'changed_by'){
			return false;
		}

		if($column == $PRIMARY_KEY){
			return false;
		}

		if($column == 'status'){
			return true;
		}

		if($column == 'order'){
			return false;
		}

		return true;
	}


	function getInsertTrigger(){
		global $PRIMARY_KEY;
		global $TABLE_NAME;
		global $TRIGGER_ALIAS;
		global $MODULE;
		global $dbHelper;

		$columns = $dbHelper->executeQuery("SHOW COLUMNS FROM $TABLE_NAME;");

		if(!$columns){
			echo $dbHelper->getError();
		}

		$columnsCount = count($columns) * 150;

		$triggerStringStub = 'DROP TRIGGER IF EXISTS '.$TRIGGER_ALIAS.'_insert_trigger;<br>'.
						'CREATE TRIGGER  '.$TRIGGER_ALIAS.'_insert_trigger <br>'.
	                    'AFTER INSERT <br>'.
	                    'ON '.$TABLE_NAME.' <br>'.
	                    'FOR EACH ROW <br>'.
	                    'BEGIN <br>'.
	                    'DECLARE message VARCHAR('.$columnsCount.'); <br>'.
	                    '<br>'.
	                    'IF @TRIGGER_DISABLED IS NULL THEN <br>'.
	                    '<br>'.
	                    'set message = \'Created new row with \'; <br>';

	   
	    foreach ($columns as $key => $column) {   	

	    	if(isColumnRequired($column['Field'])){
	    		if($column['Null'] == 'NO'){
	    			$triggerStringStub .= "set message = CONCAT(message,'field: ".$column['Field']." as ',new.".$column['Field'].",', '); <br>";
	    		}else{
	    			$triggerStringStub .= "IF new.".$column['Field']." IS NOT NULL THEN"."<br>";
	    			$triggerStringStub .= "set message = CONCAT(message,'field: ".$column['Field']." as ',new.".$column['Field'].",', '); <br>";
	    			$triggerStringStub .= "END IF;"."<br>";
	    				
	    		}
	    		
	    	}	
	    }
	   

	    $triggerStringStub .= '<br>'.
	    					'INSERT INTO tbl_ims_system_logs <br>'.
							'SET <br>'.
							'modal_name = \''.$TABLE_NAME.'\', <br>'. 
							'modal_id = new.'.$PRIMARY_KEY.', <br>'.
							'user_id = new.changed_by, <br>'.
							'action = \'N\', <br>'.
							'type = \''.$MODULE.'\', <br>'.
							'description = message, <br>'.
							'created_at = NOW(); <br>'.
		    				'END IF;<br>'.
		                    'END;';                

		return $triggerStringStub;
	}

	function getUpdateTrigger(){
		global $PRIMARY_KEY;
		global $TABLE_NAME;
		global $TRIGGER_ALIAS;
		global $MODULE;
		global $dbHelper;
		global $SOFT_DELETE;

		$columns = $dbHelper->executeQuery("SHOW COLUMNS FROM $TABLE_NAME;");

		if(!$columns){
			echo $dbHelper->getError();
		}

		$columnsCount = count($columns) * 150;

		$triggerStringStub = 'DROP TRIGGER IF EXISTS '.$TRIGGER_ALIAS.'_update_trigger;<br>'.
						'CREATE TRIGGER  '.$TRIGGER_ALIAS.'_update_trigger <br>'.
	                    'AFTER UPDATE <br>'.
	                    'ON '.$TABLE_NAME.' <br>'.
	                    'FOR EACH ROW <br>'.
	                    'BEGIN <br>'.
	                    'DECLARE message VARCHAR('.$columnsCount.'); <br>'.
	                    '<br>';

	    if($SOFT_DELETE){

	    	$triggerStringStub .= 'IF new.deleted_at IS NOT NULL THEN <br>'.
								'<br>'.
	                    		'set message = \'Deleted row with \'; <br>';

		    foreach ($columns as $key => $column) {
		    	
		    	if(isColumnRequired($column['Field'])){

			    	$triggerStringStub .= "set message = CONCAT(message,'field: ".$column['Field']." as ',old.".$column['Field'].",', '); <br>";
			    }
		    }

		    $triggerStringStub .= '<br>'.
		    					'INSERT INTO tbl_ims_system_logs <br>'.
								'SET <br>'.
								'modal_name = \''.$TABLE_NAME.'\', <br>'. 
								'modal_id = old.'.$PRIMARY_KEY.', <br>'.
								'user_id = old.changed_by, <br>'.
								'action = \'D\', <br>'.
								'type = \''.$MODULE.'\', <br>'.
								'description = message, <br>'.
								'created_at = NOW(); <br>'.
			    				'<br>'.
			    				'ELSE <br>';	
	    }                

	    $triggerStringStub .= 'set message = \'Changed \'; <br>';

	                    
	    foreach ($columns as $key => $column) {
	    	if(isColumnRequired($column['Field'])){
		    	$triggerStringStub .= 'IF old.'.$column['Field'].' <> new.'.$column['Field'].' THEN <br>'.
									  'set message = CONCAT(message,\' field: '.$column['Field'].' from \',old.'.$column['Field'].',\' to \',new.'.$column['Field'].',\', \'); <br>'.
									  'END IF; <br>';
			}
	    }

	    $triggerStringStub .= '<br>'.
	    					'IF message <> "Changed " THEN <br>'.
	    					'INSERT INTO tbl_ims_system_logs <br>'.
							'SET <br>'.
							'modal_name = \''.$TABLE_NAME.'\', <br>'. 
							'modal_id = old.'.$PRIMARY_KEY.', <br>'.
							'user_id = new.changed_by, <br>'.
							'action = \'U\', <br>'.
							'type = \''.$MODULE.'\', <br>'.
							'description = message, <br>'.
							'created_at = NOW(); <br>'.	
							'END IF; <br>';

		if($SOFT_DELETE){
			 $triggerStringStub .= 'END IF; <br>';
		}					

		$triggerStringStub .= 'END;';                

		return $triggerStringStub;
	}

	function getDeleteTrigger(){
		global $PRIMARY_KEY;
		global $TABLE_NAME;
		global $TRIGGER_ALIAS;
		global $MODULE;
		global $dbHelper;

		$columns = $dbHelper->executeQuery("SHOW COLUMNS FROM $TABLE_NAME;");

		if(!$columns){
			echo $dbHelper->getError();
		}

		$columnsCount = count($columns) * 150;

		$triggerStringStub = 
						'DROP TRIGGER IF EXISTS '.$TRIGGER_ALIAS.'_delete_trigger;<br>'.
						'CREATE TRIGGER  '.$TRIGGER_ALIAS.'_delete_trigger <br>'.
	                    'AFTER DELETE <br>'.
	                    'ON '.$TABLE_NAME.' <br>'.
	                    'FOR EACH ROW <br>'.
	                    'BEGIN <br>'.
	                    'DECLARE message VARCHAR('.$columnsCount.'); <br>'.
	                    '<br>'.
	                    'IF @TRIGGER_DISABLED IS NULL THEN <br>'.
	                    '<br>'.
	                    'set message = \'Deleted row with \'; <br>';

	                    
	    foreach ($columns as $key => $column) {
	    	if(isColumnRequired($column['Field'])){
	    	$triggerStringStub .= "set message = CONCAT(message,'field: ".$column['Field']." as ',old.".$column['Field'].",', '); <br>";
	    	}
	    }

	    $triggerStringStub .= '<br>'.
	    					'INSERT INTO tbl_ims_system_logs <br>'.
							'SET <br>'.
							'modal_name = \''.$TABLE_NAME.'\', <br>'. 
							'modal_id = old.'.$PRIMARY_KEY.', <br>'.
							'user_id = old.changed_by, <br>'.
							'action = \'D\', <br>'.
							'type = \''.$MODULE.'\', <br>'.
							'description = message, <br>'.
							'created_at = NOW(); <br>'.
		    				'END IF;<br>'.
		                    'END;';                

		return $triggerStringStub;
	}

	function getFillables(){
		global $PRIMARY_KEY;
		global $TABLE_NAME;
		global $TRIGGER_ALIAS;
		global $MODULE;
		global $dbHelper;

		$columns = $dbHelper->executeQuery("SHOW COLUMNS FROM $TABLE_NAME;");

		if(!$columns){
			echo $dbHelper->getError();
		}

		$data = '/**'."<br>".'
      * The attributes that are mass assignable.'."<br>".'
      * '."<br>".'
      * @var array '."<br>".'
     */ '."<br>".'
		protected $fillable = ['."<br>";

		foreach ($columns as $key => $column) {
			if($column['Field'] == 'changed_by'){
				$data .= "\t'".$column['Field']."',<br>";
			}
			else{
		    	if(isColumnRequired($column['Field'])){
		    		$data .= "\t'".$column['Field']."',<br>";
		    	}
	    	}
	    }
        
    	$data .= '];';

    	return $data;
	}

}

include __DIR__."/../views/trigger.php";
