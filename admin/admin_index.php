<?php
// handle all POST and GET variables here
// set a bunch of variables to be used globally
// $action
// classes to include
$page = 'admin_index';
//include '../includes/authenticate.php';
include '../includes/ttrack_functions.php';
include '../includes/ttrack_db_sqlsrv.php';
include '../oauth/Client.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script type="text/javascript" src="../js/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="../js/admin.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/admin.css">
        <title>Ttrack Admin</title>
    </head>
    <body onload="onload_functions()" style="font-family:sans-serif">
        <div id="statusMessage"></div>
        <div id="menu" style="">
	        <div class="menuitem">
	            <a href="?menu=user&action=list">Users</a>
	        </div>
	        <div class="menuitem">
		        <a href="?menu=role&action=list">Roles</a>
	        </div>
	        <div class="menuitem">
		        <a href="?menu=importqtrics&action=list">Import Q'trcs</a>
	        </div>
	        <div class="menuitem">
		        <a href="?menu=contact&action=list">Contacts</a>
	        </div>
        </div>
        <div style="clear:both;">&nbsp;</div>
<?PHP
switch ($menu) {
    case "importqtrics":
        if (isset($upload)) {
            receiveFile();
        }
        showUploadedFiles();
        showUploadForm();
        getResponseSets();
    ?>
    <?PHP
    break;
    
    default :
        echo "fell through switch, menu is $menu";
        
}
?>
    </body>
</html>
<?PHP
function getResponseSets() {
    global $conn;
    $qry = "SELECT title, ResponseSet, id from dbo.q_response_sets ORDER BY title";
    $rst = sqlsrv_prepare($conn,$qry);
    sqlsrv_execute($rst);
    while ($row = sqlsrv_fetch_array($rst)) {
        echo $row['title'];
    }
}
function showUploadedFiles() {
    $path = "../batch/uploads/";
    $d = dir($path);
    //print_r($d);
    //echo "<br />";
    ?>
    <div id='dirList'>
    <img id="dirListIcon" src="../images/info.gif" width="25" height="25" /><br />
    <div id="dirListHelp" style="display:none;border-width:1px;border-style:solid;border-color:#cccccc;padding:5px">
        <div class="xmlOnlyFile">Only the XML file has been uploaded</div>
        <div class="csvOnlyFile">Only the CSV file has been uploaded</div>
        <div class="completeFile">Both XML and CSV have been uploaded</div>
    </div>
    <script type="text/javascript">
        $('#dirListIcon').bind('click',function() {
            $('#dirListHelp').toggle();
        });
    </script>
    
    <?PHP
    $files = array();
    while ($item = $d->read()) {
        $fileBase = substr($item, 0, strlen($item) - 4);
        $xmlFlag = strpos(strtolower($item), '.xml');
        $csvFlag = strpos(strtolower($item), '.csv');
        if ($xmlFlag != false) {
            if (array_key_exists($fileBase, $files)) {
                $files[$fileBase] += 1;
            } else {
                $files[$fileBase] = 1;
            }
        }
        if ($csvFlag != false) {
            if (array_key_exists($fileBase, $files)) {
                $files[$fileBase] += 2;
            } else {
                $files[$fileBase] = 2;
            }
        } 
    }
    $fileBaseArray = array_keys($files);
    foreach ($fileBaseArray as $fileBase) {
        switch ($files[$fileBase]) {
            case 1:
            echo '<div row="notclickable" class="xmlOnlyFile" >' . $fileBase . '</div>';
            break;
            case 2:
            echo '<div row="notclickable" class="csvOnlyFile" >' . $fileBase . '</div>';
            break;
            case 3:
            echo '<div row="clickable" class="completeFile" >' . $fileBase . '</div>';
        }
    }
    ?>
    <script type="text/javascript">
        $('div[row="clickable"]').bind('click',function(){
            var qualtricsFile = $(this)[0].innerHTML;
            getQualtricsInfo(qualtricsFile);
            
        });
        function getQualtricsInfo(qualtricsFile) {
            
            console.log('clicked, doing ajax');        
            $.get("adminajax.php",{fileBase: qualtricsFile, action: 'readQualtricsData'}).done(function(data) {alert(data)
            });
        }
    </script>
    </div>
    <?PHP
    //$listing = scandir($path);
    //print_r($listing);
}
function showUploadForm() {
    ?>
    <div id="uploadForm">
        <img id="uploadHelpIcon" src="../images/info.gif" width="25" height="25" />
        <div id="uploadHelpText" style="display:none">
        In Qualtrics -- from Results button, select "Download Data" Select Show answers
        as 'choice text', download XML. After XML is downloaded, select "dummy" dates so
        that no responses are returned, and select download CSV. (Default settings of '.'
        as decimal and ',' as field separator.) The XML file will contain all the responses,
        the .CSV will have question/sequence information.
        </div>
        <hr />
        <form enctype="multipart/form-data" action="" method="POST">
            <!-- MAX_FILE_SIZE must precede the file input field -->
            <input type="hidden" name="MAX_FILE_SIZE" value="6000000" />
            <input type="hidden" name="menu" value="importqtrics">
            <input type="hidden" name="upload" value="true">
            <!-- Name of input element determines name in $_FILES array -->
            Select .csv and .xml files to upload:<br /> <input id="filename" name="userfile" type="file" />
            <input type="submit" value="Send File" />
            <script type="text/javascript">
                $('#filename').bind('change', function() {
                alert('This file size is: ' + this.files[0].size/1024/1024 + "MB")
                });
                $('#uploadHelpIcon').bind('click',function() {
                    $('#uploadHelpText').toggle();
                });
            </script>
        </form>
    </div>
    <?PHP
}
function openQualtricsData ($fileBase) {
    // we assume there is a .csv and a .xml with that name in this directory
    $handle = fopen($fileBase . '.csv','r');
    $QIds = fgetcsv($handle);
    $ItemPrompts = fgetcsv($handle);
    fclose($handle);

    $xml = simplexml_load_file($fileBase . '.xml');
    $response = $xml->Response[0];
    $inputResponseSet = $response->ResponseSet;
    echo "<br /> Read $inputResponseSet from xml file <br />";
    //print_r($QIds);
    //print_r($ItemPrompts);
    $numFields = count($QIds);
    echo "<br />found $numFields Fields in top row <br />";
}
function receiveFile() {
    $uploaddir = '../batch/uploads/';
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        echo "File is valid, and was successfully uploaded.\n";
    } else {
        echo "Possible file upload attack!\n";
    }
}
?>