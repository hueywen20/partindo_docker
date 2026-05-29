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
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>    
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js"></script>
<script src="js/shortcut.js"></script>
<script type="text/javascript">
	var u;
	var gridrows;
	var doing;

	function convertspan(rowid){
		var ts = 0;
		var price = $("#price_"+rowid+"-"+ts);
		if (price.length > 0){
			var rspan = true;
			while (rspan){
				curvalueprice = replacestr(replacestr(price.html(),".",""),",",".");
				curvalueprice = curvalueprice.replace(".00","");
				price.html(convertcodes(curvalueprice));
				ts++;
				price = $("#price_"+rowid+"-"+ts);
				if (price.length == 0){
					rspan = false;
				}
			}
		}
	}

	function deconvertspan(rowid){
		var ts = 0;
		var price = $("#price_"+rowid+"-"+ts);
		if (price.length > 0){
			var rspan = true;
			while (rspan){
				price.html(formatnumber(deconvertcodes(price.html())));
				ts++;
				price = $("#price_"+rowid+"-"+ts);
				if (price.length == 0){
					rspan = false;
				}
			}
		}
	}
	
	function convertingnogrid(){
		if (u == gridrows){
			u = 0;
		}
		else{
			var total = $("#total_"+(u+1));
			if (doing == "deconvert"){
				total.html(formatnumber(deconvertcodes(total.html())));
				deconvertspan((u+1));
			}
			else{
				curvaluetotal = replacestr(replacestr(total.html(),".",""),",",".");
				curvaluetotal = curvaluetotal.replace(".00","");
				total.html(convertcodes(curvaluetotal));
				convertspan((u+1));
			}
			u++;
			setTimeout("convertingnogrid()",1);
		}
	}
	
	shortcut.add("alt+r", function() {
		u = 0;
		gridrows = $("#totalstuffrow").val();
		var views = $("#defnumb");
		if (gridrows > 0){
			if (views.val() == "code"){
				views.val("number");
				doing = "deconvert";
			}
			else{
				views.val("code");
				doing = "convert";
			}			
			convertingnogrid();
		}
	});
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<div align="left" style="padding: 3px">
Pencarian : <select id="searchfield">
	<option value="salerid">Nomor Retur Penjualan</option>
	<option value="salerdate">Tanggal Retur Penjualan</option>
	<option value="customername">Nama Customer</option>
	<option value="saleno">No Faktur</option>
	<option value="stockcode">Kode Barang</option>
	<option value="partno">Part No</option>
	<option value="stockname">Nama Barang</option>
	<option value="brandcode">Merek</option>
	<option value="typecode">Tipe</option>
</select>&nbsp;
<input type="text" id="keyword" size="50" autocomplete="off" onkeyup="loadajax(this,event)">
<span style="display: none" id="loadingprogresstop">
<img src="img/loading.gif" border="0" width="16" height="16"></span></div>
<script>
	var headertext = [
		"No Retur",
		"Tanggal Retur",
		"Nama Customer",
		"No. Faktur",
		"Kode Barang",
		"No. Part",
		"Nama Barang",
		"Merek",
		"Tipe",
		"Qty",
		"Satuan",
		"Harga",
		"Total",
		"Action"
	];
	var headerattach = [];
	var percentwidth = 135;
	var colwidth = [5,7,10,6,9,8,10,8,8,5,4,8,8,8];
	var realcolwidth = "";
	
	function ajaxsearch(keywords,fields,page){
		$.get("saler.php", {getlist: "xml", list: "general", keyword: keywords, p: page, hdv: heightdiv, field: fields, cwidth: realcolwidth}, function(data){
			$("#loadingprogress").css("display","none");
			$("#loadingprogresstop").css("display","none");
			$("#stufflist").html(data);
			$("#defnumb").val($("#defnumbdef").val());
		});
	}
	
	function ajaxfromcookie(page){
		if (page == ""){
			page = 1;
		}
		
		$("#loadingprogress").css("display","block");
		$("#loadingprogresstop").css("display","inline-block");
		
		var fields = getCookie("fieldsaler");
		
		var keywords = getCookie("keywordsaler");
		
		$("#searchfield").val(fields);
		$("#keyword").val(keywords);
		ajaxsearch(keywords,fields,page);
	}
	
	function searchnow(){
		$("#loadingprogress").css("display","block");
		$("#loadingprogresstop").css("display","inline-block");
				
		var keywords = $("#keyword").val();
		var fields = $("#searchfield :selected").val();
		
		setCookie("keywordsaler",keywords);
		setCookie("fieldsaler",fields);
		
		ajaxsearch(keywords,fields,1);
	}
	
	function loadajax(obj,event){
		if (!detectspecialkeys(event)){
			var kn = getKeyEvent(event);
			if (kn == 13){
				searchnow();
			}
		}
	}
	var heightdiv = document.documentElement.clientHeight-80;
</script>
<script type="text/javascript" src="js/maketable.js"></script>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});

ajaxfromcookie();
</script>
<input type="hidden" id="defnumbdef" name="defnumbdef" value="$defnumb">
<input type="hidden" id="defnumb" name="defnumb" value="$defnumb">
</body>

</html>
';
?>