<?php
	require_once "global.php";
	
	if (empty($useraccess['view_stockgroup'])){
		redirecting('index.php');
	}
	
	require_once "class/stockgroup.php";
	$detailstgr['status'] = 1;
	$stgr = new stgr();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$stgr->setId($_POST['id']);
		}
		echo $stgr->checkcodeexist(trim($_POST['stgrcode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$liststgr = $stgr->getListstgr('all');
		$lists = '';
		if (sizeof($liststgr) > 0){
			$userh = new User();
			foreach ($liststgr as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$stgr->setCode($list['stgrcode']);
				$lists .= '
					<row id="'.$list['stgrid'].'">
						<cell>'.htmlspecialchars($list['stgrcode']).'</cell>
						<cell>'.htmlspecialchars($list['stgrname']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_stockgroup'])?'Ubah^stockgroup.php?getlist=detail&amp;id='.$list['stgrid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_stockgroup'] && $stgr->canDeleteStockGroup())?'Hapus^javascript:deleteitem("stockgroup.php?do=delete&amp;id='.$list['stgrid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$stgrlist = gettemplate('stockgrouplist');
		eval("\$stgrlist = \"$stgrlist\";");		
		echo $stgrlist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$stgr->setId($_REQUEST['id']);		
		$detailstgr = $stgr->getstgrDetail();
		if (sizeof($detailstgr) == 0){
			redirecting("stockgroup.php");
		}
		$stgr->setCode($detailstgr['stgrcode']);
		$detailstgr = array_map("htmlspecialchars",$detailstgr);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_stockgroup']){
		$lastid = $stgr->savestgr($_POST['stgrcode'],$_POST['stgrname'],$_POST['stockgrouptatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detailstgr['stgrcode'] = htmlspecialchars($_POST['stgrcode']);
			$detailstgr['stgrname'] = htmlspecialchars($_POST['stgrname']);
			$detailstgr['status'] = $_POST['stockgrouptatus'];
		}
		else{
			redirecting("stockgroup.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_stockgroup']){
		$results = $stgr->updatestgr($_POST['stgrcode'],$_POST['stgrname'],$_POST['stockgrouptatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("stockgroup.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_stockgroup']){
		if ($stgr->canDeleteStockGroup()){
			$stgr->deletestgr();
		}
		redirecting("stockgroup.php");		
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('stockgroup');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>