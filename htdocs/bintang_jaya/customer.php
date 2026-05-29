<?php
	require_once "global.php";
	
	if (empty($useraccess['view_customer']) && $_GET['getlist'] != 'determine' && $_GET['list'] != 'determine'){
		redirecting('index.php');
	}
	
	require_once "class/customer.php";
	require_once "class/area.php";
	require_once "class/state.php";
	require_once "class/country.php";
	require_once "class/Payment.php";
	$detailcustomer['status'] = 1;
	$customer = new customer();
	$area = new area();
	$state = new state();
	$country = new country();
	$payment = new Payment();
	
	if ($_POST['get'] == 'remainingcredit'){
		if (!empty($_POST['id'])){
			$customer->setCode($_POST['id']);
			$detailcust = $customer->getcustomerDetail();
		}
		if (empty($detailcust['remainingcredit'])){
			$detailcust['remainingcredit'] = 0;
		}
		echo $detailcust['remainingcredit'];
		exit;
	}
	
	
	if ($_GET['get'] == 'rnow'){
		if (!empty($_GET['code'])){
			$customer->setCode($_GET['code']);
			$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_GET['code']."' ");
			$startdate = strtotime($_GET['startdate']);
			$startyear =  strtotime('01-01-'.date("Y",$startdate));
			$endyear =  strtotime('31-12-'.date("Y",$startdate).' 23:59:59');
		
			//get all remainingnow in one year
			$allremainingnow = $db->fetch_one("SELECT SUM(remainingnow) AS ttlremainingnow FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."' ");
			//get all remainingprevious in one year
			$allremainingprevious = $db->fetch_one("SELECT SUM(remainingprevious) AS ttlremainingprevious FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."' ");
			//get all remainingprevioush in one year
			$allremainingprevioush = $db->fetch_one("SELECT SUM(remainingprevioush) AS ttlremainingprevioush FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."' ");
			//get all remainingnowh in one year
			$allremainingnowh = $db->fetch_one("SELECT SUM(remainingnowh) AS ttlremainingnowh FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."'  ");
			//get difference of $allremainingnow and $allremainingnowh
			$allremain = ($allremainingnow['ttlremainingnow'] - $allremainingprevious['ttlremainingprevious']) - ($allremainingnowh['ttlremainingnowh'] - $allremainingprevioush['ttlremainingprevioush']);
			//if $allremain > 0 ,$allremain is remainingprevious but if  $allremain < 0 ,$allremain is remainingprevioush
			
			if ($allremain >0 )
			{
			$remainingprevious = $allremain;
			$remainingprevioush = 0;
			}
			else{
			$remainingprevious = 0;
			$remainingprevioush = abs($allremain);
			}
			$data = $remainingprevious.'|^|'.$remainingprevioush;
			
		}
		else{
			$data = '0|^|0';
		}
		print($data);
		exit;
	}
	
	if ($_GET['get'] == 'rnowh'){
		if (!empty($_GET['code'])){
			
			$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE suppliercode = '".$_GET['code']."' ");
			$startdate = strtotime($_GET['startdate']);
			
			$startdate = strtotime($_GET['startdate']);
			$startyear =  strtotime('01-01-'.date("Y",$startdate));
			$endyear =  strtotime('31-12-'.date("Y",$startdate).' 23:59:59');
		
			//get all remainingnow in one year
			$allremainingnow = $db->fetch_one("SELECT SUM(remainingnow) AS ttlremainingnow FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."' ");
			//get all remainingprevious in one year
			$allremainingprevious = $db->fetch_one("SELECT SUM(remainingprevious) AS ttlremainingprevious FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."' ");
			//get all remainingprevioush in one year
			$allremainingprevioush = $db->fetch_one("SELECT SUM(remainingprevioush) AS ttlremainingprevioush FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."' ");
			//get all remainingnowh in one year
			$allremainingnowh = $db->fetch_one("SELECT SUM(remainingnowh) AS ttlremainingnowh FROM headerpayment hp WHERE hp.startdate > '".$startyear."' AND hp.enddate < '".$endyear."' AND hp.customerid =  '".$dbcustsup['customerid']."' AND hp.supplierid =  '".$dbcustsup['supplierid']."'  ");
			//get difference of $allremainingnow and $allremainingnowh
			$allremain = ($allremainingnow['ttlremainingnow'] - $allremainingprevious['ttlremainingprevious']) - ($allremainingnowh['ttlremainingnowh'] - $allremainingprevioush['ttlremainingprevioush']);
			//if $allremain > 0 ,$allremain is remainingprevious but if  $allremain < 0 ,$allremain is remainingprevioush
			
			if ($allremain >0 )
			{
			$remainingprevious = $allremain;
			$remainingprevioush = 0;
			}
			else{
			$remainingprevious = 0;
			$remainingprevioush = abs($allremain);
			}
			$data = $remainingprevious.'|^|'.$remainingprevioush;
			
			
		}
		else{
			$data = '0|^|0';
		}
		print($data);
		exit;
	}
	
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$customer->setId($_POST['id']);
		}
		echo $customer->checkcodeexist(trim($_POST['customercode']));
		exit;
	}
	
	if ($_POST['copy'] == 'tosupplier'){
		$returnit = false;
		if (!empty($_POST['id'])){
			$customer->setId($_POST['id']);
			$returnit = $customer->copyToSupplier($userid);
		}
		echo $returnit;
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		if ($_GET['list'] == 'detail'){
			if (!empty($_GET['id'])){
				$customer->setId($_GET['id']);
				$alladdr = $customer->getcustomeraddrdetail('all');
				if (sizeof($alladdr) > 0){
					foreach ($alladdr as $aad){
						$area->setCode($aad['areacode']);
						$dbarea = $area->getareaDetail();
						$state->setCode($aad['statecode']);
						$dbstate = $state->getstateDetail();
						$country->setCode($aad['countrycode']);
						$dbcountry = $country->getcountryDetail();
						$lists .= '
							<row id="'.$aad['detailcustid'].'">
								<cell>'.htmlspecialchars($aad['address']).'</cell>
								<cell>'.htmlspecialchars($aad['contactperson']).'</cell>
								<cell>'.htmlspecialchars($aad['postalcode']).'</cell>
								<cell>'.htmlspecialchars($dbarea['areaname']).'</cell>
								<cell>'.htmlspecialchars($dbstate['statename']).'</cell>
								<cell>'.htmlspecialchars($dbcountry['countryname']).'</cell>
								<cell>'.htmlspecialchars($aad['phone']).'</cell>
								<cell>'.htmlspecialchars($aad['fax']).'</cell>
								<cell>'.htmlspecialchars($aad['mobilenumber']).'</cell>
								<cell>'.$aad['status'].'</cell>
							</row>
						';
					}
				}
			}
			$sdlist = gettemplate('customerdetaillist');
		}
		else if ($_GET['list'] == 'general'){
			$listcustomer = $customer->getListcustomer('all');
			$lists = '';
			if (sizeof($listcustomer) > 0){
				$userh = new User();
				foreach ($listcustomer as $list){
					$userh->setId($list['lasteditedby']);
					$userdetail = $userh->getUserDetail();
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					$customer->setId($list['customerid']);
					$alladdr = $customer->getcustomeraddrdetail('all');
					$splits = sizeof($alladdr);
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0; 
						foreach ($alladdr as $apn){
							if ($io == 0){
								$listsplit .= '
										<cell>'.htmlspecialchars($apn['address']).'</cell>
										<cell>'.htmlspecialchars($apn['contactperson']).'</cell>
								';
							}
							else{
								$listsplit2 .= '
									<row id="'.$list['customerid'].'_'.$io.'">
										<cell/>
										<cell/>
										<cell>'.htmlspecialchars($apn['address']).'</cell>
										<cell>'.htmlspecialchars($apn['contactperson']).'</cell>
										<cell/>
										<cell/>
										<cell/>
										<cell/>
										<cell/>
									</row>';
							}
							$io++;
						}
					}
					else{
						$listsplit .= '
								<cell></cell>
								<cell></cell>
						';
					}

					$customer->setCode($list['customercode']);
					$lists .= '
						<row id="'.$list['customerid'].'">
							<cell'.$rstext.'>'.htmlspecialchars($list['customercode']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['customername']).'</cell>
							'.$listsplit.'
							<cell'.$rstext.'>'.$arrstatus[$list['status']].'</cell>
							<cell'.$rstext.'>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($userdetail['username']).'</cell>
							<cell'.$rstext.'>'.(($useraccess['edit_customer'])?'Ubah^customer.php?getlist=detail&amp;id='.$list['customerid'].'^_self':'-').'</cell>
							<cell'.$rstext.'>'.(($useraccess['delete_customer'] && $customer->canDeleteCustomer())?'Hapus^javascript:deleteitem("customer.php?do=delete&amp;id='.$list['customerid'].'")^_self':'-').'</cell>
						</row>
					'.$listsplit2;
				}
			}
			$sdlist = gettemplate('customerlist');
		}
		else if ($_GET['list'] == 'determine'){
			$listcustomer = $customer->getListcustomer('partial');
			$lists = '';
			if (sizeof($listcustomer) > 0){
				foreach ($listcustomer as $list){					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					$customer->setId($list['customerid']);
					$alladdr = $customer->getcustomeraddrdetail('partial');
					$splits = sizeof($alladdr);
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0;
						foreach ($alladdr as $apn){
							$area->setCode($apn['areacode']);
							$getarea = $area->getareaDetail();
							if ($io == 0){
								$listsplit .= '
										<cell>'.htmlspecialchars($apn['contactperson']).'</cell>
										<cell>'.htmlspecialchars($apn['address']).'</cell>
										<cell>'.htmlspecialchars($getarea['areaname']).'</cell>
										<cell>'.htmlspecialchars($apn['phone']).'</cell>
								';
							}
							else{
								$listsplit2 .= '
									<row id="'.$list['customerid'].'_'.$apn['detailcustid'].'">
										<cell/>
										<cell/>
										<cell>'.htmlspecialchars($apn['contactperson']).'</cell>
										<cell>'.htmlspecialchars($apn['address']).'</cell>
										<cell>'.htmlspecialchars($getarea['areaname']).'</cell>
										<cell>'.htmlspecialchars($apn['phone']).'</cell>
									</row>';
							}
							$io++;
						}
					}
					else{
						$listsplit .= '
								<cell></cell>
								<cell></cell>
								<cell></cell>
								<cell></cell>
						';
					}

					$lists .= '
						<row id="'.$list['customerid'].'">
							<cell'.$rstext.'>'.htmlspecialchars($list['customercode']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['customername']).'</cell>
							'.$listsplit.'
						</row>
					'.$listsplit2;
				}
			}
			$sdlist = gettemplate('customerlist');
		}
		eval("\$sdlist = \"$sdlist\";");
		echo $sdlist;
		exit;
	}
	else if ($_GET['getlist'] == 'determines'){
		$keywords = $_GET['keyword'];
		$fields = $_GET['field'];
		$cwarr = explode(",",$_GET['cwidth']);
		
		if (empty($keywords)){
			$keywords = array();
			$fields = array();
		}
		
		array_push($keywords,"Aktif");
		array_push($fields,"status");
		
		array_push($keywords,"1");
		array_push($fields,"detailstatus");
		
		$alldatas = $customer->searchcustomerfull($keywords,$fields,'','data',$_GET['searchmode'],'','');
		$totalrows = sizeof($alldatas);
		$totalpgs = ceil($totalrows / $general['showperpage']);
		$pgs = handlepage($_GET['page'],$totalpgs);
		
		$listcustomer = $customer->searchcustomerfull($keywords,$fields,$_GET['page'],'data',$_GET['searchmode'],$_GET['sortf'],$_GET['sortd']);
		$lists = '';
		$ctr = 1;
		if (sizeof($listcustomer) > 0){
			foreach ($listcustomer as $list){
				$customer->setId($list['customerid']);
				$alladdr = $customer->getcustomeraddrdetail('partial');
				
				$splits = sizeof($alladdr);
				
				$listsplit = '';
				$listsplit2 = '';
				$rstext = '';
				if ($splits > 0){
					if ($splits > 1){
						$rstext = ' rowspan="'.$splits.'"';
					}
					$io = 0;
					$firstaddrid = 0;
					$firstcperson = '';
					$firstaddr = '';
					$firstphone = '';
					$firstareaname = '';
					$firstareacode = '';
					foreach ($alladdr as $apn){
						if ($apn['status'] == 0){
							continue;
						}
						
						$apn = array_map("htmlspecialchars",$apn);
						
						$area->setCode($apn['areacode']);
						$dbarea = $area->getareaDetail();
						if (!empty($dbarea['areacode'])){
							$dbarea = array_map("htmlspecialchars",$dbarea);
						}
						
						if ($io == 0){
							$firstaddrid = $apn['detailcustid'];
							$firstaddr = $apn['address'];
							$firstcperson = $apn['contactperson'];
							$firstphone = $apn['phone'];
							$firstareacode = $apn['areacode'];
							$firstareaname = $dbarea['areaname'];
							$listsplit .= '
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[2].'" align="left">'.$apn['address'].'</td>
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[3].'" align="left">'.$apn['contactperson'].'</td>
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[4].'" align="left">'.wordwrap($apn['phone'],15,"<br>",true).'</td>
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[5].'" align="left">'.$dbarea['areaname'].'</td>
							';
						}
						else{
							$listsplit2 .= '
								<tr id="row_'.$ctr.'-'.$io.'" ondblclick="window.opener.setCustomer('.$apn['detailcustid'].',\''.str_replace("'","\'",$list['customercode']).'\',\''.str_replace("'","\'",$list['customername']).'\',\''.str_replace("'","\'",$apn['contactperson']).'\',\''.str_replace("'","\'",$apn['address']).'\',\''.str_replace("'","\'",$apn['areacode']).'\',\''.str_replace("'","\'",$apn['phone']).'\',\''.str_replace("'","\'",$dbarea['areaname']).'\');window.close()" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[2].'" align="left">'.$apn['address'].'</td>
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[3].'" align="left">'.$apn['contactperson'].'</td>
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[4].'" align="left">'.wordwrap($apn['phone'],15,"<br>",true).'</td>
									<td class="stufflist'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[5].'" align="left">'.$dbarea['areaname'].'</td>
								</tr>';
						}
						$io++;
					}
				}
				else{
					$listsplit .= '
						<td class="stufflist" width="'.$cwarr[2].'" align="left">-</td>
						<td class="stufflist" width="'.$cwarr[3].'" align="left">-</td>
						<td class="stufflist" width="'.$cwarr[4].'" align="left">-</td>
						<td class="stufflist" width="'.$cwarr[5].'" align="left">-</td>
					';
				}
				
				$list = array_map("htmlspecialchars",$list);
				
				if ($splits > 0){
					$lists .= '
						<tr id="row_'.$ctr.'" ondblclick="window.opener.setCustomer('.$firstaddrid.',\''.str_replace("'","\'",$list['customercode']).'\',\''.str_replace("'","\'",$list['customername']).'\',\''.str_replace("'","\'",$firstcperson).'\',\''.str_replace("'","\'",$firstaddr).'\',\''.str_replace("'","\'",$firstareacode).'\',\''.str_replace("'","\'",$firstphone).'\',\''.str_replace("'","\'",$firstareaname).'\');window.close()" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
							<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.wordwrap($list['customercode'],15,"<br>",true).'</td>
							<td class="stufflist" width="'.$cwarr[1].'" align="left"'.$rstext.'>'.$list['customername'].'</td>
							'.$listsplit.'						
						</tr>'.$listsplit2.'
					';
					$ctr++;
				}
			}
			
			if (!empty($lists)){
				$listheight = $_GET['hdv']-30;
				$pclist = '
					<div style="height: '.$listheight.'px; overflow-x: hidden; overflow-y: auto">
					<table border="0" cellpadding="3" cellspacing="0">
					'.$lists.'
					</table></div>
				';
				$pgs = $_GET['page'];
				$startrecord = ($pgs - 1) * $general['showperpage'] + 1;
				$endrecord = $startrecord + $general['showperpage'] - 1;
				if ($endrecord > $totalrows){
					$endrecord = $totalrows;
				}
				
				$pgslinks = generatepagelink($pgs,$totalpgs);
				
				$pclist .= '
					<div align="left" style="padding: 10px 5px 0 5px;">
					Halaman <b>'.$pgs.'</b> dari <b>'.$totalpgs.'</b>'.$pgslinks.'
					<span style="float: right">
					Record '.number_format($startrecord,0,",",".").' - '.number_format($endrecord,0,",",".").' dari total '.number_format($totalrows,0,",",".").'</span>
					</div>
				';
			}
		}
		echo $pclist;
		exit;
	}
	else if ($_GET['getlist'] == 'listingall'){
		$keywords = $_GET['keyword'];
		$fields = $_GET['field'];
		$cwarr = explode(",",$_GET['cwidth']);
		
		if (empty($keywords)){
			$keywords = array();
			$fields = array();
		}
		
		$alldatas = $customer->searchcustomerfull($keywords,$fields,'','data',$_GET['searchmode'],'','');
		$totalrows = sizeof($alldatas);
		$totalpgs = ceil($totalrows / $general['showperpage']);
		$pgs = handlepage($_GET['page'],$totalpgs);
		
		$listcustomer = $customer->searchcustomerfull($keywords,$fields,$_GET['page'],'data',$_GET['searchmode'],$_GET['sortf'],$_GET['sortd']);
		$lists = '';
		$ctr = 1;
		if (sizeof($listcustomer) > 0){
			$userh = new User();
			foreach ($listcustomer as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$customer->setId($list['customerid']);
				$alladdr = $customer->getcustomeraddrdetail('partial');
				
				$splits = sizeof($alladdr);
				
				$listsplit = '';
				$listsplit2 = '';
				$rstext = '';
				if ($splits > 0){
					if ($splits > 1){
						$rstext = ' rowspan="'.$splits.'"';
					}
					$io = 0;
					$firstaddrid = 0;
					$firstcperson = '';
					$firstaddr = '';
					$firstphone = '';
					$firstareaname = '';
					$firstareacode = '';
					foreach ($alladdr as $apn){
						if ($apn['status'] == 0){
							continue;
						}
						
						$apn = array_map("htmlspecialchars",$apn);
						
						$area->setCode($apn['areacode']);
						$dbarea = $area->getareaDetail();
						if (!empty($dbarea['areacode'])){
							$dbarea = array_map("htmlspecialchars",$dbarea);
						}
						
						if ($io == 0){
							$firstaddrid = $apn['detailcustid'];
							$firstaddr = $apn['address'];
							$firstcperson = $apn['contactperson'];
							$firstphone = $apn['phone'];
							$firstareacode = $apn['areacode'];
							$firstareaname = $dbarea['areaname'];
							$listsplit .= '
									<td class="stufflist padding_table_4'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[2].'" align="left">'.$apn['address'].'</td>
									<td class="stufflist padding_table_4'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[3].'" align="left">'.$apn['contactperson'].'</td>
							';
						}
						else{
							$listsplit2 .= '
								<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" ondblclick="window.location.href = \'customer.php?getlist=detail&id='.$list['customerid'].'\'" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
									<td class="stufflist padding_table_4'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[2].'" align="left">'.$apn['address'].'</td>
									<td class="stufflist padding_table_4'.(($apn['status'] == 0)?' bgnotactive':'').'" width="'.$cwarr[3].'" align="left">'.$apn['contactperson'].'</td>
								</tr>';
						}
						$io++;
					}
				}
				else{
					$listsplit .= '
						<td class="stufflist padding_table_4" width="'.$cwarr[2].'" align="left">-</td>
						<td class="stufflist padding_table_4" width="'.$cwarr[3].'" align="left">-</td>
					';
				}
				
				$list = array_map("htmlspecialchars",$list);
				
				$actioneditwidth = floor(51 / 100 * $cwarr[7]);
				$actiondeletewidth = $cwarr[7] - $actioneditwidth - 3;
				
				$customer->setCode($list['customercode']);
				$lists .= '
					<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" ondblclick="window.location.href = \'customer.php?getlist=detail&id='.$list['customerid'].'\'" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
						<td class="stufflist padding_table_4" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.wordwrap($list['customercode'],15,"<br>",true).'</td>
						<td class="stufflist padding_table_4" width="'.$cwarr[1].'" align="left"'.$rstext.'>'.$list['customername'].'</td>
						'.$listsplit.'						
						<td class="stufflist padding_table_4" width="'.$cwarr[4].'" align="center"'.$rstext.'>'.$arrstatus[$list['status']].'</td>
						<td class="stufflist padding_table_4" width="'.$cwarr[5].'" align="center"'.$rstext.'>'.date("d-M-y / H:i:s",$list['lastedited']).'</td>
						<td class="stufflist padding_table_4" width="'.$cwarr[6].'" align="center"'.$rstext.'>'.htmlspecialchars($userdetail['username']).'</td>
						<td class="stufflist padding_table_4 bgseparator" width="'.$cwarr[7].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
						(($useraccess['edit_customer'])?'<a href="customer.php?getlist=detail&id='.$list['customerid'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
						(($useraccess['delete_customer'] && $customer->canDeleteCustomer())?'<a href="javascript:deleteitem(\'customer.php?do=delete&id='.$list['customerid'].'\')">Hapus</a>':'-').'</span></td>
					</tr>'.$listsplit2.'
				';
				$ctr++;
			}
			
			if (!empty($lists)){
				$listheight = $_GET['hdv']-30;
				$pclist = '
					<div style="height: '.$listheight.'px; overflow-x: hidden; overflow-y: auto">
					<table border="0" cellpadding="3" cellspacing="0">
					'.$lists.'
					</table></div>
				';
				$pgs = $_GET['page'];
				$startrecord = ($pgs - 1) * $general['showperpage'] + 1;
				$endrecord = $startrecord + $general['showperpage'] - 1;
				if ($endrecord > $totalrows){
					$endrecord = $totalrows;
				}
				
				$pgslinks = generatepagelink($pgs,$totalpgs);
				
				$pclist .= '
					<div align="left" style="padding: 10px 5px 0 5px;">
					Halaman <b>'.$pgs.'</b> dari <b>'.$totalpgs.'</b>'.$pgslinks.'
					<span style="float: right">
					Record '.number_format($startrecord,0,",",".").' - '.number_format($endrecord,0,",",".").' dari total '.number_format($totalrows,0,",",".").'</span>
					</div>
				';
			}
		}
		echo $pclist;
		exit;
	}
	
	$cancopytosupplier = false;
	if (!empty($_REQUEST['id'])){
		$customer->setId($_REQUEST['id']);
		$detailcustomer = $customer->getcustomerDetail();
		
		$checksuppcode = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode='".$detailcustomer['customercode']."'");
		if (empty($checksuppcode['supplierid'])){
			$cancopytosupplier = true;
		}
		
		if (sizeof($detailcustomer) == 0){
			redirecting("customer.php");
		}
		
		$allpayment = $db->fetch_one("SELECT SUM(totalpayment) AS ttltotalpayment FROM headerpayment WHERE customerid =  '".$detailcustomer['customerid']."' AND status = 1 AND complete = 0 ");
		
		if (empty($allpayment))
		$allpayment['ttltotalpayment'] = 0;
		
		$customer->setCode($detailcustomer['customercode']);
		$fcredit = number_format($allpayment['ttltotalpayment'],2,",",".");
		$detailcustomer = array_map("htmlspecialchars",$detailcustomer);
		$alladdr = $customer->getcustomeraddrdetail('all');
		$alldetailid = '';
		if (sizeof($alladdr) > 0){
			foreach ($alladdr as $aad){
				$alldetailid .= ','.$aad['detailcustid'];
			}
			$alldetailid = substr($alldetailid,1);
		}
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_customer']){
		$lastid = $customer->savecustomer($_POST['customercode'],$_POST['customername'],0,$_POST['customerstatus'],$userid);

		$arrpostdel = explode(",",$_POST['detailcustbox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailcustbox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			$customer->setId($lastid);
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && (
					!empty($_POST["detailcustbox_".$arrpost[$x]."_0"]) ||
					!empty($_POST["detailcustbox_".$arrpost[$x]."_1"]) ||
					!empty($_POST["detailcustbox_".$arrpost[$x]."_2"]) ||
					(!empty($_POST["detailcustbox_".$arrpost[$x]."_3"]) && $_POST["detailcustbox_".$arrpost[$x]."_3"] != 'undefined') ||
					(!empty($_POST["detailcustbox_".$arrpost[$x]."_4"]) && $_POST["detailcustbox_".$arrpost[$x]."_4"] != 'undefined') ||
					(!empty($_POST["detailcustbox_".$arrpost[$x]."_5"]) && $_POST["detailcustbox_".$arrpost[$x]."_5"] != 'undefined') ||
					!empty($_POST["detailcustbox_".$arrpost[$x]."_6"]) ||
					!empty($_POST["detailcustbox_".$arrpost[$x]."_7"]) ||
					!empty($_POST["detailcustbox_".$arrpost[$x]."_8"]) ||
					!empty($_POST["detailcustbox_".$arrpost[$x]."_9"]))){
					$customer->savedetailcustomer($_POST["detailcustbox_".$arrpost[$x]."_0"],$_POST["detailcustbox_".$arrpost[$x]."_1"],$_POST["detailcustbox_".$arrpost[$x]."_2"],$_POST["detailcustbox_".$arrpost[$x]."_3"],$_POST["detailcustbox_".$arrpost[$x]."_4"],$_POST["detailcustbox_".$arrpost[$x]."_5"],$_POST["detailcustbox_".$arrpost[$x]."_6"],$_POST["detailcustbox_".$arrpost[$x]."_7"],$_POST["detailcustbox_".$arrpost[$x]."_8"],$_POST["detailcustbox_".$arrpost[$x]."_9"]);
				}
			}
		}
		redirecting("customer.php?getlist=detail&id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_customer']){
		if (!empty($_REQUEST['id'])){
			$results = $customer->updatecustomer($_POST['customercode'],$_POST['customername'],0,$_POST['customerstatus'],$userid);

			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailcustbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$customer->setDetailId($arrpostdel[$x]);
						$customer->deletedetailcustomer();
					}
				}
			}		
			//edited rows
			$arrpost = array_diff($arrpostt,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !in_array($arrpost[$x],$arrpostdel)){
						$customer->setDetailId($arrpost[$x]);
						$customer->updatedetailcustomer($_POST["detailcustbox_".$arrpost[$x]."_0"],$_POST["detailcustbox_".$arrpost[$x]."_1"],$_POST["detailcustbox_".$arrpost[$x]."_2"],$_POST["detailcustbox_".$arrpost[$x]."_3"],$_POST["detailcustbox_".$arrpost[$x]."_4"],$_POST["detailcustbox_".$arrpost[$x]."_5"],$_POST["detailcustbox_".$arrpost[$x]."_6"],$_POST["detailcustbox_".$arrpost[$x]."_7"],$_POST["detailcustbox_".$arrpost[$x]."_8"],$_POST["detailcustbox_".$arrpost[$x]."_9"]);
					}
				}
			}
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailcustbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && (
						!empty($_POST["detailcustbox_".$arrpost[$x]."_0"]) ||
						!empty($_POST["detailcustbox_".$arrpost[$x]."_1"]) ||
						!empty($_POST["detailcustbox_".$arrpost[$x]."_2"]) ||
						(!empty($_POST["detailcustbox_".$arrpost[$x]."_3"]) && $_POST["detailcustbox_".$arrpost[$x]."_3"] != 'undefined') ||
						(!empty($_POST["detailcustbox_".$arrpost[$x]."_4"]) && $_POST["detailcustbox_".$arrpost[$x]."_4"] != 'undefined') ||
						(!empty($_POST["detailcustbox_".$arrpost[$x]."_5"]) && $_POST["detailcustbox_".$arrpost[$x]."_5"] != 'undefined') ||
						!empty($_POST["detailcustbox_".$arrpost[$x]."_6"]) ||
						!empty($_POST["detailcustbox_".$arrpost[$x]."_7"]) ||
						!empty($_POST["detailcustbox_".$arrpost[$x]."_8"]) ||
						!empty($_POST["detailcustbox_".$arrpost[$x]."_9"]))){
						$customer->savedetailcustomer($_POST["detailcustbox_".$arrpost[$x]."_0"],$_POST["detailcustbox_".$arrpost[$x]."_1"],$_POST["detailcustbox_".$arrpost[$x]."_2"],$_POST["detailcustbox_".$arrpost[$x]."_3"],$_POST["detailcustbox_".$arrpost[$x]."_4"],$_POST["detailcustbox_".$arrpost[$x]."_5"],$_POST["detailcustbox_".$arrpost[$x]."_6"],$_POST["detailcustbox_".$arrpost[$x]."_7"],$_POST["detailcustbox_".$arrpost[$x]."_8"],$_POST["detailcustbox_".$arrpost[$x]."_9"]);
					}
				}
			}
		}
		redirecting("customer.php?getlist=detail&id=".$_REQUEST['id']);
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_customer']){
		if ($customer->canDeleteCustomer()){
			$customer->deletecustomer();
		}
		redirecting("customer.php");		
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('customer');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>