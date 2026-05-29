<?php
	require_once "global.php";
	
	if (empty($useraccess['view_country'])){
		redirecting('index.php');
	}
	
	require_once "class/country.php";
	$detailcountry['status'] = 1;
	$country = new country();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$country->setId($_POST['id']);
		}
		echo $country->checkcodeexist(trim($_POST['countrycode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listcountry = $country->getListcountry('all');
		$lists = '';
		if (sizeof($listcountry) > 0){
			$userh = new User();
			foreach ($listcountry as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$country->setCode($list['countrycode']);
				$lists .= '
					<row id="'.$list['countryid'].'">
						<cell>'.htmlspecialchars($list['countrycode']).'</cell>
						<cell>'.htmlspecialchars($list['countryname']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_country'])?'Ubah^country.php?getlist=detail&amp;id='.$list['countryid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_country'] && $country->canDeleteCountry())?'Hapus^javascript:deleteitem("country.php?do=delete&amp;id='.$list['countryid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$countrylist = gettemplate('countrylist');
		eval("\$countrylist = \"$countrylist\";");
		echo $countrylist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$country->setId($_REQUEST['id']);		
		$detailcountry = $country->getcountryDetail();
		if (sizeof($detailcountry) == 0){
			redirecting("country.php");
		}
		$country->setCode($detailcountry['countrycode']);
		$detailcountry = array_map("htmlspecialchars",$detailcountry);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_country']){
		$lastid = $country->savecountry($_POST['countrycode'],$_POST['countryname'],$_POST['countrystatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detailcountry['countrycode'] = htmlspecialchars($_POST['countrycode']);
			$detailcountry['countryname'] = htmlspecialchars($_POST['countryname']);
			$detailcountry['status'] = $_POST['countrystatus'];
		}
		else{
			redirecting("country.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_country']){
		$results = $country->updatecountry($_POST['countrycode'],$_POST['countryname'],$_POST['countrystatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("country.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_country']){
		if ($country->canDeleteCountry()){
			$country->deletecountry();
		}
		redirecting("country.php");		
	}
		
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('country');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>