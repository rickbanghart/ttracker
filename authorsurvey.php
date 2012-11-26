<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
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
	foreach ($_POST as $key=>$value) {
		$$key = $value;
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	foreach ($_GET as $key=>$value) {
		$$key = $value;
	}
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Author Survey</title>
</head>
<body onkeydown="keystrokeHandler(event)">
    <div id="contentContainer">
        <div id="statusMessage"></div>
        <div id="breadCrumbContainer"></div>
        <div id="editRegionContainer"></div>
        <div id="listItemContainer"></div>
    </div>
</body>
<script type="text/javascript">
    var ELEMENT_NODE = 1;
    var focusElement = document.createElement('div');
    var templateId;
    var serverQueue =  new Array();
    var addItemButton;
    var entryForm;
    var confirmationBox;
    initialize_page();
    function addItemButtonClicked(buttonClicked) {
        var entryForm = document.getElementById('entryForm');
        entryForm.setAttribute('itemid','0');
        entryForm.setAttribute('itemtype',buttonClicked.getAttribute('itemtype'));
        clearEntryForm();
        showEntryForm();
    }
    function buildBreadCrumbs(listItem) {
        var destination = document.getElementById('breadCrumbContainer');
        destination.innerHTML = '';
        var showAllButton = document.createElement('button');
        showAllButton.setAttribute('onclick','breadCrumbButtonClicked(this)');
        //showAllButton.onclick = 'showAllButtonClicked(this)';
        //showAllButton.setAttribute('type','button');
        var showAllButtonTextNode = document.createTextNode('Templates');
        showAllButton.appendChild(showAllButtonTextNode);
        destination.appendChild(showAllButton);
        switch (listItem.getAttribute('itemtype')) {
            case 'template':
                var templateButton = document.createElement('button');
                var templateButtonTextNode = document.createTextNode(listItem.childNodes[0].nodeValue);
                templateButton.appendChild(templateButtonTextNode);
                destination.appendChild(templateButton);
            break;
            default:
                // default statements
        }
    }
	function breadCrumbButtonClicked(buttonClicked) {
		clearEntryForm(); 
		hideEntryForm();   
	    resetData();
	}
    function cancelButtonClicked(buttonClicked) {
        showConfirmation();
    }
    function clearEntryForm() {
        document.getElementById('entryForm').style.display = 'none';
    }
	function checkServerQueue() {
		if (serverQueue.length > 0) {
			var params = serverQueue.shift();
			var destination = serverQueue.shift();
			generalAjaxCall(params, destination);
		}
	}
	function clearStatus() {
		document.getElementById('statusMessage').style.display = 'none';
	}
	function clearEntryForm() {
	    document.getElementById('entryFormTextArea').value = '';
	    document.getElementById('entryFormTitleInput').value = '';
	}
	function focusHandler(focusElement) {
	    console.log('focus handler');
	    focusElement.style.borderColor = "#ff8888";
	}
    function generalAjaxCall(params, destination){
		// params is an array of key/value pairs to be sent in URL
		// destination is the element id that displays returned data
		//console.log('retrieving ' + destination);
		//console.log('destination now has ' +  document.getElementById(destination).innerHTML);
		showStatus('Retrieving ' + destination);
		var destinationReceived = destination;
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
	  		xmlhttp=new XMLHttpRequest();
	  	} else {
			// code for IE6, IE5
	  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()  {
	  		if (xmlhttp.readyState==4 && xmlhttp.status==200)  {
	  		    //console.log(xmlhttp.responseText + ' back from server');
  		        var returnData =  eval('(' + xmlhttp.responseText + ')');
	  		    //console.log(returnData.dataDestination + ' is data destination');
	  		    switch (returnData.dataDestination) {
                    case 'templates':
                        var listItems = returnData.dataObject;
	      		        populateListItemContainer(listItems, 'template');
                         // statements
                    break;
                    case 'templateDetail':
                        console.log('got templateDetail');
                        var listItems = returnData.dataObject;
                        populateListItemContainer(listItems, 'templateDetail');
                    break;
                    default:
                        // default statements
                }
	  		        //console.log listItems(0).template_id + ' template_id';
	  		    
				if (destination == 'multiple') {
					// have to parse returned xml to find destination for each <content destination=""> element
					// <content> elements live within <lessondetails> root element
					var returnedXML = xmlhttp.responseXML;
					try {
						var contentItemsXMLList = returnedXML.childNodes[0].childNodes;
					} catch(err) {
						console.log(xmlhttp.responseText);
						console.log('ERROR: ' + err.message);
						console.log(returnedXML.nodeType);
						console.log(returnedXML.nodeName);
					}
				}
			}
			clearStatus();
			checkServerQueue();
		
		}
			
		var requestParams = '?';
		did_one = false;
		for (var key in params) {
		    if (did_one) {
		        requestParams += '&';
		    }
		    did_one = true;
			requestParams += key + '=' + params[key];
		}
		//xmlhttp.open("GET","ajax.php?token=$token;action=getstandardsselect;filterstring=" + standard_filter,true);
		xmlhttp.open("GET","ajax.php" + requestParams,true);
		//console.log('https://ajax.php' + requestParams);
		xmlhttp.send();
	}
	function hideEntryForm() {
	    document.getElementById('entryForm').style.display = 'none';
	}
    function initialize_page() {
        var dataObject = new Object();
        dataObject.action = 'get_templates';
        serverQueue.push(dataObject);
        serverQueue.push('listItemContainer');
        checkServerQueue();
        var dummy = document.createElement('div');
        dummy.setAttribute('itemType','dummy');
        buildBreadCrumbs(dummy);
        addItemButton = document.createElement('button');
        //var addItemButtonTextNode = document.createTextNode('Add');
        var addItemImage = document.createElement('img');
        addItemImage.setAttribute('src', 'images/plus_sign_23X21.gif');
        addItemImage.setAttribute('width','23');
        addItemImage.setAttribute('height','21');
        addItemButton.appendChild(addItemImage);
//        addItemButton.appendChild(addItemButtonTextNode);
        addItemButton.setAttribute('id','addItemButton');
        addItemButton.setAttribute('onclick','addItemButtonClicked(this)');
        entryForm = document.createElement('div');
        entryForm.setAttribute('id','entryForm');
        entryFormTitleLabelDiv = document.createElement('div');
        entryFormTitleLabel = document.createTextNode('Title');
        entryFormTitleLabelDiv.appendChild(entryFormTitleLabel);
        entryForm.appendChild(entryFormTitleLabelDiv);
        entryFormTitleInput = document.createElement('input');
        entryFormTitleInput.setAttribute('id', 'entryFormTitleInput');
        entryFormTitleInput.setAttribute('type','text');
        entryFormTitleInput.setAttribute('fieldname','template_title');
        entryFormTitleInput.setAttribute('onkeypress','setFormDirty(this.parentNode)');
        entryFormTitleInput.style.width = '100%';
        entryForm.appendChild(entryFormTitleInput);
        entryFormDescriptionLabelDiv = document.createElement('div');
        entryFormDescriptionLabel = document.createTextNode('Description');
        entryFormDescriptionLabelDiv.appendChild(entryFormDescriptionLabel);
        entryForm.appendChild(entryFormDescriptionLabelDiv);
        entryFormTextArea = document.createElement('textarea');
        entryFormTextArea.setAttribute('id','entryFormTextArea');
        entryFormTextArea.setAttribute('fieldname','template_description');
        entryFormTextArea.setAttribute('onkeypress','setFormDirty(this.parentNode)');
        entryFormTextArea.style.width = '100%';
        entryFormTextArea.style.width = '100%';
        entryForm.appendChild(entryFormTextArea);
        cancelButton = document.createElement('button');
        cancelButton.setAttribute('type','button');
        cancelButton.setAttribute('onclick','cancelButtonClicked(this)');
        cancelButtonText = document.createTextNode('Cancel');
        cancelButton.appendChild(cancelButtonText);
        cancelButton.style.float = 'left';
        entryForm.appendChild(cancelButton);
        saveButton = document.createElement('button');
        saveButton.setAttribute('type','button');
        saveButton.setAttribute('onclick','saveButtonClicked(this)');
        saveButtonText = document.createTextNode('Save');
        saveButton.appendChild(saveButtonText);
        saveButton.style.float = 'right';
        entryForm.appendChild(saveButton);
        var brElement = document.createElement('br');
        brElement.style.clear = 'both';
        entryForm.appendChild(brElement);
        var spacer = document.createElement('div');
        spacer.style.clear = 'both';
        var spacerContent = document.createTextNode('&nbsp;');
        spacer.appendChild(spacerContent);
        entryForm.appendChild(spacerContent);
        document.getElementById('editRegionContainer').appendChild(entryForm);
    }
    function keystrokeHandler(ev) {
        var keystroke = ev.which?ev.which:ev.keyCode;
        console.log('keystroke is ' + keystroke);
        switch (keystroke) {
            case 38:
                //up arrow
                // set current focusElement to unselected
                setUnselected();
                focusElement = focusElement.previousSibling?focusElement.previousSibling:focusElement;
                setSelected();
                console.log('index is ' + focusElement.previousSibling);
            break;
            case 40:
                setUnselected();
                focusElement = focusElement.nextSibling?focusElement.nextSibling:focusElement;
                setSelected();
                break;
                //down arrow
            case 13:
                // enter (return)
                listItemSelected(focusElement);
                break;
            default:
                // default statements
        }
    }
    function listItemSelected(listItem) {
        //called when Edit button is clicked, or item is double-clicked
	    switch (listItem.getAttribute('itemtype')) {
      		case 'template':
                var dataObject = new Object();
                dataObject.action = 'gettemplatedetail';
                dataObject.templateid = listItem.getAttribute('templateid');
                serverQueue.push(dataObject, 'listItemContainer');
                checkServerQueue();
                buildBreadCrumbs(listItem);
                populateEntryForm(listItem);
            break;
            default:
                // default statements
        }
	    console.log('list item selected');
        
    }
	function listItemClicked(listItem) {
	    var newFocusItem = listItem;
	    // focusElement no longer the focus, so turn style off
        setUnselected();
	    focusElement = listItem;
	    setSelected();
	    console.log(focusElement.getAttribute('itemtype'));
	    listItem.focus();
	    console.log(document.activeElement);
	    console.log('list item clicked');
	}
	function makeListItemElement(titleText, itemType) {
        var listItemElement = document.createElement('div');
        listItemElement.className = 'listItemDiv';
        listItemElement.setAttribute('itemtype',itemType);
        listItemElement.setAttribute('templateid', templateId);
        listItemElement.setAttribute('onclick','listItemClicked(this)');
        listItemElement.setAttribute('tabindex', '0');
        var listItemTextNode = document.createTextNode(titleText);
        listItemElement.appendChild(listItemTextNode);
        return(listItemElement)
	}
	function populateEntryForm(entryItem) {
	    document.getElementById('entryFormTitleInput').value = entryItem.childNodes[0].nodeValue;
	    document.getElementById('entryFormTextArea').value = entryItem.getAttribute('description');
	    setFormClean(document.getElementById('entryForm'));
	}
	function populateListItemContainer(listItems, itemType) {
	    var destination = document.getElementById('listItemContainer');
	    destination.innerHTML = '';
	    if (itemType == 'template') {
    	    for (var itemNum = 0; itemNum < listItems.length; itemNum ++) {
    	        var listItemElement = document.createElement('div');
    	        listItemElement.className = 'listItemDiv';
    	        listItemElement.setAttribute('itemtype',itemType);
    	        listItemElement.setAttribute('templateid', listItems[itemNum].template_id);
    	        listItemElement.setAttribute('onclick','listItemClicked(this)');
                listItemElement.setAttribute('onfocus','focusHandler(this)');
    	        listItemElement.setAttribute('description',listItems[itemNum].description + ' ');
    	        var listItemTextNode = document.createTextNode(listItems[itemNum].title);
    	        listItemElement.appendChild(listItemTextNode);
    	        
    	        destination.appendChild(listItemElement);
    	    }
    	} else if (itemType == 'templateDetail') {
            // looking for clusters
            var clusters = listItems.clusters;
            if (clusters) {
                for (clusterNum = 0; clusterNum < clusters.length; clusterNum++) {
                     var listItemElement = makeListItemElement('title here', 'cluster');
                     destination.appendChild(listItemElement);
                }
            }
            showEntryForm();
        }
        addItemButton.setAttribute('itemtype',itemType);
	    destination.appendChild(addItemButton);
        var deleteItemImage = document.createElement('img');
        deleteItemImage.setAttribute('src', 'images/minus_sign_23x21.gif');
        deleteItemImage.setAttribute('width','23');
        deleteItemImage.setAttribute('height','21');
        deleteItemImage.setAttribute('align','right');
        deleteItemImage.style.cursor = 'pointer';
        destination.appendChild(deleteItemImage);
	}
	function resetData() {
        var dataObject = new Object();
        dataObject.action = 'get_templates';
        serverQueue.push(dataObject);
        serverQueue.push('listItemContainer');
        checkServerQueue();
        var dummy = document.createElement('div');
        dummy.setAttribute('itemType','dummy');
        buildBreadCrumbs(dummy);
	}
	function saveButtonClicked(buttonClicked) {
	    var dataObject = scrapeForm(buttonClicked.parentNode);
	    dataObject.template_id = buttonClicked.parentNode.getAttribute('itemid');
	    dataObject.action = 'savetemplate';
	    console.log('dataObject title is ' + dataObject.template_title);
      serverQueue.push(dataObject);
      serverQueue.push('insertdata');
      checkServerQueue();
	}
	function scrapeForm(formItem) {
	    var dataObject = new Object();
	    for (i = 0; i < formItem.childNodes.length; i++) {
	        var elementCheck = formItem.childNodes[i];
            if (elementCheck.nodeType == ELEMENT_NODE) {
                if (elementCheck.hasAttributes() == true) {
                    var attributeList = elementCheck.attributes;
                    for (attributeNum = 0; attributeNum < attributeList.length; attributeNum++) {
                        if (attributeList[attributeNum].nodeName == 'fieldname') {
                            console.log('found fieldname ' )
                            console.log('name is ' + attributeList[attributeNum].nodeValue);
                            console.log('the value of the element is ' + elementCheck.value);
                            dataObject[attributeList[attributeNum].nodeValue] = elementCheck.value;
                        }
                    }
                    
                }
            }
        }
        return(dataObject);
	}
	function setFormClean(formDiv) {
	    console.log('setting clean');
	    formDiv.style.backgroundColor = '#eeffee';
	}
	function setFormDirty(formDiv) {
	    console.log('setting dirty');
	    formDiv.style.backgroundColor = '#ffeeee';
	}
	function setSelected() {
	    focusElement.style.backgroundColor = "#ffdddd";
	}
	function setUnselected() {
	    focusElement.style.backgroundColor = "#ddffdd";
	}
	function showConfirmation() {
	    document.getElementById('confirmationBox');
	}
	function showEntryForm() {
	    entryForm.style.display = 'block';
	}
	function showStatus(message) {
		var statusMessage = document.getElementById('statusMessage');
		statusMessage.innerHTML = '<div>' + message + '</div>';
		statusMessage.style.display = 'block';
		var containerHeight = statusMessage.offsetHeight;
		var messageHeight = statusMessage.firstChild.offsetHeight;
		var verticalOffset = (containerHeight - messageHeight) / 2;
		statusMessage.firstChild.style.top = verticalOffset + 'px';
		statusMessage.firstChild.style.position = 'relative';
	}
</script>
<style>
    button {
        border: 1px solid #888;
        border-radius:3px;
        background-color:#888;
        color:#fff;
    }
    button:hover {
        background-color:#ddd;
    }
    #breadCrumbContainer {
        width:100%;
        height:35px;
        display:block;
        border: 1px solid #888;
        border-radius:10px;
        background-color:#eef;
    }
    #contentContainer {
        font: normal 13px Arial,sans;
        width:100%;
        height:750px;
        display:block;
        background-color:#ccc;
        border: 1px solid #888;
        border-radius: 10px;
    }
	#confirmationMessage {
		position:absolute;
		background-color:#ffd;
		padding:30px;
		width:150px;
		height:100px;
		border-style:solid;
		border-width:1px;
		border-color:#888;
		border-radius:10px;
		left:250px;
		top:190px;
		display:block;
	}
	#editRegionContainer {
	    width:75%;
	    height:100%;
        display:block;
	    border: 1px solid #888;
	    border-radius:10px;
	    background-color:#fee;
	    float:left;
	}
	#entryForm {
	    width:450px;
	    padding:4px;
	    border: 1px solid #888;
	    border-radius:10px;
	    display:none;
	    background-color:#eee;
	}
	div.listItemDiv {
	    width:95%;
	    cursor:pointer;
	    padding:3px;
	    margin:3px;
	    background-color:#bbc;
	    border: 1px solid #99a;
	    border-radius:3px;
	}
	div.listItemDiv:hover {
	    background-color:#dde;
	}
	#listItemContainer {
	    width:22%;
	    height:100%;
	    display:block;
	    float:right;
	    border: 1px solid #888;
	    border-radius:10px;
	    background-color:#efe;
	}
    #statusMessage {
        position:absolute;
        top:100px;
        left:100px;
        border-style:solid;
        border-color:#A2A2A2;
        border-width:1px;
        display:none;
        background-color:#ffeeee;
        width:200px;
        height:100px;
    }
    
</style>

</html>
<?php
// Make this generic and put in include file
// return array of hash (in Perlese)
// columns are template_id, title, description
?>