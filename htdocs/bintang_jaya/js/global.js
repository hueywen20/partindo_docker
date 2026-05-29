// Every single key press action will call this function.
function shouldCancelBackspace(e) {
	var key;
	if(e){
		key = e.which? e.which : e.keyCode;
		if(key == null || ( key != 8 && key != 13)){ // return when the key is not backspace key.
			return false;
		}
	}else{
		return false;
	}

	if (e.srcElement) { // in IE
		tag = e.srcElement.tagName.toUpperCase();
		type = e.srcElement.type;
		readOnly = e.srcElement.readOnly;
		if( type == null){ // Type is null means the mouse focus on a non-form field. Disable backspace button
			return true;
		}else{
			type = e.srcElement.type.toUpperCase();
		}
	} else { // in FF
		tag = e.target.nodeName.toUpperCase();
		type = (e.target.type) ? e.target.type.toUpperCase() : "";
		readOnly = e.target.readOnly;
	}

	// we don't want to cancel the keypress (ever) if we are in an input/text area
	if ( tag == 'INPUT' || type == 'TEXT' ||type == 'TEXTAREA') {
		if(readOnly == true ) // if the field has been dsabled, disbale the back space button
			return true;
		if( ((tag == 'INPUT' && type == 'RADIO') || (tag == 'INPUT' && type == 'CHECKBOX')) && (key == 8 || key == 13) ){
			return true; // the mouse is on the radio button/checkbox, disbale the backspace button
		}
		return false;
	}

	// if we are not in one of the above things, then we want to cancel (true) if backspace
	return (key == 8 || key == 13);
}

// check the browser type
function whichBrs() {
	var agt=navigator.userAgent.toLowerCase();
	if (agt.indexOf("opera") != -1) return 'Opera';
	if (agt.indexOf("staroffice") != -1) return 'Star Office';
	if (agt.indexOf("webtv") != -1) return 'WebTV';
	if (agt.indexOf("beonex") != -1) return 'Beonex';
	if (agt.indexOf("chimera") != -1) return 'Chimera';
	if (agt.indexOf("netpositive") != -1) return 'NetPositive';
	if (agt.indexOf("phoenix") != -1) return 'Phoenix';
	if (agt.indexOf("firefox") != -1) return 'Firefox';
	if (agt.indexOf("safari") != -1) return 'Safari';
	if (agt.indexOf("skipstone") != -1) return 'SkipStone';
	if (agt.indexOf("msie") != -1) return 'Internet Explorer';
	if (agt.indexOf("netscape") != -1) return 'Netscape';
	if (agt.indexOf("mozilla/5.0") != -1) return 'Mozilla';

	if (agt.indexOf('\/') != -1) {
		if (agt.substr(0,agt.indexOf('\/')) != 'mozilla') {
			return navigator.userAgent.substr(0,agt.indexOf('\/'));
		}
		else
			return 'Netscape';
	}
	else if (agt.indexOf(' ') != -1)
		return navigator.userAgent.substr(0,agt.indexOf(' '));
	else
		return navigator.userAgent;
}

// Global events (every key press)

var browser = whichBrs();
if(browser == 'Internet Explorer'){
	document.onkeydown = function() { return !shouldCancelBackspace(event); }
}else if(browser == 'Firefox'){
	document.onkeypress = function(e) { return !shouldCancelBackspace(e); }
}


function getCookie(c_name){
	if (document.cookie.length>0){
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1){
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1)
				c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return "";
}

function setCookie(c_name,value,expiredays){
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
}

function deleteCookie (cookie_name){
	var cookie_date = new Date ( );  // current date & time
	cookie_date.setTime ( cookie_date.getTime() - 1 );
	document.cookie = cookie_name + "=; expires=" + cookie_date.toGMTString();
}

function checkCookie(cname){
	var cookname = getCookie(cname);
	if (cookname != null && cookname != ""){
		delete_cookie(cname);
	}
	else{
		setCookie(cname,'1');
	}
}

function expandall(rid){
	var getimg = $('#arrow_'+rid).attr("src");
	var totalsplit = 2;
	if (getimg.indexOf('expand') != -1){
		$('#arrow_'+rid).attr("src","img/arrow_collapse.png");
		$('#arrow_'+rid).attr("title","Collapse");
		var expands = true;
		var elements;
		while (expands){
			elements = document.getElementById('row_'+rid+'-'+totalsplit);
			if (elements){
				elements.style.display = 'table-row';
				totalsplit++;
			}
			else{
				expands = false;
			}
		}
		for (var ix = 0; ix < 11; ix++){
			document.getElementById('row_'+rid+'_'+ix).rowSpan = totalsplit;
		}
		document.getElementById('minprice_'+rid).rowSpan = totalsplit;
		document.getElementById('maxprice_'+rid).rowSpan = totalsplit;
	}
	else{
		$('#arrow_'+rid).attr("src","img/arrow_expand.png");
		$('#arrow_'+rid).attr("title","Expand");
		var collapse = true;
		var elements;
		while (collapse){
			elements = document.getElementById('row_'+rid+'-'+totalsplit);
			if (elements){
				elements.style.display = 'none';
				totalsplit++;
			}
			else{
				collapse = false;
			}
		}
		for (var ix = 0; ix < 11; ix++){
			document.getElementById('row_'+rid+'_'+ix).rowSpan = 2;
		}
		document.getElementById('minprice_'+rid).rowSpan = 2;
		document.getElementById('maxprice_'+rid).rowSpan = 2;
	}
}

function generatepagenav(arrdata){
	var pagenow = arrdata[0];
	var totalrecord = arrdata[1];
	var totalpage = arrdata[2];
	var startrecord = arrdata[3];
	var endrecord = arrdata[4];
	
	var pagenav = 'Halaman <b>'+pagenow+'</b> dari <b>'+totalpage+'</b>';
	
	if (totalpage > 1){
		var navp = '<a class="pagenavs" href="javascript:goajax('+(pagenow-1)+')" title="Sebelumnya">&lt;</a>&nbsp;';
		var navn = '<a class="pagenavs" href="javascript:goajax('+(parseInt(parseInt(pagenow)+1))+')" title="Selanjutnya">&gt;</a>';
		var navfirst = '<a class="pagenavs" href="javascript:goajax(1)" title="Halaman Pertama">&lt;&lt;</a>&nbsp;&nbsp;';
		var navlast = '&nbsp;&nbsp;<a class="pagenavs" href="javascript:goajax('+(totalpage)+')" title="Halaman Terakhir">&gt;&gt;</a>';
		
		if (pagenow == 1){
			navp = '';
			navfirst = '';
		}
		else if (pagenow == totalpage){
			navn = '';
			navlast = '';
		}

		var pagelink = '';
		pagelink += navfirst;
		pagelink += navp;
		var left = pagenow - 5;
		var right = parseInt(parseInt(pagenow) + 5);
		if (left < 1){
			right += Math.abs(left) + 1;
			left = 1;
		}
		if (right > totalpage){
			left += (totalpage - right);
			if (left < 1)
				left = 1;
			right = totalpage;
		}
		if (left > 1){
			pagelink += '...&nbsp;';
		}
		for (l = left; l <= right; l++){
			if (l == pagenow){
				pagelink += '<span class="activepagenavs">'+l+'</span>&nbsp;';
			}
			else{
				pagelink += '<a class="pagenavs" href="javascript:goajax('+l+')"><b>'+l+'</b></a>&nbsp;';					
			}
		}
		if (right < totalpage){
			pagelink += '... &nbsp;&nbsp;';
		}
		pagelink += navn;
		pagelink += navlast;
		
		pagenav += "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;"+pagelink;
	}
	$("#navpage").html(pagenav);
	
	var recordview = 'Record '+formatnumbernodec(startrecord)+' - '+formatnumbernodec(endrecord)+' dari total '+formatnumbernodec(totalrecord);
	$("#records").html(recordview);
}