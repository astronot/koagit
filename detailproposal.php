<?php require_once('includes/db/dbconnection.php'); ?>
<?php
// SQL functions
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
// Form action for submission
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
	
//Query for records
$query_koa = "SELECT * FROM koa_proposal WHERE record_id = '$recordID' ORDER BY record_id ASC";
$koatbl = mysql_query($query_koa, $koa) or die(mysql_error());
$row_koa = mysql_fetch_assoc($koatbl);
$totalRows_koa = mysql_num_rows($koatbl);

?>
<?php include('includes/vdaemon/vdaemon.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--[if IE]>
<script type="text/javascript"  src="includes/fix_eolas.js"  defer="defer"></script>
<![endif]-->
<title>Proposal Detail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/style.css" rel="stylesheet" type="text/css">
</head>
<?PHP include ('header.php'); ?>
<table align="center" bgcolor="#CBDCEE" width="640" border="1" cellpadding="0" cellspacing="0" bordercolor="#E9F0F8">
	<tr align="center" bgcolor="#E9F0F8">
		<td><br>
			<strong>Proposal Detail</strong>
		<br><br></td>
	</tr>
	<tr bgcolor="#E9F0F8">
		<td colspan="7"><input type=button value="Back" onClick="history.go(-1)" class="button" onMouseOver="this.className='button buttonhov'" onMouseOut="this.className='button'">
		  </td>
	</tr>
	<tr>
		<td><br>
			Date: <strong><?php echo $row_koa['prop_date']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal Name: <strong><?php echo $row_koa['prop_name']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal Location: <br><br><textarea class="box" name="prop_loc" value="" cols="50" rows="4"><?php echo $row_koa['prop_loc']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Organization: <strong><?php echo $row_koa['prop_org']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Project Manager: <strong><?php echo $row_koa['prop_manager']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Name: <strong><?php echo $row_koa['prop_cliname']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Address:<br><br><textarea class="box" name="prop_cliadd" value="" cols="50" rows="4"><?php echo $row_koa['prop_cliadd']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Contact: <strong><?php echo $row_koa['prop_contact']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal Description & Scope of Work:<br><br><textarea class="box" name="prop_desc" value="" cols="50" rows="4"><?php echo $row_koa['prop_desc']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Requested by: <strong><?php echo $row_koa['prop_req']; ?></strong>&nbsp;-|-&nbsp;Email Address: <strong><?php echo $row_koa['prop_reqemail']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Comments:<br><br><textarea class="box" name="prop_comments" value="" cols="50" rows="4"><?php echo $row_koa['prop_comments']; ?></textarea>
		<br><br></td>
	</tr>
	<tr bgcolor="#E9F0F8" align="center">
		<td><br><br>
			<br>
		</td>
	</tr>
	<tr bgcolor="#E9F0F8">
		<td colspan="7"><input type=button value="Back" onClick="history.go(-1)" class="button" onMouseOver="this.className='button buttonhov'" onMouseOut="this.className='button'">
		  </td>
	</tr>
</table>
<br><br><br>
<?PHP include ('footer.php'); ?>