-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 23, 2013 at 01:19 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bitjykosong`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `accessid` int(11) NOT NULL auto_increment,
  `accessname` varchar(100) NOT NULL,
  `accessgroup` varchar(100) NOT NULL,
  `accessgroupparent` varchar(100) NOT NULL,
  `accesslabel` varchar(100) NOT NULL,
  `menulabel` varchar(100) NOT NULL,
  `jsaction` varchar(100) NOT NULL,
  `sublevel` int(2) NOT NULL,
  `menuorder` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`accessid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=526 ;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`accessid`, `accessname`, `accessgroup`, `accessgroupparent`, `accesslabel`, `menulabel`, `jsaction`, `sublevel`, `menuorder`, `status`) VALUES
(1, 'view_customer', 'Customer', 'Master', 'Lihat Daftar dan Detail', 'Customer', 'showCustomer()', 1, 1, 1),
(2, 'add_customer', 'Customer', 'Master', 'Tambah', '', '', 0, 2, 1),
(3, 'edit_customer', 'Customer', 'Master', 'Ubah', '', '', 0, 3, 1),
(4, 'delete_customer', 'Customer', 'Master', 'Hapus', '', '', 0, 4, 1),
(5, 'view_supplier', 'Supplier', 'Master', 'Lihat Daftar dan Detail', 'Supplier', 'showSupplier()', 1, 5, 1),
(6, 'add_supplier', 'Supplier', 'Master', 'Tambah', '', '', 0, 6, 1),
(7, 'edit_supplier', 'Supplier', 'Master', 'Ubah', '', '', 0, 7, 1),
(8, 'delete_supplier', 'Supplier', 'Master', 'Hapus', '', '', 0, 8, 1),
(9, 'view_brand', 'Merek', 'Master', 'Lihat Daftar dan Detail', 'Merek', 'showBrand()', 1, 9, 1),
(10, 'add_brand', 'Merek', 'Master', 'Tambah', '', '', 0, 10, 1),
(11, 'edit_brand', 'Merek', 'Master', 'Ubah', '', '', 0, 11, 1),
(12, 'delete_brand', 'Merek', 'Master', 'Hapus', '', '', 0, 12, 1),
(13, 'view_type', 'Tipe', 'Master', 'Lihat Daftar dan Detail', 'Tipe', 'showType()', 1, 13, 1),
(14, 'add_type', 'Tipe', 'Master', 'Tambah', '', '', 0, 14, 1),
(15, 'edit_type', 'Tipe', 'Master', 'Ubah', '', '', 0, 15, 1),
(16, 'delete_type', 'Tipe', 'Master', 'Hapus', '', '', 0, 16, 1),
(17, 'view_units', 'Satuan', 'Master', 'Lihat Daftar dan Detail', 'Satuan', 'showUnits()', 1, 17, 1),
(18, 'add_units', 'Satuan', 'Master', 'Tambah', '', '', 0, 18, 1),
(19, 'edit_units', 'Satuan', 'Master', 'Ubah', '', '', 0, 19, 1),
(20, 'delete_units', 'Satuan', 'Master', 'Hapus', '', '', 0, 20, 1),
(21, 'view_location', 'Lokasi', 'Master', 'Lihat Daftar dan Detail', 'Lokasi', 'showLocation()', 1, 21, 1),
(22, 'add_location', 'Lokasi', 'Master', 'Tambah', '', '', 0, 22, 1),
(23, 'edit_location', 'Lokasi', 'Master', 'Ubah', '', '', 0, 23, 1),
(24, 'delete_location', 'Lokasi', 'Master', 'Hapus', '', '', 0, 24, 1),
(25, 'view_stockgroup', 'Grup Stok', 'Master', 'Lihat Daftar dan Detail', 'Grup Stok', 'showStockGroup()', 1, 25, 1),
(26, 'add_stockgroup', 'Grup Stok', 'Master', 'Tambah', '', '', 0, 26, 1),
(27, 'edit_stockgroup', 'Grup Stok', 'Master', 'Ubah', '', '', 0, 27, 1),
(28, 'delete_stockgroup', 'Grup Stok', 'Master', 'Hapus', '', '', 0, 28, 1),
(29, 'view_firststock', 'Stok Awal', 'Master', 'Lihat Daftar dan Detail', 'Stok Awal', 'showFirstStock()', 1, 29, 1),
(30, 'add_firststock', 'Stok Awal', 'Master', 'Tambah', '', '', 0, 30, 1),
(31, 'edit_firststock', 'Stok Awal', 'Master', 'Ubah', '', '', 0, 31, 1),
(32, 'delete_firststock', 'Stok Awal', 'Master', 'Hapus', '', '', 0, 32, 1),
(33, 'view_city', 'Kota', 'Tambahan|Master', 'Lihat Daftar dan Detail', 'Kota', 'showArea()', 2, 33, 1),
(34, 'add_city', 'Kota', 'Tambahan|Master', 'Tambah', '', '', 0, 34, 1),
(35, 'edit_city', 'Kota', 'Tambahan|Master', 'Ubah', '', '', 0, 35, 1),
(36, 'delete_city', 'Kota', 'Tambahan|Master', 'Hapus', '', '', 0, 36, 1),
(37, 'view_state', 'Propinsi', 'Tambahan|Master', 'Lihat Daftar dan Detail', 'Propinsi', 'showState()', 2, 37, 1),
(38, 'add_state', 'Propinsi', 'Tambahan|Master', 'Tambah', '', '', 0, 38, 1),
(39, 'edit_state', 'Propinsi', 'Tambahan|Master', 'Ubah', '', '', 0, 39, 1),
(40, 'delete_state', 'Propinsi', 'Tambahan|Master', 'Hapus', '', '', 0, 40, 1),
(41, 'view_country', 'Negara', 'Tambahan|Master', 'Lihat Daftar dan Detail', 'Negara', 'showCountry()', 2, 41, 1),
(42, 'add_country', 'Negara', 'Tambahan|Master', 'Tambah', '', '', 0, 42, 1),
(43, 'edit_country', 'Negara', 'Tambahan|Master', 'Ubah', '', '', 0, 43, 1),
(44, 'delete_country', 'Negara', 'Tambahan|Master', 'Hapus', '', '', 0, 44, 1),
(45, 'view_purchase', 'Pembelian', 'Pembelian|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Pembelian', 'showPurchaseList()', 2, 46, 1),
(46, 'add_purchase', 'Pembelian', 'Pembelian|Transaksi', 'Tambah', 'Pembelian Baru', 'showNewPurchase()', 2, 45, 1),
(47, 'edit_purchase', 'Pembelian', 'Pembelian|Transaksi', 'Ubah', '', '', 0, 47, 1),
(48, 'delete_purchase', 'Pembelian', 'Pembelian|Transaksi', 'Hapus', '', '', 0, 48, 1),
(49, 'view_sale', 'Penjualan', 'Penjualan|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Penjualan', 'showSaleList()', 2, 50, 1),
(50, 'add_sale', 'Penjualan', 'Penjualan|Transaksi', 'Tambah', 'Penjualan Baru', 'showNewSale()', 2, 49, 1),
(51, 'edit_sale', 'Penjualan', 'Penjualan|Transaksi', 'Ubah', '', '', 0, 51, 1),
(52, 'delete_sale', 'Penjualan', 'Penjualan|Transaksi', 'Hapus', '', '', 0, 52, 1),
(54, 'view_purchaser', 'Retur Pembelian', 'Retur Pembelian|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Retur Pembelian', 'showPurchaseRList()', 2, 55, 1),
(55, 'add_purchaser', 'Retur Pembelian', 'Retur Pembelian|Transaksi', 'Tambah', 'Retur Pembelian Baru', 'showNewPurchaseR()', 2, 54, 1),
(56, 'edit_purchaser', 'Retur Pembelian', 'Retur Pembelian|Transaksi', 'Ubah', '', '', 0, 56, 1),
(57, 'delete_purchaser', 'Retur Pembelian', 'Retur Pembelian|Transaksi', 'Hapus', '', '', 0, 57, 1),
(58, 'view_saler', 'Retur Penjualan', 'Retur Penjualan|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Retur Penjualan', 'showSaleRList()', 2, 59, 1),
(59, 'add_saler', 'Retur Penjualan', 'Retur Penjualan|Transaksi', 'Tambah', 'Retur Penjualan Baru', 'showNewSaleR()', 2, 58, 1),
(60, 'edit_saler', 'Retur Penjualan', 'Retur Penjualan|Transaksi', 'Ubah', '', '', 0, 60, 1),
(61, 'delete_saler', 'Retur Penjualan', 'Retur Penjualan|Transaksi', 'Hapus', '', '', 0, 61, 1),
(62, 'view_adjustin', 'Penyesuaian Stok (+)', 'Penyesuaian Stok|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Penyesuaian Stok (+)', 'showAdjustInList()', 2, 63, 1),
(63, 'add_adjustin', 'Penyesuaian Stok (+)', 'Penyesuaian Stok|Transaksi', 'Tambah', 'Penyesuaian Stok (+)', 'showNewAdjustIn()', 2, 62, 1),
(64, 'edit_adjustin', 'Penyesuaian Stok (+)', 'Penyesuaian Stok|Transaksi', 'Ubah', '', '', 0, 64, 1),
(65, 'delete_adjustin', 'Penyesuaian Stok (+)', 'Penyesuaian Stok|Transaksi', 'Hapus', '', '', 0, 65, 1),
(66, 'view_adjustout', 'Penyesuaian Stok (-)', 'Penyesuaian Stok|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Penyesuaian Stok (-)', 'showAdjustOutList()', 2, 67, 1),
(67, 'add_adjustout', 'Penyesuaian Stok (-)', 'Penyesuaian Stok|Transaksi', 'Tambah', 'Penyesuaian Stok (-)', 'showNewAdjustOut()', 2, 66, 1),
(68, 'edit_adjustout', 'Penyesuaian Stok (-)', 'Penyesuaian Stok|Transaksi', 'Ubah', '', '', 0, 68, 1),
(69, 'delete_adjustout', 'Penyesuaian Stok (-)', 'Penyesuaian Stok|Transaksi', 'Hapus', '', '', 0, 69, 1),
(70, 'view_assembly', 'Assembly', 'Assembly|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Barang Assembly', 'showAssemblyList()', 2, 71, 1),
(71, 'add_assembly', 'Assembly', 'Assembly|Transaksi', 'Tambah', 'Rakit Baru', 'showNewAssembly()', 2, 70, 1),
(72, 'edit_assembly', 'Assembly', 'Assembly|Transaksi', 'Ubah', '', '', 0, 72, 1),
(73, 'delete_assembly', 'Assembly', 'Assembly|Transaksi', 'Hapus', '', '', 0, 73, 1),
(74, 'view_stocklist', 'Daftar Barang', 'Transaksi', 'Lihat Daftar Barang', 'Daftar Barang', 'showStockList()', 1, 86, 1),
(75, 'view_stockhistory', 'Daftar Barang', 'Transaksi', 'Lihat Kartu Stok', '', '', 0, 87, 1),
(501, 'report_minstock', 'L. Stok', 'L. Stok|Laporan', 'Stok Minimum', 'Stok Minimum', 'window.open(''reportstock.php?view=stockminimum'',''_new'')', 2, 88, 1),
(502, 'report_stock', 'L. Stok', 'L. Stok|Laporan', 'Stok per Tanggal', 'Stok per Tanggal', 'showStockReport()', 2, 89, 1),
(511, 'report_salepc', 'L. Penjualan', 'L. Penjualan|Laporan', 'Penjualan per Periode per Customer', 'Penjualan per Periode per Customer', 'showSalePC()', 2, 98, 1),
(512, 'report_saledd', 'L. Penjualan', 'L. Penjualan|Laporan', 'Penjualan per Tanggal Jatuh Tempo', 'Penjualan per Tanggal Jatuh Tempo', 'showSaleDD()', 2, 99, 1),
(514, 'report_salemonthly', 'L. Penjualan', 'L. Penjualan|Laporan', 'Penjualan per Bulan', 'Penjualan per Bulan', 'showSaleMonth()', 2, 101, 1),
(507, 'report_purchasepc', 'L. Pembelian', 'L. Pembelian|Laporan', 'Pembelian per Periode per Supplier', 'Pembelian per Periode per Supplier', 'showPurchasePC()', 2, 94, 1),
(508, 'report_purchasedd', 'L. Pembelian', 'L. Pembelian|Laporan', 'Pembelian per Tanggal Jatuh Tempo', 'Pembelian per Tanggal Jatuh Tempo', 'showPurchaseDD()', 2, 95, 1),
(510, 'report_purchasemonthly', 'L. Pembelian', 'L. Pembelian|Laporan', 'Pembelian per Bulan', 'Pembelian per Bulan', 'showPurchaseMonth()', 2, 97, 1),
(516, 'report_salerpc', 'L. Retur Penjualan', 'L. Retur Penjualan|Laporan', 'Retur Penjualan per Periode per Customer', 'Retur Penjualan per Periode per Customer', 'showSaleReturnPC()', 2, 103, 1),
(515, 'report_purchaserpc', 'L. Retur Pembelian', 'L. Retur Pembelian|Laporan', 'Retur Pembelian per Periode per Supplier', 'Retur Pembelian per Periode per Supplier', 'showBuyReturnPC()', 2, 102, 1),
(505, 'report_activestuff', 'L. Stok', 'L. Stok|Laporan', 'Barang Paling Aktif per Periode', 'Barang Paling Aktif per Periode', 'showActiveStuff()', 2, 92, 1),
(521, 'report_profitloss', 'L. Laba Rugi', 'L. Laba Rugi|Laporan', 'Laba/Rugi per Periode', 'Laba/Rugi per Periode', 'showProfitLoss()', 2, 108, 1),
(87, 'view_user', 'User', 'Setting', 'Lihat Daftar dan Detail', 'User', 'showUser()', 1, 112, 1),
(88, 'add_user', 'User', 'Setting', 'Tambah', '', '', 0, 113, 1),
(89, 'edit_user', 'User', 'Setting', 'Ubah', '', '', 0, 114, 1),
(90, 'delete_user', 'User', 'Setting', 'Hapus', '', '', 0, 115, 1),
(91, 'view_usergroup', 'Grup User', 'Setting', 'Lihat Daftar dan Detail', 'Grup User', 'showUserGroup()', 1, 116, 1),
(92, 'add_usergroup', 'Grup User', 'Setting', 'Tambah', '', '', 0, 117, 1),
(93, 'edit_usergroup', 'Grup User', 'Setting', 'Ubah', '', '', 0, 118, 1),
(94, 'delete_usergroup', 'Grup User', 'Setting', 'Hapus', '', '', 0, 119, 1),
(95, 'settings', 'Pengaturan Lain-Lain', 'Setting', 'Pengaturan Lain-Lain', 'Pengaturan Lain-Lain', 'showSettings()', 1, 120, 1),
(96, 'view_codes', 'Kode Konversi', 'Tambahan|Master', 'Lihat Daftar dan Detail', 'Kode', 'showCodes()', 2, 122, 1),
(97, 'add_codes', 'Kode Konversi', 'Tambahan|Master', 'Tambah', '', '', 0, 123, 1),
(98, 'edit_codes', 'Kode Konversi', 'Tambahan|Master', 'Ubah', '', '', 0, 124, 1),
(99, 'delete_codes', 'Kode Konversi', 'Tambahan|Master', 'Hapus', '', '', 0, 125, 1),
(504, 'report_stockcard', 'L. Stok', 'L. Stok|Laporan', 'Kartu Stok per Periode', 'Kartu Stok per Periode', 'showStockCard()', 2, 91, 1),
(76, 'view_deassembly', 'De-Assembly', 'De-Assembly|Transaksi', 'Lihat Daftar dan Detail', 'Daftar Barang Pecahan', 'showDeAssemblyList()', 2, 75, 1),
(77, 'add_deassembly', 'De-Assembly', 'De-Assembly|Transaksi', 'Tambah', 'Buat Pecahan Baru', 'showNewDeAssembly()', 2, 74, 1),
(78, 'edit_deassembly', 'De-Assembly', 'De-Assembly|Transaksi', 'Ubah', '', '', 0, 76, 1),
(79, 'delete_deassembly', 'De-Assembly', 'De-Assembly|Transaksi', 'Hapus', '', '', 0, 77, 1),
(522, 'report_profitloss_invoice', 'L. Laba Rugi', 'L. Laba Rugi|Laporan', 'Laba/Rugi per Faktur', 'Laba/Rugi per Faktur', 'showProfitLossInvoice()', 2, 109, 1),
(100, 'view_payment', 'Penagihan Piutang', 'Penagihan Piutang|Transaksi', 'Lihat Daftar dan Detail', 'Penagihan Piutang Baru', 'showNewPayment()', 2, 78, 1),
(101, 'add_payment', 'Penagihan Piutang', 'Penagihan Piutang|Transaksi', 'Tambah', 'Daftar Penagihan Piutang', 'showPaymentList()', 2, 79, 1),
(102, 'edit_payment', 'Penagihan Piutang', 'Penagihan Piutang|Transaksi', 'Ubah', '', '', 0, 80, 1),
(103, 'delete_payment', 'Penagihan Piutang', 'Penagihan Piutang|Transaksi', 'Hapus', '', '', 0, 81, 1),
(53, 'open_sale_access', 'Penjualan', 'Penjualan|Transaksi', 'Buka Akses Penjualan ke Customer', '', '', 0, 53, 1),
(104, 'view_paydebt', 'Pembayaran Hutang', 'Pembayaran Hutang|Transaksi', 'Lihat Daftar dan Detail', 'Pembayaran Hutang Baru', 'showNewPayDebt()', 2, 82, 1),
(105, 'add_paydebt', 'Pembayaran Hutang', 'Pembayaran Hutang|Transaksi', 'Tambah', 'Daftar Pembayaran Hutang', 'showPayDebtList()', 2, 83, 1),
(106, 'edit_paydebt', 'Pembayaran Hutang', 'Pembayaran Hutang|Transaksi', 'Ubah', '', '', 0, 84, 1),
(107, 'delete_paydebt', 'Pembayaran Hutang', 'Pembayaran Hutang|Transaksi', 'Hapus', '', '', 0, 85, 1),
(503, 'report_stockexpired', 'L. Stok', 'L. Stok|Laporan', 'Stok Expired', 'Stok Expired', 'showExpiredStock()', 2, 90, 1),
(506, 'report_stufflist', 'L. Stok', 'L. Stok|Laporan', 'Daftar Barang', 'Daftar Barang', 'showStuffListReport()', 2, 93, 1),
(509, 'report_purchasearea', 'L. Pembelian', 'L. Pembelian|Laporan', 'Pembelian per Area / Kota', 'Pembelian per Area / Kota', 'showPurchaseArea()', 2, 96, 1),
(513, 'report_salearea', 'L. Penjualan', 'L. Penjualan|Laporan', 'Penjualan per Area / Kota', 'Penjualan per Area / Kota', 'showSaleArea()', 2, 100, 1),
(517, 'report_payment', 'L. Penagihan Piutang', 'L. Penagihan Piutang|Laporan', 'Penagihan Piutang', 'Penagihan Piutang', 'showPaymentReport()', 2, 104, 1),
(519, 'report_paydebt', 'L. Pembayaran Hutang', 'L. Pembayaran Hutang|Laporan', 'Pembayaran Hutang', 'Pembayaran Hutang', 'showPaydebtReport()', 2, 106, 1),
(518, 'report_unpaidsale', 'L. Penagihan Piutang', 'L. Penagihan Piutang|Laporan', 'Faktur Penjualan yang Belum Lunas', 'Faktur Penjualan yang Belum Lunas', 'showUnpaidSale()', 2, 105, 1),
(520, 'report_unpaidbuy', 'L. Pembayaran Hutang', 'L. Pembayaran Hutang|Laporan', 'Bon Pembelian yang Belum Lunas', 'Bon Pembelian yang Belum Lunas', 'showUnpaidBuy()', 2, 107, 1),
(108, 'closebook', 'Tutup Buku', 'Setting', 'Tutup Buku per Tahun', 'Tutup Buku per Tahun', 'showCloseBook()', 0, 121, 1),
(523, 'report_activity', 'L. Aktivitas', 'Laporan', 'Laporan Aktivitas', 'L. Aktivitas', 'showActivity()', 1, 111, 1),
(524, 'report_profitlossmonthly', 'L. Laba Rugi', 'L. Laba Rugi|Laporan', 'Laba/Rugi per Bulan', 'Laba/Rugi per Bulan', 'showProfitLossMonthly()', 2, 110, 1),
(525, 'report_timetocomplete', 'L. Penagihan Piutang', 'L. Penagihan Piutang|Laporan', 'L.Jangka Waktu Pelunasan Hutang', 'L.Jangka Waktu Pelunasan Hutang', 'showReportPaydebtsale()', 2, 130, 1);

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE IF NOT EXISTS `area` (
  `areaid` int(11) NOT NULL auto_increment,
  `areacode` varchar(100) NOT NULL,
  `areaname` varchar(100) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`areaid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`areaid`, `areacode`, `areaname`, `createddate`, `createdby`, `lastedited`, `lasteditedby`, `status`) VALUES
(1, 'MDN', 'Medan', 1301296440, 1, 1307174577, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE IF NOT EXISTS `brand` (
  `brandid` int(11) NOT NULL auto_increment,
  `brandcode` varchar(100) NOT NULL,
  `brandname` varchar(100) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`brandid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `brand`
--


-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE IF NOT EXISTS `codes` (
  `id` int(11) NOT NULL auto_increment,
  `targets` varchar(255) NOT NULL,
  `replacements` varchar(255) NOT NULL,
  `replacements_sale` varchar(255) NOT NULL,
  `orders` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` (`id`, `targets`, `replacements`, `replacements_sale`, `orders`, `createddate`, `createdby`, `lastedited`, `lasteditedby`) VALUES
(1, '1', 'A', 'Z', 4, 1306234245, 1, 1306234245, 1),
(2, '2', 'B', 'Y', 6, 1305783318, 1, 0, 1),
(3, '3', 'C', 'X', 5, 1314805564, 1, 1314805564, 1),
(4, '4', 'D', 'W', 7, 0, 0, 0, 0),
(5, '5', 'E', 'V', 8, 0, 0, 0, 0),
(6, '6', 'F', 'U', 9, 0, 0, 0, 0),
(7, '7', 'G', 'T', 10, 0, 0, 0, 0),
(8, '8', 'H', 'S', 11, 0, 0, 0, 0),
(9, '9', 'I', 'R', 12, 1307182227, 1, 1307182227, 1),
(10, '0', 'J', 'Q', 3, 0, 0, 0, 0),
(11, '00', 'K', 'P', 2, 1356328712, 1, 1356328712, 1),
(12, '000', 'L', 'O', 1, 1314805309, 1, 1314805309, 1);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `countryid` int(11) NOT NULL auto_increment,
  `countrycode` varchar(100) NOT NULL,
  `countryname` varchar(100) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`countryid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`countryid`, `countrycode`, `countryname`, `createddate`, `createdby`, `lastedited`, `lasteditedby`, `status`) VALUES
(1, 'INA', 'Indonesia', 1301296440, 1, 1307174651, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE IF NOT EXISTS `customer` (
  `customerid` int(11) NOT NULL auto_increment,
  `customercode` varchar(100) NOT NULL,
  `customername` varchar(255) NOT NULL,
  `customertype` int(5) NOT NULL,
  `credit` decimal(14,2) NOT NULL,
  `remainingcredit` decimal(13,2) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`customerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `customer`
--


-- --------------------------------------------------------

--
-- Table structure for table `customertype`
--

CREATE TABLE IF NOT EXISTS `customertype` (
  `customertypeid` int(5) NOT NULL auto_increment,
  `customertypecode` varchar(100) NOT NULL,
  `customertypename` varchar(200) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` tinyint(4) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`customertypeid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `customertype`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailadjustin`
--

CREATE TABLE IF NOT EXISTS `detailadjustin` (
  `dainid` int(11) NOT NULL auto_increment,
  `ainid` int(11) NOT NULL,
  `aindate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `stockname` varchar(255) NOT NULL,
  `partno` varchar(255) NOT NULL,
  `brandcode` varchar(255) NOT NULL,
  `typecode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `quantityf` decimal(11,2) NOT NULL,
  `unitquantityf` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `ainprice` decimal(11,2) NOT NULL,
  `totalainprice` decimal(14,2) NOT NULL,
  `realainprice` decimal(11,2) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`dainid`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailadjustin`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailadjustinitem`
--

CREATE TABLE IF NOT EXISTS `detailadjustinitem` (
  `dainiid` int(11) NOT NULL auto_increment,
  `dainid` int(11) NOT NULL,
  `dbid` int(11) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`dainiid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailadjustinitem`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailadjustout`
--

CREATE TABLE IF NOT EXISTS `detailadjustout` (
  `daoutid` int(11) NOT NULL auto_increment,
  `aoutid` int(11) NOT NULL,
  `aoutdate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `stockname` varchar(255) NOT NULL,
  `partno` varchar(255) NOT NULL,
  `brandcode` varchar(255) NOT NULL,
  `typecode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `quantityf` decimal(11,2) NOT NULL,
  `unitquantityf` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `aoutprice` decimal(11,2) NOT NULL,
  `totalaoutprice` decimal(14,2) NOT NULL,
  `realaoutprice` decimal(11,2) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`daoutid`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailadjustout`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailadjustoutitem`
--

CREATE TABLE IF NOT EXISTS `detailadjustoutitem` (
  `daoutiid` int(11) NOT NULL auto_increment,
  `daoutid` int(11) NOT NULL,
  `dbid` int(11) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`daoutiid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailadjustoutitem`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailbuy`
--

CREATE TABLE IF NOT EXISTS `detailbuy` (
  `dbid` int(11) NOT NULL auto_increment,
  `buyno` varchar(255) NOT NULL,
  `buydate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `stockname` varchar(255) NOT NULL,
  `partno` varchar(255) NOT NULL,
  `brandcode` varchar(255) NOT NULL,
  `typecode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `quantityf` decimal(11,2) NOT NULL,
  `unitquantityf` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `buyprice` decimal(14,2) NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `buypricead` decimal(14,2) NOT NULL,
  `totalbuyad` decimal(14,2) NOT NULL,
  `realbuyprice` decimal(14,2) NOT NULL,
  `description` text NOT NULL,
  `expdate` int(11) NOT NULL,
  `usedqty` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`dbid`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailbuy`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailbuyr`
--

CREATE TABLE IF NOT EXISTS `detailbuyr` (
  `dbrid` int(11) NOT NULL auto_increment,
  `buyrid` int(11) NOT NULL,
  `buyrdate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `stockname` varchar(255) NOT NULL,
  `partno` varchar(255) NOT NULL,
  `brandcode` varchar(255) NOT NULL,
  `typecode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `quantityf` decimal(11,2) NOT NULL,
  `unitquantityf` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `buyrprice` decimal(14,2) NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `extdisc` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `otherpays` decimal(14,2) NOT NULL,
  `buyrpricead` decimal(14,2) NOT NULL,
  `totalbuyrad` decimal(14,2) NOT NULL,
  `realbuyrprice` decimal(14,2) NOT NULL,
  `description` text NOT NULL,
  `claims` int(1) NOT NULL,
  `paid` int(1) NOT NULL,
  `paydate` int(11) NOT NULL,
  PRIMARY KEY  (`dbrid`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailbuyr`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailbuyritem`
--

CREATE TABLE IF NOT EXISTS `detailbuyritem` (
  `dbriid` int(11) NOT NULL auto_increment,
  `dbrid` int(11) NOT NULL,
  `dbid` int(11) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`dbriid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailbuyritem`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailcustomer`
--

CREATE TABLE IF NOT EXISTS `detailcustomer` (
  `detailcustid` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL,
  `address` text NOT NULL,
  `areacode` varchar(100) NOT NULL,
  `statecode` varchar(100) NOT NULL,
  `countrycode` varchar(100) NOT NULL,
  `postalcode` varchar(50) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `fax` varchar(50) NOT NULL,
  `contactperson` varchar(100) NOT NULL,
  `mobilenumber` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`detailcustid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailcustomer`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailoperational`
--

CREATE TABLE IF NOT EXISTS `detailoperational` (
  `doid` int(11) NOT NULL auto_increment,
  `opid` int(11) NOT NULL,
  `notes` text NOT NULL,
  `total` decimal(13,2) NOT NULL,
  PRIMARY KEY  (`doid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailoperational`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailpaydebt`
--

CREATE TABLE IF NOT EXISTS `detailpaydebt` (
  `dpid` int(11) NOT NULL auto_increment,
  `hpid` int(11) NOT NULL,
  `hbid` int(11) NOT NULL,
  `pays` decimal(13,2) NOT NULL,
  `description` text NOT NULL,
  `types` varchar(100) NOT NULL,
  PRIMARY KEY  (`dpid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailpaydebt`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailpayment`
--

CREATE TABLE IF NOT EXISTS `detailpayment` (
  `dpid` int(11) NOT NULL auto_increment,
  `hpid` int(11) NOT NULL,
  `hsid` int(11) NOT NULL,
  `pays` decimal(13,2) NOT NULL,
  `description` text NOT NULL,
  `types` varchar(100) NOT NULL,
  PRIMARY KEY  (`dpid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailpayment`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailrepaydebt`
--

CREATE TABLE IF NOT EXISTS `detailrepaydebt` (
  `drpyid` int(11) NOT NULL auto_increment,
  `hpid` int(11) NOT NULL,
  `types` int(2) NOT NULL,
  `bank` varchar(255) NOT NULL,
  `accname` varchar(255) NOT NULL,
  `accnumber` varchar(255) NOT NULL,
  `dates` int(11) NOT NULL,
  `duedates` int(11) NOT NULL,
  `totals` decimal(13,2) NOT NULL,
  `notes` text NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`drpyid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailrepaydebt`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailrepayment`
--

CREATE TABLE IF NOT EXISTS `detailrepayment` (
  `drpyid` int(11) NOT NULL auto_increment,
  `hpid` int(11) NOT NULL,
  `types` int(2) NOT NULL,
  `bank` varchar(255) NOT NULL,
  `accname` varchar(255) NOT NULL,
  `accnumber` varchar(255) NOT NULL,
  `dates` int(11) NOT NULL,
  `duedates` int(11) NOT NULL,
  `totals` decimal(13,2) NOT NULL,
  `notes` text NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`drpyid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailrepayment`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailsale`
--

CREATE TABLE IF NOT EXISTS `detailsale` (
  `dsid` int(11) NOT NULL auto_increment,
  `saleno` varchar(255) NOT NULL,
  `saledate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `stockname` varchar(255) NOT NULL,
  `partno` varchar(255) NOT NULL,
  `brandcode` varchar(255) NOT NULL,
  `typecode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `quantityf` decimal(11,2) NOT NULL,
  `unitquantityf` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `saleprice` decimal(14,2) NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `salepricead` decimal(14,2) NOT NULL,
  `totalsalead` decimal(14,2) NOT NULL,
  `realsaleprice` decimal(14,2) NOT NULL,
  `description` text NOT NULL,
  `returnsale` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`dsid`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailsale`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailsaleitem`
--

CREATE TABLE IF NOT EXISTS `detailsaleitem` (
  `dsiid` int(11) NOT NULL auto_increment,
  `dsid` int(11) NOT NULL,
  `dbid` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `returnquantity` decimal(11,2) NOT NULL,
  `tabledbid` varchar(255) NOT NULL,
  PRIMARY KEY  (`dsiid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailsaleitem`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailsaler`
--

CREATE TABLE IF NOT EXISTS `detailsaler` (
  `dsrid` int(11) NOT NULL auto_increment,
  `salerid` int(11) NOT NULL,
  `dsid` int(11) NOT NULL,
  `salerdate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `stockname` varchar(255) NOT NULL,
  `partno` varchar(255) NOT NULL,
  `brandcode` varchar(255) NOT NULL,
  `typecode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `quantityf` decimal(11,2) NOT NULL,
  `unitquantityf` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `salerprice` decimal(14,2) NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `extdisc` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `salerpricead` decimal(14,2) NOT NULL,
  `totalsalerad` decimal(14,2) NOT NULL,
  `realsalerprice` decimal(14,2) NOT NULL,
  `description` text NOT NULL,
  `claims` int(1) NOT NULL,
  `paid` int(1) NOT NULL,
  `paydate` int(11) NOT NULL,
  PRIMARY KEY  (`dsrid`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailsaler`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailsaleritem`
--

CREATE TABLE IF NOT EXISTS `detailsaleritem` (
  `dsriid` int(11) NOT NULL auto_increment,
  `dsrid` int(11) NOT NULL,
  `dbid` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `tabledbid` varchar(255) NOT NULL,
  PRIMARY KEY  (`dsriid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailsaleritem`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailstockassembly`
--

CREATE TABLE IF NOT EXISTS `detailstockassembly` (
  `dsaid` int(11) NOT NULL auto_increment,
  `stockcode` varchar(255) NOT NULL,
  `stockcodecomponent` varchar(255) NOT NULL,
  `sccname` varchar(255) NOT NULL,
  `sccpartno` varchar(255) NOT NULL,
  `sccbrandcode` varchar(255) NOT NULL,
  `scctypecode` varchar(255) NOT NULL,
  `sccquantity` decimal(11,2) NOT NULL,
  `sccunitquantity` varchar(100) NOT NULL,
  `sccquantityf` decimal(11,2) NOT NULL,
  `sccunitquantityf` varchar(100) NOT NULL,
  `sccunitcode` varchar(255) NOT NULL,
  PRIMARY KEY  (`dsaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailstockassembly`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailstockdeassembly`
--

CREATE TABLE IF NOT EXISTS `detailstockdeassembly` (
  `dsdaid` int(11) NOT NULL auto_increment,
  `stockcode` varchar(255) NOT NULL,
  `stockcodecomponent` varchar(255) NOT NULL,
  `sccname` varchar(255) NOT NULL,
  `sccpartno` varchar(255) NOT NULL,
  `sccbrandcode` varchar(255) NOT NULL,
  `scctypecode` varchar(255) NOT NULL,
  `sccquantity` decimal(11,2) NOT NULL,
  `sccunitquantity` varchar(100) NOT NULL,
  `sccquantityf` decimal(11,2) NOT NULL,
  `sccunitquantityf` varchar(100) NOT NULL,
  `sccunitcode` varchar(255) NOT NULL,
  PRIMARY KEY  (`dsdaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailstockdeassembly`
--


-- --------------------------------------------------------

--
-- Table structure for table `detailsupplier`
--

CREATE TABLE IF NOT EXISTS `detailsupplier` (
  `detailsplid` int(11) NOT NULL auto_increment,
  `supplierid` int(11) NOT NULL,
  `address` text NOT NULL,
  `areacode` varchar(100) NOT NULL,
  `statecode` varchar(100) NOT NULL,
  `countrycode` varchar(100) NOT NULL,
  `postalcode` varchar(50) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `fax` varchar(50) NOT NULL,
  `contactperson` varchar(100) NOT NULL,
  `mobilenumber` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`detailsplid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `detailsupplier`
--


-- --------------------------------------------------------

--
-- Table structure for table `headeradjustin`
--

CREATE TABLE IF NOT EXISTS `headeradjustin` (
  `ainid` int(11) NOT NULL auto_increment,
  `aindate` int(11) NOT NULL,
  `totalain` decimal(14,2) NOT NULL,
  `description` text NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`ainid`),
  KEY `aindate` (`aindate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headeradjustin`
--


-- --------------------------------------------------------

--
-- Table structure for table `headeradjustout`
--

CREATE TABLE IF NOT EXISTS `headeradjustout` (
  `aoutid` int(11) NOT NULL auto_increment,
  `aoutdate` int(11) NOT NULL,
  `totalaout` decimal(14,2) NOT NULL,
  `description` text NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`aoutid`),
  KEY `aoutdate` (`aoutdate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headeradjustout`
--


-- --------------------------------------------------------

--
-- Table structure for table `headerbuy`
--

CREATE TABLE IF NOT EXISTS `headerbuy` (
  `buyid` int(11) NOT NULL auto_increment,
  `buyno` varchar(255) NOT NULL,
  `buydate` int(11) NOT NULL,
  `orderno` varchar(255) NOT NULL,
  `duedate` int(11) NOT NULL,
  `claims` int(1) NOT NULL,
  `paid` int(1) NOT NULL,
  `paydate` int(11) NOT NULL,
  `suppliercode` varchar(100) NOT NULL,
  `supplieraddrid` int(11) NOT NULL,
  `description` text NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `otherpays` decimal(14,2) NOT NULL,
  `totalbuy` decimal(14,2) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `trtype` enum('cash','credit') NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`buyid`),
  KEY `buydate` (`buydate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headerbuy`
--


-- --------------------------------------------------------

--
-- Table structure for table `headerbuyr`
--

CREATE TABLE IF NOT EXISTS `headerbuyr` (
  `buyrid` int(11) NOT NULL auto_increment,
  `buyno` varchar(255) NOT NULL,
  `buydate` int(11) NOT NULL,
  `buyrdate` int(11) NOT NULL,
  `suppliercode` varchar(100) NOT NULL,
  `supplieraddrid` int(11) NOT NULL,
  `description` text NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `totalbuyr` decimal(14,2) NOT NULL,
  `paid` int(1) NOT NULL,
  `paydate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`buyrid`),
  KEY `buyrdate` (`buyrdate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headerbuyr`
--


-- --------------------------------------------------------

--
-- Table structure for table `headeroperational`
--

CREATE TABLE IF NOT EXISTS `headeroperational` (
  `opid` int(11) NOT NULL auto_increment,
  `monthyear` varchar(30) NOT NULL,
  `totals` decimal(13,2) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`opid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headeroperational`
--


-- --------------------------------------------------------

--
-- Table structure for table `headerpaydebt`
--

CREATE TABLE IF NOT EXISTS `headerpaydebt` (
  `hpid` int(11) NOT NULL auto_increment,
  `supplierid` int(11) NOT NULL,
  `supplieraddrid` int(11) NOT NULL,
  `customerid` int(11) NOT NULL,
  `customeraddrid` int(11) NOT NULL,
  `paymentdate` int(11) NOT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `description` text NOT NULL,
  `totalforbuy` decimal(13,2) NOT NULL,
  `totalforsale` decimal(13,2) NOT NULL,
  `totalpayment` decimal(13,2) NOT NULL,
  `cash` decimal(13,2) NOT NULL,
  `transfer` decimal(13,2) NOT NULL,
  `bank` varchar(100) NOT NULL,
  `accname` varchar(255) NOT NULL,
  `accnumber` varchar(255) NOT NULL,
  `transfernotes` text NOT NULL,
  `cheque` decimal(13,2) NOT NULL,
  `chequenotes` text NOT NULL,
  `chequedates` int(11) NOT NULL,
  `chequeduedate` int(11) NOT NULL,
  `giro` decimal(13,2) NOT NULL,
  `gironotes` text NOT NULL,
  `girodates` int(11) NOT NULL,
  `giroduedate` int(11) NOT NULL,
  `remainingprevious` decimal(13,2) NOT NULL,
  `remainingnow` decimal(13,2) NOT NULL,
  `grandtotals` decimal(13,2) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `complete` int(1) NOT NULL,
  `completedate` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`hpid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headerpaydebt`
--


-- --------------------------------------------------------

--
-- Table structure for table `headerpayment`
--

CREATE TABLE IF NOT EXISTS `headerpayment` (
  `hpid` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL,
  `customeraddrid` int(11) NOT NULL,
  `supplierid` int(11) NOT NULL,
  `supplieraddrid` int(11) NOT NULL,
  `paymentdate` int(11) NOT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `description` text NOT NULL,
  `totalforbuy` decimal(13,2) NOT NULL,
  `totalforsale` decimal(13,2) NOT NULL,
  `totalpayment` decimal(13,2) NOT NULL,
  `statusflat` enum('+','-') NOT NULL,
  `flat` decimal(11,2) NOT NULL,
  `cash` decimal(13,2) NOT NULL,
  `transfer` decimal(13,2) NOT NULL,
  `bank` varchar(100) NOT NULL,
  `accname` varchar(255) NOT NULL,
  `accnumber` varchar(255) NOT NULL,
  `transfernotes` text NOT NULL,
  `cheque` decimal(13,2) NOT NULL,
  `chequenotes` text NOT NULL,
  `chequedates` int(11) NOT NULL,
  `chequeduedate` int(11) NOT NULL,
  `giro` decimal(13,2) NOT NULL,
  `gironotes` text NOT NULL,
  `girodates` int(11) NOT NULL,
  `giroduedate` int(11) NOT NULL,
  `remainingprevious` decimal(13,2) NOT NULL,
  `remainingnow` decimal(13,2) NOT NULL,
  `remainingprevioush` decimal(13,2) NOT NULL,
  `remainingnowh` decimal(13,2) NOT NULL,
  `grandtotals` decimal(13,2) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `complete` int(1) NOT NULL,
  `completedate` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`hpid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headerpayment`
--


-- --------------------------------------------------------

--
-- Table structure for table `headersale`
--

CREATE TABLE IF NOT EXISTS `headersale` (
  `saleid` int(11) NOT NULL auto_increment,
  `saleno` varchar(255) NOT NULL,
  `saledate` int(11) NOT NULL,
  `duedate` int(11) NOT NULL,
  `paydate` int(11) NOT NULL,
  `customercode` varchar(100) NOT NULL,
  `customeraddrid` int(11) NOT NULL,
  `description` text NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `totalsale` decimal(14,2) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `claims` int(1) NOT NULL,
  `paid` int(1) NOT NULL,
  `trtype` enum('cash','credit') NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`saleid`),
  KEY `saledate` (`saledate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headersale`
--


-- --------------------------------------------------------

--
-- Table structure for table `headersaler`
--

CREATE TABLE IF NOT EXISTS `headersaler` (
  `salerid` int(11) NOT NULL auto_increment,
  `saleno` varchar(255) NOT NULL,
  `saledate` int(11) NOT NULL,
  `salerdate` int(11) NOT NULL,
  `paydate` int(11) NOT NULL,
  `customercode` varchar(100) NOT NULL,
  `customeraddrid` int(11) NOT NULL,
  `description` text NOT NULL,
  `totals` decimal(14,2) NOT NULL,
  `disc` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `totalsaler` decimal(14,2) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `claims` int(1) NOT NULL,
  `paid` int(1) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`salerid`),
  KEY `salerdate` (`salerdate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `headersaler`
--


-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `locationid` int(11) NOT NULL auto_increment,
  `locationcode` varchar(100) NOT NULL,
  `locationname` varchar(100) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`locationid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `location`
--


-- --------------------------------------------------------

--
-- Table structure for table `logassembly`
--

CREATE TABLE IF NOT EXISTS `logassembly` (
  `logid` int(11) NOT NULL auto_increment,
  `dsid` int(11) NOT NULL,
  `logdate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `price` decimal(13,2) NOT NULL,
  PRIMARY KEY  (`logid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logassembly`
--


-- --------------------------------------------------------

--
-- Table structure for table `logdeassembly`
--

CREATE TABLE IF NOT EXISTS `logdeassembly` (
  `logid` int(11) NOT NULL auto_increment,
  `logdate` int(11) NOT NULL,
  `logparentid` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(13,2) NOT NULL,
  `usedqty` decimal(11,2) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`logid`),
  KEY `logdate` (`logdate`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logdeassembly`
--


-- --------------------------------------------------------

--
-- Table structure for table `logdeassemblyparent`
--

CREATE TABLE IF NOT EXISTS `logdeassemblyparent` (
  `logid` int(11) NOT NULL auto_increment,
  `logdate` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `unitquantity` varchar(100) NOT NULL,
  `unitcode` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`logid`),
  KEY `logdate` (`logdate`),
  KEY `stockcode` (`stockcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logdeassemblyparent`
--


-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL auto_increment,
  `stockcode` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `tipe` varchar(255) NOT NULL,
  `times` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `logs`
--


-- --------------------------------------------------------

--
-- Table structure for table `online`
--

CREATE TABLE IF NOT EXISTS `online` (
  `cookieid` varchar(32) NOT NULL,
  `userid` int(11) NOT NULL,
  `lastvisit` int(11) NOT NULL,
  `ipaddress` varchar(15) NOT NULL,
  `useragent` varchar(255) NOT NULL,
  `status` int(5) NOT NULL,
  PRIMARY KEY  (`cookieid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `online`
--


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `sid` int(11) NOT NULL,
  `varkey` text NOT NULL,
  `value` text NOT NULL,
  `phrase` text NOT NULL,
  `input_type` varchar(100) NOT NULL,
  `data_type` varchar(50) NOT NULL,
  `grouping` text NOT NULL,
  `groupingname` varchar(255) NOT NULL,
  `setting_order` int(11) NOT NULL,
  `group_order` int(11) NOT NULL,
  PRIMARY KEY  (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`sid`, `varkey`, `value`, `phrase`, `input_type`, `data_type`, `grouping`, `groupingname`, `setting_order`, `group_order`) VALUES
(1, 'available', '1', 'Aktifkan / Non-aktifkan sistem ?', 'radio_yes_no', 'numeric', 'general', 'Pengaturan Umum', 1, 1),
(2, 'logintimelimit', '3600', 'Batas waktu login sebelum keluar ? (dalam satuan jam)', 'text', 'numeric', 'general', 'Pengaturan Umum', 2, 1),
(3, 'companyname', 'Bintang Jaya', 'Nama Perusahaan', 'text', 'free', 'company', 'Pengaturan Perusahaan', 1, 2),
(4, 'companyaddr', '-', 'Alamat Perusahaan', 'text', 'free', 'company', 'Pengaturan Perusahaan', 2, 2),
(5, 'companytelp', '-', 'Telepon Perusahaan', 'text', 'free', 'company', 'Pengaturan Perusahaan', 3, 2),
(6, 'installyear', '2011', 'Tahun pertama kali program di-install', 'text', 'numeric', 'general', 'Pengaturan Umum', 3, 1),
(7, 'defaultnumbering', '1', 'Gunakan kode angka secara default?', 'radio_yes_no', 'numeric', 'general', 'Pengaturan Umum', 4, 1),
(8, 'salereturnlimit', '90', 'Batas waktu retur penjualan (hari)', 'text', 'numeric', 'salesetting', 'Pengaturan Penjualan', 1, 3),
(9, 'extradisc', '60', 'Persentase pengurangan', 'text', 'numeric', 'discount', 'Pengaturan Pengurangan', 1, 4),
(10, 'blocklimit', '1', 'Batas waktu yang diperbolehkan untuk melakukan penjualan terhadap pelanggan yang berhutang (batas waktu dihitung mundur dari hari terakhir bulan sebelum bulan ini)', 'text', 'numeric', 'salesetting', 'Pengaturan Penjualan', 2, 3),
(11, 'showperpage', '20', 'Jumlah item yang ditampilkan per halaman', 'text', 'numeric', 'general', 'Pengaturan Umum', 5, 1),
(12, 'yearactivestart', '2011', 'Tahun yang aktif sekarang dalam program (awal periode)', 'text', 'numeric', 'general', 'Pengaturan Umum', 6, 1),
(13, 'yearactiveend', '2012', 'Tahun yang aktif sekarang dalam program (akhir periode)', 'text', 'numeric', 'general', 'Pengaturan Umum', 7, 1),
(14, 'maximumcashperiod', '30', 'Jangka waktu maksimum untuk pembelian / penjualan untuk dikategorikan Cash / Tunai', 'text', 'numeric', 'paymentsetting', 'Pengaturan Pembayaran', 1, 5),
(15, 'addsaleprice', '50', 'Persentase penambahan untuk harga jual', 'text', 'numeric', 'salesetting', 'Pengaturan Penjualan', 3, 3),
(16, 'saleformatno', '{SALENO}/BIJY/{MR}/{Y}', 'Format Nomor Faktur Penjualan<br>\r\n{SALENO} = Nomor Faktur<br>\r\n{m} = Bulan 1 digit (dalam angka)<br>\r\n{M} = Bulan 2 digit (dalam angka)<br>\r\n{Mr} = Bulan Romawi (huruf kecil)<br>\r\n{MR} = Bulan Romawi (huruf besar)<br>\r\n{y} = Tahun 2 digit<br>\r\n{Y} = Tahun 4 digit', 'text', 'free', 'salesetting', 'Pengaturan Penjualan', 4, 3),
(17, 'showsearchitems', '100', 'Jumlah item yang ditampilkan dalam pencarian barang', 'text', 'numeric', 'general', 'Pengaturan Umum', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE IF NOT EXISTS `state` (
  `stateid` int(11) NOT NULL auto_increment,
  `statecode` varchar(100) NOT NULL,
  `statename` varchar(100) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`stateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`stateid`, `statecode`, `statename`, `createddate`, `createdby`, `lastedited`, `lasteditedby`, `status`) VALUES
(1, 'SUMUT', 'Sumatera Utara', 1301296440, 1, 1307180060, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
  `stockid` int(11) NOT NULL auto_increment,
  `stockcode` varchar(255) NOT NULL,
  `standardname` varchar(255) NOT NULL,
  `generalname` varchar(255) NOT NULL,
  `brandcode` varchar(100) NOT NULL,
  `typecode` varchar(100) NOT NULL,
  `size` varchar(100) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  `remaining` decimal(11,2) NOT NULL,
  `realremaining` decimal(11,2) NOT NULL,
  `totalstock` decimal(11,2) NOT NULL,
  `minqty` decimal(11,2) NOT NULL,
  `buyprice` int(11) NOT NULL,
  `buyminprice` int(11) NOT NULL,
  `buymaxprice` int(11) NOT NULL,
  `sellprice` int(11) NOT NULL,
  `unitcode` varchar(100) NOT NULL,
  `locationcode` varchar(100) NOT NULL,
  `stgrcode` varchar(100) NOT NULL,
  `expdate` int(11) NOT NULL,
  `minexpdate` int(11) NOT NULL,
  `assembly` int(1) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`stockid`),
  KEY `stockcode` (`stockcode`),
  KEY `generalname` (`generalname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stock`
--


-- --------------------------------------------------------

--
-- Table structure for table `stockanually`
--

CREATE TABLE IF NOT EXISTS `stockanually` (
  `id` int(11) NOT NULL auto_increment,
  `year` int(11) NOT NULL,
  `stockid` int(11) NOT NULL,
  `quantity` decimal(11,2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stockanually`
--


-- --------------------------------------------------------

--
-- Table structure for table `stockassembly`
--

CREATE TABLE IF NOT EXISTS `stockassembly` (
  `said` int(11) NOT NULL auto_increment,
  `stockcode` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`said`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stockassembly`
--


-- --------------------------------------------------------

--
-- Table structure for table `stockdeassembly`
--

CREATE TABLE IF NOT EXISTS `stockdeassembly` (
  `sdaid` int(11) NOT NULL auto_increment,
  `stockcode` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`sdaid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stockdeassembly`
--


-- --------------------------------------------------------

--
-- Table structure for table `stockgroup`
--

CREATE TABLE IF NOT EXISTS `stockgroup` (
  `stgrid` int(11) NOT NULL auto_increment,
  `stgrcode` varchar(100) NOT NULL,
  `stgrname` varchar(100) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`stgrid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stockgroup`
--


-- --------------------------------------------------------

--
-- Table structure for table `stockpartno`
--

CREATE TABLE IF NOT EXISTS `stockpartno` (
  `partid` int(11) NOT NULL auto_increment,
  `stockcode` varchar(255) NOT NULL,
  `partno` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`partid`),
  KEY `partno` (`partno`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stockpartno`
--


-- --------------------------------------------------------

--
-- Table structure for table `stockphotos`
--

CREATE TABLE IF NOT EXISTS `stockphotos` (
  `photoid` int(11) NOT NULL auto_increment,
  `stockid` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mains` int(1) NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stockphotos`
--


-- --------------------------------------------------------

--
-- Table structure for table `stockyear`
--

CREATE TABLE IF NOT EXISTS `stockyear` (
  `id` int(11) NOT NULL auto_increment,
  `year` int(11) NOT NULL,
  `salenumber` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `stockyear`
--


-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE IF NOT EXISTS `supplier` (
  `supplierid` int(11) NOT NULL auto_increment,
  `suppliercode` varchar(100) NOT NULL,
  `suppliername` varchar(255) NOT NULL,
  `debt` decimal(14,2) NOT NULL,
  `remainingdebt` decimal(13,2) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`supplierid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `supplier`
--


-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE IF NOT EXISTS `type` (
  `typeid` int(11) NOT NULL auto_increment,
  `typecode` varchar(100) NOT NULL,
  `typename` varchar(100) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`typeid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `type`
--


-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `unitid` int(11) NOT NULL auto_increment,
  `unitcode` varchar(100) NOT NULL,
  `funit` varchar(100) NOT NULL,
  `lunit` varchar(100) NOT NULL,
  `cvalue` decimal(11,2) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`unitid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `units`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` tinyint(4) NOT NULL auto_increment,
  `usergroupid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `morepass` varchar(32) NOT NULL,
  `name` varchar(50) NOT NULL,
  `createddate` int(11) NOT NULL,
  `createdby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `usergroupid`, `username`, `pass`, `morepass`, `name`, `createddate`, `createdby`, `lastedited`, `lasteditedby`) VALUES
(1, 8, 'admin', '6730303a4f8a7eae831e4d5f30bed526', '148e25c398e18a5f0b26d30cf52deee7', 'Administrator', 1301308855, 1, 1308216892, 1);

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--

CREATE TABLE IF NOT EXISTS `usergroup` (
  `usergroupid` int(5) unsigned NOT NULL auto_increment,
  `title` varchar(25) NOT NULL,
  `access` text NOT NULL,
  `createdby` int(11) NOT NULL,
  `createddate` int(11) NOT NULL,
  `lasteditedby` int(11) NOT NULL,
  `lastedited` int(11) NOT NULL,
  `status` int(5) NOT NULL,
  PRIMARY KEY  (`usergroupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `usergroup`
--

INSERT INTO `usergroup` (`usergroupid`, `title`, `access`, `createdby`, `createddate`, `lasteditedby`, `lastedited`, `status`) VALUES
(8, 'Administrator', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,501,502,503,504,505,506,507,508,509,510,511,512,513,514,515,516,517,518,525,519,520,521,522,524,523', 0, 0, 1, 1356328237, 1),
(11, 'Penjualan', '9,10,11,12,13,15,16,17,18,21,22,23,25,27,28,29,30,33,34,35,36,37,38,41,42,45,46,48,49,69,70,73,74,87,88,89,91,92,96,97,98,99,503,504,505', 1, 1305347830, 1, 1308046259, 1);
