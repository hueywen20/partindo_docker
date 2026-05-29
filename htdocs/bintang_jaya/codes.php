<?php
	require_once "global.php";
	
	if (empty($useraccess['view_codes'])){
		redirecting('index.php');
	}
	
	$codes = new Codes();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$codes->setId($_POST['id']);
		}
		if ($codes->checkCodeTargetExist(trim($_POST['targets']))){
			echo 'targetexist';
		}
		else if ($codes->checkCodeReplacementExist(trim($_POST['replacements']))){
			echo 'replacementexist';
		}
		else if ($codes->checkCodeReplacementExist(trim($_POST['replacementsale']),'replacements_sale')){
			echo 'replacementsaleexist';
		}
		/*else if ($codes->checkCodeOrdersExist($_POST['orders'])){
			echo 'orderexist';
		}*/
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listcodes = $codes->getListCodes();
		$lists = '';
		if (sizeof($listcodes) > 0){
			$userh = new User();
			foreach ($listcodes as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				$lists .= '
					<row id="'.$list['id'].'">
						<cell>'.htmlspecialchars($list['targets']).'</cell>
						<cell>'.htmlspecialchars($list['replacements']).'</cell>
						<cell>'.htmlspecialchars($list['replacements_sale']).'</cell>
						<cell>'.$list['orders'].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_codes'])?'Ubah^codes.php?getlist=detail&amp;id='.$list['id'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_codes'])?'Hapus^javascript:deleteitem("codes.php?do=delete&amp;id='.$list['id'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$codeslist = gettemplate('codeslist');
		eval("\$codeslist = \"$codeslist\";");
		echo $codeslist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$codes->setId($_REQUEST['id']);		
		$detailcodes = $codes->getCodesDetail();
		
		if (empty($detailcodes['id'])){
			redirecting('codes.php');
		}
		
		$detailcodes = array_map("htmlspecialchars",$detailcodes);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_codes']){
		$db->query("UPDATE codes SET orders=orders+1 WHERE orders >= ".$_POST['orders']);
		$lastid = $codes->saveCodes($_POST['targets'],$_POST['replacements'],$_POST['replacements_sale'],$_POST['orders'],$userid);
		redirecting("codes.php?getlist=detail&id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_codes']){
		$results = $codes->updateCodes($_POST['targets'],$_POST['replacements'],$_POST['replacements_sale'],$_POST['orders'],$userid);
		$codes->reOrderCodes();
		redirecting("codes.php?getlist=detail&id=".$_REQUEST['id']);
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_codes']){
		$db->query("UPDATE codes SET orders=orders-1 WHERE orders >= ".$detailcodes['orders']);
		$codes->deleteCodes();
		redirecting("codes.php");		
	}
		
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('codes');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>