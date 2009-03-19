<?php include('../Connections/conn.php');
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
include('Property.php');
// improvement start from first url
$time_start = microtime(true);
$Property = new Property;
if($_GET['id']) {
echo $sql = "SELECT * FROM property_xml WHERE id = '".$_GET['id']."'";
echo "<br>";
} else {
	echo 'enter id';
	exit;
}
$rs = mysql_query($sql) or die(mysql_query().'<meta http-equiv="refresh" content="30" />');
if(mysql_num_rows($rs)) {
	while($rec = mysql_fetch_array($rs)) {
		// initial settings
		$id = $rec['id'];
		print_r($rec);
		
		echo "<br>";
		$cn1 = strtolower($rec['country']);
		$i=0;
		$found = 0;
		$gotit = 0;	
		$urls = array();
		$first = array();
		$result = array();
		$otherpageurls = '';
		$first['review'] = 0;
		$first['rating'] = 0;
		$first['heading'] = '';
		$first['ftype'] = '';
		flush();
		
		// creating directories
		if(!is_dir("files")) {
			mkdir("files", 0777);
			chmod("files", 0777);
		}
		if(!is_dir("files/countries")) {
			mkdir("files/countries", 0777);
			chmod("files/countries", 0777);
		}
		$dir = "files/countries/".$cn1;
		if(!is_dir($dir)) {
			mkdir($dir, 0777);
			chmod($dir, 0777);
		}
		$dirbase = $dir."/base";
		if(!is_dir($dirbase)) {
			mkdir($dirbase, 0777);
			chmod($dirbase, 0777);
		}
		$dirfirst = $dir."/first";
		if(!is_dir($dirfirst)) {
			mkdir($dirfirst, 0777);
			chmod($dirfirst, 0777);
		}
		$dirbaselinks = $dir."/baselinks";
		if(!is_dir($dirbaselinks)) {
			mkdir($dirbaselinks, 0777);
			chmod($dirbaselinks, 0777);
		}
		$dirotherpages = $dir."/otherpages";
		if(!is_dir($dirotherpages)) {
			mkdir($dirotherpages, 0777);
			chmod($dirotherpages, 0777);
		}
		$dirfinalxml = $dir."/finalxml";
		if(!is_dir($dirfinalxml)) {
			mkdir($dirfinalxml, 0777);
			chmod($dirfinalxml, 0777);
		}
		$dirfinalxmlall = "files/tadv";
		if(!is_dir($dirfinalxmlall)) {
			mkdir($dirfinalxmlall, 0777);
			chmod($dirfinalxmlall, 0777);
		}
		$dirreviews = $dir."/reviews";
		if(!is_dir($dirreviews)) {
			mkdir($dirreviews, 0777);
			chmod($dirreviews, 0777);
		}
		
		echo "Fetching the base page: <br>";
		flush();
		$firstfile = $dirfirst."/".$id.".html";
		// fetching the base page
		$file = $dirbase."/".$id.".html";
		echo $file." first file: ".$firstfile;		
		echo "<br>";
		
		
		echo $baseurl = $rec['baseurl'];
		$result['base']['baseurl'] = $baseurl;	
		echo "Base url: ".$baseurl;
		echo "<br>";
		flush();
		echo "Fetching the base page content: <br>";
		flush();
			$content = file_get_contents($baseurl);
			file_put_contents($file, $content);
		echo "Fetching the base page: <br>";
		flush();
		// base page crawling results
		if(eregi("Sorry, but nothing matches your search", $content)) {
			echo "<h3>Sorry, but nothing matches your search</h3>";
			echo "<br>";
		} else {
			// do something new
			if(eregi("Location Results", $content)) {
				echo "<h3>Location Results is found.</h3>";
				echo "<br>";
				$first['found'] = $rec['gotit'];
				$first['ftype'] = $rec['ftype'];
				$return = $rec['firsturl'];
				if($first['found']==1) {
					$content2 = @file_get_contents($return);
					$fp = @file_put_contents($firstfile, $content2);				
					$first['finalUrl'] = $return;	
					$first['content2'] = $content2;							
					$found = $first['found'];
				}
				
			}
			if(!$found) {
				echo "<h3>Not Found.</h3>";
				echo "<br>";
			} else {
				$gotit = 1;
				$first['review'] = $Property->getReviewCount($first['content2']);
				$first['rating'] = $Property->getAvgRating($first['content2']);
				$first['heading'] = $Property->getHeading($first['content2']);
				
				//echo "Regular expression starts here<br/>";
				$RSEXP = "<div class=\"review.*<div class=\"quote\">.*<a.*>(.*)<\/a>.*<\/div>.*<span class=\"rate s(.*)0\">.*<div class=\"username\">(.*)<\/div>.*<div class=\"date.*\">(.*)<\/div>.*<div class=\"entry\">(.*)<\/div>.*<\/div>.*<\/div>"; //
				if(preg_match_all("/$RSEXP/siU", $first['content2'], $matches, PREG_SET_ORDER)) {
					foreach($matches as $match) {
						$result['reviews'][$i]['review'] = $match[2];
						$result['reviews'][$i]['title'] = $match[1];
						$result['reviews'][$i]['author'] = trim(strip_tags($match[3]));
						$result['reviews'][$i]['date'] = trim(strip_tags($match[4]));
						$result['reviews'][$i]['description'] = trim(strip_tags($match[5]));
						$result['reviews'][$i]['source'] = $first['finalUrl'];
						$i++;
					}
				}
				if($first['review']>10) {
					$pgUrl = $Property->getOtherPageUrl($first['content2']);
					
					$urls = array();
					$perpage = 10;
					if($first['review']>100) $first['review'] =100;
					$s = $Property->getmodpages($first['review'],$perpage);	
					foreach($s as $k=>$v) {
						if($k==0) continue;
						$tmpUrl = str_replace("-or10", "-or".$v, $pgUrl);
						$urls[] = $tmpUrl;
						
						$filename = $dirotherpages."/".$rec['id']."_".$k.".html";
						
							$body = @file_get_contents($tmpUrl);	
							$put = @file_put_contents($filename, $body);
						
						// step 3
						$RSEXP = "<div class=\"review.*<div class=\"quote\">.*<a.*>(.*)<\/a>.*<\/div>.*<span class=\"rate s(.*)0\">.*<div class=\"username\">(.*)<\/div>.*<div class=\"date.*\">(.*)<\/div>.*<div class=\"entry\">(.*)<\/div>.*<\/div>.*<\/div>"; //
						if(preg_match_all("/$RSEXP/siU", $body, $matches, PREG_SET_ORDER)) {
							foreach($matches as $match) {
								$result['reviews'][$i]['review'] = $match[2];
								$result['reviews'][$i]['title'] = $match[1];
								$result['reviews'][$i]['author'] = trim(strip_tags($match[3]));
								$result['reviews'][$i]['date'] = trim(strip_tags($match[4]));
								$result['reviews'][$i]['description'] = trim(strip_tags($match[5]));					
								$result['reviews'][$i]['source'] = $tmpUrl;
								$i++;
							}
						}	
						
					}
				}				
			}
		}
		if($result['reviews']) {
			$source = '<source name="Trip Advisor" avg-rating="'.$Property->cleanupRatings($first['rating']).'">';
			foreach($result['reviews'] as $value) {
				$data['rating'] = $value['review'];
				$data['reviewer'] = $value['author'];
				$data['reviewdate'] = $value['date'];
				$data['review_title'] = $value['title'];
				$data['review_detail'] = $value['description'];
				$data['source'] = $value['source'];
				$source .= $Property->createXML($data);
			}
			$source .= '
</source>';
		}
		if($result['reviews']) {
			file_put_contents($dirfinalxml."/".$id.".xml", $source);
			$xmlpath = $rec['xmlpath'];
			$explode = explode("/", $xmlpath);
			if($explode) {
				$newDir = $dirfinalxmlall."/";
				foreach($explode as $direc) {
					if(eregi(".xml", $direc)) {
						$newDir .= $direc;					
						file_put_contents($newDir, $source);
					} else {
						$newDir .= $direc."/";
						if(!is_dir($newDir)) {
							mkdir($newDir, 0777);
							chmod($newDir, 0777);
						}
					}
				}
			}
		}
		if($result['reviews']) {
			$reviews = '';
			$reviews = serialize($result['reviews']);
			file_put_contents($dirreviews."/".$id.".txt", $reviews);
		}
		if($urls) {
			$otherpageurls = implode("::", $urls);
			
				file_put_contents($dirbaselinks."/".$id.".txt",implode("\n", $urls));
			
		}
		$ts = "update property_xml set flag = 1, ftype = '".$Property->clean($first['ftype'])."', heading = '".$Property->clean($first['heading'])."', avgrating = '".$Property->clean($first['rating'])."', totalreview = '".$Property->clean($first['review'])."', otherpageurls = '".$Property->clean($otherpageurls)."', otherpageurlflag = '1', gotit = '".$Property->clean($gotit)."'  where id = '".$rec['id']."'";
		echo $ts;
		mysql_query($ts) or die(mysql_error());
		echo "<pre>";
		print_r($result['reviews']);
		echo "</pre>";
		echo "<hr>";
		flush();
	}
	//echo '<meta http-equiv="refresh" content="15" />';
} else {
	echo 'no record found';
}
exit;
// ending
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "<hr>Time taken: ".number_format($time,6);

?>