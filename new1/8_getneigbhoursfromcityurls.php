<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";

$dirname = "cityurls/seeall";
if ($handle = opendir($dirname)) {
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "file") {
			echo $dirname."/".$file;
			echo "<br>";
			$fc = @file_get_contents($dirname."/".$file);
			if(!$fc) {
				echo 'could not get '.$dirname."/".$file;
				echo "<br>";
				exit;
			}
			
			$regexp = "<li class=\"t\-li\"><a href=\"\/n(.*)\">(.*)\((.*)\)<\/a><\/li>";
			$matches = $Crawler->regexp($regexp, $fc);
			if($matches) {
				foreach($matches as $k=>$match) {
					$arr[$k]['url'] = $base."/n".$match[1];
					$arr[$k]['baseName'] = basename($arr[$k]['url']);
					$arr[$k]['title'] = trim($match[2]);
					$arr[$k]['count'] = trim($match[3]);
					
					if(!file_exists("neighboursurl/".$arr[$k]['baseName'].".html")) {
						$fs = file_get_contents($arr[$k]['url']);
						if(!$fs) {
							echo 'could not get '.$arr[$k]['url'];
							echo "<br>";
							exit;
						}
						file_put_contents("neighboursurl/".$arr[$k]['baseName'].".html", $fs);
						echo $arr[$k]['url'].' file saved';
						echo "<br>";
					} else {
						echo $arr[$k]['url'].' file already existed';
						echo "<br>";
					}
					
					if(!file_exists("neighboursarray/".$arr[$k]['baseName'].".txt")) {
						$fs2 = serialize($arr[$k]);
						file_put_contents("neighboursarray/".$arr[$k]['baseName'].".txt", $fs2);
						echo $arr[$k]['baseName'].' file saved';
						echo "<br>";
					} else {
						echo $arr[$k]['baseName'].' file already existed';
						echo "<br>";
					}
					
					flush();
				}
			}
			echo "<pre>";
			print_r($arr);
			echo "</pre>";
			flush();
		}
	}
	closedir($handle);
}
?>