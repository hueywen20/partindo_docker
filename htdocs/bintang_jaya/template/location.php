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
				url: \'location.php\',
				data: \'check=code&locationcode=\'+objfrm.locationcode.value+\'&id=\'+ids,
				success: function(data) {
					if (data){
						alert("Kode lokasi sudah ada dalam sistem. Silahkan masukkan kode lokasi yang lain.");
						objfrm.locationcode.focus();
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
	
	//window.parent.loadiniframe("firststock.php","location");
</script>
</head>

<body>
<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_location] || ($useraccess[edit_location] && !empty($_REQUEST[id])))">
<if criteria="!empty($errors)">
<div class="error">
<ul>
	<if criteria="$errors == \'samecode\'">
	<li>Kode lokasi sudah ada dalam sistem. Silahkan masukkan kode lokasi yang lain.</li>
	</endif>
</ul></div><br>
</endif>
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'location.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
<input type="button" value="Kembali" class="button" onclick="window.open(\'location.php\',\'_self\')"></div>
<fieldset>
	<legend><if criteria="!empty($_REQUEST[id])">Ubah Lokasi<else>Tambah Lokasi</if></legend>
	<div align="left">
		<form name="frmlocation" id="frmlocation" class="formID" method="post" action="location.php" onsubmit="return checkcodeexist(this)">
		<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td align="left">Kode Lokasi</td>
			<td align="left">
			<input type="text" name="locationcode" id="locationcode" value="$detaillocation[locationcode]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Nama Lokasi</td>
			<td align="left">
			<input type="text" name="locationname" id="locationname" value="$detaillocation[locationname]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Status</td>
			<td align="left">
			<select name="locationstatus">
				<option value="1"<if criteria="$detaillocation[status] == 1"> selected</endif>>Aktif</option>
				<option value="0"<if criteria="$detaillocation[status] == 0"> selected</endif>>Non-Aktif</option>
			</select></td>
		</tr>
		<tr>
			<td align="left">
			<input type="hidden" name="id" value="$_REQUEST[id]">
			<if criteria="!empty($_REQUEST[id])">
			<if criteria="$useraccess[edit_location]">
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
<if criteria="$useraccess[add_location]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'location.php?getlist=detail\',\'_self\')"></div></endif>
<div id="locationbox" style="width: 100%; background-color:white;"></div>
<script>
	var heightauto = (document.documentElement.clientHeight-50)+"px";
	$("#locationbox").css("height",heightauto);
	mygrid = new dhtmlXGridObject("locationbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("Kode Lokasi,Nama Lokasi,Status,Update Terakhir,Oleh,Action,#cspan");
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
	mygrid.loadXML("location.php?getlist=xml");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		var idus = rId.indexOf("_");
		if (idus != -1){
			rId = rId.substr(0,idus);
		}
		window.open("location.php?getlist=detail&id="+rId,"_self");
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