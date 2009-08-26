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

//Update records upon submission, else skip
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$updateSQL = sprintf("UPDATE koa_project SET proj_number=%s, proj_name=%s WHERE record_id=%s",
                       GetSQLValueString($_POST['proj_number'], "text"),
					   GetSQLValueString($_POST['proj_name'], "text"),
					   GetSQLValueString($_GET['recordID'], "int"));
	mysql_select_db($database_koa, $koa);
  $Result1 = mysql_query($updateSQL, $koa) or die(mysql_error());

/* Update Archive and include original record id and timestamp.
$insertARCHSQL = sprintf("INSERT INTO koa_project_archive (record_id, proj_record_id, proj_timestamp, proj_date, proj_number, proj_name, proj_loc, proj_org, proj_manager, proj_cliname, proj_cliadd, proj_contact, proj_invoice, proj_attn, proj_terms, proj_contractamount, proj_po, proj_ponumber, proj_pocof, proj_propnumber, proj_othterms, proj_desc, proj_req, proj_reqemail, proj_comments, proj_billingsub, proj_billingreimb) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	                   GetSQLValueString($_POST['record_id'], "int"),
	                   GetSQLValueString($_POST['proj_record_id'], "int"),
	                   GetSQLValueString($_POST['proj_timestamp'], "text"),
                       GetSQLValueString($_POST['proj_date'], "text"),
                       GetSQLValueString($_POST['proj_number'], "text"),
                       GetSQLValueString($_POST['proj_name'], "text"),
                       GetSQLValueString($_POST['proj_loc'], "text"),
					   GetSQLValueString($_POST['proj_org'], "text"),
					   GetSQLValueString($_POST['proj_manager'], "text"),
					   GetSQLValueString($_POST['proj_cliname'], "text"),
					   GetSQLValueString($_POST['proj_cliadd'], "text"),
					   GetSQLValueString($_POST['proj_contact'], "text"),
					   GetSQLValueString($_POST['proj_invoice'], "text"),
					   GetSQLValueString($_POST['proj_attn'], "text"),
					   GetSQLValueString($_POST['proj_terms'], "text"),
					   GetSQLValueString($_POST['proj_contractamount'], "text"),
					   GetSQLValueString($_POST['proj_po'], "text"),
					   GetSQLValueString($_POST['proj_ponumber'], "text"),
					   GetSQLValueString($_POST['proj_pocof'], "text"),
					   GetSQLValueString($_POST['proj_propnumber'], "text"),
					   GetSQLValueString($_POST['proj_othterms'], "text"),
					   GetSQLValueString($_POST['proj_desc'], "text"),
					   GetSQLValueString($_POST['proj_req'], "text"),
					   GetSQLValueString($_POST['proj_reqemail'], "text"),
					   GetSQLValueString($_POST['proj_comments'], "text"),
					   GetSQLValueString($_POST['proj_billingsub'], "text"),
					   GetSQLValueString($_POST['proj_billingreimb'], "text"));
	mysql_select_db($database_koa, $koa);
  $ResultArch1 = mysql_query($insertARCHSQL, $koa) or die(mysql_error());
NO ARCHIVE */
//Send email notification
//$subj = "[Sheet Addition]";
include ('mailenterproject.php');

//Redirect if successful
$insertGoTo = "redirect.php?projNAME=$projNAME";
header(sprintf("Location: %s", $insertGoTo));
 }
 
//Get the project record number and use it to pull the recently generated record id
mysql_select_db($database_koa, $koa);
$recordID = $_GET['recordID'];
$query_koa = "SELECT * FROM koa_project WHERE record_id = $recordID ORDER BY record_id ASC";
$koatbl = mysql_query($query_koa, $koa) or die(mysql_error());
$row_koa = mysql_fetch_assoc($koatbl);
$totalRows_koa = mysql_num_rows($koatbl);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--[if IE]>
<script type="text/javascript"  src="includes/fix_eolas.js"  defer="defer"></script>
<![endif]-->
<title>Enter Project</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/style.css" rel="stylesheet" type="text/css">
</head>
<?PHP include ('header.php'); ?>
<table align="center" bgcolor="#CBDCEE" width="640" border="1" cellpadding="0" cellspacing="0" bordercolor="#E9F0F8">
	<tr align="center" bgcolor="#E9F0F8">
		<td><br>
			<strong>Enter Project</strong>
		<br><br></td>
	</tr>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
	<tr>
		<td><br>
	Project Number: <input class="text" type="text" name="proj_number" value="<?php echo $row_koa['proj_number']; ?>" size="25">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Project Name: <input class="text" type="text" name="proj_name" value="<?php echo $row_koa['proj_name']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	CC: <input class="text" type="text" name="proj_reqcc" value="" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	CC2: <input class="text" type="text" name="proj_reqcc2" value="" size="50">
		<br><br></td>
	</tr>	
	<!-- <tr>
		<td><br>
			Project Name:<strong> <?php echo $row_koa['proj_name']; ?></strong>
		<br><br></td>
	</tr>  -->
            <input type="hidden" name="proj_record_id" value="<?php echo $row_koa['record_id']; ?>">
            <input type="hidden" name="proj_timestamp" value="<?php echo $row_koa['timestamp']; ?>">
<!--            <input type="hidden" name="proj_name" value="<?php echo $row_koa['proj_name']; ?>">    -->
			<input type="hidden" name="proj_loc" value="<?php echo $row_koa['proj_loc']; ?>">
			<input type="hidden" name="proj_date" value="<?php echo $row_koa['proj_date']; ?>">
			<input type="hidden" name="proj_org" value="<?php echo $row_koa['proj_org']; ?>">
			<input type="hidden" name="proj_manager" value="<?php echo $row_koa['proj_manager']; ?>">
			<input type="hidden" name="proj_cliname" value="<?php echo $row_koa['proj_cliname']; ?>">
			<input type="hidden" name="proj_cliadd" value="<?php echo $row_koa['proj_cliadd']; ?>">
			<input type="hidden" name="proj_contact" value="<?php echo $row_koa['proj_contact']; ?>">
			<input type="hidden" name="proj_invoice" value="<?php echo $row_koa['proj_invoice']; ?>">
			<input type="hidden" name="proj_attn" value="<?php echo $row_koa['proj_attn']; ?>">
			<input type="hidden" name="proj_terms" value="<?php echo $row_koa['proj_terms']; ?>">
			<input type="hidden" name="proj_contractamount" value="<?php echo $row_koa['proj_contractamount']; ?>">
			<input type="hidden" name="proj_po" value="<?php echo $row_koa['proj_po']; ?>">
			<input type="hidden" name="proj_ponumber" value="<?php echo $row_koa['proj_ponumber']; ?>">
			<input type="hidden" name="proj_pocof" value="<?php echo $row_koa['proj_pocof']; ?>">
			<input type="hidden" name="proj_propnumber" value="<?php echo $row_koa['proj_propnumber']; ?>">
			<input type="hidden" name="proj_othterms" value="<?php echo $row_koa['proj_othterms']; ?>">
			<input type="hidden" name="proj_billingsub" value="<?php echo $row_koa['proj_billingsub']; ?>">
			<input type="hidden" name="proj_billingreimb" value="<?php echo $row_koa['proj_billingreimb']; ?>">
			<input type="hidden" name="proj_desc" value="<?php echo $row_koa['proj_desc']; ?>">
			<input type="hidden" name="proj_comments" value="<?php echo $row_koa['proj_comments']; ?>">
	     	<input type="hidden" name="proj_req" value="<?php echo $row_koa['proj_req']; ?>">
	     	<input type="hidden" name="proj_reqemail" value="<?php echo $row_koa['proj_reqemail']; ?>">
			<input type="hidden" name="MM_update" value="form1">

	<tr bgcolor="#E9F0F8" align="center">
		<td><br><br>
			<input name="submit" type="submit" value="Submit Worksheet" class="button" onMouseOver="this.className='button buttonhov'" onMouseOut="this.className='button'">
			<br>
		</td>
	</tr>
</form>
</table>
<br><br><br>
<?PHP include ('footer.php'); ?>