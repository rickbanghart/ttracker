<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
session_start();
include('includes/ttrack.php'); // sets data connection $conn
include('includes/authenticate.php'); // authentication functions
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
	<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/aop.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Set Form</title>
<script type="text/javascript">
	$(document).ready(initialize);
	function initialize() {
		console.log('initialized');
		$('div [class="expander"]').click(itemClicked);
	}
	function itemClicked() {
		if ($(this).next().hasClass("worksheet")) {
			$(this).text("hide worksheet");
			$(this).next().removeClass("worksheet").addClass("showElement");
		} else {
			$(this).text("show worksheet");
			$(this).next().removeClass("showElement").addClass("worksheet");
		}
		console.log('item clicked');
	}
	function submitForm(buttonClicked) {
    	var $form = $('form');
    	var serializedData = $form.serialize();
		//console.log(serializedData + ' serialized');
		$.ajax({
  			type: "POST",
  			url: "ajax.php",
  			data: serializedData,
  			success: success
		});
		return false;
		
	}
	function success(data, status, jqxhr) {
		console.log(status + ' is status');
		console.log(jqxhr.responseText + ' is response text');
		console.log('success from ajax');
	}
	
</script>
</head>
<body>
	<form>
	<input type="hidden" name="action" value="saveresponse" />
	<div class="pageWrapper">
	<div class="questionGroup">
	<div class="groupHead">LAET Assessment of Intern Progress: A tool for discussion</div>
	<div class="dateDisplay">Date: Feb 18, 2013</div>
	<div class="examinee">Intern: Firstname Lastname</div>
	<div class="examiner">Completed by: Firstname Lastname</div>
	<div class="instrumentItem">
		<div class="itemPrompt">Standard 1. Acts as an educated person: Communicates effectively; shows that s/he values learning; promotes both individual responsibility and individual rights; models respect both individual diversity and for community; models knowledge of American government and economics; models global perspectives.</div>
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
		<div class="expander">show worksheet</div>
		<div class="worksheet">
			<div class="worksheetPrompts">
			a. Communicates effectively: listening, speaking, writing, reading, and visual.<br />
			b. Shows that s/he values education for a pluralistic society.<br />
			c. Promotes both individual responsibility and individual rights.<br />
			d. Models respect both for individual diversity and for community.<br />
			e. Models knowledge of American government and the American economic system.<br />
			f. Offers global and international perspectives on topics, questions, and issues.<br />
			</div>
			<div class="worksheetPrompts"><textarea name="weakness"></textarea></div>
			<div class="worksheetPrompts"><textarea name="strength"></textarea></div>
			<div style="clear:both"></div>
		</div>
	</div>	
	<div class="instrumentItem">
		<div class="itemPrompt">Standard 2. Teaches elementary subject matters: Researches and validly teaches subject matter through short-range and long-range planning; connects subject matter to the world beyond school; promotes critical and higher order thinking; promotes independent learning and problem solving; engages students in inquiry and promotes curiosity; models and coaches analysis, synthesis, evaluation of ideas, skills and information.</div>
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
		<div class="itemPrompt">Standard 3. Works with students as individuals:  Respects, cares for, and communicates with all students, and holds high expectations; adapts the curriculum to them, setting measurable goals; employs multiple strategies for teaching them; motivates and engages all students; includes, accommodates, and differentiates instruction; assesses and adjusts instruction to serve individuals.</div>
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
		<div class="itemPrompt">Standard 4: Organizes and manages a class: Organizes and introduces rules and routines;  uses a range of participation structures;  promotes shared values and expectations for learning; teaches students how to participate; responds to student inattention and misbehavior; assesses class interaction and adjusts the organization as needed.</div>
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
		<div class="itemPrompt">Standard 5. Uses an equipped classroom: Designs the classroom for safety and learning: uses multiple modes and media for instruction: uses information technology for instruction and assessment: teaches students to take care of the room; assesses activity and adapts the room to support students and promote learning.</div>
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
		<div class="itemPrompt">Standard 6. Joins a faculty and school:  Attends to school policies; works with other teachers and administrators as needed; participates in school assessment, evaluation, and grading processes; participates in formal and informal professional learning for and by teachers.</div>
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
		<div class="itemPrompt">Standard 7. Engages families and community:  Communicates with parents and guardians about students' activity and learning; recognizes and responds to diverse family structures; uses community history, issues, and resources in teaching; recognizes patterns of evidence that indicate threats to students' welfare; advocates for students’ interests. </div>
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
		<div class="itemPrompt">Standard 8. Teaches deliberately and learns from experience:  Understands and accepts a teacher's responsibilities; employs a thoughtful and informed philosophy of teaching; exhibits a teacher's thoughtful and professional manner; exercises good judgment in planning and teaching; habitually reflects on and makes use of feedback to improve teaching; deliberately draws upon professional education as a resource; uses assessments, feedback, and continuing education to improve per-formance.</div>
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
	</div> <!-- end of question group -->

	<div class="buttonPanel">
		<input type="button" value="submit" onclick="submitForm(this)" />
	</div>
</div> <!-- end of page wrapper -->
</form>
</body>
</html>