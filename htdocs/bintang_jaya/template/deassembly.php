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
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_form.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_keymap_excel.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_combo.js"></script>
<script src="js/shortcut.js"></script>
<script src="js/gridf.js"></script>
<script src="js/deassembly.js"></script>
<script type="text/javascript">
	function checkform(objfrm){
		if (mygrid.getRowsNum() == 0){
			alert("Tidak ada komponen pecahan");
		}
		else{
			var ids = "";
			if (objfrm.id){
				ids = objfrm.id.value;
			}
			$.ajax({
				type: \'POST\',
				url: \'deassembly.php\',
				data: \'check=code&stockcode=\'+objfrm.stockcode.value+\'&id=\'+ids,
				success: function(data) {
					if (data){
						alert("Kode barang ini telah dipecah. Silahkan masukkan kode barang yang lain.");
						objfrm.stockcode.focus();
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
</script>
</head>

<body>
<if criteria="!empty($_REQUEST[id]) && $useraccess[add_deassembly]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'deassembly.php\',\'_self\')"></div></endif>
<div align="left">
	<form name="frmdeassembly" id="frmdeassembly" class="formID" method="post" action="deassembly.php" onsubmit="return checkform(this)">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left" width="200">Kode Barang</td>
		<td align="left">
		<div id="stockel" style="width: 300px; height:25px;"></div>
		<script type="text/javascript">
			var z = new dhtmlXCombo("stockel", "stockcode", "100%");
			z.enableFilteringMode(true, "ajax.php?list=firststock&type=xml", false);
			z.loadXML("ajax.php?list=firststock&type=xml&id=$detailstock[stockcode]");
		</script></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
		<fieldset>
			<legend>Komponen Pecahan</legend>
			<div align="left">
				<table border="0" cellpadding="3" cellspacing="3">
				<tr>
					<td align="left" class="tdhover" onclick="adddetaildeassembly()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
					<td align="left" class="tdhover" onclick="deletedetaildeassembly()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
				</tr>
				</table>
			</div>
			<div id="deassemblybox" style="width: 100%; height:200px; background-color:white;"></div>
			<script>
				mygrid = new dhtmlXGridObject("deassemblybox");
				mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
				mygrid.enableAutoWidth(true);
				mygrid.setColSorting("str,str,str,str,str,str,str,str");
				mygrid.setSkin("dhx_skyblue");
				mygrid.init();
				mygrid.attachEvent("onEditCell", editingdeassembly);
				mygrid.enableSmartRendering(true);
				mygrid.enableRowsHover(true,"hover");
				mygrid.submitOnlyChanged(false);
				mygrid.loadXML("deassembly.php?getlist=xml&list=detail&id=$_REQUEST[id]");
			</script>
		</fieldset></td>
	</tr>
	<tr>
		<td height="5" align="center" colspan="2"></td>
	</tr>
	<tr>
		<td align="left" width="200">Langsung Dipecah ?</td>
		<td align="left">
		<script type="text/javascript">
			function showhidedeascount(vals){
				if (vals == "no"){
					$("#deascountelement").css("display","none");
				}
				else if (vals == "yes"){
					$("#deascountelement").css("display","inline-block");
				}
			}
		</script>
		<select name="directs" onchange="showhidedeascount(this.value)">
			<option value="no">Tidak</option>
			<option value="yes">Ya</option>
		</select>
		<span id="deascountelement" style="display: none">
		<input type="text" name="deascount" id="deascount" value="1" size="10"> item</span></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
		<br>
		<input type="hidden" name="id" value="$_REQUEST[id]">
		<if criteria="!empty($_REQUEST[id])">
		<if criteria="$useraccess[edit_deassembly]">
		<input type="hidden" name="submits" value="Ubah">
		<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;</endif>
		<input type="button" value="Kembali" class="button" onclick="window.open(\'deassembly.php?screen=list\',\'_self\')">
		<else>
		<input type="hidden" name="submits" value="Tambah">
		<input type="submit" value="Tambah" class="button">
		</if></td>
	</tr>
	</table>
	</form>
</div>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>

</body>

</html>
';
?>