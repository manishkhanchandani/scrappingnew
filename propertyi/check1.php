<?php //include('../Connections/conn.php'); ?>
<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn = "64.186.128.115";
$database_conn = "scrapping1";
$username_conn = "manishkk";
$password_conn = "manishkk";
$conn = mysql_connect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_conn, $conn);
?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
if(!$_GET['continent']) {
	echo 'choose continent';
	exit;
}
?>
<?php
$colcontinent_rsCountryCode = "-1";
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
	echo "<br>";
}

$colcountry_rsCountry = "-1";
if (isset($country)) {
  $colcountry_rsCountry = (get_magic_quotes_gpc()) ? $country : addslashes($country);
}
mysql_select_db($database_conn, $conn);
$query_rsCountry = sprintf("SELECT * FROM property_xml WHERE country IN (%s)", $colcountry_rsCountry);
$rsCountry = mysql_query($query_rsCountry, $conn) or die(mysql_error());
$row_rsCountry = mysql_fetch_assoc($rsCountry);
$totalRows_rsCountry = mysql_num_rows($rsCountry);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<p>Check File</p>
<?php if ($totalRows_rsCountry > 0) { // Show if recordset not empty ?>
<?php echo $totalRows_rsCountry ?>
<br />
<br />
<?php do { ?>
<?php 
echo $row_rsCountry['gotit']." = ".$row_rsCountry['totalreview']; print_r($row_rsCountry['xmlpath']); ?><br />
<?php 
echo "files/propertydeliverables/new/".$_GET['continent']."/".$row_rsCountry['xmlpath']; 

if(file_exists("files/propertydeliverables/new/".$_GET['continent']."/".$row_rsCountry['xmlpath'])) { ?><br />
File found.<br />
		<?php
		$file9++;
		if($row_rsCountry['hotel_id']>0) {
			$file1++;
		} else {
			$file3[] = $row_rsCountry['xmlpath']." ".$row_rsCountry['id'];
			//mysql_query("update property_xml set gotit = 0 where id = '".$row_rsCountry['id']."'") or die('error'.__LINE__);
			//unlink("files/propertydeliverables/new/".$_GET['continent']."/".$row_rsCountry['xmlpath']);
		}
		if($row_rsCountry['gotit']>0) {
			$file4++;
		} else {
			$file5[] = $row_rsCountry['xmlpath']." ".$row_rsCountry['id'];
			//echo $sql = "update property_xml set improvement4 = 1 where id = '".$row_rsCountry['id']."'";
			//echo "<br>";
			//mysql_query($sql) or die('error'.__LINE__);
			//unlink("files/propertydeliverables/new/".$_GET['continent']."/".$row_rsCountry['xmlpath']);
		}
		
		
		if($row_rsCountry['totalreview']>0) {
			$file6++;
		} else {
			$file7[] = $row_rsCountry['xmlpath']." ".$row_rsCountry['id'];
			//unlink("files/propertydeliverables/new/".$_GET['continent']."/".$row_rsCountry['xmlpath']);
		}
		?>
<?php } else { 
	echo "<h3>not found</h3>"; 
	$file2++;
	if($row_rsCountry['totalreview']>0) {
		$file8[] = $row_rsCountry['xmlpath']." ".$row_rsCountry['id'];
		//mysql_query("update property_xml set totalreview = 0 where id = '".$row_rsCountry['id']."'") or die('error'.__LINE__);
	}
} ?>
  <?php } while ($row_rsCountry = mysql_fetch_assoc($rsCountry)); ?>
<br />
<br />
<?php echo "file1: $file1, file2: $file2, file3:".count($file3); print_r($file3);
echo "<br>";
echo "file4: $file4, file5:".count($file5); echo "<pre>"; print_r($file5);echo "</pre>"; 
echo "<br>";
echo "file6:".$file6; echo "<pre>"; print_r($file7);echo "</pre>"; 
echo "<br>";
echo "file8:<pre>"; print_r($file8);echo "</pre>"; 
echo "file9: $file9";
?>
<?php } // Show if recordset not empty ?>
<?php if ($totalRows_rsCountry == 0) { // Show if recordset empty ?>
<p>no record </p>
<?php } // Show if recordset empty ?></body>
</html>
<?php
mysql_free_result($rsCountryCode);

mysql_free_result($rsCountry);
?>
