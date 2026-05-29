<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Laba / Rugi</title>
<link media="all" href="css/print_small.css" type="text/css" rel="stylesheet">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<if criteria="!empty($list)">
<div align="center" class="reporttitle" style="width: 760px">LAPORAN LABA / RUGI<br>
$_POST[datestart] s/d $_POST[dateend]</div><br>
<div align="right" style="width: 760px">Tanggal Cetak : $printdate</div>
<table border="1" cellpadding="0" cellspacing="0" width="760">
<tr>
	<th align="center" width="100" bgcolor="#DEDEDE">NO FAKTUR</th>
	<th align="center" width="100" bgcolor="#DEDEDE">TANGGAL</th>
	<th align="center" width="180" bgcolor="#DEDEDE">NAMA SUPPLIER / CUSTOMER</th>
	<th align="center" width="100" bgcolor="#DEDEDE">TRANSAKSI</th>
	<th align="center" width="140" bgcolor="#DEDEDE">DEBET</th>
	<th align="center" width="140" bgcolor="#DEDEDE">KREDIT</th>
</tr>
$list
</table><br>
<div align="center" class="reporttitle" style="width: 760px">$textpl</div>
<else>
<div align="left"><b>Tidak ada laporan laba rugi</b></div>
</if>
</body>
</html>
';
?>