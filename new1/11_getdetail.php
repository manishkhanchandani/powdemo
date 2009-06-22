<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";

$dirname = "rest/description";
if ($handle = opendir($dirname)) {
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "dir" && !($file == "." || $file == ".svn" || $file == "..")) {
			$dirname2 = $dirname."/".$file;
			
			$folder = $file;
			if(!is_dir('rest/listing/'.$folder)) {
				echo 'Creating folder '.$folder.' in listing folder';
				echo '<br>';
				mkdir('rest/listing/'.$folder,0777);
				chmod('rest/listing/'.$folder,0777);
			}
			if(!is_dir('rest/pages/'.$folder)) {
				echo 'Creating folder '.$folder.' in pages folder';
				echo '<br>';
				mkdir('rest/pages/'.$folder,0777);
				chmod('rest/pages/'.$folder,0777);
			}
			if(!is_dir('rest/details/'.$folder)) {
				echo 'Creating folder '.$folder.' in details folder';
				echo '<br>';
				mkdir('rest/details/'.$folder,0777);
				chmod('rest/details/'.$folder,0777);
			}
			if(!is_dir('rest/description/'.$folder)) {
				echo 'Creating folder '.$folder.' in description folder';
				echo '<br>';
				mkdir('rest/description/'.$folder,0777);
				chmod('rest/description/'.$folder,0777);
			}
			if(!is_dir('rest/detailpages/'.$folder)) {
				echo 'Creating folder '.$folder.' in detailpages folder';
				echo '<br>';
				mkdir('rest/detailpages/'.$folder,0777);
				chmod('rest/detailpages/'.$folder,0777);
			}
			if(!is_dir('rest/finalxml/'.$folder)) {
				echo 'Creating folder '.$folder.' in finalxml folder';
				echo '<br>';
				mkdir('rest/finalxml/'.$folder,0777);
				chmod('rest/finalxml/'.$folder,0777);
			}
			
			if ($handle = opendir($dirname2)) {
				/* This is the correct way to loop over the directory. */
				while (false !== ($file2 = readdir($handle))) {
					$filetype2 = filetype($dirname2."/".$file2);
					if($filetype2 == "file") {
						echo $file3 = $dirname2."/".$file2;
						echo "<br>";		
						$input = file_get_contents($file3);	
						$array = unserialize($input);
						print_r($array);
						echo "<br>";		
						$url = $array['url'];
						echo $url;
						echo "<br>";
						echo $baseName = str_replace(".txt", ".html", basename($file3));
						echo "<br>";
						if(!file_exists('rest/detailpages/'.$folder.'/'.$baseName)) {
							$string = @file_get_contents($url);
							if(!$string) {
								echo 'could not fetch url '.$url.' on line '.__LINE__;
								exit;
							}
							file_put_contents('rest/detailpages/'.$folder.'/'.$baseName, $string);
							echo 'rest/detailpages/'.$folder.'/'.$baseName.' created.<br>';
						} else {
							echo 'rest/detailpages/'.$folder.'/'.$baseName.' already exists.<br>';
						}
						flush();
						//sleep(15);
						echo "<br>";
						echo "<br>";
					}
				}
				exit;
			}
		}
	}
}
?>