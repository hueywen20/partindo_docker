<?php
	require_once "global.php";
	
	if (empty($useraccess['view_type'])){
		redirecting('index.php');
	}
	
	require_once "class/type.php";
	$detailtype['status'] = 1;
	$type = new type();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$type->setId($_POST['id']);
		}
		echo $type->checkcodeexist(trim($_POST['typecode']));
		exit;
	}

	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listtype = $type->getListtype('all');
		$lists = '';
		if (sizeof($listtype) > 0){
			$userh = new User();
			foreach ($listtype as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$type->setCode($list['typecode']);
				$lists .= '
					<row id="'.$list['typeid'].'">
						<cell>'.htmlspecialchars($list['typecode']).'</cell>
						<cell>'.htmlspecialchars($list['typename']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_type'])?'Ubah^type.php?getlist=detail&amp;id='.$list['typeid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_type'] && $type->canDeleteType())?'Hapus^javascript:deleteitem("type.php?do=delete&amp;id='.$list['typeid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$typelist = gettemplate('typelist');
		eval("\$typelist = \"$typelist\";");
		echo $typelist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$type->setId($_REQUEST['id']);		
		$detailtype = $type->gettypeDetail();
		if (sizeof($detailtype) == 0){
			redirecting("type.php");
		}
		$type->setCode($detailtype['typecode']);
		$detailtype = array_map("htmlspecialchars",$detailtype);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_type']){
		$lastid = $type->savetype($_POST['typecode'],$_POST['typename'],$_POST['typestatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detailtype['typecode'] = htmlspecialchars($_POST['typecode']);
			$detailtype['typename'] = htmlspecialchars($_POST['typename']);
			$detailtype['status'] = $_POST['typestatus'];
		}
		else{
			redirecting("type.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_type']){
		$results = $type->updatetype($_POST['typecode'],$_POST['typename'],$_POST['typestatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("type.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_type']){
		if ($type->canDeleteType()){
			$type->deletetype();
		}
		redirecting("type.php");		
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('type');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>