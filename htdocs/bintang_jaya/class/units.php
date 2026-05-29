<?php
class unit{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setCode($code){
		$this->code = $code;
	}
	
	function checkcodeexist($unitcode){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'unitid <> \''.$this->id.'\'');
		}
		array_push($sqls,'unitcode = \''.$unitcode.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM units".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getListunit($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbunit = $db->fetch_all("SELECT * FROM units".$sql." ORDER BY unitcode");
		
		return $dbunit;
	}
	
	function getunitDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'unitid = \''.$this->id.'\'');
		}
		else if (!empty($this->code)){
			array_push($sqls,'unitcode = \''.$this->code.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbunit = $db->fetch_one("SELECT * FROM units".$sql." ORDER BY unitcode");
		
		return $dbunit;
	}
	
	function saveunit($unitcode,$funit,$lunit,$cvalue,$unitstatus,$userid){
		global $db;
		
		$checkexist = $db->fetch_one("SELECT * FROM units WHERE unitcode='".$unitcode."'");
		if (sizeof($checkexist) > 0){
			return false;
		}
		else{
			$inserts['unitcode'] = $unitcode;
			$inserts['funit'] = $funit;
			$inserts['lunit'] = $lunit;
			$inserts['cvalue'] = $cvalue;
			$inserts['createddate'] = time();
			$inserts['createdby'] = $userid;
            $inserts['lastedited'] = time();
			$inserts['lasteditedby'] = $userid;
			$inserts['status'] = $unitstatus;
			
			return $db->insert("units",$inserts);
		}
	}
	
	function updateunit($unitcode,$funit,$lunit,$cvalue,$unitstatus,$userid){
		global $db;
		
		if (!empty($this->id)){
			$checkexist = $db->fetch_one("SELECT * FROM units WHERE unitcode='".$unitcode."' AND unitid <> '".$this->id."'");
			if (sizeof($checkexist) > 0){
				return false;
			}
			else{
				$ucode = $this->getunitDetail();

				$updates['unitcode'] = $unitcode;
				$updates['funit'] = $funit;
				$updates['lunit'] = $lunit;
				$updates['cvalue'] = $cvalue;
				$updates['lastedited'] = time();
				$updates['lasteditedby'] = $userid;
				$updates['status'] = $unitstatus;
				
				$db->update("units",$updates,"unitid='".$this->id."'");
			
				if ($funit != $ucode['funit']){
					$db->query("UPDATE detailbuy SET unitquantityf='".$funit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['funit']."'");
					$db->query("UPDATE detailsale SET unitquantityf='".$funit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['funit']."'");
					$db->query("UPDATE detailbuyr SET unitquantityf='".$funit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['funit']."'");
					$db->query("UPDATE detailsaler SET unitquantityf='".$funit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['funit']."'");
					$db->query("UPDATE detailadjustin SET unitquantityf='".$funit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['funit']."'");
					$db->query("UPDATE detailadjustout SET unitquantityf='".$funit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['funit']."'");
					$db->query("UPDATE detailstockassembly SET sccunitquantityf='".$funit."' WHERE sccunitcode='".$ucode['unitcode']."' AND sccunitquantityf='".$ucode['funit']."'");
					$db->query("UPDATE detailstockdeassembly SET sccunitquantityf='".$funit."' WHERE sccunitcode='".$ucode['unitcode']."' AND sccunitquantityf='".$ucode['funit']."'");
				}
				if ($lunit != $ucode['lunit']){
					$db->query("UPDATE detailbuy SET unitquantity='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantity='".$ucode['lunit']."'");
					$db->query("UPDATE detailsale SET unitquantity='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantity='".$ucode['lunit']."'");
					$db->query("UPDATE detailbuyr SET unitquantity='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantity='".$ucode['lunit']."'");
					$db->query("UPDATE detailsaler SET unitquantity='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantity='".$ucode['lunit']."'");
					$db->query("UPDATE detailadjustin SET unitquantity='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantity='".$ucode['lunit']."'");
					$db->query("UPDATE detailadjustout SET unitquantity='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantity='".$ucode['lunit']."'");
					$db->query("UPDATE detailstockassembly SET sccunitquantity='".$lunit."' WHERE sccunitcode='".$ucode['unitcode']."' AND sccunitquantity='".$ucode['lunit']."'");
					$db->query("UPDATE detailstockdeassembly SET sccunitquantity='".$lunit."' WHERE sccunitcode='".$ucode['unitcode']."' AND sccunitquantity='".$ucode['lunit']."'");

					$db->query("UPDATE detailbuy SET unitquantityf='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['lunit']."'");
					$db->query("UPDATE detailsale SET unitquantityf='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['lunit']."'");
					$db->query("UPDATE detailbuyr SET unitquantityf='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['lunit']."'");
					$db->query("UPDATE detailsaler SET unitquantityf='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['lunit']."'");
					$db->query("UPDATE detailadjustin SET unitquantityf='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['lunit']."'");
					$db->query("UPDATE detailadjustout SET unitquantityf='".$lunit."' WHERE unitcode='".$ucode['unitcode']."' AND unitquantityf='".$ucode['lunit']."'");
					$db->query("UPDATE detailstockassembly SET sccunitquantityf='".$lunit."' WHERE sccunitcode='".$ucode['unitcode']."' AND sccunitquantityf='".$ucode['lunit']."'");
					$db->query("UPDATE detailstockdeassembly SET sccunitquantityf='".$lunit."' WHERE sccunitcode='".$ucode['unitcode']."' AND sccunitquantityf='".$ucode['lunit']."'");
				}
				if ($unitcode != $ucode['unitcode']){
					$db->query("UPDATE stock SET unitcode='".$unitcode."' WHERE unitcode='".$ucode['unitcode']."'");
					
					$db->query("UPDATE detailbuy SET unitcode='".$unitcode."' WHERE unitcode='".$ucode['unitcode']."'");
					$db->query("UPDATE detailsale SET unitcode='".$unitcode."' WHERE unitcode='".$ucode['unitcode']."'");
					$db->query("UPDATE detailbuyr SET unitcode='".$unitcode."' WHERE unitcode='".$ucode['unitcode']."'");
					$db->query("UPDATE detailsaler SET unitcode='".$unitcode."' WHERE unitcode='".$ucode['unitcode']."'");
					$db->query("UPDATE detailadjustin SET unitcode='".$unitcode."' WHERE unitcode='".$ucode['unitcode']."'");
					$db->query("UPDATE detailadjustout SET unitcode='".$unitcode."' WHERE unitcode='".$ucode['unitcode']."'");
					$db->query("UPDATE detailstockassembly SET sccunitcode='".$unitcode."' WHERE sccunitcode='".$ucode['unitcode']."'");
					$db->query("UPDATE detailstockdeassembly SET sccunitcode='".$unitcode."' WHERE sccunitcode='".$ucode['unitcode']."'");
				}

				return true;
			}
		}
	}
	
	function searchunit($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			//array_push($sqls,'funit LIKE (\'%'.$keyword.'%\') OR lunit LIKE (\'%'.$keyword.'%\')');
			array_push($sqls,'unitcode LIKE (\''.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbunit = $db->fetch_all("SELECT * FROM units".$sql." ORDER BY unitcode");
		
		return $dbunit;
	}
	
	function deleteunit(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM units WHERE unitid='".$this->id."'");
		}
	}
}
?>