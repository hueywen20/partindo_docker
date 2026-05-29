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
<if criteria="$_GET[msg] == \'success\'">
<div align="center" style="color: #338833; font-weight: bold">Tutup Buku Selesai</div><br>
<else>
<if criteria="$_GET[msg] == \'error\'">
<div align="center" style="color: #FF3333; font-weight: bold">Tutup Buku untuk tahun $yearclosenow tidak dapat dilakukan, karena sudah pernah dilakukan tutup buku pada tahun tersebut</div><br>
</endif>
</if>
<div align="center">
<form action="closebook.php" method="post" onsubmit="return confirm(\'Apakah anda yakin?\')">
	Lakukan Tutup Buku per Tahun sekarang ?<br>( Proses mungkin akan memakan waktu beberapa menit )
	<input type="hidden" name="donow" value="nowdoing">
	<br><br><input type="submit" value="Lakukan Sekarang" class="button">
</form>
</div>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>
</body>
</html>
';
?>