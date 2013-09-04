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
//echo("server request:\n" );
//echo($_REQUEST);
//echo('the method is ' . $_SERVER['REQUEST_METHOD'] . "\n");
	//foreach ($_REQUEST as $key=>$value) {
	    //echo 'a request key is ' . $key . "\n";
		//$$key = $value;
	//}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //echo 'POST ';
	foreach ($_POST as $key=>$value) {
	    //echo 'a post key is ' . $key . "\n";
		$$key = $value;
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //echo 'GET';
	foreach ($_GET as $key=>$value) {
		//echo "a get key is " . $key . "\n";
		$$key = $value;
		//echo $key . ' is ' . $value . "\n";
	}
}

//echo $action;
//die;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/re.css">
<title>SIS Info Schema</title>
<script type="text/javascript">
	var assignment_id = <?php echo($assignment_id) ?>;
	var parser = new DOMParser();
	console.log('in the program');
	function onload_functions() {
		$('form').submit(function() {
			var serialized = $(this).serialize();
			serialized += '&assignment_id=' + assignment_id;
			var jqxhr = $.post('ajax.php', serialized, function(data) {
				console.log("here data returned: " + data);
				
			});
			console.log('in the function');
			console.log(serialized);
			return false;
		});

		console.log('onload');
		retrieve_squap();
	}
	function retrieve_squap() {
 		//console.log(serializedData + ' serialized');
 		//document.write('retrieving squap');
 		var data_sent = "assignment_id=" + assignment_id + '&action=get_survey';
		$.ajax({
  			type: "GET",
  			dataType: "json",
  			url: "ajax.php",
  			data: data_sent,
  			success: success
		});
		return false;
	}
	function success(data, status, jqxhr) {
		
		console.log(status + ' is status');
		console.log('data returned is ' + data);
		console.log('from data ' + data.instance);
		console.log('more from data clus ' + data.clusters[0].cluster_id);
		//document.write('<br> ' + data.clusters[0].cluster_id + ' is the first cluster id ');
		//console.log(jqxhr.responseText + ' is response text');
		//console.log('success from ajax');
		renderSquap(data);
	}
	function renderSquap(squapData) {
		var clusters = squapData.clusters;
		var numClusters = clusters.length;
		var cluster = null;
		var page = $('#pageWrapper');
		var headContent = renderAIPHead(squapData);
		page.append(headContent);
		for (var clusterNum = 0; clusterNum < numClusters; clusterNum ++) {
			console.log ('rendering cluster here');
			var cluster = clusters[clusterNum];
			page.append('<div class="questionGroup" id="qg' + clusterNum + '"></div>');
			var clusterDiv = $('#qg' + clusterNum);
			console.log(clusterDiv.toString() + ' is the cluster div');
			clusterDiv.append('<div class="groupHead">' + cluster.heading + '</div>');
			var items = cluster.items;
			var numItems = items.length;
			var item = null;
			for (var itemNum = 0; itemNum < numItems; itemNum ++) {
				item = items[itemNum];
				console.log ('the item is ' + item.type + ' type');
				console.log ('the item id is ' + item.item_id);
				switch (item.type) {
					case 1:
						 var renderedItem = renderRadioGroup(item, itemNum);
					break;
					case 2:
						var renderedItem = renderTextArea(item, item.item_id);
					break
					default:
						// default statements
				}
				
				clusterDiv.append(renderedItem);
				//clusterDiv.append('<div class="instrumentItem" id="item' + itemNum + '">' + item.prompt + ' </div>');
			}
		}
		
	}
	function renderAIPHead(headInfo) {
		var headerDiv = '<div class="aipHead" style="text-align:left">Intern: ' + headInfo.examineeName + '</div>';
		headerDiv += '<div class="aipHead" style="text-align:left">Completed by: ' + headInfo.examinerName + '</div>';
		return(headerDiv);
	}
	function renderRadioGroup(item, itemNum) {
		var itemElement = document.createElement('div');
		itemElement.setAttribute('class',"instrumentItem");
		itemElement.setAttribute('id', 'item' + itemNum);
		itemPrompt = document.createTextNode(item.prompt);
		itemElement.appendChild(itemPrompt);
		var option_list = item.options;
		var numOptions  = option_list.length;
		for (var opt_num = 0;opt_num < numOptions;opt_num ++) {
			var label = option_list[opt_num].label;
			var radioButton = document.createElement('input');
			radioButton.setAttribute('type','radio');
			radioButton.setAttribute('value',option_list[opt_num].numeric_value);
			radioButton.setAttribute('name', 'item' + itemNum);
			if (option_list[opt_num].checked == 'checked') {
				radioButton.setAttribute('checked','checked');
			} 
			itemElement.appendChild(radioButton);
		}
		return(itemElement);
	}
	function renderTextArea(item, itemNum) {
		var itemElement = document.createElement('div');
		itemElement.setAttribute('class',"instrumentItem");
		itemPrompt = document.createTextNode(item.prompt);
		itemElement.appendChild(itemPrompt);
		var textArea = document.createElement('input');
		textArea.setAttribute('type','text');
		textArea.setAttribute('class','textInput');
		textArea.setAttribute('id', 't_item' + item.item_id);
		textArea.setAttribute('name', 't_item' + item.item_id);
		textArea.value = item.options[0].text_value;
		itemElement.appendChild(textArea);
		return(itemElement);		
	}
	function saveClicked() {
		console.log('save clicked');
	}
	
		
</script>
</head>

<body onload="onload_functions()">
<?PHP //echo phpinfo(); ?>
	<form>
	<div class="pageWrapper" id="pageWrapper">
		<script type="text/javascript">
			//alert('message');
		</script>
	</div>
	<input type="submit" value="Save" onclick="saveClicked()"/>
	<input type="hidden" value="saveresponse" name="action" />
	</form>
</body>
</html>