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
<script src="js/assembly.js"></script>
<script type="text/javascript">
	function checkcodeexist(objfrm){
		if (!$(".formID").validationEngine("validate")){
			alert("Ada field yang belum diisi.");
		}
		else{
			if (mygrid.getRowsNum() == 0){
				alert("Tidak ada komponen perakitan");
			}
			else{
				var ids = "";
				if (objfrm.id){
					ids = objfrm.id.value;
				}
				$.ajax({
					type: \'POST\',
					url: \'assembly.php\',
					data: \'check=code&code=\'+objfrm.stockcode.value+\'&id=\'+ids,
					success: function(data) {
						if (data){
							alert("Kode stok sudah ada dalam sistem. Silahkan masukkan kode stok yang lain.");
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
		}
		return false;
	}
	$(document).ready(function() {
		$(".formID").validationEngine();
	})
</script>
</head>

<body>
<if criteria="!empty($_REQUEST[id]) && $useraccess[add_assembly]">
<div align="right">
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'assembly.php\',\'_self\')"></div></endif>
<if criteria="!empty($errors)">
<div class="error">
<ul>
	<if criteria="$errors == \'samecode\'">
	<li>Kode Stok sudah ada dalam sistem. Silahkan masukkan kode stok yang lain.</li>
	</endif>
</ul></div><br>
</endif>
<div align="left">
	<form name="frmassembly" id="frmassembly" class="formID" method="post" action="assembly.php" onsubmit="return checkcodeexist(this)">
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
			<td align="left">
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
			<input type="text" name="buyprice" id="buyprice" value="$detailstock[buyprice]" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="insertingfs(this)"></td>
		</tr>
		<tr>
			<td align="left">Harga Jual</td>
			<td align="left">
			<input type="text" name="sellprice" id="sellprice" value="$detailstock[sellprice]" class="validate[required]" onkeypress="return checknumber(event)" onkeyup="insertingfs(this)"></td>
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
		<td align="center" colspan="2">
		<fieldset>
			<legend>Komponen</legend>
			<div align="left">
				<table border="0" cellpadding="3" cellspacing="3">
				<tr>
					<td align="left" class="tdhover" onclick="adddetailassembly()"><img src="img/add.png" border="0">&nbsp;&nbsp;<b>Tambah</b></td>
					<td align="left" class="tdhover" onclick="deletedetailassembly()"><img src="img/delete.png" border="0">&nbsp;&nbsp;<b>Hapus</b></td>
				</tr>
				</table>
			</div>
			<div id="assemblybox" style="width: 100%; height:200px; background-color:white;"></div>
			<script>
				mygrid = new dhtmlXGridObject("assemblybox");
				mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
				mygrid.enableAutoWidth(true);
				mygrid.setColSorting("str,str,str,str,str,str,str");
				mygrid.setSkin("dhx_skyblue");
				mygrid.init();
				mygrid.attachEvent("onEditCell", editingassembly);
				mygrid.enableSmartRendering(true);
				mygrid.enableRowsHover(true,"hover");
				mygrid.submitOnlyChanged(false);
				mygrid.loadXML("assembly.php?getlist=xml&list=detail&id=$_REQUEST[id]");
			</script>
		</fieldset></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
		<br>
		<input type="hidden" name="id" value="$_REQUEST[id]">
		<if criteria="!empty($_REQUEST[id])">
		<if criteria="$useraccess[edit_assembly]">
		<input type="hidden" name="submits" value="Ubah">
		<input type="submit" value="Ubah" class="button">&nbsp;&nbsp;&nbsp;</endif>
		<if criteria="$_GET[from] == \'firststock\'">
		<input type="button" value="Kembali" class="button" onclick="window.open(\'firststock.php\',\'_self\')">
		<else>
		<input type="button" value="Kembali" class="button" onclick="window.open(\'assembly.php?screen=list\',\'_self\')">
		</if>
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