<?php
$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname]</title>
$headinclude
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<if criteria="$useraccess[add_assembly]">
<div align="right" style="padding: 3px">
<span style="float: left">
<input type="button" value="Hapus Semua Field Pencarian" class="button" onclick="clearallsfield()"></span>
<input type="button" value="Tambah Baru" class="button" onclick="window.open(\'assembly.php\',\'_self\')"></div></endif>
<script type="text/javascript">
	var headertext = [
		"Kode Barang",
		"Nama Barang",
		"Kode Komponen",
		"Nama Komponen",
		"",
		""
	];
	var headerattach = [
		"<input type=\"text\" id=\"stockcode\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"generalname\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"componentcode\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"<input type=\"text\" id=\"componentname\" class=\"filterbox\" autocomplete=\"off\" onkeyup=\"loadajax(event)\">",
		"",
		""
	];
	var colwidth = [15,23,15,23,10,10];
	var percentwidth = 100;
	var realcolwidth = "";
	
	function ajaxsearch(keywords,fields,page){
		$.get("assembly.php", {getlist: "xml", \'list\': "general", \'keyword[]\': keywords, \'field[]\': fields, status: "1", p: page, hdv: heightdiv, cwidth: realcolwidth}, function(data){
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
		
		var fieldstext = getCookie("fieldassembly");
		
		if (fieldstext != ""){
			var keywordstext = getCookie("keywordassembly");
			
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
		}
		ajaxsearch(keywords,fields,page);
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
		var brcd = $("#componentcode").val();
		if (brcd != ""){
			keywords.push(brcd);
			fields.push("componentcode");
		}
		var tycd = $("#componentname").val();
		if (tycd != ""){
			keywords.push(tycd);
			fields.push("componentname");
		}
		
		setCookie("keywordassembly",keywords);
		setCookie("fieldassembly",fields);
		
		ajaxsearch(keywords,fields,1);
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
		$("#componentcode").val("");
		$("#componentname").val("");
		$("#stockcode").focus();
	}
	var heightdiv = document.documentElement.clientHeight-80;
</script>
<script type="text/javascript" src="js/maketable.js"></script>
<script>
ajaxfromcookie(1);
$(document).click(function() {
	window.parent.closeall(1);
});
</script>
</body>

</html>
';
?>