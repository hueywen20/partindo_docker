<?php
class area{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($areacode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'areaid <> \''.$this->id.'\'');
		}
		array_push($sqls,'areacode = \''.$areacode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM area".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}

	function getListarea($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbarea = $db->fetch_all("SELECT * FROM area".$sql." ORDER BY areaname");
		
		return $dbarea;
	}
	
	function getareaDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'areaid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'areacode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbarea = $db->fetch_one("SELECT * FROM area".$sql." ORDER BY areaname");
		
		return $dbarea;
	}
	
	function canDeleteArea(){
		global $db;
		
		if (!empty($this->code)){
			$checks = $db->fetch_one("SELECT * FROM detailsupplier WHERE areacode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			
			$checks = $db->fetch_one("SELECT * FROM detailcustomer WHERE areacode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function savearea($areacode,$areaname,$areastatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM area WHERE areacode='".$areacode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['areaname'] = $areaname;
			$inserts['areacode'] = $areacode;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $areastatus;
			
			return $db->insert("area",$inserts);
		}
	}
	
	function updatearea($areacode,$areaname,$areastatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM area WHERE areacode='".$areacode."' AND areaid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$acode = $this->getareaDetail();
				
				$updates['areaname'] = $areaname;
				$updates['areacode'] = $areacode;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $areastatus;
				
				$db->update("area",$updates,"areaid='".$this->id."'");
			
				if ($areacode != $acode['areacode']){
					$db->query("UPDATE detailsupplier SET areacode='".$areacode."' WHERE areacode='".$acode['areacode']."'");
					$db->query("UPDATE detailcustomer SET areacode='".$areacode."' WHERE areacode='".$acode['areacode']."'");
				}

				return true;
			}
		}
	}
	
	function searcharea($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'areaname LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbarea = $db->fetch_all("SELECT * FROM area".$sql." ORDER BY areacode");
		
		return $dbarea;
	}
	
	function deletearea(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM area WHERE areaid='".$this->id."'");
		}
	}
}
?>