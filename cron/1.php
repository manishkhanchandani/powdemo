<?php
ini_set('memory_limit','500M');
ini_set('max_execution_time','-1'); 
$itemNum=0;
class RSSParser	{
	var $channel_title="";
	var $channel_website="";
	var $channel_description="";
	var $channel_pubDate="";
	var $channel_lastUpdated="";
	var $channel_copyright="";
	var $title="";
	var $link="";
	var $description="";
	var $pubDate="";
	var $author="";
	var $url="";
	var $width="";
	var $height="";
	var $inside_tag=false;	
	function RSSParser($file)	{
			$this->xml_parser = xml_parser_create();
			xml_set_object( $this->xml_parser, &$this );
			xml_set_element_handler( $this->xml_parser, "startElement", "endElement" );
			xml_set_character_data_handler( $this->xml_parser, "characterData" );
			if($fp = @fopen("$file","r")) {
			
			} else {
				return false;
			}
			while ($data = fread($fp, 4096)){xml_parse( $this->xml_parser, $data, feof($fp)) or die( "XML error");}
			fclose($fp);
			xml_parser_free( $this->xml_parser );
		}
	
	function startElement($parser,$tag,$attributes=''){
		$this->current_tag=$tag;
		if($this->current_tag=="ITEM" || $this->current_tag=="IMAGE"){
			$this->inside_tag=true;
			$this->description="";
			$this->link="";
			$this->title="";
			$this->pubDate="";
		}
	}
	
	function endElement($parser, $tag){
		switch($tag){
			case "ITEM":
				$this->titles[]=trim($this->title);
				$this->links[]=trim($this->link);
				$this->descriptions[]=trim($this->description);
				$this->pubDates[]=trim($this->pubDate);
				$this->authors[]=trim($this->author);
				$this->author=""; $this->inside_tag=false;
				break;
			case "IMAGE":
				$this->channel_image="<img src=\"".trim($this->url)."\" width=\"".trim($this->width)."\" height=\"".trim($this->height)."\" alt=\"".trim($this->title)."\" border=\"0\" title=\"".trim($this->title)."\" />";
				$this->title=""; $this->inside_tag=false;
			default:
				break;
		}
	}
	
	function characterData($parser,$data){
		if($this->inside_tag){
			switch($this->current_tag){
				case "TITLE":
					$this->title.=$data; break;
				case "DESCRIPTION":
					$this->description.=$data; break;
				case "LINK":
					$this->link.=$data; break;
				case "URL":
					$this->url.=$data; break;					
				case "WIDTH":
					$this->width.=$data; break;
				case "HEIGHT":
					$this->height.=$data; break;
				case "PUBDATE":
					$this->pubDate.=$data; break;
				case "AUTHOR":
					$this->author.=$data;	break;
				default: break;									
			}//end switch
		}else{
			switch($this->current_tag){
				case "DESCRIPTION":
					$this->channel_description.=$data; break;
				case "TITLE":
					$this->channel_title.=$data; break;
				case "LINK":
					$this->channel_website.=$data; break;
				case "COPYRIGHT":
					$this->channel_copyright.=$data; break;
				case "PUBDATE":
					$this->channel_pubDate.=$data; break;					
				case "LASTBUILDDATE":
					$this->channel_lastUpdated.=$data; break;				
				default:
					break;
			}
		}
	}
}
?><?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsView = "SELECT a.*, b.locations FROM location_details as a LEFT JOIN location as b ON a.loc_id = b.loc_id WHERE a.rss != '' GROUP BY a.source";
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php if ($totalRows_rsView > 0) { // Show if recordset not empty ?>
  <h1>Result</h1>
  <?php do { ?>
  <p><?php echo "<pre>"; print_r($row_rsView); echo "</pre>";  ?></p>
<?php
$myRss = new RSSParser($row_rsView['rss']);
if(!$myRss) continue;
$myRss_RSSmax=0;
if($myRss_RSSmax==0 || $myRss_RSSmax>count($myRss->titles))$myRss_RSSmax=count($myRss->titles);
for($itemNum=0;$itemNum<$myRss_RSSmax;$itemNum++){?>
   <?php   
$dir = "files/".$row_rsView['locations'];
if(!is_dir($dir)) {
	mkdir($dir, 0777);
	chmod($dir, 0777);
}
  echo $filename = $dir."/".eregi_replace("[^A-Za-z0-9]","",$myRss->titles[$itemNum])."_".strtotime($myRss->pubDates[$itemNum])."_".$row_rsView['locations'].".xml"; echo "<br>"; ?>
<?php
$xml = "<add>
<doc>
<field name='titles'>".$myRss->titles[$itemNum]."</field>
<field name='descriptions'>".$myRss->descriptions[$itemNum]."</field>
<field name='links'>".$myRss->links[$itemNum]."</field>
<field name='pubDates'>".$myRss->pubDates[$itemNum]."</field>
<field name='locations'>".$row_rsView['locations']."</field>
<field name='timestamp'>".strtotime($myRss->pubDates[$itemNum])."</field>
<field name='rssurl'>".$row_rsView['rss']."</field>
</doc>
</add>";
if(!file_exists($filename)) {
	echo 'content put successfully.';
	echo "<br>";
	file_put_contents($filename, $xml);
} else {
	echo 'file already exists.';
	echo "<br>";
}
flush();

  ?>
    <?php } ?>
  <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
  <p><?php echo $totalRows_rsView ?> </p>
  <?php } // Show if recordset not empty ?></body>
</html>
<?php
mysql_free_result($rsView);
?>
