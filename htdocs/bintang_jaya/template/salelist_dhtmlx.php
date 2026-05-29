<?php
$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Bintang Jaya</title>
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
	var arrcols = [4,5,6];
	function converting(){
		if (u == gridrows){
			u = 0;
		}
		else{
			var getrows = mygrid.getRowId(u);
			if (getrows.indexOf("_") == -1){
				if (arrcols.length > 0){
					for (var k = 0; k < arrcols.length; k++){
						var curvalue = mygrid.cells(getrows,arrcols[k]).getValue();
						if (doing == "deconvert"){
							mygrid.cells(getrows,arrcols[k]).setValue(formatnumber(deconvertcodes(curvalue)));
						}
						else{
							curvalue = replacestr(replacestr(curvalue,".",""),",",".");
							curvalue = curvalue.replace(".00","");
							mygrid.cells(getrows,arrcols[k]).setValue(convertcodes(curvalue));
						}
					}
				}
			}
			u++;
			setTimeout("converting()",1);
		}
	}
	
	shortcut.add("alt+r", function() {
		u = 0;
		gridrows = mygrid.getRowsNum();
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
			converting();
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
<div id="loadingprogress" style="display: none; position: fixed; z-index: 9999; top: 0px; left: 600px; padding: 5px; background-color: #000; color: #FFF; font-size: 14px; font-weight: bold">
Loading...</div>
<br>
<div align="left" style="padding: 3px">
Pencarian : <select id="searchfield">
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
<input type="text" id="keyword" size="50" autocomplete="off" onkeyup="loadajax(this,event)"></div>
<div id="salelistbox" style="width: 100%; height:460px; background-color:white;"></div>
<script>
	mygrid = new dhtmlXGridObject("salelistbox");
	mygrid.setImagePath("js/dhtmlxGrid/codebase/imgs/");
	mygrid.setHeader("No Faktur,Tanggal Jual,Jatuh Tempo,Nama Customer,Diskon,PPN,Total,Status,Action,#cspan");
	//mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;,&nbsp;");
	//mygrid.attachHeader("<input type=\'text\' id=\'saleno\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,<input type=\'text\' id=\'saledate\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,<input type=\'text\' id=\'duedate\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,<input type=\'text\' id=\'customername\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,<input type=\'text\' id=\'disc\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,<input type=\'text\' id=\'tax\' autocomplete=\'off\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,<input type=\'text\' autocomplete=\'off\' id=\'totalsale\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,<input type=\'text\' autocomplete=\'off\' id=\'status\' class=\'filterbox\' onkeyup=\'loadajax(this&#44;event)\'>,&nbsp;,&nbsp;");
	mygrid.setInitWidthsP("13,11,11,20,9,9,9,6,6,6");
	mygrid.enableAutoWidth(true);
	mygrid.setColAlign("left,center,center,left,right,right,right,center,center,center");
	mygrid.setColTypes("ro,dhxCalendar,dhxCalendar,ro,ro,ro,ro,ro,link,link");
	mygrid.setColSorting("str,date,date,str,str,str,str,str,str,str");
	mygrid.setSkin("dhx_skyblue");
	mygrid.init();
	mygrid.enableSmartRendering(true);
	mygrid.enableRowsHover(true,"hover");
	mygrid.setDateFormat("%d-%m-%Y");
	//mygrid.setNumberFormat("0,000.00",4,",",".");
	//mygrid.setNumberFormat("0,000.00",5,",",".");
	//mygrid.setNumberFormat("0,000.00",6,",",".");
	//mygrid.loadXML("sale.php?getlist=xml&list=general");
	mygrid.attachEvent("onRowDblClicked", function(rId,cInd){
		window.open(\'sale.php?no=\'+rId,\'_self\');
	});
	
	function loadajax(obj,event){
		if (!detectspecialkeys(event)){
			$("#loadingprogress").css("display","block");
			mygrid.clearAll();
			var getsearchfield = $("#searchfield :selected").val();
			mygrid.loadXML("sale.php?getlist=xml&list=general&keyword="+obj.value+"&field="+getsearchfield, function(){
				$("#loadingprogress").css("display","none");
			});
		}
	}
</script>
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>
</if>
<input type="hidden" id="defnumb" name="defnumb" value="$defnumb">
</body>

</html>
';
?>