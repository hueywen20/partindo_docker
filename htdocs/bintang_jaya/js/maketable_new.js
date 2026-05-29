var rowselected = 0;
var rowdetailsselected = 0;

function mouseovertable(obj){
	$("#"+obj).css("background-color","#FFDB8F");
}

function mouseouttable(obj){
	$("#"+obj).css("background-color","");
}

function changespancolor(rowid,color,states){
	var ts = 1;
	var spans = $("#row_"+rowid+"-"+ts);
	if (spans.length > 0){
		var rspan = true;
		while (rspan){
			spans.css("background-color",color);
			if (states == "disabled"){
				document.getElementById("row_"+rowid+"-"+ts).onmouseover = null;
				document.getElementById("row_"+rowid+"-"+ts).onmouseout = null;
			}
			else if (states == "enabled"){
				document.getElementById("row_"+rowid+"-"+ts).onmouseover = Function("mouseovertable(this.id)");
				document.getElementById("row_"+rowid+"-"+ts).onmouseout = Function("mouseouttable(this.id)");
			}
			ts++;
			spans = $("#row_"+rowid+"-"+ts);
			if (spans.length == 0){
				rspan = false;
			}
		}
	}
}

function changebgcolor(objthis){
	if (rowselected != 0){
		if (rowselected % 2 == 0){
			$("#row_"+rowselected).css("background-color","#EEEEFF");
			changespancolor(rowselected,"#EEEEFF","enabled");
		}
		else{
			$("#row_"+rowselected).css("background-color","#FFFFFF");
			changespancolor(rowselected,"#FFFFFF","enabled");
		}
		document.getElementById("row_"+rowselected).onmouseover = Function("mouseovertable(this.id)");
		document.getElementById("row_"+rowselected).onmouseout = Function("mouseouttable(this.id)");
	}
	
	rowselected = objthis.id;
	var spanrows = rowselected.indexOf("-");
	if (spanrows != -1){
		rowselected = rowselected.substring(0,spanrows);
	}
	rowselected = rowselected.replace("row_","");
	$("#row_"+rowselected).css("background-color","#FFCCCC");
	changespancolor(rowselected,"#FFCCCC","disabled");
	document.getElementById("row_"+rowselected).onmouseover = null;
	document.getElementById("row_"+rowselected).onmouseout = null;
}

function changebgcolordetail(objthis){
	if (rowdetailsselected != 0){
		if (rowdetailsselected % 2 == 0){
			$("#rowdetails_"+rowdetailsselected).css("background-color","#EEEEFF");
			changespancolor(rowdetailsselected,"#EEEEFF","enabled");
		}
		else{
			$("#rowdetails_"+rowdetailsselected).css("background-color","#FFFFFF");
			changespancolor(rowdetailsselected,"#FFFFFF","enabled");
		}
		document.getElementById("rowdetails_"+rowdetailsselected).onmouseover = Function("mouseovertable(this.id)");
		document.getElementById("rowdetails_"+rowdetailsselected).onmouseout = Function("mouseouttable(this.id)");
	}
	
	rowdetailsselected = objthis.id;
	var spanrows = rowdetailsselected.indexOf("-");
	if (spanrows != -1){
		rowdetailsselected = rowdetailsselected.substring(0,spanrows);
	}
	rowdetailsselected = rowdetailsselected.replace("rowdetails_","");
	$("#rowdetails_"+rowdetailsselected).css("background-color","#FFCCCC");
	changespancolor(rowdetailsselected,"#FFCCCC","disabled");
	document.getElementById("rowdetails_"+rowdetailsselected).onmouseover = null;
	document.getElementById("rowdetails_"+rowdetailsselected).onmouseout = null;

	if (rowdetailsselected != 0){
		$("#rowdetails_"+rowdetailsselected).css("background-color","");
		changespancolor(rowdetailsselected,"#FFFFFF","enabled");
		document.getElementById("rowdetails_"+rowdetailsselected).onmouseover = Function("mouseovertable(this.id)");
		document.getElementById("rowdetails_"+rowdetailsselected).onmouseout = Function("mouseouttable(this.id)");
	}
}

function navigatetable(event){
	var key = 0;
	// Determine the key pressed, depending on whether window.event or the event object is in use
	if (window.event) {
		key = window.event.keyCode;
	} else if (event) {
		key = event.keyCode;
	}
	// Was the Enter key pressed?
	//alert('a');
	if (key == 13) {
		//alert('a');
	}
	return true;
}

if (typeof(fromtemplate) == 'undefined'){
	bows = document.body.offsetWidth - 26;
	/* if (bows < 1009){
		bows = 1009;
	} */
	listwidth = bows;
}
else{
	bows = (fromtemplate / 100 * document.body.offsetWidth) - 26;
	listwidth = bows + 26;
}


var addscroll = 122;
bow = bows - 26;

if (percentwidth > 100){
	bow = 1009-37;
	bows = 1009;
	bow = bow + Math.floor((percentwidth-100) / 100 * bow);
	//bow = Math.floor(percentwidth / 100 * bow);
	bows = bows + Math.floor((percentwidth-100) / 100 * bows);
	addscroll = addscroll + Math.floor((percentwidth-100) / 100 * addscroll);
}
addscroll = 0;

var allwidth = 0;
var allwidthtofit = 0;

var tblhtml = '';
var hdrs = '';

for (var py = 0; py < headertext.length; py++){
	var tw = Math.floor(colwidth[py] / 100 * bow);
	tw = tw - 8;
	allwidth = parseInt(parseInt(allwidth)+parseInt(tw)+3);
	realcolwidth += ","+tw;
	hdrs += '<th width="'+tw+'" align="center" class="headerowntable padding_table_4">'+headertext[py]+'</th>';
}
realcolwidth = realcolwidth.substr(1);

tblhtml = '<table border="0" cellpadding="0" cellspacing="0">'+
			'<tr>'+
				'<td align="left"><span id="loadingprogress" style="display: none; float: right;">'+
				'<img src="img/loading.gif" border="0" width="12" height="12"></span>'+
				'<table border="0" cellpadding="0" cellspacing="0">'+
				'<tr class="headerbackground">'+hdrs;
tblhtml += '</tr>';

if (headerattach.length > 0){
	tblhtml += '<tr class="headerbackground">';
	for (var py = 0; py < headerattach.length; py++){
		tblhtml += '<td align="center" class="headerowntable padding_table_4">'+headerattach[py]+'</td>';
	}
	tblhtml += '</tr>';
}

tblhtml += '</table></td>'+
			'</tr>'+
			'<tr>'+
				'<td align="left" valign="top">'+
				'<div align="left" id="stufflist" style="height: '+heightdiv+'px; overflow-x: hidden; width: '+listwidth+'px; overflow: auto">'+
				'</div></td>'+
			'</tr>'+
			'</table>';

document.write(tblhtml);

var stufflistel = document.getElementById("stufflist");
stufflistel.onkeypress=navigatetable;