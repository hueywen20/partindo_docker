<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	require_once "class/supplier.php";
	require_once "class/customer.php";
	require_once "class/area.php";
	require_once "class/purchase.php";
	require_once "class/Payment.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	$supplier = new supplier();
	$customer = new customer();
	$area = new area();
	$purchase = new Purchase();
	$payment = new Payment();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$purchase->setId($_POST['id']);
		}
		echo $purchase->checkOrderNoExist(trim($_POST['no']));
		exit;
	}
	
	$headerbuy['totals'] = 0;
	$headerbuy['disc'] = 0;
	$headerbuy['tax'] = 0;
	$headerbuy['totalbuy'] = 0;
	$invoicedate = date("d-m-Y");
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['no'])){
				$purchase->setBuyNo($_GET['no']);
				$allpurchase = $purchase->getDetailBuy();
				if (sizeof($allpurchase) > 0){
					foreach ($allpurchase as $ap){
						$brand->setCode($ap['brandcode']);
						$getbrandname = $brand->getBrandDetail();
						if (empty($getbrandname['brandname']))
							$getbrandname['brandname'] = $ap['brandcode'];
						$type->setCode($ap['typecode']);
						$gettypename = $type->gettypeDetail();
						if (empty($gettypename['typename']))
							$gettypename['typename'] = $ap['typecode'];
						$units->setCode($ap['unitcode']);
						$getunitname = $units->getunitDetail();
						
						if (empty($ap['expdate'])){
							$showexpdate = 0;
						}
						else{
							$showexpdate = date("d-m-Y",$ap['expdate']);
						}
						
						if ($statususer == 1){
							$ap['quantityf'] = floor((100-$discount['extradisc'])/100 * $ap['quantityf']);
							if ($ap['quantityf'] < 1){
								$ap['quantityf'] = 1;
							}
							$totalbuyfk = $ap['quantityf'] * $ap['buyprice'];
							$totaldiscfk = $ap['disc'] / 100 * $totalbuyfk;
							$ap['totalbuyad'] = $totalbuyfk - $totaldiscfk;
						}
						
						$lists .= '
							<row id="r-'.$ap['dbid'].'">
								<cell>'.htmlspecialchars($ap['stockcode']).'</cell>
								<cell>'.htmlspecialchars($ap['partno']).'</cell>
								<cell>'.htmlspecialchars($ap['stockname']).'</cell>
								<cell>'.htmlspecialchars($ap['brandcode']).'</cell>
								<cell>'.htmlspecialchars($ap['typecode']).'</cell>
								<cell>'.number_format($ap['quantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['unitquantityf']).'</cell>
								<cell>'.number_format($ap['buyprice'],2,",",".").'</cell>
								<cell>'.number_format($ap['disc'],2,",",".").'</cell>
								<cell>'.number_format($ap['totalbuyad'],2,",",".").'</cell>
								<cell>'.$showexpdate.'</cell>
								<cell>'.htmlspecialchars($ap['description']).'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('purchasedetaillist');
		}
		else if ($_GET['list'] == 'general'){
			/* if (isset($_GET['keyword'])){
				if ($_GET['keyword'] != ''){ */
					$allpurchase = $purchase->searchPurchase($_GET['keyword'],$_GET['field'],$_GET['trtype']);
					$totalrows = sizeof($allpurchase);
					$totalpgs = ceil($totalrows / $general['showperpage']);
					$pgs = handlepage($_GET['p'],$totalpgs);
					
					$listpurchase = $purchase->searchPurchase($_GET['keyword'],$_GET['field'],$_GET['trtype'],$pgs);
				/* }
			} */
			//$listpurchase = $purchase->getListPurchase('all');
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listpurchase) > 0){
				$ctr = 1;
				foreach ($listpurchase as $list){
					$supplier->setCode($list['suppliercode']);
					$getsuppliername = $supplier->getsupplierDetail();
					
					$purchase->setBuyNo($list['buyno']);
					$candelbuy = $purchase->canDeleteBuy();
					
					$getdetailbuy = $purchase->getDetailBuy();
					$splits = sizeof($getdetailbuy);
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0;
						$subtotalfk = 0;
						foreach ($getdetailbuy as $gdb){
							if (empty($gdb['expdate'])){
								$showexpdate = 0;
							}
							else{
								$showexpdate = date("d-m-Y",$gdb['expdate']);
							}
							$gdb['typecode'] = wordwrap($gdb['typecode'],15,"<br>",true);
							$gdb['brandcode'] = wordwrap($gdb['brandcode'],10,"<br>",true);
							
							$gdb['typecode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['typecode']));
							$gdb['brandcode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['brandcode']));
							
							if ($statususer == 1){
								$gdb['quantityf'] = floor((100-$discount['extradisc'])/100 * $gdb['quantityf']);
								if ($gdb['quantityf'] < 1){
									$gdb['quantityf'] = 1;
								}
								
								$totalbuyfk = $gdb['quantityf'] * $gdb['buyprice'];
								$totaldiscfk = $gdb['disc'] / 100 * $totalbuyfk;
								$subtotalfk += ($totalbuyfk - $totaldiscfk);
							}
							
							if ($io == 0){
								$listsplit .= '
									<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
									<td width="'.$cwarr[5].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
									<td width="'.$cwarr[6].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
									<td width="'.$cwarr[7].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
									<td width="'.$cwarr[8].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
									<td width="'.$cwarr[9].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
									<td width="'.$cwarr[10].'" class="stufflist" align="center">'.htmlspecialchars($gdb['unitquantityf']).'</td>
									<td width="'.$cwarr[11].'" class="stufflist" id="buyprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['buyprice']).'</td>
								';
							}
							else{
								$listsplit2 .= '
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'purchase.php?no='.$list['buyno'].'\',\'_self\')">
										<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
										<td width="'.$cwarr[5].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
										<td width="'.$cwarr[6].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
										<td width="'.$cwarr[7].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
										<td width="'.$cwarr[8].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
										<td width="'.$cwarr[9].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
										<td width="'.$cwarr[10].'" class="stufflist" align="center">'.htmlspecialchars($gdb['unitquantityf']).'</td>
										<td width="'.$cwarr[11].'" class="stufflist" id="buyprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['buyprice']).'</td>
									</tr>
								';
							}
							$io++;						
						}
					}
					else{
								$listsplit .= '
									<td width="'.$cwarr[4].'" class="stufflist" align="left"></td>
									<td width="'.$cwarr[5].'" class="stufflist" align="left"></td>
									<td width="'.$cwarr[6].'" class="stufflist" align="left"></td>
									<td width="'.$cwarr[7].'" class="stufflist" align="left"></td>
									<td width="'.$cwarr[8].'" class="stufflist" align="left"></td>
									<td width="'.$cwarr[9].'" class="stufflist" align="right"></td>
									<td width="'.$cwarr[10].'" class="stufflist" align="left"></td>
									<td width="'.$cwarr[11].'" class="stufflist" id="buyprice_'.$ctr.'-'.$io.'" align="right"></td>
								';
					}

					/*$lists .= '
						<row id="'.$list['buyno'].'">
							<cell>'.htmlspecialchars($list['buyno']).'</cell>
							<cell>'.htmlspecialchars($list['orderno']).'</cell>
							<cell>'.date("d-m-Y",$list['buydate']).'</cell>
							<cell>'.date("d-m-Y",$list['duedate']).'</cell>
							<cell>'.htmlspecialchars($getsuppliername['suppliername']).'</cell>
							<cell>'.$codest->convertcodes($list['disc']).'</cell>
							<cell>'.$codest->convertcodes($list['tax']).'</cell>
							<cell>'.$codest->convertcodes($list['totalbuy']).'</cell>
							<cell>'.$arrpurchase[$list['status']].'</cell>
							<cell>'.(($useraccess['edit_purchase'])?'Ubah^purchase.php?no='.$list['buyno'].'^_self':'-').'</cell>
							<cell>'.(($useraccess['delete_purchase'])?(($candelbuy)?'Hapus^javascript:deleteitem("purchase.php?do=delete&amp;no='.$list['buyno'].'")^_self':'-'):'-').'</cell>
						</row>
					';*/
					$actioneditwidth = floor(51 / 100 * $cwarr[14]);
					$actiondeletewidth = $cwarr[14]-$actioneditwidth-3;
					$list['orderno'] = wordwrap($list['orderno'],15,"<br>",true);
							
					$list['orderno'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($list['orderno']));
					
					if ($statususer == 1){
						$totalgdiscfk = $list['disc'] / 100 * $subtotalfk;
						$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
						$totalgtaxfk = $list['tax'] / 100 * $totalafgdiscfk;
						$list['totalbuy'] = intval($totalafgdiscfk + $totalgtaxfk);
					}
					
					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'purchase.php?no='.$list['buyno'].'\',\'_self\')">
								<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.$list['orderno'].'</td>
								<td class="stufflist" width="'.$cwarr[1].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['buydate']).'</td>
								<td class="stufflist" width="'.$cwarr[2].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['duedate']).'</td>
								<td class="stufflist" width="'.$cwarr[3].'" align="left"'.$rstext.'>'.htmlspecialchars($getsuppliername['suppliername']).'</td>
								'.$listsplit.'
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[12].'" align="right"'.$rstext.'>'.$codest->convertcodes($list['totalbuy']).'</td>
								<td class="stufflist" width="'.$cwarr[13].'" align="center"'.$rstext.'>'.$arrpurchase[$list['paid']].'</td>
								<td class="stufflist bgseparator" width="'.$cwarr[14].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
								(($useraccess['edit_purchase'])?'<a href="purchase.php?no='.$list['buyno'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
								(($useraccess['delete_purchase'])?(($candelbuy)?'<a href="javascript:deleteitem(\'purchase.php?do=delete&no='.$list['buyno'].'\')">Hapus</a>':'-'):'-').'</span></td>
							</tr>'.$listsplit2.'
					';					
					
					$ctr++;
				}
			}
			$ctrgo = $ctr-1;
			if (!empty($lists)){
				$listheight = $_GET['hdv'] - 30;
				$pclist = '
					<div style="height: '.$listheight.'px; overflow-x: hidden; overflow-y: auto">
					<table border="0" cellpadding="3" cellspacing="0">
					'.$lists.'
					</table>
					<input type="hidden" id="totalstuffrow" value="'.($ctr-1).'"></div>
				';
				$startrecord = ($pgs - 1) * $general['showperpage'] + 1;
				$endrecord = $startrecord + $general['showperpage'] - 1;
				if ($endrecord > $totalrows){
					$endrecord = $totalrows;
				}
				
				$pgslinks = generatepagelink($pgs,$totalpgs);
				
				$pclist .= '
					<div align="left" style="padding: 10px 5px 0 5px; width: '.array_sum($cwarr).'px">
					Halaman <b>'.$pgs.'</b> dari <b>'.$totalpgs.'</b>'.$pgslinks.'
					<span style="float: right">
					Record '.number_format($startrecord,0,",",".").' - '.number_format($endrecord,0,",",".").' dari total '.number_format($totalrows,0,",",".").'</span>
					</div>
				';
			}
			echo $pclist;
			exit;
			/* $pclist = gettemplate('purchaselistdetail'); */
			/*if (!empty($lists)){
				$pclist = '
					<table border="0" cellpadding="3" cellspacing="0">
					'.$lists.'
					</table>
					<input type="hidden" id="totalstuffrow" value="'.($ctr-1).'">
				';
			}*/
		}
		else if ($_GET['list'] == 'determine'){
			header("Content-type: text/xml");
			$listpurchase = $purchase->getListPurchase('all');
			$lists = '';
			if (sizeof($listpurchase) > 0){
				foreach ($listpurchase as $list){
					$supplier->setCode($list['suppliercode']);
					$getsuppliername = $supplier->getsupplierDetail();

					$lists .= '
						<row id="'.$list['buyno'].'">
							<cell>'.htmlspecialchars($list['buyno']).'</cell>
							<cell>'.htmlspecialchars($list['orderno']).'</cell>
							<cell>'.date("d-m-Y",$list['buydate']).'</cell>
							<cell>'.date("d-m-Y",$list['duedate']).'</cell>
							<cell>'.htmlspecialchars($getsuppliername['suppliername']).'</cell>
							<cell>'.$list['disc'].'</cell>
							<cell>'.$list['tax'].'</cell>
							<cell>'.$list['totalbuy'].'</cell>
						</row>
					';
				}
			}
			$pclist = gettemplate('purchaselistdetail');
		}
		eval("\$pclist = \"$pclist\";");
		echo $pclist;
		exit;
	}
	else if ($_GET['getlist'] == 'ajax'){
		if ($_GET['list'] == 'supplier' && !empty($_GET['no'])){
			$purchase->setBuyNo($_GET['no']);
			$headerbuy = $purchase->getHeaderBuy();
			if (!empty($headerbuy['suppliercode'])){
				$supplier->setCode($headerbuy['suppliercode']);
				$supplier->setDetailId($headerbuy['supplieraddrid']);
				$getsupplier = $supplier->getsupplierDetail();
				$area->setCode($getsupplier['areacode']);
				$dbarea = $area->getareaDetail();
				if (!empty($dbarea['areaname'])){
					$areaname = ' '.$dbarea['areaname'];
				}
				$suppliercperson = htmlspecialchars($getsupplier['contactperson']);
				$suppliername = htmlspecialchars($getsupplier['suppliername']);
				$supplieraddr = htmlspecialchars($getsupplier['address'].$areaname);
				$suppliertelp = htmlspecialchars($getsupplier['phone']);
				$feedback = $headerbuy['suppliercode'].'|^|'.$headerbuy['supplieraddrid'].'|^|'.$suppliername.'|^|'.$suppliercperson.'|^|'.$supplieraddr.'|^|'.$suppliertelp;
			}
		}
		echo $feedback;
		exit;
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_purchase']){
		$db->beginTransaction();
		$buydate = strtotime($_POST['buydate']);
		$duedate = strtotime($_POST['duedate']);
		
		$startyear =  strtotime('01-01-'.date("Y",$buydate));
		$endyear =  strtotime('31-12-'.date("Y",$buydate).' 23:59:59');
		
		if ($_POST['supplieraddrid'] == '-1'){
			$supplier->setCode($_POST['suppliercode']);
			$dbcustsup = $supplier->getsupplierDetail('partial');
			$supplier->setId($dbcustsup['supplierid']);
			$getsaddr = $supplier->getsupplieraddrdetail('all');
			if (sizeof($getsaddr) > 0){
				foreach ($getsaddr as $gsa){
					$_POST['supplieraddrid'] = $gsa['detailsplid'];
					break;
				}
			}
			
			$customer->setCode($_POST['suppliercode']);
			$getccode = $customer->getcustomerDetail('partial');
			$customer->setId($getccode['customerid']);
			$getcaddr = $customer->getcustomeraddrdetail('all');
			if (sizeof($getcaddr) > 0){
				foreach ($getcaddr as $gca){
					$_POST['customeraddrid'] = $gca['detailcustid'];
					break;
				}
			}
			
		}
		else{
			$detailsupplierad = $db->fetch_one("SELECT * FROM detailsupplier where detailsplid = '".$_POST['supplieraddrid']."' ");
			$detailcustomerad = $db->fetch_one("SELECT * FROM detailcustomer where address = '".$detailsupplierad['address']."' ");
			$_POST['customeraddrid'] = $detailcustomerad['detailcustid'];
			}
		
		$_POST["otherpays"] = togglenumber($_POST["otherpays"],'calculate');
		$lastorderno = $purchase->saveHeaderBuy($_POST['orderno'],$buydate,$duedate,$_POST['suppliercode'],$_POST['supplieraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['otherpays'],$_POST['totalbuy'],$_POST['trtype'],$userid);

			
			
		
		$dbsupplier = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode = '".$_POST['suppliercode']."' ");
		
		$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$_POST['suppliercode']."' ");

			
			//jika pembelian dilakukan scr kredit 
			if ($_POST['trtype'] == "credit"){
			
			$getbuyid = $db->fetch_one("SELECT * FROM headerbuy WHERE buyno = '".$lastorderno."' ");
			$_POST['id'] = $getbuyid['buyid'];
			
			$checkdebt = $payment->getHeaderPaymentByMonth($buydate,2,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],0);

			
			//jika blm ada data hutang di periode bln ini
			if (empty($checkdebt['hpid'])){
			
			
			$lastmonth = $payment->getDetailLastPaymentByMonth($buydate,2,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			$remainingprevious = 0;
			$remainingprevioush = 0;
			$remainingnow  = 0;
			$remainingnowh  = 0;
			
			
			
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
			
			//echo '('.$allremainingnow['ttlremainingnow'].' - '.$allremainingprevious['ttlremainingprevious'].') - ('.$allremainingnowh['ttlremainingnowh'].' - '.$allremainingprevioush['ttlremainingprevioush'].')';
			
			if ($allremain >0 )
			{
			$remainingprevious = $allremain;
			$remainingprevioush = 0;
			}
			else{
			$remainingprevious = 0;
			$remainingprevioush = abs($allremain);
			}
			
			
			$checknulfremainingprevioush = explode(".",$remainingprevioush);
			$checknulfremainingprevious = explode(".",$remainingprevious);
			
			
			//jika remainingnow ada
			if (!empty($checknulfremainingprevious[0])){
			$grandtotals = $_POST['totalbuy'] + $remainingprevious;
			}
			
			//jika remainingnowh ada
			else if (!empty($checknulfremainingprevioush[0])){
			
			$grandtotals = $_POST['totalbuy'] - $remainingprevioush;
			if ($grandtotals < 0){
			$remainingnowh = abs($grandtotals);
			$grandtotals = 0;
			}	
			else{
			$remainingnowh = 0;
			$grandtotals = abs($grandtotals);
			}
			
			}
			else {
			$grandtotals = $_POST['totalbuy'];
			}
			
			if ($grandtotals <=0 ){
			$complete = 1;
			$completedate = $buydate;
			}
			else{
			$complete = 0;
			$completedate = 0;
			}
			
			$invstartdate = strtotime('01-'.date("m-Y",$buydate));
			$invenddate = strtotime(date('t-m-Y',$buydate).' 23:59:59');
			
		
			$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$buydate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST['totalbuy'],$grandtotals,$userid,2,$remainingprevioush,$remainingnowh);
			
			$payment->setId($lastidpaym);
			$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
			
			$payment->updateDebtCreditnotlive($_POST['suppliercode'],0);
			}
			
			//jika ada data hutang di periode bln ini
			else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - (($totalpaybuy['totalpay']+$_POST['totalbuy']) - $totalpaybuyr['totalpay']);
				//$ttlfolpaym = $oldheader['totalforsale'] - ($oldheader['totalforbuy']+$_POST['totalbuy']);
				
				
				
				
				
				if ($oldheader['status'] == 1){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				$grandtotals = 0;
				$totalpayment = $ttlfolpaym;
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				
				
				if (!empty($checknulfremainingprevious[0])){
				if ($ttlfolpaym > 0 ){
				
				
				$newvalue = $ttlfolpaym-$oldheader['remainingprevious'];
				
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnow = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnow = 0;
				}
				}
				
				else if ($ttlfolpaym <= 0 ){
				$grandtotals = abs($ttlfolpaym - $_POST['totalbuy']);
				$remainingnow = 0;
				}
				
				}
				else{
				$grandtotals = abs($totalpayment+$oldheader['remainingprevioush']);
				}
				
				//echo $grandtotals;
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
				
				}
				
				else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();

				$totalpayment = abs($ttlfolpaym);
				
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				$newvalue = $totalpayment-$oldheader['remainingprevioush'];
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				
				if (!empty($checknulfremainingprevious[0])){
				$grandtotals = $totalpayment + $oldheader['remainingprevious'];
				
				}
				else{
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnowh = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnowh = 0;
				}
				}
				
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
			
				
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
				}

			}
			
				/* echo "Sub Total (Rp.) : ".abs($_POST['totalbuy']);
				echo "<br>Grand Total (Rp.) :".abs($grandtotals);
				echo "<br>Rprev h : ".abs($remainingprevioush);
				echo "<br>Rprev   : ".abs($remainingprevious);
				echo "<br>Rnow h : ".abs($remainingnowh);
				echo "<br>now   : ".abs($remainingnow); */
			
			
		}
		
		
		$arrpostdel = explode(",",$_POST['detailpchbox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailpchbox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		$totalitems = 0;
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["detailpchbox_".$arrpost[$x]."_0"])){
					$checkchars = strpos($_POST["detailpchbox_".$arrpost[$x]."_0"],"||");
					if ($checkchars !== false){
						$_POST["detailpchbox_".$arrpost[$x]."_0"] = substr($_POST["detailpchbox_".$arrpost[$x]."_0"],0,$checkchars);
					}
					
					$stock->setId("");
					$stock->setCode($_POST["detailpchbox_".$arrpost[$x]."_0"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["detailpchbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_5"],'calculate');
					$_POST["detailpchbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_7"],'calculate');
					$_POST["detailpchbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_8"],'calculate');
					
					$totals = $_POST["detailpchbox_".$arrpost[$x]."_5"] * $_POST["detailpchbox_".$arrpost[$x]."_7"];
					$buypricead = $_POST["detailpchbox_".$arrpost[$x]."_7"] - ($_POST["detailpchbox_".$arrpost[$x]."_8"] / 100 * $_POST["detailpchbox_".$arrpost[$x]."_7"]);
					$realbuyprice = $buypricead - ($_POST['disc'] / 100 * $buypricead);
					$realbuyprice = $realbuyprice + ($_POST['tax'] / 100 * $realbuyprice);

					if ($_POST["detailpchbox_".$arrpost[$x]."_6"] == $getunit['funit']){
						$quantity = $_POST["detailpchbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
						$realbuyprice = $realbuyprice / $getunit['cvalue'];
					}
					else{
						$quantity = $_POST["detailpchbox_".$arrpost[$x]."_5"];
						$_POST["detailpchbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
					}
					
					$totalitems += $quantity;
					
					$purchase->setBuyNo($lastorderno);
					$purchase->saveDetailBuy($_POST["detailpchbox_".$arrpost[$x]."_0"],$_POST["detailpchbox_".$arrpost[$x]."_1"],$_POST["detailpchbox_".$arrpost[$x]."_2"],$_POST["detailpchbox_".$arrpost[$x]."_3"],$_POST["detailpchbox_".$arrpost[$x]."_4"],$_POST["detailpchbox_".$arrpost[$x]."_5"],$_POST["detailpchbox_".$arrpost[$x]."_6"],$_POST["detailpchbox_".$arrpost[$x]."_7"],$_POST["detailpchbox_".$arrpost[$x]."_8"],togglenumber($_POST["detailpchbox_".$arrpost[$x]."_9"],'calculate'),strtotime($_POST["detailpchbox_".$arrpost[$x]."_10"]),$_POST["detailpchbox_".$arrpost[$x]."_11"],$buydate,$totals,$buypricead,$realbuyprice,$quantity,$unitquantity,$getstock['unitcode']);
				}
			}
		}
		
		/* update realbuyprice by adding the other pays */
		if ($_POST['otherpays'] > 0 && $totalitems > 0){
			$additionalpays = $_POST['otherpays'] / $totalitems;
			$db->query("UPDATE detailbuy SET realbuyprice = realbuyprice + ".$additionalpays." WHERE buyno = '".$db->clean($lastorderno)."'");
		}
		
		$db->endTransaction();
		redirecting("purchase.php?no=".$lastorderno);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_purchase']){
		//print_r($_POST);
		if (!empty($_POST['id'])){
			$purchase->setId($_POST['id']);
			
			$db->beginTransaction();
			$buydate = strtotime($_POST['buydate']);
			$startyear =  strtotime('01-01-'.date("Y",$buydate));
			$endyear =  strtotime('31-12-'.date("Y",$buydate).' 23:59:59');
			$duedate = strtotime($_POST['duedate']);
			if ($_POST['supplieraddrid'] == '-1'){
			$supplier->setCode($_POST['suppliercode']);
			$dbcustsup = $supplier->getsupplierDetail('partial');
			$supplier->setId($dbcustsup['supplierid']);
			$getsaddr = $supplier->getsupplieraddrdetail('all');
			if (sizeof($getsaddr) > 0){
				foreach ($getsaddr as $gsa){
					$_POST['supplieraddrid'] = $gsa['detailsplid'];
					break;
				}
			}
			
			$customer->setCode($_POST['suppliercode']);
			$getccode = $customer->getcustomerDetail('partial');
			$customer->setId($getccode['customerid']);
			$getcaddr = $customer->getcustomeraddrdetail('all');
			if (sizeof($getcaddr) > 0){
				foreach ($getcaddr as $gca){
					$_POST['customeraddrid'] = $gca['detailcustid'];
					break;
				}
			}
			
		}
		else{
			$detailsupplierad = $db->fetch_one("SELECT * FROM detailsupplier where detailsplid = '".$_POST['supplieraddrid']."' ");
			$detailcustomerad = $db->fetch_one("SELECT * FROM detailcustomer where address = '".$detailsupplierad['address']."' ");
			$_POST['customeraddrid'] = $detailcustomerad['detailcustid'];
			}
			
			$getoldheaderbuy = $purchase->getHeaderBuy();
			$dbsupplier = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode = '".$_POST['suppliercode']."' ");
			
			$_POST["otherpays"] = togglenumber($_POST["otherpays"],'calculate');
			$purchase->updateHeaderBuy($_POST['orderno'],$buydate,$duedate,$_POST['suppliercode'],$_POST['supplieraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['otherpays'],$_POST['totalbuy'],$_POST['trtype'],$userid);
			
			$dbsupplier = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode = '".$_POST['suppliercode']."' ");
			
			$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$_POST['suppliercode']."' ");
			
			
			if ( $getoldheaderbuy['suppliercode'] == $_POST['suppliercode'] )
			{
			
			if ($buydate == $getoldheaderbuy['buydate']){
			
			$statusedit = 0;
			}
			else{
			$statusedit = 1;
			}
			
			}
			else{
			$statusedit = 1;
			}
			
			
			
			//jika sama header dulu & sekarang
			if ( $statusedit == 0 ){
			if ($_POST['trtype'] == "credit"){
	
			$checkdebt = $payment->getallHeaderPaymentByMonth($buydate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			
			//jika blm ada data hutang di periode bln ini
			if (empty($checkdebt['hpid'])){
			
			
			$lastmonth = $payment->getDetailLastPaymentByMonth($buydate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			$remainingprevious = 0;
			$remainingprevioush = 0;
			$remainingnow  = 0;
			$remainingnowh  = 0;
			
			
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
			
			
			$checknulfremainingprevioush = explode(".",$remainingprevioush);
			$checknulfremainingprevious = explode(".",$remainingprevious);
			
			
			
			if (!empty($checknulfremainingprevioush[0])){
			
			$grandtotals = $_POST['totalbuy'] + $remainingprevioush;
			}
			else if (!empty($checknulfremainingprevious[0])){
			
			$grandtotals = $_POST['totalbuy'] - $remainingprevious;
			
			if ($grandtotals < 0){
			
			$remainingnow = abs($grandtotals);
			$grandtotals = 0;
			}	
			else{
			
			$remainingnow = 0;
			$grandtotals = $grandtotals;
			}
			
			}
			else {
			
			$grandtotals = $_POST['totalbuy'];
			}
			
			if ($grandtotals <=0 ){
			$complete = 1;
			$completedate = $buydate;
			}
			else{
			$complete = 0;
			$completedate = 0;
			}
			
			$invstartdate = strtotime('01-'.date("m-Y",$buydate));
			$invenddate = strtotime(date('t-m-Y',$buydate).' 23:59:59');
			
			
			
			$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$buydate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST['totalbuy'],$grandtotals,$userid,1,$remainingprevioush,$remainingnowh);
			
			$payment->setId($lastidpaym);
			$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
			$payment->updateDebtCreditnotlive($_POST['suppliercode'],0);
			
			
			}
			
			//jika ada data hutang di periode bln ini
			else{
				
				$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$_POST['id']."' AND types = 'buy' ");
				
				//jika belum ada data di detailpayment
				if (empty($getdetail['dpid']))
				{
				
				$payment->setId($checkdebt['hpid']);
				$oldestheader = $payment->getHeaderPayment();
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldestheader);
				
				$oldheader = $payment->getHeaderPayment();
				
				
				if ($oldheader['status'] == 1){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				
				$grandtotals = 0;

				$totalpayment = abs($ttlfolpaym);
				
				
				if (!empty($checknulfremainingprevious[0])){

				
				$grandtotals = $totalpayment ;
				$newvalue = $totalpayment-$oldheader['remainingprevious'];
				
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnow = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnow = 0;
				}
				
				
				}
				else{
				$grandtotals = $totalpayment+$oldheader['remainingprevioush'];
				}
				
				//$grandtotals = headerpayment['totalpayment']
				//$totalpayment = headerpayment['grandtotals']
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
			
				
				}
				
				else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				$totalpayment = abs($ttlfolpaym);
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				
				$newvalue = $totalpayment-$oldheader['remainingprevioush'];
				
				if (!empty($checknulfremainingprevious[0])){
				
				$grandtotals = $totalpayment + $oldheader['remainingprevious'];
				
				}
				else{
				if ($newvalue < 0 ){				
				$grandtotals = 0;
				$remainingnowh = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnowh = 0;
				}
				}

				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				}
				
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
				}
				
				//jika ada data di detailpayment
				else
				{
				
				$payment->setId($checkdebt['hpid']);
				
				$payment->setDetailId($getdetail['dpid']);
				
				$olddetails = $payment->getDetailPaymentFromSale($_POST['id'],"buy");
				$payment->updateDetailPayment($_POST['id'],$_POST['totalbuy'],0,"","buy","bb",0,$olddetails );
				$oldestheader = $payment->getHeaderPayment();
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldestheader);
				$payment->setId($checkdebt['hpid']);
				
				$oldheader = $payment->getHeaderPayment();
				
				
				
				if ($oldheader['status'] == 1){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				
				$grandtotals = 0;

				$totalpayment = $ttlfolpaym;
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				
				
				if (!empty($checknulfremainingprevious[0])){

				
				$grandtotals = $totalpayment ;
				$newvalue = $totalpayment-$oldheader['remainingprevious'];
				
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnow = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnow = 0;
				}
				
				
				}
				else{
				$grandtotals = $totalpayment+$oldheader['remainingprevioush'];
				}
				
				$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
				
				if (!empty($totalrepayment['totalrepay'])){
				if ($grandtotals < $totalrepayment['totalrepay'] ){
				$remainingnow = $totalrepayment['totalrepay']-$grandtotals;
				}
				else{
				$remainingnow = 0;
				}
				}
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
			
				
				}
				
				else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				$totalpayment = abs($ttlfolpaym);
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				
				
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				if (!empty($checknulfremainingprevious[0])){
				$grandtotals = $totalpayment + $oldheader['remainingprevious'];
				
				}
				else{
				$newvalue = $totalpayment-$oldheader['remainingprevioush'];
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnowh = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnowh = 0;
				}
				}
				
				$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
				
				if (!empty($totalrepayment['totalrepay'])){
				if ($grandtotals < $totalrepayment['totalrepay'] ){
				$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
				}
				else{
				$remainingnowh = 0;
				}
				}
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
				
				
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
				}
				
				
				}
				
			
			}
			/* echo "<br> grandtotals : ";
			echo $grandtotals;
			echo "<br> ttl pay : ";
			echo abs($totalpayment);
			echo "<br> R now : ";
			echo $remainingnow;
			echo "<br> R nowh : ";
			echo $remainingnowh;
			echo "<br>";
			echo "ss"; */
			}
			else{
			$checkdebt = $payment->getallHeaderPaymentByMonth($buydate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
			$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$_POST['id']."' AND types = 'buy' ");
			
			if (!empty($getdetail['dpid'])){
			
			$payment->setDetailId($getdetail['dpid']);
			$payment->deleteDetailPayment();
			
			$payment->setId($checkdebt['hpid']);
			
			$oldestheader = $payment->getHeaderPayment();
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldestheader);
			$payment->setId($checkdebt['hpid']);
			
			$oldheader = $payment->getHeaderPayment();
			
			
			if ($oldheader['status'] == 1){
			
			$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
			
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
			$grandtotals = 0;
			$totalpayment = $ttlfolpaym;
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			
			
			if (!empty($checknulfremainingprevious[0])){

			
			$grandtotals = $totalpayment ;
			$newvalue = $totalpayment-$oldheader['remainingprevious'];
			
			if ($newvalue < 0 ){
			$grandtotals = 0;
			$remainingnow = abs($newvalue);
			}
			else{
			$grandtotals = abs($newvalue);
			$remainingnow = 0;
			}
			
			
			}
			else{
			$grandtotals = $totalpayment+$oldheader['remainingprevioush'];
			}
			
			$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
			
			if (!empty($totalrepayment['totalrepay'])){
			if ($grandtotals < $totalrepayment['totalrepay'] ){
			$remainingnow = $totalrepayment['totalrepay']-$grandtotals;
			}
			else{
			$remainingnow = 0;
			}
			}
			
			$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
			
		
			
			}
			
			else{
			
			$payment->setId($checkdebt['hpid']);
			$oldheader = $payment->getHeaderPayment();
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
			$totalpayment = abs($ttlfolpaym);
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
			
			$grandtotals = $totalpayment ;
			$newvalue = $totalpayment-$oldheader['remainingprevioush'];
			
			if (!empty($checknulfremainingprevious[0])){
			$grandtotals = $totalpayment + $oldheader['remainingprevious'];
				
			}
			else{
			if ($newvalue < 0 ){
			$grandtotals = 0;
			$remainingnowh = abs($newvalue);
			}
			else{
			$grandtotals = abs($newvalue);
			$remainingnowh = 0;
			}

			}
			
			$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
			
			if (!empty($totalrepayment['totalrepay'])){
			if ($grandtotals < $totalrepayment['totalrepay'] ){
			$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
			}
			else{
			$remainingnowh = 0;
			}
			}
			
			$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);	
			
			}
			
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
			
			
			}
			
			/* else{
			
			$payment->setId($checkdebt['hpid']);
			$oldheader = $payment->getHeaderPayment();
			
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - (($totalpaybuy['totalpay']+$_POST['totalbuy']) - $totalpaybuyr['totalpay']);
			
			if ($ttlfolpaym > 0){
			
			$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
			
			
			$totalpayment = $ttlfolpaym;
			$grandtotals = $oldheader['grandtotals']+ $_POST['totalbuy'];
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			
			
			if (!empty($checknulfremainingprevious[0])){

			
			$grandtotals = $totalpayment ;
			$newvalue = $totalpayment-$oldheader['remainingprevious'];
			
			if ($newvalue < 0 ){
			$grandtotals = 0;
			$remainingnow = abs($newvalue);
			}
			else{
			$grandtotals = abs($newvalue);
			$remainingnow = 0;
			}
			
			
			}
			else{
			$grandtotals = $totalpayment+$remainingnowh;
			}
			
			//$grandtotals = headerpayment['totalpayment']
			//$totalpayment = headerpayment['grandtotals']
			
			$payment->updateHeaderPaymentOnlycash(abs($grandtotals),abs($totalpayment),$userid,$remainingnow,$remainingnowh);
			
			$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
			}
			
			else{
			
			$payment->setId($checkdebt['hpid']);
			$oldheader = $payment->getHeaderPayment();

			$totalpayment = abs($ttlfolpaym);
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			
			$grandtotals = abs($totalpayment - $oldheader['remainingprevious']);
			
			$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
			
			$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
			
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);

			
			}

			} */
			
			
			
			}
			
			}
			//jika sama header dulu & sekarang
			
			
			//jika tidak sama header dulu & sekarang
			else{
			//hapus data yang lama
			$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$getoldheaderbuy['suppliercode']."' ");
			
			
			
			$checkdebt = $payment->getallHeaderPaymentByMonth($getoldheaderbuy['buydate'],1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$getoldheadersale['customeraddrid']);
			$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$getoldheaderbuy['buyid']."' AND types = 'buy' ");
			
			if (!empty($getdetail['dpid'])){
			
			$payment->setDetailId($getdetail['dpid']);
			$payment->deleteDetailPayment();
			
			$payment->setId($checkdebt['hpid']);
			
			$oldestheader = $payment->getHeaderPayment();
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$oldestheader['complete'],$oldestheader);
			$payment->setId($checkdebt['hpid']);
			
			$oldheader = $payment->getHeaderPayment();
			
			
			if ($oldheader['status'] == 1){
			
			$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
			
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
			$grandtotals = 0;
			$totalpayment = $ttlfolpaym;
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			
			
			if (!empty($checknulfremainingprevious[0])){

			
			$grandtotals = $totalpayment ;
			$newvalue = $totalpayment-$oldheader['remainingprevious'];
			
			if ($newvalue < 0 ){
			$grandtotals = 0;
			$remainingnow = abs($newvalue);
			}
			else{
			$grandtotals = abs($newvalue);
			$remainingnow = 0;
			}
			
			
			}
			else{
			$grandtotals = $totalpayment+$oldheader['remainingprevioush'];
			}
			
			$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
			
			if (!empty($totalrepayment['totalrepay'])){
			if ($grandtotals < $totalrepayment['totalrepay'] ){
			$remainingnow = $totalrepayment['totalrepay']-$grandtotals;
			}
			else{
			$remainingnow = 0;
			}
			}
			
			$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
			
		
			
			}
			
			else{
			
			$payment->setId($checkdebt['hpid']);
			$oldheader = $payment->getHeaderPayment();
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
			$totalpayment = abs($ttlfolpaym);
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
			
			$grandtotals = $totalpayment ;
			$newvalue = $totalpayment-$oldheader['remainingprevioush'];
			
			if (!empty($checknulfremainingprevious[0])){
			$grandtotals = $totalpayment + $oldheader['remainingprevious'];
				
			}
			else{
			if ($newvalue < 0 ){
			$grandtotals = 0;
			$remainingnowh = abs($newvalue);
			}
			else{
			$grandtotals = abs($newvalue);
			$remainingnowh = 0;
			}

			}
			$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
			
			if (!empty($totalrepayment['totalrepay'])){
			if ($grandtotals < $totalrepayment['totalrepay'] ){
			$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
			}
			else{
			$remainingnowh = 0;
			}
			}
			
			$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);	
			
			}
			
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$oldheader['complete'],$oldheader);
			
			
			}//hapus data yang lama

			//tambah data baru
			if ($_POST['trtype'] == "credit"){
			
			$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$_POST['suppliercode']."' ");

			
			$checkdebt = $payment->getallHeaderPaymentByMonth($buydate,2,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],0);

			
			//jika blm ada data hutang di periode bln ini
			if (empty($checkdebt['hpid'])){
			
			
			$lastmonth = $payment->getDetailLastPaymentByMonth($buydate,2,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			$remainingprevious = 0;
			$remainingprevioush = 0;
			$remainingnow  = 0;
			$remainingnowh  = 0;
			
			
			
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
			
			
			$checknulfremainingprevioush = explode(".",$remainingprevioush);
			$checknulfremainingprevious = explode(".",$remainingprevious);
			
			
			//jika remainingnow ada
			if (!empty($checknulfremainingprevious[0])){
			$grandtotals = $_POST['totalbuy'] + $remainingprevious;
			}
			
			//jika remainingnowh ada
			else if (!empty($checknulfremainingprevioush[0])){
			
			$grandtotals = $_POST['totalbuy'] - $remainingprevioush;
			if ($grandtotals < 0){
			$remainingnowh = abs($grandtotals);
			$grandtotals = 0;
			}	
			else{
			$remainingnowh = 0;
			$grandtotals = abs($grandtotals);
			}
			
			}
			else {
			$grandtotals = $_POST['totalbuy'];
			}
			
			if ($grandtotals <=0 ){
			$complete = 1;
			$completedate = $buydate;
			}
			else{
			$complete = 0;
			$completedate = 0;
			}
			
			$invstartdate = strtotime('01-'.date("m-Y",$buydate));
			$invenddate = strtotime(date('t-m-Y',$buydate).' 23:59:59');
			
		
			$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$buydate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST['totalbuy'],$grandtotals,$userid,2,$remainingprevioush,$remainingnowh);
			
			$payment->setId($lastidpaym);
			$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
			
			$payment->updateDebtCreditnotlive($_POST['suppliercode'],0);
			}
			
			//jika ada data hutang di periode bln ini
			else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - (($totalpaybuy['totalpay']+$_POST['totalbuy']) - $totalpaybuyr['totalpay']);
				//$ttlfolpaym = $oldheader['totalforsale'] - ($oldheader['totalforbuy']+$_POST['totalbuy']);
				
				
				
				
				
				if ($oldheader['status'] == 1){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				$grandtotals = 0;
				$totalpayment = $ttlfolpaym;
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				
				
				if (!empty($checknulfremainingprevious[0])){
				if ($ttlfolpaym > 0 ){
				
				
				$newvalue = $ttlfolpaym-$oldheader['remainingprevious'];
				
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnow = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnow = 0;
				}
				}
				
				else if ($ttlfolpaym <= 0 ){
				$grandtotals = abs($ttlfolpaym - $_POST['totalbuy']);
				$remainingnow = 0;
				}
				
				}
				else{
				$grandtotals = abs($totalpayment+$oldheader['remainingprevioush']);
				}
				
				$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
				
				if (!empty($totalrepayment['totalrepay'])){
				if ($grandtotals < $totalrepayment['totalrepay'] ){
				$remainingnow = $totalrepayment['totalrepay']-$grandtotals;
				}
				else{
				$remainingnow = 0;
				}
				}
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
				
				}
				
				else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();

				$totalpayment = abs($ttlfolpaym);
				
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				$newvalue = $totalpayment-$oldheader['remainingprevioush'];
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				
				if (!empty($checknulfremainingprevious[0])){
				$grandtotals = $totalpayment + $oldheader['remainingprevious'];
				
				}
				else{
				if ($newvalue < 0 ){
				$grandtotals = 0;
				$remainingnowh = abs($newvalue);
				}
				else{
				$grandtotals = abs($newvalue);
				$remainingnowh = 0;
				}
				}
				
				$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
				
				if (!empty($totalrepayment['totalrepay'])){
				if ($grandtotals < $totalrepayment['totalrepay'] ){
				$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
				}
				else{
				$remainingnowh =0;
				}
				}
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
			
				
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalbuy'],$buydate,"","buy",0,0);
				$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
				}

			}
			
				/* echo "Sub Total (Rp.) : ".abs($_POST['totalbuy']);
				echo "<br>Grand Total (Rp.) :".abs($grandtotals);
				echo "<br>Rprev h : ".abs($remainingprevioush);
				echo "<br>Rprev   : ".abs($remainingprevious);
				echo "<br>Rnow h : ".abs($remainingnowh);
				echo "<br>now   : ".abs($remainingnow); */
			
			
		}
		
			//tambah data baru
			
			
			}

			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailpchbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$purchase->setDetailId($arrpostdel[$x]);
						$olddetail = $purchase->getDetailBuyIndv();
						if ($olddetail['usedqty'] == 0){
							$stock->setCode($olddetail['stockcode']);
							$purchase->deleteDetailBuy();
							$stock->minStock($olddetail['quantity'],'deleted');
						}
					}
				}
			}
			
			//edited rows
			$totalitems = 0;
			$arrpost = array_diff($arrpostt,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			$detailreturnbuyid = '';
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x])){
						$checkchars = strpos($_POST["detailpchbox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailpchbox_".$arrpost[$x]."_0"] = substr($_POST["detailpchbox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$purchase->setDetailId($arrpost[$x]);
						$olddetail = $purchase->getDetailBuyIndv();
						
						$_POST["detailpchbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailpchbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailpchbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_8"],'calculate');
						$_POST["detailpchbox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_9"],'calculate');
						
						$stock->setId("");
						$stock->setCode($_POST["detailpchbox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];

						//$_POST["detailpchbox_".$arrpost[$x]."_3"] = $getstock['brandcode'];
						//$_POST["detailpchbox_".$arrpost[$x]."_4"] = $getstock['typecode'];
						
						$totals = $_POST["detailpchbox_".$arrpost[$x]."_5"] * $_POST["detailpchbox_".$arrpost[$x]."_7"];
						$purchasepricead = $_POST["detailpchbox_".$arrpost[$x]."_7"] - ($_POST["detailpchbox_".$arrpost[$x]."_8"] / 100 * $_POST["detailpchbox_".$arrpost[$x]."_7"]);
						$realbuyprice = $purchasepricead - ($_POST['disc'] / 100 * $purchasepricead);
						$realbuyprice = $realbuyprice + ($_POST['tax'] / 100 * $realbuyprice);
						
						$_POST["detailpchbox_".$arrpost[$x]."_10"] = strtotime($_POST["detailpchbox_".$arrpost[$x]."_10"]);
						
						if ($_POST["detailpchbox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailpchbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realbuyprice = $realbuyprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailpchbox_".$arrpost[$x]."_5"];
							$_POST["detailpchbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
						}
						
						$totalitems += $quantity;
						
						$purchase->updateDetailBuy($_POST["detailpchbox_".$arrpost[$x]."_0"],$_POST["detailpchbox_".$arrpost[$x]."_1"],$_POST["detailpchbox_".$arrpost[$x]."_2"],$_POST["detailpchbox_".$arrpost[$x]."_3"],$_POST["detailpchbox_".$arrpost[$x]."_4"],$_POST["detailpchbox_".$arrpost[$x]."_5"],$_POST["detailpchbox_".$arrpost[$x]."_6"],$_POST["detailpchbox_".$arrpost[$x]."_7"],$_POST["detailpchbox_".$arrpost[$x]."_8"],$_POST["detailpchbox_".$arrpost[$x]."_9"],$_POST["detailpchbox_".$arrpost[$x]."_10"],$_POST["detailpchbox_".$arrpost[$x]."_11"],$buydate,$totals,$purchasepricead,$realbuyprice,$quantity,$unitquantity,$getstock['unitcode'],$olddetail);
						
						/* update if any return from this detail buy */
						$ones = $db->fetch_one("SELECT dbr.buyrid, dbr.buyrprice, dbr.disc, dbr.quantity, dbri.dbrid, dbri.dbid FROM detailbuyr dbr INNER JOIN detailbuyritem dbri ON dbr.dbrid = dbri.dbrid WHERE dbri.dbid = '".$olddetail['dbid']."'");
						if (!empty($ones['dbrid'])){
							$headerdisc = $_POST['disc'];
							$headertax = $_POST['tax'];
							$theprices = $_POST["detailpchbox_".$arrpost[$x]."_7"];
							if ($_POST["detailpchbox_".$arrpost[$x]."_6"] == $getunit['funit']){
								if ($getunit['cvalue'] > 0){
									$theprices = $_POST["detailpchbox_".$arrpost[$x]."_7"] / $getunit['cvalue'];
								}
							}
							$buyrpricead = $theprices - ($_POST["detailpchbox_".$arrpost[$x]."_8"] / 100 * $_POST["detailpchbox_".$arrpost[$x]."_7"]);
							$buyrpricead = $buyrpricead - ($headerdisc / 100 * $buyrpricead);
							$buyrpricead = $buyrpricead + ($headertax / 100 * $buyrpricead);
							
							$updates['buyrprice'] = $theprices;
							$updates['totals'] = $theprices * $ones['quantity'];
							$updates['disc'] = $_POST["detailpchbox_".$arrpost[$x]."_8"];
							$updates['extdisc'] = $headerdisc;
							$updates['tax'] = $headertax;
							$updates['buyrpricead'] = $buyrpricead;
							$updates['totalbuyrad'] = $buyrpricead * $ones['quantity'];
							$updates['realbuyrprice'] = $buyrpricead;
							$db->update("detailbuyr",$updates,"dbrid='".$db->clean($ones['dbrid'])."'");
							
							$detailreturnbuyid .= ','.$ones['dbrid'];
						}
						unset($updates);
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailpchbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailpchbox_".$arrpost[$x]."_0"])){
						$checkchars = strpos($_POST["detailpchbox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailpchbox_".$arrpost[$x]."_0"] = substr($_POST["detailpchbox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["detailpchbox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailpchbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailpchbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailpchbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailpchbox_".$arrpost[$x]."_8"],'calculate');
						
						$totals = $_POST["detailpchbox_".$arrpost[$x]."_5"] * $_POST["detailpchbox_".$arrpost[$x]."_7"];
						$buypricead = $_POST["detailpchbox_".$arrpost[$x]."_7"] - ($_POST["detailpchbox_".$arrpost[$x]."_8"] / 100 * $_POST["detailpchbox_".$arrpost[$x]."_7"]);
						$realbuyprice = $buypricead - ($_POST['disc'] / 100 * $buypricead);
						$realbuyprice = $realbuyprice + ($_POST['tax'] / 100 * $realbuyprice);
						
						if ($_POST["detailpchbox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailpchbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realbuyprice = $realbuyprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailpchbox_".$arrpost[$x]."_5"];
							$_POST["detailpchbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
						}
						
						$totalitems += $quantity;
						
						$purchase->setBuyNo($_POST['buyno']);
						$purchase->saveDetailBuy($_POST["detailpchbox_".$arrpost[$x]."_0"],$_POST["detailpchbox_".$arrpost[$x]."_1"],$_POST["detailpchbox_".$arrpost[$x]."_2"],$_POST["detailpchbox_".$arrpost[$x]."_3"],$_POST["detailpchbox_".$arrpost[$x]."_4"],$_POST["detailpchbox_".$arrpost[$x]."_5"],$_POST["detailpchbox_".$arrpost[$x]."_6"],$_POST["detailpchbox_".$arrpost[$x]."_7"],$_POST["detailpchbox_".$arrpost[$x]."_8"],togglenumber($_POST["detailpchbox_".$arrpost[$x]."_9"],'calculate'),strtotime($_POST["detailpchbox_".$arrpost[$x]."_10"]),$_POST["detailpchbox_".$arrpost[$x]."_11"],$buydate,$totals,$buypricead,$realbuyprice,$quantity,$unitquantity,$getstock['unitcode']);
					}
				}
			}

			/* update realbuyprice by adding the other pays */
			if ($_POST['otherpays'] > 0 && $totalitems > 0){
				$additionalpays = $_POST['otherpays'] / $totalitems;
				$db->query("UPDATE detailbuy SET realbuyprice = realbuyprice + ".$additionalpays." WHERE buyno = '".$db->clean($_POST['buyno'])."'");
				
				if (!empty($detailreturnbuyid)){
					$detailreturnbuyid = substr($detailreturnbuyid,1);
					$db->query("UPDATE detailbuyr SET realbuyrprice = realbuyrprice + ".$additionalpays.", otherpays = '".$additionalpays."', totalbuyrad = realbuyrprice * quantity WHERE dbrid IN (".$db->clean($detailreturnbuyid).")");
							
					/* update headerbuyr */
					$alldetails = $db->fetch_all("SELECT * FROM detailbuyr WHERE dbrid IN (".$db->clean($detailreturnbuyid).")");
					if (sizeof($alldetails) > 0){
						foreach ($alldetails as $ads){
							$gettotals = $db->fetch_one("SELECT SUM(totalbuyrad) AS totals FROM detailbuyr WHERE buyrid='".$db->clean($ads['buyrid'])."'");
							$db->query("UPDATE headerbuyr SET totals='".$db->clean($gettotals['totals'])."', totalbuyr='".$db->clean($gettotals['totals'])."' WHERE buyrid='".$ads['buyrid']."'");
						}
					}
				}
			}
			
			$db->endTransaction();
		}
		redirecting("purchase.php?no=".$_POST['buyno']);
	}

	$buyno = $_GET['no'];

	if ($_GET['do'] == 'delete' && !empty($buyno) && $useraccess['delete_purchase']){
		$db->beginTransaction();
		
		$purchase->setBuyNo($buyno);
		if ($purchase->canDeleteBuy()){
			$headerbuy = $purchase->getHeaderBuy();
			/* if ($headerbuy['trtype'] == "credit"){
			$supplier->setCode($headerbuy['suppliercode']);
			$supplier->minDebt($headerbuy['totalbuy']);
			} */
			
			if ($headerbuy['trtype'] == "credit"){
			$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$headerbuy['suppliercode']."' ");
			$checkdebt = $payment->getallHeaderPaymentByMonth($headerbuy['buydate'],1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$headerbuy['customeraddrid']);
			$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$headerbuy['buyid']."' AND types = 'buy' ");
			
			if (!empty($getdetail['dpid'])){
			
			$payment->setDetailId($getdetail['dpid']);
			$payment->deleteDetailPayment();
			
			$payment->setId($checkdebt['hpid']);
			
			$oldestheader = $payment->getHeaderPayment();
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldestheader);
			$payment->setId($checkdebt['hpid']);
			
			$oldheader = $payment->getHeaderPayment();
			
			if ($oldheader['status'] == 1){
			
			$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
			
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
			$grandtotals = 0;
			$totalpayment = $ttlfolpaym;
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			
			
			if (!empty($checknulfremainingprevious[0])){

			
			$grandtotals = $totalpayment ;
			$newvalue = $totalpayment-$oldheader['remainingprevious'];
			
			if ($newvalue < 0 ){
			$grandtotals = 0;
			$remainingnow = abs($newvalue);
			}
			else{
			$grandtotals = abs($newvalue);
			$remainingnow = 0;
			}
			
			
			}
			else{
			$grandtotals = $totalpayment+$oldheader['remainingprevioush'];
			}
			
			$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
			
			if (!empty($totalrepayment['totalrepay'])){
			if ($grandtotals < $totalrepayment['totalrepay'] ){
			$remainingnow = $totalrepayment['totalrepay']-$grandtotals;
			}
			else{
			$remainingnow = 0;
			}
			}
			
			$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
			
		
			
			}
			
			else{
			
			$payment->setId($checkdebt['hpid']);
			$oldheader = $payment->getHeaderPayment();
			$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
			$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
			$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
			$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
			$totalpayment = abs($ttlfolpaym);
			$remainingnow = $oldheader['remainingnow'];
			$remainingnowh = $oldheader['remainingnowh'];
			$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
			$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
			
			$grandtotals = $totalpayment ;
			$newvalue = $totalpayment-$oldheader['remainingprevioush'];
			
			if (!empty($checknulfremainingprevious[0])){
			$grandtotals = $totalpayment + $oldheader['remainingprevious'];
				
			}
			else{
			if ($newvalue < 0 ){
			$grandtotals = 0;
			$remainingnowh = abs($newvalue);
			}
			else{
			$grandtotals = abs($newvalue);
			$remainingnowh = 0;
			}

			}
			
			$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
			
			if (!empty($totalrepayment['totalrepay'])){
			if ($grandtotals < $totalrepayment['totalrepay'] ){
			$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
			}
			else{
			$remainingnowh = 0;
			}
			}
			
			$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);	
			
			}
			
			$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
			
			
			
			
			
			}
			
		}
			
			$purchase->deleteBuy();
		}
		
		$db->endTransaction();
		redirecting("purchase.php?screen=list");		
	}

	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_purchase'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'purchaselist';
	}
	else{
		
		if (empty($useraccess['add_purchase']) && empty($useraccess['edit_purchase'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'purchase';
		if (!empty($buyno)){
			$purchase->setBuyNo($buyno);
			$headerbuy = $purchase->getHeaderBuy();
			
			if (empty($headerbuy['buyid'])){
				redirecting('purchase.php?screen=list');
			}
			
			$invoicedate = date("d-m-Y",$headerbuy['buydate']);
			$invoiceduedate = date("d-m-Y",$headerbuy['duedate']);
			$terms = floor(($headerbuy['duedate'] - $headerbuy['buydate']) / 86400);
			
			$invstartdate = strtotime('01-'.date("m-Y",$headerbuy['buydate']));
			
			
			
			
			
			$supplier->setCode($headerbuy['suppliercode']);
			$supplier->setDetailId($headerbuy['supplieraddrid']);
			$getsupplier = $supplier->getsupplierDetail();
			$area->setCode($getsupplier['areacode']);
			$dbarea = $area->getareaDetail();
			if (!empty($dbarea['areaname'])){
				$areaname = ' '.$dbarea['areaname'];
			}
			$suppliercperson = htmlspecialchars($getsupplier['contactperson']);
			$suppliername = htmlspecialchars($getsupplier['suppliername']);
			$supplieraddr = htmlspecialchars($getsupplier['address'].$areaname);
			$suppliertelp = htmlspecialchars($getsupplier['phone']);
						
			$alldtl = $purchase->getDetailBuy();
			$alldetailid = '';
			$allcannotdel = '';
			$allidusedqty = '';
			
			$subtotalfk = 0;
			if (sizeof($alldtl) > 0){
				foreach ($alldtl as $aad){
					$stock->setId("");
					$stock->setCode($aad['stockcode']);
					$getds = $stock->getFirstStock();
					
					$alldetailid .= ',r-'.$aad['dbid'];
					if ($aad['usedqty'] > 0){
						$allcannotdel .= ',"r-'.$aad['dbid'].'"';
						$allidusedqty .= ','.$aad['usedqty'];
					}
					
					$units->setCode($getds['unitcode']);
					$getunit = $units->getunitDetail();
					$arrunits .= '
						arrunits["r-'.$aad['dbid'].'"] = new Array();
						arrunits["r-'.$aad['dbid'].'"][0] = "'.$getunit['funit'].'";
						arrunits["r-'.$aad['dbid'].'"][1] = "'.$getunit['lunit'].'";';
					$arrconversion .= '
						arrconversion["r-'.$aad['dbid'].'"] = new Array();
						arrconversion["r-'.$aad['dbid'].'"][0] = '.$getunit['cvalue'].';
						arrconversion["r-'.$aad['dbid'].'"][1] = 1;';
				
					$aad['quantityf'] = floor((100-$discount['extradisc'])/100 * $aad['quantityf']);
					if ($aad['quantityf'] < 1){
						$aad['quantityf'] = 1;
					}
					
					$totalbuyfk = $aad['quantityf'] * $aad['buyprice'];
					$totaldiscfk = $aad['disc'] / 100 * $totalbuyfk;
					$subtotalfk += ($totalbuyfk - $totaldiscfk);
				}
				$alldetailid = substr($alldetailid,1);
				if (!empty($allcannotdel)){
					$allcannotdel = substr($allcannotdel,1);
					$allidusedqty = substr($allidusedqty,1);
				}
			}
			
			if ($statususer == 1){
				$headerbuy['totals'] = $subtotalfk;
				$totalgdiscfk = $headerbuy['disc'] / 100 * $subtotalfk;
				$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
				$totalgtaxfk = $headerbuy['tax'] / 100 * $totalafgdiscfk;
				$headerbuy['totalbuy'] = $totalafgdiscfk + $totalgtaxfk;
			}
			
			$ftotals = number_format($headerbuy['totals'],2,",",".");
			$fdisc = number_format($headerbuy['disc'],2,",",".");
			$ftax = number_format($headerbuy['tax'],2,",",".");
			$fotherpays = number_format($headerbuy['otherpays'],2,",",".");
			$ftotal = number_format($headerbuy['totalbuy'],2,",",".");
			
			$headerbuy = array_map("htmlspecialchars",$headerbuy);
		}
	}

	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>