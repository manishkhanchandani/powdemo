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
}
?>