<?php
	require_once "global.php";
	
	if (empty($useraccess['view_city'])){
		redirecting('index.php');
	}
	
	require_once "class/area.php";
	$detailarea['status'] = 1;
	$area = new area();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$area->setId($_POST['id']);
		}
		echo $area->checkcodeexist(trim($_POST['areacode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listarea = $area->getListarea('all');
		$lists = '';
		if (sizeof($listarea) > 0){
			$userh = new User();
			foreach ($listarea as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$area->setCode($list['areacode']);
				$lists .= '
					<row id="'.$list['areaid'].'">
						<cell>'.htmlspecialchars($list['areacode']).'</cell>
						<cell>'.htmlspecialchars($list['areaname']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_city'])?'Ubah^area.php?getlist=detail&amp;id='.$list['areaid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_city'] && $area->canDeleteArea())?'Hapus^javascript:deleteitem("area.php?do=delete&amp;id='.$list['areaid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$arealist = gettemplate('arealist');
		eval("\$arealist = \"$arealist\";");
		echo $arealist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$area->setId($_REQUEST['id']);		
		$detailarea = $area->getareaDetail();
		if (sizeof($detailarea) == 0){
			redirecting("area.php");
		}
		$area->setCode($detailarea['areacode']);
		$detailarea = array_map("htmlspecialchars",$detailarea);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_city']){
		$lastid = $area->savearea($_POST['areacode'],$_POST['areaname'],$_POST['areastatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detailarea['areacode'] = htmlspecialchars($_POST['areacode']);
			$detailarea['areaname'] = htmlspecialchars($_POST['areaname']);
			$detailarea['status'] = $_POST['areastatus'];
		}
		else{
			redirecting("area.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_city']){
		$results = $area->updatearea($_POST['areacode'],$_POST['areaname'],$_POST['areastatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("area.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_city']){
		if ($area->canDeleteArea()){
			$area->deletearea();
		}
		redirecting("area.php");		
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('area');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>