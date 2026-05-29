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
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/dhtmlxcalendar.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_vista.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>    
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/shortcut.js"></script>
<script type="text/javascript">
	var u;
	var gridrows;
	var doing;

	function convertspan(rowid){
		var ts = 0;
		var buyprice = $("#paymentprice_"+rowid+"-"+ts);
		if (buyprice.length > 0){
			var rspan = true;
			while (rspan){
				curvaluebuyprice = replacestr(replacestr(buyprice.html(),".",""),",",".");
				curvaluebuyprice = curvaluebuyprice.replace(".00","");
				buyprice.html(convertcodes(curvaluebuyprice));
				ts++;
				buyprice = $("#paymentprice_"+rowid+"-"+ts);
				if (buyprice.length == 0){
					rspan = false;
				}
			}
		}
	}

	function deconvertspan(rowid){
		var ts = 0;
		var buyprice = $("#paymentprice_"+rowid+"-"+ts);
		if (buyprice.length > 0){
			var rspan = true;
			while (rspan){
				buyprice.html(formatnumber(deconvertcodes(buyprice.html())));
				ts++;
				buyprice = $("#paymentprice_"+rowid+"-"+ts);
				if (buyprice.length == 0){
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
<div align="left" style="padding: 3px; border-bottom: 1px dotted #333; width: 100%">
<form action="paydebt.php" method="post" onsubmit="return confirm(\'OK?\')">
<b>Jalankan auto rekap pembayaran hutang sekarang : </b>
<input type="hidden" name="submits" value="All">
<input type="submit" name="submit" value="Go" class="button">
</form></div>
<div align="left" style="padding: 3px">
Pencarian : <select id="searchfield">
	<option value="orderno">Nomor Bon Pembelian</option>
	<option value="buyrid">Nomor Retur Pembelian</option>
	<option value="suppliername">Nama Supplier</option>
</select>&nbsp;
<input type="text" id="keyword" size="50" autocomplete="off" onkeyup="loadajax(this,event)">
- Periode : 
<input type="text" name="startpaysdate" id="startpaysdate" value="$_GET[startpaysdate]"> - 
<input type="text" name="endpaysdate" id="endpaysdate" value="$_GET[endpaysdate]">
<input type="button" value="Go" class="button" onclick="goajax(document.getElementById(\'keyword\'))">
<span style="display: none" id="loadingprogresstop">
<img src="img/loading.gif" border="0" width="16" height="16"></span></div>
<script>
	var headertext = [
		"No Pembayaran",
		"Tanggal Pembayaran",
		"Nama Supplier",
		"No Bon Pembelian / Retur",
		"No Faktur Penjualan / Retur",
		"Total Pembelian / Retur",
		"Total Pembayaran",
		"Status",
		"Action"
	];
	var headerattach = [];
	var percentwidth = 100;
	var colwidth = [8,8,15,13,13,13,12,8,10];
	var realcolwidth = "";
	
	function ajaxsearch(getsearchfield,getstartpaysdate,getendpaysdate){
		var keyw = $("#keyword").val();
		$.get("paydebt.php", {getlist: "xml", list: "general", keyword: keyw, field: getsearchfield, startpaysdate: getstartpaysdate, endpaysdate: getendpaysdate, cwidth: realcolwidth}, function(data){
			$("#loadingprogress").css("display","none");
			$("#loadingprogresstop").css("display","none");
			$("#stufflist").html(data);
			$("#defnumb").val($("#defnumbdef").val());
		});
	}
	
	function ajaxfromcookie(){
		$("#loadingprogress").css("display","block");
		
		var getsearchfield = getCookie("searchfieldpayd");
		var getstartpaysdate = getCookie("startpayddate");
		var getendpaysdate = getCookie("endpayddate");
		$("#keyword").val(getCookie("searchfieldkeywd"));
		$("#startpaysdate").val(getstartpaysdate);
		$("#endpaysdate").val(getendpaysdate);
		$("#searchfield").val(getsearchfield);
		
		ajaxsearch(getsearchfield,getstartpaysdate,getendpaysdate);		
	}
	
	function goajax(obj){
		$("#loadingprogress").css("display","block");
		$("#loadingprogresstop").css("display","inline-block");
		var getsearchfield = $("#searchfield :selected").val();
		var getstartpaysdate = $("#startpaysdate").val();
		var getendpaysdate = $("#endpaysdate").val();
		
		setCookie("searchfieldkeywd",obj.value);
		setCookie("searchfieldpayd",getsearchfield);
		setCookie("startpayddate",getstartpaysdate);
		setCookie("endpayddate",getendpaysdate);
		
		ajaxsearch(getsearchfield,getstartpaysdate,getendpaysdate);
	}
	
	function loadajax(obj,event){
		if (!detectspecialkeys(event)){
			var kn = getKeyEvent(event);
			if (kn == 13){
				goajax(obj);
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

    calf = new dhtmlxCalendarObject("startpaysdate",true,{
		isWinHeader: true,
        isWinDrag: true,
		isYearEditable: true,
		isMonthEditable: true
	});
	calf.setDateFormat("%d-%m-%Y");
	calf.setSkin("vista");
	
    calef = new dhtmlxCalendarObject("endpaysdate",true,{
		isWinHeader: true,
        isWinDrag: true,
		isYearEditable: true,
		isMonthEditable: true
	});
	calef.setDateFormat("%d-%m-%Y");
	calef.setSkin("vista");
	
	<if criteria="!empty($_GET[startpaysdate]) || !empty($_GET[endpaysdate])">
		goajax(document.getElementById(\'keyword\'));
	<else>
		ajaxfromcookie();
	</if>
</script>
<input type="hidden" id="defnumbdef" name="defnumbdef" value="$defnumb">
<input type="hidden" id="defnumb" name="defnumb" value="$defnumb">
</body>
</html>
';
?>