<?php
class User{
	var $id;

	function setId($id){
		$this->id = $id;
	}
	
	function getListUser(){
		global $db;
		
		$dbuser = $db->fetch_all("SELECT * FROM user".$sql." ORDER BY username");
		
		return $dbuser;
	}
	
	function login($username, $password){
		global $db;
		$data = $db->fetch_one("SELECT u.* FROM user u INNER JOIN usergroup ug ON u.usergroupid = ug.usergroupid WHERE u.username = '" . $username . "' AND ug.status = 1");

		$encryptedPassword = $this->encryptPassword($password, $data['createddate'], $data['createdby']);

		if (strcmp($data['pass'], $encryptedPassword) == 0){
			$db->query("UPDATE online SET userid='".$data['id']."' WHERE cookieid='".$_COOKIE['mycookie']."'");
			return true;
		}
		else if (strcmp($data['morepass'], $encryptedPassword) == 0){
			$db->query("UPDATE online SET userid='".$data['id']."', status=1 WHERE cookieid='".$_COOKIE['mycookie']."'");
			return true;
		}
		else{
			return false;
		}
	}
	
	function getUserDetail(){
		global $db;
		if (!empty($this->id)){
			$datauser = $db->fetch_one("SELECT * FROM user WHERE id='".$this->id."'");
		}
		return $datauser;
	}
	
	function checkUserExist($username){
		global $db;
		
		$sqls = array();
		if (!empty($this->id)){
			array_push($sqls,'id <> \''.$this->id.'\'');
		}
		array_push($sqls,'username = \''.$username.'\'');
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		$checkexist = $db->fetch_one("SELECT * FROM user".$sql);
		if (sizeof($checkexist) > 0){
			return true;
		}
		return false;
	}
	
	function logout(){
		global $db;
		$db->query("UPDATE online SET userid=0, status=0 WHERE cookieid='".$_COOKIE['mycookie']."'");
	}

	function createNewUser($username, $usergroupid, $password, $morepassword, $name, $createdBy){
		global $db;
		$createdDate = time();

		$encryptedPassword = $this->encryptPassword($password, $createdDate, $createdBy);
		$encryptedPasswordmore = $this->encryptPassword($morepassword, $createdDate, $createdBy);
		
		$inserts['usergroupid'] = $usergroupid;
		$inserts['username'] = $username;
		$inserts['pass'] = $encryptedPassword;
		$inserts['morepass'] = $encryptedPasswordmore;
		$inserts['name'] = $name;
		$inserts['createddate'] = $createdDate;
		$inserts['createdby'] = $createdBy;
		$inserts['lastedited'] = $createdDate;
		$inserts['lasteditedby'] = $createdBy;
		return $db->insert('user',$inserts);
		
	}

	function encryptPassword($password, $createdDate, $createdBy){
		return md5($password . $createdDate . $createdBy);
	}

	function updateUser($username, $usergroupid, $password, $morepassword, $name, $userid){
		global $db;
		if (!empty($this->id)){
			$getud = $this->getUserDetail();
		
			if (!empty($password)){
				$encryptedPassword = $this->encryptPassword($password, $getud['createddate'], $getud['createdby']);
				$updates['pass'] = $encryptedPassword;
			}
		
			if (!empty($morepassword)){
				$encryptedPasswordmore = $this->encryptPassword($morepassword, $getud['createddate'], $getud['createdby']);
				$updates['morepass'] = $encryptedPasswordmore;
			}
			
			$updates['usergroupid'] = $usergroupid;
			$updates['username'] = $username;
			$updates['name'] = $name;
			$updates['lastedited'] = time();
			$updates['lasteditedby'] = $userid;
			$db->update('user',$updates,'id=\''.$this->id.'\'');
		}
	}
	
	function searchUser($keyword,$mode){
		global $db;
		
		$sqls = array();
		if ($mode == 'partial'){
			array_push($sqls,'status = 1');
		}
		
		if (!empty($keyword)){
			array_push($sqls,'username LIKE (\'%'.$keyword.'%\')');
		}
		
		$sql = '';
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		$dbusergroup = $db->fetch_all("SELECT * FROM user".$sql." ORDER BY username");
		
		return $dbusergroup;
	}
	
	function deleteUser(){
		global $db;
		
		if (!empty($this->id)){			
			$db->query("DELETE FROM user WHERE id='".$this->id."'");
		}
	}
}
?>