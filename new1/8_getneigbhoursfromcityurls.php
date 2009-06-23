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
			
			$folder = str_replace("-restaurants.html", "", $file);
			if(!is_dir('neighboursurl/'.$folder)) {
				echo 'Creating folder '.$folder.' in neighboursurl';
				echo '<br>';
				mkdir('neighboursurl/'.$folder,0777);
				chmod('neighboursurl/'.$folder,0777);
			}
			if(!is_dir('neighboursarray/'.$folder)) {
				echo 'Creating folder '.$folder.' in neighboursarray';
				echo '<br>';
				mkdir('neighboursarray/'.$folder,0777);
				chmod('neighboursarray/'.$folder,0777);
			}
			
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
					if(file_exists("neighboursurl/".$folder."/".$arr[$k]['baseName'].".html")) {
						echo $arr[$k]['url'].' file already existed';
						echo "<br>";
					} else if(file_exists("neighboursurl/".$arr[$k]['baseName'].".html")) {
						copy("neighboursurl/".$arr[$k]['baseName'].".html", "neighboursurl/".$folder."/".$arr[$k]['baseName'].".html");
						@unlink("neighboursurl/".$arr[$k]['baseName'].".html");
					} else {
						static $item=0; $item++;
						if($item%5==0) {
							echo "ITEM ($item): ";
							$Crawler->changeip();
						} else {
							echo "ITEM ($item): ";
						}
						echo "<br>";
						$fs = file_get_contents($arr[$k]['url']);
						if(!$fs) {
							echo 'could not get '.$arr[$k]['url'];
							echo "<br>";
							exit;
						}
						file_put_contents("neighboursurl/".$folder."/".$arr[$k]['baseName'].".html", $fs);
						echo $arr[$k]['url'].' file saved';
						echo "<br>";
						echo '<h1>sleeping for 10 seconds</h1>';
						echo '<br>';
						sleep(10);
					}
					
					if(file_exists("neighboursarray/".$folder."/".$arr[$k]['baseName'].".txt")) {
						echo $arr[$k]['url'].' / '.$arr[$k]['baseName'].' file already existed';
						echo "<br>";
					} else if(file_exists("neighboursarray/".$arr[$k]['baseName'].".txt")) {
						copy("neighboursarray/".$arr[$k]['baseName'].".txt", "neighboursarray/".$folder."/".$arr[$k]['baseName'].".txt");
						@unlink("neighboursarray/".$arr[$k]['baseName'].".txt");
					} else {
						$fs2 = serialize($arr[$k]);
						file_put_contents("neighboursarray/".$folder."/".$arr[$k]['baseName'].".txt", $fs2);
						echo $arr[$k]['baseName'].' file saved';
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