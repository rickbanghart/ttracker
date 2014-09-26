<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../includes/ttrack_functions.php';
include '../includes/ttrack_db_sqlsrv.php';

//echo phpinfo();
//echo $action;
//die;
switch ($action) {
	//echo 'should get templates';
	case 'deletetemplate':
	    delete_template();
	    break;
	case 'get_assignments':
		get_assignments();
		break;
	case 'get_entry_framework':
		get_survey_display_framework();
		break;
	case 'get_survey':
		get_survey();
		break;
	case 'updateoffset':
		$cursorObj['value'] = $filter;
		$cursorObj['table'] = $table;
		$cursorObj['field'] = $field;
		$cursorObj['type'] = 's';
		update_offset($cursorObj);
		 break;
	case 'readQualtricsData':
	    readQualtricsData($fileBase);
	    break;
	case 'getusers':
		$userCursorObj['offset'] = $offset;
		$userCursorObj['rowcount'] = $rowCount;
		get_users($userCursorObj);
		break;
	case 'savetemplate':
		save_template();
		
		break;
	case 'saveresponse':
		save_response();
		break;
	case 'get_surveys':
		get_surveys();
		break;
	case 'gettemplatedetail':
	    get_template_detail();
	    break;
	case 'markcomplete':
		mark_complete();
		break;
	case 'get_interns':
		get_interns();
		break;
	case 'get_templates':
	    get_templates();
	    break;
	case 'save_selection':
		save_selection();
		break;
    default:
        echo "fell through switch with action = $action";		
}
function get_entry_framework() {
	// this display is designed with an eye toward a handheld device
	$output = <<<EOM
<div class="mainContainer">
	<div class="titleContainer" id="titleContainer">
    
    </div>
	<div class="progressContainer" id="progressContainer">
    progress
    </div>
    <div class="promptContainer" id="prompt">
    prompt
    </div>
    <div class="foilContainer">
    	<div class="foil" name="foil" id="foil1" onclick="foilClicked(1)"><a >one
        </a></div>
    	<div class="foil" name="foil" id="foil2" onclick="foilClicked(2)"><a>two
        </a></div>
    	<div class="foil" name="foil" id="foil3" onclick="foilClicked(3)"><a>three
        </a></div>
    	<div class="foil" name="foil" id="foil4" onclick="foilClicked(4)"><a>four
        </a></div>
    	<div class="foil" name="foil" id="foil5" onclick="foilClicked(5)"><a>five
         </a></div>
    </div>
    <div class="surveyFooter">
        <div class="backArrow" onclick="previousItem()">
        </div>
		<div class="doneButton" id="doneButton" onclick="doneButtonClicked()">
		</div>
       <div class="forwardArrow" onclick="nextItem()">
        </div>
	</div>
</div>
EOM;
	echo $output;
}
function get_templates() {
	$qry = "{call dbo.sp_get_templates(?)}";
	// returns template_id, title, and description
	$query_params[0] = 5;
	$field_list = array('template_id','title','description');
	$returnTemplates = data_select($qry, $query_params, $field_list);
	$returnData['dataDestination'] = 'templates';
	$returnData['dataObject'] = $returnTemplates;
	echo(json_encode($returnData));
}
function readQualtricsData($fileBase) {
    // we assume there is a .csv and a .xml with that name in this directory
    // FIX ME!! hard coding directory path -- not good
    $path = '../batch/uploads/';
    $handle = fopen($path . $fileBase . '.csv','r');
    $QIds = fgetcsv($handle);
    $ItemPrompts = fgetcsv($handle);
    fclose($handle);
    $xml = simplexml_load_file($path . $fileBase . '.xml');
    $response = $xml->Response[0];
    $numFields = count($QIds);
    $inputResponseSet = $response->ResponseSet;
    $returnData['responseSet'] = $inputResponseSet;
    $returnData['numQuestions'] = $numFields;
    echo(json_encode($returnData));
}
function get_template_detail() {
    global $templateid;
    global $conn;
    $qry = "{call dbo.sp_get_template_detail(?)}";
    $query_params[0] = $templateid;
    $field_list = array('template_id', 'template_title','cluster_id','template_description',
            'cluster_header', 'item_id','item_type', 'item_prompt', 'item_description',
            'cluster_parent_id', 'option_id', 'option_numeric_value',
            'option_text_value', 'option_type', 'option_label',
            'option_description');
    $templateDetails = data_select($qry, $query_params, $field_list);
    $firstRow = $templateDetails[0];
    $templateObject['template_id'] = $firstRow['template_id'];
    $templateObject['template_title'] = $firstRow['template_title'];
    $templateObject['template_description'] = $firstRow['template_description'];
    $current_cluster = 0;
    $current_item = 0;
    $item_count = 0;
    $cluster_count = 0;
    $in_cluster = 0;
    $in_item = 0;
    $current_option = 0;
    $option_count = 0;
    for ($row = 0; $row < count($templateDetails); $row++) {
        if ($templateDetails[$row]['cluster_id'] &&($current_cluster != $templateDetails[$row]['cluster_id'])) {
            // new (or first) cluster
            $cluster_count ++;
            if ($in_cluster == 1) {
                // we have an existing cluster, so save it to clusters[]
                if ($in_item == 1) {
                    $item_object['options'] = $options;
                    $items[] = $item_object;
                    $in_item = 0;
                }
                $clusterObject['items'] = $items;
                $clusters[] = $clusterObject;
                // and initialize variables to create new cluster
                $clusterObject = array();
                $items = array();
                $item_count = 0;
            }
            $in_cluster = 1;
            $current_cluster = $templateDetails[$row]['cluster_id'];
            $clusterObject['cluster_id'] = $templateDetails[$row]['cluster_id'];
            $clusterObject['cluster_header'] = $templateDetails[$row]['cluster_header'];
        }
        if ($templateDetails[$row]['item_id'] && ($current_item != $templateDetails[$row]['item_id'])) {
            $item_count ++;
            $current_item = $templateDetails[$row]['item_id'];
            if ($in_item == 1) {
                $item_object['options'] = $options;
                $items[] = $item_object;
                $item_object = array();
            }
            $option_count = 0;
            $options = array();
            $optionObject = array();
            $item_object['item_id'] = $templateDetails[$row]['item_id'];
            $item_object['item_type'] = $templateDetails[$row]['item_type'];
            $item_object['item_prompt'] = $templateDetails[$row]['item_prompt'];
            $item_object['item_description'] = $templateDetails[$row]['item_description'];
            $in_item = 1;
        }
        if ($templateDetails[$row]['option_id'] && ($current_option != $templateDetails[$row]['option_id'])) {
            $option_count ++;
            $optionObject['option_id'] = $templateDetails[$row]['option_id'];
            $optionObject['option_numeric_value'] = $templateDetails[$row]['option_numeric_value'];
            $optionObject['option_text_value'] = $templateDetails[$row]['option_text_value'];
            $optionObject['option_type'] = $templateDetails[$row]['option_type'];
            $optionObject['option_label'] = $templateDetails[$row]['option_label'];
            $optionObject['option_description'] = $templateDetails[$row]['option_description'];
            $options[] = $optionObject;
        }
    }
    if ($cluster_count >  0) {
        if ($in_item == 1) {
            $item_object['options'] = $options;
            $items[] = $item_object;
        }
        if ($item_count > 0) {
            $clusterObject['items'] = $items;
        }
        $clusters[] = $clusterObject;
        $templateObject['clusters'] = $clusters;
    }
    $returnData['dataDestination'] = 'templateDetail';
    $returnData['dataObject'] = $templateObject;
    //echo phpinfo();
    echo(json_encode($returnData));
}
function get_survey() {
	global $mysqli;
	
    $resturnData['something']='some value here';
    $returnData['clusters'] = $clusters_array;
}
function update_offset($cursorObj) {
	global $mysqli;
	$table = $cursorObj['table'];
	$field = $cursorObj['field'];
	$value = $cursorObj['value'];
	$type = $cursorObj['type'];
	$qry = "SELECT count(*) as count FROM $table WHERE $field < ? order by $field";
	$rst = $mysqli->prepare($qry);
	$rst->bind_param($type,$value);
	$rst->bind_result($new_offset);
	$rst->execute();
	$rst->fetch();
	$output['new_offset'] = $new_offset;
	echo(json_encode($output));
}
function get_users($cursorObj) {
	global $mysqli;
	$where_clause = '';
	$qry = "SELECT SLastName, SFirstName  FROM tblStudents ORDER BY SLastName LIMIT " . $cursorObj['offset'] .',' . $cursorObj['rowcount'];
	$res = $mysqli->query($qry);
	//echo $qry;
	$counter = 0;
	while ($row = $res->fetch_assoc()) {
		$returnData[$counter] = $row;
		$counter ++;
	}
    echo(json_encode($returnData));	
}

?>
