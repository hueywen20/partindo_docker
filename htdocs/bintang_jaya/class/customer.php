<?php
class customer{
	var $id;
	var $dtid;
	var $code;
	
	function setId($id){
		global $db;
		$this->id = $db->clean($id);
	}
		
	function setCode($code){
		global $db;
		$this->code = $db->clean($code);
	}

	function setDetailId($dtid){
		global $db;
		$this->dtid = $db->clean($dtid);
	}
	
	function getListcustomer($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcustomer = $db->fetch_all("SELECT * FROM customer".$sql." ORDER BY customercode");
		
		return $dbcustomer;
	}
	
	function getcustomerDetail(){
		global $db;
		
		$getfield = '';
		$getaddr = '';
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'c.customerid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'c.customercode = \''.$this->code.'\'');
		}
		if (!empty($this->dtid)){
			$getaddr = " INNER JOIN detailcustomer dc ON c.customerid = dc.customerid";
			$getfield = ", dc.address, dc.areacode, dc.phone, dc.contactperson";
			array_push($sqls,'dc.detailcustid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcustomer = $db->fetch_one("SELECT c.*".$getfield." FROM customer c".$getaddr.$sql);
		
		
		
		return $dbcustomer;
	}
	
	function getcustomeraddrdetail($mode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'customerid = \''.$this->id.'\'');
		}
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcustomer = $db->fetch_all("SELECT * FROM detailcustomer".$sql." ORDER BY detailcustid");
		
		return $dbcustomer;
	}
	
	function checkcodeexist($customercode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'customerid <> \''.$this->id.'\'');
		}
		array_push($sqls,'customercode = \''.$customercode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM customer".$sql);
		if (sizeof($checkexist) > 0){
			return 1;
		}
		else{
			$checksuppcode = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode='".$customercode."'");
			if (!empty($checksuppcode['supplierid'])){
				return 'Kode Customer yang anda masukkan telah ada dalam kode supplier dengan nama supplier "'.$checksuppcode['suppliername'].'". Apakah anda yakin untuk menyamakannya dengan supplier tersebut ?';
			}
			
			return 0;
		}
	}
	
	function canDeleteCustomer(){
		global $db;
		
		if (!empty($this->code)){
			$checksale = $db->fetch_one("SELECT * FROM headersale WHERE customercode='".$this->code."' LIMIT 1");
			if (sizeof($checksale) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savecustomer($customercode,$customername,$customertype,$customerstatus,$userid){
		global $db;
		if ($this->checkcodeexist($customercode) != 1){
			$inserts['customername'] = $customername;
			$inserts['customercode'] = $customercode;
            $inserts["customertype"] = $customertype;
            $inserts["credit"] = 0;
            $inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $customerstatus;
			return $db->insert("customer",$inserts);
		}
	}
	
	function savedetailcustomer($customeraddress,$customercontactperson,$customerpostalcode,$customerareacode,$customerstatecode,$customercountrycode,$customerphone,$customerfax,$customermobilenumber,$customerstatus){
		global $db;
		if (!empty($this->id)){
			$inserts['customerid'] = $this->id;
			$inserts["address"] = $customeraddress;
            $inserts["areacode"] = $customerareacode;
            $inserts["statecode"] = $customerstatecode;
            $inserts["countrycode"] = $customercountrycode;
            $inserts["postalcode"] = $customerpostalcode;
            $inserts["phone"] = $customerphone;
            $inserts["fax"] = $customerfax;
            $inserts["contactperson"] = $customercontactperson;
            $inserts["mobilenumber"] = $customermobilenumber;
			$inserts['status'] = $customerstatus;
			return $db->insert("detailcustomer",$inserts);
		}
	}
	
	function updatecustomer($customercode,$customername,$customertype,$customerstatus,$userid){
		global $db;
		
		if ($this->checkcodeexist($customercode) != 1){
			$ccode = $this->getcustomerDetail();
		
			$updates['customername'] = $customername;
			$updates['customercode'] = $customercode;
			$updates["customertype"] = $customertype;
			$updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
			$updates['status'] = $customerstatus;
			
			$db->update("customer",$updates,"customerid='".$this->id."'");
			
			if ($customercode != $ccode['customercode']){
				//update header sale
				$db->query("UPDATE headersale SET customercode='".$customercode."' WHERE customercode='".$ccode['customercode']."'");
				//update header sale return
				$db->query("UPDATE headersaler SET customercode='".$customercode."' WHERE customercode='".$ccode['customercode']."'");
			}
		}
	}
	
	function updatedetailcustomer($customeraddress,$customercontactperson,$customerpostalcode,$customerareacode,$customerstatecode,$customercountrycode,$customerphone,$customerfax,$customermobilenumber,$customerstatus){
		global $db;
		
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($customeraddress)){
				$updates["address"] = $customeraddress;
			}
			if (!empty($customerareacode)){
				$updates["areacode"] = $customerareacode;
			}
			if (!empty($customerstatecode)){
				$updates["statecode"] = $customerstatecode;
			}
			if (!empty($customercountrycode)){
				$updates["countrycode"] = $customercountrycode;
			}
			if (!empty($customerpostalcode)){
				$updates["postalcode"] = $customerpostalcode;
			}
			if (!empty($customerphone)){
				$updates["phone"] = $customerphone;
			}
			if (!empty($customerfax)){
				$updates["fax"] = $customerfax;
			}
			if (!empty($customercontactperson)){
				$updates["contactperson"] = $customercontactperson;
			}
			if (!empty($customermobilenumber)){
				$updates["mobilenumber"] = $customermobilenumber;
			}
			if (isset($customerstatus)){
				$updates["status"] = $customerstatus;
			}
			
			if (sizeof($updates) > 0){
				$db->update("detailcustomer",$updates,"detailcustid='".$this->dtid."'");
			}
		}
	}
	
	function copyToSupplier($userid){
		global $db;
		
		if (!empty($this->id)){
			$getheader = $this->getcustomerDetail();
			if (!empty($getheader['customerid'])){
				$checksuppcode = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode='".$getheader['customercode']."'");
				if (!empty($checksuppcode['supplierid'])){
					return false;
				}
				$insert['suppliercode'] = $getheader['customercode'];
				$insert['suppliername'] = $getheader['customername'];
				$insert['debt'] = 0;
				$insert['createddate'] = time();
				$insert['createdby'] = $userid;
				$insert['lastedited'] = time();
				$insert['lasteditedby'] = $userid;
				$insert['status'] = $getheader['status'];
				$lastid = $db->insert("supplier",$insert);
				
				$getaddress = $this->getcustomeraddrdetail('all');
				if (sizeof($getaddress) > 0){
					foreach ($getaddress as $gadr){
						unset($gadr['customerid']);
						unset($gadr['detailcustid']);
						$gadr['supplierid'] = $lastid;
						$db->insert("detailsupplier",$gadr);
					}
				}
				return true;
			}
		}
		return false;
	}
	
	function searchcustomer($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'customercode LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcustomer = $db->fetch_all("SELECT * FROM customer".$sql." ORDER BY customercode");
		
		return $dbcustomer;
	}

	function searchcustomerfull($keyword,$field,$page,$getreturn,$searchmode = 'in',$orderfield='',$sortdetail=''){
	                     
		global $db,$general;
		
		$limits = '';
		if (!empty($page)){
			$limits = " LIMIT ".(($page-1)*$general['showperpage']).",".$general['showperpage'];
		}
		
		$addlikes = '';
		if ($searchmode == 'in'){
			$addlikes = '%';
		}
		
		$sqls = array();
		
		$innerjoin = ' INNER JOIN detailcustomer db ON s.customerid = db.customerid';
		$groupby = ' GROUP BY s.customerid';
		if (isset($keyword)){
			if (sizeof($field) == 0){
				$field = array('customername');
			}
			$strinarr = '';
			
			$arrf1 = array_search('customercode',$field);
			if ($arrf1 !== false){
				$strinarr = 's.customercode LIKE (\''.$addlikes.$db->clean($keyword[$arrf1]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf2 = array_search('customername',$field);
			if ($arrf2 !== false){
				$strinarr = 's.customername LIKE (\''.$addlikes.$db->clean($keyword[$arrf2]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf3 = array_search('address',$field);
			if ($arrf3 !== false){
				$strinarr = 'db.address LIKE (\''.$addlikes.$db->clean($keyword[$arrf3]).'%\')';				
				array_push($sqls,$strinarr);
			}
			$arrf4 = array_search('contactperson',$field);
			if ($arrf4 !== false){
				//$innerjoin = ' INNER JOIN detailcustomer b ON s.customerid = b.customerid';
				$strinarr = 'db.contactperson LIKE (\''.$addlikes.$db->clean($keyword[$arrf4]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf5 = array_search('phone',$field);
			if ($arrf5 !== false){
				//$innerjoin = ' INNER JOIN detailcustomer b ON s.customerid = b.customerid';
				$strinarr = 'db.phone LIKE (\''.$addlikes.$db->clean($keyword[$arrf5]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf6 = array_search('areaname',$field);
			if ($arrf6 !== false){
				//$innerjoin = ' INNER JOIN detailcustomer b ON s.customerid = b.customerid INNER JOIN area a ON b.areacode = a.areacode';
				$strinarr = 'db.areacode LIKE (\''.$addlikes.$db->clean($keyword[$arrf6]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf7 = array_search('detailstatus',$field);
			if ($arrf7 !== false){
				$strinarr = 'db.status = \''.$db->clean($keyword[$arrf7]).'\'';
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
					$keynow = -1;
				}
				$strinarr = 's.status = \''.$keynow.'\'';
				array_push($sqls,$strinarr);
			}
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		if (empty($orderfield)){
			$orderfield = 's.customercode';
		}
		
		$orders = 'ASC';
		if (!empty($sortdetail)){
				$orders = $sortdetail;
			}
		
		if ($getreturn == 'data'){
		
			if (!empty($orderfield)){
				switch ($orderfield){
					case 'sortcustomercode': $orderfield = 's.customercode'; break;
					case 'sortcustomername': $orderfield = 's.customername'; break;
					case 'sortcustomeradd': $orderfield = 'db.address'; break;
					case 'sortcustomercperson': $orderfield = 'db.contactperson'; break;
					case 'sortcustomertelp': $orderfield = 'db.phone'; break;
					case 'sortcustomercity': $orderfield = 'db.areacode'; break;
					case 'sortcustomerstatus': $orderfield = 's.status'; break;
					case 'sortlastediteddate': $orderfield = 's.lastedited'; break;
					case 'sortlasteditedby': $orderfield = 's.lasteditedby'; break;
					default: $orderfield = 's.customercode'; break;
				}
			}
			
			$dbcustomer = $db->fetch_all("SELECT s.* FROM customer s".$innerjoin.$sql.$groupby." ORDER BY ".$orderfield." ".$orders." ".$limits);
			
			return $dbcustomer;
		}
		else if ($getreturn == 'pagenav'){
			$dbcustomer = $db->query("SELECT * FROM customer s".$innerjoin.$sql.$groupby);
			$ttrecord = @mysql_num_rows($dbcustomer);
			
			if ($ttrecord > 0){
				$totalrecord = $ttrecord;
				$totalpage = ceil($totalrecord / $general['listitemperpage']);
				$startrecord = ($page - 1) * $general['listitemperpage'] + 1;
				$endrecord = $startrecord + $general['listitemperpage'] - 1;
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
	}
	
	function deletecustomer(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM customer WHERE customerid='".$this->id."'");
			$db->query("DELETE FROM detailcustomer WHERE customerid='".$this->id."'");
		}
	}
	
	function deletedetailcustomer(){
		global $db;
		
		if (!empty($this->dtid)){			
			$db->query("DELETE FROM detailcustomer WHERE detailcustid='".$this->dtid."'");
		}
	}
	
	function addCredit($credits){
		global $db;
		
		if (!empty($credits)){
			if (!empty($this->id)){

				$db->query("UPDATE customer SET credit=credit+".$credits." WHERE customerid='".$this->id."'");
			}
			else if (!empty($this->code)){
				
				$db->query("UPDATE customer SET credit=credit+".$credits." WHERE customercode='".$this->code."'");
			}
		}
	}
	
	function minCredit($credits){
		global $db;
		
		if (!empty($credits)){
			if (!empty($this->id)){
				$db->query("UPDATE customer SET credit=credit-".$credits." WHERE customerid='".$this->id."'");
			}
			else if (!empty($this->code)){
				$db->query("UPDATE customer SET credit=credit-".$credits." WHERE customercode='".$this->code."'");
			}
		}
	}
	
	function checkLastLimitTransaction(){
		global $db;
		global $salesetting;
		
		if (!empty($this->code)){
			$limittime = strtotime(date('Y-m').'-01 00:00:00')-1-($salesetting['blocklimit']*86400);
			$getlastsale = $db->fetch_all("SELECT * FROM headersale WHERE customercode='".$this->code."' AND saledate < ".$limittime." AND paid = 0");
			if (sizeof($getlastsale) > 0){
				return false;
			}
		}
		return true;
	}
}
?>