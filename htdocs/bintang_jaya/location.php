<?php
	require_once "global.php";
	
	if (empty($useraccess['view_location'])){
		redirecting('index.php');
	}
	
	require_once "class/location.php";
	$detaillocation['status'] = 1;
	$location = new location();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$location->setId($_POST['id']);
		}
		echo $location->checkcodeexist(trim($_POST['locationcode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listlocation = $location->getListlocation('all');
		$lists = '';
		if (sizeof($listlocation) > 0){
			$userh = new User();
			foreach ($listlocation as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$location->setCode($list['locationcode']);
				$lists .= '
					<row id="'.$list['locationid'].'">
						<cell>'.htmlspecialchars($list['locationcode']).'</cell>
						<cell>'.htmlspecialchars($list['locationname']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_location'])?'Ubah^location.php?getlist=detail&amp;id='.$list['locationid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_location'] && $location->canDeleteLocation())?'Hapus^javascript:deleteitem("location.php?do=delete&amp;id='.$list['locationid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$locationlist = gettemplate('locationlist');
		eval("\$locationlist = \"$locationlist\";");
		echo $locationlist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$location->setId($_REQUEST['id']);		
		$detaillocation = $location->getlocationDetail();
		if (sizeof($detaillocation) == 0){
			redirecting("location.php");
		}
		$location->setCode($detaillocation['locationcode']);
		$detaillocation = array_map("htmlspecialchars",$detaillocation);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_location']){
		$lastid = $location->savelocation($_POST['locationcode'],$_POST['locationname'],$_POST['locationstatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detaillocation['locationcode'] = htmlspecialchars($_POST['locationcode']);
			$detaillocation['locationname'] = htmlspecialchars($_POST['locationname']);
			$detaillocation['status'] = $_POST['locationstatus'];
		}
		else{
			redirecting("location.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_location']){
		$results = $location->updatelocation($_POST['locationcode'],$_POST['locationname'],$_POST['locationstatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("location.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_location']){
		if ($location->canDeleteLocation()){
			$location->deletelocation();
		}
		redirecting("location.php");		
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('location');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>