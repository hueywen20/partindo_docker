<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/supplier.php";
	require_once "class/customer.php";
	require_once "class/Assembly.php";
	require_once "class/DeAssembly.php";
	require_once "class/units.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$supplier = new supplier();
	$customer = new customer();
	$units = new unit();
	$assembly = new Assembly();
	$deassembly = new DeAssembly();
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_GET['view'] == 'stockminimum'){
	
		if (empty($useraccess['report_minstock'])){
			redirecting('index.php');
		}
	
		$printtemplate = 'reportstockminimum';
		$dbstockall = $db->fetch_all("SELECT * FROM stock WHERE realremaining <= minqty ORDER BY stockcode");
		$list = '';
		if (sizeof($dbstockall) > 0){
			foreach ($dbstockall as $dsa){
				$units->setCode($dsa['unitcode']);
				$getunit = $units->getunitDetail();
				$unitquantity = $getunit['lunit'];
				$list .= '
					<tr>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($dsa['stockcode']).'</td>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($dsa['generalname']).'</td>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($dsa['brandcode']).'</td>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($dsa['typecode']).'</td>
						<td align="right" height="30" class="detailitem">'.number_format($dsa['buyminprice'],2,",",".").'</td>
						<td align="right" height="30" class="detailitem">'.number_format($dsa['realremaining'],2,",",".").'</td>
						<td align="center" height="30" class="detailitem">'.htmlspecialchars($unitquantity).'</td>
					</tr>
				';
			}
		}
	}
	else if ($_GET['view'] == 'period'){
	
		if (empty($useraccess['report_stock'])){
			redirecting('index.php');
		}
		
		if (!empty($_POST['date'])){
			$printtemplate = 'reportstockperiod';
			$dateint = strtotime($_POST['date'].' 23:59:59');
			if (!empty($_POST['stockcode'])){
				$checkchars = strpos($_POST["stockcode"],"||");
				if ($checkchars !== false){
					$_POST["stockcode"] = substr($_POST["stockcode"],0,$checkchars);
				}
				
				//get purchase
				if ($statususer == 1){
					/*$getalltr = $db->fetch_one("SELECT SUM(hb.totalbuy) AS totalbuys FROM headerbuy hb INNER JOIN detailbuy db ON hb.buyno = db.buyno WHERE db.stockcode='".$_POST['stockcode']."' AND hb.buydate <= '".$dateint."' ORDER BY hb.buydate");
					$totaltransaction = $getalltr['totalbuys'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT hb.* FROM headerbuy hb INNER JOIN detailbuy db ON hb.buyno = db.buyno WHERE db.stockcode='".$_POST['stockcode']."' AND hb.buydate <= '".$dateint."' ORDER BY hb.totalbuy");
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
						if (sizeof($arrhbid) > 0){
							$dbpurchase = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuy WHERE buyno IN (".implode(",",$arrhbid).") AND stockcode='".$_POST['stockcode']."' AND buydate <= '".$dateint."'");
						}
					}*/
					$dbpurch = $db->fetch_all("SELECT * FROM detailbuy WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND buydate <= '".$db->clean($dateint)."'");
					if (sizeof($dbpurch) > 0){
						foreach ($dbpurch as $dpch){
							$dpch['qty'] = floor((100-$discount['extradisc'])/100 * $dpch['quantity']);
							if ($dpch['qty'] < 1){
								$dpch['qty'] = 1;
							}
							$dbpurchase['q'] += $dpch['qty'];
						}
					}
				}
				else{
					$dbpurchase = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuy WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND buydate <= '".$db->clean($dateint)."'");
				}
				//get sale
				if ($statususer == 1){
					/*$getalltr = $db->fetch_one("SELECT SUM(hs.totalsale) AS totalsales FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE ds.stockcode='".$_POST['stockcode']."' AND hs.saledate <= '".$dateint."' ORDER BY hs.saledate");
					$totaltransaction = $getalltr['totalsales'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT hs.* FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE ds.stockcode='".$_POST['stockcode']."' AND hs.saledate <= '".$dateint."' ORDER BY hs.totalsale");
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
						if (sizeof($arrhbid) > 0){
							$dbsale = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsale WHERE saleno IN (".implode(",",$arrhbid).") AND stockcode='".$_POST['stockcode']."' AND saledate <= '".$dateint."'");
						}
					}*/
					$dbfa = $db->fetch_all("SELECT * FROM detailsale WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND saledate <= '".$db->clean($dateint)."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							if ($rsfa['qty'] < 1){
								$rsfa['qty'] = 1;
							}
							$dbsale['q'] += $rsfa['qty'];
						}
					}
				}
				else{
					$dbsale = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsale WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND saledate <= '".$db->clean($dateint)."'");
					//echo "SELECT SUM(quantity) AS q FROM detailsale WHERE stockcode='".$_POST['stockcode']."' AND saledate <= '".$dateint."'";
				}
				//get purchase return
				if ($statususer == 1){
					/*$getalltr = $db->fetch_one("SELECT SUM(hb.totalbuyr) AS totalbuyrs FROM headerbuyr hb INNER JOIN detailbuyr db ON hb.buyrid = db.buyrid WHERE db.stockcode='".$_POST['stockcode']."' AND hb.buyrdate <= '".$dateint."' ORDER BY hb.buyrdate");
					$totaltransaction = $getalltr['totalbuyrs'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT hb.* FROM headerbuyr hb INNER JOIN detailbuyr db ON hb.buyrid = db.buyrid WHERE db.stockcode='".$_POST['stockcode']."' AND hb.buyrdate <= '".$dateint."' ORDER BY hb.totalbuyr");
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
							$dbpurchaser = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuyr WHERE buyrid IN (".implode(",",$arrhbid).") AND stockcode='".$_POST['stockcode']."' AND buyrdate <= '".$dateint."'");
						}
					}*/
					$dbfa = $db->fetch_all("SELECT * FROM detailbuyr WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND buyrdate <= '".$db->clean($dateint)."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							if ($rsfa['qty'] < 1){
								$rsfa['qty'] = 1;
							}
							$dbpurchaser['q'] += $rsfa['qty'];
						}
					}
				}
				else{
					$dbpurchaser = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuyr WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND buyrdate <= '".$db->clean($dateint)."'");
				}
				//get sale return
				if ($statususer == 1){
					/*$getalltr = $db->fetch_one("SELECT SUM(hs.totalsaler) AS totalsalers FROM headersaler hs INNER JOIN detailsaler ds ON hs.salerid = ds.salerid WHERE ds.stockcode='".$_POST['stockcode']."' AND hs.salerdate <= '".$dateint."' ORDER BY hs.salerdate");
					$totaltransaction = $getalltr['totalsalers'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT hs.* FROM headersaler hs INNER JOIN detailsaler ds ON hs.salerid = ds.salerid WHERE ds.stockcode='".$_POST['stockcode']."' AND hs.salerdate <= '".$dateint."' ORDER BY hs.totalsaler");
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
							$dbsaler = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsaler WHERE salerid IN (".implode(",",$arrhbid).") AND stockcode='".$_POST['stockcode']."' AND salerdate <= '".$dateint."'");
						}
					}*/
					$dbfa = $db->fetch_all("SELECT * FROM detailsaler WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND salerdate <= '".$db->clean($dateint)."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							if ($rsfa['qty'] < 1){
								$rsfa['qty'] = 1;
							}
							$dbsaler['q'] += $rsfa['qty'];
						}
					}
				}
				else{
					$dbsaler = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsaler WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND salerdate <= '".$db->clean($dateint)."'");
				}
				//get adjust in
				if ($statususer == 1){
					/*$getalltr = $db->fetch_one("SELECT SUM(ha.totalain) AS totalains FROM headeradjustin ha INNER JOIN detailadjustin da ON ha.ainid = da.ainid WHERE da.stockcode='".$_POST['stockcode']."' AND ha.aindate <= '".$dateint."' ORDER BY ha.aindate");
					$totaltransaction = $getalltr['totalains'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT ha.* FROM headeradjustin ha INNER JOIN detailadjustin da ON ha.ainid = da.ainid WHERE da.stockcode='".$_POST['stockcode']."' AND ha.aindate <= '".$dateint."' ORDER BY ha.totalain");
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
							$dbain = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustin WHERE ainid IN (".implode(",",$arrhbid).") AND stockcode='".$_POST['stockcode']."' AND aindate <= '".$dateint."'");
						}
					}*/
					$dbfa = $db->fetch_all("SELECT * FROM detailadjustin WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND aindate <= '".$db->clean($dateint)."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							if ($rsfa['qty'] < 1){
								$rsfa['qty'] = 1;
							}
							$dbain['q'] += $rsfa['qty'];
						}
					}
				}
				else{
					$dbain = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustin WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND aindate <= '".$db->clean($dateint)."'");
				}
				//get adjust out
				if ($statususer == 1){
					/*$getalltr = $db->fetch_one("SELECT SUM(ha.totalaout) AS totalaouts FROM headeradjustout ha INNER JOIN detailadjustout da ON ha.aoutid = da.aoutid WHERE da.stockcode='".$_POST['stockcode']."' AND ha.aoutdate <= '".$dateint."' ORDER BY ha.aoutdate");
					$totaltransaction = $getalltr['totalaouts'];
					$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
					
					$getalltr = $db->fetch_all("SELECT ha.* FROM headeradjustout ha INNER JOIN detailadjustout da ON ha.aoutid = da.aoutid WHERE da.stockcode='".$_POST['stockcode']."' AND ha.aoutdate <= '".$dateint."' ORDER BY ha.totalaout");
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
							$dbaout = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustout WHERE aoutid IN (".implode(",",$arrhbid).") AND stockcode='".$_POST['stockcode']."' AND aoutdate <= '".$dateint."'");
						}
					}*/
					$dbfa = $db->fetch_all("SELECT * FROM detailadjustout WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND aoutdate <= '".$db->clean($dateint)."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							if ($rsfa['qty'] < 1){
								$rsfa['qty'] = 1;
							}
							$dbaout['q'] += $rsfa['qty'];
						}
					}
				}
				else{
					$dbaout = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustout WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND aoutdate <= '".$db->clean($dateint)."'");
				}
				
				$getassemblyp = $assembly->getAssemblyParent($_POST['stockcode']);
				//get assembly detail
				$getassemblypsize = sizeof($getassemblyp);
				$assemblyscq = array();
				$assemblyscqt = array();
				if ($getassemblypsize > 0){
					$idparentasm = '';
					foreach ($getassemblyp as $gasbp){
						$idparentasm .= ',\''.$gasbp['stockcode'].'\'';
						$assemblyscq[$gasbp['stockcode']] = $gasbp['sccquantity'];
						
						$gettotalqty = $db->fetch_one("SELECT SUM(sccquantity) AS totalq FROM detailstockassembly WHERE stockcode='".$gasbp['stockcode']."'");
						$assemblyscqt[$gasbp['stockcode']] = $gettotalqty['totalq'];
					}
					$sql = ' AND a.stockcode IN ('.substr($idparentasm,1).')';
					//get sale
					$dbsaleasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno WHERE a.saledate <= '".$dateint."'".$sql." ORDER BY a.saledate");
					$dbsasm['q'] = 0;
					if (sizeof($dbsaleasm) > 0){
						foreach ($dbsaleasm as $dbsa){
							if ($statususer == 1){
								$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
								if ($dbsa['quantity'] < 1){
									$dbsa['quantity'] = 1;
								}
							}
							$dbsasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
						}
					}
					
					//get sale return
					$dbsalerasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid WHERE a.salerdate <= '".$dateint."'".$sql." ORDER BY a.salerdate");
					$dbsrasm['q'] = 0;
					if (sizeof($dbsalerasm) > 0){
						foreach ($dbsalerasm as $dbsa){
							if ($statususer == 1){
								$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
								if ($dbsa['quantity'] < 1){
									$dbsa['quantity'] = 1;
								}
							}
							$dbsrasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
						}
					}
				}
				
				//get deassembly
				if ($statususer == 1){
					$dbfa = $db->fetch_all("SELECT * FROM logdeassembly WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND logdate <= '".$db->clean($dateint)."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							if ($rsfa['qty'] < 1){
								$rsfa['qty'] = 1;
							}
							$dbdeasm['q'] += $rsfa['qty'];
						}
					}
				}
				else{
					$dbdeasm = $db->fetch_one("SELECT SUM(quantity) AS q FROM logdeassembly WHERE stockcode='".$db->clean($_POST['stockcode'])."' AND logdate <= '".$db->clean($dateint)."'");
				}
				
				$stock->setCode($_POST['stockcode']);
				$stockdetail = $stock->getFirstStock();
				$units->setCode($stockdetail['unitcode']);
				$getunit = $units->getunitDetail();
				$unitquantity = $getunit['lunit'];

				if ($statususer == 1){
					$stockdetail['quantity'] = floor((100-$discount['extradisc'])/100 * $stockdetail['quantity']);
					if ($stockdetail['quantity'] < 1){
						$stockdetail['quantity'] = 1;
					}
				}
				
				$stocknow = $stockdetail['quantity']+$dbpurchase['q']-$dbsale['q']-$dbpurchaser['q']+$dbsaler['q']+$dbain['q']-$dbaout['q']+$dbdeasm['q']-$dbsasm['q']+$dbsrasm['q'];
				//echo $stockdetail['quantity'].'-'.$dbpurchase['q'].'-'.$dbsale['q'].'-'.$dbpurchaser['q'].'-'.$dbsaler['q'].'-'.$dbain['q'].'-'.$dbaout['q'].'-'.$dbdeasm['q'].'-'.$dbsasm['q'].'-'.$dbsrasm['q'];
				
				$list = '
					<tr>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($_POST['stockcode']).'</td>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($stockdetail['generalname']).'</td>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($stockdetail['brandcode']).'</td>
						<td align="left" height="30" class="detailitem">'.htmlspecialchars($stockdetail['typecode']).'</td>
						<td align="right" height="30" class="detailitem">'.number_format($stockdetail['buyminprice'],2,",",".").'</td>
						<td align="right" height="30" class="detailitem">'.number_format($stocknow,2,",",".").'</td>
						<td align="center" height="30" class="detailitem">'.htmlspecialchars($unitquantity).'</td>
					</tr>
				';
			}
			else{
				$dbstock = $stock->getListStock(0);
				if (sizeof($dbstock) > 0){
					foreach ($dbstock as $dbs){
						unset($dbpurchase);
						unset($dbsale);
						unset($dbpurchaser);
						unset($dbsaler);
						unset($dbain);
						unset($dbaout);
						
						//get purchase
						if ($statususer == 1){
							$dbpurch = $db->fetch_all("SELECT * FROM detailbuy WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND buydate <= '".$db->clean($dateint)."'");
							if (sizeof($dbpurch) > 0){
								foreach ($dbpurch as $dpch){
									$dpch['qty'] = floor((100-$discount['extradisc'])/100 * $dpch['quantity']);
									if ($dpch['qty'] < 1){
										$dpch['qty'] = 1;
									}
									$dbpurchase['q'] += $dpch['qty'];
								}
							}
						}
						else{
							$dbpurchase = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuy WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND buydate <= '".$db->clean($dateint)."'");
						}
						//get sale
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailsale WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND saledate <= '".$db->clean($dateint)."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									if ($rsfa['qty'] < 1){
										$rsfa['qty'] = 1;
									}
									$dbsale['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbsale = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsale WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND saledate <= '".$db->clean($dateint)."'");
						}
						//get purchase return
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailbuyr WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND buyrdate <= '".$db->clean($dateint)."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									if ($rsfa['qty'] < 1){
										$rsfa['qty'] = 1;
									}
									$dbpurchaser['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbpurchaser = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuyr WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND buyrdate <= '".$db->clean($dateint)."'");
						}
						//get sale return
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailsaler WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND salerdate <= '".$db->clean($dateint)."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									if ($rsfa['qty'] < 1){
										$rsfa['qty'] = 1;
									}
									$dbsaler['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbsaler = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsaler WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND salerdate <= '".$db->clean($dateint)."'");
						}
						//get adjust in
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailadjustin WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND aindate <= '".$db->clean($dateint)."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									if ($rsfa['qty'] < 1){
										$rsfa['qty'] = 1;
									}
									$dbain['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbain = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustin WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND aindate <= '".$db->clean($dateint)."'");
						}
						//get adjust out
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailadjustout WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND aoutdate <= '".$db->clean($dateint)."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									if ($rsfa['qty'] < 1){
										$rsfa['qty'] = 1;
									}
									$dbaout['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbaout = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustout WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND aoutdate <= '".$db->clean($dateint)."'");
						}
						
						$getassemblyp = $assembly->getAssemblyParent($dbs['stockcode']);
						//get assembly detail
						$getassemblypsize = sizeof($getassemblyp);
						$assemblyscq = array();
						$assemblyscqt = array();
						if ($getassemblypsize > 0){
							$idparentasm = '';
							foreach ($getassemblyp as $gasbp){
								$idparentasm .= ',\''.$gasbp['stockcode'].'\'';
								$assemblyscq[$gasbp['stockcode']] = $gasbp['sccquantity'];
								
								$gettotalqty = $db->fetch_one("SELECT SUM(sccquantity) AS totalq FROM detailstockassembly WHERE stockcode='".$db->clean($gasbp['stockcode'])."'");
								$assemblyscqt[$gasbp['stockcode']] = $gettotalqty['totalq'];
							}
							$sql = ' AND a.stockcode IN ('.substr($idparentasm,1).')';
							//get sale
							$dbsaleasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno WHERE a.saledate <= '".$db->clean($dateint)."'".$sql." ORDER BY a.saledate");
							$dbsasm['q'] = 0;
							if (sizeof($dbsaleasm) > 0){
								foreach ($dbsaleasm as $dbsa){
									if ($statususer == 1){
										$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
										if ($dbsa['quantity'] < 1){
											$dbsa['quantity'] = 1;
										}
									}
									$dbsasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
								}
							}
							
							//get sale return
							$dbsalerasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid WHERE a.salerdate <= '".$db->clean($dateint)."'".$sql." ORDER BY a.salerdate");
							$dbsrasm['q'] = 0;
							if (sizeof($dbsalerasm) > 0){
								foreach ($dbsalerasm as $dbsa){
									if ($statususer == 1){
										$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
										if ($dbsa['quantity'] < 1){
											$dbsa['quantity'] = 1;
										}
									}
									$dbsrasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
								}
							}
						}
						
						//get deassembly
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM logdeassembly WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND logdate <= '".$db->clean($dateint)."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									if ($rsfa['qty'] < 1){
										$rsfa['qty'] = 1;
									}
									$dbdeasm['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbdeasm = $db->fetch_one("SELECT SUM(quantity) AS q FROM logdeassembly WHERE stockcode='".$db->clean($dbs['stockcode'])."' AND logdate <= '".$db->clean($dateint)."'");
						}
						
						$stock->setCode($dbs['stockcode']);
						$stockdetail = $stock->getFirstStock();
						$units->setCode($stockdetail['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];

						if ($statususer == 1){
							$stockdetail['quantity'] = floor((100-$discount['extradisc'])/100 * $stockdetail['quantity']);
							if ($stockdetail['quantity'] < 1){
								$stockdetail['quantity'] = 1;
							}
						}
						
						$stocknow = $stockdetail['quantity']+$dbpurchase['q']-$dbsale['q']-$dbpurchaser['q']+$dbsaler['q']+$dbain['q']-$dbaout['q']+$dbdeasm['q']-$dbsasm['q']+$dbsrasm['q'];
						
						$list .= '
							<tr>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($dbs['stockcode']).'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($stockdetail['generalname']).'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($stockdetail['brandcode']).'</td>
								<td align="left" height="30" class="detailitem">'.htmlspecialchars($stockdetail['typecode']).'</td>
								<td align="right" height="30" class="detailitem">'.number_format($stockdetail['buyminprice'],2,",",".").'</td>
								<td align="right" height="30" class="detailitem">'.number_format($stocknow,2,",",".").'</td>
								<td align="center" height="30" class="detailitem">'.htmlspecialchars($unitquantity).'</td>
							</tr>
						';
					}
				}
			}
		}
		else{
			$printtemplate = 'reportstockperiodinit';
		}
	}
	else if ($_GET['view'] == 'stockcard'){
	
		if (empty($useraccess['report_stockcard'])){
			redirecting('index.php');
		}
		
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$printtemplate = 'reportstockcard';
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			if (!empty($_POST['stockcode'])){
				$checkchars = strpos($_POST["stockcode"],"||");
				if ($checkchars !== false){
					$_POST["stockcode"] = substr($_POST["stockcode"],0,$checkchars);
				}
				$a['stockcode'] = $_POST['stockcode'];
				$dbstock = array();
				array_push($dbstock,$a);
			}
			else{
				$dbstock = $stock->getListStock();
			}
			
			if (sizeof($dbstock) > 0){
				foreach ($dbstock as $dbs){
					$lists = '';
				
					$_POST['stockcode'] = $dbs['stockcode'];
					$stock->setCode($_POST['stockcode']);
					$firststock = $stock->getFirstStock();
					
					$dateint = strtotime($_POST['datestart'])-1;
					
					if ($firststock['assembly'] == 1){
						$stockperiodstart = 0;
					}
					else{
						//get purchase
						if ($statususer == 1){
							$dbpurch = $db->fetch_all("SELECT * FROM detailbuy WHERE stockcode='".$_POST['stockcode']."' AND buydate <= '".$dateint."'");
							if (sizeof($dbpurch) > 0){
								foreach ($dbpurch as $dpch){
									$dpch['qty'] = floor((100-$discount['extradisc'])/100 * $dpch['quantity']);
									$dbpurchase['q'] += $dpch['qty'];
								}
							}
						}
						else{
							$dbpurchase = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuy WHERE stockcode='".$_POST['stockcode']."' AND buydate <= '".$dateint."'");
						}
						//get sale
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailsale WHERE stockcode='".$_POST['stockcode']."' AND saledate <= '".$dateint."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									$dbsale['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbsale = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsale WHERE stockcode='".$_POST['stockcode']."' AND saledate <= '".$dateint."'");
						}
						//get purchase return
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailbuyr WHERE stockcode='".$_POST['stockcode']."' AND buyrdate <= '".$dateint."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									$dbpurchaser['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbpurchaser = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailbuyr WHERE stockcode='".$_POST['stockcode']."' AND buyrdate <= '".$dateint."'");
						}
						//get sale return
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailsaler WHERE stockcode='".$_POST['stockcode']."' AND salerdate <= '".$dateint."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									$dbsaler['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbsaler = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailsaler WHERE stockcode='".$_POST['stockcode']."' AND salerdate <= '".$dateint."'");
						}
						//get adjust in
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailadjustin WHERE stockcode='".$_POST['stockcode']."' AND aindate <= '".$dateint."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									$dbain['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbain = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustin WHERE stockcode='".$_POST['stockcode']."' AND aindate <= '".$dateint."'");
						}
						//get adjust out
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM detailadjustout WHERE stockcode='".$_POST['stockcode']."' AND aoutdate <= '".$dateint."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									$dbaout['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbaout = $db->fetch_one("SELECT SUM(quantity) AS q FROM detailadjustout WHERE stockcode='".$_POST['stockcode']."' AND aoutdate <= '".$dateint."'");
						}
						
						$getassemblyp = $assembly->getAssemblyParent($_POST['stockcode']);
						//get assembly detail
						$getassemblypsize = sizeof($getassemblyp);
						$assemblyscq = array();
						$assemblyscqt = array();
						if ($getassemblypsize > 0){
							$idparentasm = '';
							foreach ($getassemblyp as $gasbp){
								$idparentasm .= ',\''.$gasbp['stockcode'].'\'';
								$assemblyscq[$gasbp['stockcode']] = $gasbp['sccquantity'];
								
								$gettotalqty = $db->fetch_one("SELECT SUM(sccquantity) AS totalq FROM detailstockassembly WHERE stockcode='".$gasbp['stockcode']."'");
								$assemblyscqt[$gasbp['stockcode']] = $gettotalqty['totalq'];
							}
							$sql = ' AND a.stockcode IN ('.substr($idparentasm,1).')';
							//get sale
							$dbsaleasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno WHERE a.saledate <= '".$dateint."'".$sql." ORDER BY a.saledate");
							$dbsasm['q'] = 0;
							if (sizeof($dbsaleasm) > 0){
								foreach ($dbsaleasm as $dbsa){
									if ($statususer == 1){
										$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
									}
									$dbsasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
								}
							}
							
							//get sale return
							$dbsalerasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid WHERE a.salerdate <= '".$dateint."'".$sql." ORDER BY a.salerdate");
							$dbsrasm['q'] = 0;
							if (sizeof($dbsalerasm) > 0){
								foreach ($dbsalerasm as $dbsa){
									if ($statususer == 1){
										$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
									}
									$dbsrasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
								}
							}
						}
						
						//get deassembly
						if ($statususer == 1){
							$dbfa = $db->fetch_all("SELECT * FROM logdeassembly WHERE stockcode='".$_POST['stockcode']."' AND logdate <= '".$dateint."'");
							if (sizeof($dbfa) > 0){
								foreach ($dbfa as $rsfa){
									$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
									$dbdeasm['q'] += $rsfa['qty'];
								}
							}
						}
						else{
							$dbdeasm = $db->fetch_one("SELECT SUM(quantity) AS q FROM logdeassembly WHERE stockcode='".$_POST['stockcode']."' AND logdate <= '".$dateint."'");
						}
						
						$units->setCode($firststock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];

						if ($statususer == 1){
							$firststock['quantity'] = floor((100-$discount['extradisc'])/100 * $firststock['quantity']);
						}
						
						$stockperiodstart = $firststock['quantity']+$dbpurchase['q']-$dbsale['q']-$dbpurchaser['q']+$dbsaler['q']+$dbain['q']-$dbaout['q']+$dbdeasm['q']-$dbsasm['q']+$dbsrasm['q'];
					}
					
					$remainings = $stockperiodstart;
					
					$stpn = $stock->getAllPartNo();
					$firstcreated = '';
					if (sizeof($stpn) > 0){
						foreach ($stpn as $stn){
							if (empty($firstcreated)){
								$firstcreated = $stn['partno'];
								break;
							}
						}
					}
					if ($firststock['expdate'] == 0){
						$mexdate = 0;
					}
					else{
						$mexdate = date("d-m-Y",$firststock['expdate']);
					}
					$lists .= '
						<tr>
							<td align="center">Stok Awal Periode</td>
							<td align="left">'.htmlspecialchars($firstcreated).'</td>
							<td align="left">'.htmlspecialchars($firststock['generalname']).'</td>
							<td align="left">'.htmlspecialchars($firststock['brandcode']).'</td>
							<td align="left">'.htmlspecialchars($firststock['typecode']).'</td>
							<td align="left">Stok Awal Periode</td>
							<td align="right">'.number_format($stockperiodstart,0,",",".").'</td>
							<td align="right">0</td>
							<td align="right">'.number_format($stockperiodstart,0,",",".").'</td>
							<td align="right">'.number_format($firststock['buyprice'],0,",",".").'</td>
							<td align="right">0</td>
							<td align="center">'.$mexdate.'</td>
							<td align="left"></td>
							<td align="left"></td>
						</tr>
					';
					
					$rstextasb = '';
					$listsplitasb = '';
					$listsplit2asb = '';
					if ($firststock['assembly'] == 1){
						$assembly->setCode($firststock['stockcode']);
						$allcomponent = $assembly->getAssemblyComponent();
						$szarrscname = sizeof($allcomponent);
						if ($szarrscname > 0){
							if ($szarrscname > 1){
								$rstextasb = ' rowspan="'.$szarrscname.'"';
							}
							$io = 0; 
							foreach ($allcomponent as $arrscname){
								if ($io == 0){
									$listsplitasb = '<td align="left">'.htmlspecialchars($arrscname['stockcodecomponent']).'</td>';
								}
								else{
									$listsplit2asb .= '
										<tr>
											<td align="'.$align.'">'.htmlspecialchars($arrscname['stockcodecomponent']).'</td>
										</tr>';
								}
								$io++;
							}
						}
						$listsplitasb = addslashes($listsplitasb);
						$listsplit2asb = addslashes($listsplit2asb);
					}
					
					$totalbuy = 0;
					$totalsale = 0;
					$totalbuyr = 0;
					$totalsaler = 0;
					$totalain = 0;
					$totalaout = 0;
					$getassemblyprt = $assembly->getAssemblyParent($firststock['stockcode']);
					$detailstock = $stock->getStockDetail($getassemblyprt,$startdate,$enddate);
					if (sizeof($detailstock) > 0){
						foreach ($detailstock as $dtls){
							$brand->setCode($dtls['brandcode']);
							$getbrandname = $brand->getBrandDetail();
							if (empty($getbrandname['brandname']))
								$getbrandname['brandname'] = $dtls['brandcode'];
							$type->setCode($dtls['typecode']);
							$gettypename = $type->gettypeDetail();
							if (empty($gettypename['typename']))
								$gettypename['typename'] = $dtls['typecode'];
								
							if ($dtls['status'] == 'purchase'){
								$dtls['stockin'] = discq($dtls['stockin']);
								$remainings += $dtls['stockin'];
								$supplier->setCode($dtls['sc']);
								$getsuppliername = $supplier->getsupplierDetail();
								$getscname = $getsuppliername['suppliername'];
								if (!empty($dtls['expdate'])){
									$expdate = date("d-m-Y",$dtls['expdate']);
								}
								else{
									$expdate = 0;
								}
								$align = 'left';
								$totalbuy += $dtls['stockin'] * $dtls['buyprice'];
							}
							else if ($dtls['status'] == 'sale'){
								if ($firststock['assembly'] == 1){
									$dtls['stockout'] = discq($dtls['stockout']);
									$remainings += $dtls['stockout'];
									eval("\$listsplitasbtext = \"$listsplitasb\";");
									eval("\$listsplit2asbtext = \"$listsplit2asb\";");
									
									$getdsids = substr($dtls['dbid'],strpos($dtls['dbid'],'-')+1);
									$dballsaleitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$getdsids."'");
									$totalpurchasm = 0;
									$totalpurchasm = $dballsaleitem['price'];
									
									$lists .= '
										<tr>
											<td align="center"'.$rstextasb.'>'.date("d-m-Y",$dtls['date']).'</td>
											<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['partno']).'</td>
											<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['stockname']).'</td>
											<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['brandcode']).'</td>
											<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['typecode']).'</td>
											'.$listsplitasbtext.'
											<td align="right"'.$rstextasb.'>'.number_format($dtls['stockout'],0,",",".").'</td>
											<td align="right"'.$rstextasb.'>0</td>
											<td align="right"'.$rstextasb.'>'.number_format($remainings,0,",",".").'</td>
											<td align="right"'.$rstextasb.'>'.number_format($totalpurchasm,0,",",".").'</td>
											<td align="right"'.$rstextasb.'>0</td>
											<td align="center"'.$rstextasb.'>'.$expdate.'</td>
											<td align="'.$align.'"'.$rstextasb.'>'.htmlspecialchars($dtls['invc']).'</td>
											<td align="left"'.$rstextasb.'>Assembly</td>
										</tr>
									'.$listsplit2asbtext;
									
									$totalbuy += $dtls['stockout'] * $totalpurchasm;
								}
								$dtls['stockout'] = discq($dtls['stockout']);
								$remainings -= $dtls['stockout'];
								$customer->setCode($dtls['sc']);
								$getcustomername = $customer->getcustomerDetail();
								$getscname = $getcustomername['customername'];
								$expdate = '0';
								$align = 'right';
								$totalsale += $dtls['stockout'] * $dtls['saleprice'];
							}
							else if ($dtls['status'] == 'saleasm'){
								$dtls['stockout'] = discq($dtls['stockout']);
								$remainings -= $dtls['stockout'];
								$getscname = $dtls['sc'];
								$expdate = '0';
								$align = 'left';
								$dtls['buyprice'] = 0;
							}
							else if ($dtls['status'] == 'buyreturn'){
								$dtls['stockout'] = discq($dtls['stockout']);
								$remainings -= $dtls['stockout'];
								$customer->setCode($dtls['sc']);
								$getsuppliername = $supplier->getsupplierDetail();
								$getscname = $getsuppliername['suppliername'];
								$expdate = '0';
								$align = 'left';
								$totalbuyr += $dtls['stockout'] * $dtls['buyprice'];
							}
							else if ($dtls['status'] == 'salereturn'){
								$dtls['stockin'] = discq($dtls['stockin']);
								$remainings += $dtls['stockin'];
								$customer->setCode($dtls['sc']);
								$getcustomername = $customer->getcustomerDetail();
								$getscname = $getcustomername['customername'];
								$expdate = '0';
								$align = 'right';
								$totalsaler += $dtls['stockin'] * $dtls['saleprice'];
							}
							else if ($dtls['status'] == 'salerasm'){
								$dtls['stockin'] = discq($dtls['stockin']);
								$remainings += $dtls['stockin'];
								$getscname = $dtls['sc'];
								$expdate = '0';
								$align = 'left';
								$totalsaler += $dtls['stockin'] * $dtls['saleprice'];
							}
							else if ($dtls['status'] == 'adjustin'){
								$dtls['stockin'] = discq($dtls['stockin']);
								$remainings += $dtls['stockin'];
								$getscname = 'Penyesuaian(+)';
								$expdate = '0';
								$align = 'left';
								$totalain += $dtls['stockin'] * $dtls['buyprice'];
							}
							else if ($dtls['status'] == 'adjustout'){
								$dtls['stockout'] = discq($dtls['stockout']);
								$remainings -= $dtls['stockout'];
								$getscname = 'Penyesuaian(-)';
								$expdate = '0';
								$align = 'right';
								$totalaout += $dtls['stockout'] * $dtls['saleprice'];
							}
							else if ($dtls['status'] == 'logdeassembly' && $dtls['stockin'] > 0){
								$dtls['stockin'] = discq($dtls['stockin']);
								$remainings += $dtls['stockin'];
								$getscname = $dtls['description'];
								$expdate = '0';
								$align = 'left';
								$dtls['description'] = '';
								$totalbuy += $dtls['stockin'] * $dtls['buyprice'];
							}
							else if ($dtls['status'] == 'logdeassembly' && $dtls['stockout'] > 0){
								$dtls['stockout'] = discq($dtls['stockout']);
								$remainings -= $dtls['stockout'];
								$getscname = $dtls['description'];
								$expdate = '0';
								$align = 'left';
								$dtls['description'] = '';
								$totalsale += $dtls['stockout'] * $dtls['buyprice'];
							}
							else if ($dtls['status'] == 'logdeassemblyparent' && $dtls['stockin'] > 0){
								$dtls['stockin'] = discq($dtls['stockin']);
								$remainings += $dtls['stockin'];
								$getscname = $dtls['sc'];
								$expdate = '0';
								$align = 'left';
								$dtls['description'] = 'Re-Assembly';
							}
							else if ($dtls['status'] == 'logdeassemblyparent' && $dtls['stockout'] > 0){
								$dtls['stockout'] = discq($dtls['stockout']);
								$remainings -= $dtls['stockout'];
								$getscname = $dtls['sc'];
								$expdate = '0';
								$align = 'left';
								$dtls['description'] = 'De-Assembly';
							}
							
							if ($dtls['status'] == 'logdeassemblyparent'){
								$rstext = '';
								$listsplit = '';
								$listsplit2 = '';
								$arrscname = explode("|^|",$getscname);
								$szarrscname = sizeof($arrscname);
								if ($szarrscname > 0){
									if ($szarrscname > 1){
										$rstext = ' rowspan="'.$szarrscname.'"';
									}
									$io = 0; 
									for ($io = 0; $io < $szarrscname; $io++){
										if ($io == 0){
											$listsplit = '<td align="'.$align.'">'.htmlspecialchars($arrscname[$io]).'</td>';
										}
										else{
											$listsplit2 .= '
												<tr>
													<td align="'.$align.'">'.htmlspecialchars($arrscname[$io]).'</td>
												</tr>';
										}
									}
									$lists .= '
										<tr>
											<td align="center"'.$rstext.'>'.date("d-m-Y",$dtls['date']).'</td>
											<td align="left"'.$rstext.'>'.htmlspecialchars($dtls['partno']).'</td>
											<td align="left"'.$rstext.'>'.htmlspecialchars($dtls['stockname']).'</td>
											<td align="left"'.$rstext.'>'.htmlspecialchars($dtls['brandcode']).'</td>
											<td align="left"'.$rstext.'>'.htmlspecialchars($dtls['typecode']).'</td>
											'.$listsplit.'
											<td align="right"'.$rstext.'>'.number_format($dtls['stockin'],0,",",".").'</td>
											<td align="right"'.$rstext.'>'.number_format($dtls['stockout'],0,",",".").'</td>
											<td align="right"'.$rstext.'>'.number_format($remainings,0,",",".").'</td>
											<td align="right"'.$rstext.'>'.number_format($dtls['buyprice'],0,",",".").'</td>
											<td align="right"'.$rstext.'>'.number_format($dtls['saleprice'],0,",",".").'</td>
											<td align="center"'.$rstext.'>'.$expdate.'</td>
											<td align="'.$align.'"'.$rstext.'>'.htmlspecialchars($dtls['invc']).'</td>
											<td align="left"'.$rstext.'>'.htmlspecialchars($dtls['description']).'</td>
										</tr>
									'.$listsplit2;
								}
							}
							else{
								$lists .= '
									<tr>
										<td align="center">'.date("d-m-Y",$dtls['date']).'</td>
										<td align="left">'.htmlspecialchars($dtls['partno']).'</td>
										<td align="left">'.htmlspecialchars($dtls['stockname']).'</td>
										<td align="left">'.htmlspecialchars($dtls['brandcode']).'</td>
										<td align="left">'.htmlspecialchars($dtls['typecode']).'</td>
										<td align="'.$align.'">'.htmlspecialchars($getscname).'</td>
										<td align="right">'.number_format($dtls['stockin'],0,",",".").'</td>
										<td align="right">'.number_format($dtls['stockout'],0,",",".").'</td>
										<td align="right">'.number_format($remainings,0,",",".").'</td>
										<td align="right">'.number_format($dtls['buyprice'],0,",",".").'</td>
										<td align="right">'.number_format($dtls['saleprice'],0,",",".").'</td>
										<td align="center">'.$expdate.'</td>
										<td align="'.$align.'">'.htmlspecialchars($dtls['invc']).'</td>
										<td align="left">'.htmlspecialchars($dtls['description']).'</td>
									</tr>
								';
							}
							
							if ($dtls['status'] == 'salereturn' && $firststock['assembly'] == 1){
								$dtls['stockin'] = discq($dtls['stockin']);
								$remainings -= $dtls['stockin'];
								eval("\$listsplitasbtext = \"$listsplitasb\";");
								eval("\$listsplit2asbtext = \"$listsplit2asb\";");
								
								$getdsids = substr($dtls['dbid'],strpos($dtls['dbid'],'-')+1);
								$dballsaleitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$getdsids."'");
								$totalpurchasm = 0;
								$totalpurchasm = $dballsaleitem['price'];
								
								$lists .= '
									<tr>
										<td align="center"'.$rstextasb.'>'.date("d-m-Y",$dtls['date']).'</td>
										<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['partno']).'</td>
										<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['stockname']).'</td>
										<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['brandcode']).'</td>
										<td align="left"'.$rstextasb.'>'.htmlspecialchars($dtls['typecode']).'</td>
										'.$listsplitasbtext.'
										<td align="right"'.$rstextasb.'>0</td>
										<td align="right"'.$rstextasb.'>'.number_format($dtls['stockin'],0,",",".").'</td>
										<td align="right"'.$rstextasb.'>'.number_format($remainings,0,",",".").'</td>
										<td align="right"'.$rstextasb.'>0</td>
										<td align="right"'.$rstextasb.'>0</td>
										<td align="center"'.$rstextasb.'>'.$expdate.'</td>
										<td align="'.$align.'"'.$rstextasb.'>'.htmlspecialchars($dtls['invc']).'</td>
										<td align="left"'.$rstextasb.'>Retur Jual &amp; De-Assembly</td>
									</tr>
								'.$listsplit2asbtext;
							}
						}
					}
					
					$totalbuyf = number_format($totalbuy,2,",",".");
					$totalsalef = number_format($totalsale,2,",",".");
					$totalbuyrf = number_format($totalbuyr,2,",",".");
					$totalsalerf = number_format($totalsaler,2,",",".");
					$totalainf = number_format($totalain,2,",",".");
					$totalaoutf = number_format($totalaout,2,",",".");
					
					$list .= '
						<div align="left" style="width: 100%; padding-top: 10px">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td align="left">Kode Barang : <b>'.$_POST['stockcode'].'</b></td>
							<td align="right">Tanggal Cetak : '.$printdate.'</td>
						</tr>
						</table></div>
						<table border="1" cellpadding="2" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="6%" bgcolor="#DEDEDE">TANGGAL</th>
							<th align="center" width="6%" bgcolor="#DEDEDE">NO PART</th>
							<th align="center" width="11%" bgcolor="#DEDEDE">NAMA</th>
							<th align="center" width="8%" bgcolor="#DEDEDE">MEREK</th>
							<th align="center" width="8%" bgcolor="#DEDEDE">TIPE</th>
							<th align="center" width="11%" bgcolor="#DEDEDE">S / C</th>
							<th align="center" width="5%" bgcolor="#DEDEDE">MASUK</th>
							<th align="center" width="5%" bgcolor="#DEDEDE">KELUAR</th>
							<th align="center" width="5%" bgcolor="#DEDEDE">SISA</th>
							<th align="center" width="8%" bgcolor="#DEDEDE">HARGA BELI</th>
							<th align="center" width="8%" bgcolor="#DEDEDE">HARGA JUAL</th>
							<th align="center" width="6%" bgcolor="#DEDEDE">EXP DATE</th>
							<th align="center" width="6%" bgcolor="#DEDEDE">FAKTUR</th>
							<th align="center" width="7%" bgcolor="#DEDEDE">KETERANGAN</th>
						</tr>
						'.$lists.'
						</table>
						<br>
						<div align="left" style="width: 100%; padding-bottom: 10px; border-bottom: 1px solid #000">
						<table border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td align="left"><b>Total Pembelian</b></td>
							<td align="center"><b>:</b></td>
							<td align="right"><b>'.$totalbuyf.'</b></td>
						</tr>
						<tr>
							<td align="left"><b>Total Penjualan</b></td>
							<td align="center"><b>:</b></td>
							<td align="right"><b>'.$totalsalef.'</b></td>
						</tr>
						<tr>
							<td align="left"><b>Total Retur Pembelian</b></td>
							<td align="center"><b>:</b></td>
							<td align="right"><b>'.$totalbuyrf.'</b></td>
						</tr>
						<tr>
							<td align="left"><b>Total Retur Penjualan</b></td>
							<td align="center"><b>:</b></td>
							<td align="right"><b>'.$totalsalerf.'</b></td>
						</tr>
						<tr>
							<td align="left"><b>Total Penyesuaian (+)</b></td>
							<td align="center"><b>:</b></td>
							<td align="right"><b>'.$totalainf.'</b></td>
						</tr>
						<tr>
							<td align="left"><b>Total Penyesuaian (-)</b></td>
							<td align="center"><b>:</b></td>
							<td align="right"><b>'.$totalaoutf.'</b></td>
						</tr>
						</table></div>
					';
				}
			}
		}
		else{
			$printtemplate = 'reportstockcardinit';
		}
	}
	else if ($_GET['view'] == 'expired'){
		if (empty($useraccess['report_stockexpired'])){
			redirecting('index.php');
		}
		
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$printtemplate = 'reportstockexpired';
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			
			$getexpstock = $db->fetch_all("SELECT * FROM detailbuy WHERE expdate >= '".$startdate."' AND expdate <= '".$enddate."'");
			if (sizeof($getexpstock) > 0){
				foreach ($getexpstock as $ges){
					$units->setCode($ges['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					if ($statususer == 1){
						$stocknow = floor((100-$discount['extradisc'])/100 * $ges['quantity']);
					}
					else{
						$stocknow = $ges['quantity'];
					}
					
					$list .= '
						<tr>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ges['stockcode']).'</td>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ges['stockname']).'</td>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ges['brandcode']).'</td>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ges['typecode']).'</td>
							<td align="right" height="30" class="detailitem">'.number_format($ges['buyprice'],2,",",".").'</td>
							<td align="right" height="30" class="detailitem">'.number_format($stocknow,2,",",".").'</td>
							<td align="center" height="30" class="detailitem">'.htmlspecialchars($unitquantity).'</td>
							<td align="center" height="30" class="detailitem">'.date("d-m-Y",$ges['expdate']).'</td>
						</tr>
					';
				}
			}
		}
		else{
			$printtemplate = 'reportstockexpiredinit';
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