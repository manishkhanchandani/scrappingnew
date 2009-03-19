<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
?>
<?php 

require_once("RecursiveSearch.php");
$directory = "files/propertydeliverables/".$_GET['p']."/".$_GET['continent'];
$search = new RecursiveSearch($directory);
echo count($search->files);
?>