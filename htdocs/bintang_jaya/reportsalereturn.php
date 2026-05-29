<?php
	require_once "global.php";
	
	if (empty($useraccess['report_salerpc'])){
		redirecting('index.php');
	}
	
	require_once "class/customer.php";
	require_once "class/SaleR.php";
	$customer = new customer();
	$saler = new SaleR();
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_GET['view'] == 'periodcustomer'){
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$printtemplate = 'reportsalereturnpc';
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			$sql = '';
			if (!empty($_POST['customercode'])){
				$sql = " AND customercode='".$_POST['customercode']."'";
			}
			/*if ($statususer == 1){
				$getalltr = $db->fetch_one("SELECT SUM(totalsaler) AS totalsalers FROM headersaler WHERE (salerdate >= '".$startdate."' AND salerdate <= '".$enddate."')".$sql." ORDER BY salerdate");
				$totaltransaction = $getalltr['totalsalers'];
				$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
				
				$getalltr = $db->fetch_all("SELECT * FROM headersaler WHERE (salerdate >= '".$startdate."' AND salerdate <= '".$enddate."')".$sql." ORDER BY totalsaler");
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
						$dbsale = $db->fetch_all("SELECT * FROM headersaler WHERE salerid IN (".implode(",",$arrhbid).")".$sql." ORDER BY salerdate");
					}
				}
			}
			else{*/
				$dbsale = $db->fetch_all("SELECT * FROM headersaler WHERE (salerdate >= '".$startdate."' AND salerdate <= '".$enddate."')".$sql." ORDER BY salerdate");
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
					
					$datenow = date("d-M-Y",$ds['salerdate']);
					$datenowdt = date("d-M-Y H:i:s",$ds['createddate']);
					$datenowdd = date("d-M-Y",$ds['saledate']);
					if ($datego != $datenow){
						if ($total != 0){
							$list .= '
								<tr>
									<td align="center"><b>'.$temptrx.'</b></td>
									<td align="left">&nbsp;<b>TOTAL</b></td>
									<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
								</tr>
							';
						}
						$list .= '
							<tr>
								<td align="center" colspan="3" valign="bottom" height="35" bgcolor="#EEE">
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
					//get detail purchase return
					$saler->setId($ds['salerid']);
					$dbdetailsaler = $saler->getDetailSaleR();
					$listinner = '';
					if (sizeof($dbdetailsaler) > 0){
						$listinner .= '
							<tr>
								<td width="10%">&nbsp;</td>
								<td colspan="2" align="left" width="90%" style="border: 0px solid #000">
								<table border="1" cellpadding="0" width="100%" cellspacing="0">
								<tr>
									<th align="center" width="8%" bgcolor="#EFEFEF">No. Faktur</th>
									<th align="center" width="13%" bgcolor="#EFEFEF">Kode</th>
									<th align="center" width="13%" bgcolor="#EFEFEF">Nama</th>
									<th align="center" width="9%" bgcolor="#EFEFEF">Merek</th>
									<th align="center" width="9%" bgcolor="#EFEFEF">Tipe</th>
									<th align="center" width="9%" bgcolor="#EFEFEF">Part No</th>
									<th align="center" width="7%" bgcolor="#EFEFEF">Qty</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Harga</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Modal</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Diskon</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Sub Total</th>
								</tr>
						';
						$subtotalfk = 0;
						foreach ($dbdetailsaler as $dbds){
							if ($statususer == 1){
								$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
								if ($dbds['quantityf'] < 1){
									$dbds['quantityf'] = 1;
								}
								
								$totalbuyfk = $dbds['quantityf'] * $dbds['salerprice'];
								$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
								$tempstd = $totalbuyfk - $totaldiscfk;
								$tempstd = $tempstd - $dbds['extdisc'] / 100 * $tempstd;
								$tempstd = $tempstd + $dbds['tax'] / 100 * $tempstd;
								$dbds['totalsalerad'] = $tempstd;
								$subtotalfk += $dbds['totalsalerad'];
							}
							
							/* get sale number */
							$getdbdetail = $db->fetch_one("SELECT saleno, saledate FROM detailsale WHERE dsid='".$dbds['dsid']."'");
							
							/* get capitals */
							$capitalstext = '';
							
							/* check if stock is assembly */
							$getfsdetail = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$dbds['stockcode']."'");
							if ($getfsdetail['assembly'] == 1){
								$getdetailitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbds['dsid']."'");
								if (empty($getdetailitem['price'])){
									$getdetailitem['price'] = 0;
								}
								$capitals = $getdetailitem['price'];
								$capitalstext = number_format($capitals,2,",",".").' ('.number_format($dbds['quantityf'],2,",",".").')';
							}
							else{
								$saler->setDetailId($dbds['dsrid']);
								$getdetailitem = $saler->getDetailSaleRItem();
								if (sizeof($getdetailitem) > 0){
									$temporaryqty = 0;
									foreach ($getdetailitem as $gdi){
										if ($statususer == 1){
											$gdi['quantity'] = floor((100-$discount['extradisc'])/100 * $gdi['quantityf']);
											if ($gdi['quantity'] < 1){
												$gdi['quantity'] = 1;
											}
											$temporaryqty += $gdi['quantity'];
										}
										
										if ($gdi['dbid'] == -1){
											$dbpurch = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$dbds['stockcode']."'");
											if (empty($dbpurch['buyprice'])){
												$dbpurch['buyprice'] = 0;
											}
											$capitals = $dbpurch['buyprice'];
										}
										else{
											if ($gdi['tabledbid'] == 'logdeassembly'){
												$dbpurch = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$gdi['dbid']."'");
												if (empty($dbpurch['price'])){
													$dbpurch['price'] = 0;
												}
												$capitals = $dbpurch['price'];
											}
											else{
												$dbpurch = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$gdi['dbid']."'");
												if (empty($dbpurch['realbuyprice'])){
													$dbpurch['realbuyprice'] = 0;
												}
												$capitals = $dbpurch['realbuyprice'];
											}
										}
										if ($statususer == 1){
											if ($temporaryqty <= $dbds['quantityf']){
												$capitalstext .= '<br>'.number_format($capitals,2,",",".").' ('.number_format($gdi['quantity'],2,",",".").')';
											}
										}
										else{
											$capitalstext .= '<br>'.number_format($capitals,2,",",".").' ('.number_format($gdi['quantity'],2,",",".").')';
										}
									}
									$capitalstext = substr($capitalstext,4);
								}
							}
							
							$listinner .= '
								<tr>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
									'.htmlspecialchars($getdbdetail['saleno']).'
									<br>('.date("d-M-Y",$getdbdetail['saledate']).')</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['salerprice'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.$capitalstext.'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalsalerad'],2,",",".").'</td>
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
						$ds['totalsaler'] = $totalafgdiscfk + $taxvalue;
					}
					
					$list .= '
						<tr>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['salerid']).'</td>
							<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
						</tr>
					'.$listinner.'
						<tr>
							<td align="left" height="30" class="detailitem"></td>
							<td align="right" height="30" class="detailitem">TOTAL</td>
							<td align="right" height="30" class="detailitem">'.number_format($ds['totalsaler'],2,",",".").'</td>
						</tr>
						<tr>
							<td colspan="3" height="3" style="border: 0px solid #FFF; background-color: #000"></td>
						</tr>
					';
					$temptrx++;
					$tempdisc += $discvalue;
					$temptax += $taxvalue;
					$temptotal += $ds['totalsaler'];
					$trx++;
					$disc += $discvalue;
					$tax += $taxvalue;
					$total += $ds['totalsaler'];
				}
				$list .= '
					<tr>
						<td align="center"><b>'.$temptrx.'</b></td>
						<td align="left">&nbsp;<b>TOTAL</b></td>
						<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
					</tr>
					<tr>
						<td align="center"><b>'.$trx.'</b></td>
						<td align="left">&nbsp;<b>GRAND TOTAL</b></td>
						<td align="right" height="30"><b>'.number_format($total,2,",",".").'</b></td>
					</tr>
				';
			}
		}
		else{
			$printtemplate = 'reportsalereturnpcinit';
		}
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>