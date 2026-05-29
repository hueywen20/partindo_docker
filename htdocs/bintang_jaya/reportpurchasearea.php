<?php
	require_once "global.php";
	
	require_once "class/supplier.php";
	require_once "class/purchase.php";
	require_once "class/PurchaseR.php";
	require_once "class/area.php";
	$supplier = new supplier();
	$purchase = new Purchase();
	$purchaser = new PurchaseR();
	$area = new area();
	
	$printdate = date("d-M-Y / H:i:s");
	if (empty($useraccess['report_purchasearea'])){
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
		
		$printtemplate = 'reportbuyarea';
		
		$allarea = $area->getListarea('partial');
		if (sizeof($allarea) > 0){
			$temparray = array();
			$supp = array();
			foreach ($allarea as $aar){
				$detailsupp = $db->fetch_all("SELECT ds.*, s.suppliercode FROM detailsupplier ds INNER JOIN supplier s ON ds.supplierid = s.supplierid WHERE ds.areacode='".$aar['areacode']."' GROUP BY ds.detailsplid");
				if (sizeof($detailsupp) > 0){
					foreach ($detailsupp as $dspl){
						$checkfirst = $db->fetch_one("SELECT * FROM detailsupplier WHERE supplierid='".$dspl['supplierid']."' ORDER BY detailsplid LIMIT 1");
						if (!in_array($dspl['supplierid'],$temparray) && $dspl['detailsplid'] == $checkfirst['detailsplid']){
							$supp[$aar['areaid']] .= ',\''.str_replace("'","\'",$dspl['suppliercode']).'\'';
							array_push($temparray,$dspl['supplierid']);
						}
					}
					if (!empty($supp[$aar['areaid']])){
						$supp[$aar['areaid']] = substr($supp[$aar['areaid']],1);
					}
				}
			}
		
			$trx = 0;
			$disc = 0;
			$tax = 0;
			$total = 0;
			if (sizeof($supp) > 0){
				foreach ($supp as $keys => $asp){
					$area->setId($keys);
					$areadetail = $area->getareaDetail();
				
					$dbbuy = $db->fetch_all("SELECT * FROM headerbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."') AND suppliercode IN (".$asp.")".$sql." ORDER BY buydate");
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
							<span style="float: left">Area / Kota : <b>'.htmlspecialchars($areadetail['areaname']).'</b></span>
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
		$printtemplate = 'reportbuyareainit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>