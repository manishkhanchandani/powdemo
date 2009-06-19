<?php
include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";
$url = "cities/all.html";
$file = file_get_contents($url);
$arr1 = explode("<p class=\"section-header\">USA</p>", $file);
$arr2 = explode("google_ad_section_start",$arr1[1]);
$content = $arr2[0];
file_put_contents("cities/main.html", $content);
echo 'done';
?>