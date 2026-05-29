<?php
	require_once "global.php";
	
	require_once "class/customer.php";
	require_once "class/supplier.php";
	require_once "class/Stock.php";
	require_once "class/DeAssembly.php";
	$customer = new customer();
	$supplier = new supplier();
	$stock = new Stock();
	$deassembly = new DeAssembly();
	
	$printdate = date("d-M-Y / H:i:s");
	if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
		$printtemplate = 'reportprofitlossinvoice';
		$startdate = strtotime($_POST['datestart']);
		$enddate = strtotime($_POST['dateend'].' 23:59:59');
		$sql = '';
		if (!empty($_POST['customercode'])){
			$sql = " AND customercode='".$_POST['customercode']."'";
		}
		
		$dbsale = $db->fetch_all("SELECT * FROM headersale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."')".$sql." ORDER BY saledate");
		
		$datego = '';
		$temptrx = 0;
		$tempdisc = 0;
		$temptax = 0;
		$temptotal = 0;
		$trx = 0;
		$disc = 0;
		$tax = 0;
		$total = 0;
		$totalallprofitloss = 0;
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
								<td width="10%" align="center"><b>'.$temptrx.'</b></td>
								<td width="44%" align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
								<td width="13%" align="right" height="30"><b>'.number_format($tempdisc,2,",",".").'</b></td>
								<td width="13%" align="right" height="30"><b>'.number_format($temptax,2,",",".").'</b></td>
								<td width="20%" align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
							</tr>
						';
					}
					$list .= '
						<tr>
							<td width="100%" align="left" colspan="7">
							<br>&nbsp;<b>'.$datenow.'</b></td>
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
				$dbdetailsale = $db->fetch_all("SELECT * FROM detailsale WHERE saleno='".$ds['saleno']."' ORDER BY dsid");
				if (sizeof($dbdetailsale) > 0){
					$listinner .= '
						<tr>
							<td width="10%">&nbsp;</td>
							<td colspan="6" align="left" width="90%" style="border: 0px solid #000">
							<table border="1" cellpadding="0" width="100%" cellspacing="0">
							<tr>
								<th align="center" width="16%" bgcolor="#EFEFEF">Kode</th>
								<th align="center" width="16%" bgcolor="#EFEFEF">Nama</th>
								<th align="center" width="10%" bgcolor="#EFEFEF">Merek</th>
								<th align="center" width="10%" bgcolor="#EFEFEF">Tipe</th>
								<th align="center" width="10%" bgcolor="#EFEFEF">Part No</th>
								<th align="center" width="8%" bgcolor="#EFEFEF">Qty</th>
								<th align="center" width="10%" bgcolor="#EFEFEF">Harga Beli</th>
								<th align="center" width="10%" bgcolor="#EFEFEF">Harga Jual</th>
								<th align="center" width="10%" bgcolor="#EFEFEF">Total L / R</th>
							</tr>
					';
					$totalprofitloss = 0;
					$subtotalfk = 0;
					foreach ($dbdetailsale as $dbds){
						$stock->setCode($dbds['stockcode']);
						$fsk = $stock->getFirstStock();
						$dbdetailitem = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dbds['dsid']."'");
						if (sizeof($dbdetailitem) > 0){
							if ($fsk['assembly'] == 1){
								$totalbuypr = 0;
								$componentlistcode = '';
								$componentlistname = '';
								$componentlistqty = '';
								$componentlistbuyp = '';
								$componentlistsalep = '';
								$componentlistpl = '';
								/*foreach ($dbdetailitem as $dbdi){
									if ($dbdi['dbid'] == -1){
										$dbdbid['realbuyprice'] = $fsk['buyprice'];
										$dbdbid['stockcode'] = $fsk['stockcode'];
										$dbdbid['stockname'] = $fsk['generalname'];
									}
									else{
										$dbdbid = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbdi['dbid']."'");
									}
									$totalbuypr += $dbdbid['realbuyprice'] * $dbdi['quantity'];
									$componentlistcode .= '<br>&nbsp;&nbsp;'.$dbdbid['stockcode'];
									$componentlistname .= '<br>&nbsp;&nbsp;'.$dbdbid['stockname'];
									$componentlistqty .= '<br>'.number_format($dbdi['quantity'],2,",",".");
									$componentlistbuyp .= '<br>'.number_format($dbdbid['realbuyprice'],2,",",".");
									$componentlistsalep .= '<br>&nbsp;';
									$componentlistpl .= '<br>&nbsp;';
								}*/
								//$totalbuypr = $totalbuypr / $dbds['quantity'];
							
								if ($statususer == 1){
									$dbds['quantity'] = floor((100-$discount['extradisc'])/100 * $dbds['quantity']);
									if ($dbds['quantity'] < 1){
										$dbds['quantity'] = 1;
									}
									
									$totalbuyfk = $dbds['quantity'] * $dbds['saleprice'];
									$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
									$subtotalfk += ($totalbuyfk - $totaldiscfk);
								}
								
								/* get all buy price */
								$thebuyprices = 0;
								$dballlogas = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dbds['dsid']."'");
								if (sizeof($dballlogas) > 0){
									foreach ($dballlogas as $dbag){
										if ($dbag['dbid'] == -1){
											$dbfrombuy = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$dbds['stockcode']."'");
											$byprice = $dbfrombuy['buyprice'];
										}
										else{
											$dbfrombuy = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbag['dbid']."'");
											$byprice = $dbfrombuy['realbuyprice'];
										}
										if ($statususer == 1){
											$dbag['quantity'] = floor((100-$discount['extradisc'])/100 * $dbag['quantity']);
											if ($dbag['quantity'] < 1){
												$dbag['quantity'] = 1;
											}
										}
										$thebuyprices += $byprice * $dbag['quantity'];
									}
								}
								
								//$dblogas = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbds['dsid']."' AND stockcode='".$dbds['stockcode']."'");
								//$profitloss = ($dbds['realsaleprice'] - $dblogas['price']) * $dbds['quantity'];
								$profitloss = ($dbds['realsaleprice'] * $dbds['quantity']) - $thebuyprices;
								$totalprofitloss += $profitloss;
								$listinner .= '
									<tr>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.htmlspecialchars($dbds['stockcode']).$componentlistcode.'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.htmlspecialchars($dbds['stockname']).$componentlistname.'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.htmlspecialchars($dbds['brandcode']).$componentlistname.'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.htmlspecialchars($dbds['typecode']).$componentlistname.'</td>
										<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.htmlspecialchars($dbds['partno']).$componentlistname.'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.number_format($dbds['quantity'],2,",",".").$componentlistqty.'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.number_format(($thebuyprices / $dbds['quantity']),2,",",".").$componentlistbuyp.'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.number_format($dbds['realsaleprice'],2,",",".").$componentlistsalep.'</td>
										<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">
										'.number_format($profitloss,2,",",".").$componentlistpl.'</td>
									</tr>
								';
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
										
										$totalbuyfk = $dbdi['quantity'] * $dbds['saleprice'];
										$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
										$subtotalfk += ($totalbuyfk - $totaldiscfk);
									}
									
									$profitloss = ($dbds['realsaleprice'] - $dbdbid['price']) * $dbdi['quantity'];
									$totalprofitloss += $profitloss;
									$listinner .= '
										<tr>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbdi['quantity'],2,",",".").'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbdbid['price'],2,",",".").'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['realsaleprice'],2,",",".").'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($profitloss,2,",",".").'</td>
										</tr>
									';
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
										
										$totalbuyfk = $dbdi['quantity'] * $dbds['saleprice'];
										$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
										$subtotalfk += ($totalbuyfk - $totaldiscfk);
									}
									
									$profitloss = ($dbds['realsaleprice'] - $dbdbid['realbuyprice']) * $dbdi['quantity'];
									$totalprofitloss += $profitloss;
									$listinner .= '
										<tr>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
											<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbdi['quantity'],2,",",".").'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbdbid['realbuyprice'],2,",",".").'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['realsaleprice'],2,",",".").'</td>
											<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($profitloss,2,",",".").'</td>
										</tr>
									';
								}
							}
						}
					}
					$totalallprofitloss += $totalprofitloss;
					$listinner .= '
							<tr>
								<td align="left" height="30" class="detailitem" colspan="8" bgcolor="#EFEFEF">&nbsp;</th>
								<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($totalprofitloss,2,",",".").'</th>
							</tr>
							</table></td>
						</tr>
					';
				}
				
				if ($statususer == 1){
					$discvalue = $list['disc'] / 100 * $subtotalfk;
					$totalafgdiscfk = $subtotalfk - $discvalue;
					$taxvalue = $list['tax'] / 100 * $totalafgdiscfk;
					$ds['totalsale'] = $totalafgdiscfk + $taxvalue;
				}
				
				$list .= '
					<tr>
						<td width="10%" align="center" height="30" class="detailitem">'.$datenowdt.'</td>
						<td width="10%" align="center" height="30" class="detailitem">'.$datenowdd.'</td>
						<td width="14%" align="left" height="30" class="detailitem">'.htmlspecialchars($ds['saleno']).'</td>
						<td width="20%" align="left" height="30" class="detailitem">'.htmlspecialchars($getcustomer['customername']).'</td>
						<td width="13%" align="right" height="30" class="detailitem">'.number_format($discvalue,2,",",".").'</td>
						<td width="13%" align="right" height="30" class="detailitem">'.number_format($taxvalue,2,",",".").'</td>
						<td width="20%" align="right" height="30" class="detailitem">'.number_format($ds['totalsale'],2,",",".").'</td>
					</tr>
				'.$listinner;
				
				$temptrx++;
				$tempdisc += $discvalue;
				$temptax += $taxvalue;
				$temptotal += $ds['totalsale'];
				$trx++;
				$disc += $discvalue;
				$tax += $taxvalue;
				$total += $ds['totalsale'];
			}
			
			if ($totalallprofitloss > 0){
				$textpl = 'KEUNTUNGAN';
			}
			else if ($totalallprofitloss < 0){
				$textpl = 'KERUGIAN';
			}
			else{
				$textpl = 'IMPAS';
			}
			
			$list .= '
				<tr>
					<td width="10%" align="center"><b>'.$temptrx.'</b></td>
					<td width="44%" align="left" colspan="3">&nbsp;<b>TOTAL</b></td>
					<td width="13%" align="right" height="30"><b>'.number_format($tempdisc,2,",",".").'</b></td>
					<td width="13%" align="right" height="30"><b>'.number_format($temptax,2,",",".").'</b></td>
					<td width="20%" align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
				</tr>
				<tr>
					<td width="10%" align="center"><b>'.$trx.'</b></td>
					<td width="44%" align="left" colspan="3">&nbsp;<b>GRAND TOTAL</b></td>
					<td width="13%" align="right" height="30"><b>'.number_format($disc,2,",",".").'</b></td>
					<td width="13%" align="right" height="30"><b>'.number_format($tax,2,",",".").'</b></td>
					<td width="20%" align="right" height="30"><b>'.number_format($total,2,",",".").'</b></td>
				</tr>
				<tr>
					<td width="10%" align="center">&nbsp;</td>
					<td width="44%" align="left" colspan="3">&nbsp;<b>'.$textpl.'</b></td>
					<td width="13%" align="right" height="30">&nbsp;</td>
					<td width="13%" align="right" height="30">&nbsp;</td>
					<td width="20%" align="right" height="30"><b>'.number_format($totalallprofitloss,2,",",".").'</b></td>
				</tr>
			';
		}
	}
	else{
		$printtemplate = 'reportprofitlossinvoiceinit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>