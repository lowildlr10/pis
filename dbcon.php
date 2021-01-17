<?php

	if($_SERVER['HTTP_HOST'] == "localhost"){
		$serverHost = "localhost";
		$serverUser = "root";
		$serverPass = "car007"; 
		$serverDb = "dbpis";
	}else{
		$serverHost = "localhost";
		$serverUser = "root";
		$serverPass = "car007"; 
		$serverDb = "dbpis";
		/*
		$serverHost = "localhost";
		$serverUser = "dbuser";
		$serverPass = "c2rsy543m"; 
		$serverDb = "dbpis";
		
		$serverHost = "localhost";
		$serverUser = "root";
		$serverPass = "car007"; 
		$serverDb = "dbpis";*/
	}
/*
	$conn = mysql_pconnect($serverHost,$serverUser,$serverPass) or die(mysql_error());
	mysql_select_db($serverDb,$conn) or die("Could not select db");
*/

$conn = mysqli_connect($serverHost, $serverUser, $serverPass, $serverDb);
if (empty($conn)) {
    die("mysqli_connect failed: " . mysqli_connect_error());
}
/*
print "connected to " . mysqli_get_host_info($conn) . "\n";
mysqli_close($conn);
*/
?>
