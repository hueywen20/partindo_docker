<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Stok Expired</title>
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_vista.css">
$headinclude
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxcombo.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcombo.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
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
<form name="reportstock" action="reportstock.php?view=expired" method="post" target="_blank">
<table border="0" cellpadding="5" cellspacing="5">
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
</table>
<div align="left">&nbsp;&nbsp;&nbsp;
<input type="submit" value="Cetak" class="button"></div>
</form>
</body>
</html>
';
?>