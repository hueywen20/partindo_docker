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
<if criteria="!empty($buyno)">
<script type="text/javascript">
	var idnodel = [$allcannotdel];
	var idusedqty = [$allidusedqty];
</script>
</endif>
<script src="js/purchase.js"></script>
<script src="js/shortcut.js"></script>
<script type="text/javascript">
	shortcut.add("alt+a", function() {
		adddetailpurchase();
	});
	
	function checkkeyupdate(event){
		var kn = getKeyEvent(event);
		if (kn != 8 && kn != 37 && kn != 39){
			var getbuydate = $("#buydate").val();
			if (getbuydate.length == 2 || getbuydate.length == 5){
				getbuydate = getbuydate+"-";
				$("#buydate").val(getbuydate);
			}
		}
		checkmaximumdate();
	}
	
	function blurdate(){
		var getbuydate = $("#buydate").val();
		if (getbuydate.length  != 10){
			alert("Tanggal Pembelian tidak lengkap. Tanggal Pembelian akan dibuat hari ini.");
			var datenow = new Date();
			$("#buydate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
		}
		setDueDate(\'buydate\',$(\'#terms\').val(),\'duedate\');
	}
	
	function checkmaximumdate(){
		var getbuydate = $("#buydate").val();
		var showalertdate = false;
		if (getbuydate != ""){
			var getday = getbuydate.substr(0,2);
			var getmonth = parseFloat(getbuydate.substr(3,2)) - 1;
			var getyear = getbuydate.substr(6);
			var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
			var bdate = msdate.getTime();
			var datenow = new Date();
			var dtnow = datenow.getTime();
			
			if (bdate > dtnow){
				alert("Tanggal Pembelian melewati hari ini");
				$("#buydate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
			}
			
			setDueDate(\'buydate\',$(\'#terms\').val(),\'duedate\');
		}
	}
	
	function settrtype(){
		var maximums = $paymentsetting[maximumcashperiod];
		var iterms = $("#terms").val();
		var inputterms = parseInt(iterms);
		if (inputterms < maximums || iterms == "" || inputterms == 0){
			document.getElementById("trtype").options[0].selected = true;
		}
		else{
			document.getElementById("trtype").options[1].selected = true;
		}
	}
	
	function checkcodeexist(objfrm){
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			if ($("#suppliercode").val() == ""){
				alert("Nama Supplier belum ada. Silahkan pilih nama supplier terlebih dahulu.");
			}
			else{
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
					alert("Tidak ada data pembelian");
				}
				else if (qtynotfill){
					alert("Ada jumlah barang yang belum diisi");
				}
				else if (unitsnotfill){
					alert("Ada satuan yang belum diisi");
				}
				else{
					/* var getbuydate = $("#buydate").val();
					var showalertdate = false;
					if (getbuydate != ""){
						var getday = getbuydate.substr(0,2);
						var getmonth = parseFloat(getbuydate.substr(3,2)) - 1;
						var getyear = getbuydate.substr(6);
						var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
						var bdate = msdate.getTime();
						var datenow = new Date();
						var dtnow = datenow.getTime();
						
						if (bdate > dtnow){
							showalertdate = true;
						}
					} */
					
					var ids = "";
					if (objfrm.id){
						ids = objfrm.id.value;
					}
					$.ajax({
						type: \'POST\',
						url: \'purchase.php\',
						data: \'check=code&no=\'+objfrm.orderno.value+\'&id=\'+ids,
						success: function(data) {
							if (data){
								alert("Nomor Bon sudah ada dalam sistem. Silahkan masukkan nomor bon yang lain.");
								objfrm.orderno.focus();
							}
							else{
								
									if (confirm("Data sudah benar?")){
										objfrm.submit();
									}
								
							}
						}
					});
				}
			}
		}
		return false;
	}
	
	var u;
	var gridrows;
	var doing;
	var arrcols = [7,8,9];
	function converting(){
		if (u == gridrows){
			u = 0;
		}
		else{
			var getrows = mygrid.getRowId(u);
			if (getrows.indexOf("_") == -1){
				if (arrcols.length > 0){
					for (var k = 0; k < arrcols.length; k++){
						var curvalue = mygrid.cells(getrows,arrcols[k]).getValue();
						if (doing == "deconvert"){
							mygrid.cells(getrows,arrcols[k]).setValue(formatnumber(deconvertcodes(curvalue)));
						}
						else{
							curvalue = replacestr(replacestr(curvalue,".",""),",",".");
							curvalue = curvalue.replace(".00","");
							mygrid.cells(getrows,arrcols[k]).setValue(convertcodes(curvalue));
						}
					}
				}
			}
			u++;
			setTimeout("converting()",1);
		}
	}
	
	/*shortcut.add("alt+r", function() {
		var views = $("#defnumb");
		if (views.val() == "code"){
			views.val("number");
			doing = "deconvert";
		}
		else{
			views.val("code");
			doing = "convert";
		}
		
		var sbtotal = $("#subtotal");
		var sbdisc = $("#disc");
		var sbtax = $("#tax");
		var sbgt = $("#grandtotal");
		if (doing == "deconvert"){
			if (sbtotal.html() != ""){
				sbtotal.html(formatnumber(deconvertcodes(sbtotal.html())));
			}
			if (sbdisc.val() != ""){
				sbdisc.val(formatnumber(deconvertcodes(sbdisc.val())));
			}
			if (sbtax.val() != ""){
				sbtax.val(formatnumber(deconvertcodes(sbtax.val())));
			}
			if (sbgt.html() != ""){
				sbgt.html(formatnumber(deconvertcodes(sbgt.html())));
			}
		}
		else{
			if (sbtotal.html() != ""){
				var curvalue1 = replacestr(replacestr(sbtotal.html(),".",""),",",".");
				curvalue1 = curvalue1.replace(".00","");
				sbtotal.html(convertcodes(curvalue1));
			}
			if (sbdisc.val() != ""){
				var curvalue1 = replacestr(replacestr(sbdisc.val(),".",""),",",".");
				curvalue1 = curvalue1.replace(".00","");
				sbdisc.val(convertcodes(curvalue1));
			}
			if (sbtax.val() != ""){
				var curvalue1 = replacestr(replacestr(sbtax.val(),".",""),",",".");
				curvalue1 = curvalue1.replace(".00","");
				sbtax.val(convertcodes(curvalue1));
			}
			if (sbgt.html() != ""){
				var curvalue1 = replacestr(replacestr(sbgt.html(),".",""),",",".");
				curvalue1 = curvalue1.replace(".00","");
				sbgt.html(convertcodes(curvalue1));
			}
		}
		
		u = 0;
		gridrows = mygrid.getRowsNum();
		if (gridrows > 0){
			converting();
		}
	});*/
	
	$(document).ready(function() {
		$(".formID").validationEngine();
		cale = new dhtmlxCalendarObject("buydate",true,{
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
		
		$("#buydate").blur(blurdate);
	})
</script>
</head>

<body>
<if criteria="!empty($_REQUEST[no]) && $useraccess[add_purchase]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'purchase.php\',\'_self\')"></div></endif>
<form class="formID" id="formID" name="purchase" action="purchase.php" method="post" onsubmit="return checkcodeexist(this)">
<fieldset>
	<legend>Informasi Pembelian</legend>
	<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td align="left">Nomor Bon</td>
		<td align="left">
		<input type="text" name="orderno" id="orderno" value="$headerbuy[orderno]" class="validate[required]"></td>
		<td align="left">Tanggal Bon</td>
		<td align="left">
		<input type="text" name="buydate" id="buydate" value="$invoicedate" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
	</tr>
	<if criteria="!empty($headerbuy[buyno])">
	<tr>
		<td align="left">Nomor Faktur</td>
		<td align="left">
		<input type="text" name="buyno" id="buyno" value="$headerbuy[buyno]" readonly></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	</endif>
	<tr>
		<td align="left">Supplier</td>
		<td align="left" colspan="3">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><img src="img/supplier.png" border="0" style="cursor: pointer" title="Cari Supplier" onclick="window.open(\'supplier.php?getlist=determine\',\'supplierlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
			<td align="center" width="20"><b>&gt;</b></td>
			<td align="left">
			<input type="hidden" name="suppliercode" id="suppliercode" value="$headerbuy[suppliercode]">
			<input type="hidden" name="supplieraddrid" id="supplieraddrid" value="$headerbuy[supplieraddrid]">
			<div id="supplierdetail" style="font-weight: bold">
			<if criteria="!empty($_REQUEST[no])">
			$headerbuy[suppliercode] - $suppliername - $suppliercperson - $supplieraddr - $suppliertelp</endif></div></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td align="left">Jenis Transaksi</td>
		<td align="left">
		<select name="trtype" id="trtype">
			<option value="cash"<if criteria="$headerbuy[trtype] == \'cash\'"> selected</endif>>Tunai</option>
			<option value="credit"<if criteria="$headerbuy[trtype] == \'credit\'"> selected</endif>>Kredit</option>
		</select></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	<tr>
		<td align="left">Jangka Waktu</td>
		<td align="left">
		<input type="text" name="terms" id="terms" value="$terms" onkeypress="return checknumber(event)" class="validate[required]" onkeyup="settrtype();setDueDate(\'buydate\',this.value,\'duedate\')"> hari - </td>
		<td align="left">Jatuh Tempo</td>
		<td align="left">
		<input type="text" name="duedate" id="duedate" value="$invoiceduedate" readonly></td>
	</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Daftar Barang yang dibeli</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="adddetailpurchase()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
			<td align="left" class="tdhover" onclick="deletedetailpurchase()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailpchbox" style="width: 100%; height:300px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrid = new dhtmlXGridObject("detailpchbox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrid.enableAutoWidth(true);
		mygrid.setColSorting("str,str,str,str,str,str,str,str,str,str,date,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.attachEvent("onEditCell", editingpurchase);
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.submitOnlyChanged(false);
		mygrid.setDateFormat("%d-%m-%Y");
		mygrid.loadXML("purchase.php?getlist=xml&list=detail&no=$_REQUEST[no]");
		<if criteria="!empty($headerbuy[buyno])">
		mygrid.attachEvent("onBeforeRowDeleted", function(rId){
			if (s_in_array(rId,idnodel)){
				alert("Maaf, detail pembelian ini tidak bisa dihapus, karena ada transaksi yang telah menggunakan detail pembelian ini.");
				return false;
			}
		});
		</endif>
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
		<textarea name="description" rows="5" cols="50">$headerbuy[description]</textarea></td>
	</tr>
	</table></td>
	<td width="50%" align="right" valign="top">
	<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="right" width="30%"><b>Sub Total (Rp.) :</b></td>
		<td align="right" width="20%">
		<input type="hidden" name="totals" id="totals" value="$headerbuy[totals]">
		<div id="subtotal" align="right" style="font-weight: bold">$ftotals</div></td>
	</tr>
	<tr>
		<td align="right" width="30%"><b>Diskon (%) :</b></td>
		<td align="right" width="20%">
		<input type="text" name="disc" id="disc" value="$fdisc" style="text-align: right" onkeypress="return checknumber(event)" onkeyup="countgrandtotal(this)"></td>
	</tr>
	<tr>
		<td align="right" width="30%"><b>PPN (%) :</b></td>
		<td align="right" width="20%">
		<input type="text" name="tax" id="tax" value="$ftax" style="text-align: right" onkeypress="return checknumber(event)" onkeyup="countgrandtotal(this)"></td>
	</tr>
	<tr>
		<td align="right" width="30%"><b>Biaya Lain-Lain :</b></td>
		<td align="right" width="20%">
		<input type="text" name="otherpays" id="otherpays" value="$fotherpays" style="text-align: right" onkeypress="return checknumber(event)" onkeyup="countgrandtotal(this)"></td>
	</tr>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td align="right" width="30%" style="font-size: 16px"><b>Grand Total (Rp.) :</b></td>
		<td align="right" width="20%">
		<input type="hidden" name="totalbuy" id="totalbuy" value="$headerbuy[totalbuy]">
		<div id="grandtotal" align="right" style="font-weight: bold; font-size: 16px">$ftotal</div></td>
	</tr>
	</table></td>
</tr>
</table>
<div align="center">
	<if criteria="empty($_REQUEST[no])">
	<input type="hidden" name="submits" value="Tambah">
	<input type="submit" value="Tambah" class="button">
	<else>
	<script type="text/javascript">
		$arrunits
		$arrconversion
	</script>
	<input type="hidden" name="id" value="$headerbuy[buyid]">
	<input type="hidden" name="detailid" value="$alldetailid">
	<if criteria="$useraccess[edit_purchase]">
	<input type="hidden" name="submits" value="Ubah">
	<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;</endif>
	<input type="button" value="Kembali" class="button" onclick="window.open(\'purchase.php?screen=list\',\'_self\')">
	</if>
</div>
</form>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>
<input type="hidden" id="defnumb" name="defnumb" value="$defnumb">
</body>

</html>
';
?>