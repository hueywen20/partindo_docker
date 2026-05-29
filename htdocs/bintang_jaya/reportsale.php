<?php
	require_once "global.php";
	
	require_once "class/customer.php";
	require_once "class/customer.php";
	require_once "class/Sale.php";
	require_once "class/SaleR.php";
	require_once "class/Purchase.php";
	require_once "class/PurchaseR.php";
	require_once "class/Payment.php";
	require_once "class/Stock.php";
	$stock = new Stock();
	$customer = new customer();
	$sale = new Sale();
	$saler = new SaleR();
	$purchase = new Purchase();
	$purchaser = new PurchaseR();
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
	if ($_GET['view'] == 'periodcustomer'){
		
		if (empty($useraccess['report_salepc'])){
			redirecting('index.php');
		}
		
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			$sqls = '';
			if (!empty($_POST['customercode'])){
				$sqls = " AND customercode='".$_POST['customercode']."'";
			}
			
			if ($_POST['basedon'] == 'saledate' || !empty($sqls)){
				$printtemplate = 'reportsalepc';
				/*if ($statususer == 1){
					$getalltr = $db->fetch_one("SELECT SUM(totalsale) AS totalsales FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."')".$sql." ORDER BY saledate");
					$totaltransaction = $getalltr['totalsales'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."')".$sql." ORDER BY totalsale");
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
					$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."')".$sql.$sqls." ORDER BY saledate");
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
				if (sizeof($dbsale) > 0){
					foreach ($dbsale as $ds){
						$customer->setCode($ds['customercode']);
						$getcustomer = $customer->getcustomerDetail();
						
						$datenow = date("d-M-Y",$ds['saledate']);
						$datenowdt = date("d-M-Y",$ds['saledate']);
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
									<td align="center" colspan="4" height="35" valign="bottom" bgcolor="#EEE">
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
						//get detail sale
						$sale->setSaleNo($ds['saleno']);
						$dbdetailsale = $sale->getDetailSale();
						$listinner = '';
						if (sizeof($dbdetailsale) > 0){
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
							foreach ($dbdetailsale as $dbds){
								$stock->setCode($dbds['stockcode']);
								$fsk = $stock->getFirstStock();
								$totalbuysall = 0;
								$totalsalesall = 0;
								$dbdetailitem = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dbds['dsid']."'");
								if (sizeof($dbdetailitem) > 0){
									if ($fsk['assembly'] == 1){
										if ($statususer == 1){
											$dbds['quantity'] = floor((100-$discount['extradisc'])/100 * $dbds['quantity']);
											if ($dbds['quantity'] < 1){
												$dbds['quantity'] = 1;
											}
										}
										
										$dblogas = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbds['dsid']."' AND stockcode='".$dbds['stockcode']."'");
										$totalbuysall += $dblogas['price'] * $dbds['quantity'];
										$totalsalesall += $dbds['realsaleprice'] * $dbds['quantity'];
									}
									else if ($fsk['assembly'] == 2){
										foreach ($dbdetailitem as $dbdi){
											if ($dbdi['tabledbid'] != 'logdeassembly'){
												continue;
											}
											$dbdbid = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$dbdi['dbid']."'");
									
											if ($statususer == 1){
												$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
												if ($dbdi['quantity'] < 1){
													$dbdi['quantity'] = 1;
												}
											}
											
											$totalbuysall += $dbdbid['price']* $dbdi['quantity'];
											$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
										}
									}
									else{
										foreach ($dbdetailitem as $dbdi){
											if ($dbdi['dbid'] == -1){
												$dbdbid['realbuyprice'] = $fsk['buyprice'];
												$dbdbid['stockcode'] = $fsk['stockcode'];
												$dbdbid['stockname'] = $fsk['generalname'];
											}
											else{
												$dbdbid = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbdi['dbid']."'");
											}
									
											if ($statususer == 1){
												$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
												if ($dbdi['quantity'] < 1){
													$dbdi['quantity'] = 1;
												}
											}
											
											$totalbuysall += $dbdbid['realbuyprice']* $dbdi['quantity'];
											$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
										}
									}
								}
								
								if ($totalbuysall <= 0){
									$profitloss = $totalsalesall;
									$percentages = $profitloss * 100;
								}
								else{
									$profitloss = $totalsalesall - $totalbuysall;
									$percentages = ($profitloss / $totalbuysall) * 100;
								}
								
								if ($statususer == 1){
									$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
									if ($dbds['quantityf'] < 1){
										$dbds['quantityf'] = 1;
									}
									
									$totalbuyfk = $dbds['quantityf'] * $dbds['saleprice'];
									$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
									$dbds['totalsalead'] = ($totalbuyfk - $totaldiscfk);
									$subtotalfk += $dbds['totalsalead'];
								}
								
								$listinner .= '
									<tr>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['saleprice'],2,",",".").'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalsalead'],2,",",".").'<br>( <i>'.number_format($percentages,2,",",".").'%</i> )</td>
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
							$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
						}
														
						//get return sale
						$tempsaler = 0;
						$gsr['totalsaler'] = 0;
						$getsaler = $saler->getSaleRFromSale($ds['saleno']);
						if (sizeof($getsaler) > 0){
							$listinner .= '
								<tr>
									<td width="10%" height="25" align="left"></td>
									<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR JUAL</b></i></td>
								</tr>
								<tr>
									<td width="10%" align="left"></td>
									<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
									<table border="1" cellpadding="0" width="100%" cellspacing="0">
							';
							
							foreach ($getsaler as $gsr){											
								if ($statususer == 1){
									$gsr['quantityf'] = discq($gsr['quantityf']);
									$totalsalerfk = $gsr['quantityf'] * $gsr['salerprice'];
									$totaldiscfk = $gsr['detaildisc'] / 100 * $totalsalerfk;
									$tempstd = $totalsalerfk - $totaldiscfk;
									$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
									$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
									$gsr['totalsalerad'] = $totalsalerfk - $totaldiscfk;
								}
								$tempsaler += $gsr['totalsalerad'];
								
								$listinner .= '
									<tr>
										<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.htmlspecialchars($gsr['stockcode']).'<br>
										( '.date("d-M-Y",$gsr['salerdate']).' )</td>
										<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
										<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
										<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
										<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
										<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
										<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['salerprice'],2,",",".").'</td>
										<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
										<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalsalerad'],2,",",".").'</td>
									</tr>
								';
							}
							
							$listinner .= '
									</table></td>
								</tr>
							';
							
							$gsr['totalsaler'] = $tempsaler;
						}
						
						$totalfinal = ($ds['totalsale']-$gsr['totalsaler']);
						
						$list .= '
							<tr>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
								<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
								<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
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
					$listalls = '
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="10%" bgcolor="#DEDEDE">NO FAKTUR</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
							<th align="center" width="70%" bgcolor="#DEDEDE">NAMA CUSTOMER</th>
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
			else if ($_POST['basedon'] == 'customer'){
				$printtemplate = 'reportsalepcpercustomer';
				
				$allcustomer = $customer->getListcustomer('partial');
				if (sizeof($allcustomer) > 0){
					$trx = 0;
					$disc = 0;
					$tax = 0;
					$total = 0;
					foreach ($allcustomer as $asp){
						$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."') AND customercode='".$asp['customercode']."'".$sql." ORDER BY saledate");
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						$list = '';
						if (sizeof($dbsale) > 0){
							foreach ($dbsale as $ds){
								$customer->setCode($ds['customercode']);
								$getcustomer = $customer->getcustomerDetail();
								
								$datenow = date("d-M-Y",$ds['saledate']);
								$datenowdt = date("d-M-Y",$ds['saledate']);
								$datenowdd = date("d-M-Y",$ds['duedate']);

								$discvalue = ($ds['disc'] / 100) * $ds['totals'];
								$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
							
								$listinner = '';
								//get detail sale
								$sale->setSaleNo($ds['saleno']);
								$dbdetailsale = $sale->getDetailSale();
								if (sizeof($dbdetailsale) > 0){
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
									foreach ($dbdetailsale as $dbds){
										$stock->setCode($dbds['stockcode']);
										$fsk = $stock->getFirstStock();
										$totalbuysall = 0;
										$totalsalesall = 0;
										$dbdetailitem = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dbds['dsid']."'");
										if (sizeof($dbdetailitem) > 0){
											if ($fsk['assembly'] == 1){
												if ($statususer == 1){
													$dbds['quantity'] = floor((100-$discount['extradisc'])/100 * $dbds['quantity']);
													if ($dbds['quantity'] < 1){
														$dbds['quantity'] = 1;
													}
												}
												
												$dblogas = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbds['dsid']."' AND stockcode='".$dbds['stockcode']."'");
												$totalbuysall += $dblogas['price'] * $dbds['quantity'];
												$totalsalesall += $dbds['realsaleprice'] * $dbds['quantity'];
											}
											else if ($fsk['assembly'] == 2){
												foreach ($dbdetailitem as $dbdi){
													if ($dbdi['tabledbid'] != 'logdeassembly'){
														continue;
													}
													$dbdbid = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$dbdi['dbid']."'");
											
													if ($statususer == 1){
														$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
														if ($dbdi['quantity'] < 1){
															$dbdi['quantity'] = 1;
														}
													}
													
													$totalbuysall += $dbdbid['price']* $dbdi['quantity'];
													$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
												}
											}
											else{
												foreach ($dbdetailitem as $dbdi){
													if ($dbdi['dbid'] == -1){
														$dbdbid['realbuyprice'] = $fsk['buyprice'];
														$dbdbid['stockcode'] = $fsk['stockcode'];
														$dbdbid['stockname'] = $fsk['generalname'];
													}
													else{
														$dbdbid = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbdi['dbid']."'");
													}
											
													if ($statususer == 1){
														$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
														if ($dbdi['quantity'] < 1){
															$dbdi['quantity'] = 1;
														}
													}
													
													$totalbuysall += $dbdbid['realbuyprice']* $dbdi['quantity'];
													$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
												}
											}
										}
										
										if ($totalbuysall <= 0){
											$profitloss = $totalsalesall;
											$percentages = $profitloss * 100;
										}
										else{
											$profitloss = $totalsalesall - $totalbuysall;
											$percentages = ($profitloss / $totalbuysall) * 100;
										}
											
										if ($statususer == 1){
											$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
											if ($dbds['quantityf'] < 1){
												$dbds['quantityf'] = 1;
											}
											
											$totalbuyfk = $dbds['quantityf'] * $dbds['saleprice'];
											$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
											$dbds['totalsalead'] = ($totalbuyfk - $totaldiscfk);
											$subtotalfk += $dbds['totalsalead'];
										}
										
										$listinner .= '
											<tr>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['saleprice'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalsalead'],2,",",".").'<br>( <i>'.number_format($percentages,2,",",".").'%</i> )</td>
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
									$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
								}
								
								//get return sale
								$tempsaler = 0;
								$gsr['totalsaler'] = 0;
								$getsaler = $saler->getSaleRFromSale($ds['saleno']);
								if (sizeof($getsaler) > 0){
									$listinner .= '
										<tr>
											<td width="10%" height="25" align="left"></td>
											<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR JUAL</b></i></td>
										</tr>
										<tr>
											<td width="10%" align="left"></td>
											<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
											<table border="1" cellpadding="0" width="100%" cellspacing="0">
									';
									
									foreach ($getsaler as $gsr){											
										if ($statususer == 1){
											$gsr['quantityf'] = discq($gsr['quantityf']);
											$totalsalerfk = $gsr['quantityf'] * $gsr['salerprice'];
											$totaldiscfk = $gsr['detaildisc'] / 100 * $totalsalerfk;
											$tempstd = $totalsalerfk - $totaldiscfk;
											$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
											$gsr['totalsalerad'] = $tempstd;
										}
										$tempsaler += $gsr['totalsalerad'];
									
										$listinner .= '
											<tr>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
												'.htmlspecialchars($gsr['stockcode']).'<br>
												( '.date("d-M-Y",$gsr['salerdate']).' )</td>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
												<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['salerprice'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalsalerad'],2,",",".").'</td>
											</tr>
										';
									}
									
									$listinner .= '
											</table></td>
										</tr>
									';
									
									$gsr['totalsaler'] = $tempsaler;
								}
								
								$totalfinal = ($ds['totalsale']-$gsr['totalsaler']);
								
								$list .= '
									<tr>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
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
								<span style="float: left">Customer : <b>'.$asp['customercode'].' - '.$asp['customername'].'</b></span>
								Tanggal Cetak : '.$printdate.'</div>
								<div align="center" style="width: 100%; padding-bottom: 20px; border-bottom: 1px dotted #000; clear: both">
								<table border="1" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<th align="center" width="10%" bgcolor="#DEDEDE">NO FAKTUR</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
									<th align="center" width="70%" bgcolor="#DEDEDE">NAMA CUSTOMER</th>
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
				$printtemplate = 'reportsalepc';
				if (sizeof($arrtype) > 0){
					$gtrx = 0;
					$gdisc = 0;
					$gtax = 0;
					$gtotal = 0;
					foreach ($arrtype as $keyp => $artp){
						$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE trtype='".$keyp."' AND (saledate >= '".$startdate."' AND saledate <= '".$enddate."')".$sql." ORDER BY saledate");
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
						if (sizeof($dbsale) > 0){
							foreach ($dbsale as $ds){
								$customer->setCode($ds['customercode']);
								$getcustomer = $customer->getcustomerDetail();
								
								$datenow = date("d-M-Y",$ds['saledate']);
								$datenowdt = date("d-M-Y",$ds['saledate']);
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
											<td align="center" colspan="4" height="35" valign="bottom" bgcolor="#EEE">
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
								//get detail sale
								$sale->setSaleNo($ds['saleno']);
								$dbdetailsale = $sale->getDetailSale();
								$listinner = '';
								if (sizeof($dbdetailsale) > 0){
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
									foreach ($dbdetailsale as $dbds){
										$stock->setCode($dbds['stockcode']);
										$fsk = $stock->getFirstStock();
										$totalbuysall = 0;
										$totalsalesall = 0;
										$dbdetailitem = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dbds['dsid']."'");
										if (sizeof($dbdetailitem) > 0){
											if ($fsk['assembly'] == 1){
												if ($statususer == 1){
													$dbds['quantity'] = floor((100-$discount['extradisc'])/100 * $dbds['quantity']);
													if ($dbds['quantity'] < 1){
														$dbds['quantity'] = 1;
													}
												}
												
												$dblogas = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbds['dsid']."' AND stockcode='".$dbds['stockcode']."'");
												$totalbuysall += $dblogas['price'] * $dbds['quantity'];
												$totalsalesall += $dbds['realsaleprice'] * $dbds['quantity'];
											}
											else if ($fsk['assembly'] == 2){
												foreach ($dbdetailitem as $dbdi){
													if ($dbdi['tabledbid'] != 'logdeassembly'){
														continue;
													}
													$dbdbid = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$dbdi['dbid']."'");
											
													if ($statususer == 1){
														$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
														if ($dbdi['quantity'] < 1){
															$dbdi['quantity'] = 1;
														}
													}
													
													$totalbuysall += $dbdbid['price']* $dbdi['quantity'];
													$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
												}
											}
											else{
												foreach ($dbdetailitem as $dbdi){
													if ($dbdi['dbid'] == -1){
														$dbdbid['realbuyprice'] = $fsk['buyprice'];
														$dbdbid['stockcode'] = $fsk['stockcode'];
														$dbdbid['stockname'] = $fsk['generalname'];
													}
													else{
														$dbdbid = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbdi['dbid']."'");
													}
											
													if ($statususer == 1){
														$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
														if ($dbdi['quantity'] < 1){
															$dbdi['quantity'] = 1;
														}
													}
													
													$totalbuysall += $dbdbid['realbuyprice']* $dbdi['quantity'];
													$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
												}
											}
										}
										
										if ($totalbuysall <= 0){
											$profitloss = $totalsalesall;
											$percentages = $profitloss * 100;
										}
										else{
											$profitloss = $totalsalesall - $totalbuysall;
											$percentages = ($profitloss / $totalbuysall) * 100;
										}
										
										if ($statususer == 1){
											$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
											if ($dbds['quantityf'] < 1){
												$dbds['quantityf'] = 1;
											}
											
											$totalbuyfk = $dbds['quantityf'] * $dbds['saleprice'];
											$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
											$dbds['totalsalead'] = ($totalbuyfk - $totaldiscfk);
											$subtotalfk += $dbds['totalsalead'];
										}
										
										$listinner .= '
											<tr>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
												<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['saleprice'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalsalead'],2,",",".").'<br>( <i>'.number_format($percentages,2,",",".").'%</i> )</td>
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
									$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
								}
																
								//get return sale
								$tempsaler = 0;
								$gsr['totalsaler'] = 0;
								$getsaler = $saler->getSaleRFromSale($ds['saleno']);
								if (sizeof($getsaler) > 0){
									$listinner .= '
										<tr>
											<td width="10%" height="25" align="left"></td>
											<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR JUAL</b></i></td>
										</tr>
										<tr>
											<td width="10%" align="left"></td>
											<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
											<table border="1" cellpadding="0" width="100%" cellspacing="0">
									';
									
									foreach ($getsaler as $gsr){											
										if ($statususer == 1){
											$gsr['quantityf'] = discq($gsr['quantityf']);
											$totalsalerfk = $gsr['quantityf'] * $gsr['salerprice'];
											$totaldiscfk = $gsr['detaildisc'] / 100 * $totalsalerfk;
											$tempstd = $totalsalerfk - $totaldiscfk;
											$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
											$gsr['totalsalerad'] = $tempstd;
										}
										$tempsaler += $gsr['totalsalerad'];
										
										$listinner .= '
											<tr>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
												'.htmlspecialchars($gsr['stockcode']).'<br>
												( '.date("d-M-Y",$gsr['salerdate']).' )</td>
												<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
												<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
												<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['salerprice'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
												<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalsalerad'],2,",",".").'</td>
											</tr>
										';
									}
									
									$listinner .= '
											</table></td>
										</tr>
									';
									
									$gsr['totalsaler'] = $tempsaler;
								}
								
								$totalfinal = ($ds['totalsale']-$gsr['totalsaler']);
								
								$list .= '
									<tr>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
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
								$gdisc += $discvalue;
								$gtax += $taxvalue;
								$gtotal += $totalfinal;
							}
							$listalls .= '
								<div align="center">
								<h2>'.$artp.'</h2></div>
								<table border="1" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<th align="center" width="10%" bgcolor="#DEDEDE">NO FAKTUR</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
									<th align="center" width="70%" bgcolor="#DEDEDE">NAMA CUSTOMER</th>
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
									<td align="right" colspan="2"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL PENJUALAN '.$artp.'</b></td>
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
						<h2>TOTAL PENJUALAN KESELURUHAN</h2></div>
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
			$printtemplate = 'reportsalepcinit';
		}
	}
	else if ($_GET['view'] == 'duedate'){
		
		if (empty($useraccess['report_saledd'])){
			redirecting('index.php');
		}
		
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$printtemplate = 'reportsaledd';
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			$sql = '';
			if (!empty($_POST['customercode'])){
				$sql = " AND customercode='".$_POST['customercode']."'";
			}
			/*if ($statususer == 1){
				$getalltr = $db->fetch_one("SELECT SUM(totalsale) AS totalsales FROM headersale WHERE (duedate >= '".$startdate."' AND duedate <= '".$enddate."')".$sql." ORDER BY duedate");
				$totaltransaction = $getalltr['totalsales'];
				$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
				
				$getalltr = $db->fetch_all("SELECT * FROM headersale WHERE (duedate >= '".$startdate."' AND duedate <= '".$enddate."')".$sql." ORDER BY totalsale");
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
						$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE saleid IN (".implode(",",$arrhbid).")".$sql." ORDER BY duedate");
					}
				}
			}
			else{*/
				$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (duedate >= '".$startdate."' AND duedate <= '".$enddate."')".$sql." ORDER BY duedate");
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
			if (sizeof($dbsale) > 0){
				foreach ($dbsale as $ds){
					$customer->setCode($ds['customercode']);
					$getcustomer = $customer->getcustomerDetail();
					
					$datenow = date("d-M-Y",$ds['duedate']);
					$datenowdt = date("d-M-Y",$ds['saledate']);
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
					//get detail sale
					$sale->setSaleNo($ds['saleno']);
					$dbdetailsale = $sale->getDetailSale();
					$listinner = '';
					if (sizeof($dbdetailsale) > 0){
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
						foreach ($dbdetailsale as $dbds){
							$stock->setCode($dbds['stockcode']);
							$fsk = $stock->getFirstStock();
							$totalbuysall = 0;
							$totalsalesall = 0;
							$dbdetailitem = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dbds['dsid']."'");
							if (sizeof($dbdetailitem) > 0){
								if ($fsk['assembly'] == 1){
									if ($statususer == 1){
										$dbds['quantity'] = floor((100-$discount['extradisc'])/100 * $dbds['quantity']);
										if ($dbds['quantity'] < 1){
											$dbds['quantity'] = 1;
										}
									}
									
									$dblogas = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbds['dsid']."' AND stockcode='".$dbds['stockcode']."'");
									$totalbuysall += $dblogas['price'] * $dbds['quantity'];
									$totalsalesall += $dbds['realsaleprice'] * $dbds['quantity'];
								}
								else if ($fsk['assembly'] == 2){
									foreach ($dbdetailitem as $dbdi){
										if ($dbdi['tabledbid'] != 'logdeassembly'){
											continue;
										}
										$dbdbid = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$dbdi['dbid']."'");
								
										if ($statususer == 1){
											$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
											if ($dbdi['quantity'] < 1){
												$dbdi['quantity'] = 1;
											}
										}
										
										$totalbuysall += $dbdbid['price']* $dbdi['quantity'];
										$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
									}
								}
								else{
									foreach ($dbdetailitem as $dbdi){
										if ($dbdi['dbid'] == -1){
											$dbdbid['realbuyprice'] = $fsk['buyprice'];
											$dbdbid['stockcode'] = $fsk['stockcode'];
											$dbdbid['stockname'] = $fsk['generalname'];
										}
										else{
											$dbdbid = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbdi['dbid']."'");
										}
								
										if ($statususer == 1){
											$dbdi['quantity'] = floor((100-$discount['extradisc'])/100 * $dbdi['quantity']);
											if ($dbdi['quantity'] < 1){
												$dbdi['quantity'] = 1;
											}
										}
										
										$totalbuysall += $dbdbid['realbuyprice']* $dbdi['quantity'];
										$totalsalesall += $dbds['realsaleprice'] * $dbdi['quantity'];
									}
								}
							}
							
							if ($totalbuysall <= 0){
								$profitloss = $totalsalesall;
								$percentages = $profitloss * 100;
							}
							else{
								$profitloss = $totalsalesall - $totalbuysall;
								$percentages = ($profitloss / $totalbuysall) * 100;
							}
							
							if ($statususer == 1){
								$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
								if ($dbds['quantityf'] < 1){
									$dbds['quantityf'] = 1;
								}
								
								$totalbuyfk = $dbds['quantityf'] * $dbds['saleprice'];
								$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
								$dbds['totalsalead'] = ($totalbuyfk - $totaldiscfk);
								$subtotalfk += $dbds['totalsalead'];
							}
							
							$listinner .= '
								<tr>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['saleprice'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalsalead'],2,",",".").'<br>( <i>'.number_format($percentages,2,",",".").'%</i> )</td>
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
						$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
					}
														
					//get return sale
					$tempsaler = 0;
					$gsr['totalsaler'] = 0;
					$getsaler = $saler->getSaleRFromSale($ds['saleno']);
					if (sizeof($getsaler) > 0){
						$listinner .= '
							<tr>
								<td width="10%" height="25" align="left"></td>
								<td width="90%" colspan="3" height="25" align="left" style="padding-left: 10px"><i><b>RETUR JUAL</b></i></td>
							</tr>
							<tr>
								<td width="10%" align="left"></td>
								<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
								<table border="1" cellpadding="0" width="100%" cellspacing="0">
						';
						
						foreach ($getsaler as $gsr){											
							if ($statususer == 1){
								$gsr['quantityf'] = discq($gsr['quantityf']);
								$totalsalerfk = $gsr['quantityf'] * $gsr['salerprice'];
								$totaldiscfk = $gsr['detaildisc'] / 100 * $totalsalerfk;
								$tempstd = $totalsalerfk - $totaldiscfk;
								$tempstd = $tempstd - $gsr['extdisc'] / 100 * $tempstd;
								$tempstd = $tempstd + $gsr['tax'] / 100 * $tempstd;
								$gsr['totalsalerad'] = $tempstd;
							}
							$tempsaler += $gsr['totalsalerad'];
							
							$listinner .= '
								<tr>
									<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
									'.htmlspecialchars($gsr['stockcode']).'<br>
									( '.date("d-M-Y",$gsr['salerdate']).' )</td>
									<td width="16%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['stockname']).'</td>
									<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['brandcode']).'</td>
									<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['typecode']).'</td>
									<td width="10%" align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($gsr['partno']).'</td>
									<td width="8%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['quantityf'],2,",",".").'</td>
									<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['salerprice'],2,",",".").'</td>
									<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($gsr['detaildisc'],2,",",".").'</td>
									<td width="10%" align="right" height="30" class="detailitem" bgcolor="#EFEFEF">- '.number_format($gsr['totalsalerad'],2,",",".").'</td>
								</tr>
							';
						}
						
						$listinner .= '
								</table></td>
							</tr>
						';
						
						$gsr['totalsaler'] = $tempsaler;
					}
					
					$totalfinal = ($ds['totalsale']-$gsr['totalsaler']);
					
					$list .= '
						<tr>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
							<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
							<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
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
			$printtemplate = 'reportsaleddinit';
		}
	}
	else if ($_GET['view'] == 'monthly'){
		
		if (empty($useraccess['report_salemonthly'])){
			redirecting('index.php');
		}
		
		if ($_POST['basedon'] == 'saledate'){
			if (!empty($_POST['monthstart']) && !empty($_POST['yearstart']) && !empty($_POST['monthend']) && !empty($_POST['yearend'])){
				$printtemplate = 'reportsalem';
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
				if (!empty($_POST['customercode'])){
					$sqls = " AND customercode='".$_POST['customercode']."'";
				}
				
				$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."')".$sql.$sqls." ORDER BY saledate");
				$datego = '';
				$temptrx = 0;
				$tempdisc = 0;
				$temptax = 0;
				$temptotal = 0;
				$trx = 0;
				$disc = 0;
				$tax = 0;
				$total = 0;
				if (sizeof($dbsale) > 0){
					foreach ($dbsale as $ds){
						$customer->setCode($ds['customercode']);
						$getcustomer = $customer->getcustomerDetail();
						
						$datenow = date("M-Y",$ds['saledate']);
						$datenowdt = date("d-M-Y",$ds['saledate']);
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
							//get detail sale
							$sale->setSaleNo($ds['saleno']);
							$dbdetailsale = $sale->getDetailSale();
							$listinner = '';
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
						
							$discvalue = $ds['disc'] / 100 * $subtotalfk;
							$totalafgdiscfk = $subtotalfk - $discvalue;
							$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
							$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
						}
						
						if ($ds['paid'] == 1){
							$printstatus = 'Lunas';
							$getpayment = $db->fetch_one("SELECT * FROM detailpayment WHERE hsid='".$ds['saleid']."' AND types='sale'");
							if (empty($getpayment['hpid'])){
								$printstatus = 'Lunas';
							}
							else{
							$payment->setId($getpayment['hpid']);
							
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
							
							$alldetailrepay = $payment->getDetailRePayment();
							
							$ik = sizeof($alldetailrepay);
							if ($ik > 0){
								$blastit = floor($temporarytotal / $ik);
								$blastcounter = 0;
								
								$iy = 0;
								
								$detailsrp = '<ul>';
								foreach ($alldetailrepay as $adr){
									if ($statususer == 1){
										if ($iy == ($ik-1)){
											$blasttext = $temporarytotal - $blastcounter;
										}
										else{
											$blasttext = $blastit;
											$blastcounter += $blastit;
										}
										$adr['totals'] = $blasttext;
										$iy++;
									}
									
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
						}
						else{
							$printstatus = 'Piutang';
						}
						
						$list .= '
							<tr>
								<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
								<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
								<td align="right" height="30" class="detailitem">'.number_format($discvalue,2,",",".").'</td>
								<td align="right" height="30" class="detailitem">'.number_format($taxvalue,2,",",".").'</td>
								<td align="right" height="30" class="detailitem">'.number_format($ds['totalsale'],2,",",".").'</td>
								<td align="left" height="30" class="detailitem">'.$printstatus.'</td>
							</tr>
						';
						$temptrx++;
						$tempdisc += $discvalue;
						$temptax += $taxvalue;
						$temptotal += $ds['totalsale'];
						$trx++;
						$disc += $discvalue;
						$tax += $taxvalue;
						$total += $ds['totalsale'];
						
						//get return sale
						$totalsaler = 0;
						$returnsale = $saler->getSaleRFromSale($ds['saleno']);
						if (sizeof($returnsale) > 0){
							foreach ($returnsale as $rsl){
								if ($statususer == 1){
									$rsl['quantityf'] = discq($rsl['quantityf']);
									
									$totalsalerfk = $rsl['quantityf'] * $rsl['salerprice'];
									$totaldiscfk = $rsl['detaildisc'] / 100 * $totalsalerfk;
									$tempstd = $totalsalerfk - $totaldiscfk;
									$tempstd = $tempstd - $rsl['extdisc'] / 100 * $tempstd;
									$tempstd = $tempstd + $rsl['tax'] / 100 * $tempstd;
									$rsl['totalsalerad'] = $tempstd;								
								}
								$totalsaler += $rsl['totalsalerad'];
							}						
							$list .= '
								<tr>
									<td align="center" height="30" class="detailitem"></td>
									<td align="center" height="30" class="detailitem"></td>
									<td align="left" height="30" class="detailitem" colspan="4">Retur Jual : '.htmlspecialchars($ds['saleno']).'</td>
									<td align="right" height="30" class="detailitem">- '.number_format($totalsaler,2,",",".").'</td>
									<td align="right" height="30" class="detailitem"></td>
								</tr>
							';
							$temptotal -= $totalsaler;
							$total -= $totalsaler;
						}
					}
					$listalls = '
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
							<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
							<th align="center" width="13%" bgcolor="#DEDEDE">NO FAKTUR</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">NAMA CUSTOMER</th>
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
		else if ($_POST['basedon'] == 'customer'){
			if (!empty($_POST['monthstart']) && !empty($_POST['yearstart']) && !empty($_POST['monthend']) && !empty($_POST['yearend'])){
				$printtemplate = 'reportsalem';
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
				
				if (!empty($_POST['customercode'])){
					$allcustomer = $db->fetch_all("SELECT * FROM customer WHERE customercode = '".$_POST['customercode']."'");
				}
				else{
					$allcustomer = $customer->getListcustomer('partial');
				}
				
				if (sizeof($allcustomer) > 0){
					$trx = 0;
					$disc = 0;
					$tax = 0;
					$total = 0;
					foreach ($allcustomer as $ac){
						$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."') AND customercode = '".$ac['customercode']."'".$sql." ORDER BY saledate");
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						if (sizeof($dbsale) > 0){
							$list .= '
								<tr>
									<td width="100%" align="left" colspan="8" valign="bottom" height="30">
									<b>'.htmlspecialchars($ac['customercode'].' - '.$ac['customername']).'</b></td>
								</tr>
							';
							foreach ($dbsale as $ds){
								$datenowdt = date("d-M-Y",$ds['saledate']);
								$datenowdd = date("d-M-Y",$ds['duedate']);
								$discvalue = ($ds['disc'] / 100) * $ds['totals'];
								$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
								
								if ($statususer == 1){
									//get detail sale
									$sale->setSaleNo($ds['saleno']);
									$dbdetailsale = $sale->getDetailSale();
									$listinner = '';
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
								
									$discvalue = $ds['disc'] / 100 * $subtotalfk;
									$totalafgdiscfk = $subtotalfk - $discvalue;
									$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
									$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
								}
								
								if ($ds['paid'] == 1){
									$printstatus = 'Lunas';
									$getpayment = $db->fetch_one("SELECT * FROM detailpayment WHERE hsid='".$ds['saleid']."' AND types='sale'");
									if (empty($getpayment['hpid'])){
										$printstatus = 'Lunas';
									}
									else{
									$payment->setId($getpayment['hpid']);
									
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
									
									$alldetailrepay = $payment->getDetailRePayment();
									
									$ik = sizeof($alldetailrepay);
									if ($ik > 0){
										$blastit = floor($temporarytotal / $ik);
										$blastcounter = 0;
										
										$iy = 0;
										
										$detailsrp = '<ul>';
										foreach ($alldetailrepay as $adr){
											if ($statususer == 1){
												if ($iy == ($ik-1)){
													$blasttext = $temporarytotal - $blastcounter;
												}
												else{
													$blasttext = $blastit;
													$blastcounter += $blastit;
												}
												$adr['totals'] = $blasttext;
												$iy++;
											}
											
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
								}
								else{
									$printstatus = 'Piutang';
								}
								
								$list .= '
									<tr>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ac['customername']).'</td>
										<td align="right" height="30" class="detailitem">'.number_format($discvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($taxvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($ds['totalsale'],2,",",".").'</td>
										<td align="left" height="30" class="detailitem">'.$printstatus.'</td>
									</tr>
								';
								$temptrx++;
								$tempdisc += $discvalue;
								$temptax += $taxvalue;
								$temptotal += $ds['totalsale'];
								$trx++;
								$disc += $discvalue;
								$tax += $taxvalue;
								$total += $ds['totalsale'];
								
								//get return sale
								$totalsaler = 0;
								$returnsale = $saler->getSaleRFromSale($ds['saleno']);
								if (sizeof($returnsale) > 0){
									foreach ($returnsale as $rsl){
										if ($statususer == 1){
											$rsl['quantityf'] = discq($rsl['quantityf']);
											
											$totalsalerfk = $rsl['quantityf'] * $rsl['salerprice'];
											$totaldiscfk = $rsl['detaildisc'] / 100 * $totalsalerfk;
											$tempstd = $totalsalerfk - $totaldiscfk;
											$tempstd = $tempstd - $rsl['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $rsl['tax'] / 100 * $tempstd;
											$rsl['totalsalerad'] = $tempstd;								
										}
										$list .= '
											<tr>
												<td align="center" height="30" class="detailitem">'.date("d-M-Y",$rsl['salerdate']).'</td>
												<td align="center" height="30" class="detailitem"></td>
												<td align="left" height="30" class="detailitem" colspan="4">Retur Jual : '.htmlspecialchars($ds['saleno']).'</td>
												<td align="right" height="30" class="detailitem">- '.number_format($rsl['totalsalerad'],2,",",".").'</td>
												<td align="right" height="30" class="detailitem"></td>
											</tr>
										';
										$totalsaler += $rsl['totalsalerad'];
									}						
									$temptotal -= $totalsaler;
									$total -= $totalsaler;
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
							<th align="center" width="13%" bgcolor="#DEDEDE">NO FAKTUR</th>
							<th align="center" width="18%" bgcolor="#DEDEDE">NAMA CUSTOMER</th>
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
				$printtemplate = 'reportsalem';
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
				if (!empty($_POST['customercode'])){
					$sqls = " AND customercode='".$_POST['customercode']."'";
				}
				
				if (sizeof($arrtype) > 0){
					$gtrx = 0;
					$gdisc = 0;
					$gtax = 0;
					$gtotal = 0;
					foreach ($arrtype as $keyp => $artp){
						$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE trtype='".$keyp."' AND (saledate >= '".$startdate."' AND saledate <= '".$enddate."')".$sql.$sqls." ORDER BY saledate");
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
						if (sizeof($dbsale) > 0){
							foreach ($dbsale as $ds){
								$customer->setCode($ds['customercode']);
								$getcustomer = $customer->getcustomerDetail();
								
								$datenow = date("M-Y",$ds['saledate']);
								$datenowdt = date("d-M-Y",$ds['saledate']);
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
									//get detail sale
									$sale->setSaleNo($ds['saleno']);
									$dbdetailsale = $sale->getDetailSale();
									$listinner = '';
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
								
									$discvalue = $ds['disc'] / 100 * $subtotalfk;
									$totalafgdiscfk = $subtotalfk - $discvalue;
									$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
									$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
								}
								
								if ($ds['paid'] == 1){
									$printstatus = 'Lunas';
									$getpayment = $db->fetch_one("SELECT * FROM detailpayment WHERE hsid='".$ds['saleid']."' AND types='sale'");
									if (empty($getpayment['hpid'])){
										$printstatus = 'Lunas';
									}
									else{
									$payment->setId($getpayment['hpid']);
									
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
									
									$alldetailrepay = $payment->getDetailRePayment();
									
									$ik = sizeof($alldetailrepay);
									if ($ik > 0){
										$blastit = floor($temporarytotal / $ik);
										$blastcounter = 0;
										
										$iy = 0;
										
										$detailsrp = '<ul>';
										foreach ($alldetailrepay as $adr){
											if ($statususer == 1){
												if ($iy == ($ik-1)){
													$blasttext = $temporarytotal - $blastcounter;
												}
												else{
													$blasttext = $blastit;
													$blastcounter += $blastit;
												}
												$adr['totals'] = $blasttext;
												$iy++;
											}
											
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
								}
								else{
									$printstatus = 'Piutang';
								}
								
								$list .= '
									<tr>
										<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
										<td align="center" height="30" class="detailitem">'.$datenowdd.'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
										<td align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
										<td align="right" height="30" class="detailitem">'.number_format($discvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($taxvalue,2,",",".").'</td>
										<td align="right" height="30" class="detailitem">'.number_format($ds['totalsale'],2,",",".").'</td>
										<td align="left" height="30" class="detailitem">'.$printstatus.'</td>
									</tr>
								';
								$temptrx++;
								$tempdisc += $discvalue;
								$temptax += $taxvalue;
								$temptotal += $ds['totalsale'];
								$trx++;
								$disc += $discvalue;
								$tax += $taxvalue;
								$total += $ds['totalsale'];
								$gtrx++;
								$gdisc += $discvalue;
								$gtax += $taxvalue;
								$gtotal += $ds['totalsale'];
								
								//get return sale
								$totalsaler = 0;
								$returnsale = $saler->getSaleRFromSale($ds['saleno']);
								if (sizeof($returnsale) > 0){
									foreach ($returnsale as $rsl){
										if ($statususer == 1){
											$rsl['quantityf'] = discq($rsl['quantityf']);
											
											$totalsalerfk = $rsl['quantityf'] * $rsl['salerprice'];
											$totaldiscfk = $rsl['detaildisc'] / 100 * $totalsalerfk;
											$tempstd = $totalsalerfk - $totaldiscfk;
											$tempstd = $tempstd - $rsl['extdisc'] / 100 * $tempstd;
											$tempstd = $tempstd + $rsl['tax'] / 100 * $tempstd;
											$rsl['totalsalerad'] = $tempstd;								
										}
										$totalsaler += $rsl['totalsalerad'];
									}						
									$list .= '
										<tr>
											<td align="center" height="30" class="detailitem"></td>
											<td align="center" height="30" class="detailitem"></td>
											<td align="left" height="30" class="detailitem" colspan="4">Retur Jual : '.htmlspecialchars($ds['saleno']).'</td>
											<td align="right" height="30" class="detailitem">- '.number_format($totalsaler,2,",",".").'</td>
											<td align="right" height="30" class="detailitem"></td>
										</tr>
									';
									$temptotal -= $totalsaler;
									$total -= $totalsaler;
									$gtotal -= $totalsaler;
								}
							}
							$listalls .= '
								<div align="center">
								<h2>'.$artp.'</h2></div>
								<table border="1" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<th align="center" width="10%" bgcolor="#DEDEDE">TANGGAL</th>
									<th align="center" width="10%" bgcolor="#DEDEDE">JATUH TEMPO</th>
									<th align="center" width="13%" bgcolor="#DEDEDE">NO FAKTUR</th>
									<th align="center" width="18%" bgcolor="#DEDEDE">NAMA CUSTOMER</th>
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
									<td align="left" colspan="3">&nbsp;<b>TOTAL PENJUALAN '.$artp.'</b></td>
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
						<td align="left" width="41%">&nbsp;<b>TOTAL PENJUALAN KESELURUHAN</b></td>
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
			$printtemplate = 'reportsaleminit';
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