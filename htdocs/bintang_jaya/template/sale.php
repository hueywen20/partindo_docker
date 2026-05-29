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
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_vista.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxcombo.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_form.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_keymap_excel.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_combo.js"></script>
<script src="js/sale.js"></script>
<script src="js/shortcut.js"></script>
<script type="text/javascript">
	shortcut.add("alt+a", function() {
		adddetailsale();
	});
	
	function checkkeyupdate(event){
		var kn = getKeyEvent(event);
		if (kn != 8 && kn != 37 && kn != 39){
			var getsaledate = $("#saledate").val();
			if (getsaledate.length == 2 || getsaledate.length == 5){
				getsaledate = getsaledate+"-";
				$("#saledate").val(getsaledate);
			}
		}
		checkmaximumdate();
	}
	
	function blurdate(){
		var getsaledate = $("#saledate").val();
		if (getsaledate.length  != 10){
			alert("Tanggal Penjualan tidak lengkap. Tanggal Penjualan akan dibuat hari ini.");
			var datenow = new Date();
			$("#saledate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
		}
		setDueDate(\'saledate\',$(\'#terms\').val(),\'duedate\');
	}
	
	function checkmaximumdate(){
		var getsaledate = $("#saledate").val();
		var showalertdate = false;
		if (getsaledate != ""){
			var getday = getsaledate.substr(0,2);
			var getmonth = parseFloat(getsaledate.substr(3,2)) - 1;
			var getyear = getsaledate.substr(6);
			var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
			var bdate = msdate.getTime();
			var datenow = new Date();
			var dtnow = datenow.getTime();
			
			if (bdate > dtnow){
				alert("Tanggal Penjualan melewati hari ini");
				$("#saledate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
			}
			
			setDueDate(\'saledate\',$(\'#terms\').val(),\'duedate\');
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
			if ($("#customercode").val() == ""){
				alert("Nama Customer belum ada. Silahkan pilih nama customer terlebih dahulu.");
			}
			else if ($("#allow").val() == "0" && $("#terms").val() > 0){
				alert("Customer ini tidak diperbolehkan melakukan transaksi kredit, karena masih ada faktur yang belum dibayar");
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
					alert("Tidak ada data penjualan");
				}
				else if (qtynotfill){
					alert("Ada jumlah barang yang belum diisi");
				}
				else if (unitsnotfill){
					alert("Ada satuan yang belum diisi");
				}
				else{
					var getsaledate = $("#saledate").val();
					var showalertdate = false;
					if (getsaledate != ""){
						var getday = getsaledate.substr(0,2);
						var getmonth = parseFloat(getsaledate.substr(3,2)) - 1;
						var getyear = getsaledate.substr(6);
						var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
						var bdate = msdate.getTime();
						var datenow = new Date();
						var dtnow = datenow.getTime();
						
						if (bdate > dtnow){
							showalertdate = true;
						}
					}
					
					var ids = "";
					if (objfrm.id){
						ids = objfrm.id.value;
					}
					$.ajax({
						type: \'POST\',
						url: \'sale.php\',
						data: \'check=code&no=\'+objfrm.saleno.value+\'&date=\'+objfrm.saledate.value+\'&id=\'+ids,
						success: function(data) {
							if (data){
								alert("Nomor Faktur Penjualan sudah ada dalam sistem. Silahkan masukkan nomor faktur yang lain.");
								objfrm.saleno.focus();
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
	$(document).ready(function() {
		$(".formID").validationEngine();
		cale = new dhtmlxCalendarObject("saledate",true,{
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
		
		$("#saledate").blur(blurdate);
	})
</script>
<if criteria="!empty($headersale[saleno])">
<script type="text/javascript">
	var idnodel = [$allcannotdel];
	var idreturn = [$allidreturn];
</script>
</endif>
</head>

<body>
$errmsg
<if criteria="!empty($_REQUEST[no]) && $useraccess[add_sale]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'sale.php\',\'_self\')"></div></endif>
<form class="formID" name="sale" action="sale.php" method="post" onsubmit="return checkcodeexist(this)">
<fieldset>
	<legend>Informasi Penjualan</legend>
	<table border="0" cellpadding="2" cellspacing="2">
	<if criteria="empty($_REQUEST[no])">
	<tr>
		<td align="left">Nomor Faktur</td>
		<td align="left">
		<input type="text" name="saleno" id="saleno" value="$headersale[saleno]" class="validate[required]"></td>
		<td align="left">Tanggal Faktur</td>
		<td align="left">
		<input type="text" name="saledate" id="saledate" value="$invoicedate" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
	</tr>
	<tr>
		<td align="left">Customer</td>
		<td align="left" colspan="3">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><img src="img/customer.png" border="0" style="cursor: pointer" title="Cari Customer" onclick="window.open(\'customer.php?getlist=determine\',\'customerlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
			<td align="center" width="20"><b>&gt;</b></td>
			<td align="left">
			<input type="hidden" name="customercode" id="customercode" value="$headersale[customercode]">
			<input type="hidden" name="customeraddrid" id="customeraddrid" value="$headersale[customeraddrid]">
			<div id="customerdetail" style="font-weight: bold">
			<if criteria="!empty($_REQUEST[no]) || !empty($errmsg)">
			$headersale[customercode] - $customername - $customercperson - $customeraddr - $customertelp</endif></div></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td align="left">Jenis Transaksi</td>
		<td align="left">
		<select name="trtype" id="trtype">
			<option value="cash"<if criteria="$headersale[trtype] == \'cash\'"> selected</endif>>Tunai</option>
			<option value="credit"<if criteria="$headersale[trtype] == \'credit\'"> selected</endif>>Kredit</option>
		</select></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	<tr>
		<td align="left">Jangka Waktu</td>
		<td align="left">
		<input type="text" name="terms" id="terms" value="$terms" onkeypress="return checknumber(event)" class="validate[required]" onkeyup="settrtype();setDueDate(\'saledate\',this.value,\'duedate\')"> hari - </td>
		<td align="left">Jatuh Tempo</td>
		<td align="left">
		<input type="text" name="duedate" id="duedate" value="$invoiceduedate" readonly>
		<span style="font-weight: bold; color: #FF5555; cursor: pointer" id="messages" onclick="window.open(\'openaccess.php\',\'openaccess\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=500,height=200\');"></span>
		<input type="hidden" id="allow" value="1"></td>
	</tr>
	<else>
	<tr>
		<td align="left">Nomor Penjualan</td>
		<td align="left">
		<input type="text" name="saleid" id="saleid" value="$headersale[saleid]" readonly></td>
		<td align="left">Nomor Faktur</td>
		<td align="left">
		<input type="text" name="saleno" id="saleno" value="$headersale[saleno]" class="validate[required]"></td>
		<td align="left">Tanggal Faktur</td>
		<td align="left">
		<input type="text" name="saledate" id="saledate" value="$invoicedate" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
	</tr>
	<tr>
		<td align="left">Customer</td>
		<td align="left" colspan="5">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><img src="img/customer.png" border="0" style="cursor: pointer" title="Cari Customer" onclick="window.open(\'customer.php?getlist=determine\',\'customerlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
			<td align="center" width="20"><b>&gt;</b></td>
			<td align="left">
			<input type="hidden" name="customercode" id="customercode" value="$headersale[customercode]">
			<input type="hidden" name="customeraddrid" id="customeraddrid" value="$headersale[customeraddrid]">
			<div id="customerdetail" style="font-weight: bold">
			<if criteria="!empty($_REQUEST[no])">
			$headersale[customercode] - $customername - $customercperson - $customeraddr - $customertelp</endif></div></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td align="left">Jenis Transaksi</td>
		<td align="left">
		<select name="trtype" id="trtype">
			<option value="cash"<if criteria="$headersale[trtype] == \'cash\'"> selected</endif>>Tunai</option>
			<option value="credit"<if criteria="$headersale[trtype] == \'credit\'"> selected</endif>>Kredit</option>
		</select></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	<tr>
		<td align="left">Jangka Waktu</td>
		<td align="left">
		<input type="text" name="terms" id="terms" value="$terms" onkeypress="return checknumber(event)" class="validate[required]" onkeyup="settrtype();setDueDate(\'saledate\',this.value,\'duedate\')"> hari - </td>
		<td align="left">Jatuh Tempo</td>
		<td align="left" colspan="3">
		<input type="text" name="duedate" id="duedate" value="$invoiceduedate" readonly>
		<span style="font-weight: bold; color: #FF5555; cursor: pointer" id="messages" onclick="window.open(\'openaccess.php\',\'openaccess\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=500,height=200\');"></span>
		<input type="hidden" id="allow" value="1"></td>
	</tr>
	</if>
	</table>
</fieldset>
<fieldset>
	<legend>Daftar Barang yang dijual</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="adddetailsale()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
			<td align="left" class="tdhover" onclick="deletedetailsale()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailsalebox" style="width: 100%; height:300px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrid = new dhtmlXGridObject("detailsalebox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrid.enableAutoWidth(true);
		mygrid.setColSorting("str,str,str,str,str,str,str,str,str,str,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.attachEvent("onEditCell", editingsale);
		mygrid.submitOnlyChanged(false);
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.loadXML("sale.php?getlist=xml&list=detail&no=$_REQUEST[no]",function(){
			<if criteria="!empty($errmsg)">
			$cachesalebox
			</endif>
		});
		<if criteria="!empty($headersale[saleno])">
		mygrid.attachEvent("onBeforeRowDeleted", function(rId){
			if (s_in_array(rId,idnodel)){
				alert("Maaf, detail penjualan ini tidak bisa dihapus, karena ada transaksi retur penjualan yang telah menggunakan detail penjualan ini.");
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
		<textarea name="description" rows="5" cols="50">$headersale[description]</textarea></td>
	</tr>
	</table></td>
	<td width="50%" align="right" valign="top">
	<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="right" width="30%"><b>Sub Total (Rp.) :</b></td>
		<td align="right" width="20%">
		<input type="hidden" name="totals" id="totals" value="$headersale[totals]">
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
		<td colspan="2"><hr></td>
	</tr>
	<tr>
		<td align="right" width="30%" style="font-size: 16px"><b>Grand Total (Rp.) :</b></td>
		<td align="right" width="20%">
		<input type="hidden" name="totalsale" id="totalsale" value="$headersale[totalsale]">
		<div id="grandtotal" align="right" style="font-weight: bold; font-size: 16px">$ftotal</div></td>
	</tr>
	</table></td>
</tr>
</table>
<div align="center"><br />
	<if criteria="empty($_REQUEST[no])">
	<input type="hidden" name="submits" value="Tambah">
	<input type="submit" value="Tambah" class="button">
	<else>
	<script type="text/javascript">
		$arrsaleprice
		$arrunits
		$arrconversion
		$arrstockqty
		$tempqtycode
		$tempqtyforedit
		$arrunitscur
	</script>
	<input type="hidden" name="id" id="id" value="$headersale[saleid]">
	<input type="hidden" name="detailid" value="$alldetailid">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="50%" align="left">
		<if criteria="$useraccess[edit_sale]">
		<input type="hidden" name="submits" value="Ubah">
		<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;</endif>
		<input type="button" value="Kembali" class="button" onclick="window.open(\'sale.php?screen=list\',\'_self\')"></td>
		<td width="50%" align="right">
		<script type="text/javascript">
			function openprintview(link){
				var V = $("#paper :selected").val();
				window.open(link+"&paper="+V,"_blank");
			}
		</script>
		Kertas : <select id="paper">
			<option value="hvs">HVS</option>
			<option value="contformsmall">Continuous Form Kecil</option>
			<option value="contformbig">Continuous Form Besar</option>
		</select>
		<input type="button" value="Print Tanpa Header" class="button" onclick="openprintview(\'saleinvoice.php?op=no&no=$headersale[saleid]\')">&nbsp;&nbsp;&nbsp;
		<input type="button" value="Print Dengan Header" class="button" onclick="openprintview(\'saleinvoice.php?op=yes&no=$headersale[saleid]\')"></td>
	</tr>
	</table>
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