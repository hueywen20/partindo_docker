<?php
class AdjustOut{
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
	
	function getListAdjustOut($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustout = $db->fetch_all("SELECT * FROM headeradjustout".$sql." ORDER BY aoutdate");
		
		return $dbadjustout;
	}
	
	function getHeaderAdjustOut(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'aoutid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustout = $db->fetch_one("SELECT * FROM headeradjustout".$sql);
		
		return $dbadjustout;
	}
	
	function getDetailAdjustOut(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'aoutid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustout = $db->fetch_all("SELECT * FROM detailadjustout".$sql." ORDER BY daoutid");
		
		return $dbadjustout;
	}
	
	function getDetailAdjustOutIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'daoutid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbadjustout = $db->fetch_one("SELECT * FROM detailadjustout".$sql);
		
		return $dbadjustout;
	}
	
	function getDetailAdjustOutItem(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'daoutid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_all("SELECT * FROM detailadjustoutitem".$sql." ORDER BY daoutiid DESC");
		
		return $dbsale;
	}
	
	function saveHeaderAdjustOut($aoutdate,$description,$totalaout,$userid){
		global $db;
		$inserts['aoutdate'] = $aoutdate;
		$inserts['description'] = $description;
		$inserts['totalaout'] = $totalaout;
		$inserts['status'] = 1;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$lastid = $db->insert("headeradjustout",$inserts);
		
		return $lastid;
	}
	
	function saveDetailAdjustOut($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$aoutprice,$totalaoutprice,$description,$aoutdate,$realaoutprice,$quantity,$unitquantity,$unitcode){
		global $db,$stock;
		if (!empty($this->id)){
			$inserts['aoutid'] = $this->id;
			$inserts["aoutdate"] = $aoutdate;
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
            $inserts["aoutprice"] = $aoutprice;
			$inserts["totalaoutprice"] = $totalaoutprice;
			$inserts["realaoutprice"] = $realaoutprice;
			$inserts["description"] = $description;
			
			$lastdbrid = $db->insert("detailadjustout",$inserts);
			
			//insert to detail adjust in item
			$this->saveDetailAOutItem($lastdbrid,$stockcode,$quantity);
			
			$stock->setCode($stockcode);
			$stock->minStock($quantity);
		}
	}
	
	function updateHeaderAdjustOut($aoutdate,$description,$totalaout,$userid){
		global $db;
		//get old header adjust in
		$oldheader = $this->getHeaderAdjustOut();
		
		$updates['aoutdate'] = $aoutdate;
		$updates['description'] = $description;
		$updates['totalaout'] = $totalaout;
		$updates['status'] = 1;
		$updates['lastedited'] = time();
		$updates['lasteditedby'] = $userid;
		
		$db->update("headeradjustout",$updates,"aoutid='".$this->id."'");
	}
	
	function updateDetailAdjustOut($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$aoutprice,$totalaoutprice,$description,$aoutdate,$realaoutprice,$quantity,$unitquantity,$unitcode,$olddetail){
		global $db,$stock;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($aoutdate)){
				$updates["aoutdate"] = $aoutdate;
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
			if (!empty($aoutprice)){
				$updates["aoutprice"] = $aoutprice;
			}
			if (!empty($totalaoutprice)){
				$updates["totalaoutprice"] = $totalaoutprice;
			}
			if (!empty($realaoutprice)){
				$updates["realaoutprice"] = $realaoutprice;
			}
			if (!empty($description)){
				$updates["description"] = $description;
			}
			
			if (sizeof($updates) > 0){
				$db->update("detailadjustout",$updates,"daoutid='".$this->dtid."'");
				if (($olddetail['stockcode'] != $stockcode && !empty($stockcode)) || ($olddetail['quantity'] != $quantity && !empty($quantity))){					
					$stock->setCode($olddetail['stockcode']);
					$this->deleteDetailAOutItem($olddetail['stockcode']);
					$stock->addStock($olddetail['quantity']);
					
					$stock->setCode($stockcode);
					$this->saveDetailAOutItem($this->dtid,$stockcode,$quantity);
					$stock->minStock($quantity);
				}
				else{
					$stock->setCode($stockcode);
					$stock->addStock(0);
				}
			}
		}
	}
	
	function saveDetailAOutItem($dbaoutid,$stockcode,$quantity){
		global $db,$stock;
		
		if ($quantity > 0){
			$gstr = $stock->getStockToReduced();
			if (sizeof($gstr) > 0){
				foreach ($gstr as $str){
					if ($quantity == 0){
						break;
					}
					$valuetoinsert = 0;
					$valuetoupdate = 0;
					$stockrm = $str['quantity'] - $str['usedqty'];
					if ($quantity <= $stockrm){
						$valuetoupdate = $quantity;
						$valuetoinsert = $quantity;
						$quantity = 0;
					}
					else{
						$valuetoupdate = $stockrm;
						$valuetoinsert = $stockrm;
						$quantity = $quantity - $stockrm;
					}
					$inserts['daoutid'] = $dbaoutid;
					$inserts['dbid'] = $str['dbid'];
					$inserts['quantity'] = $valuetoinsert;
					$db->insert("detailadjustoutitem",$inserts);
				
					$db->query("UPDATE detailbuy SET usedqty=usedqty+".$valuetoupdate." WHERE dbid='".$str['dbid']."'");
				}
			}
		}
	}
	
	function deleteDetailAOutItem($stockcode){
		global $db;
		
		if (!empty($this->dtid)){
			$olddetailitem = $this->getDetailAdjustOutItem();
			if (sizeof($olddetailitem) > 0){
				foreach ($olddetailitem as $odi){
					if ($odi['dbid'] == -1){
						$db->query("UPDATE stock SET remaining=remaining+".$odi['quantity']." WHERE stockcode='".$stockcode."'");
					}
					else{
						$db->query("UPDATE detailbuy SET usedqty=usedqty-".$odi['quantity']." WHERE dbid='".$odi['dbid']."'");
					}
				}
				$db->query("DELETE FROM detailadjustoutitem WHERE daoutid='".$this->dtid."'");
			}
		}
	}
	
	function deleteAdjustOut(){
		global $db, $stock;
		
		if (!empty($this->id)){
			$alldetail = $this->getDetailAdjustOut();
			if (sizeof($alldetail) > 0){
				foreach ($alldetail as $ad){
					$stock->setCode($ad['stockcode']);
					
					$this->setDetailId($ad['daoutid']);
					$this->deleteDetailAOutItem($ad['stockcode']);
					
					$stock->addStock($ad['quantity']);
				}
				$db->query("DELETE FROM detailadjustout WHERE aoutid='".$this->id."'");
			}
			$db->query("DELETE FROM headeradjustout WHERE aoutid='".$this->id."'");
		}
	}
	
	function searchAdjustOut($keyword,$field,$page = -1){
		global $db, $general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		$sqls = array();
		/* if (isset($keyword)){ */
			if (empty($field)){
				$field = 'p.aoutdate';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			if ($field != 'aoutdate'){
				global $general;
				$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
				$enddatetoshow = strtotime("31-12-".$general['yearactiveend']);

				array_push($sqls,'p.aoutdate >= \''.$startdatetoshow.'\' AND p.aoutdate <= \''.$enddatetoshow.'\'');
			}
			
			switch ($field){
				case 'aoutdate' : 
					$strinarr = 'p.aoutdate=\''.strtotime($keyword).'\'';
					$field = 'p.aoutdate';
					break;
				case 'stockcode' : 
					$innerjoin = ' INNER JOIN detailadjustout d ON p.aoutid = d.aoutid';
					$strinarr = 'd.stockcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aoutdate';
					$groupby = ' GROUP BY p.aoutid';
					break;
				case 'partno' : 
					$innerjoin = ' INNER JOIN detailadjustout d ON p.aoutid = d.aoutid';
					$strinarr = 'd.partno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aoutdate';
					$groupby = ' GROUP BY p.aoutid';
					break;
				case 'stockname' : 
					$innerjoin = ' INNER JOIN detailadjustout d ON p.aoutid = d.aoutid';
					$strinarr = 'd.stockname LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aoutdate';
					$groupby = ' GROUP BY p.aoutid';
					break;
				case 'brandcode' : 
					$innerjoin = ' INNER JOIN detailadjustout d ON p.aoutid = d.aoutid';
					$strinarr = 'd.brandcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aoutdate';
					$groupby = ' GROUP BY p.aoutid';
					break;
				case 'typecode' : 
					$innerjoin = ' INNER JOIN detailadjustout d ON p.aoutid = d.aoutid';
					$strinarr = 'd.typecode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aoutdate';
					$groupby = ' GROUP BY p.aoutid';
					break;
				default : 
					$strinarr = $field.' LIKE (\''.$db->clean($keyword).'%\')';
					$field = 'p.aoutdate';
					break;
			}
			array_push($sqls,$strinarr);
		/* } */
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbaout = $db->fetch_all("SELECT p.* FROM headeradjustout p".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit);
		
		return $dbaout;
	}
}
?>