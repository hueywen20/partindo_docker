<?php
class Access{
	var $id;
	var $groups;
	var $aid;
	
	function setId($id){
		$this->id = $id;
	}
	
	function setGroups($groups){
		$this->groups = $groups;
	}
	
	function setAccessId($aid){
		$this->aid = $aid;
	}
	
	function getAccessGroup(){
		global $db;
		
		$dbag = $db->fetch_all("SELECT * FROM access GROUP BY accessgroup ORDER BY accessid");
		return $dbag;
	}
	
	function getListAccess($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		if (!empty($this->groups)){
			array_push($sqls,'accessgroup=\''.$this->groups.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbaccess = $db->fetch_all("SELECT * FROM access".$sql." ORDER BY accessid");
		
		return $dbaccess;
	}
	
	function getAccessDetail(){
		global $db;
		
		if (!empty($this->aid)){
			$dbaccess = $db->fetch_all("SELECT * FROM access WHERE accessid IN (".$this->aid.") ORDER BY menuorder");
			return $dbaccess;
		}
	}
	
	function getUserByAccess($accid){
		global $db;
		
		$dbusers = array();
		if (!empty($accid)){
			$dbusergroup = $db->fetch_all("SELECT * FROM usergroup WHERE access LIKE '".$accid.",%' OR access LIKE '%,".$accid."' OR access LIKE '%,".$accid.",%'");
			$ugtext = '';
			if (sizeof($dbusergroup) > 0){
				$ugarr = array();
				foreach ($dbusergroup as $dug){
					array_push($ugarr,$dug['usergroupid']);
				}
				$ugtext = implode(",",$ugarr);
				
				$dbusers = $db->fetch_all("SELECT * FROM user WHERE usergroupid IN (".$ugtext.")");
			}
		}
		
		return $dbusers;
	}
}
?>