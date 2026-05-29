var mmsel;
var slv1 = new Array();
var slv2 = new Array();

function findPos(obj,tp) {
	var coords = { x: 0, y: 0 };
	if (obj.offsetParent) {
		while (obj) {
			coords.x += obj.offsetLeft;
			coords.y += obj.offsetTop;
			obj = obj.offsetParent;
		}
	}
	if (tp == 'x')
		return coords.x;
	else
		return coords.y;
}

function closeall(lvl){
	if (lvl == 1){
		for (var n = 0; n < slv1.length; n++){
			if (document.getElementById('sub1_'+slv1[n]))
				document.getElementById('sub1_'+slv1[n]).style.display = 'none';
		}
		if (mmsel != null){
			mouseout(mmsel);
			mmsel.onmouseout = Function("mouseout(this)");
			mmsel.onmouseover = Function("mouseover(this)");
		}
		closeall(2);
	}
	else if (lvl == 2){
		for (var n = 0; n < slv2.length; n++){
			if (document.getElementById('sub2_'+slv2[n]))
				document.getElementById('sub2_'+slv2[n]).style.display = 'none';
		}
		if (mmsel != null){
			mouseout(mmsel);
			mmsel.onmouseout = Function("mouseout(this)");
			mmsel.onmouseover = Function("mouseover(this)");
		}
	}
}

function showsub1(orders,objparent){
	closeall(1);
	if (document.getElementById('sub1_'+orders)){
		document.getElementById('sub1_'+orders).style.left = findPos(objparent,'x')+'px';
		document.getElementById('sub1_'+orders).style.display = 'block';
	}
	mmsel = objparent;
	mouseover(objparent);
	objparent.onmouseout = null;
	objparent.onmouseover = null;
}

function showsub2(orders,objparent){
	closeall(2);
	if (document.getElementById('sub2_'+orders)){
		document.getElementById('sub2_'+orders).style.left = (parseInt(findPos(objparent,'x'))+parseInt(objparent.offsetWidth))+'px';
		document.getElementById('sub2_'+orders).style.top = findPos(objparent,'y')+'px';
		document.getElementById('sub2_'+orders).style.display = 'block';
	}
	mmsel = objparent;
	mouseover(objparent);
	objparent.onmouseout = null;
	objparent.onmouseover = null;
}

function mouseover(obj){
	obj.style.backgroundColor = 'black';
	obj.style.color = 'white';
}

function mouseout(obj){
	obj.style.backgroundColor = '';
	obj.style.color = 'black';
}

var html = "";
var htmlsub1 = "";
var htmlsub2 = "";
html = '<div class="abs" style="top: 3px; left: 3px" onclick="return false;">'+
			'<table border="1" cellpadding="0" cellspacing="0"><tr>';
for (var x = 0; x < mm.length; x++){
	if (mm[x][2] == 1)
		arrows = '&nbsp;&nbsp;&nbsp;<img src="img/arrow-down.gif" border="0">';
	else
		arrows = '';
	if (mm[x][1] == ""){
		html += 
			'<td bgcolor="#f2f2f2" align="left" class="tdmenu" onclick="showsub1('+x+',this)" onmouseover="mouseover(this)" onmouseout="mouseout(this)">'+
			'<img src="img/bulletlist.gif" border="0"> '+mm[x][0]+arrows+'</td>';
	}
	else{
		html += 
			'<td bgcolor="#f2f2f2" align="left" class="tdmenu" onclick="'+mm[x][1]+'" onmouseover="mouseover(this)" onmouseout="mouseout(this)">'+
			'<img src="img/bulletlist.gif" border="0"> '+mm[x][0]+arrows+'</td>';
	}
	
	//sub level 1
	if (mm[x][2] == 1){
		var sub1 = mm[x][3];
		if (sub1.length > 0){
			slv1.push(x);
			htmlsub1 += '<div id="sub1_'+x+'" class="abs hid" style="top: 28px">'+
						'<table border="1" cellpadding="0" cellspacing="0">';
			for (var p = 0; p < sub1.length; p++){
				if (sub1[p][2] == 1)
					arrows = '<span style="float: right; margin-left: 10px; padding-top: 3px"><img src="img/arrow-right.gif" border="0"></span>';
				else
					arrows = '';
				htmlsub1 += '<tr>';
				if (sub1[p][1] == ""){
					htmlsub1 += '<td bgcolor="#FFFFFF" align="left" class="tdmenu" onclick="showsub2(\''+x+'_'+p+'\',this)" onmouseover="mouseover(this)" onmouseout="mouseout(this)">'+sub1[p][0]+arrows+'</td>';
				}
				else{
					htmlsub1 += '<td bgcolor="#FFFFFF" align="left" class="tdmenu" onclick="'+sub1[p][1]+';closeall(1)" onmouseover="mouseover(this)" onmouseout="mouseout(this)">'+sub1[p][0]+arrows+'</td>';
				}
				htmlsub1 += '</tr>';
				
				//sub level 2
				if (sub1[p][2] == 1){
					var sub2 = sub1[p][3];
					if (sub2.length > 0){
						slv2.push(x+'_'+p);
						htmlsub2 += '<div id="sub2_'+x+'_'+p+'" class="abs hid">'+
									'<table border="1" cellpadding="0" cellspacing="0">';
						for (var q = 0; q < sub2.length; q++){
							if (sub2[q][2] == 1)
								arrows = '&nbsp;&nbsp;&nbsp;<img src="img/arrow-right.gif" border="0">';
							else
								arrows = '';
							if (sub2[q][1] == ""){
								htmlsub2 += '<tr>'+
											'<td bgcolor="#FFFFFF" align="left" class="tdmenu" onclick="showsub3('+q+',this)" onmouseover="mouseover(this)" onmouseout="mouseout(this)">'+sub2[q][0]+arrows+'</td>'+
										'</tr>';
							}
							else{
								htmlsub2 += '<tr>'+
											'<td bgcolor="#FFFFFF" align="left" class="tdmenu" onclick="'+sub2[q][1]+';closeall(1)" onmouseover="mouseover(this)" onmouseout="mouseout(this)">'+sub2[q][0]+arrows+'</td>'+
										'</tr>';
							}
						}
						htmlsub2 += '</table>'+
								'</div>';
					}
				}

			}
			htmlsub1 += '</table>'+
					'</div>';
		}
	}
}
html += '	</tr></table>'+
		'</div>';

document.write('<div id="softmenu">'+html+htmlsub1+htmlsub2+'</div>');