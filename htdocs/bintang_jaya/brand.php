<?php
	require_once "global.php";
	
	if (empty($useraccess['view_brand'])){
		redirecting('index.php');
	}
	
	require_once "class/Brand.php";
	$detailbrand['status'] = 1;
	$brand = new Brand();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$brand->setId($_POST['id']);
		}
		echo $brand->checkcodeexist(trim($_POST['brandcode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$listbrand = $brand->getListBrand('all');
		$lists = '';
		if (sizeof($listbrand) > 0){
			$userh = new User();
			foreach ($listbrand as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$brand->setCode($list['brandcode']);
				$lists .= '
					<row id="'.$list['brandid'].'">
						<cell>'.htmlspecialchars($list['brandcode']).'</cell>
						<cell>'.htmlspecialchars($list['brandname']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
						<cell>'.(($useraccess['edit_brand'])?'Ubah^brand.php?getlist=detail&amp;id='.$list['brandid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_brand'] && $brand->canDeleteBrand())?'Hapus^javascript:deleteitem("brand.php?do=delete&amp;id='.$list['brandid'].'")^_self':'-').'</cell>
					</row>
				';
			}
		}
		$brandlist = gettemplate('brandlist');
		eval("\$brandlist = \"$brandlist\";");
		echo $brandlist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$brand->setId($_REQUEST['id']);		
		$detailbrand = $brand->getBrandDetail();
		if (sizeof($detailbrand) == 0){
			redirecting("brand.php");
		}
		$brand->setCode($detailbrand['brandcode']);
		$detailbrand = array_map("htmlspecialchars",$detailbrand);
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_brand']){
		$lastid = $brand->saveBrand($_POST['brandcode'],$_POST['brandname'],$_POST['brandstatus'],$userid);
		if ($lastid == false){
			$errors = 'samecode';
			$detailbrand['brandcode'] = htmlspecialchars($_POST['brandcode']);
			$detailbrand['brandname'] = htmlspecialchars($_POST['brandname']);
			$detailbrand['status'] = $_POST['brandstatus'];
		}
		else{
			redirecting("brand.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_brand']){
		$results = $brand->updateBrand($_POST['brandcode'],$_POST['brandname'],$_POST['brandstatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			redirecting("brand.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_brand']){
		if ($brand->canDeleteBrand()){
			$brand->deleteBrand();
		}
		redirecting("brand.php");		
	}
		
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('brand');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>