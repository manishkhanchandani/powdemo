<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";

$dirname = "cityurls";
if ($handle = opendir($dirname)) {
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "file") {
			echo $dirname."/".$file;
			echo "<br>";
			$fc = file_get_contents($dirname."/".$file);
			$regexp = "<li class=\"t\-li\"><a href=\"\/n(.*)\">(.*)\(.*<\/a><\/li>";
			$matches = $Crawler->regexp($regexp, $fc);
			if($matches) {
				foreach($matches as $k=>$match) {
					$arr[$k]['urls'] = $base."/n".$match[1];
					$arr[$k]['titles'] = $match[2];
				}
				echo "<pre>";
				print_r($arr);
			}
			$regexp = "<li class=\"hood\-group\"><a href=\"#\" onclick=\"new Ajax.Updater\('hoods_long', '(.*)',.*\">See all<\/a><\/li>";
			$matches = $Crawler->regexp($regexp, $fc);
			if($matches) {
				$url = $base.$matches[0][1];
				echo $url;
				echo "<br>";
				$seeall = file_get_contents($url);
				file_put_contents($dirname."/seeall/".$file, $seeall);
			}
		exit;
		}
	}
	closedir($handle);
}
?>