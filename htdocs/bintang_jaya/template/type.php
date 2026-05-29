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
				url: \'type.php\',
				data: \'check=code&typecode=\'+objfrm.typecode.value+\'&id=\'+ids,
				success: function(data) {
					if (data){
						alert("Kode tipe sudah ada dalam sistem. Silahkan masukkan kode tipe yang lain.");
						objfrm.typecode.focus();
					}
					else{
						objfrm.submit();
					}
				}
			});
		}
		return false;
	}
	
	$(document).ready(function() {
		$(".formID").validationEngine();
	})
	
	//window.parent.loadiniframe("firststock.php","type");
</script>
</head>

<body>
<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_type] || ($useraccess[edit_type] && !empty($_REQUEST[id])))">
<if criteria="!empty($errors)">
<div class="error">
<ul>
	<if criteria="$errors == \'samecode\'">
	<li>Kode tipe sudah ada dalam sistem. Silahkan masukkan kode negara yang lain.</li>
	</endif>
</ul></div><br>
</endif>
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'type.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
<input type="button" value="Kembali" class="button" onclick="window.open(\'type.php\',\'_self\')"></div>
<fieldset>
	<legend><if criteria="!empty($_REQUEST[id])">Ubah Tipe<else>Tambah Tipe</if></legend>
	<div align="left">
		<form name="frmtype" id="frmtype" class="formID" method="post" action="type.php" onsubmit="return checkcodeexist(this)">
		<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td align="left">Kode Tipe</td>
			<td align="left">
			<input type="text" name="typecode" id="typecode" value="$detailtype[typecode]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Nama Tipe</td>
			<td align="left">
			<input type="text" name="typename" id="typename" value="$detailtype[typename]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Status</td>
			<td align="left">
			<select name="typestatus">
				<option value="1"<if criteria="$detailtype[status] == 1"> selected</endif>>Aktif</option>
				<option value="0"<if criteria="$detailtype[status] == 0"> selected</endif>>Non-Aktif</option>
			</select></td>
		</tr>
		<tr>
			<td align="left">
			<input type="hidden" name="id" value="$_REQUEST[id]">
			<if criteria="!empty($_REQUEST[id])">
			<if criteria="$useraccess[edit_type]">
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
<if criteria="$useraccess[add_type]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'type.php?getlist=detail\',\'_self\')"></div></endif>
<div id="typebox" style="width: 100%; height:460px; background-color:white;"></div>
<script>
	var heightauto = (document.documentElement.clientHeight-50)+"px";
	$("#typebox").css("height",heightauto);
	mygrid = new dhtmlXGridObject("typebox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("Kode Tipe,Nama Tipe,Status,Update Terakhir,Oleh,Action,#cspan");
	mygrid.attachHeader("#text_filter,#text_filter,&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;");
	mygrid.setInitWidthsP("15,25,10,19,15,8,8");
	mygrid.enableAutoWidth(true);
	mygrid.setColAlign("left,left,center,center,left,center,center");
	mygrid.setColTypes("ro,ro,ro,ro,ro,link,link");
	mygrid.setColSorting("str,str,str,str,str,str,str");
	mygrid.setSkin("dhx_skyblue");
	mygrid.init();
	mygrid.enableSmartRendering(true);
	mygrid.enableRowsHover(true,"hover");
	mygrid.loadXML("type.php?getlist=xml");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		var idus = rId.indexOf("_");
		if (idus != -1){
			rId = rId.substr(0,idus);
		}
		window.open("type.php?getlist=detail&id="+rId,"_self");
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