<?php
	require_once "global.php";
	
	if (empty($useraccess['view_usergroup'])){
		redirecting('index.php');
	}
	
	$detailusergroup['status'] = 1;
	$usergroup = new UserGroup();
	$access = new Access();
	
	if ($_POST['check'] == 'title'){
		if (!empty($_POST['id'])){
			$usergroup->setId($_POST['id']);
		}
		echo $usergroup->checkUserGroupExist(trim($_POST['title']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listusergroup = $usergroup->getListUserGroup('all');
		$lists = '';
		if (sizeof($listusergroup) > 0){
			$userh = new User();
			foreach ($listusergroup as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				if (empty($list['lastedited'])){
					$showle = 0;
				}
				else{
					$showle = date("d-M-y / H:i:s",$list['lastedited']);
				}
				
				$lists .= '
					<row id="'.$list['usergroupid'].'">
						<cell>'.htmlspecialchars($list['usergroupid']).'</cell>
						<cell>'.htmlspecialchars($list['title']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.$showle.'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_usergroup'])?'Ubah^usergroup.php?getlist=detail&amp;id='.$list['usergroupid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_usergroup'])?'Hapus^javascript:deleteitem("usergroup.php?do=delete&amp;id='.$list['usergroupid'].'")^_self':'-').'</cell>
					</row>
				';
			}
			$usergrouplist = gettemplate('usergrouplist');
			eval("\$usergrouplist = \"$usergrouplist\";");
			echo $usergrouplist;
		}
		exit;
	}
	
	$thisugaccess = array();
	if (!empty($_REQUEST['id'])){
		$usergroup->setId($_REQUEST['id']);
		$detailusergroup = $usergroup->getUserGroupDetail();
		
		if (empty($detailusergroup['usergroupid'])){
			redirecting('usergroup.php');
		}
		
		$detailusergroup = array_map("htmlspecialchars",$detailusergroup);
		$thisugaccess = explode(",",$detailusergroup['access']);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_usergroup']){
		$lastid = $usergroup->saveUserGroup($_POST['title'],$userid,implode(",",$_POST['accessp']),$_POST['usergroupstatus']);
		redirecting("usergroup.php?getlist=detail&id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_usergroup']){
		$results = $usergroup->updateUserGroup($_POST['title'],$userid,implode(",",$_POST['accessp']),$_POST['usergroupstatus']);
		redirecting("usergroup.php?getlist=detail&id=".$_REQUEST['id']);
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_usergroup']){
		$usergroup->deleteUserGroup();
		redirecting("usergroup.php");		
	}
	
	//get all access
	$allag = $access->getAccessGroup();
	if (sizeof($allag) > 0){
		$splits = 4;
		$ct = 0;
		foreach ($allag as $aag){
			if ($ct % $splits == 0){
				$accesslist .= '
					<tr>
				';
			}
			$accesslist .= '
						<td align="left" valign="top">
						<fieldset style="width: 280px">
							<legend><b>'.$aag['accessgroup'].'</b></legend>
			';
			$access->setGroups($aag['accessgroup']);
			$agdetail = $access->getListAccess('partial');
			$agdtotal = sizeof($agdetail);
			if ($agdtotal > 0){
				$agdct = 0;
				$accessdetail = '';
				$agdthisgroup = '';
				foreach ($agdetail as $agd){
					$agdthisgroup .= ','.$agd['accessid'];
				}
				$agdthisgroup = substr($agdthisgroup,1);
				foreach ($agdetail as $agd){
					$checked = '';
					if (in_array($agd['accessid'],$thisugaccess)){
						$agdct++;
						$checked = ' checked';
					}
					$accessdetail .= '
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" value="'.$agd['accessid'].'" name="accessp[]" id="id_'.$agd['accessid'].'"'.$checked.' onclick="scanchkbox(\''.$agdthisgroup.'\',\'checkall_'.$aag['accessgroup'].'\')"> <label for="id_'.$agd['accessid'].'">'.$agd['accesslabel'].'</label><br>
					';
					
				}
				$accesslist .= '
							<input type="checkbox" onclick="togglechkbox(\''.$agdthisgroup.'\',this)"'.(($agdct == $agdtotal)?' checked':'').' id="checkall_'.$aag['accessgroup'].'"> <label for="checkall_'.$aag['accessgroup'].'">Pilih Semua</label><br>
				'.$accessdetail;
			}
			$accesslist .= '
						</fieldset></td>
			';
			if ($ct % $splits < ($splits-1)){
				$accesslist .= '
						<td width="20"></td>
				';
			}
			if ($ct % $splits == ($splits-1)){
				$accesslist .= '
					</tr>
					<tr>
						<td colspan="'.$splits.'" height="10"></td>
					</tr>
				';
			}
			$ct++;
		}
		$modnow = $ct % $splits;
		if ($modnow > 0){
			for ($xct = $modnow; $xct < $splits; $xct++){
				$accesslist .= '
							<td align="left"></td>
				';
				if ($xct % $splits < ($splits-1)){
					$accesslist .= '
							<td width="20"></td>
					';
				}
				if ($xct % $splits == ($splits-1)){
					$accesslist .= '
						</tr>
					';
				}
			}
		}
	}
		
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('usergroup');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>