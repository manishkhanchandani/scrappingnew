<?php //require_once('../Connections/conn.php'); ?>
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
$colcontinent_rsCountryCode = "na";
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
}

$colcountry_rsCountry = "-1";
if (isset($country)) {
  $colcountry_rsCountry = (get_magic_quotes_gpc()) ? $country : addslashes($country);
}
mysql_select_db($database_conn, $conn);
$query_rsCountry = sprintf("SELECT distinct a.country, b.cnt as review, c.cnt as noreview, d.cnt as nofound, e.cnt as totalip, f.cnt as totalreviewcount FROM poii_xml as a LEFT JOIN poii_review as b ON a.country = b.country LEFT JOIN poii_noreview as c ON a.country = c.country LEFT JOIN poii_nofound as d ON a.country = d.country LEFT JOIN poii_total as e ON a.country = e.country LEFT JOIN poii_reviewcount as f ON a.country = f.country WHERE e.cnt > 0 AND a.country IN (%s) ORDER BY totalip DESC, review DESC, noreview DESC, nofound DESC", $colcountry_rsCountry);
$rsCountry = mysql_query($query_rsCountry, $conn) or die(mysql_error());
$row_rsCountry = mysql_fetch_assoc($rsCountry);
$totalRows_rsCountry = mysql_num_rows($rsCountry);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reports</title>
<style type="text/css">
<!--
body {
	font-family: Verdana;
	font-size: 11px;
}
-->
</style>
</head>

<body>
<h1>Reports</h1>
<?php if ($totalRows_rsCountry > 0) { // Show if recordset not empty ?>
  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <td><strong>Country</strong></td>
      <td><strong>Total Property </strong></td>
      <td><strong>Review Found </strong></td>
      <td><strong>Review No Found </strong></td>
      <td><strong>Property Not Found </strong></td>
      <td><strong>Property Found </strong></td>
      <td><strong>Reviewe Count </strong></td>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_rsCountry['country']; ?></td>
        <td><?php echo $row_rsCountry['totalip']; ?>
        <?php $totalip += $row_rsCountry['totalip']; ?></td>
        <td><a href="reports_reviewfound.php?country=<?php echo $row_rsCountry['country']; ?>"><?php echo $row_rsCountry['review']; ?></a> [<?php echo number_format(($row_rsCountry['review']/$row_rsCountry['totalip'])*100,2); ?> %]
        <?php $totalreview += $row_rsCountry['review']; ?></td>
        <td><a href="reports_noreviewfound.php?country=<?php echo $row_rsCountry['country']; ?>"><?php echo $row_rsCountry['noreview']; ?></a> [<?php echo number_format(($row_rsCountry['noreview']/$row_rsCountry['totalip'])*100,2); ?> %]
        <?php $totalnoreview += $row_rsCountry['noreview']; ?> </td>
        <td><a href="reports_notfound.php?country=<?php echo $row_rsCountry['country']; ?>"><?php echo $row_rsCountry['nofound']; ?></a> [<?php echo number_format(($row_rsCountry['nofound']/$row_rsCountry['totalip'])*100,2); ?> %]
        <?php $totalnoproperty += $row_rsCountry['nofound']; ?> </td>
        <td><?php echo $row_rsCountry['review']+$row_rsCountry['noreview']; ?> [<?php echo number_format((($row_rsCountry['review']+$row_rsCountry['noreview'])/$row_rsCountry['totalip'])*100,2); ?> %]        
        <?php $totalproperty += ($row_rsCountry['review']+$row_rsCountry['noreview']); ?></td>
        <td><?php echo $row_rsCountry['totalreviewcount']; ?>
        <?php $totalreviewcount += $row_rsCountry['totalreviewcount']; ?></td>
      </tr>
      <?php } while ($row_rsCountry = mysql_fetch_assoc($rsCountry)); ?>
    <tr>
      <th>&nbsp;</th>
      <th><?php echo $totalip; ?> [100 %]&nbsp;</th>
      <th><?php echo $totalreview; ?> [<?php echo number_format(($totalreview/$totalip)*100,2); ?> %]&nbsp;</th>
      <th><?php echo $totalnoreview; ?>  [<?php echo number_format(($totalnoreview/$totalip)*100,2); ?> %]&nbsp;</th>
      <th><?php echo $totalnoproperty; ?>  [<?php echo number_format(($totalnoproperty/$totalip)*100,2); ?> %]&nbsp;</th>
      <th><?php echo $totalproperty; ?>  [<?php echo number_format(($totalproperty/$totalip)*100,2); ?> %]&nbsp;</th>
      <th><?php echo $totalreviewcount; ?></th>
    </tr>
  </table>
  <?php } // Show if recordset not empty ?>
<?php if ($totalRows_rsCountry == 0) { // Show if recordset empty ?>
  <p>No Record Found. </p>
  <?php } // Show if recordset empty ?></body>
</html>
<?php
mysql_free_result($rsCountryCode);

mysql_free_result($rsCountry);
?>
