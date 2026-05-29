<?php
class stgr{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($stgrcode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'stgrid <> \''.$this->id.'\'');
		}
		array_push($sqls,'stgrcode = \''.$stgrcode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM stockgroup".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListstgr($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstgr = $db->fetch_all("SELECT * FROM stockgroup".$sql." ORDER BY stgrcode");
		
		return $dbstgr;
	}
	
	function getstgrDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'stgrid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'stgrcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstgr = $db->fetch_one("SELECT * FROM stockgroup".$sql." ORDER BY stgrname");
		
		return $dbstgr;
	}
	
	function canDeleteStockGroup(){
		global $db;
		
		if (!empty($this->code)){
			$checks = $db->fetch_one("SELECT * FROM stock WHERE stgrcode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savestgr($stgrcode,$stgrname,$stockgrouptatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM stockgroup WHERE stgrcode='".$stgrcode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['stgrname'] = $stgrname;
			$inserts['stgrcode'] = $stgrcode;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $stockgrouptatus;
			
			return $db->insert("stockgroup",$inserts);
		}
	}
	
	function updatestgr($stgrcode,$stgrname,$stockgrouptatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM stockgroup WHERE stgrcode='".$stgrcode."' AND stgrid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$scode = $this->getstgrDetail();
				
				$updates['stgrname'] = $stgrname;
				$updates['stgrcode'] = $stgrcode;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $stockgrouptatus;
				
				$db->update("stockgroup",$updates,"stgrid='".$this->id."'");
			
				if ($stgrcode != $scode['stgrcode']){
					$db->query("UPDATE stock SET stgrcode='".$stgrcode."' WHERE stgrcode='".$scode['stgrcode']."'");
				}
				
				return true;
			}
		}
	}
	
	function searchstgr($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'stgrcode LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstgr = $db->fetch_all("SELECT * FROM stockgroup".$sql." ORDER BY stgrcode");
		
		return $dbstgr;
	}
	
	function deletestgr(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM stockgroup WHERE stgrid='".$this->id."'");
		}
	}
}
?>