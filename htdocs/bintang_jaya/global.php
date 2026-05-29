<?php
	date_default_timezone_set('Asia/Jakarta');
   	
	require_once "class/Database.php";
	require_once "incl/misc_function.php";
	$db = new Database();
	$db->connect();
	
	$arrdayname = array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
	$arrmonthname = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$arrdaynameassoc = array(
		'Sunday' => 'Minggu',
		'Monday' => 'Senin',
		'Tuesday' => 'Selasa',
		'Wednesday' => 'Rabu',
		'Thursday' => 'Kamis',
		'Friday' => 'Jumat',
		'Saturday' => 'Sabtu'
	);
	$arrmonthnameassoc = array(
		'January' => 'Januari',
		'February' => 'Februari',
		'March' => 'Maret',
		'April' => 'April',
		'May' => 'Mei',
		'June' => 'Juni',
		'July' => 'Juli',
		'August' => 'Agustus',
		'September' => 'September',
		'October' => 'Oktober',
		'November' => 'November',
		'December' => 'Desember'
	);
	$arrdaynameassocst = array(
		'Sun' => 'Minggu',
		'Mon' => 'Senin',
		'Tue' => 'Selasa',
		'Wed' => 'Rabu',
		'Thu' => 'Kamis',
		'Fri' => 'Jumat',
		'Sat' => 'Sabtu'
	);
	$arrmonthnameassocst = array(
		'Jan' => 'Jan',
		'Feb' => 'Feb',
		'Mar' => 'Mar',
		'Apr' => 'Apr',
		'May' => 'Mei',
		'Jun' => 'Jun',
		'Jul' => 'Jul',
		'Aug' => 'Agu',
		'Sep' => 'Sep',
		'Oct' => 'Okt',
		'Nov' => 'Nov',
		'Dec' => 'Des'
	);
	
	$scriptexc = array("index","login");
	
	$thisurl = currentURL();
	
	$result = $db->query("Select * from settings");
	while ($stg = @mysql_fetch_array($result)){
		$pvl = str_replace('"','\"',$stg['value']);
		eval("\$".$stg['grouping']."['".$stg['varkey']."'] = \"$pvl\";");
	}
		
	//check cookie
	$nwtm = time();
	$yearsoftwarenow = date("Y");
	$db->query("DELETE FROM online WHERE lastvisit < ".($nwtm-$general['logintimelimit']));
	if (!empty($_COOKIE['mycookie'])){		
		$db->query("UPDATE online SET userid=0, status=0 WHERE lastvisit < ".($nwtm-$general['logintimelimit'])." AND userid <> 0");
		
		$upol = 'lastvisit='.$nwtm;
		if (!empty($upol)){
			$db->query("UPDATE online SET ".$upol." WHERE cookieid='".$_COOKIE['mycookie']."'");
		}
		
		$dbonline = $db->query("SELECT * FROM online WHERE cookieid='".$_COOKIE['mycookie']."'");
		if (@mysql_num_rows($dbonline) > 0){
			$rsol = @mysql_fetch_array($dbonline);
			$userid = $rsol['userid'];
			$statususer = $rsol['status'];
		}
		else{
			$usip = getIP();
			$db->query("INSERT INTO `online`(`cookieid`,`userid`,`lastvisit`,`ipaddress`,`useragent`) 
						  VALUES('".$_COOKIE['mycookie']."',0,".$nwtm.",'".$usip."','".$_SERVER['HTTP_USER_AGENT']."')");
		}
	}
	else{
		$usip = getIP();
		$ckie = md5($nwtm.$usip);
		setcookie('mycookie',$ckie,0,'/');
		$db->query("INSERT INTO `online`(`cookieid`,`userid`,`lastvisit`,`ipaddress`,`useragent`) 
					  VALUES('".$ckie."',0,".$nwtm.",'".$usip."','".$_SERVER['HTTP_USER_AGENT']."')");
	}
	
	require_once "class/User.php";
	require_once "class/UserGroup.php";
	require_once "class/Access.php";
	$user = new User();
	if (!empty($userid)){
		$user->setId($userid);
		$userdetail = $user->getUserDetail();
		
		$ugroup = new UserGroup();
		$ugroup->setId($userdetail['usergroupid']);
		$ugroupdetail = $ugroup->getUserGroupDetail();
		
		//get user access
		$uaccess = new Access();
		if ($statususer == 1){
			$arraccss = explode(",",$ugroupdetail['access']);
			if (in_array(95,$arraccss)){
				unset($arraccss[array_search(95,$arraccss)]);
			}
			if (in_array(108,$arraccss)){
				unset($arraccss[array_search(108,$arraccss)]);
			}
			if (in_array(87,$arraccss)){
				unset($arraccss[array_search(87,$arraccss)]);
			}
			if (in_array(88,$arraccss)){
				unset($arraccss[array_search(88,$arraccss)]);
			}
			if (in_array(89,$arraccss)){
				unset($arraccss[array_search(89,$arraccss)]);
			}
			if (in_array(90,$arraccss)){
				unset($arraccss[array_search(90,$arraccss)]);
			}
			if (in_array(91,$arraccss)){
				unset($arraccss[array_search(91,$arraccss)]);
			}
			if (in_array(92,$arraccss)){
				unset($arraccss[array_search(92,$arraccss)]);
			}
			if (in_array(93,$arraccss)){
				unset($arraccss[array_search(93,$arraccss)]);
			}
			if (in_array(94,$arraccss)){
				unset($arraccss[array_search(94,$arraccss)]);
			}
			$ugroupdetail['access'] = implode(",",$arraccss);
		}
		$uaccess->setAccessId($ugroupdetail['access']);
		$uaccarr = $uaccess->getAccessDetail();
		$useraccess = array();
		
		$menulv1 = array();
		$menulv2 = array();
		if (sizeof($uaccarr) > 0){
			foreach ($uaccarr as $uacr){
				$useraccess[$uacr['accessname']] = 1;
				if ($uacr['sublevel'] == 1){
					if (empty($menulv1[$uacr['accessgroupparent']])){
						$menulv1[$uacr['accessgroupparent']] = array();
					}
					array_push($menulv1[$uacr['accessgroupparent']],'["'.$uacr['menulabel'].'","'.$uacr['jsaction'].'",0');
					//$menulv1[$uacr['accessgroupparent']] = ',["'.$uacr['menulabel'].'","'.$uacr['jsaction'].'",0,[]]'.;
				}
				else if ($uacr['sublevel'] == 2){
					$agp = explode("|",$uacr['accessgroupparent']);
					if (empty($menulv1[$agp[1]])){
						$menulv1[$agp[1]] = array();
					}
					if (!in_array('["'.$agp[0].'","",1',$menulv1[$agp[1]])){
						array_push($menulv1[$agp[1]],'["'.$agp[0].'","",1');
					}
					if (empty($menulv2[$agp[0]])){
						$menulv2[$agp[0]] = array();
					}
					array_push($menulv2[$agp[0]],'["'.$uacr['menulabel'].'","'.$uacr['jsaction'].'",0');
					//$menulv2[$uacr['accessgroupparent']] = ',["'.$uacr['menulabel'].'","'.$uacr['jsaction'].'",0,[]]';
				}
			}
		}
		//print_r($menulv2);
		
		$jsaccess = '';
		//prepare for javascript
		if (sizeof($menulv1) > 0){
			foreach ($menulv1 as $keys => $ml1){
				$jsaccess .= ',["'.$keys.'","",1,[';
				if (sizeof($ml1) > 0){
					$jsaccess1 = '';
					foreach ($ml1 as $keyss => $aml1){
						$jsaccess1 .= ','.$aml1.',[';
						if (substr($aml1,-1) == '1'){
							$temp = substr($aml1,2);
							$temp = substr($temp,0,strpos($temp,'"'));
							if (sizeof($menulv2[$temp]) > 0){
								$jsaccess2 = '';
								foreach ($menulv2[$temp] as $ml2){
									$jsaccess2 .= ','.$ml2.',[]]';
								}
								$jsaccess1 .= substr($jsaccess2,1);
							}
						}
						$jsaccess1 .= ']]';
					}
					$jsaccess .= substr($jsaccess1,1);
				}
				$jsaccess .= ']]';
			}
			$jsaccess = substr($jsaccess,1);
		}
	}
	else{
		if (!strstr($thisurl,'index.php') && !strstr($thisurl,'login.php')){
			redirecting('index.php');
		}
	}
	
	if ($general[available] == '0' && $userdetail['usergroupid'] != 8 && !empty($userid)){
		$db->query("DELETE o FROM online o INNER JOIN user u ON o.userid = u.id WHERE u.usergroupid <> 8");
		$userid = 0;
		$msgoff = 'offline';
		if (!strstr($thisurl,'index.php')){
			redirecting('index.php?msg=offline');
		}
	}

	//get client browser
	$cbrow = checkBrowser(true);

	$arrstatus = array("Non-Aktif","Aktif");
	$arrpurchase = array("Hutang","Lunas");
	$arrsale = array("Piutang","Lunas");
	$arrpays = array("Belum Lunas","Lunas");
	$repaystatus = array("","Tunai","Transfer","Cek","Giro");
	$debtstatus = array("","Piutang","Hutang");
	
	//get conversion codes
	require_once "class/Codes.php";
	$codest = new Codes();
	$arrcodes = $codest->getListCodes();
	
	//prepare for javascript
	if (sizeof($arrcodes) > 0){
		$ntargets = '';
		$rtargets = '';
		foreach ($arrcodes as $arrc){
			$ntargets .= ',"'.$arrc['targets'].'"';
			$rtargets .= ',"'.$arrc['replacements'].'"';
		}
		$ntargets = 'var NT = ['.substr($ntargets,1).'];';
		$rtargets = 'var RT = ['.substr($rtargets,1).'];';
	}
	
	if ($general['defaultnumbering']){
		$defnumb = 'code';
	}
	else{
		$defnumb = 'number';
	}
	
	//change payment status
	$allpays = $db->fetch_all("SELECT * FROM detailrepayment WHERE status = 0 AND types IN (3,4) AND (duedates <= '".$nwtm."' AND duedates > 0) GROUP BY hpid");
	if (sizeof($allpays) > 0){
		$db->beginTransaction();
		foreach ($allpays as $aps){
			$checkallpaid = $db->fetch_all("SELECT * FROM detailrepayment WHERE hpid = '".$aps['hpid']."'");
			$allcomplete = true;
			if (sizeof($checkallpaid) > 0){
				foreach ($checkallpaid as $cap){
					if ($cap['status'] == 0){
						if ($cap['types'] == 3 || $cap['types'] == 4){
							if ($cap['duedates'] <= $nwtm){
								$db->query("UPDATE detailrepayment SET status = 1 WHERE drpyid = '".$cap['drpyid']."'");
							}
							else{
								$allcomplete = false;
							}
						}
					}
				}
			}
			if ($allcomplete){
				$getheaderpayment = $db->fetch_one("SELECT * FROM headerpayment WHERE hpid='".$aps['hpid']."'");
				$getdetailpayment = $db->fetch_all("SELECT * FROM detailpayment WHERE hpid='".$aps['hpid']."'");
				if (sizeof($getdetailpayment) > 0){
					foreach ($getdetailpayment as $gdp){
						if ($gdp['types'] == 'return'){
							$db->query("UPDATE detailsaler SET paid=1, paydate=".$nwtm." WHERE dsrid='".$gdp['hsid']."'");
						}
						else if ($gdp['types'] == 'sale'){
							$db->query("UPDATE headersale SET paid=1, paydate=".$nwtm." WHERE saleid='".$gdp['hsid']."'");
						}
						else if ($gdp['types'] == 'returnby'){
							$db->query("UPDATE detailbuyr SET paid=1, paydate=".$nwtm." WHERE dbrid='".$gdp['hsid']."'");
						}
						else if ($gdp['types'] == 'buy'){
							$db->query("UPDATE headerbuy SET paid=1, paydate=".$nwtm." WHERE buyid='".$gdp['hsid']."'");
						}
					}
				}
				$db->query("UPDATE headerpayment SET complete=1, completedate=".$nwtm." WHERE hpid='".$aps['hpid']."'");
				$db->query("UPDATE customer SET credit=credit-".$getheaderpayment['totalforsale']." WHERE customerid='".$getheaderpayment['customerid']."'");
				$getcustomer = $db->fetch_one("SELECT * FROM customer WHERE customerid='".$getheaderpayment['customerid']."'");
				$db->query("UPDATE supplier SET debt=debt-".$getheaderpayment['totalforbuy']." WHERE suppliercode='".$getcustomer['customercode']."'");
			}
		}
		$db->endTransaction();
	}
	
	/* //change paydebt status
	$allpays = $db->fetch_all("SELECT * FROM detailrepaydebt WHERE status = 0 AND types IN (3,4) AND (duedates <= '".$nwtm."' AND duedates > 0) GROUP BY hpid");
	if (sizeof($allpays) > 0){
		$db->beginTransaction();
		foreach ($allpays as $aps){
			$checkallpaid = $db->fetch_all("SELECT * FROM detailrepaydebt WHERE hpid = '".$aps['hpid']."'");
			$allcomplete = true;
			if (sizeof($checkallpaid) > 0){
				foreach ($checkallpaid as $cap){
					if ($cap['status'] == 0){
						if ($cap['types'] == 3 || $cap['types'] == 4){
							if ($cap['duedates'] <= $nwtm){
								$db->query("UPDATE detailrepaydebt SET status = 1 WHERE drpyid = '".$cap['drpyid']."'");
							}
							else{
								$allcomplete = false;
							}
						}
					}
				}
			}
			if ($allcomplete){
				$getheaderpayment = $db->fetch_one("SELECT * FROM headerpaydebt WHERE hpid='".$aps['hpid']."'");
				$getdetailpayment = $db->fetch_all("SELECT * FROM detailpaydebt WHERE hpid='".$aps['hpid']."'");
				if (sizeof($getdetailpayment) > 0){
					foreach ($getdetailpayment as $gdp){
						if ($gdp['types'] == 'return'){
							$db->query("UPDATE detailbuyr SET paid=1, paydate=".$nwtm." WHERE dbrid='".$gdp['hbid']."'");
						}
						else{
							$db->query("UPDATE headerbuy SET paid=1, paydate=".$nwtm." WHERE buyid='".$gdp['hbid']."'");
						}
					}
				}
				$db->query("UPDATE headerpaydebt SET complete=1, completedate=".$nwtm." WHERE hpid='".$aps['hpid']."'");
				$db->query("UPDATE supplier SET debt=debt-".$getheaderpayment['totalpayment']." WHERE supplierid='".$getheaderpayment['supplierid']."'");
			}
		}
		$db->endTransaction();
	} */
	
	if (empty($general['showsearchitems'])){
		$general['showsearchitems'] = 100;
	}
?>