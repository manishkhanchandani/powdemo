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
					@mysql_query($sql);
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
	
	public function getHotelListingHTML($input) {
		$regexp = "<table.*id=\"r\-t\".*>(.*)<\/table>";
		$matches = $this->regexp($regexp, $input);
		return $matches[0][1];
	}
	
	public function getHotelListingDetailsLimited($input, $base) {
		$arr = explode('<tr>', $input);
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
				$arr2[$k] = $arr1[$k];
			}
		} else {
			echo 'could not get result2';
			exit;
		}
		return $arr2;
	}
	
	public function parseDetails($input) {
		$regexp = "<h1 class=\"page-title.*\">(.*)<\/h1>";
		$matches = $this->regexp($regexp, $input);
		$arr['title'] = $matches[0][1];
		
		$regexp = " <div>.*<span class=\"phone tel\">(.*)<\/span>.*<\/div>";
		$matches = $this->regexp($regexp, $input);
		$arr['phone'] = $matches[0][1];
		
		$regexp = " <p class=\"rest\-info\">(.*)<\/p>";
		$matches = $this->regexp($regexp, $input);
		$info = $matches[0][1];
		
		$regexp = "<span class=\"street\-address\">(.*)<\/span>";
		$matches = $this->regexp($regexp, $info);
		$arr['streeaddr'] = $matches[0][1];
		
		$regexp = "<span class=\"locality\">(.*)<\/span>";
		$matches = $this->regexp($regexp, $info);
		$arr['locality'] = $matches[0][1];
		$arr['city'] = $matches[0][1];
		$arr['country'] = "US";
		
		$regexp = "<span class=\"region\">(.*)<\/span>";
		$matches = $this->regexp($regexp, $info);
		$arr['region'] = $matches[0][1];
		$arr['province'] = $matches[0][1];
		
		$regexp = "<a href=\"(.*)\" class=\".*postal\-code\">(.*)<\/a>";
		$matches = $this->regexp($regexp, $info);
		$arr['zip'] = $matches[0][2];
		
		$regexp = "<a href=\".*\" class=\"url\".*>(.*)<\/a>";
		$matches = $this->regexp($regexp, $info);
		$arr['linktext'] = $matches[0][1];
		
		$regexp = "<span class=\"pricerange\">(.*)<\/span>";
		$matches = $this->regexp($regexp, $input);
		$arr['pricerange'] = $matches[0][1];
		
		$regexp = "<img alt=\"\\\$\" src=\"http:\/\/static\.urbanspoon\.com\/1\/dollar\.gif\" style=\"width:8px;height:10px\" \/>";
		$matches = $this->regexp($regexp, $input);
		$arr['dollarcount'] = count($matches);
		$tmp3 = explode("<!-- MENU TAB -->", $input);
		$tmp4 = explode("<!-- FRIENDS TAB -->", $tmp3[1]);
		$inputMenu = $tmp4[0];
		
		$regexp = "<b><a href=\".*\">(.*)<\/a><\/b>";
		$matches = $this->regexp($regexp, $inputMenu);
		if($matches) {
			foreach($matches as $match) {
				$arr['cusine'][] = $match[1];
			}
		}
		
		$tmp1 = explode("<!-- REVIEWS TAB -->", $input);
		$tmp2 = explode("<!-- POSTS TAB -->", $tmp1[1]);
		$input2 = $tmp2[0];
		
		$regexp = " <tr>(.*)<\/tr>";
		$matches = $this->regexp($regexp, $input2);
		if($matches) {
			foreach($matches as $k=>$match) {
				$info2 = $match[1];
				$regexp = "<span class=\"review\-score\">(.*)<\/span>";
				$matches2 = $this->regexp($regexp, $info2);
				$arr['critic-reviews'][$k]['score'] = $matches2[0][1];				
				$regexp = "<span class=\"review\-title\"><a href=\"(.*)\".*>(.*)<\/a><\/span>";
				$matches2 = $this->regexp($regexp, $info2);
				$arr['critic-reviews'][$k]['title'] = $matches2[0][1];
				$regexp = "<span class=\"com\-date\">(.*) \- <\/span>(.*)<br\/>";
				$matches2 = $this->regexp($regexp, $info2);
				$arr['critic-reviews'][$k]['date'] = $matches2[0][1];
				$arr['critic-reviews'][$k]['desc'] = trim($matches2[0][2]);
			}
		}
		return $arr;
	}
}
?>