<?php
	require_once "global.php";
	
	if (empty($useraccess['view_firststock'])){
		redirecting('index.php');
	}
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	require_once "class/resizeimage.php";

	$detailstock['status'] = 1;
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	
	$supportedfile = array('image/jpeg','image/gif','image/png');
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$stock->setId($_POST['id']);
		}
		echo $stock->checkStockCodeNoExist(trim($_POST['stockcode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		$keywords = $_GET['keyword'];
		$fields = $_GET['field'];
		if (!isset($_GET['asm'])){
			$_GET['asm'] = -1;
		}
		if (!isset($_GET['status'])){
			$_GET['status'] = -1;
		}
		$liststock = $stock->searchStock($keywords,$fields,'data',$_GET['asm'],$_GET['status'],$_GET['p']);
		/* if (isset($_GET['keyword'])){
			if ($_GET['keyword'] != ''){
				$keywords = $_GET['keyword'];
				if (!isset($_GET['asm'])){
					$_GET['asm'] = -1;
				}
				if (!isset($_GET['status'])){
					$_GET['status'] = -1;
				}
				$fields = $_GET['field'];
				$liststock = $stock->searchStock($keywords,$fields,$_GET['asm'],$_GET['status']);
			}
		} */
		//$liststock = $stock->getListstock(0);
		$lists = '';
		if (sizeof($liststock) > 0){
			$userh = new User();
			foreach ($liststock as $list){
				$brand->setCode($list['brandcode']);
				$getbrandname = $brand->getBrandDetail();
				$type->setCode($list['typecode']);
				$gettypename = $type->gettypeDetail();
				$stgr->setCode($list['stgrcode']);
				$getstgrname = $stgr->getstgrDetail();
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				if ($list['lastedited'] == 0){
					$mexdate = 0;
				}
				else{
					$mexdate = date("d-M-y / H:i:s",$list['lastedited']);
				}
				
				if ($list['assembly'] == 1){
					$idrowdhtmlx = $list['stockid'].'|!s';
				}
				else{
					$idrowdhtmlx = $list['stockid'];
				}
				
				$lists .= '
					<row id="'.$idrowdhtmlx.'">
						<cell>'.htmlspecialchars($list['stockcode']).'</cell>
						<cell>'.htmlspecialchars($list['generalname']).'</cell>
						<cell>'.htmlspecialchars($list['stgrcode']).'</cell>
						<cell>'.htmlspecialchars($list['brandcode']).'</cell>
						<cell>'.htmlspecialchars($list['typecode']).'</cell>
						<cell>'.$codest->convertcodes($list['buyprice']).'</cell>
						<cell>'.$codest->convertcodes($list['sellprice']).'</cell>
						<cell>'.$arrstatus[$list['status']].'</cell>
						<cell>'.$mexdate.'</cell>
						<cell>'.htmlspecialchars($userdetail['username']).'</cell>
				';
				
				$stock->setCode($list['stockcode']);
				if ($list['assembly'] == 1){
					$lists .= '
						<cell>'.(($useraccess['edit_assembly'])?'Ubah^assembly.php?from=firststock&amp;id='.$list['stockid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_assembly'] && $stock->canDeleteFirstStock())?'Hapus^javascript:deleteitem("assembly.php?from=firststock&amp;do=delete&amp;id='.$list['stockid'].'")^_self':'-').'</cell>
					';
				}
				else{
					$lists .= '
						<cell>'.(($useraccess['edit_firststock'])?'Ubah^firststock.php?getlist=detail&amp;id='.$list['stockid'].'^_self':'-').'</cell>
						<cell>'.(($useraccess['delete_firststock'] && $stock->canDeleteFirstStock())?'Hapus^javascript:deleteitem("firststock.php?do=delete&amp;id='.$list['stockid'].'")^_self':'-').'</cell>
					';
				}
				$lists .= '
					</row>
				';
			}
		}
		$stocklist = gettemplate('firststocklist');
		eval("\$stocklist = \"$stocklist\";");
		echo $stocklist;
		exit;
	}
	else if ($_GET['getlist'] == 'pagenav'){
		$keywords = $_GET['keyword'];
		$fields = $_GET['field'];
		if (!isset($_GET['asm'])){
			$_GET['asm'] = -1;
		}
		if (!isset($_GET['status'])){
			$_GET['status'] = -1;
		}
		$liststock = $stock->searchStock($keywords,$fields,'pagenav',$_GET['asm'],$_GET['status'],$_GET['p']);
		echo $liststock;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$stock->setId($_REQUEST['id']);		
		$detailstock = $stock->getFirstStock();
		
		if (empty($detailstock['stockid'])){
			redirecting('firststock.php');
		}
		
		$detailstock['quantity'] = number_format($detailstock['quantity'],2,",",".");
		$detailstock['minqty'] = number_format($detailstock['minqty'],2,",",".");
		$detailstock['buyprice'] = $codest->convertcodes($detailstock['buyprice']);
		$detailstock['sellprice'] = $codest->convertcodes($detailstock['sellprice']);
		$detailstock['remaining'] = number_format($detailstock['remaining'],2,",",".");
		if (!empty($detailstock['expdate'])){
			$detailstock['expdate'] = date("d-m-Y",$detailstock['expdate']);
		}
		$stock->setCode($detailstock['stockcode']);
		$dballpartno = $stock->getAllPartNo();
		$arrpartno = array();
		if (sizeof($dballpartno) > 0){
			foreach ($dballpartno as $dbap){
				array_push($arrpartno,$dbap['partno']);
			}
			$allpartno = implode("\r\n",$arrpartno);
		}
		$selbrand = urlencode($detailstock['brandcode']);
		$seltype = urlencode($detailstock['typecode']);
		$sellocation = urlencode($detailstock['locationcode']);
		$selunit = urlencode($detailstock['unitcode']);
		$selstgr = urlencode($detailstock['stgrcode']);
		$detailstock = array_map("htmlspecialchars",$detailstock);
		
		//get all photos
		$photogal = '';
		$allphotos = $stock->getAllPhotos('partial');
		if (sizeof($allphotos) > 0){
			foreach ($allphotos as $aphs){
				if (!empty($aphs['filename'])){
					$photogal .= '
						<input type="hidden" name="photoid" value="'.$aphs['photoid'].'">
						<img src="products/'.$aphs['filename'].'" border="0">&nbsp;&nbsp;&nbsp;
						<input type="file" name="files"><br>
					';
				}
			}
		}
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_firststock']){
		$_POST['quantity'] = togglenumber($_POST['quantity'],'calculate');
		$_POST['minqty'] = togglenumber($_POST['minqty'],'calculate');
		$_POST['buyprice'] = togglenumber($_POST['buyprice'],'calculate');
		$_POST['sellprice'] = togglenumber($_POST['sellprice'],'calculate');
		if (!is_numeric($_POST['buyprice'])){
			$_POST['buyprice'] = $codest->deconvertcodes($_POST['buyprice']);
		}
		if (!is_numeric($_POST['sellprice'])){
			$_POST['sellprice'] = $codest->deconvertcodes($_POST['sellprice']);
		}
		$lastid = $stock->saveStock($_POST['stockcode'],$_POST['standardname'],$_POST['generalname'],$_POST['brandcode'],$_POST['typecode'],$_POST['partno'],$_POST['size'],$_POST['locationcode'],$_POST['stgrcode'],$_POST['quantity'],$_POST['minqty'],$_POST['buyprice'],$_POST['sellprice'],$_POST['unitcode'],$_POST['expdate'],0,$_POST['stockstatus'],$userid);
		
		if ($lastid == false){
			$errors = 'samecode';
			$detailstock['stockcode'] = htmlspecialchars($_POST['stockcode']);
			$detailstock['standardname'] = htmlspecialchars($_POST['standardname']);
			$detailstock['generalname'] = htmlspecialchars($_POST['generalname']);
			$detailstock['brandcode'] = htmlspecialchars($_POST['brandcode']);
			$detailstock['typecode'] = htmlspecialchars($_POST['typecode']);
			$detailstock['size'] = htmlspecialchars($_POST['size']);
			$detailstock['locationcode'] = htmlspecialchars($_POST['locationcode']);
			$detailstock['stgrcode'] = htmlspecialchars($_POST['stgrcode']);
			$detailstock['quantity'] = number_format($_POST['qty'],0,",",".");
			$detailstock['minqty'] = number_format($_POST['minqty'],0,",",".");
			$detailstock['buyprice'] = number_format($_POST['buyprice'],0,",",".");
			$detailstock['sellprice'] = number_format($_POST['sellprice'],0,",",".");
			$detailstock['unitcode'] = htmlspecialchars($_POST['unitcode']);
			$detailstock['expdate'] = htmlspecialchars($_POST['expdate']);
			$allpartno = $_POST['partno'];
		}
		else{
			//save product photo
			if ($_FILES['files']['size'] > 0 && in_array($_FILES['files']['type'],$supportedfile)){
				$filesname = $_FILES['files']['name'];
				$fileloc = $_FILES['files']['tmp_name'];

				$extension = substr($filesname,strrpos($filesname,'.')+1);

				$k = 1;
				while (file_exists("products/".$filesname)){
					if (strrpos($filesname,"_"))
						$filesname = substr($filesname,0,strrpos($filesname,"_")).'.'.$extension;
					$filesname = substr($filesname,0,strrpos($filesname,"."))."_".$k.'.'.$extension;
					$k++;
				}

				$path = "products/".$filesname;

				list($widthori, $heightori) = getimagesize($fileloc);
				if ($widthori <= 1000 && $heightori <= 700){
					copy($_FILES['files']['tmp_name'], $path);
				}
				else{
					$pt = new photo(array($fileloc));
					$pt->resizeImageP(1000,700);
					$pt->saveToFile(array($path));
				}
				
				$insertp['stockid'] = $lastid;
				$insertp['filename'] = $filesname;
				$insertp['mains'] = 1;
				$insertp['status'] = 1;
				$db->insert("stockphotos",$insertp);
			}

			redirecting("firststock.php?getlist=detail&id=".$lastid);
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_firststock']){
		$_POST['quantity'] = togglenumber($_POST['quantity'],'calculate');
		$_POST['minqty'] = togglenumber($_POST['minqty'],'calculate');
		$_POST['buyprice'] = togglenumber($_POST['buyprice'],'calculate');
		$_POST['sellprice'] = togglenumber($_POST['sellprice'],'calculate');
		if (!is_numeric($_POST['buyprice'])){
			$_POST['buyprice'] = $codest->deconvertcodes($_POST['buyprice']);
		}
		if (!is_numeric($_POST['sellprice'])){
			$_POST['sellprice'] = $codest->deconvertcodes($_POST['sellprice']);
		}
		$results = $stock->updateStock($_POST['stockcode'],$_POST['standardname'],$_POST['generalname'],$_POST['brandcode'],$_POST['typecode'],$_POST['partno'],$_POST['size'],$_POST['locationcode'],$_POST['stgrcode'],$_POST['quantity'],$_POST['minqty'],$_POST['buyprice'],$_POST['sellprice'],$_POST['unitcode'],$_POST['expdate'],$_POST['stockstatus'],$userid);
		if ($results == false){
			$errors = 'samecode';
		}
		else{
			if ($_FILES['files']['size'] > 0 && in_array($_FILES['files']['type'],$supportedfile)){
				$getphoto = $stock->getPhotoById($_POST['photoid']);
				if (!empty($getphoto['filename'])){
					if (file_exists('products/'.$getphoto['filename'])){
						unlink('products/'.$getphoto['filename']);
					}
				}
			
				$filesname = $_FILES['files']['name'];
				$fileloc = $_FILES['files']['tmp_name'];

				$extension = substr($filesname,strrpos($filesname,'.')+1);

				$k = 1;
				while (file_exists("products/".$filesname)){
					if (strrpos($filesname,"_"))
						$filesname = substr($filesname,0,strrpos($filesname,"_")).'.'.$extension;
					$filesname = substr($filesname,0,strrpos($filesname,"."))."_".$k.'.'.$extension;
					$k++;
				}

				$path = "products/".$filesname;

				list($widthori, $heightori) = getimagesize($fileloc);
				if ($widthori <= 1000 && $heightori <= 700){
					copy($_FILES['files']['tmp_name'], $path);
				}
				else{
					$pt = new photo(array($fileloc));
					$pt->resizeImageP(1000,700);
					$pt->saveToFile(array($path));
				}
				
				if (!empty($_POST['photoid'])){
					$updatep['filename'] = $filesname;
					$db->update('stockphotos',$updatep,"photoid='".$_POST['photoid']."'");
				}
				else{
					$insertp['stockid'] = $_REQUEST['id'];
					$insertp['filename'] = $filesname;
					$insertp['mains'] = 1;
					$insertp['status'] = 1;
					$db->insert("stockphotos",$insertp);
				}
			}
			redirecting("firststock.php?getlist=detail&id=".$_REQUEST['id']);
		}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_firststock']){
		if ($stock->canDeleteFirstStock()){
			$stock->deleteStock();
		}
		redirecting("firststock.php");
	}
	
	/*$brandoption = $brand->getListBrand('partial');
	if (sizeof($brandoption) > 0){
		$brando = '<select name="brandcode" id="brandcode"><option value=""></option>';
		foreach ($brandoption as $bo){
			$brando .= '<option value="'.$bo['brandcode'].'"'.((htmlspecialchars($bo['brandcode']) == $detailstock['brandcode'])?' selected':'').'>'.$bo['brandname'].' - '.$bo['brandcode'].'</option>';
		}
		$brando .= '</select>
		<script type="text/javascript">
			var b = dhtmlXComboFromSelect("brandcode");
			b.enableFilteringMode(true);
		</script>';
	}
	
	$typeoption = $type->getListtype('partial');
	if (sizeof($typeoption) > 0){
		$typeo = '<select name="typecode" id="typecode"><option value=""></option>';
		foreach ($typeoption as $do){
			$typeo .= '<option value="'.$do['typecode'].'"'.((htmlspecialchars($do['typecode']) == $detailstock['typecode'])?' selected':'').'>'.$do['typename'].' - '.$do['typecode'].'</option>';
		}
		$typeo .= '</select>
		<script type="text/javascript">
			var t = dhtmlXComboFromSelect("typecode");
			t.enableFilteringMode(true);
		</script>';
	}
	
	$locationoption = $location->getListlocation('partial');
	if (sizeof($locationoption) > 0){
		$locationo = '<select name="locationcode" id="locationcode"><option value=""></option>';
		foreach ($locationoption as $lo){
			$locationo .= '<option value="'.$lo['locationcode'].'"'.((htmlspecialchars($lo['locationcode']) == $detailstock['locationcode'])?' selected':'').'>'.$lo['locationname'].' - '.$lo['locationcode'].'</option>';
		}
		$locationo .= '</select>
		<script type="text/javascript">
			var l = dhtmlXComboFromSelect("locationcode");
			l.enableFilteringMode(true);
		</script>';
	}
	
	$stgroption = $stgr->getListstgr('partial');
	if (sizeof($stgroption) > 0){
		$stgro = '<select name="stgrcode" id="stgrcode"><option value=""></option>';
		foreach ($stgroption as $so){
			$stgro .= '<option value="'.$so['stgrcode'].'"'.((htmlspecialchars($so['stgrcode']) == $detailstock['stgrcode'])?' selected':'').'>'.$so['stgrname'].' - '.$so['stgrcode'].'</option>';
		}
		$stgro .= '</select>
		<script type="text/javascript">
			var s = dhtmlXComboFromSelect("stgrcode");
			s.enableFilteringMode(true);
		</script>';
	}
	
	$unitsoption = $units->getListunit('partial');
	if (sizeof($unitsoption) > 0){
		$unitso = '<select name="unitcode" id="unitcode"><option value=""></option>';
		foreach ($unitsoption as $uo){
			$unitso .= '<option value="'.$uo['unitcode'].'"'.((htmlspecialchars($uo['unitcode']) == $detailstock['unitcode'])?' selected':'').'>'.$uo['lunit'].' - '.$uo['unitcode'].'</option>';
		}
		$unitso .= '</select>
		<script type="text/javascript">
			var u = dhtmlXComboFromSelect("unitcode");
			u.enableFilteringMode(true);
		</script>';
	}*/
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('firststock');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>