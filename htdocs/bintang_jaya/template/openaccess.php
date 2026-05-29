<?php
$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname]</title>
$headinclude
</head>

<body>
<if criteria="$opens == \'success\'">
<script type="text/javascript">
	window.opener.allowSale();
	window.close();
</script>
<else>
<div align="center">
<fieldset>
	<legend>Buka Akses</legend>
	<div align="center"><br>
	<form action="openaccess.php" method="post">
	Masukkan password : 
	<input type="password" name="openpass" size="30">
	<input type="hidden" name="actions" value="openit">
	<br><br><input type="submit" class="button" value="Submit">
	</form><br></div>
</fieldset>
</div>
</if>
</body>

</html>
';
?>