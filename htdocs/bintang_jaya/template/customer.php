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
					url: \'customer.php\',
					data: \'check=code&customercode=\'+objfrm.customercode.value+\'&id=\'+ids,
					success: function(data) {
						if (data == 1){
							alert("Kode customer sudah ada dalam sistem. Silahkan masukkan kode customer yang lain.");
							objfrm.customercode.focus();
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
	
	function copytosupplier(){
		var ids = "";
		if (document.frmcustomer.id){
			ids = document.frmcustomer.id.value;
		}
		$.ajax({
			type: \'POST\',
			url: \'customer.php\',
			data: \'copy=tosupplier&id=\'+ids,
			success: function(data) {
				if (data){
					alert("Customer telah dijadikan sebagai supplier");
					$("#buttonsupplier").css("display","none");
				}
				else{
					alert("Kode customer yang akan dijadikan supplier telah ada. Silahkan melakukan pengubahan kode supplier yang telah ada tersebut terlebih dahulu.");
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
<if criteria="$_GET[getlist] == \'determine\'">
	<script>
		deleteCookie("cookiecostumerdetaildtm");
		deleteCookie("cookiecostumersortfielddtm");
		deleteCookie("cookiecostumerkeyworddtm");
		deleteCookie("cookiecostumerfielddtm");
		
		var headertext = [
		"Kode Customer <img id=\'sortcustomercode\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(\"sortcustomercode\")\'>",
		"Nama Customer <img id=\'sortcustomername\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(\"sortcustomername\")\'>",
		"Alamat Customer <img id=\'sortcustomeradd\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>",
		"C.Person <img id=\'sortcustomercperson\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>",
		"Telp <img id=\'sortcustomertelp\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>",
		"Kota <img id=\'sortcustomercity\' src=\'img/updown.png\' border=\'0\' style=\'vertical-align: middle;cursor:pointer;\' onclick=\'sorts(this.id)\'>"
	];
	var headerattach = [
		\'<input type="text" id="customercode" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
		\'<input type="text" id="customername" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
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
		
		var searchm = $("input:radio[name=searchmode]:checked").val();
		if (searchm == ""){
			searchm = "in";
		}
		
		clearTimeout(timers);
		timers = null;
		timers = setTimeout(function(){
			ajaxvarsearch = $.get("customer.php", {getlist: "determines", \'keyword[]\': keywords, \'field[]\': fields, \'searchmode\': searchm, \'page\': page, \'asm\': \'-1\', sortf: sortfield, sortd: sortdetail, hdv: heightdiv, cwidth: realcolwidth}, function(data){
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
		
		var fieldstext = getCookie("cookiecostumerfielddtm");
		
		if (fieldstext != ""){
			var keywordstext = getCookie("cookiecostumerkeyworddtm");
			
			var keywords = keywordstext.split(",");
			var fields = fieldstext.split(",");
			
			var lf = fields.length;
			for (var i = 0; i < lf; i++){
				$("#"+fields[i]).val(keywords[i]);
			}
		}
		
		var sortstuff = getCookie("cookiecostumersortfielddtm");
		var sortstuffdetail = getCookie("cookiecostumerdetaildtm");
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
		
		var f1 = $("#customercode").val();
		if (f1 != ""){
			keywords.push(f1);
			fields.push("customercode");
		}
		var f2 = $("#customername").val();
		if (f2  != ""){
			keywords.push(f2);
			fields.push("customername");
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
		
		setCookie("cookiecostumerkeyworddtm",keywords);
		setCookie("cookiecostumerfielddtm",fields);
		
		var sortfield = getCookie("cookiecostumersortfielddtm");
		var sortdetail = getCookie("cookiecostumerdetaildtm");
		
		ajaxsearch(keywords,fields,sortfield,sortdetail,1);
	}
	
	function sorts(id,field){
		
		var sortfield = "";
		var sortdetail = "";
		var sortstuff = getCookie("cookiecostumersortfielddtm");
		if (sortstuff == id){
			var sortstuffdetail = getCookie("cookiecostumerdetaildtm");
			
			if (sortstuffdetail == "ASC"){
				$("#"+id).attr("src","img/down.png");
				setCookie("cookiecostumerdetaildtm","DESC");
				sortdetail = "DESC";
			}
			else{
				$("#"+id).attr("src","img/up.png");
				setCookie("cookiecostumerdetaildtm","ASC");
				sortdetail = "ASC";
			}
		}
		else{
			if (sortstuff != ""){
				$("#"+sortstuff).attr("src","img/updown.png");
			}
			
			$("#"+id).attr("src","img/up.png");
			setCookie("cookiecostumersortfielddtm",id);
			setCookie("cookiecostumerdetaildtm","ASC");
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
	<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_customer] || ($useraccess[edit_customer] && !empty($_REQUEST[id])))">
	<if criteria="!empty($errors)">
	<div class="error">
	<ul>
		<if criteria="$errors == \'samecode\'">
		<li>Kode customer sudah ada dalam sistem. Silahkan masukkan kode customer yang lain.</li>
		</endif>
	</ul></div><br>
	</endif>
	<div align="right">
	<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'customer.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
	<input type="button" value="Kembali" class="button" onclick="window.open(\'customer.php?getlist=listingscreen\',\'_self\')"></div>
	<fieldset>
		<legend><if criteria="!empty($_REQUEST[id])">Ubah customer<else>Tambah customer</if></legend>
		<div align="left">
			<form name="frmcustomer" id="frmcustomer" class="formID" method="post" action="customer.php" onsubmit="return checkcodeexist(this)">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" align="left" valign="top">
				<table border="0" cellpadding="5" cellspacing="5">
				<tr>
					<td align="left">Kode Customer</td>
					<td align="left">
					<input type="text" name="customercode" id="customercode" value="$detailcustomer[customercode]" class="validate[required]"></td>
					<td align="left">Nama Customer</td>
					<td align="left">
					<input type="text" name="customername" size="50" id="customername" value="$detailcustomer[customername]" class="validate[required]">
					<if criteria="$cancopytosupplier">
					&nbsp;&nbsp;&nbsp;
					<input type="button" class="button" id="buttonsupplier" value="Jadikan Supplier" onclick="copytosupplier()"></endif></td>
				</tr>
				<if criteria="!empty($_REQUEST[id])">
				<tr>
					<td align="left">Total Piutang</td>
					<td align="left">
					<b>Rp. $fcredit</b></td>
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
					<td align="left" class="tdhover" onclick="adddetailcustomer()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
					<td align="left" class="tdhover" onclick="deletedetailcustomer()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<td width="100%" align="left" valign="top">
				<div id="detailcustbox" style="width: 100%; height:200px; background-color:white;"></div>
				<script type="text/javascript" src="js/gridf.js"></script>
				<script>
					mygrid = new dhtmlXGridObject("detailcustbox");
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
					mygrid.loadXML("customer.php?getlist=xml&list=detail&id=$_REQUEST[id]");
				</script>
				</td>
			</tr>
			<tr>
				<td width="100%" align="left" valign="top">
				<table border="0" cellpadding="5" cellspacing="5">
				<tr>
					<td align="left">Status</td>
					<td align="left">
					<select name="customerstatus">
						<option value="1"<if criteria="$detailcustomer[status] == 1"> selected</endif>>Aktif</option>
						<option value="0"<if criteria="$detailcustomer[status] == 0"> selected</endif>>Non-Aktif</option>
					</select></td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<td align="center" colspan="2">
				<if criteria="!empty($_REQUEST[id])">
				<input type="hidden" name="id" value="$_REQUEST[id]">
				<input type="hidden" name="detailid" value="$alldetailid">
				<if criteria="$useraccess[edit_customer]">
				<input type="hidden" name="submits" value="Ubah">
				<input type="submit" value="Ubah" class="button"></endif>
				<if criteria="$useraccess[delete_customer]">
				<input type="button" value="Hapus" class="button" onclick="deleteitem(\'customer.php?do=delete&id=$_REQUEST[id]\')"></endif>
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
	<if criteria="$useraccess[add_customer]">
	<div align="right" style="padding: 3px">
	<span style="float: left">
	<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
	<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'customer.php?getlist=detail\',\'_self\')"></div></endif>
	<script type="text/javascript">
		var headertext = [
			"Kode Customer",
			"Nama Customer",
			"Alamat Customer",
			"C.Person",
			"Status",
			"Update Terakhir",
			"Oleh",
			"Action"
		];
		var headerattach = [
			\'<input type="text" id="customercode" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
			\'<input type="text" id="customername" autocomplete="off" onkeyup="loadajax(event)" onkeypress="return checkenters(event)" class="filterbox">\',
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
				ajaxvarsearch = $.get("customer.php", {getlist: "listingall", \'keyword[]\': keywords, \'field[]\': fields, \'searchmode\': searchm, \'page\': page, \'asm\': \'-1\', sortf: sortfield, sortd: sortdetail, hdv: heightdiv, cwidth: realcolwidth}, function(data){
					$("#loadingprogress").css("display","none");
					$("#stufflist").html(data);
					rowselected = 0;
				});
			},200);
		}
		
		function ajaxfromcookie(page){
			if (page == ""){
				var keywordspage = getCookie("cookiepagecustomer");
				if (keywordspage == ""){
					page = 1;
				}
				else{
					page = keywordspage;
				}
			}
			
			setCookie("cookiepagecustomer",page);
			
			$("#loadingprogress").css("display","inline-block");
			
			var fieldstext = getCookie("cookiecustomerfield");
			
			if (fieldstext != ""){
				var keywordstext = getCookie("cookiecustomerkeyword");
				
				var keywords = keywordstext.split(",");
				var fields = fieldstext.split(",");
				
				var lf = fields.length;
				for (var i = 0; i < lf; i++){
					$("#"+fields[i]).val(keywords[i]);
				}
			}
			
			var sortstuff = getCookie("cookiecustomersortfield");
			var sortstuffdetail = getCookie("cookiecustomerdetail");
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
			
			var f1 = $("#customercode").val();
			if (f1 != ""){
				keywords.push(f1);
				fields.push("customercode");
			}
			var f2 = $("#customername").val();
			if (f2  != ""){
				keywords.push(f2);
				fields.push("customername");
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
			
			setCookie("cookiecustomerkeyword",keywords);
			setCookie("cookiecustomerfield",fields);
			
			var sortfield = getCookie("cookiecustomersortfield");
			var sortdetail = getCookie("cookiecustomerdetail");
			
			ajaxsearch(keywords,fields,sortfield,sortdetail,1);
		}
		
		function sorts(id,field){
			
			var sortfield = "";
			var sortdetail = "";
			var sortstuff = getCookie("cookiecustomersortfield");
			if (sortstuff == id){
				var sortstuffdetail = getCookie("cookiecustomerdetail");
				
				if (sortstuffdetail == "ASC"){
					$("#"+id).attr("src","img/down.png");
					setCookie("cookiecustomerdetail","DESC");
					sortdetail = "DESC";
				}
				else{
					$("#"+id).attr("src","img/up.png");
					setCookie("cookiecustomerdetail","ASC");
					sortdetail = "ASC";
				}
			}
			else{
				if (sortstuff != ""){
					$("#"+sortstuff).attr("src","img/updown.png");
				}
				
				$("#"+id).attr("src","img/up.png");
				setCookie("cookiecustomersortfield",id);
				setCookie("cookiecustomerdetail","ASC");
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
			deleteCookie("cookiepagecustomer");
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
		deleteCookie("cookiepagecustomer");
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