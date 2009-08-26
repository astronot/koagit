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
	$insertSQL = sprintf("INSERT INTO koa_bg (record_id, bg_date, bg_projnumber, bg_name, bg_org, bg_manager, bg_cliname, bg_cliadd, bg_contact, bg_invoice, bg_attn, bg_terms, bg_contractamount, bg_po, bg_ponumber, bg_pocof, bg_propnumber, bg_othterms, bg_desc, bg_req, bg_reqemail, bg_comments) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	                   GetSQLValueString($_POST['record_id'], "int"),
                       GetSQLValueString($_POST['bg_date'], "text"),
                       GetSQLValueString($_POST['bg_projnumber'], "text"),
                       GetSQLValueString($_POST['bg_name'], "text"),
					   GetSQLValueString($_POST['bg_org'], "text"),
					   GetSQLValueString($_POST['bg_manager'], "text"),
					   GetSQLValueString($_POST['bg_cliname'], "text"),
					   GetSQLValueString($_POST['bg_cliadd'], "text"),
					   GetSQLValueString($_POST['bg_contact'], "text"),
					   GetSQLValueString($_POST['bg_invoice'], "text"),
					   GetSQLValueString($_POST['bg_attn'], "text"),
					   GetSQLValueString($_POST['bg_terms'], "text"),
					   GetSQLValueString($_POST['bg_contractamount'], "text"),
					   GetSQLValueString($_POST['bg_po'], "text"),
					   GetSQLValueString($_POST['bg_ponumber'], "text"),
					   GetSQLValueString($_POST['bg_pocof'], "text"),
					   GetSQLValueString($_POST['bg_propnumber'], "text"),
					   GetSQLValueString($_POST['bg_othterms'], "text"),
					   GetSQLValueString($_POST['bg_desc'], "text"),
					   GetSQLValueString($_POST['bg_req'], "text"),
					   GetSQLValueString($_POST['bg_reqemail'], "text"),
					   GetSQLValueString($_POST['bg_comments'], "text"));
	mysql_select_db($database_koa, $koa);
  $Result1 = mysql_query($insertSQL, $koa) or die(mysql_error());

//Insert into Archive
//Get the project number and use it to pull the recently generated record id
$bgNAME = $_POST['bg_name'];

$query_koa = "SELECT record_id, timestamp FROM koa_bg WHERE bg_name = '$bgNAME' ORDER BY record_id ASC";
$koatbl = mysql_query($query_koa, $koa) or die(mysql_error());
$row_koa = mysql_fetch_assoc($koatbl);
$totalRows_koa = mysql_num_rows($koatbl);
//Send email notification
//$subj = "[Sheet Addition]";
include ('mailbg.php');

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
<title>New Billing Group Worksheet</title>
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
			<strong>New Billing Group Worksheet</strong>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="bgDATE">Date:</vllabel> <input class="text" type="text" name="bg_date" value="<?php echo date("m-d-Y") ?>" size="16"><vlvalidator name="bgDATE" type="required" control="bg_date" errmsg="Date">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="bgPROJNUMBER">Existing Project #:</vllabel> <input class="text" type="text" name="bg_projnumber" value="" size="50"><vlvalidator name="bgPROJNUMBER" type="required" control="bg_projnumber" errmsg="Existing Project #">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="bgNAME">Billing Group Name:</vllabel> <input class="text" type="text" name="bg_name" value="" size="50"><vlvalidator name="bgNAME" type="required" control="bg_name" errmsg="Billing Group Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	KOA Organization: <input class="text" type="text" name="bg_org" value="" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="bgMANAGER">KOA Project Manager:</vllabel> <input class="text" type="text" name="bg_manager" value="" size="50"><vlvalidator name="bgMANAGER" type="required" control="bg_manager" errmsg="Project Manager">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="bgCLINAME">Client Name:</vllabel> <input class="text" type="text" name="bg_cliname" value="" size="50"><vlvalidator name="bgCLINAME" type="required" control="bg_cliname" errmsg="Client Name">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Client Address:<br><br><textarea class="box" name="bg_cliadd" value="" cols="50" rows="4"></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
	Client Contact: <input class="text" type="text" name="bg_contact" value="" size="50">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Invoice sent to same:  <input type="radio" name="bg_invoice" value="Yes">Yes&nbsp;-|-&nbsp;<input type="radio" name="bg_invoice" value="No">No&nbsp;&nbsp;Attn:&nbsp;<input class="text" type="text" name="bg_attn" value="" size="30">
		<br><br></td>
	</tr>
	<tr>
		<td><vllabel errclass="error" validators="bgTERMS">Terms of Contract:</vllabel>&nbsp;
		<select name="bg_terms">
		<option value="">Select One
		<option value="Fixed Fee (Lump Sum)">Fixed Fee (Lump Sum)
		<option value="Hourly (No Cap)">Hourly (No Cap)
		<option value="Hourly - Not-To-Exceed">Hourly - Not-To-Exceed
		<option value="Other">Other		
		</select><vlvalidator name="bgTERMS" type="required" control="bg_terms" errmsg="Terms of Contract">
		&nbsp;-|-&nbsp;Contract Amount:&nbsp;$<input class="text" type="text" name="bg_contractamount" value="" size="25">
		</td>
	</tr>
	<tr>
		<td><br>
			Purchase Order:  <input type="radio" name="bg_po" value="Yes">Yes&nbsp;&nbsp;<input type="radio" name="bg_po" value="No">No&nbsp;-|-&nbsp;PO #:&nbsp;<input class="text" type="text" name="bg_ponumber" value="" size="25">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Proposal or Contract on File:  <input type="radio" name="bg_pocof" value="Yes">Yes&nbsp;&nbsp;<input type="radio" name="bg_pocof" value="No">No&nbsp;-|-&nbsp;
				Proposal #: <input class="text" type="text" name="bg_propnumber" value="" size="20">
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Other Terms:<br><br><textarea class="box" name="bg_othterms" value="" cols="50" rows="4"></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			Project Description & Scope of Work:<br><br><textarea class="box" name="bg_desc" value="" cols="50" rows="4"></textarea>
		<br><br></td>
	</tr>
	<tr>
		<td><br>
			<vllabel errclass="error" validators="bgREQ">Requested by:</vllabel> <input class="text" type="text" name="bg_req" value="" size="30"><vlvalidator name="bgREQ" type="required" control="bg_req" errmsg="Requested by">&nbsp;-|-&nbsp;<vllabel errclass="error" validators="bgREQEMAIL">Email Address:</vllabel> <input class="text" type="text" name="bg_reqemail" value="" size="30"><vlvalidator name="bgREQEMAIL" type="required" control="bg_reqemail" errmsg="Requested by Email">
		<br></td>
	</tr>
	<tr>
		<td bgcolor="#FF3333">
			<center><b>DO NOT FORGET TO ENTER YOUR FULL EMAIL ADDRESS IE: <br> FIRSTNAME.LASTNAME@KRECHOJARD.COM !!</color></b></center>
		</td>
	</tr>
	<tr>
		<td><br>
			Comments:<br><br><textarea class="box" name="bg_comments" value="" cols="50" rows="4"></textarea>
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