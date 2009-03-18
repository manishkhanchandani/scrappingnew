<?php
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('contents.zip'); // name of zip file

$files = $obj->listContent();       // array of file information

foreach($files as $f) {
    foreach ($f as $k => $v) {
        echo "$k: $v<br>";
    }
    echo "<br>";
}
?>