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
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_form.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_keymap_excel.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_combo.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js"></script>
<if criteria="!empty($buyrid)">
<script type="text/javascript">
	var iddetail = [$alldetailidjs];
</script>
</endif>
<script src="js/purchaser.js"></script>
<script type="text/javascript">	
	function checkkeyupdate(event){
		var kn = getKeyEvent(event);
		if (kn != 8 && kn != 37 && kn != 39){
			var getbuyrdate = $("#buyrdate").val();
			if (getbuyrdate.length == 2 || getbuyrdate.length == 5){
				getbuyrdate = getbuyrdate+"-";
				$("#buyrdate").val(getbuyrdate);
			}
		}
		checkmaximumdate();
	}
	
	function blurdate(){
		var getbuyrdate = $("#buyrdate").val();
		if (getbuyrdate.length  != 10){
			alert("Tanggal Retur Pembelian tidak lengkap. Tanggal Retur Pembelian akan dibuat hari ini.");
			var datenow = new Date();
			$("#buyrdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
		}
	}

	function checkmaximumdate(){
		var getbuyrdate = $("#buyrdate").val();
		var showalertdate = false;
		if (getbuyrdate != ""){
			var getday = getbuyrdate.substr(0,2);
			var getmonth = parseFloat(getbuyrdate.substr(3,2)) - 1;
			var getyear = getbuyrdate.substr(6);
			var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
			var bdate = msdate.getTime();
			var datenow = new Date();
			var dtnow = datenow.getTime();
			
			if (bdate > dtnow){
				alert("Tanggal Retur Pembelian melewati hari ini");
				$("#buyrdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
			}
		}
	}
	
	function checkform(objfrm){
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			if ($("#suppliercode").val() == ""){
				alert("Nama Supplier belum ada. Silahkan pilih nama supplier terlebih dahulu.");
			}
			else{
				if (mygrid.getRowsNum() == 0){
					alert("Tidak ada data retur pembelian");
				}
				else{
					var getbuyrdate = $("#buyrdate").val();
					var showalertdate = false;
					if (getbuyrdate != ""){
						var getday = getbuyrdate.substr(0,2);
						var getmonth = parseFloat(getbuyrdate.substr(3,2)) - 1;
						var getyear = getbuyrdate.substr(6);
						var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
						var bdate = msdate.getTime();
						var datenow = new Date();
						var dtnow = datenow.getTime();
						
						
						return confirm("Data sudah benar ?");
					}
				}
			}
		}
		return false;
	}
	$(document).ready(function() {
		$(".formID").validationEngine();
		cale = new dhtmlxCalendarObject("buyrdate",true,{
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
		
		$("#buyrdate").blur(blurdate);
	});
</script>
</head>

<body>
<if criteria="!empty($_REQUEST[id]) && $useraccess[add_purchaser]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'purchaser.php\',\'_self\')"></div></endif>
<form class="formID" id="formID" name="purchaser" action="purchaser.php" method="post" onsubmit="return checkform(this)">
<fieldset>
	<legend>Informasi Retur Pembelian</legend>
	<table border="0" cellpadding="2" cellspacing="2">
	<if criteria="!empty($headerbuyr[buyrid])">
	<tr>
		<td align="left">Nomor Retur Pembelian</td>
		<td align="left">
		<input type="text" name="id" id="id" value="$headerbuyr[buyrid]" readonly></td>
		<td align="left">Tanggal Retur Pembelian</td>
		<td align="left">
		<input type="text" name="buyrdate" id="buyrdate" value="$invoicedate" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
	</tr>
	<else>
	<tr>
		<td align="left">Tanggal Retur Pembelian</td>
		<td align="left">
		<input type="text" name="buyrdate" id="buyrdate" value="$invoicedate" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	</if>
	<tr>
		<td align="left">Supplier</td>
		<td align="left" colspan="3">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><img src="img/supplier.png" border="0" style="cursor: pointer" title="Cari Supplier" onclick="window.open(\'supplier.php?getlist=determine\',\'supplierlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
			<td align="center" width="20"><b>&gt;</b></td>
			<td align="left">
			<input type="hidden" name="suppliercode" id="suppliercode" value="$headerbuyr[suppliercode]">
			<input type="hidden" name="supplieraddrid" id="supplieraddrid" value="$headerbuyr[supplieraddrid]">
			<div id="supplierdetail" style="font-weight: bold">
			<if criteria="!empty($_REQUEST[id])">
			$headerbuyr[suppliercode] - $suppliername - $suppliercperson - $supplieraddr - $suppliertelp</endif></div></td>
		</tr>
		</table></td>
	</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Daftar Barang Supplier</legend>
	<div id="detailstockbox" style="height:230px; background-color:white; overflow: auto"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		var widthauto = document.documentElement.clientWidth-60;
		$("#detailstockbox").css("width",widthauto+"px");
		mygrids = new dhtmlXGridObject("detailstockbox");
		mygrids.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		/* mygrids.enableAutoWidth(true); */
		mygrids.setHeader("Tanggal Beli,No Bon,Kode Barang,No. Part,Nama Barang,Merek,Tipe,Sisa,Satuan,Harga (Rp.),Diskon (%),Diskon Bon (%),PPN (%),Biaya Lain-lain,Total (Rp.),Exp Date");
		mygrids.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		mygrids.setInitWidthsP("6,7,9,9,10,8,8,5,5,7,4,4,4,8,8,6");
		mygrids.setColAlign("center,left,left,left,left,left,left,right,left,right,right,right,right,right,right,center");
		mygrids.setColTypes("dhxCalendar,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,dhxCalendar");
		mygrids.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		mygrids.setSkin("dhx_skyblue");
		mygrids.init();
		mygrids.enableSmartRendering(true);
		mygrids.enableRowsHover(true,"hover");
		mygrids.setDateFormat("%d-%m-%Y");
		mygrids.attachEvent("onRowDblClicked", function(rId,cInd){
			var fno = mygrids.cells(rId,1).getValue();
			var stcd = mygrids.cells(rId,2).getValue();
			var nprt = mygrids.cells(rId,3).getValue();
			var stnm = mygrids.cells(rId,4).getValue();
			var brcd = mygrids.cells(rId,5).getValue();
			var tycd = mygrids.cells(rId,6).getValue();
			var qty = mygrids.cells(rId,7).getValue();
			var units = mygrids.cells(rId,8).getValue();
			var price = mygrids.cells(rId,9).getValue();
			var disc = mygrids.cells(rId,10).getValue();
			var extdisc = mygrids.cells(rId,11).getValue();
			var tax = mygrids.cells(rId,12).getValue();
			var otherpays = mygrids.cells(rId,13).getValue();
			var total = mygrids.cells(rId,14).getValue();
			arrmaxr[rId] = qty;
			if (mygrid.doesRowExist(rId)){
				alert("Barang ini telah ada dalam daftar barang yang diretur. Silahkan pilih barang yang lain");
			}
			else{
				mygrid.addRow(rId,[fno,stcd,nprt,stnm,brcd,tycd,qty,units,price,disc,extdisc,tax,otherpays,total]);
				countgrandtotal();
			}
		});
		<if criteria="!empty($_REQUEST[id])">
		mygrids.loadXML("purchaser.php?getlist=xml&list=getsupplierstuff&code=$headerbuyr[suppliercode]");
		</endif>
	</script>
</fieldset>
<fieldset>
	<legend>Daftar Barang yang diretur</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="deletedetailpurchaser()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailpchrbox" style="height:230px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrid = new dhtmlXGridObject("detailpchrbox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		/* mygrid.enableAutoWidth(true); */
		mygrid.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.attachEvent("onEditCell", editingpurchaser);
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.submitOnlyChanged(false);
		mygrid.setDateFormat("%d-%m-%Y");
		mygrid.loadXML("purchaser.php?getlist=xml&list=detail&id=$_REQUEST[id]");
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
		<textarea name="description" rows="5" cols="50">$headerbuyr[description]</textarea></td>
	</tr>
	</table></td>
	<td width="50%" align="right" valign="top">
	<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="right" width="30%" style="font-size: 16px"><b>Grand Total (Rp.) :</b></td>
		<td align="right" width="20%">
		<input type="hidden" name="totals" id="totals" value="$headerbuyr[totals]">
		<input type="hidden" name="totalbuyr" id="totalbuyr" value="$headerbuyr[totalbuyr]">
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
		$arrmaxqty
	</script>
	<input type="hidden" name="id" value="$headerbuyr[buyrid]">
	<input type="hidden" name="detailid" value="$alldetailid">
	<input type="hidden" name="submits" value="Ubah">
	<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;
	<input type="button" value="Kembali" class="button" onclick="window.open(\'purchaser.php?screen=list\',\'_self\')">
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