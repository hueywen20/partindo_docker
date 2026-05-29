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
	
	function getDetailBuyRItem(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dbrid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuyr = $db->fetch_all("SELECT * FROM detailbuyritem".$sql." ORDER BY dbriid DESC");
		
		return $dbbuyr;
	}
	
	function getDetailIdFromItem($dbid){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'dbr.buyrid = \''.$this->id.'\'');
		}
		if (!empty($dbid)){
			array_push($sqls,'dbri.dbid = \''.$dbid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuyr = $db->fetch_one("SELECT dbr.* FROM detailbuyr dbr INNER JOIN detailbuyritem dbri ON dbr.dbrid = dbri.dbrid".$sql);
		
		return $dbbuyr['dbrid'];
	}
	
	function saveHeaderBuyR($buyrdate,$suppliercode,$supplieraddrid,$description,$totals,$disc,$tax,$totalbuyr,$userid){
		global $db,$supplier,$customer;
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
		
		$checkcostumsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$suppliercode."' ");
			if (!empty($checkcostumsup['customerid'])){
			$value = $checkcostumsup['debt']-$totalbuyr;
			
			
			
			if ( $value < 0 ){
			
			$supplier->setCode($suppliercode);
			$supplier->minDebt(0);
			$customer->setCode($suppliercode);
			$customer->addCredit(abs($value));
			
			}
			else{
			
			$supplier->setCode($suppliercode);
			$supplier->minDebt($totalbuyr);
			}
			
			}
			else{
			$supplier->setCode($suppliercode);
			$supplier->minDebt($totalbuyr);
			
			}
		
		
		
		return $lastid;
	}
	
	function saveDetailBuyR($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$buyrprice,$disc,$extdisc,$tax,$otherpays,$totalbuyrad,$description,$buyrdate,$totals,$buyrpricead,$realbuyrprice,$quantity,$unitquantity,$unitcode,$dbid){
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
			$inserts["extdisc"] = $extdisc;
			$inserts["tax"] = $tax;
			$inserts["otherpays"] = $otherpays;
			$inserts["buyrpricead"] = $buyrpricead;
			$inserts["totalbuyrad"] = $totalbuyrad;
			$inserts["realbuyrprice"] = $realbuyrprice;
			$inserts["description"] = $description;
			
			$lastdbrid = $db->insert("detailbuyr",$inserts);
			
			//insert to detail buy return item
			//$this->saveDetailRItem($lastdbrid,$stockcode,$quantity);
			$this->saveDetailRItem($lastdbrid,$dbid,$quantity);
			
			$stock->setCode($stockcode);
			$stock->minStock($quantity);
			
			return $lastdbrid;
		}
	}

	function updateHeaderBuyR($buyrdate,$suppliercode,$supplieraddrid,$description,$totals,$disc,$tax,$totalbuyr,$userid){
		global $db,$supplier,$customer;
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
		$supplier->setId("");
		$customer->setId("");
		
		if ($oldheader['suppliercode'] != $suppliercode){
			$checkcostumsupold = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$oldheader['suppliercode']."' ");
			
			if (!empty($checkcostumsupold['customerid'])){
			//$value = $checkcostumsupold['debt']-$oldheader['totalbuyr'];
			if ( $checkcostumsupold['debt'] <= 0 ){
			
			if ($oldheader['totalbuyr'] > $checkcostumsupold['credit']){
			
			if (!empty($oldheader['suppliercode'])){
			$db->query("UPDATE customer SET credit=0 WHERE customercode='".$oldheader['suppliercode']."'");
			}
			$supplier->setCode($oldheader['suppliercode']);
			$supplier->addDebt(abs($oldheader['totalbuyr']-$checkcostumsupold['credit']));
			
			}
			else{
			$different = $checkcostumsupold['credit'] - $oldheader['totalbuyr'];
			if (!empty($oldheader['suppliercode'])){
				$db->query("UPDATE customer SET credit=credit-".$oldheader['totalbuyr']." WHERE customercode='".$oldheader['suppliercode']."'");
			}		
			}
			
			// tambahkan ke debt kija ada sisa pengurangan
			
			}
			else{
			$supplier->setCode($oldheader['suppliercode']);
			$supplier->addDebt($oldheader['totalbuyr']);
			}
			
			}
			
			else if (empty($checkcostumsupold['customerid'])){
			$supplier->setCode($oldheader['suppliercode']);
			$supplier->addDebt($oldheader['totalbuyr']);
			}
			
			
			$checkcostumsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$suppliercode."' ");
			if (!empty($checkcostumsup['customerid'])){
			$values = $checkcostumsup['debt']-$totalbuyr;
			
			
			
			if ( $values < 0 ){
			
			$supplier->setCode($suppliercode);
			$supplier->minDebt(0);
			$customer->setCode($suppliercode);
			$customer->addCredit(abs($values));
			
			}
			else{
			
			$supplier->setCode($suppliercode);
			$supplier->minDebt($totalbuyr);
			}
			
			}
			else if (empty($checkcostumsup['customerid'])){
			$supplier->setCode($suppliercode);
			$supplier->minDebt($totalbuyr);
			
			}
			
		}
		else if ($oldheader['totalbuyr'] != $totalbuyr){
		
			$checkcostumsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$suppliercode."' ");
			
			if (!empty($checkcostumsup['customerid'])){
			$newtotalbuyr = $oldheader['totalbuyr']-($oldheader['totalbuyr']-$totalbuyr);
			$value = $checkcostumsup['debt']- $totalbuyr;
			
			
			if ( $value < 0 ){
			
			$customer->setCode($suppliercode);
			$customer->addCredit($totalbuyr-$oldheader['totalbuyr']);
			
			}
			else{
			
			$supplier->setCode($suppliercode);
			$supplier->addDebt($oldheader['totalbuyr']-$totalbuyr);
			}
			
			}
			else{
			$supplier->setCode($suppliercode);
			$supplier->addDebt($oldheader['totalbuyr']-$totalbuyr);
			
			}
		
			
		}
	}
	
	function updateDetailBuyR($stockcode,$partno,$stockname,$brandcode,$typecode,$quantityf,$unitquantityf,$buyrprice,$disc,$extdisc,$tax,$otherpays,$totalbuyrad,$description,$buyrdate,$totals,$buyrpricead,$realbuyrprice,$quantity,$unitquantity,$unitcode,$dbid,$olddetail){
		global $db,$stock;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($buyrdate)){
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
				$updates["buyrprice"] = $buyrprice;
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
			if (!empty($otherpays)){
				$updates["otherpays"] = $otherpays;
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
				$db->update("detailbuyr",$updates,"dbrid='".$this->dtid."'");
				if (($olddetail['stockcode'] != $stockcode && !empty($stockcode)) || ($olddetail['quantity'] != $quantity && !empty($quantity))){					
					$stock->setCode($olddetail['stockcode']);
					$this->deleteDetailRItem($olddetail['stockcode']);
					$stock->addStock($olddetail['quantity']);
					
					$stock->setCode($stockcode);
					$this->saveDetailRItem($this->dtid,$dbid,$quantity);
					$stock->minStock($quantity);
				}
				else{
					$stock->setCode($stockcode);
					$stock->addStock(0);
				}
			}
		}
	}
	
	/*function saveDetailRItem($dbrid,$stockcode,$quantity){
		global $db;
		
		if ($quantity > 0){
			$allcodedetail = $db->fetch_all("SELECT * FROM detailbuy WHERE stockcode='".$stockcode."' AND quantity > usedqty ORDER BY buydate,expdate DESC");
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
	}*/
	
	/*function deleteDetailRItem($stockcode){
		global $db;
		
		if (!empty($this->dtid)){
			$olddetailitem = $this->getDetailBuyRItem();
			if (sizeof($olddetailitem) > 0){
				foreach ($olddetailitem as $odi){
					if ($odi['dbid'] == -1){
						$db->query("UPDATE stock SET remaining=remaining+".$odi['quantity']." WHERE stockcode='".$stockcode."'");
					}
					else{
						$db->query("UPDATE detailbuy SET usedqty=usedqty-".$odi['quantity']." WHERE dbid='".$odi['dbid']."'");
					}
				}
				$db->query("DELETE FROM detailbuyritem WHERE dbrid='".$this->dtid."'");
			}
		}
	}*/
	
	function saveDetailRItem($dbrid,$dbid,$quantity){
		global $db;
		
		if ($quantity > 0){
			$inserts['dbrid'] = $dbrid;
			$inserts['dbid'] = $dbid;
			$inserts['quantity'] = $quantity;
			$db->insert("detailbuyritem",$inserts);
		
			$db->query("UPDATE detailbuy SET usedqty=usedqty+".$quantity." WHERE dbid='".$dbid."'");
		}
	}
	
	function deleteDetailRItem($stockcode){
		global $db;
		
		if (!empty($this->dtid)){
			$olddetailitem = $this->getDetailBuyRItem();
			if (sizeof($olddetailitem) > 0){
				foreach ($olddetailitem as $odi){
					if ($odi['dbid'] == -1){
						$db->query("UPDATE stock SET remaining=remaining+".$odi['quantity']." WHERE stockcode='".$stockcode."'");
					}
					else{
						$db->query("UPDATE detailbuy SET usedqty=usedqty-".$odi['quantity']." WHERE dbid='".$odi['dbid']."'");
					}
				}
				$db->query("DELETE FROM detailbuyritem WHERE dbrid='".$this->dtid."'");
			}
		}
	}
	
	function deleteBuyR(){
		global $db, $stock, $payment;
		
		if (!empty($this->id)){
			$alldetail = $this->getDetailBuyR();
			if (sizeof($alldetail) > 0){
				foreach ($alldetail as $ad){
					$stock->setCode($ad['stockcode']);
					
					$this->setDetailId($ad['dbrid']);
					$buyrid = $this->id;
					
					$this->setId($buyrid);
					$headerbuyr = $this->getHeaderBuyR();
					
					$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$headerbuyr['suppliercode']."' ");
			
					$checkdebt = $payment->getallHeaderPaymentByMonth($headerbuyr['buyrdate'],2,$dbcustsup['supplierid'],$headerbuyr['supplieraddrid'],$dbcustsup['customerid'],0);
					$payment->setId($checkdebt['hpid']);
					$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$ad['dbrid']."' AND types = 'returnby' ");
					
					if (!empty($getdetail['dpid'])){
					
					$payment->setDetailId($getdetail['dpid']);
					$payment->deleteDetailPayment();
					$payment->setId($checkdebt['hpid']);
					
					$oldestheader = $payment->getHeaderPayment();
					$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldestheader);
					$payment->setId($checkdebt['hpid']);
					
					
					$oldheader = $payment->getHeaderPayment();
					
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
					
					$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
				
					if ($grandtotals < $totalrepayment['totalrepay'] ){
					$remainingnow = $totalrepayment['totalrepay']-$grandtotals;
					}
					else{
					$remainingnow = 0;
					}
					
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
					
					$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
					if ($grandtotals < $totalrepayment['totalrepay'] ){
					$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
					}
					else{
					$remainingnowh = 0;
					}
					
					$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);
					
					
					
					$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
					}
					
					}
		
					
					$this->deleteDetailRItem($ad['stockcode']);
					
					$stock->addStock($ad['quantity']);
				}
				$db->query("DELETE FROM detailbuyr WHERE buyrid='".$this->id."'");
			}
			$db->query("DELETE FROM headerbuyr WHERE buyrid='".$this->id."'");
		}
	}
	
	function getUnpaidBuyR($suppliercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'paid = 0');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'buyrdate >= '.$startdate);
			array_push($sqls,'buyrdate <= '.$enddate);
		}
		if (!empty($suppliercode)){
			array_push($sqls,'suppliercode = \''.$suppliercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuy = $db->fetch_all("SELECT * FROM headerbuyr".$sql." ORDER BY buyrdate");
		
		return $dbbuy;
	}
	
	function searchBuyR($keyword,$field,$page = -1){
		global $db, $general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		$sqls = array();
		/* if (isset($keyword)){ */
			if (empty($field)){
				$field = 's.buyrdate';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			if ($field != 'buyrdate'){
				global $general;
				$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
				$enddatetoshow = strtotime("31-12-".$general['yearactiveend']);

				array_push($sqls,'s.buyrdate >= \''.$startdatetoshow.'\' AND s.buyrdate <= \''.$enddatetoshow.'\'');
			}

			switch ($field){
				case 'buyrdate' : 
					$strinarr = 's.buyrdate=\''.strtotime($keyword).'\'';
					$field = 's.buyrdate';
					break;
				case 'suppliername' : 
					$innerjoin = ' INNER JOIN supplier c ON s.suppliercode = c.suppliercode';
					$strinarr = 'c.suppliername LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					break;
				case 'stockcode' : 
					$innerjoin = ' INNER JOIN detailbuyr d ON s.buyrid = d.buyrid';
					$strinarr = 'd.stockcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					$groupby = ' GROUP BY s.buyrid';
					break;
				case 'partno' : 
					$innerjoin = ' INNER JOIN detailbuyr d ON s.buyrid = d.buyrid';
					$strinarr = 'd.partno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					$groupby = ' GROUP BY s.buyrid';
					break;
				case 'stockname' : 
					$innerjoin = ' INNER JOIN detailbuyr d ON s.buyrid = d.buyrid';
					$strinarr = 'd.stockname LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					$groupby = ' GROUP BY s.buyrid';
					break;
				case 'brandcode' : 
					$innerjoin = ' INNER JOIN detailbuyr d ON s.buyrid = d.buyrid';
					$strinarr = 'd.brandcode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					$groupby = ' GROUP BY s.buyrid';
					break;
				case 'typecode' : 
					$innerjoin = ' INNER JOIN detailbuyr d ON s.buyrid = d.buyrid';
					$strinarr = 'd.typecode LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					$groupby = ' GROUP BY s.buyrid';
					break;
				case 'orderno' : 
					$innerjoin = ' INNER JOIN detailbuyr d ON s.buyrid = d.buyrid INNER JOIN detailbuyritem dbri ON dbri.dbrid = d.dbrid INNER JOIN detailbuy dby ON dbri.dbid = dby.dbid INNER JOIN headerbuy hby ON dby.buyno = hby.buyno';
					$strinarr = 'hby.orderno LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					$groupby = ' GROUP BY s.buyrid';
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
					$field = 's.buyrid';
					break;
				default : 
					$strinarr = $field.' LIKE (\''.$db->clean($keyword).'%\')';
					$field = 's.buyrdate';
					break;
			}
			array_push($sqls,$strinarr);
		/* } */
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuyr = $db->fetch_all("SELECT s.* FROM headerbuyr s".$innerjoin.$sql.$groupby." ORDER BY ".$field.$addlimit);
		
		return $dbbuyr;
	}
	
	function getUnclaimBuyR($suppliercode,$startdate,$enddate){
		global $db;
				
		$sqls = array();
		array_push($sqls,'dbr.claims = 0');
		if (!empty($startdate) && !empty($enddate)){
			array_push($sqls,'dbr.buyrdate >= '.$startdate);
			array_push($sqls,'dbr.buyrdate <= '.$enddate);
		}
		if (!empty($suppliercode)){
			array_push($sqls,'hbr.suppliercode = \''.$suppliercode.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbuyr = $db->fetch_all("SELECT dbr.*, hbr.*, dbri.dbid FROM detailbuyritem dbri INNER JOIN detailbuyr dbr ON dbri.dbrid = dbr.dbrid INNER JOIN detailbuy db ON dbri.dbid = db.dbid INNER JOIN headerbuyr hbr ON dbr.buyrid = hbr.buyrid".$sql);
		
		return $dbbuyr;
	}
	
	function getBuyRFromBuy($buyno){
		global $db;
		
		$dbbuyr = array();
		if (!empty($buyno)){
			$dbbuyr = $db->fetch_all("SELECT dbr.*, hbr.*, dbr.disc AS detaildisc FROM detailbuyritem dbri INNER JOIN detailbuyr dbr ON dbri.dbrid = dbr.dbrid INNER JOIN detailbuy db ON dbri.dbid = db.dbid INNER JOIN headerbuyr hbr ON dbr.buyrid = hbr.buyrid WHERE db.buyno='".$buyno."'");
		}
		
		return $dbbuyr;
	}
}
?>