<?php
$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname]</title>
$headinclude
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxgrid.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxcombo.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_skyblue.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_yahoolike.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_vista.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxcombo.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_form.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_keymap_excel.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_combo.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js"></script>
<if criteria="!empty($aoutid)">
<script type="text/javascript">
	var iddetail = [$alldetailidjs];
	var idusedqty = [$allidusedqty];
</script>
</endif>
<script src="js/adjustout.js"></script>
<script type="text/javascript">
	function checkmaximumdate(){
		var getaoutdate = $("#aoutdate").val();
		var showalertdate = false;
		if (getaoutdate != ""){
			var getday = getaoutdate.substr(0,2);
			var getmonth = parseFloat(getaoutdate.substr(3,2)) - 1;
			var getyear = getaoutdate.substr(6);
			var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
			var bdate = msdate.getTime();
			var datenow = new Date();
			var dtnow = datenow.getTime();
			
			if (bdate > dtnow){
				alert("Tanggal Penyesuaian melewati hari ini");
				$("#aoutdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
			}
		}
	}
	
	function blurdate(){
		var getaoutdate = $("#aoutdate").val();
		if (getaoutdate.length  != 10){
			alert("Tanggal Penyesuaian tidak lengkap. Tanggal Penyesuaian akan dibuat hari ini.");
			var datenow = new Date();
			$("#aoutdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
		}
	}
	
	$(document).ready(function() {
		$(".formID").validationEngine();
		cale = new dhtmlxCalendarObject("aoutdate",true,{
			isWinHeader: true,
		      isWinDrag: true,
			isYearEditable: true,
			isMonthEditable: true
		});
		cale.setDateFormat("%d-%m-%Y");
		cale.setSkin("vista");
		cale.attachEvent("onClick", function(){
			checkmaximumdate();
		});
		
		$("#aoutdate").blur(blurdate);
	})

	function checkform(objfrm){
		var qtynotfill = false;
		var unitsnotfill = false;
		var totalrows = mygrid.getRowsNum();
		if (totalrows > 0){
			for (var b = 0; b < totalrows; b++){
				var stockcodeinbox = mygrid.cells(mygrid.getRowId(b),0).getValue();
				var qtyit = mygrid.cells(mygrid.getRowId(b),5).getValue();
				var unitsit = mygrid.cells(mygrid.getRowId(b),6).getValue();
				if (qtyit == "" && stockcodeinbox != ""){
					qtynotfill = true; break;
				}
				if (unitsit == "" && stockcodeinbox != ""){
					unitsnotfill = true; break;
				}
			}
		}
		if (totalrows == 0){
			alert("Tidak ada data penyesuaian");
		}
		else if (qtynotfill){
			alert("Ada jumlah barang yang belum diisi");
		}
		else if (unitsnotfill){
			alert("Ada satuan yang belum diisi");
		}
		else{
			var getaoutdate = $("#aoutdate").val();
			var showalertdate = false;
			if (getaoutdate != ""){
				var getday = getaoutdate.substr(0,2);
				var getmonth = parseFloat(getaoutdate.substr(3,2)) - 1;
				var getyear = getaoutdate.substr(6);
				var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
				var bdate = msdate.getTime();
				var datenow = new Date();
				var dtnow = datenow.getTime();
				
				if (bdate > dtnow){
					showalertdate = true;
				}
			}
			if (showalertdate){
				alert("Tanggal Penyesuaian melewati hari ini");
			}
			else{
				return confirm("Data sudah benar ?");
			}
		}
		return false;
	}
</script>
</head>

<body>
<if criteria="!empty($_REQUEST[id]) && $useraccess[add_adjustout]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'adjustout.php\',\'_self\')"></div></endif>
<form class="formID" id="formID" name="adjustout" action="adjustout.php" method="post" onsubmit="return checkform(this)">
<fieldset>
	<legend>Informasi Penyesuaian Stok (-)</legend>
	<table border="0" cellpadding="2" cellspacing="2">
	<if criteria="!empty($headeraout[aoutid])">
	<tr>
		<td align="left">Nomor Penyesuaian Stok (-)</td>
		<td align="left">
		<input type="text" name="id" id="id" value="$headeraout[aoutid]" readonly></td>
		<td align="left">Tanggal Penyesuaian Stok (-)</td>
		<td align="left">
		<input type="text" name="aoutdate" id="aoutdate" value="$aoutdate" onkeyup="checkmaximumdate()"></td>
	</tr>
	<else>
	<tr>
		<td align="left">Tanggal Penyesuaian Stok (-)</td>
		<td align="left">
		<input type="text" name="aoutdate" id="aoutdate" value="$aoutdate" onkeyup="checkmaximumdate()"></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	</if>
	</table>
</fieldset>
<fieldset>
	<legend>Daftar Barang yang disesuaikan</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="adddetailaout()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
			<td align="left" class="tdhover" onclick="deletedetailaout()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailaoutbox" style="width: 100%; height:300px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrid = new dhtmlXGridObject("detailaoutbox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrid.enableAutoWidth(true);
		mygrid.setColSorting("str,str,str,str,str,str,str,str,str,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.attachEvent("onEditCell", editingaout);
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.submitOnlyChanged(false);
		mygrid.setDateFormat("%d-%m-%Y");
		mygrid.loadXML("adjustout.php?getlist=xml&list=detail&id=$_REQUEST[id]");
		mygrid.attachEvent("onAfterRowDeleted", function(id,pid){
			countgrandtotal();
		});
	</script>
</fieldset><br>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="50%" align="left" valign="top">
	<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="left" valign="top">Keterangan</td>
		<td align="left" valign="top">
		<textarea name="description" rows="5" cols="50">$headeraout[description]</textarea></td>
	</tr>
	</table></td>
	<td width="50%" align="right" valign="top">
	<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="right" width="30%" style="font-size: 16px"><b>Grand Total (Rp.) :</b></td>
		<td align="right" width="20%">
		<input type="hidden" name="totalaout" id="totalaout" value="$headeraout[totalaout]">
		<div id="grandtotal" align="right" style="font-weight: bold; font-size: 16px">$ftotal</div></td>
	</tr>
	</table></td>
</tr>
</table>
<div align="center">
	<if criteria="empty($_REQUEST[id])">
	<input type="hidden" name="submits" value="Tambah">
	<input type="submit" value="Tambah" class="button">
	<else>
	<script type="text/javascript">
		$arrunits
		$arrconversion
	</script>
	<input type="hidden" name="id" value="$headeraout[aoutid]">
	<input type="hidden" name="detailid" value="$alldetailid">
	<input type="hidden" name="submits" value="Ubah">
	<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;
	<input type="button" value="Kembali" class="button" onclick="window.open(\'adjustout.php?screen=list\',\'_self\')">
	</if>
</div>
</form>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>

</body>

</html>
';
?>