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
        <div id="listContainer">
            <div id="listHeader">List Header</div>
            <div id="listItemContainer"></div>
        </div>
    </div>
</body>
<script type="text/javascript">
    var ELEMENT_NODE = 1;
    var latestServerReturn = '';
    var focusElement = document.createElement('div');
    var templateId;
    var serverQueue =  new Array();
    var confirmationObject = new Object();
    var templateObject = new Object();
    var addItemButton;
    var entryForm;
    var saveButton;
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
        console.log('cancel clicked');
        showConfirmation();
    }
    function cancelConfirmation() {
        hideConfirmation();
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
	function confirmationTrue(yesButton) {
	    deleteItemConfirmed();
	    console.log('go ahead');
	}
	function deleteItemClicked(buttonClicked) {
	    console.log('delete item clicked');
	    confirmationObject.item = focusElement;
	    confirmationObject.action = 'delete';
	    showConfirmation();
	}
	function deleteItemConfirmed() {
	    var dataObject = new Object();
	    dataObject.action = 'deletetemplate';
	    dataObject.template_id = focusElement.getAttribute('templateid');
	    console.log(dataObject.template_id + ' is the template id');
	    generalAjaxCall(dataObject,'update');
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
	  		    latestServerReturn = xmlhttp.responseText;
  		        var returnData =  eval('(' + xmlhttp.responseText + ')');
	  		    //console.log(returnData.dataDestination + ' is data destination');
	  		    switch (returnData.dataDestination) {
                    case 'templates':
                        var listItems = returnData.dataObject;
	      		        populateListItemContainer(listItems, 'template');
                         // statements
                    break;
                    case 'update':
                        console.log('back from update');
                        break;
                    case 'templateDetail':
                        templateObject = returnData.dataObject;
                        listItems = templateObject.clusters;
                        //populateListItemContainer(listItems, 'cluster');
                        renderTemplate();
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
	function hideConfirmation() {
	    document.getElementById('confirmationMessage').style.display = 'none';
	    
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
        confirmationBox = document.createElement('div');
        confirmationBox.setAttribute('id','confirmationMessage');
        var messageDiv = document.createElement('div');
        var messageText = document.createTextNode('Confirm?');
        messageDiv.appendChild(messageText);
        confirmationBox.appendChild(messageDiv);
        var yesButton = document.createElement('button');
        yesButton.setAttribute('onclick','confirmationTrue(this)');
        var noButton = document.createElement('button');
        noButton.setAttribute('onclick','cancelConfirmation(this)');
        var yesText = document.createTextNode('Yes');
        var noText = document.createTextNode('No');
        yesButton.appendChild(yesText);
        noButton.appendChild(noText);
        confirmationBox.appendChild(yesButton);
        confirmationBox.appendChild(noButton);
        confirmationBox.style.display = 'none';
        document.getElementById('editRegionContainer').appendChild(confirmationBox);
        //var spacer = document.createElement('div');
        //spacer.style.clear = 'both';
        //var spacerContent = document.createTextNode('');
        //spacer.appendChild(spacerContent);
        //entryForm.appendChild(spacerContent);
        document.getElementById('editRegionContainer').appendChild(entryForm);
    }
    function keystrokeHandler(ev) {
        var keystroke = ev.which?ev.which:ev.keyCode;
        //console.log('keystroke is ' + keystroke);
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
    function logObject(object) {
        for (property in object) {
            console.log(property + ': ' + object[property]+'; ');
        }
    }
    function listItemDblClick(listItem) {
        setUnselected();
        focusElement = listItem;
        setSelected();
        listItemSelected(listItem);
    }
    function listItemSelected(listItem) {
        //called when Edit button is clicked, or item is double-clicked
	    switch (listItem.getAttribute('itemtype')) {
      		case 'template':
                //populateEntryForm(listItem);
                var dataObject = new Object();
                dataObject.action = 'gettemplatedetail';
                dataObject.templateid = listItem.getAttribute('templateid');
                serverQueue.push(dataObject, 'listItemContainer');
                checkServerQueue();
                buildBreadCrumbs(listItem);
            break;
       		case 'cluster':
       			// need to display title 
       			console.log('handling cluster');
       			populateEntryForm(listItem);
       					
            default:
                console.log('fell through listItemSelected switch with itemtype = ' + listItem.getAttribute('itemtype'));
                // default statements
        }
        
    }
	function listItemClicked(listItem) {
	    var newFocusItem = listItem;
	    console.log('list item clicked');
	    // focusElement no longer the focus, so turn style off
      setUnselected();
	    focusElement = newFocusItem;
	    setSelected();
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
	    document.getElementById('entryForm').setAttribute('itemid',entryItem.getAttribute('templateid'));
	    setFormClean(document.getElementById('entryForm'));
	    showEntryForm();
	}
	function populateListItemContainer(listItems, itemType) {
	    var destination = document.getElementById('listItemContainer');
	    destination.innerHTML = '';
	    document.getElementById('listHeader').innerHTML = itemType;
	    if (itemType == 'template') {
    	    for (var itemNum = 0; itemNum < listItems.length; itemNum ++) {
    	        var listItemElement = document.createElement('div');
    	        listItemElement.className = 'listItemDiv';
    	        listItemElement.setAttribute('itemtype',itemType);
    	        listItemElement.setAttribute('templateid', listItems[itemNum].template_id);
    	        listItemElement.setAttribute('onclick', 'listItemClicked(this);');
              listItemElement.setAttribute('ondblclick', "listItemDblClick(this);");
    	        listItemElement.setAttribute('description',listItems[itemNum].description + ' ');
    	        var listItemTextNode = document.createTextNode(listItems[itemNum].title);
    	        listItemElement.appendChild(listItemTextNode);
    	        
    	        destination.appendChild(listItemElement);
    	    }
    	} else if (itemType == 'cluster') {
    	    console.log('doing clusters, count: ' + listItems.length);
    	    for (clusterNum = 0;clusterNum < listItems.length;clusterNum ++) {
                 var listItemElement = makeListItemElement(listItems[clusterNum].cluster_header, 'cluster');
                 destination.appendChild(listItemElement);
    	         console.log(listItems[clusterNum].cluster_header);
    	    }
    	} else if (itemType == 'templateDetail') {
            // looking for clusters
            showEntryForm();
        }
        addItemButton.setAttribute('itemtype',itemType);
	    destination.appendChild(addItemButton);
	    var deleteItemButton = document.createElement('button');
	    deleteItemButton.setAttribute('onclick','deleteItemClicked(this)');
        var deleteItemImage = document.createElement('img');
        deleteItemImage.setAttribute('src', 'images/minus_sign_23x21.gif');
        deleteItemImage.setAttribute('width','23');
        deleteItemImage.setAttribute('height','21');
        deleteItemButton.setAttribute('align','right');
        deleteItemImage.style.cursor = 'pointer';
        deleteItemButton.appendChild(deleteItemImage);
        destination.appendChild(deleteItemButton);
	}
	function renderCluster(clusterObj) {
	    var editRegion = document.getElementById('editRegionContainer');
        var clusterDiv = document.createElement('div');
        var clusterTitleDiv = document.createElement('div');
        var clusterTitleText = document.createTextNode(clusterObj.cluster_header);
        if (clusterObj.hasOwnProperty('clusters')) {
            
            console.log(clusterObj.clusters.length + ' clusters in cluster');
        } else {
            console.log('no clusters here');
        }
        if (clusterObj.hasOwnProperty('items')) {
            for (itemNum = 0; itemNum < clusterObj.items.length; itemNum++) {
                renderItem(clusterObj.items[itemNum]);
            }
            console.log(clusterObj.items.length + ' items in cluster');
        } else {
            console.log('no items here');
        }
        clusterTitleDiv.appendChild(clusterTitleText);
        clusterDiv.appendChild(clusterTitleDiv);
        editRegion.appendChild(clusterDiv);
	}
	function renderItem(itemObj) {
	    var editRegion = document.getElementById('editRegionContainer');
	    var itemDiv = document.createElement('div');
	    var itemPrompt = document.createTextNode(itemObj.item_prompt);
	    itemDiv.appendChild(itemPrompt);
	    editRegion.appendChild(itemDiv);
	    if (itemObj.hasOwnProperty('options')) {
	        for (optionNum = 0; optionNum < itemObj.options.length; optionNum++) {
                var optionDiv = document.createElement('div');
                var optionLabel = document.createTextNode(itemObj.options[optionNum].option_label);
                optionDiv.appendChild(optionLabel);
                editRegion.appendChild(optionDiv);
            }
	    } else {
	        console.log('item has no options');
	    }
	}
    function renderTemplate() {
        console.log('rendering template');
        console.log(templateObject.template_title + ' is the template title');
        var editRegion = document.getElementById('editRegionContainer');
        var templateTitleDiv = document.createElement('div');
        var templateTitleText = document.createTextNode(templateObject.template_title);
        templateTitleDiv.appendChild(templateTitleText);
        editRegion.appendChild(templateTitleDiv);
        var templateDescriptionDiv = document.createElement('div');
        var templateDescriptionText = document.createTextNode(templateObject.template_description);
        templateDescriptionDiv.appendChild(templateDescriptionText);
        editRegion.appendChild(templateDescriptionDiv);
        var clusterList = templateObject.clusters;
        console.log(latestServerReturn + ' was from server');
        for (var clusterNum = 0;clusterNum < clusterList.length;clusterNum ++) {
            renderCluster(clusterList[clusterNum]);
        }
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
	    //console.log('dataObject title is ' + dataObject.template_title);
        serverQueue.push(dataObject);
        serverQueue.push('insertdata');
        checkServerQueue();
        setFormClean(buttonClicked.parentNode);
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
	    saveButton.style.display = 'none';
	    formDiv.style.backgroundColor = '#eeffee';
	}
	function setFormDirty(formDiv) {
	    console.log('setting dirty');
	    saveButton.style.display = 'block';
	    formDiv.style.backgroundColor = '#ffeeee';
	}
	function setSelected() {
	    focusElement.style.backgroundColor = "#ffdddd";
	}
	function setUnselected() {
	    focusElement.style.backgroundColor = "#ddffdd";
	}
	function showConfirmation() {
	    document.getElementById('confirmationMessage').style.display = 'block';
	    
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
		font-family:sans;
		font-size:14px;
		font-weight:bold;
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
		    width:85%;
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
		#listContainer {
		    width:22%;
		    height:100%;
		    display:block;
		    float:right;
		    border: 1px solid #888;
		    border-radius:10px;
		    background-color:#efe;
		}
		#listItemContainer {
		    
		    display:block;
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