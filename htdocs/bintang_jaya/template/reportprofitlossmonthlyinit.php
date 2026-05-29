<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Laba / Rugi per Bulan</title>
$headinclude
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxgrid.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_skyblue.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_form.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_keymap_excel.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_combo.js"></script>
<script src="js/operational.js"></script>
</head>

<body>
<form name="reportprofitlossmonthly" action="reportprofitlossmonthly.php" method="post" onsubmit="return confirm(\'Data sudah benar?\')">
<div align="left">Bulan : 
	<select name="monthstart" id="monthstart" onchange="loadpagefromcombo(this,\'reportprofitlossmonthly.php?yearstart=\'+$(\'#yearstart :selected\').val()+\'&monthstart=\')">
		<option value="01"<if criteria="$_REQUEST[monthstart] == \'01\'"> selected</endif>>$arrmonthname[0]</option>
		<option value="02"<if criteria="$_REQUEST[monthstart] == \'02\'"> selected</endif>>$arrmonthname[1]</option>
		<option value="03"<if criteria="$_REQUEST[monthstart] == \'03\'"> selected</endif>>$arrmonthname[2]</option>
		<option value="04"<if criteria="$_REQUEST[monthstart] == \'04\'"> selected</endif>>$arrmonthname[3]</option>
		<option value="05"<if criteria="$_REQUEST[monthstart] == \'05\'"> selected</endif>>$arrmonthname[4]</option>
		<option value="06"<if criteria="$_REQUEST[monthstart] == \'06\'"> selected</endif>>$arrmonthname[5]</option>
		<option value="07"<if criteria="$_REQUEST[monthstart] == \'07\'"> selected</endif>>$arrmonthname[6]</option>
		<option value="08"<if criteria="$_REQUEST[monthstart] == \'08\'"> selected</endif>>$arrmonthname[7]</option>
		<option value="09"<if criteria="$_REQUEST[monthstart] == \'09\'"> selected</endif>>$arrmonthname[8]</option>
		<option value="10"<if criteria="$_REQUEST[monthstart] == \'10\'"> selected</endif>>$arrmonthname[9]</option>
		<option value="11"<if criteria="$_REQUEST[monthstart] == \'11\'"> selected</endif>>$arrmonthname[10]</option>
		<option value="12"<if criteria="$_REQUEST[monthstart] == \'12\'"> selected</endif>>$arrmonthname[11]</option>
	</select>
	<select name="yearstart" id="yearstart" onchange="loadpagefromcombo(this,\'reportprofitlossmonthly.php?monthstart=\'+$(\'#monthstart :selected\').val()+\'&yearstart=\')">
	$cbyear
	</select>
</div><br>
<fieldset>
	<legend>Biaya Operasional</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="adddetailoperational()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
			<td align="left" class="tdhover" onclick="deletedetailoperational()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailoperationalbox" style="width: 100%; height:200px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrid = new dhtmlXGridObject("detailoperationalbox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrid.enableAutoWidth(true);
		mygrid.setHeader("Detail Biaya,Jumlah ( Rp. )");
		mygrid.setInitWidthsP("70,30");
		mygrid.enableAutoWidth(true);
		mygrid.setColAlign("left,right");
		mygrid.setColTypes("ed,ed");
		mygrid.setColSorting("str,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.attachEvent("onEditCell", editingoperational);
		mygrid.submitOnlyChanged(false);
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.loadXML("reportprofitlossmonthly.php?getlist=detailxml&monthstart=$_REQUEST[monthstart]&yearstart=$_REQUEST[yearstart]");
		mygrid.attachEvent("onAfterRowDeleted", function(id,pid){
			countgrandtotal();
		});
	</script>
</fieldset><br>
<div align="right">
	<b>Grand Total : </b>
	<input type="hidden" name="totals" id="totals" value="$headeroperational[totals]">
	<span id="grandtotal" align="right" style="font-weight: bold; font-size: 16px; padding-left: 10px">$ftotal</span>
</div>
<div align="center">
	<input type="hidden" name="detailid" value="$alldetailid">
	<input type="hidden" name="act" value="saving">
	<input type="submit" value="Simpan" class="button">
	<input type="button" value="Reset" class="button" onclick="deleteitem(\'reportprofitlossmonthly.php?act=delete&monthstart=$_REQUEST[monthstart]&yearstart=$_REQUEST[yearstart]\')">
	<input type="button" value="CETAK LAPORAN LABA RUGI" class="button" onclick="window.open(\'reportprofitlossmonthly.php?act=prints&monthstart=$_REQUEST[monthstart]&yearstart=$_REQUEST[yearstart]\',\'_blank\')">
</div>
</form>
</body>
</html>
';
?>