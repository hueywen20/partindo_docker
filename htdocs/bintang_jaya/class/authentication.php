<?php
	function otentikasi($username,$passwords)
	{
		global $db;
		$row = $db->fetch_one("Select password,usergroupid,salt,userid from user where username='$username'");

		$pl = md5(md5($passwords).$row['salt']);

		if ($pl==$row['password'] && $row['usergroupid'] != '4')
		{
			$db->query("UPDATE online SET userid=0 WHERE userid='".$row['userid']."'");
			$db->query("UPDATE user SET lastlogin=".time()." WHERE userid='".$row['userid']."'");
			$db->query("UPDATE online SET userid=".$row['userid']." WHERE cookieid='".$_COOKIE['mycookie']."'");
			return true;
		}
		return false;
	}

	function getpassword($username)
	{
		global $db;
		$hasil = $db->fetch_one("Select password from user where username='$username'");
		return $hasil[0];
	}
?>