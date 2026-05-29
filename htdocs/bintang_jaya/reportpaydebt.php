<?php
	require_once "global.php";
	
	require_once "class/supplier.php";
	require_once "class/purchase.php";
	require_once "class/PurchaseR.php";
	require_once "class/PayDebt.php";
	require_once "class/area.php";
	$supplier = new supplier();
	$purchase = new Purchase();
	$purchaser = new PurchaseR();
	$paydebt = new PayDebt();
	$area = new area();
	
	if (empty($useraccess['report_paydebt'])){
		redirecting('index.php');
	}
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_POST['printit'] == 'prints'){
		$printtemplate = 'reportpaydebt';
		
		$sqls = array();
		if ($_POST['basedon'] == 'complete'){
			array_push($sqls,'complete = 1');
			
			if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
				$startdate = strtotime($_POST['datestart']);
				$enddate = strtotime($_POST['dateend'].' 23:59:59');
				
				array_push($sqls,"completedate >= '".$startdate."' AND completedate <= '".$enddate."'");
			}
		}
		else if ($_POST['basedon'] == 'incomplete'){
			array_push($sqls,'complete = 0');
			
			if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
				$startdate = strtotime($_POST['datestart']);
				$enddate = strtotime($_POST['dateend'].' 23:59:59');
				
				array_push($sqls,"startdate >= '".$startdate."' AND enddate <= '".$enddate."'");
			}
		}
		else{
			$fields = 'paymentdate';
			
			if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
				$startdate = strtotime($_POST['datestart']);
				$enddate = strtotime($_POST['dateend'].' 23:59:59');
				
				array_push($sqls,"startdate >= '".$startdate."' AND enddate <= '".$enddate."'");
			}
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		if (!empty($_POST['suppliercode'])){
			$alldata = $db->fetch_all("SELECT * FROM supplier WHERE suppliercode='".$_POST['suppliercode']."'");
		}
		else{
			$alldata = $supplier->getListsupplier('partial');
		}
		
		$trx = 0;
		$total = 0;		
		if (sizeof($alldata) > 0){
			foreach ($alldata as $adt){
				if (!empty($sql)){
					$sqlq = $sql." AND supplierid='".$adt['supplierid']."'";
				}
				else{
					$sqlq = " WHERE supplierid='".$adt['supplierid']."'";
				}
				$allpaydebt = $db->fetch_all("SELECT * FROM headerpaydebt".$sqlq);
				$temptrx = 0;
				$temptotal = 0;
				$list = '';
				if (sizeof($allpaydebt) > 0){
					foreach ($allpaydebt as $apd){
						$paydebt->setId($apd['hpid']);
						$getdetailpaydebt = $paydebt->getDetailPayDebt();
						$splits = sizeof($getdetailpaydebt);
						
						$listsplit = '';
						$listsplit2 = '';
						$rstext = '';
						$temporarytotal = 0;
						if ($splits > 0){
							if ($splits > 1){
								$rstext = ' rowspan="'.$splits.'"';
							}
							$io = 0;
							$subtotalfk = 0;
							foreach ($getdetailpaydebt as $gdb){
								if ($gdb['types'] == 'return'){
									$purchaser->setDetailId($gdb['hbid']);
									$detailheadersr = $purchaser->getDetailBuyRIndv();
								
									if ($statususer == 1){
										$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
										$totalbuyfk = $detailheadersr['quantityf'] * $detailheadersr['buyrprice'];
										$totaldiscfk = $detailheadersr['disc'] / 100 * $totalbuyfk;
										$gdb['pays'] = $totalbuyfk - $totaldiscfk;
										$temporarytotal -= $gdb['pays'];
									}
									
									$getpchdetail = $purchaser->getDetailBuyRItem();
									$getdbid = '';
									if (sizeof($getpchdetail) > 0){
										foreach ($getpchdetail as $gpch){
											$getdbid = $gpch['dbid'];
											break;
										}
									}
									$purchase->setDetailId($getdbid);
									$detaildetbuy = $purchase->getDetailBuyIndv();
									$purchase->setBuyNo($detaildetbuy['orderno']);
									$detailheaderbuy = $purchase->getHeaderBuy();
									
									$detailheaders['orderno'] = 'RETUR BELI #'.$detailheadersr['buyrid'].' DARI BON : '.$detailheaderbuy['orderno'];
									
									$aligns = 'right';
									$signs = '-';
								}
								else{
									$purchase->setId($gdb['hbid']);
									$detailheaders = $purchase->getHeaderBuy();
									
									if ($statususer == 1){
										$gdb['pays'] = 0;
										$purchase->setBuyNo($detailheaders['buyno']);
										$alldetailp = $purchase->getDetailBuy();
										if (sizeof($alldetailp) > 0){
											foreach ($alldetailp as $adp){
												$adp['quantityf'] = discq($adp['quantityf']);
												$totalbuyfk = $adp['quantityf'] * $adp['buyprice'];
												$totaldiscfk = $adp['disc'] / 100 * $totalbuyfk;
												$gdb['pays'] += $totalbuyfk - $totaldiscfk;
											}
											$totalgdiscfk = $detailheaders['disc'] / 100 * $gdb['pays'];
											$totalafgdiscfk = $gdb['pays'] - $totalgdiscfk;
											$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
											$gdb['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
											
											$temporarytotal += $gdb['pays'];
										}
									}
								
									$aligns = 'left';
									$signs = '';
								}
								
								if ($io == 0){
									$listsplit .= '
										<td align="'.$aligns.'">'.htmlspecialchars($detailheaders['orderno']).'</td>
										<td align="right">'.$signs.number_format($gdb['pays'],2,",",".").'</td>
									';
								}
								else{
									$listsplit2 .= '
										<tr>
											<td align="'.$aligns.'">'.htmlspecialchars($detailheaders['orderno']).'</td>
											<td align="right">'.$signs.number_format($gdb['pays'],2,",",".").'</td>
										</tr>
									';
								}
								$io++;						
							}
						}
						else{
									$listsplit .= '
										<td align="left"></td>
										<td align="right"></td>
									';
						}
						
						if ($statususer == 1){
							$apd['totalpayment'] = $temporarytotal;
						}
						
						$temptrx++;
						$temptotal += $apd['totalpayment'];
						$trx++;
						$total += $apd['totalpayment'];
						
						$list .= '
								<tr>
									<td align="left"'.$rstext.'>'.$apd['hpid'].'</td>
									<td align="center"'.$rstext.'>'.date("d-m-Y",$apd['paymentdate']).'</td>
									'.$listsplit.'
									<td align="right"'.$rstext.'>'.number_format($apd['totalpayment'],0,",",".").'</td>
									<td align="right"'.$rstext.'>'.$arrpays[$apd['complete']].'</td>
								</tr>'.$listsplit2.'
						';					
					}
										
					$listall .= '
						<div align="right" style="width: 100%; padding-top: 10px">
						<span style="float: left">Supplier : <b>'.htmlspecialchars($adt['suppliercode'].' - '.$adt['suppliername']).'</b></span>
						Tanggal Cetak : '.$printdate.'</div>
						<div align="center" style="width: 100%; padding-bottom: 20px; border-bottom: 1px dotted #000; clear: both">
						<table border="1" cellpadding="3" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="13%" bgcolor="#DEDEDE">NO PEMBAYARAN</th>
							<th align="center" width="12%" bgcolor="#DEDEDE">TGL PEMBAYARAN</th>
							<th align="center" width="25%" bgcolor="#DEDEDE">NO BON PEMBELIAN / RETUR</th>
							<th align="center" width="20%" bgcolor="#DEDEDE">TOTAL PEMBELIAN / RETUR</th>
							<th align="center" width="20%" bgcolor="#DEDEDE">TOTAL PEMBAYARAN</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">STATUS</th>
						</tr>
						'.$list.'
						<tr>
							<td align="center"><b>'.$temptrx.'</b></td>
							<td align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
							<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
							<td align="right" height="30"></td>
						</tr>
						</table></div>
					';
				}
			}
			
			if (!empty($listall)){
				$listall .= '
					<div align="left" style="width: 100%; padding: 10px 0">
					<table border="0" cellpadding="3" cellspacing="0" width="100%">
					<tr>
						<td align="center" width="13%"><b>'.$trx.'</b></td>
						<td align="left" width="57%">&nbsp;<b>GRAND TOTAL</b></td>
						<td align="right" width="20%"><b>'.number_format($total,2,",",".").'</b></td>
						<td align="right" width="10%"></td>
					</tr>
					</table></div>
				';
			}
		}
	}
	else{
		$printtemplate = 'reportpaydebtinit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>