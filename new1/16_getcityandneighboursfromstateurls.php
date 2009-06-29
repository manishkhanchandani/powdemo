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
		$arr = array();
		$arrc = array();
		$filetype = filetype($dirname."/".$file);
		if($filetype == "file") {
			echo $dirname."/".$file;
			echo "<br>";
			if(!($file == "Southern-California-restaurants.html" || $file == "Northern-California-restaurants.html")) {
				continue;
			}
			$folder = str_replace("-restaurants.html", "", $file);
			echo $folder;
			echo "<br>";
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
				$neigbhour = '';
				foreach($matches as $k=>$match) {
					$arr[$k]['url'] = $base."/n".$match[1];
					$arr[$k]['baseName'] = basename($arr[$k]['url']);
					$arr[$k]['title'] = trim($match[2]);
					$arr[$k]['count'] = trim($match[3]);
					$neigbhour .= "neighboursurl/".$folder."/".$arr[$k]['baseName'].".html
";
					if(file_exists("neighboursurl/".$folder."/".$arr[$k]['baseName'].".html")) {
						echo $arr[$k]['url'].' file already existed';
						echo "<br>";
					} else {
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
						echo '<h1>sleeping for 2 seconds</h1>';
						echo '<br>';
						sleep(2);
					}
					
					if(file_exists("neighboursarray/".$folder."/".$arr[$k]['baseName'].".txt")) {
						echo $arr[$k]['url'].' / '.$arr[$k]['baseName'].' file already existed';
						echo "<br>";
					} else {
						$fs2 = serialize($arr[$k]);
						file_put_contents("neighboursarray/".$folder."/".$arr[$k]['baseName'].".txt", $fs2);
						echo $arr[$k]['baseName'].' file saved';
						echo "<br>";
					}
					flush();
				}
				file_put_contents("statelist/neigbhour/".$file, $neigbhour);
			}
			
			$regexp = "<li><b><a href=\"\/c(.*)\">(.*)<\/a><\/b><\/li>";
			$matches = $Crawler->regexp($regexp, $fc);
			if($matches) {
				$city = '';
				foreach($matches as $k=>$match) {
					$arrc[$k]['url'] = $base."/c".$match[1];
					$arrc[$k]['baseName'] = basename($arr[$k]['url']);
					$arrc[$k]['title'] = trim($match[2]);
					$city .= "cityurls/".$arrc[$k]['baseName'].".html
";
					if(file_exists("cityurls/".$arrc[$k]['baseName'].".html")) {
						echo "cityurls/".$arrc[$k]['baseName'].".html file already existed";
						echo "<br>";
					} else {
						echo "<br>";
						$fs = file_get_contents($arrc[$k]['url']);
						if(!$fs) {
							echo 'could not get '.$arrc[$k]['url'];
							echo "<br>";
							exit;
						}
						file_put_contents("cityurls/".$arrc[$k]['baseName'].".html", $fs);
						echo $arrc[$k]['url'].' file saved as cityurls/'.$arrc[$k]['baseName'].'.html';
						echo "<br>";
						echo '<h1>sleeping for 2 seconds</h1>';
						echo '<br>';
						sleep(2);
					}
					file_put_contents("statelist/city/".$file, $city);
					flush();
				}
			}
			/*
			echo "<pre>";
			print_r($arr);
			print_r($arrc);
			echo "</pre>";
			exit;
			*/
			flush();
		}
	}
	closedir($handle);
}
?>