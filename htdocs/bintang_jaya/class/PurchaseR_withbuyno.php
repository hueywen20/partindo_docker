<?php
class PurchaseR{
	var $id;
	var $dtid;
	var $buyno;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setBuyNo($buyno){
		$this->buyno = $buyno;
	}
	
	function setDetailId($dtid){
		$this->dtid = $dtid;
	}
	
	function getListPurchaseR($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpurchaser = $db->fetch_all("SELECT * FROM headerbuyr".$sql." ORDER BY buyrdate");
		
		return $dbpurchaser;
	}
	
	function getHeaderBuyR(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'buyrid = \''.$this->id.'\'');
		}
		else if (!empty($this->buyno)){
			array_push($sqls,'buyno = \''.$this->buyno.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuyr = $db->fetch_one("SELECT * FROM headerbuyr".$sql);
		
		return $dbbuyr;
	}
	
	function getDetailBuyR(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'buyrid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuyr = $db->fetch_all("SELECT * FROM detailbuyr".$sql." ORDER BY dbrid");
		
		return $dbbuyr;
	}
	
	function getDetailBuyRIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dbrid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuy = $db->fetch_one("SELECT * FROM detailbuyr".$sql);
		
		return $dbbuy;
	}
	
	function saveHeaderBuyR($buyno,$buydate,$buyrdate,$suppliercode,$supplieraddrid,$description,$totals,$disc,$tax,$totalbuyr,$userid){
		global $db,$supplier;
		$inserts['buyno'] = $buyno;
		$inserts['buydate'] = $buydate;
		$inserts['buyrdate'] = $buyrdate;
		$inserts['suppliercode'] = $suppliercode;
		$inserts['supplieraddrid'] = $supplieraddrid;
		$inserts['description'] = $description;
		$inserts['totals'] = $totals;
		$inserts['disc'] = $disc;
		$inserts['tax'] = $tax;
		$inserts['totalbuyr'] = $totalbuyr;
		$inserts['status'] = 1;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$lastid = $db->insert("headerbuyr",$inserts);
		
		$supplier->setCode($suppliercode);
		$supplier->minDebt($totalbuyr);
		
		return $lastid;
	}
	
	function saveDetailBuyR($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$buyrprice,$disc,$totalbuyrad,$description,$buyrdate,$totals,$buyrpricead,$realbuyrprice,$quantity,$unitquantity,$unitcode){
		global $db,$stock;
		if (!empty($this->id)){
			$inserts['buyrid'] = $this->id;
			$inserts["buyrdate"] = $buyrdate;
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
            $inserts["buyrprice"] = $buyrprice;
			$inserts["totals"] = $totals;
			$inserts["disc"] = $disc;
			$inserts["buyrpricead"] = $buyrpricead;
			$inserts["totalbuyrad"] = $totalbuyrad;
			$inserts["realbuyrprice"] = $realbuyrprice;
			$inserts["description"] = $description;
			
			$lastdbrid = $db->insert("detailbuyr",$inserts);
			
			//insert to detail buy return item
			$this->saveDetailRItem($lastdbrid,$stockcode,$quantity);
			
			$stock->setCode($stockcode);
			$stock->minStock($quantity);
		}
	}
	
	function updateHeaderBuyR($buyno,$buydate,$buyrdate,$suppliercode,$supplieraddrid,$description,$totals,$disc,$tax,$totalbuyr,$userid){
		global $db,$supplier;
		//get old header buy return
		$oldheader = $this->getHeaderBuyR();
		
		$updates['buyno'] = $buyno;
		$updates['buydate'] = $buydate;
		$updates['buyrdate'] = $buyrdate;
		$updates['suppliercode'] = $suppliercode;
		$updates['supplieraddrid'] = $supplieraddrid;
		$updates['description'] = $description;
		$updates['totals'] = $totals;
		$updates['disc'] = $disc;
		$updates['tax'] = $tax;
		$updates['totalbuyr'] = $totalbuyr;
		$updates['status'] = 1;
		$updates['lastedited'] = time();
		$updates['lasteditedby'] = $userid;
		
		$db->update("headerbuyr",$updates,"buyrid='".$this->id."'");
		
		if ($oldheader['suppliercode'] != $suppliercode){
			$supplier->setCode($oldheader['suppliercode']);
			$supplier->addDebt($oldheader['totalbuyr']);
			
			$supplier->setCode($suppliercode);
			$supplier->minDebt($totalbuyr);
		}
		else if ($oldheader['totalbuyr'] != $totalbuyr){
			$supplier->setCode($suppliercode);
			$supplier->addDebt($oldheader['totalbuyr']-$totalbuyr);
		}
	}
	
	function updateDetailBuyR($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$buyrprice,$disc,$totalbuyrad,$description,$buyrdate,$totals,$buyrpricead,$realbuyrprice,$quantity,$unitquantity,$unitcode,$olddetail,$detailbuy){
		global $db,$stock;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($detailbuy['dbid'])){
				$updates["dbid"] = $detailbuy['dbid'];
			}
			if (!empty($buydate)){
				$updates["buyrdate"] = $buyrdate;
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
			if (!empty($buyrprice)){
				$updates["buyrprice"] = $buypricer;
			}
			if (!empty($totals)){
				$updates["totals"] = $totals;
			}
			if (!empty($disc)){
				$updates["disc"] = $disc;
			}
			if (!empty($buyrpricead)){
				$updates["buyrpricead"] = $buyrpricead;
			}
			if (!empty($totalbuyrad)){
				$updates["totalbuyrad"] = $totalbuyrad;
			}
			if (!empty($realbuyrprice)){
				$updates["realbuyrprice"] = $realbuyrprice;
			}
			if (!empty($description)){
				$updates["description"] = $description;
			}
			
			if (sizeof($updates) > 0){
				if ($olddetail['stockcode'] != $stockcode){
					if ($detailbuy['usedqty'] == 0){
						$stock->setCode($olddetail['stockcode']);
						$db->query("DELETE FROM detailbuyritem WHERE dbrid='".$olddetail['dbrid']."'");
						$db->update("UPDATE detailbuy SET usedqty=usedqty-".$quantity." WHERE dbid='".$olddetail['dbid']."'");
						$stock->addStock($olddetail['quantity']);
						
						$stock->setCode($stockcode);
						
						unset($inserts);
						$inserts['dbrid'] = $this->dtid;
						$inserts['dbid'] = $detailbuy['dbid'];
						$inserts['quantity'] = $quantity;
						$db->insert("detailbuyritem",$inserts);
						$db->update("UPDATE detailbuy SET usedqty=usedqty+".$quantity." WHERE dbid='".$detailbuy['dbid']."'");
						
						$stock->minStock($quantity);
						
						$db->update("detailbuyr",$updates,"dbrid='".$this->dtid."'");
					}
				}
				else if ($olddetail['quantity'] != $quantity){
					if ($quantity >= $detailbuy['usedqty']){
						$stock->setCode($stockcode);
						$stock->addStock($olddetail['quantity']-$quantity);
						
						$db->update("UPDATE detailbuyr SET quantity=".$quantity." WHERE dbrid='".$this->dtid."'");
						$db->update("UPDATE detailbuy SET usedqty=usedqty+".($quantity-$olddetail['quantity'])." WHERE dbid='".$detailbuy['dbid']."'");
						
						$db->update("detailbuyr",$updates,"dbrid='".$this->dtid."'");
					}
				}
				else{
					$db->update("detailbuyr",$updates,"dbrid='".$this->dtid."'");
					$stock->setCode($stockcode);
					$stock->addStock(0);
				}
			}
		}
	}
	
	function saveDetailRItem($dbrid,$stockcode,$quantity){
		global $db;
		
		if ($quantity > 0 && !empty($this->buyno)){
			$allcodedetail = $db->fetch_all("SELECT * FROM detailbuy WHERE buyno='".$this->buyno."' AND stockcode='".$stockcode."' AND quantity > usedqty ORDER BY buydate,expdate");
			if (sizeof($allcodedetail) > 0){
				foreach ($allcodedetail as $str){
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
					$inserts['dbrid'] = $dbrid;
					$inserts['dbid'] = $str['dbid'];
					$inserts['quantity'] = $valuetoinsert;
					$db->insert("detailbuyritem",$inserts);
				
					$db->query("UPDATE detailbuy SET usedqty=usedqty+".$valuetoupdate." WHERE dbid='".$str['dbid']."'");
				}
			}
		}
	}
	
	function deleteDetailBuyR(){
		global $db;
		
		if (!empty($this->dtid)){
			$db->query("DELETE FROM detailbuyr WHERE dbrid='".$this->dtid."'");
		}
	}
	
	function deleteBuyR(){
		global $db, $stock;
		
		if (!empty($this->id)){
			$alldetail = $this->getDetailBuyR();
			if (sizeof($alldetail) > 0){
				foreach ($alldetail as $ad){
					$stock->setCode($ad['stockcode']);
					
					$db->query("DELETE FROM detailbuyritem WHERE dbrid='".$ad['dbrid']."'");
					$db->update("UPDATE detailbuy SET usedqty=usedqty-".$quantity." WHERE dbid='".$ad['dbid']."'");
					
					$stock->addStock($ad['quantity']);
				}
				$db->query("DELETE FROM detailbuyr WHERE buyrid='".$this->id."'");
			}
			$db->query("DELETE FROM headerbuyr WHERE buyrid='".$this->id."'");
		}
	}
}
?>