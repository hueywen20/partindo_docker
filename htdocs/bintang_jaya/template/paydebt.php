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
<script src="js/paydebt.js"></script>
<script src="js/shortcut.js"></script>
<script type="text/javascript">
	shortcut.add("alt+a", function() {
		adddetailpaydebt();
	});
	function checkcodeexist(objfrm){
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			if ($("#suppliercode").val() == ""){
				alert("Nama Supplier belum ada. Silahkan pilih nama Supplier terlebih dahulu.");
			}
			else{
				if (mygrid.getRowsNum() == 0){
					alert("Tidak ada data pembayaran");
				}
				else{
					if (checktotalpayment()){
						if (confirm("Data sudah benar?")){
							return true;
						}
					}
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
	
	function checkkeyupdate(event){
		var kn = getKeyEvent(event);
		if (kn != 8 && kn != 37 && kn != 39){
			var getpaymentdate = $("#paymentdate").val();
			if (getpaymentdate.length == 2 || getpaymentdate.length == 5){
				getpaymentdate = getpaymentdate+"-";
				$("#paymentdate").val(getpaymentdate);
			}
		}
		checkmaximumdate();
	}
	
	function blurdate(){
		var getpaymentdate = $("#paymentdate").val();
		if (getpaymentdate.length  != 10){
			alert("Tanggal Pembayaran tidak lengkap. Tanggal Pembayaran akan dibuat hari ini.");
			var datenow = new Date();
			$("#paymentdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
		}
		setDueDate(\'paymentdate\',$(\'#terms\').val(),\'duedate\');
	}

	function checkmaximumdate(){
		var getpaymentdate = $("#paymentdate").val();
		var showalertdate = false;
		if (getpaymentdate != ""){
			var getday = getpaymentdate.substr(0,2);
			var getmonth = parseFloat(getpaymentdate.substr(3,2)) - 1;
			var getyear = getpaymentdate.substr(6);
			var msdate = new Date(getyear,getmonth,getday,0,0,0,0);
			var bdate = msdate.getTime();
			var datenow = new Date();
			var dtnow = datenow.getTime();
			
			if (bdate > dtnow){
				alert("Tanggal Pembayaran melewati hari ini");
				$("#paymentdate").val(setLengthDate(datenow.getDate(), 2) + dateSplitter + setLengthDate(datenow.getMonth() + 1, 2) + dateSplitter + setLengthDate(datenow.getFullYear(), 4));
			}
			
			setDueDate(\'paymentdate\',$(\'#terms\').val(),\'duedate\');
		}
	}
	
	$(document).ready(function() {
		$(".formID").validationEngine();
		cale = new dhtmlxCalendarObject("paymentdate",true,{
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
		
		cale1 = new dhtmlxCalendarObject("startdate",true,{
			isWinHeader: true,
		      isWinDrag: true,
			isYearEditable: true,
			isMonthEditable: true
		});
		cale1.setDateFormat("%d-%m-%Y");
		cale1.setSkin("vista");
		
		cale2 = new dhtmlxCalendarObject("enddate",true,{
			isWinHeader: true,
		      isWinDrag: true,
			isYearEditable: true,
			isMonthEditable: true
		});
		cale2.setDateFormat("%d-%m-%Y");
		cale2.setSkin("vista");
		
		cale3 = new dhtmlxCalendarObject("completedate",true,{
			isWinHeader: true,
		      isWinDrag: true,
			isYearEditable: true,
			isMonthEditable: true
		});
		cale3.setDateFormat("%d-%m-%Y");
		cale3.setSkin("vista");
		
		
	})
	
	function checkval(objfrm){
	var grandtotalsthis = parseFloat($("#grandtotals").val());
	var totalpaythis = parseFloat($("#totalpay").val());
	var completeopt = ($("select#complete").val());
	
	var statusflat = $("#statusflat").val();
	var flatvalue = replacestr(replacestr($("#flat").val(),".",""),",",".");
	if (statusflat == "+"){
		totalpaythis = parseFloat(parseFloat(totalpaythis) + parseFloat(flatvalue));
	}
	
	if (totalpaythis < grandtotalsthis){
	alert ("Nilai Pembayaran Yang Anda Masukkan Kurang Dari Nilai Hutang");
	}
	else{
	if (confirm("Data sudah benar?")){
	//objfrm.submit();
	return true;
	}
	}
	return false;
	}
	
</script>
</head>

<body>
<if criteria="!empty($_REQUEST[no]) && $useraccess[add_paydebt]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'paydebt.php\',\'_self\')"></div></endif>
<form class="formID" id="formID" name="paydebt" action="paydebt.php" method="post" onsubmit="return checkval(this)">
<fieldset>
	<legend>Informasi Pembayaran</legend>
	<table border="0" cellpadding="2" cellspacing="2">
	<if criteria="empty($headerpayment[hpid])">
	<tr>
		<td align="left">Tanggal Pembayaran</td>
		<td align="left">
		<input type="text" name="paymentdate" id="paymentdate" value="$invoicedate" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
		<td align="left"></td>
		<td align="left"></td>
	</tr>
	<else>
	<tr>
		<td align="left">Nomor Pembayaran</td>
		<td align="left">
		<input type="text" name="hpid" id="hpid" value="$headerpayment[hpid]" readonly></td>
		<td align="left">Tanggal Pembayaran</td>
		<td align="left">
		<input type="text" name="paymentdate" id="paymentdate" value="$invoicedate" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="checkkeyupdate(event)"></td>
	</tr>
	</if>
	<if criteria="!empty($customername)">
	<tr>
		<td align="left">Customer</td>
		<td align="left" colspan="3">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><img src="img/customer.png" border="0" style="cursor: pointer" title="Cari customer" onclick="window.open(\'customer.php?getlist=determine\',\'customerlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
			<td align="center" width="20"><b>&gt;</b></td>
			<td align="left">
			<input type="hidden" name="customercode" id="customercode" value="$getcustomer[customercode]">
			<input type="hidden" name="customeraddrid" id="customeraddrid" value="$headerpayment[customeraddrid]">
			<div id="customerdetail" style="font-weight: bold">
			<if criteria="!empty($_REQUEST[no])">
			$getcustomer[customercode] - $customername - $customercperson - $customeraddr - $customertelp</endif></div></td>
		</tr>
		</table></td>
	</tr>
	<else>
	<tr>
		<td align="left">Supplier</td>
		<td align="left" colspan="3">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left"><img src="img/supplier.png" border="0" style="cursor: pointer" title="Cari supplier" onclick="window.open(\'supplier.php?getlist=determine\',\'supplierlist\',\'statusbar=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width=800,height=310\');"></td>
			<td align="center" width="20"><b>&gt;</b></td>
			<td align="left">
			<input type="hidden" name="suppliercode" id="suppliercode" value="$getsupplier[suppliercode]">
			<input type="hidden" name="supplieraddrid" id="supplieraddrid" value="$headerpayment[supplieraddrid]">
			<div id="supplierdetail" style="font-weight: bold">
			<if criteria="!empty($_REQUEST[no])">
			$getsupplier[suppliercode] - $suppliername - $suppliercperson - $supplieraddr - $suppliertelp</endif></div></td>
		</tr>
		</table></td>
	</tr>
	</if>
	<tr>
		<td align="left">Periode</td>
		<td align="left">
		<input type="text" name="startdate" id="startdate" value="$invstartdate"></td>
		<td align="center">-</td>
		<td align="left">
		<input type="text" name="enddate" id="enddate" value="$invenddate">
		<img onclick="searchbuylist()" src="img/icon_view.png" border="0" style="cursor: pointer">
		<span style="visibility: hidden" id="loadingsearchinvoice"><img src="img/loading.gif" border="0" width="16" height="16"></span></td>
	</tr>
	<tr>
		<td align="left" valign="top">Status</td>
		<td align="left" valign="top">
		<script type="text/javascript">
			function togglecomplete(vals){
				if (vals == "0"){
					$("#paydatetext").css("display","none");
				}
				else if (vals == "1"){
					$("#paydatetext").css("display","block");
				}
			}
		</script>
		<select name="complete" id="complete" onchange="togglecomplete(this.value)">
			<option value="0">Belum Lunas</option>
			<option value="1"<if criteria="$headerpayment[complete] == 1"> selected</endif>>Lunas</option>
		</select></td>
		<td align="left" colspan="2">
		<div id="paydatetext" style="display: <if criteria="$headerpayment[complete] == 1">block<else>none</if>">
		Tanggal Lunas 
		<input type="text" name="completedate" id="completedate" value="$fcompletedate"></div></td>
	</tr>
	</table>
</fieldset>
<fieldset>
	<legend>Daftar Bon Pembelian</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="deletedetailpaydebt()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailpaybox" style="width: 99%; height:300px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygrid = new dhtmlXGridObject("detailpaybox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrid.enableAutoWidth(true);
		mygrid.setColSorting("str,str,int,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.submitOnlyChanged(false);
		mygrid.setNumberFormat("0,000.00",4,",",".");
		mygrid.loadXML("paydebt.php?getlist=xml&list=detail&no=$_REQUEST[no]");
		/* var indexdels;
		mygrid.attachEvent("onBeforeRowDeleted", function(id,pid){
			if (id.indexOf("buy_") != -1){
				indexdels = mygrid.getRowIndex(id);
			}
		}); */
		mygrid.attachEvent("onAfterRowDeleted", function(id,pid){
			/* if (id.indexOf("buy_") != -1){
				var idnow;
				while (true){
					idnow = mygrid.getRowId(indexdels);
					if (typeof(idnow) == "undefined"){
						break;
					}
					if (idnow.indexOf("buy_") != -1){
						break;
					}
					mygrid.deleteRow(idnow);
				}
			} */
			countgrandtotal();
		});
	</script>
</fieldset>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="50%" align="left" valign="top"></td>
	<td width="50%" align="right" valign="top">
	<table border="0" width="100%" cellpadding="3" cellspacing="0">
	<tr>
		<td align="right" width="70%" style="font-size: 16px"><b>Sub Total (Rp.) :</b></td>
		<td align="right" width="30%">
		<input type="hidden" name="saveto" id="saveto" value="">
		<input type="hidden" name="totalpayment" id="totalpayment" value="$headerpayment[totalpayment]">
		<div id="totalpaymenttext" align="right" style="font-weight: bold; font-size: 16px">$ftotal</div></td>
	</tr>
	</table></td>
</tr>
</table>
<fieldset>
	<legend>Informasi Pelunasan</legend>
	<div align="left">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left" class="tdhover" onclick="addrepaydebt()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
			<td align="left" class="tdhover" onclick="deleterepaydebt()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
		</tr>
		</table>
	</div>
	<div id="detailrepaybox" style="width: 99%; height:200px; background-color:white"></div>
	<script type="text/javascript" src="js/gridf.js"></script>
	<script>
		mygridrp = new dhtmlXGridObject("detailrepaybox");
		mygridrp.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygridrp.enableAutoWidth(true);
		mygridrp.setColSorting("str,str,str,str,date,date,int,str,str");
		mygridrp.setSkin("dhx_skyblue");
		mygridrp.init();
		mygridrp.enableSmartRendering(true);
		mygridrp.enableRowsHover(true,"hover");
		mygridrp.submitOnlyChanged(false);
		mygridrp.setDateFormat("%d-%m-%Y");
		mygridrp.attachEvent("onEditCell", editingrepay);
		mygridrp.attachEvent("onAfterRowDeleted", fillremaining);
		mygridrp.loadXML("paydebt.php?getlist=xml&list=detailrepay&no=$_REQUEST[no]");
	</script>
</fieldset><br>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="50%" align="left" valign="top">
	<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="left" valign="top">Keterangan</td>
		<td align="left" valign="top">
		<textarea name="description" rows="5" cols="50">$headerpayment[description]</textarea></td>
	</tr>
	</table></td>
	<td width="50%" align="right" valign="top">
	<table border="0" width="100%" cellpadding="3" cellspacing="0">
	<if criteria="$statususer == 1">
	<tr>
		<td align="right" width="70%" style="font-size: 16px"><b>Grand Total (Rp.) :</b></td>
		<td align="right" width="30%">
		<input type="hidden" name="grandtotals" id="grandtotals" value="$headerpayment[grandtotals]">
		<div id="grandtotalstext" align="right" style="font-weight: bold; font-size: 16px">$fgrandtotals</div></td>
	</tr>
	<else>
	


	
	<tr >
	
	
		<td align="right" width="70%"><b>Kelebihan Bayar Piutang Bulan Lalu (Rp.) :</b></td>
		<td align="right" width="30%">
		
		<input type="text" name="remainingprevious" id="remainingprevious" value="$fremainingprevious" style="text-align: right" onkeypress="return checknumber(event)" onkeyup="insertingfs(this);countgrandtotal()"> </td>
	</tr>
	

	<tr >
	

		<td align="right" width="70%"><b>Kelebihan Bayar Hutang Bulan Lalu (Rp.) :</b></td>
		<td align="right" width="30%">
		<input type="text" name="remainingprevioush" id="remainingprevioush" value="$fremainingprevioush" style="text-align: right" onkeypress="return checknumber(event)" onkeyup="insertingfs(this);countgrandtotal()"> </td>
	</tr>
	
	
	<tr>
		<td align="right" width="100%" colspan="2"><hr></td>
	</tr>
	<tr>
		<td align="right" width="70%" style="font-size: 16px"><b>Grand Total (Rp.) :</b></td>
		<td align="right" width="30%">
		<input type="hidden" name="grandtotals" id="grandtotals" value="$headerpayment[grandtotals]">
		<div id="grandtotalstext" align="right" style="font-weight: bold; font-size: 16px">$fgrandtotals</div></td>
	</tr>
	
	<tr >

		<td align="right" width="70%">
		<script type="text/javascript">
			function toggleflatstatus(obj){
				if (obj.value == "+"){
					obj.value = "-";
					$("#statusflat").val("-");
				}
				else{
					obj.value = "+";
					$("#statusflat").val("+");
				}
			}
		</script>
		<input type="hidden" name="statusflat" id="statusflat" value="$headerpayment[statusflat]">
		<input type="button" class="button" value="$headerpayment[statusflat]" onclick="toggleflatstatus(this);fillremaining()">
		<b>Penyesuaian (Rp.) :</b></td>
		<td align="right" width="30%">
		<input type="text" name="flat" id="flat" style="text-align: right" onkeypress="return checknumber(event)" onkeyup="insertingfs(this);fillremaining()" value="$hflat">
		</td>
	</tr>
	
	<tr>
		<td align="right" width="70%"><b>Kelebihan Bayar Hutang Bulan Ini (Rp.) :</b></td>
		<td align="right" width="30%">
		<input type="hidden" name="remainingnowh" id="remainingnowh" value="$headerpayment[remainingnowh]">
		<span id="remainingnowtext" align="right" style="font-weight: bold; font-size: 16px">$fremainingnow</span></td>
	</tr>
	</if>
	</table></td>
</tr>
</table>
<div align="center">
	<input type="hidden" name="detailadded" id="detailadded" value="">
	<input type="hidden" name="statuspayment" id="statuspayment" value="$headerpayment[complete]">
	<if criteria="empty($_REQUEST[no])">
	<input type="hidden" name="submits" value="Tambah">
	<input type="submit" value="Tambah" class="button">
	<else>
	<input type="hidden" name="id" value="$headerpayment[hpid]">
	<input type="hidden" name="detailid" id="detailid" value="$alldetailid">
	<input type="hidden" name="detailrpid" id="detailrpid" value="$alldetailrpid">
	<if criteria="$useraccess[edit_purchase]">
	<input type="hidden" name="submits" value="Ubah">
	<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;</endif>
	<input type="button" value="Kembali" class="button" onclick="window.open(\'paydebt.php?screen=list\',\'_self\')">&nbsp;&nbsp;&nbsp;
	</if>
</div>
</form>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>
<input type="hidden" id="totalpay" name="totalpay" value="$ttlpay[alltotals]" >
<input type="hidden" id="defnumb" name="defnumb" value="$defnumb">
</body>

</html>
';
?>