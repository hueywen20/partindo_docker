<?php
class Stock{
	var $id;
	var $code;
	var $partno;
	
	function setId($id){
		global $db;
		$this->id = $db->clean($id);
	}
	
	function setCode($code){
		global $db;
		$this->code = $db->clean($code);
	}
	
	function setPartNo($partno){
		global $db;
		$this->partno = $db->clean($partno);
	}
	
	function checkStockCodeNoExist($stockcode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'stockid <> \''.$this->id.'\'');
		}
		array_push($sqls,'stockcode = \''.$db->clean($stockcode).'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM stock".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListStock($mode = -1, $page = -1, $limits = -1){
		global $db,$general;
		
		$addsql = '';
		if ($page != -1){
			$addsql = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		if ($limits != -1){
			$addsql = ' LIMIT '.$limits;
		}
		
		if ($mode == -1){
			$dbstock = $db->fetch_all("SELECT * FROM stock ORDER BY stockcode".$addsql);
		}
		else if ($mode == 2){
			$dbstock = $db->fetch_all("SELECT sda.*, s.stockid, s.generalname FROM stockdeassembly sda INNER JOIN stock s ON sda.stockcode = s.stockcode ORDER BY sda.stockcode");
		}
		else{
			$dbstock = $db->fetch_all("SELECT * FROM stock WHERE assembly IN (".$mode.") ORDER BY stockcode");
		}
		
		return $dbstock;
	}
	
	function getAllPartNo(){
		global $db;
		
		$sqls = array();
		if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstock = $db->fetch_all("SELECT * FROM stockpartno".$sql." ORDER BY partid");
		
		return $dbstock;
	}
	
	function updateStockDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		array_push($sqls,'usedqty < quantity');
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbfirststock = $db->fetch_one("SELECT * FROM stock WHERE stockcode = '".$this->code."'");
		$dbstock = $db->fetch_one("SELECT SUM(quantity-usedqty) AS qty, MIN(realbuyprice) AS minp, MAX(realbuyprice) AS maxp, MIN(expdate) AS expdate FROM detailbuy".$sql." GROUP BY partno ORDER BY buydate");
		
		if (sizeof($dbstock) > 0){
			$dbstock['qty'] = $dbstock['qty'] + $dbfirststock['remaining'];
			$dbstock['minp'] = min($dbstock['minp'],$dbfirststock['buyprice']);
			$dbstock['maxp'] = max($dbstock['maxp'],$dbfirststock['buyprice']);
			$dbstock['expdate'] = min($dbstock['expdate'],$dbfirststock['expdate']);
		}
		else{
			$dbstock['qty'] = $dbfirststock['remaining'];
			$dbstock['minp'] = $dbfirststock['buyprice'];
			$dbstock['maxp'] = $dbfirststock['buyprice'];
			$dbstock['expdate'] = $dbfirststock['expdate'];
		}
		
		$updates['realremaining'] = $dbstock['qty'];
		$updates['buyminprice'] = $dbstock['minp'];
		$updates['buymaxprice'] = $dbstock['maxp'];
		$updates['minexpdate'] = $dbstock['expdate'];
		
		$db->update("stock",$updates,"stockcode='".$this->code."'");
	}
	
	function getStockAll(){
		global $db;
		
		$sqls = array();
		if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		if (!empty($this->partno)){
			array_push($sqls,'partno = \''.$this->partno.'\'');
		}
		array_push($sqls,'usedqty < quantity');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbfirststock = $db->fetch_one("SELECT * FROM stock WHERE stockcode = '".$this->code."'");
		if ($dbfirststock['assembly'] == 2){
			$dbstock = $db->fetch_one("SELECT MIN(price) AS minp, MAX(price) AS maxp FROM logdeassembly".$sql." ORDER BY logdate");
			$dbstock['mexp'] = $dbfirststock['expdate'];
		}
		else{
			$checkbuyexist = $db->query("SELECT * FROM detailbuy".$sql);
			$totalrows = @mysql_num_rows($checkbuyexist);

			$dbstock = $db->fetch_one("SELECT MIN(realbuyprice) AS minp, MAX(realbuyprice) AS maxp FROM detailbuy".$sql." AND realbuyprice > 0 ORDER BY buydate");
			//echo "SELECT MIN(realbuyprice) AS minp, MAX(realbuyprice) AS maxp FROM detailbuy".$sql." AND realbuyprice > 0 ORDER BY buydate";
			$dbstocks = $db->fetch_one("SELECT MIN(expdate) AS mexp FROM detailbuy".$sql." AND expdate > 0 ORDER BY buydate");
			$dbstock['mexp'] = $dbstocks['mexp'];
		}

		$dbstock['qty'] = $dbfirststock['realremaining'];
		if (sizeof($dbstock) > 0 && !empty($dbstock['minp'])){
			if ($dbfirststock['remaining'] == 0 || $dbfirststock['buyprice'] == 0){
				$dbstock['minp'] = $dbstock['minp'];
				$dbstock['maxp'] = $dbstock['maxp'];
				$dbstock['mexp'] = $dbstock['mexp'];
			}
			else{
				$dbstock['minp'] = min($dbstock['minp'],$dbfirststock['buyprice']);
				$dbstock['maxp'] = max($dbstock['maxp'],$dbfirststock['buyprice']);
				$dbstock['mexp'] = min($dbstock['mexp'],$dbfirststock['expdate']);
			}
		}
		else{
			if ($totalrows == 0){
				$dbstock['minp'] = 0;
				$dbstock['maxp'] = 0;
				$dbstock['mexp'] = 0;
			}
			else{
				$dbstock['minp'] = $dbfirststock['buyprice'];
				$dbstock['maxp'] = $dbfirststock['buyprice'];
				$dbstock['mexp'] = $dbfirststock['expdate'];
			}
		}
		return $dbstock;
	}
	
	function canDeleteFirstStock($mode = ''){
		global $db;
		
		if (!empty($this->code)){
			if ($mode == 'deassembly'){
				$checkall = $db->fetch_all("SELECT * FROM detailstockdeassembly WHERE stockcode='".$this->code."'");
				if (sizeof($checkall) > 0){
					foreach ($checkall as $ca){
						$checks = $db->fetch_one("SELECT * FROM detailbuy WHERE stockcode='".$db->clean($ca['stockcodecomponent'])."' LIMIT 1");
						if (sizeof($checks) > 0){
							return false;
						}
			
						$checks = $db->fetch_one("SELECT * FROM detailsale WHERE stockcode='".$db->clean($ca['stockcodecomponent'])."' LIMIT 1");
						if (sizeof($checks) > 0){
							return false;
						}
					
						$checks = $db->fetch_one("SELECT * FROM detailbuyr WHERE stockcode='".$db->clean($ca['stockcodecomponent'])."' LIMIT 1");
						if (sizeof($checks) > 0){
							return false;
						}
			
						$checks = $db->fetch_one("SELECT * FROM detailsaler WHERE stockcode='".$db->clean($ca['stockcodecomponent'])."' LIMIT 1");
						if (sizeof($checks) > 0){
							return false;
						}
			
						$checks = $db->fetch_one("SELECT * FROM detailadjustin WHERE stockcode='".$db->clean($ca['stockcodecomponent'])."' LIMIT 1");
						if (sizeof($checks) > 0){
							return false;
						}
			
						$checks = $db->fetch_one("SELECT * FROM detailadjustout WHERE stockcode='".$db->clean($ca['stockcodecomponent'])."' LIMIT 1");
						if (sizeof($checks) > 0){
							return false;
						}
					}
				}
			}
			else{
				$checks = $db->fetch_one("SELECT * FROM detailbuy WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM detailsale WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
				
				$checks = $db->fetch_one("SELECT * FROM detailbuyr WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM detailsaler WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM detailadjustin WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM detailadjustout WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM logassembly WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM detailstockassembly WHERE stockcodecomponent='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM logdeassemblyparent WHERE stockcode='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			
				$checks = $db->fetch_one("SELECT * FROM detailstockdeassembly WHERE stockcodecomponent='".$this->code."' LIMIT 1");
				if (sizeof($checks) > 0){
					return false;
				}
			}
			
			return true;
		}
		return true;
	}
	
	function getStockToReduced(){
		global $db;
		
		$sqls = array();
		if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		array_push($sqls,'usedqty < quantity');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstock = $db->fetch_all("SELECT * FROM detailbuy".$sql." ORDER BY buydate, expdate");

		return $dbstock;
	}
	
	function getFirstStock(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'stockid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbfirststock = $db->fetch_one("SELECT * FROM stock".$sql);
		
		return $dbfirststock;
	}
	
	function getStockDetail($getassemblyp = array(), $startdate = 0, $enddate = 0, $cuts = 0){
		global $db,$discount,$deassembly,$assembly;
		
		$sqls = array();
		$sqlspd = array();
		$sqlssd = array();
		$sqlsprd = array();
		$sqlssrd = array();
		$sqlsaid = array();
		$sqlsaod = array();
		$sqlslogpd = array();
		$sqlslogd = array();
		if (!empty($this->code)){
			array_push($sqls,'a.stockcode = \''.$this->code.'\'');
		}
		if (!empty($startdate)){
			array_push($sqlspd,'a.buydate >= \''.$startdate.'\'');
			array_push($sqlssd,'a.saledate >= \''.$startdate.'\'');
			array_push($sqlsprd,'a.buyrdate >= \''.$startdate.'\'');
			array_push($sqlssrd,'a.salerdate >= \''.$startdate.'\'');
			array_push($sqlsaid,'a.aindate >= \''.$startdate.'\'');
			array_push($sqlsaod,'a.aoutdate >= \''.$startdate.'\'');
			array_push($sqlslogpd,'a.logdate >= \''.$startdate.'\'');
			array_push($sqlslogd,'a.logdate >= \''.$startdate.'\'');
		}
		if (!empty($enddate)){
			array_push($sqlspd,'a.buydate <= \''.$enddate.'\'');
			array_push($sqlssd,'a.saledate <= \''.$enddate.'\'');
			array_push($sqlsprd,'a.buyrdate <= \''.$enddate.'\'');
			array_push($sqlssrd,'a.salerdate <= \''.$enddate.'\'');
			array_push($sqlsaid,'a.aindate <= \''.$enddate.'\'');
			array_push($sqlsaod,'a.aoutdate <= \''.$enddate.'\'');
			array_push($sqlslogpd,'a.logdate <= \''.$enddate.'\'');
			array_push($sqlslogd,'a.logdate <= \''.$enddate.'\'');
		}
		
		$sql = '';
		$sqlpd = '';
		$sqlsd = '';
		$sqlprd = '';
		$sqlsrd = '';
		$sqlaid = '';
		$sqlaod = '';
		$sqllogp = '';
		$sqllog = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		if (empty($sql)){
			$strconcat = ' WHERE ';
		}
		else{
			$strconcat = ' AND ';
		}
		if (sizeof($sqlspd) > 0){
			$sqlpd = $strconcat.implode(' AND ',$sqlspd);
		}
		if (sizeof($sqlssd) > 0){
			$sqlsd = $strconcat.implode(' AND ',$sqlssd);
		}
		if (sizeof($sqlsprd) > 0){
			$sqlprd = $strconcat.implode(' AND ',$sqlsprd);
		}
		if (sizeof($sqlssrd) > 0){
			$sqlsrd = $strconcat.implode(' AND ',$sqlssrd);
		}
		if (sizeof($sqlsaid) > 0){
			$sqlaid = $strconcat.implode(' AND ',$sqlsaid);
		}
		if (sizeof($sqlsaod) > 0){
			$sqlaod = $strconcat.implode(' AND ',$sqlsaod);
		}
		if (sizeof($sqlslogpd) > 0){
			$sqllogp = $strconcat.implode(' AND ',$sqlslogpd);
		}
		if (sizeof($sqlslogd) > 0){
			$sqllog = $strconcat.implode(' AND ',$sqlslogd);
		}
		//get purchase
		if ($cuts == 1){
			$getalltr = $db->fetch_one("SELECT SUM(hb.totalbuy) AS totalbuys FROM headerbuy hb INNER JOIN detailbuy db ON hb.buyno = db.buyno WHERE (db.buydate >= '".$startdate."' AND db.buydate <= '".$enddate."') AND db.stockcode='".$this->code."' ORDER BY db.buydate");
			$totaltransaction = $getalltr['totalbuys'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT hb.* FROM headerbuy hb INNER JOIN detailbuy db ON hb.buyno = db.buyno WHERE (db.buydate >= '".$startdate."' AND db.buydate <= '".$enddate."') AND db.stockcode='".$this->code."' ORDER BY hb.totalbuy");
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
					if (empty($sql)){
						$cutsql = ' WHERE a.buyno IN ('.$textimplodehbid.')';
					}
					else{
						$cutsql = ' AND a.buyno IN ('.$textimplodehbid.')';
					}
					$dbpurchase = $db->query("SELECT a.*, b.suppliercode, b.orderno FROM detailbuy a INNER JOIN headerbuy b ON a.buyno=b.buyno".$sql.$cutsql." ORDER BY a.buydate");
				}
			}
		}
		else{
			$dbpurchase = $db->query("SELECT a.*, b.suppliercode, b.orderno FROM detailbuy a INNER JOIN headerbuy b ON a.buyno=b.buyno".$sql.$sqlpd." ORDER BY a.buydate");
		}
		//echo "SELECT a.*, b.suppliercode FROM detailbuy a INNER JOIN headerbuy b ON a.buyno=b.buyno".$sql.$sqlpd." ORDER BY a.buydate";
		//get sale
		if ($cuts == 1){
			$getalltr = $db->fetch_one("SELECT SUM(hs.totalsale) AS totalsales FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE (ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY ds.saledate");
			$totaltransaction = $getalltr['totalsales'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT hs.* FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE (ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY hs.totalsale");
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
				$cutsql = '';
				if (sizeof($arrhbid) > 0){
					$textimplodehbid = implode(",",$arrhbid);
					if (empty($sql)){
						$cutsql = ' WHERE a.saleno IN ('.$textimplodehbid.')';
					}
					else{
						$cutsql = ' AND a.saleno IN ('.$textimplodehbid.')';
					}
					$dbsale = $db->query("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno".$sql.$cutsql." ORDER BY a.saledate");
				}
			}
		}
		else{
			$dbsale = $db->query("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno".$sql.$sqlsd." ORDER BY a.saledate");
		}
		//get purchase return
		if ($cuts == 1){
			$getalltr = $db->fetch_one("SELECT SUM(hb.totalbuyr) AS totalbuyrs FROM headerbuyr hb INNER JOIN detailbuyr db ON hb.buyrid = db.buyrid WHERE (db.buyrdate >= '".$startdate."' AND db.buyrdate <= '".$enddate."') AND db.stockcode='".$this->code."' ORDER BY db.buyrdate");
			$totaltransaction = $getalltr['totalbuyrs'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT hb.* FROM headerbuyr hb INNER JOIN detailbuyr db ON hb.buyrid = db.buyrid WHERE (db.buyrdate >= '".$startdate."' AND db.buyrdate <= '".$enddate."') AND db.stockcode='".$this->code."' ORDER BY hb.totalbuyr");
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
				$textimplodehbid = '';
				$cutsql = '';
				if (sizeof($arrhbid) > 0){
					$textimplodehbid = implode(",",$arrhbid);
					if (empty($sql)){
						$cutsql = ' WHERE a.buyrid IN ('.$textimplodehbid.')';
					}
					else{
						$cutsql = ' AND a.buyrid IN ('.$textimplodehbid.')';
					}
					$dbpurchaser = $db->query("SELECT a.*, b.suppliercode FROM detailbuyr a INNER JOIN headerbuyr b ON a.buyrid=b.buyrid".$sql.$cutsql." ORDER BY a.buyrdate");
				}
			}
		}
		else{
			$dbpurchaser = $db->query("SELECT a.*, b.suppliercode FROM detailbuyr a INNER JOIN headerbuyr b ON a.buyrid=b.buyrid".$sql.$sqlprd." ORDER BY a.buyrdate");
		}
		//get sale return
		if ($cuts == 1){
			$getalltr = $db->fetch_one("SELECT SUM(hs.totalsaler) AS totalsalers FROM headersaler hs INNER JOIN detailsaler ds ON hs.salerid = ds.salerid WHERE (ds.salerdate >= '".$startdate."' AND ds.salerdate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY ds.salerdate");
			$totaltransaction = $getalltr['totalsalers'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT hs.* FROM headersaler hs INNER JOIN detailsaler ds ON hs.salerid = ds.salerid WHERE (ds.salerdate >= '".$startdate."' AND ds.salerdate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY hs.totalsaler");
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
				$textimplodehbid = '';
				$cutsql = '';
				if (sizeof($arrhbid) > 0){
					$textimplodehbid = implode(",",$arrhbid);
					if (empty($sql)){
						$cutsql = ' WHERE a.salerid IN ('.$textimplodehbid.')';
					}
					else{
						$cutsql = ' AND a.salerid IN ('.$textimplodehbid.')';
					}
					$dbsaler = $db->query("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid".$sql.$cutsql." ORDER BY a.salerdate");
				}
			}
		}
		else{
			$dbsaler = $db->query("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid".$sql.$sqlsrd." ORDER BY a.salerdate");
		}
		//get adjust in
		if ($cuts == 1){
			$getalltr = $db->fetch_one("SELECT SUM(ha.totalain) AS totalains FROM headeradjustin ha INNER JOIN detailadjustin da ON ha.ainid = da.ainid WHERE (da.aindate >= '".$startdate."' AND da.aindate <= '".$enddate."') AND da.stockcode='".$this->code."' ORDER BY da.aindate");
			$totaltransaction = $getalltr['totalains'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT ha.* FROM headeradjustin ha INNER JOIN detailadjustin da ON ha.ainid = da.ainid WHERE (da.aindate >= '".$startdate."' AND da.aindate <= '".$enddate."') AND da.stockcode='".$this->code."' ORDER BY ha.totalain");
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
				$textimplodehbid = '';
				$cutsql = '';
				if (sizeof($arrhbid) > 0){
					$textimplodehbid = implode(",",$arrhbid);
					if (empty($sql)){
						$cutsql = ' WHERE a.ainid IN ('.$textimplodehbid.')';
					}
					else{
						$cutsql = ' AND a.ainid IN ('.$textimplodehbid.')';
					}
					$dbain = $db->query("SELECT a.* FROM detailadjustin a".$sql.$cutsql." ORDER BY aindate");
				}
			}
		}
		else{
			$dbain = $db->query("SELECT a.* FROM detailadjustin a".$sql.$sqlaid." ORDER BY aindate");
		}
		//get adjust out
		if ($cuts == 1){
			$getalltr = $db->fetch_one("SELECT SUM(ha.totalaout) AS totalaouts FROM headeradjustout ha INNER JOIN detailadjustout da ON ha.aoutid = da.aoutid WHERE (da.aoutdate >= '".$startdate."' AND da.aoutdate <= '".$enddate."') AND da.stockcode='".$this->code."' ORDER BY da.aoutdate");
			$totaltransaction = $getalltr['totalaouts'];
			$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
			
			$getalltr = $db->fetch_all("SELECT ha.* FROM headeradjustout ha INNER JOIN detailadjustout da ON ha.aoutid = da.aoutid WHERE (da.aoutdate >= '".$startdate."' AND da.aoutdate <= '".$enddate."') AND da.stockcode='".$this->code."' ORDER BY ha.totalaout");
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
				$textimplodehbid = '';
				$cutsql = '';
				if (sizeof($arrhbid) > 0){
					$textimplodehbid = implode(",",$arrhbid);
					if (empty($sql)){
						$cutsql = ' WHERE a.aoutid IN ('.$textimplodehbid.')';
					}
					else{
						$cutsql = ' AND a.aoutid IN ('.$textimplodehbid.')';
					}
					$dbaout = $db->query("SELECT a.* FROM detailadjustout a".$sql.$cutsql." ORDER BY aoutdate");
				}
			}
		}
		else{
			$dbaout = $db->query("SELECT a.* FROM detailadjustout a".$sql.$sqlaod." ORDER BY aoutdate");
		}
		
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
			$sql = ' WHERE a.stockcode IN ('.substr($idparentasm,1).')';
			//get sale
			if ($cuts == 1){
				$getalltr = $db->fetch_one("SELECT SUM(hs.totalsale) AS totalsales FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE (ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY ds.saledate");
				$totaltransaction = $getalltr['totalsales'];
				$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
				
				$getalltr = $db->fetch_all("SELECT hs.* FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE (ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY hs.totalsale");
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
					$cutsql = '';
					if (sizeof($arrhbid) > 0){
						$textimplodehbid = implode(",",$arrhbid);
						if (empty($sql)){
							$cutsql = ' WHERE a.saleno IN ('.$textimplodehbid.')';
						}
						else{
							$cutsql = ' AND a.saleno IN ('.$textimplodehbid.')';
						}
						$dbsaleasm = $db->query("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno".$sql.$cutsql." ORDER BY a.saledate");
					}
				}
			}
			else{
				$dbsaleasm = $db->query("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno".$sql.$sqlsd." ORDER BY a.saledate");
			}
			
			//get sale return
			if ($cuts == 1){
				$getalltr = $db->fetch_one("SELECT SUM(hs.totalsale) AS totalsales FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE (ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY ds.saledate");
				$totaltransaction = $getalltr['totalsales'];
				$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
				
				$getalltr = $db->fetch_all("SELECT hs.* FROM headersale hs INNER JOIN detailsale ds ON hs.saleno = ds.saleno WHERE (ds.saledate >= '".$startdate."' AND ds.saledate <= '".$enddate."') AND ds.stockcode='".$this->code."' ORDER BY hs.totalsale");
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
					$cutsql = '';
					if (sizeof($arrhbid) > 0){
						$textimplodehbid = implode(",",$arrhbid);
						if (empty($sql)){
							$cutsql = ' WHERE a.saleno IN ('.$textimplodehbid.')';
						}
						else{
							$cutsql = ' AND a.saleno IN ('.$textimplodehbid.')';
						}
						$dbsalerasm = $db->query("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid".$sql.$cutsql." ORDER BY a.saledate");
					}
				}
			}
			else{
				$dbsalerasm = $db->query("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid".$sql.$sqlsrd." ORDER BY a.salerdate");
			}
		}
		
		//get log deassembly parent
		$dblogp = $db->query("SELECT a.* FROM logdeassemblyparent a".$sql.$sqllogp." ORDER BY logdate");
		//get log deassembly
		$dblog = $db->query("SELECT a.* FROM logdeassembly a".$sql.$sqllog." ORDER BY logdate");
		
		$dbstockori = array();
		if (@mysql_num_rows($dbpurchase) > 0){
			while ($dbp = $db->fetch_array($dbpurchase)){
				$dbdetail['dbid'] = 'b-'.$dbp['dbid'];
				$dbdetail['date'] = $dbp['buydate'];
				$dbdetail['partno'] = $dbp['partno'];
				$dbdetail['stockname'] = $dbp['stockname'];
				$dbdetail['brandcode'] = $dbp['brandcode'];
				$dbdetail['typecode'] = $dbp['typecode'];
				$dbdetail['sc'] = $dbp['suppliercode'];
				$dbdetail['stockin'] = $dbp['quantity'];
				$dbdetail['stockout'] = 0;
				$dbdetail['buyprice'] = $dbp['realbuyprice'];
				$dbdetail['saleprice'] = 0;
				$dbdetail['expdate'] = $dbp['expdate'];
				$dbdetail['description'] = $dbp['description'];
				$dbdetail['status'] = 'purchase';
				$dbdetail['invc'] = $dbp['orderno'];
				$dbstockori[] = $dbdetail;
			}
		}
		if (@mysql_num_rows($dbsale) > 0){
			while ($dbs = $db->fetch_array($dbsale)){
				$dbdetail['dbid'] = 's-'.$dbs['dsid'];
				$dbdetail['date'] = $dbs['saledate'] + 1;
				$dbdetail['partno'] = $dbs['partno'];
				$dbdetail['stockname'] = $dbs['stockname'];
				$dbdetail['brandcode'] = $dbs['brandcode'];
				$dbdetail['typecode'] = $dbs['typecode'];
				$dbdetail['sc'] = $dbs['customercode'];
				$dbdetail['stockin'] = 0;
				$dbdetail['stockout'] = $dbs['quantity'];
				$dbdetail['buyprice'] = 0;
				$dbdetail['saleprice'] = $dbs['realsaleprice'];
				$dbdetail['expdate'] = '-';
				$dbdetail['description'] = $dbs['description'];
				$dbdetail['status'] = 'sale';
				$dbdetail['invc'] = $dbs['saleno'];
				$dbstockori[] = $dbdetail;
			}
		}
		if (@mysql_num_rows($dbpurchaser) > 0){
			while ($dbs = $db->fetch_array($dbpurchaser)){
				/* get buy */
				$dbbuys = $db->fetch_all("SELECT hb.orderno FROM detailbuyritem dbri INNER JOIN detailbuy db ON dbri.dbid = db.dbid INNER JOIN headerbuy hb ON db.buyno = hb.buyno WHERE dbri.dbrid='".$dbs['dbrid']."'");
				$getbuynos = '';
				if (sizeof($dbbuys) > 0){
					foreach ($dbbuys as $dbbs){
						$getbuynos .= ' '.$dbbs['orderno'];
					}
				}
			
				$dbdetail['dbid'] = 'br-'.$dbs['dbrid'];
				$dbdetail['date'] = $dbs['buyrdate'] + 2;
				$dbdetail['partno'] = $dbs['partno'];
				$dbdetail['stockname'] = $dbs['stockname'];
				$dbdetail['brandcode'] = $dbs['brandcode'];
				$dbdetail['typecode'] = $dbs['typecode'];
				$dbdetail['sc'] = $dbs['customercode'];
				$dbdetail['stockin'] = 0;
				$dbdetail['stockout'] = $dbs['quantity'];
				$dbdetail['buyprice'] = 0;
				$dbdetail['saleprice'] = $dbs['realbuyrprice'];
				$dbdetail['expdate'] = '-';
				$dbdetail['description'] = 'Retur Beli'.$getbuynos.(!empty($dbs['description'])?' | '.$dbs['description']:'');
				$dbdetail['status'] = 'buyreturn';
				$dbdetail['invc'] = $dbs['buyrid'];
				$dbstockori[] = $dbdetail;
			}
		}
		if (@mysql_num_rows($dbsaler) > 0){
			while ($dbs = $db->fetch_array($dbsaler)){
				/* get sale */
				$dbsales = $db->fetch_all("SELECT ds.saleno FROM detailsaler dsr INNER JOIN detailsale ds ON dsr.dsid = ds.dsid WHERE dsr.dsrid='".$dbs['dsrid']."'");
				$getsalenos = '';
				if (sizeof($dbsales) > 0){
					foreach ($dbsales as $dbbs){
						$getsalenos .= ' '.$dbbs['saleno'];
					}
				}
			
				$dbdetail['dbid'] = 'sr-'.$dbs['dsrid'];
				$dbdetail['date'] = $dbs['salerdate'] + 3;
				$dbdetail['partno'] = $dbs['partno'];
				$dbdetail['stockname'] = $dbs['stockname'];
				$dbdetail['brandcode'] = $dbs['brandcode'];
				$dbdetail['typecode'] = $dbs['typecode'];
				$dbdetail['sc'] = $dbs['customercode'];
				$dbdetail['stockin'] = $dbs['quantity'];
				$dbdetail['stockout'] = 0;
				$dbdetail['buyprice'] = 0;
				$dbdetail['saleprice'] = $dbs['realsalerprice'];
				$dbdetail['expdate'] = '-';
				$dbdetail['description'] = 'Retur Jual'.$getsalenos.(!empty($dbs['description'])?' | '.$dbs['description']:'');
				$dbdetail['status'] = 'salereturn';
				$dbdetail['invc'] = $dbs['salerid'];
				$dbstockori[] = $dbdetail;
			}
		}
		if (@mysql_num_rows($dbain) > 0){
			while ($dbs = $db->fetch_array($dbain)){
				$dbdetail['dbid'] = 'ain-'.$dbs['dainid'];
				$dbdetail['date'] = $dbs['aindate'] + 4;
				$dbdetail['partno'] = $dbs['partno'];
				$dbdetail['stockname'] = $dbs['stockname'];
				$dbdetail['brandcode'] = $dbs['brandcode'];
				$dbdetail['typecode'] = $dbs['typecode'];
				$dbdetail['sc'] = $dbs['customercode'];
				$dbdetail['stockin'] = $dbs['quantity'];
				$dbdetail['stockout'] = 0;
				$dbdetail['buyprice'] = $dbs['realainprice'];
				$dbdetail['saleprice'] = 0;
				$dbdetail['expdate'] = '-';
				$dbdetail['description'] = $dbs['description'];
				$dbdetail['status'] = 'adjustin';
				$dbdetail['invc'] = $dbs['ainid'];
				$dbstockori[] = $dbdetail;
			}
		}
		if (@mysql_num_rows($dbaout) > 0){
			while ($dbs = $db->fetch_array($dbaout)){
				$dbdetail['dbid'] = 'aout-'.$dbs['daoutid'];
				$dbdetail['date'] = $dbs['aoutdate'] + 5;
				$dbdetail['partno'] = $dbs['partno'];
				$dbdetail['stockname'] = $dbs['stockname'];
				$dbdetail['brandcode'] = $dbs['brandcode'];
				$dbdetail['typecode'] = $dbs['typecode'];
				$dbdetail['sc'] = $dbs['customercode'];
				$dbdetail['stockin'] = 0;
				$dbdetail['stockout'] = $dbs['quantity'];
				$dbdetail['buyprice'] = 0;
				$dbdetail['saleprice'] = $dbs['realaoutprice'];
				$dbdetail['expdate'] = '-';
				$dbdetail['description'] = $dbs['description'];
				$dbdetail['status'] = 'adjustout';
				$dbdetail['invc'] = $dbs['aoutid'];
				$dbstockori[] = $dbdetail;
			}
		}
		if (@mysql_num_rows($dblogp) > 0){
			while ($dbs = $db->fetch_array($dblogp)){
				$this->setId("");
				$this->setCode($dbs['stockcode']);
				$fstock = $this->getFirstStock();
			
				$deassembly->setCode($dbs['stockcode']);
				$getcpnt = $deassembly->getDeAssemblyComponent();
				$dacpnt = '';
				if (sizeof($getcpnt) > 0){
					foreach ($getcpnt as $gcpnt){
						$dacpnt .= '|^|'.$gcpnt['stockcodecomponent'];
					}
					$dacpnt = substr($dacpnt,3);
				}
			
				$dbdetail['dbid'] = 'logp-'.$dbs['logid'];
				$dbdetail['date'] = $dbs['logdate'];
				
				$getpartno = $this->getAllPartNo();
				
				$dbdetail['partno'] = '-';
				if (sizeof($getpartno) > 0){
					foreach ($getpartno as $gpn){
						$dbdetail['partno'] = $gpn['partno'];
						break;
					}
				}
				$dbdetail['stockname'] = $fstock['generalname'];
				$dbdetail['brandcode'] = $fstock['brandcode'];
				$dbdetail['typecode'] = $fstock['typecode'];
				$dbdetail['sc'] = $dacpnt;
				if (strstr($dbs['description'],'deassembly')){
					$dbdetail['stockin'] = 0;
					$dbdetail['stockout'] = $dbs['quantity'];
				}
				else{
					$dbdetail['stockin'] = $dbs['quantity'];
					$dbdetail['stockout'] = 0;
				}
				$dbdetail['buyprice'] = 0;
				$dbdetail['saleprice'] = 0;
				$dbdetail['expdate'] = '-';
				$dbdetail['description'] = $dbs['description'];
				$dbdetail['status'] = 'logdeassemblyparent';
				$dbdetail['invc'] = '';
				$dbstockori[] = $dbdetail;
			}
		}
		if (@mysql_num_rows($dblog) > 0){
			while ($dbs = $db->fetch_array($dblog)){
				$this->setId("");
				$this->setCode($dbs['stockcode']);
				$fstock = $this->getFirstStock();
				
				$dbdetail['dbid'] = 'log-'.$dbs['logid'];
				$dbdetail['date'] = $dbs['logdate'];
				$getpartno = $this->getAllPartNo();
				$dbdetail['partno'] = '-';
				if (sizeof($getpartno) > 0){
					foreach ($getpartno as $gpn){
						$dbdetail['partno'] = $gpn['partno'];
						break;
					}
				}
				$dbdetail['stockname'] = $fstock['generalname'];
				$dbdetail['brandcode'] = $fstock['brandcode'];
				$dbdetail['typecode'] = $fstock['typecode'];
				$dbdetail['sc'] = '';
				if (strstr($dbs['description'],'Pecahan dari')){
					$dbdetail['stockin'] = $dbs['quantity'];
					$dbdetail['stockout'] = 0;
				}
				else{
					$dbdetail['stockin'] = 0;
					$dbdetail['stockout'] = $dbs['quantity'];
				}
				$dbdetail['buyprice'] = $dbs['price'];
				$dbdetail['saleprice'] = 0;
				$dbdetail['expdate'] = '-';
				$dbdetail['description'] = $dbs['description'];
				$dbdetail['status'] = 'logdeassembly';
				$dbdetail['invc'] = '';
				$dbstockori[] = $dbdetail;
			}
		}
		if ($getassemblypsize > 0){
			if (@mysql_num_rows($dbsaleasm) > 0){
				//$thisfs = $this->getFirstStock();
				$thisfs = $db->fetch_one("SELECT * FROM detailstockassembly WHERE stockcodecomponent='".$this->code."'");
				/*$getpartno = $this->getAllPartNo();
				$thispartnos = '-';
				if (sizeof($getpartno) > 0){
					foreach ($getpartno as $gpn){
						$thispartnos = $gpn['partno'];
						break;
					}
				}*/
				$thispartnos = $thisfs['sccpartno'];
				while ($dbs = $db->fetch_array($dbsaleasm)){
					if (!empty($assemblyscqt[$dbs['stockcode']])){
						$dbs['realsaleprice'] = round($dbs['realsaleprice'] / $assemblyscqt[$dbs['stockcode']],2);
					}
					
					//get buy price
					$dbdetail['buyprice'] = 0;
					$bprice = $db->fetch_all("SELECT * FROM detailsaleitem WHERE dsid='".$dbs['dsid']."'");
					if (sizeof($bprice) > 0){
						foreach ($bprice as $bpr){
							if ($bpr['dbid'] == -1){
								$getbpone = $this->getFirstStock();
								$dbdetail['buyprice'] += $getbpone['buyprice'];
							}
							else{
								$getbpone = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$bpr['dbid']."' AND stockcode='".$this->code."'");
								$dbdetail['buyprice'] += $getbpone['realbuyprice'];
							}
						}
					}
				
					$dbdetail['dbid'] = 'sasm-'.$dbs['dsid'];
					$dbdetail['date'] = $dbs['saledate'] + 1;
					$dbdetail['partno'] = $thispartnos;
					$dbdetail['stockname'] = $thisfs['sccname'];
					$dbdetail['brandcode'] = $thisfs['sccbrandcode'];
					$dbdetail['typecode'] = $thisfs['scctypecode'];
					$dbdetail['sc'] = $dbs['stockcode'];
					$dbdetail['stockin'] = 0;
					$dbdetail['stockout'] = $dbs['quantity'] * $assemblyscq[$dbs['stockcode']];
					$dbdetail['saleprice'] = 0;
					$dbdetail['expdate'] = '-';
					$dbdetail['description'] = 'Assembly';
					$dbdetail['status'] = 'saleasm';
					$dbdetail['invc'] = $dbs['saleno'];
					$dbstockori[] = $dbdetail;
				}
			}
			
			if (@mysql_num_rows($dbsalerasm) > 0){
				$thisfs = $db->fetch_one("SELECT * FROM detailstockassembly WHERE stockcodecomponent='".$this->code."'");
				$thispartnos = $thisfs['sccpartno'];
				while ($dbs = $db->fetch_array($dbsalerasm)){
					if (!empty($assemblyscqt[$dbs['stockcode']])){
						$dbs['realsaleprice'] = round($dbs['realsaleprice'] / $assemblyscqt[$dbs['stockcode']],2);
					}
				
					/* get sale */
					$dbsales = $db->fetch_all("SELECT ds.saleno FROM detailsaler dsr INNER JOIN detailsale ds ON dsr.dsid = ds.dsid WHERE dsr.dsrid='".$dbs['dsrid']."'");
					$getsalenos = '';
					if (sizeof($dbsales) > 0){
						foreach ($dbsales as $dbbs){
							$getsalenos .= ' '.$dbbs['saleno'];
						}
					}
			
					$dbdetail['dbid'] = 'srasm-'.$dbs['dsrid'];
					$dbdetail['date'] = $dbs['salerdate'] + 3;
					$dbdetail['partno'] = $thispartnos;
					$dbdetail['stockname'] = $thisfs['sccname'];
					$dbdetail['brandcode'] = $thisfs['sccbrandcode'];
					$dbdetail['typecode'] = $thisfs['scctypecode'];
					$dbdetail['sc'] = $dbs['stockcode'];
					$dbdetail['stockin'] = $dbs['quantity'] * $assemblyscq[$dbs['stockcode']];
					$dbdetail['stockout'] = 0;
					$dbdetail['buyprice'] = 0;
					$dbdetail['saleprice'] = 0;
					$dbdetail['expdate'] = '-';
					$dbdetail['description'] = 'Retur Jual Assembly'.$getsalenos;
					$dbdetail['status'] = 'salerasm';
					$dbdetail['invc'] = $dbs['salerid'];
					$dbstockori[] = $dbdetail;
				}
			}
		}
		
		if (sizeof($dbstockori) > 0){
			$dbstock = multisort($dbstockori,'date','dbid','partno','stockname','brandcode','typecode','sc','stockin','stockout','buyprice','saleprice','expdate','description','status','invc');
		}
		
		return $dbstock;
	}
	
	function saveStock($stockcode,$standardname,$generalname,$brandcode,$typecode,$partno,$size,$locationcode,$stgrcode,$qty,$minqty,$buyprice,$sellprice,$unitcode,$expdate,$assembly,$status,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$stockcode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['stockcode'] = $stockcode;
			$inserts['standardname'] = $standardname;
			$inserts['generalname'] = $generalname;
			$inserts['brandcode'] = $brandcode;
			$inserts['typecode'] = $typecode;
			$inserts['size'] = $size;
			$inserts['locationcode'] = $locationcode;
			$inserts['stgrcode'] = $stgrcode;
			$inserts['quantity'] = str_replace(".","",$qty);
			$inserts['remaining'] = str_replace(".","",$qty);
			$inserts['totalstock'] = str_replace(".","",$qty);
			$inserts['realremaining'] = str_replace(".","",$qty);
			$inserts['minqty'] = str_replace(".","",$minqty);
			$inserts['buyprice'] = str_replace(".","",$buyprice);
			$inserts['buyminprice'] = str_replace(".","",$buyprice);
			$inserts['buymaxprice'] = str_replace(".","",$buyprice);
			$inserts['sellprice'] = str_replace(".","",$sellprice);
			$inserts['unitcode'] = $unitcode;
			$inserts['expdate'] = strtotime($expdate);
			$inserts['minexpdate'] = strtotime($expdate);
			$inserts['assembly'] = $assembly;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $status;
			$lastid = $db->insert("stock",$inserts);
			
			//insert part number
			$allpartno = explode("\r\n",$partno);
			if (sizeof($allpartno) > 0){
				foreach ($allpartno as $apn){
					unset($insertpn);
					$insertpn['stockcode'] = $stockcode;
					$insertpn['partno'] = $apn;
					$insertpn['status'] = 1;
					$db->insert("stockpartno",$insertpn);
				}
			}
			
			return $lastid;
		}
	}
	
	function updateStock($stockcode,$standardname,$generalname,$brandcode,$typecode,$partno,$size,$locationcode,$stgrcode,$qty,$minqty,$buyprice,$sellprice,$unitcode,$expdate,$status,$userid){
		global $db,$units;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$db->clean($stockcode)."' AND stockid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$getcode = $this->getFirstStock();
				
				$additional = $qty-$getcode['quantity'];
				
				$updates['stockcode'] = $stockcode;
				$updates['standardname'] = $standardname;
				$updates['generalname'] = $generalname;
				$updates['brandcode'] = $brandcode;
				$updates['typecode'] = $typecode;
				$updates['size'] = $size;
				$updates['locationcode'] = $locationcode;
				$updates['stgrcode'] = $stgrcode;
				$updates['quantity'] = $qty;//str_replace(".","",$qty);
				$updates['remaining'] = $getcode['remaining']+$additional;
				$updates['realremaining'] = $getcode['realremaining']+$additional;
				$updates['totalstock'] = $getcode['totalstock']+$additional;
				$updates['minqty'] = $minqty;//str_replace(".","",$minqty);
				$updates['buyprice'] = $buyprice;//str_replace(".","",$buyprice);
				$updates['sellprice'] = $sellprice;//str_replace(".","",$sellprice);
				$updates['unitcode'] = $unitcode;
				$updates['expdate'] = strtotime($expdate);
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $status;
				
				//insert part number
				$this->code = $db->clean($getcode['stockcode']);
				$dballpartno = $this->getAllPartNo();
				$allpartno = explode("\r\n",$partno);
				$arrpartno = array();
				if (sizeof($dballpartno) > 0){
					foreach ($dballpartno as $dbap){
						array_push($arrpartno,$dbap['partno']);
					}
				}
				$rsdiffdel = array_diff($arrpartno,$allpartno);
				if (sizeof($rsdiffdel) > 0){
					foreach ($rsdiffdel as $rdff){
						$db->query("DELETE FROM stockpartno WHERE partno='".$db->clean($rdff)."' AND stockcode='".$this->code."'");
					}
				}
				$rsdiffins = array_diff($allpartno,$arrpartno);
				if (sizeof($rsdiffins) > 0){
					foreach ($rsdiffins as $apn){
						if (!empty($apn)){
							unset($insertpn);
							$insertpn['stockcode'] = $stockcode;
							$insertpn['partno'] = $apn;
							$insertpn['status'] = 1;
							$db->insert("stockpartno",$insertpn);
						}
					}
				}
				$db->update("stock",$updates,"stockid='".$this->id."'");
				
				if ($this->code != $stockcode){
					$db->query("UPDATE stockpartno SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailbuy SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailsale SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailbuyr SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailsaler SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailadjustin SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailadjustout SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");

					if ($getcode['assembly'] == 1){
						$db->query("UPDATE stockassembly SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
						$db->query("UPDATE detailstockassembly SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
					}
					else{
						$db->query("UPDATE stockdeassembly SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
						$db->query("UPDATE detailstockdeassembly SET stockcode='".$db->clean($stockcode)."' WHERE stockcode='".$this->code."'");
						$db->query("UPDATE detailstockdeassembly SET stockcodecomponent='".$db->clean($stockcode)."' WHERE stockcodecomponent='".$this->code."'");
						$db->query("UPDATE detailstockassembly SET stockcodecomponent='".$db->clean($stockcode)."' WHERE stockcodecomponent='".$this->code."'");
					}
					
					$this->code = $db->clean($stockcode);
				}
				
				if ($getcode['generalname'] != $generalname){
					$db->query("UPDATE detailbuy SET stockname='".$db->clean($generalname)."' WHERE stockcode='".$this->code."' AND stockname='".$db->clean($getcode['generalname'])."'");
					$db->query("UPDATE detailsale SET stockname='".$db->clean($generalname)."' WHERE stockcode='".$this->code."' AND stockname='".$db->clean($getcode['generalname'])."'");
					$db->query("UPDATE detailbuyr SET stockname='".$db->clean($generalname)."' WHERE stockcode='".$this->code."' AND stockname='".$db->clean($getcode['generalname'])."'");
					$db->query("UPDATE detailsaler SET stockname='".$db->clean($generalname)."' WHERE stockcode='".$this->code."' AND stockname='".$db->clean($getcode['generalname'])."'");
					$db->query("UPDATE detailadjustin SET stockname='".$db->clean($generalname)."' WHERE stockcode='".$this->code."' AND stockname='".$db->clean($getcode['generalname'])."'");
					$db->query("UPDATE detailadjustout SET stockname='".$db->clean($generalname)."' WHERE stockcode='".$this->code."' AND stockname='".$db->clean($getcode['generalname'])."'");

					if ($getcode['assembly'] == 0){
						$db->query("UPDATE detailstockassembly SET sccname='".$db->clean($generalname)."' WHERE stockcodecomponent='".$this->code."' AND sccname='".$db->clean($getcode['generalname'])."'");
					}
					$db->query("UPDATE detailstockdeassembly SET sccname='".$db->clean($generalname)."' WHERE stockcodecomponent='".$this->code."' AND sccname='".$db->clean($getcode['generalname'])."'");
				}
				
				if ($getcode['brandcode'] != $brandcode){
					$db->query("UPDATE detailbuy SET brandcode='".$db->clean($brandcode)."' WHERE stockcode='".$this->code."' AND brandcode='".$db->clean($getcode['brandcode'])."'");
					$db->query("UPDATE detailsale SET brandcode='".$db->clean($brandcode)."' WHERE stockcode='".$this->code."' AND brandcode='".$db->clean($getcode['brandcode'])."'");
					$db->query("UPDATE detailbuyr SET brandcode='".$db->clean($brandcode)."' WHERE stockcode='".$this->code."' AND brandcode='".$db->clean($getcode['brandcode'])."'");
					$db->query("UPDATE detailsaler SET brandcode='".$db->clean($brandcode)."' WHERE stockcode='".$this->code."' AND brandcode='".$db->clean($getcode['brandcode'])."'");
					$db->query("UPDATE detailadjustin SET brandcode='".$db->clean($brandcode)."' WHERE stockcode='".$this->code."' AND brandcode='".$db->clean($getcode['brandcode'])."'");
					$db->query("UPDATE detailadjustout SET brandcode='".$db->clean($brandcode)."' WHERE stockcode='".$this->code."' AND brandcode='".$db->clean($getcode['brandcode'])."'");

					if ($getcode['assembly'] == 0){
						$db->query("UPDATE detailstockassembly SET sccbrandcode='".$db->clean($brandcode)."' WHERE stockcodecomponent='".$this->code."' AND sccbrandcode='".$db->clean($getcode['brandcode'])."'");
					}
					$db->query("UPDATE detailstockdeassembly SET sccbrandcode='".$db->clean($brandcode)."' WHERE stockcodecomponent='".$this->code."' AND sccbrandcode='".$db->clean($getcode['brandcode'])."'");
				}
				
				if ($getcode['typecode'] != $typecode){
					$db->query("UPDATE detailbuy SET typecode='".$db->clean($typecode)."' WHERE stockcode='".$this->code."' AND typecode='".$db->clean($getcode['typecode'])."'");
					$db->query("UPDATE detailsale SET typecode='".$db->clean($typecode)."' WHERE stockcode='".$this->code."' AND typecode='".$db->clean($getcode['typecode'])."'");
					$db->query("UPDATE detailbuyr SET typecode='".$db->clean($typecode)."' WHERE stockcode='".$this->code."' AND typecode='".$db->clean($getcode['typecode'])."'");
					$db->query("UPDATE detailsaler SET typecode='".$db->clean($typecode)."' WHERE stockcode='".$this->code."' AND typecode='".$db->clean($getcode['typecode'])."'");
					$db->query("UPDATE detailadjustin SET typecode='".$db->clean($typecode)."' WHERE stockcode='".$this->code."' AND typecode='".$db->clean($getcode['typecode'])."'");
					$db->query("UPDATE detailadjustout SET typecode='".$db->clean($typecode)."' WHERE stockcode='".$this->code."' AND typecode='".$db->clean($getcode['typecode'])."'");

					if ($getcode['assembly'] == 0){
						$db->query("UPDATE detailstockassembly SET scctypecode='".$db->clean($typecode)."' WHERE stockcodecomponent='".$this->code."' AND scctypecode='".$db->clean($getcode['typecode'])."'");
					}
					$db->query("UPDATE detailstockdeassembly SET scctypecode='".$db->clean($typecode)."' WHERE stockcodecomponent='".$this->code."' AND scctypecode='".$db->clean($getcode['typecode'])."'");
				}
				
				if ($getcode['unitcode'] != $unitcode){
					$units->setCode($unitcode);
					$getunitdetail = $units->getunitDetail();
					$db->query("UPDATE detailbuy SET unitcode='".$db->clean($unitcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailbuy SET unitquantityf='".$db->clean($getunitdetail['lunit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity = unitquantityf");
					$db->query("UPDATE detailbuy SET unitquantityf='".$db->clean($getunitdetail['funit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity <> unitquantityf");

					$db->query("UPDATE detailsale SET unitcode='".$db->clean($unitcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailsale SET unitquantityf='".$db->clean($getunitdetail['lunit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity = unitquantityf");
					$db->query("UPDATE detailsale SET unitquantityf='".$db->clean($getunitdetail['funit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity <> unitquantityf");

					$db->query("UPDATE detailbuyr SET unitcode='".$db->clean($unitcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailbuyr SET unitquantityf='".$db->clean($getunitdetail['lunit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity = unitquantityf");
					$db->query("UPDATE detailbuyr SET unitquantityf='".$db->clean($getunitdetail['funit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity <> unitquantityf");

					$db->query("UPDATE detailsaler SET unitcode='".$db->clean($unitcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailsaler SET unitquantityf='".$db->clean($getunitdetail['lunit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity = unitquantityf");
					$db->query("UPDATE detailsaler SET unitquantityf='".$db->clean($getunitdetail['funit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity <> unitquantityf");

					$db->query("UPDATE detailadjustin SET unitcode='".$db->clean($unitcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailadjustin SET unitquantityf='".$db->clean($getunitdetail['lunit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity = unitquantityf");
					$db->query("UPDATE detailadjustin SET unitquantityf='".$db->clean($getunitdetail['funit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity <> unitquantityf");

					$db->query("UPDATE detailadjustout SET unitcode='".$db->clean($unitcode)."' WHERE stockcode='".$this->code."'");
					$db->query("UPDATE detailadjustout SET unitquantityf='".$db->clean($getunitdetail['lunit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity = unitquantityf");
					$db->query("UPDATE detailadjustout SET unitquantityf='".$db->clean($getunitdetail['funit'])."', unitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcode='".$this->code."' AND unitquantity <> unitquantityf");

					if ($getcode['assembly'] == 0){
						$db->query("UPDATE detailstockassembly SET sccunitcode='".$db->clean($unitcode)."' WHERE stockcodecomponent='".$this->code."'");
						$db->query("UPDATE detailstockassembly SET sccunitquantityf='".$db->clean($getunitdetail['lunit'])."', sccunitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcodecomponent='".$this->code."' AND sccunitquantity = sccunitquantityf");
						$db->query("UPDATE detailstockassembly SET sccunitquantityf='".$db->clean($getunitdetail['funit'])."', sccunitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcodecomponent='".$this->code."' AND sccunitquantity <> sccunitquantityf");
					}
					$db->query("UPDATE detailstockdeassembly SET sccunitcode='".$db->clean($unitcode)."' WHERE stockcodecomponent='".$this->code."'");
					$db->query("UPDATE detailstockdeassembly SET sccunitquantityf='".$db->clean($getunitdetail['lunit'])."', sccunitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcodecomponent='".$this->code."' AND sccunitquantity = sccunitquantityf");
					$db->query("UPDATE detailstockdeassembly SET sccunitquantityf='".$db->clean($getunitdetail['funit'])."', sccunitquantity='".$db->clean($getunitdetail['lunit'])."' WHERE stockcodecomponent='".$this->code."' AND sccunitquantity <> sccunitquantityf");
				}
								
				$upd = $this->getStockAll();
				if ($upd['minp'] > 0){
					$updatesrl['buyminprice'] = $upd['minp'];
				}
				if ($upd['maxp'] > 0){
					$updatesrl['buymaxprice'] = $upd['maxp'];
				}
				if ($upd['mexp'] > 0){
					$updatesrl['minexpdate'] = $upd['mexp'];
				}
				$updating = false;
				if (sizeof($updatesrl) > 0){
					$updating = true;
					$db->update("stock",$updatesrl,"stockcode='".$this->code."'");
				}

				if (!$updating){
					$getlastbuy = $db->fetch_one("SELECT * FROM detailbuy WHERE stockcode='".$this->code."' ORDER BY buydate DESC LIMIT 1");
					if (empty($getlastbuy['dbid'])){
						$getfirststock = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$this->code."'");
						$db->query("UPDATE stock SET buyminprice='".$getfirststock['buyprice']."', buymaxprice='".$getfirststock['buyprice']."', minexpdate='".$getfirststock['expdate']."' WHERE stockcode='".$this->code."'");
					}
					else{
						$db->query("UPDATE stock SET buyminprice=".$getlastbuy['realbuyprice'].", buymaxprice=".$getlastbuy['realbuyprice'].", minexpdate=".$getlastbuy['expdate']." WHERE stockcode='".$this->code."'");
					}
				}
				//$db->query("UPDATE stock SET buyminprice=".$upd['minp'].", buymaxprice=".$upd['maxp'].", minexpdate=".$upd['mexp']." WHERE stockid='".$this->id."'");
				
				//update deassembly price
				$db->query("UPDATE logdeassembly SET price='".$buyprice."' WHERE stockcode='".$db->clean($stockcode)."' AND price=0");
				
				//update assembly price
				$db->query("UPDATE logassembly SET price='".$buyprice."' WHERE stockcode='".$db->clean($stockcode)."' AND price=0");
				
				return true;
			}
		}
	}
	
	function searchStock($keyword,$field,$getreturn,$asm,$status = -1,$page = -1,$limits = -1){
		global $db,$general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		if ($limits != -1){
			$addlimit = ' LIMIT '.$limits;
		}
		
		$sqls = array();
		if ($asm != -1){
			array_push($sqls,'s.assembly IN ('.$db->clean($asm).')');
		}
		if ($status != -1){
			array_push($sqls,'s.status=\''.$db->clean($status).'\'');
		}
		if (isset($keyword)){
			if (empty($field)){
				$field = 'generalname';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			$arrsc = array_search('stockcode',$field);
			if ($arrsc !== false){
				$strinarr = 's.stockcode LIKE (\''.$db->clean($keyword[$arrsc]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrscr = array_search('stockcoderange',$field);
			if ($arrscr !== false){
				$strinarr = $keyword[$arrscr];
				array_push($sqls,$strinarr);
			}
			$arrgn = array_search('generalname',$field);
			if ($arrgn !== false){
				$strinarr = 's.generalname LIKE (\''.$db->clean($keyword[$arrgn]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrmexp = array_search('minexpdate',$field);
			if ($arrmexp !== false){
				$strinarr = 's.minexpdate=\''.strtotime($keyword[$arrmexp]).'\'';
				array_push($sqls,$strinarr);
			}
			$arrbr = array_search('brandname',$field);
			if ($arrbr !== false){
				/*$innerjoin = ' INNER JOIN brand b ON s.brandcode = b.brandcode';
				$strinarr = 'b.brandname LIKE (\''.$keyword.'%\')';
				$field = 'b.'.$field;*/
				$strinarr = 's.brandcode LIKE (\''.$db->clean($keyword[$arrbr]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrty = array_search('typename',$field);
			if ($arrty !== false){
				/*$innerjoin = ' INNER JOIN type t ON s.typecode = t.typecode';
				$strinarr = 't.typename LIKE (\''.$keyword.'%\')';
				$field = 't.'.$field;*/
				$strinarr = 's.typecode LIKE (\''.$db->clean($keyword[$arrty]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrsz = array_search('size',$field);
			if ($arrsz !== false){
				$strinarr = 's.size LIKE (\''.$db->clean($keyword[$arrsz]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrrm = array_search('realremaining',$field);
			if ($arrrm !== false){
				$strinarr = 's.realremaining LIKE (\''.$db->clean($keyword[$arrrm]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrbmn = array_search('buyminprice',$field);
			if ($arrbmn !== false){
				$strinarr = 's.buyminprice LIKE (\''.$db->clean($keyword[$arrbmn]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrbmx = array_search('buymaxprice',$field);
			if ($arrbmx !== false){
				$strinarr = 's.buymaxprice LIKE (\''.$db->clean($keyword[$arrbmx]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrbpr = array_search('buyprice',$field);
			if ($arrbpr !== false){
				$strinarr = 's.buyprice LIKE (\''.$db->clean($keyword[$arrbpr]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrspr = array_search('sellprice',$field);
			if ($arrspr !== false){
				$strinarr = 's.sellprice LIKE (\''.$db->clean($keyword[$arrspr]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrlc = array_search('locationname',$field);
			if ($arrlc !== false){
				/*$innerjoin = ' INNER JOIN location l ON s.locationcode = l.locationcode';
				$strinarr = 'l.locationname LIKE (\''.$keyword.'%\')';
				$field = 'l.'.$field;*/
				$strinarr = 's.locationcode LIKE (\''.$db->clean($keyword[$arrlc]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrsg = array_search('stockgroup',$field);
			if ($arrsg !== false){
				$strinarr = 's.stgrcode LIKE (\''.$db->clean($keyword[$arrsg]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrpn = array_search('partno',$field);
			if ($arrpn !== false){
				$innerjoin = ' INNER JOIN stockpartno p ON s.stockcode = p.stockcode';
				$strinarr = 'p.partno LIKE (\''.$db->clean($keyword[$arrpn]).'%\')';
				$groupby = ' GROUP BY s.stockcode';
				//$field = 'p.'.$field;
				array_push($sqls,$strinarr);
			}
			$arrun = array_search('unitname',$field);
			if ($arrun !== false){
				$innerjoin = ' INNER JOIN units u ON s.unitcode = u.unitcode';
				$strinarr = 'u.lunit LIKE (\''.$db->clean($keyword[$arrun]).'%\')';
				$groupby = ' GROUP BY s.stockcode';
				array_push($sqls,$strinarr);
			}
			$arrst = array_search('status',$field);
			if ($arrst !== false){
				global $arrstatus;
				$candb = false;
				$keynow = -1;
				foreach ($arrstatus as $keys => $ast){
					$postn = strpos(strtolower($ast),strtolower($keyword[$arrst]));
					if ($postn === false){
						continue;
					}
					else{
						if ($postn == 0){
							$candb = true;
							$keynow = $keys;
							break;
						}
					}
				}
				if (!$candb){
					return array();
				}
				$strinarr = 's.status = \''.$keynow.'\'';
				array_push($sqls,$strinarr);
			}
			
			$field = 's.stockcode';
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		//echo "SELECT s.* FROM stock s".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit;
		
		if ($getreturn == 'data'){
			$dbstock = $db->fetch_all("SELECT s.* FROM stock s".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit);
			
			return $dbstock;
		}
		else if ($getreturn == 'pagenav'){
			$dbstock = $db->fetch_one("SELECT COUNT(s.stockid) AS totalrecord FROM stock s".$innerjoin.$sql.$groupby);
			
			if ($dbstock['totalrecord'] > 0){
				$totalrecord = $dbstock['totalrecord'];
				$totalpage = ceil($totalrecord / $general['showperpage']);
				$startrecord = ($page - 1) * $general['showperpage'] + 1;
				$endrecord = $startrecord + $general['showperpage'] - 1;
				if ($endrecord > $totalrecord){
					$endrecord = $totalrecord;
				}
			}
			else{
				$page = 0;
				$totalrecord = 0;
				$totalpage = 0;
				$startrecord = 0;
				$endrecord = 0;
			}
			
			return $page.'|^|'.$totalrecord.'|^|'.$totalpage.'|^|'.$startrecord.'|^|'.$endrecord;
		}
		
		return $dbstock;
	}
	
	function deleteStock(){
		global $db;
		
		if (!empty($this->id)){			
			$fst = $this->getFirstStock();
			if (!empty($fst['stockcode'])){
				$db->query("DELETE FROM stockpartno WHERE stockcode='".$db->clean($fst['stockcode'])."'");
			}
			$allphotos = $this->getAllPhotos('partial');
			if (sizeof($allphotos) > 0){
				foreach ($allphotos as $aphs){
					if (!empty($aphs['filename'])){
						if (file_exists('products/'.$aphs['filename'])){
							unlink('products/'.$aphs['filename']);
						}
					}
				}
			}
			$db->query("DELETE FROM stockphotos WHERE stockid='".$this->id."'");
			$db->query("DELETE FROM stock WHERE stockid='".$this->id."'");
		}
	}
	
	function addStock($quantity,$mode = ''){
		global $db;
		
		$upd = $this->getStockAll();
		if ($upd['minp'] > 0){
			$updates['buyminprice'] = $upd['minp'];
		}
		if ($upd['maxp'] > 0){
			$updates['buymaxprice'] = $upd['maxp'];
		}
		if ($upd['mexp'] > 0){
			$updates['minexpdate'] = $upd['mexp'];
		}

		$updating = false;
		if (sizeof($updates) > 0){
			$updating = true;
			$db->update("stock",$updates,"stockcode='".$this->code."'");
		}

		if ($mode == 'deleted' && !$updating){
			$getlastbuy = $db->fetch_one("SELECT * FROM detailbuy WHERE stockcode='".$this->code."' ORDER BY buydate DESC LIMIT 1");
			if (empty($getlastbuy['dbid'])){
				$getfirststock = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$this->code."'");
				$db->query("UPDATE stock SET buyminprice='".$getfirststock['buyprice']."', buymaxprice='".$getfirststock['buyprice']."', minexpdate='".$getfirststock['expdate']."' WHERE stockcode='".$this->code."'");
			}
			else{
				$db->query("UPDATE stock SET buyminprice=".$getlastbuy['realbuyprice'].", buymaxprice=".$getlastbuy['realbuyprice'].", minexpdate=".$getlastbuy['expdate']." WHERE stockcode='".$this->code."'");
			}
		}

		if (!empty($quantity)){
			//$db->query("UPDATE stock SET realremaining=realremaining+".$quantity.", buyminprice=".$upd['minp'].", buymaxprice=".$upd['maxp'].", minexpdate=".$upd['mexp']." WHERE stockcode='".$this->code."'");
			$db->query("UPDATE stock SET realremaining=realremaining+".$quantity." WHERE stockcode='".$this->code."'");

			$insertlgs['stockcode'] = $this->code;
			$insertlgs['quantity'] = $quantity;
			$insertlgs['url'] = currentURL();
			$insertlgs['tipe'] = '+';
			$insertlgs['times'] = date("Y-m-d H:i:s");
			$db->insert("logs",$insertlgs);
		}
		//else{
			//$db->query("UPDATE stock SET buyminprice=".$upd['minp'].", buymaxprice=".$upd['maxp'].", minexpdate=".$upd['mexp']." WHERE stockcode='".$this->code."'");
		//}
	}
	
	function minStock($quantity,$mode = ''){
		global $db;
		
		if (!empty($quantity)){
			$upd = $this->getStockAll();
			if ($upd['minp'] > 0){
				$updates['buyminprice'] = $upd['minp'];
			}
			if ($upd['maxp'] > 0){
				$updates['buymaxprice'] = $upd['maxp'];
			}
			if ($upd['mexp'] > 0){
				$updates['minexpdate'] = $upd['mexp'];
			}

			$updating = false;
			if (sizeof($updates) > 0){
				$db->update("stock",$updates,"stockcode='".$this->code."'");
				$updating = true;
			}

			if ($mode == 'deleted' && !$updating){
				$getlastbuy = $db->fetch_one("SELECT * FROM detailbuy WHERE stockcode='".$this->code."' ORDER BY buydate DESC LIMIT 1");
				if (empty($getlastbuy['dbid'])){
					$getfirststock = $db->fetch_one("SELECT * FROM stock WHERE stockcode='".$this->code."'");
					$db->query("UPDATE stock SET buyminprice='".$getfirststock['buyprice']."', buymaxprice='".$getfirststock['buyprice']."', minexpdate='".$getfirststock['expdate']."' WHERE stockcode='".$this->code."'");
				}
				else{
					$db->query("UPDATE stock SET buyminprice=".$getlastbuy['realbuyprice'].", buymaxprice=".$getlastbuy['realbuyprice'].", minexpdate=".$getlastbuy['expdate']." WHERE stockcode='".$this->code."'");
				}
			}

			$db->query("UPDATE stock SET realremaining=realremaining-".$quantity." WHERE stockcode='".$this->code."'");
			//$db->query("UPDATE stock SET realremaining=realremaining-".$quantity.", buyminprice=".$upd['minp'].", buymaxprice=".$upd['maxp'].", minexpdate=".$upd['mexp']." WHERE stockcode='".$this->code."'");

			$insertlgs['stockcode'] = $this->code;
			$insertlgs['quantity'] = $quantity;
			$insertlgs['url'] = currentURL();
			$insertlgs['tipe'] = '-';
			$insertlgs['times'] = date("Y-m-d H:i:s");
			$db->insert("logs",$insertlgs);
		}
	}
	
	function addTotalStock($quantity){
		global $db;
		
		if (!empty($quantity)){
			$db->query("UPDATE stock SET totalstock=totalstock+".$quantity." WHERE stockcode='".$this->code."'");
		}
	}
	
	function minTotalStock($quantity){
		global $db;
		
		if (!empty($quantity)){
			$db->query("UPDATE stock SET totalstock=totalstock-".$quantity." WHERE stockcode='".$this->code."'");
		}
	}
	
	function getPhotoById($photoid){
		global $db;
		
		if (!empty($photoid)){
			$dbpid = $db->fetch_one("SELECT * FROM stockphotos WHERE photoid='".$photoid."'");
		}
		return $dbpid;
	}
	
	function getAllPhotos($mode){
		global $db;
		
		if (!empty($this->id)){
			$sqls = array();
			array_push($sqls,'stockid=\''.$this->id.'\'');
			if ($mode == 'partial'){
				array_push($sqls,'status = 1');
			}
			
			$sql = '';
			if (sizeof($sqls) > 0){
				$sql = ' WHERE '.implode(' AND ',$sqls);
			}
			$dbpid = $db->fetch_all("SELECT * FROM stockphotos".$sql." ORDER BY mains DESC, lastedited DESC");
		}
		return $dbpid;
	}
	
	function getMainPhoto(){
		global $db;
		
		if (!empty($this->id)){
			$dbpid = $db->fetch_one("SELECT * FROM stockphotos WHERE stockid='".$this->id."' AND mains=1 LIMIT 1");
		}
		return $dbpid;
	}
}
?>