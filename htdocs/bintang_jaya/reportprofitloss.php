<?php
	require_once "global.php";
	
	require_once "class/customer.php";
	require_once "class/supplier.php";
	require_once "class/purchase.php";
	require_once "class/Sale.php";
	require_once "class/PurchaseR.php";
	require_once "class/SaleR.php";
	require_once "class/AdjustIn.php";
	require_once "class/AdjustOut.php";
	$customer = new customer();
	$supplier = new supplier();
	$purchase = new Purchase();
	$sale = new Sale();
	$purchaser = new PurchaseR();
	$saler = new SaleR();
	$ain = new AdjustIn();
	$aout = new AdjustOut();
	
	$printdate = date("d-M-Y / H:i:s");
	if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
		$printtemplate = 'reportprofitloss';
		$startdate = strtotime($_POST['datestart']);
		$enddate = strtotime($_POST['dateend'].' 23:59:59');
		
		$dbtrx = array();
		//get purchase
		/*if ($statususer == 1){
			$getalltr = $db->fetch_one("SELECT SUM(totalbuy) AS totalbuys FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."') ORDER BY buydate");
			$totaltransaction = $getalltr['totalbuys'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."') ORDER BY totalbuy");
			$arrhbid = array();
			if (sizeof($getalltr) > 0){
				$tempforsets = 0;
				foreach ($getalltr as $gatr){
					$tempforsets += $gatr['totalbuy'];
					if ($tempforsets > $getsets){
						break;
					}
					array_push($arrhbid,$gatr['buyid']);
				}
				if (sizeof($arrhbid) > 0){
					$dbpurchase = $db->fetch_all("SELECT * FROM headerbuy WHERE buyid IN (".implode(",",$arrhbid).")".$sql." ORDER BY buydate");
				}
			}
		}
		else{*/
			$dbpurchase = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."') ORDER BY buydate");
		//}
		if (sizeof($dbpurchase) > 0){
			foreach ($dbpurchase as $dbp){					
				if ($statususer == 1){
					//get detail purchase
					$purchase->setBuyNo($dbp['buyno']);
					$dbdetailbuy = $purchase->getDetailBuy();
					if (sizeof($dbdetailbuy) > 0){
						$subtotalfk = 0;
						foreach ($dbdetailbuy as $dbds){
							$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
							if ($dbds['quantityf'] < 1){
								$dbds['quantityf'] = 1;
							}
							
							$totalbuyfk = $dbds['quantityf'] * $dbds['buyprice'];
							$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
							$dbds['totalbuyad'] = ($totalbuyfk - $totaldiscfk);
							$subtotalfk += $dbds['totalbuyad'];
						}
					}
				
					$discvalue = $dbp['disc'] / 100 * $subtotalfk;
					$totalafgdiscfk = $subtotalfk - $discvalue;
					$taxvalue = $dbp['tax'] / 100 * $totalafgdiscfk;
					$dbp['totalbuy'] = $totalafgdiscfk + $taxvalue;
				}
				
				$supplier->setCode($dbp['suppliercode']);
				$getname = $supplier->getsupplierDetail();
				$dbdetail['invoiceno'] = $dbp['orderno'];
				$dbdetail['date'] = $dbp['buydate'];
				$dbdetail['scname'] = $getname['suppliername'];
				$dbdetail['transaction'] = 'Pembelian';
				$dbdetail['debet'] = 0;
				$dbdetail['credit'] = $dbp['totalbuy'];
				$dbtrx[] = $dbdetail;
			}
		}
		
		//get sale
		/*if ($statususer == 1){
			$getalltr = $db->fetch_one("SELECT SUM(totalsale) AS totalsales FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."') ORDER BY saledate");
			$totaltransaction = $getalltr['totalsales'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."') ORDER BY totalsale");
			$arrhbid = array();
			if (sizeof($getalltr) > 0){
				$tempforsets = 0;
				foreach ($getalltr as $gatr){
					$tempforsets += $gatr['totalsale'];
					if ($tempforsets > $getsets){
						break;
					}
					array_push($arrhbid,$gatr['saleid']);
				}
				if (sizeof($arrhbid) > 0){
					$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE saleid IN (".implode(",",$arrhbid).")".$sql." ORDER BY saledate");
				}
			}
		}
		else{*/
			$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."') ORDER BY saledate");
		//}
		if (sizeof($dbsale) > 0){
			foreach ($dbsale as $dbp){
				if ($statususer == 1){
					//get detail sale
					$sale->setSaleNo($dbp['saleno']);
					$dbdetailsale = $sale->getDetailSale();
					if (sizeof($dbdetailsale) > 0){
						$subtotalfk = 0;
						foreach ($dbdetailsale as $dbds){
							$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
							if ($dbds['quantityf'] < 1){
								$dbds['quantityf'] = 1;
							}
							
							$totalbuyfk = $dbds['quantityf'] * $dbds['saleprice'];
							$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
							$dbds['totalsalead'] = ($totalbuyfk - $totaldiscfk);
							$subtotalfk += $dbds['totalsalead'];
						}
					}
				
					$discvalue = $dbp['disc'] / 100 * $subtotalfk;
					$totalafgdiscfk = $subtotalfk - $discvalue;
					$taxvalue = $dbp['tax'] / 100 * $totalafgdiscfk;
					$dbp['totalsale'] = $totalafgdiscfk + $taxvalue;
				}
				$customer->setCode($dbp['customercode']);
				$getname = $customer->getcustomerDetail();
				$dbdetail['invoiceno'] = $dbp['saleno'];
				$dbdetail['date'] = $dbp['saledate'];
				$dbdetail['scname'] = $getname['customername'];
				$dbdetail['transaction'] = 'Penjualan';
				$dbdetail['debet'] = $dbp['totalsale'];
				$dbdetail['credit'] = 0;
				$dbtrx[] = $dbdetail;
			}
		}
		
		//get purchase return
		/*if ($statususer == 1){
			$getalltr = $db->fetch_one("SELECT SUM(totalbuyr) AS totalbuyrs FROM headerbuyr WHERE (buyrdate >= '".$startdate."' AND buyrdate <= '".$enddate."') ORDER BY buyrdate");
			$totaltransaction = $getalltr['totalbuyrs'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT * FROM headerbuyr WHERE (buyrdate >= '".$startdate."' AND buyrdate <= '".$enddate."') ORDER BY totalbuyr");
			$arrhbid = array();
			if (sizeof($getalltr) > 0){
				$tempforsets = 0;
				foreach ($getalltr as $gatr){
					$tempforsets += $gatr['totalbuyr'];
					if ($tempforsets > $getsets){
						break;
					}
					array_push($arrhbid,$gatr['buyrid']);
				}
				if (sizeof($arrhbid) > 0){
					$dbpurchaser = $db->fetch_all("SELECT * FROM headerbuyr WHERE buyrid IN (".implode(",",$arrhbid).")".$sql." ORDER BY buyrdate");
				}
			}
		}
		else{*/
			$dbpurchaser = $db->fetch_all("SELECT * FROM headerbuyr WHERE (buyrdate >= '".$startdate."' AND buyrdate <= '".$enddate."') ORDER BY buyrdate");
		//}
		if (sizeof($dbpurchaser) > 0){
			foreach ($dbpurchaser as $dbp){
				if ($statususer == 1){
					//get detail buy return
					$purchaser->setId($dbp['buyrid']);
					$dbdetailbuyr = $purchaser->getDetailBuyR();
					if (sizeof($dbdetailbuyr) > 0){
						$subtotalfk = 0;
						foreach ($dbdetailbuyr as $dbds){
							$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
							if ($dbds['quantityf'] < 1){
								$dbds['quantityf'] = 1;
							}
							
							$totalbuyfk = $dbds['quantityf'] * $dbds['buyrprice'];
							$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
							$tempstd = $totalbuyfk - $totaldiscfk;
							$tempstd = $tempstd - $dbds['extdisc'] / 100 * $tempstd;
							$tempstd = $tempstd + $dbds['tax'] / 100 * $tempstd;
							$dbds['totalbuyrad'] = $tempstd;
							$subtotalfk += $dbds['totalbuyrad'];
						}
					}
				
					$discvalue = $dbp['disc'] / 100 * $subtotalfk;
					$totalafgdiscfk = $subtotalfk - $discvalue;
					$taxvalue = $dbp['tax'] / 100 * $totalafgdiscfk;
					$dbp['totalbuyr'] = $totalafgdiscfk + $taxvalue;
				}
				$supplier->setCode($dbp['suppliercode']);
				$getname = $supplier->getsupplierDetail();
				$dbdetail['invoiceno'] = $dbp['buyrid'];
				$dbdetail['date'] = $dbp['buyrdate'];
				$dbdetail['scname'] = $getname['suppliername'];
				$dbdetail['transaction'] = 'Retur Pembelian';
				$dbdetail['debet'] = $dbp['totalbuyr'];
				$dbdetail['credit'] = 0;
				$dbtrx[] = $dbdetail;
			}
		}
		
		//get sale return
		/*if ($statususer == 1){
			$getalltr = $db->fetch_one("SELECT SUM(totalsaler) AS totalsalers FROM headersaler WHERE (salerdate >= '".$startdate."' AND salerdate <= '".$enddate."') ORDER BY salerdate");
			$totaltransaction = $getalltr['totalsalers'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT * FROM headersaler WHERE (salerdate >= '".$startdate."' AND salerdate <= '".$enddate."') ORDER BY totalsaler");
			$arrhbid = array();
			if (sizeof($getalltr) > 0){
				$tempforsets = 0;
				foreach ($getalltr as $gatr){
					$tempforsets += $gatr['totalsaler'];
					if ($tempforsets > $getsets){
						break;
					}
					array_push($arrhbid,$gatr['salerid']);
				}
				if (sizeof($arrhbid) > 0){
					$dbsaler = $db->fetch_all("SELECT * FROM headersaler WHERE salerid IN (".implode(",",$arrhbid).")".$sql." ORDER BY salerdate");
				}
			}
		}
		else{*/
			$dbsaler = $db->fetch_all("SELECT * FROM headersaler WHERE (salerdate >= '".$startdate."' AND salerdate <= '".$enddate."') ORDER BY salerdate");
		//}
		if (sizeof($dbsaler) > 0){
			foreach ($dbsaler as $dbp){
				if ($statususer == 1){
					//get detail sale return
					$saler->setId($dbp['salerid']);
					$dbdetailsaler = $saler->getDetailSaleR();
					if (sizeof($dbdetailsaler) > 0){
						$subtotalfk = 0;
						foreach ($dbdetailsaler as $dbds){
							$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
							if ($dbds['quantityf'] < 1){
								$dbds['quantityf'] = 1;
							}
							
							$totalbuyfk = $dbds['quantityf'] * $dbds['salerprice'];
							$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
							$tempstd = $totalbuyfk - $totaldiscfk;
							$tempstd = $tempstd - $dbds['extdisc'] / 100 * $tempstd;
							$tempstd = $tempstd + $dbds['tax'] / 100 * $tempstd;
							$dbds['totalbuyrad'] = $tempstd;
							$dbds['totalsalerad'] = $tempstd;
							$subtotalfk += $dbds['totalsalerad'];
						}
					}
				
					$discvalue = $dbp['disc'] / 100 * $subtotalfk;
					$totalafgdiscfk = $subtotalfk - $discvalue;
					$taxvalue = $dbp['tax'] / 100 * $totalafgdiscfk;
					$dbp['totalsaler'] = $totalafgdiscfk + $taxvalue;
				}
				$customer->setCode($dbp['customercode']);
				$getname = $customer->getcustomerDetail();
				$dbdetail['invoiceno'] = $dbp['salerid'];
				$dbdetail['date'] = $dbp['salerdate'];
				$dbdetail['scname'] = $getname['customername'];
				$dbdetail['transaction'] = 'Retur Penjualan';
				$dbdetail['debet'] = 0;
				$dbdetail['credit'] = $dbp['totalsaler'];
				$dbtrx[] = $dbdetail;
			}
		}
		
		//get adjust in
		/*if ($statususer == 1){
			$getalltr = $db->fetch_one("SELECT SUM(totalain) AS totalains FROM headeradjustin WHERE (aindate >= '".$startdate."' AND aindate <= '".$enddate."') ORDER BY aindate");
			$totaltransaction = $getalltr['totalains'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT * FROM headeradjustin WHERE (aindate >= '".$startdate."' AND aindate <= '".$enddate."') ORDER BY totalain");
			$arrhbid = array();
			if (sizeof($getalltr) > 0){
				$tempforsets = 0;
				foreach ($getalltr as $gatr){
					$tempforsets += $gatr['totalain'];
					if ($tempforsets > $getsets){
						break;
					}
					array_push($arrhbid,$gatr['ainid']);
				}
				if (sizeof($arrhbid) > 0){
					$dbain = $db->fetch_all("SELECT * FROM headeradjustin WHERE ainid IN (".implode(",",$arrhbid).")".$sql." ORDER BY aindate");
				}
			}
		}
		else{*/
			$dbain = $db->fetch_all("SELECT * FROM headeradjustin WHERE (aindate >= '".$startdate."' AND aindate <= '".$enddate."') ORDER BY aindate");
		//}
		if (sizeof($dbain) > 0){
			foreach ($dbain as $dbp){
				if ($statususer == 1){
					//get detail adjust in
					$ain->setId($dbp['ainid']);
					$dbdetailain = $ain->getDetailAdjustIn();
					if (sizeof($dbdetailain) > 0){
						$subtotalfk = 0;
						foreach ($dbdetailain as $dbds){
							$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
							if ($dbds['quantityf'] < 1){
								$dbds['quantityf'] = 1;
							}
							
							$totalbuyfk = $dbds['quantityf'] * $dbds['ainprice'];
							$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
							$dbds['totalainad'] = ($totalbuyfk - $totaldiscfk);
							$subtotalfk += $dbds['totalainad'];
						}
					}
				
					$discvalue = $dbp['disc'] / 100 * $subtotalfk;
					$totalafgdiscfk = $subtotalfk - $discvalue;
					$taxvalue = $dbp['tax'] / 100 * $totalafgdiscfk;
					$dbp['totalain'] = $totalafgdiscfk + $taxvalue;
				}
				$dbdetail['invoiceno'] = $dbp['ainid'];
				$dbdetail['date'] = $dbp['aindate'];
				$dbdetail['scname'] = '-';
				$dbdetail['transaction'] = 'Penyesuaian Stok (+)';
				$dbdetail['debet'] = $dbp['totalain'];
				$dbdetail['credit'] = 0;
				$dbtrx[] = $dbdetail;
			}
		}
		
		//get adjust out
		/*if ($statususer == 1){
			$getalltr = $db->fetch_one("SELECT SUM(totalaout) AS totalaouts FROM headeradjustout WHERE (aoutdate >= '".$startdate."' AND aoutdate <= '".$enddate."') ORDER BY aoutdate");
			$totaltransaction = $getalltr['totalaouts'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT * FROM headeradjustout WHERE (aoutdate >= '".$startdate."' AND aoutdate <= '".$enddate."') ORDER BY totalaout");
			$arrhbid = array();
			if (sizeof($getalltr) > 0){
				$tempforsets = 0;
				foreach ($getalltr as $gatr){
					$tempforsets += $gatr['totalaout'];
					if ($tempforsets > $getsets){
						break;
					}
					array_push($arrhbid,$gatr['aoutid']);
				}
				if (sizeof($arrhbid) > 0){
					$dbaout = $db->fetch_all("SELECT * FROM headeradjustout WHERE aoutid IN (".implode(",",$arrhbid).")".$sql." ORDER BY aoutdate");
				}
			}
		}
		else{*/
			$dbaout = $db->fetch_all("SELECT * FROM headeradjustout WHERE (aoutdate >= '".$startdate."' AND aoutdate <= '".$enddate."') ORDER BY aoutdate");
		//}
		if (sizeof($dbaout) > 0){
			foreach ($dbaout as $dbp){
				if ($statususer == 1){
					//get detail adjust out
					$aout->setId($dbp['aoutid']);
					$dbdetailaout = $aout->getDetailAdjustOut();
					if (sizeof($dbdetailaout) > 0){
						$subtotalfk = 0;
						foreach ($dbdetailaout as $dbds){
							$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
							if ($dbds['quantityf'] < 1){
								$dbds['quantityf'] = 1;
							}
							
							$totalbuyfk = $dbds['quantityf'] * $dbds['aoutprice'];
							$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
							$dbds['totalaoutad'] = ($totalbuyfk - $totaldiscfk);
							$subtotalfk += $dbds['totalaoutad'];
						}
					}
				
					$discvalue = $dbp['disc'] / 100 * $subtotalfk;
					$totalafgdiscfk = $subtotalfk - $discvalue;
					$taxvalue = $dbp['tax'] / 100 * $totalafgdiscfk;
					$dbp['totalaout'] = $totalafgdiscfk + $taxvalue;
				}
				$dbdetail['invoiceno'] = $dbp['aoutid'];
				$dbdetail['date'] = $dbp['aoutdate'];
				$dbdetail['scname'] = '-';
				$dbdetail['transaction'] = 'Penyesuaian Stok (-)';
				$dbdetail['debet'] = 0;
				$dbdetail['credit'] = $dbp['totalaout'];
				$dbtrx[] = $dbdetail;
			}
		}
		
		if (sizeof($dbtrx) > 0){
			$dbpl = multisort($dbtrx,'date','invoiceno','scname','transaction','debet','credit');
			$datego = '';
			$temptrx = 0;
			$tempdebet = 0;
			$tempcredit = 0;
			$trx = 0;
			$debet = 0;
			$credit = 0;
			foreach ($dbpl as $dbplr){
				$datenow = date("d-M-Y",$dbplr['date']);
				if ($datego != $datenow){
					if ($trx != 0){
						$list .= '
							<tr>
								<td width="100" align="center"><b>'.$temptrx.'</b></td>
								<td width="380" align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
								<td width="140" align="right" height="30"><b>'.number_format($tempdebet,2,",",".").'</b></td>
								<td width="140" align="right" height="30"><b>'.number_format($tempcredit,2,",",".").'</b></td>
							</tr>
						';
					}
					$list .= '
						<tr>
							<td width="760" align="left" colspan="6">
							&nbsp;<b>'.$datenow.'</b></td>
						</tr>
					';
					$temptrx = 0;
					$tempdebet = 0;
					$tempcredit = 0;
					$datego = $datenow;
				}
				$discvalue = ($ds['disc'] / 100) * $ds['totals'];
				$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
				$list .= '
					<tr>
						<td width="100" align="left" height="30" class="detailitem">'.htmlspecialchars($dbplr['invoiceno']).'</td>
						<td width="100" align="center" height="30" class="detailitem">'.$datenow.'</td>
						<td width="180" align="left" height="30" class="detailitem">'.htmlspecialchars($dbplr['scname']).'</td>
						<td width="100" align="left" height="30" class="detailitem">'.htmlspecialchars($dbplr['transaction']).'</td>
						<td width="140" align="right" height="30" class="detailitem">'.number_format($dbplr['debet'],2,",",".").'</td>
						<td width="140" align="right" height="30" class="detailitem">'.number_format($dbplr['credit'],2,",",".").'</td>
					</tr>
				';
				$temptrx++;
				$tempdebet += $dbplr['debet'];
				$tempcredit += $dbplr['credit'];
				$trx++;
				$debet += $dbplr['debet'];
				$credit += $dbplr['credit'];
			}
			$list .= '
				<tr>
					<td width="100" align="center"><b>'.$temptrx.'</b></td>
					<td width="380" align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
					<td width="140" align="right" height="30"><b>'.number_format($tempdebet,2,",",".").'</b></td>
					<td width="140" align="right" height="30"><b>'.number_format($tempcredit,2,",",".").'</b></td>
				</tr>
				<tr>
					<td width="100" align="center"><b>'.$trx.'</b></td>
					<td width="380" align="left" colspan="3">&nbsp;<b>GRAND TOTAL</b></td>
					<td width="140" align="right" height="30"><b>'.number_format($debet,2,",",".").'</b></td>
					<td width="140" align="right" height="30"><b>'.number_format($credit,2,",",".").'</b></td>
				</tr>
			';
			
			$profitloss = $debet - $credit;
			if ($profitloss > 0){
				$textpl = 'KEUNTUNGAN : Rp. '.number_format($profitloss,2,",",".");
			}
			else if ($profitloss < 0){
				$textpl = 'KERUGIAN : Rp. '.number_format(abs($profitloss),2,",",".");
			}
			else{
				$textpl = 'IMPAS';
			}
		}
	}
	else{
		$printtemplate = 'reportprofitlossinit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>