<?php
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
ini_set("include_path", dirname(__FILE__)."/pear");
echo ini_get("include_path");
echo "<br>";
include ('Archive/Zip.php'); 
include('../Connections/conn.php');

$continent = $_GET['continent'];
if(!$continent) {
	echo 'enter continent';
	exit;
}
$s = $_GET['s']; 
$m = $_GET['m'];

$dir = "files/db";
if(!is_dir($dir)) {
	mkdir($dir, 0777);
	chmod($dir, 0777);
}
$path = $dir."/".$continent;
if(!is_dir($path)) {
	mkdir($path, 0777);
	chmod($path, 0777);
}
$obj = new Archive_Zip($dir.'/'.$continent.'.zip'); // name of zip file

$colcontinent_rsCountryCode = "%";
if (isset($_GET['continent'])) {
  $colcontinent_rsCountryCode = (get_magic_quotes_gpc()) ? $_GET['continent'] : addslashes($_GET['continent']);
}
mysql_select_db($database_conn, $conn);
$query_rsCountryCode = sprintf("SELECT * FROM country WHERE continent_code like '%s' ORDER BY continent_code ASC", $colcontinent_rsCountryCode);
$rsCountryCode = mysql_query($query_rsCountryCode, $conn) or die(mysql_error());
$row_rsCountryCode = mysql_fetch_assoc($rsCountryCode);
$totalRows_rsCountryCode = mysql_num_rows($rsCountryCode);

if($totalRows_rsCountryCode >0) {
	do {
		$cntr[] = $row_rsCountryCode['iso2'];
	} while($row_rsCountryCode = mysql_fetch_assoc($rsCountryCode));
	echo $country = "'".implode("', '", $cntr)."'";
} else {
	echo 'no country found.';
	exit;
}

$rsCnt = mysql_query('select count(*) as cnt from property_xml where country IN ('.$country.') and hotel_id != 0') or die('error');
$recCnt = mysql_fetch_array($rsCnt);
echo $cnt = $recCnt['cnt'];
echo "<br>";
$max = $cnt;
if($m) $max = $m;
if(!$s) $startCounter = 0; else $startCounter = $s;
$totalPages = ceil($cnt/$max)-1;
echo "Start: ".$startCounter." , max: $max , cnt: $cnt Total pages: $totalPages<hr>";
for($counter=$startCounter;$counter<=$totalPages;$counter++) {
	echo $counter; 	echo "<br>"; echo $start = $max*$counter; echo "<hr>"; 
	
	echo $sql='select * from `property_xml` where country IN ('.$country.') and hotel_id != 0 LIMIT '.$start.', '.$max;
	echo "<br>";
	flush();
	$rs = mysql_query($sql) or die('error');
	
	$data = '';
	while($result = mysql_fetch_array($rs)) {
		$query = "update property_xml set baseurl = '".addslashes(stripslashes(trim($result['baseurl'])))."', baseflag = '".addslashes(stripslashes(trim($result['baseflag'])))."', firsturl = '".addslashes(stripslashes(trim($result['firsturl'])))."', firsturlflag = '".addslashes(stripslashes(trim($result['firsturlflag'])))."', otherpageurls = '".addslashes(stripslashes(trim($result['otherpageurls'])))."', otherpageurlflag = '".addslashes(stripslashes(trim($result['otherpageurlflag'])))."', flag = '".addslashes(stripslashes(trim($result['flag'])))."', gotit = '".addslashes(stripslashes(trim($result['gotit'])))."', ftype = '".addslashes(stripslashes(trim($result['ftype'])))."', heading = '".addslashes(stripslashes(trim($result['heading'])))."', avgrating = '".addslashes(stripslashes(trim($result['avgrating'])))."', totalreview = '".addslashes(stripslashes(trim($result['totalreview'])))."', toconsider = '".addslashes(stripslashes(trim($result['toconsider'])))."', improvement1 = '".addslashes(stripslashes(trim($result['improvement1'])))."', improvement2 = '".addslashes(stripslashes(trim($result['improvement2'])))."', improvement3 = '".addslashes(stripslashes(trim($result['improvement3'])))."' WHERE id = '".$result['id']."'";
		$data .= $query.";\n\n";
	}		
	$fp = fopen($path."/".$continent."_".$counter.".sql","w");
	fwrite($fp, $data);
	fclose($fp);
	$files[] = $path."/".$continent."_".$counter.".sql";
	echo "<hr>";
	flush();
}
if ($obj->create($files)) {
    echo 'Created successfully!';
} else {
    echo 'Error in file creation';
}	
?>