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
		var saleprice = $("#saleprice_"+rowid+"-"+ts);
		if (saleprice.length > 0){
			var rspan = true;
			while (rspan){
				curvaluesaleprice = replacestr(replacestr(saleprice.html(),".",""),",",".");
				curvaluesaleprice = curvaluesaleprice.replace(".00","");
				saleprice.html(convertcodes(curvaluesaleprice));
				ts++;
				saleprice = $("#saleprice_"+rowid+"-"+ts);
				if (saleprice.length == 0){
					rspan = false;
				}
			}
		}
	}

	function deconvertspan(rowid){
		var ts = 0;
		var saleprice = $("#saleprice_"+rowid+"-"+ts);
		if (saleprice.length > 0){
			var rspan = true;
			while (rspan){
				saleprice.html(formatnumber(deconvertcodes(saleprice.html())));
				ts++;
				saleprice = $("#saleprice_"+rowid+"-"+ts);
				if (saleprice.length == 0){
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
<if criteria="$_GET[getlist] == determine">
<div id="salelistbox" style="width: 100%; height:500px; background-color:white;"></div>
<script>
	mygrid = new dhtmlXGridObject("salelistbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("No Faktur,Tanggal Jual,Jatuh Tempo,Nama Customer,Diskon,PPN,Total");
	mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
	mygrid.setInitWidthsP("15,12,12,25,12,12,12");
	mygrid.enableAutoWidth(true);
	mygrid.setColAlign("left,center,center,left,right,right,right");
	mygrid.setColTypes("ro,dhxCalendar,dhxCalendar,ro,ron,ron,ron");
	mygrid.setColSorting("str,date,date,str,int,int,int");
	mygrid.setSkin("dhx_skyblue");
	mygrid.init();
	mygrid.enableSmartRendering(true);
	mygrid.enableRowsHover(true,"hover");
	mygrid.setDateFormat("%d-%m-%Y");
	mygrid.setNumberFormat("0,000.00",4,",",".");
	mygrid.setNumberFormat("0,000.00",5,",",".");
	mygrid.setNumberFormat("0,000.00",6,",",".");
	mygrid.loadXML("sale.php?getlist=xml&list=determine");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		window.opener.setSale(mygrid.cells(rId,0).getValue(),mygrid.cells(rId,1).getValue());
		window.close();
	});
</script>
<else>
<div align="left" style="padding: 3px">
Pencarian :
<select id="trtype" onchange="searchnow()">
	<option value="">Semua<ioption>
	<option value="cash">Tunai<ioption>
	<option value="credit">Kredit<ioption>
</select>
-
<select id="searchfield">
	<option value="saleno">Nomor Faktur Penjualan</option>
	<option value="saledate">Tanggal Penjualan</option>
	<option value="duedate">Tanggal Jatuh Tempo</option>
	<option value="customername">Nama Customer</option>
	<option value="stockcode">Kode Barang</option>
	<option value="partno">Part No</option>
	<option value="stockname">Nama Barang</option>
	<option value="brandcode">Merek</option>
	<option value="typecode">Tipe</option>
	<option value="status">Status</option>
</select>&nbsp;
<input type="text" id="keyword" size="50" autocomplete="off" onkeyup="loadajax(this,event)">
<span style="display: none" id="loadingprogresstop">
<img src="img/loading.gif" border="0" width="16" height="16"></span></div>
<script>
	var headertext = [
		"No Faktur",
		"Tanggal Jual",
		"Jatuh Tempo",
		"Nama Customer",
		"Kode Barang",
		"No. Part",
		"Nama Barang",
		"Merek",
		"Tipe",
		"Qty",
		"Satuan",
		"Harga",
		"Total",
		"Status",
		"Action"
	];
	var headerattach = [];
	var percentwidth = 135;
	var colwidth = [5,6,6,9,9,8,8,8,8,5,4,6,7,5,6];
	var realcolwidth = "";
	
	function ajaxsearch(keywords,fields,trtypes,page){
		$.get("sale.php", {getlist: "xml", list: "general", keyword: keywords, p: page, hdv: heightdiv, field: fields, trtype: trtypes, cwidth: realcolwidth}, function(data){
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
		
		var fields = getCookie("fieldsale");
		
		/* if (fields != ""){ */
			var keywords = getCookie("keywordsale");
			var trtype = getCookie("trtypesale");
			
			$("#trtype").val(trtype);
			$("#searchfield").val(fields);
			$("#keyword").val(keywords);
			ajaxsearch(keywords,fields,trtype,page);
		/* }
		else{
			$("#loadingprogress").css("display","none");
		} */
	}
	
	function searchnow(){
		$("#loadingprogress").css("display","block");
		$("#loadingprogresstop").css("display","inline-block");
				
		var keywords = $("#keyword").val();
		var fields = $("#searchfield :selected").val();
		var trtype = $("#trtype :selected").val();
		
		setCookie("keywordsale",keywords);
		setCookie("fieldsale",fields);
		setCookie("trtypesale",trtype);
		
		ajaxsearch(keywords,fields,trtype,1);
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
</if>
<input type="hidden" id="defnumbdef" name="defnumbdef" value="$defnumb">
<input type="hidden" id="defnumb" name="defnumb" value="$defnumb">
</body>

</html>
';
?>