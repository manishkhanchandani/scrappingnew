<?
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('2.zip'); // name of zip file
// make zip archive

//add Files
for($i=0;$i<1000;$i++) {
	$files[] = $i.".html";
}

if ($obj->create($files)) {
    echo 'Created successfully!';
} else {
    echo 'Error in file creation';
}
?> 