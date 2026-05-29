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
	function checktitleexist(objfrm){
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
				url: \'usergroup.php\',
				data: \'check=title&title=\'+objfrm.title.value+\'&id=\'+ids,
				success: function(data) {
					if (data){
						alert("Nama Grup User sudah terdaftar dalam sistem. Silahkan masukkan nama grup user yang lain.");
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
		return false;
	}
	
	function togglechkbox(accessid,thetrigger){
		var A = accessid.split(",");
		for (var i = 0; i < A.length; i++){
			document.getElementById("id_"+A[i]).checked = thetrigger.checked;
		}
	}
	
	function scanchkbox(accessid,thetrigger){
		var A = accessid.split(",");
		var B = true;
		for (var i = 0; i < A.length; i++){
			if (!document.getElementById("id_"+A[i]).checked){
				B = false;
				break;
			}
		}
		if (B){
			document.getElementById(thetrigger).checked = true;
		}
		else{
			document.getElementById(thetrigger).checked = false;
		}
	}

	$(document).ready(function() {
		$(".formID").validationEngine();
	})
</script>
</head>

<body>
<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_usergroup] || ($useraccess[edit_usergroup] && !empty($_REQUEST[id])))">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'usergroup.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
<input type="button" value="Kembali" class="button" onclick="window.open(\'usergroup.php\',\'_self\')"></div>
<fieldset>
	<legend><if criteria="!empty($_REQUEST[id])">Ubah Grup User<else>Tambah Grup User</if></legend>
	<div align="left">
		<form name="frmusergroup" id="frmusergroup" class="formID" method="post" action="usergroup.php" onsubmit="return checktitleexist(this)">
		<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td align="left" width="150">Nama Grup User</td>
			<td align="left">
			<input type="text" name="title" id="title" value="$detailusergroup[title]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left" width="150">Status</td>
			<td align="left">
			<select name="usergroupstatus">
				<option value="1"<if criteria="$detailusergroup[status] == 1"> selected</endif>>Aktif</option>
				<option value="0"<if criteria="$detailusergroup[status] == 0"> selected</endif>>Non-Aktif</option>
			</select></td>
		</tr>
		<tr>
			<td colspan="2" align="left">
			Hak Akses : </td>
		</tr>
		<tr>
			<td colspan="2" align="left">
			<table border="0" cellpadding="0" cellspacing="0">
			$accesslist
			</table></td>
		</tr>
		<tr>
			<td align="left">
			<if criteria="!empty($_REQUEST[id])">
			<input type="hidden" name="id" value="$_REQUEST[id]">
			<if criteria="$useraccess[edit_usergroup]">
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
<if criteria="$useraccess[add_usergroup]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'usergroup.php?getlist=detail\',\'_self\')"></div></endif>
<div id="usergroupbox" style="width: 100%; background-color:white;"></div>
<script>
	var heightauto = (document.documentElement.clientHeight-50)+"px";
	$("#usergroupbox").css("height",heightauto);
	mygrid = new dhtmlXGridObject("usergroupbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("ID Grup User,Nama Grup User,Status,Update Terakhir,Oleh,Action,#cspan");
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
	mygrid.loadXML("usergroup.php?getlist=xml");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		var idus = rId.indexOf("_");
		if (idus != -1){
			rId = rId.substr(0,idus);
		}
		window.open("usergroup.php?getlist=detail&id="+rId,"_self");
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