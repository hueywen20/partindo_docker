<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Penjualan per Tanggal per Customer</title>
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_vista.css">
$headinclude
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/sale.js"></script>
<script>
window.onload = function() {
    cal = new dhtmlxCalendarObject("datestart",true,{
		isWinHeader: true,
        isWinDrag: true,
		isYearEditable: true,
		isMonthEditable: true
	});
	cal.setDateFormat("%d-%m-%Y");
	cal.setSkin("vista");
	
    cale = new dhtmlxCalendarObject("dateend",true,{
		isWinHeader: true,
        isWinDrag: true,
		isYearEditable: true,
		isMonthEditable: true
	});
	cale.setDateFormat("%d-%m-%Y");
	cale.setSkin("vista");
}
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<form name="reportlongdebt" action="reportlongdebt.php" method="post" target="_blank">
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
	<td align="left">Dari Tanggal</td>
	<td align="left">
	<input type="text" name="datestart" id="datestart" size="20" readonly></td>
</tr>
<tr>
	<td align="left">Sampai Tanggal</td>
	<td align="left">
	<input type="text" name="dateend" id="dateend" size="20" readonly></td>
</tr>
<tr>
	<td align="left">Status Pelunasan</td>
	<td align="left">
	<select option name="completestat">
	<option value="all">Semua</option>
	<option value="0">Blm Lunas</option>
	<option value="1">Lunas</option>
	</select></td>
</tr>


</table>
<div align="left">&nbsp;&nbsp;&nbsp;
<input type="hidden" name="cetak" id="cetak" value="yes">
<input type="submit" value="Cetak" class="button"></div>
</form>
</body>
</html>
';
?>