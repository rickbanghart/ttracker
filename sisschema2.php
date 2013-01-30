<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>
<body>
<?php
$action = '';
include('includes/ttrack.php'); // sets data connection $conn
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	foreach ($_POST as $key=>$value) {
		$$key = $value;
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	foreach ($_GET as $key=>$value) {
		$$key = $value;
	}
}
if ($action == 'table_detail') {
	show_table_detail($table_name);
} else {
	show_tables();
}
function get_tables() {
	global $conn;
	$qry = "SELECT t1.Table_Name,	
				t1.Short_Desc AS Table_Short_Desc,
				t1.Last_Updated,
				t1.Table_Type,
				t1.Data_Type,
				t1.Sis_Reference,
				t1.Long_Desc,
				t1.Remarks AS Table_Remarks,
				InCollegeOfEducationCopyOfSISData
			FROM dbo.SIS_AATABLES AS t1
			WHERE t1.InCollegeOfEducationCopyOfSISData = 1
			ORDER BY t1.Table_Name";
	$rst = sqlsrv_query($conn, $qry);
	return $rst;
}
function get_table_info($table_name) {
	global $conn;
	$qry = "SELECT t1.Table_Name,	
				t1.Short_Desc AS Table_Short_Desc,
				t1.Last_Updated,
				t1.Table_Type,
				t1.Data_Type,
				t1.Sis_Reference,
				t1.Long_Desc,
				t1.Remarks AS Table_Remarks,
				InCollegeOfEducationCopyOfSISData,
				t2.Column_Seq,
				Column_Name,
				t2.Short_Desc AS Column_Short_Desc,
				t2.Sis_Reference,
				Primary_Key_Seq,
				t2.Data_Type,
				Length,
				t2.Long_Desc AS Column_Long_Desc,
				t2.Remarks AS Column_Remarks
			FROM dbo.SIS_AATABLES AS t1, dbo.SIS_AACOLUMNS as t2
			WHERE t1.Table_Name = t2.Table_Name
			ORDER BY t1.Table_Name, t2.Column_Seq";
	$rst = sqlsrv_query($conn, $qry);
	return $rst;
}
?>
<?php
function show_table_detail($table_name) {
	global $conn;
	$qry = 'SELECT COUNT(*) AS num_recs FROM '. "ED4DBT.SISINFO.dbo.".$table_name;
	if ($rst = sqlsrv_query($conn, $qry)) {
		$row = sqlsrv_fetch_array($rst);
		echo '<br> ' . $row['num_recs'] . ' rows found <br>';
	} else {
		$errors = sqlsrv_errors();
		foreach( $errors as $error) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br>";
            echo "code: ".$error[ 'code']."<br>";
            echo "message: ".$error[ 'message']."<br>";
        }
		echo '<br> Row Count Failed<br>';
	}
	?>
    <a href="?action=show_tables">Show Tables</a><br />
    <?php
	$qry = "SELECT t1.Column_Name, 
				t1.Column_Seq,	
				t1.Short_Desc,
				t1.Sis_Reference,
				t1.Data_Type,
				t1.Length,
				t1.Long_Desc,
				t1.Remarks
			FROM dbo.SIS_AAColumns AS t1
			WHERE t1.Table_Name = ? 
			ORDER BY t1.Column_seq";
	$params[0] = $table_name;
	$first_row = 1;
	?>
    <br /><hr  />
    <?php
	$column_names = '';
	$rst1 = sqlsrv_query($conn, $qry, $params);
	while ($row = sqlsrv_fetch_array($rst1)) {
		$column_names_array[] = $row['Column_Name'];
		if ($first_row == 1) {
			$column_names.= $row['Column_Name'];
			$first_row = 0;
			?>
            <table border="1"><tr>
            <th colspan="6">Column Information for <?php echo $table_name ?>,</th></tr>
            <tr><th>Column Name</th><th>Data Type</th><td>Length</td>
            <th>Short Description</th><th>Long Description</th><td>Remarks</td></tr>
            <?php
		} else {
			$column_names .= ', '. $row['Column_Name'];
		}
		?>
        <tr><td><?php echo $row['Column_Name'] ?></td>
        <td><?php echo $row['Data_Type'] ?></td>
        <td><?php echo $row['Length'] ?></td>
        <td><?php echo $row['Short_Desc'] ?></td>
        <td><?php echo $row['Long_Desc'] ?></td>
        <td><?php echo $row['Remarks'] ?></td>
        </tr>
        <?php
		if ($rst = sqlsrv_query($conn, $qry)) {
		}
	}
	?></table><?php
	$params[0] = 'ED4DBT.SISINFO.dbo.' . $table_name;
	$qry = "SELECT TOP 30 $column_names FROM $params[0]";
	echo $qry . "<br>" . $params[0] . "<br>";
	$rst = sqlsrv_query($conn, $qry);
	//	$errors = sqlsrv_errors();
	/*	foreach( $errors as $error) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br>";
            echo "code: ".$error[ 'code']."<br>";
            echo "message: ".$error[ 'message']."<br>";
		}
*/$first_row = 1;
	?>
    <table border="1">
    <?php
	while ($row = sqlsrv_fetch_array($rst)) {
		if ($first_row == 1) {
			$first_row = 0;
			?>
			<tr><?php
            foreach ($column_names_array as $column_name) {
				?><th><?php echo $column_name ?></th><?php
            }
        	?></tr><tr><?php
            foreach ($column_names_array as $column_name) {
				$field_value = ! is_object($row[$column_name])?$row[$column_name]:'Object';
				?><td><?php echo $field_value ?></td><?php ;
			}
			?></tr><?php
		} else {
        	?></tr><tr><?php
			
            foreach ($column_names_array as $column_name) {
				$field_value = ! is_object($row[$column_name])?$row[$column_name]:'Object';
				?><td><?php echo $field_value ?></td><?php
			}
			?></tr><?php
        }
	}
    ?>
     </table>
     <?php
	// now we select a few rows from the table to see what it looks like
	$qry = "SELECT TOP 10 * FROM " . "ED4DBT.SISINFO.dbo." . $table_name;
	
}
function show_tables() {
	$rst = get_tables();
	$first_row = 1;
	?>
	<table border="1">
	<?php
	while ($row = sqlsrv_fetch_array($rst)) {
		if ($first_row == 1) {
			?>
			<tr><th>Table Name</th><th>Short Description</th><th>In COE</th></tr>
			<?php
			$first_row = 0;
		}
		?>
		<tr><td>
        <a href="?action=table_detail&table_name=<?php echo $row['Table_Name'] ?>
        "><?php echo $row['Table_Name'] ?></td>
        <td><?php echo $row['Table_Short_Desc'] ?></td>
		<td><?php echo $row['InCollegeOfEducationCopyOfSISData'] ?></td></tr>
		<?php
	}
	?>
	</table>
    <?php
}
?>
</body>
</html>                                                                                                                                                                                                           