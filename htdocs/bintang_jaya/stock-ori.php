<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	
	if ($_POST['getlist'] == 'ajax'){
		$liststock = $stock->searchstock($_POST['keyword'],$_POST['field']);
		$lists = '';
		if (sizeof($liststock) > 0){
			$ctr = 1;
			foreach ($liststock as $list){
				$listsplit = '';
				$listpartno = '';
				$listsplit2 = '';
				$stock->setCode($list['stockcode']);
				$allpartno = $stock->getAllPartNo();
				$splits = sizeof($allpartno);
				if ($splits > 0){
					$listpartno = '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
					$io = 0; 
					foreach ($allpartno as $apn){
						$listpartno .= '
							<tr>
								<td width="100%" align="center"'.(($io < $splits-1)?' style="border-bottom: 1px solid #CCCCFF"':'').'>'.$apn['partno'].'</td>
							</tr>
						';
						$io++;
					}
					$listpartno .= '</table>';
				}
				/*if ($splits > 0){
					$io = 0;
					foreach ($allpartno as $apn){
						$stock->setPartNo($apn['partno']);
						$stockstatus = $stock->getStockAll();
						if ($io > 0){
							$listsplit2 .= '
								<tr class="bglist" ondblclick="window.open(\'stock.php?id='.$list['stockcode'].'\',\'_self\')" onmouseover="this.style.backgroundColor=\'#CCFFCC\'" onmouseout="this.style.backgroundColor=\'#EFEFEF\'">
									<td align="left">'.$apn['partno'].'</td>
									<td align="right">'.number_format($stockstatus['qty'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['minp'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['maxp'],0,",",".").'</td>
									<td align="center">'.date("d-M-y",$stockstatus['mexp']).'</td>
								</tr>
							';
						}
						else{
							$listsplit .= '
									<td align="left">'.$apn['partno'].'</td>
									<td align="right">'.number_format($stockstatus['qty'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['minp'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['maxp'],0,",",".").'</td>
									<td align="center">'.date("d-M-y",$stockstatus['mexp']).'</td>
							';
						}
						$io++;
					}
				}
				else{*/
					$stock->setPartNo('');
					$stockstatus = $stock->getStockAll();
					$listsplit .= '
						<td align="right">'.number_format($stockstatus['qty'],0,",",".").'</td>
						<td align="right">'.number_format($stockstatus['minp'],0,",",".").'</td>
						<td align="right">'.number_format($stockstatus['maxp'],0,",",".").'</td>
						<td align="center">'.date("d-M-y",$stockstatus['mexp']).'</td>
					';
				//}
				$brand->setCode($list['brandcode']);
				$getbrandname = $brand->getBrandDetail();
				$type->setCode($list['typecode']);
				$gettypename = $type->gettypeDetail();
				$location->setCode($list['locationcode']);
				$getlocationname = $location->getlocationDetail();
				$lists .= '
					<tr class="bglist" ondblclick="window.open(\'stock.php?id='.$list['stockid'].'\',\'_self\')" onmouseover="this.style.backgroundColor=\'#CCFFCC\'" onmouseout="this.style.backgroundColor=\'#EFEFEF\'">
						<td align="right">'.number_format($ctr,0,",",".").'</td>
						<td align="left">'.$list['stockcode'].'</td>
						<td align="left">'.$list['generalname'].'</td>
						<td align="left">'.$getbrandname['brandname'].'</td>
						<td align="left">'.$gettypename['typename'].'</td>
						<td align="left">'.$getlocationname['locationname'].'</td>
						'.$listsplit.'
						<td align="left">'.$listpartno.'</td>
					</tr>
				'.$listsplit2;
				$ctr++;
			}
		}
		//$stocklist = gettemplate('stocklist');
		//eval("\$stocklist = \"$stocklist\";");
		echo $stocklist;
		exit;
	}
		
	if (empty($_REQUEST['id'])){
		$liststock = $stock->getListStock();
		$lists = '';
		if (sizeof($liststock) > 0){
			$ctr = 1;
			foreach ($liststock as $list){
				$listsplit = '';
				$listpartno = '';
				$listsplit2 = '';
				$stock->setCode($list['stockcode']);
				$allpartno = $stock->getAllPartNo();
				$splits = sizeof($allpartno);
				if ($splits > 0){
					$listpartno = '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
					$io = 0; 
					foreach ($allpartno as $apn){
						$listpartno .= '
							<tr>
								<td width="100%" align="center"'.(($io < $splits-1)?' style="border-bottom: 1px solid #CCCCFF"':'').'>'.$apn['partno'].'</td>
							</tr>
						';
						$io++;
					}
					$listpartno .= '</table>';
				}
				/*if ($splits > 0){
					$io = 0;
					foreach ($allpartno as $apn){
						$stock->setPartNo($apn['partno']);
						$stockstatus = $stock->getStockAll();
						if ($io > 0){
							$listsplit2 .= '
								<tr class="bglist" ondblclick="window.open(\'stock.php?id='.$list['stockcode'].'\',\'_self\')" onmouseover="this.style.backgroundColor=\'#CCFFCC\'" onmouseout="this.style.backgroundColor=\'#EFEFEF\'">
									<td align="left">'.$apn['partno'].'</td>
									<td align="right">'.number_format($stockstatus['qty'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['minp'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['maxp'],0,",",".").'</td>
									<td align="center">'.date("d-M-y",$stockstatus['mexp']).'</td>
								</tr>
							';
						}
						else{
							$listsplit .= '
									<td align="left">'.$apn['partno'].'</td>
									<td align="right">'.number_format($stockstatus['qty'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['minp'],0,",",".").'</td>
									<td align="right">'.number_format($stockstatus['maxp'],0,",",".").'</td>
									<td align="center">'.date("d-M-y",$stockstatus['mexp']).'</td>
							';
						}
						$io++;
					}
				}
				else{*/
					$stock->setPartNo('');
					$stockstatus = $stock->getStockAll();
					$listsplit .= '
						<td align="right">'.number_format($stockstatus['qty'],0,",",".").'</td>
						<td align="right">'.number_format($stockstatus['minp'],0,",",".").'</td>
						<td align="right">'.number_format($stockstatus['maxp'],0,",",".").'</td>
						<td align="center">'.date("d-M-y",$stockstatus['mexp']).'</td>
					';
				//}
				$brand->setCode($list['brandcode']);
				$getbrandname = $brand->getBrandDetail();
				$type->setCode($list['typecode']);
				$gettypename = $type->gettypeDetail();
				$location->setCode($list['locationcode']);
				$getlocationname = $location->getlocationDetail();
				$lists .= '
					<tr class="bglist" ondblclick="window.open(\'stock.php?id='.$list['stockid'].'\',\'_self\')" onmouseover="this.style.backgroundColor=\'#CCFFCC\'" onmouseout="this.style.backgroundColor=\'#EFEFEF\'">
						<td align="right">'.number_format($ctr,0,",",".").'</td>
						<td align="left">'.$list['stockcode'].'</td>
						<td align="left">'.$list['generalname'].'</td>
						<td align="left">'.$getbrandname['brandname'].'</td>
						<td align="left">'.$gettypename['typename'].'</td>
						<td align="left">'.$getlocationname['locationname'].'</td>
						'.$listsplit.'
						<td align="left">'.$listpartno.'</td>
					</tr>
				'.$listsplit2;
				$ctr++;
			}
		}
		//$stocklist = gettemplate('stocklist');
		//eval("\$stocklist = \"$stocklist\";");
	}
	else{
		$stock->setId($_REQUEST['id']);
		$firststock = $stock->getFirstStock();
		$brand->setCode($firststock['brandcode']);
		$getbrandname = $brand->getBrandDetail();
		$type->setCode($firststock['typecode']);
		$gettypename = $type->gettypeDetail();
		$location->setCode($firststock['locationcode']);
		$getlocationname = $location->getlocationDetail();
		$stgr->setCode($firststock['stgrcode']);
		$getstgrname = $stgr->getstgrDetail();
		
		$stock->setCode($firststock['stockcode']);
		$stpn = $stock->getAllPartNo();
		$firstcreated = '';
		$stprn = '';
		if (sizeof($stpn) > 0){
			$stprn = '<select size="5" multiple="multiple" style="width: 300px">';
			foreach ($stpn as $stn){
				if (empty($firstcreated))
					$firstcreated = $stn['partno'];
				$stprn .= '<option>'.$stn['partno'].'</option>';
			}
			$stprn .= '</select>';
		}
		$ctr = 1;
		$remainings = 0;
		$assets = 0;
		if (sizeof($firststock) > 0){
			$remainings += $firststock['quantity'];
			$lists .= '
				<tr class="bglist" onmouseover="this.style.backgroundColor=\'#CCFFCC\'" onmouseout="this.style.backgroundColor=\'#EFEFEF\'">
					<td align="right">'.number_format($ctr,0,",",".").'</td>
					<td align="center">'.date("d-M-Y",$firststock['createddate']).'</td>
					<td align="left">'.$firstcreated.'</td>
					<td align="left">'.$firststock['generalname'].'</td>
					<td align="left">Stok Awal</td>
					<td align="right">'.number_format($firststock['quantity'],0,",",".").'</td>
					<td align="right">0</td>
					<td align="right">'.number_format($remainings,0,",",".").'</td>
					<td align="right">'.number_format($firststock['buyprice'],0,",",".").'</td>
					<td align="right">0</td>
					<td align="center">'.date("d-M-Y",$firststock['expdate']).'</td>
					<td align="left">&nbsp;</td>
				</tr>
			';
			$ctr++;
			$assets += $firststock['quantity'] * $firststock['buyprice'];
		}
		$detailstock = $stock->getStockDetail();
		if (sizeof($detailstock) > 0){
			foreach ($detailstock as $dtls){
				$remainings += $dtls['quantity'];
				$lists .= '
					<tr class="bglist" onmouseover="this.style.backgroundColor=\'#CCFFCC\'" onmouseout="this.style.backgroundColor=\'#EFEFEF\'">
						<td align="right">'.number_format($ctr,0,",",".").'</td>
						<td align="center">'.date("d-M-Y",$dtls['buydate']).'</td>
						<td align="left">'.$dtls['partno'].'</td>
						<td align="left">'.$dtls['stockname'].'</td>
						<td align="left">'.$dtls['suppliercode'].'</td>
						<td align="right">'.number_format($dtls['quantity'],0,",",".").'</td>
						<td align="right">0</td>
						<td align="right">'.number_format($remainings,0,",",".").'</td>
						<td align="right">'.number_format($dtls['buyprice'],0,",",".").'</td>
						<td align="right">0</td>
						<td align="center">'.date("d-M-Y",$dtls['expdate']).'</td>
						<td align="left">'.$dtls['description'].'</td>
					</tr>
				';
				$ctr++;
				$assets += $dtls['quantity'] * $dtls['buyprice'];
			}
		}
		$assetsf = number_format($assets,0,",",".");
		$stocklist = gettemplate('stockdetail');
		eval("\$stocklist = \"$stocklist\";");
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('stock');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>