<?php
	require_once "global.php";
	
	if (empty($useraccess['view_user'])){
		redirecting('index.php');
	}
	
	require_once "class/UserGroup.php";
	$userh = new User();
	$usergroup = new UserGroup();
	
	if ($_POST['check'] == 'username'){
		if (!empty($_POST['id'])){
			$userh->setId($_POST['id']);
		}
		echo $userh->checkuserExist(trim($_POST['username']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listuser = $userh->getListUser();
		$lists = '';
		if (sizeof($listuser) > 0){
			foreach ($listuser as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$getug['title'] = '';
				if (!empty($list['usergroupid'])){
					$usergroup->setId($list['usergroupid']);
					$getug = $usergroup->getUserGroupDetail();
				}
				
				if (empty($list['lastedited'])){
					$showle = 0;
				}
				else{
					$showle = date("d-M-y / H:i:s",$list['lastedited']);
				}
				
				$lists .= '
					<row id="'.$list['id'].'">
						<cell>'.htmlspecialchars($list['id']).'</cell>
						<cell>'.htmlspecialchars($list['username']).'</cell>
						<cell>'.htmlspecialchars($getug['title']).'</cell>
						<cell>'.htmlspecialchars($list['name']).'</cell>
						<cell>'.$showle.'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_user'])?'Ubah^user.php?getlist=detail&amp;id='.$list['id'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_user'])?'Hapus^javascript:deleteitem("user.php?do=delete&amp;id='.$list['id'].'")^_self':'-').'</cell>
					</row>
				';
			}
			$userlist = gettemplate('userlist');
			eval("\$userlist = \"$userlist\";");
			echo $userlist;
		}
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$userh->setId($_REQUEST['id']);
		$gud = $userh->getUserDetail();
		
		if (empty($gud['id'])){
			redirecting('user.php');
		}
		
		$selug = $gud['usergroupid'];
		$detailuser = array_map("htmlspecialchars",$gud);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_user']){
		$lastid = $userh->createNewUser($_POST['username'],$_POST['usergroupid'],$_POST['password'],$_POST['morepassword'],$_POST['name'],$userid);
		redirecting("user.php?getlist=detail&id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_user']){
		$lastid = $userh->updateUser($_POST['username'],$_POST['usergroupid'],$_POST['password'],$_POST['morepassword'],$_POST['name'],$userid);
		redirecting("user.php?getlist=detail&id=".$_REQUEST['id']);
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_user']){
		$userh->deleteUser();
		redirecting("user.php");		
	}
		
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('user');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>