<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
include ("../includes/ttrack_db_sqlsrv.php");
include ("../includes/ttrack_functions.php");

switch ($action) {
	case 'readxml':
	    readxml();
	    break;
    default:
        echo "fell through switch with action = $action";		
}
function readxml() {
    global $conn;
    global $fileBase;
    global $start_run;
    $max_run = 20; // load no more than this number of responses to avoid time out problems (and provide feedback)
    $xml = simplexml_load_file($fileBase . '.xml');
    $runaway = 0;
    //echo ($xml->count() . ' number of elements in xml <br />');
    if (isset ($start_run)) {
	    //echo("Starting with $start_run <br />");
    } else {
	    $start_run = 0;
    }      
    $end_run = $start_run + $max_run;
    if ($end_run > $xml->count()) {
		$end_run = $xml->count();
    }
    //echo "ending run with $end_run <br />";
    for ($response_num = (int)$start_run; $response_num < $end_run;$response_num ++) {
	    $response = $xml->Response[$response_num];
	    //echo ($response->children()->count() . " count of children <br />");
	    $answer_array = $response->children();
	    $ResponseID = (string)$answer_array->ResponseID;
	    //echo "<br /> Saving $ResponseID <br />";
	    for ($answer_num = 0; $answer_num < count($answer_array);$answer_num ++) {
		    $QId = (string)$answer_array[$answer_num]->getName();
		    $Response = (string)$answer_array[$answer_num];
		    $table_name = 'q_responses';
		    $fieldNames = array('ResponseID','QId','Response');
		    $fieldValues = array(&$ResponseID, &$QId, &$Response);
		    if ($Response) {
			    insert_record_sqlsrv($table_name, $fieldNames, $fieldValues,'line 57ish');
		    }
	    }
	    //echo(" Response $response_num <br />"); 
    }
    //echo (" finished with $response_num <br />");
    if ($response_num < $xml->count()) {
	    //echo "Click below to continue <br />";
	    //echo '<a href="?fileBase='.$fileBase.'&start_run='.$response_num.'">Continue from </a>';
    }
    if ($response_num < $xml->count() - 1) {
        $returnData['start_run'] = $response_num;
        $returnData['total'] = $xml->count();
        $returnData['fileBase'] = $fileBase;
    } else {
        $returnData['start_run'] = 'finished';
        $returnData['fileBase'] = $fileBase;
        $returnData['total'] = $xml->count();
    }
    echo(json_encode($returnData));
}
?>
