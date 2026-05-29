<?php
class state{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($statecode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'stateid <> \''.$this->id.'\'');
		}
		array_push($sqls,'statecode = \''.$statecode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM state".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListstate($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstate = $db->fetch_all("SELECT * FROM state".$sql." ORDER BY statename");
		
		return $dbstate;
	}
	
	function getstateDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'stateid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'statecode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstate = $db->fetch_one("SELECT * FROM state".$sql." ORDER BY statename");
		
		return $dbstate;
	}
	
	function canDeleteState(){
		global $db;
		
		if (!empty($this->code)){
			$checks = $db->fetch_one("SELECT * FROM detailsupplier WHERE statecode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}

			$checks = $db->fetch_one("SELECT * FROM detailcustomer WHERE statecode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savestate($statecode,$statename,$statestatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM state WHERE statecode='".$statecode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['statename'] = $statename;
			$inserts['statecode'] = $statecode;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $statestatus;
			
			return $db->insert("state",$inserts);
		}
	}
	
	function updatestate($statecode,$statename,$statestatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM state WHERE statecode='".$statecode."' AND stateid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$scode = $this->getstateDetail();
				
				$updates['statename'] = $statename;
				$updates['statecode'] = $statecode;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $statestatus;
				
				$db->update("state",$updates,"stateid='".$this->id."'");
			
				if ($statecode != $scode['statecode']){
					$db->query("UPDATE detailsupplier SET statecode='".$statecode."' WHERE statecode='".$scode['statecode']."'");
					$db->query("UPDATE detailcustomer SET statecode='".$statecode."' WHERE statecode='".$scode['statecode']."'");
				}

				return true;
			}
		}
	}
	
	function searchstate($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'statename LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstate = $db->fetch_all("SELECT * FROM state".$sql." ORDER BY statecode");
		
		return $dbstate;
	}
	
	function deletestate(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM state WHERE stateid='".$this->id."'");
		}
	}
}
?>