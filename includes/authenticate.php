<?php 
//
function isOnline() {
	global $token;
    $query_params = array($token, $time_out_duration);
	$qry = "{call dbo.sp_authenticate(?,?)}";
	$rst = sqlsrv_query($conn, $qry, $query_params);
      if( ($errors = sqlsrv_errors() ) != null)
      {
         foreach( $errors as $error)
         {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."\n";
            echo "code: ".$error[ 'code']."\n";
            echo "message: ".$error[ 'message']."\n";
         }
      }
	//echo 'returned from query was ' . $rst;
	$row = sqlsrv_fetch_object($rst);
	return($row['active'];
}
?>