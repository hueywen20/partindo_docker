<?php
class Brand{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($brandcode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'brandid <> \''.$this->id.'\'');
		}
		array_push($sqls,'brandcode = \''.$brandcode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM brand".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListBrand($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbrand = $db->fetch_all("SELECT * FROM brand".$sql." ORDER BY brandname");
		
		return $dbbrand;
	}
	
	function getBrandDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'brandid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'brandcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbrand = $db->fetch_one("SELECT * FROM brand".$sql." ORDER BY brandname");
		
		return $dbbrand;
	}
	
	function canDeleteBrand(){
		global $db;
		
		if (!empty($this->code)){
			$checks = $db->fetch_one("SELECT * FROM stock WHERE brandcode='".$this->code."' LIMIT 1");
			if (sizeof($checks) > 0){
				return false;
			}
			return true;
		}
		return true;
	}
	
	function saveBrand($brandcode,$brandname,$brandstatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM brand WHERE brandcode='".$brandcode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['brandname'] = $brandname;
			$inserts['brandcode'] = $brandcode;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $brandstatus;
			
			return $db->insert("brand",$inserts);
		}
	}
	
	function updateBrand($brandcode,$brandname,$brandstatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM brand WHERE brandcode='".$brandcode."' AND brandid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$bcode = $this->getBrandDetail();
				
				$updates['brandname'] = $brandname;
				$updates['brandcode'] = $brandcode;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $brandstatus;
				
				$db->update("brand",$updates,"brandid='".$this->id."'");
			
				if ($brandcode != $bcode['brandcode']){
					$db->query("UPDATE stock SET brandcode='".$brandcode."' WHERE brandcode='".$bcode['brandcode']."'");
					
					$db->query("UPDATE detailbuy SET brandcode='".$brandcode."' WHERE brandcode='".$bcode['brandcode']."'");
					$db->query("UPDATE detailsale SET brandcode='".$brandcode."' WHERE brandcode='".$bcode['brandcode']."'");
					$db->query("UPDATE detailbuyr SET brandcode='".$brandcode."' WHERE brandcode='".$bcode['brandcode']."'");
					$db->query("UPDATE detailsaler SET brandcode='".$brandcode."' WHERE brandcode='".$bcode['brandcode']."'");
					$db->query("UPDATE detailadjustin SET brandcode='".$brandcode."' WHERE brandcode='".$bcode['brandcode']."'");
					$db->query("UPDATE detailadjustout SET brandcode='".$brandcode."' WHERE brandcode='".$bcode['brandcode']."'");
					$db->query("UPDATE detailstockassembly SET sccbrandcode='".$brandcode."' WHERE sccbrandcode='".$bcode['brandcode']."'");
					$db->query("UPDATE detailstockdeassembly SET sccbrandcode='".$brandcode."' WHERE sccbrandcode='".$bcode['brandcode']."'");
				}
				
				return true;
			}
		}
	}
	
	function searchBrand($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'brandcode LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbbrand = $db->fetch_all("SELECT * FROM brand".$sql." ORDER BY brandcode");
		
		return $dbbrand;
	}
	
	function deleteBrand(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM brand WHERE brandid='".$this->id."'");
		}
	}
}
?>