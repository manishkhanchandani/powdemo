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
	}
}
