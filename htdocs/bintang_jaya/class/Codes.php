<?php
class Codes{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function checkCodeTargetExist($targets){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'id <> \''.$this->id.'\'');
		}
		array_push($sqls,'targets = \''.$targets.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM codes".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function checkCodeReplacementExist($replacements,$usefield = 'replacements'){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'id <> \''.$this->id.'\'');
		}
		if (empty($usefield)){
			$usefield = 'replacements';
		}
		array_push($sqls,$usefield.' = \''.$replacements.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM codes".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function checkCodeOrdersExist($orders){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'id <> \''.$this->id.'\'');
		}
		array_push($sqls,'orders = \''.$orders.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM codes".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}

	function getListCodes(){
		global $db;
		
		$sqls = array();
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcodes = $db->fetch_all("SELECT * FROM codes".$sql." ORDER BY orders");
		
		return $dbcodes;
	}

	function reOrderCodes(){
		global $db;
		
		$allcodes = $this->getListCodes();
		if (sizeof($allcodes) > 0){
			$countorders = 1;
			foreach ($allcodes as $acd){
				$db->query("UPDATE codes SET orders='".$countorders."' WHERE id='".$acd['id']."'");
				$countorders++;
			}
		}
	}
	
	function getCodesDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'id = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbcodes = $db->fetch_one("SELECT * FROM codes".$sql);
		
		return $dbcodes;
	}
	
	function saveCodes($targets,$replacements,$replacementsale,$orders,$userid){
		global $db;
		
		$inserts['targets'] = $targets;
		$inserts['replacements'] = strtoupper($replacements);
		$inserts['replacements_sale'] = strtoupper($replacementsale);
		$inserts['orders'] = $orders;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		
		return $db->insert("codes",$inserts);
	}
	
	function updateCodes($targets,$replacements,$replacementsale,$orders,$userid){
		global $db;
		
		if (!empty($this->id)){
			$updates['targets'] = $targets;
			$updates['replacements'] = strtoupper($replacements);
			$updates['replacements_sale'] = strtoupper($replacementsale);
			$updates['orders'] = $orders;
			$updates['createddate'] = time();
			$updates['createdby'] = $userid;
			$updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
				
			$db->update("codes",$updates,"id='".$this->id."'");
		}
	}
	
	function deleteCodes(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM codes WHERE id='".$this->id."'");
		}
	}
	
	function convertcodes($value,$usefield = 'replacements',$extras = false){
		global $general;
		$arrc = $this->getListCodes();
		if ($general['defaultnumbering']){
			$results = strval($value);
			$results = str_replace(".00","",$results);
			$listcd = array();
			if (sizeof($arrc) > 0){
				foreach ($arrc as $ac){
					array_push($listcd,$ac['targets']);
				}
			}
			if ($extras){
				for ($ixp = 0; $ixp < strlen($results); $ixp++){
					if (!in_array($results[$ixp],$listcd)){
						break;
					}
				}
				$excluded = substr($results,$ixp);
				$results = substr($results,0,$ixp);
			}
			$results = strrev($results);
			if (sizeof($arrc) > 0){
				foreach ($arrc as $ac){
					$results = str_replace($ac['targets'],$ac[$usefield],$results);
				}
			}
			$results = strrev($results).$excluded;
		}
		else{
			$results = number_format($value,0,",",".");
		}
		return $results;
	}
	
	function deconvertcodes($value,$usefield = 'replacements'){
		$results = strval($value);
		$arrc = $this->getListCodes();
		if (sizeof($arrc) > 0){
			foreach ($arrc as $ac){
				$results = str_replace($ac[$usefield],$ac['targets'],$results);
			}
		}
		return $results;
	}
}
?>