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
			if(file_exists($dirname."/seeall/".$file)) {
				echo "see all exists.";
				echo "<br>";
			} else {
				echo "<font color=red>see all does not exists.</font>";
				echo "<br>";
			}
			flush();
		}
	}
	closedir($handle);
}

?>