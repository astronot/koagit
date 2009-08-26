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
	$updateSQL = sprintf("UPDATE koa_proposal SET prop_number=%s, prop_name=%s WHERE record_id=%s",
	                   GetSQLValueString($_POST['prop_number'], "text"),
					   GetSQLValueString($_POST['prop_name'], "text"),
					   GetSQLValueString($_GET['recordID'], "int"));
	mysql_select_db($database_koa, $koa);
  $Result1 = mysql_query($updateSQL, $koa) or die(mysql_error());

/* Update Archive and include original record id and timestamp.
$insertARCHSQL = sprintf("INSERT INTO koa_proposal_archive (record_id, prop_record_id, prop_timestamp, prop_date, prop_number, prop_name, prop_loc, prop_org, prop_manager, prop_cliname, prop_cliadd, prop_contact, prop_desc, prop_req, prop_reqemail, prop_comments) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	                   GetSQLValueString($_POST['record_id'], "int"),
	                   GetSQLValueString($_POST['prop_record_id'], "int"),
	                   GetSQLValueString($_POST['prop_timestamp'], "text"),
                       GetSQLValueString($_POST['prop_date'], "text"),
                       GetSQLValueString($_POST['prop_number'], "text"),
                       GetSQLValueString($_POST['prop_name'], "text"),
                       GetSQLValueString($_POST['prop_loc'], "text"),
					   GetSQLValueString($_POST['prop_org'], "text"),
					   GetSQLValueString($_POST['prop_manager'], "text"),
					   GetSQLValueString($_POST['prop_cliname'], "text"),
					   GetSQLValueString($_POST['prop_cliadd'], "text"),
					   GetSQLValueString($_POST['prop_contact'], "text"),
					   GetSQLValueString($_POST['prop_desc'], "text"),
					   GetSQLValueString($_POST['prop_req'], "text"),
					   GetSQLValueString($_POST['prop_reqemail'], "text"),
					   GetSQLValueString($_POST['prop_comments'], "text"));
	mysql_select_db($database_koa, $koa);
  $ResultArch1 = mysql_query($insertARCHSQL, $koa) or die(mysql_error());
End Archive */
//Send email notification
//$subj = "[Sheet Addition]";
include ('mailenterproposal.php');

//Redirect if successful
$insertGoTo = "redirect.php?propDESC=$propDESC";
header(sprintf("Location: %s", $insertGoTo));
 }
 
//Get the proposal number and use it to pull the recently generated record id
mysql_select_db($database_koa, $koa);
$recordID = $_GET['recordID'];
$query_koa = "SELECT * FROM koa_proposal WHERE record_id = $recordID ORDER BY record_id ASC";
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
<title>Enter Proposal</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/style.css" rel="stylesheet" type="text/css">
</head>
<?PHP include ('header.php'); ?>
<table align="center" bgcolor="#CBDCEE" width="640" border="1" cellpadding="0" cellspacing="0" bordercolor="#E9F0F8">
<form method="post" name="form1" runat="vdaemon" action="<?php echo $editFormAction; ?>">
	<tr bgcolor="#E9F0F8">
		<td>
			<vlsummary class="error" headertext="The following required fields must be completed:" displaymode="bulletlist">
		</td>
	</tr>
	<tr align="center" bgcolor="#E9F0F8">
		<td><br>
			<strong>Enter Proposal</strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Proposal Number: <input class="text" type="text" name="prop_number" value="<?php echo $row_koa['prop_number']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Proposal Name: <input class="text" type="text" name="prop_name" value="<?php echo $row_koa['prop_name']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	CC: <input class="text" type="text" name="prop_reqcc" value="" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	CC2: <input class="text" type="text" name="prop_reqcc2" value="" size="50">
		<br><br></td>
	</tr>
<!--	<tr>
		<td><br>
			Proposal Name: <strong><?php echo $row_koa['prop_name']; ?></strong>
		<br><br></td>
	</tr> -->
		<br><br></td>
	</tr>
            <input type="hidden" name="prop_record_id" value="<?php echo $row_koa['record_id']; ?>">
            <input type="hidden" name="prop_timestamp" value="<?php echo $row_koa['timestamp']; ?>">
<!--	     		<input type="hidden" name="prop_name" value="<?php echo $row_koa['prop_name']; ?>">  -->
			<input type="hidden" name="prop_loc" value="<?php echo $row_koa['prop_loc']; ?>">
			<input type="hidden" name="prop_date" value="<?php echo $row_koa['prop_date']; ?>">
			<input type="hidden" name="prop_org" value="<?php echo $row_koa['prop_org']; ?>">
			<input type="hidden" name="prop_manager" value="<?php echo $row_koa['prop_manager']; ?>">
			<input type="hidden" name="prop_cliname" value="<?php echo $row_koa['prop_cliname']; ?>">
			<input type="hidden" name="prop_cliadd" value="<?php echo $row_koa['prop_cliadd']; ?>">
			<input type="hidden" name="prop_contact" value="<?php echo $row_koa['prop_contact']; ?>">
			<input type="hidden" name="prop_desc" value="<?php echo $row_koa['prop_desc']; ?>">
			<input type="hidden" name="prop_comments" value="<?php echo $row_koa['prop_comments']; ?>">
			<input type="hidden" name="prop_req" value="<?php echo $row_koa['prop_req']; ?>">
			<input type="hidden" name="prop_reqemail" value="<?php echo $row_koa['prop_reqemail']; ?>">
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