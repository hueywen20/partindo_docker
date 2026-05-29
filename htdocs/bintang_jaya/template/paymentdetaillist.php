<?php
$html = '<?xml version="1.0" encoding="UTF-8"?>
<rows>
<head>
<column type="ro" align="left" width="18">No Faktur Penjualan / Retur</column>
<column type="ro" align="center" width="15">Tanggal Penjualan / Retur</column>
<column type="ro" align="left" width="18">No Bon Pembelian / Retur</column>
<column type="ro" align="center" width="15">Tanggal Pembelian / Retur</column>
<column type="ron" align="right" width="18">Total</column>
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