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
	<link rel="stylesheet" type="text/css" href="css/set.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Set Form</title>
</head>
<script type="text/javascript">
	function submitForm(buttonClicked) {
		console.log("submit clicked");
		saveResponse();
	}
	
	function ajaxCall (params) {
		console.log('in the ajax script');
		var xmlhttp;
		var async = false;
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()  {
			console.log('ready state changed');
		  	if (xmlhttp.readyState==4 && xmlhttp.status==200)  {
				console.log('200 returned');
				console.log('server returned: ' + xmlhttp.responseText)
		    }
		}
		xmlhttp.open("POST","ajax.php",async);
		//Send the proper header information along with the request
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(params);
;
	}
	function saveResponse() {
		// talk to database from here
		// save current state of radio buttons, check boxes, text entry fields
		// *** QUESTION: Should the undo function be enabled?
		 
		ajaxCall('action=saveresponse;l1=2');
	}	
</script>
<body>
	<form>
	<div class="pageWrapper">
	<div class="questionGroup">
	<div class="groupHead">Questions about Your Learning</div>
	<div class="instrumentItem">
		<div class="itemPrompt">My knowledge and understanding of diversity increased.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l1" value="0" />
				<input type="radio" name="l1" value="1" />
				<input type="radio" name="l1" value="2" />
				<input type="radio" name="l1" value="3" />
				<input type="radio" name="l1" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">My knowledge and understanding of social justice increased.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l2" value="0" />
				<input type="radio" name="l2" value="1" />
				<input type="radio" name="l2" value="2" />
				<input type="radio" name="l2" value="3" />
				<input type="radio" name="l2" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">My appreciation of multiple perspectives has deepened.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l3" value="0" />
				<input type="radio" name="l3" value="1" />
				<input type="radio" name="l3" value="2" />
				<input type="radio" name="l3" value="3" />
				<input type="radio" name="l3" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I became more skilled with educational technology.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l4" value="0" />
				<input type="radio" name="l4" value="1" />
				<input type="radio" name="l4" value="2" />
				<input type="radio" name="l4" value="3" />
				<input type="radio" name="l4" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I increased my capacity to teach about diversity.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l5" value="0" />
				<input type="radio" name="l5" value="1" />
				<input type="radio" name="l5" value="2" />
				<input type="radio" name="l5" value="3" />
				<input type="radio" name="l5" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I improved my ability to communicate clearly about diversity issues to multiple stakeholders.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l6" value="0" />
				<input type="radio" name="l6" value="1" />
				<input type="radio" name="l6" value="2" />
				<input type="radio" name="l6" value="3" />
				<input type="radio" name="l6" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I was able to connect the course materials to my own interests.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l7" value="0" />
				<input type="radio" name="l7" value="1" />
				<input type="radio" name="l7" value="2" />
				<input type="radio" name="l7" value="3" />
				<input type="radio" name="l7" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I was intellectually stimulated in this course.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l8" value="0" />
				<input type="radio" name="l8" value="1" />
				<input type="radio" name="l8" value="2" />
				<input type="radio" name="l8" value="3" />
				<input type="radio" name="l8" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I had opportunities to learn from others in this course.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="l9" value="0" />
				<input type="radio" name="l9" value="1" />
				<input type="radio" name="l9" value="2" />
				<input type="radio" name="l9" value="3" />
				<input type="radio" name="l9" value="4" />
			</div>
		</div>
	</div>	
	</div>
	<div class="questionGroup">
	<div class="groupHead">Questions about the Course</div>
	<div class="instrumentItem">
		<div class="itemPrompt">I had opportunities to contribute my ideas in this course.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c1" value="0" />
				<input type="radio" name="c1" value="1" />
				<input type="radio" name="c1" value="2" />
				<input type="radio" name="c1" value="3" />
				<input type="radio" name="c1" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The organization of the course made sense to me.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c2" value="0" />
				<input type="radio" name="c2" value="1" />
				<input type="radio" name="c2" value="2" />
				<input type="radio" name="c2" value="3" />
				<input type="radio" name="c2" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The course materials were thought provoking and educational.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c3" value="0" />
				<input type="radio" name="c3" value="1" />
				<input type="radio" name="c3" value="2" />
				<input type="radio" name="c3" value="3" />
				<input type="radio" name="c3" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The course topics were interesting.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c4" value="0" />
				<input type="radio" name="c4" value="1" />
				<input type="radio" name="c4" value="2" />
				<input type="radio" name="c4" value="3" />
				<input type="radio" name="c4" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I would recommend this course to others.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c5" value="0" />
				<input type="radio" name="c5" value="1" />
				<input type="radio" name="c5" value="2" />
				<input type="radio" name="c5" value="3" />
				<input type="radio" name="c5" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">I gained experience participating as a member of a professional community of educators.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c6" value="0" />
				<input type="radio" name="c6" value="1" />
				<input type="radio" name="c6" value="2" />
				<input type="radio" name="c6" value="3" />
				<input type="radio" name="c6" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The reading assignments were educational and worth my time.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c7" value="0" />
				<input type="radio" name="c7" value="1" />
				<input type="radio" name="c7" value="2" />
				<input type="radio" name="c7" value="3" />
				<input type="radio" name="c7" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The discussions were educational and worth my time.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c8" value="0" />
				<input type="radio" name="c8" value="1" />
				<input type="radio" name="c8" value="2" />
				<input type="radio" name="c8" value="3" />
				<input type="radio" name="c8" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The writing assignments were educational and worth my time.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c9" value="0" />
				<input type="radio" name="c9" value="1" />
				<input type="radio" name="c9" value="2" />
				<input type="radio" name="c9" value="3" />
				<input type="radio" name="c9" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The topics of the course were relevant for me.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c10" value="0" />
				<input type="radio" name="c10" value="1" />
				<input type="radio" name="c10" value="2" />
				<input type="radio" name="c10" value="3" />
				<input type="radio" name="c10" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt"> The materials and assignments of the course matched the stated course objectives.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c11" value="0" />
				<input type="radio" name="c11" value="1" />
				<input type="radio" name="c11" value="2" />
				<input type="radio" name="c11" value="3" />
				<input type="radio" name="c11" value="4" />
			</div>                          
		</div>
	</div>
	<div class="instrumentItem">
		<div class="itemPrompt">The course provided a variety of modes of engagement (e.g., text; graphics; media; analysis; creativity).</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c12" value="0" />
				<input type="radio" name="c12" value="1" />
				<input type="radio" name="c12" value="2" />
				<input type="radio" name="c12" value="3" />
				<input type="radio" name="c12" value="4" />
			</div>
		</div>
	</div>
	<div class="instrumentItem">
		<div class="itemPrompt">The structure of course requirements supported my learning.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="c12" value="0" />
				<input type="radio" name="c12" value="1" />
				<input type="radio" name="c12" value="2" />
				<input type="radio" name="c12" value="3" />
				<input type="radio" name="c12" value="4" />
			</div>
		</div>
	</div>
	
	</div>
	<div class="questionGroup">
	<div class="groupHead">Questions about Your Instructor</div>
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor was available and responded to me.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i1" value="0" />
				<input type="radio" name="i1" value="1" />
				<input type="radio" name="i1" value="2" />
				<input type="radio" name="i1" value="3" />
				<input type="radio" name="i1" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor supported my learning in this course.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i2" value="0" />
				<input type="radio" name="i2" value="1" />
				<input type="radio" name="i2" value="2" />
				<input type="radio" name="i2" value="3" />
				<input type="radio" name="i2" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor showed respect for me and my work.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i3" value="0" />
				<input type="radio" name="i3" value="1" />
				<input type="radio" name="i3" value="2" />
				<input type="radio" name="i3" value="3" />
				<input type="radio" name="i3" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor's comments on my work were constructive.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i4" value="0" />
				<input type="radio" name="i4" value="1" />
				<input type="radio" name="i4" value="2" />
				<input type="radio" name="i4" value="3" />
				<input type="radio" name="i4" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor welcomed a variety of viewpoints into the course.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i5" value="0" />
				<input type="radio" name="i5" value="1" />
				<input type="radio" name="i5" value="2" />
				<input type="radio" name="i5" value="3" />
				<input type="radio" name="i5" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor's communications were clear and easy to understand.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i6" value="0" />
				<input type="radio" name="i6" value="1" />
				<input type="radio" name="i6" value="2" />
				<input type="radio" name="i6" value="3" />
				<input type="radio" name="i6" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor set reasonable expectations.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i7" value="0" />
				<input type="radio" name="i7" value="1" />
				<input type="radio" name="i7" value="2" />
				<input type="radio" name="i7" value="3" />
				<input type="radio" name="i7" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor accommodated my learning situation.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i8" value="0" />
				<input type="radio" name="i8" value="1" />
				<input type="radio" name="i8" value="2" />
				<input type="radio" name="i8" value="3" />
				<input type="radio" name="i8" value="4" />
			</div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">The instructor encouraged multiple forms of participation in the course.</div>
		<div class="likertResponse">
			<div class="likertAnchors"><span>Strongly Agree</span><span>Agree</span><span>Neutral</span><span>Disgree</span><span>Strongly Disagree</span></div>
			<div class="likertRadio">
				<input type="radio" name="i9" value="0" />
				<input type="radio" name="i9" value="1" />
				<input type="radio" name="i9" value="2" />
				<input type="radio" name="i9" value="3" />
				<input type="radio" name="i9" value="4" />
			</div>
		</div>
	</div>
</div>
	<div class="questionGroup">
	<div class="groupHead">Comments</div>

	<div class="instrumentItem">
		<div class="itemPrompt">Comments about your learning in this course</div>
		<textarea name="learningDomments" class="textInput"></textarea>
	</div>
	<div class="instrumentItem">
		<div class="itemPrompt">Comments about this course</div>
		<textarea name="courseComments" class="textInput"></textarea>
	</div>
	<div class="instrumentItem">
		<div class="itemPrompt">Comments about the instructor of course</div>
		<textarea name="instructorComments" class="textInput"></textarea>
	</div>
	<div class="instrumentItem">
		<div class="itemPrompt">Other comments</div>
		<textarea name="otherDomments" class="textInput"></textarea>
	</div>
	</div>
	<div class="buttonPanel">
		<input type="button" value="submit" onclick="submitForm(this)" />
	</div>
</div>
</form>
</body>
</html>