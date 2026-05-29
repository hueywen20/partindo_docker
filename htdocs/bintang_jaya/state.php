<?php
	require_once "global.php";
	
	if (empty($useraccess['view_state'])){
		redirecting('index.php');
	}
	
	require_once "class/state.php";
	$detailstate['status'] = 1;
	$state = new state();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$state->setId($_POST['id']);
		}
		echo $state->checkcodeexist(trim($_POST['statecode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$liststate = $state->getListstate('all');
		$lists = '';
		if (sizeof($liststate) > 0){
			$userh = new User();
			foreach ($liststate as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$state->setCode($list['statecode']);
				$lists .= '
					<row id="'.$list['stateid'].'">
						<cell>'.htmlspecialchars($list['statecode']).'</cell>
						<cell>'.htmlspecialchars($list['statename']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_state'])?'Ubah^state.php?getlist=detail&amp;id='.$list['stateid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_state'] && $state->canDeleteState())?'Hapus^javascript:deleteitem("state.php?do=delete&amp;id='.$list['stateid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$statelist = gettemplate('statelist');
		eval("\$statelist = \"$statelist\";");
		echo $statelist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$state->setId($_REQUEST['id']);		
		$detailstate = $state->getstateDetail();
		if (sizeof($detailstate) == 0){
			redirecting("state.php");
		}
		$state->setCode($detailstate['statecode']);
		$detailstate = array_map("htmlspecialchars",$detailstate);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_state']){
		$lastid = $state->savestate($_POST['statecode'],$_POST['statename'],$_POST['statestatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detailstate['statecode'] = htmlspecialchars($_POST['statecode']);
			$detailstate['statename'] = htmlspecialchars($_POST['statename']);
			$detailstate['status'] = $_POST['statestatus'];
		}
		else{
			redirecting("state.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_state']){
		$results = $state->updatestate($_POST['statecode'],$_POST['statename'],$_POST['statestatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("state.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_state']){
		if ($state->canDeleteState()){
			$state->deletestate();
		}
		redirecting("state.php");		
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('state');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>