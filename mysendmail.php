<!--form start here-->

<?php
$to = 'elapandya@gmail.com';

// subject
$subject = 'Pandya Family Foundation Website => Contact Form';
// message

$message = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="stylesheet1.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Query Form</title>
<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<Style>
BODY, P,TD{ font-family: Arial,Verdana,Helvetica, sans-serif; font-size: 10pt }
A{font-family: Arial,Verdana,Helvetica, sans-serif;}
B {	font-family : Arial, Helvetica, sans-serif;	font-size : 12px;	font-weight : bold;}
.error_strings{ font-family:Verdana; font-size:10px; color:#660000;}
</Style>
<!--- flash script start ------>
<script type="text/javascript" src="js/mootools-yui-compressed.js"></script>
<script type="text/javascript" src="js/slideshow.js"></script>
<!--- flash script end ------>
</head>

<body>
<br /><br /><br />
<div align="center">
<table width="800" border="1" cellspacing="4" cellpadding="5" bgcolor="#efe6c7" bordercolor="#6A77AB" style="border-collapse: collapse; padding:5px;color:#602600;">
  <tr>
    <td colspan="2" align="center"><p style="margin-left: 22; margin-right: 22" align="justify"><h1 align="center">Pandya Family Foundation Contact Form</h1></p></td>
  </tr>  <tr>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;Your name:</p></td>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;'. $_POST['name']. '</p></td>
  </tr>
  <tr>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;Email :</p></td>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;'. $_POST['email'] .'</p></td>
  </tr>
   <tr>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;Phno:</p></td>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;'. $_POST['phno'] .'</p></td>
  </tr>
  <tr>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;Comments:</p></td>
    <td><p style="margin-left: 22; margin-right: 22" align="justify">&nbsp;'. $_POST['comments'].'</p></td>
  </tr>
</table>
</div>

</body>
</html>';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//$headers .= 'To: elapandya@gmail.com' . "\r\n";
$headers .= 'From:'.$_POST['email']."\r\n";
// Mail it
$CHECK=mail($to, $subject, $message, $headers);

?> 
<?php
		if($CHECK) { ?><script type="text/javascript">
			window.location = "thanks.html"
			</script> <?php	}
		else  { ?><script type="text/javascript">
			window.location = "error.html"
			</script> <?php } 
?>