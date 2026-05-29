<?php
class SaleR{
	var $id;
	var $dtid;
	var $saleno;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setSaleNo($saleno){
		$this->saleno = $saleno;
	}
	
	function setDetailId($dtid){
		$this->dtid = $dtid;
	}
	
	function getListSaleR($mode,$customercode='',$startdate=0,$enddate=0){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		if (!empty($customercode)){
			array_push($sqls,'customercode = \''.$customercode.'\'');
		}
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'salerdate >= '.$startdate);
			array_push($sqls,'salerdate <= '.$enddate);
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_all("SELECT * FROM headersaler".$sql." ORDER BY salerdate");
		
		return $dbsaler;
	}
	
	function getHeaderSaleR(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'salerid = \''.$this->id.'\'');
		}
		else if (!empty($this->saleno)){
			array_push($sqls,'saleno = \''.$this->saleno.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_one("SELECT * FROM headersaler".$sql);
		
		return $dbsaler;
	}
	
	function getDetailSaleR(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'salerid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_all("SELECT * FROM detailsaler".$sql." ORDER BY dsrid");
		
		return $dbsaler;
	}
	
	function getDetailSaleRIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dsrid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsale = $db->fetch_one("SELECT * FROM detailsaler".$sql);
		
		return $dbsale;
	}
	
	function getDetailSaleRItem(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dsrid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_all("SELECT * FROM detailsaleritem".$sql." ORDER BY dsriid DESC");
		
		return $dbsaler;
	}
	
	function getDetailIdFromItem($dsid){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'salerid = \''.$this->id.'\'');
		}
		if (!empty($dsid)){
			array_push($sqls,'dsid = \''.$dsid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_one("SELECT * FROM detailsaler".$sql);
		
		return $dbsaler['dsrid'];
	}
	
	function saveHeaderSaleR($salerdate,$customercode,$customeraddrid,$description,$totals,$disc,$tax,$totalsaler,$userid){
		global $db,$customer,$supplier;
		$inserts['saleno'] = $saleno;
		$inserts['saledate'] = $saledate;
		$inserts['salerdate'] = $salerdate;
		$inserts['customercode'] = $customercode;
		$inserts['customeraddrid'] = $customeraddrid;
		$inserts['description'] = $description;
		$inserts['totals'] = $totals;
		$inserts['disc'] = $disc;
		$inserts['tax'] = $tax;
		$inserts['totalsaler'] = $totalsaler;
		$inserts['status'] = 1;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$lastid = $db->insert("headersaler",$inserts);
		
		$supplier->setId("");
		$customer->setId("");
		
		$checkcostumsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$customercode."' ");
		
		if (!empty($checkcostumsup['supplierid'])){
		$value = $checkcostumsup['credit']-$totalsaler;	
		$suppliercode = $checkcostumsup['suppliercode'];
		if ( $value < 0 ){
			
		$supplier->setCode($suppliercode);
		$supplier->addDebt(abs($value));
		$customer->setCode($suppliercode);
		$customer->minCredit(0);
			
		}
		else{
			
		$customer->setCode($customercode);
		$customer->minCredit($totalsaler);
		}
			
		}
		else{
		$customer->setCode($customercode);
		$customer->minCredit($totalsaler);
		}
		
		return $lastid;
	}
	
	function saveDetailSaleR($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$salerprice,$disc,$extdisc,$tax,$totalsalerad,$description,$salerdate,$totals,$salerpricead,$realsalerprice,$quantity,$unitquantity,$unitcode,$dsid){
		global $db,$stock,$assembly;
		if (!empty($this->id)){
			$inserts['salerid'] = $this->id;
			$inserts['dsid'] = $dsid;
			$inserts["salerdate"] = $salerdate;
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
            $inserts["salerprice"] = $salerprice;
			$inserts["totals"] = $totals;
			$inserts["disc"] = $disc;
			$inserts["extdisc"] = $extdisc;
			$inserts["tax"] = $tax;
			$inserts["salerpricead"] = $salerpricead;
			$inserts["totalsalerad"] = $totalsalerad;
			$inserts["realsalerprice"] = $realsalerprice;
			$inserts["description"] = $description;
			
			$lastdsrid = $db->insert("detailsaler",$inserts);
			
			$stock->setId("");
			$stock->setCode($stockcode);
			$getfs = $stock->getFirstStock();
			
			if ($quantity > 0){
				$db->query("UPDATE detailsale SET returnsale=returnsale+".$quantity." WHERE dsid='".$dsid."'");				
			}
			
			if ($getfs['assembly'] == 1){
				$assembly->setCode($stockcode);
				$getac = $assembly->getAssemblyComponent();
				if (sizeof($getac) > 0){
					foreach ($getac as $gac){
						$stock->setCode($gac['stockcodecomponent']);
						$this->saveDetailRItem($lastdsrid,$dsid,$gac['stockcodecomponent'],$quantity*$gac['sccquantity'],$getfs['assembly']);
						$stock->addStock($quantity*$gac['sccquantity']);
					}
				}
			}
			else{
				//insert to detail sale return item
				$this->saveDetailRItem($lastdsrid,$dsid,$stockcode,$quantity,$getfs['assembly']);
				$stock->addStock($quantity);
			}
		return $lastdsrid;
		}
	}
	
	function updateHeaderSaleR($salerdate,$customercode,$customeraddrid,$description,$totals,$disc,$tax,$totalsaler,$userid){
		global $db,$customer,$supplier;
		//get old header sale return
		$oldheader = $this->getHeaderSaleR();
		
		$updates['saleno'] = $saleno;
		$updates['saledate'] = $saledate;
		$updates['salerdate'] = $salerdate;
		$updates['customercode'] = $customercode;
		$updates['customeraddrid'] = $customeraddrid;
		$updates['description'] = $description;
		$updates['totals'] = $totals;
		$updates['disc'] = $disc;
		$updates['tax'] = $tax;
		$updates['totalsaler'] = $totalsaler;
		$updates['status'] = 1;
		$updates['lastedited'] = time();
		$updates['lasteditedby'] = $userid;
		
		$db->update("headersaler",$updates,"salerid='".$this->id."'");
		$supplier->setId("");
		$customer->setId("");
		
		if ($oldheader['customercode'] != $customercode){
			$checkcostumsupold = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$oldheader['customercode']."' ");
			
			if (!empty($checkcostumsupold['supplierid'])){
			if ( $checkcostumsupold['credit'] <= 0 ){
			
			if ($oldheader['totalsaler'] > $checkcostumsupold['debt']){
			
			if (!empty($oldheader['customercode'])){
			//echo "UPDATE supplier SET debt=0 WHERE suppliercode='".$oldheader['customercode']."'";
			$db->query("UPDATE supplier SET debt=0 WHERE suppliercode='".$oldheader['customercode']."'");
			}
			$customer->setCode($oldheader['customercode']);
			$customer->addCredit(abs($oldheader['totalsaler']-$checkcostumsupold['debt']));
			}
			else{
			if (!empty($oldheader['customercode'])){
				$db->query("UPDATE supplier SET debt=debt-".$oldheader['totalsaler']." WHERE suppliercode='".$oldheader['customercode']."'");
			}
			}
			
			}
			
			else{
			
			$customer->setCode($oldheader['customercode']);
			$customer->addCredit($oldheader['totalsaler']);
			}
			
			}
			
			else if (empty($checkcostumsupold['supplierid'])){
			$customer->setCode($oldheader['customercode']);
			$customer->addCredit($oldheader['totalsaler']);
			
			}
			
			$checkcostumsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$customercode."' ");
			
			if (!empty($checkcostumsup['supplierid'])){
			$value = $checkcostumsup['credit']-$totalsaler;	
			//echo $checkcostumsup['credit'].' - '.$totalsaler;	
			$suppliercode = $checkcostumsup['suppliercode'];
			if ( $value < 0 ){
				
			$supplier->setCode($suppliercode);
			$supplier->addDebt(abs($value));
			$customer->setCode($suppliercode);
			$customer->minCredit(0);
				
			}
			else{
				
			$customer->setCode($customercode);
			$customer->minCredit($totalsaler);
			}
				
			}
			else{
			$customer->setCode($customercode);
			$customer->minCredit($totalsaler);
			}
			
			//$customer->setCode($oldheader['customercode']);
			//$customer->addCredit($oldheader['totalsaler']);
			
			//$customer->setCode($customercode);
			//$customer->minCredit($totalsaler);
		}
		else if ($oldheader['totalsaler'] != $totalsaler){
			
			$supplier->setId("");
			$customer->setId("");
			
			$checkcostumsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$customercode."' ");
			
			
			if (!empty($checkcostumsup['supplierid'])){
			$value = $checkcostumsup['credit']-$totalsaler;	
			$suppliercode = $checkcostumsup['suppliercode'];
			if ( $value < 0 ){
				
			$supplier->setCode($suppliercode);
			$supplier->addDebt($totalsaler-$oldheader['totalsaler']);
				
			}
			else{

			$customer->setCode($customercode);
			$customer->addCredit($oldheader['totalsaler']-$totalsaler);
			}
				
			}
			else{
			$customer->setCode($customercode);
			$customer->addCredit($oldheader['totalsaler']-$totalsaler);
			}
		
		}
	}
	
	function updateDetailSaleR($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$salerprice,$disc,$extdisc,$tax,$totalsalerad,$description,$salerdate,$totals,$salerpricead,$realsalerprice,$quantity,$unitquantity,$unitcode,$dsid,$olddetail){
		global $db,$stock,$assembly;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($dsid)){
				$updates["dsid"] = $dsid;
			}
			if (!empty($salerdate)){
				$updates["salerdate"] = $salerdate;
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
			if (!empty($salerprice)){
				$updates["salerprice"] = $salerprice;
			}
			if (!empty($totals)){
				$updates["totals"] = $totals;
			}
			if (!empty($disc)){
				$updates["disc"] = $disc;
			}
			if (!empty($extdisc)){
				$updates["extdisc"] = $extdisc;
			}
			if (!empty($tax)){
				$updates["tax"] = $tax;
			}
			if (!empty($salerpricead)){
				$updates["salerpricead"] = $salerpricead;
			}
			if (!empty($totalsalerad)){
				$updates["totalsalerad"] = $totalsalerad;
			}
			if (!empty($realsalerprice)){
				$updates["realsalerprice"] = $realsalerprice;
			}
			if (!empty($description)){
				$updates["description"] = $description;
			}

			if (sizeof($updates) > 0){
				$db->update("detailsaler",$updates,"dsrid='".$this->dtid."'");
				if (($olddetail['stockcode'] != $stockcode && !empty($stockcode)) || ($olddetail['quantity'] != $quantity && !empty($quantity))){					
					if ($quantity > 0){
						$db->query("UPDATE detailsale SET returnsale=returnsale-".$olddetail['quantity']." WHERE dsid='".$dsid."'");				
						$db->query("UPDATE detailsale SET returnsale=returnsale+".$quantity." WHERE dsid='".$dsid."'");				
					}
					
					$stock->setId("");
					$stock->setCode($olddetail['stockcode']);
					$getfs = $stock->getFirstStock();
					if ($getfs['assembly'] == 1){
						$assembly->setCode($olddetail['stockcode']);
						$getac = $assembly->getAssemblyComponent();
						if (sizeof($getac) > 0){
							foreach ($getac as $gac){
								$stock->setCode($gac['stockcodecomponent']);
								$this->deleteDetailRItem($gac['stockcodecomponent'],$dsid);
								$stock->minStock($olddetail['quantity']*$gac['sccquantity']);
							}
						}
						
						$assembly->setCode($stockcode);
						$getac = $assembly->getAssemblyComponent();
						if (sizeof($getac) > 0){
							foreach ($getac as $gac){
								$stock->setCode($gac['stockcodecomponent']);
								$this->saveDetailRItem($this->dtid,$dsid,$gac['stockcodecomponent'],$quantity*$gac['sccquantity'],$getfs['assembly']);
								$stock->addStock($quantity*$gac['sccquantity']);
							}
						}
					}
					else{
						$this->deleteDetailRItem($olddetail['stockcode'],$dsid);
						$stock->minStock($olddetail['quantity']);
						
						$stock->setCode($stockcode);
						$this->saveDetailRItem($this->dtid,$dsid,$stockcode,$quantity,$getfs['assembly']);
						$stock->addStock($quantity);
					}
				}
				else{
					$stock->setCode($stockcode);
					$stock->addStock(0);
				}
			}
		}
	}
	
	function saveDetailRItem($dsrid,$dsid,$stockcode,$quantity,$assembly){
		global $db;
		
		if ($quantity > 0){
			if ($assembly == 2){
				$allcodedetails = $db->fetch_all("SELECT dsi.* FROM detailsale ds INNER JOIN detailsaleitem dsi ON ds.dsid = dsi.dsid WHERE ds.dsid='".$dsid."' AND dsi.quantity > dsi.returnquantity AND tabledbid='logdeassembly' ORDER BY dsiid DESC");
				if (sizeof($allcodedetails) > 0){
					foreach ($allcodedetails as $acd){
						if ($quantity == 0){
							break;
						}
						$valueiu = 0;
						
						$requantity = $acd['quantity'] - $acd['returnquantity'];
						
						if ($quantity <= $requantity){
							$valueiu = $quantity;
							$quantity = 0;
						}
						else{
							$valueiu = $requantity;
							$quantity = $quantity-$requantity;
						}
						
						$inserts['dsrid'] = $dsrid;
						$inserts['dbid'] = $acd['dbid'];
						$inserts['quantity'] = $valueiu;
						$inserts['tabledbid'] = 'logdeassembly';
						$db->insert("detailsaleritem",$inserts);
					
						$db->query("UPDATE logdeassembly SET usedqty=usedqty-".$valueiu." WHERE logid='".$acd['dbid']."'");
						
						$db->query("UPDATE detailsaleitem SET returnquantity=returnquantity+".$valueiu." WHERE dsiid='".$acd['dsiid']."'");
					}
				}
			}
			else{
				if ($assembly == 1){
					$allcodedetails = $db->fetch_all("SELECT dsi.* FROM detailsaleitem dsi INNER JOIN detailsale ds ON ds.dsid = dsi.dsid INNER JOIN detailbuy db ON dsi.dbid = db.dbid WHERE ds.dsid='".$dsid."' AND db.stockcode='".$stockcode."' AND dsi.quantity > dsi.returnquantity ORDER BY dsiid DESC");
				}
				else{
					$allcodedetails = $db->fetch_all("SELECT dsi.* FROM detailsale ds INNER JOIN detailsaleitem dsi ON ds.dsid = dsi.dsid WHERE ds.dsid='".$dsid."' AND dsi.quantity > dsi.returnquantity ORDER BY dsiid DESC");
				}
				if (sizeof($allcodedetails) > 0){
					foreach ($allcodedetails as $acd){
						if ($quantity == 0){
							break;
						}
						$valueiu = 0;
						
						$requantity = $acd['quantity'] - $acd['returnquantity'];
						
						if ($quantity <= $requantity){
							$valueiu = $quantity;
							$quantity = 0;
						}
						else{
							$valueiu = $requantity;
							$quantity = $quantity-$requantity;
						}
						
						$inserts['dsrid'] = $dsrid;
						$inserts['dbid'] = $acd['dbid'];
						$inserts['quantity'] = $valueiu;
						$inserts['tabledbid'] = 'detailbuy';
						$db->insert("detailsaleritem",$inserts);
					
						if ($acd['dbid'] == -1){
							$db->query("UPDATE stock SET remaining=remaining+".$valueiu." WHERE stockcode='".$stockcode."'");
						}
						else{
							$db->query("UPDATE detailbuy SET usedqty=usedqty-".$valueiu." WHERE dbid='".$acd['dbid']."'");
						}
						
						$db->query("UPDATE detailsaleitem SET returnquantity=returnquantity+".$valueiu." WHERE dsiid='".$acd['dsiid']."'");
					}
				}
			}
		}
	}
	
	function saveDetailRItemAs($dsrid,$dsid,$stockcode,$quantity){
		global $db;
		
		if ($quantity > 0){
			$allcodedetails = $db->fetch_all("SELECT dsi.* FROM detailsaleitem dsi INNER JOIN detailsale ds ON ds.dsid = dsi.dsid INNER JOIN detailbuy db ON dsi.dbid = db.dbid WHERE ds.dsid='".$dsid."' AND db.stockcode='".$stockcode."' ORDER BY dsiid DESC");
			if (sizeof($allcodedetails) > 0){
				foreach ($allcodedetails as $acd){
					if ($quantity == 0){
						break;
					}
					$valueiu = 0;
					
					if ($quantity <= $acd['quantity']){
						$valueiu = $quantity;
						$quantity = 0;
					}
					else{
						$valueiu = $acd['quantity'];
						$quantity = $quantity-$acd['quantity'];
					}
					
					$inserts['dsrid'] = $dsrid;
					$inserts['dbid'] = $acd['dbid'];
					$inserts['quantity'] = $valueiu;
					$inserts['tabledbid'] = 'detailbuy';
					$db->insert("detailsaleritem",$inserts);
				
					$db->query("UPDATE detailbuy SET usedqty=usedqty-".$valueiu." WHERE dbid='".$acd['dbid']."'");
				}
			}
		}
	}
	
	function deleteDetailRItem($stockcode,$dsid){
		global $db;
		
		if (!empty($this->dtid)){
			$olddetailitem = $this->getDetailSaleRItem();
			if (sizeof($olddetailitem) > 0){
				foreach ($olddetailitem as $odi){
					if ($odi['dbid'] == -1){
						$db->query("UPDATE stock SET remaining=remaining-".$odi['quantity']." WHERE stockcode='".$stockcode."'");
					}
					else{
						if ($odi['tabledbid'] == 'logdeassembly'){
							$db->query("UPDATE logdeassembly SET usedqty=usedqty+".$odi['quantity']." WHERE logid='".$odi['dbid']."'");
						}
						else{
							$db->query("UPDATE detailbuy SET usedqty=usedqty+".$odi['quantity']." WHERE dbid='".$odi['dbid']."'");
						}
					}
					$db->query("UPDATE detailsaleitem SET returnquantity=returnquantity-".$odi['quantity']." WHERE dbid='".$odi['dbid']."' AND dsid='".$dsid."'");
				}
				$db->query("DELETE FROM detailsaleritem WHERE dsrid='".$this->dtid."'");
			}
		}
	}
	
	function deleteSaleR(){
		global $db, $stock, $assembly, $payment;
		
		if (!empty($this->id)){
			$alldetail = $this->getDetailSaleR();
			if (sizeof($alldetail) > 0){
				foreach ($alldetail as $ad){
					$this->setDetailId($ad['dsrid']);
					$stock->setId("");
					$stock->setCode($ad['stockcode']);
					$getfs = $stock->getFirstStock();
					
					$this->setId($this->id);
					$headersaler = $this->getHeaderSaleR();
					
					$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$headersaler['customercode']."' ");

					//hutang
					$checkdebt = $payment->getallHeaderPaymentByMonth($headersaler['salerdate'],2,$dbcustsup['supplierid'],$headersaler['supplieraddrid'],$dbcustsup['customerid'],$headersaler['customeraddrid']);

					$payment->setId($checkdebt['hpid']);

					$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$ad['dsrid']."' AND types = 'return' ");


					if (!empty($getdetail['dpid'])){
					
					$payment->setDetailId($getdetail['dpid']);
					$payment->deleteDetailPayment();
					$payment->setId($checkdebt['hpid']);

					$oldestheader = $payment->getHeaderPayment();
					$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldestheader);
					$payment->setId($checkdebt['hpid']);

					if ($oldheader['status'] == 1){

					$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
					$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);

					$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
					$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
					$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
					$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
					//$ttlfolpaym = $totalpaysale['totalpay'] - $oldheader['totalforbuy'];
					$grandtotals = 0;
					$totalpayment = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
					$remainingnow = $oldheader['remainingnow'];
					$remainingnowh = $oldheader['remainingnowh'];


					if (!empty($checknulfremainingprevious[0])){


					$grandtotals = $totalpayment ;
					$newvalue = $totalpayment-$oldheader['remainingprevious'];

					if ($newvalue < 0 ){
					$grandtotals = 0;
					$remainingnow = abs($newvalue);
					}
					else{
					$grandtotals = abs($newvalue);
					$remainingnow = 0;
					}


					}
					else{
					$grandtotals = $totalpayment+$oldheader['remainingprevioush'];

					}

					//$grandtotals = headerpayment['totalpayment']
					//$totalpayment = headerpayment['grandtotals']

					$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);



					}

					else{

					$payment->setId($checkdebt['hpid']);
					$oldheader = $payment->getHeaderPayment();

					$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
					$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
					$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
					$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
					$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
					$totalpayment = abs($ttlfolpaym);
					$remainingnow = $oldheader['remainingnow'];
					$remainingnowh = $oldheader['remainingnowh'];
					$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
					$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
					$grandtotals = 0;

					if (!empty($checknulfremainingprevious[0])){


					$grandtotals = $totalpayment ;
					$grandtotals = $totalpayment+$oldheader['remainingprevious'];


					}
					else{

					$newvalue = $totalpayment-$oldheader['remainingprevioush'];

					if ($newvalue < 0 ){
					$grandtotals = 0;
					$remainingnowh = abs($newvalue);
					}
					else{
					$grandtotals = abs($newvalue);
					$remainingnowh = 0;
					}

					}


					$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);



					$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
					}
					}
					
					
					if ($getfs['assembly'] == 1){
						$assembly->setCode($ad['stockcode']);
						$getac = $assembly->getAssemblyComponent();
						if (sizeof($getac) > 0){
							foreach ($getac as $gac){
								$stock->setCode($gac['stockcodecomponent']);
								$this->deleteDetailRItem($gac['stockcodecomponent'],$ad['dsid']);
								$stock->minStock($ad['quantity']*$gac['sccquantity']);
							}
						}
					}
					else{
						$this->deleteDetailRItem($ad['stockcode'],$ad['dsid']);
						$stock->minStock($ad['quantity']);
					}
					$db->query("UPDATE detailsale SET returnsale=returnsale-".$ad['quantity']." WHERE dsid='".$ad['dsid']."'");
				}
				$db->query("DELETE FROM detailsaler WHERE salerid='".$this->id."'");
			}
			$db->query("DELETE FROM headersaler WHERE salerid='".$this->id."'");
		}
	}
	
	function searchSaleR($keyword,$field,$page = -1){
		global $db, $general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		$sqls = array();
		/* if (isset($keyword)){ */
			if (empty($field)){
				$field = 's.salerdate';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			if ($field != 'salerdate'){
				global $general;
				$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
				$enddatetoshow = strtotime("31-12-".$general['yearactiveend']);

				array_push($sqls,'s.salerdate >= \''.$startdatetoshow.'\' AND s.salerdate <= \''.$enddatetoshow.'\'');
			}
			
			switch ($field){
				case 'salerdate' : 
					$strinarr = 's.salerdate=\''.strtotime($keyword).'\'';
					$field = 's.salerdate';
					break;
				case 'customername' : 
					$innerjoin = ' INNER JOIN customer c ON s.customercode = c.customercode';
					$strinarr = 'c.customername LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					break;
				case 'stockcode' : 
					$innerjoin = ' INNER JOIN detailsaler d ON s.salerid = d.salerid';
					$strinarr = 'd.stockcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					$groupby = ' GROUP BY s.salerid';
					break;
				case 'partno' : 
					$innerjoin = ' INNER JOIN detailsaler d ON s.salerid = d.salerid';
					$strinarr = 'd.partno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					$groupby = ' GROUP BY s.salerid';
					break;
				case 'stockname' : 
					$innerjoin = ' INNER JOIN detailsaler d ON s.salerid = d.salerid';
					$strinarr = 'd.stockname LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					$groupby = ' GROUP BY s.salerid';
					break;
				case 'brandcode' : 
					$innerjoin = ' INNER JOIN detailsaler d ON s.salerid = d.salerid';
					$strinarr = 'd.brandcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					$groupby = ' GROUP BY s.salerid';
					break;
				case 'typecode' : 
					$innerjoin = ' INNER JOIN detailsaler d ON s.salerid = d.salerid';
					$strinarr = 'd.typecode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					$groupby = ' GROUP BY s.salerid';
					break;
				case 'saleno' : 
					$innerjoin = ' INNER JOIN detailsaler d ON s.salerid = d.salerid INNER JOIN detailsale dsl ON d.dsid = dsl.dsid INNER JOIN headersale hsl ON dsl.saleno = hsl.saleno';
					$strinarr = 'hsl.saleno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					$groupby = ' GROUP BY s.salerid';
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
					$field = 's.salerid';
					break;
				default : 
					$strinarr = $field.' LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.salerdate';
					break;
			}
			array_push($sqls,$strinarr);
		/* } */
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_all("SELECT s.* FROM headersaler s".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit);
		
		return $dbsaler;
	}
	
	function getUnpaidSaleR($customercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'paid = 0');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'salerdate >= '.$startdate);
			array_push($sqls,'salerdate <= '.$enddate);
		}
		if (!empty($customercode)){
			array_push($sqls,'customercode = \''.$customercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_all("SELECT * FROM headersaler".$sql." ORDER BY salerdate");
		
		return $dbsaler;
	}
	
	function getUnclaimSaleR($customercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'dsr.claims = 0');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'dsr.salerdate >= '.$startdate);
			array_push($sqls,'dsr.salerdate <= '.$enddate);
		}
		if (!empty($customercode)){
			array_push($sqls,'hsr.customercode = \''.$customercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsaler = $db->fetch_all("SELECT dsr.*, hsr.* FROM detailsaler dsr INNER JOIN detailsale ds ON dsr.dsid = ds.dsid INNER JOIN headersaler hsr ON dsr.salerid = hsr.salerid".$sql);
		
		return $dbsaler;
	}
	
	function getSaleRFromSale($saleno){
		global $db;
		
		$dbsaler = array();
		if (!empty($saleno)){
			$dbsaler = $db->fetch_all("SELECT dsr.*, hsr.*, dsr.disc AS detaildisc FROM detailsaler dsr INNER JOIN detailsale ds ON dsr.dsid = ds.dsid INNER JOIN headersaler hsr ON dsr.salerid = hsr.salerid WHERE ds.saleno='".$saleno."'");
		}
		
		return $dbsaler;
	}
}
?>