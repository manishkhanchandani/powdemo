<?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsView = "SELECT count(source) as cnt, source, `fullreview` FROM `location_details` WHERE loc_type = '' or loc_type is NULL GROUP BY source ORDER BY source";
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php echo $query_rsView; ?>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td><strong>cnt</strong></td>
    <td><strong>source</strong></td>
    <td><strong>fullreview</strong></td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsView['cnt']; ?></td>
      <td><?php echo $row_rsView['source']; ?></td>
      <td><a href="<?php echo $row_rsView['fullreview']; ?>" target="_blank"><?php echo $row_rsView['fullreview']; ?></a></td>
    </tr>
    <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($rsView);
?>
