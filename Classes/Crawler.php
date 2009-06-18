<?php
class Crawler {
	public function regexp($regexp, $input) {
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
			return $matches;
		}
	}
	public function excelHeader() {
		$header = "Link\tTitle\tSource\tDate\tSummary\tFull Review Link\tAll Review Link\n";
		return $header;
	}
	public function excelBody($return) {
		if($return) {
			foreach($return as $k => $v) {
				$excel .= $v['link']."\t".$v['title']."\t".$v['source']."\t".$v['date']."\t".$v['summary']."\t".$v['fullreview']."\t".$v['allreview']."\n";
			}
		}
		return $excel;
	}
	public function export($filename, $excel) {
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=$filename");
		header("Pragma: no-cache");
		header("Expires: 0");
		print "$excel"; 
	}
	public function getListing($input) {
		$arr = explode("<h1 class=\"small-title\">Recent critic reviews", $input);
		$inp1 = $arr[1];
		
		$arr2 = explode("</table>", $inp1);
		$inp2 = $arr2[0];
		
		$regexp = "<tr>.*<td.*>.*<\/td>.*<td>.*<span.*><a href=\"(.*)\">(.*)<\/a><\/span> by <i>(.*)<\/i>.*<span class=\"com\-date\">(.*)\-<\/span>(.*)<a href=\"(.*)\".*>full review<\/a> \| <a href=\"(.*)\".*>all(.*)reviews<\/a>.*<\/td>.*<\/tr>";
		$matches = $this->regexp($regexp, $inp2);
		
		$baseUrl = "http://www.urbanspoon.com";
		if($matches) {
			foreach($matches as $k=>$match) {
				$return[$k]['link'] = $baseUrl.trim($match[1]);
				$return[$k]['title'] = trim($match[2]);
				$return[$k]['source'] = trim($match[3]);
				$return[$k]['date'] = trim($match[4]);
				$return[$k]['cdate'] = strtotime(trim($match[4]));
				$return[$k]['cdate2'] = date("r", strtotime(trim($match[4])));
				$summary = trim($match[5]);
				$summary = str_replace("\n", " ", $summary);
				$summary = str_replace("\t", " ", $summary);
				$return[$k]['summary'] = $summary;
				$return[$k]['fullreview'] = trim($match[6]);
				$return[$k]['allreview'] = $baseUrl.trim($match[7]);
			}
		}
		return $return;
	}
	
	public function getHotelListingDetails($input, $base, $page, $row_rsView) {
		$regexp = "<table.*id=\"r\-t\".*>(.*)<\/table>";
		$matches = $this->regexp($regexp, $input);
		echo "<pre>";
		if($matches[0][1]) {
			$arr = explode('<tr>', $matches[0][1]);
			if($arr) {
				foreach($arr as $k=>$v) {
					$v = trim($v);
					if(substr($v,0,3)!="<td") {
						continue;
					}
					if(!eregi("google_ad_client", $v)) {
						$regexp = "<div class=\"t\">.*<a href=\"(.*)\">(.*)<\/a>";
						$matches2 = $this->regexp($regexp, $v);
						if($matches2) {
							$arr1[$k]['url'] = $base.$matches2[0][1];
							$arr1[$k]['name'] = $matches2[0][2];
						}
						$regexp = "<a class=\"image\" href=\"#\" onclick=\"placePopup\(this\); new Ajax.Updater\('vote-popup', '.*\/([0-9]*)', \{asynchronous:true, evalScripts:true, onComplete:function\(request\)\{showPopup\(\)\}\}\); return false;\">";
						$matches2 = $this->regexp($regexp, $v);
						if($matches2) {
							$arr1[$k]['id'] = $matches2[0][1];
						}						
						$regexp = "<\/div>(.*)<td";
						$matches2 = $this->regexp($regexp, $v);
						if($matches2) {
							$arr1[$k]['info'] = nl2br(trim($matches2[0][1]));
							if(eregi("<br \/>", $arr1[$k]['info'])) {
								$regexp = "(.*)\-.*<br \/>.*<br \/>(.*)$";
								$matches3 = $this->regexp($regexp, $arr1[$k]['info']);
								$arr1[$k]['info2'] = trim($matches3[0][1]);
								$arr1[$k]['address'] = trim($matches3[0][2]);
							} else {
								$arr1[$k]['address'] = trim($matches2[0][1]);
							}
						}
					}
					if(!$arr1[$k]['id']) continue;
					$sql = "insert into location_hotels set nid = '".addslashes(stripslashes(trim($row_rsView['nid'])))."', hotelsiteid = '".addslashes(stripslashes(trim($arr1[$k]['id'])))."', page = '".$page."', name = '".addslashes(stripslashes(trim($arr1[$k]['name'])))."', link = '".addslashes(stripslashes(trim($arr1[$k]['url'])))."', neighbour = '".addslashes(stripslashes(trim($row_rsView['titles'])))."', country = 'United States', info = '".addslashes(stripslashes(trim($arr1[$k]['info'])))."', info2 = '".addslashes(stripslashes(trim($arr1[$k]['info2'])))."', address = '".addslashes(stripslashes(trim($arr1[$k]['address'])))."'";
					echo $sql;
					mysql_query($sql) or die('error'.mysql_error());
					echo "<br>";
					echo "<br>";
					echo "<hr>";
					flush();
				}
			} else {
				echo 'could not get result2';
				exit;
			}
		} else {
			echo 'could not get result';
			exit;
		}
	}
}
?>