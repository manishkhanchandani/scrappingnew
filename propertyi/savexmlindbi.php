<?php
include('../Connections/conn.php');
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');

include('Property.php');
$Property = new Property;

require_once("RecursiveSearch.php");
$directory = "files/originialxml";
$search = new RecursiveSearch($directory);	

if($search->files) {
	$data = array();
	foreach($search->files as $v) {
		$data = array();
		echo $v;
		echo "<br>";
		echo $xmlfile = str_replace("files/originialxml/", "", $v);
		echo "<br>";
		$tmp = explode("/",$xmlfile);
		$folder = $tmp[0];
		echo $folder;
		echo "<br>";
		$entries = simplexml_load_file($v);
		foreach($entries->children('http://www.wctravel.com')->generalInfo as $entry) {
			$data['name'] = sprintf("%s", $entry->name);
			$data['hotel_id'] = sprintf("%s", $entry->hotelId);
			$data['city'] = sprintf("%s", $entry->city);
			$data['shortdescription'] = sprintf("%s", $entry->shortDescription);
			$data['phone'] = sprintf("%s", $entry->directPhone);
			$data['sabre_id'] = sprintf("%s", $entry->sabreId);
			$data['streetaddress'] = sprintf("%s", $entry->streetAddress->line);
			$data['province'] = sprintf("%s", $entry->stateCode);
			$data['streetaddress2'] = sprintf("%s", $entry->streetAddress2);
			$data['postalcode'] = sprintf("%s", $entry->stateCode);
			$data['chaincode'] = sprintf("%s", $entry->chainCode);
			break;
		}	
		$data['country'] = $folder;
		$data['xmlpath'] = $xmlfile;
		$Property->insertxmlindb($data);
		echo "<br>";
	}
} else {
	echo 'no files';
}
				


?>