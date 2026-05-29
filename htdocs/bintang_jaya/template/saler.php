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
<if criteria="!empty($salerid)">
<script type="text/javascript">
	var iddetail = [$alldetailidjs];
</script>
</endif>
<script src="js/saler.js"></script>
<script type="text/javascript">
	function checkkeyupdate(event){
		var kn = getKeyEvent(event);
		if (kn != 8 && kn != 37 && kn != 39){
			var getsalerdate = $("#salerdate").val();
			if (getsalerdate.length == 2 || getsalerdate.length == 5){
				getsalerdate = getsalerdate+"-";
				$("#salerdate").val(getsalerdate);
			}
		}
		checkmaximumdate();
	}
	
	function blurdate(){
		var getsalerdate = $("#salerdate").val();
		if (getsalerdate.length  != 10){
			alert("Tanggal Retur Penjualan tidak lengkap. Tanggal Retur Penjualan akan dibuat hari ini.");
			var datenow = new Date();
			$("#salerdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
		}
	}

	function checkmaximumdate(){
		var getsalerdate = $("#salerdate").val();
		var showalertdate = false;
		if (getsalerdate != ""){
			var getday = getsalerdate.substr(0,2);
			var getmonth = parseFloat(getsalerdate.substr(3,2)) - 1;
			var getyear = getsalerdate.substr(6);
			var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
			var bdate = msdate.getTime();
			var datenow = new Date();
			var dtnow = datenow.getTime();
			
			if (bdate > dtnow){
				alert("Tanggal Retur Penjualan melewati hari ini");
				$("#salerdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
			}

		}
	}
	
	function checkform(objfrm){
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			if ($("#customercode").val() == ""){
				alert("Nama Customer belum ada. Silahkan pilih nama customer terlebih dahulu.");
			}
			else{
				if (mygrid.getRowsNum() == 0){
					alert("Tidak ada data retur penjualan");
				}
				else{
					var getsalerdate = $("#salerdate").val();
					var showalertdate = false;
					if (getsalerdate != ""){
						var getday = getsalerdate.substr(0,2);
						var getmonth = parseFloat(getsalerdate.substr(3,2)) - 1;
						var getyear = getsalerdate.substr(6);
						var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
						var bdate = msdate.getTime();
						var datenow = new Date();
						var dtnow = datenow.getTime();
						
						if (bdate > dtnow){
							showalertdate = true;
						}
					}
					
						return confirm("Data sudah benar ?");
					
				}
			}
		}
		return false;
	}
	$(document).ready(function() {
		$(".formID").validationEngine();
		cale = new dhtmlxCalendarObject("salerdate",true,{
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
		
		$("#salerdate").blur(blurdate);
	});
</script>
</head>

<body>
<if criteria="!empty($_REQUEST[id]) && $useraccess[add_saler]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'saler.php\',\'_self\')"></div></endif>
<form class="formID" id="formID" name="saler" action="saler.php" method="post" onsubmit="return checkform(this)">
<fieldset>
	<legend>Informasi Retur Penjualan</legend>
	<table border="0" cellpadding="2" cellspacing="2">
	<if criteria="!empty($headersaler[salerid])">
	<tr>
		<td align="left">Nomor Retur Penjualan</td>
		<td align="left">
		<input type="text" name="id" id="id" value="$headersaler[salerid]" readonly></td>
		<td align="left">Tanggal Retur Penjualan</td>
		<td align="left">
		<input type="text" name="salerdate" id="salerdate" value="$invoicedate" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
	</tr>
	<else>
	<tr>
		<td align="left">Tanggal Retur Penjualan</td>
		<td align="left">
		<input type="text" name="salerdate" id="salerdate" value="$invoicedate" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	</if>
	<tr>
		<td align="left">Customer</td>
		<td align="left" colspan="3">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><img src="img/customer.png" border="0" style="cursor: pointer" title="Cari Customer" onclick="window.open(\'customer.php?getlist=determine\',\'customerlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
			<td align="center" width="20"><b>&gt;</b></td>
			<td align="left">
			<input type="hidden" name="customercode" id="customercode" value="$headersaler[customercode]">
			<input type="hidden" name="customeraddrid" id="customeraddrid" value="$headersaler[customeraddrid]">
			<div id="customerdetail" style="font-weight: bold">
			<if criteria="!empty($_REQUEST[id])">
			$headersaler[customercode] - $customername - $customercperson - $customeraddr - $customertelp</endif></div></td>
		</tr>
		</table></td>
	</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Daftar Barang Customer</legend>
	<div id="detailstockbox" style="width: 100%; height:230px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrids = new dhtmlXGridObject("detailstockbox");
		mygrids.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrids.enableAutoWidth(true);
		mygrids.setHeader("Tanggal Jual,No Faktur,Kode Barang,No. Part,Nama Barang,Merek,Tipe,Sisa,Satuan,Harga (Rp.),Diskon (%),Diskon Bon (%),PPN (%),Total (Rp.)");
		mygrids.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		mygrids.setInitWidthsP("6,7,10,9,12,8,8,6,6,8,4,4,4,8");
		mygrids.setColAlign("center,left,left,left,left,left,left,right,left,right,right,right,right,right");
		mygrids.setColTypes("dhxCalendar,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
		mygrids.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str");
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
			var total = mygrids.cells(rId,13).getValue();
			arrmaxr[rId] = qty;
			if (mygrid.doesRowExist(rId)){
				alert("Barang ini telah ada dalam daftar barang yang diretur. Silahkan pilih barang yang lain");
			}
			else{
				mygrid.addRow(rId,[fno,stcd,nprt,stnm,brcd,tycd,qty,units,price,disc,extdisc,tax,total]);
				countgrandtotal();
			}
		});
		<if criteria="!empty($_REQUEST[id])">
		mygrids.loadXML("saler.php?getlist=xml&list=getcustomerstuff&code=$headersaler[customercode]");
		</endif>
	</script>
</fieldset>
<fieldset>
	<legend>Daftar Barang yang diretur</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="deletedetailsaler()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailsalerbox" style="width: 100%; height:300px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrid = new dhtmlXGridObject("detailsalerbox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrid.enableAutoWidth(true);
		mygrid.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.attachEvent("onEditCell", editingsaler);
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.submitOnlyChanged(false);
		mygrid.setDateFormat("%d-%m-%Y");
		mygrid.loadXML("saler.php?getlist=xml&list=detail&id=$_REQUEST[id]");
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
		<textarea name="description" rows="5" cols="50">$headersaler[description]</textarea></td>
	</tr>
	</table></td>
	<td width="50%" align="right" valign="top">
	<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="right" width="30%" style="font-size: 16px"><b>Grand Total (Rp.) :</b></td>
		<td align="right" width="20%">
		<input type="hidden" name="totals" id="totals" value="$headersaler[totals]">
		<input type="hidden" name="totalsaler" id="totalsaler" value="$headersaler[totalsaler]">
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
	<input type="hidden" name="id" value="$headersaler[salerid]">
	<input type="hidden" name="detailid" value="$alldetailid">
	<input type="hidden" name="submits" value="Ubah">
	<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;
	<input type="button" value="Kembali" class="button" onclick="window.open(\'saler.php?screen=list\',\'_self\')">
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