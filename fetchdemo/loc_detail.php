<?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsView = "SELECT * FROM location WHERE flag = 0 limit 10";
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php if ($totalRows_rsView > 0) { // Show if recordset not empty 
	include('../Classes/Crawler.php');
	$Crawler = new Crawler;
	$base = "http://www.urbanspoon.com";
	echo "<pre>";
do { 
	print_r($row_rsView);
	$input = file_get_contents($row_rsView['url']);
	$regexp = "<p><a href=\"(.*)\">See more ".$row_rsView['locations']." critics reviews<\/a><\/p>";
	$return = $Crawler->regexp($regexp, $input);
	$link = '';
	if($return) {
		$link = $base.$return[0][1];
	}
	if(!$return) {
		$regexp = "<p><a href=\"\/recent\-comments(.*)\">See more recent reviews<\/a><\/p>";
		$return = $Crawler->regexp($regexp, $input);
		$link = $base."/recent-comments".$return[0][1];
	} 
	print_r($return);
	echo "<br>";
	$flag = 1;
	if(!$return) {
		echo 'no return';
		$flag = 2;
		//exit;
	}
	$sql = "update location set reviewurl = '".addslashes(stripslashes(trim($link)))."', flag = ".$flag." where loc_id = '".$row_rsView['loc_id']."'";
	mysql_query($sql) or die('error due to '.mysql_error());
	echo $sql;
	echo "<br>";
	flush();
} while ($row_rsView = mysql_fetch_assoc($rsView)); 
?>
<meta http-equiv="refresh" content="15" />
<?php
} // Show if recordset not empty ?>
</body>
</html>
<?php
mysql_free_result($rsView);
?>
