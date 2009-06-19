<?php

ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";
$url = "cities/mainmodcities.html";
$input = file_get_contents($url);
$regexp = "<p class=\"city\"><a href=\"(.*)\">(.*)<\/a><\/p>";
$matches = $Crawler->regexp($regexp, $input);

if($matches) {
	foreach($matches as $k=>$match) {
		$urls[$k]['title'] = strip_tags($match[2]);
		$urls[$k]['url'] = $base.$match[1];
		$urls[$k]['type'] = 'City';
	}
	echo "<pre>";
	print_r($urls);
	$cityurl = serialize($urls);
	file_put_contents('cities/citylist.txt', $cityurl);
}
$regexp = "<p style=\"(.*)\"><a href=\"(.*)\">(.*)<\/a><\/p>";
$matches = $Crawler->regexp($regexp, $input);
if($matches) {
	foreach($matches as $k=>$match) {
		$urls2[$k]['title'] = strip_tags($match[3]);
		$urls2[$k]['url'] = $base.$match[2];
		$urls2[$k]['type'] = 'State';
	}
	echo "<pre>";
	print_r($urls2);
	$stateurl = serialize($urls2);
	file_put_contents('cities/statelist.txt', $stateurl);
	echo 'done';
}

?>