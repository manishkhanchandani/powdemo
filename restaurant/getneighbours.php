<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include('../Classes/Crawler.php');
$Crawler = new Crawler;

$base = "http://www.urbanspoon.com";

mysql_select_db($database_conn, $conn);
$query_rsView = "SELECT * FROM location WHERE flag3 = 0 ORDER BY loc_id ASC limit 1";
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
<?php if ($totalRows_rsView > 0) { // Show if recordset not empty ?>
  <?php do { ?>
<?php
echo "<pre>";
print_r($rsView);
echo "</pre>";
	
$input = file_get_contents($row_rsView['url']);
$regexp = "<li class=\"hood\-group\"><a href=\"#\" onclick=\"new Ajax.Updater\('hoods_long', '(.*)',.*\">See all<\/a><\/li>";
$matches = $Crawler->regexp($regexp, $input);
if($matches) {
	//echo "<pre>";
	//print_r($matches);
	//echo "</pre>";
	$url = $base.$matches[0][1];
	echo $url;
	echo "<br>";
	$input2 = file_get_contents($url);
	$regexp = "<li class=\"t\-li\"><a href=\"(.*)\">(.*)\(.*<\/a><\/li>";
	$matches2 = $Crawler->regexp($regexp, $input);
	//echo "<pre>";
	//print_r($matches2);
	//echo "</pre>";
	if($matches2) {
		foreach($matches2 as $k=>$match) {
			$urls[$k] = $base.$match[1];
			$titles[$k] = $match[2];
			$sql = "INSERT INTO `location_neighbours` (`loc_id` , `urls` , `titles` , `flag` ) VALUES ('".$row_rsView['loc_id']."', '".addslashes(stripslashes(trim($urls[$k])))."', '".addslashes(stripslashes(trim($titles[$k])))."', '0' )";
			echo $sql;
			echo "<br>";
			mysql_query($sql) or die('error');
		}
	}
	$sql = "update location set flag3 = 1 where loc_id = '".$row_rsView['loc_id']."'";
	echo $sql;
	echo "<br>";
	mysql_query($sql) or die('error');
	//echo "<pre>";
	//print_r($urls);
	//print_r($titles);
	//echo "</pre>";
} else {
	echo "<pre>";
	print_r($row_rsView);
	echo "</pre>";
	echo 'no match found.'.__LINE__.'<br>';
	$sql = "update location set flag3 = 2 where loc_id = '".$row_rsView['loc_id']."'";
	echo $sql;
	echo "<br>";
	mysql_query($sql) or die('error');	
	/*
	$regexp = "<li class=\"t\-li\"><a href=\"(.*)\">(.*)\(.*<\/a><\/li>";
	$matches2 = $Crawler->regexp($regexp, $input);
	//echo "<pre>";
	//print_r($matches2);
	if($matches2) {
		foreach($matches2 as $k=>$match) {
			$urls[$k] = $base.$match[1];
			$titles[$k] = $match[2];
			$sql = "INSERT INTO `location_neighbours` (`loc_id` , `urls` , `titles` , `flag` ) VALUES ('".$row_rsView['loc_id']."', '".addslashes(stripslashes(trim($urls[$k])))."', '".addslashes(stripslashes(trim($titles[$k])))."', '0' )";
			echo $sql;
			echo "<br>";
			mysql_query($sql) or die('error');
		}
		$sql = "update location set flag3 = 3 where loc_id = '".$row_rsView['loc_id']."'";
		echo $sql;
		echo "<br>";
		mysql_query($sql) or die('error');		
	} else {
		$sql = "update location set flag3 = 2 where loc_id = '".$row_rsView['loc_id']."'";
		echo $sql;
		echo "<br>";
		mysql_query($sql) or die('error');	
	}
	*/
}
?>
    <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
	<meta http-equiv="refresh" content="10" />
  <?php } // Show if recordset not empty ?>
</body>
</html>
<?php
mysql_free_result($rsView);
?>
