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
	$updateSQL = sprintf("UPDATE koa_project SET proj_date=%s, proj_number=%s, proj_name=%s, proj_loc=%s, proj_org=%s, proj_manager=%s, proj_cliname=%s, proj_cliadd=%s, proj_contact=%s, proj_invoice=%s, proj_attn=%s, proj_terms=%s, proj_contractamount=%s, proj_po=%s, proj_ponumber=%s, proj_pocof=%s, proj_propnumber=%s, proj_othterms=%s, proj_desc=%s, proj_req=%s, proj_reqemail=%s, proj_comments=%s, proj_billingsub=%s, proj_billingreimb=%s WHERE record_id=%s",
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
					   GetSQLValueString($_POST['proj_billingreimb'], "text"),
					   GetSQLValueString($_GET['recordID'], "int"));
	mysql_select_db($database_koa, $koa);
  $Result1 = mysql_query($updateSQL, $koa) or die(mysql_error());

// Update Archive and include original record id and timestamp.
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
<?php include('includes/vdaemon/vdaemon.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--[if IE]>
<script type="text/javascript"  src="includes/fix_eolas.js"  defer="defer"></script>
<![endif]-->
<title>Edit Project</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/style.css" rel="stylesheet" type="text/css">
</head>
<?PHP include ('header.php'); ?>
<table align="center" bgcolor="#CBDCEE" width="640" border="1" cellpadding="0" cellspacing="0" bordercolor="#E9F0F8">
	<tr align="center" bgcolor="#E9F0F8">
		<td><br>
			<strong>Edit Project</strong>
		<br><br></td>
	</tr>
<form method="post" name="form1" runat="vdaemon" action="<?php echo $editFormAction; ?>">
	<tr bgcolor="#E9F0F8">
		<td>
			<vlsummary class="error" headertext="The following required fields must be completed:" displaymode="bulletlist">
		</td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="projDATE">Date:</vllabel> <input class="text" type="text" name="proj_date" value="<?php echo $row_koa['proj_date']; ?>" size="16"><vlvalidator name="projDATE" type="required" control="proj_date" errmsg="Date">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Project Number: <input class="text" type="text" name="proj_number" value="<?php echo $row_koa['proj_number']; ?>" size="25">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="projNAME">Project Name:</vllabel> <input class="text" type="text" name="proj_name" value="<?php echo $row_koa['proj_name']; ?>" size="50"><vlvalidator name="projNAME" type="required" control="proj_name" errmsg="Project Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Project Location: <br><br><textarea class="box" name="proj_loc" value="" cols="50" rows="4"><?php echo $row_koa['proj_loc']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Organization: <input class="text" type="text" name="proj_org" value="<?php echo $row_koa['proj_org']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="projMANAGER">Project Manager:</vllabel> <input class="text" type="text" name="proj_manager" value="<?php echo $row_koa['proj_manager']; ?>" size="50"><vlvalidator name="projMANAGER" type="required" control="proj_manager" errmsg="Project Manager">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="projCLINAME">Client Name:</vllabel> <input class="text" type="text" name="proj_cliname" value="<?php echo $row_koa['proj_cliname']; ?>" size="50"><vlvalidator name="projCLINAME" type="required" control="proj_cliname" errmsg="Client Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Address:<br><br><textarea class="box" name="proj_cliadd" value="" cols="50" rows="4"><?php echo $row_koa['proj_cliadd']; ?></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Client Contact: <input class="text" type="text" name="proj_contact" value="<?php echo $row_koa['proj_contact']; ?>" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Invoice sent to same:  <input type="radio" name="proj_invoice" value="Yes">Yes&nbsp;-|-&nbsp;<input type="radio" name="proj_invoice" value="No">No&nbsp;&nbsp;Attn:&nbsp;<input class="text" type="text" name="proj_attn" value="<?php echo $row_koa['proj_attn']; ?>" size="30">
		<br><br></td>
	</tr>
	<tr>
		<td>Terms of Contract:&nbsp;
		<select name="proj_terms">
		<option value="<?php echo $row_koa['proj_terms']; ?>"><?php echo $row_koa['proj_terms']; ?>
		<option value="Lump Sum">Lump Sum
		<option value="Hourly">Hourly
		<option value="Not-To-Exceed">Not-To-Exceed
		<option value="Other">Other		
		</select>
		&nbsp;-|-&nbsp;Contract Amount:&nbsp;$<input class="text" type="text" name="proj_contractamount" value="<?php echo $row_koa['proj_contractamount']; ?>" size="25">
		</td>
	</tr>
	<tr>
		<td><br>
			Purchase Order:  <input type="radio" name="proj_po" value="Yes">Yes&nbsp;&nbsp;<input type="radio" name="proj_po" value="No">No&nbsp;-|-&nbsp;PO #:&nbsp;<input class="text" type="text" name="proj_ponumber" value="<?php echo $row_koa['proj_ponumber']; ?>" size="25">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal or Contract on File:  <input type="radio" name="proj_pocof" value="Yes">Yes&nbsp;&nbsp;<input type="radio" name="proj_pocof" value="No">No&nbsp;-|-&nbsp;
				Proposal #: <input class="text" type="text" name="proj_propnumber" value="<?php echo $row_koa['proj_propnumber']; ?>" size="20">
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
			<vllabel errclass="error" validators="projREQ">Requested by:</vllabel> <input class="text" type="text" name="proj_req" value="<?php echo $row_koa['proj_req']; ?>" size="30"><vlvalidator name="projREQ" type="required" control="proj_req" errmsg="Requested by">&nbsp;-|-&nbsp;<vllabel errclass="error" validators="projREQEMAIL">Email Address:</vllabel> <input class="text" type="text" name="proj_reqemail" value="<?php echo $row_koa['proj_reqemail']; ?>" size="30"><vlvalidator name="projREQEMAIL" type="required" control="proj_reqemail" errmsg="Requested by Email">
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
			Subcontractors:<input type="checkbox" name="proj_billingsub" value="Yes">
			Reimbursables:<input type="checkbox" name="proj_billingreimb" value="Yes">
		<br><br></td>
	</tr>
            <input type="hidden" name="proj_record_id" value="<?php echo $row_koa['record_id']; ?>">
            <input type="hidden" name="proj_timestamp" value="<?php echo $row_koa['timestamp']; ?>">
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