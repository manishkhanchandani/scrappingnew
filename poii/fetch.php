<?php

$sql = "";
$rs = mysql_fetch_array($sql) or die('error '.mysql_error());
if(mysql_num_rows($rs)) {
	while($rec = mysql_fetch_array($rs)) {
		$result = array();
		$first = array();
	
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
		$baseurl = "http://www.tripadvisor.com/Search?q=".urlencode($rec['name'])."+".urlencode($rec['city'])."&geo=1&returnTo=__2F__&hid=&verbose=&hur=&ssrc=h";
		
		if($content = @file_get_contents($baseurl)) {
		
		} else {
			$poi->poi_notfound($rec['id']);
			mail("naveenkhanchandani@gmail.com", "Redo for ".$rec['id'], "Redo for ".$rec['id']." for table poii_xml", "poii@mkgalaxy.com");
			continue;
		}
		
		// crawl base content and get the first url
		if(eregi("Sorry, but nothing matches your search", $content)) {
			echo "<h3>Sorry, but nothing matches your search</h3>";
			echo "<br>";
			$poi->poi_notfound($rec['id']);
			continue;
		} else {	
			if(eregi("Location Results", $content)) {
				$tmp1 = explode("Location Results", $content);
				$tmp2 = explode("</table>", $tmp1[1]);
				$input = $tmp2[0];
				$regexp = "<tr>.*<td.*class=\".*searchPadding\".*>(.*)<\/td>.*<td.*class=\".*searchPadding\".*>(.*)<\/td><\/tr>";
				if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
					foreach($matches as $match) {
						$return = $this->parseLinks($match[2]);
						if(eregi("http://", $return)) {
							$return = $return;
						} else {
							$return = "http://www.tripadvisor.com".$return;
						}
						$content2 = @file_get_contents($return);				
						$result = $poi->validateplace($content2, $rec);
						if($result['found']) {
							$result['finalUrl'] = $return;	
							$result['content2'] = $content2;				
							$sql = "update property_xml set firsturl = '".$poi->clean($return)."', firsturlflag = 1 where id = '".$rec['id']."'";
							mysql_query($sql) or die('error'.__LINE__." ".mysql_error());					
							break;
						}
					}
				} else {	
					$poi->poi_notfound($rec['id']);
					continue;
				}
			} else {				
				$poi->poi_notfound($rec['id']);
				continue;
			}
		}
		
		if($result['found']) {
			$first['review'] = $poi->getReviewCount($result['content2']);
			$first['rating'] = $poi->getAvgRating($result['content2']);
			$first['heading'] = $poi->getHeading($result['content2']);
		} else {
			$poi->poi_notfound($rec['id']);
			continue;
		}
	}
} else {
	echo 'no record found.';
}
?>