<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn = "64.186.128.115";
$database_conn = "scrapping1";
$username_conn = "manishkk";
$password_conn = "manishkk";
$conn = mysql_connect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_conn, $conn);
?>
<?php
//include('../Connections/conn.php');
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
$time_start = microtime(true);
include('POI.php');
$poi = new POI;
$limit = $_GET['limit']; if(!$limit) $limit = 10;

if($_GET['s']) {
	echo $sql = "select * from poii_xml where imp1 = 0 and poi_id != 0 and gotit = 0 limit $s,$limit";
} else if($_GET['cns']) {
	$cns = explode(",",strtolower($_GET['cns']));
	$cns2 = "'".implode("', '", $cns)."'";
	echo $sql = "select * from poii_xml where imp1 = 0 and country IN (".$cns2.") and poi_id != 0 and gotit = 0 limit $limit";
} else if($_GET['cn']) {
	echo $sql = "select * from poii_xml where imp1 = 0 and country = '".$cn."' and poi_id != 0 and gotit = 0 limit $limit";
} else if($_GET['id']) {
	echo $sql = "select * from poii_xml where id = '".$_GET['id']."'";
} else {
	echo $sql = "select * from poii_xml where imp1 = 0 and poi_id != 0 and gotit = 0 limit $limit";
}
echo "<br>";
$rs = mysql_query($sql) or die(mysql_error().'<meta http-equiv="refresh" content="30" />');
if(mysql_num_rows($rs)) {
	while($rec = mysql_fetch_array($rs)) {
		$result = array();
		$first = array();
		$gotit = 1;
		$source = '';
		$i=0;
	
		// creating directories
		if(!is_dir("files")) {
			mkdir("files", 0777);
			chmod("files", 0777);
		}
		
		$dirfinalxmlall = "files/tadv_poi";
		if(!is_dir($dirfinalxmlall)) {
			mkdir($dirfinalxmlall, 0777);
			chmod($dirfinalxmlall, 0777);
		}
		// fetch the url
		$baseurl = "http://www.tripadvisor.com/Search?q=".urlencode($rec['name'])."+".urlencode($rec['city']);
		echo "<br>";
		echo $baseurl;
		echo "<br>";
		echo $sql = "update poii_xml set baseurl = '".$poi->clean($baseurl)."', baseflag = 1 where id = '".$rec['id']."'";
		echo "<br>";
		mysql_query($sql) or die('error'.__LINE__." ".mysql_error());	
					
		if($content = @file_get_contents($baseurl)) {
		
		} else {
			$poi->poi_notfound($rec['id']);
			echo "<h3>Cannot parse the base file</h3>";
			echo "<br>";
			mail("naveenkhanchandani@gmail.com", "Redo for ".$rec['id'], "Redo for ".$rec['id']." and line no. ".__LINE__." of file ".__FILE__." for table poii_xml", "poii@mkgalaxy.com");
			continue;
		}
		
		// crawl base content and get the first url
		if(eregi("Sorry, but nothing matches your search", $content)) {
			echo "<h3>Sorry, but nothing matches your search</h3>";
			echo "<br>";
			$poi->poi_notfound($rec['id']);
			continue;
			exit;
		} 	
		if(!eregi("Location Results", $content)) {		
			echo "<h3>No Location</h3>";
			echo "<br>";
			$poi->poi_notfound($rec['id']);
			continue;
			exit;
		} 
		echo "<h3>Location found</h3>";
		echo "<br>";
		$tmp1 = explode("Location Results", $content);
		$tmp2 = explode("</table>", $tmp1[1]);
		$input = $tmp2[0];
		$regexp = "<tr>.*<td.*class=\".*searchPadding\".*>(.*)<\/td>.*<td.*class=\".*searchPadding\".*>(.*)<\/td><\/tr>";
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				$return = $poi->parseLinks($match[2]);
				if(eregi("http://", $return)) {
					$return = $return;
				} else {
					$return = "http://www.tripadvisor.com".$return;
				}
				$content2 = @file_get_contents($return);				
				$first = $poi->validateplaceImp($content2, $rec);
				if($first['found']) {
					$first['finalUrl'] = $return;	
					$first['content2'] = $content2;				
					echo $sql = "update poii_xml set firsturl = '".$poi->clean($return)."', firsturlflag = 1 where id = '".$rec['id']."'";
					echo "<br>";
					mysql_query($sql) or die('error'.__LINE__." ".mysql_error());					
					break;
				}
			}
		} else {
			$poi->poi_notfound($rec['id']);
			continue;
			exit;	
		}
		if($first['found']) {
			$first['review'] = $poi->getReviewCount($first['content2']);
			$first['rating'] = $poi->getAvgRating($first['content2']);
			$first['heading'] = $poi->getHeading($first['content2']);
			
			//echo "Regular expression starts here<br/>";
			$RSEXP = "<div class=\"review.*<div class=\"quote\">.*<a.*>(.*)<\/a>.*<\/div>.*<span class=\"rate s(.*)0\">.*<div class=\"username\">(.*)<\/div>.*<div class=\"date.*\">(.*)<\/div>.*<div class=\"entry\">(.*)<\/div>.*<\/div>.*<\/div>"; //
			if(preg_match_all("/$RSEXP/siU", $first['content2'], $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					$result['reviews'][$i]['review'] = $match[2];
					$result['reviews'][$i]['title'] = $match[1];
					$result['reviews'][$i]['author'] = trim(strip_tags($match[3]));
					$result['reviews'][$i]['date'] = trim(strip_tags($match[4]));
					$result['reviews'][$i]['description'] = substr(trim(strip_tags($match[5])),0,150);
					$result['reviews'][$i]['source'] = $first['finalUrl'];
					$i++;
				}
			}
			
			
			
			if($first['review']>10) {
				$pgUrl = $poi->getOtherPageUrl($first['content2']);
				
				$urls = array();
				$perpage = 10;
				if($first['review']>100) $first['review'] =100;
				$s = $poi->getmodpages($first['review'],$perpage);	
				foreach($s as $k=>$v) {
					if($k==0) continue;
					$tmpUrl = str_replace("-or10", "-or".$v, $pgUrl);
					$urls[] = $tmpUrl;
					
					if($body = @file_get_contents($tmpUrl)) {
					
					} else {
						mail("naveenkhanchandani@gmail.com", "Redo for ".$rec['id'], "Redo for ".$rec['id']." and line no. ".__LINE__." of file ".__FILE__." for table poii_xml", "poii@mkgalaxy.com");
					}
					
					// step 3
					$RSEXP = "<div class=\"review.*<div class=\"quote\">.*<a.*>(.*)<\/a>.*<\/div>.*<span class=\"rate s(.*)0\">.*<div class=\"username\">(.*)<\/div>.*<div class=\"date.*\">(.*)<\/div>.*<div class=\"entry\">(.*)<\/div>.*<\/div>.*<\/div>"; //
					if(preg_match_all("/$RSEXP/siU", $body, $matches, PREG_SET_ORDER)) {
						foreach($matches as $match) {
							$result['reviews'][$i]['review'] = $match[2];
							$result['reviews'][$i]['title'] = $match[1];
							$result['reviews'][$i]['author'] = trim(strip_tags($match[3]));
							$result['reviews'][$i]['date'] = trim(strip_tags($match[4]));
							$result['reviews'][$i]['description'] = substr(trim(strip_tags($match[5])),0,150);					
							$result['reviews'][$i]['source'] = $tmpUrl;
							$i++;
						}
					}						
				}
			}	
		
		
			
			if($result['reviews']) {
				$source = '<source name="Trip Advisor" avg-rating="'.$poi->cleanupRatings($first['rating']).'">';
				foreach($result['reviews'] as $value) {
					$data['rating'] = $value['review'];
					$data['reviewer'] = $value['author'];
					$data['reviewdate'] = $value['date'];
					$data['review_title'] = $value['title'];
					$data['review_detail'] = $value['description'];
					$data['source'] = $value['source'];
					$source .= $poi->createXML($data);
				}
				$source .= '
</source>';
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
				
				if($urls) {
					$otherpageurls = implode("::", $urls);
				}
				$ts = "update poii_xml set flag = 1, ftype = '".$poi->clean($first['ftype'])."', heading = '".$poi->clean($first['heading'])."', avgrating = '".$poi->clean($first['rating'])."', totalreview = '".$poi->clean($first['review'])."', otherpageurls = '".$poi->clean($otherpageurls)."', otherpageurlflag = '1', gotit = '".$poi->clean($gotit)."', reviewxml = '".$poi->clean($source)."'  where id = '".$rec['id']."'";
				echo "<br>";
				echo $ts;
				mysql_query($ts) or die(mysql_error());
				echo "<pre>";
				print_r($result['reviews']);
				echo "</pre>";
				echo "<hr>";
				flush();
			} else {
				$ts = "update poii_xml set imp1 = 1, flag = 1, ftype = '".$poi->clean($first['ftype'])."', heading = '".$poi->clean($first['heading'])."', avgrating = '".$poi->clean($first['rating'])."', totalreview = '".$poi->clean($first['review'])."', otherpageurls = '".$poi->clean($otherpageurls)."', otherpageurlflag = '1', gotit = '".$poi->clean($gotit)."', reviewxml = ''  where id = '".$rec['id']."'";
				echo "<br>";
				echo $ts;
				mysql_query($ts) or die(mysql_error());
			}
				
		} else {
			$poi->poi_notfound($rec['id']);
			continue;
			exit;
		}
	}
	echo '<meta http-equiv="refresh" content="5" />';
} else {
	echo 'no record found.';
}
?>