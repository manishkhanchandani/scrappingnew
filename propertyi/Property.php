<?php
class Property {
	public $arrF = array();
	
	// recursive directory function
	public function RecurseDir($directory) {
		$thisdir = array("name", "struct");
		$thisdir['name'] = $directory;
		if ($dir = @opendir($directory)) {
			$i = 0;
			while ($file = readdir($dir)) {
				if (($file != ".")&&($file != "..")) {
					$tempDir = $directory."/".$file;
					if (is_dir($tempDir)) {
						$thisdir['struct'][] = $this->RecurseDir($tempDir,$file);
					} else {
						$thisdir['struct'][] = $file;
						array_push($this->arrF, $directory."/".$file);
					}
					$i++;
				}
			}
			if ($i == 0) {
				// empty directory
				$thisdir['struct'] = -2;
			}
		} else {
			// directory could not be accessed
			$thisdir['struct'] = -1;
		}
		return $thisdir;
	}
	
	public function clean($text) {
		return addslashes(stripslashes(trim($text)));
	}
	
	// insert in db
	public function insertxmlindb($data) {
		$sql = "INSERT INTO `property_xml` ( `xmlpath` , `hotel_id` , `url` , `phone` , `streetaddress` , `additional`, `name`, `shortdescription`, `city`, `sabre_id`, `country`, `province`, `streetaddress2`, `postalcode`, `chaincode` ) VALUES ( '".$this->clean($data['xmlpath'])."', '".$this->clean($data['hotel_id'])."', '".$this->clean($data['url'])."', '".$this->clean($data['phone'])."', '".$this->clean($data['streetaddress'])."', '".$this->clean($data['additional'])."', '".$this->clean($data['name'])."', '".$this->clean($data['shortdescription'])."', '".$this->clean($data['city'])."', '".$this->clean($data['sabre_id'])."', '".$this->clean($data['country'])."', '".$this->clean($data['province'])."', '".$this->clean($data['streetaddress2'])."', '".$this->clean($data['postalcode'])."', '".$this->clean($data['chaincode'])."')";
		echo $sql;
		echo "<br>";
		mysql_query($sql) or die(mysql_error());
	}
	
	public function parseLinks($link) {
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if(preg_match_all("/$regexp/siU", $link, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				return $match[2];
			}
		}
		return false;
	}
	public function parseLinks2($link) {
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if(preg_match_all("/$regexp/siU", $link, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				$ret['url'] = $match[2];
				$ret['text'] = $match[3];
				return $ret;
			}
		}
		return false;
	}
	public function getHeading($content) {
		$regexp = "<h1 id=\"HEADING\">(.*)<\/h1>";
		if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				$required['name'] = trim($match[1]);
				break;
			}
		}
		return $required['name'];
	}
	public function getBasePageParseResult($input, $saveFirstPageFile, $rec) {
		
		$result = array();		
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
				$result = $this->validateplace($content2, $rec);
				if($result['found']) {
					$result['finalUrl'] = $return;	
					$result['content2'] = $content2;				
					$fp = file_put_contents($saveFirstPageFile, $content2) or die('error'.__LINE__);
					$sql = "update property_xml set firsturl = '".$this->clean($return)."', firsturlflag = 1 where id = '".$rec['id']."'";
					mysql_query($sql) or die('error'.__LINE__." ".mysql_error());					
					break;
				}
			}
		} 		
		return $result;
	}
	public function getSavedPageParseResult($saveFirstPageFile, $rec) {
	
		$result = array();		
		$content2 = @file_get_contents($saveFirstPageFile);				
		$result = $this->validateplace($content2, $rec);
		if($result['found']) {
			$result['finalUrl'] = $rec['firsturl'];	
			$result['content2'] = $content2;
		}
		return $result;
	}
	public function validateplace($content2, $rec) {
		$result['found'] = 0;
		$result['ftype'] = '';
		$stAddress = $rec['streetaddress'];
		$city = $rec['city'];
		$stAddress2 = str_ireplace("Street", "St", $stAddress);
		$stAddress3 = str_ireplace('Boulevard', 'Blvd', $stAddress);
		$stAddress4 = str_ireplace('Road', 'Rd', $stAddress);
		$stAddress5 = str_ireplace('Road No', 'Rd', $stAddress);
		$stAddress6 = str_ireplace('Saint', 'St', $stAddress);
		$stAddress7 = str_ireplace('Saint', 'St.', $stAddress);
		
		$postalcode = $rec['postalcode'];
		//$country = $this->getCountry($rec['country']);
		$phone = substr($rec['phone'], -6);
		$name = str_ireplace("Hotel","",$rec['name']);
		$name = trim($name);
		$regexp = "<address>(.*)<\/address>";
		$cont = $this->getAddress($regexp, $content2);
		if(@eregi($stAddress, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress';
		} else if(@eregi($stAddress2, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress2';
		} else if(@eregi($postalcode, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'postalcode';
		} else if(@eregi($city, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'city';
		} else if(@eregi($stAddress3, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress3';
		} else if(@eregi($stAddress4, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress4';
		} else if(@eregi($stAddress5, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress5';
		} else if(@eregi($stAddress6, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress6';
		} else if(@eregi($stAddress7, $cont) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress7';
		} else {					
			$heading = $this->getHeading($content2);
			if(@eregi($heading, $name)) {
				if(@eregi($stAddress, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress_2';
				} else if(@eregi($stAddress2, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress2_2';
				} else if(@eregi($postalcode, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'postalcode_2';
				} else if(@eregi($city, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'city_2';
				} else if(@eregi($stAddress3, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress3_2';
				} else if(@eregi($stAddress4, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress4_2';
				} else if(@eregi($stAddress5, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress5_2';
				} else if(@eregi($stAddress6, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress6_2';
				} else if(@eregi($stAddress7, $cont) && @eregi($heading, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress7_2';
				}
			}
		}
		return $result;
	}
	public function getCountry($country) {
		$sql = "select * from country where iso2 = '".strtoupper($country)."'";
		$rs = mysql_query($sql) or die('error '.mysql_error());
		$rec = mysql_fetch_array($rs);
		return $rec['name'];
	}
	public function getReviewCount($content) {
		$regexp = "<li class=\"moreRevws\"><a href=\"#REVIEWS\">Read(.*)review.*<\/a><\/li>";
		if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {					
			foreach($matches as $match) {
				$review = trim($match[1]);
				break;
			}					
		} else {
			$regexp = "<a href=\".*#REVIEWS\">Read(.*)review.*<\/a>";
			if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {					
				foreach($matches as $match) {	
					$review = trim($match[1]);
					break;
				}					
			} else {
				$regexp = "<span class=\"pgCount\"><span>(.*)<\/span> <i>of<\/i>(.*)<\/span>";
				if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {					
					foreach($matches as $match) {
						$review = trim($match[2]);
						break;
					}					
				} else {
					$review = NULL;
				}
			}
		}
		return $review;	
	}
	public function regexp($regexp, $input) {
		//$regexp = "tripadvisor.com(.*)Reviews(.*)$";
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				$result[] = $match;
			}
		}
		return $result;
	}
		
	public function getpages($total) {
		$max = 10;
		$totalPages = ceil($total/$max)-1;
		for($i=0;$i<=$totalPages;$i++) {
			$s[] = $i * $max;
		}
		return $s;
	}
	
	public function getmodpages($total,$max=10) {
	
		$totalPages = ceil($total/$max)-1;
		for($i=0;$i<=$totalPages;$i++) {
			$s[] = $i * $max;
		}
		return $s;
	}
	public function getRating($file) {
		$regExp = "<b class=\"label\">TripAdvisor Traveler Rating:.*<ul>.*<\/ul>.*<\/b>.*<span class=\"rate .*\"><img.*alt=\"(.*) of 5 stars\".*\/><\/span>.*<span class=\"more\"><a.*>.*(.*) Review.*<\/a><\/span>";
	
		if(preg_match_all("/$regExp/siU",$file, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				
				$m['avg_rate'] = $match[1];
				$m['total_reviews'] = $match[2];
			}
		} else {
			$regExp = "<div class=\"rating\">.*<b class=\"label\">TripAdvisor Traveler Rating:.*<\/b>.*<span class=\"rate.*\"><img.*alt=\"(.*) of 5 stars\".*\/><\/span>.*<\/div>.*Read(.*)review";
			if(preg_match_all("/$regExp/siU", $file, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {					
					if(is_numeric(trim($match[2]))){
						$m['avg_rate'] = $match[1];
						$m['total_reviews'] = $match[2];
					} else {
						$regExp = "<div class=\"rating\">.*<b class=\"label\">TripAdvisor Traveler Rating:.*<\/b>.*<span class=\"rate.*\"><img.*alt=\"(.*) of 5 stars\".*\/><\/span>.*<div class=\"sep showing top\">.*<\/div>.*<h2>Reviews of.*<\/h2>.*<span class=\"count\">.*\(.*of (.*)\).*<\/span>.*<\/div>";
						if(preg_match_all("/$regExp/siU", $file, $matches, PREG_SET_ORDER)) 	    {
							foreach($matches as $match) {
								$m['avg_rate'] = $match[1];
								$m['total_reviews'] = $match[2];
							}
						}
					}
				}
			} else {
				$regExp = "<div class=\"rating\">.*<b class=\"label\">TripAdvisor Traveler Rating:.*<\/b>.*<span class=\"rate.*\"><img.*alt=\"(.*) of 5 stars\".*\/><\/span>.*<\/div>.*Read (.*) review";
				if(preg_match_all("/$regExp/siU", $file, $matches, PREG_SET_ORDER)) 	    {
					foreach($matches as $match) {
						
						$m['avg_rate'] = $match[1];
						$m['total_reviews'] = $match[2];
					}
				} 
			}
		}
		return $m;
	}
	public function getAvgRating($content) {
		$regExp = "<div class=\".*rating\">.*<b.*>TripAdvisor Traveler Rating.*<\/b>.*<span class=\"rate.*\"><img.*alt=\"(.*) of 5 stars\".*\/><\/span>.*<\/div>";
		if(preg_match_all("/$regExp/siU", $content, $matches, PREG_SET_ORDER)) 	    {
			foreach($matches as $match) {
				$rating = trim($match[1]);
				break;
			}
		}
		return $rating; 
	}
	public function convertLatin1ToHtml2($msg) {
		$chars = str_split($msg, 1);
		$length = count($chars);
		for ($i=0; $i<$length; $i++) {
			if(ord($chars[$i])> 127) {
				$str.= ''; //$str.= '&#'. ord($chars[$i]).';';
			} else{
				$str.= $chars[$i];
			}
		}
		return $str;	
	
	}
	
	public function getOtherPageUrl($content) {
		$regexp = "<span class=\"next\"><a href=\"(.*)#REVIEWS\">Next.*<\/a><\/span>";
		if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {					
			foreach($matches as $match) {	
				if(eregi("http://",$match[1])) {
					$pgUrl = $match[1];
				} else {
					$pgUrl = "http://www.tripadvisor.com".$match[1];
				}	
				return $pgUrl;
				break;
			}					
		} else {
			$regexp = "<span class=\"next\"><a href=\"(.*)\">Next.*<\/a><\/span>";
			if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {					
				foreach($matches as $match) {
					if(eregi("http://",$match[1])) {
						$pgUrl = $match[1];
					} else {
						$pgUrl = "http://www.tripadvisor.com".$match[1];
					}	
					return $pgUrl;	
					break;
				}					
			} else {		
				$regexp = "<div class=\"pagination\">.*<a href=\"(.*)\">2<\/a>.*<\/div>";
				if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {					
					foreach($matches as $match) {
						if(eregi("http://",$match[1])) {
							$pgUrl = $match[1];
						} else {
							$pgUrl = "http://www.tripadvisor.com".$match[1];
						}	
						return $pgUrl;
						break;
					}					
				} 
			}
		}
		return false;
	}
	public function createXML($data) {
$review ='
<review>
	<rating>'.$this->cleanupRatings($data['rating']).'</rating>
	<author><![CDATA['.$this->cleanupAuthor($data['reviewer']).']]></author>
	<date>'.$this->convertLatin1ToHtml2(date('Y-m-d',strtotime($data['reviewdate']))).'</date>
	<title><![CDATA['.$this->convertLatin1ToHtml2($data['review_title']).']]></title>
	<excerpt><![CDATA['.$this->convertLatin1ToHtml2($data['review_detail']).']]></excerpt>
	<link><![CDATA['.$data['source'].']]></link>
</review>'; 
		return $review;
	}
	
	public function cleanupRatings($rating){
		if(!trim($rating)){
			return '0.0';
		}else{
			switch(trim($rating)){
				case '1a' : return('0.5');
							break;
				case '2a' : return('1.5');
							break;
				case '3a' : return('2.5');
							break;
				case '4a' : return('3.5');
							break;
				case '5a' : return('4.5');
							break;
				case 1 : return('1.0');
							break;
				case 2 : return('2.0');
							break;
				case 3 : return('3.0');
							break;
				case 4 : return('4.0');
							break;
				case 5 : return('5.0');
							break;
				default: return trim($rating);
			}
		}
	}
	public function cleanupAuthor($author_string){
		$substring = 'By '; 
		if (strpos($author_string, $substring) === 0) {
			$string = str_replace("By ",'',trim($author_string));
				return($string);
		}else{
				return(trim($author_string));
		}
	}
	
	public function insertIntoNewDbStatement($data) {
		$sql = "('".addslashes(stripslashes(trim($data['poi_id'])))."', '".addslashes(stripslashes(trim($data['poi_name'])))."', '".addslashes(stripslashes(trim($data['reviewer'])))."', '".addslashes(stripslashes(trim($data['reviewdate'])))."', '".addslashes(stripslashes(trim($data['review_title'])))."', '".addslashes(stripslashes(trim($data['rating'])))."', '".addslashes(stripslashes(trim($data['review_detail'])))."', '".addslashes(stripslashes(trim($data['source'])))."', '".addslashes(stripslashes(trim($data['filename'])))."', '".addslashes(stripslashes(trim($data['targetSite'])))."', '".addslashes(stripslashes(trim($data['avgrating'])))."', '".addslashes(stripslashes(trim($data['xml_id'])))."','".addslashes(stripslashes(trim($data['country'])))."')";
		return $sql;
	}
	public function getAddress($regexp, $input) {
		$return = $this->regexp($regexp, $input);
		return $return[0][1];
	}
}
?>