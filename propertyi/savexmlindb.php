<?php
include('../Connections/conn.php');
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
exit;
if(!$_GET['c']) {
	echo 'please choose country';
	exit;
}
$c = $_GET['c'];
include('Property.php');
$Property = new Property;

echo "<pre>";
$directory = "files/originialxml/$c";
$arr = $Property->RecurseDir($directory);
$files = $Property->arrF;
if($files) {
	foreach($files as $v) {
		echo $v;
		echo "<br>";
		echo $xmlfile = str_replace("files/originialxml/", "", $v);
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
		}
		$data['country'] = $c;		
		$data['xmlpath'] = $xmlfile;
		$Property->insertxmlindb($data);
		echo "<br>";
	}
} else {
	echo 'no files';
}
?>