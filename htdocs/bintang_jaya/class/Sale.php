<?php
class Sale{
	var $id;
	var $dtid;
	var $saleno;
	
	function setId($id){
		global $db;
		$this->id = $db->clean($id);
	}
	
	function setSaleNo($saleno){
		global $db;
		$this->saleno = $db->clean($saleno);
	}
	
	function setDetailId($dtid){
		global $db;
		if (stristr($dtid,"r-")){
			$dtid = str_replace("r-","",$dtid);
		}
		$this->dtid = $db->clean($dtid);
	}
	
	function getListSale($mode){
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
		$dbsale = $db->fetch_all("SELECT * FROM headersale".$sql." ORDER BY saledate");
		
		return $dbsale;
	}
	
	function getHeaderSale(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'saleid = \''.$this->id.'\'');
		}
		else if (!empty($this->saleno)){
			array_push($sqls,'saleno = \''.$this->saleno.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_one("SELECT * FROM headersale".$sql);
		
		return $dbsale;
	}
	
	function getDetailSale(){
		global $db;
		
		$sqls = array();
		if (!empty($this->saleno)){
			array_push($sqls,'saleno = \''.$this->saleno.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_all("SELECT * FROM detailsale".$sql." ORDER BY dsid");
		
		return $dbsale;
	}
	
	function getDetailSaleIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dsid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_one("SELECT * FROM detailsale".$sql." ORDER BY dsid");
		
		return $dbsale;
	}
	
	function getDetailSaleItem($tabledbid = ''){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dsid = \''.$this->dtid.'\'');
		}
		if (!empty($tabledbid)){
			if ($tabledbid == 'detailbuy'){
				array_push($sqls,'tabledbid IN (\'detailbuy\',\'stock\')');
			}
			else{
				array_push($sqls,'tabledbid = \''.$tabledbid.'\'');
			}
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_all("SELECT * FROM detailsaleitem".$sql." ORDER BY dsiid DESC");
		
		return $dbsale;
	}
	
	function checkSaleNoExist($saleno,$saledate){
		global $db;
		
		$salenof = $this->generateSaleNumber($saleno,$saledate);
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'saleid <> \''.$this->id.'\'');
		}
		array_push($sqls,'saleno = \''.$salenof.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM headersale".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function generateSaleNumber($saleno,$saledate){
		global $salesetting;
		
		$monthonedigit = date("n",$saledate);
		$monthtwodigit = date("m",$saledate);
		$yeartwodigit = date("y",$saledate);
		$yearfourdigit = date("Y",$saledate);
		
		$monthromancesmall = '';
		switch ($monthonedigit){
			case 1 : $monthromancesmall = 'i'; break;
			case 2 : $monthromancesmall = 'ii'; break;
			case 3 : $monthromancesmall = 'iii'; break;
			case 4 : $monthromancesmall = 'iv'; break;
			case 5 : $monthromancesmall = 'v'; break;
			case 6 : $monthromancesmall = 'vi'; break;
			case 7 : $monthromancesmall = 'vii'; break;
			case 8 : $monthromancesmall = 'viii'; break;
			case 9 : $monthromancesmall = 'ix'; break;
			case 10 : $monthromancesmall = 'x'; break;
			case 11 : $monthromancesmall = 'xi'; break;
			case 12 : $monthromancesmall = 'xii'; break;
		}
		
		$monthromancebig = strtoupper($monthromancesmall);
		
		$strsaleno = str_ireplace('{SALENO}',$saleno,$salesetting['saleformatno']);
		$strsaleno = str_replace('{m}',$monthonedigit,$strsaleno);
		$strsaleno = str_replace('{M}',$monthtwodigit,$strsaleno);
		$strsaleno = str_replace('{y}',$yeartwodigit,$strsaleno);
		$strsaleno = str_replace('{Y}',$yearfourdigit,$strsaleno);
		$strsaleno = str_replace('{Mr}',$monthromancesmall,$strsaleno);
		$strsaleno = str_replace('{MR}',$monthromancebig,$strsaleno);
		
		return $strsaleno;
	}
	
	function saveHeaderSale($saleno,$saledate,$duedate,$customercode,$customeraddrid,$description,$totals,$disc,$tax,$totalsale,$trtype,$userid){
		global $db,$customer;
		$sfno = $this->generateSaleNumber($saleno,$saledate);
		$inserts['saleno'] = $sfno;
		$inserts['saledate'] = $saledate;
		$inserts['duedate'] = $duedate;
		$inserts['trtype'] = $trtype;
		$inserts['paydate'] = 0;
		$inserts['customercode'] = $customercode;
		$inserts['customeraddrid'] = $customeraddrid;
		$inserts['description'] = $description;
		$inserts['totals'] = $totals;
		$inserts['disc'] = $disc;
		$inserts['tax'] = $tax;
		$inserts['totalsale'] = $totalsale;
		if ($saledate == $duedate){
			$inserts['status'] = 2;
		}
		else{
			$inserts['status'] = 1;
		}
		if ($trtype == 'credit'){
			$inserts['paid'] = 0;
			$inserts['paydate'] = 0;
			$inserts['claims'] = 0;
		}
		else{
			$inserts['paid'] = 1;
			$inserts['paydate'] = $saledate;
			$inserts['claims'] = 1;
		}
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$lastid = $db->insert("headersale",$inserts);
		//$lastsaleno = str_pad($lastid,5,'0',STR_PAD_LEFT);
		//$db->query("UPDATE headersale SET saleno='".$lastsaleno."' WHERE saleid='".$lastid."'");
		
		//update last number
		global $yearsoftwarenow;
		$db->query("UPDATE stockyear SET salenumber=salenumber+1 WHERE year='".$yearsoftwarenow."'");
		
		if ($trtype == 'credit'){
			$customer->setCode($customercode);
			$customer->addCredit($totalsale);
		}
		
		return $sfno;
	}
	
	function saveDetailSale($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$saleprice,$disc,$totalsalead,$description,$saledate,$totals,$salepricead,$realsaleprice,$quantity,$unitquantity,$unitcode){
		global $db,$stock,$assembly,$deassembly,$units;
		if (!empty($this->saleno)){
			$inserts['saleno'] = $this->saleno;
			$inserts["saledate"] = $saledate;
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
            $inserts["saleprice"] = $saleprice;
			$inserts["totals"] = $totals;
			$inserts["disc"] = $disc;
			$inserts["salepricead"] = $salepricead;
			$inserts["totalsalead"] = $totalsalead;
			$inserts["realsaleprice"] = $realsaleprice;
			$inserts["description"] = $description;
			$inserts["returnsale"] = 0;
			
			$lastdsid = $db->insert("detailsale",$inserts);
			
			$stock->setId("");
			$stock->setCode($stockcode);
			$getfs = $stock->getFirstStock();
			if ($getfs['assembly'] == 1){
				$assembly->setCode($stockcode);
				$getac = $assembly->getAssemblyComponent();
				if (sizeof($getac) > 0){
					foreach ($getac as $gac){
						$stock->setCode($gac['stockcodecomponent']);
						$this->saveDetailItem($lastdsid,$gac['stockcodecomponent'],$quantity*$gac['sccquantity']);
						$stock->minStock($quantity*$gac['sccquantity']);
					}
				}
				
				//save to log assembly
				$insertla['dsid'] = $lastdsid;
				$insertla['logdate'] = $saledate;
				$insertla['stockcode'] = $stockcode;
				$insertla['quantity'] = $quantity;
				$insertla['unitquantity'] = $unitquantity;
				$insertla['unitcode'] = $unitcode;
				$insertla['price'] = $getfs['buyprice'];
				$db->insert("logassembly",$insertla);
			}
			else if ($getfs['assembly'] == 2){
				$scparent = $deassembly->getDeAssemblyParent($stockcode);
				if (sizeof($scparent) > 0){
					//save to log deassembly
					$this->saveDetailItemDeAssembly($lastdsid,$stockcode,$quantity,$scparent['sccquantity'],$scparent['stockcode'],$quantityf,$saledate);
					
					$stock->setCode($stockcode);
					$stock->minStock($quantity);
				}
			}
			else{
				$this->saveDetailItem($lastdsid,$stockcode,$quantity);
				$stock->minStock($quantity);
			}
		}
	}
	
	function updateHeaderSale($saleno,$saledate,$duedate,$customercode,$customeraddrid,$description,$totals,$disc,$tax,$totalsale,$trtype,$userid){
		global $db,$customer;
		
		if (!empty($this->id)){
			//get old header sale
			$oldheader = $this->getHeaderSale();
			
			$updates['saleno'] = $saleno;
			$updates['saledate'] = $saledate;
			$updates['duedate'] = $duedate;
			$updates['trtype'] = $trtype;
			$updates['paydate'] = 0;
			$updates['customercode'] = $customercode;
			$updates['customeraddrid'] = $customeraddrid;
			$updates['description'] = $description;
			$updates['totals'] = $totals;
			$updates['disc'] = $disc;
			$updates['tax'] = $tax;
			$updates['totalsale'] = $totalsale;
			$updates['status'] = 1;
			$updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
			$db->update("headersale",$updates,"saleid='".$this->id."'");
			
			if ($oldheader['saleno'] != $saleno){
				$db->query("UPDATE detailsale SET saleno='".$saleno."' WHERE saleno='".$oldheader['saleno']."'");
			}
			
			//sekarang dulu kredit
			if ($oldheader['trtype'] == "credit" && $trtype == "credit"){
			if ($oldheader['customercode'] != $customercode){
				$customer->setCode($oldheader['customercode']);
				$customer->minCredit($oldheader['totalsale']);
				
				$customer->setCode($customercode);
				$customer->addCredit($totalsale);
			}
			else if ($oldheader['totalsale'] != $totalsale){
				$customer->setCode($customercode);
				$customer->addCredit($totalsale-$oldheader['totalsale']);
			}
			
			}
			
			//dulu cash sekarang kredit
			else if ($oldheader['trtype'] == "cash" && $trtype == "credit"){
			$customer->setCode($customercode);
			$customer->addCredit($totalsale);
			}
			
			//dulu kredit sekarang cash
			else if ($oldheader['trtype'] == "credit" && $trtype == "cash"){
			$customer->setCode($oldheader['customercode']);
			$customer->minCredit($oldheader['totalsale']);
			}
			
			
		}
	}
	
	function updateDetailSale($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$saleprice,$disc,$totalsalead,$description,$totals,$salepricead,$realsaleprice,$quantity,$unitquantity,$unitcode,$olddetail){
		global $db,$stock,$assembly,$deassembly;
		$updates = array();
		if (!empty($this->id) && !empty($this->dtid)){
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
			if (!empty($saleprice)){
				$updates["saleprice"] = $saleprice;
			}
			if (!empty($totals)){
				$updates["totals"] = $totals;
			}
			if (!empty($disc)){
				$updates["disc"] = $disc;
			}
			if (!empty($salepricead)){
				$updates["salepricead"] = $salepricead;
			}
			if (!empty($totalsalead)){
				$updates["totalsalead"] = $totalsalead;
			}
			if (!empty($realsaleprice)){
				$updates["realsaleprice"] = $realsaleprice;
			}
			if (!empty($description)){
				$updates["description"] = $description;
			}
			
			//echo $this->dtid;
			//print_r($updates);
			
			if (sizeof($updates) > 0){
				$db->update("detailsale",$updates,"dsid='".$this->dtid."'");
				
				//print_r($olddetail);
				if (($olddetail['stockcode'] != $stockcode && !empty($stockcode)) || ($olddetail['quantity'] != $quantity && !empty($quantity))){
					//echo $olddetail['stockcode'].'-'.$stockcode.'-'.$olddetail['quantity'].'-'.$quantity;
					
					$stock->setId("");
					$stock->setCode($olddetail['stockcode']);
					$getfs = $stock->getFirstStock();
					if ($getfs['assembly'] == 1){
						$assembly->setCode($olddetail['stockcode']);
						$getac = $assembly->getAssemblyComponent();
						if (sizeof($getac) > 0){
							foreach ($getac as $gac){
								$stock->setCode($gac['stockcodecomponent']);
								$this->deleteDetailItem($gac['stockcodecomponent']);
								$stock->addStock($olddetail['quantity']*$gac['sccquantity']);
							}
						}
						$db->query("DELETE FROM logassembly WHERE dsid='".$this->dtid."'");
					}
					else if ($getfs['assembly'] == 2){
						$this->deleteDetailItemDeAssembly($olddetail['stockcode']);
					}
					else{
						$this->deleteDetailItem($olddetail['stockcode']);
						$stock->addStock($olddetail['quantity']);
					}
					
					$stock->setId("");
					$stock->setCode($stockcode);
					$getfs = $stock->getFirstStock();
					if ($getfs['assembly'] == 1){
						$assembly->setCode($stockcode);
						$getac = $assembly->getAssemblyComponent();
						if (sizeof($getac) > 0){
							foreach ($getac as $gac){
								$stock->setCode($gac['stockcodecomponent']);
								$this->saveDetailItem($this->dtid,$gac['stockcodecomponent'],$quantity*$gac['sccquantity']);
								$stock->minStock($quantity*$gac['sccquantity']);
							}
						}
						
						//save to log assembly
						$insertla['dsid'] = $this->dtid;
						$insertla['logdate'] = $saledate;
						$insertla['stockcode'] = $stockcode;
						$insertla['quantity'] = $quantity;
						$insertla['unitquantity'] = $unitquantity;
						$insertla['unitcode'] = $unitcode;
						$insertla['price'] = $getfs['buyprice'];
						$db->insert("logassembly",$insertla);
					}
					else if ($getfs['assembly'] == 2){
						$scparent = $deassembly->getDeAssemblyParent($stockcode);
						if (sizeof($scparent) > 0){
							//save to log deassembly
							$this->saveDetailItemDeAssembly($this->dtid,$stockcode,$quantity,$scparent['sccquantity'],$scparent['stockcode'],$quantityf,$olddetail['saledate']);
							
							$stock->setCode($stockcode);
							$stock->minStock($quantity);
						}
					}
					else{
						$this->saveDetailItem($this->dtid,$stockcode,$quantity);
						$stock->minStock($quantity);
					}					
				}
				else{
					$stock->setCode($stockcode);
					$stock->addStock(0);
				}
			}
		}
	}
	
	function saveDetailItem($lastdsid,$stockcode,$quantity){
		global $db,$stock;
		
		$fstk = $stock->getFirstStock();
		if ($fstk['remaining'] > 0){
			$valuetoinsert = 0;
			$valuetoupdate = 0;
			if ($quantity <= $fstk['remaining']){
				$valuetoupdate = $fstk['remaining'] - $quantity;
				$valuetoinsert = $quantity;
				$quantity = 0;
			}
			else{
				$valuetoupdate = 0;
				$valuetoinsert = $fstk['remaining'];
				$quantity = $quantity - $fstk['remaining'];
			}
			$inserts['dsid'] = $lastdsid;
			$inserts['dbid'] = -1;
			$inserts['quantity'] = $valuetoinsert;
			$inserts['returnquantity'] = 0;
			$inserts['tabledbid'] = 'stock';
			$db->insert("detailsaleitem",$inserts);
		
			$db->query("UPDATE stock SET remaining=".$valuetoupdate." WHERE stockcode='".$stockcode."'");
		}
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
					$inserts['dsid'] = $lastdsid;
					$inserts['dbid'] = $str['dbid'];
					$inserts['quantity'] = $valuetoinsert;
					$inserts['returnquantity'] = 0;
					$inserts['tabledbid'] = 'detailbuy';
					$db->insert("detailsaleitem",$inserts);
				
					$db->query("UPDATE detailbuy SET usedqty=usedqty+".$valuetoupdate." WHERE dbid='".$str['dbid']."'");
				}
			}
		}
	}
	
	function saveDetailItemDeAssembly($lastdsid,$stockcode,$quantity,$qtyfactor,$scparent,$quantityf,$saledate){
		global $db,$stock,$deassembly,$units;
		
		if ($quantity > 0){
			$gstr = $db->fetch_all("SELECT * FROM logdeassembly WHERE stockcode='".$stockcode."' AND usedqty < quantity ORDER BY logdate");
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
					$inserts['dsid'] = $lastdsid;
					$inserts['dbid'] = $str['logid'];
					$inserts['quantity'] = $valuetoinsert;
					$inserts['returnquantity'] = 0;
					$inserts['tabledbid'] = 'logdeassembly';
					$db->insert("detailsaleitem",$inserts);
				
					$db->query("UPDATE logdeassembly SET usedqty=usedqty+".$valuetoupdate." WHERE logid='".$str['logid']."'");
				}
			}
			if ($quantity > 0){
				$deassembly->setCode($scparent);
				$getdacomponent = $deassembly->getDeAssemblyComponent();
				if (sizeof($getdacomponent) > 0){
					$stock->setId("");
					$stock->setCode($scparent);
					$parentfirststock = $stock->getFirstStock();
				
					$parentquantity = ceil($quantity / $qtyfactor);
					
					$this->saveDetailItem($lastdsid,$scparent,$parentquantity);
					$stock->minStock($parentquantity);
					
					$units->setCode($parentfirststock['unitcode']);
					$unitdetail = $units->getunitDetail();
					
					$insertlogdap['logdate'] = $saledate-1;
					$insertlogdap['stockcode'] = $scparent;
					$insertlogdap['quantity'] = $parentquantity;
					$insertlogdap['unitquantity'] = $unitdetail['lunit'];
					$insertlogdap['unitcode'] = $parentfirststock['unitcode'];
					$insertlogdap['description'] = 'deassembly';
					$logparentid = $db->insert("logdeassemblyparent",$insertlogdap);
					
					foreach ($getdacomponent as $gdcpnt){
						$componentquantity = $gdcpnt['sccquantity'] * $parentquantity;
						
						$insertlogda['logdate'] = $saledate-1;
						$insertlogda['logparentid'] = $logparentid;
						$insertlogda['stockcode'] = $gdcpnt['stockcodecomponent'];
						$insertlogda['quantity'] = $componentquantity;
						$insertlogda['unitquantity'] = $gdcpnt['sccunitquantity'];
						//$insertlogda['quantityf'] = $quantityf;
						//$insertlogda['unitquantityf'] = $gdcpnt['sccunitquantityf'];
						$insertlogda['unitcode'] = $gdcpnt['sccunitcode'];
						$insertlogda['description'] = 'Pecahan dari : '.$scparent;
						if ($gdcpnt['stockcodecomponent'] == $stockcode){
							$insertlogda['usedqty'] = $quantity;
						}
						else{
							$insertlogda['usedqty'] = 0;
						}
						
						$stock->setId("");
						$stock->setCode($gdcpnt['stockcodecomponent']);
						$cpntfirststock = $stock->getFirstStock();
						$insertlogda['price'] = $cpntfirststock['buyprice'];
						$insertlogda['status'] = 1;
				
						$lastldaid = $db->insert("logdeassembly",$insertlogda);
						
						if ($gdcpnt['stockcodecomponent'] == $stockcode){
							$insertsame['dsid'] = $lastdsid;
							$insertsame['dbid'] = $lastldaid;
							$insertsame['quantity'] = $quantity;
							$insertsame['returnquantity'] = 0;
							$insertsame['tabledbid'] = 'logdeassembly';
							$db->insert("detailsaleitem",$insertsame);
						}
						
						$stock->setCode($gdcpnt['stockcodecomponent']);
						$stock->addStock($componentquantity);
						$stock->addTotalStock($componentquantity);
					}
				}
			}
		}
	}
	
	function deleteDetailItem($stockcode){
		global $db;
		
		if (!empty($this->dtid)){
			$olddetailitem = $this->getDetailSaleItem();
			if (sizeof($olddetailitem) > 0){
				foreach ($olddetailitem as $odi){
					if ($odi['dbid'] == -1){
						$db->query("UPDATE stock SET remaining=remaining+".$odi['quantity']." WHERE stockcode='".$stockcode."'");
					}
					else{
						$db->query("UPDATE detailbuy SET usedqty=usedqty-".$odi['quantity']." WHERE dbid='".$odi['dbid']."'");
					}
				}
				$db->query("DELETE FROM detailsaleitem WHERE dsid='".$this->dtid."'");
				$db->query("DELETE FROM logassembly WHERE dsid='".$this->dtid."'");
			}
		}
	}
	
	function deleteDetailItemDeAssembly($stockcode){
		global $db,$stock,$deassembly;
		
		if (!empty($this->dtid)){
			$olddetailitem = $this->getDetailSaleItem('logdeassembly');
			$idalready = array();
			if (sizeof($olddetailitem) > 0){
				foreach ($olddetailitem as $odi){
					$getldadetail = $db->fetch_one("SELECT * FROM logdeassembly WHERE logid='".$odi['dbid']."'");
					
					$db->query("UPDATE logdeassembly SET usedqty=usedqty-".$odi['quantity']." WHERE logid='".$odi['dbid']."'");
					
					$ldasp = $db->fetch_one("SELECT MAX(usedqty) AS maxqty FROM logdeassembly WHERE logparentid='".$getldadetail['logparentid']."'");
					$ldaspall = $db->fetch_all("SELECT * FROM logdeassembly WHERE logparentid='".$getldadetail['logparentid']."'");
					
					$scparent = $deassembly->getDeAssemblyParent($stockcode);
					$dbcountpbstock = $db->fetch_one("SELECT SUM(quantity) AS totalremaining FROM logdeassemblyparent WHERE stockcode='".$scparent['stockcode']."'");
					
					$olddetailitemdbuy = $this->getDetailSaleItem('detailbuy');
					if ($ldasp['maxqty'] > 0){
						$db->query("UPDATE logdeassembly SET quantity='".$ldasp['maxqty']."' WHERE logparentid='".$getldadetail['logparentid']."'");
						$db->query("UPDATE logdeassemblyparent SET quantity='".$ldasp['maxqty']."' WHERE logid='".$getldadetail['logparentid']."'");
						
						if (sizeof($olddetailitemdbuy) > 0){
							$maxqtyw = $ldasp['maxqty'];
							$dbgetotherlog = $db->fetch_one("SELECT ds.* FROM logdeassembly ld INNER JOIN detailsaleitem ds ON ld.logid = ds.dbid WHERE ld.logparentid='".$getldadetail['logparentid']."' AND ds.tabledbid='logdeassembly' AND ld.stockcode <> '".$stockcode."' ORDER BY ld.logid LIMIT 1");
							foreach ($olddetailitemdbuy as $odidb){
								if ($odidb['dbid'] == -1){
									$db->query("UPDATE stock SET remaining=remaining-".($maxqtyw-$odidb['quantity'])." WHERE stockcode='".$scparent['stockcode']."'");
								}
								else{
									$db->query("UPDATE detailbuy SET usedqty=usedqty+".($maxqtyw-$odidb['quantity'])." WHERE dbid='".$odidb['dbid']."'");
								}
								
								//save new dsid
								if ($maxqtyw > 0){
									if ($maxqtyw > $odidb['quantity']){
										$db->query("UPDATE detailsaleitem SET dsid='".$dbgetotherlog['dsid']."' WHERE dsiid='".$odidb['dsiid']."'");
										$maxqtyw -= $odidb['quantity'];
									}
									else{
										$db->query("UPDATE detailsaleitem SET dsid='".$dbgetotherlog['dsid']."', quantity='".$maxqtyw."' WHERE dsiid='".$odidb['dsiid']."'");
										$maxqtyw = 0;
									}
								}
								else{
									$db->query("DELETE FROM detailsaleitem WHERE dsiid='".$odidb['dsiid']."'");
								}								
							}
						}
					}
					else{
						$db->query("DELETE FROM logdeassembly WHERE logparentid='".$getldadetail['logparentid']."'");
						$db->query("DELETE FROM logdeassemblyparent WHERE logid='".$getldadetail['logparentid']."'");
						
						if (sizeof($olddetailitemdbuy) > 0 && !in_array($odi['dsid'],$idalready)){
							foreach ($olddetailitemdbuy as $odidb){
								if ($odidb['dbid'] == -1){
									$db->query("UPDATE stock SET remaining=remaining+".$odidb['quantity']." WHERE stockcode='".$scparent['stockcode']."'");
								}
								else{
									$db->query("UPDATE detailbuy SET usedqty=usedqty-".$odidb['quantity']." WHERE dbid='".$odidb['dbid']."'");
								}
							}
							array_push($idalready,$odi['dsid']);
						}
					}
					
					if (sizeof($ldaspall) > 0){
						foreach ($ldaspall as $ldasp){
							$dbcountstock = $db->fetch_one("SELECT SUM(quantity-usedqty) AS totalremaining, SUM(quantity) AS totalstock FROM logdeassembly WHERE stockcode='".$ldasp['stockcode']."'");
							$db->query("UPDATE stock SET realremaining='".$dbcountstock['totalremaining']."', totalstock='".$dbcountstock['totalstock']."' WHERE stockcode='".$ldasp['stockcode']."'");
						}
					}
					
					$dbcountpastock = $db->fetch_one("SELECT SUM(quantity) AS totalremaining FROM logdeassemblyparent WHERE stockcode='".$scparent['stockcode']."'");
					$db->query("UPDATE stock SET realremaining=realremaining+".($dbcountpbstock['totalremaining']-$dbcountpastock['totalremaining'])." WHERE stockcode='".$scparent['stockcode']."'");
				}
				$db->query("DELETE FROM detailsaleitem WHERE dsid='".$this->dtid."'");
			}
		}
	}
	
	function canDeleteSale(){
		global $db;
		
		if (!empty($this->saleno)){
			$rowexist = 0;

			$dbchecksaleritem = $db->query("SELECT * FROM detailsaler dsr INNER JOIN detailsale ds ON dsr.dsid = ds.dsid INNER JOIN headersale hs ON ds.saleno = hs.saleno WHERE hs.saleno='".$this->saleno."'");
			$rowexist += @mysql_num_rows($dbchecksaleritem);

			if ($rowexist > 0){
				return false;
			}
			else{
				return true;
			}
		}
		return false;
	}
	
	function deleteSale(){
		global $db, $stock, $assembly, $deassembly;
		
		if (!empty($this->saleno)){
			$alldetail = $this->getDetailSale();
			if (sizeof($alldetail) > 0){
				foreach ($alldetail as $ad){
					$this->setDetailId($ad['dsid']);
					$stock->setId("");
					$stock->setCode($ad['stockcode']);
					$getfs = $stock->getFirstStock();
					if ($getfs['assembly'] == 1){
						$assembly->setCode($ad['stockcode']);
						$getac = $assembly->getAssemblyComponent();
						if (sizeof($getac) > 0){
							foreach ($getac as $gac){
								$stock->setCode($gac['stockcodecomponent']);
								$this->deleteDetailItem($gac['stockcodecomponent']);
								$stock->addStock($ad['quantity']*$gac['sccquantity']);
							}
						}
					}
					else if ($getfs['assembly'] == 2){
						$this->deleteDetailItemDeAssembly($ad['stockcode']);
					}
					else{
						$this->deleteDetailItem($ad['stockcode']);
						$stock->addStock($ad['quantity']);
					}
				}
				$db->query("DELETE FROM detailsale WHERE saleno='".$this->saleno."'");
			}
			$db->query("DELETE FROM headersale WHERE saleno='".$this->saleno."'");
		}
	}
	
	function searchSale($keyword,$field,$trtype,$page = -1){
		global $db, $general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		$sqls = array();
		/* if (isset($keyword)){ */
			if (empty($field)){
				$field = 's.saledate';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			if (!empty($trtype)){
				array_push($sqls,'trtype=\''.$trtype.'\'');
			}
			
			if ($field != 'saledate'){
				global $general;
				$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
				$enddatetoshow = strtotime("31-12-".$general['yearactiveend']);

				array_push($sqls,'s.saledate >= \''.$startdatetoshow.'\' AND s.saledate <= \''.$enddatetoshow.'\'');
			}
			
			switch ($field){
				case 'saledate' : 
					$strinarr = 's.saledate=\''.strtotime($keyword).'\'';
					$field = 's.saledate';
					break;
				case 'duedate' : 
					$strinarr = 's.duedate=\''.strtotime($keyword).'\'';
					$field = 's.saledate';
					break;
				case 'customername' : 
					$innerjoin = ' INNER JOIN customer c ON s.customercode = c.customercode';
					$strinarr = 'c.customername LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.saledate';
					break;
				case 'stockcode' : 
					$innerjoin = ' INNER JOIN detailsale d ON s.saleno = d.saleno';
					$strinarr = 'd.stockcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.saledate';
					$groupby = ' GROUP BY s.saleno';
					break;
				case 'partno' : 
					$innerjoin = ' INNER JOIN detailsale d ON s.saleno = d.saleno';
					$strinarr = 'd.partno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.saledate';
					$groupby = ' GROUP BY s.saleno';
					break;
				case 'stockname' : 
					$innerjoin = ' INNER JOIN detailsale d ON s.saleno = d.saleno';
					$strinarr = 'd.stockname LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.saledate';
					$groupby = ' GROUP BY s.saleno';
					break;
				case 'brandcode' : 
					$innerjoin = ' INNER JOIN detailsale d ON s.saleno = d.saleno';
					$strinarr = 'd.brandcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.saledate';
					$groupby = ' GROUP BY s.saleno';
					break;
				case 'typecode' : 
					$innerjoin = ' INNER JOIN detailsale d ON s.saleno = d.saleno';
					$strinarr = 'd.typecode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.saledate';
					$groupby = ' GROUP BY s.saleno';
					break;
				case 'status' :
					global $arrsale;
					$candb = false;
					$keynow = -1;
					foreach ($arrsale as $keys => $ast){
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
					$strinarr = 's.status = \''.$keynow.'\'';
					$field = 's.saledate';
					break;
				default : 
					$strinarr = $field.' LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.saledate';
					break;
			}
			array_push($sqls,$strinarr);
		/* } */
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_all("SELECT s.* FROM headersale s".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit);
		
		return $dbsale;
	}
	
	function getUnpaidSale($customercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'paid = 0');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'saledate >= '.$startdate);
			array_push($sqls,'saledate <= '.$enddate);
		}
		if (!empty($customercode)){
			array_push($sqls,'customercode = \''.$customercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_all("SELECT * FROM headersale".$sql." ORDER BY saledate");
		
		return $dbsale;
	}
	
	function getUnclaimSale($customercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'claims = 0 AND trtype = \'credit\'');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'saledate >= '.$startdate);
			array_push($sqls,'saledate <= '.$enddate);
		}
		if (!empty($customercode)){
			array_push($sqls,'customercode = \''.$customercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_all("SELECT * FROM headersale".$sql." ORDER BY saledate");
		
		return $dbsale;
	}
}
?>