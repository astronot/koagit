<?php //This code is not stand-alone, and must be included after an insert to function ?>
<?php
//Set field variables
if ((isset($_POST["prop_number"])) && ($_POST["prop_number"] != "")) { $prop_number = $_POST["prop_number"]; } else { $prop_number = "Not Entered"; }
if ((isset($_POST["prop_loc"])) && ($_POST["prop_loc"] != "")) { $prop_loc = $_POST["prop_loc"]; } else { $prop_loc = "Not Entered"; }
if ((isset($_POST["prop_org"])) && ($_POST["prop_org"] != "")) { $prop_org = $_POST["prop_org"]; } else { $prop_org = "Not Entered"; }
if ((isset($_POST["prop_manager"])) && ($_POST["prop_manager"] != "")) { $prop_manager = $_POST["prop_manager"]; } else { $prop_manager = "Not Entered"; }
if ((isset($_POST["prop_cliadd"])) && ($_POST["prop_cliadd"] != "")) { $prop_cliadd = $_POST["prop_cliadd"]; } else { $prop_cliadd = "Not Entered"; }
if ((isset($_POST["prop_contact"])) && ($_POST["prop_contact"] != "")) { $prop_contact = $_POST["prop_contact"]; } else { $prop_contact = "Not Entered"; }
if ((isset($_POST["prop_desc"])) && ($_POST["prop_desc"] != "")) { $prop_desc = $_POST["prop_desc"]; } else { $prop_desc = "Not Entered"; }
if ((isset($_POST["prop_comments"])) && ($_POST["prop_comments"] != "")) { $prop_comments = $_POST["prop_comments"]; } else { $prop_comments = "Not Entered"; }
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
$mail->FromName = "Proposal DB";
$mail->AddAddress("pandp@krechojard.com", "New Proposal Notify List");
$mail->AddAddress("$prop_reqemail", "$prop_req");
if ((isset($_POST["prop_reqcc"])) && ($_POST["prop_reqcc"] != "")) {
$mail->AddCC("$prop_reqcc");
	}
if ((isset($_POST["prop_reqcc2"])) && ($_POST["prop_reqcc2"] != "")) {
$mail->AddCC("$prop_reqcc2");
	}
//$mail->AddReplyTo("$prop_reqemail", "$prop_req");

$mail->WordWrap = 50;      		// set word wrap to 50 characters
$mail->IsHTML(true);          	// set email format to HTML

$mail->Subject = "PROPOSAL NUMBER ENTERED - $prop_name";
$mail->Body    = "***********************************<br>* Date: <b>$prop_date</b><br>* Proposal Number:  <font style=\"color:red;\"><b>$prop_number</b></font><br>* Proposal Name:  <b>$prop_name</b><br>* Proposal Location:  <b>$prop_loc</b><br>* KOA Organization:  <b>$prop_org</b><br>* KOA Project Manager:  <b>$prop_manager</b><br>* Client Name:  <b>$prop_cliname</b><br>* Client Address:  <b>$prop_cliadd</b><br>* Contact:  <b>$prop_contact</b><br>* Proposal Description & Scope of Work:  <b>$prop_desc</b><br>* Requested by:  <b>$prop_req</b><br>* Comments:  <b>$prop_comments</b><br>***********************************\n";
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