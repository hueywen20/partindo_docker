<?php
class type{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($typecode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'typeid <> \''.$this->id.'\'');
		}
		array_push($sqls,'typecode = \''.$typecode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM type".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListtype($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbtype = $db->fetch_all("SELECT * FROM type".$sql." ORDER BY typename");
		
		return $dbtype;
	}
	
	function gettypeDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'typeid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'typecode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbtype = $db->fetch_one("SELECT * FROM type".$sql." ORDER BY typename");
		
		return $dbtype;
	}
	
	function canDeleteType(){
		global $db;
		
		if (!empty($this->code)){
			$checks = $db->fetch_one("SELECT * FROM stock WHERE typecode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savetype($typecode,$typename,$typestatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM type WHERE typecode='".$typecode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['typename'] = $typename;
			$inserts['typecode'] = $typecode;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $typestatus;
			
			return $db->insert("type",$inserts);
		}
	}
	
	function updatetype($typecode,$typename,$typestatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM type WHERE typecode='".$typecode."' AND typeid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$tcode = $this->gettypeDetail();

				$updates['typename'] = $typename;
				$updates['typecode'] = $typecode;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $typestatus;
				
				$db->update("type",$updates,"typeid='".$this->id."'");
			
				if ($typecode != $tcode['typecode']){
					$db->query("UPDATE stock SET typecode='".$typecode."' WHERE typecode='".$tcode['typecode']."'");
					
					$db->query("UPDATE detailbuy SET typecode='".$typecode."' WHERE typecode='".$tcode['typecode']."'");
					$db->query("UPDATE detailsale SET typecode='".$typecode."' WHERE typecode='".$tcode['typecode']."'");
					$db->query("UPDATE detailbuyr SET typecode='".$typecode."' WHERE typecode='".$tcode['typecode']."'");
					$db->query("UPDATE detailsaler SET typecode='".$typecode."' WHERE typecode='".$tcode['typecode']."'");
					$db->query("UPDATE detailadjustin SET typecode='".$typecode."' WHERE typecode='".$tcode['typecode']."'");
					$db->query("UPDATE detailadjustout SET typecode='".$typecode."' WHERE typecode='".$tcode['typecode']."'");
					$db->query("UPDATE detailstockassembly SET scctypecode='".$typecode."' WHERE scctypecode='".$tcode['typecode']."'");
					$db->query("UPDATE detailstockdeassembly SET scctypecode='".$typecode."' WHERE scctypecode='".$tcode['typecode']."'");
				}

				return true;
			}
		}
	}
	
	function searchtype($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'typecode LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbtype = $db->fetch_all("SELECT * FROM type".$sql." ORDER BY typecode");
		
		return $dbtype;
	}
	
	function deletetype(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM type WHERE typeid='".$this->id."'");
		}
	}
}
?>