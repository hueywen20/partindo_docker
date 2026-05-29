<?php
class country{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($countrycode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'countryid <> \''.$this->id.'\'');
		}
		array_push($sqls,'countrycode = \''.$countrycode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM country".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListcountry($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcountry = $db->fetch_all("SELECT * FROM country".$sql." ORDER BY countryname");
		
		return $dbcountry;
	}
	
	function getcountryDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'countryid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'countrycode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcountry = $db->fetch_one("SELECT * FROM country".$sql." ORDER BY countryname");
		
		return $dbcountry;
	}
	
	function canDeleteCountry(){
		global $db;
		
		if (!empty($this->code)){
			$checks = $db->fetch_one("SELECT * FROM detailsupplier WHERE countrycode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}

			$checks = $db->fetch_one("SELECT * FROM detailcustomer WHERE countrycode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savecountry($countrycode,$countryname,$countrystatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM country WHERE countrycode='".$countrycode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['countryname'] = $countryname;
			$inserts['countrycode'] = $countrycode;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $countrystatus;
			
			return $db->insert("country",$inserts);
		}
	}
	
	function updatecountry($countrycode,$countryname,$countrystatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM country WHERE countrycode='".$countrycode."' AND countryid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$ccode = $this->getcountryDetail();
				
				$updates['countryname'] = $countryname;
				$updates['countrycode'] = $countrycode;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $countrystatus;
				
				$db->update("country",$updates,"countryid='".$this->id."'");
			
				if ($countrycode != $ccode['countrycode']){
					$db->query("UPDATE detailsupplier SET countrycode='".$countrycode."' WHERE countrycode='".$ccode['countrycode']."'");
					$db->query("UPDATE detailcustomer SET countrycode='".$countrycode."' WHERE countrycode='".$ccode['countrycode']."'");
				}

				return true;
			}
		}
	}
	
	function searchcountry($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'countryname LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcountry = $db->fetch_all("SELECT * FROM country".$sql." ORDER BY countrycode");
		
		return $dbcountry;
	}
	
	function deletecountry(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM country WHERE countryid='".$this->id."'");
		}
	}
}
?>