<?php require_once('includes/db/dbconnection.php'); ?>
<?php
//Paging info
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_koa = 1000;
$pageNum_koa = 0;
if (isset($_GET['pageNum_koa'])) {
  $pageNum_koa = $_GET['pageNum_koa'];
}
$startRow_koa = $pageNum_koa * $maxRows_koa;
//Query for records
mysql_select_db($database_koa, $koa);
$query_koa = "SELECT * FROM koa_bg ORDER BY record_id ASC"; 
$query_limit_koa = sprintf("%s LIMIT %d, %d", $query_koa, $startRow_koa, $maxRows_koa);
$koatbl = mysql_query($query_limit_koa, $koa) or die(mysql_error());
$row_koa = mysql_fetch_assoc($koatbl);
//More paging info
if (isset($_GET['totalRows_koa'])) {
  $totalRows_koa = $_GET['totalRows_koa'];
} else {
  $all_koa = mysql_query($query_koa);
  $totalRows_koa = mysql_num_rows($all_koa);
}
$totalPages_koa = ceil($totalRows_koa/$maxRows_koa)-1;
$queryString_koa = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_koa") == false && 
        stristr($param, "totalRows_koa") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_koa = "&" . htmlentities(implode("&", $newParams));
  }
$queryString_koa = sprintf("&totalRows_koa=%d%s", $totalRows_koa, $queryString_koa);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--[if IE]>
<script type="text/javascript"  src="includes/fix_eolas.js"  defer="defer"></script>
<![endif]-->
<title>List - Billing Groups</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/style.css" rel="stylesheet" type="text/css">
</head>
<?PHP include ('header.php'); ?>
<table align="center" bgcolor="#CBDCEE" width="900" border="1" cellpadding="0" cellspacing="0" bordercolor="#E9F0F8">
              <tr align="center" bgcolor="#E9F0F8">
		      <td colspan="6"><br>
			  <strong>Record List </strong>(<?php echo $totalRows_koa; ?> total)
		     <br><br></td>
			  </tr>
			  <tr bgcolor="#8FB4DA">
                <td>Project Number </td>
                <td>Billing Group Name </td>
                <td>Project Manager</td>
                <td>Client Name</td>
				<td>Contact</td>
				<td>Requester</td>
              </tr>
<?php do { ?>
              <tr>
                <td> <?php echo $row_koa['bg_projnumber']; ?>&nbsp; </td>
                <td> <?php echo $row_koa['bg_name']; ?>&nbsp; </td>
                <td> <?php echo $row_koa['bg_manager']; ?>&nbsp; </td>
                <td> <?php echo $row_koa['bg_cliname']; ?>&nbsp; </td>
				<td> <?php echo $row_koa['bg_contact']; ?>&nbsp; </td>
				<td> <?php echo $row_koa['bg_req']; ?>&nbsp; </td>
              </tr>
<?php } while ($row_koa = mysql_fetch_assoc($koatbl)); ?>
            </table>
            <br>
            <table border="0" width="50%" align="center">
              <tr>
                <td width="23%" align="center">
                  <?php if ($pageNum_koa > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_koa=%d%s", $currentPage, 0, $queryString_koa); ?>">First</a>
                  <?php } // Show if not first page ?>
                </td>
                <td width="31%" align="center">
                  <?php if ($pageNum_koa > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_koa=%d%s", $currentPage, max(0, $pageNum_koa - 1), $queryString_koa); ?>">Previous</a>
                  <?php } // Show if not first page ?>
                </td>
                <td width="23%" align="center">
                  <?php if ($pageNum_koa < $totalPages_koa) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_koa=%d%s", $currentPage, min($totalPages_koa, $pageNum_koa + 1), $queryString_koa); ?>">Next</a>
                  <?php } // Show if not last page ?>
                </td>
                <td width="23%" align="center">
                  <?php if ($pageNum_koa < $totalPages_koa) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_koa=%d%s", $currentPage, $totalPages_koa, $queryString_koa); ?>">Last</a>
                  <?php } // Show if not last page ?>
                </td>
              </tr>
            </table>
<?php
mysql_free_result($koatbl);
?>
<?PHP include ('footer.php'); ?>