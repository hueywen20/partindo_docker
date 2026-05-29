<?php
class PayDebt{
	var $id;
	var $dtid;
	var $dtrid;
	
	function setId($id){
		global $db;
		$this->id = $db->clean($id);
	}
	
	function setDetailId($dtid){
		global $db;
		$this->dtid = $db->clean($dtid);
	}
	
	function setDetailRePayId($dtrid){
		global $db;
		if (stristr($dtrid,"r-")){
			$dtrid = str_replace("r-","",$dtrid);
		}
		$this->dtrid = $db->clean($dtrid);
	}
	
	function getListPayDebt($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpaydebt = $db->fetch_all("SELECT * FROM headerpaydebt".$sql." ORDER BY paymentdate");
		
		return $dbpaydebt;
	}
	
	function getHeaderPayDebt(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'hpid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpaydebt = $db->fetch_one("SELECT * FROM headerpaydebt".$sql);
		
		return $dbpaydebt;
	}
	
	function getDetailPayDebt($types = ''){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'dp.hpid = \''.$this->id.'\'');
		}
		if (!empty($types)){
			array_push($sqls,'dp.types = \''.$types.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpaydebt = $db->fetch_all("SELECT dp.* FROM detailpayment dp ".$sql." ORDER BY dp.dpid");
		
		return $dbpaydebt;
	}
	
	function getDetailRePayDebt(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'dp.hpid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpaydebt = $db->fetch_all("SELECT dp.* FROM detailrepaydebt dp ".$sql." ORDER BY dp.drpyid");
		
		return $dbpaydebt;
	}
	
	function getDetailPayDebtIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dpid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpaydebt = $db->fetch_one("SELECT * FROM detailpaydebt".$sql);
		
		return $dbpaydebt;
	}
	
	function getDetailRePayDebtIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtrid)){
			array_push($sqls,'drpyid = \''.$this->dtrid.'\'');
		}
		
		$dbpaydebt = array();
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
			$dbpaydebt = $db->fetch_one("SELECT * FROM detailrepaydebt".$sql);
		}
		
		return $dbpaydebt;
	}
	
	function getDetailPayDebtFromBuy($hbid,$types){
		global $db;
		if (!empty($hbid) && !empty($this->id) && !empty($types)){
			$dbpaydebt = $db->fetch_one("SELECT * FROM detailpaydebt WHERE hbid='".$hbid."' AND hpid='".$this->id."' AND types='".$types."'");
		}
		return $dbpaydebt;
	}
	
	function saveHeaderPayDebt($supplierid,$supplieraddrid,$customerid,$customeraddrid,$paymentdate,$cash,$transfer,$bank,$accname,$accnumber,$transfernotes,$cheque,$chequenotes,$chequedates,$chequeduedate,$giro,$gironotes,$girodates,$giroduedate,$remainingprevious,$remainingnow,$complete,$completedate,$startdate,$enddate,$description,$totalpayment,$grandtotals,$userid){
		global $db, $supplier;
		
		$inserts['supplierid'] = $supplierid;
		$inserts['supplieraddrid'] = $supplieraddrid;
		$inserts['customerid'] = $customerid;
		$inserts['customeraddrid'] = $customeraddrid;
		$inserts['paymentdate'] = $paymentdate;
		$inserts['startdate'] = $startdate;
		$inserts['enddate'] = $enddate;
		$inserts['description'] = $description;
		$inserts['totalpayment'] = $totalpayment;
		$inserts['cash'] = $cash;
		$inserts['transfer'] = $transfer;
		$inserts['bank'] = $bank;
		$inserts['accname'] = $accname;
		$inserts['accnumber'] = $accnumber;
		$inserts['transfernotes'] = $transfernotes;
		$inserts['cheque'] = $cheque;
		$inserts['chequenotes'] = $chequenotes;
		$inserts['chequedates'] = $chequedates;
		$inserts['chequeduedate'] = $chequeduedate;
		$inserts['giro'] = $giro;
		$inserts['gironotes'] = $gironotes;
		$inserts['girodates'] = $girodates;
		$inserts['giroduedate'] = $giroduedate;
		$inserts['remainingprevious'] = $remainingprevious;
		$inserts['remainingnow'] = $remainingnow;
		$inserts['grandtotals'] = $grandtotals;
		$inserts['status'] = 1;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$inserts['complete'] = $complete;
		if ($complete){
			if (empty($completedate)){
				$inserts['completedate'] = time();
			}
			else{
				$inserts['completedate'] = $completedate;
			}
		}
		$lastid = $db->insert("headerpaydebt",$inserts);
			
		/* if (!empty($remainingprevious)){
			$db->query("UPDATE supplier SET remainingdebt=remainingdebt-".$remainingprevious." WHERE supplierid='".$supplierid."'");
		}
		if (!empty($remainingnow)){
			$db->query("UPDATE supplier SET remainingdebt=remainingdebt+".$remainingnow." WHERE supplierid='".$supplierid."'");
		} */
		
		/* if ($complete){
			$supplier->setId($supplierid);
			$supplier->minDebt($totalpayment);
		} */
		
		return $lastid;
	}
	
	function updateDebtCredit($csid,$totalforbuy,$totalforsale,$complete){
		global $db, $supplier, $customer;
		
		$updates = array();
		if ($totalforbuy > 0){
			if ($complete){
				$supplier->setCode($csid);
				$supplier->minDebt($totalforbuy);
			}
			$updates['totalforbuy'] = $totalforbuy;
		}
		if ($totalforsale > 0){
			if ($complete){
				$customer->setCode($csid);
				$customer->minCredit($totalforsale);
			}
			$updates['totalforsale'] = $totalforsale;
		}
		
		if (sizeof($updates) > 0 && !empty($this->id)){
			$db->update("headerpaydebt",$updates,"hpid='".$this->id."'");
		}
	}
	
	function saveDetailPayDebt($hbid,$pays,$paymentdate,$description,$types,$complete,$completedate){
		global $db;
		if (!empty($this->id)){
			$inserts['hpid'] = $this->id;
			$inserts['hbid'] = $hbid;
			$inserts['pays'] = $pays;
			$inserts['description'] = $description;
			$inserts['types'] = $types;
			$db->insert("detailpaydebt",$inserts);
			
			$updatepaid = '';
			if ($complete){
				if (empty($completedate)){
					$updatepaid = ', paid=1, paydate='.time();
				}
				else{
					$updatepaid = ', paid=1, paydate='.$completedate;
				}
			}
			
			if ($types == 'returnsl'){
				$db->query("UPDATE detailsaler SET claims=1".$updatepaid." WHERE dsrid='".$hbid."'");
			}
			else if ($types == 'sale'){
				$db->query("UPDATE headersale SET claims=1".$updatepaid." WHERE saleid='".$hbid."'");
			}
			else if ($types == 'return'){
				$db->query("UPDATE detailbuyr SET claims=1".$updatepaid." WHERE dbrid='".$hbid."'");
			}
			else{
				$db->query("UPDATE headerbuy SET claims=1".$updatepaid." WHERE buyid='".$hbid."'");
			}
		}
	}
	
	function saveDetailRePayDebt($types,$bank,$accname,$accnumber,$dates,$duedates,$totals,$notes,$complete){
		global $db;
		if (!empty($this->id)){
			$inserts['hpid'] = $this->id;
			$inserts['types'] = $types;
			$inserts['bank'] = $bank;
			$inserts['accname'] = $accname;
			$inserts['accnumber'] = $accnumber;
			$inserts['dates'] = $dates;
			$inserts['duedates'] = $duedates;
			$inserts['totals'] = $totals;
			$inserts['notes'] = $notes;
			if ($types == 1 || $types == 2){
				$inserts['status'] = 1;
			}
			else{
				$inserts['status'] = $complete;
			}
			$db->insert("detailrepaydebt",$inserts);
		}
	}
	
	function updateHeaderPayDebt($supplierid,$supplieraddrid,$paymentdate,$cash,$transfer,$bank,$accname,$accnumber,$transfernotes,$cheque,$chequenotes,$chequedates,$chequeduedate,$giro,$gironotes,$girodates,$giroduedate,$remainingprevious,$remainingnow,$complete,$completedate,$startdate,$enddate,$description,$totalpayment,$grandtotals,$oldheader,$userid){
		global $db,$supplier;
		
		$updates['supplierid'] = $supplierid;
		$updates['supplieraddrid'] = $supplieraddrid;
		$updates['paymentdate'] = $paymentdate;
		$updates['startdate'] = $startdate;
		$updates['enddate'] = $enddate;
		$updates['description'] = $description;
		$updates['totalpayment'] = $totalpayment;
		$updates['cash'] = $cash;
		$updates['transfer'] = $transfer;
		$updates['bank'] = $bank;
		$updates['accname'] = $accname;
		$updates['accnumber'] = $accnumber;
		$updates['transfernotes'] = $transfernotes;
		$updates['cheque'] = $cheque;
		$updates['chequenotes'] = $chequenotes;
		$updates['chequedates'] = $chequedates;
		$updates['chequeduedate'] = $chequeduedate;
		$updates['giro'] = $giro;
		$updates['gironotes'] = $gironotes;
		$updates['girodates'] = $girodates;
		$updates['giroduedate'] = $giroduedate;
		$updates['remainingprevious'] = $remainingprevious;
		$updates['remainingnow'] = $remainingnow;
		$updates['grandtotals'] = $grandtotals;
		$updates['complete'] = $complete;
		if ($complete){
			if (empty($completedate)){
				$updates['completedate'] = time();
			}
			else{
				$updates['completedate'] = $completedate;
			}
		}
		else{
			$updates['completedate'] = 0;
		}
		$updates['lastedited'] = time();
		$updates['lasteditedby'] = $userid;
		
		$db->update("headerpaydebt",$updates,"hpid='".$this->id."'");
		
		if ($oldheader['supplierid'] != $supplierid){
			$db->query("UPDATE supplier SET remainingdebt=remainingdebt+".$oldheader['remainingprevious']." WHERE supplierid='".$oldheader['supplierid']."'");
			$db->query("UPDATE supplier SET remainingdebt=remainingdebt-".$oldheader['remainingnow']." WHERE supplierid='".$oldheader['supplierid']."'");
			
			$db->query("UPDATE supplier SET remainingdebt=remainingdebt-".$remainingprevious." WHERE supplierid='".$supplierid."'");
			$db->query("UPDATE supplier SET remainingdebt=remainingdebt+".$remainingnow." WHERE supplierid='".$supplierid."'");
		}
		else{
			if ($oldheader['remainingprevious'] != $remainingprevious){
				$db->query("UPDATE supplier SET remainingdebt=remainingdebt+".($oldheader['remainingprevious']-$remainingprevious)." WHERE supplierid='".$supplierid."'");
			}
			if ($oldheader['remainingnow'] != $remainingnow){
				$db->query("UPDATE supplier SET remainingdebt=remainingdebt+".($remainingnow-$oldheader['remainingnow'])." WHERE supplierid='".$supplierid."'");
			}
		}
		
		/* if ($complete){
			if ($oldheader['supplierid'] != $supplierid){
				$supplier->setId($oldheader['supplierid']);
				$supplier->addDebt($oldheader['totalpayment']);
				
				$supplier->setId($supplierid);
				$supplier->minDebt($totalpayment);
			}
			else if ($oldheader['totalpayment'] != $totalpayment){
				$supplier->setId($supplierid);
				$supplier->addDebt($oldheader['totalpayment']-$totalpayment);
			}
			else if ($oldheader['complete'] == 0){
				$supplier->setId($oldheader['supplierid']);
				$supplier->minDebt($totalpayment);
			}
		}
		else{
			if ($oldheader['complete'] == 1){
				$supplier->setId($oldheader['supplierid']);
				$supplier->addDebt($oldheader['totalpayment']);
			}
		} */
	}
	
	function updateDebtCreditEdit($supplierid,$suppliercode,$complete,$oldheader){
		global $db, $customer, $supplier;
		
		$alldetail = $this->getDetailPayDebt();
		if (sizeof($alldetail) > 0){
			$totalforbuy = 0;
			$totalforsale = 0;
			foreach ($alldetail as $ad){
				if ($ad['types'] == 'sale'){
					$totalforsale += $ad['pays'];
				}
				else if ($ad['types'] == 'returnsl'){
					$totalforsale -= $ad['pays'];
				}
				else if ($ad['types'] == 'buy'){
					$totalforbuy += $ad['pays'];
				}
				else if ($ad['types'] == 'return'){
					$totalforbuy -= $ad['pays'];
				}
			}
		}
		
		$db->query("UPDATE headerpaydebt SET totalforbuy='".$totalforbuy."', totalforsale='".$totalforsale."' WHERE hpid = '".$this->id."'");
		
		if ($complete){			
			if ($oldheader['complete'] == 0){
				$supplier->setId($oldheader['supplierid']);
				$getscode = $supplier->getsupplierDetail();
				$supplier->minDebt($totalforbuy);
				
				$customer->setCode($getscode['suppliercode']);
				$customer->minCredit($totalforsale);
			}
			else{
				if ($oldheader['supplierid'] != $supplierid){
					$supplier->setId($oldheader['supplierid']);
					$getscode = $supplier->getsupplierDetail();
					$supplier->addDebt($oldheader['totalforbuy']);
					
					$supplier->setId("");
					$supplier->setCode($suppliercode);
					$supplier->minDebt($totalforbuy);
					
					$customer->setCode($getscode['suppliercode']);
					$customer->addCredit($oldheader['totalforsale']);
					
					$customer->setCode($suppliercode);
					$customer->minCredit($totalforsale);
				}
				else if ($oldheader['totalforsale'] != $totalforsale){
					$customer->setCode($suppliercode);
					$customer->addCredit($oldheader['totalforsale']-$totalforsale);
				}
				else if ($oldheader['totalforbuy'] != $totalforbuy){
					$supplier->setCode($suppliercode);
					$supplier->addDebt($oldheader['totalforbuy']-$totalforbuy);
				}
			}
		}
		else{
			if ($oldheader['complete'] == 1){
				$supplier->setId($oldheader['supplierid']);
				$getscode = $supplier->getsupplierDetail();
				$supplier->addDebt($oldheader['totalforbuy']);
				
				$customer->setCode($getscode['suppliercode']);
				$customer->addCredit($oldheader['totalforsale']);
			}
		}
	}
	
	function updateDetailPayDebt($hbid,$pays,$paymentdate,$description,$types,$complete,$completedate,$olddetail){
		global $db,$stock;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($hbid)){
				$updates["hbid"] = $hbid;
			}
			if (!empty($pays)){
				$updates["pays"] = $pays;
			}
			if (!empty($description)){
				$updates["description"] = $description;
			}
			if (!empty($types)){
				$updates["types"] = $types;
			}
			$updatepaid = '';
			if ($complete){
				$updatepaid = ', paid=1';
			}
			else{
				$updatepaid = ', paid=0, paydate=0';
			}
			
			if (empty($completedate)){
				$completedate = time();
			}
			
			if (sizeof($updates) > 0){
				$db->update("detailpaydebt",$updates,"dpid='".$this->dtid."'");
				if ($olddetail['hbid'] != $hbid){
					if ($olddetail['types'] == 'returnsl'){
						$db->query("UPDATE detailsaler SET claims=0, paid=0, paydate=0 WHERE dsrid='".$olddetail['hbid']."'");
					}
					else if ($olddetail['types'] == 'sale'){
						$db->query("UPDATE headersale SET claims=0, paid=0, paydate=0 WHERE saleid='".$olddetail['hbid']."'");
					}
					else if ($olddetail['types'] == 'return'){
						$db->query("UPDATE detailbuyr SET claims=0, paid=0, paydate=0 WHERE dbrid='".$olddetail['hbid']."'");
					}
					else if ($olddetail['types'] == 'sale'){
						$db->query("UPDATE headerbuy SET claims=0, paid=0, paydate=0 WHERE buyid='".$olddetail['hbid']."'");
					}
					
					if ($types == 'returnsl'){
						$db->query("UPDATE detailsaler SET paydate=".$completedate." WHERE dsrid='".$hbid."'");
						$db->query("UPDATE detailsaler SET claims=1".$updatepaid." WHERE dsrid='".$hbid."'");
					}
					else if ($types == 'sale'){
						$db->query("UPDATE headersale SET paydate=".$completedate." WHERE saleid='".$hbid."'");
						$db->query("UPDATE headersale SET claims=1".$updatepaid." WHERE saleid='".$hbid."'");
					}
					else if ($types == 'return'){
						$db->query("UPDATE detailbuyr SET paydate=".$completedate." WHERE dbrid='".$hbid."'");
						$db->query("UPDATE detailbuyr SET claims=1".$updatepaid." WHERE dbrid='".$hbid."'");
					}
					else if ($types == 'buy'){
						$db->query("UPDATE headerbuy SET paydate=".$completedate." WHERE buyid='".$hbid."'");
						$db->query("UPDATE headerbuy SET claims=1".$updatepaid." WHERE buyid='".$hbid."'");
					}
				}
				else{
					if ($types == 'returnsl'){
						$db->query("UPDATE detailsaler SET paydate=".$completedate." WHERE dsrid='".$hbid."'");
						$db->query("UPDATE detailsaler SET claims=1".$updatepaid." WHERE dsrid='".$hbid."'");
					}
					else if ($types == 'sale'){
						$db->query("UPDATE headersale SET paydate=".$completedate." WHERE saleid='".$hbid."'");
						$db->query("UPDATE headersale SET claims=1".$updatepaid." WHERE saleid='".$hbid."'");
					}
					else if ($types == 'return'){
						$db->query("UPDATE detailbuyr SET paydate=".$completedate." WHERE dbrid='".$hbid."'");
						$db->query("UPDATE detailbuyr SET claims=1".$updatepaid." WHERE dbrid='".$hbid."'");
					}
					else if ($types == 'buy'){
						$db->query("UPDATE headerbuy SET paydate=".$completedate." WHERE buyid='".$hbid."'");
						$db->query("UPDATE headerbuy SET claims=1".$updatepaid." WHERE buyid='".$hbid."'");
					}
				}
			}
		}
	}
	
	function updateDetailRePayDebt($types,$bank,$accname,$accnumber,$dates,$duedates,$totals,$notes,$complete){
		global $db;
		if (!empty($this->id) && !empty($this->dtrid)){
			$olddetail = $this->getDetailRePayDebtIndv();
			
			$updates['types'] = $types;
			$updates['bank'] = $bank;
			$updates['accname'] = $accname;
			$updates['accnumber'] = $accnumber;
			$updates['dates'] = $dates;
			$updates['duedates'] = $duedates;
			$updates['totals'] = $totals;
			$updates['notes'] = $notes;
			if ($types == 1 || $types == 2){
				$updates['status'] = 1;
			}
			else{
				$updates['status'] = $complete;
			}
			$db->update("detailrepaydebt",$updates,"drpyid='".$this->dtrid."'");
		}
	}
	
	function deleteDetailPayDebt(){
		global $db;
		
		if (!empty($this->dtid)){
			$getdetail = $this->getDetailPayDebtIndv();
			if ($getdetail['types'] == 'returnsl'){
				$db->query("UPDATE detailsaler SET claims=0, paid=0, paydate=0 WHERE dsrid='".$getdetail['hbid']."'");
			}
			else if ($getdetail['types'] == 'sale'){
				$db->query("UPDATE headersale SET claims=0, paid=0, paydate=0 WHERE saleid='".$getdetail['hbid']."'");
			}
			else if ($getdetail['types'] == 'return'){
				$db->query("UPDATE detailbuyr SET claims=0, paid=0, paydate=0 WHERE dbrid='".$getdetail['hbid']."'");
			}
			else if ($getdetail['types'] == 'buy'){
				$db->query("UPDATE headerbuy SET claims=0, paid=0, paydate=0 WHERE buyid='".$getdetail['hbid']."'");
			}
			$db->query("DELETE FROM detailpaydebt WHERE dpid='".$this->dtid."'");
		}
	}
	
	function deletePayDebt(){
		global $db, $supplier, $customer;
		
		if (!empty($this->id)){
			$oldheader = $this->getHeaderPayDebt();
			$alldetail = $this->getDetailPayDebt();
			if (sizeof($alldetail) > 0){
				$totalforbuy = 0;
				$totalforsale = 0;
				foreach ($alldetail as $ad){
					$this->dtid = $ad['dpid'];
					$this->deleteDetailPayDebt();
					if ($ad['types'] == 'sale'){
						$totalforsale += $ad['pays'];
					}
					else if ($ad['types'] == 'returnsl'){
						$totalforsale -= $ad['pays'];
					}
					else if ($ad['types'] == 'buy'){
						$totalforbuy += $ad['pays'];
					}
					else if ($ad['types'] == 'return'){
						$totalforbuy -= $ad['pays'];
					}
				}
			}
			
			if (!empty($oldheader['remainingprevious'])){
				$db->query("UPDATE supplier SET remainingdebt=remainingdebt+".$oldheader['remainingprevious']." WHERE supplierid='".$oldheader['supplierid']."'");
			}
			if (!empty($oldheader['remainingnow'])){
				$db->query("UPDATE supplier SET remainingdebt=remainingdebt-".$oldheader['remainingnow']." WHERE supplierid='".$oldheader['supplierid']."'");
			}
			
			$db->query("DELETE FROM detailrepaydebt WHERE hpid='".$this->id."'");
			$db->query("DELETE FROM headerpaydebt WHERE hpid='".$this->id."'");
			if ($oldheader['complete'] == 1){
				if ($totalforbuy > 0){
					$supplier->setId($oldheader['supplierid']);
					$supplier->addDebt($totalforbuy);
				}
				if ($totalforsale > 0){
					$supplier->setId($oldheader['supplierid']);
					$getscode = $supplier->getsupplierDetail('partial');
					
					$customer->setCode($getscode['suppliercode']);
					$customer->addCredit($totalforsale);
				}
			}
		}
	}
	
	function deleteDetailRePayDebt(){
		global $db;
		
		if (!empty($this->dtrid)){
			$db->query("DELETE FROM detailrepaydebt WHERE drpyid='".$this->dtrid."'");
		}
	}
	
	function searchPayDebt($keyword,$field,$startpaysdate,$endpaysdate){
		global $db;
		
		$sqls = array();
		if (isset($keyword)){
			$keyword = $db->clean($keyword);
			if (empty($field)){
				$field = 'hpid';
			}
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			if (empty($keyword)){
				$field = 'p.hpid';
			}
			
			switch ($field){
				case 'paymentdate' : 
					$strinarr = 'p.paymentdate=\''.strtotime($keyword).'\'';
					$field = 'p.hpid';
					break;
				case 'suppliername' : 
					$innerjoin = ' INNER JOIN supplier s ON p.supplierid = s.supplierid';
					$strinarr = 's.suppliername LIKE (\''.$keyword.'%\')';
					$field = 's.suppliername';
					break;
				case 'customername' : 
					$innerjoin = ' INNER JOIN customer s ON p.customerid = s.customerid';
					$strinarr = 's.customername LIKE (\''.$keyword.'%\')';
					$field = 's.customername';
					break;
				case 'orderno' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN headerbuy hb ON d.hsid = hb.buyid';
					$strinarr = 'hb.orderno LIKE (\''.$keyword.'%\') AND d.types=\'buy\'';
					$field = 'hb.orderno';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'buyrid' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN detailbuyr dbr ON d.hsid = dbr.dbrid';
					$strinarr = 'dbr.buyrid LIKE (\''.$keyword.'%\') AND d.types=\'return\'';
					$field = 'dbr.buyrid';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'saleno' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN headersale hs ON d.hsid = hs.saleid';
					$strinarr = 'hs.saleno LIKE (\''.$keyword.'%\') AND d.types=\'sale\'';
					$field = 'hs.saleno';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'salerid' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN detailsaler dsr ON d.hsid = dsr.dsrid';
					$strinarr = 'dsr.salerid LIKE (\''.$keyword.'%\') AND d.types=\'returnsl\'';
					$field = 'dsr.salerid';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'status' :
					global $arrpaydebt;
					$candb = false;
					$keynow = -1;
					foreach ($arrpaydebt as $keys => $ast){
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
					$field = 'p.hpid';
					break;
			}
			if (!empty($strinarr)){
				array_push($sqls,$strinarr);
			}
		}
		
		if (!empty($startpaysdate)){
			array_push($sqls,'p.startdate >= '.$startpaysdate);
		}
		
		if (!empty($endpaysdate)){
			array_push($sqls,'p.enddate <= '.$endpaysdate);
		}
		
		array_push($sqls,'p.status = 2');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpaydebt = $db->fetch_all("SELECT p.* FROM headerpayment p".$innerjoin.$sql.$groupby." ORDER BY ".$field);
		
		return $dbpaydebt;
	}
}
?>