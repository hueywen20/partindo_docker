function countgrandtotal(){			
	//count all sub total
	var totalrow = mygrid.getRowsNum();
	if (totalrow > 0){
		var totals = 0;
		for (var b = 0; b < totalrow; b++){
			var subvalue = mygrid.cells(mygrid.getRowId(b),1).getValue();
			subvalue = replacestr(replacestr(subvalue,".",""),",",".");
			if (IsNumeric(subvalue)){
				totals = parseFloat(parseFloat(totals) + parseFloat(subvalue));
			}
		}
		$("#totals").val(totals);
		$("#grandtotal").html(formatnumber(totals));
	}
	else{
		$("#totals").val(0);
		$("#grandtotal").html("0,00");
	}
}

function editingoperational(stage,rId,cInd){
	if (stage == 2){
		if (cInd == 1){
			var price = mygrid.cells(rId,1).getValue();
			price = replacestr(replacestr(price,".",""),",",".");
			var chknumprice = IsNumeric(price);
			
			if (!chknumprice){
				alert("Jumlah harus dalam angka");
				return false;
			}
			mygrid.cells(rId,1).setValue(formatnumber(price));
			
			countgrandtotal();
		}
	}
	return true;
}