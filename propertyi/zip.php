<?php
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
ini_set("include_path", dirname(__FILE__)."/pear");
echo ini_get("include_path");
echo "<br>";
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('files/'.$_GET['c'].'.zip'); // name of zip file
// make zip archive

//add Files
$dirname = "contents";
require_once("RecursiveSearch.php");
$directory = "files/".$_GET['c'];
$search = new RecursiveSearch($directory);	
echo "<pre>";
if($search->files) {
	$files = array();
	foreach($search->files as $v) {
		$files[] = $v;
	}
}
print_r($files);
	
if ($obj->create($files)) {
    echo 'Created successfully!';
} else {
    echo 'Error in file creation';
}
?> 