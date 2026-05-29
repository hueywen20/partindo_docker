<?php
class Operational{
	var $id;
	var $monthyear;
	var $dtid;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setMonthYear($monthyear){
		$this->monthyear = $monthyear;
	}
	
	function setDetailId($dtid){
		$this->dtid = $dtid;
	}
	
	function getHeaderOperational(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'opid = \''.$this->id.'\'');
		}
		else if (!empty($this->monthyear)){
			array_push($sqls,'monthyear = \''.$this->monthyear.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dboperational = $db->fetch_one("SELECT * FROM headeroperational".$sql);
		
		return $dboperational;		
	}
	
	function getDetailOperational(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'d.opid = \''.$this->id.'\'');
		}
		else if (!empty($this->monthyear)){
			array_push($sqls,'h.monthyear = \''.$this->monthyear.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dboperational = $db->fetch_all("SELECT d.*, h.totals, h.monthyear FROM detailoperational d INNER JOIN headeroperational h ON d.opid = h.opid".$sql." ORDER BY d.doid");
		
		return $dboperational;
	}
	
	function saveHeaderOperational($monthyear,$totals,$userid){
		global $db;
		
		$inserts['monthyear'] = $monthyear;
		$inserts['totals'] = $totals;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$inserts['status'] = 1;
		
		return $db->insert("headeroperational",$inserts);
	}
	
	function saveDetailOperational($notes,$total){
		global $db;
		
		if (!empty($this->id)){
			$inserts['opid'] = $this->id;
			$inserts['notes'] = $notes;
			$inserts['total'] = $total;
			
			$db->insert("detailoperational",$inserts);
		}
	}
	
	function updateHeaderOperational($totals,$userid){
		global $db;
		
		if (!empty($this->monthyear)){
			$updates['totals'] = $totals;
			$updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
			
			$db->update("headeroperational",$updates,"monthyear='".$this->monthyear."'");
		}
	}
	
	function updateDetailOperational($notes,$total){
		global $db;

		if (!empty($this->dtid)){
			$updates['notes'] = $notes;
			$updates['total'] = $total;
			$db->update("detailoperational",$updates,"doid='".$this->dtid."'");
		}
	}
	
	function deleteDetailOperational(){
		global $db;
		
		if (!empty($this->dtid)){
			$db->query("DELETE FROM detailoperational WHERE doid='".$this->dtid."'");
		}
	}
	
	function deleteOperational(){
		global $db;
		
		if (!empty($this->monthyear)){
			$getopid = $this->getHeaderOperational();
			if (!empty($getopid['opid'])){
				$db->query("DELETE FROM detailoperational WHERE opid='".$getopid['opid']."'");
			}
			$db->query("DELETE FROM headeroperational WHERE monthyear='".$this->monthyear."'");
		}
	}
}
?>