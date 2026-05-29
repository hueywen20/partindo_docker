<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Daftar Barang</title>
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_vista.css">
$headinclude
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<form name="reportstufflist" action="reportstufflist.php" method="post" target="_blank">
<table border="0" cellpadding="5" cellspacing="5">
<tr>
	<td align="left">Kode Barang</td>
	<td align="left">
	<input type="text" name="stockcodestart" size="21"> - <input type="text" name="stockcodeend" size="21"></td>
</tr>
<tr>
	<td align="left">Nama Umum</td>
	<td align="left">
	<input type="text" name="generalname" size="50"></td>
</tr>
<tr>
	<td align="left">Merek</td>
	<td align="left">
	<input type="text" name="brandname" size="50"></td>
</tr>
<tr>
	<td align="left">Tipe</td>
	<td align="left">
	<input type="text" name="typename" size="50"></td>
</tr>
<tr>
	<td align="left">Ukuran</td>
	<td align="left">
	<input type="text" name="size" size="50"></td>
</tr>
<tr>
	<td align="left">Lokasi</td>
	<td align="left">
	<input type="text" name="locationname" size="50"></td>
</tr>
<tr>
	<td align="left">Sisa</td>
	<td align="left">
	<input type="text" name="realremaining" size="50"></td>
</tr>
<tr>
	<td align="left">Satuan</td>
	<td align="left">
	<input type="text" name="unitname" size="50"></td>
</tr>
<tr>
	<td align="left">Modal Min</td>
	<td align="left">
	<input type="text" name="buyminprice" size="50"></td>
</tr>
<tr>
	<td align="left">Modal Max</td>
	<td align="left">
	<input type="text" name="buymaxprice" size="50"></td>
</tr>
<tr>
	<td align="left">Exp Date</td>
	<td align="left">
	<input type="text" name="minexpdate" size="50"></td>
</tr>
<tr>
	<td align="left">Nomor Part</td>
	<td align="left">
	<input type="text" name="partno" size="50"></td>
</tr>
<tr>
	<td align="left">Grup Stok</td>
	<td align="left">
	<select name="stockgroup">
		<option value=""></option>
		$allgroups
	</select></td>
</tr>
<tr>
	<td align="left">Data ke - </td>
	<td align="left">
	<input type="text" name="startlimit" size="5"> - Sebanyak <input type="text" name="manys" size="5"></td>
</tr>
</table>
<div align="left">&nbsp;&nbsp;&nbsp;
<input type="hidden" value="prints" name="printit">
<input type="submit" value="Cetak" class="button"></div>
</form>
</body>
</html>
';
?>