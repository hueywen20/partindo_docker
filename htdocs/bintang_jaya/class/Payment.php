<?php
class Payment{
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
	
	function getListPayment($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpayment = $db->fetch_all("SELECT * FROM headerpayment".$sql." ORDER BY paymentdate");
		
		return $dbpayment;
	}
	
	function getHeaderPayment(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'hpid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpayment = $db->fetch_one("SELECT * FROM headerpayment".$sql);
		
		return $dbpayment;
	}
	
	function getHeaderPaymentByMonth($thedate,$status,$supplierid,$supplieraddrid,$customerid,$customeraddrid){
		global $db;
		
		$invstartdate = strtotime('01-'.date("m-Y",$thedate));
		$invenddate = strtotime(date('t-m-Y',$thedate));
		
		$dbpayment = $db->fetch_one("SELECT hp.* FROM headerpayment hp WHERE (startdate = '".$invstartdate."' OR enddate =  '".$invenddate."' ) AND hp.supplierid =  '".$supplierid."' AND hp.customerid =  '".$customerid."' AND complete = 0 ORDER BY hpid DESC  LIMIT 1 ");
		
		
		
		
		return $dbpayment;
	}
	
	function getallHeaderPaymentByMonth($thedate,$status,$supplierid,$supplieraddrid,$customerid,$customeraddrid){
		global $db;
		
		$invstartdate = strtotime('01-'.date("m-Y",$thedate));
		$invenddate = strtotime(date('t-m-Y',$thedate));
		
		$dbpayment = $db->fetch_one("SELECT hp.* FROM headerpayment hp WHERE (startdate = '".$invstartdate."' OR enddate =  '".$invenddate."' ) AND hp.supplierid =  '".$supplierid."' AND hp.customerid =  '".$customerid."'  ORDER BY hpid DESC  LIMIT 1 ");
		
		
		
		
		return $dbpayment;
	}
	
	function getSameHeaderPaymentByMonth($thedate,$status,$supplierid,$supplieraddrid,$customerid,$customeraddrid){
		global $db;
		
		$invstartdate = strtotime('01-'.date("m-Y",$thedate));
		$invenddate = strtotime(date('t-m-Y',$thedate));
		
		$dbpayment = $db->fetch_one("SELECT hp.* FROM headerpayment hp WHERE (startdate = '".$invstartdate."' OR enddate =  '".$invenddate."' ) AND hp.supplierid =  '".$supplierid."' AND hp.customerid =  '".$customerid."' ORDER BY hp.completedate DESC,hp.hpid DESC LIMIT 1");
		
		
		
		
		return $dbpayment;
	}
	
	function getDetailPaymentByMonth($thedate,$types,$hsid,$status,$supplierid,$supplieraddrid,$customerid,$customeraddrid){
		global $db;
		
		$invstartdate = strtotime('01-'.date("m-Y",$thedate));
		$invenddate = strtotime(date('t-m-Y',$thedate).' 23:59:59');
		
		$dbpayment = $db->fetch_one("SELECT dp.* FROM headerpayment hp INNER JOIN detailpayment dp ON hp.hpid = dp.hpid WHERE hp.startdate = '".$invstartdate."'  AND hp.enddate =  '".$invenddate."' AND hp.customerid =  '".$customerid."' AND hp.supplierid =  '".$supplierid."' AND dp.types='".$types."' AND dp.hsid='".$hsid."'");
		
		
		return $dbpayment;
	}
	
	function getDetailLastPaymentByMonth($thedate,$status,$supplierid,$supplieraddrid,$customerid,$customeraddrid){
		global $db;
		
		$invstartdate = strtotime('01-'.date("m-Y",$thedate));
		$invenddate = strtotime(date('t-m-Y',$thedate).' 23:59:59');
		
		$dbpayment = $db->fetch_one("SELECT * FROM headerpayment hp WHERE hp.startdate < '".$invstartdate."' AND hp.customerid =  '".$customerid."' AND hp.supplierid =  '".$supplierid."' ORDER BY hp.completedate DESC,hp.hpid DESC LIMIT 1   ");
		
	
		
		
		return $dbpayment;
	}
	
	function getDetailNextPaymentByMonth($thedate,$status,$supplierid,$supplieraddrid,$customerid,$customeraddrid){
		global $db;
		
		$invstartdate = strtotime('01-'.date("m-Y",$thedate));
		$invenddate = strtotime(date('t-m-Y',$thedate).' 23:59:59');
		
		$dbpayment = $db->fetch_one("SELECT * FROM headerpayment hp WHERE hp.startdate > '".$invstartdate."' AND hp.customerid =  '".$customerid."' AND hp.supplierid =  '".$supplierid."'  ORDER BY hp.startdate DESC LIMIT 1 ");
		
	
		
		
		return $dbpayment;
	}

	
	function getDetailPayment($types = ''){
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
		$dbpayment = $db->fetch_all("SELECT dp.* FROM detailpayment dp ".$sql." ORDER BY dp.dpid");
		
		return $dbpayment;
	}
	
	function getDetailRePayment(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'dp.hpid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpayment = $db->fetch_all("SELECT dp.* FROM detailrepayment dp ".$sql." ORDER BY dp.drpyid");
		
		return $dbpayment;
	}
	
	function getDetailPaymentIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dpid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpayment = $db->fetch_one("SELECT * FROM detailpayment".$sql);
		
		return $dbpayment;
	}
	
	function getDetailRePaymentIndv(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtrid)){
			array_push($sqls,'drpyid = \''.$this->dtrid.'\'');
		}
		
		$dbpayment = array();
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
			$dbpayment = $db->fetch_one("SELECT * FROM detailrepayment".$sql);
		}
		
		return $dbpayment;
	}
	
	function getDetailPaymentFromSale($hsid,$types){
		global $db;
		if (!empty($hsid) && !empty($this->id) && !empty($types)){
			$dbpayment = $db->fetch_one("SELECT * FROM detailpayment WHERE hsid='".$hsid."' AND hpid='".$this->id."' AND types='".$types."'");
		}
		return $dbpayment;
	}
	
	function saveHeaderPayment($supplierid,$supplieraddrid,$customerid,$customeraddrid,$paymentdate,$cash,$transfer,$bank,$accname,$accnumber,$transfernotes,$cheque,$chequenotes,$chequedates,$chequeduedate,$giro,$gironotes,$girodates,$giroduedate,$remainingprevious,$remainingnow,$complete,$completedate,$startdate,$enddate,$description,$totalpayment,$grandtotals,$userid,$status,$remainingprevioush,$remainingnowh,$flat = 0,$statusflat = '+'){
		global $db, $customer;
		
		$inserts['supplierid'] = $supplierid;
		$inserts['supplieraddrid'] = $supplieraddrid;
		$inserts['customerid'] = $customerid;
		$inserts['customeraddrid'] = $customeraddrid;
		$inserts['paymentdate'] = $paymentdate;
		$inserts['startdate'] = $startdate;
		$inserts['enddate'] = $enddate;
		$inserts['description'] = $description;
		$inserts['totalpayment'] = $totalpayment;
		$inserts['flat'] = $flat;
		$inserts['statusflat'] = $statusflat;
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
		$inserts['remainingprevioush'] = $remainingprevioush;
		$inserts['remainingnowh'] = $remainingnowh;
		$inserts['grandtotals'] = $grandtotals;
		$inserts['status'] = $status;
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
		$lastid = $db->insert("headerpayment",$inserts);
		

		if ($status == 1){
		$remainingprevious2 = $remainingprevious;
		$remainingnow2  = $remainingnow;
		}
		else{
		$remainingprevious2 = $remainingprevioush;
		$remainingnow2  = $remainingnowh;
		}
			
		
		if (!empty($remainingprevious2)){
			$db->query("UPDATE customer SET remainingcredit=remainingcredit-".$remainingprevious2." WHERE customerid='".$customerid."'");
		}
		if (!empty($remainingnow2)){
			$db->query("UPDATE customer SET remainingcredit=remainingcredit+".$remainingnow2." WHERE customerid='".$customerid."'");
		}
		
		return $lastid;
	}
	
	function updateDebtCredit($csid,$totalforbuy,$totalforsale,$complete){
		global $db, $supplier, $customer;
		
		$updates = array();
		if ($totalforbuy > 0){
			/* if ($complete){
				$supplier->setCode($csid);
				$supplier->minDebt($totalforbuy);
			} */
			$updates['totalforbuy'] = $totalforbuy;
		}
		if ($totalforsale > 0){
			/* if ($complete){
				$customer->setCode($csid);
				$customer->minCredit($totalforsale);
			} */
			$updates['totalforsale'] = $totalforsale;
		}
		
		$supplier->setId("");
		$customer->setId("");

		$customer->setCode($csid);
		$getscode = $customer->getcustomerDetail();
		$totalpayment = $totalforsale-$totalforbuy;
		
		
		if ($totalpayment > 0){
		$checkcostumsu = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$csid."' ");
		if ($complete){
		if (!empty($checkcostumsu['supplierid'])){
		
		
		if ($getscode['credit'] < $totalpayment){
		
		$db->query("UPDATE customer SET credit=0 WHERE customercode='".$csid."'");

		$supplier->setCode($getscode['customercode']);
		$supplier->addDebt($totalpayment-$getscode['credit']);

		}
		else{
		$customer->minCredit($totalpayment);
		}
		}	
		else{
		$customer->minCredit($totalpayment);
		}
		}
		}
		
		else{
		if ($complete){
		$checkcostumsu = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$csid."' ");
	
		if (!empty($checkcostumsu['supplierid'])){
		
		
		if ($checkcostumsu['debt'] < $totalpayment){
		
		$db->query("UPDATE supplier SET debt=0 WHERE suppliercode='".$csid."'");

		$customer->setCode($getscode['customercode']);
		$customer->minCredit($totalpayment-$checkcostumsu['debt']);

		}
		else{
		$supplier->addDebt($totalpayment);
		}
		}	
		else{
		$supplier->addDebt($totalpayment);
		}
		}
		
		}
		
		
		
		if ($totalforbuy > $totalforsale)
		$updates['status']  = 2;
		else
		$updates['status']  = 1;
		
		
		if (sizeof($updates) > 0 && !empty($this->id)){
			$db->update("headerpayment",$updates,"hpid='".$this->id."'");
		}
	}
	
	function saveDetailPayment($hsid,$pays,$paymentdate,$description,$types,$complete,$completedate){
		global $db;
		if (!empty($this->id)){
			$inserts['hpid'] = $this->id;
			$inserts['hsid'] = $hsid;
			$inserts['pays'] = $pays;
			$inserts['description'] = $description;
			$inserts['types'] = $types;
			$db->insert("detailpayment",$inserts);
			
			$updatepaid = '';
			if ($complete){
				if (empty($completedate)){
					$updatepaid = ', paid=1, paydate='.time();
				}
				else{
					$updatepaid = ', paid=1, paydate='.$completedate;
				}
			}
			
			if ($types == 'return'){
				$db->query("UPDATE detailsaler SET claims=1".$updatepaid." WHERE dsrid='".$hsid."'");
			}
			else if ($types == 'sale'){
				$db->query("UPDATE headersale SET claims=1".$updatepaid." WHERE saleid='".$hsid."'");
			}
			else if ($types == 'returnby'){
				$db->query("UPDATE detailbuyr SET claims=1".$updatepaid." WHERE dbrid='".$hsid."'");
			}
			else if ($types == 'buy'){
				$db->query("UPDATE headerbuy SET claims=1".$updatepaid." WHERE buyid='".$hsid."'");
			}
		}
	}
	
	function saveDetailRePayment($types,$bank,$accname,$accnumber,$dates,$duedates,$totals,$notes,$complete){
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
			$db->insert("detailrepayment",$inserts);
		}
	}
	
	
	function updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh){
		global $db,$customer;
		

		$updates['totalpayment'] = $totalpayment;
		$updates['grandtotals'] = $grandtotals;
		$updates['remainingnow'] = $remainingnow;
		$updates['remainingnowh'] = $remainingnowh;
		$updates['lastedited'] = time();
		$updates['lasteditedby'] = $userid;
		
		
		
		$db->update("headerpayment",$updates,"hpid='".$this->id."'");
		
	}
	
	function updateHeaderPayment($customerid,$customeraddrid,$paymentdate,$cash,$transfer,$bank,$accname,$accnumber,$transfernotes,$cheque,$chequenotes,$chequedates,$chequeduedate,$giro,$gironotes,$girodates,$giroduedate,$remainingprevious,$remainingnow,$complete,$completedate,$startdate,$enddate,$description,$totalpayment,$grandtotals,$oldheader,$userid,$remainingprevioush,$flat = 0,$statusflat = ''){
		global $db,$customer;
		
		$updates['customerid'] = $customerid;
		$updates['customeraddrid'] = $customeraddrid;
		$updates['paymentdate'] = $paymentdate;
		$updates['startdate'] = $startdate;
		$updates['enddate'] = $enddate;
		$updates['description'] = $description;
		$updates['totalpayment'] = $totalpayment;
		if (!empty($flat)){
			$updates['flat'] = $flat;
		}
		if (!empty($statusflat)){
			$updates['statusflat'] = $statusflat;
		}
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
		$updates['remainingprevioush'] = $remainingprevioush;
		
		//piutang
		if ($oldheader['status'] == 1){
		$updates['remainingnow'] = $remainingnow;
		$oldheader['remainingnow'] = $oldheader['remainingnow'];
		}
		else{
		$updates['remainingnowh'] = $remainingnow;
		$oldheader['remainingnow'] = $oldheader['remainingnowh'];
		}
		
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
		
		
		/* if ($oldheader['complete'] == "1" && $complete == "1"){
			if ($oldheader['customerid'] != $customerid){
			$customer->setId($oldheader['customerid']);
			$customer->minCredit($oldheader['totalpayment']);
			
			$customer->setId($customerid);
			$customer->addCredit($totalpayment);
			}
			else {
			
			if ($oldheader['totalpayment'] != $totalpayment){

			$customer->setId($customerid);
			$customer->addCredit($totalpayment-$oldheader['totalpayment']);
			
			}
			
			}
			
			
			}
		else if ($oldheader['complete'] == "0" && $complete == "1"){
			
			$customer->setId($customerid);
			$customer->addCredit($totalpayment-$oldheader['totalpayment']);

		
		}
		else if ($oldheader['complete'] == "1" && $complete == "0"){
			echo "s";
			if ($oldheader['customerid'] != $customerid){
			$customer->setId($oldheader['customerid']);
			$customer->addCredit($oldheader['totalpayment']);
			}
			else{
			echo "s";
			$customer->setId($customerid);
			$customer->addCredit($totalpayment);
			}
		
		}
		 */
		
		$updates['lastedited'] = time();
		$updates['lasteditedby'] = $userid;
		
		$db->update("headerpayment",$updates,"hpid='".$this->id."'");
		
		/* if ($oldheader['customerid'] != $customerid){
			$db->query("UPDATE customer SET remainingcredit=remainingcredit+".$oldheader['remainingprevious']." WHERE customerid='".$oldheader['customerid']."'");
			$db->query("UPDATE customer SET remainingcredit=remainingcredit-".$oldheader['remainingnow']." WHERE customerid='".$oldheader['customerid']."'");
			
			$db->query("UPDATE customer SET remainingcredit=remainingcredit-".$remainingprevious." WHERE customerid='".$customerid."'");
			$db->query("UPDATE customer SET remainingcredit=remainingcredit+".$remainingnow." WHERE customerid='".$customerid."'");
		}
		else{
			if ($oldheader['remainingprevious'] != $remainingprevious){
				$db->query("UPDATE customer SET remainingcredit=remainingcredit+".($oldheader['remainingprevious']-$remainingprevious)." WHERE customerid='".$customerid."'");
			}
			if ($oldheader['remainingnow'] != $remainingnow){
				$db->query("UPDATE customer SET remainingcredit=remainingcredit+".($remainingnow-$oldheader['remainingnow'])." WHERE customerid='".$customerid."'");
			}
		} */
	}
	
	function updateDebtCreditEdit($customerid,$customercode,$complete,$oldheader){
		global $db, $customer, $supplier;
		
		$header = $this->getHeaderPayment();
		$alldetail = $this->getDetailPayment();
		if (sizeof($alldetail) > 0){
			$totalforbuy = 0;
			$totalforsale = 0;
			foreach ($alldetail as $ad){
				if ($ad['types'] == 'sale'){
					$totalforsale += $ad['pays'];
				}
				else if ($ad['types'] == 'return'){
					$totalforsale -= $ad['pays'];
				}
				else if ($ad['types'] == 'buy'){
					$totalforbuy += $ad['pays'];
				}
				else if ($ad['types'] == 'returnby'){
					$totalforbuy -= $ad['pays'];
				}
			}
		}
		
		if ($totalforbuy > $totalforsale)
		$status  = 2;
		else
		$status  = 1;
		
		//echo "<br>";
		/*echo $totalforsale;
		echo "<br>";
		echo $status; */
		
		$db->query("UPDATE headerpayment SET totalforbuy='".$totalforbuy."', totalforsale='".$totalforsale."', status='".$status."' WHERE hpid = '".$this->id."'");
		$totalpayment = $header['totalpayment'];
		if ($complete){			
			if ($oldheader['complete'] == 0){
				$supplier->setId("");
				$customer->setId("");
		
				$customer->setId($customerid);
				$getscode = $customer->getcustomerDetail();
				
				$checkcostumsu = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customerid = '".$customerid."' ");
			
				if (!empty($checkcostumsu['supplierid'])){
				
				if ($getscode['credit'] < $totalpayment){
				
				$db->query("UPDATE customer SET credit=0 WHERE customerid='".$customerid."'");
				
				$supplier->setCode($getscode['customercode']);
				$supplier->addDebt($totalpayment-$getscode['credit']);
				
				}
				else{
				$customer->minCredit($totalpayment);
				}
			}	
				else{
				$customer->minCredit($totalpayment);
				}
				
				/* $supplier->setCode($getscode['customercode']);
				$supplier->minDebt($totalforbuy); */
			}
			else{
				if ($oldheader['customerid'] != $customerid){
					$customer->setId($oldheader['customerid']);
					$getscode = $customer->getcustomerDetail();
					$customer->addCredit($oldheader['totalpayment']);
					
					$customer->setId("");
					$customer->setCode($customercode);
					$customer->minCredit($totalpayment);
					
					/* $supplier->setCode($getscode['customercode']);
					$supplier->addDebt($oldheader['totalforbuy']); */
					
					/* $supplier->setCode($customercode);
					$supplier->minDebt($totalforbuy); */
				}
				else if ($oldheader['totalpayment'] != $totalpayment){
					$customer->setCode($customercode);
					$customer->addCredit($oldheader['totalpayment']-$totalpayment);
				}
				/* else if ($oldheader['totalforbuy'] != $totalforbuy){
					$supplier->setCode($customercode);
					$supplier->addDebt($oldheader['totalforbuy']-$totalforbuy);
				} */
			}
		}
		else{
			if ($oldheader['complete'] == 1){
				$customer->setId($oldheader['customerid']);
				$getscode = $customer->getcustomerDetail();
				$customer->addCredit($oldheader['totalpayment']);
				
				/* $supplier->setId($oldheader['customerid']);
				$supplier->addDebt($oldheader['totalforbuy']); */
			}
		}
	}
	
	
	function updateDebtCreditnotlive($csid,$complete){
		global $db, $supplier, $customer;
		
		$alldetail = $this->getDetailPayment();
		if (sizeof($alldetail) > 0){
			$totalforbuy = 0;
			$totalforsale = 0;
			foreach ($alldetail as $ad){
				if ($ad['types'] == 'sale'){
					$totalforsale += $ad['pays'];
				}
				else if ($ad['types'] == 'return'){
					$totalforsale -= $ad['pays'];
				}
				else if ($ad['types'] == 'buy'){
					$totalforbuy += $ad['pays'];
				}
				else if ($ad['types'] == 'returnby'){
					$totalforbuy -= $ad['pays'];
				}
			}
		}
		
		if ($totalforbuy > $totalforsale)
		$status  = 2;
		else
		$status  = 1;
		
		$db->query("UPDATE headerpayment SET totalforbuy='".abs($totalforbuy)."', totalforsale='".abs($totalforsale)."', status='".$status."' WHERE hpid = '".$this->id."'");
		
		$supplier->setId("");
		$customer->setId("");

		$customer->setCode($csid);
		$getscode = $customer->getcustomerDetail();
		$totalpayment = $totalforsale-$totalforbuy;
		
		
		if ($totalpayment > 0){
		$checkcostumsu = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$csid."' ");
		if ($complete){
		if (!empty($checkcostumsu['supplierid'])){
		
		
		if ($getscode['credit'] < $totalpayment){
		
		$db->query("UPDATE customer SET credit=0 WHERE customercode='".$csid."'");

		$supplier->setCode($getscode['customercode']);
		$supplier->addDebt($totalpayment-$getscode['credit']);

		}
		else{
		$customer->minCredit($totalpayment);
		}
		}	
		else{
		$customer->minCredit($totalpayment);
		}
		}
		}
		
		else{
		if ($complete){
		$checkcostumsu = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$csid."' ");
	
		if (!empty($checkcostumsu['supplierid'])){
		
		
		if ($checkcostumsu['debt'] < $totalpayment){
		
		$db->query("UPDATE supplier SET debt=0 WHERE suppliercode='".$csid."'");

		$customer->setCode($getscode['customercode']);
		$customer->minCredit($totalpayment-$checkcostumsu['debt']);

		}
		else{
		$supplier->addDebt($totalpayment);
		}
		}	
		else{
		$supplier->addDebt($totalpayment);
		}
		}
		
		}
		
		
	}
	
	
	
	
	function updateDebtCreditEdit2($supplierid,$suppliercode,$complete,$oldheader){
		global $db, $customer, $supplier;
		

		$alldetail = $this->getDetailPayment();
		if (sizeof($alldetail) > 0){
			$totalforbuy = 0;
			$totalforsale = 0;
			foreach ($alldetail as $ad){
				if ($ad['types'] == 'sale'){
					$totalforsale += $ad['pays'];
				}
				else if ($ad['types'] == 'return'){
					$totalforsale -= $ad['pays'];
				}
				else if ($ad['types'] == 'buy'){
					$totalforbuy += $ad['pays'];
				}
				else if ($ad['types'] == 'returnby'){
					$totalforbuy -= $ad['pays'];
				}
			}
		}
		
		if ($totalforbuy > $totalforsale)
		$status  = 2;
		else
		$status  = 1;
		
		//echo "UPDATE headerpayment SET totalforbuy='".$totalforbuy."', totalforsale='".$totalforsale."', status='".$status."' WHERE hpid = '".$this->id."'";
		
		$db->query("UPDATE headerpayment SET totalforbuy='".abs($totalforbuy)."', totalforsale='".abs($totalforsale)."', status='".$status."' WHERE hpid = '".$this->id."'");
		
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
				
				$customer->setId($oldheader['customerid']);
				$customer->addCredit($oldheader['totalforsale']);
			}
		}
	}
	
	function updateDebtCreditEdit3($supplierid,$suppliercode,$complete,$oldheader){
		global $db, $customer, $supplier;
		

		$alldetail = $this->getDetailPayment();
		if (sizeof($alldetail) > 0){
			$totalforbuy = 0;
			$totalforsale = 0;
			foreach ($alldetail as $ad){
				if ($ad['types'] == 'sale'){
					$totalforsale += $ad['pays'];
				}
				else if ($ad['types'] == 'return'){
					$totalforsale -= $ad['pays'];
				}
				else if ($ad['types'] == 'buy'){
					$totalforbuy += $ad['pays'];
				}
				else if ($ad['types'] == 'returnby'){
					$totalforbuy -= $ad['pays'];
				}
			}
		}
		
		if ($totalforbuy < $totalforsale)
		$status  = 1;
		else
		$status  = 2;
		
		//echo "UPDATE headerpayment SET totalforbuy='".$totalforbuy."', totalforsale='".$totalforsale."', status='".$status."' WHERE hpid = '".$this->id."'";
		
		$db->query("UPDATE headerpayment SET totalforbuy='".abs($totalforbuy)."', totalforsale='".abs($totalforsale)."', status='".$status."' WHERE hpid = '".$this->id."'");
		
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
				
				$customer->setId($oldheader['customerid']);
				$customer->addCredit($oldheader['totalforsale']);
			}
		}
	}
	
	function updateDebtCreditEdit4($customerid,$customercode,$complete,$oldheader){
		global $db, $customer, $supplier;
		
		$alldetail = $this->getDetailPayment();
		if (sizeof($alldetail) > 0){
			$totalforbuy = 0;
			$totalforsale = 0;
			foreach ($alldetail as $ad){
				if ($ad['types'] == 'sale'){
					$totalforsale += $ad['pays'];
				}
				else if ($ad['types'] == 'return'){
					$totalforsale -= $ad['pays'];
				}
				else if ($ad['types'] == 'buy'){
					$totalforbuy += $ad['pays'];
				}
				else if ($ad['types'] == 'returnby'){
					$totalforbuy -= $ad['pays'];
				}
			}
		}
		
		if ($totalforbuy < $totalforsale)
		$status  = 2;
		else
		$status  = 1;
		
		/* echo "<br>";
		echo $totalforsale;
		echo "<br>";
		echo $status; */
		
		$db->query("UPDATE headerpayment SET totalforbuy='".$totalforbuy."', totalforsale='".$totalforsale."', status='".$status."' WHERE hpid = '".$this->id."'");
		
		if ($complete){			
			if ($oldheader['complete'] == 0){
				$customer->setId($oldheader['customerid']);
				$getscode = $customer->getcustomerDetail();
				$customer->minCredit($totalforsale);
				
				$supplier->setCode($getscode['customercode']);
				$supplier->minDebt($totalforbuy);
			}
			else{
				if ($oldheader['customerid'] != $customerid){
					$customer->setId($oldheader['customerid']);
					$getscode = $customer->getcustomerDetail();
					$customer->addCredit($oldheader['totalforsale']);
					
					$customer->setId("");
					$customer->setCode($customercode);
					$customer->minCredit($totalforsale);
					
					$supplier->setCode($getscode['customercode']);
					$supplier->addDebt($oldheader['totalforbuy']);
					
					$supplier->setCode($customercode);
					$supplier->minDebt($totalforbuy);
				}
				else if ($oldheader['totalforsale'] != $totalforsale){
					$customer->setCode($customercode);
					$customer->addCredit($oldheader['totalforsale']-$totalforsale);
				}
				else if ($oldheader['totalforbuy'] != $totalforbuy){
					$supplier->setCode($customercode);
					$supplier->addDebt($oldheader['totalforbuy']-$totalforbuy);
				}
			}
		}
		else{
			if ($oldheader['complete'] == 1){
				$customer->setId($oldheader['customerid']);
				$getscode = $customer->getcustomerDetail();
				$customer->addCredit($oldheader['totalforsale']);
				
				$supplier->setId($oldheader['customerid']);
				$supplier->addDebt($oldheader['totalforbuy']);
			}
		}
	}
	
	
	function updateDetailPayment($hsid,$pays,$paymentdate,$description,$types,$complete,$completedate,$olddetail){
		global $db,$stock;
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($hsid)){
				$updates["hsid"] = $hsid;
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
			
			if ($complete != "bb" ){
			$updatepaid = '';
			if ($complete){
				$updatepaid = ', paid=1';
			}
			else{
				$updatepaid = ', paid=0, paydate=0';
			}
			}
			
			if (empty($completedate)){
				$completedate = time();
			}
			
			if (sizeof($updates) > 0){				
				$db->update("detailpayment",$updates,"dpid='".$this->dtid."'");
				if ($olddetail['hsid'] != $hsid){
					if ($olddetail['types'] == 'return'){
						$db->query("UPDATE detailsaler SET claims=0, paid=0, paydate=0 WHERE dsrid='".$olddetail['hsid']."'");
					}
					else if ($olddetail['types'] == 'sale'){
						$db->query("UPDATE headersale SET claims=0, paid=0, paydate=0 WHERE saleid='".$olddetail['hsid']."'");
					}
					else if ($olddetail['types'] == 'returnby'){
						$db->query("UPDATE detailbuyr SET claims=0, paid=0, paydate=0 WHERE dbrid='".$olddetail['hsid']."'");
					}
					else if ($olddetail['types'] == 'sale'){
						$db->query("UPDATE headerbuy SET claims=0, paid=0, paydate=0 WHERE buyid='".$olddetail['hsid']."'");
					}
					
					if ($types == 'return'){
						$db->query("UPDATE detailsaler SET paydate=".$completedate." WHERE dsrid='".$hsid."'");
						$db->query("UPDATE detailsaler SET claims=1".$updatepaid." WHERE dsrid='".$hsid."'");
					}
					else if ($types == 'sale'){
						$db->query("UPDATE headersale SET paydate=".$completedate." WHERE saleid='".$hsid."'");
						$db->query("UPDATE headersale SET claims=1".$updatepaid." WHERE saleid='".$hsid."'");
					}
					else if ($types == 'returnby'){
						$db->query("UPDATE detailbuyr SET paydate=".$completedate." WHERE dbrid='".$hsid."'");
						$db->query("UPDATE detailbuyr SET claims=1".$updatepaid." WHERE dbrid='".$hsid."'");
					}
					else if ($types == 'buy'){
						$db->query("UPDATE headerbuy SET paydate=".$completedate." WHERE buyid='".$hsid."'");
						$db->query("UPDATE headerbuy SET claims=1".$updatepaid." WHERE buyid='".$hsid."'");
					}
				}
				else{
					if ($types == 'return'){
						$db->query("UPDATE detailsaler SET paydate=".$completedate." WHERE dsrid='".$hsid."'");
						$db->query("UPDATE detailsaler SET claims=1".$updatepaid." WHERE dsrid='".$hsid."'");
					}
					else if ($types == 'sale'){
						$db->query("UPDATE headersale SET paydate=".$completedate." WHERE saleid='".$hsid."'");
						$db->query("UPDATE headersale SET claims=1".$updatepaid." WHERE saleid='".$hsid."'");
					}
					else if ($types == 'returnby'){
						$db->query("UPDATE detailbuyr SET paydate=".$completedate." WHERE dbrid='".$hsid."'");
						$db->query("UPDATE detailbuyr SET claims=1".$updatepaid." WHERE dbrid='".$hsid."'");
					}
					else if ($types == 'buy'){
						$db->query("UPDATE headerbuy SET paydate=".$completedate." WHERE buyid='".$hsid."'");
						$db->query("UPDATE headerbuy SET claims=1".$updatepaid." WHERE buyid='".$hsid."'");
					}
				}
			}
		}
	}
	
	function updateDetailRePayment($types,$bank,$accname,$accnumber,$dates,$duedates,$totals,$notes,$complete){
		global $db;
		if (!empty($this->id) && !empty($this->dtrid)){
			$olddetail = $this->getDetailRePaymentIndv();
			
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
			$db->update("detailrepayment",$updates,"drpyid='".$this->dtrid."'");
		}
	}
	
	function deleteDetailPayment(){
		global $db;
		
		if (!empty($this->dtid)){
			$getdetail = $this->getDetailPaymentIndv();
			if ($getdetail['types'] == 'return'){
				$db->query("UPDATE detailsaler SET claims=0, paid=0, paydate=0 WHERE dsrid='".$getdetail['hsid']."'");
			}
			else if ($getdetail['types'] == 'sale'){
				$db->query("UPDATE headersale SET claims=0, paid=0, paydate=0 WHERE saleid='".$getdetail['hsid']."'");
			}
			else if ($getdetail['types'] == 'returnby'){
				$db->query("UPDATE detailbuyr SET claims=0, paid=0, paydate=0 WHERE dbrid='".$getdetail['hsid']."'");
			}
			else if ($getdetail['types'] == 'buy'){
				$db->query("UPDATE headerbuy SET claims=0, paid=0, paydate=0 WHERE buyid='".$getdetail['hsid']."'");
			}
			$db->query("DELETE FROM detailpayment WHERE dpid='".$this->dtid."'");
			 
		}
	}
	
	function deleteDetailRePayment(){
		global $db;
		
		if (!empty($this->dtrid)){
			$db->query("DELETE FROM detailrepayment WHERE drpyid='".$this->dtrid."'");
		}
	}
	
	function deletePayment(){
		global $db, $customer, $supplier;
		
		if (!empty($this->id)){
			$oldheader = $this->getHeaderPayment();
			$alldetail = $this->getDetailPayment();
			if (sizeof($alldetail) > 0){
				$totalforbuy = 0;
				$totalforsale = 0;
				foreach ($alldetail as $ad){
					$this->dtid = $ad['dpid'];
					$this->deleteDetailPayment();
					if ($ad['types'] == 'sale'){
						$totalforsale += $ad['pays'];
					}
					else if ($ad['types'] == 'return'){
						$totalforsale -= $ad['pays'];
					}
					else if ($ad['types'] == 'buy'){
						$totalforbuy += $ad['pays'];
					}
					else if ($ad['types'] == 'returnby'){
						$totalforbuy -= $ad['pays'];
					}
				}
			}
			
			if (!empty($oldheader['remainingprevious'])){
				$db->query("UPDATE customer SET remainingcredit=remainingcredit+".$oldheader['remainingprevious']." WHERE customerid='".$oldheader['customerid']."'");
			}
			if (!empty($oldheader['remainingnow'])){
				$db->query("UPDATE customer SET remainingcredit=remainingcredit-".$oldheader['remainingnow']." WHERE customerid='".$oldheader['customerid']."'");
			}
			
			$db->query("DELETE FROM detailrepayment WHERE hpid='".$this->id."'");
			$db->query("DELETE FROM headerpayment WHERE hpid='".$this->id."'");
			if ($oldheader['complete'] == 1){
				if ($totalforsale > 0){
					$customer->setId($oldheader['customerid']);
					$customer->addCredit($totalforsale);
				}
				if ($totalforbuy > 0){
					$customer->setId($oldheader['customerid']);
					$getscode = $customer->getcustomerDetail('partial');
					
					$supplier->setCode($getscode['customercode']);
					$supplier->addDebt($totalforbuy);
				}
			}
		}
	}
	
	function searchPayment($keyword,$field,$startpaysdate,$endpaysdate){
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
			switch ($field){
				case 'paymentdate' : 
					$strinarr = 'p.paymentdate=\''.strtotime($keyword).'\'';
					$field = 'p.hpid';
					break;
				case 'customername' : 
					$innerjoin = ' INNER JOIN customer s ON p.customerid = s.customerid';
					$strinarr = 's.customername LIKE (\''.$keyword.'%\')';
					$field = 's.customername';
					break;
				case 'suppliername' : 
					$innerjoin = ' INNER JOIN supplier s ON p.supplierid = s.supplierid';
					$strinarr = 's.suppliername LIKE (\''.$keyword.'%\')';
					$field = 's.suppliername';
					break;
				case 'saleno' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN headersale hs ON d.hsid = hs.saleid';
					$strinarr = 'hs.saleno LIKE (\''.$keyword.'%\') AND d.types=\'sale\'';
					$field = 'hs.saleno';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'salerid' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN detailsaler dsr ON d.hsid = dsr.dsrid';
					$strinarr = 'dsr.salerid LIKE (\''.$keyword.'%\') AND d.types=\'return\'';
					$field = 'dsr.salerid';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'orderno' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN headerbuy hb ON d.hsid = hb.buyid';
					$strinarr = 'hb.orderno LIKE (\''.$keyword.'%\') AND d.types=\'buy\'';
					$field = 'hb.orderno';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'buyrid' : 
					$innerjoin = ' INNER JOIN detailpayment d ON p.hpid = d.hpid INNER JOIN detailbuyr dbr ON d.hsid = dbr.dbrid';
					$strinarr = 'dbr.buyrid LIKE (\''.$keyword.'%\') AND d.types=\'returnby\'';
					$field = 'dbr.buyrid';
					$groupby = ' GROUP BY p.hpid';
					break;
				case 'status' :
					global $arrpayment;
					$candb = false;
					$keynow = -1;
					foreach ($arrpayment as $keys => $ast){
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
				default : $strinarr = $field.' LIKE (\''.$keyword.'%\')';
			}
			array_push($sqls,$strinarr);
		}
		
		if (!empty($startpaysdate)){
			array_push($sqls,'startdate >= '.$startpaysdate);
		}
		
		if (!empty($endpaysdate)){
			array_push($sqls,'enddate <= '.$endpaysdate);
		}
		
		array_push($sqls,'p.status = 1');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbpayment = $db->fetch_all("SELECT p.* FROM headerpayment p".$innerjoin.$sql.$groupby." ORDER BY ".$field);
		
		return $dbpayment;
	}
}
?>