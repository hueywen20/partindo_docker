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

	if (empty($useraccess['view_payment'])){
		redirecting('index.php');
	}
	
	$printdate = date("d").' '.$arrmonthname[date("n")-1].' '.date("Y");
	
	$paperarray = array(
					"f4" => "600px",
					"a4" => "550px",
					"letter" => "500px"
				);
	$papersize = $_GET['paper'];
	$paperoptionheight = $paperarray[$papersize];
	if (empty($paperoptionheight)){
		$paperoptionheight = $paperarray['letter'];
	}
	
	$startdate = strtotime($_GET['startpaysdate']);
	$enddate = strtotime($_GET['endpaysdate'].' 23:59:59');
	$paymentno = array();
	if (!empty($startdate) && !empty($enddate)){
		$allpayment = $db->fetch_all("SELECT hp.* FROM headerpayment hp INNER JOIN customer c ON c.customerid = hp.customerid WHERE hp.startdate >= '".$startdate."' AND hp.enddate <= '".$enddate."' AND hp.status = 1 ORDER BY c.customername");
		if (sizeof($allpayment) > 0){
			foreach ($allpayment as $apy){
				array_push($paymentno,$apy['hpid']);
			}
		}
		$_GET['op'] = 'yes';
	}
	else if (!empty($_GET['no'])){
		$paymentno = array($_GET['no']);
	}
	
	if (sizeof($paymentno) > 0){
		$printtemplate = gettemplate('printpaymenttemplate');
		foreach ($paymentno as $pno){
			$payment->setId($pno);
			$headerpayment = $payment->getHeaderpayment();
			$invoicedate = date("d-M-Y",$headerpayment['paymentdate']);
			$startdatef = date("d-m-Y",$headerpayment['startdate']);
			$enddatef = date("d-m-Y",$headerpayment['enddate']);
			
			$customer->setId($headerpayment['customerid']);
			$customer->setDetailId($headerpayment['customeraddrid']);
			$getcustomer = $customer->getCustomerDetail();
			$area->setCode($getcustomer['areacode']);
			$dbarea = $area->getareaDetail();
			if (!empty($dbarea['areaname'])){
				$areaname = ' '.$dbarea['areaname'];
			}
			$customername = $getcustomer['customername'];
			$customeraddr = $getcustomer['address'].$areaname;
			$customertelp = $getcustomer['phone'];

			$temporarytotal = 0;
			$i = 1;
			//payment detail for sale
			$detailpayment = $payment->getDetailPayment('sale');
			$paymentdetail = '';
			$totalsalewalk = 0;
			$totalinvoice = 0;
			$allinvoice = '';
			if (sizeof($detailpayment) > 0){
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th width="50" align="center">NO</th>
								<th width="30"></th>
								<th width="280" align="center">NOMOR FAKTUR JUAL</th>
								<th width="10"></th>
								<th width="180" align="center">TANGGAL FAKTUR JUAL</th>
								<th width="10"></th>
								<th width="200" align="center">JUMLAH ( Rp. )</th>
							</tr>
							</table></td>
						</tr>
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
				';
				foreach ($detailpayment as $ds){
					$sale->setId($ds['hsid']);
					$detailheaders = $sale->getHeaderSale();
									
					if ($statususer == 1){
						$ds['pays'] = 0;
						$sale->setSaleNo($detailheaders['saleno']);
						$alldetailp = $sale->getDetailSale();
						if (sizeof($alldetailp) > 0){
							foreach ($alldetailp as $adp){
								$adp['quantityf'] = discq($adp['quantityf']);
								$totalsalefk = $adp['quantityf'] * $adp['saleprice'];
								$totaldiscfk = $adp['disc'] / 100 * $totalsalefk;
								$ds['pays'] += $totalsalefk - $totaldiscfk;
							}
							$totalgdiscfk = $detailheaders['disc'] / 100 * $ds['pays'];
							$totalafgdiscfk = $ds['pays'] - $totalgdiscfk;
							$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
							$ds['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
							
							$temporarytotal += $ds['pays'];
						}
					}
					
					if ($_GET['usecode'] == 'yes'){
						$printsaleno = $codest->convertcodes($detailheaders['saleno'],'replacements_sale',true);
					}
					else{
						$printsaleno = $detailheaders['saleno'];
					}
					
					$paymentdetail .= '
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="right">'.number_format($i,0,",",".").'</td>
								<td width="30"></td>
								<td width="280" align="left">'.$printsaleno.'</td>
								<td width="10"></td>
								<td width="180" align="center">'.date("d/m/Y",$detailheaders['saledate']).'</td>
								<td width="10"></td>
								<td width="200" align="right">'.number_format($ds['pays'],0,",",".").'</td>
							</tr>
							</table></td>
						</tr>
					';
					
					$allinvoice .= ', '.$printsaleno;
					
					$totalsalewalk += $ds['pays'];
					$i++;
					$totalinvoice++;
				}
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="center"></td>
								<td width="30"></td>
								<td width="280" align="center"></td>
								<td width="10"></td>
								<td width="180" align="right">TOTAL</td>
								<td width="10"></td>
								<td width="200" align="right">'.number_format($totalsalewalk,0,",",".").'</td>
							</tr>
							</table></td>
						</tr>
				';
			}

			//payment detail for sale return
			$detailpayment = $payment->getDetailPayment('return');
			$totalsalerwalk = 0;
			if (sizeof($detailpayment) > 0){
				if (sizeof($detailpayment) > 0){
					$paymentdetail .= '
						<tr>
							<td width="760" height="10"></td>
						</tr>
					';
				}
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th width="50" align="center">NO</th>
								<th width="30"></th>
								<th width="470" align="center">TANGGAL RETUR JUAL</th>
								<th width="10"></th>
								<th width="200" align="center">JUMLAH ( Rp. )</th>
							</tr>
							</table></td>
						</tr>
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
				';
				foreach ($detailpayment as $ds){
					$saler->setDetailId($ds['hsid']);
					$detailheaders = $saler->getDetailSaleRIndv();
										
					if ($statususer == 1){
						$detailheaders['quantityf'] = discq($detailheaders['quantityf']);
						$totalsalefk = $detailheaders['quantityf'] * $detailheaders['salerprice'];
						$totaldiscfk = $detailheaders['disc'] / 100 * $totalsalefk;
						$ds['pays'] = $totalsalefk - $totaldiscfk;
						$temporarytotal -= $ds['pays'];
					}
					
					$paymentdetail .= '
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="right">'.number_format($i,0,",",".").'</td>
								<td width="30"></td>
								<td width="470" align="left">'.date("d/m/Y",$detailheaders['salerdate']).'</td>
								<td width="10"></td>
								<td width="200" align="right">( '.number_format($ds['pays'],0,",",".").' )</td>
							</tr>
							</table></td>
						</tr>
					';
					$totalsalerwalk += $ds['pays'];
					$i++;
				}
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="center"></td>
								<td width="30"></td>
								<td width="280" align="center"></td>
								<td width="10"></td>
								<td width="180" align="right">TOTAL</td>
								<td width="10"></td>
								<td width="200" align="right">( '.number_format($totalsalerwalk,0,",",".").' )</td>
							</tr>
							</table></td>
						</tr>
				';
			}
			
			//payment detail for buy
			$detailpayment = $payment->getDetailPayment('buy');
			$totalbuywalk = 0;
			if (sizeof($detailpayment) > 0){
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th width="50" align="center">NO</th>
								<th width="30"></th>
								<th width="280" align="center">NOMOR BON PEMBELIAN</th>
								<th width="10"></th>
								<th width="180" align="center">TANGGAL PEMBELIAN</th>
								<th width="10"></th>
								<th width="200" align="center">JUMLAH ( Rp. )</th>
							</tr>
							</table></td>
						</tr>
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
				';
				foreach ($detailpayment as $ds){
					$purchase->setId($ds['hsid']);
					$detailheaders = $purchase->getHeaderBuy();
									
					if ($statususer == 1){
						$ds['pays'] = 0;
						$purchase->setBuyNo($detailheaders['buyno']);
						$alldetailp = $purchase->getDetailBuy();
						if (sizeof($alldetailp) > 0){
							foreach ($alldetailp as $adp){
								$adp['quantityf'] = discq($adp['quantityf']);
								$totalbuyfk = $adp['quantityf'] * $adp['buyprice'];
								$totaldiscfk = $adp['disc'] / 100 * $totalbuyfk;
								$ds['pays'] += $totalbuyfk - $totaldiscfk;
							}											
							$totalgdiscfk = $detailheaders['disc'] / 100 * $ds['pays'];
							$totalafgdiscfk = $ds['pays'] - $totalgdiscfk;
							$totalgtaxfk = $detailheaders['tax'] / 100 * $totalafgdiscfk;
							$ds['pays'] = intval($totalafgdiscfk + $totalgtaxfk);
							
							$temporarytotal -= $ds['pays'];
						}
					}
					
					$paymentdetail .= '
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="right">'.number_format($i,0,",",".").'</td>
								<td width="30"></td>
								<td width="280" align="left">'.$detailheaders['orderno'].'</td>
								<td width="10"></td>
								<td width="180" align="center">'.date("d/m/Y",$detailheaders['buydate']).'</td>
								<td width="10"></td>
								<td width="200" align="right">( '.number_format($ds['pays'],0,",",".").' )</td>
							</tr>
							</table></td>
						</tr>
					';
					$totalbuywalk += $ds['pays'];
					$i++;
				}
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="center"></td>
								<td width="30"></td>
								<td width="280" align="center"></td>
								<td width="10"></td>
								<td width="180" align="right">TOTAL</td>
								<td width="10"></td>
								<td width="200" align="right">( '.number_format($totalbuywalk,0,",",".").' )</td>
							</tr>
							</table></td>
						</tr>
				';
			}

			//payment detail for return buy
			$detailpayment = $payment->getDetailPayment('returnby');
			$totalbuyrwalk = 0;
			if (sizeof($detailpayment) > 0){
				if (sizeof($detailpayment) > 0){
					$paymentdetail .= '
						<tr>
							<td width="760" height="10"></td>
						</tr>
					';
				}
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th width="50" align="center">NO</th>
								<th width="30"></th>
								<th width="470" align="center">TANGGAL RETUR BELI</th>
								<th width="10"></th>
								<th width="200" align="center">JUMLAH ( Rp. )</th>
							</tr>
							</table></td>
						</tr>
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
				';
				foreach ($detailpayment as $ds){
					$purchaser->setDetailId($ds['hsid']);
					$detailheaders = $purchaser->getDetailBuyRIndv();
										
					if ($statususer == 1){
						$detailheaders['quantityf'] = discq($detailheaders['quantityf']);
						$totalbuyfk = $detailheaders['quantityf'] * $detailheaders['buyrprice'];
						$totaldiscfk = $detailheaders['disc'] / 100 * $totalbuyfk;
						$ds['pays'] = $totalbuyfk - $totaldiscfk;
						$temporarytotal += $ds['pays'];
					}
					
					$paymentdetail .= '
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="right">'.number_format($i,0,",",".").'</td>
								<td width="30"></td>
								<td width="470" align="left">'.date("d/m/Y",$detailheaders['buyrdate']).'</td>
								<td width="10"></td>
								<td width="200" align="right">'.number_format($ds['pays'],0,",",".").'</td>
							</tr>
							</table></td>
						</tr>
					';
					$totalbuyrwalk += $ds['pays'];
					$i++;
				}
				$paymentdetail .= '
						<tr>
							<td width="760" height="1" style="border-bottom: 1px solid #333"></td>
						</tr>
						<tr>
							<td width="760" align="left" height="25">
							<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50" align="center"></td>
								<td width="30"></td>
								<td width="280" align="center"></td>
								<td width="10"></td>
								<td width="180" align="right">TOTAL</td>
								<td width="10"></td>
								<td width="200" align="right">'.number_format($totalbuyrwalk,0,",",".").'</td>
							</tr>
							</table></td>
						</tr>
				';
			}
							
			if ($statususer == 1){
				$headerpayment['totalpayment'] = $temporarytotal;
				$headerpayment['grandtotals'] = $temporarytotal;
			}
			
			$allinvoice = substr($allinvoice,2);
			
			$fremainingprevious = number_format($headerpayment['remainingprevious'],2,",",".");
			$ftotal = number_format($headerpayment['grandtotals'],2,",",".");
			$terbilangs = ucwords(terbilang($headerpayment['grandtotals']));
			$terbilangtotalinvoice = ucwords(terbilang($totalinvoice));
			
			$headerpayment = array_map("htmlspecialchars",$headerpayment);
		
			eval("\$printtext .= \"$printtemplate\";");
		}
	}
	
	if (!empty($printtext)){
		$headinclude = gettemplate('headinclude');
		eval("\$headinclude = \"$headinclude\";");

		$tmpl = gettemplate('printpayment');
		eval("\$template = \"$tmpl\";");
		echo $template;
	}
?>