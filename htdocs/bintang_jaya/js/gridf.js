function adddetailsupplier(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailsupplier(){
	mygrid.deleteSelectedRows();
}

function adddetailcustomer(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailcustomer(){
	mygrid.deleteSelectedRows();
}

function adddetailpurchase(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailpurchase(){
	if (typeof(idnodel) != "undefined"){
		if (!s_in_array(mygrid.getSelectedRowId(),idnodel)){
			mygrid.deleteSelectedRows();
			countgrandtotal();
		}
		else{
			alert("Data pembelian yang dipilih tidak dapat dihapus, karena telah terjadi transaksi penjualan dari data ini");
		}
	}
	else{
		mygrid.deleteSelectedRows();
		countgrandtotal();
	}
}

function adddetailsale(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailsale(){
	mygrid.deleteSelectedRows();
	countgrandtotal();
}

function adddetailpurchaser(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailpurchaser(){
	mygrid.deleteSelectedRows();
	countgrandtotal();
}

function adddetailsaler(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailsaler(){
	mygrid.deleteSelectedRows();
	countgrandtotal();
}

function adddetailain(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailain(){
	mygrid.deleteSelectedRows();
	countgrandtotal();
}

function adddetailaout(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailaout(){
	mygrid.deleteSelectedRows();
	countgrandtotal();
}

function adddetailpayment(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailpayment(){
	mygrid.deleteSelectedRows();
	countgrandtotal();
}

function adddetailassembly(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailassembly(){
	mygrid.deleteSelectedRows();
}

function adddetaildeassembly(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetaildeassembly(){
	mygrid.deleteSelectedRows();
}

function addrepayment(){
	mygridrp.addRow(mygridrp.uid(), "");
}

function deleterepayment(){
	mygridrp.deleteSelectedRows();
}

function addrepaydebt(){
	mygridrp.addRow(mygridrp.uid(), "");
}

function deleterepaydebt(){
	mygridrp.deleteSelectedRows();
}

function adddetailoperational(){
	mygrid.addRow(mygrid.uid(), "");
}

function deletedetailoperational(){
	mygrid.deleteSelectedRows();
}