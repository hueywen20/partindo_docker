<?php
	require_once "global.php";
	
	if (empty($useraccess['view_supplier']) && $_GET['getlist'] != 'determine' && $_GET['list'] != 'determine'){
		redirecting('index.php');
	}
	
	require_once "class/supplier.php";
	require_once "class/area.php";
	require_once "class/state.php";
	require_once "class/country.php";
	$detailsupplier['status'] = 1;
	$supplier = new supplier();
	$area = new area();
	$state = new state();
	$country = new country();
	
	if ($_POST['get'] == 'remainingdebt'){
		if (!empty($_POST['id'])){
			$supplier->setCode($_POST['id']);
			$detailspl = $supplier->getsupplierDetail();
		}
		if (empty($detailspl['remainingdebt'])){
			$detailspl['remainingdebt'] = 0;
		}
		echo $detailspl['remainingdebt'];
		exit;
	}
	
	if ($_GET['get'] == 'rnow'){
		if (!empty($_GET['code'])){
			$supplier->setCode($_GET['code']);
			$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON s.suppliercode = c.customercode WHERE suppliercode = '".$_GET['code']."' ");
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
			$supplier->setId($_POST['id']);
		}
		echo $supplier->checkcodeexist(trim($_POST['suppliercode']));
		exit;
	}
	
	if ($_POST['copy'] == 'tocustomer'){
		$returnit = false;
		if (!empty($_POST['id'])){
			$supplier->setId($_POST['id']);
			$returnit = $supplier->copyToCustomer($userid);
		}
		echo $returnit;
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		header("Content-type: text/xml");
		if ($_GET['list'] == 'detail'){
			if (!empty($_GET['id'])){
				$supplier->setId($_GET['id']);
				$alladdr = $supplier->getsupplieraddrdetail('all');
				if (sizeof($alladdr) > 0){
					foreach ($alladdr as $aad){
						$area->setCode($aad['areacode']);
						$dbarea = $area->getareaDetail();
						$state->setCode($aad['statecode']);
						$dbstate = $state->getstateDetail();
						$country->setCode($aad['countrycode']);
						$dbcountry = $country->getcountryDetail();
						$lists .= '
							<row id="'.$aad['detailsplid'].'">
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
			$sdlist = gettemplate('supplierdetaillist');
		}
		else if ($_GET['list'] == 'general'){
			$listsupplier = $supplier->getListsupplier('all');
			$lists = '';
			if (sizeof($listsupplier) > 0){
				$userh = new User();
				foreach ($listsupplier as $list){
					$userh->setId($list['lasteditedby']);
					$userdetail = $userh->getUserDetail();
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					$supplier->setId($list['supplierid']);
					$alladdr = $supplier->getsupplieraddrdetail('all');
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
									<row id="'.$list['supplierid'].'_'.$io.'">
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

					$supplier->setCode($list['suppliercode']);
					$lists .= '
						<row id="'.$list['supplierid'].'">
							<cell'.$rstext.'>'.htmlspecialchars($list['suppliercode']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['suppliername']).'</cell>
							'.$listsplit.'
							<cell'.$rstext.'>'.$arrstatus[$list['status']].'</cell>
							<cell'.$rstext.'>'.date("d-M-y / H:i:s",$list['lastedited']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($userdetail['username']).'</cell>
							<cell'.$rstext.'>'.(($useraccess['edit_supplier'])?'Ubah^supplier.php?getlist=detail&amp;id='.$list['supplierid'].'^_self':'-').'</cell>
							<cell'.$rstext.'>'.(($useraccess['delete_supplier'] && $supplier->canDeleteSupplier())?'Hapus^javascript:deleteitem("supplier.php?do=delete&amp;id='.$list['supplierid'].'")^_self':'-').'</cell>
						</row>
					'.$listsplit2;
				}
			}
			$sdlist = gettemplate('supplierlist');
		}
		else if ($_GET['list'] == 'determine'){
			$listsupplier = $supplier->getListsupplier('partial');
			$lists = '';
			if (sizeof($listsupplier) > 0){
				foreach ($listsupplier as $list){					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					$supplier->setId($list['supplierid']);
					$alladdr = $supplier->getsupplieraddrdetail('partial');
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
									<row id="'.$list['supplierid'].'_'.$apn['detailsplid'].'">
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
						<row id="'.$list['supplierid'].'">
							<cell'.$rstext.'>'.htmlspecialchars($list['suppliercode']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['suppliername']).'</cell>
							'.$listsplit.'
						</row>
					'.$listsplit2;
				}
			}
			$sdlist = gettemplate('supplierlist');
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
		
		$alldatas = $supplier->searchsupplierfull($keywords,$fields,'','data',$_GET['searchmode'],'','');
		$totalrows = sizeof($alldatas);
		$totalpgs = ceil($totalrows / $general['showperpage']);
		$pgs = handlepage($_GET['page'],$totalpgs);
		
		$listsupplier = $supplier->searchsupplierfull($keywords,$fields,$_GET['page'],'data',$_GET['searchmode'],$_GET['sortf'],$_GET['sortd']);
		$lists = '';
		$ctr = 1;
		if (sizeof($listsupplier) > 0){
			foreach ($listsupplier as $list){
				$supplier->setId($list['supplierid']);
				$alladdr = $supplier->getsupplieraddrdetail('partial');
				
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
							$firstaddrid = $apn['detailsplid'];
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
								<tr id="row_'.$ctr.'-'.$io.'" ondblclick="window.opener.setSupplier('.$apn['detailsplid'].',\''.str_replace("'","\'",$list['suppliercode']).'\',\''.str_replace("'","\'",$list['suppliername']).'\',\''.str_replace("'","\'",$apn['contactperson']).'\',\''.str_replace("'","\'",$apn['address']).'\',\''.str_replace("'","\'",$apn['areacode']).'\',\''.str_replace("'","\'",$apn['phone']).'\',\''.str_replace("'","\'",$dbarea['areaname']).'\');window.close()" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
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
						<tr id="row_'.$ctr.'" ondblclick="window.opener.setSupplier('.$firstaddrid.',\''.str_replace("'","\'",$list['suppliercode']).'\',\''.str_replace("'","\'",$list['suppliername']).'\',\''.str_replace("'","\'",$firstcperson).'\',\''.str_replace("'","\'",$firstaddr).'\',\''.str_replace("'","\'",$firstareacode).'\',\''.str_replace("'","\'",$firstphone).'\',\''.str_replace("'","\'",$firstareaname).'\');window.close()" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
							<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.wordwrap($list['suppliercode'],15,"<br>",true).'</td>
							<td class="stufflist" width="'.$cwarr[1].'" align="left"'.$rstext.'>'.$list['suppliername'].'</td>
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

		$alldatas = $supplier->searchsupplierfull($keywords,$fields,'','data',$_GET['searchmode'],'','');
		$totalrows = sizeof($alldatas);
		$totalpgs = ceil($totalrows / $general['showperpage']);
		$pgs = handlepage($_GET['page'],$totalpgs);
		
		$listsupplier = $supplier->searchsupplierfull($keywords,$fields,$_GET['page'],'data',$_GET['searchmode'],$_GET['sortf'],$_GET['sortd']);
		$lists = '';
		$ctr = 1;
		if (sizeof($listsupplier) > 0){
			$userh = new User();
			foreach ($listsupplier as $list){
				$userh->setId($list['lasteditedby']);
				$userdetail = $userh->getUserDetail();
				
				$supplier->setId($list['supplierid']);
				$alladdr = $supplier->getsupplieraddrdetail('partial');
				
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
							$firstaddrid = $apn['detailsplid'];
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
								<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" ondblclick="window.location.href = \'supplier.php?getlist=detail&id='.$list['supplierid'].'\'" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
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
				
				$supplier->setCode($list['suppliercode']);
				$lists .= '
					<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" ondblclick="window.location.href = \'supplier.php?getlist=detail&id='.$list['supplierid'].'\'" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#FFFFFF').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)">
						<td class="stufflist padding_table_4" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.wordwrap($list['suppliercode'],15,"<br>",true).'</td>
						<td class="stufflist padding_table_4" width="'.$cwarr[1].'" align="left"'.$rstext.'>'.$list['suppliername'].'</td>
						'.$listsplit.'						
						<td class="stufflist padding_table_4" width="'.$cwarr[4].'" align="center"'.$rstext.'>'.$arrstatus[$list['status']].'</td>
						<td class="stufflist padding_table_4" width="'.$cwarr[5].'" align="center"'.$rstext.'>'.date("d-M-y / H:i:s",$list['lastedited']).'</td>
						<td class="stufflist padding_table_4" width="'.$cwarr[6].'" align="center"'.$rstext.'>'.htmlspecialchars($userdetail['username']).'</td>
						<td class="stufflist padding_table_4 bgseparator" width="'.$cwarr[7].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
						(($useraccess['edit_supplier'])?'<a href="supplier.php?getlist=detail&id='.$list['supplierid'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
						(($useraccess['delete_supplier'] && $supplier->canDeleteSupplier())?'<a href="javascript:deleteitem(\'supplier.php?do=delete&id='.$list['supplierid'].'\')">Hapus</a>':'-').'</span></td>
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
	
	if (!empty($_REQUEST['id'])){
		$supplier->setId($_REQUEST['id']);
		$detailsupplier = $supplier->getsupplierDetail();
		
		$checkcustcode = $db->fetch_one("SELECT * FROM customer WHERE customercode='".$detailsupplier['suppliercode']."'");
		if (empty($checkcustcode['customerid'])){
			$cancopytocustomer = true;
		}
		
		if (sizeof($detailsupplier) == 0){
			redirecting("supplier.php");
		}
		
		$allpaydebt = $db->fetch_one("SELECT SUM(totalpayment) AS ttltotalpaydebt FROM headerpayment WHERE supplierid =  '".$detailsupplier['supplierid']."' AND status = 2 AND complete = 0 ");

		if (empty($allpaydebt))
		$allpaydebt['ttltotalpaydebt'] = 0;
		
		$supplier->setCode($detailsupplier['suppliercode']);
		$fdebt = number_format($allpaydebt['ttltotalpaydebt'],2,",",".");
		$detailsupplier = array_map("htmlspecialchars",$detailsupplier);
		$alladdr = $supplier->getsupplieraddrdetail('all');
		$alldetailid = '';
		if (sizeof($alladdr) > 0){
			foreach ($alladdr as $aad){
				$alldetailid .= ','.$aad['detailsplid'];
			}
			$alldetailid = substr($alldetailid,1);
		}
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_supplier']){
		$lastid = $supplier->savesupplier($_POST['suppliercode'],$_POST['suppliername'],$_POST['supplierstatus'],$userid);
		/*if ($lastid == false){
			$errors = 'samecode';
			$detailsupplier['suppliercode'] = htmlspecialchars($_POST['suppliercode']);
			$detailsupplier['suppliername'] = htmlspecialchars($_POST['suppliername']);
            $detailsupplier["address"] = htmlspecialchars($_POST["address"]);
            $detailsupplier["areacode"] = htmlspecialchars($_POST["areacode"]);
            $detailsupplier["statecode"] = htmlspecialchars($_POST["statecode"]);
            $detailsupplier["countrycode"] = htmlspecialchars($_POST["countrycode"]);
            $detailsupplier["postalcode"] = htmlspecialchars($_POST["postalcode"]);
            $detailsupplier["phone"] = htmlspecialchars($_POST["phone"]);
            $detailsupplier["fax"] = htmlspecialchars($_POST["fax"]);
            $detailsupplier["contactperson"] = htmlspecialchars($_POST["contactperson"]);
            $detailsupplier["mobilenumber"] = htmlspecialchars($_POST["mobilenumber"]);
            $detailsupplier["debt"] = htmlspecialchars($_POST["debt"]);
			$detailsupplier['status'] = $_POST['supplierstatus'];
		}
		else{*/
		$arrpostdel = explode(",",$_POST['detailsplbox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailsplbox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			$supplier->setId($lastid);
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && (
					!empty($_POST["detailsplbox_".$arrpost[$x]."_0"]) ||
					!empty($_POST["detailsplbox_".$arrpost[$x]."_1"]) ||
					!empty($_POST["detailsplbox_".$arrpost[$x]."_2"]) ||
					(!empty($_POST["detailsplbox_".$arrpost[$x]."_3"]) && $_POST["detailsplbox_".$arrpost[$x]."_3"] != 'undefined') ||
					(!empty($_POST["detailsplbox_".$arrpost[$x]."_4"]) && $_POST["detailsplbox_".$arrpost[$x]."_4"] != 'undefined') ||
					(!empty($_POST["detailsplbox_".$arrpost[$x]."_5"]) && $_POST["detailsplbox_".$arrpost[$x]."_5"] != 'undefined') ||
					!empty($_POST["detailsplbox_".$arrpost[$x]."_6"]) ||
					!empty($_POST["detailsplbox_".$arrpost[$x]."_7"]) ||
					!empty($_POST["detailsplbox_".$arrpost[$x]."_8"]) ||
					!empty($_POST["detailsplbox_".$arrpost[$x]."_9"]))){
					$supplier->savedetailsupplier($_POST["detailsplbox_".$arrpost[$x]."_0"],$_POST["detailsplbox_".$arrpost[$x]."_1"],$_POST["detailsplbox_".$arrpost[$x]."_2"],$_POST["detailsplbox_".$arrpost[$x]."_3"],$_POST["detailsplbox_".$arrpost[$x]."_4"],$_POST["detailsplbox_".$arrpost[$x]."_5"],$_POST["detailsplbox_".$arrpost[$x]."_6"],$_POST["detailsplbox_".$arrpost[$x]."_7"],$_POST["detailsplbox_".$arrpost[$x]."_8"],$_POST["detailsplbox_".$arrpost[$x]."_9"]);
				}
			}
		}
		redirecting("supplier.php?getlist=detail&id=".$lastid);
		//}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_supplier']){
		//print_r($_POST);
		if (!empty($_REQUEST['id'])){
			$results = $supplier->updatesupplier($_POST['suppliercode'],$_POST['suppliername'],$_POST['supplierstatus'],$userid);
			/*if ($results == false){
				$errors = 'samecode';
			}
			else{*/
			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailsplbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$supplier->setDetailId($arrpostdel[$x]);
						$supplier->deletedetailsupplier();
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
						$supplier->setDetailId($arrpost[$x]);
						$supplier->updatedetailsupplier($_POST["detailsplbox_".$arrpost[$x]."_0"],$_POST["detailsplbox_".$arrpost[$x]."_1"],$_POST["detailsplbox_".$arrpost[$x]."_2"],$_POST["detailsplbox_".$arrpost[$x]."_3"],$_POST["detailsplbox_".$arrpost[$x]."_4"],$_POST["detailsplbox_".$arrpost[$x]."_5"],$_POST["detailsplbox_".$arrpost[$x]."_6"],$_POST["detailsplbox_".$arrpost[$x]."_7"],$_POST["detailsplbox_".$arrpost[$x]."_8"],$_POST["detailsplbox_".$arrpost[$x]."_9"]);
					}
				}
			}
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailsplbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && (
						!empty($_POST["detailsplbox_".$arrpost[$x]."_0"]) ||
						!empty($_POST["detailsplbox_".$arrpost[$x]."_1"]) ||
						!empty($_POST["detailsplbox_".$arrpost[$x]."_2"]) ||
						(!empty($_POST["detailsplbox_".$arrpost[$x]."_3"]) && $_POST["detailsplbox_".$arrpost[$x]."_3"] != 'undefined') ||
						(!empty($_POST["detailsplbox_".$arrpost[$x]."_4"]) && $_POST["detailsplbox_".$arrpost[$x]."_4"] != 'undefined') ||
						(!empty($_POST["detailsplbox_".$arrpost[$x]."_5"]) && $_POST["detailsplbox_".$arrpost[$x]."_5"] != 'undefined') ||
						!empty($_POST["detailsplbox_".$arrpost[$x]."_6"]) ||
						!empty($_POST["detailsplbox_".$arrpost[$x]."_7"]) ||
						!empty($_POST["detailsplbox_".$arrpost[$x]."_8"]) ||
						!empty($_POST["detailsplbox_".$arrpost[$x]."_9"]))){
						$supplier->savedetailsupplier($_POST["detailsplbox_".$arrpost[$x]."_0"],$_POST["detailsplbox_".$arrpost[$x]."_1"],$_POST["detailsplbox_".$arrpost[$x]."_2"],$_POST["detailsplbox_".$arrpost[$x]."_3"],$_POST["detailsplbox_".$arrpost[$x]."_4"],$_POST["detailsplbox_".$arrpost[$x]."_5"],$_POST["detailsplbox_".$arrpost[$x]."_6"],$_POST["detailsplbox_".$arrpost[$x]."_7"],$_POST["detailsplbox_".$arrpost[$x]."_8"],$_POST["detailsplbox_".$arrpost[$x]."_9"]);
					}
				}
			}
		}
		redirecting("supplier.php?getlist=detail&id=".$_REQUEST['id']);
		//}
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_supplier']){
		if ($supplier->canDeleteSupplier()){
			$supplier->deletesupplier();
		}
		redirecting("supplier.php");		
	}
	/*$areaoption = $area->getListarea('partial');
	if (sizeof($areaoption) > 0){
		$areao = '<select name="areacode" id="areacode"><option value=""></option>';
		foreach ($areaoption as $ao){
			$areao .= '<option value="'.$ao['areacode'].'"'.((htmlspecialchars($ao['areacode']) == $detailsupplier['areacode'])?' selected':'').'>'.$ao['areaname'].' - '.$ao['areacode'].'</option>';
		}
		$areao .= '</select>
		<script type="text/javascript">
			var a = dhtmlXComboFromSelect("areacode");
			a.enableFilteringMode(true);
		</script>';
	}

	$stateoption = $state->getListstate('partial');
	if (sizeof($stateoption) > 0){
		$stateo = '<select name="statecode" id="statecode"><option value=""></option>';
		foreach ($stateoption as $so){
			$stateo .= '<option value="'.$so['statecode'].'"'.((htmlspecialchars($so['statecode']) == $detailsupplier['statecode'])?' selected':'').'>'.$so['statename'].' - '.$so['statecode'].'</option>';
		}
		$stateo .= '</select>
		<script type="text/javascript">
			var s = dhtmlXComboFromSelect("statecode");
			s.enableFilteringMode(true);
		</script>';
	}

	$countryoption = $country->getListcountry('partial');
	if (sizeof($countryoption) > 0){
		$countryo = '<select name="countrycode" id="countrycode"><option value=""></option>';
		foreach ($countryoption as $co){
			$countryo .= '<option value="'.$co['countrycode'].'"'.((htmlspecialchars($co['countrycode']) == $detailsupplier['countrycode'])?' selected':'').'>'.$co['countryname'].' - '.$co['countrycode'].'</option>';
		}
		$countryo .= '</select>
		<script type="text/javascript">
			var c = dhtmlXComboFromSelect("countrycode");
			c.enableFilteringMode(true);
		</script>';
	}*/
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('supplier');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>