<?php
	require_once "global.php";
	
	if (empty($useraccess['closebook'])){
		redirecting('index.php');
	}
	
	require_once "class/Stock.php";
	$stock = new Stock();
	
	if ($_POST['donow'] == 'nowdoing'){
		$yearnow = date("Y");
		$dbcheck = $db->fetch_one("SELECT * FROM stockyear WHERE year='".$yearnow."'");
		if (!empty($dbcheck['id'])){
			$_GET['msg'] = 'error';
		}
		else{
			$inserts['year'] = $yearnow;
			$inserts['salenumber'] = 1;
			$db->insert("stockyear",$inserts);
			
			$allstock = $stock->getListStock();
			if (sizeof($allstock) > 0){
				foreach ($allstock as $ast){
					unset($inst);
					$inst['year'] = $yearnow;
					$inst['stockid'] = $ast['stockid'];
					$inst['quantity'] = $ast['realremaining'];
					$db->insert("stockanually",$inst);
				}
			}
			
			$db->query("UPDATE settings SET value='".$yearnow."' WHERE varkey='yearactivestart' AND grouping='general'");
			$db->query("UPDATE settings SET value='".$yearnow."' WHERE varkey='yearactiveend' AND grouping='general'");
			
			redirecting("closebook.php?msg=success");
		}
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('closebook');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>