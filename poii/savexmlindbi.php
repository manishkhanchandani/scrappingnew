<?php
include('../Connections/conn.php');
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
include('POI.php');
$poi = new POI;
require_once("RecursiveSearch.php");
$directory = "files/poi";
$search = new RecursiveSearch($directory);	

if($search->files) {
	$data = array();
	foreach($search->files as $v) {
		$data = array();
		echo $v;
		echo "<br>";
		echo $xmlfile = str_replace("files/poi/", "", $v);
		echo "<br>";
		$tmp = explode("/",$xmlfile);
		$folder = $tmp[0];
		echo $folder;
		echo "<br>";
		$entries = simplexml_load_file($v);
		if($entries->visiting_the_city) {
			$input = $entries->visiting_the_city->details;
		}
		if($entries->dining) {
			$input = $entries->dining->details;
		}
		if($entries->shopping) {
			$input = $entries->shopping->details;
		}
		foreach($input as $entry) {
			$data['name'] = sprintf("%s", $entry->name);
			$data['poi_id'] = sprintf("%s", $entry->id);
			$data['eds_choice'] = sprintf("%s", $entry->eds_choice);
			$data['address'] = sprintf("%s", $entry->address->address1);			
			$data['address2'] = sprintf("%s", $entry->address->address2);
			$data['city'] = sprintf("%s", $entry->address->city);
			$data['province'] = sprintf("%s", $entry->address->state);
			$data['phone'] = sprintf("%s", $entry->phone);
			$data['fax'] = sprintf("%s", $entry->fax);
			$data['url'] = sprintf("%s", $entry->url);
			$data['zip'] = sprintf("%s", $entry->address->zip);
			$data['email'] = sprintf("%s", $entry->email);
			break;
		}	
		$data['country'] = $folder;
		$data['xmlpath'] = $xmlfile;
		$poi->insertxmlindb($data);
		echo "<br>";
	}
} else {
	echo 'no files';
}
				


?>