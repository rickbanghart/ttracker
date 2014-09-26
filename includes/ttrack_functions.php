<?php 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	foreach ($_POST as $key=>$value) {
		$$key = $value;
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	foreach ($_GET as $key=>$value) {
		$$key = $value;
	}
}
function data_select($qry, $query_params, $field_list) {
   global $conn;
	$returnRecords = array();
	$rst = sqlsrv_query($conn, $qry, $query_params);
	sql_errors_display();
	$first_row = 1;
	while ($row = sqlsrv_fetch_object($rst)) {
	    foreach ($row as $key=>$value) {
	        //echo('  field in row is: ' . $key);
             // loop through values 
        } 
	    foreach ($field_list as $field) {
    	    $rowItem[$field] = $row->$field;
	    }
	    $returnRecords[] = $rowItem;
	}
    return($returnRecords);
}
function debug_log($msg) {
	if ($debug_log = fopen('E:\\Inetpub\\wwwroot\\TTracker\log\debug.txt','a')) {
	} else {
		die ("couldn't open log file");
	}
	fwrite($debug_log,$msg . "\n");
}
function escape_data ($data){
	if(!is_array($data)){
		$data=trim($data);
		$data=htmlspecialchars($data, ENT_QUOTES);
		$data=stripslashes($data);
	}
	return $data;
}
function insert_record_sqlsrv($tableName,$fieldNameArray,$fieldValueArray,$msg) {
	global $conn;
	$fields = '(' . $fieldNameArray[0];
	$values = '(' . '?';
	for ($i = 1; $i < count($fieldNameArray);$i++) {
		$fields .= ',' . $fieldNameArray[$i];
		$values .= ',' . '?';
		$a_params[$i + 1] = & $fieldValueArray[$i];
	}
	$fields .= ')';
	$values .= ')';
	$qry = "INSERT INTO $tableName $fields VALUES $values";
	//echo ("<br />$qry<br />");
	if ($stmt = sqlsrv_prepare($conn, $qry, $fieldValueArray)) {
		//echo "created rst <br />";
	} else {
		sql_errors_display($msg . ' 61 ttrack_functions');
	}
	//print_r($stmt);
	//echo ("<br /> Above is statement handle <br />");
	sqlsrv_execute($stmt);
	sql_errors_display($msg . ' 66 ttrack_functions');
	$qry = "SELECT SCOPE_IDENTITY()";
    $stmt = sqlsrv_prepare($conn,$qry);
    sqlsrv_execute($stmt);
	sqlsrv_fetch($stmt);
	$last_id = sqlsrv_get_field($stmt, 0);
	sql_errors_display($msg . ' 71 ttrack_functions');
  return($last_id);
	//echo $qry . "<br />";
}
function role_to_roleid($role) {
    global $conn;
    $qry = "SELECT role_id FROM role WHERE role = ?";
    $params = array(&$role);
    $rst = sqlsrv_prepare($conn, $qry, $params);
    sqlsrv_execute($rst);
    sqlsrv_fetch($rst);
    $role_id = sqlsrv_get_field($rst,0);
    return ($role_id);
}
function sql_errors_display ($msg) {
	if( ($errors = sqlsrv_errors() ) != null) {
				echo "sql errors from $msg <br />";
         foreach( $errors as $error)
         {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']." <br />\n";
            echo "code: ".$error[ 'code']." <br />\n";
            echo "message: ".$error[ 'message']." <br />\n";
         }
      }
}
function user_exist_sqlsrv($email) {
	global $conn;
	$qry = "SELECT user_id	 FROM tt_user WHERE email = ?";
	$params = array(&$email);
	$rst = sqlsrv_prepare($conn, $qry, $params);
	sqlsrv_execute($rst);
	if (sqlsrv_fetch($rst)) {
		$user_id = sqlsrv_get_field($rst,0);
	} else {
		$user_id = 0;
	}
	return($user_id);
}
function user_is_role($email, $role) {
    $return_value = 0;
    $user_id = user_exist_sqlsrv($email);
    $role_id = role_to_roleid($role);
    global $conn;
    $qry = "SELECT count(*) AS count FROM user_role WHERE user_id = ? AND role_id = ? AND active = 1";
    $params = array(&$user_id, &$role_id);
    $rst = sqlsrv_prepare($conn,$qry,$params);
    sqlsrv_execute($rst);
    sqlsrv_fetch($rst);
    error_log("checked $email for role $role using $user_id and $role_id");
    $return_value = sqlsrv_get_field($rst,0); 
    sql_errors_display("from user is role");   
    return($return_value);
}
?>