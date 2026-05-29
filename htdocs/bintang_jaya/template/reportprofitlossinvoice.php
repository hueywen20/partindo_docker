<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Laba Rugi per Faktur</title>
<link media="all" href="css/print_small.css" type="text/css" rel="stylesheet">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<if criteria="!empty($list)">
<div align="center" class="reporttitle" style="width: 100%">LAPORAN LABA RUGI PER FAKTUR<br>
$_POST[datestart] s/d $_POST[dateend]</div><br>
<div align="right" style="width: 100%">Tanggal Cetak : $printdate</div>
<table border="1" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
	<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
	<th align="center" width="14%" bgcolor="#DEDEDE">NO FAKTUR</th>
	<th align="center" width="20%" bgcolor="#DEDEDE">NAMA CUSTOMER</th>
	<th align="center" width="13%" bgcolor="#DEDEDE">DISKON</th>
	<th align="center" width="13%" bgcolor="#DEDEDE">PPN</th>
	<th align="center" width="20%" bgcolor="#DEDEDE">TOTAL</th>
</tr>
$list
</table>
<else>
<div align="left"><b>Tidak ada laporan laba rugi per faktur</b></div>
</if>
</body>
</html>
';
?>