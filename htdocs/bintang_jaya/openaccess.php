<?php
	require_once "global.php";
	
	if (empty($useraccess['open_sale_access'])){
		redirecting('index.php');
	}
	
	$opens = '';
	if ($_POST['actions'] == 'openit'){
		$alluser = $uaccess->getUserByAccess(53);
		if (sizeof($alluser) > 0){
			foreach ($alluser as $aus){
				$postpass = $user->encryptPassword($_POST['openpass'],$aus['createddate'],$aus['createdby']);
				if ($aus['pass'] == $postpass){
					$opens = 'success';
					break;
				}
			}
		}
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('openaccess');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>