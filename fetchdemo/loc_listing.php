<?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsLoc = "SELECT * FROM location WHERE reviewurl != '' AND flag2 = 0 ORDER BY loc_id ASC LIMIT 1";
$rsLoc = mysql_query($query_rsLoc, $conn) or die(mysql_error());
$row_rsLoc = mysql_fetch_assoc($rsLoc);
$totalRows_rsLoc = mysql_num_rows($rsLoc);
?><?php
include('../Classes/Crawler.php');
$Crawler = new Crawler;

?>
<?php if ($totalRows_rsLoc > 0) { // Show if recordset not empty
echo "<pre>"; 
do { 
print_r($row_rsLoc);
	$input = file_get_contents($row_rsLoc['reviewurl']);
	$return = $Crawler->getListing($input);
	if($return) {
		$flag2 = 1;
		foreach($return as $v) {
			$sql = "insert into location_details set loc_id = '".addslashes(stripslashes(trim($row_rsLoc['loc_id'])))."', link = '".addslashes(stripslashes(trim($v['link'])))."', title = '".addslashes(stripslashes(trim($v['title'])))."', source = '".addslashes(stripslashes(trim($v['source'])))."', cdate = '".addslashes(stripslashes(trim($v['cdate'])))."', summary = '".addslashes(stripslashes(trim($v['summary'])))."', fullreview = '".addslashes(stripslashes(trim($v['fullreview'])))."', allreview = '".addslashes(stripslashes(trim($v['allreview'])))."'";
			echo $sql;
			echo "<br>";
			mysql_query($sql) or die('error'.mysql_error());
		}
	} else {
		$flag2 = 2;
	}
	$sql = "update location set flag2 = '".$flag2."' where loc_id = '".$row_rsLoc['loc_id']."'";
	echo $sql;
	echo "<br>";
	mysql_query($sql) or die('error2');
} while ($row_rsLoc = mysql_fetch_assoc($rsLoc));  
?>
<meta http-equiv="refresh" content="15" />
<?php
} // Show if recordset not empty ?>
<?php
mysql_free_result($rsLoc);
?>