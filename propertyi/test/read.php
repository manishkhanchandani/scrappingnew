<?php
require_once "File/Archive.php";
$source = File_Archive::read("contents2.zip/");
echo "<pre>";
print_r($source);
?>