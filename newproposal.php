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
	
// Insert new record if submitted, else skip
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$insertSQL = sprintf("INSERT INTO koa_proposal (record_id, prop_date, prop_name, prop_loc, prop_org, prop_manager, prop_cliname, prop_cliadd, prop_contact, prop_desc, prop_req, prop_reqemail, prop_comments) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	                   GetSQLValueString($_POST['record_id'], "int"),
                       GetSQLValueString($_POST['prop_date'], "text"),
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
  $Result1 = mysql_query($insertSQL, $koa) or die(mysql_error());

//Insert into Archive
//Get the proposal number and use it to pull the recently generated record id
$propNAME = $_POST['prop_name'];

$query_koa = "SELECT record_id, timestamp FROM koa_proposal WHERE prop_name = '$propNAME' ORDER BY record_id ASC";
$koatbl = mysql_query($query_koa, $koa) or die(mysql_error());
$row_koa = mysql_fetch_assoc($koatbl);
$totalRows_koa = mysql_num_rows($koatbl);

/* Insert into archive - include the recently generated primary record id
$insertARCHSQL = sprintf("INSERT INTO koa_proposal_archive (record_id, prop_record_id, prop_timestamp, prop_date, prop_name, prop_loc, prop_org, prop_manager, prop_cliname, prop_cliadd, prop_contact, prop_desc, prop_req, prop_reqemail, prop_comments) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	                   GetSQLValueString($_POST['record_id'], "int"),
	                   GetSQLValueString($row_koa['record_id'], "int"),
	                   GetSQLValueString($row_koa['timestamp'], "text"),
                       GetSQLValueString($_POST['prop_date'], "text"),
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
NO ARCHIVE */
//Send email notification
//$subj = "[Sheet Addition]";
$recordID = $row_koa['record_id'];
include ('mailproposal.php');

//Redirect if successful
$insertGoTo = "redirect.php?propDESC=$propDESC";
header(sprintf("Location: %s", $insertGoTo));

 }
?>
<?php include('includes/vdaemon/vdaemon.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--[if IE]>
<script type="text/javascript"  src="includes/fix_eolas.js"  defer="defer"></script>
<![endif]-->
<title>New Proposal Worksheet</title>
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
			<strong>New Proposal Worksheet</strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propDATE">Date:</vllabel> <input class="text" type="text" name="prop_date" value="<?php echo date("Y-m-d") ?>" size="16"><vlvalidator name="propDATE" type="required" control="prop_date" errmsg="Date">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propNAME">Proposal Name:</vllabel> <input class="text" type="text" name="prop_name" value="" size="50"><vlvalidator name="propNAME" type="required" control="prop_name" errmsg="Proposal Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal Location: <br><br><textarea class="box" name="prop_loc" value="" cols="50" rows="4"></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	KOA Organization: <input class="text" type="text" name="prop_org" value="" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	KOA Project Manager: <input class="text" type="text" name="prop_manager" value="" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propCLINAME">Client Name:</vllabel> <input class="text" type="text" name="prop_cliname" value="" size="50"><vlvalidator name="propCLINAME" type="required" control="prop_cliname" errmsg="Client Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Address:<br><br><textarea class="box" name="prop_cliadd" value="" cols="50" rows="4"></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Contact: <input class="text" type="text" name="prop_contact" value="" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal Description & Scope of Work:<br><br><textarea class="box" name="prop_desc" value="" cols="50" rows="4"></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="propREQ">Requested by:</vllabel> <input class="text" type="text" name="prop_req" value="" size="30"><vlvalidator name="propREQ" type="required" control="prop_req" errmsg="Requested by">&nbsp;-|-&nbsp;<vllabel errclass="error" validators="propREQEMAIL">Email Address:</vllabel> <input class="text" type="text" name="prop_reqemail" value="" size="30"><vlvalidator name="propREQEMAIL" type="required" control="prop_reqemail" errmsg="Requested by Email">
		<br></td>
	</tr>
	<tr>
		<td bgcolor="#FF3333">
			<center><b>DO NOT FORGET TO ENTER YOUR FULL EMAIL ADDRESS IE: <br> FIRSTNAME.LASTNAME@KRECHOJARD.COM !!</color></b></center>
		</td>
	</tr>
	<tr>
		<td><br>
			Comments:<br><br><textarea class="box" name="prop_comments" value="" cols="50" rows="4"></textarea>
		<br><br></td>
	</tr>
            <input type="hidden" name="MM_insert" value="form1">

	<tr bgcolor="#E9F0F8" align="center">
		<td><br><br>
			<input name="submit" type="submit" value="Submit Worksheet" class="button" onMouseOver="this.className='button buttonhov'" onMouseOut="this.className='button'" onclick="return confirm('Are you sure you want to submit the form?');">
			<br>
		</td>
	</tr>
</form>
</table>
<br><br><br>
<?PHP include ('footer.php'); ?>