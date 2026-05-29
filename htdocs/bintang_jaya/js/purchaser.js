function setPurchase(buyno,buydate){
	if (buyno != ""){
		var A = new Array();
		A.push(buyno);
		if (buydate != "")
			A.push(buydate);
		$("#purchasedetail").html(A.join(" | "));
		$("#buyno").val(buyno);
		$("#buydate").val(buydate);
		$.ajax({
			type: 'GET',
			url: 'purchase.php',
			data: 'getlist=ajax&list=supplier&no='+encodeURIComponent(buyno),
			success: function(data) {
				if (data != ""){
					var B = data.split("|^|");
					if (B.length > 0){
						var C = new Array();
						C.push(B[0]);
						if (typeof(B[2]) != "undefined" && B[2] != ""){
							C.push(B[2]);
						}
						if (typeof(B[3]) != "undefined" && B[3] != ""){
							C.push(B[3]);
						}
						if (typeof(B[4]) != "undefined" && B[4] != ""){
							C.push(B[4]);
						}
						if (typeof(B[5]) != "undefined" && B[5] != ""){
							C.push(B[5]);
						}
						$("#supplierdetail").html(C.join(" - "));
						$("#suppliercode").val(B[0]);
						$("#supplieraddrid").val(B[1]);
					}
				}
			}
		});
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
		//mygrid.clearAll();
		mygrids.clearAll();
		mygrids.loadXML("purchaser.php?getlist=xml&list=getsupplierstuff&code="+encodeURIComponent(code));
	}
}

function countgrandtotal(obj){
	//count all sub total
	var totalrow = mygrid.getRowsNum();
	if (totalrow > 0){
		var totals = 0;
		for (var b = 0; b < totalrow; b++){
			totals = parseFloat(parseFloat(totals) + parseFloat(replacestr(replacestr(mygrid.cells(mygrid.getRowId(b),13).getValue(),".",""),",",".")));
		}
		$("#totals").val(totals);
	}
	else{
		$("#totals").val(0);
	}
	
	var subtotal = $("#totals").val();
	var grandtotal = $("#totalbuyr");
	var grandtotalel = $("#grandtotal");
	var gt = subtotal;
	if (IsNumeric(gt)){
		grandtotal.val(gt);
		grandtotalel.html(formatnumber(gt));		
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
	mygrid.cells(rId,10).setValue("");
	mygrid.cells(rId,11).setValue("");
	mygrid.cells(rId,12).setValue("");
	mygrid.cells(rId,13).setValue("");
	mygrid.cells(rId,14).setValue("");
}

var arrunits = new Array();
var arrconversion = new Array();
var arrmaxr = new Array();
var tempqtyforedit = new Array();

function editingpurchaser(stage,rId,cInd){
	if (stage == 0){
		if (cInd == 0){
			var buyno = $("#buyno").val();
			if (buyno != ""){
				setNewComboValue("ajax.php?list=getstockcode&buyno="+encodeURIComponent(buyno),0);
			}
		}
		else if (cInd == 1){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=partno&stockcode="+encodeURIComponent(stockcode),1);
			}
		}
		else if (cInd == 3){
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
		}
		else if (cInd == 7){
			var stockcode = mygrid.cells(rId,0).getValue();
			if (stockcode != ""){
				setNewComboValue("ajax.php?list=unitpurchase&stockcode="+encodeURIComponent(stockcode),7);
			}
		}
	}
	if (stage == 2){
		if (cInd == 0){
			var stockcode = mygrid.cells(rId,0).getValue();
			var buyno = $("#buyno").val();
			clearrowgrid(rId);
			if (stockcode != "" && buyno != ""){
				$.ajax({
					type: 'GET',
					url: 'ajax.php?list=getstockforreturn&buyno='+buyno+'&stockcode='+encodeURIComponent(stockcode),
					success: function(data) {
						if (data == "false"){
							alert("Tidak ada kode barang "+stockcode+" di faktur pembelian ini.");
							return false;	
						}
						else{
							var arrdata = data.split("|^|");
							mygrid.cells(rId,2).setValue(arrdata[0]);
							
							var splitunits = arrdata[1].split(",");
							arrunits[rId] = new Array();
							for (var th = 0; th < splitunits.length; th++){
								arrunits[rId][th] = splitunits[th];
							}
							var splitconv = arrdata[2].split(",");
							arrconversion[rId] = new Array();
							for (var th = 0; th < splitconv.length; th++){
								arrconversion[rId][th] = splitconv[th];
							}
							
							arrmaxr[rId] = arrdata[3];
							
							var qty = mygrid.cells(rId,5).getValue();
							var price = mygrid.cells(rId,7).getValue();
							var disc = mygrid.cells(rId,8).getValue();
							var extdisc = mygrid.cells(rId,9).getValue();
							var tax = mygrid.cells(rId,10).getValue();
							qty = replacestr(replacestr(qty,".",""),",",".");
							price = replacestr(replacestr(price,".",""),",",".");
							disc = replacestr(replacestr(disc,".",""),",",".");
							extdisc = replacestr(replacestr(extdisc,".",""),",",".");
							tax = replacestr(replacestr(tax,".",""),",",".");
							var chknumqty = IsNumeric(qty);
							var chknumprice = IsNumeric(price);
							var chknumdisc = IsNumeric(disc);
							var chknumextdisc = IsNumeric(extdisc);
							var chknumtax = IsNumeric(tax);
							
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
									totals = parseFloat(parseFloat(totals) + parseFloat(replacestr(replacestr(mygrid.cells(mygrid.getRowId(b),10).getValue(),".",""),",",".")));
								}
								$("#totals").val(totals);
								$("#subtotal").html(formatnumber(totals));
								countgrandtotal();
							}
						}
					}
				})
			}
			else{
				mygrid.cells(rId,2).setValue("");
			}
		}
		else if (cInd == 6 || cInd == 7 || cInd == 8 || cInd == 9 || cInd == 10 || cInd == 11 || cInd == 12){
			var qty = mygrid.cells(rId,6).getValue();
			var price = mygrid.cells(rId,8).getValue();
			var disc = mygrid.cells(rId,9).getValue();
			var extdisc = mygrid.cells(rId,10).getValue();
			var tax = mygrid.cells(rId,11).getValue();
			var otherpays = mygrid.cells(rId,12).getValue();
			qty = replacestr(replacestr(qty,".",""),",",".");
			price = replacestr(replacestr(price,".",""),",",".");
			disc = replacestr(replacestr(disc,".",""),",",".");
			extdisc = replacestr(replacestr(extdisc,".",""),",",".");
			tax = replacestr(replacestr(tax,".",""),",",".");
			otherpays = replacestr(replacestr(otherpays,".",""),",",".");
			var chknumqty = IsNumeric(qty);
			var chknumprice = IsNumeric(price);
			var chknumdisc = IsNumeric(disc);
			var chknumextdisc = IsNumeric(extdisc);
			var chknumtax = IsNumeric(tax);
			var chknumotherpays = IsNumeric(otherpays);
			
			if (cInd == 6){
				if (typeof(tempqtyforedit[rId]) == "undefined" || tempqtyforedit[rId] == "" || stockcode != tempqtycode[rId]){
					tempqtyforedit[rId] = 0;
				}
				if (!chknumqty){
					alert("Quantity harus dalam angka");
					return false;
				}
				//var qtytemp;
				/*if (arrunits[rId].length > 0){
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
				}*/
				var rrm = parseFloat(parseFloat(arrmaxr[rId]) + parseFloat(tempqtyforedit[rId]));
				if (parseFloat(qty) > rrm){
					alert("Jumlah barang yang diretur tidak bisa lebih besar dari stok yang tersisa dari pembelian ini, stok yang tersisa : "+formatnumber(rrm));
					return false;
				}
				mygrid.cells(rId,6).setValue(formatnumber(qty));
			}
			else if (cInd == 7){
				if (price != "" && arrunits[rId].length > 0){
					var XK = s_in_array_ri(mygrid.cells(rId,7).getValue(),arrunits[rId]);
					if (XK != -1){
						price = price*arrconversion[rId][XK];
						mygrid.cells(rId,8).setValue(formatnumber(price));
					}
				}
				
				if (typeof(tempqtyforedit[rId]) == "undefined" || tempqtyforedit[rId] == "" || stockcode != tempqtycode[rId]){
					tempqtyforedit[rId] = 0;
				}
				var qtytemp;
				if (qty != ""){
					if (arrunits[rId].length > 0){
						var XK = s_in_array_ri(mygrid.cells(rId,7).getValue(),arrunits[rId]);
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
						alert("Jumlah barang yang diretur tidak bisa lebih besar dari stok yang tersisa dari pembelian ini, stok yang tersisa : "+formatnumber(arrmaxr[rId]));
						return false;
					}
				}
			}
			else if (cInd == 8){
				if (!chknumprice){
					alert("Harga harus dalam angka");
					return false;
				}
				mygrid.cells(rId,8).setValue(formatnumber(price));
				
				var XK = s_in_array_ri(mygrid.cells(rId,6).getValue(),arrunits[rId]);
				if (XK != -1){
					price = price*arrconversion[rId][XK];
				}
				mygrid.cells(rId,8).setValue(formatnumber(price));
			}
			else if (cInd == 9){
				if (!chknumdisc){
					alert("Diskon harus dalam angka");
					return false;
				}
				mygrid.cells(rId,9).setValue(formatnumber(disc));
			}
			else if (cInd == 10){
				if (!chknumextdisc){
					alert("Diskon Bon harus dalam angka");
					return false;
				}
				mygrid.cells(rId,10).setValue(formatnumber(extdisc));
			}
			else if (cInd == 11){
				if (!chknumtax){
					alert("PPN harus dalam angka");
					return false;
				}
				mygrid.cells(rId,11).setValue(formatnumber(tax));
			}
			else if (cInd == 12){
				if (!chknumotherpays){
					alert("Biaya lain-lain harus dalam angka");
					return false;
				}
				mygrid.cells(rId,12).setValue(formatnumber(otherpays));
			}
			
			if (chknumqty && chknumprice){
				var disct = 0;
				var totals = qty * price;
				if (chknumdisc){
					disct = (disc * totals) / 100;
					totals -= disct;
				}
				if (chknumextdisc){
					disct = (extdisc * totals) / 100;
					totals -= disct;
				}
				if (chknumtax){
					disct = (tax * totals) / 100;
					totals += disct;
				}
				if (chknumotherpays){
					totals += parseFloat(otherpays) * qty;
				}
				mygrid.cells(rId,13).setValue(formatnumber(totals.toFixed(2)));
			}
			else{
				mygrid.cells(rId,13).setValue(0);
			}
			
			//count all sub total
			var totalrow = mygrid.getRowsNum();
			if (totalrow > 0){
				var totals = 0;
				for (var b = 0; b < totalrow; b++){
					totals = parseFloat(parseFloat(totals) + parseFloat(replacestr(replacestr(mygrid.cells(mygrid.getRowId(b),13).getValue(),".",""),",",".")));
				}
				$("#totals").val(totals);
				countgrandtotal();
			}
		}
	}
	return true;
}