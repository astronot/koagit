<?php //This code is not stand-alone, and must be included after an insert to function ?>
<?php
//Set field variables
if ((isset($_POST["sheet_number"])) && ($_POST["sheet_number"] != "")) { $sheet_number = $_POST["sheet_number"]; } else { $sheet_number = "Not Entered"; }
if ((isset($_POST["description"])) && ($_POST["description"] != "")) { $description = $_POST["description"]; } else { $description = "Not Entered"; }
if ((isset($_POST["revision"])) && ($_POST["revision"] != "")) { $revision = $_POST["revision"]; } else { $revision = "Not Entered"; }
if ((isset($_POST["revised_by"])) && ($_POST["revised_by"] != "")) { $revised_by = $_POST["revised_by"]; } else { $revised_by = "Not Entered"; }
if ((isset($_POST["revision_date"])) && ($_POST["revision_date"] != "")) { $revision_date = $_POST["revision_date"]; } else { $revision_date = "Not Entered"; }
$timestamp = $timeSTMP;
?>
<?php
require("class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();     			// set mailer to use SMTP
$mail->Host = "smtp.1and1.com";  		// specify main and backup server
$mail->SMTPAuth = true;     	// turn on SMTP authentication
$mail->Username = "itsupport@s222999558.onlinehome.us";  	// SMTP username
$mail->Password = "krechojardit"; 		// SMTP password

$mail->From = "support@krechojard.com";
$mail->FromName = "CLM Sheet Catalog";
$mail->AddAddress("clmsheet@krechojard.com", "CLM Notify List");
// $mail->AddReplyTo("support@krechojard.com", "Support2");

$mail->WordWrap = 50;      		// set word wrap to 50 characters
$mail->IsHTML(true);          	// set email format to HTML

$mail->Subject = "$subj -- $sheet_number";
$mail->Body    = "***********************************<br>* Sheet#: <b>$sheet_number</b><br>* Description:  <b>$description</b>     Revision:  <b>$revision</b><br>* Revised By:  <b>$revised_by</b>     Revision Date:  <b>$revision_date</b><br>* First Issued Date:  <b>$timestamp</b><br>***********************************\n";
$mail->AltBody = "***********************************\r\n\r\n\r\nSheet#: <$sheet_number>\r\nDescription:  <$description>     Revision:  <$revision>\r\nRevised By:  <$revised_by>     Revision Date:  <$revision_date>\r\nFirst Issued Date:  <$timestamp>\n";

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