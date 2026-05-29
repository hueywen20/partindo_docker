<?php
class DeAssembly{
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
	
	function checkcodeexist($stockcode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'sdaid <> \''.$this->id.'\'');
		}
		array_push($sqls,'stockcode = \''.$db->clean($stockcode).'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM stockdeassembly".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getDeAssembly(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'sdaid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbdeasm = $db->fetch_one("SELECT * FROM stockdeassembly".$sql);
		
		return $dbdeasm;
	}
	
	function getDeAssemblyComponent(){
		global $db;
		
		$sqls = array();
		if (!empty($this->code)){
			array_push($sqls,'stockcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstock = $db->fetch_all("SELECT * FROM detailstockdeassembly".$sql." ORDER BY dsdaid");
		
		return $dbstock;
	}
	
	function getDeAssemblyDetailComponent(){
		global $db;
		
		$sqls = array();
		if (!empty($this->dtid)){
			array_push($sqls,'dsdaid = \''.$this->dtid.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbstock = $db->fetch_one("SELECT * FROM detailstockdeassembly".$sql);
		
		return $dbstock;
	}
	
	function getDeAssemblyParent($deassemblypartcode){
		global $db;
		
		$returnarray = array();
		if (!empty($deassemblypartcode)){
			$dbapc = $db->fetch_one("SELECT * FROM detailstockdeassembly WHERE stockcodecomponent='".$db->clean($deassemblypartcode)."'");
			$returnarray = $dbapc;
		}
		return $returnarray;
	}
	
	function saveDeAssembly(){
		global $db;
		$inserts['stockcode'] = $this->code;
		$inserts['status'] = 1;
		
		return $db->insert("stockdeassembly",$inserts);
	}
	
	function saveDetailDeAssembly($stockcodecomponent,$partno,$stockname,$brandcode,$typecode,$sccquantityf,$sccunitquantityf,$sccquantity,$sccunitquantity,$unitcode){
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
			
			$db->insert("detailstockdeassembly",$inserts);
			
			$db->query("UPDATE stock SET assembly = 2 WHERE stockcode = '".$db->clean($stockcodecomponent)."'");
		}
	}
	
	function updateDeAssembly($newcode){
		global $db;
		$updates['stockcode'] = $newcode;
		$updates['status'] = 1;
		
		$db->update("stockdeassembly",$updates,"stockcode='".$this->code."'");
	}
	
	function updateDetailDeAssembly($newcode,$stockcodecomponent,$partno,$stockname,$brandcode,$typecode,$sccquantityf,$sccunitquantityf,$sccquantity,$sccunitquantity,$unitcode){
		global $db;
		if (!empty($this->dtid)){
			$getcomponent = $this->getDeAssemblyDetailComponent();
			
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
			
			$db->update("detailstockdeassembly",$updates,"dsdaid='".$this->dtid."'");
			
			$db->query("UPDATE stock SET assembly = 2 WHERE stockcode = '".$db->clean($stockcodecomponent)."'");
		}
	}
	
	function deleteComponent(){
		global $db;
		if (!empty($this->dtid)){
			$getcomponent = $this->getDeAssemblyDetailComponent();
			if (sizeof($getcomponent) > 0){
				$db->query("UPDATE stock SET assembly = 0 WHERE stockcode = '".$db->clean($getcomponent['stockcodecomponent'])."'");
			}
			
			$db->query("DELETE FROM detailstockdeassembly WHERE dsdaid='".$this->dtid."'");
		}
	}
	
	function deleteDeAssembly(){
		global $db;
		if (!empty($this->code)){
			$getcomponent = $this->getDeAssemblyComponent();
			if (sizeof($getcomponent) > 0){
				foreach ($getcomponent as $gcm){
					$db->query("UPDATE stock SET assembly = 0 WHERE stockcode = '".$db->clean($gcm['stockcodecomponent'])."'");
				}
			}
		
			$db->query("DELETE FROM detailstockdeassembly WHERE stockcode='".$this->code."'");
			$db->query("DELETE FROM stockdeassembly WHERE stockcode='".$this->code."'");
		}
	}
	
	function searchDeAssembly($keyword,$field,$getreturn,$page = -1,$limits = -1){
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
				$strinarr = 'sk.generalname LIKE (\''.$db->clean($keyword[$arrgn]).'%\')';
				array_push($sqls,$strinarr);
			}
			$arrbr = array_search('componentcode',$field);
			if ($arrbr !== false){
				$innerjoin = ' INNER JOIN detailstockdeassembly b ON s.stockcode = b.stockcode';
				$strinarr = 'b.stockcodecomponent LIKE (\''.$db->clean($keyword[$arrbr]).'%\')';
				$groupby = ' GROUP BY sk.stockid';
				array_push($sqls,$strinarr);
			}
			$arrbrs = array_search('componentname',$field);
			if ($arrbrs !== false){
				$innerjoin = ' INNER JOIN detailstockdeassembly b ON s.stockcode = b.stockcode';
				$strinarr = 'b.sccname LIKE (\''.$db->clean($keyword[$arrbrs]).'%\')';
				$groupby = ' GROUP BY sk.stockid';
				array_push($sqls,$strinarr);
			}			
		}
		$orderfield = 's.stockcode';
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		if ($getreturn == 'data'){
			$dbstock = $db->fetch_all("SELECT s.*, sk.generalname FROM stockdeassembly s INNER JOIN stock sk ON s.stockcode = sk.stockcode".$innerjoin.$sql.$groupby." ORDER BY ".$orderfield.$addlimit);
			
			return $dbstock;
		}
		else if ($getreturn == 'pagenav'){
			$dbstock = $db->fetch_one("SELECT COUNT(s.stockid) AS totalrecord FROM stockdeassembly s INNER JOIN stock sk ON s.stockcode = sk.stockcode".$innerjoin.$sql.$groupby);
			
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