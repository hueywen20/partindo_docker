<?php
$html = '
<div style="width: 760px; height: $paperoptionheight;">
<table border="0" cellpadding="0" cellspacing="0" width="760">
<tr>
	<td width="760" align="center"><h2>TANDA TERIMA</h2></td>
</tr>
<if criteria="$_GET[op] == yes">
<tr>
	<td width="760" align="center">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="140" align="left" valign="top">Sudah Terima Dari</td>
		<td width="20" align="center" valign="top">:</td>
		<td width="600" align="left" valign="top" class="underline">$company[companyname]</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="140" align="left" valign="top">Alamat</td>
		<td width="20" align="center" valign="top">:</td>
		<td width="600" align="left" valign="top" class="underline">$company[companyaddr]</td>
	</tr>
	</table></td>
</tr>
<tr>
	<td height="10"></td>
</tr>
</endif>
<tr>
	<td width="760" align="center">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="140" align="left" valign="top">Bon Sebanyak</td>
		<td width="20" align="center" valign="top">:</td>
		<td width="600" align="left" valign="top" class="underline">
		$totalinvoice ( $terbilangtotalinvoice ) lembar
		<br>
		( $allinvoice )</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="140" align="left" valign="top">Dari Tanggal</td>
		<td width="20" align="center" valign="top">:</td>
		<td width="600" align="left" valign="top" class="underline">
		<b>$startdatef</b> Sampai Tanggal <b>$enddatef</b></td>
	</tr>
	<if criteria="$headerpayment[remainingprevious] > 0 && $statususer != 1">
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="140" align="left" valign="top">Kelebihan Uang<br>Bulan Lalu</td>
		<td width="20" align="center" valign="top">:</td>
		<td width="600" align="left" valign="top" class="underline">Rp. $fremainingprevious</td>
	</tr>
	</endif>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="140" align="left" valign="top">Jumlah Uang</td>
		<td width="20" align="center" valign="top">:</td>
		<td width="600" align="left" valign="top" class="underline"><font size="+1">Rp. <b>$ftotal</b></font>
		<br>
		( $terbilangs Rupiah )</td>
	</tr>
	</table></td>
</tr>
</table><br>
<div style="width: 760px" align="right">
<div align="center" style="float: right">
Medan, $printdate
<br><br><br><br><br>
( $customername )
</div></div>
</div>';
?>