<?php
class POI {
	public function clean($text) {
		return addslashes(stripslashes(trim($text)));
	}
	
	// insert in db
	public function insertxmlindb($data) {
		$sql = "INSERT INTO `poii_xml` (`xmlpath` , `poi_id` , `country` , `name` , `eds_choice` , `address` , `address2` , `city` , `province` , `phone` , `fax` , `url` , `zip` , `email`) VALUES ( '".$this->clean($data['xmlpath'])."', '".$this->clean($data['poi_id'])."', '".$this->clean($data['country'])."', '".$this->clean($data['name'])."', '".$this->clean($data['eds_choice'])."', '".$this->clean($data['address'])."', '".$this->clean($data['address2'])."', '".$this->clean($data['city'])."', '".$this->clean($data['province'])."', '".$this->clean($data['phone'])."', '".$this->clean($data['fax'])."', '".$this->clean($data['url'])."', '".$this->clean($data['zip'])."', '".$this->clean($data['email'])."')";
		echo $sql;
		echo "<br>";
		mysql_query($sql) or die(mysql_error());
	}
	
	public function poi_notfound($id) {
		echo $sql = "update poii_xml set imp1=1, flag = 1, gotit = 0 where id = '".$id."'";
		echo "<br>";
		mysql_query($sql) or die(mysql_error()." on line ".__LINE__);
		echo '<meta http-equiv="refresh" content="5" />';
	}
	public function validateplace($content2, $rec) {
		$result['found'] = 0;
		$result['ftype'] = '';
		$name = $rec['name'];
		$stAddress = $rec['address'];
		$stAddress2 = $rec['address2'];
		$city = $rec['city'];
		$province = $rec['province'];
		$phone = substr($rec['phone'], -8);
		$fax = substr($rec['fax'], -8);
		$link = $rec['url'];
		if($link) {
			$link2 = parse_url($link);
			$host = $link2['host'];
		} else {
			$host = '';
		}
		$postalcode = $rec['zip'];
		$email = $rec['email'];
		
		if(@eregi($stAddress, $content2) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress';
		} else if(@eregi($stAddress2, $content2) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress2';
		} else if(@eregi($host, $content2) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'host';
		} else if(@eregi($email, $content2) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'email';
		} else if(@eregi($phone, $content2) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'phone';
		} else if(@eregi($fax, $content2) && @eregi($name, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'fax';
		} else if(@eregi($city, $content2) && @eregi($name, $content2) && @eregi($province, $content2) && @eregi($postalcode, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'city, postal code and province';
		} else {					
			$heading = $this->getHeading($content2);
			$heading = trim($heading);
			if(@eregi($heading, $name)) {
				if(@eregi($stAddress, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress_2';
				} else if(@eregi($stAddress2, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'staddress2_2';
				} else if(@eregi($host, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'host_2';
				} else if(@eregi($email, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'email_2';
				} else if(@eregi($phone, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'phone_2';
				} else if(@eregi($fax, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'fax_2';
				} else if(@eregi($city, $content2) && @eregi($province, $content2) && @eregi($postalcode, $content2)) {
					$result['found'] = 1;
					$result['ftype'] = 'city, postal code and province_2';
				} 
			}
		}
		return $result;
	}
	
	public function validateplaceImp($content2, $rec) {
		$result['found'] = 0;
		$result['ftype'] = '';
		$name = $rec['name'];
		$stAddress = $rec['address'];
		$stAddress2 = $rec['address2'];
		$city = $rec['city'];
		$province = $rec['province'];
		$phone = substr($rec['phone'], -8);
		$fax = substr($rec['fax'], -8);
		$link = $rec['url'];
		if($link) {
			$link2 = parse_url($link);
			$host = $link2['host'];
		} else {
			$host = '';
		}
		$postalcode = $rec['zip'];
		$email = $rec['email'];
		
		if(@eregi($stAddress, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress';
		} else if(@eregi($stAddress2, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'staddress2';
		} else if(@eregi($host, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'host';
		} else if(@eregi($email, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'email';
		} else if(@eregi($phone, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'phone';
		} else if(@eregi($fax, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'fax';
		} else if(@eregi($city, $content2) && @eregi($province, $content2) && @eregi($postalcode, $content2)) {
			$result['found'] = 1;
			$result['ftype'] = 'city, postal code and province';
		} 
		return $result;
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
	
	public function getAddress($regexp, $input) {
		$return = $this->regexp($regexp, $input);
		return $return[0][1];
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
}
?>