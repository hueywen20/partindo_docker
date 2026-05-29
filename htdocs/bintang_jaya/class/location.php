<?php
class location{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($locationcode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'locationid <> \''.$this->id.'\'');
		}
		array_push($sqls,'locationcode = \''.$locationcode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM location".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListlocation($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dblocation = $db->fetch_all("SELECT * FROM location".$sql." ORDER BY locationname");
		
		return $dblocation;
	}
	
	function getlocationDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'locationid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'locationcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dblocation = $db->fetch_one("SELECT * FROM location".$sql." ORDER BY locationname");
		
		return $dblocation;
	}
	
	function canDeleteLocation(){
		global $db;
		
		if (!empty($this->code)){
			$checks = $db->fetch_one("SELECT * FROM stock WHERE locationcode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savelocation($locationcode,$locationname,$locationstatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM location WHERE locationcode='".$locationcode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['locationname'] = $locationname;
			$inserts['locationcode'] = $locationcode;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $locationstatus;
			
			return $db->insert("location",$inserts);
		}
	}
	
	function updatelocation($locationcode,$locationname,$locationstatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM location WHERE locationcode='".$locationcode."' AND locationid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$lcode = $this->getlocationDetail();
				
				$updates['locationname'] = $locationname;
				$updates['locationcode'] = $locationcode;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $locationstatus;
				
				$db->update("location",$updates,"locationid='".$this->id."'");

				if ($locationcode != $lcode['locationcode']){
					$db->query("UPDATE stock SET locationcode='".$locationcode."' WHERE locationcode='".$lcode['locationcode']."'");
				}
				
				return true;
			}
		}
	}
	
	function searchlocation($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'locationcode LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dblocation = $db->fetch_all("SELECT * FROM location".$sql." ORDER BY locationcode");
		
		return $dblocation;
	}
	
	function deletelocation(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM location WHERE locationid='".$this->id."'");
		}
	}
}
?>