var heightauto = document.documentElement.clientHeight-50;

$.window.prepare({
	dock: 'bottom',       // change the dock direction: 'left', 'right', 'top', 'bottom'
	animationSpeed: 200,  // set animation speed
	minWinLong: 180       // set minimized window long dimension width in pixel
});
function showBrand(){
	var urls = "brand.php";
	//if (!$.window.searchWindow(urls)){
		$.window({
			title: "Pengaturan Merek",
			url: urls,
			width: "99%",
			height: heightauto,
			resizable:true
		});
	//}
	
	closeall(1);
}
function showFirstStock(){   
	$.window({
		title: "Pengaturan Stok Awal",
		url: "firststock.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showArea(){   
	$.window({
		title: "Pengaturan Kota",
		url: "area.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showCountry(){   
$.window({
		title: "Pengaturan Negara",
		url: "country.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showType(){   
$.window({
		title: "Pengaturan Tipe",
		url: "type.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showUnits(){   
$.window({
		title: "Pengaturan Satuan",
		url: "units.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showStockGroup(){   
$.window({
		title: "Pengaturan Grup Stok",
		url: "stockgroup.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showState(){   
$.window({
		title: "Pengaturan Propinsi",
		url: "state.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showLocation(){   
$.window({
		title: "Pengaturan Lokasi",
		url: "location.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showCustomer(){   
$.window({
		title: "Pengaturan Customer",
		url: "customer.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showSupplier(){   
$.window({
		title: "Pengaturan Supplier",
		url: "supplier.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}
function showStockReport(){   
$.window({
		title: "Laporan Stok per Tanggal",
		url: "reportstock.php?view=period",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showStockCard(){   
$.window({
		title: "Laporan Kartu Stok per Periode",
		url: "reportstock.php?view=stockcard",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showExpiredStock(){   
$.window({
		title: "Laporan Stok Expired",
		url: "reportstock.php?view=expired",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showStuffListReport(){
$.window({
		title: "Laporan Daftar Barang",
		url: "reportstufflist.php",
		width: 500,
		height: 550,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showSalePC(){   
$.window({
		title: "Laporan Penjualan per Periode per Customer",
		url: "reportsale.php?view=periodcustomer",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showSaleDD(){   
$.window({
		title: "Laporan Penjualan per Tanggal Jatuh Tempo",
		url: "reportsale.php?view=duedate",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showSaleArea(){   
$.window({
		title: "Laporan Penjualan per Area / Kota",
		url: "reportsalearea.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showSaleMonth(){   
$.window({
		title: "Laporan Penjualan per Bulan",
		url: "reportsale.php?view=monthly",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showPurchasePC(){   
$.window({
		title: "Laporan Pembelian per Periode per Supplier",
		url: "reportpurchase.php?view=periodsupplier",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showPurchaseDD(){   
$.window({
		title: "Laporan Pembelian per Tanggal Jatuh Tempo",
		url: "reportpurchase.php?view=duedate",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showPurchaseArea(){   
$.window({
		title: "Laporan Pembelian per Area / Kota",
		url: "reportpurchasearea.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showPurchaseMonth(){   
$.window({
		title: "Laporan Pembelian per Bulan",
		url: "reportpurchase.php?view=monthly",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showBuyReturnPC(){   
$.window({
		title: "Laporan Retur Pembelian per Periode per Supplier",
		url: "reportbuyreturn.php?view=periodsupplier",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showSaleReturnPC(){   
$.window({
		title: "Laporan Retur Penjualan per Periode per Customer",
		url: "reportsalereturn.php?view=periodcustomer",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showActiveStuff(){   
$.window({
		title: "Laporan Barang Paling Aktif per Periode",
		url: "reportactivestuff.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}
function showProfitLoss(){   
$.window({
		title: "Laporan Laba/Rugi per Periode",
		url: "reportprofitloss.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showProfitLossInvoice(){   
$.window({
		title: "Laporan Laba/Rugi per Faktur",
		url: "reportprofitlossinvoice.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showProfitLossMonthly(){   
$.window({
		title: "Laporan Laba/Rugi per Bulan",
		url: "reportprofitlossmonthly.php",
		width: "80%",
		height: heightauto,
		x: -1,
		resizable:true
	});
	
	closeall(1);
}

function showPaymentReport(){   
$.window({
		title: "Laporan Penagihan Piutang",
		url: "reportpayment.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showPaydebtReport(){   
$.window({
		title: "Laporan Pembayaran Hutang",
		url: "reportpaydebt.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showUnpaidSale(){   
$.window({
		title: "Laporan Faktur Penjualan yang Belum Lunas (Piutang)",
		url: "reportunpaidsale.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showUnpaidBuy(){   
$.window({
		title: "Laporan Bon Pembelian yang Belum Lunas (Hutang)",
		url: "reportunpaidbuy.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showStockList(){   
$.window({
		title: "Daftar Barang",
		url: "stock.php",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordstuff");
			deleteCookie("fieldstuff");
		}
	});
	
	closeall(1);
}

function showNewPurchase(){   
$.window({
		title: "Pembelian Baru",
		url: "purchase.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showPurchaseList(){   
$.window({
		title: "Daftar Pembelian",
		url: "purchase.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordpurchase");
			deleteCookie("fieldpurchase");
			deleteCookie("trtypepurchase");
		}
	});
	
	closeall(1);
}

function showNewSale(){   
$.window({
		title: "Penjualan Baru",
		url: "sale.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showSaleList(){   
$.window({
		title: "Daftar Penjualan",
		url: "sale.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordsale");
			deleteCookie("fieldsale");
			deleteCookie("trtypesale");
		}
	});
	
	closeall(1);
}

function showNewPurchaseR(){   
$.window({
		title: "Retur Pembelian Baru",
		url: "purchaser.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showPurchaseRList(){   
$.window({
		title: "Daftar Retur Pembelian",
		url: "purchaser.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordbuyr");
			deleteCookie("fieldbuyr");
		}
	});
	
	closeall(1);
}

function showNewSaleR(){   
$.window({
		title: "Retur Penjualan Baru",
		url: "saler.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showSaleRList(){   
$.window({
		title: "Daftar Retur Penjualan",
		url: "saler.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordsaler");
			deleteCookie("fieldsaler");
		}
	});
	
	closeall(1);
}

function showNewAdjustIn(){   
$.window({
		title: "Penyesuaian Stok (+)",
		url: "adjustin.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showAdjustInList(){   
$.window({
		title: "Daftar Penyesuaian Stok (+)",
		url: "adjustin.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordain");
			deleteCookie("fieldain");
		}
	});
	
	closeall(1);
}

function showNewAdjustOut(){   
$.window({
		title: "Penyesuaian Stok (-)",
		url: "adjustout.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showAdjustOutList(){   
$.window({
		title: "Daftar Penyesuaian Stok (-)",
		url: "adjustout.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordaout");
			deleteCookie("fieldaout");
		}
	});
	
	closeall(1);
}

function showNewAssembly(){   
$.window({
		title: "Rakit Baru",
		url: "assembly.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showAssemblyList(){   
$.window({
		title: "Daftar Barang Assembly",
		url: "assembly.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keywordassembly");
			deleteCookie("fieldassembly");
		}
	});
	
	closeall(1);
}

function showNewDeAssembly(){   
$.window({
		title: "Buat Barang Pecahan Baru",
		url: "deassembly.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showDeAssemblyList(){   
$.window({
		title: "Daftar Barang Pecahan",
		url: "deassembly.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("keyworddeassembly");
			deleteCookie("fielddeassembly");
		}
	});
	
	closeall(1);
}

function showNewPayment(){   
$.window({
		title: "Buat Penagihan Piutang Baru",
		url: "payment.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showPaymentList(){   
$.window({
		title: "Daftar Penagihan Piutang",
		url: "payment.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("searchfieldpay");
			deleteCookie("startpaydate");
			deleteCookie("endpaydate");
			deleteCookie("searchfieldkeyw");
		}
	});
	
	closeall(1);
}

function showNewPayDebt(){   
$.window({
		title: "Buat Pembayaran Hutang Baru",
		url: "paydebt.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showPayDebtList(){   
$.window({
		title: "Daftar Pembayaran Hutang",
		url: "paydebt.php?screen=list",
		width: "99%",
		height: heightauto,
		resizable:true,
		onClose: function(wnd) {
			deleteCookie("searchfieldpayd");
			deleteCookie("startpayddate");
			deleteCookie("endpayddate");
			deleteCookie("searchfieldkeywd");
		}
	});
	
	closeall(1);
}

function showUserGroup(){   
$.window({
		title: "Pengaturan Grup User",
		url: "usergroup.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showUser(){   
$.window({
		title: "Pengaturan User",
		url: "user.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showCodes(){   
$.window({
		title: "Pengaturan Kode",
		url: "codes.php",
		width: "99%",
		height: heightauto,
		resizable:true
	});
	
	closeall(1);
}

function showSettings(){   
$.window({
		title: "Pengaturan Lain-Lain",
		url: "settings.php",
		width: 800,
		height: 500,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showReportPaydebtsale(){   
$.window({
		title: "Laporan Jangka Waktu Pelunasan Piutang",
		url: "reportlongdebt.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showCloseBook(){   
$.window({
		title: "Tutup Buku",
		url: "closebook.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function showActivity(){
$.window({
		title: "Laporan Aktivitas",
		url: "reportactivity.php",
		width: 500,
		height: 400,
		x: -1,
		y: -1,
		resizable:true
	});
	
	closeall(1);
}

function loadiniframe(file,mode){
	var A = document.getElementsByTagName("iframe");
	if (A.length > 0){
		for (var i = 0; i < A.length; i++){
			if (A[i].src.indexOf(file) != -1){
				A[i].contentWindow.loadChange(mode);
				break;
			}
		}
	}
}

function minimizeallwindow(){
	$.window.minAll();
}