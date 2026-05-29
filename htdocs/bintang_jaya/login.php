<?php
	$logs = $_REQUEST['log'];

	require_once 'global.php';

	if ($logs != 1 && $logs != 0){
		redirecting('index.php');
	}
	
	if ($logs == "0")   //logout
	{
		$user->logout();
		redirecting('index.php');
	}
	else
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if (!empty($username) && !empty($password))
		{
			$login = $user->login($username,$password);
			if ($login) //login success
			{
				redirecting('index.php');
			}
			else{
				redirecting('index.php?msg=loginerror');
			}
		}
		else{
			redirecting('index.php?msg=loginerror');
		}
	}
?>