<?php
	require_once "global.php";
	
	if (empty($useraccess['report_profitlossmonthly'])){
		redirecting('index.php');
	}
	
	require_once "class/Operational.php";
	require_once "class/Stock.php";
	$stock = new Stock();
	$operational = new Operational();
	
	$monthnow = date("m");
	$yearnow = date("Y");
	$diffyear = $yearnow - $general['installyear'];
	$cbyear = '';
	if (empty($_REQUEST['monthstart'])){
		$_REQUEST['monthstart'] = $monthnow;
	}
	if (empty($_REQUEST['yearstart'])){
		$_REQUEST['yearstart'] = $yearnow;
	}
	$getmonthyear = $_REQUEST['monthstart'].'-'.$_REQUEST['yearstart'];
	
	if ($_POST['act'] == 'saving'){
		$operational->setMonthYear($getmonthyear);
		$headeroperational = $operational->getHeaderOperational();
		if (empty($headeroperational['opid'])){
			/* create new */
			$db->beginTransaction();
			$lastid = $operational->saveHeaderOperational($getmonthyear,$_POST['totals'],$userid);
			$operational->setId($lastid);
		
			$arrpostdel = explode(",",$_POST['detailoperationalbox_rowsdeleted']);
			$arrpost = explode(",",$_POST['detailoperationalbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailoperationalbox_".$arrpost[$x]."_0"])){					
						$_POST["detailoperationalbox_".$arrpost[$x]."_1"] = togglenumber($_POST["detailoperationalbox_".$arrpost[$x]."_1"],'calculate');
						$operational->saveDetailOperational($_POST["detailoperationalbox_".$arrpost[$x]."_0"],$_POST["detailoperationalbox_".$arrpost[$x]."_1"]);
					}
				}
			}
			$db->endTransaction();
		}
		else{
			/* edit existing */
			$db->beginTransaction();
			$operational->updateHeaderOperational($_POST['totals'],$userid);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailoperationalbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x])){
						$operational->setDetailId($arrpostdel[$x]);
						$operational->deleteDetailOperational();
					}
				}
			}
			
			//edited rows
			$arrpostt = explode(",",$_POST['detailid']);
			$arrpost = array_diff($arrpostt,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailoperationalbox_".$arrpost[$x]."_0"])){
						$operational->setDetailId($arrpost[$x]);
						$_POST["detailoperationalbox_".$arrpost[$x]."_1"] = togglenumber($_POST["detailoperationalbox_".$arrpost[$x]."_1"],'calculate');
						$operational->updateDetailOperational($_POST["detailoperationalbox_".$arrpost[$x]."_0"],$_POST["detailoperationalbox_".$arrpost[$x]."_1"]);
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailoperationalbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				$operational->setId($headeroperational['opid']);
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailoperationalbox_".$arrpost[$x]."_0"])){					
						$_POST["detailoperationalbox_".$arrpost[$x]."_1"] = togglenumber($_POST["detailoperationalbox_".$arrpost[$x]."_1"],'calculate');
						$operational->saveDetailOperational($_POST["detailoperationalbox_".$arrpost[$x]."_0"],$_POST["detailoperationalbox_".$arrpost[$x]."_1"]);
					}
				}
			}
			$db->endTransaction();
		}
		
		redirecting("reportprofitlossmonthly.php?monthstart=".$_REQUEST['monthstart']."&yearstart=".$_REQUEST['yearstart']);
	}
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_GET['act'] == 'prints'){
		$printtemplate = 'reportprofitlossmonthly';
		$startdate = strtotime('01-'.$_REQUEST['monthstart'].'-'.$_REQUEST['yearstart']);
		$enddate = strtotime(getdaysinmonth($_REQUEST['monthstart'],$_REQUEST['yearstart']).'-'.$_REQUEST['monthstart'].'-'.$_REQUEST['yearstart'].' 23:59:59');
		
		$thismonthsale = 0;
		$stockcapital = 0;
		$thismonthcapital = 0;
		$thismonthreturnsale = 0;
		$thismonthreturnsalecapital = 0;
		$stockreturnsale = 0;
		$stockreturnsalecapital = 0;
		$prevreturnsale = 0;
		$prevreturnsalecapital = 0;
		$thismonthbuy = 0;
		$buystock = 0;
		$buycapital = 0;
		$thismonthreturnbuy = 0;
		$prevreturnbuy = 0;
		$prevreturnbuycapital = 0;
		
		/* get sale and previous month buy */
		$dbsale = $db->fetch_all("SELECT * FROM detailsale WHERE saledate >= '".$startdate."' AND saledate <= '".$enddate."'");
		if (sizeof($dbsale) > 0){
			foreach ($dbsale as $dsl){
				$dsl['quantity'] = discq($dsl['quantity']);
				$dsl['quantityf'] = discq($dsl['quantityf']);
				$thismonthsale += $dsl['quantityf'] * $dsl['realsaleprice'];
			
				$stock->setCode($dsl['stockcode']);
				$firststock = $stock->getFirstStock();
				if ($firststock['assembly'] == 2){
					$getdetailitem = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dsl['dsid']."' AND tabledbid='logdeassembly'");
					if (sizeof($getdetailitem) > 0){
						$temporaryquantity = 0;
						foreach ($getdetailitem as $gdi){
							$getlogda = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$gdi['dbid']."'");
							$monthyearonwalk = date("m-Y",$getlogda['logdate']);
							if ($monthyearonwalk != $getmonthyear){
								if ($statususer == 1){
									$gdi['quantity'] = discq($gdi['quantity']);
									$temporaryquantity += $gdi['quantity'];
									if ($temporaryquantity <= $dsl['quantity']){
										$stockcapital += $gdi['quantity'] * $getlogda['price'];
									}
								}
								else{
									$stockcapital += $gdi['quantity'] * $getlogda['price'];
								}
							}
							else{
								if ($statususer == 1){
									$gdi['quantity'] = discq($gdi['quantity']);
									$temporaryquantity += $gdi['quantity'];
									if ($temporaryquantity <= $dsl['quantity']){
										$thismonthcapital += $gdi['quantity'] * $getlogda['price'];
									}
								}
								else{
									$thismonthcapital += $gdi['quantity'] * $getlogda['price'];
								}
							}
						}
					}
				}
				else{
					$getdetailitem = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dsl['dsid']."'");
					if (sizeof($getdetailitem) > 0){
						$temporaryquantity = 0;
						foreach ($getdetailitem as $gdi){
							if ($gdi['dbid'] == -1 && $gdi['tabledbid'] == 'stock'){
								if ($statususer == 1){
									$gdi['quantity'] = discq($gdi['quantity']);
									$temporaryquantity += $gdi['quantity'];
									if ($temporaryquantity <= $dsl['quantity']){
										$stockcapital += $gdi['quantity'] * $firststock['buyprice'];
									}
								}
								else{
									$stockcapital += $gdi['quantity'] * $firststock['buyprice'];
								}
							}
							else{
								$getdetailbuy = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$gdi['dbid']."'");
								$monthyearonwalk = date("m-Y",$getdetailbuy['buydate']);
								if ($monthyearonwalk != $getmonthyear){
									if ($statususer == 1){
										$gdi['quantity'] = discq($gdi['quantity']);
										$temporaryquantity += $gdi['quantity'];
										if ($temporaryquantity <= $dsl['quantity']){
											$stockcapital += $gdi['quantity'] * $getdetailbuy['realbuyprice'];
										}
									}
									else{
										$stockcapital += $gdi['quantity'] * $getdetailbuy['realbuyprice'];
									}
								}
								else{
									if ($statususer == 1){
										$gdi['quantity'] = discq($gdi['quantity']);
										$temporaryquantity += $gdi['quantity'];
										if ($temporaryquantity <= $dsl['quantity']){
											$thismonthcapital += $gdi['quantity'] * $getdetailbuy['realbuyprice'];
										}
									}
									else{
										$thismonthcapital += $gdi['quantity'] * $getdetailbuy['realbuyprice'];
									}
								}
							}
						}
					}
				}
			}
		}
		
		$qtyforbuyreturn = array();
		$qtyforbuyreturnla = array();
		/* get return sale */
		$dbreturnsale = $db->fetch_all("SELECT * FROM detailsaler WHERE salerdate >= '".$startdate."' AND salerdate <= '".$enddate."'");
		if (sizeof($dbreturnsale) > 0){
			foreach ($dbreturnsale as $dbrs){
				$dbrs['quantity'] = discq($dbrs['quantity']);
				$dbrs['quantityf'] = discq($dbrs['quantityf']);
				
				$dbsourcesale = $db->fetch_one("SELECT * FROM detailsale WHERE dsid='".$dbrs['dsid']."'");
				$monthyearsaleonwalk = date("m-Y",$dbsourcesale['saledate']);
				
				$dbsourcebuy = $db->fetch_all("SELECT dsri.*, db.buydate FROM detailsaleritem dsri INNER JOIN detailbuy db ON dsri.dbid = db.dbid WHERE dsri.dsrid='".$dbrs['dsrid']."'");
				if (sizeof($dbsourcebuy) > 0){
					foreach ($dbsourcebuy as $dbsby){
						$dbsby['quantity'] = discq($dbsby['quantity']);
						$monthyearonwalk = date("m-Y",$dbsby['buydate']);
						if ($monthyearonwalk != $getmonthyear){
							if ($monthyearsaleonwalk != $getmonthyear){
								$prevreturnsale += $dbsby['quantity'] * $dbrs['realsalerprice'];
								
								/* check if stock is assembly */
								$stock->setCode($dbrs['stockcode']);
								$getfsdetail = $stock->getFirstStock();
								if ($getfsdetail['assembly'] == 1){
									$getdetailitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbrs['dsid']."'");
									if (empty($getdetailitem['price'])){
										$getdetailitem['price'] = 0;
									}
									$prevreturnsalecapital += $dbsby['quantity'] * $getdetailitem['price'];
								}
								else{
									$temporaryqty = 0;
									$temporaryqty += $dbsby['quantity'];
									if ($dbsby['dbid'] == -1){
										$capitals = $getfsdetail['buyprice'];
									}
									else{
										if ($dbsby['tabledbid'] == 'logdeassembly'){
											$dbpurch = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$dbsby['dbid']."'");
											if (empty($dbpurch['price'])){
												$dbpurch['price'] = 0;
											}
											$capitals = $dbpurch['price'];
										}
										else{
											$dbpurch = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbsby['dbid']."'");
											if (empty($dbpurch['realbuyprice'])){
												$dbpurch['realbuyprice'] = 0;
											}
											$capitals = $dbpurch['realbuyprice'];
										}
									}
									
									if ($statususer == 1){
										if ($temporaryqty <= $dbrs['quantity']){
											$prevreturnsalecapital += $dbsby['quantity'] * $capitals;
										}
									}
									else{
										$prevreturnsalecapital += $dbsby['quantity'] * $capitals;
									}
								}
							}
							else{
								$stockreturnsale += $dbsby['quantity'] * $dbrs['realsalerprice'];
								
								/* check if stock is assembly */
								$stock->setCode($dbrs['stockcode']);
								$getfsdetail = $stock->getFirstStock();
								if ($getfsdetail['assembly'] == 1){
									$getdetailitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbrs['dsid']."'");
									if (empty($getdetailitem['price'])){
										$getdetailitem['price'] = 0;
									}
									$stockreturnsalecapital += $dbsby['quantity'] * $getdetailitem['price'];
								}
								else{
									$temporaryqty = 0;
									$temporaryqty += $dbsby['quantity'];
									if ($dbsby['dbid'] == -1){
										$capitals = $getfsdetail['buyprice'];
									}
									else{
										if ($dbsby['tabledbid'] == 'logdeassembly'){
											$dbpurch = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$dbsby['dbid']."'");
											if (empty($dbpurch['price'])){
												$dbpurch['price'] = 0;
											}
											$capitals = $dbpurch['price'];
										}
										else{
											$dbpurch = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbsby['dbid']."'");
											if (empty($dbpurch['realbuyprice'])){
												$dbpurch['realbuyprice'] = 0;
											}
											$capitals = $dbpurch['realbuyprice'];
										}
									}
									
									if ($statususer == 1){
										if ($temporaryqty <= $dbrs['quantity']){
											$stockreturnsalecapital += $dbsby['quantity'] * $capitals;
										}
									}
									else{
										$stockreturnsalecapital += $dbsby['quantity'] * $capitals;
									}
								}
							}
						}
						else{
							$thismonthreturnsale += $dbsby['quantity'] * $dbrs['realsalerprice'];
							
							/* check if stock is assembly */
							$stock->setCode($dbrs['stockcode']);
							$getfsdetail = $stock->getFirstStock();
							if ($getfsdetail['assembly'] == 1){
								$getdetailitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$dbrs['dsid']."'");
								if (empty($getdetailitem['price'])){
									$getdetailitem['price'] = 0;
								}
								$thismonthreturnsalecapital += $dbsby['quantity'] * $getdetailitem['price'];
							}
							else{
								$temporaryqty = 0;
								$temporaryqty += $dbsby['quantity'];
								if ($dbsby['dbid'] == -1){
									$capitals = $getfsdetail['buyprice'];
								}
								else{
									if ($dbsby['tabledbid'] == 'logdeassembly'){
										$dbpurch = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$dbsby['dbid']."'");
										if (empty($dbpurch['price'])){
											$dbpurch['price'] = 0;
										}
										$capitals = $dbpurch['price'];
									}
									else{
										$dbpurch = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbsby['dbid']."'");
										if (empty($dbpurch['realbuyprice'])){
											$dbpurch['realbuyprice'] = 0;
										}
										$capitals = $dbpurch['realbuyprice'];
									}
								}
								
								if ($statususer == 1){
									if ($temporaryqty <= $dbrs['quantity']){
										$thismonthreturnsalecapital += $dbsby['quantity'] * $capitals;
									}
								}
								else{
									$thismonthreturnsalecapital += $dbsby['quantity'] * $capitals;
								}
							}
							
							if ($dbsby['tabledbid'] == 'logdeassembly'){
								$qtyforbuyreturnla[$dbsby['dbid']] += $dbsby['quantity'];
							}
							else{
								$qtyforbuyreturn[$dbsby['dbid']] += $dbsby['quantity'];
							}
						}
					}
				}
			}
		}
		//print_r($qtyforbuyreturn);
		
		/* get buy */
		$dbbuy = $db->fetch_all("SELECT * FROM detailbuy WHERE buydate >= '".$startdate."' AND buydate <= '".$enddate."'");
		if (sizeof($dbbuy) > 0){
			foreach ($dbbuy as $dby){
				$dbsalenow = $db->fetch_one("SELECT SUM(dsi.quantity) AS salenow FROM detailsaleitem dsi INNER JOIN detailsale ds ON dsi.dsid = ds.dsid WHERE dsi.dbid='".$dby['dbid']."' AND ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."' AND dsi.tabledbid = 'detailbuy'");
				$dbbuyrnow = $db->fetch_one("SELECT SUM(dbri.quantity) AS buyrnow FROM detailbuyritem dbri INNER JOIN detailbuyr dbr ON dbri.dbrid = dbr.dbrid WHERE dbri.dbid='".$dby['dbid']."' AND dbr.buyrdate >= '".$startdate."' AND dbr.buyrdate <= '".$enddate."'");
				
				$dbsaleafter = $db->fetch_one("SELECT SUM(dsi.quantity) AS saleafter FROM detailsaleitem dsi INNER JOIN detailsale ds ON dsi.dsid = ds.dsid WHERE dsi.dbid='".$dby['dbid']."' AND ds.saledate > '".$enddate."' AND dsi.tabledbid = 'detailbuy'");
				$dbsalerafter = $db->fetch_one("SELECT SUM(dsri.quantity) AS salerafter FROM detailsaleritem dsri INNER JOIN detailsaler dsr ON dsri.dsrid = dsr.dsrid WHERE dsri.dbid='".$dby['dbid']."' AND dsr.salerdate > '".$enddate."' AND dsri.tabledbid = 'detailbuy'");
				$dbbuyrafter = $db->fetch_one("SELECT SUM(dbri.quantity) AS buyrafter FROM detailbuyritem dbri INNER JOIN detailbuyr dbr ON dbri.dbrid = dbr.dbrid WHERE dbri.dbid='".$dby['dbid']."' AND dbr.buyrdate > '".$enddate."'");
				$dby['quantity'] = discq($dby['quantity']);
				$dby['quantityf'] = discq($dby['quantityf']);
				$dby['usedqty'] = discq($dby['usedqty']);
				$thismonthbuy += $dby['quantityf'] * $dby['realbuyprice'];
				
				$dbsalenow['salenow'] = discq($dbsalenow['salenow']);
				$dbbuyrnow['buyrnow'] = discq($dbbuyrnow['buyrnow']);
				$dbsaleafter['saleafter'] = discq($dbsaleafter['saleafter']);
				$dbsalerafter['salerafter'] = discq($dbsalerafter['salerafter']);
				$dbbuyrafter['buyrafter'] = discq($dbbuyrafter['buyrafter']);
				
				$becomestock = $dby['quantity'] - $dbsalenow['salenow'] - $dbbuyrnow['buyrnow'] + $qtyforbuyreturn[$dby['dbid']];
				//$becomestock = $dby['quantity'] - $dbsalenow['salenow'] - $dbbuyrnow['buyrnow'] + $dbsaleafter['saleafter'] - $dbsalerafter['salerafter'] + $dbbuyrafter['buyrafter'] + $qtyforbuyreturn[$dby['dbid']];
				
				/*echo $dby['quantity'].":".$dbsalenow['salenow'].":".$dbbuyrnow['buyrnow'].":".$dbsaleafter['saleafter'].":".$dbsalerafter['salerafter'].":".$dbbuyrafter['buyrafter'].":".$qtyforbuyreturn[$dby['dbid']];*/
				$buystock += $becomestock * $dby['realbuyprice'];
				
				/* $dby['usedqty'] = $dby['usedqty'] - $dbsaleafter['saleafter'] + $dbsalerafter['salerafter'] - $dbbuyrafter['buyrafter'] - $qtyforbuyreturn[$dby['dbid']];
				if ($dby['usedqty'] < $dby['quantity']){
					$becomestock = $dby['quantity'] - $dby['usedqty'];
					$buystock += $becomestock * $dby['realbuyprice'];
				} */
			}
		}
		
		/* get return buy */
		$dbreturnbuy = $db->fetch_all("SELECT * FROM detailbuyr WHERE buyrdate >= '".$startdate."' AND buyrdate <= '".$enddate."'");
		if (sizeof($dbreturnbuy) > 0){
			foreach ($dbreturnbuy as $dbrs){
				$dbrs['quantity'] = discq($dbrs['quantity']);
				$dbrs['quantityf'] = discq($dbrs['quantityf']);
				$dbitems = $db->fetch_all("SELECT * FROM detailbuyritem WHERE dbrid='".$dbrs['dbrid']."'");
				if (sizeof($dbitems) > 0){
					foreach ($dbitems as $dbsb){
						$dbsourcebuy = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$dbsb['dbid']."'");
						$monthyearonwalk = date("m-Y",$dbsourcebuy['buydate']);
						if ($monthyearonwalk != $getmonthyear){
							$prevreturnbuy += $dbrs['quantityf'] * $dbrs['realbuyrprice'];
						}
						else{
							$thismonthreturnbuy += $dbrs['quantityf'] * $dbrs['realbuyrprice'];
						}
					}
				}
			}
		}
		$buycapital = $thismonthbuy - $buystock - $thismonthreturnbuy;
		$prevreturnbuycapital = $prevreturnbuy;
		
		/* get logdeassembly that will become stock */
		$dblogda = $db->fetch_all("SELECT * FROM logdeassembly WHERE logdate >= '".$startdate."' AND logdate <= '".$enddate."'");
		if (sizeof($dblogda) > 0){
			foreach ($dblogda as $dbda){
				$dbsalenow = $db->fetch_one("SELECT SUM(dsi.quantity) AS salenow FROM detailsaleitem dsi INNER JOIN detailsale ds ON dsi.dsid = ds.dsid WHERE dsi.dbid='".$dby['dbid']."' AND ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."' AND dsi.tabledbid = 'logdeassembly'");
				
				$dbsaleafter = $db->fetch_one("SELECT SUM(dsi.quantity) AS saleafter FROM detailsaleitem dsi INNER JOIN detailsale ds ON dsi.dsid = ds.dsid WHERE dsi.dbid='".$dbda['logid']."' AND ds.saledate > '".$enddate."' AND dsi.tabledbid = 'logdeassembly'");
				$dbsalerafter = $db->fetch_one("SELECT SUM(dsri.quantity) AS salerafter FROM detailsaleritem dsri INNER JOIN detailsaler dsr ON dsri.dsrid = dsr.dsrid WHERE dsri.dbid='".$dbda['logid']."' AND dsr.salerdate > '".$enddate."' AND dsri.tabledbid = 'logdeassembly'");
				
				$dbda['quantity'] = discq($dbda['quantity']);
				$dbda['usedqty'] = discq($dbda['usedqty']);
				$dbsalenow['salenow'] = discq($dbsalenow['salenow']);
				$dbsaleafter['saleafter'] = discq($dbsaleafter['saleafter']);
				$dbsalerafter['salerafter'] = discq($dbsalerafter['salerafter']);
				
				$becomestock = $dbda['quantity'] - $dbsalenow['salenow'] + $qtyforbuyreturnla[$dbda['dbid']];
				$buystock += $becomestock * $dbda['price'];
/* 				
				$dbda['usedqty'] = $dbda['usedqty'] - $dbsaleafter['saleafter'] + $dbsalerafter['salerafter'] - $qtyforbuyreturnla[$dbda['dbid']];
				
				if ($dbda['usedqty'] < $dbda['quantity']){
					$becomestock = $dbda['quantity'] - $dbda['usedqty'];
					$buystock += $becomestock * $dbda['price'];
				}
 */			}
		}
		
		/* get operational cost */
		$operational->setMonthYear($getmonthyear);
		$headeroperational = $operational->getHeaderOperational();
		
		/* print to html */
		$monthint = intval($_REQUEST['monthstart']);
		$printheadermonth = $arrmonthname[$monthint-1].' '.$_REQUEST['yearstart'];
		$printmonth = strtoupper($arrmonthname[$monthint-1]);
		
		$grandtotals = 0;
		$grandtotals += $thismonthsale;
		$first = $grandtotals;
		$thismonthsaletext = number_format($thismonthsale,0,",",".");
		$firsttext = number_format($first,0,",",".");
		
		$grandtotals -= $stockcapital;
		$second = $grandtotals;
		$stockcapitaltext = number_format($stockcapital,0,",",".");
		$secondtext = number_format($second,0,",",".");
		
		$grandtotals -= $thismonthcapital;
		$third = $grandtotals;
		$thismonthcapitaltext = number_format($thismonthcapital,0,",",".");
		$thirdtext = number_format($third,0,",",".");
		
		$grandtotals -= $thismonthreturnsale;
		$fourth = $grandtotals;
		$thismonthreturnsaletext = number_format($thismonthreturnsale,0,",",".");
		$fourthtext = number_format($fourth,0,",",".");
		
		$grandtotals += $thismonthreturnsalecapital;
		$fifth = $grandtotals;
		$thismonthreturnsalecapitaltext = number_format($thismonthreturnsalecapital,0,",",".");
		$fifthtext = number_format($fifth,0,",",".");
		
		$grandtotals -= $stockreturnsale;
		$sixth = $grandtotals;
		$stockreturnsaletext = number_format($stockreturnsale,0,",",".");
		$sixthtext = number_format($sixth,0,",",".");
		
		$grandtotals += $stockreturnsalecapital;
		$seventh = $grandtotals;
		$stockreturnsalecapitaltext = number_format($stockreturnsalecapital,0,",",".");
		$seventhtext = number_format($seventh,0,",",".");
		
		$grandtotals -= $prevreturnsale;
		$eight = $grandtotals;
		$prevreturnsaletext = number_format($prevreturnsale,0,",",".");
		$eighttext = number_format($eight,0,",",".");
		
		$grandtotals += $prevreturnsalecapital;
		$ninth = $grandtotals;
		$prevreturnsalecapitaltext = number_format($prevreturnsalecapital,0,",",".");
		$ninthtext = number_format($ninth,0,",",".");
		
		$grandtotals -= $thismonthbuy;
		$tenth = $grandtotals;
		$thismonthbuytext = number_format($thismonthbuy,0,",",".");
		$tenthtext = number_format($tenth,0,",",".");
		
		$grandtotals += $buystock;
		$eleventh = $grandtotals;
		$buystocktext = number_format($buystock,0,",",".");
		$eleventhtext = number_format($eleventh,0,",",".");
		
		$grandtotals += $buycapital;
		$twelveth = $grandtotals;
		$buycapitaltext = number_format($buycapital,0,",",".");
		$twelvethtext = number_format($twelveth,0,",",".");
		
		$grandtotals += $thismonthreturnbuy;
		$thirteenth = $grandtotals;
		$thismonthreturnbuytext = number_format($thismonthreturnbuy,0,",",".");
		$thirteenthtext = number_format($thirteenth,0,",",".");
		
		$grandtotals += $prevreturnbuy;
		$fourteenth = $grandtotals;
		$prevreturnbuytext = number_format($prevreturnbuy,0,",",".");
		$fourteenthtext = number_format($fourteenth,0,",",".");
		
		$grandtotals -= $prevreturnbuycapital;
		$fifteenth = $grandtotals;
		$prevreturnbuycapitaltext = number_format($prevreturnbuycapital,0,",",".");
		$fifteenthtext = number_format($fifteenth,0,",",".");
		
		$grandtotals -= $headeroperational['totals'];
		$sixteenth = $grandtotals;
		$operationalcost = number_format($headeroperational['totals'],0,",",".");
		$sixteenthtext = number_format($sixteenth,0,",",".");
		
		$grandtotalstext = number_format($grandtotals,0,",",".");
	}
	else if ($_GET['act'] == 'delete'){
		$operational->setMonthYear($getmonthyear);
		$operational->deleteOperational();
		
		redirecting("reportprofitlossmonthly.php?monthstart=".$_REQUEST['monthstart']."&yearstart=".$_REQUEST['yearstart']);
	}
	else{
		$printtemplate = 'reportprofitlossmonthlyinit';
		if ($diffyear >= 0){
			for ($i = 0; $i <= $diffyear; $i++){
				$printyears = ($yearnow-$i);
				$cbyear .= '<option value="'.$printyears.'"'.(($_REQUEST['yearstart'] == $printyears)?' selected':'').'>'.$printyears.'</option>';
			}
		}
		
		$operational->setMonthYear($getmonthyear);
		if (!empty($_REQUEST['monthstart']) && !empty($_REQUEST['yearstart'])){
			$alldetail = $operational->getDetailOperational();
			if ($_GET['getlist'] == 'detailxml'){
				header("Content-type: text/xml");
				if (sizeof($alldetail) > 0){
					foreach ($alldetail as $as){
						$lists .= '
							<row id="'.$as['doid'].'">
								<cell>'.htmlspecialchars($as['notes']).'</cell>
								<cell>'.number_format($as['total'],2,",",".").'</cell>
							</row>
						';
					}
				}
				
				$slist = gettemplate('operationallist');
				eval("\$slist = \"$slist\";");
				echo $slist;
				exit;
			}
			$alldetailid = '';
			if (sizeof($alldetail) > 0){
				foreach ($alldetail as $as){
					$alldetailid .= ','.$as['doid'];
				}
				$alldetailid = substr($alldetailid,1);
			}
		}
		$headeroperational = $operational->getHeaderOperational();
		$ftotal = number_format($headeroperational['totals'],2,",",".");
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>