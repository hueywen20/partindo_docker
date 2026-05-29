<?php
$html = '
<if criteria="!empty($lists)">
<table border="0" width="100%" cellpadding="3" cellspacing="3">
<if criteria="empty($_POST[getlist])">
<tr>
	<td align="center"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'stockcode\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'generalname\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'brandname\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'typename\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'locationname\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'realremaining\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'buyminprice\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'buymaxprice\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'minexpdate\')" style="width: 98%"></td>
	<td align="center"><input type="text" name="keyword" onkeyup="searchStock(this.value,\'partno\')" style="width: 98%"></td>
</tr>
</endif>
<tr>
	<th align="center">No</th>
	<th align="center">Kode Barang</th>
	<th align="center">Nama Umum</th>
	<th align="center">Merek</th>
	<th align="center">Tipe</th>
	<th align="center">Lokasi</th>
	<th align="center">Sisa</th>
	<th align="center">Modal Min</th>
	<th align="center">Modal Max</th>
	<th align="center">Exp Date</th>
	<th align="center">Nomor Part</th>
</tr>
$lists
</table>
<else>
Tidak ada daftar barang
</if>
';
?>