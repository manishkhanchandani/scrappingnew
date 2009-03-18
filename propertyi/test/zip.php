<?
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('contents.zip'); // name of zip file
// make zip archive

//add Files
$dirname = "contents";
if ($handle = opendir($dirname)) {
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "file") {
			// anything
			$files[] = $dirname."/".$file;
		}
	}
	closedir($handle);
}

if ($obj->create($files)) {
    echo 'Created successfully!';
} else {
    echo 'Error in file creation';
}
?> 