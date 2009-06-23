<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";

$dirname = "rest/detailpages";
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
					if($file2=="."||$file2==".."||$file2==".svn") continue;
					if($filetype2 == "file") {
						echo $file3 = $dirname2."/".$file2;
						echo "<br>";	
						$input = file_get_contents($file3);	
						echo $baseName = str_replace(".html", ".txt", basename($file3));
						echo $baseName2 = str_replace(".html", "", basename($file3));
						echo "<br>";
						$array = $Crawler->parseDetails($input);
						$array['id'] = $baseName2;
						$array['rid'] = $array['country']."/".$array['province']."/".$array['city']."/".$array['folder'];
						$string = serialize($array);
						echo $string;
						echo "<pre>";
						print_r($array);
						$xml = $Crawler->createXmlString($array);
						echo htmlentities($xml);
						exit;
						continue;
						exit;
						if(!file_exists('rest/details/'.$folder.'/'.$baseName)) {
							$string = $Crawler->parseDetails($input);
							if(!$string) {
								echo 'could not parse on line '.__LINE__;
								exit;
							}
							file_put_contents('rest/details/'.$folder.'/'.$baseName, $string);
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