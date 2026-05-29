<?php
class Purchase{
	var $id;
	var $dtid;
	var $buyno;
	var $orderno;
	
	function setId($id){
		global $db;
		$this->id = $db->clean($id);
	}
	
	function setBuyNo($buyno){
		global $db;
		$this->buyno = $db->clean($buyno);
	}
	
	function setOrderNo($orderno){
		global $db;
		$this->orderno = $db->clean($orderno);
	}
	
	function setDetailId($dtid){
		global $db;
		if (stristr($dtid,"r-")){
			$dtid = str_replace("r-","",$dtid);
		}
		$this->dtid = $db->clean($dtid);
	}
	
	function getListPurchase($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'credit'){
			array_push($sqls,'status = 1');
		}
		else if ($mode == 'cash'){
			array_push($sqls,'status = 2');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpurchase = $db->fetch_all("SELECT * FROM headerbuy".$sql." ORDER BY buydate");
		
		return $dbpurchase;
	}
	
	function getHeaderBuy(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'buyid = \''.$this->id.'\'');
		}
		else if (!empty($this->buyno)){
			array_push($sqls,'buyno = \''.$this->buyno.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuy = $db->fetch_one("SELECT * FROM headerbuy".$sql);
		
		return $dbbuy;
	}
	
	function getDetailBuy(){
		global $db;
		
		$sqls = array();
		if (!empty($this->buyno)){
			array_push($sqls,'buyno = \''.$this->buyno.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuy = $db->fetch_all("SELECT * FROM detailbuy".$sql." ORDER BY dbid");
		
		return $dbbuy;
	}
	
	function getDetailBuyIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dbid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuy = $db->fetch_one("SELECT * FROM detailbuy".$sql);
		
		return $dbbuy;
	}
	
	function checkOrderNoExist($orderno){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'buyid <> \''.$this->id.'\'');
		}
		array_push($sqls,'orderno = \''.$orderno.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM headerbuy".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function saveHeaderBuy($orderno,$buydate,$duedate,$suppliercode,$supplieraddrid,$description,$totals,$disc,$tax,$otherpays,$totalbuy,$trtype,$userid){
		global $db,$supplier;
		if (!$this->checkOrderNoExist($orderno)){
			$inserts['orderno'] = $orderno;
			$inserts['buydate'] = $buydate;
			$inserts['duedate'] = $duedate;
			$inserts['trtype'] = $trtype;
			$inserts['paydate'] = 0;
			$inserts['suppliercode'] = $suppliercode;
			$inserts['supplieraddrid'] = $supplieraddrid;
			$inserts['description'] = $description;
			$inserts['totals'] = $totals;
			$inserts['disc'] = $disc;
			$inserts['tax'] = $tax;
			$inserts['otherpays'] = $otherpays;
			$inserts['totalbuy'] = $totalbuy;
			if ($trtype == 'credit'){
				$inserts['paid'] = 0;
				$inserts['paydate'] = 0;
				$inserts['claims'] = 0;
			}
			else{
				$inserts['paid'] = 1;
				$inserts['paydate'] = $buydate;
				$inserts['claims'] = 1;
			}
			$inserts['status'] = 1;
            $inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$lastid = $db->insert("headerbuy",$inserts);
			$lastbuyno = str_pad($lastid,5,'0',STR_PAD_LEFT);
			$db->query("UPDATE headerbuy SET buyno='".$lastbuyno."' WHERE buyid='".$lastid."'");
			
			if ($trtype == 'credit'){
				$supplier->setCode($suppliercode);
				$supplier->addDebt($totalbuy);
			}
			
			return $lastbuyno;
		}
	}
	
	function saveDetailBuy($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$buyprice,$disc,$totalbuyad,$expdate,$description,$buydate,$totals,$buypricead,$realbuyprice,$quantity,$unitquantity,$unitcode){
		global $db,$stock;
		if (!empty($this->buyno)){
			$inserts['buyno'] = $this->buyno;
			$inserts["buydate"] = $buydate;
            $inserts["stockcode"] = $stockcode;
            $inserts["stockname"] = $stockname;
            $inserts["brandcode"] = $brandcode;
            $inserts["typecode"] = $typecode;
            $inserts["partno"] = $partno;
            $inserts["quantity"] = $quantity;
            $inserts["unitquantity"] = $unitquantity;
            $inserts["quantityf"] = $quantityf;
            $inserts["unitquantityf"] = $unitquantityf;
            $inserts["unitcode"] = $unitcode;
            $inserts["buyprice"] = $buyprice;
			$inserts["totals"] = $totals;
			$inserts["disc"] = $disc;
			$inserts["buypricead"] = $buypricead;
			$inserts["totalbuyad"] = $totalbuyad;
			$inserts["realbuyprice"] = $realbuyprice;
			$inserts["expdate"] = $expdate;
			$inserts["description"] = $description;
			$inserts["usedqty"] = 0;
			
			$db->insert("detailbuy",$inserts);
			
			$stock->setCode($stockcode);
			$stock->addStock($quantity);
			$stock->addTotalStock($quantity);
		}
	}
	
	function updateHeaderBuy($orderno,$buydate,$duedate,$suppliercode,$supplieraddrid,$description,$totals,$disc,$tax,$otherpays,$totalbuy,$trtype,$userid){
		global $db,$supplier;
		if (!$this->checkOrderNoExist($orderno)){
			//get old header buy
			$oldheader = $this->getHeaderBuy();
			
			$updates['orderno'] = $orderno;
			$updates['buydate'] = $buydate;
			$updates['duedate'] = $duedate;
			$updates['trtype'] = $trtype;
			$updates['paydate'] = 0;
			$updates['suppliercode'] = $suppliercode;
			$updates['supplieraddrid'] = $supplieraddrid;
			$updates['description'] = $description;
			$updates['totals'] = $totals;
			$updates['disc'] = $disc;
			$updates['tax'] = $tax;
			$updates['otherpays'] = $otherpays;
			$updates['totalbuy'] = $totalbuy;
			$updates['status'] = 1;
            $updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
			
			$db->update("headerbuy",$updates,"buyid='".$this->id."'");
			
			//sekarang dulu kredit
			if ($oldheader['trtype'] == "credit" && $trtype == "credit"){
			if ($oldheader['suppliercode'] != $suppliercode){
				$supplier->setCode($oldheader['suppliercode']);
				$supplier->minDebt($oldheader['totalbuy']);
				
				$supplier->setCode($suppliercode);
				$supplier->addDebt($totalbuy);
			}
			else if ($oldheader['totalbuy'] != $totalbuy){
				$supplier->setCode($suppliercode);
				$supplier->addDebt($totalbuy-$oldheader['totalbuy']);
			}
			
			}
			
			//dulu cash sekarang kredit
			else if ($oldheader['trtype'] == "cash" && $trtype == "credit"){
			$supplier->setCode($suppliercode);
			$supplier->addDebt($totalbuy);
			}
			
			//dulu kredit sekarang cash
			else if ($oldheader['trtype'] == "credit" && $trtype == "cash"){
			$supplier->setCode($oldheader['suppliercode']);
			$supplier->minDebt($oldheader['totalbuy']);
			}
			
		}
	}
	
	function updateDetailBuy($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$buyprice,$disc,$totalbuyad,$expdate,$description,$buydate,$totals,$buypricead,$realbuyprice,$quantity,$unitquantity,$unitcode,$olddetail){
		global $db,$stock;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($buydate)){
				$updates["buydate"] = $buydate;
			}
			if (!empty($stockcode)){
				$updates["stockcode"] = $stockcode;
			}
            if (!empty($stockname)){
				$updates["stockname"] = $stockname;
			}
            if (!empty($brandcode)){
				$updates["brandcode"] = $brandcode;
			}
            if (!empty($typecode)){
				$updates["typecode"] = $typecode;
			}
            if (!empty($partno)){
				$updates["partno"] = $partno;
			}
            if (!empty($quantity)){
				$updates["quantity"] = $quantity;
			}
			if (!empty($unitquantity)){
				$updates["unitquantity"] = $unitquantity;
			}
			if (!empty($quantityf)){
				$updates["quantityf"] = $quantityf;
			}
			if (!empty($unitquantityf)){
				$updates["unitquantityf"] = $unitquantityf;
			}
            if (!empty($unitcode)){
				$updates["unitcode"] = $unitcode;
			}
			if (!empty($buyprice)){
				$updates["buyprice"] = $buyprice;
			}
			if (!empty($totals)){
				$updates["totals"] = $totals;
			}
			if (!empty($disc)){
				$updates["disc"] = $disc;
			}
			if (!empty($buypricead)){
				$updates["buypricead"] = $buypricead;
			}
			if (!empty($totalbuyad)){
				$updates["totalbuyad"] = $totalbuyad;
			}
			if (!empty($realbuyprice)){
				$updates["realbuyprice"] = $realbuyprice;
			}
			if (!empty($expdate)){
				$updates["expdate"] = $expdate;
			}
			if (!empty($description)){
				$updates["description"] = $description;
			}
			
			if (sizeof($updates) > 0){				
				if ($olddetail['stockcode'] != $stockcode){
					if ($olddetail['usedqty'] == 0){
						//$this->deleteDetailBuy();
						$db->update("detailbuy",$updates,"dbid='".$this->dtid."'");

						$stock->setCode($olddetail['stockcode']);
						$stock->minStock($olddetail['quantity'],'deleted');
						$stock->minTotalStock($quantity);
						
						$stock->setCode($stockcode);
						$stock->addStock($quantity);
						$stock->addTotalStock($quantity);
					}
				}
				else if ($olddetail['quantity'] != $quantity){
					if ($quantity >= $olddetail['usedqty']){
						$db->update("detailbuy",$updates,"dbid='".$this->dtid."'");

						$stock->setCode($stockcode);
						$stock->addStock($quantity-$olddetail['quantity']);
						
						$stock->addTotalStock($quantity-$olddetail['quantity']);						
					}
				}
				else{
					$db->update("detailbuy",$updates,"dbid='".$this->dtid."'");
					$stock->setCode($stockcode);
					$stock->addStock(0,'deleted');
				}
			}
		}
	}
	
	function deleteDetailBuy(){
		global $db;
		
		if (!empty($this->dtid)){
			$db->query("DELETE FROM detailbuy WHERE dbid='".$this->dtid."'");
		}
	}
	
	function canDeleteBuy(){
		global $db;
		
		if (!empty($this->buyno)){
			$rowexist = 0;

			$dbchecksaleitem = $db->query("SELECT * FROM detailsaleitem dsi INNER JOIN detailbuy db ON dsi.dbid = db.dbid INNER JOIN headerbuy hb ON db.buyno = hb.buyno WHERE hb.buyno='".$this->buyno."'");
			$rowexist += @mysql_num_rows($dbchecksaleitem);

			$dbchecksaleritem = $db->query("SELECT * FROM detailsaleritem dsi INNER JOIN detailbuy db ON dsi.dbid = db.dbid INNER JOIN headerbuy hb ON db.buyno = hb.buyno WHERE hb.buyno='".$this->buyno."'");
			$rowexist += @mysql_num_rows($dbchecksaleritem);

			$dbcheckbuyritem = $db->query("SELECT * FROM detailbuyritem dsi INNER JOIN detailbuy db ON dsi.dbid = db.dbid INNER JOIN headerbuy hb ON db.buyno = hb.buyno WHERE hb.buyno='".$this->buyno."'");
			$rowexist += @mysql_num_rows($dbcheckbuyritem);

			//$dbcheck = $db->fetch_one("SELECT SUM(db.usedqty) AS uq FROM headerbuy hb INNER JOIN detailbuy db ON hb.buyno = db.buyno WHERE hb.buyno='".$this->buyno."'");
			//if ($dbcheck['uq'] > 0){
			if ($rowexist > 0){
				return false;
			}
			else{
				return true;
			}
		}
		return false;
	}
	
	function deleteBuy(){
		global $db, $stock;
		
		if (!empty($this->buyno)){
			$alldetail = $this->getDetailBuy();
			if (sizeof($alldetail) > 0){
				$db->query("DELETE FROM detailbuy WHERE buyno='".$this->buyno."'");
				foreach ($alldetail as $ad){
					$stock->setCode($ad['stockcode']);
					$stock->minStock($ad['quantity'],'deleted');
					$stock->minTotalStock($ad['quantity']);
				}
			}
			$db->query("DELETE FROM headerbuy WHERE buyno='".$this->buyno."'");
		}
	}
	
	function getUnpaidBuy($suppliercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'paid = 0');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'buydate >= '.$startdate);
			array_push($sqls,'buydate <= '.$enddate);
		}
		if (!empty($suppliercode)){
			array_push($sqls,'suppliercode = \''.$suppliercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuy = $db->fetch_all("SELECT * FROM headerbuy".$sql." ORDER BY buydate");
		
		return $dbbuy;
	}
	
	function searchPurchase($keyword,$field,$trtype,$page = -1){
		global $db, $general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		$sqls = array();
		/* if (isset($keyword)){ */
			if (empty($field)){
				$field = 'p.buydate';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			if (!empty($trtype)){
				array_push($sqls,'trtype=\''.$trtype.'\'');
			}
			
			if ($field != 'buydate'){
				global $general;
				$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
				$enddatetoshow = strtotime("31-12-".$general['yearactiveend']);

				array_push($sqls,'p.buydate >= \''.$startdatetoshow.'\' AND p.buydate <= \''.$enddatetoshow.'\'');
			}
			
			switch ($field){
				case 'buydate' : 
					$strinarr = 'p.buydate=\''.strtotime($keyword).'\'';
					$field = 'p.buydate';
					break;
				case 'duedate' : 
					$strinarr = 'p.duedate=\''.strtotime($keyword).'\'';
					$field = 'p.buydate';
					break;
				case 'suppliername' : 
					$innerjoin = ' INNER JOIN supplier s ON p.suppliercode = s.suppliercode';
					$strinarr = 's.suppliername LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.suppliername';
					break;
				case 'stockcode' : 
					$innerjoin = ' INNER JOIN detailbuy d ON p.buyno = d.buyno';
					$strinarr = 'd.stockcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.buydate';
					$groupby = ' GROUP BY p.buyno';
					break;
				case 'partno' : 
					$innerjoin = ' INNER JOIN detailbuy d ON p.buyno = d.buyno';
					$strinarr = 'd.partno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.buydate';
					$groupby = ' GROUP BY p.buyno';
					break;
				case 'stockname' : 
					$innerjoin = ' INNER JOIN detailbuy d ON p.buyno = d.buyno';
					$strinarr = 'd.stockname LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.buydate';
					$groupby = ' GROUP BY p.buyno';
					break;
				case 'brandcode' : 
					$innerjoin = ' INNER JOIN detailbuy d ON p.buyno = d.buyno';
					$strinarr = 'd.brandcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.buydate';
					$groupby = ' GROUP BY p.buyno';
					break;
				case 'typecode' : 
					$innerjoin = ' INNER JOIN detailbuy d ON p.buyno = d.buyno';
					$strinarr = 'd.typecode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.buydate';
					$groupby = ' GROUP BY p.buyno';
					break;
				case 'status' :
					global $arrpurchase;
					$candb = false;
					$keynow = -1;
					foreach ($arrpurchase as $keys => $ast){
						$postn = strpos(strtolower($ast),strtolower($keyword));
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
					$strinarr = 'p.status = \''.$keynow.'\'';
					$field = 'p.buydate';
					break;
				default : 
					$strinarr = $field.' LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.buydate';
					break;
			}
			
			array_push($sqls,$strinarr);
		/* } */
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpurchase = $db->fetch_all("SELECT p.* FROM headerbuy p".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit);
		
		return $dbpurchase;
	}
	
	function getUnclaimBuy($suppliercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'claims = 0 AND trtype = \'credit\'');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'buydate >= '.$startdate);
			array_push($sqls,'buydate <= '.$enddate);
		}
		if (!empty($suppliercode)){
			array_push($sqls,'suppliercode = \''.$suppliercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuy = $db->fetch_all("SELECT * FROM headerbuy".$sql." ORDER BY buydate");
		
		return $dbbuy;
	}
}
?>