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
$query_koa = "SELECT * FROM koa_project WHERE record_id = '$recordID' ORDER BY record_id ASC";
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
<title>Project Detail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/style.css" rel="stylesheet" type="text/css">
</head>
<?PHP include ('header.php'); ?>
<table align="center" bgcolor="#CBDCEE" width="640" border="1" cellpadding="0" cellspacing="0" bordercolor="#E9F0F8">
	<tr align="center" bgcolor="#E9F0F8">
		<td><br>
			<strong>Project Detail</strong>
		<br><br></td>
	</tr>
	<tr bgcolor="#E9F0F8">
			  	<td colspan="7"><input type=button value="Back" onClick="history.go(-1)" class="button" onMouseOver="this.className='button buttonhov'" onMouseOut="this.className='button'">
		  		</td>
			  </tr>

	<tr>
		<td><br>
			Date: <strong><?php echo $row_koa['proj_date']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Project Number: <strong><?php echo $row_koa['proj_number']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Project Name: <strong><?php echo $row_koa['proj_name']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Project Location: <br><br><textarea class="box" name="proj_loc" value="" cols="50" rows="4"><?php echo $row_koa['proj_loc']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Organization: <strong><?php echo $row_koa['proj_org']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Project Manager: <strong><?php echo $row_koa['proj_manager']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Name: <strong><?php echo $row_koa['proj_cliname']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Address:<br><br><textarea class="box" name="proj_cliadd" value="" cols="50" rows="4"><?php echo $row_koa['proj_cliadd']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Client Contact: <strong><?php echo $row_koa['proj_contact']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Invoice sent to same:  <strong><?php echo $row_koa['proj_invoice']; ?></strong>&nbsp;&nbsp;Attn:&nbsp;<strong><?php echo $row_koa['proj_attn']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td>Terms of Contract:&nbsp;<strong><?php echo $row_koa['proj_terms']; ?></strong>
		&nbsp;-|-&nbsp;Contract Amount:&nbsp;$<strong><?php echo $row_koa['proj_contractamount']; ?></strong>
		</td>
	</tr>
	<tr>
		<td><br>
			Purchase Order:  <strong><?php echo $row_koa['proj_po']; ?></strong>&nbsp;-|-&nbsp;PO #:&nbsp;<strong><?php echo $row_koa['proj_ponumber']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal or Contract on File:  <strong><?php echo $row_koa['proj_pocof']; ?></strong>&nbsp;-|-&nbsp;
				Proposal #: <strong><?php echo $row_koa['proj_propnumber']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Other Terms:<br><br><textarea class="box" name="proj_othterms" value="" cols="50" rows="4"><?php echo $row_koa['proj_othterms']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Project Description & Scope of Work:<br><br><textarea class="box" name="proj_desc" value="" cols="50" rows="4"><?php echo $row_koa['proj_desc']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Requested by: <strong><?php echo $row_koa['proj_req']; ?></strong>&nbsp; -|-&nbsp;Email Address: <strong><?php echo $row_koa['proj_reqemail']; ?></strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Comments:<br><br><textarea class="box" name="proj_comments" value="" cols="50" rows="4"><?php echo $row_koa['proj_comments']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Additional Billing Groups:<br><br>
			Subcontractors: <strong><?php echo $row_koa['proj_billingsub']; ?></strong>
			Reimbursables: <strong><?php echo $row_koa['proj_billingreimb']; ?></strong>
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