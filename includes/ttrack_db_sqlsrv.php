<?php
//$serverName = "Ed4DbT,20000";
//$serverName = "educ-srvsql2dev.campusad.msu.edu\SRVSQL2DEV,1433";
//$connectionInfo = array( "Database"=>"TTracker");
//$connectionInfo = array( "Database"=>"banghart", "UID"=>"CAMPUSAD\banghart", "PWD"=>"temp_12345");

$serverName = "educ-srvsql2dev.campusad.msu.edu\SRVSQL2DEV";
//$serverName = "educ-srvsql2dev.campusad.msu.edu, 1433";
//$connectionInfo = array( "Database"=>"TTracker");
$connectionInfo = array( "Database"=>"TEdata", "UID"=>"TE-data", "PWD"=>"temp_12345");
//$connectionInfo = array( "Database"=>"TTracker");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if ($conn === false) {
	die(print_r(sqlsrv_errors(), true));
} else {
	//echo("<br />good connection <br />");
}



//$connectionInfo = array( "Database"=>"TTracker");
//$conn = sqlsrv_connect( $serverName, $connectionInfo);
//if ($conn === false) {
//	die(print_r(sqlsrv_errors(), true));
//} else {
//	echo("<br />good connection <br />");
//}
?>
