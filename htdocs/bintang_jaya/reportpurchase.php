<?php
	require_once "global.php";
	
	require_once "class/supplier.php";
	require_once "class/purchase.php";
	require_once "class/PurchaseR.php";
	require_once "class/PayDebt.php";
	require_once "class/Payment.php";
	$supplier = new supplier();
	$purchase = new Purchase();
	$purchaser = new PurchaseR();
	$paydebt = new PayDebt();
	$payment = new Payment();
	
	$sql = '';
	if ($_POST['trtype'] == "cash"){
		$printtype = "CASH / TUNAI";
		$arrtype = array("cash" => $printtype);
		$sql .= " AND trtype='cash'";
	}
	else if ($_POST['trtype'] == "credit"){
		$printtype = "CREDIT / KREDIT";
		$arrtype = array("credit" => $printtype);
		$sql .= " AND trtype='credit'";
	}
	else{
		$arrtype = array("cash" => "CASH / TUNAI","credit" => "CREDIT / KREDIT");
	}
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_GET['view'] == 'periodsupplier'){
		
		if (empty($useraccess['report_purchasepc'])){
			redirecting('index.php');
		}
		
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			$sqls = '';
			if (!empty($_POST['suppliercode'])){
				$sqls = " AND suppliercode='".$_POST['suppliercode']."'";
			}
			
			if ($_POST['basedon'] == 'buydate' || !empty($sqls)){
				$printtemplate = 'reportbuypc';
				/*if ($statususer == 1){
					$getalltr = $db->fetch_one("SELECT SUM(totalbuy) AS totalbuys FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql." ORDER BY buydate");
					$totaltransaction = $getalltr['totalbuys'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql." ORDER BY totalbuy");
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
							$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE buyid IN (".implode(",",$arrhbid).")".$sql." ORDER BY buydate");
						}
					}
				}
				else{*/
					$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql.$sqls." ORDER BY buydate");
				//}
				$datego = '';
				$temptrx = 0;
				$tempdisc = 0;
				$temptax = 0;
				$temptotal = 0;
				
				$temptrxcash = 0;
				$temptrxdisccash = 0;
				$temptrxtaxcash = 0;
				$temptrxtotalcash = 0;
				
				$trxcash = 0;
				$trxdisccash = 0;
				$trxtaxcash = 0;
				$trxtotalcash = 0;
				
				$temptrxcredit = 0;
				$temptrxdisccredit = 0;
				$temptrxtaxcredit = 0;
				$temptrxtotalcredit = 0;
				
				$trxcredit = 0;
				$trxdisccredit = 0;
				$trxtaxcredit = 0;
				$trxtotalcredit = 0;
				
				$trx = 0;
				$disc = 0;
				$tax = 0;
				$total = 0;
				if (sizeof($dbbuy) > 0){
					foreach ($dbbuy as $ds){
						$supplier->setCode($ds['suppliercode']);
						$getsupplier = $supplier->getsupplierDetail();
						
						$datenow = date("d-M-Y",$ds['buydate']);
						$datenowdt = date("d-M-Y",$ds['buydate']);
						$datenowdd = date("d-M-Y",$ds['duedate']);
						if ($datego != $datenow){
							if ($total != 0){
								$list .= '
									<tr>
										<td align="center"><b>'.$temptrx.'</b></td>
										<td align="right" colspan="2" align="right"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
										<td align="right" height="30">
										<b>'.number_format($tempdisc,2,",",".").'</b>
										<br><b>'.number_format($temptax,2,",",".").'</b>
										<br><b>'.number_format($temptotal,2,",",".").'</b></td>
									</tr>
								';
							}
							$list .= '
								<tr>
									<td align="left" colspan="4" height="35" valign="bottom" bgcolor="#EEE">
									<font size="+1"><b>'.$datenow.'</b></font></td>
								</tr>
							';
							$temptrx = 0;
							$tempdisc = 0;
							$temptax = 0;
							$temptotal = 0;
							$datego = $datenow;
						}
						$discvalue = ($ds['disc'] / 100) * $ds['totals'];
						$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
					
						$listinner = '';
						//get detail purchase
						$purchase->setBuyNo($ds['buyno']);
						$dbdetailbuy = $purchase->getDetailBuy();
						$listinner = '';
						if (sizeof($dbdetailbuy) > 0){
							$listinner .= '
								<tr>
									<td width="10%">&nbsp;</td>
									<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
									<table border="1" cellpadding="0" width="100%" cellspacing="0">
									<tr>
										<th align="center" width="16%" bgcolor="#EFEFEF">Kode</th>
										<th align="center" width="16%" bgcolor="#EFEFEF">Nama</th>
										<th align="center" width="10%" bgcolor="#EFEFEF">Merek</th>
										<th align="center" width="10%" bgcolor="#EFEFEF">Tipe</th>
										<th align="center" width="10%" bgcolor="#EFEFEF">Part No</th>
										<th align="center" width="8%" bgcolor="#EFEFEF">Qty</th>
										<th align="center" width="10%" bgcolor="#EFEFEF">Harga</th>
										<th align="center" width="10%" bgcolor="#EFEFEF">Diskon</th>
										<th align="center" width="10%" bgcolor="#EFEFEF">Sub Total</th>
									</tr>
							';
							$subtotalfk = 0;
							foreach ($dbdetailbuy as $dbds){
								if ($statususer == 1){
									$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
									if ($dbds['quantityf'] < 1){
										$dbds['quantityf'] = 1;
									}
									
									$totalbuyfk = $dbds['quantityf'] * $dbds['buyprice'];
									$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
									$dbds['totalbuyad'] = ($totalbuyfk - $totaldiscfk);
									$subtotalfk += $dbds['totalbuyad'];
								}
								
								$listinner .= '
									<tr>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['buyprice'],2,",",".").'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalbuyad'],2,",",".").'</td>
									</tr>
								';
							}
							
							$listinner .= '
									</table></td>
								</tr>
							';
						}
					
						if ($statususer == 1){
							$discvalue = $ds['disc'] / 100 * $subtotalfk;
							$totalafgdiscfk = $subtotalfk - $discvalue;
							$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
							$ds['totalbuy'] = $totalafgdiscfk + $taxvalue;
						}
														
						//get return buy
						$tempbuyr = 0;
						$gsr['totalbuyr'] = 0;
						$getbuyr = $purchaser->getBuyRFromBuy($ds['buyno']);
						if (sizeof($getbuyr) > 0){
							$listinner .= '
								<tr>
									<td width="10%" height="25" align="left"></td>
									<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR BELI</b></i></td>
								</tr>
								<tr>
									<td width="10%" align="left"></td>
									<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
									<table border="1" cellpadding="0" width="100%" cellspacing="0">
							';
							
							foreach ($getbuyr as $gsr){											
								if ($statususer == 1){
									$gsr['quantityf'] = discq($gsr['quantityf']);
									$totalbuyrfk = $gsr['quantityf'] * $gsr['buyrprice'];
									$totaldiscfk = $gsr['detaildisc'] / 100 * $totalbuyrfk;
									$tempstd = $totalbuyrfk - $totaldiscfk;
									$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
									$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
									$gsr['totalbuyrad'] = $tempstd;
								}
								$tempbuyr += $gsr['totalbuyrad'];
								
								$listinner .= '
									<tr>
										<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.htmlspecialchars($gsr['stockcode']).'<br>
										( '.date("d-M-Y",$gsr['buyrdate']).' )</td>
										<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
										<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
										<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
										<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
										<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
										<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['buyrprice'],2,",",".").'</td>
										<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
										<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalbuyrad'],2,",",".").'</td>
									</tr>
								';
							}
							
							$listinner .= '
									</table></td>
								</tr>
							';
							
							$gsr['totalbuyr'] = $tempbuyr;
						}
						
						$totalfinal = ($ds['totalbuy']-$gsr['totalbuyr']);
						
						$list .= '
							<tr>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyno']).'<br>'.htmlspecialchars($ds['orderno']).'</td>
								<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
								<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($getsupplier['suppliername']).'</td>
							</tr>
						'.$listinner.'
							<tr>
								<td align="left" height="30" class="detailitem"></td>
								<td align="right" height="30" class="detailitem" colspan="2">DISKON<br>PPN<br>TOTAL</td>
								<td align="right" height="30" class="detailitem">
								'.number_format($discvalue,2,",",".").
								'<br>'.number_format($taxvalue,2,",",".").
								'<br>'.number_format($totalfinal,2,",",".").'</td>
							</tr>
							<tr>
								<td colspan="4" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
							</tr>
						';
						$temptrx++;
						$tempdisc += $discvalue;
						$temptax += $taxvalue;
						$temptotal += $totalfinal;
						$trx++;
						$disc += $discvalue;
						$tax += $taxvalue;
						$total += $totalfinal;
						
						if ($ds['trtype'] == 'cash'){
							$temptrxcash++;
							$temptrxdisccash += $discvalue;
							$temptrxtaxcash += $taxvalue;
							$temptrxtotalcash += $totalfinal;
							
							$trxcash++;
							$trxdisccash += $discvalue;
							$trxtaxcash += $taxvalue;
							$trxtotalcash += $totalfinal;
						}
						else{
							$temptrxcredit++;
							$temptrxdisccredit += $discvalue;
							$temptrxtaxcredit += $taxvalue;
							$temptrxtotalcredit += $totalfinal;
							
							$trxcredit++;
							$trxdisccredit += $discvalue;
							$trxtaxcredit += $taxvalue;
							$trxtotalcash += $totalfinal;
						}
					}
					$listalls = '
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="10%" bgcolor="#DEDEDE">NO FAKTUR / NO BON</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
							<th align="center" width="70%" bgcolor="#DEDEDE">NAMA SUPPLIER</th>
						</tr>
						'.$list.'
						<tr>
							<td align="center"><b>'.$temptrx.'</b></td>
							<td align="right" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
							<td align="right" height="30">
							<b>'.number_format($tempdisc,2,",",".").'</b>
							<br><b>'.number_format($temptax,2,",",".").'</b>
							<br><b>'.number_format($temptotal,2,",",".").'</b></td>
						</tr>
						<tr>
							<td colspan="4" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
						</tr>
						<tr>
							<td align="center"><b>'.$trx.'</b></td>
							<td align="right" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>GRAND TOTAL</b></td>
							<td align="right" height="30">
							<b>'.number_format($disc,2,",",".").'</b>
							<br><b>'.number_format($tax,2,",",".").'</b>
							<br><b>'.number_format($total,2,",",".").'</b></td>
						</tr>
						</table>
					';
				}
			}
			else if ($_POST['basedon'] == 'supplier'){
				$printtemplate = 'reportbuypcpersupplier';
				
				$allsupplier = $supplier->getListsupplier('partial');
				if (sizeof($allsupplier) > 0){
					$trx = 0;
					$disc = 0;
					$tax = 0;
					$total = 0;
					foreach ($allsupplier as $asp){
						$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."') AND suppliercode='".$asp['suppliercode']."'".$sql." ORDER BY buydate");
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						$list = '';
						if (sizeof($dbbuy) > 0){
							foreach ($dbbuy as $ds){
								$supplier->setCode($ds['suppliercode']);
								$getsupplier = $supplier->getsupplierDetail();
								
								$datenow = date("d-M-Y",$ds['buydate']);
								$datenowdt = date("d-M-Y",$ds['buydate']);
								$datenowdd = date("d-M-Y",$ds['duedate']);
								
								$discvalue = ($ds['disc'] / 100) * $ds['totals'];
								$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
							
								$listinner = '';
								//get detail purchase
								$purchase->setBuyNo($ds['buyno']);
								$dbdetailbuy = $purchase->getDetailBuy();
								if (sizeof($dbdetailbuy) > 0){
									$listinner .= '
										<tr>
											<td width="10%">&nbsp;</td>
											<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
											<table border="1" cellpadding="0" width="100%" cellspacing="0">
											<tr>
												<th align="center" width="16%" bgcolor="#EFEFEF">Kode</th>
												<th align="center" width="16%" bgcolor="#EFEFEF">Nama</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Merek</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Tipe</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Part No</th>
												<th align="center" width="8%" bgcolor="#EFEFEF">Qty</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Harga</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Diskon</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Sub Total</th>
											</tr>
									';
									$subtotalfk = 0;
									foreach ($dbdetailbuy as $dbds){
										if ($statususer == 1){
											$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
											if ($dbds['quantityf'] < 1){
												$dbds['quantityf'] = 1;
											}
											
											$totalbuyfk = $dbds['quantityf'] * $dbds['buyprice'];
											$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
											$dbds['totalbuyad'] = ($totalbuyfk - $totaldiscfk);
											$subtotalfk += $dbds['totalbuyad'];
										}
										
										$listinner .= '
											<tr>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['buyprice'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalbuyad'],2,",",".").'</td>
											</tr>
										';
									}
									
									$listinner .= '
											</table></td>
										</tr>
									';
								}
							
								if ($statususer == 1){
									$discvalue = $ds['disc'] / 100 * $subtotalfk;
									$totalafgdiscfk = $subtotalfk - $discvalue;
									$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
									$ds['totalbuy'] = $totalafgdiscfk + $taxvalue;
								}
														
								//get return buy
								$tempbuyr = 0;
								$gsr['totalbuyr'] = 0;
								$getbuyr = $purchaser->getBuyRFromBuy($ds['buyno']);
								if (sizeof($getbuyr) > 0){
									$listinner .= '
										<tr>
											<td width="10%" height="25" align="left"></td>
											<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR BELI</b></i></td>
										</tr>
										<tr>
											<td width="10%" align="left"></td>
											<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
											<table border="1" cellpadding="0" width="100%" cellspacing="0">
									';
									
									foreach ($getbuyr as $gsr){											
										if ($statususer == 1){
											$gsr['quantityf'] = discq($gsr['quantityf']);
											$totalbuyrfk = $gsr['quantityf'] * $gsr['buyrprice'];
											$totaldiscfk = $gsr['detaildisc'] / 100 * $totalbuyrfk;
											$tempstd = $totalbuyrfk - $totaldiscfk;
											$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
											$gsr['totalbuyrad'] = $tempstd;
										}
										$tempbuyr += $gsr['totalbuyrad'];
										
										$listinner .= '
											<tr>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
												'.htmlspecialchars($gsr['stockcode']).'<br>
												( '.date("d-M-Y",$gsr['buyrdate']).' )</td>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
												<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['buyrprice'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalbuyrad'],2,",",".").'</td>
											</tr>
										';
									}
									
									$listinner .= '
											</table></td>
										</tr>
									';
									
									$gsr['totalbuyr'] = $tempbuyr;
								}
								
								$totalfinal = ($ds['totalbuy']-$gsr['totalbuyr']);
								
								$list .= '
									<tr>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyno']).'<br>'.htmlspecialchars($ds['orderno']).'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($getsupplier['suppliername']).'</td>
									</tr>
								'.$listinner.'
									<tr>
										<td align="left" height="30" class="detailitem"></td>
										<td align="right" height="30" class="detailitem" colspan="2">DISKON<br>PPN<br>TOTAL</td>
										<td align="right" height="30" class="detailitem">
										'.number_format($discvalue,2,",",".").
										'<br>'.number_format($taxvalue,2,",",".").
										'<br>'.number_format($totalfinal,2,",",".").'</td>
									</tr>
									<tr>
										<td colspan="4" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
									</tr>
								';
								$temptrx++;
								$tempdisc += $discvalue;
								$temptax += $taxvalue;
								$temptotal += $totalfinal;
								$trx++;
								$disc += $discvalue;
								$tax += $taxvalue;
								$total += $totalfinal;
							}
						
							$listall .= '
								<div align="right" style="width: 100%; padding-top: 10px">
								<span style="float: left">Supplier : <b>'.$asp['suppliercode'].' - '.$asp['suppliername'].'</b></span>
								Tanggal Cetak : '.$printdate.'</div>
								<div align="center" style="width: 100%; padding-bottom: 20px; border-bottom: 1px dotted #000; clear: both">
								<table border="1" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<th align="center" width="10%" bgcolor="#DEDEDE">NO FAKTUR / NO BON</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
									<th align="center" width="70%" bgcolor="#DEDEDE">NAMA SUPPLIER</th>
								</tr>
								'.$list.'
								<tr>
									<td align="center"><b>'.$temptrx.'</b></td>
									<td align="right" height="30" class="detailitem" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
									<td align="right" height="30" class="detailitem" style="font-weight: bold">
									'.number_format($tempdisc,2,",",".").
									'<br>'.number_format($temptax,2,",",".").
									'<br>'.number_format($temptotal,2,",",".").'</td>
								</tr>
								</table></div>
							';
						}
					}
					$listall .= '
						<div align="left" style="width: 100%; padding: 10px 0">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td align="center" width="10%"><b>'.$trx.'</b></td>
							<td align="right" width="20%"><b>DISKON</b><br><b>PPN</b><br><b>GRAND TOTAL</b></td>
							<td align="right" width="70%">
							<b>'.number_format($disc,2,",",".").'</b>
							<br><b>'.number_format($tax,2,",",".").'</b>
							<br><b>'.number_format($total,2,",",".").'</b></td>
						</tr>
						</table></div>
					';
				}
			}
			else{
				$printtemplate = 'reportbuypc';
				if (sizeof($arrtype) > 0){
					$gtrx = 0;
					$gdisc = 0;
					$gtax = 0;
					$gtotal = 0;
					foreach ($arrtype as $keyp => $artp){
						$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE trtype='".$keyp."' AND (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql." ORDER BY buydate");
						$datego = '';
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						$trx = 0;
						$disc = 0;
						$tax = 0;
						$total = 0;
						$list = '';
						if (sizeof($dbbuy) > 0){
							foreach ($dbbuy as $ds){
								$supplier->setCode($ds['suppliercode']);
								$getsupplier = $supplier->getsupplierDetail();
								
								$datenow = date("d-M-Y",$ds['buydate']);
								$datenowdt = date("d-M-Y",$ds['buydate']);
								$datenowdd = date("d-M-Y",$ds['duedate']);
								if ($datego != $datenow){
									if ($total != 0){
										$list .= '
											<tr>
												<td align="center"><b>'.$temptrx.'</b></td>
												<td align="right" colspan="2" align="right"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
												<td align="right" height="30">
												<b>'.number_format($tempdisc,2,",",".").'</b>
												<br><b>'.number_format($temptax,2,",",".").'</b>
												<br><b>'.number_format($temptotal,2,",",".").'</b></td>
											</tr>
										';
									}
									$list .= '
										<tr>
											<td align="left" colspan="4" height="35" valign="bottom" bgcolor="#EEE">
											<font size="+1"><b>'.$datenow.'</b></font></td>
										</tr>
									';
									$temptrx = 0;
									$tempdisc = 0;
									$temptax = 0;
									$temptotal = 0;
									$datego = $datenow;
								}
								$discvalue = ($ds['disc'] / 100) * $ds['totals'];
								$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
							
								$listinner = '';
								//get detail purchase
								$purchase->setBuyNo($ds['buyno']);
								$dbdetailbuy = $purchase->getDetailBuy();
								$listinner = '';
								if (sizeof($dbdetailbuy) > 0){
									$listinner .= '
										<tr>
											<td width="10%">&nbsp;</td>
											<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
											<table border="1" cellpadding="0" width="100%" cellspacing="0">
											<tr>
												<th align="center" width="16%" bgcolor="#EFEFEF">Kode</th>
												<th align="center" width="16%" bgcolor="#EFEFEF">Nama</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Merek</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Tipe</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Part No</th>
												<th align="center" width="8%" bgcolor="#EFEFEF">Qty</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Harga</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Diskon</th>
												<th align="center" width="10%" bgcolor="#EFEFEF">Sub Total</th>
											</tr>
									';
									$subtotalfk = 0;
									foreach ($dbdetailbuy as $dbds){
										if ($statususer == 1){
											$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
											if ($dbds['quantityf'] < 1){
												$dbds['quantityf'] = 1;
											}
											
											$totalbuyfk = $dbds['quantityf'] * $dbds['buyprice'];
											$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
											$dbds['totalbuyad'] = ($totalbuyfk - $totaldiscfk);
											$subtotalfk += $dbds['totalbuyad'];
										}
										
										$listinner .= '
											<tr>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['buyprice'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalbuyad'],2,",",".").'</td>
											</tr>
										';
									}
									
									$listinner .= '
											</table></td>
										</tr>
									';
								}
							
								if ($statususer == 1){
									$discvalue = $ds['disc'] / 100 * $subtotalfk;
									$totalafgdiscfk = $subtotalfk - $discvalue;
									$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
									$ds['totalbuy'] = $totalafgdiscfk + $taxvalue;
								}
																
								//get return buy
								$tempbuyr = 0;
								$gsr['totalbuyr'] = 0;
								$getbuyr = $purchaser->getBuyRFromBuy($ds['buyno']);
								if (sizeof($getbuyr) > 0){
									$listinner .= '
										<tr>
											<td width="10%" height="25" align="left"></td>
											<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR BELI</b></i></td>
										</tr>
										<tr>
											<td width="10%" align="left"></td>
											<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
											<table border="1" cellpadding="0" width="100%" cellspacing="0">
									';
									
									foreach ($getbuyr as $gsr){											
										if ($statususer == 1){
											$gsr['quantityf'] = discq($gsr['quantityf']);
											$totalbuyrfk = $gsr['quantityf'] * $gsr['buyrprice'];
											$totaldiscfk = $gsr['detaildisc'] / 100 * $totalbuyrfk;
											$tempstd = $totalbuyrfk - $totaldiscfk;
											$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
											$gsr['totalbuyrad'] = $tempstd;
										}
										$tempbuyr += $gsr['totalbuyrad'];
										
										$listinner .= '
											<tr>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
												'.htmlspecialchars($gsr['stockcode']).'<br>
												( '.date("d-M-Y",$gsr['buyrdate']).' )</td>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
												<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['buyrprice'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalbuyrad'],2,",",".").'</td>
											</tr>
										';
									}
									
									$listinner .= '
											</table></td>
										</tr>
									';
									
									$gsr['totalbuyr'] = $tempbuyr;
								}
								
								$totalfinal = ($ds['totalbuy']-$gsr['totalbuyr']);
								
								$list .= '
									<tr>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyno']).'<br>'.htmlspecialchars($ds['orderno']).'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($getsupplier['suppliername']).'</td>
									</tr>
								'.$listinner.'
									<tr>
										<td align="left" height="30" class="detailitem"></td>
										<td align="right" height="30" class="detailitem" colspan="2">DISKON<br>PPN<br>TOTAL</td>
										<td align="right" height="30" class="detailitem">
										'.number_format($discvalue,2,",",".").
										'<br>'.number_format($taxvalue,2,",",".").
										'<br>'.number_format($totalfinal,2,",",".").'</td>
									</tr>
									<tr>
										<td colspan="4" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
									</tr>
								';
								$temptrx++;
								$tempdisc += $discvalue;
								$temptax += $taxvalue;
								$temptotal += $totalfinal;
								$trx++;
								$disc += $discvalue;
								$tax += $taxvalue;
								$total += $totalfinal;
								$gtrx++;
								$gdisc += $discvalue;
								$gtax += $taxvalue;
								$gtotal += $totalfinal;
							}
							$listalls .= '
								<div align="center">
								<h2>'.$artp.'</h2></div>
								<table border="1" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<th align="center" width="10%" bgcolor="#DEDEDE">NO FAKTUR / NO BON</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
									<th align="center" width="70%" bgcolor="#DEDEDE">NAMA SUPPLIER</th>
								</tr>
								'.$list.'
								<tr>
									<td align="center"><b>'.$temptrx.'</b></td>
									<td align="right" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
									<td align="right" height="30">
									<b>'.number_format($tempdisc,2,",",".").'</b>
									<br><b>'.number_format($temptax,2,",",".").'</b>
									<br><b>'.number_format($temptotal,2,",",".").'</b></td>
								</tr>
								<tr>
									<td colspan="4" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
								</tr>
								<tr>
									<td align="center"><b>'.$trx.'</b></td>
									<td align="right" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL PEMBELIAN '.$artp.'</b></td>
									<td align="right" height="30">
									<b>'.number_format($disc,2,",",".").'</b>
									<br><b>'.number_format($tax,2,",",".").'</b>
									<br><b>'.number_format($total,2,",",".").'</b></td>
								</tr>
								</table>
							';
						}
					}
					$listalls .= '
						<div align="center">
						<h2>TOTAL PEMBELIAN KESELURUHAN</h2></div>
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td align="center" width="10%"><b>'.$gtrx.'</b></td>
							<td align="right"  width="20%"><b>DISKON</b><br><b>PPN</b><br><b>GRAND TOTAL</b></td>
							<td align="right" height="70">
							<b>'.number_format($gdisc,2,",",".").'</b>
							<br><b>'.number_format($gtax,2,",",".").'</b>
							<br><b>'.number_format($gtotal,2,",",".").'</b></td>
						</tr>
						</table>
					';
				}
			}
		}
		else{
			$printtemplate = 'reportbuypcinit';
		}
	}
	else if ($_GET['view'] == 'duedate'){
		
		if (empty($useraccess['report_purchasedd'])){
			redirecting('index.php');
		}
		
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$printtemplate = 'reportbuydd';
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			$sql = '';
			if (!empty($_POST['suppliercode'])){
				$sql = " AND suppliercode='".$_POST['suppliercode']."'";
			}
			/*if ($statususer == 1){
				$getalltr = $db->fetch_one("SELECT SUM(totalbuy) AS totalbuys FROM headerbuy WHERE (duedate >= '".$startdate."' AND duedate <= '".$enddate."')".$sql." ORDER BY duedate");
				$totaltransaction = $getalltr['totalbuys'];
				$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
				
				$getalltr = $db->fetch_all("SELECT * FROM headerbuy WHERE (duedate >= '".$startdate."' AND duedate <= '".$enddate."')".$sql." ORDER BY totalbuy");
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
						$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE buyid IN (".implode(",",$arrhbid).")".$sql." ORDER BY duedate");
					}
				}
			}
			else{*/
				$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE (duedate >= '".$startdate."' AND duedate <= '".$enddate."')".$sql." ORDER BY duedate");
			//}
			$datego = '';
			$temptrx = 0;
			$tempdisc = 0;
			$temptax = 0;
			$temptotal = 0;
			$trx = 0;
			$disc = 0;
			$tax = 0;
			$total = 0;
			if (sizeof($dbbuy) > 0){
				foreach ($dbbuy as $ds){
					$supplier->setCode($ds['suppliercode']);
					$getsupplier = $supplier->getsupplierDetail();
					
					$datenow = date("d-M-Y",$ds['duedate']);
					$datenowdt = date("d-M-Y",$ds['buydate']);
					$datenowdd = date("d-M-Y",$ds['duedate']);
					if ($datego != $datenow){
						if ($total != 0){
							$list .= '
								<tr>
									<td align="center"><b>'.$temptrx.'</b></td>
									<td align="right" colspan="2" align="right"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
									<td align="right" height="30">
									<b>'.number_format($tempdisc,2,",",".").'</b>
									<br><b>'.number_format($temptax,2,",",".").'</b>
									<br><b>'.number_format($temptotal,2,",",".").'</b></td>
								</tr>
							';
						}
						$list .= '
							<tr>
								<td align="center" colspan="4" valign="bottom" height="35" bgcolor="#EEE">
								<font size="+1"><b>'.$datenow.'</b></font></td>
							</tr>
						';
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						$datego = $datenow;
					}
					$discvalue = ($ds['disc'] / 100) * $ds['totals'];
					$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
				
					$listinner = '';
					//get detail purchase
					$purchase->setBuyNo($ds['buyno']);
					$dbdetailbuy = $purchase->getDetailBuy();
					$listinner = '';
					if (sizeof($dbdetailbuy) > 0){
						$listinner .= '
							<tr>
								<td width="10%">&nbsp;</td>
								<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
								<table border="1" cellpadding="0" width="100%" cellspacing="0">
								<tr>
									<th align="center" width="16%" bgcolor="#EFEFEF">Kode</th>
									<th align="center" width="16%" bgcolor="#EFEFEF">Nama</th>
									<th align="center" width="10%" bgcolor="#EFEFEF">Merek</th>
									<th align="center" width="10%" bgcolor="#EFEFEF">Tipe</th>
									<th align="center" width="10%" bgcolor="#EFEFEF">Part No</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Qty</th>
									<th align="center" width="10%" bgcolor="#EFEFEF">Harga</th>
									<th align="center" width="10%" bgcolor="#EFEFEF">Diskon</th>
									<th align="center" width="10%" bgcolor="#EFEFEF">Sub Total</th>
								</tr>
						';
						$subtotalfk = 0;
						foreach ($dbdetailbuy as $dbds){
							if ($statususer == 1){
								$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
								if ($dbds['quantityf'] < 1){
									$dbds['quantityf'] = 1;
								}
								
								$totalbuyfk = $dbds['quantityf'] * $dbds['buyprice'];
								$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
								$dbds['totalbuyad'] = ($totalbuyfk - $totaldiscfk);
								$subtotalfk += $dbds['totalbuyad'];
							}
							
							$listinner .= '
								<tr>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['buyprice'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalbuyad'],2,",",".").'</td>
								</tr>
							';
						}
						
						$listinner .= '
								</table></td>
							</tr>
						';
					}
				
					if ($statususer == 1){
						$discvalue = $ds['disc'] / 100 * $subtotalfk;
						$totalafgdiscfk = $subtotalfk - $discvalue;
						$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
						$ds['totalbuy'] = $totalafgdiscfk + $taxvalue;
					}
														
					//get return buy
					$tempbuyr = 0;
					$gsr['totalbuyr'] = 0;
					$getbuyr = $purchaser->getBuyRFromBuy($ds['buyno']);
					if (sizeof($getbuyr) > 0){
						$listinner .= '
							<tr>
								<td width="10%" height="25" align="left"></td>
								<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR BELI</b></i></td>
							</tr>
							<tr>
								<td width="10%" align="left"></td>
								<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
								<table border="1" cellpadding="0" width="100%" cellspacing="0">
						';
						
						foreach ($getbuyr as $gsr){											
							if ($statususer == 1){
								$gsr['quantityf'] = discq($gsr['quantityf']);
								$totalbuyrfk = $gsr['quantityf'] * $gsr['buyrprice'];
								$totaldiscfk = $gsr['detaildisc'] / 100 * $totalbuyrfk;
								$tempstd = $totalbuyrfk - $totaldiscfk;
								$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
								$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
								$gsr['totalbuyrad'] = $tempstd;
							}
							$tempbuyr += $gsr['totalbuyrad'];
						
							$listinner .= '
								<tr>
									<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
									'.htmlspecialchars($gsr['stockcode']).'<br>
									( '.date("d-M-Y",$gsr['buyrdate']).' )</td>
									<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
									<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
									<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
									<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
									<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
									<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['buyrprice'],2,",",".").'</td>
									<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
									<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalbuyrad'],2,",",".").'</td>
								</tr>
							';
						}
						
						$listinner .= '
								</table></td>
							</tr>
						';
						
						$gsr['totalbuyr'] = $tempbuyr;
					}
					
					$totalfinal = ($ds['totalbuy']-$gsr['totalbuyr']);
					
					$list .= '
						<tr>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyno']).'<br>'.htmlspecialchars($ds['orderno']).'</td>
							<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
							<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($getsupplier['suppliername']).'</td>
						</tr>
					'.$listinner.'
						<tr>
							<td align="left" height="30" class="detailitem"></td>
							<td align="right" height="30" class="detailitem" colspan="2">DISKON<br>PPN<br>TOTAL</td>
							<td align="right" height="30" class="detailitem">
							'.number_format($discvalue,2,",",".").
							'<br>'.number_format($taxvalue,2,",",".").
							'<br>'.number_format($totalfinal,2,",",".").'</td>
						</tr>
						<tr>
							<td colspan="4" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
						</tr>
					';
					$temptrx++;
					$tempdisc += $discvalue;
					$temptax += $taxvalue;
					$temptotal += $totalfinal;
					$trx++;
					$disc += $discvalue;
					$tax += $taxvalue;
					$total += $totalfinal;
				}
				$list .= '
					<tr>
						<td align="center"><b>'.$temptrx.'</b></td>
						<td align="right" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
						<td align="right" height="30">
						<b>'.number_format($tempdisc,2,",",".").'</b>
						<br><b>'.number_format($temptax,2,",",".").'</b>
						<br><b>'.number_format($temptotal,2,",",".").'</b></td>
					</tr>
					<tr>
						<td colspan="4" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
					</tr>
					<tr>
						<td align="center"><b>'.$trx.'</b></td>
						<td align="right" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>GRAND TOTAL</b></td>
						<td align="right" height="30">
						<b>'.number_format($disc,2,",",".").'</b>
						<br><b>'.number_format($tax,2,",",".").'</b>
						<br><b>'.number_format($total,2,",",".").'</b></td>
					</tr>
				';
			}
		}
		else{
			$printtemplate = 'reportbuyddinit';
		}
	}
	else if ($_GET['view'] == 'monthly'){
		
		if (empty($useraccess['report_purchasemonthly'])){
			redirecting('index.php');
		}
		
		if ($_POST['basedon'] == 'buydate'){
			if (!empty($_POST['monthstart']) && !empty($_POST['yearstart']) && !empty($_POST['monthend']) && !empty($_POST['yearend'])){
				$printtemplate = 'reportbuym';
				$startdate = strtotime('01-'.$_POST['monthstart'].'-'.$_POST['yearstart']);
				$intmonthstart = intval($_POST['monthstart']);
				$intmonthend = intval($_POST['monthend']);
				if ($intmonthend == 12){
					$enddate = strtotime('01-01-'.($_POST['yearend']+1));
				}
				else{
					$enddate = strtotime('01-'.($intmonthend+1).'-'.$_POST['yearend']);
				}
				$enddate--;
				
				$startmonth = date("M Y",$startdate);
				$endmonth = date("M Y",$enddate);
				
				$sqls = '';
				if (!empty($_POST['suppliercode'])){
					$sqls = " AND suppliercode='".$_POST['suppliercode']."'";
				}
				
				/*if ($statususer == 1){
					$getalltr = $db->fetch_one("SELECT SUM(totalbuy) AS totalbuys FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql." ORDER BY buydate");
					$totaltransaction = $getalltr['totalbuys'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql." ORDER BY totalbuy");
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
							$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE buyid IN (".implode(",",$arrhbid).") ORDER BY buydate");
						}
					}
				}
				else{*/
					$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql.$sqls." ORDER BY buydate");
				//}
				$datego = '';
				$temptrx = 0;
				$tempdisc = 0;
				$temptax = 0;
				$temptotal = 0;
				$trx = 0;
				$disc = 0;
				$tax = 0;
				$total = 0;
				if (sizeof($dbbuy) > 0){
					foreach ($dbbuy as $ds){
						$supplier->setCode($ds['suppliercode']);
						$getsupplier = $supplier->getsupplierDetail();
						
						$datenow = date("M-Y",$ds['buydate']);
						$datenowdt = date("d-M-Y",$ds['buydate']);
						$datenowdd = date("d-M-Y",$ds['duedate']);
						if ($datego != $datenow){
							if ($total != 0){
								$list .= '
									<tr>
										<td align="center"><b>'.$temptrx.'</b></td>
										<td align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
										<td align="right" height="30"><b>'.number_format($tempdisc,2,",",".").'</b></td>
										<td align="right" height="30"><b>'.number_format($temptax,2,",",".").'</b></td>
										<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
										<td align="right" height="30"></td>
									</tr>
								';
							}
							$list .= '
								<tr>
									<td width="100%" align="left" colspan="8" valign="bottom" height="30">
									&nbsp;<b>'.$datenow.'</b></td>
								</tr>
							';
							$temptrx = 0;
							$tempdisc = 0;
							$temptax = 0;
							$temptotal = 0;
							$datego = $datenow;
						}
						$discvalue = ($ds['disc'] / 100) * $ds['totals'];
						$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
						
						if ($statususer == 1){
							//get detail purchase
							$purchase->setBuyNo($ds['buyno']);
							$dbdetailbuy = $purchase->getDetailBuy();
							$listinner = '';
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
						
							$discvalue = $ds['disc'] / 100 * $subtotalfk;
							$totalafgdiscfk = $subtotalfk - $discvalue;
							$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
							$ds['totalbuy'] = $totalafgdiscfk + $taxvalue;
						}
						
						if ($ds['paid'] == 1){
							$printstatus = 'Lunas';
							$getpaydebt = $db->fetch_one("SELECT * FROM detailpayment WHERE hsid='".$ds['buyid']."' AND types='buy'");
							$payment->setId($getpaydebt['hpid']);
							$alldetailrepay = $payment->getDetailRePayment();
							if (sizeof($alldetailrepay) > 0){
								$detailsrp = '<ul>';
								foreach ($alldetailrepay as $adr){
									$detailsrp .= '<li>'.$repaystatus[$adr['types']];
									if (!empty($adr['bank'])){
										$detailsrp .= ' ('.htmlspecialchars($adr['bank']);
										if (!empty($adr['accnumber'])){
											$detailsrp .= ' - '.htmlspecialchars($adr['accnumber']);
										}
										if (!empty($adr['accname'])){
											$detailsrp .= ' a/n '.htmlspecialchars($adr['accname']);
										}
										$detailsrp .= ')';
									}
									$detailsrp .= ' : '.number_format($adr['totals'],2,",",".").'</li>';
								}
								$detailsrp .= '</ul>';
								$printstatus .= $detailsrp;
							}
						}
						else{
							$printstatus = 'Hutang';
						}
						
						$list .= '
							<tr>
								<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
								<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyno']).'<br>'.htmlspecialchars($ds['orderno']).'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($getsupplier['suppliername']).'</td>
								<td align="right" height="30" class="detailitem">'.number_format($discvalue,2,",",".").'</td>
								<td align="right" height="30" class="detailitem">'.number_format($taxvalue,2,",",".").'</td>
								<td align="right" height="30" class="detailitem">'.number_format($ds['totalbuy'],2,",",".").'</td>
								<td align="left" height="30" class="detailitem">'.$printstatus.'</td>
							</tr>
						';
						$temptrx++;
						$tempdisc += $discvalue;
						$temptax += $taxvalue;
						$temptotal += $ds['totalbuy'];
						$trx++;
						$disc += $discvalue;
						$tax += $taxvalue;
						$total += $ds['totalbuy'];
						
						//get return purchase
						$totalbuyr = 0;
						$returnbuy = $purchaser->getBuyRFromBuy($ds['buyno']);
						if (sizeof($returnbuy) > 0){
							foreach ($returnbuy as $rsl){
								if ($statususer == 1){
									$rsl['quantityf'] = discq($rsl['quantityf']);
									
									$totalbuyrfk = $rsl['quantityf'] * $rsl['buyrprice'];
									$totaldiscfk = $rsl['detaildisc'] / 100 * $totalbuyrfk;
									$tempstd = $totalbuyrfk - $totaldiscfk;
									$tempstd = $tempstd - $rsl['extdisc'] / 100 * $tempstd;
									$tempstd = $tempstd + $rsl['tax'] / 100 * $tempstd;
									$rsl['totalbuyrad'] = $tempstd;								
								}
								$totalbuyr += $rsl['totalbuyrad'];
							}						
							$list .= '
								<tr>
									<td align="center" height="30" class="detailitem"></td>
									<td align="center" height="30" class="detailitem"></td>
									<td align="left" height="30" class="detailitem" colspan="4">Retur Beli : '.htmlspecialchars($ds['orderno']).'</td>
									<td align="right" height="30" class="detailitem">- '.number_format($totalbuyr,2,",",".").'</td>
									<td align="right" height="30" class="detailitem"></td>
								</tr>
							';
							$temptotal -= $totalbuyr;
							$total -= $totalbuyr;
						}
					}
					$listalls = '
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
							<th align="center" width="13%" bgcolor="#DEDEDE">NO FAKTUR / NO BON</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">NAMA SUPPLIER</th>
							<th align="center" width="11%" bgcolor="#DEDEDE">DISKON</th>
							<th align="center" width="11%" bgcolor="#DEDEDE">PPN</th>
							<th align="center" width="15%" bgcolor="#DEDEDE">TOTAL</th>
							<th align="center" width="12%" bgcolor="#DEDEDE">STATUS</th>
						</tr>
						'.$list.'
						<tr>
							<td align="center"><b>'.$temptrx.'</b></td>
							<td align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
							<td align="right" height="30"><b>'.number_format($tempdisc,2,",",".").'</b></td>
							<td align="right" height="30"><b>'.number_format($temptax,2,",",".").'</b></td>
							<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
							<td align="right" height="30"></td>
						</tr>
						<tr>
							<td align="center"><b>'.$trx.'</b></td>
							<td align="left" colspan="3">&nbsp;<b>GRAND TOTAL</b></td>
							<td align="right" height="30"><b>'.number_format($disc,2,",",".").'</b></td>
							<td align="right" height="30"><b>'.number_format($tax,2,",",".").'</b></td>
							<td align="right" height="30"><b>'.number_format($total,2,",",".").'</b></td>
							<td align="right" height="30"></td>
						</tr>
						</table>
					';
				}
			}
		}
		else if ($_POST['basedon'] == 'supplier'){
			if (!empty($_POST['monthstart']) && !empty($_POST['yearstart']) && !empty($_POST['monthend']) && !empty($_POST['yearend'])){
				$printtemplate = 'reportbuym';
				$startdate = strtotime('01-'.$_POST['monthstart'].'-'.$_POST['yearstart']);
				$intmonthstart = intval($_POST['monthstart']);
				$intmonthend = intval($_POST['monthend']);
				if ($intmonthend == 12){
					$enddate = strtotime('01-01-'.($_POST['yearend']+1));
				}
				else{
					$enddate = strtotime('01-'.($intmonthend+1).'-'.$_POST['yearend']);
				}
				$enddate--;
				
				$startmonth = date("M Y",$startdate);
				$endmonth = date("M Y",$enddate);
				
				if (!empty($_POST['suppliercode'])){
					$allsupplier = $db->fetch_all("SELECT * FROM supplier WHERE suppliercode = '".$_POST['suppliercode']."'");
				}
				else{
					$allsupplier = $supplier->getListsupplier('partial');
				}
				
				if (sizeof($allsupplier) > 0){
					$trx = 0;
					$disc = 0;
					$tax = 0;
					$total = 0;
					foreach ($allsupplier as $ac){
						$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."') AND suppliercode = '".$ac['suppliercode']."'".$sql." ORDER BY buydate");
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						if (sizeof($dbbuy) > 0){
							$list .= '
								<tr>
									<td width="100%" align="left" colspan="8" valign="bottom" height="30">
									<b>'.htmlspecialchars($ac['suppliercode'].' - '.$ac['suppliername']).'</b></td>
								</tr>
							';
							foreach ($dbbuy as $ds){
								$datenowdt = date("d-M-Y",$ds['buydate']);
								$datenowdd = date("d-M-Y",$ds['duedate']);
								$discvalue = ($ds['disc'] / 100) * $ds['totals'];
								$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
								
								if ($statususer == 1){
									//get detail purchase
									$purchase->setBuyNo($ds['buyno']);
									$dbdetailbuy = $purchase->getDetailBuy();
									$listinner = '';
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
								
									$discvalue = $ds['disc'] / 100 * $subtotalfk;
									$totalafgdiscfk = $subtotalfk - $discvalue;
									$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
									$ds['totalbuy'] = $totalafgdiscfk + $taxvalue;
								}
								
								if ($ds['paid'] == 1){
									$printstatus = 'Lunas';
									$getpaydebt = $db->fetch_one("SELECT * FROM detailpayment WHERE hsid='".$ds['buyid']."' AND types='buy'");
									$payment->setId($getpaydebt['hpid']);
									$alldetailrepay = $payment->getDetailRePayment();
									if (sizeof($alldetailrepay) > 0){
										$detailsrp = '<ul>';
										foreach ($alldetailrepay as $adr){
											$detailsrp .= '<li>'.$repaystatus[$adr['types']];
											if (!empty($adr['bank'])){
												$detailsrp .= ' ('.htmlspecialchars($adr['bank']);
												if (!empty($adr['accnumber'])){
													$detailsrp .= ' - '.htmlspecialchars($adr['accnumber']);
												}
												if (!empty($adr['accname'])){
													$detailsrp .= ' a/n '.htmlspecialchars($adr['accname']);
												}
												$detailsrp .= ')';
											}
											$detailsrp .= ' : '.number_format($adr['totals'],2,",",".").'</li>';
										}
										$detailsrp .= '</ul>';
										$printstatus .= $detailsrp;
									}
								}
								else{
									$printstatus = 'Hutang';
								}
								
								$list .= '
									<tr>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyno']).'<br>'.htmlspecialchars($ds['orderno']).'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ac['suppliername']).'</td>
										<td align="right" height="30" class="detailitem">'.number_format($discvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($taxvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($ds['totalbuy'],2,",",".").'</td>
										<td align="left" height="30" class="detailitem">'.$printstatus.'</td>
									</tr>
								';
								$temptrx++;
								$tempdisc += $discvalue;
								$temptax += $taxvalue;
								$temptotal += $ds['totalbuy'];
								$trx++;
								$disc += $discvalue;
								$tax += $taxvalue;
								$total += $ds['totalbuy'];
								
								//get return purchase
								$totalbuyr = 0;
								$returnbuy = $purchaser->getBuyRFromBuy($ds['buyno']);
								if (sizeof($returnbuy) > 0){
									foreach ($returnbuy as $rsl){
										if ($statususer == 1){
											$rsl['quantityf'] = discq($rsl['quantityf']);
											
											$totalbuyrfk = $rsl['quantityf'] * $rsl['buyrprice'];
											$totaldiscfk = $rsl['detaildisc'] / 100 * $totalbuyrfk;
											$tempstd = $totalbuyrfk - $totaldiscfk;
											$tempstd = $tempstd - $rsl['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $rsl['tax'] / 100 * $tempstd;
											$rsl['totalbuyrad'] = $tempstd;								
										}
										$list .= '
											<tr>
												<td align="center" height="30" class="detailitem">'.date("d-M-Y",$rsl['buyrdate']).'</td>
												<td align="center" height="30" class="detailitem"></td>
												<td align="left" height="30" class="detailitem" colspan="4">Retur Beli : '.htmlspecialchars($ds['orderno']).'</td>
												<td align="right" height="30" class="detailitem">- '.number_format($rsl['totalbuyrad'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem"></td>
											</tr>
										';
										$totalbuyr += $rsl['totalbuyrad'];
									}						
									$temptotal -= $totalbuyr;
									$total -= $totalbuyr;
								}
							}
							$list .= '
								<tr>
									<td align="center"><b>'.$temptrx.'</b></td>
									<td align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
									<td align="right" height="30"><b>'.number_format($tempdisc,2,",",".").'</b></td>
									<td align="right" height="30"><b>'.number_format($temptax,2,",",".").'</b></td>
									<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
									<td align="right" height="30"></td>
								</tr>
							';
						}
					}
					$listalls = '
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
							<th align="center" width="13%" bgcolor="#DEDEDE">NO FAKTUR / NO BON</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">NAMA SUPPLIER</th>
							<th align="center" width="11%" bgcolor="#DEDEDE">DISKON</th>
							<th align="center" width="11%" bgcolor="#DEDEDE">PPN</th>
							<th align="center" width="15%" bgcolor="#DEDEDE">TOTAL</th>
							<th align="center" width="12%" bgcolor="#DEDEDE">STATUS</th>
						</tr>
						'.$list.'
						<tr>
							<td align="center"><b>'.$trx.'</b></td>
							<td align="left" colspan="3">&nbsp;<b>GRAND TOTAL</b></td>
							<td align="right" height="30"><b>'.number_format($disc,2,",",".").'</b></td>
							<td align="right" height="30"><b>'.number_format($tax,2,",",".").'</b></td>
							<td align="right" height="30"><b>'.number_format($total,2,",",".").'</b></td>
							<td align="right" height="30"></td>
						</tr>
						</table>
					';
				}
			}
		}
		else if ($_POST['basedon'] == 'trtype'){
			if (!empty($_POST['monthstart']) && !empty($_POST['yearstart']) && !empty($_POST['monthend']) && !empty($_POST['yearend'])){
				$printtemplate = 'reportbuym';
				$startdate = strtotime('01-'.$_POST['monthstart'].'-'.$_POST['yearstart']);
				$intmonthstart = intval($_POST['monthstart']);
				$intmonthend = intval($_POST['monthend']);
				if ($intmonthend == 12){
					$enddate = strtotime('01-01-'.($_POST['yearend']+1));
				}
				else{
					$enddate = strtotime('01-'.($intmonthend+1).'-'.$_POST['yearend']);
				}
				$enddate--;
				
				$startmonth = date("M Y",$startdate);
				$endmonth = date("M Y",$enddate);
				
				$sqls = '';
				if (!empty($_POST['suppliercode'])){
					$sqls = " AND suppliercode='".$_POST['suppliercode']."'";
				}
				
				if (sizeof($arrtype) > 0){
					$gtrx = 0;
					$gdisc = 0;
					$gtax = 0;
					$gtotal = 0;
					foreach ($arrtype as $keyp => $artp){
						$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE trtype='".$keyp."' AND (buydate >= '".$startdate."' AND buydate <= '".$enddate."')".$sql.$sqls." ORDER BY buydate");
						$datego = '';
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						$trx = 0;
						$disc = 0;
						$tax = 0;
						$total = 0;
						$list = '';
						if (sizeof($dbbuy) > 0){
							foreach ($dbbuy as $ds){
								$supplier->setCode($ds['suppliercode']);
								$getsupplier = $supplier->getsupplierDetail();
								
								$datenow = date("M-Y",$ds['buydate']);
								$datenowdt = date("d-M-Y",$ds['buydate']);
								$datenowdd = date("d-M-Y",$ds['duedate']);
								if ($datego != $datenow){
									if ($total != 0){
										$list .= '
											<tr>
												<td align="center"><b>'.$temptrx.'</b></td>
												<td align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
												<td align="right" height="30"><b>'.number_format($tempdisc,2,",",".").'</b></td>
												<td align="right" height="30"><b>'.number_format($temptax,2,",",".").'</b></td>
												<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
												<td align="right" height="30"></td>
											</tr>
										';
									}
									$list .= '
										<tr>
											<td width="100%" align="left" colspan="8" valign="bottom" height="30">
											&nbsp;<b>'.$datenow.'</b></td>
										</tr>
									';
									$temptrx = 0;
									$tempdisc = 0;
									$temptax = 0;
									$temptotal = 0;
									$datego = $datenow;
								}
								$discvalue = ($ds['disc'] / 100) * $ds['totals'];
								$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
								
								if ($statususer == 1){
									//get detail purchase
									$purchase->setBuyNo($ds['buyno']);
									$dbdetailbuy = $purchase->getDetailBuy();
									$listinner = '';
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
								
									$discvalue = $ds['disc'] / 100 * $subtotalfk;
									$totalafgdiscfk = $subtotalfk - $discvalue;
									$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
									$ds['totalbuy'] = $totalafgdiscfk + $taxvalue;
								}
								
								if ($ds['paid'] == 1){
									$printstatus = 'Lunas';
									$getpaydebt = $db->fetch_one("SELECT * FROM detailpayment WHERE hbid='".$ds['buyid']."' AND types='buy'");
									$payment->setId($getpaydebt['hpid']);
									$alldetailrepay = $payment->getDetailRePayment();
									if (sizeof($alldetailrepay) > 0){
										$detailsrp = '<ul>';
										foreach ($alldetailrepay as $adr){
											$detailsrp .= '<li>'.$repaystatus[$adr['types']];
											if (!empty($adr['bank'])){
												$detailsrp .= ' ('.htmlspecialchars($adr['bank']);
												if (!empty($adr['accnumber'])){
													$detailsrp .= ' - '.htmlspecialchars($adr['accnumber']);
												}
												if (!empty($adr['accname'])){
													$detailsrp .= ' a/n '.htmlspecialchars($adr['accname']);
												}
												$detailsrp .= ')';
											}
											$detailsrp .= ' : '.number_format($adr['totals'],2,",",".").'</li>';
										}
										$detailsrp .= '</ul>';
										$printstatus .= $detailsrp;
									}
								}
								else{
									$printstatus = 'Hutang';
								}
								
								$list .= '
									<tr>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyno']).'<br>'.htmlspecialchars($ds['orderno']).'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($getsupplier['suppliername']).'</td>
										<td align="right" height="30" class="detailitem">'.number_format($discvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($taxvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($ds['totalbuy'],2,",",".").'</td>
										<td align="left" height="30" class="detailitem">'.$printstatus.'</td>
									</tr>
								';
								$temptrx++;
								$tempdisc += $discvalue;
								$temptax += $taxvalue;
								$temptotal += $ds['totalbuy'];
								$trx++;
								$disc += $discvalue;
								$tax += $taxvalue;
								$total += $ds['totalbuy'];
								$gtrx++;
								$gdisc += $discvalue;
								$gtax += $taxvalue;
								$gtotal += $ds['totalbuy'];
								
								//get return purchase
								$totalbuyr = 0;
								$returnbuy = $purchaser->getBuyRFromBuy($ds['buyno']);
								if (sizeof($returnbuy) > 0){
									foreach ($returnbuy as $rsl){
										if ($statususer == 1){
											$rsl['quantityf'] = discq($rsl['quantityf']);
											
											$totalbuyrfk = $rsl['quantityf'] * $rsl['buyrprice'];
											$totaldiscfk = $rsl['detaildisc'] / 100 * $totalbuyrfk;
											$tempstd = $totalbuyrfk - $totaldiscfk;
											$tempstd = $tempstd - $rsl['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $rsl['tax'] / 100 * $tempstd;
											$rsl['totalbuyrad'] = $tempstd;								
										}
										$totalbuyr += $rsl['totalbuyrad'];
									}						
									$list .= '
										<tr>
											<td align="center" height="30" class="detailitem"></td>
											<td align="center" height="30" class="detailitem"></td>
											<td align="left" height="30" class="detailitem" colspan="4">Retur Beli : '.htmlspecialchars($ds['orderno']).'</td>
											<td align="right" height="30" class="detailitem">- '.number_format($totalbuyr,2,",",".").'</td>
											<td align="right" height="30" class="detailitem"></td>
										</tr>
									';
									$temptotal -= $totalbuyr;
									$total -= $totalbuyr;
									$gtotal -= $totalbuyr;
								}
							}
							$listalls .= '
								<div align="center">
								<h2>'.$artp.'</h2></div>
								<table border="1" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
									<th align="center" width="13%" bgcolor="#DEDEDE">NO FAKTUR / NO BON</th>
									<th align="center" width="18%" bgcolor="#DEDEDE">NAMA SUPPLIER</th>
									<th align="center" width="11%" bgcolor="#DEDEDE">DISKON</th>
									<th align="center" width="11%" bgcolor="#DEDEDE">PPN</th>
									<th align="center" width="15%" bgcolor="#DEDEDE">TOTAL</th>
									<th align="center" width="12%" bgcolor="#DEDEDE">STATUS</th>
								</tr>
								'.$list.'
								<tr>
									<td align="center"><b>'.$temptrx.'</b></td>
									<td align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
									<td align="right" height="30"><b>'.number_format($tempdisc,2,",",".").'</b></td>
									<td align="right" height="30"><b>'.number_format($temptax,2,",",".").'</b></td>
									<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
									<td align="right" height="30"></td>
								</tr>
								<tr>
									<td align="center"><b>'.$trx.'</b></td>
									<td align="left" colspan="3">&nbsp;<b>TOTAL PEMBELIAN '.$artp.'</b></td>
									<td align="right" height="30"><b>'.number_format($disc,2,",",".").'</b></td>
									<td align="right" height="30"><b>'.number_format($tax,2,",",".").'</b></td>
									<td align="right" height="30"><b>'.number_format($total,2,",",".").'</b></td>
									<td align="right" height="30"></td>
								</tr>
								</table>
							';
						}
					}
					$listalls .= '
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
						<td align="center" width="10%"><b>'.$gtrx.'</b></td>
						<td align="left" width="41%">&nbsp;<b>TOTAL PEMBELIAN KESELURUHAN</b></td>
						<td align="right" width="11%" height="30"><b>'.number_format($gdisc,2,",",".").'</b></td>
						<td align="right" width="11%" height="30"><b>'.number_format($gtax,2,",",".").'</b></td>
						<td align="right" width="15%" height="30"><b>'.number_format($gtotal,2,",",".").'</b></td>
						<td align="right" width="12%" height="30"></td>
						</tr>
						</table>
					';
				}
			}
		}
		else{
			$printtemplate = 'reportbuyminit';
			$yearnow = date("Y");
			$diffyear = $yearnow - $general['installyear'];
			$cbyear = '';
			if ($diffyear >= 0){
				for ($i = 0; $i <= $diffyear; $i++){
					$cbyear .= '<option value="'.($yearnow-$i).'">'.($yearnow-$i).'</option>';
				}
			}
		}
	}
	else{
		redirecting('index.php');
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>