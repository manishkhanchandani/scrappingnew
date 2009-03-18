<?php
include ('Archive/Zip.php');        // imports
$fileObj = "contents.zip";
if (file_exists($fileObj)) {
    $obj = new Archive_Zip($fileObj); // name of zip file
} else {
    die('File does not exist');
}

$files = array('contents/z.html');   // additional files to store

if ($obj->delete(array('by_name' => $files))) {
    echo 'Deleted successfully!';
} else {
    echo 'Error in file deletion';     
}
?>