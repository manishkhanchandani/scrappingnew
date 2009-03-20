<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
if($_GET['c']=="af") {
	$cntr = explode(",", "AO,BF,BI,BJ,BW,CD,CF,CG,CI,CM,CV,DJ,DZ,EG,EH,ER,ET,GA,GH,GM,GN,GQ,GW,KE,KM,LR,LS,LY,MA,MG,ML,MR,MU,MW,MZ,NA,NE,NG,RE,RW,SC,SD,SH,SL,SN,SO,ST,SZ,TD,TG,TN,TZ,UG,YT,ZA,ZM,ZW");	
} else if($_GET['c']=="as") {
	$cntr = explode(",", "AE,AF,AM,AZ,BD,BH,BN,BT,CC,CN,CX,GE,HK,ID,IL,IN,IO,IQ,IR,JO,JP,KG,KH,KP,KR,KW,KZ,LA,LB,LK,MM,MN,MO,MV,MY,NP,OM,PH,PK,PS,QA,SA,SG,SY,TH,TJ,TM,TR,TW,UZ,VN,YE");	
} else if($_GET['c']=="eu") {
	$cntr = explode(",","AD,AL,AT,AX,BA,BE,BG,BY,CH,CS,CY,CZ,DE,DK,EE,ES,FI,FO,FR,GB,GG,GI,GR,HR,HU,IE,IM,IS,IT,JE,LI,LT,LU,LV,MC,MD,ME,MK,MT,NL,NO,PL,PT,RO,RS,RU,SE,SI,SJ,SK,SM,UA,VA");	
} else if($_GET['c']=="na") {
	$cntr = explode(",","AG,AI,AN,AW,BB,BM,BS,BZ,CA,CR,CU,DM,DO,GD,GL,GP,GT,HN,HT,JM,KN,KY,LC,MQ,MS,MX,NI,PA,PM,PR,SV,TC,TT,US,VC,VG,VI,MF");	
} else if($_GET['c']=="oc") {
	$cntr = explode(",","AS,AU,CK,FJ,FM,GU,KI,MH,MP,NC,NF,NR,NU,NZ,PF,PG,PN,PW,SB,TK,TL,TO,TV,UM,VU,WF,WS");	
} else if($_GET['c']=="sa") {
	$cntr = explode(",", "AR,BO,BR,CL,CO,EC,GF,GY,PE,PY,SR,UY,VE");	
} else {
	echo 'choose continent';
	exit;
}
echo $country = "'".implode("','", $cntr)."'";
?>
<?php
$colname_rsView = "-1";
if (isset($country)) {
  $colname_rsView = (get_magic_quotes_gpc()) ? $country : addslashes($country);
}
mysql_select_db($database_conn, $conn);
$query_rsView = sprintf("SELECT a.gotit as gotita, b.gotit as gotitb, a.* FROM property_xml as a LEFT JOIN property_xml_yahoo as b ON a.id = b.id WHERE a.country IN (%s) and a.hotel_id != 0", $colname_rsView);
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
?><?php echo $query_rsView ?>
  <?php do { ?>
<?php
if($row_rsView['gotita']==1) {
	$found1++;
	echo $found1."<br>";
	continue;
}
$originalxml = "files/originialxml/".$row_rsView['xmlpath'];
$entries = simplexml_load_file($originalxml);
foreach($entries->children('http://www.wctravel.com')->generalInfo as $entry) {
	if(sprintf("%s", $entry->reviewsURL)) {
		$found2++;
	echo $found2."<br>";
		continue;
	} 
}
if($row_rsView['gotitb']==1) {
	$found3++;
	echo $found3."<br>";
	continue;
}
	


?>
    <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); 
 ?>
<?php echo "$found1, $found2, $found3, ".($found1+$found2+$found3); ?>
<?php
mysql_free_result($rsView);
?>
