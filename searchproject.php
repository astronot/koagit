<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--[if IE]>
<script type="text/javascript"  src="includes/fix_eolas.js"  defer="defer"></script>
<![endif]-->
<title>Search - Project</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/style.css" rel="stylesheet" type="text/css">
</head>
<?PHP include ('header.php'); ?>
<table align="center" bgcolor="#CBDCEE" width="600" border="1" cellpadding="0" cellspacing="0" bordercolor="#E9F0F8">
<form method="post" name="form1" action="resultproject.php">
	<tr align="center" bgcolor="#E9F0F8">
		<td><br>
			<strong>Search</strong>
		<br><br></td>
	</tr>
	<tr align="center">
		<td><br>
			Find: <input class="text" type="text" name="data" value="" size="20">
			In: <select name="field" class="Menu">
                              <option value="proj_number" selected >Project #</option>
                              <option value="proj_name" >Project Name</option>
							  <option value="proj_req" >Requester</option>
				</select>
		<br><br></td>
	</tr>
	<tr bgcolor="#E9F0F8" align="center">
		<td><br>
			<input name="submit" type="submit" value="Submit Request" class="button" onMouseOver="this.className='button buttonhov'" onMouseOut="this.className='button'">
		</td>
	</tr>
</form>
</table>
<br><br><br>
<?PHP include ('footer.php'); ?>