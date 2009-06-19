<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";

$dirname = "stateurls";
if ($handle = opendir($dirname)) {
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "file") {
			echo $dirname."/".$file;
			echo "<br>";
			$fc = file_get_contents($dirname."/".$file);
			
			$regexp = "<li><b><a href=\"\/c(.*)\">(.*)<\/a><\/b><\/li>";
			$matches = $Crawler->regexp($regexp, $fc);
			if($matches) {
				foreach($matches as $match) {
					$url = $base."/c".$match[1];
					echo $url;
					echo "<br>";
					$baseName = basename($url);
					echo $baseName;
					echo "<br>";
					if(!file_exists("cityurls/".$baseName)) {
						$fs = file_get_contents($url);
						file_put_contents("cityurls/".$baseName, $fs);
						echo 'file saved';
						echo "<br>";
					} else {
						echo 'file already existed';
						echo "<br>";
					}
				}
			}
			flush();
		}
	}
	closedir($handle);
}
?>