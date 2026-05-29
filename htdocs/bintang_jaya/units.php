<?php
	require_once "global.php";
	
	if (empty($useraccess['view_units'])){
		redirecting('index.php');
	}
	
	require_once "class/units.php";
	$detailunit['status'] = 1;
	$unit = new unit();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$unit->setId($_POST['id']);
		}
		echo $unit->checkcodeexist(trim($_POST['unitcode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listunit = $unit->getListunit('all');
		$lists = '';
		if (sizeof($listunit) > 0){
			$userh = new User();
			foreach ($listunit as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				$lists .= '
					<row id="'.$list['unitid'].'">
						<cell>'.htmlspecialchars($list['unitcode']).'</cell>
						<cell>'.htmlspecialchars($list['funit']).'</cell>
						<cell>'.$list['cvalue'].'</cell>
						<cell>'.htmlspecialchars($list['lunit']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_units'])?'Ubah^units.php?getlist=detail&amp;id='.$list['unitid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_units'])?'Hapus^javascript:deleteitem("units.php?do=delete&amp;id='.$list['unitid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$unitlist = gettemplate('unitslist');
		eval("\$unitlist = \"$unitlist\";");
		echo $unitlist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$unit->setId($_REQUEST['id']);		
		$detailunit = $unit->getunitDetail();
		
		if (empty($detailunit['unitid'])){
			redirecting('units.php');
		}
		
		$detailunit['cvalue'] = number_format($detailunit['cvalue'],2,",",".");
		
		$detailunit = array_map("htmlspecialchars",$detailunit);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_units']){
		$_POST['cvalue'] = togglenumber($_POST['cvalue'],'calculate');
		if (empty($_POST['cvalue']) || $_POST['cvalue'] == 0 || !is_numeric($_POST['cvalue'])){
			$_POST['cvalue'] = 1;
		}
		$lastid = $unit->saveunit($_POST['unitcode'],$_POST['funit'],$_POST['lunit'],$_POST['cvalue'],$_POST['unitstatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detailunit['unitcode'] = htmlspecialchars($_POST['unitcode']);
			$detailunit['funit'] = htmlspecialchars($_POST['funit']);
			$detailunit['lunit'] = htmlspecialchars($_POST['lunit']);
			$detailunit['cvalue'] = $_POST['cvalue'];
			$detailunit['status'] = $_POST['unitstatus'];
		}
		else{
			redirecting("units.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_units']){
		$_POST['cvalue'] = togglenumber($_POST['cvalue'],'calculate');
		if (empty($_POST['cvalue']) || $_POST['cvalue'] == 0 || !is_numeric($_POST['cvalue'])){
			$_POST['cvalue'] = 1;
		}
		$results = $unit->updateunit($_POST['unitcode'],$_POST['funit'],$_POST['lunit'],$_POST['cvalue'],$_POST['unitstatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("units.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_units']){
		$unit->deleteunit();
		redirecting("units.php");		
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('units');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>