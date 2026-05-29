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
		var convvalue = parseFloat(replacestr(replacestr(objfrm.cvalue.value,".",""),",","."));
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else if (convvalue <= 0){
			alert("Nilai konversi harus lebih besar dari 0");
		}
		else{
			var ids = "";
			if (objfrm.id){
				ids = objfrm.id.value;
			}
			$.ajax({
				type: \'POST\',
				url: \'units.php\',
				data: \'check=code&unitcode=\'+objfrm.unitcode.value+\'&id=\'+ids,
				success: function(data) {
					if (data){
						alert("Kode satuan sudah ada dalam sistem. Silahkan masukkan kode satuan yang lain.");
						objfrm.unitcode.focus();
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
	
	//window.parent.loadiniframe("firststock.php","unit");
</script>
</head>

<body>
<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_units] || ($useraccess[edit_units] && !empty($_REQUEST[id])))">
<if criteria="!empty($errors)">
<div class="error">
<ul>
	<if criteria="$errors == \'samecode\'">
	<li>Kode Satuan sudah ada dalam sistem. Silahkan masukkan kode satuan yang lain.</li>
	</endif>
</ul></div><br>
</endif>
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'units.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
<input type="button" value="Kembali" class="button" onclick="window.open(\'units.php\',\'_self\')"></div>
<fieldset>
	<legend><if criteria="!empty($_REQUEST[id])">Ubah Satuan<else>Tambah Satuan</if></legend>
	<div align="left">
		<form name="frmunit" id="frmunit" class="formID" method="post" action="units.php" onsubmit="return checkcodeexist(this)">
		<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td align="left">Kode Satuan</td>
			<td align="left">
			<input type="text" name="unitcode" id="unitcode" value="$detailunit[unitcode]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<table border="0" cellpadding="2" cellspacing="2">
			<tr>
				<th align="center">Satuan Awal</th>
				<th align="center"></th>
				<th align="center">Nilai Konversi</th>
				<th align="center">Satuan Akhir</th>
			</tr>
			<tr>
				<td align="left">
				<input type="text" name="funit" id="funit" value="$detailunit[funit]" class="validate[required]"></td>
				<td align="center">=</td>
				<td align="left">
				<input type="text" name="cvalue" size="10" id="cvalue" value="$detailunit[cvalue]" class="validate[required]" style="text-align: right" onkeypress="return checknumber(event)"></td>
				<td align="left">
				<input type="text" name="lunit" id="lunit" value="$detailunit[lunit]" class="validate[required]"></td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td align="left">Status</td>
			<td align="left">
			<select name="unitstatus">
				<option value="1"<if criteria="$detailunit[status] == 1"> selected</endif>>Aktif</option>
				<option value="0"<if criteria="$detailunit[status] == 0"> selected</endif>>Non-Aktif</option>
			</select></td>
		</tr>
		<tr>
			<td align="left">
			<input type="hidden" name="id" value="$_REQUEST[id]">
			<if criteria="!empty($_REQUEST[id])">
			<if criteria="$useraccess[edit_units]">
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
<if criteria="$useraccess[add_units]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'units.php?getlist=detail\',\'_self\')"></div></endif>
<div id="unitsbox" style="width: 100%; height:460px; background-color:white;"></div>
<script>
	var heightauto = (document.documentElement.clientHeight-50)+"px";
	$("#unitsbox").css("height",heightauto);
	mygrid = new dhtmlXGridObject("unitsbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("Kode Satuan,Satuan Awal,Nilai,Satuan Akhir,Status,Update Terakhir,Oleh,Action,#cspan");
	mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;");
	mygrid.setInitWidthsP("12,10,8,10,10,19,15,8,8");
	mygrid.enableAutoWidth(true);
	mygrid.setColAlign("left,left,right,left,center,center,left,center,center");
	mygrid.setColTypes("ro,ro,ron,ro,ro,ro,ro,link,link");
	mygrid.setColSorting("str,str,int,str,str,str,str,str,str");
	mygrid.setSkin("dhx_skyblue");
	mygrid.init();
	mygrid.enableSmartRendering(true);
	mygrid.enableRowsHover(true,"hover");
	mygrid.setNumberFormat("0,000.00",2,",",".");
	mygrid.loadXML("units.php?getlist=xml");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		var idus = rId.indexOf("_");
		if (idus != -1){
			rId = rId.substr(0,idus);
		}
		window.open("units.php?getlist=detail&id="+rId,"_self");
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