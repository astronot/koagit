<?php //This code is not stand-alone, and must be included after an insert to function ?>
<?php
//Set field variables
if ((isset($_POST["proj_number"])) && ($_POST["proj_number"] != "")) { $proj_number = $_POST["proj_number"]; } else { $proj_number = "Not Entered"; }
if ((isset($_POST["proj_loc"])) && ($_POST["proj_loc"] != "")) { $proj_loc = $_POST["proj_loc"]; } else { $proj_loc = "Not Entered"; }
if ((isset($_POST["proj_org"])) && ($_POST["proj_org"] != "")) { $proj_org = $_POST["proj_org"]; } else { $proj_org = "Not Entered"; }
if ((isset($_POST["proj_cliadd"])) && ($_POST["proj_cliadd"] != "")) { $proj_cliadd = $_POST["proj_cliadd"]; } else { $proj_cliadd = "Not Entered"; }
if ((isset($_POST["proj_desc"])) && ($_POST["proj_desc"] != "")) { $proj_desc = $_POST["proj_desc"]; } else { $proj_desc = "Not Entered"; }
if ((isset($_POST["proj_comments"])) && ($_POST["proj_comments"] != "")) { $proj_comments = $_POST["proj_comments"]; } else { $proj_comments = "Not Entered"; }
if ((isset($_POST["proj_invoice"])) && ($_POST["proj_invoice"] != "")) { $proj_invoice = $_POST["proj_invoice"]; } else { $proj_invoice = "Not Entered"; }
if ((isset($_POST["proj_attn"])) && ($_POST["proj_attn"] != "")) { $proj_attn = $_POST["proj_attn"]; } else { $proj_attn = "Not Entered"; }
if ((isset($_POST["proj_terms"])) && ($_POST["proj_terms"] != "")) { $proj_terms = $_POST["proj_terms"]; } else { $proj_terms = "Not Entered"; }
if ((isset($_POST["proj_contractamount"])) && ($_POST["proj_contractamount"] != "")) { $proj_contractamount = $_POST["proj_contractamount"]; } else { $proj_contractamount = "Not Entered"; }
if ((isset($_POST["proj_po"])) && ($_POST["proj_po"] != "")) { $proj_po = $_POST["proj_po"]; } else { $proj_po = "Not Entered"; }
if ((isset($_POST["proj_ponumber"])) && ($_POST["proj_ponumber"] != "")) { $proj_ponumber = $_POST["proj_ponumber"]; } else { $proj_ponumber = "Not Entered"; }
if ((isset($_POST["proj_pocof"])) && ($_POST["proj_pocof"] != "")) { $proj_pocof = $_POST["proj_pocof"]; } else { $proj_pocof = "Not Entered"; }
if ((isset($_POST["proj_propnumber"])) && ($_POST["proj_propnumber"] != "")) { $proj_propnumber = $_POST["proj_propnumber"]; } else { $proj_propnumber = "Not Entered"; }
if ((isset($_POST["proj_othterms"])) && ($_POST["proj_othterms"] != "")) { $proj_othterms = $_POST["proj_othterms"]; } else { $proj_othterms = "Not Entered"; }
if ((isset($_POST["proj_billingsub"])) && ($_POST["proj_billingsub"] != "")) { $proj_billingsub = $_POST["proj_billingsub"]; } else { $proj_billingsub = "No"; }
if ((isset($_POST["proj_billingreimb"])) && ($_POST["proj_billingreimb"] != "")) { $proj_billingreimb = $_POST["proj_billingreimb"]; } else { $proj_billingreimb = "No"; }
$timestamp = $timeSTMP;
?>
<?php
require("class.phpmailer.php");

$mail = new PHPMailer();
$mail->SMTPDebug = 1;

$mail->IsSMTP();     			// set mailer to use SMTP
$mail->Host = "localhost";  		// specify main and backup server
$mail->SMTPAuth = false;     	// turn on SMTP authentication
$mail->Username = "itsupport@s222999558.onlinehome.us";  	// SMTP username
$mail->Password = "krechojardit"; 		// SMTP password

$mail->From = "support@krechojard.com";
$mail->FromName = "Project DB";
$mail->AddCC("pandp@krechojard.com", "New Project Notify List");
if ((isset($_POST["proj_reqcc"])) && ($_POST["proj_reqcc"] != "")) {
$mail->AddCC("$proj_reqcc");
	}
if ((isset($_POST["proj_reqcc2"])) && ($_POST["proj_reqcc2"] != "")) {
$mail->AddCC("$proj_reqcc2");
	}
$mail->AddAddress("$proj_reqemail", "$proj_req");
//$mail->AddReplyTo("$proj_reqemail", "$proj_req");

$mail->WordWrap = 50;      		// set word wrap to 50 characters
$mail->IsHTML(true);          	// set email format to HTML

$mail->Subject = "PROJECT NUMBER ENTERED - $proj_name";
$mail->Body    = "***********************************<br>* Date: <b>$proj_date</b><br>* Project Number:  <font style=\"color:red;\"><b>$proj_number</b></font><br>* Project Name:  <b>$proj_name</b><br>* Project Location:  <b>$proj_loc</b><br>* KOA Organization:  <b>$proj_org</b><br>* KOA Project Manager:  <b>$proj_manager</b><br>* Client Name:  <b>$proj_cliname</b><br>* Client Address:  <b>$proj_cliadd</b><br>* Client Contact:  <b>$proj_contact</b><br>* Invoice sent to same:  <b>$proj_invoice</b>  Attn:  <b>$proj_attn</b><br>* Terms of Contract:  <b>$proj_terms</b>  Contract Amount:  <b>$proj_contractamount</b><br>* Purchase Order:  <b>$proj_po</b>  PO #:  <b>$proj_ponumber</b><br>* Proposal or Contract on File:  <b>$proj_pocof</b><br>* Proposal #:  <b>$proj_propnumber</b><br>* Other Terms:  <b>$proj_othterms</b><br>* Project Description & Scope of Work:  <b>$proj_desc</b><br>* Requested by:  <b>$proj_req</b><br>* Comments:  <b>$proj_comments</b><br>* Additional Billing Groups:  <b>Subcontractors: $proj_billingsub  Reimbursables: $proj_billingreimb</b><br>***********************************\n";
// $mail->AltBody = "***********************************\r\n\r\n\r\nSheet#: <$sheet_number>\r\nDescription:  <$description>     Revision:  <$revision>\r\nRevised By:  <$revised_by>     Revision Date:  <$revision_date>\r\nFirst Issued Date:  <$timestamp>\n";

if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

//echo "Message has been sent";

//$insertGoTo = "result.php?empID=$empName";
//header(sprintf("Location: %s", $insertGoTo));
?>