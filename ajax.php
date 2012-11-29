<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
$serverName = "Ed4DbT,20000";
$connectionInfo = array( "Database"=>"TTracker");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
$action = "";
$examiner_id;
$examinee_id;
// handle all POST and GET variables here
// set a bunch of variables to be used globally
// $action

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //echo 'POST ';
	foreach ($_POST as $key=>$value) {
	    //echo 'a key is ' . $key;
		$$key = $value;
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //echo 'GET';
	foreach ($_GET as $key=>$value) {
		$$key = $value;
		//echo $key . ' is ' . $value . ' ';
	}
}
//echo $action;
//die;
switch ($action) {
	//echo 'should get templates';
	case 'get_assignments':
		get_assignments();
		break;
	case 'get_entry_framework':
		get_survey_display_framework();
		break;
	case 'get_survey':
		get_survey();
		break;
	case 'savetemplate':
		save_template();
		
		break;
	case 'get_surveys':
		get_surveys();
		break;
	case 'gettemplatedetail':
	    get_template_detail();
	    break;
	case '':
		mark_complete();
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
function get_assignments() {
	global $conn;
	global $instance_id;
	$query_params = array($instance_id);

	$qry = "{call EDUC\banghart.sp_get_assignments(?)}";
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
	$first_row = 1;
	echo ("<h1>Instance is: " . $query_params[0] . "</h1>");
	echo ("<h1>Type int " . gettype($query_params[0]) . "</h1>");
	while ($row = sqlsrv_fetch_array($rst)) {
		    echo("<h1> returned instance " . $row['instance_id'] . "</h1>");
		if ($first_row == 1) { ?>
        	<table border="1"><tr><th>Student</th>
            <th>Due</th>
            <th>Complete</th>
            <th>Examiner</th>
            </tr> <?php
			$first_row = 0;
		}
        ?><tr><td style="cursor:pointer;" onclick="get_survey(<?php 
		echo $row['instance_id'] . ',' . $row['examiner_id'] . "," . $row['examinee_id'] . ')">';
		echo $row['examinee_first_name'] . ' ' . $row['examinee_last_name'] ?></td>
        <td><?php echo $row['due_date']->format('m-d-Y'); ?></td>
        <td><?php echo $row['complete'] ?></td>
        <td><?php echo $row['examiner_first_name'] . ' ' . $row['examiner_last_name'] ?></td>
        
        </tr><?php
	}
    if ($first_row == 0) {
		?></table>
		<?php
	}
}
function data_select($qry, $query_params, $field_list) {
    global $conn;
	$returnRecords = array();
	$rst = sqlsrv_query($conn, $qry, $query_params);
    if( ($errors = sqlsrv_errors() ) != null) {
         foreach( $errors as $error) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."\n";
            echo "code: ".$error[ 'code']."\n";
            echo "message: ".$error[ 'message']."\n";
         }
    }
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
function get_template_detail() {
    global $templateid;
    global $conn;
    $qry = "{call dbo.sp_get_template_detail(?)}";
    $query_params[0] = $templateid;
    $field_list = array('template_id', 'template_title','cluster_id','template_description',
            'cluster_header', 'item_id', 'item_prompt', 'item_description',
            'cluster_parent_id', 'option_id', 'option_numeric_value',
            'option_text_value', 'option_type', 'option_label',
            'option_description');
    $templateDetails = data_select($qry, $query_params, $field_list);
    $firstRow = $templateDetails[0];
    $templateObject['template_id'] = $firstRow['template_id'];
    $templateObject['template_title'] = $firstRow['template_title'];
    $templateObject['template_description'] = $firstRow['template_description'];
    for ($row = 0; $row < count($templateDetails); $row++) {
          if ($templateDetails[0]['cluster_id']) {
            echo 'found a cluster';
          }
    }
    $returnData['dataDestination'] = 'templateDetail';
    $returnData['dataObject'] = $templateObject;
    echo(json_encode($returnData));
}
function get_survey_display_framework() { ?>
	<div class="mainContainer">
        <div class="surveyHeader" id="surveyHeader">
        </div>
        <div class="titleContainer" id="titleContainer">
        </div>
        <div id="promptContainer">
    		<div id="prompt">In the ID prompt
    		</div>
            <div id="description">
            </div>
        </div>
    <div id="leftText">
    	<div>Strengths and evidence</div>
    	<textarea id="strength" class="textEntry" rows="10" cols="25">
        </textarea>
    </div>
    <div id="rightText">
    	<div>Practices to work on</div>
    	<textarea id="weakness" class="textEntry" rows="10" cols="25">
        </textarea>
    </div>
    <div class="foilContainer">
    	<div class="foil" name="foil" id="foil0" onclick="foilClicked(0)"><a >one
        </a></div>
    	<div class="foil" name="foil" id="foil1" onclick="foilClicked(1)"><a>two
        </a></div>
    	<div class="foil" name="foil" id="foil2" onclick="foilClicked(2)"><a>three
        </a></div>
    	<div class="foil" name="foil" id="foil3" onclick="foilClicked(3)"><a>four
        </a></div>
    	<div class="foil" name="foil" id="foil4" onclick="foilClicked(4)"><a>five
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
	<?php
}
//LEFT JOIN TTracker.dbo.st_response AS t8 ON t8.examinee_id = ? AND
//						t8.examiner_id = ? AND
//						t8.instance_id = ?
function get_survey() {
	// delivers an XML version of an entire survey
	// display will be handled client side
	global $conn;
	global $instance_id;
	global $examiner_id;
	global $examinee_id;
	$query_params[0] = intval($instance_id);
	$query_params[1] = intval($examiner_id);
	$query_params[2] = intval($examinee_id);
	$returnData['instance'] = $instance_id;
	$returnData['examiner'] = $examiner_id;
	$returnData['examinee'] = $examinee_id;
	//$query_params[3] = $instance_id;
	$qry = "{call EDUC\banghart.sp_get_survey(?,?,?)}";
	$rst = sqlsrv_query($conn,$qry,$query_params);
      if( ($errors = sqlsrv_errors() ) != null)
      {
         foreach( $errors as $error)
         {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."\n";
            echo "code: ".$error[ 'code']."\n";
            echo "message: ".$error[ 'message']."\n";
         }
         die('that is all');
      }
      $returnData[] = 'one element';
      $counter  = 1;
      	while ($row = sqlsrv_fetch_array($rst, SQLSRV_FETCH_ASSOC)) {
      	    $returnData[$counter] = $row;
            $counter ++;
        }
        $testJSON = '{"status":"OK"}';
        echo(json_encode($returnData));
}
function get_survey_old() { 
	// delivers an XML version of an entire survey
	// display will be handled client side
	global $conn;
	global $instance_id;
	global $examiner_id;
	global $examinee_id;
	$query_params[0] = intval($instance_id);
	$query_params[1] = intval($examiner_id);
	$query_params[2] = intval($examinee_id);
	//$query_params[3] = $instance_id;
	$qry = "{call EDUC\banghart.sp_get_survey(?,?,?)}";
	$rst = sqlsrv_query($conn,$qry,$query_params);
      if( ($errors = sqlsrv_errors() ) != null)
      {
         foreach( $errors as $error)
         {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."\n";
            echo "code: ".$error[ 'code']."\n";
            echo "message: ".$error[ 'message']."\n";
         }
         die('that is all');
      }
	$doc = new DOMDocument();
	$survey_element =  $doc->createElement('survey');
	$survey_element->setAttribute('id',1);
	$survey_element->setAttribute('examiner_id',$examiner_id);
	$survey_element->setAttribute('examinee_id',$examinee_id);
	$survey_element->setAttribute('instance_id',$instance_id);
	$current_item = -1;
	$in_item = 0;
	$first_row = 1;
	$itemSequence = 1;
	$current_option = -1;
	while ($row = sqlsrv_fetch_array($rst, SQLSRV_FETCH_ASSOC)) {
		if ($first_row == 1) {
			$first_row = 0;
			$survey_element->setAttribute('title',$row['template_title']);
			$survey_element->setAttribute('semester',$row['semester']);
			$survey_element->setAttribute('year',$row['year']);
			$survey_element->setAttribute('examiner_name', $row['examiner_first_name'] . ' ' . $row['examiner_last_name']);
			$survey_element->setAttribute('examinee_name', $row['examinee_first_name'] . ' ' . $row['examinee_last_name']);
			$survey_description = $doc->createElement('description');
			$description_text_node = $doc->createTextNode($row['template_description']);
			$survey_description->appendChild($description_text_node);
			$survey_element->appendChild($survey_description);
		}
		if ($row['item_id'] != $current_item) {
			$text_response_sequence = 1;
			$current_item = $row['item_id'];
			if ($in_item == 1) {
				$survey_element->appendChild($item_element);
			}
			$in_item = 1;
			$item_element = $doc->createElement('item');
			$item_element->setAttribute('item_id', $row['item_id']);
			$item_element->setAttribute('sequence',$itemSequence);
			$itemSequence ++;
			$prompt_element = $doc->createElement('prompt');
			$prompt_text_node = $doc->createTextNode($row['prompt']);
			$prompt_element->appendChild($prompt_text_node);
			$item_element->appendChild($prompt_element);
			$description_element = $doc->createElement('description');
			$description_text_node = $doc->createTextNode($row['description']);
			$description_element->appendChild($description_text_node);
			$item_element->appendChild($description_element);
			$text_response_element = make_text_response($doc, $row);
			$text_response_element->setAttribute('label','strength');
			$item_element->appendChild($text_response_element);
			$text_response_sequence ++;
			$option_element = make_item_option($doc,$row);
			$current_option = $row['option_id'];
			$item_element->appendChild($option_element);
		} else {
			if ($text_response_sequence < 3) {
				$text_response_sequence ++;
				$text_response_element = make_text_response($doc, $row);
				$text_response_element->setAttribute('label','weakness');
				$item_element->appendChild($text_response_element);
			}
			if ($current_option != $row['option_id']) {
				$current_option = $row['option_id'];
				$option_element = make_item_option($doc,$row);
				$item_element->appendChild($option_element);
			}
		}
	}
	if ($in_item == 1) {
		$survey_element->appendChild($item_element);
	}
	header ("Content-Type:text/xml");
	//echo '<test>' . $examinee_id . '</test>'; 
	echo $doc->saveXML($survey_element);
}
function make_text_response($doc, $row) {
	$text_response_element = $doc->createElement('text_response');
	$text_response_text_node = $doc->createTextNode($row['text_response'] . ' ');
	$text_response_element->appendChild($text_response_text_node);
	return ($text_response_element);
}
function get_surveys() {
	// this selects survey instances
	global $conn;
	$height = 400;
	$width = 400;
	$titleBarHeight = 30;
	$title = "Data Entry Tasks" ;
	$rowHeight = 30;
	$qry = "{call EDUC\banghart.sp_get_surveys()}";
	$rst = sqlsrv_query($conn, $qry);
	$first_row = 1;
	?>
<div style="height:<?php echo $height ?>px;
    			 width:<?php echo $width ?>px;
                 padding:10px;
                 border-style:solid;
                 border-width:1px;
                 border-color:#cccccc;">
    <?php
	while ($row = sqlsrv_fetch_array($rst)) {
		if ($first_row == 1) {
			$first_row = 0;
			?>
            <div style="background-color:#ffdddd;
                         display:block;
                         text-align:center;
                         width:<?php echo $width ?>px;
                         height:<?php echo $titleBarHeight ?>px;"  >Data Entry Tasks</div>
        <?php    
		}
        ?>
		<div id="selectSurvey" style="display:block;
        						cursor:pointer;
        						background-color:#dddddd;
                                padding-left:5px;
                                width:<?php echo $width ?>px;
                                height:<?php echo $rowHeight ?>px;">
       <span style="cursor:pointer;" onclick="select_survey(<?php echo $row['instance_id'] ?>)" instance_id="<?php echo $row['instance_id'] ?>"> <?php echo $row['title'] . $row['instance_id']?></span>
		<?php
	}
	if ($first_row == 0) {
		?></div></div><?php
	} else {
		echo "No surveys found </div>";
	}
}
function make_item_option($doc,$row) {
	$option_element = $doc->createElement('option');
	//echo ('option choice is ' + $row['option_choice']);
	if ($row['option_choice'] == $row['option_id']) {
		$option_element->setAttribute('selected',1);
	} else {
		$option_element->setAttribute('selected',0);
	}
	$option_element->setAttribute('option_id',$row['option_id']);
	$option_element->setAttribute('numeric_value',$row['numeric_value']);
	$option_element->setAttribute('text_value',$row['text_value']);
	$option_element->setAttribute('type',$row['option_type']);
	return ($option_element);
}
function mark_complete() {
	global $conn, $instance_id, $examiner_id, $examinee_id;
	$qry = "{call dbo.sp_mark_survey_complete(?,?,?)}";
	$query_params[0] = $instance_id;
	$query_params[1] = $examiner_id;
	$query_params[2] = $examinee_id;
	if (sqlsrv_query($conn,$qry,$query_params)) {
		echo '<return>success</return>';
	} else {
		//sql_errors_display();
		echo '<return>fail</return>';
	}
}
function save_template() {
    global $conn;
    global $template_title;
    global $template_description;
    global $template_id;
    $returnData['status'] = 'success';
    // echo 'sending to description ' . $template_description;
    $query_params = array($template_title, $template_description, $template_id);
	$qry = "{call dbo.sp_save_template(?,?,?)}";
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
	echo(json_encode($returnData));
	//echo 'from server is: ' . $row->id_value;
}
function save_selection() {
	global $conn;
	global $instance_id;
	global $item_id;
	global $option_id;
	global $examiner_id;
	global $examinee_id;
	global $strength;
	global $weakness;
	$weakness = $weakness?$weakness:"";
	$strength = $strength?$strength:"";
	$query_params[0] = $option_id;
	$query_params[1] = $examinee_id;
	$query_params[2] = $examiner_id;
	$query_params[3] = $instance_id;
	$query_params[4] = $item_id;
	$query_params[5] = $strength;
	$query_params[6] = $weakness;
	$qry = "{call EDUC\banghart.sp_save_survey_response(?,?,?,?,?,?,?)}";
	$rst = sqlsrv_query($conn,$qry,$query_params);
		if (! sqlsrv_query($conn,$qry,$query_params)) {
		$qry = "{call EDUC\banghart.sp_update_survey_response(?,?,?,?,?,?,?)}";
		$rst = sqlsrv_query($conn,$qry,$query_params);

			echo ' updated ***** ';
	}
	echo $rst . " **** ";
	echo "examinee: $examinee_id , examiner: $examiner_id, instance: $instance_id, item: $item_id; option: $option_id, strength: $strength, weakness: $weakness";
	 
}

function sql_errors_display () {
	if( ($errors = sqlsrv_errors() ) != null) {
         foreach( $errors as $error)
         {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."\n";
            echo "code: ".$error[ 'code']."\n";
            echo "message: ".$error[ 'message']."\n";
         }
      }
			echo ' updated ***** ';
}
?>
