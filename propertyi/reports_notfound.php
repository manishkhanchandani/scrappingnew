<?php require_once('../Connections/conn.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsView = 10;
$pageNum_rsView = 0;
if (isset($_GET['pageNum_rsView'])) {
  $pageNum_rsView = $_GET['pageNum_rsView'];
}
$startRow_rsView = $pageNum_rsView * $maxRows_rsView;

$colname_rsView = "-1";
if (isset($_GET['country'])) {
  $colname_rsView = (get_magic_quotes_gpc()) ? $_GET['country'] : addslashes($_GET['country']);
}
mysql_select_db($database_conn, $conn);
$query_rsView = sprintf("SELECT * FROM property_xml WHERE country = '%s' AND gotit = 0 and flag = 1", $colname_rsView);
$query_limit_rsView = sprintf("%s LIMIT %d, %d", $query_rsView, $startRow_rsView, $maxRows_rsView);
$rsView = mysql_query($query_limit_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);

if (isset($_GET['totalRows_rsView'])) {
  $totalRows_rsView = $_GET['totalRows_rsView'];
} else {
  $all_rsView = mysql_query($query_rsView);
  $totalRows_rsView = mysql_num_rows($all_rsView);
}
$totalPages_rsView = ceil($totalRows_rsView/$maxRows_rsView)-1;

$queryString_rsView = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsView") == false && 
        stristr($param, "totalRows_rsView") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsView = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsView = sprintf("&totalRows_rsView=%d%s", $totalRows_rsView, $queryString_rsView);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Not found</title>
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
<h1>Report :: No Review Found </h1>
<?php if ($totalRows_rsView > 0) { // Show if recordset not empty ?>
  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>id</th>
      <th>baseurl</th>
      <th>xmlpath</th>
      <th>hotel_id</th>
      <th>name</th>
      <th>url</th>
      <th>phone</th>
      <th>streetaddress</th>
      <th>city</th>
      <th>province</th>
      <th>streetaddress2</th>
      <th>postalcode</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_rsView['id']; ?></td>
        <td><a href="<?php echo $row_rsView['baseurl']; ?>" target="_blank"><?php echo $row_rsView['baseurl']; ?></a></td>
        <td><?php echo $row_rsView['xmlpath']; ?></td>
        <td><?php echo $row_rsView['hotel_id']; ?></td>
        <td><?php echo $row_rsView['name']; ?></td>
        <td><?php echo $row_rsView['url']; ?></td>
        <td><?php echo $row_rsView['phone']; ?></td>
        <td><?php echo $row_rsView['streetaddress']; ?></td>
        <td><?php echo $row_rsView['city']; ?></td>
        <td><?php echo $row_rsView['province']; ?></td>
        <td><?php echo $row_rsView['streetaddress2']; ?></td>
        <td><?php echo $row_rsView['postalcode']; ?></td>
      </tr>
      <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
      </table>
  <p> Records <?php echo ($startRow_rsView + 1) ?> to <?php echo min($startRow_rsView + $maxRows_rsView, $totalRows_rsView) ?> of <?php echo $totalRows_rsView ?></p>
  <table border="0" width="50%" align="center">
    <tr>
      <td width="23%" align="center"><?php if ($pageNum_rsView > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsView=%d%s", $currentPage, 0, $queryString_rsView); ?>">First</a>
      <?php } // Show if not first page ?>      </td>
      <td width="31%" align="center"><?php if ($pageNum_rsView > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsView=%d%s", $currentPage, max(0, $pageNum_rsView - 1), $queryString_rsView); ?>">Previous</a>
      <?php } // Show if not first page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_rsView < $totalPages_rsView) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsView=%d%s", $currentPage, min($totalPages_rsView, $pageNum_rsView + 1), $queryString_rsView); ?>">Next</a>
      <?php } // Show if not last page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_rsView < $totalPages_rsView) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsView=%d%s", $currentPage, $totalPages_rsView, $queryString_rsView); ?>">Last</a>
      <?php } // Show if not last page ?>      </td>
    </tr>
      </table>
  <?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
<?php if ($totalRows_rsView == 0) { // Show if recordset empty ?>
  <p>No Record Found. </p>
  <?php } // Show if recordset empty ?></body>
</html>
<?php
mysql_free_result($rsView);
?>
