<?php
class supplier{
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
	
	function getListsupplier($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsupplier = $db->fetch_all("SELECT * FROM supplier".$sql." ORDER BY suppliercode");
		
		return $dbsupplier;
	}
	
	function getsupplierDetail(){
		global $db;
		
		$getfield = '';
		$getaddr = '';
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'s.supplierid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'s.suppliercode = \''.$this->code.'\'');
		}
		if (!empty($this->dtid)){
			$getaddr = " INNER JOIN detailsupplier ds ON s.supplierid = ds.supplierid";
			$getfield = ", ds.address, ds.areacode, ds.phone, ds.contactperson";
			array_push($sqls,'ds.detailsplid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsupplier = $db->fetch_one("SELECT s.*".$getfield." FROM supplier s".$getaddr.$sql);
		
		return $dbsupplier;
	}
	
	function getsupplieraddrdetail($mode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'supplierid = \''.$this->id.'\'');
		}
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsupplier = $db->fetch_all("SELECT * FROM detailsupplier".$sql." ORDER BY detailsplid");
		
		return $dbsupplier;
	}
	
	function checkcodeexist($suppliercode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'supplierid <> \''.$this->id.'\'');
		}
		array_push($sqls,'suppliercode = \''.$suppliercode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM supplier".$sql);
		if (sizeof($checkexist) > 0){
			return 1;
		}
		else{
			$checkcustcode = $db->fetch_one("SELECT * FROM customer WHERE customercode='".$suppliercode."'");
			if (!empty($checkcustcode['customerid'])){
				return 'Kode Supplier yang anda masukkan telah ada dalam kode customer dengan nama customer "'.$checkcustcode['customername'].'". Apakah anda yakin untuk menyamakannya dengan customer tersebut ?';
			}
			return 0;
		}
	}
	
	function canDeleteSupplier(){
		global $db;
		
		if (!empty($this->code)){
			$checkbuy = $db->fetch_one("SELECT * FROM headerbuy WHERE suppliercode='".$this->code."' LIMIT 1");
			if (sizeof($checkbuy) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savesupplier($suppliercode,$suppliername,$supplierstatus,$userid){
		global $db;
		if ($this->checkcodeexist($suppliercode) != 1){
			$inserts['suppliername'] = $suppliername;
			$inserts['suppliercode'] = $suppliercode;
            $inserts["debt"] = 0;
			$inserts['status'] = $supplierstatus;
            $inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			return $db->insert("supplier",$inserts);
		}
	}
	
	function savedetailsupplier($supplieraddress,$suppliercontactperson,$supplierpostalcode,$supplierareacode,$supplierstatecode,$suppliercountrycode,$supplierphone,$supplierfax,$suppliermobilenumber,$supplierstatus){
		global $db;
		if (!empty($this->id)){
			$inserts['supplierid'] = $this->id;
			$inserts["address"] = $supplieraddress;
            $inserts["areacode"] = $supplierareacode;
            $inserts["statecode"] = $supplierstatecode;
            $inserts["countrycode"] = $suppliercountrycode;
            $inserts["postalcode"] = $supplierpostalcode;
            $inserts["phone"] = $supplierphone;
            $inserts["fax"] = $supplierfax;
            $inserts["contactperson"] = $suppliercontactperson;
            $inserts["mobilenumber"] = $suppliermobilenumber;
			$inserts['status'] = $supplierstatus;
			return $db->insert("detailsupplier",$inserts);
		}
	}
	
	function updatesupplier($suppliercode,$suppliername,$supplierstatus,$userid){
		global $db;
		
		if ($this->checkcodeexist($suppliercode) != 1){
			$scode = $this->getsupplierDetail();
		
			$updates['suppliername'] = $suppliername;
			$updates['suppliercode'] = $suppliercode;
			$updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
			$updates['status'] = $supplierstatus;
			
			$db->update("supplier",$updates,"supplierid='".$this->id."'");
			
			if ($suppliercode != $scode['suppliercode']){
				//update header sale
				$db->query("UPDATE headerbuy SET suppliercode='".$suppliercode."' WHERE suppliercode='".$scode['suppliercode']."'");
				//update header sale return
				$db->query("UPDATE headerbuyr SET suppliercode='".$suppliercode."' WHERE suppliercode='".$scode['suppliercode']."'");
			}
		}
	}
	
	function updatedetailsupplier($supplieraddress,$suppliercontactperson,$supplierpostalcode,$supplierareacode,$supplierstatecode,$suppliercountrycode,$supplierphone,$supplierfax,$suppliermobilenumber,$supplierstatus){
		global $db;
		
		if (!empty($this->id) && !empty($this->dtid)){
			if (!empty($supplieraddress)){
				$updates["address"] = $supplieraddress;
			}
			if (!empty($supplierareacode)){
				$updates["areacode"] = $supplierareacode;
			}
			if (!empty($supplierstatecode)){
				$updates["statecode"] = $supplierstatecode;
			}
			if (!empty($suppliercountrycode)){
				$updates["countrycode"] = $suppliercountrycode;
			}
			if (!empty($supplierpostalcode)){
				$updates["postalcode"] = $supplierpostalcode;
			}
			if (!empty($supplierphone)){
				$updates["phone"] = $supplierphone;
			}
			if (!empty($supplierfax)){
				$updates["fax"] = $supplierfax;
			}
			if (!empty($suppliercontactperson)){
				$updates["contactperson"] = $suppliercontactperson;
			}
			if (!empty($suppliermobilenumber)){
				$updates["mobilenumber"] = $suppliermobilenumber;
			}
			if (isset($supplierstatus)){
				$updates["status"] = $supplierstatus;
			}
			
			if (sizeof($updates) > 0){
				$db->update("detailsupplier",$updates,"detailsplid='".$this->dtid."'");
			}
		}
	}
	
	function copyToCustomer($userid){
		global $db;
		
		if (!empty($this->id)){
			$getheader = $this->getsupplierDetail();
			if (!empty($getheader['supplierid'])){
				$checkcustcode = $db->fetch_one("SELECT * FROM customer WHERE customercode='".$getheader['suppliercode']."'");
				if (!empty($checkcustcode['customerid'])){
					return false;
				}
				$insert['customercode'] = $getheader['suppliercode'];
				$insert['customername'] = $getheader['suppliername'];
				$insert['credit'] = 0;
				$insert['createddate'] = time();
				$insert['createdby'] = $userid;
				$insert['lastedited'] = time();
				$insert['lasteditedby'] = $userid;
				$insert['status'] = $getheader['status'];
				$lastid = $db->insert("customer",$insert);
				
				$getaddress = $this->getsupplieraddrdetail('all');
				if (sizeof($getaddress) > 0){
					foreach ($getaddress as $gadr){
						unset($gadr['supplierid']);
						unset($gadr['detailsplid']);
						$gadr['customerid'] = $lastid;
						$db->insert("detailcustomer",$gadr);
					}
				}
				return true;
			}
		}
		return false;
	}
	
	function searchsupplier($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'suppliercode LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbsupplier = $db->fetch_all("SELECT * FROM supplier".$sql." ORDER BY suppliercode");
		
		return $dbsupplier;
	}
	function searchsupplierfull($keyword,$field,$page,$getreturn,$searchmode = 'in',$orderfield='',$sortdetail=''){
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
		$innerjoin = ' INNER JOIN detailsupplier db ON s.supplierid = db.supplierid';
		$groupby = ' GROUP BY s.supplierid';
		
		if (isset($keyword)){
			if (sizeof($field) == 0){
				$field = array('suppliername');
			}
			$strinarr = '';
			
			$arrf1 = array_search('suppliercode',$field);
			if ($arrf1 !== false){
				$strinarr = 's.suppliercode LIKE (\''.$addlikes.$keyword[$arrf1].'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf2 = array_search('suppliername',$field);
			if ($arrf2 !== false){
				$strinarr = 's.suppliername LIKE (\''.$addlikes.$keyword[$arrf2].'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf3 = array_search('address',$field);
			if ($arrf3 !== false){
				$strinarr = 'db.address LIKE (\''.$addlikes.$keyword[$arrf3].'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf4 = array_search('contactperson',$field);
			if ($arrf4 !== false){
				$strinarr = 'db.contactperson LIKE (\''.$addlikes.$keyword[$arrf4].'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf5 = array_search('phone',$field);
			if ($arrf5 !== false){
				$strinarr = 'db.phone LIKE (\''.$addlikes.$keyword[$arrf5].'%\')';
				array_push($sqls,$strinarr);
			}
			$arrf6 = array_search('areaname',$field);
			if ($arrf6 !== false){
				$strinarr = 'db.areacode LIKE (\''.$addlikes.$keyword[$arrf6].'%\')';
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
			$orderfield = 's.suppliercode';
		}
		
		$orders = 'ASC';
		if (!empty($sortdetail)){
			$orders = $sortdetail;
		}
		
		if ($getreturn == 'data'){
			if (!empty($orderfield)){
				switch ($orderfield){
					case 'sortsuppliercode': $orderfield = 's.suppliercode'; break;
					case 'sortsuppliername': $orderfield = 's.suppliername'; break;
					case 'sortsupplieradd': $orderfield = 'db.address'; break;
					case 'sortsuppliercperson': $orderfield = 'db.contactperson'; break;
					case 'sortsuppliertelp': $orderfield = 'db.phone'; break;
					case 'sortsuppliercity': $orderfield = 'db.areacode'; break;
					case 'sortsupplierstatus': $orderfield = 's.status'; break;
					case 'sortlastediteddate': $orderfield = 's.lastedited'; break;
					case 'sortlasteditedby': $orderfield = 's.lasteditedby'; break;
					default: $orderfield = 's.suppliercode'; break;
				}
			}
			
			$dbsupplier = $db->fetch_all("SELECT s.* FROM supplier s".$innerjoin.$sql.$groupby." ORDER BY ".$orderfield." ".$orders." ".$limits);
			
			return $dbsupplier;
		}
		else if ($getreturn == 'pagenav'){
			$dbsupplier = $db->query("SELECT * FROM supplier s".$innerjoin.$sql.$groupby);
			$ttrecord = @mysql_num_rows($dbsupplier);
			
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
	
	function deletesupplier(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM supplier WHERE supplierid='".$this->id."'");
			$db->query("DELETE FROM detailsupplier WHERE supplierid='".$this->id."'");
		}
	}
	
	function deletedetailsupplier(){
		global $db;
		
		if (!empty($this->dtid)){			
			$db->query("DELETE FROM detailsupplier WHERE detailsplid='".$this->dtid."'");
		}
	}
	
	function addDebt($debts){
		global $db;
		
		if (!empty($debts)){
			if (!empty($this->id)){
				
				$db->query("UPDATE supplier SET debt=debt+".$debts." WHERE supplierid='".$this->id."'");
			}
			else if (!empty($this->code)){
				$db->query("UPDATE supplier SET debt=debt+".$debts." WHERE suppliercode='".$this->code."'");
			}
		}
	}
	
	function minDebt($debts){
		global $db;
		
		if (!empty($debts)){
			if (!empty($this->id)){
				$db->query("UPDATE supplier SET debt=debt-".$debts." WHERE supplierid='".$this->id."'");
			}
			else if (!empty($this->code)){
				$db->query("UPDATE supplier SET debt=debt-".$debts." WHERE suppliercode='".$this->code."'");
			}
		}
	}
}
?>