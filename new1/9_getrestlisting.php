<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";

$dirname = "neighboursurl";
if ($handle = opendir($dirname)) {
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "file") {
			// creating folder
			$folder = str_replace("-restaurants.html", "", $file);
			echo $dirname."/".$file;
			echo "<br>";
			echo $folder;
			echo "<br>";
			if(!is_dir('restaurants/listing/'.$folder)) {
				echo 'Creating folder '.$folder.' in listing folder';
				echo '<br>';
				mkdir('restaurants/listing/'.$folder,0777);
				chmod('restaurants/listing/'.$folder,0777);
			}
			if(!is_dir('restaurants/pages/'.$folder)) {
				echo 'Creating folder '.$folder.' in pages folder';
				echo '<br>';
				mkdir('restaurants/pages/'.$folder,0777);
				chmod('restaurants/pages/'.$folder,0777);
			}
			if(!is_dir('restaurants/details/'.$folder)) {
				echo 'Creating folder '.$folder.' in details folder';
				echo '<br>';
				mkdir('restaurants/details/'.$folder,0777);
				chmod('restaurants/details/'.$folder,0777);
			}
			if(!is_dir('restaurants/description/'.$folder)) {
				echo 'Creating folder '.$folder.' in description folder';
				echo '<br>';
				mkdir('restaurants/description/'.$folder,0777);
				chmod('restaurants/description/'.$folder,0777);
			}
			// get listing pages
			$input = file_get_contents($dirname."/".$file);
			$baseName = str_replace(".html","",basename($file));
				
			$txtfile = str_replace(".html",".txt",basename($file));
			$input2 = file_get_contents("neighboursarray/".$txtfile);
			$array = unserialize($input2);
			$urlToTake = $array['url'];
				
			$regexp = "<span class=\"muted\">\(Showing 1\-50 of (.*)\)<\/span>";
			$matches = $Crawler->regexp($regexp, $input);
			if($matches) {
				$total = $matches[0][1];
			} else {
				$regexp = "<span class=\"muted\">\((.*)\)<\/span>";
				$matches = $Crawler->regexp($regexp, $input);
				if($matches) {
					$total = $matches[0][1];
				} else {
					echo 'no match2 '.__LINE__;
					exit;
				}
			}
			echo 'total is ';
			echo $total;
			echo "<br>";
			$max = 50;
			$page = 0;
			$totalPages = ceil($total/$max)-1;
			for($i=$page; $i<=$totalPages; $i++) {
				$page = $i;
				$pgUrl = $urlToTake."?page=".$page;
				echo $pgUrl."<br>";				
				flush();
				if($page>0) {
					if(!file_exists('restaurants/listing/'.$folder."/".$baseName."-".$page.".html")) {
						$input3 = file_get_contents($pgUrl);
						file_put_contents('restaurants/listing/'.$folder."/".$baseName."-".$page.".html", $input3);
					}					
					$input4 = $Crawler->getHotelListingHTML($input3);
				} else {
					if(!file_exists('restaurants/listing/'.$folder."/".$baseName."-".$page.".html")) {
						file_put_contents('restaurants/listing/'.$folder."/".$baseName."-".$page.".html", $input);
					}
					$input4 = $Crawler->getHotelListingHTML($input);				
				}
				if(!file_exists('restaurants/pages/'.$folder."/".$baseName."-".$page.".html")) {
					file_put_contents('restaurants/pages/'.$folder."/".$baseName."-".$page.".html", $input4);
				}
			}
			echo '<h1>sleeping for 15 secs</h1>';
			flush();
			sleep(15);
			echo "<br>";
			echo "<br>";
			exit;
		}
	}
	closedir($handle);
}
?>