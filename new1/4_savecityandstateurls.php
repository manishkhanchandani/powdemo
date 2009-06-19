<?php

ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";
$url = "cities/citylist.txt";
$input = file_get_contents($url);
$array = unserialize($input);
foreach($array as $v) {
	echo "<pre>";
	print_r($v);
	$name = basename($v['url']);
	if(!file_exists('cityurls/'.$name)) {
		$file = file_get_contents($v['url']);
		file_put_contents('cityurls/'.$name, $file);
	}
	flush();
}

$url = "cities/statelist.txt";
$input = file_get_contents($url);
$array = unserialize($input);
foreach($array as $v) {
	echo "<pre>";
	print_r($v);
	$name = basename($v['url']);
	if(!file_exists('stateurls/'.$name)) {
		$file = file_get_contents($v['url']);
		file_put_contents('stateurls/'.$name, $file);
	}
	flush();
}
echo 'done';
?>