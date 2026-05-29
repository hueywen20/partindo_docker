<?php
$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname]</title>
$headinclude
<script type="text/javascript">
	$(document).ready(function() {
		$(".formID").validationEngine();
	})
</script>
</head>

<body>
<form class="formID" action="settings.php" method="post">
<div align="left" style="border-bottom: 1px dotted #555; padding-bottom: 10px;">Jenis Pengaturan : $gset</div>
<if criteria="!empty($contentset)">
<div align="left">
<table border="0" cellpadding="5" cellspacing="5">
$contentset
</table>
<input type="hidden" name="submits" value="Ubah">
<input type="submit" value="Ubah" class="button">
</div>
</form>
</endif>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>
</body>
</html>
';
?>