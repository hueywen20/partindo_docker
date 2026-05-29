<?php
	require_once "global.php";
	
	if (empty($userid)){
		redirecting("index.php");
	}
	
	if ($_GET['list'] == 'brand'){
		require_once "class/Brand.php";
		$brand = new Brand();
		if ($_GET['type'] == 'xml'){
			$brandoption = $brand->searchBrand($_GET['mask'],'partial');
			if (sizeof($brandoption) > 0){
				foreach ($brandoption as $bo){
					$brando .= '<option value="'.htmlspecialchars($bo['brandcode']).'"'.(($bo['brandcode'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($bo['brandcode']).'</option>';
				}
			}
			header("Content-type:text/xml");
			$brando = '<?xml version="1.0"?><complete>'.$brando.'</complete>';
		}
		else{
			$brandoption = $brand->getListBrand('partial');
			if (sizeof($brandoption) > 0){
				foreach ($brandoption as $bo){
					$brando .= '<option value="'.htmlspecialchars($bo['brandcode']).'"'.(($bo['brandcode'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($bo['brandcode']).'</option>';
				}
			}
			$brando = '<select name="brandcode" id="brandcode"><option value=""></option>'.$brando.'</select>';
		}
		echo $brando;
	}
	else if ($_GET['list'] == 'type'){
		require_once "class/type.php";
		$type = new type();
		if ($_GET['type'] == 'xml'){
			$typeoption = $type->searchType($_GET['mask'],'partial');
			if (sizeof($typeoption) > 0){
				foreach ($typeoption as $do){
					$typeo .= '<option value="'.htmlspecialchars($do['typecode']).'"'.(($do['typecode'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($do['typecode']).'</option>';
				}
			}
			header("Content-type:text/xml");
			$typeo = '<?xml version="1.0"?><complete>'.$typeo.'</complete>';
		}
		else{
			$typeoption = $type->getListtype('partial');
			if (sizeof($typeoption) > 0){
				$typeo = '<select name="typecode" id="typecode"><option value=""></option>';
				foreach ($typeoption as $do){
					$typeo .= '<option value="'.htmlspecialchars($do['typecode']).'"'.(($do['typecode'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($do['typecode']).'</option>';
				}
				$typeo .= '</select>';
			}
		}
		echo $typeo;
	}
	else if ($_GET['list'] == 'location'){
		require_once "class/location.php";
		$location = new location();
		if ($_GET['type'] == 'xml'){
			$locationoption = $location->searchlocation($_GET['mask'],'partial');
			if (sizeof($locationoption) > 0){
				foreach ($locationoption as $lo){
					$locationo .= '<option value="'.htmlspecialchars($lo['locationcode']).'"'.(($lo['locationcode'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($lo['locationcode']).'</option>';
				}
			}
			header("Content-type:text/xml");
			$locationo = '<?xml version="1.0"?><complete>'.$locationo.'</complete>';
		}
		else{
			$locationoption = $location->getListlocation('partial');
			if (sizeof($locationoption) > 0){
				$locationo = '<select name="locationcode" id="locationcode"><option value=""></option>';
				foreach ($locationoption as $lo){
					$locationo .= '<option value="'.htmlspecialchars($lo['locationcode']).'"'.(($lo['locationcode'] == $detailstock['locationcode'])?' selected':'').'>'.htmlspecialchars($lo['locationcode']).'</option>';
				}
				$locationo .= '</select>';
			}
		}
		echo $locationo;
	}
	else if ($_GET['list'] == 'stgr'){
		require_once "class/stockgroup.php";
		$stgr = new stgr();
		if ($_GET['type'] == 'xml'){
			$stgroption = $stgr->searchstgr($_GET['mask'],'partial');
			if (sizeof($stgroption) > 0){
				foreach ($stgroption as $so){
					$stgro .= '<option value="'.htmlspecialchars($so['stgrcode']).'"'.(($so['stgrcode'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($so['stgrcode']).'</option>';
				}
			}
			header("Content-type:text/xml");
			$stgro = '<?xml version="1.0"?><complete>'.$stgro.'</complete>';
		}
		else{
			$stgroption = $stgr->getListstgr('partial');
			if (sizeof($stgroption) > 0){
				$stgro = '<select name="stgrcode" id="stgrcode"><option value=""></option>';
				foreach ($stgroption as $so){
					$stgro .= '<option value="'.htmlspecialchars($so['stgrcode']).'"'.(($so['stgrcode'] == $detailstock['stgrcode'])?' selected':'').'>'.htmlspecialchars($so['stgrcode']).'</option>';
				}
				$stgro .= '</select>';
			}
		}
		echo $stgro;
	}
	else if ($_GET['list'] == 'unit'){
		require_once "class/units.php";
		$units = new unit();
		if ($_GET['type'] == 'xml'){
			$unitsoption = $units->searchunit($_GET['mask'],'partial');
			if (sizeof($unitsoption) > 0){
				foreach ($unitsoption as $uo){
					$unitso .= '<option value="'.htmlspecialchars($uo['unitcode']).'"'.(($uo['unitcode'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($uo['unitcode']).'</option>';
				}
			}
			header("Content-type:text/xml");
			$unitso = '<?xml version="1.0"?><complete>'.$unitso.'</complete>';
		}
		else{
			$unitsoption = $units->getListunit('partial');
			if (sizeof($unitsoption) > 0){
				$unitso = '<select name="unitcode" id="unitcode"><option value=""></option>';
				foreach ($unitsoption as $uo){
					$unitso .= '<option value="'.htmlspecialchars($uo['unitcode']).'"'.(($uo['unitcode'] == $detailstock['unitcode'])?' selected':'').'>'.htmlspecialchars($uo['unitcode']).'</option>';
				}
				$unitso .= '</select>';
			}
		}
		echo $unitso;
	}
	else if ($_GET['list'] == 'user'){
		require_once "class/User.php";
		$userot = new User();
		if ($_GET['type'] == 'xml'){
			$ugoption = $userot->searchUser($_GET['mask'],'');
			if (sizeof($ugoption) > 0){
				foreach ($ugoption as $do){
					$ugo .= '<option value="'.$do['id'].'"'.(($do['id'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($do['username']).'</option>';
				}
			}
			header("Content-type:text/xml");
			$ugo = '<?xml version="1.0"?><complete>'.$ugo.'</complete>';
		}
		echo $ugo;
	}
	else if ($_GET['list'] == 'usergroup'){
		require_once "class/UserGroup.php";
		$usergroup = new UserGroup();
		if ($_GET['type'] == 'xml'){
			$ugoption = $usergroup->searchUserGroup($_GET['mask'],'partial');
			if (sizeof($ugoption) > 0){
				foreach ($ugoption as $do){
					$ugo .= '<option value="'.$do['usergroupid'].'"'.(($do['usergroupid'] == stripslashes($_GET['id']))?' selected="1"':'').'>'.htmlspecialchars($do['title']).'</option>';
				}
			}
			header("Content-type:text/xml");
			$ugo = '<?xml version="1.0"?><complete>'.$ugo.'</complete>';
		}
		echo $ugo;
	}
	else if ($_GET['list'] == 'unitdetail'){
		require_once "class/units.php";
		$units = new unit();
		$unitsoption = $units->getListunit('partial');
		header("Content-type:text/xml");
		$unitso .= '<?xml version="1.0"?>';
		$unitso = '<complete>';
		if (sizeof($unitsoption) > 0){
			foreach ($unitsoption as $uo){
				$unitso .= '<option value="'.htmlspecialchars($uo['unitcode']).'"'.(($uo['unitcode'] == $detailstock['unitcode'])?' selected':'').'>'.htmlspecialchars($uo['unitcode']).'</option>';
			}
		}
		$unitso .= '</complete>';
		print($unitso);
	}
	else if ($_GET['list'] == 'area'){
		require_once "class/area.php";
		$area = new area();
		//$areaoption = $area->getListarea('partial');
		$areaoption = $area->searcharea($_GET['mask'],'all');
		header("Content-type:text/xml");
		$areao .= '<?xml version="1.0"?>';
		$areao = '<complete>';
		if (sizeof($areaoption) > 0){
			//$areao = '<select name="areacode" id="areacode"><option value=""></option>';
			foreach ($areaoption as $ao){
				$areao .= '<option value="'.htmlspecialchars($ao['areacode']).'">'.htmlspecialchars($ao['areaname']).'</option>';
			}
		}
		$areao .= '</complete>';
		//$areao .= '</select>';
		print($areao);
	}
	else if ($_GET['list'] == 'state'){
		require_once "class/state.php";
		$state = new state();
		//$stateoption = $state->getListstate('partial');
		$stateoption = $state->searchstate($_GET['mask'],'all');
		header("Content-type:text/xml");
		$stateo .= '<?xml version="1.0"?>';
		$stateo = '<complete>';
		if (sizeof($stateoption) > 0){
			//$stateo = '<select name="statecode" id="statecode"><option value=""></option>';
			foreach ($stateoption as $so){
				$stateo .= '<option value="'.htmlspecialchars($so['statecode']).'">'.htmlspecialchars($so['statename']).'</option>';
			}
			//$stateo .= '</select>';
		}
		$stateo .= '</complete>';
		print($stateo);
	}
	else if ($_GET['list'] == 'country'){
		require_once "class/country.php";
		$country = new country();
		//$countryoption = $country->getListcountry('partial');
		$countryoption = $country->searchcountry($_GET['mask'],'all');
		header("Content-type:text/xml");
		$countryo .= '<?xml version="1.0"?>';
		$countryo = '<complete>';
		if (sizeof($countryoption) > 0){
			//$countryo = '<select name="countrycode" id="countrycode"><option value=""></option>';
			foreach ($countryoption as $co){
				$countryo .= '<option value="'.htmlspecialchars($co['countrycode']).'">'.htmlspecialchars($co['countryname']).'</option>';
			}
			//$countryo .= '</select>';
		}
		$countryo .= '</complete>';
		print($countryo);
	}
	else if ($_GET['list'] == 'firststock'){
		require_once "class/Stock.php";
		require_once "class/Brand.php";
		require_once "class/type.php";
		$stock = new Stock();
		
		if (!isset($_GET['asm'])){
			$_GET['asm'] = -1;
		}
		
		if (!empty($_GET['id'])){
			$_GET['mask'] = $_GET['id'];
		}
		
		$stockoption = $stock->searchStock(array($_GET['mask']),array('stockcode'),'data',$_GET['asm'],-1,-1,$general['showsearchitems']);
		header("Content-type:text/xml");
		$stocko .= '<?xml version="1.0"?>';
		$stocko = '<complete>';
		if (sizeof($stockoption) > 0){
			foreach ($stockoption as $so){
				/* $brand = new Brand();
				$brand->setCode($stockoption['brandcode']);
				$getbrandname = $brand->getBrandDetail();
				
				$type = new type();
				$type->setCode($stockoption['typecode']);
				$gettypename = $type->gettypeDetail(); */
				
				$stock->setCode($so['stockcode']);
				$allpartno = $stock->getAllPartNo();
				if (sizeof($allpartno) > 0){
					foreach ($allpartno as $apn){
						$stocko .= '<option value="'.htmlspecialchars($so['stockcode'].'||'.$apn['partno']).'"'.(($so['stockcode'] == $_GET['id'])?' selected="1"':'').'>'.htmlspecialchars($so['stockcode']).'|^|'.htmlspecialchars($apn['partno']).'|^|'.htmlspecialchars($so['generalname']).'|^|'.htmlspecialchars($so['brandcode']).'|^|'.htmlspecialchars($so['typecode']).'|^|'.htmlspecialchars($so['size']).'</option>';
					}
				}
			}
		}
		$stocko .= '</complete>';
		print($stocko);
	}
	else if ($_GET['list'] == 'partno'){
		if (empty($_GET['get'])){
			header("Content-type:text/xml");
			$stocko .= '<?xml version="1.0"?>';
			$stocko = '<complete>';
			if (!empty($_GET['stockcode'])){
				require_once "class/Stock.php";
				$stock = new Stock();
				$arrsc = explode('||',$_GET['stockcode']);
				$stock->setCode($arrsc[0]);
				$stockoption = $stock->getAllPartNo();
				if (sizeof($stockoption) > 0){
					foreach ($stockoption as $so){
						$stocko .= '<option value="'.htmlspecialchars($so['partno']).'">'.htmlspecialchars($so['partno']).'</option>';
					}
				}
			}
			else{
				$addsqlasm = '';
				if (isset($_GET['asm'])){
					$addsqlasm = ' AND s.assembly IN ('.$db->clean($_GET['asm']).')';
				}
				$stockoption = $db->fetch_all("SELECT spn.partno, s.stockcode, s.generalname, s.brandcode, s.typecode, s.size FROM stockpartno spn INNER JOIN stock s ON spn.stockcode = s.stockcode WHERE spn.status=1 AND spn.partno LIKE '%".$db->clean($_GET['mask'])."%'".$addsqlasm." ORDER BY spn.partno LIMIT ".$general['showsearchitems']);
				if (sizeof($stockoption) > 0){
					foreach ($stockoption as $so){
						$stocko .= '<option value="'.htmlspecialchars($so['stockcode'].'||'.$so['partno']).'">'.htmlspecialchars($so['partno']).'|^|'.htmlspecialchars($so['stockcode']).'|^|'.htmlspecialchars($so['generalname']).'|^|'.htmlspecialchars($so['brandcode']).'|^|'.htmlspecialchars($so['typecode']).'|^|'.htmlspecialchars($so['size']).'</option>';
					}
				}
			}
			$stocko .= '</complete>';
			print($stocko);
		}
		else if ($_GET['get'] == 'stockcode' && !empty($_GET['pn'])){
			$arrsc = explode('||',$_GET['pn']);
			$stockcode = $arrsc[0];
			$stockpartno = $arrsc[1];
			
			$dbpn = $db->fetch_one("SELECT * FROM stockpartno WHERE partno='".$db->clean($stockpartno)."' AND stockcode='".$db->clean($stockcode)."'");
			echo $dbpn['stockcode'];
		}
	}
	else if ($_GET['list'] == 'stockname'){
		header("Content-type:text/xml");
		$stocko .= '<?xml version="1.0"?>';
		$stocko = '<complete>';
		$addsqlasm = '';
		if (isset($_GET['asm'])){
			$addsqlasm = ' AND s.assembly IN ('.$db->clean($_GET['asm']).')';
		}
		$stockoption = $db->fetch_all("SELECT spn.partno, s.stockcode, s.generalname, s.brandcode, s.typecode, s.size FROM stockpartno spn INNER JOIN stock s ON spn.stockcode = s.stockcode WHERE spn.status=1 AND s.generalname LIKE '%".$db->clean($_GET['mask'])."%'".$addsqlasm." ORDER BY s.generalname LIMIT ".$general['showsearchitems']);
		if (sizeof($stockoption) > 0){
			foreach ($stockoption as $so){
				$stocko .= '<option value="'.htmlspecialchars($so['stockcode'].'||'.$so['partno']).'">'.htmlspecialchars($so['generalname']).'|^|'.htmlspecialchars($so['partno']).'|^|'.htmlspecialchars($so['stockcode']).'|^|'.htmlspecialchars($so['brandcode']).'|^|'.htmlspecialchars($so['typecode']).'|^|'.htmlspecialchars($so['size']).'</option>';
			}
		}
		$stocko .= '</complete>';
		print($stocko);
	}
	else if ($_GET['list'] == 'brands'){
		header("Content-type:text/xml");
		$stocko .= '<?xml version="1.0"?>';
		$stocko = '<complete>';
		$addsqlasm = '';
		if (isset($_GET['asm'])){
			$addsqlasm = ' AND s.assembly IN ('.$db->clean($_GET['asm']).')';
		}
		$stockoption = $db->fetch_all("SELECT spn.partno, s.stockcode, s.generalname, s.brandcode, s.typecode, s.size FROM stockpartno spn INNER JOIN stock s ON spn.stockcode = s.stockcode WHERE spn.status=1 AND s.brandcode LIKE '%".$db->clean($_GET['mask'])."%'".$addsqlasm." ORDER BY s.brandcode LIMIT ".$general['showsearchitems']);
		if (sizeof($stockoption) > 0){
			foreach ($stockoption as $so){
				$stocko .= '<option value="'.htmlspecialchars($so['stockcode'].'||'.$so['partno']).'">'.htmlspecialchars($so['brandcode']).'|^|'.htmlspecialchars($so['partno']).'|^|'.htmlspecialchars($so['stockcode']).'|^|'.htmlspecialchars($so['generalname']).'|^|'.htmlspecialchars($so['typecode']).'|^|'.htmlspecialchars($so['size']).'</option>';
			}
		}
		$stocko .= '</complete>';
		print($stocko);
	}
	else if ($_GET['list'] == 'types'){
		header("Content-type:text/xml");
		$stocko .= '<?xml version="1.0"?>';
		$stocko = '<complete>';
		$addsqlasm = '';
		if (isset($_GET['asm'])){
			$addsqlasm = ' AND s.assembly IN ('.$db->clean($_GET['asm']).')';
		}
		$stockoption = $db->fetch_all("SELECT spn.partno, s.stockcode, s.generalname, s.brandcode, s.typecode, s.size FROM stockpartno spn INNER JOIN stock s ON spn.stockcode = s.stockcode WHERE spn.status=1 AND s.typecode LIKE '%".$db->clean($_GET['mask'])."%'".$addsqlasm." ORDER BY s.typecode LIMIT ".$general['showsearchitems']);
		if (sizeof($stockoption) > 0){
			foreach ($stockoption as $so){
				$stocko .= '<option value="'.htmlspecialchars($so['stockcode'].'||'.$so['partno']).'">'.htmlspecialchars($so['typecode']).'|^|'.htmlspecialchars($so['partno']).'|^|'.htmlspecialchars($so['stockcode']).'|^|'.htmlspecialchars($so['generalname']).'|^|'.htmlspecialchars($so['brandcode']).'|^|'.htmlspecialchars($so['size']).'</option>';
			}
		}
		$stocko .= '</complete>';
		print($stocko);
	}
	else if ($_GET['list'] == 'getstockname'){
		if (!empty($_GET['stockcode'])){
			require_once "class/Stock.php";
			require_once "class/units.php";
			require_once "class/Assembly.php";
			require_once "class/DeAssembly.php";
			$stock = new Stock();
			$units = new unit();
			$assembly = new Assembly();
			$deassembly = new DeAssembly();
			
			$arrsc = explode('||',$_GET['stockcode']);
			$stockcode = $arrsc[0];
			$stockpartno = $arrsc[1];
			
			$stock->setCode($stockcode);
			$stockoption = $stock->getFirstStock();
			$units->setCode($stockoption['unitcode']);
			$unitdetail = $units->getunitDetail();
			
			//get part no
			$rspartno = $db->fetch_one("SELECT * FROM stockpartno WHERE stockcode='".$stockcode."' AND partno='".$stockpartno."'");
			
			$stockname = '';
			if (sizeof($stockoption) > 0){
				if ($stockoption['assembly'] == 1){
					$assembly->setCode($stockcode);
					$getac = $assembly->getAssemblyComponent();
					if (sizeof($getac) > 0){
						$tempmax = -1;
						foreach ($getac as $gac){
							$stock->setCode($gac['stockcodecomponent']);
							$stockcmpt = $stock->getFirstStock();
							if ($tempmax == -1){
								$tempmax = floor($stockcmpt['realremaining']/$gac['sccquantity']);
							}
							else{
								$tempmax = min($tempmax,floor($stockcmpt['realremaining']/$gac['sccquantity']));
							}
						}
					}
					$stockoption['sellprice'] = $stockoption['sellprice'] + ($salesetting['addsaleprice'] / 100 * $stockoption['sellprice']);
					$stockname = htmlspecialchars($stockoption['generalname']).'|^|'.$stockoption['sellprice'].'|^|'.$tempmax.'|^|'.$unitdetail['funit'].','.$unitdetail['lunit'].'|^|'.$unitdetail['cvalue'].',1|^|0|^|'.$stockoption['brandcode'].'|^|'.$stockoption['typecode'].'|^|'.$rspartno['partno'].'|^|0';
				}
				else if ($stockoption['assembly'] == 2){
					$getac = $deassembly->getDeAssemblyParent($stockcode);
					if (sizeof($getac) > 0){
						$stock->setCode($getac['stockcode']);
						$parentfirststock = $stock->getFirstStock();
						$tempmax = $stockoption['realremaining'] + $parentfirststock['realremaining'] * $getac['sccquantity'];
					}
					$stockoption['sellprice'] = $stockoption['sellprice'] + ($salesetting['addsaleprice'] / 100 * $stockoption['sellprice']);
					$stockname = htmlspecialchars($stockoption['generalname']).'|^|'.$stockoption['sellprice'].'|^|'.$tempmax.'|^|'.$unitdetail['funit'].','.$unitdetail['lunit'].'|^|'.$unitdetail['cvalue'].',1|^|0|^|'.$stockoption['brandcode'].'|^|'.$stockoption['typecode'].'|^|'.$rspartno['partno'].'|^|0';
				}
				else{
					$stockoption['buyminprice'] = $stockoption['buyminprice'] + ($salesetting['addsaleprice'] / 100 * $stockoption['buyminprice']);
					$stockname = htmlspecialchars($stockoption['generalname']).'|^|'.$stockoption['buyminprice'].'|^|'.$stockoption['realremaining'].'|^|'.$unitdetail['funit'].','.$unitdetail['lunit'].'|^|'.$unitdetail['cvalue'].',1|^|'.($stockoption['totalstock'] - $stockoption['realremaining']).'|^|'.$stockoption['brandcode'].'|^|'.$stockoption['typecode'].'|^|'.$rspartno['partno'].'|^|'.$stockoption['buymaxprice'];
				}
			}
			echo $stockname;
		}
	}
	else if ($_GET['list'] == 'getstockcode'){
		header("Content-type:text/xml");
		$stockcode .= '<?xml version="1.0"?>';
		$stockcode = '<complete>';
		if (!empty($_GET['buyno'])){
			$allcodedetail = $db->fetch_all("SELECT * FROM detailbuy WHERE buyno='".$_GET['buyno']."' GROUP BY stockcode");
			if (sizeof($allcodedetail) > 0){
				foreach ($allcodedetail as $acd){
					$stockcode .= '<option value="'.$acd['stockcode'].'">'.$acd['stockcode'].'</option>';
				}
			}
		}
		else if (!empty($_GET['saleno'])){
			$allcodedetail = $db->fetch_all("SELECT * FROM detailsale WHERE saleno='".$_GET['saleno']."' GROUP BY stockcode");
			if (sizeof($allcodedetail) > 0){
				foreach ($allcodedetail as $acd){
					$stockcode .= '<option value="'.$acd['stockcode'].'">'.$acd['stockcode'].'</option>';
				}
			}
		}
		$stockcode .= '</complete>';
		print($stockcode);
	}
	else if ($_GET['list'] == 'getstockforreturn'){
		if (!empty($_GET['stockcode'])){
			require_once "class/Stock.php";
			require_once "class/units.php";
			$stock = new Stock();
			$units = new unit();
			$stock->setCode($_GET['stockcode']);
			$stockoption = $stock->getFirstStock();
			$units->setCode($stockoption['unitcode']);
			$unitdetail = $units->getunitDetail();
			if (!empty($_GET['buyno'])){
				require_once "class/purchase.php";
				$purchase = new Purchase();

				$allcodedetail = $db->fetch_one("SELECT SUM(quantity-usedqty) AS maxr FROM detailbuy WHERE buyno='".$_GET['buyno']."' AND stockcode='".$_GET['stockcode']."' GROUP BY stockcode");
				if (empty($allcodedetail['maxr']))
					$allcodedetail['maxr'] = 0;
				
				$stockname = '';
				if (sizeof($stockoption) > 0){
					$stockname = htmlspecialchars($stockoption['generalname']).'|^|'.$unitdetail['funit'].','.$unitdetail['lunit'].'|^|'.$unitdetail['cvalue'].',1'.'|^|'.$allcodedetail['maxr'];
				}
			}
			else if (!empty($_GET['saleno'])){
				require_once "class/sale.php";
				$sale = new Sale();

				$allcodedetail = $db->fetch_one("SELECT SUM(quantity) AS maxr FROM detailsale WHERE saleno='".$_GET['saleno']."' AND stockcode='".$_GET['stockcode']."' GROUP BY stockcode");
				if (empty($allcodedetail['maxr']))
					$allcodedetail['maxr'] = 0;
				
				$stockname = '';
				if (sizeof($stockoption) > 0){
					$stockname = htmlspecialchars($stockoption['generalname']).'|^|'.$unitdetail['funit'].','.$unitdetail['lunit'].'|^|'.$unitdetail['cvalue'].',1'.'|^|'.$allcodedetail['maxr'];
				}
			}
			echo $stockname;
		}
	}
	else if ($_GET['list'] == 'getunitname'){
		if (!empty($_GET['unitcode'])){
			require_once "class/units.php";
			$units = new unit();
			$units->setCode($_GET['unitcode']);
			$getunitname = $units->getunitDetail();
			$unitname = '';
			if (sizeof($getunitname) > 0){
				$unitname = htmlspecialchars($getunitname['funit']).'|^|'.$getunitname['cvalue'].'|^|'.htmlspecialchars($getunitname['lunit']);
			}
			echo $unitname;
		}
	}
	else if ($_GET['list'] == 'brandpurchase'){
		if (!empty($_GET['stockcode'])){
			require_once "class/Stock.php";
			require_once "class/Brand.php";
			$stock = new Stock();
			$arrsc = explode('||',$_GET['stockcode']);
			$stock->setCode($arrsc[0]);
			$stockoption = $stock->getFirstStock();
			header("Content-type:text/xml");
			$stocko .= '<?xml version="1.0"?>';
			$stocko = '<complete>';
			if (sizeof($stockoption) > 0){
				$brand = new Brand();
				$brand->setCode($stockoption['brandcode']);
				$getbrandname = $brand->getBrandDetail();
				
				$stocko .= '<option value="'.htmlspecialchars($stockoption['brandcode']).'">'.htmlspecialchars($getbrandname['brandcode']).'</option>';
			}
			$stocko .= '</complete>';
			print($stocko);
		}
		else{
			
		}
	}
	else if ($_GET['list'] == 'typepurchase'){
		if (!empty($_GET['stockcode'])){
			require_once "class/Stock.php";
			require_once "class/type.php";
			$stock = new Stock();
			$arrsc = explode('||',$_GET['stockcode']);
			$stock->setCode($arrsc[0]);
			$stockoption = $stock->getFirstStock();
			header("Content-type:text/xml");
			$stocko .= '<?xml version="1.0"?>';
			$stocko = '<complete>';
			if (sizeof($stockoption) > 0){
				$type = new type();
				$type->setCode($stockoption['typecode']);
				$gettypename = $type->gettypeDetail();
				
				$stocko .= '<option value="'.htmlspecialchars($stockoption['typecode']).'">'.htmlspecialchars($gettypename['typecode']).'</option>';
			}
			$stocko .= '</complete>';
			print($stocko);
		}
	}
	else if ($_GET['list'] == 'unitpurchase'){
		if (!empty($_GET['stockcode'])){
			require_once "class/Stock.php";
			require_once "class/units.php";
			$stock = new Stock();
			$arrsc = explode('||',$_GET['stockcode']);
			$stock->setCode($arrsc[0]);
			$stockoption = $stock->getFirstStock();
			header("Content-type:text/xml");
			$stocko .= '<?xml version="1.0"?>';
			$stocko = '<complete>';
			if (sizeof($stockoption) > 0){
				$unit = new unit();
				$unit->setCode($stockoption['unitcode']);
				$getunitname = $unit->getunitDetail();
				
				if ($getunitname['funit'] == $getunitname['lunit'] && $getunitname['cvalue']==1){
					$stocko .= '<option value="'.htmlspecialchars($getunitname['lunit']).'">'.htmlspecialchars($getunitname['lunit']).'</option>';
				}
				else{
					$stocko .= '<option value="'.htmlspecialchars($getunitname['funit']).'">'.htmlspecialchars($getunitname['funit']).'</option>';
					$stocko .= '<option value="'.htmlspecialchars($getunitname['lunit']).'">'.htmlspecialchars($getunitname['lunit']).'</option>';
				}
			}
			$stocko .= '</complete>';
			print($stocko);
		}
	}
	else if ($_GET['list'] == 'checklasttransaction'){
		if (!empty($_GET['customercode'])){
			require_once "class/customer.php";
			$customer = new customer();
			$customer->setCode($_GET['customercode']);
			$cancredit = $customer->checkLastLimitTransaction();
			echo $cancredit;
		}
	}
?>