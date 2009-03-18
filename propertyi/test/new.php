<?php
include ('Archive/Zip.php');        // imports
$fileObj = "contents.zip";
if (file_exists($fileObj)) {
    $obj = new Archive_Zip($fileObj); // name of zip file
} else {
    die('File does not exist');
}

$files = array('contents/z.html');   // additional files to store

if ($obj->add($files)) {
    echo 'Added successfully!';
} else {
    echo 'Error in file addition';
}
?>