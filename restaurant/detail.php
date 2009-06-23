<?php
include('../Classes/Crawler.php');
$Crawler = new Crawler;
$input = file_get_contents("page.html");
$regexp = "<h1 class=\"page-title.*\">(.*)<\/h1>";
$matches = $Crawler->regexp($regexp, $input);
$arr['title'] = $matches[0][1];

$regexp = " <div>.*<span class=\"phone tel\">(.*)<\/span>.*<\/div>";
$matches = $Crawler->regexp($regexp, $input);
$arr['phone'] = $matches[0][1];

$regexp = " <p class=\"rest-info\">(.*)<\/p>";
$matches = $Crawler->regexp($regexp, $input);
$info = $matches[0][1];

$regexp = "<span class=\"street-address\">(.*)<\/span>";
$matches = $Crawler->regexp($regexp, $info);
$arr['streeaddr'] = $matches[0][1];

$regexp = "<span class=\"locality\">(.*)<\/span>";
$matches = $Crawler->regexp($regexp, $info);
$arr['locality'] = $matches[0][1];

$regexp = "<span class=\"region\">(.*)<\/span>";
$matches = $Crawler->regexp($regexp, $info);
$arr['region'] = $matches[0][1];

$regexp = "<a href=\"(.*)\" class=\".*postal\-code\">(.*)<\/a>";
$matches = $Crawler->regexp($regexp, $info);
$arr['zip'] = $matches[0][2];

$regexp = "<a href=\".*\" class=\"url\".*>(.*)<\/a>";
$matches = $Crawler->regexp($regexp, $info);
$arr['linktext'] = $matches[0][1];
$tmp1 = explode("<!-- REVIEWS TAB -->", $input);
$tmp2 = explode("<!-- POSTS TAB -->", $tmp1[1]);
$input2 = $tmp2[0];

$regexp = " <tr>(.*)<\/tr>";
$matches = $Crawler->regexp($regexp, $input2);
if($matches) {
	foreach($matches as $k=>$match) {
		$info2 = $match[1];
		echo htmlentities($info2);
		echo "<hr>";
		$regexp = "<span class=\"review\-score\">(.*)<\/span>";
		$matches2 = $Crawler->regexp($regexp, $info2);
		$arr['critic-reviews'][$k]['score'] = $matches2[0][1];
		
		
		$regexp = "<span class=\"review\-title\"><a href=\"(.*)\".*>(.*)<\/a><\/span>";
		$matches2 = $Crawler->regexp($regexp, $info2);
		$arr['critic-reviews'][$k]['title'] = $matches2[0][1];
		$regexp = "<span class=\"com\-date\">(.*) \- <\/span>(.*)<br\/>";
		$matches2 = $Crawler->regexp($regexp, $info2);
		$arr['critic-reviews'][$k]['date'] = $matches2[0][1];
		$arr['critic-reviews'][$k]['desc'] = trim($matches2[0][2]);
		if(!$arr['critic-reviews'][$k]['desc']) {
			$regexp = "<\/span>.*<br\/>(.*)<br\/>";
			$matches2 = $Crawler->regexp($regexp, $info2);
			$arr['critic-reviews'][$k]['desc'] = trim($matches2[0][1]);
		}
	}
}

echo "<pre>";
print_r($arr);
?>