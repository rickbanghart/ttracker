<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
session_start();
include('includes/ttrack.php'); // sets data connection $conn
include('includes/authenticate.php'); // authentication functions
	$qry = "{call dbo.sp_get_courses()}";

	$rst = sqlsrv_query($conn, $qry);
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

?>
<html>
	<head>
		<title>Assign Surveys</title>
	</head>
	<body>
		<select type="select" name="subject">
		<?php
		    while ($row = sqlsrv_fetch_array($rst)) {
		
				echo '<option value="">' . $row[0] .'</option>';
			}
		?>
			
		</select>
		
	</body>
</html>