<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Aktivitas</title>
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
<form name="reportactivity" action="reportactivity.php" method="post" target="_blank">
<table border="0" cellpadding="5" cellspacing="5">
<tr>
	<td align="left">Nama User</td>
	<td align="left" width="200">
	<div id="userel" style="width:100%; height:25px;"></div>
	<script type="text/javascript">
		var z = new dhtmlXCombo("userel", "userid", "100%");
		z.enableFilteringMode(true, "ajax.php?list=user&type=xml", false);
		z.loadXML("ajax.php?list=user&type=xml");
	</script></td>
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
	<td align="left">Jenis Aktivitas</td>
	<td align="left">
	<select name="activitytype">
		<option value="">Semua</option>
		<option value="create">Tambah Baru</option>
		<option value="edit">Edit</option>
	</select></td>
</tr>
<tr>
	<td align="left">Aktivitas</td>
	<td align="left">
	<select name="activity">
		<option value="all">Semua</option>
		<option value="area">Kota</option>
		<option value="brand">Merek</option>
		<option value="codes">Kode</option>
		<option value="country">Negara</option>
		<option value="customer">Customer</option>
		<option value="adjustin">Penyesuaian Stok ( + )</option>
		<option value="adjustout">Penyesuaian Stok ( - )</option>
		<option value="buy">Pembelian</option>
		<option value="buyr">Retur Pembelian</option>
		<option value="paydebt">Pelunasan Hutang</option>
		<option value="payment">Pelunasan Piutang</option>
		<option value="sale">Penjualan</option>
		<option value="saler">Retur Penjualan</option>
		<option value="location">Lokasi</option>
		<option value="state">Propinsi</option>
		<option value="stock">Stok / Barang</option>
		<option value="stockgroup">Grup Stok</option>
		<option value="supplier">Supplier</option>
		<option value="type">Tipe</option>
		<option value="units">Satuan</option>
		<option value="user">User</option>
		<option value="usergroup">Grup User</option>
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