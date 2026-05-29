<?php
	require_once "global.php";
	
	if (empty($useraccess['report_activestuff'])){
		redirecting('index.php');
	}
	
	require_once "class/Stock.php";
	require_once "class/customer.php";
	require_once "class/supplier.php";
	$stock = new stock();
	$customer = new customer();
	$supplier = new supplier();
	
	$printdate = date("d-M-Y / H:i:s");
	if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
		$printtemplate = 'reportactivestuff';
		$startdate = strtotime($_POST['datestart']);
		$enddate = strtotime($_POST['dateend'].' 23:59:59');
		if ($_POST['activetype'] == 'purchase'){
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
						array_push($arrhbid,"'".$gatr['buyno']."'");
					}
					$textimplodehbid = '';
					if (sizeof($arrhbid) > 0){
						$textimplodehbid = implode(",",$arrhbid);
						$dbbuy = $db->fetch_all("SELECT SUM(quantity) AS q, stockcode FROM detailbuy WHERE buyno IN (".$textimplodehbid.")".$sql." GROUP BY stockcode ORDER BY q DESC");
					}
				}
			}
			else{*/
				$dbbuy = $db->fetch_all("SELECT SUM(quantity) AS q, stockcode FROM detailbuy WHERE (buydate >= '".$startdate."' AND buydate <= '".$enddate."') GROUP BY stockcode ORDER BY q DESC");
			//}
			if (sizeof($dbbuy) > 0){
				foreach ($dbbuy as $dbs){
					$stock->setCode($dbs['stockcode']);
					$gets = $stock->getFirstStock();
					$list .= '
						<tr>
							<td width="760" align="left" height="30" colspan="4">&nbsp;&nbsp;<b>'.htmlspecialchars($gets['stockcode'].' | '.$gets['generalname']).'</b></td>
						</tr>
					';
					$addsql = '';
					if ($statususer == 1 && !empty($textimplodehbid)){
						$addsql = ' AND d.buyno IN ('.$textimplodehbid.')';
					}
					$dbdetail = $db->fetch_all("SELECT SUM(d.quantity) AS qty, d.unitquantity, h.buyno, h.suppliercode FROM detailbuy d INNER JOIN headerbuy h ON d.buyno=h.buyno WHERE d.stockcode='".$dbs['stockcode']."' AND (d.buydate >= '".$startdate."' AND d.buydate <= '".$enddate."')".$addsql." GROUP BY d.buyno");
					if (sizeof($dbdetail) > 0){
						$trx = 0;
						$qtrx = 0;
						foreach ($dbdetail as $dbd){
							$supplier->setCode($dbd['suppliercode']);
							$getc = $supplier->getsupplierDetail();
							
							if ($statususer == 1){
								$dbd['qty'] = floor((100-$discount['extradisc'])/100 * $dbd['qty']);
								if ($dbd['qty'] < 1){
									$dbd['qty'] = 1;
								}
							}
							
							$list .= '
								<tr>
									<td width="150" align="left" height="30" class="detailitem">'.htmlspecialchars($dbd['buyno']).'</td>
									<td width="250" align="left" height="30" class="detailitem">'.htmlspecialchars($getc['suppliername']).'</td>
									<td width="200" align="right" height="30" class="detailitem">'.number_format($dbd['qty'],2,",",".").'</td>
									<td width="160" align="center" height="30" class="detailitem">'.htmlspecialchars($dbd['unitquantity']).'</td>
								</tr>
							';
							$trx++;
							$qtrx += $dbd['qty'];
						}
						$list .= '
							<tr>
								<td width="150" align="center" height="30"><b>'.number_format($trx,0,",",".").'</b></td>
								<td width="250" align="left" height="30">&nbsp;<b>TOTAL</b></td>
								<td width="200" align="right" height="30"><b>'.number_format($qtrx,2,",",".").'</b></td>
								<td width="160" align="center" height="30"><b>'.htmlspecialchars($dbd['unitquantity']).'</b></td>
							</tr>
						';
					}
				}
			}
		}
		else{
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
						array_push($arrhbid,"'".$gatr['saleno']."'");
					}
					$textimplodehbid = '';
					if (sizeof($arrhbid) > 0){
						$textimplodehbid = implode(",",$arrhbid);
						$dbsale = $db->fetch_all("SELECT SUM(quantity) AS q, stockcode FROM detailsale WHERE saleno IN (".$textimplodehbid.")".$sql." GROUP BY stockcode ORDER BY q DESC");
					}
				}
			}
			else{*/
				$dbsale = $db->fetch_all("SELECT SUM(quantity) AS q, stockcode FROM detailsale WHERE (saledate >= '".$startdate."' AND saledate <= '".$enddate."') GROUP BY stockcode ORDER BY q DESC");
			//}
			if (sizeof($dbsale) > 0){
				foreach ($dbsale as $dbs){
					$stock->setCode($dbs['stockcode']);
					$gets = $stock->getFirstStock();
					$list .= '
						<tr>
							<td width="760" align="left" height="30" colspan="4">&nbsp;&nbsp;<b>'.htmlspecialchars($gets['stockcode'].' | '.$gets['generalname']).'</b></td>
						</tr>
					';
					$dbdetail = $db->fetch_all("SELECT SUM(d.quantity) AS qty, d.unitquantity, h.saleno, h.customercode FROM detailsale d INNER JOIN headersale h ON d.saleno=h.saleno WHERE d.stockcode='".$dbs['stockcode']."' AND (d.saledate >= '".$startdate."' AND d.saledate <= '".$enddate."') GROUP BY d.saleno");
					if (sizeof($dbdetail) > 0){
						$trx = 0;
						$qtrx = 0;
						foreach ($dbdetail as $dbd){
							$customer->setCode($dbd['customercode']);
							$getc = $customer->getcustomerDetail();
							
							if ($statususer == 1){
								$dbd['qty'] = floor((100-$discount['extradisc'])/100 * $dbd['qty']);
								if ($dbd['qty'] < 1){
									$dbd['qty'] = 1;
								}
							}
							
							$list .= '
								<tr>
									<td width="150" align="left" height="30" class="detailitem">'.htmlspecialchars($dbd['saleno']).'</td>
									<td width="250" align="left" height="30" class="detailitem">'.htmlspecialchars($getc['customername']).'</td>
									<td width="200" align="right" height="30" class="detailitem">'.number_format($dbd['qty'],2,",",".").'</td>
									<td width="160" align="center" height="30" class="detailitem">'.htmlspecialchars($dbd['unitquantity']).'</td>
								</tr>
							';
							$trx++;
							$qtrx += $dbd['qty'];
						}
						$list .= '
							<tr>
								<td width="150" align="center" height="30"><b>'.number_format($trx,2,",",".").'</b></td>
								<td width="250" align="left" height="30">&nbsp;<b>TOTAL</b></td>
								<td width="200" align="right" height="30"><b>'.number_format($qtrx,2,",",".").'</b></td>
								<td width="160" align="center" height="30"><b>'.htmlspecialchars($dbd['unitquantity']).'</b></td>
							</tr>
						';
					}
				}
			}
		}
	}
	else{
		$printtemplate = 'reportactivestuffinit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>