<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include('../Classes/Crawler.php');
$Crawler = new Crawler;

$base = "http://www.urbanspoon.com";

mysql_select_db($database_conn, $conn);
$query_rsView = "SELECT * FROM location_neighbours WHERE flag = 0 limit 1";
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
print_r($row_rsView);
echo "</pre>";

$input = file_get_contents($row_rsView['urls']);
$regexp = "<span class=\"muted\">\(Showing 1\-50 of (.*)\)<\/span>";
$matches = $Crawler->regexp($regexp, $input);
if($matches) {
	$total = $matches[0][1];
} else {
	$regexp = "<span class=\"muted\">\((.*)\)<\/span>";
	$matches = $Crawler->regexp($regexp, $input);
	if($matches) {
		$total = $matches[0][1];
	} else {
		echo 'no match2';
		exit;
	}
}
$max = 50;
$page = 0;
if($row_rsView['pages']>0) {
	$page = $row_rsView['pages'];
}
$totalPages = ceil($total/$max)-1;

for($i=$page; $i<=$totalPages; $i++) {
	$page = $i;
	if($page>0) {
		$input = file_get_contents($row_rsView['urls']."?page=".$page);
	}
	$Crawler->getHotelListingDetails($input, $base, $page, $row_rsView);
	if($page==$totalPages) {
		$flag = 1;
	} else {
		$flag = 0;
	}
	$sql = "update location_neighbours set flag = '".$flag."', pages = '".$page."' where nid = '".$row_rsView['nid']."'";
	echo $sql;
	echo "<br>";
	mysql_query($sql) or die('error'.mysql_error());
}
?>
    <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
  <?php } // Show if recordset not empty ?></body>
</html>
<?php
mysql_free_result($rsView);
?>
