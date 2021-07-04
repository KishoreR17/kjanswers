<?php

$host="localhost"; 
$username="root"; 
$password=""; 
$database="testdb";
$con=mysqli_connect($host,$username,$password, $database);
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

$file  = $_FILES['filename']['tmp_name'];
$open = fopen($file, 'r');
while (($line = fgetcsv($open)) !== FALSE) {
    $rows[] = $line;
}
fclose($open);

$headers = $rows[0];
$tableName = "grootan".rand(0,100000);
$type = "VARCHAR";
$length = 255;
$end = end($headers);
$query = "CREATE TABLE $tableName (";
$insert = "";
foreach($headers as $key => $column) {
	
	if($key > 0) {
		$query .= " ";
	}
 $insert .= $column;
 $query .= "$column" . " " . "$type" . "(255)" ;
 if($column != $end) {
	 $query .= ",";
	 $insert .= ",";
 }
}

$query .= " ); ";
mysqli_query($con, $query);
unset($rows[0]);
$success = 0;
$fail = 0;
foreach($rows as $key => $row) {
	$values = "";
	for($i = 0; $i< count($headers); $i++) {
		if($headers[$i] == 'password') {
			$rowVal = md5($row[$i]);
		} else {
			$rowVal = $row[$i];
		}
		$values .= "'".$rowVal."'";
		if($i < count($headers) - 1) {
			$values .= ",";
		}
	}
	if(mysqli_query($con, "INSERT INTO $tableName ($insert) VALUES ($values)")) {
		$success = ++$success;
	}else {
		$fail = ++$fail;
	}
}
echo "CSV file records are imported to Database...<br/>";
echo "<br/>Total No of Records : ".count($rows);
echo "<br/>Total No of Success : ".$success;
echo "<br/>Total No of Fails : ".$fail;

?>