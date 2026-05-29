<?php
	require_once "global.php";
	
	require_once "class/customer.php";
	require_once "class/Sale.php";
	require_once "class/SaleR.php";
	require_once "class/area.php";
	require_once "class/Stock.php";
	$stock = new Stock();
	$customer = new customer();
	$sale = new Sale();
	$saler = new SaleR();
	$area = new area();
	
	$printdate = date("d-M-Y / H:i:s");
	if (empty($useraccess['report_salearea'])){
		redirecting('index.php');
	}
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
	
	if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
		$startdate = strtotime($_POST['datestart']);
		$enddate = strtotime($_POST['dateend'].' 23:59:59');
		
		$printtemplate = 'reportsalearea';
		
		$allarea = $area->getListarea('partial');
		if (sizeof($allarea) > 0){
			$temparray = array();
			$cust = array();
			foreach ($allarea as $aar){
				$detailcust = $db->fetch_all("SELECT ds.*, s.customercode FROM detailcustomer ds INNER JOIN customer s ON ds.customerid = s.customerid WHERE ds.areacode='".$aar['areacode']."' GROUP BY ds.detailcustid");
				if (sizeof($detailcust) > 0){
					foreach ($detailcust as $dspl){
						$checkfirst = $db->fetch_one("SELECT * FROM detailcustomer WHERE customerid='".$dspl['customerid']."' ORDER BY detailcustid LIMIT 1");
						if (!in_array($dspl['customerid'],$temparray) && $dspl['detailcustid'] == $checkfirst['detailcustid']){
							$cust[$aar['areaid']] .= ',\''.str_replace("'","\'",$dspl['customercode']).'\'';
							array_push($temparray,$dspl['customerid']);
						}
					}
					if (!empty($cust[$aar['areaid']])){
						$cust[$aar['areaid']] = substr($cust[$aar['areaid']],1);
					}
				}
			}
		
			$trx = 0;
			$disc = 0;
			$tax = 0;
			$total = 0;
			if (sizeof($cust) > 0){
				foreach ($cust as $keys => $asp){
					$area->setId($keys);
					$areadetail = $area->getareaDetail();
				
					$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."') AND customercode IN (".$asp.")".$sql." ORDER BY saledate");
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
									
									$profitloss = $totalsalesall - $totalbuysall;
									$percentages = ($profitloss / $totalbuysall) * 100;
									
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
							<span style="float: left">Area / Kota : <b>'.htmlspecialchars($areadetail['areaname']).'</b></span>
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
								<td align="right" colspan="2" align="right"><b>DISKON</b><br><b>PPN</b><br><b>TOTAL</b></td>
								<td align="right" height="30">
								<b>'.number_format($tempdisc,2,",",".").'</b>
								<br><b>'.number_format($temptax,2,",",".").'</b>
								<br><b>'.number_format($temptotal,2,",",".").'</b></td>
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
						<td align="right" colspan="2" width="20%"><b>DISKON</b><br><b>PPN</b><br><b>GRAND TOTAL</b></td>
						<td align="right" height="30" width="70%">
						<b>'.number_format($disc,2,",",".").'</b>
						<br><b>'.number_format($tax,2,",",".").'</b>
						<br><b>'.number_format($total,2,",",".").'</b></td>
					</tr>
					</table></div>
				';
			}
		}
	}
	else{
		$printtemplate = 'reportsaleareainit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>