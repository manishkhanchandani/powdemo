<?php
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$input = file_get_contents("page.html");
$regexp = "<h1 class=\"page-title.*\">(.*)<\/h1>";
$matches = $Crawler->regexp($regexp, $input);
$arr['title'] = $matches[0][1];

$regexp = " <div>.*<span class=\"phone tel\">(.*)<\/span>.*<\/div>";
$matches = $Crawler->regexp($regexp, $input);
$arr['phone'] = $matches[0][1];

$regexp = " <p class=\"rest-info\">(.*)<\/p>";
$matches = $Crawler->regexp($regexp, $input);
$info = $matches[0][1];

$regexp = "<span class=\"street-address\">(.*)<\/span>";
$matches = $Crawler->regexp($regexp, $info);
$arr['streeaddr'] = $matches[0][1];

$regexp = "<span class=\"locality\">(.*)<\/span>";
$matches = $Crawler->regexp($regexp, $info);
$arr['locality'] = $matches[0][1];

$regexp = "<span class=\"region\">(.*)<\/span>";
$matches = $Crawler->regexp($regexp, $info);
$arr['region'] = $matches[0][1];

$regexp = "<a href=\"(.*)\" class=\".*postal\-code\">(.*)<\/a>";
$matches = $Crawler->regexp($regexp, $info);
$arr['zip'] = $matches[0][2];

$regexp = "<a href=\".*\" class=\"url\".*>(.*)<\/a>";
$matches = $Crawler->regexp($regexp, $info);
$arr['linktext'] = $matches[0][1];


echo "<pre>";
print_r($arr);
?>