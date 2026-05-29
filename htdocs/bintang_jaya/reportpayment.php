<?php
	require_once "global.php";
	
	require_once "class/customer.php";
	require_once "class/supplier.php";
	require_once "class/sale.php";
	require_once "class/SaleR.php";
	require_once "class/purchase.php";
	require_once "class/PurchaseR.php";
	require_once "class/Payment.php";
	require_once "class/area.php";
	$customer = new customer();
	$sale = new Sale();
	$saler = new SaleR();
	$payment = new Payment();
	$supplier = new supplier();
	$purchase = new Purchase();
	$purchaser = new PurchaseR();
	$area = new area();
	
	if (empty($useraccess['report_payment'])){
		redirecting('index.php');
	}
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_POST['printit'] == 'prints'){
		$printtemplate = 'reportpayment';
		
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
		
		if (!empty($_POST['customercode'])){
			$alldata = $db->fetch_all("SELECT * FROM customer WHERE customercode='".$_POST['customercode']."'");
		}
		else{
			$alldata = $customer->getListcustomer('partial');
		}
		
		$trx = 0;
		$total = 0;		
		if (sizeof($alldata) > 0){
			foreach ($alldata as $adt){
				if (!empty($sql)){
					$sqlq = $sql." AND customerid='".$adt['customerid']."'";
				}
				else{
					$sqlq = " WHERE customerid='".$adt['customerid']."'";
				}
				$allpaydebt = $db->fetch_all("SELECT * FROM headerpayment".$sqlq);
				$temptrx = 0;
				$temptotal = 0;
				$list = '';
				if (sizeof($allpaydebt) > 0){
					foreach ($allpaydebt as $apd){
						$payment->setId($apd['hpid']);
						$getdetailpayment = $payment->getDetailPayment();
						$splits = sizeof($getdetailpayment);
						
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
							foreach ($getdetailpayment as $gdb){
								if ($gdb['types'] == 'return'){
									$saler->setDetailId($gdb['hsid']);
									$detailheadersr = $saler->getDetailSaleRIndv();
									
									if ($statususer == 1){
										$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
										$totalsalefk = $detailheadersr['quantityf'] * $detailheadersr['salerprice'];
										$totaldiscfk = $detailheadersr['disc'] / 100 * $totalsalefk;
										$gdb['pays'] = $totalsalefk - $totaldiscfk;
										$temporarytotal -= $gdb['pays'];
									}

									$sale->setDetailId($detailheadersr['dsid']);
									$detailheadersale = $sale->getDetailSaleIndv();
									
									$detailheaders['saleno'] = 'RETUR JUAL #'.$detailheadersr['salerid'].' DARI FAKTUR : '.$detailheadersale['saleno'];
									$detailheaders['saledate'] = $detailheadersr['salerdate'];
									
									$aligns = 'left';
									$signs = '-';
								}
								else if ($gdb['types'] == 'sale'){
									$sale->setId($gdb['hsid']);
									$detailheaders = $sale->getHeaderSale();
								
									if ($statususer == 1){
										$gdb['pays'] = 0;
										$sale->setSaleNo($detailheaders['saleno']);
										$alldetailp = $sale->getDetailSale();
										if (sizeof($alldetailp) > 0){
											foreach ($alldetailp as $adp){
												$adp['quantityf'] = discq($adp['quantityf']);
												$totalsalefk = $adp['quantityf'] * $adp['saleprice'];
												$totaldiscfk = $adp['disc'] / 100 * $totalsalefk;
												$gdb['pays'] += $totalsalefk - $totaldiscfk;
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
								else if ($gdb['types'] == 'returnby'){
									$purchaser->setDetailId($gdb['hsid']);
									$detailheadersr = $purchaser->getDetailBuyRIndv();
									
									if ($statususer == 1){
										$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
										$totalbuyfk = $detailheadersr['quantityf'] * $detailheadersr['buyrprice'];
										$totaldiscfk = $detailheadersr['disc'] / 100 * $totalbuyfk;
										$gdb['pays'] = $totalbuyfk - $totaldiscfk;
										$temporarytotal += $gdb['pays'];
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
									$signs = '';
								}
								else if ($gdb['types'] == 'buy'){
									$purchase->setId($gdb['hsid']);
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
											
											$temporarytotal -= $gdb['pays'];
										}
									}
								
									$aligns = 'left';
									$signs = '-';
								}
								
								if ($io == 0){
									if ($gdb['types'] == 'return' || $gdb['types'] == 'sale'){
										$listsplit .= '
											<td align="'.$aligns.'">'.htmlspecialchars($detailheaders['saleno']).'</td>
											<td align="center">-</td>
											<td id="paymentprice_'.$ctr.'-'.$io.'" align="right">'.$signs.number_format($gdb['pays'],2,",",".").'</td>
										';
									}
									else{
										$listsplit .= '
											<td align="center">-</td>
											<td align="'.$aligns.'">'.htmlspecialchars($detailheaders['orderno']).'</td>
											<td id="paymentprice_'.$ctr.'-'.$io.'" align="right">'.$signs.number_format($gdb['pays'],2,",",".").'</td>
										';
									}
								}
								else{
									if ($gdb['types'] == 'return' || $gdb['types'] == 'sale'){
										$listsplit2 .= '
											<tr>
												<td align="'.$aligns.'">'.htmlspecialchars($detailheaders['saleno']).'</td>
												<td align="center">-</td>
												<td align="right">'.$signs.number_format($gdb['pays'],2,",",".").'</td>
											</tr>
										';
									}
									else{
										$listsplit2 .= '
											<tr>
												<td align="center">-</td>
												<td align="'.$aligns.'">'.htmlspecialchars($detailheaders['orderno']).'</td>
												<td align="right">'.$signs.number_format($gdb['pays'],2,",",".").'</td>
											</tr>
										';
									}
								}
								$io++;						
							}
						}
						else{
									$listsplit .= '
										<td align="left"></td>
										<td align="right"></td>
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
						<span style="float: left">Customer / Supplier : <b>'.htmlspecialchars($adt['customercode'].' - '.$adt['customername']).'</b></span>
						Tanggal Cetak : '.$printdate.'</div>
						<div align="center" style="width: 100%; padding-bottom: 20px; border-bottom: 1px dotted #000; clear: both">
						<table border="1" cellpadding="3" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="10%" bgcolor="#DEDEDE">NO PENAGIHAN</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">TGL PENAGIHAN</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">NO FAKTUR PENJUALAN / RETUR</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">NO BON PEMBELIAN / RETUR</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">TOTAL FAKTUR / RETUR</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">TOTAL PENAGIHAN</th>
							<th align="center" width="8%" bgcolor="#DEDEDE">STATUS</th>
						</tr>
						'.$list.'
						<tr>
							<td align="center"><b>'.$temptrx.'</b></td>
							<td align="left" colspan="4">&nbsp;<b>TOTAL</b></td>
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
						<td align="center" width="10%"><b>'.$trx.'</b></td>
						<td align="left" width="64%">&nbsp;<b>GRAND TOTAL</b></td>
						<td align="right" width="18%"><b>'.number_format($total,2,",",".").'</b></td>
						<td align="right" width="8%"></td>
					</tr>
					</table></div>
				';
			}
		}
	}
	else{
		$printtemplate = 'reportpaymentinit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>