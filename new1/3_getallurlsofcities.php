<?php
include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";
$url = "cities/main.html";
$file = file_get_contents($url);
$regex = "<p class=\"city\"><a href=\"(.*)\">Albuquerque<\/a><\	/p>";
$matches = $Crawler->regexp($regexp, $file);
print_r($matches);
if($matches) {
	foreach($matches as $k=>$match) {
		$urls[$k]['title'] = $match[2];
		$urls[$k]['url'] = $base.$match[1];
	}
	echo "<pre>";
	print_r($urls);
	echo 'done';
}
?>