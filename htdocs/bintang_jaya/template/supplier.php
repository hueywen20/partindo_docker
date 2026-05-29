<?php
$html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname]</title>
$headinclude
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxgrid.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxcombo.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_skyblue.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxcombo.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_rowspan.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>    
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_form.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_keymap_excel.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_combo.js"></script>
<script type="text/javascript">
	function checkcodeexist(objfrm){
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			if (mygrid.getRowsNum() == 0){
				alert("Tidak ada data alamat");
			}
			else{
				var ids = "";
				if (objfrm.id){
					ids = objfrm.id.value;
				}
				$.ajax({
					type: \'POST\',
					url: \'supplier.php\',
					data: \'check=code&suppliercode=\'+objfrm.suppliercode.value+\'&id=\'+ids,
					success: function(data) {
						if (data == 1){
							alert("Kode Supplier sudah ada dalam sistem. Silahkan masukkan kode Supplier yang lain.");
							objfrm.suppliercode.focus();
						}
						else if (data == 0){
							objfrm.submit();
						}
						else{
							if (confirm(data)){
								objfrm.submit();
							}
						}
					}
				});
			}
		}
		return false;
	}
	
	function copytocustomer(){
		var ids = "";
		if (document.frmsupplier.id){
			ids = document.frmsupplier.id.value;
		}
		$.ajax({
			type: \'POST\',
			url: \'supplier.php\',
			data: \'copy=tocustomer&id=\'+ids,
			success: function(data) {
				if (data){
					alert("Supplier telah dijadikan sebagai customer");
					$("#buttonsupplier").css("display","none");
				}
				else{
					alert("Kode supplier yang akan dijadikan customer telah ada. Silahkan melakukan pengubahan kode customer yang telah ada tersebut terlebih dahulu.");
				}
			}
		});		
	}

	$(document).ready(function() {
		$(".formID").validationEngine();
	})
</script>
</head>

<body>
<if criteria="$_GET[getlist] == determine">
	<script>
		deleteCookie("cookiesuppliersortfielddtm");
		deleteCookie("cookiesupplierdetaildtm");
		deleteCookie("cookiesupplierkeyworddtm");
		deleteCookie("cookiesupplierfielddtm");
		
		var headertext = [
		"Kode Supplier <img id=\'sortsuppliercode\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(\"sortcustomercode\")\'>",
		"Nama Supplier <img id=\'sortsuppliername\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(\"sortcustomername\")\'>",
		"Alamat Supplier <img id=\'sortsupplieradd\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>",
		"C.Person <img id=\'sortsuppliercperson\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>",
		"Telp <img id=\'sortsuppliertelp\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>",
		"Kota <img id=\'sortsuppliercity\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>"
	];
	var headerattach = [
		\'<input type="text" id="suppliercode" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
		\'<input type="text" id="suppliername" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
		\'<input type="text" id="address" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
		\'<input type="text" id="contactperson" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
		\'<input type="text" id="phone" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
		\'<input type="text" id="areaname" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\'
	];
	var percentwidth = 100;
	var colwidth = [12,15,15,15,10,10];
	var realcolwidth = "";
	
	var ajaxvarsearch = null;
	var timers = null;
	
	function ajaxsearch(keywords,fields,sortfield,sortdetail,page){
		if (ajaxvarsearch != null){
			ajaxvarsearch.abort();
		}
		
		searchm = "front";
		
		clearTimeout(timers);
		timers = null;
		timers = setTimeout(function(){
			ajaxvarsearch = $.get("supplier.php", {getlist: "determines", \'keyword[]\': keywords, \'field[]\': fields, \'searchmode\': searchm, \'page\': page, \'asm\': \'-1\', sortf: sortfield, sortd: sortdetail, hdv: heightdiv, cwidth: realcolwidth}, function(data){
				$("#loadingprogress").css("display","none");
				$("#stufflist").html(data);
				rowselected = 0;
			});
		},500);
	}
	
	function ajaxfromcookie(page){
		if (page == ""){
			page = 1;
		}
		
		$("#loadingprogress").css("display","inline-block");
				
		var keywords = new Array();
		var fields = new Array();
		
		var sortstuff = "";
		var sortstuffdetail = "";
		
		var fieldstext = getCookie("cookiesupplierfielddtm");
		
		if (fieldstext != ""){
			var keywordstext = getCookie("cookiesupplierkeyworddtm");
			
			var keywords = keywordstext.split(",");
			var fields = fieldstext.split(",");
			
			var lf = fields.length;
			for (var i = 0; i < lf; i++){
				$("#"+fields[i]).val(keywords[i]);
			}
		}
		
		var sortstuff = getCookie("cookiesuppliersortfielddtm");
		var sortstuffdetail = getCookie("cookiesupplierdetaildtm");
		if (sortstuff != ""){
			if (sortstuffdetail == "ASC"){
				$("#"+sortstuff).attr("src","img/up.png");
			}
			else{
				$("#"+sortstuff).attr("src","img/down.png");
			}
		}
		
		ajaxsearch(keywords,fields,sortstuff,sortstuffdetail,page);
	}
	
	function searchnow(){
		$("#loadingprogress").css("display","inline-block");
				
		var keywords = new Array();
		var fields = new Array();
		
		var f1 = $("#suppliercode").val();
		if (f1 != ""){
			keywords.push(f1);
			fields.push("suppliercode");
		}
		var f2 = $("#suppliername").val();
		if (f2  != ""){
			keywords.push(f2);
			fields.push("suppliername");
		}
		var f3 = $("#address").val();
		if (f3 != ""){
			keywords.push(f3);
			fields.push("address");
		}
		var f4 = $("#contactperson").val();
		if (f4 != ""){
			keywords.push(f4);
			fields.push("contactperson");
		}
		var f5 = $("#phone").val();
		if (f5 != ""){
			keywords.push(f5);
			fields.push("phone");
		}
		var f6 = $("#areaname").val();
		if (f6 != ""){
			keywords.push(f6);
			fields.push("areaname");
		}
		
		setCookie("cookiesupplierkeyworddtm",keywords);
		setCookie("cookiesupplierfielddtm",fields);
		
		var sortfield = getCookie("cookiesuppliersortfielddtm");
		var sortdetail = getCookie("cookiesupplierdetaildtm");
		
		ajaxsearch(keywords,fields,sortfield,sortdetail,1);
	}
	
	function sorts(id,field){
		
		var sortfield = "";
		var sortdetail = "";
		var sortstuff = getCookie("cookiesuppliersortfielddtm");
		if (sortstuff == id){
			var sortstuffdetail = getCookie("cookiesupplierdetaildtm");
			
			if (sortstuffdetail == "ASC"){
				$("#"+id).attr("src","img/down.png");
				setCookie("cookiesupplierdetaildtm","DESC");
				sortdetail = "DESC";
			}
			else{
				$("#"+id).attr("src","img/up.png");
				setCookie("cookiesupplierdetaildtm","ASC");
				sortdetail = "ASC";
			}
		}
		else{
			if (sortstuff != ""){
				$("#"+sortstuff).attr("src","img/updown.png");
			}
			
			$("#"+id).attr("src","img/up.png");
			setCookie("cookiesuppliersortfielddtm",id);
			setCookie("cookiesupplierdetaildtm","ASC");
			sortdetail = "ASC";
		}
			
		sortfield = id;
		searchnow();
	}
	
	function checkenters(event){
		var G = getKeyEvent(event);
		if (G == 13){
			return false;
		}
	}
	
	function loadajax(event){
		var G = getKeyEvent(event);
		if (G == 13){
			if (ajaxvarsearch != null){
				ajaxvarsearch.abort();
			}
		}
		searchnow();
	}
	
	var heightdiv = document.documentElement.clientHeight-80;
	</script>
<script type="text/javascript" src="js/maketable.js"></script>
<script>
ajaxfromcookie("");
</script>
<else>
	<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_supplier] || ($useraccess[edit_supplier] && !empty($_REQUEST[id])))">
	<if criteria="!empty($errors)">
	<div class="error">
	<ul>
		<if criteria="$errors == \'samecode\'">
		<li>Kode Supplier sudah ada dalam sistem. Silahkan masukkan kode Supplier yang lain.</li>
		</endif>
	</ul></div><br>
	</endif>
	<div align="right">
	<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'supplier.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
	<input type="button" value="Kembali" class="button" onclick="window.open(\'supplier.php?getlist=listingscreen\',\'_self\')"></div>
	<fieldset>
		<legend><if criteria="!empty($_REQUEST[id])">Ubah supplier<else>Tambah supplier</if></legend>
		<div align="left">
			<form name="frmsupplier" id="frmsupplier" class="formID" method="post" action="supplier.php" onsubmit="return checkcodeexist(this)">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" align="left" valign="top">
				<table border="0" cellpadding="5" cellspacing="5">
				<tr>
					<td align="left">Kode Supplier</td>
					<td align="left">
					<input type="text" name="suppliercode" id="suppliercode" value="$detailsupplier[suppliercode]" class="validate[required]"></td>
					<td align="left">Nama Supplier</td>
					<td align="left">
					<input type="text" name="suppliername" size="50" id="suppliername" value="$detailsupplier[suppliername]" class="validate[required]">
					<if criteria="$cancopytocustomer">
					&nbsp;&nbsp;&nbsp;
					<input type="button" class="button" id="buttonsupplier" value="Jadikan Customer" onclick="copytocustomer()"></endif></td>
				</tr>
				<if criteria="!empty($_REQUEST[id])">
				<tr>
					<td align="left">Total Hutang</td>
					<td align="left">
					<b>Rp. $fdebt</b></td>
					<td align="left"></td>
					<td align="left"></td>
				</tr>
				</endif>
				</table></td>
			</tr>
			<tr>
				<td width="100%" align="left" valign="top">
				<table border="0" cellpadding="3" cellspacing="3">
				<tr>
					<td align="left" class="tdhover" onclick="adddetailsupplier()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
					<td align="left" class="tdhover" onclick="deletedetailsupplier()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<td width="100%" align="left" valign="top">
				<div id="detailsplbox" style="width: 100%; height:200px; background-color:white;"></div>
				<script type="text/javascript" src="js/gridf.js"></script>
				<script>
					mygrid = new dhtmlXGridObject("detailsplbox");
					mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
					//mygrid.setHeader("Alamat,Contact Person,Kode Pos,Kota,Propinsi,Negara,Telepon,Fax,No. HP,Status");
					mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
					mygrid.setInitWidthsP("21,8,5,11,11,11,9,9,9,6");
					mygrid.enableAutoWidth(true);
					mygrid.setColAlign("left,left,left,left,left,left,left,left,left,left");
					//mygrid.setColTypes("ed,ed,ed,combo,combo,combo,ed,ed,ed,coro");
					mygrid.setColSorting("str,str,str,str,str,str,str,str,str,str");
					var combo9 = mygrid.getCombo(9);
					combo9.put(0, "Non-Aktif");
					combo9.put(1, "Aktif");
					mygrid.setSkin("dhx_skyblue");
					mygrid.init();
					mygrid.enableSmartRendering(true);
					mygrid.enableRowsHover(true,"hover");
					mygrid.loadXML("supplier.php?getlist=xml&list=detail&id=$_REQUEST[id]");
				</script>
				</td>
			</tr>
			<tr>
				<td width="100%" align="left" valign="top">
				<table border="0" cellpadding="5" cellspacing="5">
				<tr>
					<td align="left">Status</td>
					<td align="left">
					<select name="supplierstatus">
						<option value="1"<if criteria="$detailsupplier[status] == 1"> selected</endif>>Aktif</option>
						<option value="0"<if criteria="$detailsupplier[status] == 0"> selected</endif>>Non-Aktif</option>
					</select></td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<td align="center" colspan="2">
				<if criteria="!empty($_REQUEST[id])">
				<input type="hidden" name="id" value="$_REQUEST[id]">
				<input type="hidden" name="detailid" value="$alldetailid">
				<if criteria="$useraccess[edit_supplier]">
				<input type="hidden" name="submits" value="Ubah">
				<input type="submit" value="Ubah" class="button"></endif>
				<if criteria="$useraccess[delete_supplier]">
				<input type="button" value="Hapus" class="button" onclick="deleteitem(\'supplier.php?do=delete&id=$_REQUEST[id]\')"></endif>
				<else>
				<input type="hidden" name="submits" value="Tambah">
				<input type="submit" value="Tambah" class="button">
				</if></td>
			</tr>
			</table>
			</form>
		</div>
	</fieldset><br>
	<else>
	<if criteria="$useraccess[add_supplier]">
	<div align="right" style="padding: 3px">
	<span style="float: left">
	<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
	<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'supplier.php?getlist=detail\',\'_self\')"></div></endif>
	<script type="text/javascript">
		var headertext = [
			"Kode Supplier",
			"Nama Supplier",
			"Alamat Supplier",
			"C.Person",
			"Status",
			"Update Terakhir",
			"Oleh",
			"Action"
		];
		var headerattach = [
			\'<input type="text" id="suppliercode" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
			\'<input type="text" id="suppliername" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
			\'<input type="text" id="address" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
			\'<input type="text" id="contactperson" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
			\'<input type="text" id="status" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
			\'\',
			\'\',
			\'\'
		];
		var percentwidth = 100;
		var colwidth = [10,15,22,10,6,15,10,12];
		var realcolwidth = "";
		
		var ajaxvarsearch = null;
		var timers = null;
		
		function ajaxsearch(keywords,fields,sortfield,sortdetail,page){
			if (ajaxvarsearch != null){
				ajaxvarsearch.abort();
			}
		
			searchm = "front";
			
			clearTimeout(timers);
			timers = null;
			timers = setTimeout(function(){
				ajaxvarsearch = $.get("supplier.php", {getlist: "listingall", \'keyword[]\': keywords, \'field[]\': fields, \'searchmode\': searchm, \'page\': page, \'asm\': \'-1\', sortf: sortfield, sortd: sortdetail, hdv: heightdiv, cwidth: realcolwidth}, function(data){
					$("#loadingprogress").css("display","none");
					$("#stufflist").html(data);
					rowselected = 0;
				});
			},200);
		}
		
		function ajaxfromcookie(page){
			if (page == ""){
				var keywordspage = getCookie("cookiepagesupplier");
				if (keywordspage == ""){
					page = 1;
				}
				else{
					page = keywordspage;
				}
			}
			
			setCookie("cookiepagesupplier",page);
			
			$("#loadingprogress").css("display","inline-block");
			
			var fieldstext = getCookie("cookiesupplierfield");
			
			if (fieldstext != ""){
				var keywordstext = getCookie("cookiesupplierkeyword");
				
				var keywords = keywordstext.split(",");
				var fields = fieldstext.split(",");
				
				var lf = fields.length;
				for (var i = 0; i < lf; i++){
					$("#"+fields[i]).val(keywords[i]);
				}
			}
			
			var sortstuff = getCookie("cookiesuppliersortfield");
			var sortstuffdetail = getCookie("cookiesupplierdetail");
			if (sortstuff != ""){
				if (sortstuffdetail == "ASC"){
					$("#"+sortstuff).attr("src","img/up.png");
				}
				else{
					$("#"+sortstuff).attr("src","img/down.png");
				}
			}
			
			ajaxsearch(keywords,fields,sortstuff,sortstuffdetail,page);
		}
		
		function searchnow(){
			$("#loadingprogress").css("display","inline-block");
					
			var keywords = new Array();
			var fields = new Array();
			
			var f1 = $("#suppliercode").val();
			if (f1 != ""){
				keywords.push(f1);
				fields.push("suppliercode");
			}
			var f2 = $("#suppliername").val();
			if (f2  != ""){
				keywords.push(f2);
				fields.push("suppliername");
			}
			var f3 = $("#address").val();
			if (f3 != ""){
				keywords.push(f3);
				fields.push("address");
			}
			var f4 = $("#contactperson").val();
			if (f4 != ""){
				keywords.push(f4);
				fields.push("contactperson");
			}
			var f7 = $("#status").val();
			if (f7 != ""){
				keywords.push(f7);
				fields.push("status");
			}
			
			setCookie("cookiesupplierkeyword",keywords);
			setCookie("cookiesupplierfield",fields);
			
			var sortfield = getCookie("cookiesuppliersortfield");
			var sortdetail = getCookie("cookiesupplierdetail");
			
			ajaxsearch(keywords,fields,sortfield,sortdetail,1);
		}
		
		function sorts(id,field){
			
			var sortfield = "";
			var sortdetail = "";
			var sortstuff = getCookie("cookiesuppliersortfield");
			if (sortstuff == id){
				var sortstuffdetail = getCookie("cookiesupplierdetail");
				
				if (sortstuffdetail == "ASC"){
					$("#"+id).attr("src","img/down.png");
					setCookie("cookiesupplierdetail","DESC");
					sortdetail = "DESC";
				}
				else{
					$("#"+id).attr("src","img/up.png");
					setCookie("cookiesupplierdetail","ASC");
					sortdetail = "ASC";
				}
			}
			else{
				if (sortstuff != ""){
					$("#"+sortstuff).attr("src","img/updown.png");
				}
				
				$("#"+id).attr("src","img/up.png");
				setCookie("cookiesuppliersortfield",id);
				setCookie("cookiesupplierdetail","ASC");
				sortdetail = "ASC";
			}
				
			sortfield = id;
			searchnow();
		}
		
		function checkenters(event){
			var G = getKeyEvent(event);
			if (G == 13){
				return false;
			}
		}
		
		function loadajax(event){
			<if criteria="empty($_GET[getlist])">
			deleteCookie("cookiepagesupplier");
			</endif>
			var G = getKeyEvent(event);
			if (G == 13){
				if (ajaxvarsearch != null){
					ajaxvarsearch.abort();
				}
			}
			searchnow();
		}
		
		var heightdiv = document.documentElement.clientHeight-100;
		</script>
	<script type="text/javascript" src="js/maketable_new.js"></script>
	<script>
		<if criteria="empty($_GET[getlist])">
		deleteCookie("cookiepagesupplier");
		</endif>
		ajaxfromcookie("");
		function clearallsfield(){
			var allinputtag = document.getElementsByTagName("input");
			var focusit = false;
			for (var t = 0; t < allinputtag.length; t++){
				if (allinputtag[t].type == "text"){
					allinputtag[t].value = "";
					if (!focusit){
						allinputtag[t].focus();
						focusit = true;
					}
				}
			}
			searchnow();
		}
	</script>
	</if>
	<script>
	$(document).click(function() {
		window.parent.closeall(1);
	});
	</script>
</if>
</body>
</html>';
?>