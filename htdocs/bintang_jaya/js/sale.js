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
		$.ajax({
			type: 'GET',
			url: 'ajax.php?list=checklasttransaction&customercode='+encodeURIComponent(code),
			success: function(data) {
				if (!data){
					$("#messages").html("Customer ini tidak diperbolehkan melakukan transaksi kredit, karena masih ada faktur yang belum dibayar");
					$("#allow").val(0);
				}
				else{
					allowSale();
				}
			}
		});
	}
}

function allowSale(){
	$("#messages").html("");
	$("#allow").val(1);	
}

function countgrandtotal(obj){			
	//count all sub total
	var totalrow = mygrid.getRowsNum();
	if (totalrow > 0){
		var totals = 0;
		for (var b = 0; b < totalrow; b++){
			var subvalue = mygrid.cells(mygrid.getRowId(b),9).getValue();
			subvalue = replacestr(replacestr(subvalue,".",""),",",".");
			if (IsNumeric(subvalue)){
				totals = parseFloat(parseFloat(totals) + parseFloat(subvalue));
			}
		}
		$("#totals").val(totals);
		$("#subtotal").html(formatnumber(totals));
	}
	else{
		$("#totals").val(0);
		$("#subtotal").html("0");
	}
	
	var subtotal = $("#totals").val();
	var disc = $("#disc").val();
	disc = replacestr(replacestr(disc,".",""),",",".");
	var tax = $("#tax").val();
	tax = replacestr(replacestr(tax,".",""),",",".");
	var grandtotal = $("#totalsale");
	var grandtotalel = $("#grandtotal");
	var gt = parseFloat(subtotal);
	if (IsNumeric(disc)){
		if (disc > 100){
			disc = 100;
			$("#disc").val("100");
		}
		var discv = (disc * gt) / 100;
		gt = gt-discv;
	}
	if (IsNumeric(tax)){
		if (tax > 100){
			tax = 100;
			$("#tax").val("100");
		}
		var taxv = (tax * gt) / 100;
		gt = parseFloat(parseFloat(gt)+parseFloat(taxv));
	}
	gt = gt.toFixed(2);
	if (IsNumeric(gt)){
		grandtotal.val(gt);
		grandtotalel.html(formatnumber(gt));		
	}
	else{
		grandtotal.val(0);
		grandtotalel.html("0");
	}
	if (obj){
		insertingfs(obj);
	}
}

function setNewComboValue(url,index){
	mygrid.getColumnCombo(index).clearAll(false);
	mygrid.getColumnCombo(index).loadXML(url);
}

function clearrowgrid(rId){
	mygrid.cells(rId,1).setValue("");
	mygrid.cells(rId,2).setValue("");
	mygrid.cells(rId,3).setValue("");
	mygrid.cells(rId,4).setValue("");
	mygrid.cells(rId,5).setValue("");
	mygrid.cells(rId,6).setValue("");
	mygrid.cells(rId,7).setValue("");
	mygrid.cells(rId,8).setValue("");
	mygrid.cells(rId,9).setValue("");
	mygrid.cells(rId,10).setValue("");
}

var arrsaleprice = new Array();
var arrunits = new Array();
var arrconversion = new Array();
var arrstockqty = new Array();
var tempqtycode = new Array();
var tempqtyforedit = new Array();
var tempunitsc = new Array();

function checkqtyingrid(stockcode,rId){
	/* check any same stock code in grid */				
	var totalrow = mygrid.getRowsNum();
	var totals = 0;
	var qtytemp;
	if (totalrow > 0){
		for (var b = 0; b < totalrow; b++){
			if (mygrid.cells(mygrid.getRowId(b),0).getValue() == stockcode && mygrid.getRowId(b) != rId){
				var subvalue = mygrid.cells(mygrid.getRowId(b),5).getValue();
				subvalue = replacestr(replacestr(subvalue,".",""),",",".");
				if (IsNumeric(subvalue)){
					if (arrunits[mygrid.getRowId(b)].length > 0){
						var XK = s_in_array_ri(mygrid.cells(mygrid.getRowId(b),6).getValue(),arrunits[mygrid.getRowId(b)]);
						if (XK != -1){
							qtytemp = subvalue*arrconversion[mygrid.getRowId(b)][XK];
						}
						else{
							qtytemp = subvalue;
						}
					}
					else{
						qtytemp = subvalue;
					}
					totals = parseFloat(parseFloat(totals) + parseFloat(qtytemp));
				}
			}
		}
	}
	return totals;
}

function fillotherfield(stockcode,rId,fillpartno){
	$.ajax({
		type: 'GET',
		url: 'ajax.php',
		data: 'list=getstockname&stockcode='+encodeURIComponent(stockcode),
		success: function(data) {
			var arrdata = data.split("|^|");
			if (fillpartno){
				mygrid.getColumnCombo(1).clearAll(false);
				mygrid.cells(rId,1).setValue(arrdata[8]);
			}
			mygrid.getColumnCombo(2).clearAll(false);
			mygrid.cells(rId,2).setValue(arrdata[0]);
			mygrid.getColumnCombo(3).clearAll(false);
			mygrid.cells(rId,3).setValue(arrdata[6]);
			mygrid.getColumnCombo(4).clearAll(false);
			mygrid.cells(rId,4).setValue(arrdata[7]);
			//mygrid.cells(rId,7).setValue(formatnumber(arrdata[1]));
			
			arrsaleprice[rId] = arrdata[1];
			arrstockqty[rId] = arrdata[2];
			var splitunits = arrdata[3].split(",");
			arrunits[rId] = new Array();
			for (var th = 0; th < splitunits.length; th++){
				arrunits[rId][th] = splitunits[th];
			}
			
			mygrid.cells(rId,6).setValue(splitunits[1]);
			
			var splitconv = arrdata[4].split(",");
			arrconversion[rId] = new Array();
			for (var th = 0; th < splitconv.length; th++){
				arrconversion[rId][th] = splitconv[th];
			}
			
			var qty = mygrid.cells(rId,5).getValue();
			var price = mygrid.cells(rId,7).getValue();
			var disc = mygrid.cells(rId,8).getValue();
			qty = replacestr(replacestr(qty,".",""),",",".");
			price = replacestr(replacestr(price,".",""),",",".");
			disc = replacestr(replacestr(disc,".",""),",",".");
			var chknumqty = IsNumeric(qty);
			var chknumprice = IsNumeric(price);
			var chknumdisc = IsNumeric(disc);
			
			if (chknumqty && chknumprice){
				var disct = 0;
				var totals = qty * price;
				if (chknumdisc){
					disct = (disc * totals) / 100;
				}
				var resultcalc = (totals-disct).toFixed(2);
				mygrid.cells(rId,9).setValue(formatnumber(resultcalc));
			}
			else{
				mygrid.cells(rId,9).setValue(0);
			}
			
			//count all sub total
			var totalrow = mygrid.getRowsNum();
			if (totalrow > 0){
				var totals = 0;
				for (var b = 0; b < totalrow; b++){
					totals = parseFloat(parseFloat(totals) + parseFloat(replacestr(replacestr(mygrid.cells(mygrid.getRowId(b),9).getValue(),".",""),",",".")));
				}
				$("#totals").val(totals);
				$("#subtotal").html(formatnumber(totals));
				countgrandtotal();
			}
		}
	})
}

function editingsale(stage,rId,cInd){
	if (stage == 0){
		/* if (cInd == 1){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=partno&stockcode="+encodeURIComponent(stockcode),1);
			}
			else{
				setNewComboValue("ajax.php?list=partno",1);
			}
		} */
		/* else if (cInd == 3){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=brandpurchase&stockcode="+encodeURIComponent(stockcode),3);
			}
		}
		else if (cInd == 4){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=typepurchase&stockcode="+encodeURIComponent(stockcode),4);
			}
		} */
		if (cInd == 0){
			if (typeof(idnodel) != 'undefined'){
				if (s_in_array(rId,idnodel)){
					alert("Maaf, kode stok ini tidak bisa diganti dengan yang lain, karena ada transaksi retur penjualan yang telah menggunakan detail penjualan ini.");
					return false;
				}
			}
		}
		else if (cInd == 6){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=unitpurchase&stockcode="+encodeURIComponent(stockcode),6);
			}
		}
		
		//get current quantity for edit
		/*if ($("#id").val() != ""){
			if (cInd == 5){
				tempqtyforedit[rId] = replacestr(replacestr(mygrid.cells(rId,5).getValue(),".",""),",",".");
			}
			else{
				tempqtyforedit[rId] = 0;
			}
		}
		else{
			tempqtyforedit[rId] = 0;
		}*/
	}
	if (stage == 2){
		var stockcode = mygrid.cells(rId,0).getValue();
		if (cInd == 0){
			clearrowgrid(rId);
			if (stockcode != ""){
				fillotherfield(stockcode,rId,true);
			}
			else{
				mygrid.cells(rId,2).setValue("");
			}
		}
		else if (cInd == 1){
			var partnos = mygrid.getColumnCombo(1).getSelectedValue();
			if (partnos != "" && partnos != null){
				var arrpn = partnos.split("||");
				var stockcode = arrpn[0];
				if (typeof(idnodel) != 'undefined'){
					if (s_in_array(rId,idnodel)){
						alert("Maaf, kode stok ini tidak bisa diganti dengan yang lain, karena ada transaksi retur penjualan yang telah menggunakan detail penjualan ini.");
						return false;
					}
				}
				if (stockcode != ""){
					mygrid.cells(rId,0).setValue(stockcode);
					fillotherfield(stockcode,rId,false);
				}
				/* $.ajax({
					type: 'GET',
					url: 'ajax.php?list=partno&get=stockcode&pn='+encodeURIComponent(partnos),
					success: function(data) {
						if (data != ""){
							mygrid.cells(rId,0).setValue(data);
							fillotherfield(data,rId,false);
						}
					}
				}); */
			}
		}
		else if (cInd == 2){
			var partnos = mygrid.getColumnCombo(2).getSelectedValue();
			if (partnos != "" && partnos != null){
				var arrpn = partnos.split("||");
				var stockcode = arrpn[0];
				var partno = arrpn[1];
				
				if (typeof(idnodel) != 'undefined'){
					if (s_in_array(rId,idnodel)){
						alert("Maaf, kode stok ini tidak bisa diganti dengan yang lain, karena ada transaksi retur penjualan yang telah menggunakan detail penjualan ini.");
						return false;
					}
				}
				
				mygrid.getColumnCombo(1).clearAll(false);
				mygrid.cells(rId,1).setValue(partno);
				
				if (stockcode != ""){
					mygrid.cells(rId,0).setValue(stockcode);
					fillotherfield(stockcode,rId,false);
				}
				/* $.ajax({
					type: 'GET',
					url: 'ajax.php?list=partno&get=stockcode&pn='+encodeURIComponent(partnos),
					success: function(data) {
						if (data != ""){
							mygrid.cells(rId,0).setValue(data);
							fillotherfield(data,rId,false);
						}
					}
				}); */
			}
		}
		else if (cInd == 3){
			var partnos = mygrid.getColumnCombo(3).getSelectedValue();
			if (partnos != "" && partnos != null){
				var arrpn = partnos.split("||");
				var stockcode = arrpn[0];
				var partno = arrpn[1];
				
				if (typeof(idnodel) != 'undefined'){
					if (s_in_array(rId,idnodel)){
						alert("Maaf, kode stok ini tidak bisa diganti dengan yang lain, karena ada transaksi retur penjualan yang telah menggunakan detail penjualan ini.");
						return false;
					}
				}

				mygrid.getColumnCombo(1).clearAll(false);
				mygrid.cells(rId,1).setValue(partno);
				
				if (stockcode != ""){
					mygrid.cells(rId,0).setValue(stockcode);
					fillotherfield(stockcode,rId,false);
				}
				/* $.ajax({
					type: 'GET',
					url: 'ajax.php?list=partno&get=stockcode&pn='+encodeURIComponent(partnos),
					success: function(data) {
						if (data != ""){
							mygrid.cells(rId,0).setValue(data);
							fillotherfield(data,rId,false);
						}
					}
				}); */
			}
		}
		else if (cInd == 4){
			var partnos = mygrid.getColumnCombo(4).getSelectedValue();
			if (partnos != "" && partnos != null){
				var arrpn = partnos.split("||");
				var stockcode = arrpn[0];
				var partno = arrpn[1];
				
				if (typeof(idnodel) != 'undefined'){
					if (s_in_array(rId,idnodel)){
						alert("Maaf, kode stok ini tidak bisa diganti dengan yang lain, karena ada transaksi retur penjualan yang telah menggunakan detail penjualan ini.");
						return false;
					}
				}

				mygrid.getColumnCombo(1).clearAll(false);
				mygrid.cells(rId,1).setValue(partno);
				
				if (stockcode != ""){
					mygrid.cells(rId,0).setValue(stockcode);
					fillotherfield(stockcode,rId,false);
				}
				/* $.ajax({
					type: 'GET',
					url: 'ajax.php?list=partno&get=stockcode&pn='+encodeURIComponent(partnos),
					success: function(data) {
						if (data != ""){
							mygrid.cells(rId,0).setValue(data);
							fillotherfield(data,rId,false);
						}
					}
				}); */
			}
		}
		else if (cInd == 5 || cInd == 6 || cInd == 7 || cInd == 8){
			var qty = mygrid.cells(rId,5).getValue();
			var price = mygrid.cells(rId,7).getValue();
			var disc = mygrid.cells(rId,8).getValue();
			qty = replacestr(replacestr(qty,".",""),",",".");
			price = replacestr(replacestr(price,".",""),",",".");
			disc = replacestr(replacestr(disc,".",""),",",".");
			var chknumqty = IsNumeric(qty);
			var chknumprice = IsNumeric(price);
			var chknumdisc = IsNumeric(disc);
			
			if (cInd == 5){
				if (typeof(tempqtyforedit[rId]) == "undefined" || tempqtyforedit[rId] == "" || stockcode != tempqtycode[rId]){
					tempqtyforedit[rId] = 0;
				}
				if (!chknumqty || qty <= 0){
					alert("Quantity harus dalam angka dan harus lebih besar dari 0");
					return false;
				}
				var qtytemp;
				if (arrunits[rId].length > 0){
					var XK = s_in_array_ri(mygrid.cells(rId,6).getValue(),arrunits[rId]);
					if (XK != -1){
						qtytemp = qty*arrconversion[rId][XK];
						tempqty = tempqtyforedit[rId] * arrconversion[rId][XK];
					}
					else{
						qtytemp = qty;
						tempqty = tempqtyforedit[rId];
					}
				}
				else{
					qtytemp = qty;
					tempqty = tempqtyforedit[rId];
				}
				if (parseFloat(qtytemp) > parseFloat(parseFloat(arrstockqty[rId]) + parseFloat(tempqty) - checkqtyingrid(stockcode,rId))){
					alert("Jumlah barang melebihi stok, stok sekarang : "+formatnumber(parseFloat(parseFloat(arrstockqty[rId]) + parseFloat(tempqty) - checkqtyingrid(stockcode,rId))));
					return false;
				}
				
				if (typeof(idnodel) != "undefined"){
					var sqty = s_in_array_ri(rId,idnodel);
					if (sqty != -1){
						if (parseFloat(qtytemp) < parseFloat(idreturn[sqty])){
							alert("Jumlah barang tidak bisa lebih kecil dari barang yang telah diretur, stok yang diretur : "+formatnumber(idreturn[sqty]));
							return false;
						}
					}
				}
				
				mygrid.cells(rId,5).setValue(formatnumber(qty));
			}
			else if (cInd == 6){
				if (price != "" && arrunits[rId].length > 0){
					var XK = s_in_array_ri(mygrid.cells(rId,6).getValue(),arrunits[rId]);
					var checkedprice = price;
					if (XK != -1){
						checkedprice = price / arrconversion[rId][XK];
					}
					if (parseFloat(checkedprice) < parseFloat(arrsaleprice[rId])){
						alert("Harga barang tidak boleh di bawah harga jual : "+formatnumber(arrsaleprice[rId]));
					}
				}
				
				if (typeof(tempqtyforedit[rId]) == "undefined" || tempqtyforedit[rId] == "" || stockcode != tempqtycode[rId]){
					tempqtyforedit[rId] = 0;
				}
				var qtytemp;
				if (qty != ""){
					if (arrunits[rId].length > 0){
						var XK = s_in_array_ri(mygrid.cells(rId,6).getValue(),arrunits[rId]);
						if (XK != -1){
							qtytemp = qty*arrconversion[rId][XK];
						}
						else{
							qtytemp = qty;
						}

						var XKs = s_in_array_ri(tempunitsc[rId],arrunits[rId]);
						if (XKs != -1){
							tempqty = tempqtyforedit[rId] * arrconversion[rId][XKs];
						}
						else{
							tempqty = tempqtyforedit[rId];
						}
					}
					else{
						qtytemp = qty;
						tempqty = tempqtyforedit[rId];
					}
					if (parseFloat(qtytemp) > parseFloat(parseFloat(arrstockqty[rId]) + parseFloat(tempqty) - checkqtyingrid(stockcode,rId))){
						alert("Jumlah barang melebihi stok, stok sekarang : "+formatnumber(parseFloat(parseFloat(arrstockqty[rId]) + parseFloat(tempqty) - checkqtyingrid(stockcode,rId))));
						return false;
					}
					
					if (typeof(idnodel) != "undefined"){
						var sqty = s_in_array_ri(rId,idnodel);
						if (sqty != -1){
							if (parseFloat(qtytemp) < parseFloat(idreturn[sqty])){
								alert("Jumlah barang tidak bisa lebih kecil dari barang yang telah diretur, stok yang diretur : "+formatnumber(idreturn[sqty]));
								return false;
							}
						}
					}
				}
			}
			else if (cInd == 7){				
				var XK = s_in_array_ri(mygrid.cells(rId,6).getValue(),arrunits[rId]);
				var checkedprice = price;
				if (XK != -1){
					checkedprice = price / arrconversion[rId][XK];
				}
				
				if (!chknumprice){
					alert("Harga harus dalam angka");
					return false;
				}
				else if (parseFloat(checkedprice) < parseFloat(arrsaleprice[rId])){
					alert("Harga barang tidak boleh di bawah harga jual : "+formatnumber(arrsaleprice[rId]));
					//return false;
				}

				mygrid.cells(rId,7).setValue(formatnumber(price));
			}
			else if (cInd == 8){
				if (!chknumdisc){
					alert("Diskon harus dalam angka");
					return false;
				}
				mygrid.cells(rId,8).setValue(formatnumber(disc));
			}
			
			if (chknumqty && chknumprice){
				var disct = 0;
				var totals = qty * price;
				if (chknumdisc){
					disct = (disc * totals) / 100;
				}
				var resultcalc = (totals-disct).toFixed(2);
				mygrid.cells(rId,9).setValue(formatnumber(resultcalc));
			}
			else{
				mygrid.cells(rId,9).setValue(0);
			}
			
			countgrandtotal();
		}
	}
	return true;
}