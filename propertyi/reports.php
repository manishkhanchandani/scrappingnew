<?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsCountry = "SELECT distinct a.country, b.cnt as review, c.cnt as noreview, d.cnt as nofound, e.cnt as totalip FROM property_xml as a LEFT JOIN ip_review as b ON a.country = b.country LEFT JOIN ip_noreview as c ON a.country = c.country LEFT JOIN ip_nofound as d ON a.country = d.country LEFT JOIN ip_total as e ON a.country = e.country WHERE e.cnt > 0 ORDER BY totalip DESC, review DESC, noreview DESC, nofound DESC";
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
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td><strong>Country</strong></td>
    <td><strong>Total Property </strong></td>
    <td><strong>Review Found </strong></td>
    <td><strong>Review No Found </strong></td>
    <td><strong>Property Not Found </strong></td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsCountry['country']; ?></td>
      <td><?php echo $row_rsCountry['totalip']; ?></td>
      <td><a href="reports_reviewfound.php?country=<?php echo $row_rsCountry['country']; ?>"><?php echo $row_rsCountry['review']; ?></a> [<?php echo number_format(($row_rsCountry['review']/$row_rsCountry['totalip'])*100,2); ?> %] </td>
      <td><a href="reports_noreviewfound.php?country=<?php echo $row_rsCountry['country']; ?>"><?php echo $row_rsCountry['noreview']; ?></a> [<?php echo number_format(($row_rsCountry['noreview']/$row_rsCountry['totalip'])*100,2); ?> %]  </td>
      <td><a href="reports_notfound.php?country=<?php echo $row_rsCountry['country']; ?>"><?php echo $row_rsCountry['nofound']; ?></a> [<?php echo number_format(($row_rsCountry['nofound']/$row_rsCountry['totalip'])*100,2); ?> %] </td>
    </tr>
    <?php } while ($row_rsCountry = mysql_fetch_assoc($rsCountry)); ?>
</table>
<p>&nbsp; </p>
</body>
</html>
<?php
mysql_free_result($rsCountry);
?>
