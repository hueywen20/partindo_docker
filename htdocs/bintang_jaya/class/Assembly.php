<?php
class Assembly{
	var $id;
	var $code;
	var $dtid;
	
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
		if (stristr($dtid,"r-")){
			$dtid = str_replace("r-","",$dtid);
		}
		$this->dtid = $db->clean($dtid);
	}
	
	function getAssemblyComponent(){
		global $db;
		
		$sqls = array();
		if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstock = $db->fetch_all("SELECT * FROM detailstockassembly".$sql." ORDER BY stockcodecomponent");
		
		return $dbstock;
	}
	
	function getAssemblyParent($stockcode){
		global $db;
		
		$sqls = array();
		if (!empty($stockcode)){
			array_push($sqls,'stockcodecomponent = \''.$db->clean($stockcode).'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstock = $db->fetch_all("SELECT * FROM detailstockassembly".$sql." ORDER BY dsaid");
		
		return $dbstock;
	}
	
	function saveAssembly(){
		global $db;
		$inserts['stockcode'] = $this->code;
		$inserts['status'] = 1;
		
		$db->insert("stockassembly",$inserts);
	}
	
	function saveDetailAssembly($stockcodecomponent,$partno,$stockname,$brandcode,$typecode,$sccquantityf,$sccunitquantityf,$sccquantity,$sccunitquantity,$unitcode){
		global $db;
		if (!empty($this->code)){
			$inserts['stockcode'] = $this->code;
			$inserts['stockcodecomponent'] = $stockcodecomponent;
			$inserts['sccname'] = $stockname;
			$inserts['sccpartno'] = $partno;
			$inserts['sccbrandcode'] = $brandcode;
			$inserts['scctypecode'] = $typecode;
			$inserts['sccquantityf'] = $sccquantityf;
			$inserts['sccunitquantityf'] = $sccunitquantityf;
			$inserts['sccquantity'] = $sccquantity;
			$inserts['sccunitquantity'] = $sccunitquantity;
			$inserts['sccunitcode'] = $unitcode;
			
			$db->insert("detailstockassembly",$inserts);
		}
	}
	
	function updateAssembly($newcode){
		global $db;
		$updates['stockcode'] = $newcode;
		$updates['status'] = 1;
		
		$db->update("stockassembly",$updates,"stockcode='".$this->code."'");
	}
	
	function updateDetailAssembly($newcode,$stockcodecomponent,$partno,$stockname,$brandcode,$typecode,$sccquantityf,$sccunitquantityf,$sccquantity,$sccunitquantity,$unitcode){
		global $db;
		if (!empty($this->dtid)){
			$updates['stockcode'] = $newcode;
			$updates['stockcodecomponent'] = $stockcodecomponent;
			$updates['sccname'] = $stockname;
			$updates['sccpartno'] = $partno;
			$updates['sccbrandcode'] = $brandcode;
			$updates['scctypecode'] = $typecode;
			$updates['sccquantityf'] = $sccquantityf;
			$updates['sccunitquantityf'] = $sccunitquantityf;
			$updates['sccquantity'] = $sccquantity;
			$updates['sccunitquantity'] = $sccunitquantity;
			$updates['sccunitcode'] = $unitcode;
			
			$db->update("detailstockassembly",$updates,"dsaid='".$this->dtid."'");
		}
	}
	
	function deleteComponent(){
		global $db;
		if (!empty($this->dtid)){
			$db->query("DELETE FROM detailstockassembly WHERE dsaid='".$this->dtid."'");
		}
	}
	
	function deleteAssembly(){
		global $db;
		if (!empty($this->code)){
			$db->query("DELETE FROM detailstockassembly WHERE stockcode='".$this->code."'");
			$db->query("DELETE FROM stockassembly WHERE stockcode='".$this->code."'");
		}
	}
	
	function searchAssembly($keyword,$field,$getreturn,$page = -1,$limits = -1){
		global $db,$general;
		
		$addlimit = '';
		if ($page != -1){
			$addlimit = ' LIMIT '.($page-1)*$general['showperpage'].','.$general['showperpage'];
		}
		
		if ($limits != -1){
			$addlimit = ' LIMIT '.$limits;
		}
		
		$sqls = array();
		if (isset($keyword)){
			$strinarr = '';
			$innerjoin = '';
			$groupby = '';
			
			$arrsc = array_search('stockcode',$field);
			if ($arrsc !== false){
				$strinarr = 's.stockcode LIKE (\''.$db->clean($keyword[$arrsc]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrgn = array_search('generalname',$field);
			if ($arrgn !== false){
				$strinarr = 's.generalname LIKE (\''.$db->clean($keyword[$arrgn]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrbr = array_search('componentcode',$field);
			if ($arrbr !== false){
				$innerjoin = ' INNER JOIN detailstockassembly b ON s.stockcode = b.stockcode';
				$strinarr = 'b.stockcodecomponent LIKE (\''.$db->clean($keyword[$arrbr]).'%\')';
				$groupby = ' GROUP BY s.stockid';
				array_push($sqls,$strinarr);
			}
			$arrbrs = array_search('componentname',$field);
			if ($arrbrs !== false){
				$innerjoin = ' INNER JOIN detailstockassembly b ON s.stockcode = b.stockcode';
				$strinarr = 'b.sccname LIKE (\''.$db->clean($keyword[$arrbrs]).'%\')';
				$groupby = ' GROUP BY s.stockid';
				array_push($sqls,$strinarr);
			}			
		}
		$orderfield = 's.stockcode';
		
		array_push($sqls,'s.assembly = 1');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		if ($getreturn == 'data'){
			$dbstock = $db->fetch_all("SELECT s.* FROM stock s".$innerjoin.$sql.$groupby." ORDER BY ".$orderfield.$addlimit);
			
			return $dbstock;
		}
		else if ($getreturn == 'pagenav'){
			$dbstock = $db->fetch_one("SELECT COUNT(s.stockid) AS totalrecord FROM stock s".$innerjoin.$sql.$groupby);
			
			if ($dbstock['totalrecord'] > 0){
				$totalrecord = $dbstock['totalrecord'];
				$totalpage = ceil($totalrecord / $general['showperpage']);
				$startrecord = ($page - 1) * $general['showperpage'] + 1;
				$endrecord = $startrecord + $general['showperpage'] - 1;
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
		
		return $dbstock;
	}
}
?>