<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 

include_once('../Connections/conn.php');
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$base = "http://www.urbanspoon.com";
$dirname = "neighboursurl";
if ($handle = opendir($dirname)) {
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "dir" && !($file == "." || $file == ".svn" || $file == "..")) {
			$dirname2 = $dirname."/".$file;
			$folder = $file;
			if($folder!="SF-Bay-Area") continue;
			echo $folder;
			echo "<br>";
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
			if(!is_dir('rest/finalxmlreviews/'.$folder)) {
				echo 'Creating folder '.$folder.' in finalxmlreviews folder';
				echo '<br>';
				mkdir('rest/finalxmlreviews/'.$folder,0777);
				chmod('rest/finalxmlreviews/'.$folder,0777);
			}
			if ($handle = opendir($dirname2)) {
				while (false !== ($file2 = readdir($handle))) {
					$filetype2 = filetype($dirname2."/".$file2);
					if($filetype2 == "file") {
						echo $file3 = $dirname2."/".$file2;
						echo "<br>";
						$input = file_get_contents($file3);
						echo $baseNameMain = str_replace(".html","",basename($file3));
						echo "<br>";
						$txtfileMain = $baseNameMain.".txt";
						$input2 = file_get_contents("neighboursarray/".$folder."/".$txtfileMain);
						$array = unserialize($input2);

						$urlToTakeMain = $array['url'];
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
							$pgUrl = $urlToTakeMain."?page=".$page;
							echo $pgUrl."<br>";				
							flush();
							if($page>0) {
								if(!file_exists('rest/listing/'.$folder."/".$baseNameMain."-".$page.".html")) {
									echo '<h1>sleeping for 15 secs</h1>';
									sleep(15);
									static $item=0; $item++;
									if($item%3==0) {
										echo __LINE__.". ITEM ($item): ";
										$Crawler->changeip();
									} else {
										echo __LINE__.". items ($item): ";
									}
									echo "<br>";
									$input3 = file_get_contents($pgUrl);
									
									if(!$input3) {
										echo 'cannot get file '.$pgUrl.' <br>';exit;
									}
									file_put_contents('rest/listing/'.$folder."/".$baseNameMain."-".$page.".html", $input3);
								}					
								$input4 = $Crawler->getHotelListingHTML($input3);
							} else {
								if(!file_exists('rest/listing/'.$folder."/".$baseNameMain."-".$page.".html")) {
									file_put_contents('rest/listing/'.$folder."/".$baseNameMain."-".$page.".html", $input);
								}
								$input4 = $Crawler->getHotelListingHTML($input);				
							}
							if(!file_exists('rest/pages/'.$folder."/".$baseNameMain."-".$page.".html")) {
								file_put_contents('rest/pages/'.$folder."/".$baseNameMain."-".$page.".html", $input4);
							}
							
							// start with another script from 10
							echo "<h3>Another script starting</h3>";
							$input = file_get_contents('rest/pages/'.$folder."/".$baseNameMain."-".$page.".html");	
							$input2 = file_get_contents('rest/listing/'.$folder."/".$baseNameMain."-".$page.".html");
							$infoMore = $Crawler->listingPage($input2);
							$info = $Crawler->getHotelListingDetailsLimited($input, $base);
							if($info) {
								foreach($info as $k=>$v) {
									$v['folder'] = $folder;		
									$v['lat'] = $infoMore[$v['id']]['lat'];
									$v['lon'] = $infoMore[$v['id']]['lon'];
									$v['neighbour'] = $infoMore[$v['id']]['neighbour'];
									if(!file_exists('rest/description/'.$folder.'/'.$v['id'].'.txt')) {
										$string = serialize($v);
										file_put_contents('rest/description/'.$folder.'/'.$v['id'].'.txt', $string);
										echo 'rest/description/'.$folder.'/'.$v['id'].'.txt created.<br>';
									} else {
										echo 'rest/description/'.$folder.'/'.$v['id'].'.txt already exists.<br>';
									}
									// another script starts here 11
									$input = file_get_contents('rest/description/'.$folder.'/'.$v['id'].'.txt');	
									$array = unserialize($input);
									print_r($array);
									echo "<br>";		
									$url = $array['url'];
									echo $url;
									echo "<br>";
									echo $baseName = $v['id'].".html";
									echo "<br>";
									if(!file_exists('rest/detailpages/'.$folder.'/'.$baseName)) {
										echo '<h1>sleeping for 15 secs</h1>';
										sleep(15);
										static $item=0; $item++;
										if($item%3==0) {
											echo __LINE__.". ITEM ($item): ";
											$Crawler->changeip();
										} else {
											echo __LINE__.". items ($item): ";
										}
										
										echo "<br>";
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
																		$input = file_get_contents('rest/detailpages/'.$folder.'/'.$baseName);	
									$baseName = $v['id'].".txt";
									$baseNameXML = $v['id'].".xml";
									$baseName2 = $v['id'];
									echo "<br>";
									$array = $Crawler->parseDetails($input);
									
									if(!$array) {
										echo 'could not parse on line '.__LINE__;
										exit;
									}
									$array['folder'] = $folder;
									$array['id'] = $baseName2;
									$array['rid'] = $array['country']."/".$array['province']."/".$array['city']."/".$array['folder'];
									$array['lat'] = $v['lat'];
									$array['lon'] = $v['lon'];
									$array['neighbour'] = $v['neighbour'];
									$string = serialize($array);
									print_r($array);
									$Crawler->insertRestaurant($array);
									$xml = $Crawler->createXmlString($array);
									if(!file_exists('rest/details/'.$folder.'/'.$baseName)) {
										file_put_contents('rest/details/'.$folder.'/'.$baseName, $string);
										echo 'rest/detail/'.$folder.'/'.$baseName.' created.<br>';
									} else {
										echo 'rest/detail/'.$folder.'/'.$baseName.' already exists.<br>';
									}
									if(!file_exists('rest/finalxml/'.$folder.'/'.$baseNameXML)) {
										file_put_contents('rest/finalxml/'.$folder.'/'.$baseNameXML, $xml['data']);
										echo 'rest/finalxml/'.$folder.'/'.$baseNameXML.' created.<br>';
									} else {
										echo 'rest/finalxml/'.$folder.'/'.$baseNameXML.' already exists.<br>';
									}
									if($xml['criticreviews']) {
										if(!file_exists('rest/finalxmlreviews/'.$folder.'/'.$baseNameXML)) {
											file_put_contents('rest/finalxmlreviews/'.$folder.'/'.$baseNameXML, $xml['criticreviews']);
											echo 'rest/finalxmlreviews/'.$folder.'/'.$baseNameXML.' created.<br>';
										} else {
											echo 'rest/finalxmlreviews/'.$folder.'/'.$baseNameXML.' already exists.<br>';
										}
									}
									sleep(2);
									flush();
								}
							} else {
								echo 'could not get info. '.__LINE__;
								exit;
							}
							sleep(2);						
						}
						sleep(2);
						flush();
						echo "<br>";
						echo "<br>";
					}
				}
			}
		}
	}
}
?>