<?php
$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Tanda Pembayaran</title>
<link media="all" href="css/print.css" type="text/css" rel="stylesheet">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0" onload="window.print()">
<table border="0" cellpadding="0" cellspacing="0" width="760">
<tr>
	<td width="760" align="left">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="380" align="left" valign="top">
		<if criteria="$_GET[op] == yes">
		<div style="font-size: 18px; font-weight: bold; text-transform: uppercase">$company[companyname]</div>
		$company[companyaddr]<br>
		Telp. $company[companytelp]</endif></td>
		<td width="380" colspan="2" align="right" valign="top">
		Tanggal : $invoicedate<br>
		<b>Kepada : </b> $customername<br>
		$customeraddr<br>
		$customertelp
		</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="380" align="left" valign="top">
		<b>NOTA PEMBAYARAN</b></td>
		<td width="380" colspan="2" align="right" valign="top"></td>
	</tr>
	<tr>
		<td colspan="3" valign="top" class="detailitem">
		<table border="0" width="760" cellpadding="2" cellspacing="0">
		$paymentdetail
		<tr>
			<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
		</tr>
		<tr>
			<td width="760" align="left" height="25">
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="50" align="center"></td>
				<td width="30"></td>
				<td width="280" align="center"></td>
				<td width="10"></td>
				<td width="180" align="right"><font size="3"><b>GRAND TOTAL</b></font></td>
				<td width="10"></td>
				<td width="200" align="right"><font size="3"><b>$ftotal</b></font></td>
			</tr>
			</table></td>
		</tr>
		</table></td>
	</tr>
	</table></td>
</tr>
</table>
<div style="width: 760px" align="left">
<b>Terbilang : </b> $terbilangs Rupiah
</div><br>
<div style="width: 695px; height: 60px" align="right">
Tertanda
</div>
<div style="width: 760px" align="right">
( _____________________ )
</div>
</body>

</html>
';
?>