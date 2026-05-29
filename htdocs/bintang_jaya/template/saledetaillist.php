<?php
$html = '<?xml version="1.0" encoding="UTF-8"?>
<rows>
<head>
<column type="combo" width="9" source="ajax.php?list=firststock" auto="true" cache="false">Kode Barang</column>
<column type="combo" width="9" source="ajax.php?list=partno" auto="true" cache="false">No. Part</column>
<column type="combo" width="17" source="ajax.php?list=stockname" auto="true" cache="false">Nama Barang</column>
<column type="combo" width="9" source="ajax.php?list=brands" auto="true" cache="false">Merek</column>
<column type="combo" width="9" source="ajax.php?list=types" auto="true" cache="false">Tipe</column>
<column type="ed" align="right" width="6">Qty</column>
<column type="combo" width="6" filter="true" xmlcontent="1">Satuan</column>
<column type="ed" align="right" width="8">Harga (Rp.)</column>
<column type="ed" align="right" width="6">Diskon (%)</column>
<column type="ro" align="right" width="9">Total (Rp.)</column>
<column type="txt" width="12">Keterangan</column>
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