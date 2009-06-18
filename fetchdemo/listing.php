<?php
include('../Classes/Crawler.php');
$Crawler = new Crawler;

$input = file_get_contents('listing.html');
$return = $Crawler->getListing($input);
$header = $Crawler->excelHeader();
$excel = $Crawler->excelBody($return);
$filename = "test.xls";
$header = $Crawler->export($filename, $header.$excel);

?>