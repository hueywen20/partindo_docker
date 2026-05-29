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
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxcombo.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_vista.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_skyblue.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxcombo.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_rowspan.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>    
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/shortcut.js"></script>
<script type="text/javascript">
	function loadChange(mode){
		$.ajax({
			type: \'GET\',
			url: \'ajax.php\',
			data: \'list=\'+mode,
			success: function(data) {
				$("#"+mode+"el").html(data);
				var b = dhtmlXComboFromSelect(mode+"code");
				b.enableFilteringMode(true);
				scanelement();
			}
		})
	}
	
	function checkform(frm){
		<if criteria="!empty($_REQUEST[id])">
		var qtyhid = replacestr(replacestr($("#quantityhid").val(),".",""),",",".");
		var qty = replacestr(replacestr($("#quantity").val(),".",""),",",".");
		var rm = replacestr(replacestr($("#remaining").val(),".",""),",",".");
		var limits = qtyhid-rm;
		if (qty < limits){
			alert("Quantity tidak boleh kurang dari jumlah stok yang sudah terpakai");
			return false;
		}
		</endif>
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			var ids = "";
			if (frm.id){
				ids = frm.id.value;
			}
			$.ajax({
				type: \'POST\',
				url: \'firststock.php\',
				data: \'check=code&stockcode=\'+frm.stockcode.value+\'&id=\'+ids,
				success: function(data) {
					if (data){
						alert("Kode stok sudah ada dalam sistem. Silahkan masukkan kode stok yang lain.");
						frm.stockcode.focus();
					}
					else{
						if (confirm("Data sudah benar?")){
							frm.submit();
						}
					}
				}
			});
		}
		return false;
	}
	
	<if criteria="empty($_GET[getlist])">
	var u;
	var gridrows;
	var doing;
	var arrcols = [5,6];
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
	</endif>
	
	shortcut.add("alt+r", function() {
		var views = $("#defnumb");
		if (views.val() == "code"){
			views.val("number");
			doing = "deconvert";
		}
		else{
			views.val("code");
			doing = "convert";
		}
		
		var buypr = $("#buyprice");
		var sellpr = $("#sellprice");
		if (doing == "deconvert"){
			if (buypr.val() != ""){
				buypr.val(formatnumber(deconvertcodes(buypr.val())));
			}
			if (sellpr.val() != ""){
				sellpr.val(formatnumber(deconvertcodes(sellpr.val())));
			}
		}
		else{
			if (buypr.val() != ""){
				var curvalue1 = replacestr(replacestr(buypr.val(),".",""),",",".");
				curvalue1 = curvalue1.replace(".00","");
				buypr.val(convertcodes(curvalue1));
			}
			
			if (sellpr.val() != ""){
				var curvalue2 = replacestr(replacestr(sellpr.val(),".",""),",",".");
				curvalue2 = curvalue2.replace(".00","");
				sellpr.val(convertcodes(curvalue2));
			}
		}
		
		<if criteria="empty($_GET[getlist])">
		u = 0;
		gridrows = mygrid.getRowsNum();
		if (gridrows > 0){
			converting();
		}
		</endif>
	});

	$(document).ready(function() {
		$(".formID").validationEngine();
	})
</script>
</head>

<body>
<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_firststock] || ($useraccess[edit_firststock] && !empty($_REQUEST[id])))">
<div align="right" style="padding: 3px">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'firststock.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
<input type="button" value="Kembali" class="button" onclick="window.open(\'firststock.php\',\'_self\')"></div>
<if criteria="!empty($errors)">
<div class="error">
<ul>
	<if criteria="$errors == \'samecode\'">
	<li>Kode Stok sudah ada dalam sistem. Silahkan masukkan kode stok yang lain.</li>
	</endif>
</ul></div><br>
</endif>
<fieldset>
	<legend><if criteria="!empty($_REQUEST[id])">Ubah Stok<else>Tambah Stok</if></legend>
	<div align="left">
		<form name="frmstock" id="frmstock" class="formID" method="post" action="firststock.php" enctype="multipart/form-data" onsubmit="return checkform(this)">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="left" width="50%">
			<table border="0" cellpadding="5" cellspacing="5">
			<tr>
				<td align="left">Kode Stok</td>
				<td align="left">
				<input type="text" name="stockcode" id="stockcode" value="$detailstock[stockcode]" class="validate[required]"></td>
			</tr>
			<tr>
				<td align="left">Nama Standard</td>
				<td align="left">
				<input type="text" name="standardname" size="30" id="standardname" value="$detailstock[standardname]" class="validate[required]"></td>
			</tr>
			<tr>
				<td align="left">Nama Umum</td>
				<td align="left">
				<input type="text" name="generalname" size="30" id="generalname" value="$detailstock[generalname]" class="validate[required]"></td>
			</tr>
			<tr>
				<td align="left">Merek</td>
				<td align="left">
				<div id="brandel" style="width:100%; height:25px;"></div>
				<script type="text/javascript">
					var z = new dhtmlXCombo("brandel", "brandcode", "100%");
					z.enableFilteringMode(true, "ajax.php?list=brand&type=xml", false);
					z.loadXML("ajax.php?list=brand&type=xml&id=$selbrand");
				</script></td>
			</tr>
			<tr>
				<td align="left">Tipe</td>
				<td align="left">
				<div id="typeel" style="width:100%; height:25px;"></div>
				<script type="text/javascript">
					var z = new dhtmlXCombo("typeel", "typecode", "100%");
					z.enableFilteringMode(true, "ajax.php?list=type&type=xml", false);
					z.loadXML("ajax.php?list=type&type=xml&id=$seltype");
				</script></td>
			</tr>
			<tr>
				<td align="left" valign="top">Nomor Part</td>
				<td align="left">
				<textarea name="partno" id="partno" cols="30" rows="4" class="validate[required]">$allpartno</textarea></td>
			</tr>
			<tr>
				<td align="left">Ukuran</td>
				<td align="left">
				<input type="text" name="size" id="size" value="$detailstock[size]" class="validate[required]"></td>
			</tr>
			<tr>
				<td align="left">Status</td>
				<td align="left">
				<select name="stockstatus">
					<option value="1"<if criteria="$detailstock[status] == 1"> selected</endif>>Aktif</option>
					<option value="0"<if criteria="$detailstock[status] == 0"> selected</endif>>Non-Aktif</option>
				</select></td>
			</tr>
			</table></td>
			<td align="left" width="50%" valign="top">
			<table border="0" cellpadding="5" cellspacing="5">
			<tr>
				<td align="left">Lokasi</td>
				<td align="left" width="127">
				<div id="locationel" style="width:100%; height:25px;"></div>
				<script type="text/javascript">
					var y = new dhtmlXCombo("locationel", "locationcode", "100%");
					y.enableFilteringMode(true, "ajax.php?list=location&type=xml", false);
					y.loadXML("ajax.php?list=location&type=xml&id=$sellocation");
				</script></td>
			</tr>
			<tr>
				<td align="left">Grup Stok</td>
				<td align="left">
				<div id="stgrel" style="width:100%; height:25px;"></div>
				<script type="text/javascript">
					var x = new dhtmlXCombo("stgrel", "stgrcode", "100%");
					x.enableFilteringMode(true, "ajax.php?list=stgr&type=xml", false);
					x.loadXML("ajax.php?list=stgr&type=xml&id=$selstgr");
				</script></td>
			</tr>
			<tr>
				<td align="left">Quantity</td>
				<td align="left">
				<input type="hidden" name="quantityhid" id="quantityhid" value="$detailstock[quantity]">
				<input type="text" name="quantity" id="quantity" value="$detailstock[quantity]" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="insertingfs(this)"></td>
			</tr>
			<tr>
				<td align="left">Quantity Minimum</td>
				<td align="left">
				<input type="text" name="minqty" id="minqty" value="$detailstock[minqty]" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="insertingfs(this)"></td>
			</tr>
			<if criteria="!empty($_REQUEST[id])">
			<tr>
				<td align="left">Sisa</td>
				<td align="left">
				<input type="text" name="remaining" id="remaining" value="$detailstock[remaining]" readonly></td>
			</tr>
			</endif>
			<tr>
				<td align="left">Harga Beli</td>
				<td align="left">
				<input type="text" name="buyprice" id="buyprice" value="$detailstock[buyprice]" class="validate[required]" onkeyup="insertingfs(this)"></td>
			</tr>
			<tr>
				<td align="left">Harga Jual</td>
				<td align="left">
				<input type="text" name="sellprice" id="sellprice" value="$detailstock[sellprice]" class="validate[required]" onkeyup="insertingfs(this)"></td>
			</tr>
			<tr>
				<td align="left">Satuan Terkecil</td>
				<td align="left">
				<div id="unitel" style="width:100%; height:25px;"></div>
				<script type="text/javascript">
					var w = new dhtmlXCombo("unitel", "unitcode", "100%");
					w.enableFilteringMode(true, "ajax.php?list=unit&type=xml", false);
					w.loadXML("ajax.php?list=unit&type=xml&id=$selunit");
				</script></td>
			</tr>
			<tr>
				<td align="left">Expired Date</td>
				<td align="left">
				<input type="text" name="expdate" id="expdate" value="$detailstock[expdate]" class="validate[required]"></td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<table border="0" cellpadding="5" cellspacing="5">
			<tr>
				<td align="left" width="88" valign="top">Foto Produk</td>
				<td align="left" valign="top">
				<if criteria="empty($photogal)">
				<input type="file" name="files"><br><br>
				<else>
				$photogal</if></td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td align="center" colspan="2">
			<input type="hidden" name="id" value="$_REQUEST[id]">
			<if criteria="!empty($_REQUEST[id])">
			<if criteria="$useraccess[edit_firststock]">
			<input type="hidden" name="submits" value="Ubah">
			<input type="submit" value="Ubah" class="button"></endif>
			<else>
			<input type="hidden" name="submits" value="Tambah">
			<input type="submit" value="Tambah" class="button">
			</if></td>
		</tr>
		</table>
		</form>
	</div>
</fieldset>
<script>
cal = new dhtmlxCalendarObject("expdate",true,{
	isWinHeader: true,
	isWinDrag: true,
	isYearEditable: true,
	isMonthEditable: true
});
cal.setDateFormat("%d-%m-%Y");
cal.setSkin("vista");
</script>
<else>
<div id="loadingprogress" style="display: none; position: fixed; z-index: 9999; top: 0px; left: 500px; padding: 5px; background-color: #000; color: #FFF; font-size: 14px; font-weight: bold">
Loading...</div>
<if criteria="$useraccess[add_firststock]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'firststock.php?getlist=detail\',\'_self\')"></div></endif>
<div id="fsbox" style="width: 99%; background-color:white;"></div>
<script>
	var heightauto = (document.documentElement.clientHeight-70)+"px";
	$("#fsbox").css("height",heightauto);
	mygrid = new dhtmlXGridObject("fsbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("Kode Stok,Nama Umum,Grup Stok,Merek,Tipe,Harga Beli,Harga Jual,Status,Update Terakhir,Oleh,Action,#cspan");
	//mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;,&nbsp;,&nbsp;,&nbsp;");
	mygrid.attachHeader("<input type=\'text\' id=\'stockcode\' class=\'filterbox\' autocomplete=\'off\' onkeyup=\'loadajax(event)\'>,<input type=\'text\' id=\'generalname\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(event)\'>,<input type=\'text\' id=\'stockgroup\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(event)\'>,<input type=\'text\' id=\'brandname\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(event)\'>,<input type=\'text\' id=\'typename\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(event)\'>,<input type=\'text\' id=\'buyprice\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(event)\'>,<input type=\'text\' id=\'sellprice\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(event)\'>,<input type=\'text\' autocomplete=\'off\' id=\'status\' class=\'filterbox\' onkeyup=\'loadajax(event)\'>,&nbsp;,&nbsp;,&nbsp;,&nbsp;");
	mygrid.setInitWidthsP("7,12,12,10,9,9,9,5,10,7,5,5");
	mygrid.enableAutoWidth(true);
	mygrid.setColAlign("left,left,left,left,left,right,right,center,center,left,center,center");
	mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,link,link");
	mygrid.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str");
	mygrid.setSkin("dhx_skyblue");
	mygrid.init();
	mygrid.enableSmartRendering(true);
	mygrid.enableRowsHover(true,"hover");
	mygrid.setNumberFormat("0,000",5,",",".");
	mygrid.setNumberFormat("0,000",6,",",".");
	//mygrid.loadXML("firststock.php?getlist=xml");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		var idus = rId.indexOf("_");
		if (idus != -1){
			rId = rId.substr(0,idus);
		}
		var idas = rId.indexOf("|!s");
		if (idas != -1){
			rId = rId.substr(0,idas);
			window.open("assembly.php?from=firststock&id="+rId,"_self");
		}
		else{
			window.open("firststock.php?getlist=detail&id="+rId,"_self");
		}
	});
	
	function goajax(page){
		var keywords = new Array();
		var fields = new Array();
		
		var stcd = $("#stockcode").val();
		if (stcd != ""){
			keywords.push(stcd);
			fields.push("stockcode");
		}
		var gnm = $("#generalname").val();
		if (gnm  != ""){
			keywords.push(gnm);
			fields.push("generalname");
		}
		var stgr = $("#stockgroup").val();
		if (stgr != ""){
			keywords.push(stgr);
			fields.push("stockgroup");
		}
		var brcd = $("#brandname").val();
		if (brcd != ""){
			keywords.push(brcd);
			fields.push("brandname");
		}
		var tycd = $("#typename").val();
		if (tycd != ""){
			keywords.push(tycd);
			fields.push("typename");
		}
		var bpr = $("#buyprice").val();
		if (bpr != ""){
			keywords.push(bpr);
			fields.push("buyprice");
		}
		var spr = $("#sellprice").val();
		if (spr != ""){
			keywords.push(spr);
			fields.push("sellprice");
		}
		var sts = $("#status").val();
		if (sts != ""){
			keywords.push(sts);
			fields.push("status");
		}
			
		if (page == ""){
			page = 1;
		}
		
		var joinsk = keywords.join("&keyword[]=");
		var joinsf = fields.join("&field[]=");
		
		mygrid.clearAll();
		mygrid.loadXML("firststock.php?getlist=xml&p="+page+"&keyword[]="+joinsk+"&field[]="+joinsf);
		
		$.get("firststock.php", {getlist: "pagenav", \'keyword[]\': joinsk, \'field[]\': joinsf, \'p\': page}, function(data){
			$("#loadingprogress").css("display","none");
			var arrdata = data.split("|^|");
			generatepagenav(arrdata);
		});
	}
		
	function loadajax(event){
		if (!detectspecialkeys(event)){
			var kn = getKeyEvent(event);
			if (kn == 13){
				$("#loadingprogress").css("display","block");
				goajax(1);
			}
		}
	}
	
	goajax(1);
	
	function clearallsfield(){
		$("#stockcode").val("");
		$("#generalname").val("");
		$("#stockgroup").val("");
		$("#brandname").val("");
		$("#typename").val("");
		$("#buyprice").val("");
		$("#sellprice").val("");
		$("#status").val("");
		$("#stockcode").focus();
	}
</script>
<div align="left" style="padding-top: 8px">
<span id="navpage"></span>
<span id="records" style="float: right; font-weight: bold"></span>
</div>
</if>
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