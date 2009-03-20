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
		echo $sql = "update poii_xml set flag = 1, gotit = 0 where id = '".$id."'";
		echo "<br>";
		//mysql_query($sql) or die(mysql_error()." on line ".__LINE__);
	}
}
?>