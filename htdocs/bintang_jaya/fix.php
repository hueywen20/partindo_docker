<?php
	require_once "global.php";
	
	$a = $db->fetch_all("SELECT * FROM detailstockdeassembly");
	foreach ($a as $b){
		$b = $db->fetch_one("SELECT * FROM stock WHERE stockcode = '".$db->clean($b['stockcode'])."'");
		if (empty($b['stockid'])){
			echo $b['stockcode'].'<br />';
		}
	}
?>