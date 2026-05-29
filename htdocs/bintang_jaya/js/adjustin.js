function countgrandtotal(obj){
	//count all sub total
	var grandtotal = $("#totalain");
	var grandtotalel = $("#grandtotal");
	
	var totalrow = mygrid.getRowsNum();
	if (totalrow > 0){
		var totals = 0;
		for (var b = 0; b < totalrow; b++){
			totals = parseFloat(parseFloat(totals) + parseFloat(replacestr(replacestr(mygrid.cells(mygrid.getRowId(b),8).getValue(),".",""),",",".")));
		}
		grandtotal.val(totals);
		grandtotalel.html(formatnumber(totals));		
	}
	else{
		grandtotal.val(0);
		grandtotalel.html("0");
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
}

var arrunits = new Array();
var arrconversion = new Array();
var arrmaxr = new Array();
var tempqtyforedit = new Array();

function fillotherfield(stockcode,rId,fillpartno){
	$.ajax({
		type: 'GET',
		url: 'ajax.php?list=getstockname&stockcode='+encodeURIComponent(stockcode),
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
			
			mygrid.cells(rId,7).setValue(formatnumber(arrdata[9]));

			arrmaxr[rId] = arrdata[5];
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
			qty = replacestr(replacestr(qty,".",""),",",".");
			price = replacestr(replacestr(price,".",""),",",".");
			var chknumqty = IsNumeric(qty);
			var chknumprice = IsNumeric(price);
			
			if (chknumqty && chknumprice){
				var totals = qty * price;
				mygrid.cells(rId,8).setValue(formatnumber(totals.toFixed(2)));
			}
			else{
				mygrid.cells(rId,8).setValue(0);
			}
			
			countgrandtotal();
		}
	})
}

function editingain(stage,rId,cInd){
	if (stage == 0){
		/* if (cInd == 1){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=partno&stockcode="+encodeURIComponent(stockcode),1);
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
		/* else  */if (cInd == 6){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=unitpurchase&stockcode="+encodeURIComponent(stockcode),6);
			}
		}
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
		else if (cInd == 5 || cInd == 6 || cInd == 7){
			var qty = mygrid.cells(rId,5).getValue();
			var price = mygrid.cells(rId,7).getValue();
			qty = replacestr(replacestr(qty,".",""),",",".");
			price = replacestr(replacestr(price,".",""),",",".");
			var chknumqty = IsNumeric(qty);
			var chknumprice = IsNumeric(price);
			
			if (cInd == 5){
				if (typeof(tempqtyforedit[rId]) == "undefined" || tempqtyforedit[rId] == "" || stockcode != tempqtycode[rId]){
					tempqtyforedit[rId] = 0;
				}
				if (!chknumqty){
					alert("Quantity harus dalam angka");
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
				if (parseFloat(qtytemp) > parseFloat(parseFloat(arrmaxr[rId]) + parseFloat(tempqty))){
					alert("Jumlah barang yang disesuaikan tidak bisa lebih besar dari total stok yang telah terpakai, total stok : "+formatnumber(arrmaxr[rId]));
					return false;
				}
				mygrid.cells(rId,5).setValue(formatnumber(qty));
			}
			else if (cInd == 6){
				if (price != "" && arrunits[rId].length > 0){
					var XK = s_in_array_ri(mygrid.cells(rId,6).getValue(),arrunits[rId]);
					if (XK != -1){
						price = price*arrconversion[rId][XK];
						mygrid.cells(rId,7).setValue(formatnumber(price));
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
					if (parseFloat(qtytemp) > parseFloat(parseFloat(arrmaxr[rId]) + parseFloat(tempqty))){
						alert("Jumlah barang yang disesuaikan tidak bisa lebih besar dari total stok yang telah terpakai, total stok : "+formatnumber(arrmaxr[rId]));
						return false;
					}
				}
			}
			else if (cInd == 7){
				if (!chknumprice){
					alert("Harga harus dalam angka");
					return false;
				}
				mygrid.cells(rId,7).setValue(formatnumber(price));
				
				var XK = s_in_array_ri(mygrid.cells(rId,6).getValue(),arrunits[rId]);
				if (XK != -1){
					price = price*arrconversion[rId][XK];
				}
				mygrid.cells(rId,7).setValue(formatnumber(price));
			}
			
			if (chknumqty && chknumprice){
				var totals = qty * price;
				mygrid.cells(rId,8).setValue(formatnumber(totals.toFixed(2)));
			}
			else{
				mygrid.cells(rId,8).setValue(0);
			}
			
			countgrandtotal();
		}
	}
	return true;
}