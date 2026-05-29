<?php
	require_once "global.php";
	
	require_once "class/supplier.php";
	require_once "class/purchase.php";
	require_once "class/customer.php";
	require_once "class/PurchaseR.php";
	require_once "class/PayDebt.php";
	$supplier = new supplier();
	$purchase = new Purchase();
	$purchaser = new PurchaseR();
	$paydebt = new PayDebt();
	$customer = new customer();
	
	
		
		if (empty($useraccess['report_timetocomplete'])){
			redirecting('index.php');
		}

		$printtemplate = 'reportlongdebt';
		
	
	if ( $_POST['cetak'] == 'yes' ){
	$printdate = date("d-M-Y / H:i:s");
	
	if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
	$datestart = strtotime($_POST['datestart']);
	$dateend = strtotime($_POST['dateend']);
	
	//get complete or not
	if ($_POST['completestat'] == 'all')
	$completestat = '';
	
	else
	$completestat = ' AND hp.complete = '.$_POST['completestat'];
	
	
	if (!empty($_POST['customercode']))
	$allsaleperiod = $db->fetch_all("SELECT hs.*,hp.*,dp.* FROM headersale hs INNER JOIN detailpayment dp ON hs.saleid = dp.hsid INNER JOIN headerpayment hp ON dp.hpid = hp.hpid WHERE dp.types='sale' AND hs.saledate >='".$datestart."' AND saledate <='".$dateend."' AND hs.customercode ='".$_POST['customercode']."' AND trtype='credit' AND types ='sale'".$completestat);
	
	else
	$allsaleperiod = $db->fetch_all("SELECT hs.*,hp.*,dp.* FROM headersale hs INNER JOIN detailpayment dp ON hs.saleid = dp.hsid INNER JOIN headerpayment hp ON dp.hpid = hp.hpid WHERE dp.types='sale' AND hs.saledate >='".$datestart."' AND saledate <='".$dateend."' AND trtype='credit' AND types ='sale'".$completestat);
	
	
	if (sizeof($allsaleperiod) > 0){
		$listsales = '';
	foreach ($allsaleperiod as $alsp){
		
		//getsaledate
		$thissaledate = date("d-M-Y",$alsp['saledate']);
		
		//getcostumer detail
		$customer->setCode($alsp['customercode']);
		$getcustomer = $customer->getcustomerDetail();
		

		if (empty($alsp['complete'])){
		$completedate  = '-';
		$diffrentcomsale = '-';
		}
		
		else{
		$completedate  = date("d-M-Y",$alsp['completedate']);

		$diffrentcomsale = getdifferentdate(date("d-m-Y",$alsp['saledate']),date("d-m-Y",$alsp['completedate'])).' Hari';
		
		}
		
		
		
		
		$listsales .= '<tr>';
		$listsales .= '<td align="center" height="30"><b>'.$alsp['saleno'].'</b></td>';
		$listsales .= '<td align="center" height="30"><b>'.$getcustomer['customername'].'</b></td>';
		$listsales .= '<td align="center" height="30"><b>'.$thissaledate.'</b></td>';
		$listsales .= '<td align="center" height="30"><b>'.$completedate.'</b></td>';
		$listsales .= '<td align="center" height="30"><b>'.$diffrentcomsale.'</b></td>';
		$listsales .= '</tr>';
		
		}
	}
	
	
	}
	
	
	$printtemplate = 'printdebtreport';
	}
	
	
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>