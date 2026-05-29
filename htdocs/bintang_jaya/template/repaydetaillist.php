<?php
$html = '<?xml version="1.0" encoding="UTF-8"?>
<rows>
<head>
<column type="coro" align="left" width="10">Tipe Bayar
	<option value="1">Tunai</option>
	<option value="2">Transfer</option>
	<option value="3">Cek</option>
	<option value="4">Giro</option>
</column>
<column type="ed" align="left" width="10">Bank</column>
<column type="ed" align="left" width="15">Nama Rekening</column>
<column type="ed" align="left" width="12">Nomor Rekening</column>
<column type="dhxCalendarA" align="center" width="9">Tanggal</column>
<column type="dhxCalendarA" align="center" width="9">Jatuh Tempo</column>
<column type="ed" align="right" width="13">Jumlah</column>
<column type="ro" align="right" width="6">Status</column>
<column type="txt" width="16">Keterangan</column>
<settings>
	<colwidth>%</colwidth>
</settings>
</head>
<if criteria="!empty($lists)">
$lists
</endif>
</rows>
';
?>