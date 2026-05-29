<?php
$html = '<?xml version="1.0" encoding="UTF-8"?>
<rows>
<head>
<column type="combo" width="10" source="ajax.php?list=firststock&amp;asm=0" auto="true" cache="false">Kode Barang</column>
<column type="combo" width="10" source="ajax.php?list=partno&amp;asm=0" auto="true" cache="false">No. Part</column>
<column type="combo" width="15" source="ajax.php?list=stockname&amp;asm=0" auto="true" cache="false">Nama Barang</column>
<column type="combo" width="10" source="ajax.php?list=brands&amp;asm=0" auto="true" cache="false">Merek</column>
<column type="combo" width="10" source="ajax.php?list=types&amp;asm=0" auto="true" cache="false">Tipe</column>
<column type="ed" align="right" width="7">Qty</column>
<column type="combo" width="7" filter="true" xmlcontent="1">Satuan</column>
<column type="ed" align="right" width="8">Harga (Rp.)</column>
<column type="ro" align="right" width="9">Total (Rp.)</column>
<column type="txt" width="14">Keterangan</column>
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