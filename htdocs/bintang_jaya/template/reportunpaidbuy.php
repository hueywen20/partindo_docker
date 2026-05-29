<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Bon Pembelian yang Belum Lunas (Hutang)</title>
<link media="all" href="css/print_small.css" type="text/css" rel="stylesheet">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<if criteria="!empty($listall)">
<div align="center" class="reporttitle" style="width: 100%">LAPORAN BON PEMBELIAN YANG BELUM LUNAS (HUTANG)</div></div>
$listall
<else>
<div align="left"><b>Tidak ada laporan bon pembelian yang belum lunas (hutang)</b></div>
</if>
</body>
</html>
';
?>