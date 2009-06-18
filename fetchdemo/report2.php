<?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsReport = "SELECT source, loc_type, rss, fullreview FROM location_details WHERE loc_type != '' AND loc_type is not null Group by source order by source";
$rsReport = mysql_query($query_rsReport, $conn) or die(mysql_error());
$row_rsReport = mysql_fetch_assoc($rsReport);
$totalRows_rsReport = mysql_num_rows($rsReport);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php echo $query_rsReport; ?>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td>source</td>
    <td>loc_type</td>
    <td>rss</td>
    <td>review url </td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsReport['source']; ?></td>
      <td><?php echo $row_rsReport['loc_type']; ?></td>
      <td><?php echo $row_rsReport['rss']; ?></td>
      <td><a href="<?php echo $row_rsReport['fullreview']; ?>" target="_blank"><?php echo $row_rsReport['fullreview']; ?></a></td>
    </tr>
    <?php } while ($row_rsReport = mysql_fetch_assoc($rsReport)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($rsReport);
?>
