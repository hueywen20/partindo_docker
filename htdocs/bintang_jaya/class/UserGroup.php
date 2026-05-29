<?php
class UserGroup{
	var $id;
	var $code;
	
	function setId($id){
		$this->id = $id;
	}
	
	function getListUserGroup($mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbusergroup = $db->fetch_all("SELECT * FROM usergroup".$sql." ORDER BY title");
		
		return $dbusergroup;
	}
	
	function getUserGroupDetail(){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'usergroupid = \''.$this->id.'\'');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbusergroup = $db->fetch_one("SELECT * FROM usergroup".$sql);
		
		return $dbusergroup;
	}
	
	function checkUserGroupExist($title){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'usergroupid <> \''.$this->id.'\'');
		}
		array_push($sqls,'title = \''.$title.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM usergroup".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		return false;
	}
	
	function saveUserGroup($title,$userid,$access,$status){
		global $db;
		
		$inserts['title'] = $title;
		$inserts['access'] = $access;
		$inserts['createddate'] = time();
		$inserts['createdby'] = $userid;
		$inserts['lastedited'] = time();
		$inserts['lasteditedby'] = $userid;
		$inserts['status'] = $status;
		
		return $db->insert("usergroup",$inserts);
	}
	
	function updateUserGroup($title,$userid,$access,$status){
		global $db;
		
		if (!empty($this->id)){
			$updates['title'] = $title;
			$updates['access'] = $access;
			$updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
			$updates['status'] = $status;
			
			$db->update("usergroup",$updates,"usergroupid='".$this->id."'");
			return true;
		}
	}
	
	function searchUserGroup($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'title LIKE (\'%'.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbusergroup = $db->fetch_all("SELECT * FROM usergroup".$sql." ORDER BY title");
		
		return $dbusergroup;
	}
	
	function deleteUserGroup(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM usergroup WHERE usergroupid='".$this->id."'");
		}
	}
}
?>