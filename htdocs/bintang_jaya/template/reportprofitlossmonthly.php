<?php
$html.= '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$company[companyname] - Laporan Laba / Rugi per Bulan</title>
<link media="all" href="css/print_small.css" type="text/css" rel="stylesheet">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<div align="center" class="reporttitle" style="width: 760px">LAPORAN LABA / RUGI<br>
BULAN $printheadermonth</div><br>
<div align="right" style="width: 760px">Tanggal Cetak : $printdate</div>
<table border="1" cellpadding="3" cellspacing="0" width="760">
<tr>
	<th align="center" width="50" bgcolor="#DEDEDE">NO</th>
	<th align="center" width="230" bgcolor="#DEDEDE">KETERANGAN</th>
	<th align="center" width="160" bgcolor="#DEDEDE">DEBET</th>
	<th align="center" width="160" bgcolor="#DEDEDE">KREDIT</th>
	<th align="center" width="160" bgcolor="#DEDEDE">SISA</th>
</tr>
<tr>
	<td align="right" width="50" height="30">1</td>
	<td align="left" width="230">PENJUALAN TOTAL</td>
	<td align="right" width="160">$thismonthsaletext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$firsttext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">2</td>
	<td align="left" width="230">MODAL PENJUALAN BARANG STOK</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$stockcapitaltext</td>
	<td align="right" width="160">$secondtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">3</td>
	<td align="left" width="230">MODAL PENJUALAN BULAN $printmonth</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$thismonthcapitaltext</td>
	<td align="right" width="160">$thirdtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">4</td>
	<td align="left" width="230">RETUR PENJUALAN BULAN $printmonth</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$thismonthreturnsaletext</td>
	<td align="right" width="160">$fourthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">5</td>
	<td align="left" width="230">MODAL RETUR PENJUALAN BULAN $printmonth</td>
	<td align="right" width="160">$thismonthreturnsalecapitaltext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$fifthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">6</td>
	<td align="left" width="230">RETUR PENJUALAN BARANG STOK</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$stockreturnsaletext</td>
	<td align="right" width="160">$sixthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">7</td>
	<td align="left" width="230">MODAL RETUR PENJUALAN BARANG STOK</td>
	<td align="right" width="160">$stockreturnsalecapitaltext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$seventhtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">8</td>
	<td align="left" width="230">RETUR PENJUALAN BULAN SEBELUMNYA</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$prevreturnsaletext</td>
	<td align="right" width="160">$eighttext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">9</td>
	<td align="left" width="230">MODAL RETUR PENJUALAN BULAN SEBELUMNYA</td>
	<td align="right" width="160">$prevreturnsalecapitaltext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160" style="font-weight: bold; font-size: 16px">$ninthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">10</td>
	<td align="left" width="230">PEMBELIAN TOTAL</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$thismonthbuytext</td>
	<td align="right" width="160">$tenthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">11</td>
	<td align="left" width="230">MODAL PEMBELIAN BARANG STOK</td>
	<td align="right" width="160">$buystocktext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$eleventhtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">12</td>
	<td align="left" width="230">MODAL PEMBELIAN BARANG BULAN $printmonth</td>
	<td align="right" width="160">$buycapitaltext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$twelvethtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">13</td>
	<td align="left" width="230">RETUR PEMBELIAN BULAN $printmonth</td>
	<td align="right" width="160">$thismonthreturnbuytext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$thirteenthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">14</td>
	<td align="left" width="230">RETUR PEMBELIAN BULAN SEBELUMNYA</td>
	<td align="right" width="160">$prevreturnbuytext</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$fourteenthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">15</td>
	<td align="left" width="230">MODAL RETUR PEMBELIAN BULAN SEBELUMNYA</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$prevreturnbuycapitaltext</td>
	<td align="right" width="160" style="font-weight: bold; font-size: 16px">$fifteenthtext</td>
</tr>
<tr>
	<td align="right" width="50" height="30">16</td>
	<td align="left" width="230">BIAYA OPERASIONAL</td>
	<td align="right" width="160">-</td>
	<td align="right" width="160">$operationalcost</td>
	<td align="right" width="160">$sixteenthtext</td>
</tr>
<tr>
	<td align="center" colspan="4" height="30">
	<if criteria="$grandtotalstext > 0">
	<b>KEUNTUNGAN</b>
	<else>
		<if criteria="$grandtotalstext < 0">
		<b>KERUGIAN</b>
		<else>
		<b>IMPAS</b>
		</if>
	</if></td>
	<td align="right" width="160" style="font-weight: bold; font-size: 16px">$grandtotalstext</td>
</tr>
</table>
</body>
</html>
';
?>