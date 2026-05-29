<?php
$html = '
<if criteria="!empty($_REQUEST[id])">
	<div align="left"><b><u>Kartu Stok</u></b></div>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="left" width="50%" valign="top">
		<table border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td align="left">Kode Stok</td>
			<td align="left">: $firststock[stockcode]</td>
		</tr>
		<tr>
			<td align="left">Nama Umum</td>
			<td align="left">: $firststock[generalname]</td>
		</tr>
		<tr>
			<td align="left">Nama Standard</td>
			<td align="left">: $firststock[standardname]</td>
		</tr>
		<tr>
			<td align="left" valign="top">Nomor Part</td>
			<td align="left">
			$stprn</td>
		</tr>
		</table></td>
		<td align="left" width="50%" valign="top">
		<span style="float: right">
		<div align="right">
		<input type="button" class="button" value="Tutup" onclick="window.open(\'stock.php\',\'_self\')"></div>
		<br><span style="font-weight: bold; font-size: 20px">Total Aset : </span><span style="font-weight: bold; font-size: 20px" id="assetelement">$assetsf</span>
		</span>
		<table border="0" cellpadding="5" cellspacing="5">
		<tr>
			<td align="left">Grup Stok</td>
			<td align="left">: $firststock[stgrcode]</td>
		</tr>
		<tr>
			<td align="left">Merek</td>
			<td align="left">: $firststock[brandcode]</td>
		</tr>
		<tr>
			<td align="left">Tipe</td>
			<td align="left">: $firststock[typecode]</td>
		</tr>
		<tr>
			<td align="left">Ukuran</td>
			<td align="left">: $firststock[size]</td>
		</tr>
		<tr>
			<td align="left">Lokasi</td>
			<td align="left">: $firststock[locationcode]</td>
		</tr>
		</table></td>
	</tr>
	</table>
	<div id="stockbox" style="width: 99%; background-color:white;"></div>
	<script>
		var heightauto = (document.documentElement.clientHeight-200)+"px";
		$("#stockbox").css("height",heightauto);
		arrcols = [10,11];
	
		mygrid = new dhtmlXGridObject("stockbox");
		mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
		mygrid.setHeader("No,Tanggal,Part No,Nama Umum,Merek,Tipe,S / C,Masuk,Keluar,Sisa,Harga Beli,Harga Jual,Exp Date,Faktur,Keterangan");
		mygrid.attachHeader("&nbsp;,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		mygrid.setInitWidthsP("2,6,6,11,8,8,11,4,4,4,7,7,6,6,10");
		mygrid.enableAutoWidth(true);
		mygrid.setColAlign("right,center,left,left,left,left,left,right,right,right,right,right,center,left,left");
		mygrid.setColTypes("ron,dhxCalendar,ro,ro,ro,ro,ro,ron,ron,ron,ro,ro,dhxCalendar,ro,ro");
		mygrid.setColSorting("int,date,str,str,str,str,str,int,int,int,str,str,date,str,str");
		mygrid.setSkin("dhx_skyblue");
		mygrid.init();
		mygrid.enableRowspan(true);
		mygrid.enableSmartRendering(true);
		mygrid.enableRowsHover(true,"hover");
		mygrid.setDateFormat("%d-%m-%Y");
		mygrid.setNumberFormat("0,000",0,",",".");
		mygrid.setNumberFormat("0,000.00",7,",",".");
		mygrid.setNumberFormat("0,000.00",8,",",".");
		mygrid.setNumberFormat("0,000.00",9,",",".");
		mygrid.attachEvent("onRowDblClicked", function(rId,cInd){return false;});
		mygrid.loadXML("stock.php?getlist=xml&id=$_REQUEST[id]");
	</script>
<else>
<div align="left" style="border-bottom: 1px dotted #555; margin-bottom: 3px; padding-top: 3px; padding-bottom: 3px">
Cari Berdasarkan 
<select id="misc">
	<option value="stockgroup">Grup Stok</option>
</select> <b>:</b>
<select id="groupdesc" onchange="searchnow()">
	<option value=""></option>
	$allgroups
</select>
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()">
</div>
<script type="text/javascript">
	var headertext = [
		"No",
		"Kode Barang",
		"Nama Umum",
		"Merek",
		"Tipe",
		"Ukuran",
		"Lokasi",
		"Sisa",
		"Satuan",
		"Modal Min",
		"Modal Max",
		"Exp Date",
		"Nomor Part",
		""
	];
	var headerattach = [
		"",
		"<input type=\"text\" id=\"stockcode\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"generalname\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"brandname\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"typename\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"size\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"locationname\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"realremaining\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"unitname\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"buyminprice\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"buymaxprice\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"minexpdate\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"partno\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		""
	];
	var colwidth = [2,15,18,9,10,6,4,4,4,6,6,5,9,2];
	var percentwidth = 99;
	var realcolwidth = "";
	
	function ajaxsearch(keywords,fields,page){
		$.get("stock.php", {getlist: "xml", \'keyword[]\': keywords, \'field[]\': fields, status: "1", p: page, hdv: heightdiv, cwidth: realcolwidth}, function(data){
			$("#loadingprogress").css("display","none");
			$("#stufflist").html(data);
			$("#defnumb").val($("#defnumbdef").val());
			doing = "deconvert";
		});
	}
	
	function ajaxfromcookie(page){
		if (page == ""){
			page = 1;
		}
		
		$("#loadingprogress").css("display","block");
		
		var fieldstext = getCookie("fieldstuff");
		
		if (fieldstext != ""){
			var keywordstext = getCookie("keywordstuff");
			
			var keywords = keywordstext.split(",");
			var fields = fieldstext.split(",");
			
			var lf = fields.length;
			for (var i = 0; i < lf; i++){
				if (fields[i] == "stockgroup"){
					$("#groupdesc").val(keywords[i]);
				}
				else{
					$("#"+fields[i]).val(keywords[i]);
				}
			}
			ajaxsearch(keywords,fields,page);
		}
		else{
			$("#loadingprogress").css("display","none");
		}
	}
	
	function searchnow(){
		$("#loadingprogress").css("display","block");
				
		var keywords = new Array();
		var fields = new Array();
				
		var stcd = $("#stockcode").val();
		if (stcd != ""){
			keywords.push(stcd);
			fields.push("stockcode");
		}
		var gnm = $("#generalname").val();
		if (gnm  != ""){
			keywords.push(gnm);
			fields.push("generalname");
		}
		var brcd = $("#brandname").val();
		if (brcd != ""){
			keywords.push(brcd);
			fields.push("brandname");
		}
		var tycd = $("#typename").val();
		if (tycd != ""){
			keywords.push(tycd);
			fields.push("typename");
		}
		var sz = $("#size").val();
		if (sz != ""){
			keywords.push(sz);
			fields.push("size");
		}
		var lccd = $("#locationname").val();
		if (lccd != ""){
			keywords.push(lccd);
			fields.push("locationname");
		}
		var rrm = $("#realremaining").val();
		if (rrm != ""){
			keywords.push(rrm);
			fields.push("realremaining");
		}
		var uncd = $("#unitname").val();
		if (uncd != ""){
			keywords.push(uncd);
			fields.push("unitname");
		}
		var bmin = $("#buyminprice").val();
		if (bmin != ""){
			keywords.push(bmin);
			fields.push("buyminprice");
		}
		var bmax = $("#buymaxprice").val();
		if (bmax != ""){
			keywords.push(bmax);
			fields.push("buymaxprice");
		}
		var expd = $("#minexpdate").val();
		if (expd != ""){
			keywords.push(expd);
			fields.push("minexpdate");
		}
		var prtn = $("#partno").val();
		if (prtn != ""){
			keywords.push(prtn);
			fields.push("partno");
		}
		var misc = $("#misc :selected").val();
		var mkey = $("#groupdesc :selected").val();
		//var mkey = $("#misckeyword").val();
		//if (mkey != ""){
			keywords.push(mkey);
			fields.push(misc);
		//}
		
		setCookie("keywordstuff",keywords);
		setCookie("fieldstuff",fields);
		
		if (fields.length > 0){
			ajaxsearch(keywords,fields,1);
		}
		else{
			$("#loadingprogress").css("display","none");
		}
	}
	
	function loadajax(event){
		if (!detectspecialkeys(event)){
			var kn = getKeyEvent(event);
			if (kn == 13){
				searchnow();
			}
		}
	}
	function clearallsfield(){
		$("#stockcode").val("");
		$("#generalname").val("");
		$("#brandname").val("");
		$("#typename").val("");
		$("#size").val("");
		$("#locationname").val("");
		$("#realremaining").val("");
		$("#unitname").val("");
		$("#buyminprice").val("");
		$("#buymaxprice").val("");
		$("#minexpdate").val("");
		$("#partno").val("");
		//$("#misckeyword").val("");
		$("#stockcode").focus();
	}
	var heightdiv = document.documentElement.clientHeight-80;
</script>
<script type="text/javascript" src="js/maketable.js"></script>
<script type="text/javascript">
	ajaxfromcookie();
</script>
</if>
';
?>