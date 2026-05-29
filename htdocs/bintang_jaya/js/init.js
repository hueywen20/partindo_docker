function scanelement(){
	//for focus background color
	var tags;
	var f = false;
	var patt = /(input|select|textarea)/i;
	var itype = /(text|password)/i;
	var alltag = document.getElementsByTagName('*');
	for (var t = 0; t < alltag.length; t++){
		if (patt.test(alltag[t].tagName)){
			if (itype.test(alltag[t].type)){
				if (alltag[t].readOnly){
					alltag[t].style.backgroundColor = '#DEDEDE';
				}
				else{
					alltag[t].style.backgroundColor = '#EFEFEF';
					alltag[t].onfocus = function(){
						this.style.backgroundColor = '#CCFFCC';
					}
					if (alltag[t].className != 'dhx_combo_input'){
						alltag[t].onblur = function(){
							this.style.backgroundColor = '#EFEFEF';
						}
					}
					if (!f){
						alltag[t].focus();
						f = true;
					}
				}
			}
		}
	}
}

function deleteitem(link){
	if (confirm("Apakah anda yakin ?")){
		window.open(link,"_self");
	}
}

function toggle(id){
	var a = document.getElementById(id);
	if (a.style.display == 'block')
		a.style.display = 'none';
	else
		a.style.display = 'block';
}

function init(){
	scanelement();
}

function s_in_array(keyw,ary){
	var found = false;
	if (typeof(ary) != undefined){
		for (var b = 0; b < ary.length; b++){
			if (keyw == ary[b]){
				found = true;
				break;
			}
		}
	}
	return found;
}

function s_in_array_ri(keyw,ary){
	var found = -1;
	if (typeof(ary) != "undefined"){
		for (var b = 0; b < ary.length; b++){
			if (keyw == ary[b]){
				found = b;
				break;
			}
		}
	}
	return found;
}

function reverseString(str){
	var i=str.length;
	i=i-1;
	var newstr = "";
	for (var x = i; x >=0; x--){
		newstr += str.charAt(x);
	}
	return newstr;
}

function loadpagefromcombo(obj,url){
	window.open(url+obj.options[obj.selectedIndex].value,"_self");
}

function setLengthDate(dateValue, length){
   dateValue = ''+dateValue; // set dateValue to string
   while(dateValue.length < length){
      dateValue = '0' + dateValue;
   }
   return dateValue;
}

var dateSplitter = '-';
// date format is dd-mm-yyyy
function setDueDate(dateElement, dueDate, targetElement){
	if (dueDate == ''){
		$("#"+targetElement).val('');
	}
	else{
		var invoiceDate = $("#"+dateElement).val().split(dateSplitter);
		if (invoiceDate[0].length > 0 && invoiceDate[1].length > 0 && invoiceDate[2].length > 0){
			var currentDate = new Date();
			var dueDate = new Date(invoiceDate[2], parseInt(invoiceDate[1], 10) - 1, parseInt(invoiceDate[0], 10) + parseInt(dueDate), currentDate.getHours(), currentDate.getMinutes(), currentDate.getSeconds(), currentDate.getMilliseconds());

			$("#"+targetElement).val(setLengthDate(dueDate.getDate(), 2) + dateSplitter + setLengthDate(dueDate.getMonth() + 1, 2) + dateSplitter + setLengthDate(dueDate.getFullYear(), 4));
		}
		else{
			$("#"+targetElement).val('');
		}
	}
}

window.onload = init;