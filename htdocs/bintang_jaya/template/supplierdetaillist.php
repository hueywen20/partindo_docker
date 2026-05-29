<?php
$html = '<?xml version="1.0" encoding="UTF-8"?>
<rows>
<head>
<column type="ed" width="15">Alamat</column>
<column type="ed" width="8">Contact Person</column>
<column type="ed" width="5">Kode Pos</column>
<column type="combo" width="11" source="ajax.php?list=area" auto="true" cache="false">Kota</column>
<column type="combo" width="11" source="ajax.php?list=state" auto="true" cache="false">Propinsi</column>
<column type="combo" width="11" source="ajax.php?list=country" auto="true" cache="false">Negara</column>
<column type="ed" width="10">Telepon</column>
<column type="ed" width="10">Fax</column>
<column type="ed" width="11">No. HP</column>
<column type="coro" width="8">Status</column>
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