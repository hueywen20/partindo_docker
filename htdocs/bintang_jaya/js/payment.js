
function setCustomer(addrid,code,name,cperson,address,city,phone){
	if (code != ""){
		var A = new Array();
		A.push(code);
		if (name != "")
			A.push(name);
		if (cperson != "")
			A.push(cperson);
		if (address != "")
			A.push(address);
		if (city != "")
			A.push(city);
		if (phone != "")
			A.push(phone);
		$("#customerdetail").html(A.join(" - "));
		$("#customercode").val(code);
		$("#customeraddrid").val(addrid);
		
		var startdate = $("#startdate").val();
		
		$.ajax({
			type: 'GET',
			url: 'customer.php',
			data: 'get=rnow&code='+code+'&typedata=remainingnow&startdate='+startdate,
			success: function(data) {
				var arrdata = data.split("|^|");
				$("#remainingprevious").val(formatnumber(arrdata[0]));
				//$("#remainingprevioustext").html(formatnumber(arrdata[0]));
				/* if ( arrdata[0] != 0){
				$("#remainingprevioustable").removeClass("hide");
				} */
				
				$("#remainingprevioush").val(formatnumber(arrdata[1]));
				//$("#remainingprevioustexth").html(formatnumber(arrdata[1]));
				/* if ( arrdata[1] != 0){
				$("#remainingprevioushtable").removeClass("hide");
				} */
				
				//$("#remainingprevioustext").html(formatnumber(data));
				
				
			}
		});
		
		mygrid.clearAll();
	}
}

function setSupplier(addrid,code,name,cperson,address,city,phone){
	if (code != ""){
		var A = new Array();
		A.push(code);
		if (name != "")
			A.push(name);
		if (cperson != "")
			A.push(cperson);
		if (address != "")
			A.push(address);
		if (city != "")
			A.push(city);
		if (phone != "")
			A.push(phone);
		$("#supplierdetail").html(A.join(" - "));
		$("#suppliercode").val(code);
		$("#supplieraddrid").val(addrid);
		
		$.ajax({
			type: 'POST',
			url: 'supplier.php',
			data: 'get=remainingdebt&id='+code,
			success: function(data) {
				$("#remainingprevious").val(data);
				$("#remainingprevioustext").html(formatnumber(data));
			}
		});
		
		mygrid.clearAll();
	}
}

function countgrandtotal(){
	//count all sub total
	var totalpay = $("#totalpayment");
	var totalpayel = $("#totalpaymenttext");
	
	var grandtotal = $("#grandtotals");
	var grandtotalel = $("#grandtotalstext");
	
	var totalrow = mygrid.getRowsNum();
	if (totalrow > 0){
		var totals = 0;
		var totalsbuy = 0;
		var totalssale = 0;
		var totalsbuyr = 0;
		var totalssaler = 0;
		var totalforbuy = 0;
		var totalforsale = 0;
		
		for (var b = 0; b < totalrow; b++){
			var subvalue = mygrid.cells(mygrid.getRowId(b),4).getValue();
			var idrows = mygrid.getRowId(b);
			var typerow = idrows.split("_");
			//alert(typerow[0]);
			if (IsNumeric(subvalue)){
				if (typerow[0]=="buy"){
				totalsbuy = parseFloat(parseFloat(totalsbuy) + parseFloat(subvalue));
				}
				else if (typerow[0]=="sale"){
				totalssale = parseFloat(parseFloat(totalssale) + parseFloat(subvalue));
				}
				else if (typerow[0]=="returnby"){
				totalsbuyr = parseFloat(parseFloat(totalsbuyr) + parseFloat(subvalue));
				}
				else if (typerow[0]=="return"){
				totalssaler = parseFloat(parseFloat(totalssaler) + parseFloat(subvalue));
				}
				
				totalforbuy = parseFloat(parseFloat(totalsbuy) - parseFloat(totalsbuyr));
				totalforsale = parseFloat(parseFloat(totalssale) - parseFloat(totalssaler));
				totals = parseFloat(parseFloat(totalforsale) - parseFloat(totalforbuy));
				totals = Math.abs(totals);
			}
		}
	//alert(totals);
	/* var totalrowbuy = mygrid.getRowsNum();
	if (totalrowbuy > 0){
		var totals = 0;
		for (var b = 0; b < totalrowbuy; b++){
			var subvalue = mygrid.cells(mygrid.getRowId(b),4).getValue();
			var idrows = mygrid.getRowId(b);
			var typerow = idrows.split("_");
			alert(typerow[0]);
			if (IsNumeric(subvalue)){
				totals = parseFloat(parseFloat(totals) + parseFloat(subvalue));
			}
		} */
		
		
		if (totals < 0){
			$("#saveto").val("paydebt");
		}
		
		totalpay.val(totals);
		totalpayel.html(formatnumber(totals));		
		//alert(totals);
	
		var rprev = replacestr(replacestr($("#remainingprevioush").val(),".",""),",",".");
		var rpre = replacestr(replacestr($("#remainingprevious").val(),".",""),",",".");
		
		if (rprev == ""){
		rprev = 0;
		}
		else{
		rprev = rprev;
		}
		
		if (rpre == ""){
		rpre = 0;
		}
		else{
		rpre = rpre;
		}
		
		
		totals = parseFloat(parseFloat(totals) - parseFloat(rpre))+parseFloat(rprev);;
		
		
		
		if (totals < 0 ){
		totals =0;
		}
		else{
		totals = totals;
		}
		
		grandtotal.val(totals);
		grandtotalel.html(formatnumber(totals));
	}
	else{
		totalpay.val(0);
		totalpayel.html("0");
		
		grandtotal.val(0);
		grandtotalel.html("0");
	}
	fillremaining();
}

function doafterupdate(){
	$("#loadingsearchinvoice").css("visibility","hidden");
	var detailidv = $("#detailid").val();
	var arrdetailidv = new Array();
	if (detailidv != "" && typeof(detailidv) != 'undefined'){
		arrdetailidv = detailidv.split(",");
	}
	var totalrow = mygrid.getRowsNum();
	var rowids;
	if (totalrow > 0){
		var totals = 0;
		var arrtemp = new Array();
		for (var b = 0; b < totalrow; b++){
			rowids = mygrid.getRowId(b);
			if (!s_in_array(rowids,arrdetailidv)){
				arrtemp.push(rowids);
			}
		}
		if (arrtemp.length > 0){
			$("#detailadded").val(arrtemp.join(","));
		}
	}
	countgrandtotal();
}

function searchsalelist(){
	$("#loadingsearchinvoice").css("visibility","visible");
	//mygrid.clearAll();
	var customercode = $("#customercode").val();
	if (customercode == ''){
		alert("Nama Customer belum dipilih");
		$("#loadingsearchinvoice").css("visibility","hidden");
	}
	else{
		
		var code = $("#customercode").val();
		var startdate = $("#startdate").val();
		
		$.ajax({
			type: 'GET',
			url: 'customer.php',
			data: 'get=rnow&code='+code+'&typedata=remainingnow&startdate='+startdate,
			success: function(data) {
				var arrdata = data.split("|^|");
				$("#remainingprevious").val(formatnumber(arrdata[0]));
				//$("#remainingprevioustext").html(formatnumber(arrdata[0]));
				/* if ( arrdata[0] != 0){
				$("#remainingprevioustable").removeClass("hide");
				} */
				
				$("#remainingprevioush").val(formatnumber(arrdata[1]));
				//$("#remainingprevioustexth").html(formatnumber(arrdata[1]));
				/* if ( arrdata[1] != 0){
				$("#remainingprevioushtable").removeClass("hide");
				} */
				
				//$("#remainingprevioustext").html(formatnumber(data));
				
			}
		});
			
		mygrid.updateFromXML('payment.php?getlist=xml&list=determine&customercode='+encodeURIComponent(customercode)+'&startdate='+encodeURIComponent($("#startdate").val())+'&enddate='+encodeURIComponent($("#enddate").val()),true,false,doafterupdate);
	}
}

function checktotalpayment(){
	var repayrows = mygridrp.getRowsNum();
	var totalalls = 0;	
	var statusnow = $("#complete :selected").val();
	var statuspayment = $("#statuspayment").val();
	var returntoform = true;
	
	if (repayrows > 0){
		for (var b = 0; b < repayrows; b++){
			var subvalue = mygridrp.cells(mygridrp.getRowId(b),6).getValue();
			subvalue = replacestr(replacestr(subvalue,".",""),",",".");
			if (IsNumeric(subvalue)){
				totalalls = parseFloat(parseFloat(totalalls) + parseFloat(subvalue));
			}
			
			var types = mygridrp.cells(mygridrp.getRowId(b),0).getValue();
			
			if (statuspayment == "0" || statuspayment == ""){
				if (types == 3){
					var chequedates = mygridrp.cells(mygridrp.getRowId(b),4).getValue();
					var chequeduedate = mygridrp.cells(mygridrp.getRowId(b),5).getValue();
					if (chequedates == "" || chequeduedate == ""){
						alert("Tanggal dan Jatuh Tempo dari Cek harus diisi");
						returntoform = false;
					}
					else{
						if (chequedates != chequeduedate && statusnow == "1"){
							alert("Pelunasan tidak bisa dilakukan karena tanggal dan jatuh tempo cek tidak sama");
							returntoform = false;
						}
					}
				}
				if (types == 4){
					var girodates = mygridrp.cells(mygridrp.getRowId(b),4).getValue();
					var giroduedate = mygridrp.cells(mygridrp.getRowId(b),5).getValue();
					if (girodates == "" || giroduedate == ""){
						alert("Tanggal dan Jatuh Tempo dari Giro harus diisi");
						returntoform = false;
					}
					else{
						if (girodates != giroduedate && statusnow == "1"){
							alert("Pelunasan tidak bisa dilakukan karena tanggal dan jatuh tempo giro tidak sama");
							returntoform = false;
						}
					}
				}
			}
		}
	}
	
	var rprev = replacestr(replacestr($("#remainingprevioush").val(),".",""),",",".");
	var rpre = replacestr(replacestr($("#remainingprevious").val(),".",""),",",".");
	/*if (rprev != ""){
		totalalls -= rprev;
	}*/
	if (rnow != ""){
		totalalls -= rnow;
	}
	
	var grandtotals = parseFloat($("#grandtotals").val());
	var totalpayment = parseFloat($("#totalpayment").val());
	if (rprev >= totalpayment){
		returntoform = true;
	}
	else{
		if (totalalls > 0){
			if (totalalls != grandtotals){
				alert("Jumlah pelunasan yang dimasukkan TIDAK SAMA dengan jumlah penagihan piutang");
				returntoform = true;
			}
		}
		else{
			if (statusnow == "1"){
				alert("Jumlah pelunasan yang dimasukkan TIDAK SAMA dengan jumlah penagihan piutang");
				returntoform = true;
			}
		}
	}
	
	return returntoform;
}

function fillremaining(){
	var rprev = replacestr(replacestr($("#remainingprevioush").val(),".",""),",",".");
	var rpre = replacestr(replacestr($("#remainingprevious").val(),".",""),",",".");
	var totalpayment = $("#totalpayment").val();
	var totalflat = replacestr(replacestr($("#flat").val(),".",""),",",".");
	var grandtotal = $("#grandtotals").val();
	if (grandtotal != ""){
		var repayrows = mygridrp.getRowsNum();
		var totalalls = 0;	
		
		if (repayrows > 0){
			for (var b = 0; b < repayrows; b++){
				var subvalue = mygridrp.cells(mygridrp.getRowId(b),6).getValue();
				subvalue = replacestr(replacestr(subvalue,".",""),",",".");
				if (IsNumeric(subvalue)){
					totalalls = parseFloat(parseFloat(totalalls) + parseFloat(subvalue));
				}
			}
		}
		
		if (totalpayment > rpre){
			var remainingnow = totalalls - grandtotal;
			if (remainingnow < 0){
				remainingnow = 0;
			}
		}
		else{
			var remainingnow = parseFloat(parseFloat(totalalls) + parseFloat(rpre) - parseFloat(totalpayment));
			if (remainingnow < 0){
				remainingnow = 0;
			}
		}
		//alert(formatnumber(remainingnow));
		
		if (totalflat== ""){
		totalflat = 0;
		}
		
		var statusflat = $("#statusflat").val();
		if (statusflat == "-"){
			remainingnow = parseFloat(parseFloat(remainingnow) - parseFloat(totalflat));
		}
		
		$("#remainingnow").val(remainingnow);
		$("#totalpay").val(totalalls);
		$("#remainingnowtext").html(formatnumber(remainingnow));
		
		/*}
		else{
			$("#remainingnow").val(0);
			$("#remainingnowtext").html(formatnumber(0));
		}*/
		
	}
}

function editingrepay(stage,rId,cInd){
	if (stage == 0){
		if (cInd == 6){
			var types = mygridrp.cells(rId,0).getValue();
			if (types == 5){
				return false;
			}
		}
	}
	else if (stage == 2){
		if (cInd == 0){
			var types = mygridrp.cells(rId,0).getValue();
			var price = mygridrp.cells(rId,6).getValue();
			if (types == 5 || types == 6){
				if (price != ""){
					price = replacestr(price,"-","");
					price = "-"+price;
					mygridrp.cells(rId,6).setValue(price);
				}
			}
			else{
				if (price != ""){
					price = replacestr(price,"-","");
					mygridrp.cells(rId,6).setValue(price);
				}
			}
		}
		else if (cInd == 6){
			var types = mygridrp.cells(rId,0).getValue();
			var price = mygridrp.cells(rId,6).getValue();
			if (types == 5 || types == 6){
				price = "-"+price;
			}
			price = replacestr(replacestr(price,".",""),",",".");
			var chknumprice = IsNumeric(price);
			
			if (cInd == 6){
				if (price != "" && chknumprice){
					mygridrp.cells(rId,6).setValue(formatnumber(price));
				}				
			}
			
			fillremaining();
		}
	}
	return true;
}
