<?php
include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$input = file_get_contents('loc.html');
$regexp = "<a href=\"(.*)\">(.*)<\/a>";
$return = $Crawler->regexp($regexp, $input);
echo "<pre>";
print_r($return);
$base = "http://www.urbanspoon.com";
foreach($return as $v) {
	echo $sql = "insert into `location` set `locations` = '".strip_tags(addslashes(stripslashes(trim($v[2]))))."', `url` = '".addslashes(stripslashes(trim($base.$v[1])))."', `flag` = 0";
	echo "<br>";
	mysql_query($sql) or die('error'.mysql_error());
}
?>