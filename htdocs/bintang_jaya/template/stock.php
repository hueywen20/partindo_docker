<?php
$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname]</title>
$headinclude
<script src="js/shortcut.js"></script>
<script type="text/javascript">
	function searchStock(kw,fd){
		$.ajax({
			type: \'POST\',
			url: \'stock.php\',
			data: \'getlist=ajax&keyword=\'+kw+\'&field=\'+fd,
			success: function(data) {
				$("#ajaxstocklist").html(data);
			}
		})
	}
	
	var u;
	var gridrows;
	var doing;
	var arrcols = new Array();
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
			else{
				var curvalue = mygrid.cells(getrows,arrcols[0]).getValue();
				if (doing == "deconvert"){
					mygrid.cells(getrows,arrcols[0]).setValue(formatnumber(deconvertcodes(curvalue)));
				}
				else{
					curvalue = replacestr(replacestr(curvalue,".",""),",",".");
					curvalue = curvalue.replace(".00","");
					mygrid.cells(getrows,arrcols[0]).setValue(convertcodes(curvalue));
				}
			}
			u++;
			setTimeout("converting()",1);
		}
	}
	
	function convertingnogrid(){
		if (u == gridrows){
			u = 0;
		}
		else{
			var minvalue = $("#minprice_"+(u+1));
			var maxvalue = $("#maxprice_"+(u+1));
			if (doing == "deconvert"){
				minvalue.html(formatnumber(deconvertcodes(minvalue.html())));
				maxvalue.html(formatnumber(deconvertcodes(maxvalue.html())));
			}
			else{
				curvaluemin = replacestr(replacestr(minvalue.html(),".",""),",",".");
				curvaluemin = curvaluemin.replace(".00","");
				minvalue.html(convertcodes(curvaluemin));
				
				curvaluemax = replacestr(replacestr(maxvalue.html(),".",""),",",".");
				curvaluemax = curvaluemax.replace(".00","");
				maxvalue.html(convertcodes(curvaluemax));
			}
			u++;
			setTimeout("convertingnogrid()",1);
		}
	}
	
	<if criteria="!empty($_REQUEST[id])">
	shortcut.add("alt+r", function() {
		u = 0;
		gridrows = mygrid.getRowsNum();
		var views = $("#defnumb");
		var assetelement = $("#assetelement").html();
		if (gridrows > 0){
			if (views.val() == "code"){
				views.val("number");
				doing = "deconvert";
				$("#assetelement").html(formatnumber(deconvertcodes(assetelement)));
			}
			else{
				views.val("code");
				doing = "convert";
				assetelement = replacestr(replacestr(assetelement,".",""),",",".");
				assetelement = assetelement.replace(".00","");
				$("#assetelement").html(convertcodes(assetelement));
			}			
			converting();
		}
	});
	<else>
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
	</if>
</script>
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/dhtmlxgrid.css">
<link rel="stylesheet" type="text/css" href="js/dhtmlxGrid/codebase/skins/dhtmlxgrid_dhx_skyblue.css">
<script src="js/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="js/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="js/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_rowspan.js"></script>
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_pgn.js"></script>   
<script src="js/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>    
<script src="js/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src="js/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_dhxcalendar.js"></script>
</head>

<body marginwidth="1" marginheight="0">
$stocklist
<script>
$(document).click(function() {
	window.parent.closeall(1);
});
</script>
<input type="hidden" id="defnumbdef" name="defnumbdef" value="$defnumb">
<input type="hidden" id="defnumb" name="defnumb" value="$defnumb">
</body>

</html>
';
?>