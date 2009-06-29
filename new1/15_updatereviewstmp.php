<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";

mysql_select_db($database_conn, $conn);
$query_rsReview = "SELECT * FROM reviews as r LEFT JOIN restaurants as r2 ON r.restaurant_id = r2.restaurant_id";
$rsReview = mysql_query($query_rsReview, $conn) or die(mysql_error());
$row_rsReview = mysql_fetch_assoc($rsReview);
$totalRows_rsReview = mysql_num_rows($rsReview);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php if ($totalRows_rsReview > 0) { // Show if recordset not empty ?>

  <?php do { ?>
<?php
$folder = substr(strrchr($row_rsReview['rid'],"/"),1);
echo $folder;
echo "<br>";
echo 'rest/detailpages/'.$folder.'/'.$row_rsReview['id'].'.html';
echo "<br>";
$input = @file_get_contents('rest/detailpages/'.$folder.'/'.$row_rsReview['id'].'.html');
if(!$input) {
	$sql = "INSERT INTO `reviews2` (`restaurant_id` , `id` ,`score` ,`rdate` ,`url` ,`desc`, `review_source`) VALUES ('".$row_rsReview['restaurant_id']."', '".addslashes(stripslashes(trim($row_rsReview['id'])))."' , '".addslashes(stripslashes(trim($row_rsReview['score'])))."' , '".addslashes(stripslashes(trim($row_rsReview['rdate'])))."' , '".addslashes(stripslashes(trim($row_rsReview['url'])))."' , '".addslashes(stripslashes(trim($row_rsReview['desc'])))."' , '".addslashes(stripslashes(trim($row_rsReview['review_source'])))."')";
	echo $sql;
	echo "<br>";
	mysql_query($sql) or die(__LINE__." ".mysql_error());
	continue;
}
$baseName = $row_rsReview['id'].".txt";
$baseNameXML = $row_rsReview['id'].".xml";
$baseName2 = $row_rsReview['id'];
echo "<br>";
$array = $Crawler->parseDetails($input);

if(!$array) {
	echo 'could not parse on line '.__LINE__;
	exit;
}
$array['folder'] = $folder;
$array['id'] = $baseName2;
$array['rid'] = $array['country']."/".$array['province']."/".$array['city']."/".$array['folder'];
$array['lat'] = $v['lat'];
$array['lon'] = $v['lon'];
$array['neighbour'] = $v['neighbour'];
			if($array['critic-reviews']) {
				foreach($array['critic-reviews'] as $reviews) {
					if($reviews['score'] || $reviews['desc']) { 
						if(md5(trim($reviews['title']))!=md5(trim($row_rsReview['url']))) {
							continue;
						}
						$src = '';
						$src = $row_rsReview['review_source'];
						if(!$src) $src = $reviews['source'];
						$sql = "INSERT INTO `reviews2` (`restaurant_id` , `id` ,`score` ,`rdate` ,`url` ,`desc`, `review_source`) VALUES ('".$row_rsReview['restaurant_id']."', '".addslashes(stripslashes(trim($row_rsReview['id'])))."' , '".addslashes(stripslashes(trim($row_rsReview['score'])))."' , '".addslashes(stripslashes(trim($row_rsReview['rdate'])))."' , '".addslashes(stripslashes(trim($row_rsReview['url'])))."' , '".addslashes(stripslashes(trim($row_rsReview['desc'])))."' , '".addslashes(stripslashes(trim($src)))."')";
						echo $sql;
						echo "<br>";
						mysql_query($sql) or die(__LINE__." ".mysql_error());
					}
				}
			}
 ?>
    <?php } while ($row_rsReview = mysql_fetch_assoc($rsReview)); ?>
  <?php } // Show if recordset not empty ?></body>
</html>
<?php
mysql_free_result($rsReview);
?>
