<?php
class AdjustIn{
	var $id;
	var $dtid;
	
	function setId($id){
		global $db;
		$this->id = $db->clean($id);
	}
	
	function setDetailId($dtid){
		global $db;
		if (stristr($dtid,"r-")){
			$dtid = str_replace("r-","",$dtid);
		}
		$this->dtid = $db->clean($dtid);
	}
	
	function getListAdjustIn($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustin = $db->fetch_all("SELECT * FROM headeradjustin".$sql." ORDER BY aindate");
		
		return $dbadjustin;
	}
	
	function getHeaderAdjustIn(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'ainid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustin = $db->fetch_one("SELECT * FROM headeradjustin".$sql);
		
		return $dbadjustin;
	}
	
	function getDetailAdjustIn(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'ainid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustin = $db->fetch_all("SELECT * FROM detailadjustin".$sql." ORDER BY dainid");
		
		return $dbadjustin;
	}
	
	function getDetailAdjustInIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dainid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustin = $db->fetch_one("SELECT * FROM detailadjustin".$sql);
		
		return $dbadjustin;
	}
	
	function getDetailAdjustInItem(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dainid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_all("SELECT * FROM detailadjustinitem".$sql." ORDER BY dainiid DESC");
		
		return $dbsale;
	}
	
	function saveHeaderAdjustIn($aindate,$description,$totalain,$userid){
		global $db;
		$inserts['aindate'] = $aindate;
		$inserts['description'] = $description;
		$inserts['totalain'] = $totalain;
		$inserts['status'] = 1;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$lastid = $db->insert("headeradjustin",$inserts);
		
		return $lastid;
	}
	
	function saveDetailAdjustIn($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$ainprice,$totalainprice,$description,$aindate,$realainprice,$quantity,$unitquantity,$unitcode){
		global $db,$stock;
		if (!empty($this->id)){
			$inserts['ainid'] = $this->id;
			$inserts["aindate"] = $aindate;
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
            $inserts["ainprice"] = $ainprice;
			$inserts["totalainprice"] = $totalainprice;
			$inserts["realainprice"] = $realainprice;
			$inserts["description"] = $description;
			
			$lastdbrid = $db->insert("detailadjustin",$inserts);
			
			//insert to detail adjust in item
			$this->saveDetailAInItem($lastdbrid,$stockcode,$quantity);
			
			$stock->setCode($stockcode);
			$stock->addStock($quantity);
		}
	}
	
	function updateHeaderAdjustIn($aindate,$description,$totalain,$userid){
		global $db;
		//get old header adjust in
		$oldheader = $this->getHeaderAdjustIn();
		
		$updates['aindate'] = $aindate;
		$updates['description'] = $description;
		$updates['totalain'] = $totalain;
		$updates['status'] = 1;
		$updates['lastedited'] = time();
		$updates['lasteditedby'] = $userid;
		
		$db->update("headeradjustin",$updates,"ainid='".$this->id."'");
	}
	
	function updateDetailAdjustIn($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$ainprice,$totalainprice,$description,$aindate,$realainprice,$quantity,$unitquantity,$unitcode,$olddetail){
		global $db,$stock;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($aindate)){
				$updates["aindate"] = $aindate;
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
			if (!empty($ainprice)){
				$updates["ainprice"] = $ainprice;
			}
			if (!empty($totalainprice)){
				$updates["totalainprice"] = $totalainprice;
			}
			if (!empty($realainprice)){
				$updates["realainprice"] = $realainprice;
			}
			if (!empty($description)){
				$updates["description"] = $description;
			}
			
			if (sizeof($updates) > 0){
				$db->update("detailadjustin",$updates,"dainid='".$this->dtid."'");
				if (($olddetail['stockcode'] != $stockcode && !empty($stockcode)) || ($olddetail['quantity'] != $quantity && !empty($quantity))){					
					$stock->setCode($olddetail['stockcode']);
					$this->deleteDetailAInItem($olddetail['stockcode']);
					$stock->minStock($olddetail['quantity']);
					
					$stock->setCode($stockcode);
					$this->saveDetailAInItem($this->dtid,$stockcode,$quantity);
					$stock->addStock($quantity);
				}
				else{
					$stock->setCode($stockcode);
					$stock->addStock(0);
				}
			}
		}
	}
	
	function saveDetailAInItem($dbainid,$stockcode,$quantity){
		global $db;
		
		if ($quantity > 0){
			$allcodedetail = $db->fetch_all("SELECT * FROM detailbuy WHERE stockcode='".$stockcode."' AND usedqty > 0 ORDER BY buydate DESC, expdate DESC");
			if (sizeof($allcodedetail) > 0){
				foreach ($allcodedetail as $str){
					if ($quantity == 0){
						break;
					}
					$valuetoinsert = 0;
					$valuetoupdate = 0;
					
					if ($quantity <= $str['usedqty']){
						$valuetoupdate = $quantity;
						$valuetoinsert = $quantity;
						$quantity = 0;
					}
					else{
						$valuetoupdate = $str['usedqty'];
						$valuetoinsert = $str['usedqty'];
						$quantity = $quantity - $str['usedqty'];
					}
					$inserts['dainid'] = $dbainid;
					$inserts['dbid'] = $str['dbid'];
					$inserts['quantity'] = $valuetoinsert;
					$db->insert("detailadjustinitem",$inserts);
				
					$db->query("UPDATE detailbuy SET usedqty=usedqty-".$valuetoupdate." WHERE dbid='".$str['dbid']."'");
				}
			}
		}
	}
	
	function deleteDetailAInItem($stockcode){
		global $db;
		
		if (!empty($this->dtid)){
			$olddetailitem = $this->getDetailAdjustInItem();
			if (sizeof($olddetailitem) > 0){
				foreach ($olddetailitem as $odi){
					if ($odi['dbid'] == -1){
						$db->query("UPDATE stock SET remaining=remaining+".$odi['quantity']." WHERE stockcode='".$stockcode."'");
					}
					else{
						$db->query("UPDATE detailbuy SET usedqty=usedqty+".$odi['quantity']." WHERE dbid='".$odi['dbid']."'");
					}
				}
				$db->query("DELETE FROM detailadjustinitem WHERE dainid='".$this->dtid."'");
			}
		}
	}
	
	function deleteAdjustIn(){
		global $db, $stock;
		
		if (!empty($this->id)){
			$alldetail = $this->getDetailAdjustIn();
			if (sizeof($alldetail) > 0){
				foreach ($alldetail as $ad){
					$stock->setCode($ad['stockcode']);

					$this->setDetailId($ad['dainid']);
					$this->deleteDetailAInItem($ad['stockcode']);
					
					$stock->minStock($ad['quantity']);
				}
				$db->query("DELETE FROM detailadjustin WHERE ainid='".$this->id."'");
			}
			$db->query("DELETE FROM headeradjustin WHERE ainid='".$this->id."'");
		}
	}
	
	function searchAdjustIn($keyword,$field,$page = -1){
		global $db, $general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		$sqls = array();
		/* if (isset($keyword)){ */
			if (empty($field)){
				$field = 'p.aindate';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			if ($field != 'aindate'){
				global $general;
				$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
				$enddatetoshow = strtotime("31-12-".$general['yearactiveend']);

				array_push($sqls,'p.aindate >= \''.$startdatetoshow.'\' AND p.aindate <= \''.$enddatetoshow.'\'');
			}
			
			switch ($field){
				case 'aindate' : 
					$strinarr = 'p.aindate=\''.strtotime($keyword).'\'';
					$field = 'p.aindate';
					break;
				case 'stockcode' : 
					$innerjoin = ' INNER JOIN detailadjustin d ON p.ainid = d.ainid';
					$strinarr = 'd.stockcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aindate';
					$groupby = ' GROUP BY p.ainid';
					break;
				case 'partno' : 
					$innerjoin = ' INNER JOIN detailadjustin d ON p.ainid = d.ainid';
					$strinarr = 'd.partno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aindate';
					$groupby = ' GROUP BY p.ainid';
					break;
				case 'stockname' : 
					$innerjoin = ' INNER JOIN detailadjustin d ON p.ainid = d.ainid';
					$strinarr = 'd.stockname LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aindate';
					$groupby = ' GROUP BY p.ainid';
					break;
				case 'brandcode' : 
					$innerjoin = ' INNER JOIN detailadjustin d ON p.ainid = d.ainid';
					$strinarr = 'd.brandcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aindate';
					$groupby = ' GROUP BY p.ainid';
					break;
				case 'typecode' : 
					$innerjoin = ' INNER JOIN detailadjustin d ON p.ainid = d.ainid';
					$strinarr = 'd.typecode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aindate';
					$groupby = ' GROUP BY p.ainid';
					break;
				default : 
					$strinarr = $field.' LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aindate';
					break;
			}
			array_push($sqls,$strinarr);
		/* } */
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbain = $db->fetch_all("SELECT p.* FROM headeradjustin p".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit);
		
		return $dbain;
	}
}
?>