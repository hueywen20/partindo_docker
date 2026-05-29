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
}

var arrunits = new Array();
var arrconversion = new Array();

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
		}
	})
}

function editingdeassembly(stage,rId,cInd){
	if (stage == 0){
		/* if (cInd == 1){
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
		else  */if (cInd == 6){
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
		else if (cInd == 5){
			var qty = mygrid.cells(rId,5).getValue();
			qty = replacestr(replacestr(qty,".",""),",",".");
			var chknumqty = IsNumeric(qty);
			
			if (!chknumqty){
				alert("Quantity harus dalam angka");
				return false;
			}
			mygrid.cells(rId,5).setValue(formatnumber(qty));
		}
		else if (cInd == 7){
			var price = mygrid.cells(rId,7).getValue();
			price = replacestr(replacestr(price,".",""),",",".");
			var chknums = IsNumeric(price);
			
			if (!chknums){
				alert("Harga harus dalam angka");
				return false;
			}
			mygrid.cells(rId,7).setValue(formatnumber(price));
		}
	}
	return true;
}