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
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_skyblue.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxcombo.js"></script>
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
				url: \'user.php\',
				data: \'check=username&username=\'+objfrm.title.value+\'&id=\'+ids,
				success: function(data) {
					if (data){
						alert("Nama User sudah terdaftar dalam sistem. Silahkan masukkan nama user yang lain.");
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
	$(document).ready(function() {
		$(".formID").validationEngine();
	})
</script>
</head>

<body>
<if criteria="$_GET[getlist] == \'detail\' && ($useraccess[add_user] || ($useraccess[edit_user] && !empty($_REQUEST[id])))">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'user.php?getlist=detail\',\'_self\')">&nbsp;&nbsp;&nbsp;
<input type="button" value="Kembali" class="button" onclick="window.open(\'user.php\',\'_self\')"></div>
<fieldset>
	<legend><if criteria="!empty($_REQUEST[id])">Ubah User<else>Tambah User</if></legend>
	<div align="left">
		<form name="frmuser" id="frmuser" class="formID" method="post" action="user.php" onsubmit="return checktitleexist(this)">
		<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td align="left">Nama User</td>
			<td align="left">
			<input type="text" name="username" id="username" value="$detailuser[username]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Password</td>
			<td align="left">
			<input type="password" name="password" id="password" value=""<if criteria="empty($_REQUEST[id])"> class="validate[required]"</endif>></td>
		</tr>
		<tr>
			<td align="left">Password Tambahan</td>
			<td align="left">
			<input type="password" name="morepassword" id="morepassword" value=""></td>
		</tr>
		<tr>
			<td align="left">Nama</td>
			<td align="left">
			<input type="text" name="name" id="name" value="$detailuser[name]" class="validate[required]"></td>
		</tr>
		<tr>
			<td align="left">Grup User</td>
			<td align="left">
			<div id="ugel" style="width:100%; height:25px;"></div>
			<script type="text/javascript">
				var z = new dhtmlXCombo("ugel", "usergroupid", "100%");
				z.enableFilteringMode(true, "ajax.php?list=usergroup&type=xml", false);
				z.loadXML("ajax.php?list=usergroup&type=xml&id=$selug");
			</script></td>
		</tr>
		<tr>
			<td align="left">
			<if criteria="!empty($_REQUEST[id])">
			<input type="hidden" name="id" value="$_REQUEST[id]">
			<if criteria="$useraccess[edit_user]">
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
<if criteria="$useraccess[add_user]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'user.php?getlist=detail\',\'_self\')"></div></endif>
<div id="userbox" style="width: 100%; background-color:white;"></div>
<script>
	var heightauto = (document.documentElement.clientHeight-50)+"px";
	$("#userbox").css("height",heightauto);
	mygrid = new dhtmlXGridObject("userbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("ID User,Nama User,Nama Grup User,Nama,Update Terakhir,Oleh,Action,#cspan");
	mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;,&nbsp;,&nbsp;,&nbsp;");
	mygrid.setInitWidthsP("12,15,15,15,12,15,8,8");
	mygrid.enableAutoWidth(true);
	mygrid.setColAlign("left,left,left,left,center,left,center,center");
	mygrid.setColTypes("ro,ro,ro,ro,ro,ro,link,link");
	mygrid.setColSorting("str,str,str,str,str,str,str,str");
	mygrid.setSkin("dhx_skyblue");
	mygrid.init();
	mygrid.enableSmartRendering(true);
	mygrid.enableRowsHover(true,"hover");
	mygrid.loadXML("user.php?getlist=xml");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		var idus = rId.indexOf("_");
		if (idus != -1){
			rId = rId.substr(0,idus);
		}
		window.open("user.php?getlist=detail&id="+rId,"_self");
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