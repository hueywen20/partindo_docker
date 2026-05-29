<?php
$html = '<?xml version="1.0" encoding="UTF-8"?>
<rows>
<head>
<column type="combo" width="18" source="ajax.php?list=firststock&amp;asm=0" auto="true" cache="false">Kode Barang</column>
<column type="combo" width="10" source="ajax.php?list=partno&amp;asm=0" auto="true" cache="false">No. Part</column>
<column type="combo" width="20" source="ajax.php?list=stockname&amp;asm=0" auto="true" cache="false">Nama Barang</column>
<column type="combo" width="11" source="ajax.php?list=brands&amp;asm=0" auto="true" cache="false">Merek</column>
<column type="combo" width="11" source="ajax.php?list=types&amp;asm=0" auto="true" cache="false">Tipe</column>
<column type="ed" align="right" width="10">Qty</column>
<column type="combo" width="10" filter="true" xmlcontent="1">Satuan</column>
<column type="ed" width="10" align="right">Harga</column>
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