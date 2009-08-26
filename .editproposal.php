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
	$updateSQL = sprintf("UPDATE koa_proposal SET prop_date=%s, prop_number=%s, prop_name=%s, prop_loc=%s, prop_org=%s, prop_manager=%s, prop_cliname=%s, prop_cliadd=%s, prop_contact=%s, prop_desc=%s, prop_req=%s, prop_reqemail=%s, prop_comments=%s WHERE record_id=%s",
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
					   GetSQLValueString($_POST['prop_comments'], "text"),
					   GetSQLValueString($_GET['recordID'], "int"));
	mysql_select_db($database_koa, $koa);
  $Result1 = mysql_query($updateSQL, $koa) or die(mysql_error());

// Update Archive and include original record id and timestamp.
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

//Send email notification
//$subj = "[Sheet Addition]";
//include ('mailproposal.php');

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
<title>Edit Proposal</title>
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
			<strong>Edit Proposal</strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propDATE">Date:</vllabel> <input class="text" type="text" name="prop_date" value="<?php echo $row_koa['prop_date']; ?>" size="16"><vlvalidator name="propDATE" type="required" control="prop_date" errmsg="Date">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Proposal Number: <input class="text" type="text" name="prop_number" value="<?php echo $row_koa['prop_number']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propNAME">Proposal Name:</vllabel> <input class="text" type="text" name="prop_name" value="<?php echo $row_koa['prop_name']; ?>" size="50"><vlvalidator name="propNAME" type="required" control="prop_name" errmsg="Proposal Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal Location: <br><br><textarea class="box" name="prop_loc" cols="50" rows="4"><?php echo $row_koa['prop_loc']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Organization: <input class="text" type="text" name="prop_org" value="<?php echo $row_koa['prop_org']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Project Manager: <input class="text" type="text" name="prop_manager" value="<?php echo $row_koa['prop_manager']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propCLINAME">Client Name:</vllabel> <input class="text" type="text" name="prop_cliname" value="<?php echo $row_koa['prop_cliname']; ?>" size="50"><vlvalidator name="propCLINAME" type="required" control="prop_cliname" errmsg="Client Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Address:<br><br><textarea class="box" name="prop_cliadd" cols="50" rows="4"><?php echo $row_koa['prop_cliadd']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Contact: <input class="text" type="text" name="prop_contact" value="<?php echo $row_koa['prop_contact']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal Description & Scope of Work:<br><br><textarea class="box" name="prop_desc" cols="50" rows="4"><?php echo $row_koa['prop_desc']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propREQ">Requested by:</vllabel> <input class="text" type="text" name="prop_req" value="<?php echo $row_koa['prop_req']; ?>" size="30"><vlvalidator name="propREQ" type="required" control="prop_req" errmsg="Requested by">&nbsp;-|-&nbsp;<vllabel errclass="error" validators="propREQEMAIL">Email Address:</vllabel> <input class="text" type="text" name="prop_reqemail" value="<?php echo $row_koa['prop_reqemail']; ?>" size="30"><vlvalidator name="propREQEMAIL" type="required" control="prop_reqemail" errmsg="Requested by Email">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Comments:<br><br><textarea class="box" name="prop_comments" cols="50" rows="4"><?php echo $row_koa['prop_comments']; ?></textarea>
		<br><br></td>
	</tr>
            <input type="hidden" name="prop_record_id" value="<?php echo $row_koa['record_id']; ?>">
            <input type="hidden" name="prop_timestamp" value="<?php echo $row_koa['timestamp']; ?>">
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