<?php
$html .= '
<if criteria="$i % $totalrow == 0">
<table border="0" cellpadding="0" cellspacing="0" width="760">
<tr>
	<td width="760" align="left">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="380" align="left" valign="top">
		<if criteria="$_GET[op] == yes">
		<div style="font-size: 18px; font-weight: bold; text-transform: uppercase">$company[companyname]</div>
		$company[companyaddr]<br>
		Telp. $company[companytelp]</endif></td>
		<td width="380" colspan="2" align="right" valign="top">
		Tanggal : $invoicedate<br>
		<b>Kepada : </b> $customername<br>
		$customeraddr<br>
		$customertelp
		</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="380" align="left" valign="top">
		NO : $headersale[saleno]</td>
		<td width="380" colspan="2" align="right" valign="top">
		Jatuh Tempo : $invoiceduedate</td>
	</tr>
	<tr>
		<td colspan="3" height="280" valign="top" class="detailitem">
		<table border="1" width="760" cellpadding="2" cellspacing="0">
		<tr>
			<th width="30" align="center">NO</th>
			<th width="100" align="center">QTY</th>
			<th width="100" align="center">NO PART</th>
			<th width="220" align="center">NAMA BARANG</th>
			<th width="90" align="center">@ HARGA</th>
			<th width="90" align="center">DISKON</th>
			<th width="130" align="center">SUB TOTAL</th>
		</tr>
</endif>
		<tr>
			<td width="30" align="right">$inf</td>
			<td width="100" align="left">
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="60" align="right">$quantity</td>
				<td width="5"></td>
				<td width="35" align="left">$lunits</td>
			</tr>
			</table></td>
			<td width="100" align="left">$salepartno</td>
			<td width="220" align="left">$salestockname</td>
			<td width="90" align="right">$saleprice</td>
			<td width="90" align="right">$saledisc</td>
			<td width="130" align="right">$totalsalead</td>
		</tr>
<if criteria="$i % $totalrow == ($totalrow - 1)">
<if criteria="$page < $totalpage">
		</table></td>
	</tr>
	</table></td>
</tr>
</table><br><br><br><br><br><br>
<div align="right" style="font-size: 12px; width: 760px">Hal $page / $totalpage</div><br>
<else>
		</table></td>
	</tr>
	<tr>
		<td width="380" align="left" valign="top">
		<table border="0" width="380" cellpadding="0" cellspacing="0">
		<tr>
			<td width="190" align="center">
			<b>DIPERIKSA OLEH</b><br><br><br>
			_________________
			</td>
			<td width="190" align="center">
			<b>TANDA TERIMA</b><br><br><br>
			_________________
			</td>
		</tr>
		</table></td>
		<td width="150"></td>
		<td width="230" align="right" valign="top">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="100" align="right"><b>DISKON</b> : Rp. </td>
			<td width="130" align="right">$fdisc</td>
		</tr>
		<tr>
			<td colspan="2" height="5"></td>
		</tr>
		<tr>
			<td width="100" align="right"><b>PPN</b> : Rp. </td>
			<td width="130" align="right">$ftax</td>
		</tr>
		<tr>
			<td colspan="2" height="5"><hr></td>
		</tr>
		<tr>
			<td width="100" align="right"><b>TOTAL</b> : Rp. </td>
			<td width="130" align="right" style="font-size: 16px"><b>$ftotal</b></td>
		</tr>
		</table></td>
	</tr>
	</table></td>
</tr>
</table>
<div align="left" style="font-size: 11px"><b>Perhatian ! Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</b></div>
<div align="right" style="font-size: 12px; width: 760px">Hal $page / $totalpage</div>
</if>
</endif>
';
?>