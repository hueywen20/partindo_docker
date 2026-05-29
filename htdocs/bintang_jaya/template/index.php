<?php
$html = '
<if criteria="!empty($userid)">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname]</title>
<link media="all" href="css/sidebar.css" type="text/css" rel="stylesheet">
<link media="all" href="css/thickbox.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="js/jquerywindows/css/jquery.window.css">
<script type="text/javascript">
	var mm = [$jsaccess];
</script>
<script type="text/javascript" src="js/sidebar.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/global.js"></script>
<script type="text/javascript" src="js/jquerywindows/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquerywindows/jquery.window.js"></script>
<script type="text/javascript" src="./js/link.js"></script>
<script type="text/javascript" src="js/thickbox.js"></script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<div align="right"><b>Selamat Datang, $userdetail[username].</b> <a href="login.php?log=0" class="logoutlink"><b>Logout</b></a>&nbsp;&nbsp;&nbsp;</div>
<script>
<if criteria="$openclosebook">
	showCloseBook();
</endif>
$(document).click(function() {
	closeall(1);
});
$("#softmenu").click(function() {
	return false;
})
</script>

</body>

</html>
<else>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Bintang Jaya</title>
<link media="all" href="css/styles.css" type="text/css" rel="stylesheet">
<script type="text/javascript">
function checkall(obj){
	if (obj.username.value == ""){
		obj.username.focus();
		return false;
	}
	else if (obj.password.value == ""){
		obj.password.focus();
		return false;
	}
	return true;
}
if (self.parent.frames.length != 0){
	self.parent.location.replace(document.location.href);
}
</script>
<script type="text/javascript" src="js/init.js"></script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<br><br><br>
<form name="frmlogin" action="login.php" method="post" onsubmit="return checkall(this)">
<div align="center">
	<if criteria="$_GET[msg] == \'loginerror\'">
	<b>Maaf, username dan password anda salah</b><br>
	<else>
		<if criteria="$_GET[msg] == \'offline\' || $msgoff == \'offline\'">
		<b>Maaf, sistem sedang offline</b><br>
		</endif>
	</if>
	<div class="loginbox">
	<table border="0" cellpadding="5" cellspacing="5">
	<tr>
		<td align="left">User Name</td>
		<td align="left"><input type="text" name="username" size="25" autocomplete="off"></td>
	</tr>
	<tr>
		<td align="left">Password</td>
		<td align="left"><input type="password" name="password" size="25"></td>
	</tr>
	</table><br>
	<input type="submit" value="Login" class="button">
	</div>
</div>
</form>
</body>

</html>
</if>';
?>