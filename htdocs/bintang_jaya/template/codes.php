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
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_skyblue.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_rowspan.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>    
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script type="text/javascript">
	function checkcodeexist(objfrm){
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			var ids = "";
			if (objfrm.id){
				ids = objfrm.id.value;
			}
			$.ajax({
				type: \'POST\',
				url: \'codes.php\',
				data: \'check=code&targets=\'+objfrm.targets.value+\'&replacements=\'+objfrm.replacements.value+\'&replacementsale=\'+objfrm.replacements_sale.value+\'&orders=\'+objfrm.orders.value+\'&id=\'+ids,
				success: function(data) {
					if (data == "targetexist"){
						alert("Angka yang akan dikonversi sudah ada dalam sistem. Silahkan masukkan angka yang akan dikonversi lainnya.");
						objfrm.targets.focus();
					}
					else if (data == "replacementexist"){
						alert("Hasil konversi sudah ada dalam sistem. Silahkan masukkan hasil konversi lainnya.");
						objfrm.replacements.focus();
					}
					else if (data == "replacementsaleexist"){
						alert("Hasil konversi untuk faktur penjualan sudah ada dalam sistem. Silahkan masukkan hasil konversi lainnya.");
						objfrm.replacements_sale.focus();
					}
					/*else if (data == "orderexist"){
						alert("Urutan konversi sudah ada dalam sistem. Silahkan masukkan urutan konversi lainnya.");
						objfrm.orders.focus();
					}*/
					else{
						if (confirm("Data sudah benar?")){
							objfrm.submit();
						}
					}
				}
			});
		}
		return false;
	}
	$(document).ready(function() {
		$(".formID").validationEngine();
	})
</script>
</head>

<body>
<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_codes] || ($useraccess[edit_codes] && !empty($_REQUEST[id])))">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'codes.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
<input type="button" value="Kembali" class="button" onclick="window.open(\'codes.php\',\'_self\')"></div>
<fieldset>
	<legend><if criteria="!empty($_REQUEST[id])">Ubah Kode<else>Tambah Kode</if></legend>
	<div align="left">
		<form name="frmcodes" id="frmcodes" class="formID" method="post" action="codes.php" onsubmit="return checkcodeexist(this)">
		<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td align="left">Angka yang akan Dikonversi</td>
			<td align="left">
			<input type="text" name="targets" id="targets" value="$detailcodes[targets]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Hasil Konversi</td>
			<td align="left">
			<input type="text" name="replacements" id="replacements" value="$detailcodes[replacements]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Hasil Konversi ( Untuk Faktur Penjualan )</td>
			<td align="left">
			<input type="text" name="replacements_sale" id="replacements_sale" value="$detailcodes[replacements_sale]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Urutan Konversi</td>
			<td align="left">
			<input type="text" name="orders" id="orders" value="$detailcodes[orders]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">
			<input type="hidden" name="id" value="$_REQUEST[id]">
			<if criteria="!empty($_REQUEST[id])">
			<if criteria="$useraccess[edit_codes]">
			<input type="hidden" name="submits" value="Ubah">
			<input type="submit" value="Ubah" class="button"></endif>
			<else>
			<input type="hidden" name="submits" value="Tambah">
			<input type="submit" value="Tambah" class="button">
			</if></td>
			<td align="left"></td>		
		</tr>
		</table>
		</form>
	</div>
</fieldset>
<else>
<if criteria="$useraccess[add_codes]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'codes.php?getlist=detail\',\'_self\')"></div></endif>
<div id="codesbox" style="width: 100%; height:460px; background-color:white;"></div>
<script>
	var heightauto = (document.documentElement.clientHeight-50)+"px";
	$("#codesbox").css("height",heightauto);
	mygrid = new dhtmlXGridObject("codesbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("Angka yang Dikonversi,Hasil Konversi,Hasil Konversi (Faktur Penjualan),Urutan Konversi,Update Terakhir,Oleh,Action,#cspan");
	mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;,&nbsp;,&nbsp;,&nbsp;");
	mygrid.setInitWidthsP("15,15,12,12,15,15,8,8");
	mygrid.enableAutoWidth(true);
	mygrid.setColAlign("center,center,center,right,center,left,center,center");
	mygrid.setColTypes("ro,ro,ro,ron,ro,ro,link,link");
	mygrid.setColSorting("str,str,str,int,str,str,str,str");
	mygrid.setSkin("dhx_skyblue");
	mygrid.init();
	mygrid.enableSmartRendering(true);
	mygrid.enableRowsHover(true,"hover");
	mygrid.loadXML("codes.php?getlist=xml");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		var idus = rId.indexOf("_");
		if (idus != -1){
			rId = rId.substr(0,idus);
		}
		window.open("codes.php?getlist=detail&id="+rId,"_self");
	});
		
	function clearallsfield(){
		var allinputtag = document.getElementsByTagName("input");
		var focusit = false;
		var sel = 0;
		for (var t = 0; t < allinputtag.length; t++){
			if (allinputtag[t].type == "text"){
				if (allinputtag[t].value != ""){
					sel = t;
				}
				allinputtag[t].value = "";
				if (!focusit){
					allinputtag[t].focus();
					focusit = true;
				}
			}
		}
		allinputtag[sel].onkeydown();
	}
</script>
</if>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>

</body>

</html>
';
?>