<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Penjualan per Bulan</title>
$headinclude
<script src="js/sale.js"></script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<form name="reportsale" action="reportsale.php?view=monthly" method="post" target="_blank">
<table border="0" cellpadding="5" cellspacing="5">
<tr>
	<td align="left">Kode Customer</td>
	<td align="left">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left"><img src="img/customer.png" border="0" style="cursor: pointer" title="Cari Customer" onclick="window.open(\'customer.php?getlist=determine\',\'customerlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
		<td align="center" width="20"><b>&gt;</b></td>
		<td align="left">
		<input type="hidden" name="customercode" id="customercode">
		<input type="hidden" name="customeraddrid" id="customeraddrid">
		<div id="customerdetail" style="font-weight: bold"></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td align="left">Dari Bulan</td>
	<td align="left">
	<select name="monthstart">
		<option value="01">$arrmonthname[0]</option>
		<option value="02">$arrmonthname[1]</option>
		<option value="03">$arrmonthname[2]</option>
		<option value="04">$arrmonthname[3]</option>
		<option value="05">$arrmonthname[4]</option>
		<option value="06">$arrmonthname[5]</option>
		<option value="07">$arrmonthname[6]</option>
		<option value="08">$arrmonthname[7]</option>
		<option value="09">$arrmonthname[8]</option>
		<option value="10">$arrmonthname[9]</option>
		<option value="11">$arrmonthname[10]</option>
		<option value="12">$arrmonthname[11]</option>
	</select>
	<select name="yearstart">
	$cbyear
	</select></td>
</tr>
<tr>
	<td align="left">Sampai Bulan</td>
	<td align="left">
	<select name="monthend">
		<option value="01">$arrmonthname[0]</option>
		<option value="02">$arrmonthname[1]</option>
		<option value="03">$arrmonthname[2]</option>
		<option value="04">$arrmonthname[3]</option>
		<option value="05">$arrmonthname[4]</option>
		<option value="06">$arrmonthname[5]</option>
		<option value="07">$arrmonthname[6]</option>
		<option value="08">$arrmonthname[7]</option>
		<option value="09">$arrmonthname[8]</option>
		<option value="10">$arrmonthname[9]</option>
		<option value="11">$arrmonthname[10]</option>
		<option value="12">$arrmonthname[11]</option>
	</select>
	<select name="yearend">
	$cbyear
	</select></td>
</tr>
<tr>
	<td align="left">Tipe Transaksi</td>
	<td align="left">
	<select name="trtype">
		<option value="">Tunai dan Kredit</option>
		<option value="cash">Tunai</option>
		<option value="credit">Kredit</option>
	</select></td>
</tr>
<tr>
	<td align="left">Cetak Berdasarkan</td>
	<td align="left">
	<select name="basedon">
		<option value="customer">Per Customer</option>
		<option value="saledate">Tanggal Penjualan</option>
		<option value="trtype">Tipe Transaksi</option>
	</select></td>
</tr>
</table>
<div align="left">&nbsp;&nbsp;&nbsp;
<input type="submit" value="Cetak" class="button"></div>
</form>
</body>
</html>
';
?>