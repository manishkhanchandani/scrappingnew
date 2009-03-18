<?php
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('contents2.zip'); // name of zip file
$files = array('contents/1.html');   // 
$p_params = array('by_preg'=>"/.*html$/");
$files = $obj->extract($p_params);       // array of file information

foreach($files as $f) {
    foreach ($f as $k => $v) {
        echo "$k: $v<br>";
    }
    echo "<br>";
}
?>