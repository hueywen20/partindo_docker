<?php
	require_once "global.php";
	
	//$user->createNewUser('admin','adminbit4jaya',1,'Administrator');
	
	$openclosebook = false;
	$yearnow = date("Y");
	$checkclosebook = $db->fetch_one("SELECT * FROM stockyear WHERE year='".$yearnow."'");
	if (empty($checkclosebook['id']) && $useraccess['closebook']){
		$openclosebook = true;
	}
	
	$tmpl = gettemplate('index');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>