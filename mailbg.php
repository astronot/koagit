<?php //This code is not stand-alone, and must be included after an insert to function ?>
<?php
//Set field variables
if ((isset($_POST["bg_name"])) && ($_POST["bg_name"] != "")) { $bg_loc = $_POST["bg_name"]; } else { $bg_name = "Not Entered"; }
if ((isset($_POST["bg_org"])) && ($_POST["bg_org"] != "")) { $bg_org = $_POST["bg_org"]; } else { $bg_org = "Not Entered"; }
if ((isset($_POST["bg_cliadd"])) && ($_POST["bg_cliadd"] != "")) { $bg_cliadd = $_POST["bg_cliadd"]; } else { $bg_cliadd = "Not Entered"; }
if ((isset($_POST["bg_desc"])) && ($_POST["bg_desc"] != "")) { $bg_desc = $_POST["bg_desc"]; } else { $bg_desc = "Not Entered"; }
if ((isset($_POST["bg_comments"])) && ($_POST["bg_comments"] != "")) { $bg_comments = $_POST["bg_comments"]; } else { $bg_comments = "Not Entered"; }
if ((isset($_POST["bg_invoice"])) && ($_POST["bg_invoice"] != "")) { $bg_invoice = $_POST["bg_invoice"]; } else { $bg_invoice = "Not Entered"; }
if ((isset($_POST["bg_attn"])) && ($_POST["bg_attn"] != "")) { $bg_attn = $_POST["bg_attn"]; } else { $bg_attn = "Not Entered"; }
if ((isset($_POST["bg_terms"])) && ($_POST["bg_terms"] != "")) { $bg_terms = $_POST["bg_terms"]; } else { $bg_terms = "Not Entered"; }
if ((isset($_POST["bg_contractamount"])) && ($_POST["bg_contractamount"] != "")) { $bg_contractamount = $_POST["bg_contractamount"]; } else { $bg_contractamount = "Not Entered"; }
if ((isset($_POST["bg_po"])) && ($_POST["bg_po"] != "")) { $bg_po = $_POST["bg_po"]; } else { $bg_po = "Not Entered"; }
if ((isset($_POST["bg_ponumber"])) && ($_POST["bg_ponumber"] != "")) { $bg_ponumber = $_POST["bg_ponumber"]; } else { $bg_ponumber = "Not Entered"; }
if ((isset($_POST["bg_pocof"])) && ($_POST["bg_pocof"] != "")) { $bg_pocof = $_POST["bg_pocof"]; } else { $bg_pocof = "Not Entered"; }
if ((isset($_POST["bg_propnumber"])) && ($_POST["bg_propnumber"] != "")) { $bg_propnumber = $_POST["bg_propnumber"]; } else { $bg_propnumber = "Not Entered"; }
if ((isset($_POST["bg_othterms"])) && ($_POST["bg_othterms"] != "")) { $bg_othterms = $_POST["bg_othterms"]; } else { $bg_othterms = "Not Entered"; }
$timestamp = $timeSTMP;
?>
<?php
require("class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();     			// set mailer to use SMTP
$mail->Host = "localhost";  		// specify main and backup server
$mail->SMTPAuth = false;     	// turn on SMTP authentication
$mail->Username = "itsupport@s222999558.onlinehome.us";  	// SMTP username
$mail->Password = "krechojardit"; 		// SMTP password

$mail->From = "$bg_reqemail";
$mail->FromName = "$bg_req";
$mail->AddAddress("pandp@krechojard.com", "New Billing Group Notify List");
$mail->AddReplyTo("$bg_reqemail", "$bg_req");

$mail->WordWrap = 50;      		// set word wrap to 50 characters
$mail->IsHTML(true);          	// set email format to HTML

$mail->Subject = "NEW BILLING GROUP FOR $bg_projnumber - $bg_name";
$mail->Body    = "***********************************<br>* Date: <b>$bg_date</b><br>* Existing Project #:  <b>$bg_projnumber</b><br>* Billing Group Name:  <b>$bg_name</b><br>* KOA Organization:  <b>$bg_org</b><br>* KOA Project Manager:  <b>$bg_manager</b><br>* Client Name:  <b>$bg_cliname</b><br>* Client Address:  <b>$bg_cliadd</b><br>* Client Contact:  <b>$bg_contact</b><br>* Invoice sent to same:  <b>$bg_invoice</b>  Attn:  <b>$bg_attn</b><br>* Terms of Contract:  <b>$bg_terms</b>  Contract Amount:  <b>$bg_contractamount</b><br>* Purchase Order:  <b>$bg_po</b>  PO #:  <b>$bg_ponumber</b><br>* Proposal or Contract on File:  <b>$bg_pocof</b><br>* Proposal #:  <b>$bg_propnumber</b><br>* Other Terms:  <b>$bg_othterms</b><br>* Project Description & Scope of Work:  <b>$bg_desc</b><br>* Requested by:  <b>$bg_req</b><br>* Comments:  <b>$bg_comments</b><br>***********************************\n";
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