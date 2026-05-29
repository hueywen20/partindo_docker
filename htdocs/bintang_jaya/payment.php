<?php
	require_once "global.php";
	
	require_once "class/customer.php";
	require_once "class/supplier.php";
	require_once "class/sale.php";
	require_once "class/SaleR.php";
	require_once "class/purchase.php";
	require_once "class/PurchaseR.php";
	require_once "class/Payment.php";
	require_once "class/PayDebt.php";
	require_once "class/area.php";
	$customer = new customer();
	$sale = new Sale();
	$saler = new SaleR();
	$payment = new Payment();
	$paydebt = new PayDebt();
	$supplier = new supplier();
	$purchase = new Purchase();
	$purchaser = new PurchaseR();
	$area = new area();
	
	$paymentno = $_REQUEST['no'];
	$headerpayment['totalpayment'] = 0;
	$headerpayment['statusflat'] = '+';
	
	if (!empty($paymentno)){
		$payment->setId($paymentno);
		$headerpayment = $payment->getHeaderPayment();
		if ( $headerpayment['status'] == 2){
		redirecting("paydebt.php?no=".$paymentno);
		} 
	}
		
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($paymentno)){
				$temporarytotal = 0;
				$payment->setId($paymentno);
				$allpayment = $payment->getDetailPayment();
				if (sizeof($allpayment) > 0){
					foreach ($allpayment as $ap){						
						if ($ap['types'] == 'return'){
							$saler->setDetailId($ap['hsid']);
							$detailheadersr = $saler->getDetailSaleRIndv();
									
							if ($statususer == 1){
								$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
								$totalsalefk = $detailheadersr['quantityf'] * $detailheadersr['salerprice'];
								$totaldiscfk = $detailheadersr['disc'] / 100 * $totalsalefk;
								$ap['pays'] = $totalsalefk - $totaldiscfk;
								$temporarytotal -= $ap['pays'];
							}
							
							$sale->setDetailId($detailheadersr['dsid']);
							$detailheadersale = $sale->getDetailSaleIndv();
							
							$detailheaders['saleno'] = 'RETUR JUAL #'.$detailheadersr['salerid'].' DARI FAKTUR : '.$detailheadersale['saleno'];
							$detailheaders['saledate'] = $detailheadersr['salerdate'];
							
							$aligns = 'left';
							$signs = '';
						}
						else if ($ap['types'] == 'sale'){
							$sale->setId($ap['hsid']);
							$detailheaders = $sale->getHeaderSale();
								
							if ($statususer == 1){
								$ap['pays'] = 0;
								$sale->setSaleNo($detailheaders['saleno']);
								$alldetailp = $sale->getDetailSale();
								if (sizeof($alldetailp) > 0){
									foreach ($alldetailp as $adp){
										$adp['quantityf'] = discq($adp['quantityf']);
										$totalsalefk = $adp['quantityf'] * $adp['saleprice'];
										$totaldiscfk = $adp['disc'] / 100 * $totalsalefk;
										$ap['pays'] += $totalsalefk - $totaldiscfk;
									}
									$totalgdiscfk = $detailheaders['disc'] / 100 * $ap['pays'];
									$totalafgdiscfk = $ap['pays'] - $totalgdiscfk;
									$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
									$ap['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
									
									$temporarytotal += $ap['pays'];
								}
							}
						
							$aligns = 'left';
							$signs = '';
						}
						else if ($ap['types'] == 'returnby'){
							$purchaser->setDetailId($ap['hsid']);
							$detailheadersr = $purchaser->getDetailBuyRIndv();
								
							if ($statususer == 1){
								$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
								$totalbuyfk = $detailheadersr['quantityf'] * $detailheadersr['buyrprice'];
								$totaldiscfk = $detailheadersr['disc'] / 100 * $totalbuyfk;
								$ap['pays'] = $totalbuyfk - $totaldiscfk;
								$temporarytotal += $ap['pays'];
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
							$detailheaders['buydate'] = $detailheadersr['buyrdate'];
							
							$aligns = 'left';
							$signs = '';
						}
						else if ($ap['types'] == 'buy'){
							$purchase->setId($ap['hsid']);
							$detailheaders = $purchase->getHeaderBuy();
									
							if ($statususer == 1){
								$ap['pays'] = 0;
								$purchase->setBuyNo($detailheaders['buyno']);
								$alldetailp = $purchase->getDetailBuy();
								if (sizeof($alldetailp) > 0){
									foreach ($alldetailp as $adp){
										$adp['quantityf'] = discq($adp['quantityf']);
										$totalbuyfk = $adp['quantityf'] * $adp['buyprice'];
										$totaldiscfk = $adp['disc'] / 100 * $totalbuyfk;
										$ap['pays'] += $totalbuyfk - $totaldiscfk;
									}
									$totalgdiscfk = $detailheaders['disc'] / 100 * $ap['pays'];
									$totalafgdiscfk = $ap['pays'] - $totalgdiscfk;
									$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
									$ap['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
									
									$temporarytotal -= $ap['pays'];
								}
							}
						
							$aligns = 'left';
							$signs = '';
						}
						
						if ($ap['types'] == 'return' || $ap['types'] == 'sale'){
							$lists .= '
								<row id="'.$ap['types'].'_'.$ap['hsid'].'">
									<cell align="'.$aligns.'">'.htmlspecialchars($detailheaders['saleno']).'</cell>
									<cell>'.date("d-m-Y",$detailheaders['saledate']).'</cell>
									<cell align="center">-</cell>
									<cell align="center">-</cell>
									<cell>'.$signs.$ap['pays'].'</cell>
									<cell>'.htmlspecialchars($ap['description']).'</cell>
								</row>
							';
						}
						else{
							$lists .= '
								<row id="'.$ap['types'].'_'.$ap['hsid'].'">
									<cell align="center">-</cell>
									<cell align="center">-</cell>
									<cell align="'.$aligns.'">'.htmlspecialchars($detailheaders['orderno']).'</cell>
									<cell>'.date("d-m-Y",$detailheaders['buydate']).'</cell>
									<cell>'.$signs.$ap['pays'].'</cell>
									<cell>'.htmlspecialchars($ap['description']).'</cell>
								</row>
							';
						}
					}
				}
			}
			$pclist = gettemplate('paymentdetaillist');
		}
		else if ($_GET['list'] == 'general'){
			if (isset($_GET['keyword'])){	
				if ($_GET['keyword'] != '' || (!empty($_GET['startpaysdate']) && !empty($_GET['endpaysdate']))){
					$startdatetoquery = 0;
					$enddatetoquery = 0;
					if (!empty($_GET['startpaysdate']) && !empty($_GET['endpaysdate'])){
						$startdatetoquery = strtotime($_GET['startpaysdate']);
						$enddatetoquery = strtotime($_GET['endpaysdate'].' 23:59:59');
					}
					$listpayment = $payment->searchPayment($_GET['keyword'],$_GET['field'],$startdatetoquery,$enddatetoquery);
				}
			}
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listpayment) > 0){
				$ctr = 1;
				foreach ($listpayment as $list){
					$setalignsc = 'left';
					$setscname = '';
					if (!empty($list['supplierid'])){
						$supplier->setId($list['supplierid']);
						$getsuppliername = $supplier->getsupplierDetail();
						$setscname = $getsuppliername['suppliername'];
						$setalignsc = 'right';
					}
					else{
						$customer->setId($list['customerid']);
						$getcustomername = $customer->getcustomerDetail();
						$setscname = $getcustomername['customername'];
					}
					
					$customer->setId($list['customerid']);
					$getcustomername = $customer->getcustomerDetail();
					
					$payment->setId($list['hpid']);
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
								$detailheaders = $saler->getDetailSaleRIndv();
								$detailheaders['saleno'] = $detailheaders['salerid'];
									
								if ($statususer == 1){
									$detailheaders['quantityf'] = discq($detailheaders['quantityf']);
									$totalsalefk = $detailheaders['quantityf'] * $detailheaders['salerprice'];
									$totaldiscfk = $detailheaders['disc'] / 100 * $totalsalefk;
									$gdb['pays'] = $totalsalefk - $totaldiscfk;
									$temporarytotal -= $gdb['pays'];
								}
								
								$aligns = 'right';
								$signs = '';
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
								$detailheaders = $purchaser->getDetailBuyRIndv();
								$detailheaders['orderno'] = $detailheaders['buyrid'];
								
								if ($statususer == 1){
									$detailheaders['quantityf'] = discq($detailheaders['quantityf']);
									$totalbuyfk = $detailheaders['quantityf'] * $detailheaders['buyrprice'];
									$totaldiscfk = $detailheaders['disc'] / 100 * $totalbuyfk;
									$gdb['pays'] = $totalbuyfk - $totaldiscfk;
									$temporarytotal += $gdb['pays'];
								}
								
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
								$signs = '';
							}
							
							if ($io == 0){
								if ($gdb['types'] == 'return' || $gdb['types'] == 'sale'){
									$listsplit .= '
										<td width="'.$cwarr[3].'" class="stufflist" align="'.$aligns.'">'.htmlspecialchars($detailheaders['saleno']).'</td>
										<td width="'.$cwarr[4].'" class="stufflist" align="center">-</td>
										<td width="'.$cwarr[5].'" class="stufflist" id="paymentprice_'.$ctr.'-'.$io.'" align="right">'.$signs.$codest->convertcodes($gdb['pays']).'</td>
									';
								}
								else{
									$listsplit .= '
										<td width="'.$cwarr[3].'" class="stufflist" align="center">-</td>
										<td width="'.$cwarr[4].'" class="stufflist" align="'.$aligns.'">'.htmlspecialchars($detailheaders['orderno']).'</td>
										<td width="'.$cwarr[5].'" class="stufflist" id="paymentprice_'.$ctr.'-'.$io.'" align="right">'.$signs.$codest->convertcodes($gdb['pays']).'</td>
									';
								}
							}
							else{
								if ($gdb['types'] == 'return' || $gdb['types'] == 'sale'){
									$listsplit2 .= '
										<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'payment.php?no='.$list['hpid'].'\',\'_self\')">
											<td width="'.$cwarr[3].'" class="stufflist" align="'.$aligns.'">'.htmlspecialchars($detailheaders['saleno']).'</td>
											<td width="'.$cwarr[4].'" class="stufflist" align="center">-</td>
											<td width="'.$cwarr[5].'" class="stufflist" id="paymentprice_'.$ctr.'-'.$io.'" align="right">'.$signs.$codest->convertcodes($gdb['pays']).'</td>
										</tr>
									';
								}
								else{
									$listsplit2 .= '
										<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'payment.php?no='.$list['hpid'].'\',\'_self\')">
											<td width="'.$cwarr[3].'" class="stufflist" align="center">-</td>
											<td width="'.$cwarr[4].'" class="stufflist" align="'.$aligns.'">'.htmlspecialchars($detailheaders['orderno']).'</td>
											<td width="'.$cwarr[5].'" class="stufflist" id="paymentprice_'.$ctr.'-'.$io.'" align="right">'.$signs.$codest->convertcodes($gdb['pays']).'</td>
										</tr>
									';
								}
							}
							$io++;
						}
					}
					else{
						$listsplit .= '
							<td width="'.$cwarr[3].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[4].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[5].'" class="stufflist" id="paymentprice_'.$ctr.'-'.$io.'" align="right"></td>
						';
					}

					$actioneditwidth = floor(51 / 100 * $cwarr[8]);
					$actiondeletewidth = $cwarr[8]-$actioneditwidth-3;
					
					if ($statususer == 1){
						$list['grandtotals'] = $temporarytotal;
					}
					
					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'payment.php?no='.$list['hpid'].'\',\'_self\')">
								<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.$list['hpid'].'</td>
								<td class="stufflist" width="'.$cwarr[1].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['paymentdate']).'</td>
								<td class="stufflist" width="'.$cwarr[2].'" align="left"'.$rstext.'>'.htmlspecialchars($setscname).'</td>
								'.$listsplit.'
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[6].'" align="right"'.$rstext.'>'.$codest->convertcodes($list['grandtotals']).'</td>
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[7].'" align="right"'.$rstext.'>'.$arrpays[$list['complete']].'</td>
								<td class="stufflist bgseparator" width="'.$cwarr[8].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
								(($useraccess['edit_payment'])?'<a href="payment.php?no='.$list['hpid'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
								(($useraccess['delete_payment'])?'<a href="javascript:deleteitem(\'payment.php?do=delete&no='.$list['hpid'].'\')">Hapus</a>':'-').'</span></td>
							</tr>'.$listsplit2.'
					';					
					
					$ctr++;
				}
			}
			$ctrgo = $ctr-1;
			$pclist = gettemplate('paymentlistdetail');
		}
		else if ($_GET['list'] == 'determine'){
			header("Content-type: text/xml");
			$stdate = strtotime($_GET['startdate']);
			$eddate = strtotime($_GET['enddate'].' 23:59:59');
			
			/* get sale */
			$listsale = $sale->getUnclaimSale($_GET['customercode'],$stdate,$eddate);
			$lists = '';
			$lsl = '';
			$lby = '';
			$tsl = 0;
			$tby = 0;
			if (sizeof($listsale) > 0){
				foreach ($listsale as $list){
					if ($statususer == 1){
						$ap['pays'] = 0;
						$sale->setSaleNo($list['saleno']);
						$alldetailp = $sale->getDetailSale();
						if (sizeof($alldetailp) > 0){
							foreach ($alldetailp as $adp){
								$adp['quantityf'] = discq($adp['quantityf']);
								$totalsalefk = $adp['quantityf'] * $adp['saleprice'];
								$totaldiscfk = $adp['disc'] / 100 * $totalsalefk;
								$ap['pays'] += $totalsalefk - $totaldiscfk;
							}
							$totalgdiscfk = $list['disc'] / 100 * $ap['pays'];
							$totalafgdiscfk = $ap['pays'] - $totalgdiscfk;
							$totalgtaxfk = $list['tax'] / 100 * $totalafgdiscfk;
							$list['totalsale'] = intval($totalafgdiscfk + $totalgtaxfk);
						}
					}
					$lsl .= '
						<row id="sale_'.$list['saleid'].'">
							<cell>'.htmlspecialchars($list['saleno']).'</cell>
							<cell>'.date("d-m-Y",$list['saledate']).'</cell>
							<cell align="center">-</cell>
							<cell align="center">-</cell>
							<cell>'.$list['totalsale'].'</cell>
							<cell></cell>
						</row>
					';
					$tsl += $list['totalsale'];
				}
			}
					
			/* get return sale */
			$getsaler = $saler->getUnclaimSaleR($_GET['customercode'],$stdate,$eddate);
			if (sizeof($getsaler) > 0){
				foreach ($getsaler as $gsr){
					$sale->setDetailId($gsr['dsid']);
					$dtsls = $sale->getDetailSaleIndv();
					$printreturn = 'RETUR JUAL #'.$gsr['salerid'].' DARI FAKTUR : '.$dtsls['saleno'];
						
					if ($statususer == 1){								
						$gsr['quantityf'] = discq($gsr['quantityf']);
						$totalsalerfk = $gsr['quantityf'] * $gsr['salerprice'];
						$totaldiscfk = $gsr['disc'] / 100 * $totalsalerfk;
						$gsr['totalsalerad'] = $totalsalerfk - $totaldiscfk;
					}
					
					$lsl .= '
						<row id="return_'.$gsr['dsrid'].'">
							<cell align="left">'.htmlspecialchars($printreturn).'</cell>
							<cell>'.date("d-m-Y",$gsr['salerdate']).'</cell>
							<cell align="center">-</cell>
							<cell align="center">-</cell>
							<cell>'.$gsr['totalsalerad'].'</cell>
							<cell></cell>
						</row>
					';
					
					$tsl -= $gsr['totalsalerad'];
				}
			}
			
			/* get buy */
			$listbuy = $purchase->getUnclaimBuy($_GET['customercode'],$stdate,$eddate);
			if (sizeof($listbuy) > 0){
				foreach ($listbuy as $list){
					if ($statususer == 1){
						$ap['pays'] = 0;
						$purchase->setBuyNo($list['buyno']);
						$alldetailp = $purchase->getDetailBuy();
						if (sizeof($alldetailp) > 0){
							foreach ($alldetailp as $adp){
								$adp['quantityf'] = discq($adp['quantityf']);
								$totalbuyfk = $adp['quantityf'] * $adp['buyprice'];
								$totaldiscfk = $adp['disc'] / 100 * $totalbuyfk;
								$ap['pays'] += $totalbuyfk - $totaldiscfk;
							}
							$totalgdiscfk = $list['disc'] / 100 * $ap['pays'];
							$totalafgdiscfk = $ap['pays'] - $totalgdiscfk;
							$totalgtaxfk = $list['tax'] / 100 * $totalafgdiscfk;
							$list['totalbuy'] = intval($totalafgdiscfk + $totalgtaxfk);
						}
					}
					$lby .= '
						<row id="buy_'.$list['buyid'].'">
							<cell align="center">-</cell>
							<cell align="center">-</cell>
							<cell>'.htmlspecialchars($list['orderno']).'</cell>
							<cell>'.date("d-m-Y",$list['buydate']).'</cell>
							<cell>'.$list['totalbuy'].'</cell>
							<cell></cell>
						</row>
					';
					
					$tby += $list['totalbuy'];
				}
			}
			
			/* get return buy */
			$getbuyr = $purchaser->getUnclaimBuyR($_GET['customercode'],$stdate,$eddate);
			if (sizeof($getbuyr) > 0){
				foreach ($getbuyr as $gsr){
					$getorderno = $db->fetch_one("SELECT hb.orderno FROM detailbuy db INNER JOIN headerbuy hb ON db.buyno = hb.buyno WHERE db.dbid = '".$gsr['dbid']."'");
					$printreturn = 'RETUR BELI #'.$gsr['buyrid'].' DARI BON : '.$getorderno['orderno'];
						
					if ($statususer == 1){								
						$gsr['quantityf'] = discq($gsr['quantityf']);
						$totalbuyfk = $gsr['quantityf'] * $gsr['buyrprice'];
						$totaldiscfk = $gsr['disc'] / 100 * $totalbuyfk;
						$gsr['totalbuyrad'] = $totalbuyfk - $totaldiscfk;
					}

					$lby .= '
						<row id="returnby_'.$gsr['dbrid'].'">
							<cell align="center">-</cell>
							<cell align="center">-</cell>
							<cell align="left">'.htmlspecialchars($printreturn).'</cell>
							<cell>'.date("d-m-Y",$gsr['buyrdate']).'</cell>
							<cell>'.$gsr['totalbuyrad'].'</cell>
							<cell></cell>
						</row>
					';
					
					$tby -= $gsr['totalbuyrad'];
				}
			}
			/*$listsaler = $saler->getUnclaimSaleR($_GET['customercode'],strtotime($_GET['startdate']),strtotime($_GET['enddate']));
			if (sizeof($listsaler) > 0){
				foreach ($listsaler as $list){
					$lists .= '
						<row id="return_'.$list['salerid'].'">
							<cell align="right">'.htmlspecialchars($list['salerid']).'</cell>
							<cell>'.date("d-m-Y",$list['salerdate']).'</cell>
							<cell>-'.$list['totalsaler'].'</cell>
							<cell></cell>
						</row>
					';
				}
			}*/
			/* if ($tsl >= $tby){ */
				$lists = $lsl.$lby;
			/* }
			else{
				$lists = $lby.$lsl;
			} */
			$pclist = gettemplate('paymentdetaillist');
		}
		else if ($_GET['list'] == 'detailrepay'){
			header("Content-type: text/xml");
			if (!empty($paymentno)){
				$temporarytotal = 0;
				$payment->setId($paymentno);
				$allpayment = $payment->getDetailRePayment();
				
				$ik = sizeof($allpayment);
				
				if ($statususer == 1){
					$alldtl = $payment->getDetailPayment();
					$temporarytotal = 0;
					if (sizeof($alldtl) > 0){
						foreach ($alldtl as $aad){
							if ($aad['types'] == 'return'){
								$saler->setDetailId($aad['hsid']);
								$detailheadersr = $saler->getDetailSaleRIndv();
									
								$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
								$totalsalerfk = $detailheadersr['quantityf'] * $detailheadersr['salerprice'];
								$totaldiscfk = $detailheadersr['disc'] / 100 * $totalsalerfk;
								$aad['pays'] = $totalsalerfk - $totaldiscfk;
								$temporarytotal -= $aad['pays'];
							}
							else if ($aad['types'] == 'sale'){
								$sale->setId($aad['hsid']);
								$detailheaders = $sale->getHeaderSale();
										
								$aad['pays'] = 0;
								$sale->setSaleNo($detailheaders['saleno']);
								$alldetailp = $sale->getDetailSale();
								if (sizeof($alldetailp) > 0){
									foreach ($alldetailp as $adp){
										$adp['quantityf'] = discq($adp['quantityf']);
										$totalsalefk = $adp['quantityf'] * $adp['saleprice'];
										$totaldiscfk = $adp['disc'] / 100 * $totalsalefk;
										$aad['pays'] += $totalsalefk - $totaldiscfk;
									}
									$totalgdiscfk = $detailheaders['disc'] / 100 * $aad['pays'];
									$totalafgdiscfk = $aad['pays'] - $totalgdiscfk;
									$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
									$aad['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
									
									$temporarytotal += $aad['pays'];
								}
							}
							else if ($aad['types'] == 'returnby'){
								$purchaser->setDetailId($aad['hsid']);
								$detailheadersr = $purchaser->getDetailBuyRIndv();
									
								$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
								$totalbuyfk = $detailheadersr['quantityf'] * $detailheadersr['buyrprice'];
								$totaldiscfk = $detailheadersr['disc'] / 100 * $totalbuyfk;
								$aad['pays'] = $totalbuyfk - $totaldiscfk;
								$temporarytotal += $aad['pays'];
							}
							else if ($aad['types'] == 'buy'){
								$purchase->setId($aad['hsid']);
								$detailheaders = $purchase->getHeaderBuy();
										
								$aad['pays'] = 0;
								$purchase->setBuyNo($detailheaders['buyno']);
								$alldetailp = $purchase->getDetailBuy();
								if (sizeof($alldetailp) > 0){
									foreach ($alldetailp as $adp){
										$adp['quantityf'] = discq($adp['quantityf']);
										$totalbuyfk = $adp['quantityf'] * $adp['buyprice'];
										$totaldiscfk = $adp['disc'] / 100 * $totalbuyfk;
										$aad['pays'] += $totalbuyfk - $totaldiscfk;
									}
									$totalgdiscfk = $detailheaders['disc'] / 100 * $aad['pays'];
									$totalafgdiscfk = $aad['pays'] - $totalgdiscfk;
									$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
									$aad['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
									
									$temporarytotal -= $aad['pays'];
								}
							}
						}
					}
					$blastit = floor($temporarytotal / $ik);
					$blastcounter = 0;
				}
				
				if ($ik > 0){
					$iy = 0;
					foreach ($allpayment as $ap){
						if ($statususer == 1){
							if ($iy == ($ik-1)){
								$blasttext = $temporarytotal - $blastcounter;
							}
							else{
								$blasttext = $blastit;
								$blastcounter += $blastit;
							}
							$ap['totals'] = $blasttext;
							$iy++;
						}
						
						if (!empty($ap['dates'])){
							$printdates = date("d-m-Y",$ap['dates']);
						}
						else{
							$printdates = '';
						}
						if (!empty($ap['duedates'])){
							$printduedates = date("d-m-Y",$ap['duedates']);
						}
						else{
							$printduedates = '';
						}
						
						if ($ap['types'] == 5 || $ap['types'] == 6){
							$signs = '';
						}
						else{
							$signs = '';
						}
						
						$printstatus = '';
						if ($ap['status'] == 1){
							$printstatus = 'Lunas';
						}
						
						$lists .= '
							<row id="r-'.$ap['drpyid'].'">
								<cell align="left">'.$ap['types'].'</cell>
								<cell align="left">'.htmlspecialchars($ap['bank']).'</cell>
								<cell align="left">'.htmlspecialchars($ap['accname']).'</cell>
								<cell align="left">'.htmlspecialchars($ap['accnumber']).'</cell>
								<cell align="center">'.$printdates.'</cell>
								<cell align="center">'.$printduedates.'</cell>
								<cell align="right">'.$signs.number_format($ap['totals'],2,",",".").'</cell>
								<cell align="center">'.htmlspecialchars($printstatus).'</cell>
								<cell align="left">'.htmlspecialchars($ap['notes']).'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('repaydetaillist');
		}
		eval("\$pclist = \"$pclist\";");
		echo $pclist;
		exit;
	}
	
	if ($_POST['submits'] == 'All' && $useraccess['add_payment']){
		$db->beginTransaction();
		$dbgetcustomer = $customer->getListcustomer('partial');
		$paymentdate = time();
		if (sizeof($dbgetcustomer) > 0){
			foreach ($dbgetcustomer as $dbgc){				
				$customer->setId($dbgc['customerid']);
				$getsaddr = $customer->getcustomeraddrdetail('all');
				if (sizeof($getsaddr) > 0){
					foreach ($getsaddr as $gsa){
						$_POST['customeraddrid'] = $gsa['detailcustid'];
						break;
					}
				}
				
				$allsale = $db->fetch_one("SELECT * FROM headersale WHERE trtype = 'credit' AND claims = 0 AND customercode='".$dbgc['customercode']."' ORDER BY saledate LIMIT 1");
				$allsaleend = $db->fetch_one("SELECT * FROM headersale WHERE trtype = 'credit' AND claims = 0 AND customercode='".$dbgc['customercode']."' ORDER BY saledate DESC LIMIT 1");
				
				$canprocess = false;
				if (empty($allsale['saleid'])){
					$allsaler = $db->fetch_one("SELECT dsr.* FROM detailsaler dsr INNER JOIN headersaler hsr ON dsr.salerid = hsr.salerid WHERE dsr.claims = 0 AND hsr.customercode='".$dbgc['customercode']."' ORDER BY dsr.salerdate LIMIT 1");
					$allsalerend = $db->fetch_one("SELECT dsr.* FROM detailsaler dsr INNER JOIN headersaler hsr ON dsr.salerid = hsr.salerid WHERE dsr.claims = 0 AND hsr.customercode='".$dbgc['customercode']."' ORDER BY dsr.salerdate DESC LIMIT 1");
					if (!empty($allsaler['salerid'])){
						$canprocess = true;
						$startingsdate = $allsaler['salerdate'];
						$endingsdate = $allsalerend['salerdate'];
					}
				}
				else{
					$canprocess = true;
					$startingsdate = $allsale['saledate'];
					$endingsdate = $allsaleend['saledate'];
				}
				
				if ($canprocess){
					$startingmonthwalk = date("n",$startingsdate);
					$startingyearwalk = date("Y",$startingsdate);
					$monthnows = date("n",$endingsdate);
					$yearnows = date("Y",$endingsdate);
					
					do{
						$manydays = getdaysinmonth($startingmonthwalk,$startingyearwalk);
						
						$startdatetext = '01-'.str_pad($startingmonthwalk,2,"0",STR_PAD_LEFT).'-'.$startingyearwalk;
						$enddatetext = str_pad($manydays,2,"0",STR_PAD_LEFT).'-'.str_pad($startingmonthwalk,2,"0",STR_PAD_LEFT).'-'.$startingyearwalk.' 23:59:59';
						$startdate = strtotime($startdatetext);
						$enddate = strtotime($enddatetext);
						
						/* get last month */
						if ($startingmonthwalk == 1){
							$prevmonthstarttext = '01-12-'.($startingyearwalk-1);
							$prevmonthendtext = '31-12-'.($startingyearwalk-1);
						}
						else{
							$prevmonthstarttext = '01-'.str_pad(($startingmonthwalk-1),2,"0",STR_PAD_LEFT).'-'.$startingyearwalk;
							$prevmonthendtext = str_pad(getdaysinmonth(($startingmonthwalk-1),$startingyearwalk),2,"0",STR_PAD_LEFT).'-'.str_pad(($startingmonthwalk-1),2,"0",STR_PAD_LEFT).'-'.$startingyearwalk.' 23:59:59';
						}
						$prevmonthstart = strtotime($prevmonthstarttext);
						$prevmonthend = strtotime($prevmonthendtext);
						
						$headerscreated = false;
						$saveto = '';
						$thismonthgt = 0;
						/* check if current month have payment already */
						$dbpaymentthismonth = $db->fetch_one("SELECT * FROM headerpayment WHERE customerid='".$dbgc['customerid']."' AND (startdate='".$startdate."' AND enddate='".$enddate."') AND complete = 0 AND status = 1 LIMIT 1");
						if (!empty($dbpaymentthismonth['hpid'])){
							$lastid = $dbpaymentthismonth['hpid'];
							$headerscreated = true;
							$payment->setId($lastid);
							$saveto = 'payment';
							$thismonthgt = $dbpaymentthismonth['grandtotals'];
						}
						else{
							/* check if current month have paydebt already */
							$supplier->setCode($dbgc['customercode']);
							$suppdetail = $supplier->getsupplierDetail();
							if (!empty($suppdetail['supplierid'])){
								$supplier->setId($suppdetail['supplierid']);
								
								$getsaddrsupp = $supplier->getsupplieraddrdetail('all');
								if (sizeof($getsaddrsupp) > 0){
									foreach ($getsaddrsupp as $gsa){
										$_POST['supplieraddrid'] = $gsa['detailsplid'];
										break;
									}
								}
								$supplier->setId("");
								
								$dbpaymentthismonth = $db->fetch_one("SELECT * FROM headerpayment WHERE supplierid='".$suppdetail['supplierid']."' AND (startdate='".$startdate."' AND enddate='".$enddate."') AND complete = 0 AND status = 2 LIMIT 1");
								if (!empty($dbpaymentthismonth['hpid'])){
									$lastid = $dbpaymentthismonth['hpid'];
									$headerscreated = true;
									$payment->setId($lastid);
									$saveto = 'paydebt';
									$thismonthgt = $dbpaymentthismonth['grandtotals'];
								}
							}
							else{								
								$dbpaymentthismonth = $db->fetch_one("SELECT * FROM headerpayment WHERE customerid='".$dbgc['customerid']."' AND (startdate='".$startdate."' AND enddate='".$enddate."') AND complete = 0 AND status = 2 LIMIT 1");
								if (!empty($dbpaymentthismonth['hpid'])){
									$lastid = $dbpaymentthismonth['hpid'];
									$headerscreated = true;
									$payment->setId($lastid);
									$saveto = 'paydebt';
									$thismonthgt = $dbpaymentthismonth['grandtotals'];
									
								}
							}
						}
							
						$dbpaymentprev = $db->fetch_one("SELECT SUM(remainingnow) AS trn FROM headerpayment WHERE customerid='".$dbgc['customerid']."' AND (startdate='".$prevmonthstart."' AND enddate='".$prevmonthend."') AND status = 1");
						$remainingfromprevious = $dbpaymentprev['remainingnow'];
						
						$dbpaydebtprev = $db->fetch_one("SELECT SUM(remainingnowh) AS trn FROM headerpayment WHERE supplierid='".$suppdetail['supplierid']."' AND (startdate='".$prevmonthstart."' AND enddate='".$prevmonthend."') AND status = 2");
						$remainingfromprevious = $dbpaydebtprev['remainingnow'];
						
						$remainingfromprevious = $dbpaymentprev['trn'] - $dbpaydebtprev['trn'];
						
						if (empty($remainingfromprevious)){
							$remainingfromprevious = 0;
						}
						
						$completetrx = 0;
						
						/* check the sale and buy first */
						$totalpymt = 0;
						$totalpymtb = 0;
						/* get sale */
						$alldatas = $db->fetch_one("SELECT SUM(totalsale) AS totals FROM headersale WHERE trtype = 'credit' AND claims=0 AND saledate >= '".$startdate."' AND saledate <= '".$enddate."' AND customercode='".$dbgc['customercode']."'");
						$totalpymt += $alldatas['totals'];
						
						/* get return sale */
						$alldatas = $db->fetch_one("SELECT SUM(dsr.totalsalerad) AS totals FROM detailsaler dsr INNER JOIN detailsale ds ON dsr.dsid = ds.dsid INNER JOIN headersaler hsr ON dsr.salerid = hsr.salerid WHERE dsr.claims=0 AND dsr.salerdate >= '".$startdate."' AND dsr.salerdate <= '".$enddate."' AND hsr.customercode='".$dbgc['customercode']."'");
						$totalpymt -= $alldatas['totals'];
							
						/* get buy */
						$alldatas = $db->fetch_one("SELECT SUM(totalbuy) AS totals FROM headerbuy WHERE trtype = 'credit' AND claims=0 AND buydate >= '".$startdate."' AND buydate <= '".$enddate."' AND suppliercode='".$dbgc['customercode']."'");
						$totalpymtb += $alldatas['totals'];
									
						/* get return buy */
						$alldatas = $db->fetch_one("SELECT SUM(dbr.totalbuyrad) AS totals FROM detailbuyritem dbri INNER JOIN detailbuyr dbr ON dbri.dbrid = dbr.dbrid INNER JOIN detailbuy db ON dbri.dbid = db.dbid INNER JOIN headerbuyr hbr ON dbr.buyrid = hbr.buyrid WHERE dbr.claims=0 AND dbr.buyrdate >= '".$startdate."' AND dbr.buyrdate <= '".$enddate."' AND hbr.suppliercode='".$dbgc['customercode']."'");
						$totalpymtb -= $alldatas['totals'];
						
						$crossresult = $totalpymt - $totalpymtb;
						if ($saveto == 'paydebt'){
							$crossresult -= $thismonthgt;
						}
						else{
							$crossresult += $thismonthgt;
						}
						
						$crossresult -= $remainingfromprevious;
						
						/* get sale */
						$arrpost = $sale->getUnclaimSale($dbgc['customercode'],$startdate,$enddate);
						$arrpostsz = sizeof($arrpost);
						if ($arrpostsz > 0){
							if (!$headerscreated){
								$headerscreated = true;
								$lastid = $payment->saveHeaderPayment($suppdetail['supplierid'],$_POST['supplieraddrid'],$dbgc['customerid'],$_POST['customeraddrid'],$paymentdate,0,0,'','','','',0,'',0,0,0,'',0,0,0,0,$completetrx,0,$startdate,$enddate,'',0,0,$userid,1,0,0);
								$payment->setId($lastid);
							}
							foreach ($arrpost as $apst){
								if (!empty($apst['saleid'])){
									$payment->saveDetailPayment($apst['saleid'],$apst['totalsale'],$paymentdate,'','sale',$completetrx,$apst['saledate']);
									/* $totalpymt += $apst['totalsale']; */
								}
							}
						}
						
						/* get return sale */
						$arrpost = $saler->getUnclaimSaleR($dbgc['customercode'],$startdate,$enddate);
						$arrpostsz = sizeof($arrpost);
						if ($arrpostsz > 0){
							if (!$headerscreated){
								$headerscreated = true;
								$lastid = $payment->saveHeaderPayment($suppdetail['supplierid'],$_POST['supplieraddrid'],$dbgc['customerid'],$_POST['customeraddrid'],$paymentdate,0,0,'','','','',0,'',0,0,0,'',0,0,0,0,$completetrx,0,$startdate,$enddate,'',0,0,$userid,1,0,0);
								$payment->setId($lastid);
							}
							foreach ($arrpost as $apst){
								if (!empty($apst['dsrid'])){
									$payment->saveDetailPayment($apst['dsrid'],$apst['totalsalerad'],$paymentdate,'','return',$completetrx,0);
									/* $totalpymt -= $apst['totalsalerad']; */
								}
							}
						}
							
						/* get buy */
						$arrpostb = $purchase->getUnclaimBuy($dbgc['customercode'],$startdate,$enddate);
						$arrpostbsz = sizeof($arrpostb);
						$purchaseexist = false;
						if ($arrpostbsz > 0){
							if (!$headerscreated){
								$headerscreated = true;
								$lastid = $payment->saveHeaderPayment($suppdetail['supplierid'],$_POST['supplieraddrid'],$dbgc['customerid'],$_POST['customeraddrid'],$paymentdate,0,0,'','','','',0,'',0,0,0,'',0,0,0,0,$completetrx,0,$startdate,$enddate,'',0,0,$userid,1,0,0);
								$payment->setId($lastid);
							}
							foreach ($arrpostb as $apst){
								if (!empty($apst['buyid'])){					
									$payment->saveDetailPayment($apst['buyid'],$apst['totalbuy'],$paymentdate,'','buy',$completetrx,$apst['buydate']);
									/* $totalpymtb += $apst['totalbuy']; */
								}
							}
						}
									
						/* get return buy */
						$getbuyr = $purchaser->getUnclaimBuyR($dbgc['customercode'],$startdate,$enddate);
						if (sizeof($getbuyr) > 0){
							if (!$headerscreated){
								$headerscreated = true;
								$lastid = $payment->saveHeaderPayment($suppdetail['supplierid'],$_POST['supplieraddrid'],$dbgc['customerid'],$_POST['customeraddrid'],$paymentdate,0,0,'','','','',0,'',0,0,0,'',0,0,0,0,$completetrx,0,$startdate,$enddate,'',0,0,$userid,1,0,0);
								$payment->setId($lastid);
							}
							foreach ($getbuyr as $gsr){
								if (!empty($gsr['dbrid'])){
									$payment->saveDetailPayment($gsr['dbrid'],$gsr['totalbuyrad'],$paymentdate,'','returnby',$completetrx,0);
									/* $totalpymtb -= $gsr['totalbuyrad']; */
								}
							}
						}
							
						if ($crossresult >= 0){
							if ($saveto == 'paydebt'){
								$db->update("UPDATE headerpayment SET status = 1 WHERE hpid = '".$lastid."'");
							}
							
							if ($headerscreated){
								$pays = $crossresult - $remainingfromprevious;
								$payment->updateDebtCredit($dbgc['customercode'],$totalpymtb,$totalpymt,$completetrx);
								$db->query("UPDATE headerpayment SET remainingprevious=".$remainingfromprevious.", totalpayment=".$pays.", grandtotals=".$crossresult." WHERE hpid='".$lastid."'");
							}
						}
						else{
							$crossresult = abs($crossresult);
						
							if ($saveto == 'payment'){
								$db->update("UPDATE headerpayment SET status = 2 WHERE hpid = '".$lastid."'");
							}
															
							if ($headerscreated){
								if ($remainingfromprevious > 0){
									$remainingfromprevious = 0 - $remainingfromprevious;
								}
								else{
									$remainingfromprevious = abs($remainingfromprevious);
								}
								$pays = $crossresult - $remainingfromprevious;
								$payment->updateDebtCredit($dbgc['customercode'],$totalpymtb,$totalpymt,$completetrx);
								$db->query("UPDATE headerpayment SET remainingprevious=".$remainingfromprevious.", totalpayment=".$pays.", grandtotals=".$crossresult." WHERE hpid='".$lastid."'");
							}
						}
												
						if ($startingmonthwalk == $monthnows && $startingyearwalk == $yearnows){
							break;
						}
						
						if ($startingmonthwalk == 12){
							$startingmonthwalk = 1;
							$startingyearwalk++;
						}
						else{
							$startingmonthwalk++;
						}
					}
					while (true);
				}
			}
		}
		$db->endTransaction();
		
		$getlastpayment = $db->fetch_one("SELECT * FROM headerpayment ORDER BY enddate DESC LIMIT 1");
		if (!empty($getlastpayment['hpid'])){
			$redirectstartdate = date("d-m-Y",$getlastpayment['startdate']);
			$redirectenddate = date("d-m-Y",$getlastpayment['enddate']);
		}
		redirecting("payment.php?screen=list&startpaysdate=".$redirectstartdate."&endpaysdate=".$redirectenddate);
	}
	else if ($_POST['submits'] == 'Tambah' && $useraccess['add_payment']){
		$db->beginTransaction();
		$paymentdate = strtotime($_POST['paymentdate']);
		$startdate = strtotime($_POST['startdate']);
		$enddate = strtotime($_POST['enddate'].' 23:59:59');
		$completedate = strtotime($_POST['completedate']);
		
		
			$customer->setCode($_POST['customercode']);
			$getscode = $customer->getcustomerDetail('partial');
			$customer->setId($getscode['customerid']);
			if ($_POST['customeraddrid'] == '-1'){
				$getsaddr = $customer->getcustomeraddrdetail('all');
				if (sizeof($getsaddr) > 0){
					foreach ($getsaddr as $gsa){
						$_POST['customeraddrid'] = $gsa['detailcustid'];
						break;
					}
				}
				
				$supplier->setCode($_POST['customercode']);
				$getccode = $supplier->getsupplierDetail('partial');
				$supplier->setId($getccode['supplierid']);
				$getcaddr = $supplier->getsupplieraddrdetail('all');
				if (sizeof($getcaddr) > 0){
					foreach ($getcaddr as $gca){
						$_POST['supplieraddrid'] = $gca['detailsplid'];
						break;
					}
				}
			}
			else{
			$detailcustomerad = $db->fetch_one("SELECT * FROM detailcustomer where detailcustid = '".$_POST['customeraddrid']."' ");
			$detailsupplierad = $db->fetch_one("SELECT * FROM detailsupplier where address = '".$detailcustomerad['address']."' ");
			$_POST['supplieraddrid'] = $detailsupplierad['detailsplid'];
			}
			
			$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");
			
			$_POST['remainingprevioush'] =  togglenumber($_POST["remainingprevioush"],'calculate');
			$_POST['remainingprevious'] = togglenumber($_POST["remainingprevious"],'calculate');
			$_POST['flat'] = togglenumber($_POST["flat"],'calculate');
			
			$lastid = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$getscode['customerid'],$_POST['customeraddrid'],$paymentdate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$_POST['remainingprevious'],$_POST['remainingnow'],$_POST['complete'],$completedate,$startdate,$enddate,$_POST['description'],$_POST['totalpayment'],$_POST['grandtotals'],$userid,1,$_POST['remainingprevioush'],$_POST['remainingnowh'],$_POST['flat'],$_POST['statusflat']);
			
			$payment->setId($lastid);
			
			//save detail repayment
			$arrpostdel = explode(",",$_POST['detailrepaybox_rowsdeleted']);
			$arrpost = explode(",",$_POST['detailrepaybox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailrepaybox_".$arrpost[$x]."_0"])){
						$_POST["detailrepaybox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailrepaybox_".$arrpost[$x]."_6"],'calculate');
						$payment->saveDetailRePayment($_POST["detailrepaybox_".$arrpost[$x]."_0"],$_POST["detailrepaybox_".$arrpost[$x]."_1"],$_POST["detailrepaybox_".$arrpost[$x]."_2"],$_POST["detailrepaybox_".$arrpost[$x]."_3"],strtotime($_POST["detailrepaybox_".$arrpost[$x]."_4"]),strtotime($_POST["detailrepaybox_".$arrpost[$x]."_5"]),$_POST["detailrepaybox_".$arrpost[$x]."_6"],$_POST["detailrepaybox_".$arrpost[$x]."_8"],$_POST['complete']);
					}
				}
			}

			$arrpost = explode(",",$_POST['detailadded']);
			$arrpost = array_unique($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				$totalforbuy = 0;
				$totalforsale = 0;
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailpaybox_".$arrpost[$x]."_0"])){					
						$_POST["detailpaybox_".$arrpost[$x]."_4"] = togglenumber(abs($_POST["detailpaybox_".$arrpost[$x]."_4"]),'calculate');
						
						if (strstr($arrpost[$x],'return_')){
							$saveids = str_replace("return_","",$arrpost[$x]);
							$types = 'return';
							$totalforsale -= $_POST["detailpaybox_".$arrpost[$x]."_4"];
						}
						else if (strstr($arrpost[$x],'sale_')){
							$saveids = str_replace("sale_","",$arrpost[$x]);
							$types = 'sale';
							$totalforsale += $_POST["detailpaybox_".$arrpost[$x]."_4"];
						}
						else if (strstr($arrpost[$x],'returnby_')){
							$saveids = str_replace("returnby_","",$arrpost[$x]);
							$types = 'returnby';
							$totalforbuy -= $_POST["detailpaybox_".$arrpost[$x]."_4"];
						}
						else if (strstr($arrpost[$x],'buy_')){
							$saveids = str_replace("buy_","",$arrpost[$x]);
							$types = 'buy';
							$totalforbuy += $_POST["detailpaybox_".$arrpost[$x]."_4"];
						}
						
						$payment->saveDetailPayment($saveids,$_POST["detailpaybox_".$arrpost[$x]."_4"],$paymentdate,$_POST["detailpaybox_".$arrpost[$x]."_5"],$types,$_POST['complete'],$completedate);
					}
				}
				
				$payment->updateDebtCredit($getscode['customercode'],$totalforbuy,$totalforsale,$_POST['complete']);
			}
			$db->endTransaction();
			redirecting("payment.php?no=".$lastid);
		
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_payment']){
		if (!empty($_POST['id'])){
			$payment->setId($_POST['id']);
			
			
			
			$db->beginTransaction();
			$paymentdate = strtotime($_POST['paymentdate']);
			$startdate = strtotime($_POST['startdate']);
			$enddate = strtotime($_POST['enddate'].' 23:59:59');
			$completedate = strtotime($_POST['completedate']);
			
			$customer->setCode($_POST['customercode']);
			$getscode = $customer->getcustomerDetail('partial');
			$customer->setId($getscode['customerid']);
			if ($_POST['customeraddrid'] == '-1'){
				$getcaddr = $customer->getcustomeraddrdetail('all');
				if (sizeof($getcaddr) > 0){
					foreach ($getcaddr as $gca){
						$_POST['customeraddrid'] = $gca['detailcustid'];
						break;
					}
				}
			}
			$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");
			
			$_POST['remainingprevioush'] =  togglenumber($_POST["remainingprevioush"],'calculate');
			$_POST['remainingprevious'] = togglenumber($_POST["remainingprevious"],'calculate');
			
			/* for detail repayment */
			$arrpostt = explode(",",$_POST['detailrpid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailrepaybox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$payment->setDetailRePayId($arrpostdel[$x]);
						$getdetailrepay = $payment->getDetailRePaymentIndv();
						if (!empty($getdetailrepay['drpyid'])){
							$payment->deleteDetailRePayment();
						}
					}
				}
			}
			
			//edited rows
			$arrpost = array_diff($arrpostt,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailrepaybox_".$arrpost[$x]."_0"])){
						$payment->setDetailRePayId($arrpost[$x]);
						$_POST["detailrepaybox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailrepaybox_".$arrpost[$x]."_6"],'calculate');
						$payment->updateDetailRePayment($_POST["detailrepaybox_".$arrpost[$x]."_0"],$_POST["detailrepaybox_".$arrpost[$x]."_1"],$_POST["detailrepaybox_".$arrpost[$x]."_2"],$_POST["detailrepaybox_".$arrpost[$x]."_3"],strtotime($_POST["detailrepaybox_".$arrpost[$x]."_4"]),strtotime($_POST["detailrepaybox_".$arrpost[$x]."_5"]),$_POST["detailrepaybox_".$arrpost[$x]."_6"],$_POST["detailrepaybox_".$arrpost[$x]."_8"],$_POST['complete']);
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailrepaybox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailrepaybox_".$arrpost[$x]."_0"])){
						$_POST["detailrepaybox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailrepaybox_".$arrpost[$x]."_6"],'calculate');
						$payment->saveDetailRePayment($_POST["detailrepaybox_".$arrpost[$x]."_0"],$_POST["detailrepaybox_".$arrpost[$x]."_1"],$_POST["detailrepaybox_".$arrpost[$x]."_2"],$_POST["detailrepaybox_".$arrpost[$x]."_3"],strtotime($_POST["detailrepaybox_".$arrpost[$x]."_4"]),strtotime($_POST["detailrepaybox_".$arrpost[$x]."_5"]),$_POST["detailrepaybox_".$arrpost[$x]."_6"],$_POST["detailrepaybox_".$arrpost[$x]."_8"],$_POST['complete']);
					}
				}
			}
			
			/* for detail payment */
			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailpaybox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){						
						if (strstr($arrpostdel[$x],'return_')){
							$delids = str_replace("return_","",$arrpostdel[$x]);
							$types = 'return';
						}
						else if (strstr($arrpostdel[$x],'sale_')){
							$delids = str_replace("sale_","",$arrpostdel[$x]);
							$types = 'sale';
						}
						else if (strstr($arrpostdel[$x],'returnby_')){
							$delids = str_replace("returnby_","",$arrpostdel[$x]);
							$types = 'returnby';
						}
						else if (strstr($arrpostdel[$x],'buy_')){
							$delids = str_replace("buy_","",$arrpostdel[$x]);
							$types = 'buy';
						}
						
						$dbpm = $payment->getDetailPaymentFromSale($delids,$types);
						$payment->setDetailId($dbpm['dpid']);
						$payment->deleteDetailPayment();
					}
				}
			}
			
			//edited rows
			$arrpost = array_diff($arrpostt,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailpaybox_".$arrpost[$x]."_0"])){
						if (strstr($arrpost[$x],'return_')){
							$editids = str_replace("return_","",$arrpost[$x]);
							$types = 'return';
						}
						else if (strstr($arrpost[$x],'sale_')){
							$editids = str_replace("sale_","",$arrpost[$x]);
							$types = 'sale';
						}
						else if (strstr($arrpost[$x],'returnby_')){
							$editids = str_replace("returnby_","",$arrpost[$x]);
							$types = 'returnby';
						}
						else if (strstr($arrpost[$x],'buy_')){
							$editids = str_replace("buy_","",$arrpost[$x]);
							$types = 'buy';
						}
						
						$olddetail = $payment->getDetailPaymentFromSale($editids,$types);
						$payment->setDetailId($olddetail['dpid']);
						
						$_POST["detailpaybox_".$arrpost[$x]."_4"] = togglenumber(abs($_POST["detailpaybox_".$arrpost[$x]."_4"]),'calculate');
						
						$payment->updateDetailPayment($editids,$_POST["detailpaybox_".$arrpost[$x]."_4"],$paymentdate,$_POST["detailpaybox_".$arrpost[$x]."_5"],$types,$_POST['complete'],$completedate,$olddetail);
					}
					/* else{
						if (strstr($arrpost[$x],'return_')){
							$delids = str_replace("return_","",$arrpost[$x]);
							$types = 'return';
						}
						else if (strstr($arrpost[$x],'sale_')){
							$delids = str_replace("sale_","",$arrpost[$x]);
							$types = 'sale';
						}
						else if (strstr($arrpost[$x],'returnby_')){
							$delids = str_replace("returnby_","",$arrpost[$x]);
							$types = 'returnby';
						}
						else if (strstr($arrpost[$x],'buy_')){
							$delids = str_replace("buy_","",$arrpost[$x]);
							$types = 'buy';
						}
						
						$dbpm = $payment->getDetailPaymentFromSale($delids,$types);
						$payment->setDetailId($dbpm['dpid']);
						$payment->deleteDetailPayment();
					} */
				}
			}
			
			//added rows
			$arrpost = explode(",",$_POST['detailadded']);
			$arrpost = array_unique($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailpaybox_".$arrpost[$x]."_0"])){
						$_POST["detailpaybox_".$arrpost[$x]."_4"] = togglenumber(abs($_POST["detailpaybox_".$arrpost[$x]."_4"]),'calculate');
						
						if (strstr($arrpost[$x],'return_')){
							$saveids = str_replace("return_","",$arrpost[$x]);
							$types = 'return';
						}
						else if (strstr($arrpost[$x],'sale_')){
							$saveids = str_replace("sale_","",$arrpost[$x]);
							$types = 'sale';
						}
						else if (strstr($arrpost[$x],'returnby_')){
							$saveids = str_replace("returnby_","",$arrpost[$x]);
							$types = 'returnby';
						}
						else if (strstr($arrpost[$x],'buy_')){
							$saveids = str_replace("buy_","",$arrpost[$x]);
							$types = 'buy';
						}
						
						$payment->saveDetailPayment($saveids,$_POST["detailpaybox_".$arrpost[$x]."_4"],$paymentdate,$_POST["detailpaybox_".$arrpost[$x]."_5"],$types,$_POST['complete'],$completedate);
					}
				}
			}
			
			/* if ($_POST['saveto'] == 'paydebt'){
				$lastid = $paydebt->saveHeaderPayDebt(0,0,$getscode['customerid'],$_POST['customeraddrid'],$paymentdate,0,0,'','','','',0,'',0,0,0,'',0,0,$_POST['remainingprevious'],$_POST['remainingnow'],$completetrx,$completedate,$startdate,$enddate,$_POST['description'],abs($_POST['totalpayment']),$_POST['grandtotals'],$userid);
				
				$paydebt->setId($lastid);
				
				$allrepayment = $payment->getDetailRePayment();
				if (sizeof($allrepayment) > 0){
					foreach ($allrepayment as $adppym){
						$paydebt->saveDetailRePayDebt($adppym['types'],$adppym['bank'],$adppym['accname'],$adppym['accnumber'],$adppym['dates'],$adppym['duedates'],$adppym['totals'],$adppym['notes'],$adppym['status']);
					}
				}
				
				$alldetailpayment = $payment->getDetailPayment();
				if (sizeof($alldetailpayment) > 0){
					foreach ($alldetailpayment as $adppym){
						if ($adppym['types'] == 'return'){
							$adppym['types'] = 'returnsl';
						}
						else if ($adppym['types'] == 'returnby'){
							$adppym['types'] = 'return';
						}
						$paydebt->saveDetailPayDebt($adppym['hsid'],$adppym['pays'],$paymentdate,'',$adppym['types'],0,0);
					}
				}
				$payment->deletePayment();
			} */
			$checkdebt['hpid'] = $_POST['id'] ;
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlpayment = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
			
			$_POST['flat'] = togglenumber($_POST["flat"],'calculate');
			
			$oldheader = $payment->getHeaderPayment();
			$payment->updateHeaderPayment($getscode['customerid'],$_POST['customeraddrid'],$paymentdate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$_POST['remainingprevious'],$_POST['remainingnow'],$_POST['complete'],$completedate,$startdate,$enddate,$_POST['description'],$_POST['totalpayment'],$_POST['grandtotals'],$oldheader,$userid,$_POST['remainingprevioush'],$_POST['flat'],$_POST['statusflat']);
			
			
			$customer->setId("");
			$payment->updateDebtCreditEdit($getscode['customerid'],$getscode['customercode'],$_POST['complete'],$oldheader);
			
			
			
			
			$nextmonth = $payment->getDetailNextPaymentByMonth($startdate,2,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			//echo $nextmonth['hpid'];
			
				if (!empty($nextmonth['hpid'])){
				$payment->setId($nextmonth['hpid']);
				//echo $nextmonth['hpid'];
				
				
				
				$payment->setId($nextmonth['hpid']);
				
				$otherheader = $payment->getHeaderPayment();

				
				
				if ($otherheader['status'] == 1){
				
				$checknulfremainingprevioush = explode(".",$_POST['remainingnowh']);
				$checknulfremainingprevious = explode(".",$_POST['remainingnow']);
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$nextmonth['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$nextmonth['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$nextmonth['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$nextmonth['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				
				$grandtotals = 0;

				$totalpayment = $ttlfolpaym;
				$remainingnow = $otherheader['remainingnow'];
				$remainingnowh = $otherheader['remainingnowh'];
				
				
				if (!empty($checknulfremainingprevious[0])){

				
				$grandtotals = $totalpayment ;
				$newvalue = $totalpayment-$_POST['remainingnow'];
				
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnow = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnow = 0;
				}
				
				
				}
				else{
				$grandtotals = $totalpayment+$_POST['remainingnowh'];
				}
				
				//$grandtotals = headerpayment['totalpayment']
				//$totalpayment = headerpayment['grandtotals']
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
			
				
				}
				
				else{
				
				$payment->setId($nextmonth['hpid']);
				$otherheader = $payment->getHeaderPayment();
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$nextmonth['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$nextmonth['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$nextmonth['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$nextmonth['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				$totalpayment = abs($ttlfolpaym);
				$remainingnow = $otherheader['remainingnow'];
				$remainingnowh = $otherheader['remainingnowh'];
				
				
				
				$checknulfremainingprevioush = explode(".",$_POST['remainingnowh']);
				$checknulfremainingprevious = explode(".",$_POST['remainingnow']);
				
				if (!empty($checknulfremainingprevious[0])){
				$grandtotals = $totalpayment + $_POST['remainingnow'];
				
				}
				else{
				$newvalue = $totalpayment-$_POST['remainingnowh'];
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnowh = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnowh = 0;
				}
				}
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
				
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$otherheader['complete'],$otherheader);
				}
				
				$db->query("UPDATE headerpayment SET remainingprevious='".$_POST['remainingnow']."',remainingprevioush='".$_POST['remainingnowh']."' WHERE hpid='".$nextmonth['hpid']."'");
			
			}
			
			
			
			
			$db->endTransaction();
		}
		if ($_POST['saveto'] == 'paydebt'){
			redirecting("paydebt.php?no=".$lastid);
		}
		else{
			redirecting("payment.php?no=".$_POST['id']);
		}
	}

	if ($_GET['do'] == 'delete' && !empty($paymentno) && $useraccess['delete_payment']){
		$db->beginTransaction();
		
		$payment->setId($paymentno);
		$payment->deletePayment();
		
		$db->endTransaction();
		redirecting("payment.php?screen=list");		
	}

	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_payment'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'paymentlist';
	}
	else{
		
		if (empty($useraccess['add_payment']) && empty($useraccess['edit_payment'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'payment';
		if (!empty($paymentno)){
			$payment->setId($paymentno);
			$headerpayment = $payment->getHeaderPayment();
			
			if (empty($headerpayment['hpid'])){
				redirecting('payment.php?screen=list');
			}
			
			$invoicedate = date("d-m-Y",$headerpayment['paymentdate']);
			$invstartdate = date("d-m-Y",$headerpayment['startdate']);
			$invenddate = date("d-m-Y",$headerpayment['enddate']);
			
			$customer->setId($headerpayment['customerid']);
			$customer->setDetailId($headerpayment['customeraddrid']);
			$getcustomer = $customer->getcustomerDetail();
			$area->setCode($getcustomer['areacode']);
			$dbarea = $area->getareaDetail();
			if (!empty($dbarea['areaname'])){
				$areaname = ' '.$dbarea['areaname'];
			}
			$customercperson = htmlspecialchars($getcustomer['contactperson']);
			$customername = htmlspecialchars($getcustomer['customername']);
			$customeraddr = htmlspecialchars($getcustomer['address'].$areaname);
			$customertelp = htmlspecialchars($getcustomer['phone']);
						
			$alldtl = $payment->getDetailPayment();
			$alldetailid = '';
						
			$alldtlrp = $payment->getDetailRePayment();
			$alldetailrpid = '';
			if (sizeof($alldtlrp) > 0){
				foreach ($alldtlrp as $aad){
					$alldetailrpid .= ',r-'.$aad['drpyid'];
				}
				$alldetailrpid = substr($alldetailrpid,1);
			}
			
			$subtotalfk = 0;
			$temporarytotal = 0;
			if (sizeof($alldtl) > 0){
				foreach ($alldtl as $aad){
					$alldetailid .= ','.$aad['types'].'_'.$aad['hsid'];
					if ($statususer == 1){
						if ($aad['types'] == 'return'){
							$saler->setDetailId($aad['hsid']);
							$detailheadersr = $saler->getDetailSaleRIndv();
								
							$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
							$totalsalerfk = $detailheadersr['quantityf'] * $detailheadersr['salerprice'];
							$totaldiscfk = $detailheadersr['disc'] / 100 * $totalsalerfk;
							$aad['pays'] = $totalsalerfk - $totaldiscfk;
							$temporarytotal -= $aad['pays'];
						}
						else if ($aad['types'] == 'sale'){
							$sale->setId($aad['hsid']);
							$detailheaders = $sale->getHeaderSale();
									
							$aad['pays'] = 0;
							$sale->setSaleNo($detailheaders['saleno']);
							$alldetailp = $sale->getDetailSale();
							if (sizeof($alldetailp) > 0){
								foreach ($alldetailp as $adp){
									$adp['quantityf'] = discq($adp['quantityf']);
									$totalsalefk = $adp['quantityf'] * $adp['saleprice'];
									$totaldiscfk = $adp['disc'] / 100 * $totalsalefk;
									$aad['pays'] += $totalsalefk - $totaldiscfk;
								}
								$totalgdiscfk = $detailheaders['disc'] / 100 * $aad['pays'];
								$totalafgdiscfk = $aad['pays'] - $totalgdiscfk;
								$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
								$aad['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
								
								$temporarytotal += $aad['pays'];
							}
						}
						else if ($aad['types'] == 'returnby'){
							$purchaser->setDetailId($aad['hsid']);
							$detailheadersr = $purchaser->getDetailBuyRIndv();
								
							$detailheadersr['quantityf'] = discq($detailheadersr['quantityf']);
							$totalbuyfk = $detailheadersr['quantityf'] * $detailheadersr['buyrprice'];
							$totaldiscfk = $detailheadersr['disc'] / 100 * $totalbuyfk;
							$aad['pays'] = $totalbuyfk - $totaldiscfk;
							$temporarytotal += $aad['pays'];
						}
						else if ($aad['types'] == 'buy'){
							$purchase->setId($aad['hsid']);
							$detailheaders = $purchase->getHeaderBuy();
									
							$aad['pays'] = 0;
							$purchase->setBuyNo($detailheaders['buyno']);
							$alldetailp = $purchase->getDetailBuy();
							if (sizeof($alldetailp) > 0){
								foreach ($alldetailp as $adp){
									$adp['quantityf'] = discq($adp['quantityf']);
									$totalbuyfk = $adp['quantityf'] * $adp['buyprice'];
									$totaldiscfk = $adp['disc'] / 100 * $totalbuyfk;
									$aad['pays'] += $totalbuyfk - $totaldiscfk;
								}
								$totalgdiscfk = $detailheaders['disc'] / 100 * $aad['pays'];
								$totalafgdiscfk = $aad['pays'] - $totalgdiscfk;
								$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
								$aad['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
								
								$temporarytotal -= $aad['pays'];
							}
						}
					}
				}
				$alldetailid = substr($alldetailid,1);
			}
			
			if ($statususer == 1){
				$headerpayment['totalpayment'] = $temporarytotal;
			}
			
			if ($headerpayment['complete'] == 1){
				$fcompletedate = date("d-m-Y",$headerpayment['completedate']);
			}
			else{
				$fcompletedate = '';
			}
			
			if (!empty($headerpayment['chequedates'])){
				$fchequedates = date("d-m-Y",$headerpayment['chequedates']);
			}
			if (!empty($headerpayment['chequeduedate'])){
				$fchequeduedates = date("d-m-Y",$headerpayment['chequeduedate']);
			}
			if (!empty($headerpayment['girodates'])){
				$fgirodates = date("d-m-Y",$headerpayment['girodates']);
			}
			if (!empty($headerpayment['giroduedate'])){
				$fgiroduedates = date("d-m-Y",$headerpayment['giroduedate']);
			}
			
			$ik = 0;
			$arrik = array(0,0,0,0);
			if ($headerpayment['cash'] != 0){
				$ik++;
				$arrik[0] = 1;
				$fcash = number_format($headerpayment['cash'],0,",",".");
			}
			if ($headerpayment['transfer'] != 0){
				$ik++;
				$arrik[1] = 1;
				$ftransfer = number_format($headerpayment['transfer'],0,",",".");
			}
			if ($headerpayment['cheque'] != 0){
				$ik++;
				$arrik[2] = 1;
				$fcheque = number_format($headerpayment['cheque'],0,",",".");
			}
			if ($headerpayment['giro'] != 0){
				$ik++;
				$arrik[3] = 1;
				$fgiro = number_format($headerpayment['giro'],0,",",".");
			}
			
			if ($statususer == 1 && $ik > 0){
				$blastit = floor($temporarytotal / $ik);
				$blastcounter = 0;
				for ($iy = 0; $iy < sizeof($arrik); $iy++){
					if ($iy == ($ik-1)){
						$blasttext = $temporarytotal - $blastcounter;
					}
					else{
						$blasttext = $blastit;
						$blastcounter += $blastit;
					}
					switch ($iy){
						case 0 :
							if ($arrik[$iy] == 1){
								$fcash = number_format($blasttext,0,",",".");
							}
							break;
						case 1 :
							if ($arrik[$iy] == 1){
								$ftransfer = number_format($blasttext,0,",",".");
							}
							break;
						case 2 :
							if ($arrik[$iy] == 1){
								$fcheque = number_format($blasttext,0,",",".");
							}
							break;
						case 3 :
							if ($arrik[$iy] == 1){
								$fgiro = number_format($blasttext,0,",",".");
							}
							break;
					}
				}
			}
			
			/*if ($headerpayment['remainingprevious'] == 0){
				$headerpayment['remainingprevious'] = $getcustomer['remainingcredit'];
			}*/
			//$headerpayment['grandtotals'] = $headerpayment['totalpayment'] - $headerpayment['remainingprevious'];
			
			$checknulfremainingprevioush = explode(".",$headerpayment['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$headerpayment['remainingprevious']);
			
			$fremainingprevious = number_format($headerpayment['remainingprevious'],2,",",".");
			$fremainingprevioush = number_format($headerpayment['remainingprevioush'],2,",",".");
			
			
			$checknulfremaining = explode(".",$headerpayment['remainingnow']);
			$checknulfremainingh = explode(".",$headerpayment['remainingnowh']);
	
			$fremainingnow = number_format($headerpayment['remainingnow'],2,",",".");
			$fremainingnowh = number_format($headerpayment['remainingnowh'],2,",",".");
			

			$ftotal = number_format($headerpayment['totalpayment'],2,",",".");
			if ($statususer == 1){
				$fgrandtotals = number_format($headerpayment['totalpayment'],2,",",".");
			}
			else{
				$fgrandtotals = number_format($headerpayment['grandtotals'],2,",",".");
			}
			$hflat= number_format($headerpayment['flat'],2,",",".");
			$headerpayment = array_map("htmlspecialchars",$headerpayment);
			
			$ttlpay = $db->fetch_one("SELECT SUM(totals) AS alltotals FROM detailrepayment WHERE hpid='".$headerpayment['hpid']."'");
			
		}
		else{
			$invoicedate = date("d-m-Y");
			$invstartdate = '01-'.date("m-Y");
			$invenddate = date('t-m-Y');
			$ttlpay['alltotals']=0;
		}
	}

	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>