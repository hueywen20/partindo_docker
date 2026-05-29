var rowselected = 0;

function mouseovertable(obj){
	$("#"+obj).css("background-color","#FFCCCC");
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
			$("#row_"+rowselected).css("background-color","#EEFFEE");
			changespancolor(rowselected,"#EEFFEE","enabled");
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
	$("#row_"+rowselected).css("background-color","#FFDB8F");
	changespancolor(rowselected,"#FFDB8F","disabled");
	document.getElementById("row_"+rowselected).onmouseover = null;
	document.getElementById("row_"+rowselected).onmouseout = null;
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

var bows = document.body.offsetWidth;
var addscroll = 122;
if (bows < 1009){
	bows = 1009;
}
bow = bows - 37;
if (percentwidth > 100){
	bow = 1009-37;
	bows = 1009;
	bow = bow + Math.floor((percentwidth-100) / 100 * bow);
	//bow = Math.floor(percentwidth / 100 * bow);
	bows = bows + Math.floor((percentwidth-100) / 100 * bows);
	addscroll = addscroll + Math.floor((percentwidth-100) / 100 * addscroll);
}

var allwidth = 0;

var tblhtml = '';
tblhtml = '<table border="0" cellpadding="0" cellspacing="0">'+
			'<tr>'+
				'<td align="left" width="'+bows+'"><span style="float: right; display: none" id="loadingprogress"><img src="img/loading.gif" border="0" width="16" height="16"></span>'+
				'<table border="0" cellpadding="3" cellspacing="0">'+
				'<tr class="headerbackground">';
				
for (var py = 0; py < headertext.length; py++){
	var tw = Math.floor(colwidth[py] / 100 * bow);
	tw = tw-6;
	allwidth = parseInt(parseInt(allwidth)+parseInt(tw));
	realcolwidth += ","+tw;
	tblhtml += '<th width="'+tw+'" align="center" class="headerowntable">'+headertext[py]+'</th>';
}
realcolwidth = realcolwidth.substr(1);

tblhtml += '</tr>';

if (headerattach.length > 0){
	tblhtml += '<tr class="headerbackground">';
	for (var py = 0; py < headerattach.length; py++){
		tblhtml += '<td align="center" class="headerowntable">'+headerattach[py]+'</td>';
	}
	tblhtml += '</tr>';
}

var listwidth = parseInt(parseInt(allwidth)+parseInt(addscroll)+parseInt(6));

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