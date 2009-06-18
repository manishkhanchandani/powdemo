<?php
include('../Classes/Crawler.php');
$Crawler = new Crawler;

$input = file_get_contents('listing.html');
$return = $Crawler->getListing($input);
echo "<pre>";
print_r($return);

?>