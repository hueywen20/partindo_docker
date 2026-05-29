<?php
$html = '<?xml version="1.0" encoding="UTF-8"?>
<rows>
<head>
<column type="ro" width="8">No Bon</column>
<column type="ro" width="9">Kode Barang</column>
<column type="ro" width="9">No. Part</column>
<column type="ro" width="10">Nama Barang</column>
<column type="ro" width="9">Merek</column>
<column type="ro" width="9">Tipe</column>
<column type="ed" align="right" width="5">Qty</column>
<column type="ro" width="5">Satuan</column>
<column type="ro" align="right" width="8">Harga (Rp.)</column>
<column type="ed" align="right" width="4">Diskon (%)</column>
<column type="ed" align="right" width="4">Diskon Bon (%)</column>
<column type="ed" align="right" width="4">PPN (%)</column>
<column type="ed" align="right" width="8">Biaya Lain-lain</column>
<column type="ro" align="right" width="8">Total (Rp.)</column>
<column type="txt" width="8">Keterangan</column>
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