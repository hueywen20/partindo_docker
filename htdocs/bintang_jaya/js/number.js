function formatnumber(prices){
	var separator = '.';
	var decseparator = ',';
	var decimals = decseparator+'00';
	
	price = ''+prices;
	var signs = '';
	if (price[0] == '-'){
		signs = '-';
		price = price.substr(1);
	}
	var decimalpoint = price.indexOf(".");
	if (decimalpoint != -1){
		price = price.substr(0,decimalpoint);
		var temps = Math.round(prices*100)/100;
		temps = ''+temps;
		var decimalnow = temps.substr(decimalpoint+1);
		if (decimalnow.length == 0){
			decimalnow = '00';
		}
		else if (decimalnow.length == 1){
			decimalnow = decimalnow + '0';
		}
		decimals = decseparator+decimalnow;
	}
	if (price.length > 3) {
		mod = price.length % 3;
		output = (mod > 0 ? price.substr(0,mod) : '');
		for (var i=0 ; i < Math.floor(price.length / 3); i++) {
			if ((mod == 0) && (i == 0))
				output += price.substr(mod + 3 * i, 3);
			else
				output += separator+price.substr(mod + 3 * i, 3);
		}
		return signs+output+decimals;
	}
	else{
		return signs+price+decimals;
	}
}

function formatnumbernodec(prices){
	var separator = '.';
	
	price = ''+prices;
	if (price.length > 3) {
		mod = price.length % 3;
		output = (mod > 0 ? price.substr(0,mod) : '');
		for (var i=0 ; i < Math.floor(price.length / 3); i++) {
			if ((mod == 0) && (i == 0))
				output += price.substr(mod + 3 * i, 3);
			else
				output += separator+price.substr(mod + 3 * i, 3);
		}
		return output;
	}
	else{
		return price;
	}
}

function formatnumberkeyup(prices){
	var separator = '.';
	var decseparator = ',';
	
	price = ''+prices;
	var decimalpoint = price.indexOf(",");
	var decimals = '';
	if (decimalpoint != -1){
		decimals = decseparator+price.substr(decimalpoint+1);
		price = price.substr(0,decimalpoint);
	}
	if (price.length > 3) {
		mod = price.length % 3;
		output = (mod > 0 ? price.substr(0,mod) : '');
		for (var i=0 ; i < Math.floor(price.length / 3); i++) {
			if ((mod == 0) && (i == 0))
				output += price.substr(mod + 3 * i, 3);
			else
				output += separator+price.substr(mod + 3 * i, 3);
		}
		return output+decimals;
	}
	else{
		return price+decimals;
	}
}

function replacestr(str,find,replacement){
	if (str != ""){
		while (str.indexOf(find) != -1){
			str = str.replace(find,replacement);	
		}
	}
	return str;
}

function insertingfs(obj){
	if (IsNumeric(obj.value)){
		var price = replacestr(obj.value,".","");
		obj.value = formatnumberkeyup(price);
	}
}

function IsNumeric(strString){
	var strValidChars = "0123456789.-";
	var strChar;
	var blnResult = true;

	if (strString.length == 0)
		return false;

	for (i = 0; i < strString.length && blnResult == true; i++){
		strChar = strString.charAt(i);
		if (strValidChars.indexOf(strChar) == -1){
			blnResult = false;
		}
	}
	return blnResult;
}

function insertingfsext(obj){
	var arrobjvalue = obj.value.split(" ");
	for (var xp = 0; xp < arrobjvalue.length; xp++){
		if (IsNumeric(arrobjvalue[xp])){
			arrobjvalue[xp] = replacestr(arrobjvalue[xp],".","");
			arrobjvalue[xp] = formatnumber(arrobjvalue[xp]);
		}
	}
	obj.value = arrobjvalue.join(" ");
}

function validfrIdt(){
	var x=document.forms["frIdt"].getElementsByTagName("input");
	var j=0;
	for(var i=0;i<x.length;i++){
		if(x[i].type=='checkbox' && x[i].name=='chxidt[]' && x[i].checked==true){
			j+=1;
		}
	}
	if(j==0){
		alert("Belum ada yang dipilih");
		return false;
	}else{
		return confirm("Anda Yakin");
	}
}

function getKeyEvent(e){
	var keynum;
	if (window.event) 
		keynum = e.keyCode;
	else if(e.which) 
		keynum = e.which;
	return keynum;
}

function checknumber(e){
	var kn = getKeyEvent(e);
	if ((kn > 47 && kn < 58) || kn == 8 || kn == 9 || kn == 37 || kn == 38 || kn == 39 || kn == 40 || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || kn == 44 || e.keyCode == 46){
		return true;
	}
	else
		return false;
}

function detectspecialkeys(e){
	var evtobj = window.event? event : e;
	if (evtobj.altKey || evtobj.ctrlKey || evtobj.shiftKey){
		return true;
	}
	return false;
}

function convertcodes(value){
	results = ""+value;
	results = reverseString(results);
	if (NT.length > 0){
		for (var i = 0; i < NT.length; i++){
			results = replacestr(results,NT[i],RT[i]);
		}
	}
	results = reverseString(results);
	return results;
}

function deconvertcodes(value){
	results = ""+value;
	if (NT.length > 0){
		for (var i = 0; i < NT.length; i++){
			results = replacestr(results,RT[i],NT[i]);
		}
	}
	return results;
}