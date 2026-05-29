<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/area.php";
	require_once "class/customer.php";
	require_once "class/supplier.php";
	require_once "class/units.php";
	require_once "class/Sale.php";
	require_once "class/Assembly.php";
	require_once "class/DeAssembly.php";
	require_once "class/Payment.php";
	$stock = new Stock();
	$payment = new Payment();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	$customer = new customer();
	$supplier = new supplier();
	$area = new area();
	$sale = new Sale();
	$assembly = new Assembly();
	$deassembly = new DeAssembly();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$sale->setId($_POST['id']);
		}
		echo $sale->checkSaleNoExist(trim($_POST['no']),strtotime($_POST['date']));
		exit;
	}

	$headersale['totals'] = 0;
	$headersale['disc'] = 0;
	$headersale['tax'] = 0;
	$headersale['totalsale'] = 0;
	$invoicedate = date("d-m-Y");
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['no'])){
				$sale->setSaleNo($_GET['no']);
				$allsale = $sale->getDetailSale();
				if (sizeof($allsale) > 0){
					foreach ($allsale as $as){
						$brand->setCode($as['brandcode']);
						$getbrandname = $brand->getBrandDetail();
						$type->setCode($as['typecode']);
						$gettypename = $type->gettypeDetail();
						$units->setCode($as['unitcode']);
						$getunitname = $units->getunitDetail();
						
						if ($statususer == 1){
							$as['quantityf'] = floor((100-$discount['extradisc'])/100 * $as['quantityf']);
							if ($as['quantityf'] < 1){
								$as['quantityf'] = 1;
							}
							$totalsalefk = $as['quantityf'] * $as['saleprice'];
							$totaldiscfk = $as['disc'] / 100 * $totalsalefk;
							$as['totalsalead'] = $totalsalefk - $totaldiscfk;
						}
						
						$lists .= '
							<row id="r-'.$as['dsid'].'">
								<cell>'.htmlspecialchars($as['stockcode']).'</cell>
								<cell>'.htmlspecialchars($as['partno']).'</cell>
								<cell>'.htmlspecialchars($as['stockname']).'</cell>
								<cell>'.htmlspecialchars($as['brandcode']).'</cell>
								<cell>'.htmlspecialchars($as['typecode']).'</cell>
								<cell>'.number_format($as['quantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($as['unitquantityf']).'</cell>
								<cell>'.number_format($as['saleprice'],2,",",".").'</cell>
								<cell>'.number_format($as['disc'],2,",",".").'</cell>
								<cell>'.number_format($as['totalsalead'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($as['description']).'</cell>
							</row>
						';
					}
				}
			}
			$slist = gettemplate('saledetaillist');
		}
		else if ($_GET['list'] == 'general'){
			/* if (isset($_GET['keyword'])){
				if ($_GET['keyword'] != ''){ */
					$allsale = $sale->searchSale($_GET['keyword'],$_GET['field'],$_GET['trtype']);
					$totalrows = sizeof($allsale);
					$totalpgs = ceil($totalrows / $general['showperpage']);
					$pgs = handlepage($_GET['p'],$totalpgs);
					
					$listsale = $sale->searchSale($_GET['keyword'],$_GET['field'],$_GET['trtype'],$pgs);
				/* }
			} */
			//$listsale = $sale->getListSale('all');
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listsale) > 0){
				$ctr = 1;
				foreach ($listsale as $list){
					$customer->setCode($list['customercode']);
					$getcustomername = $customer->getcustomerDetail();

					$sale->setSaleNo($list['saleno']);
					$getdetailsale = $sale->getDetailSale();
					$splits = sizeof($getdetailsale);
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0;
						$subtotalfk = 0;
						foreach ($getdetailsale as $gdb){
							$gdb['typecode'] = wordwrap($gdb['typecode'],15,"<br>",true);
							$gdb['brandcode'] = wordwrap($gdb['brandcode'],10,"<br>",true);
							
							$gdb['typecode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['typecode']));
							$gdb['brandcode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['brandcode']));
							
							if ($statususer == 1){
								$gdb['quantityf'] = floor((100-$discount['extradisc'])/100 * $gdb['quantityf']);
								if ($gdb['quantityf'] < 1){
									$gdb['quantityf'] = 1;
								}
								
								$totalsalefk = $gdb['quantityf'] * $gdb['saleprice'];
								$totaldiscfk = $gdb['disc'] / 100 * $totalsalefk;
								$subtotalfk += ($totalsalefk - $totaldiscfk);
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
									<td width="'.$cwarr[11].'" class="stufflist" id="saleprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['saleprice']).'</td>
								';
							}
							else{
								$listsplit2 .= '
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'sale.php?no='.$list['saleno'].'\',\'_self\')">
										<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
										<td width="'.$cwarr[5].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
										<td width="'.$cwarr[6].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
										<td width="'.$cwarr[7].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
										<td width="'.$cwarr[8].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
										<td width="'.$cwarr[9].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
										<td width="'.$cwarr[10].'" class="stufflist" align="center">'.htmlspecialchars($gdb['unitquantityf']).'</td>
										<td width="'.$cwarr[11].'" class="stufflist" id="saleprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['saleprice']).'</td>
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
									<td width="'.$cwarr[11].'" class="stufflist" id="saleprice_'.$ctr.'-'.$io.'" align="right"></td>
								';
					}
					
					/*$lists .= '
						<row id="'.$list['saleno'].'">
							<cell>'.htmlspecialchars($list['saleno']).'</cell>
							<cell>'.date("d-m-Y",$list['saledate']).'</cell>
							<cell>'.date("d-m-Y",$list['duedate']).'</cell>
							<cell>'.htmlspecialchars($getcustomername['customername']).'</cell>
							<cell>'.$codest->convertcodes($list['disc']).'</cell>
							<cell>'.$codest->convertcodes($list['tax']).'</cell>
							<cell>'.$codest->convertcodes($list['totalsale']).'</cell>
							<cell>'.$arrsale[$list['status']].'</cell>
							<cell>'.(($useraccess['edit_sale'])?'Ubah^sale.php?no='.$list['saleno'].'^_self':'-').'</cell>
							<cell>'.(($useraccess['delete_sale'])?'Hapus^javascript:deleteitem("sale.php?do=delete&amp;no='.$list['saleno'].'")^_self':'-').'</cell>
						</row>
					';*/
					$actioneditwidth = floor(51 / 100 * $cwarr[14]);
					$actiondeletewidth = $cwarr[14]-$actioneditwidth-3;
					
					if ($statususer == 1){
						$totalgdiscfk = $list['disc'] / 100 * $subtotalfk;
						$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
						$totalgtaxfk = $list['tax'] / 100 * $totalafgdiscfk;
						$list['totalsale'] = $totalafgdiscfk + $totalgtaxfk;
					}
					
					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'sale.php?no='.$list['saleno'].'\',\'_self\')">
								<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.wordwrap($list['saleno'],8,"<br>",true).'</td>
								<td class="stufflist" width="'.$cwarr[1].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['saledate']).'</td>
								<td class="stufflist" width="'.$cwarr[2].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['duedate']).'</td>
								<td class="stufflist" width="'.$cwarr[3].'" align="left"'.$rstext.'>'.htmlspecialchars($getcustomername['customername']).'</td>
								'.$listsplit.'
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[12].'" align="right"'.$rstext.'>'.$codest->convertcodes($list['totalsale']).'</td>
								<td class="stufflist" width="'.$cwarr[13].'" align="center"'.$rstext.'>'.$arrsale[$list['paid']].'</td>
								<td class="stufflist bgseparator" width="'.$cwarr[14].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
								(($useraccess['edit_sale'])?'<a href="sale.php?no='.$list['saleno'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
								(($useraccess['delete_sale'] && $sale->canDeleteSale())?'<a href="javascript:deleteitem(\'sale.php?do=delete&no='.$list['saleno'].'\')">Hapus</a>':'-').'</span></td>
							</tr>'.$listsplit2.'
					';					
					
					$ctr++;
				}
			}
			$ctrgo = $ctr-1;
			/* $slist = gettemplate('salelistdetail'); */
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
		}
		else if ($_GET['list'] == 'determine'){
			header("Content-type: text/xml");
			$listsale = $sale->getListSale('all');
			$lists = '';
			if (sizeof($listsale) > 0){
				foreach ($listsale as $list){
					$customer->setCode($list['customercode']);
					$getcustomername = $customer->getcustomerDetail();

					$lists .= '
						<row id="'.$list['saleno'].'">
							<cell>'.htmlspecialchars($list['saleno']).'</cell>
							<cell>'.date("d-m-Y",$list['saledate']).'</cell>
							<cell>'.date("d-m-Y",$list['duedate']).'</cell>
							<cell>'.htmlspecialchars($getcustomername['customername']).'</cell>
							<cell>'.$list['disc'].'</cell>
							<cell>'.$list['tax'].'</cell>
							<cell>'.$list['totalsale'].'</cell>
						</row>
					';
				}
			}
			$slist = gettemplate('salelistdetail');
		}
		eval("\$slist = \"$slist\";");
		echo $slist;
		exit;
	}
	else if ($_GET['getlist'] == 'ajax'){
		if ($_GET['list'] == 'customer' && !empty($_GET['no'])){
			$sale->setSaleNo($_GET['no']);
			$headersale = $sale->getHeaderSale();
			if (!empty($headersale['customercode'])){
				$customer->setCode($headersale['customercode']);
				$customer->setDetailId($headersale['customeraddrid']);
				$getcustomer = $customer->getcustomerDetail();
				$area->setCode($getcustomer['areacode']);
				$dbarea = $area->getareaDetail();
				if (!empty($dbarea['areaname'])){
					$areaname = ' '.$dbarea['areaname'];
				}
				$customercperson = htmlspecialchars($getcustomer['contactperson']);
				$customername = htmlspecialchars($getcustomer['customername']);
				$customeraddr = htmlspecialchars($getcustomer['address'].$areaname);
				$customertelp = htmlspecialchars($getcustomer['phone']);
				$feedback = $headersale['customercode'].'|^|'.$headersale['customeraddrid'].'|^|'.$customername.'|^|'.$customercperson.'|^|'.$customeraddr.'|^|'.$customertelp;
			}
		}
		echo $feedback;
		exit;
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_sale']){
		//print_r($_POST);
		$db->beginTransaction();
		$saledate = strtotime($_POST['saledate']);
		$duedate = strtotime($_POST['duedate']);
		$startyear =  strtotime('01-01-'.date("Y",$saledate));
		$endyear =  strtotime('31-12-'.date("Y",$saledate).' 23:59:59');
		if ($_POST['customeraddrid'] == '-1'){
				$customer->setCode($_POST['customercode']);
				$getccode = $customer->getcustomerDetail('partial');
				$customer->setId($getccode['customerid']);
				$getcaddr = $customer->getcustomeraddrdetail('all');
				if (sizeof($getcaddr) > 0){
					foreach ($getcaddr as $gca){
						$_POST['customeraddrid'] = $gca['detailcustid'];
						break;
					}
				}
				
				$supplier->setCode($_POST['customercode']);
				$getccode = $supplier->getsupplierDetail('partial');
				$supplier->setId($getccode['supplierid']);
				$getcaddr = $supplier->getsupplieraddrdetail('all');
				if (sizeof($getcaddr) > 0){
					foreach ($getcaddr as $gca){
						$_POST['supplieraddrid'] = $gca['detailsplid'];
						break;
					}
				}
				
			}
			else{
			$detailcustomerad = $db->fetch_one("SELECT * FROM detailcustomer where detailcustid = '".$_POST['customeraddrid']."' ");
			$detailsupplierad = $db->fetch_one("SELECT * FROM detailsupplier where address = '".$detailcustomerad['address']."' ");
			$_POST['supplieraddrid'] = $detailsupplierad['detailsplid'];
			}
			
		$lastsaleno = $sale->saveHeaderSale($_POST['saleno'],$saledate,$duedate,$_POST['customercode'],$_POST['customeraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['totalsale'],$_POST['trtype'],$userid);
		
			
			$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");


			
			$dbcustomer = $db->fetch_one("SELECT * FROM customer WHERE customercode = '".$_POST['customercode']."' ");
			
			//jika pembelian dilakukan scr kredit 
			if ($_POST['trtype'] == "credit"){
			
			$getsaleid = $db->fetch_one("SELECT * FROM headersale WHERE saleno = '".$lastsaleno."' ");
			$_POST['id'] = $getsaleid['saleid'];
			
			$checkdebt = $payment->getHeaderPaymentByMonth($saledate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			//jika blm ada data hutang di periode bln ini
			if (empty($checkdebt['hpid'])){
			
			
			$lastmonth = $payment->getDetailLastPaymentByMonth($saledate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
			
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
			
			$grandtotals = $_POST['totalsale'] + $remainingprevioush;
			}
			else if (!empty($checknulfremainingprevious[0])){
			
			$grandtotals = $_POST['totalsale'] - $remainingprevious;
			
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
			
			$grandtotals = $_POST['totalsale'];
			}
			
			if ($grandtotals <=0 ){
			$complete = 1;
			$completedate = $saledate;
			}
			else{
			$complete = 0;
			$completedate = 0;
			}
			
			$invstartdate = strtotime('01-'.date("m-Y",$saledate));
			$invenddate = strtotime(date('t-m-Y',$saledate).' 23:59:59');
			
			
			
			$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$saledate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST['totalsale'],$grandtotals,$userid,1,$remainingprevioush,$remainingnowh);
			
			$payment->setId($lastidpaym);
			$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
			$payment->updateDebtCreditnotlive($_POST['customercode'],0);
			
			
			}
			
			//jika ada data hutang di periode bln ini
			else{
			
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( ($totalpaysale['totalpay']+$_POST['totalsale']) - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				
				
				//$ttlfolpaym = ($oldheader['totalforsale']+$_POST['totalsale']) - $oldheader['totalforbuy'];
				if ($ttlfolpaym > 0){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				
				$totalpayment = $ttlfolpaym;
				$grandtotals = $oldheader['grandtotals']+ $_POST['totalsale'];
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
				
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
				}
				
				else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				
				$ttlfolpaym = ( ($totalpaysale['totalpay']+$_POST['totalsale']) - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				//$ttlfolpaym = ($oldheader['totalforsale']+$_POST['totalsale']) - $oldheader['totalforbuy'];
				$totalpayment = abs($ttlfolpaym);
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				$grandtotals = 0;
				
				if (!empty($checknulfremainingprevious[0])){

				
				$grandtotals = $totalpayment ;
				$grandtotals = $totalpayment+$oldheader['remainingprevious'];

				
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
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
				
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);

				
				}
				
			
			}
			/* echo "Sub Total (Rp.) : ".abs($_POST['totalsale']);
			echo "<br>Grand Total (Rp.) :".abs($grandtotals);
			echo "<br>Rprev h : ".abs($remainingprevioush);
			echo "<br>Rprev   : ".abs($remainingprevious);
			echo "<br>Rnow h : ".abs($remainingnowh);
			echo "<br>now   : ".abs($remainingnow); */
			
		}
		
		
		$arrpostdel = explode(",",$_POST['detailsalebox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailsalebox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		$errmsg = '';
		$cachesalebox = '';
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["detailsalebox_".$arrpost[$x]."_0"])){					
					$checkchars = strpos($_POST["detailsalebox_".$arrpost[$x]."_0"],"||");
					if ($checkchars !== false){
						$_POST["detailsalebox_".$arrpost[$x]."_0"] = substr($_POST["detailsalebox_".$arrpost[$x]."_0"],0,$checkchars);
					}
					
					/* show on grid if any error occured */
					$cachesalebox .= '
						mygrid.addRow('.$arrpost[$x].',["'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_0"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_1"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_2"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_3"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_4"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_5"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_6"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_7"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_8"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_9"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_10"]).'"]);
					';
					
					$stock->setId("");
					$stock->setCode($_POST["detailsalebox_".$arrpost[$x]."_0"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
				
					$_POST["detailsalebox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_5"],'calculate');
					$_POST["detailsalebox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_7"],'calculate');
					$_POST["detailsalebox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_8"],'calculate');
					
					$totals = $_POST["detailsalebox_".$arrpost[$x]."_5"] * $_POST["detailsalebox_".$arrpost[$x]."_7"];
					$salepricead = $_POST["detailsalebox_".$arrpost[$x]."_7"] - ($_POST["detailsalebox_".$arrpost[$x]."_8"] / 100 * $_POST["detailsalebox_".$arrpost[$x]."_7"]);
					$realsaleprice = $salepricead - ($_POST['disc'] / 100 * $salepricead);
					$realsaleprice = $realsaleprice + ($_POST['tax'] / 100 * $realsaleprice);

					if ($_POST["detailsalebox_".$arrpost[$x]."_6"] == $getunit['funit']){
						$quantity = $_POST["detailsalebox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
						$realsaleprice = $realsaleprice / $getunit['cvalue'];
					}
					else{
						$quantity = $_POST["detailsalebox_".$arrpost[$x]."_5"];
						$_POST["detailsalebox_".$arrpost[$x]."_6"] = $getunit['lunit'];
					}
					
					if ($getstock['realremaining'] < $quantity && $getstock['assembly'] != 1){
						$errmsg .= '<div class="error">- Kode Barang : '.$getstock['stockcode'].' melebihi sisa stok sekarang : '.$getstock['realremaining'].'</div>';
					}
					
					$sale->setSaleNo($lastsaleno);
					$sale->saveDetailSale($_POST["detailsalebox_".$arrpost[$x]."_0"],$_POST["detailsalebox_".$arrpost[$x]."_1"],$_POST["detailsalebox_".$arrpost[$x]."_2"],$_POST["detailsalebox_".$arrpost[$x]."_3"],$_POST["detailsalebox_".$arrpost[$x]."_4"],$_POST["detailsalebox_".$arrpost[$x]."_5"],$_POST["detailsalebox_".$arrpost[$x]."_6"],$_POST["detailsalebox_".$arrpost[$x]."_7"],$_POST["detailsalebox_".$arrpost[$x]."_8"],togglenumber($_POST["detailsalebox_".$arrpost[$x]."_9"],'calculate'),$_POST["detailsalebox_".$arrpost[$x]."_10"],$saledate,$totals,$salepricead,$realsaleprice,$quantity,$unitquantity,$getstock['unitcode']);
				}
			}
		}
		
		
		
		$db->endTransaction();
		if (empty($errmsg)){
			redirecting("sale.php?no=".$lastsaleno);
		}
		else{
			$headersale['saleno'] = $_POST['saleno'];
			$invoicedate = $_POST['saledate'];
			$headersale['customercode'] = $_POST['customercode'];
			$headersale['customeraddrid'] = $_POST['customeraddrid'];
			
			$customer->setId('');
			$customer->setCode($_POST['customercode']);
			$customer->setDetailId($_POST['customeraddrid']);
			$getcustomer = $customer->getCustomerDetail();
			$area->setCode($getcustomer['areacode']);
			$dbarea = $area->getareaDetail();
			if (!empty($dbarea['areaname'])){
				$areaname = ' '.$dbarea['areaname'];
			}
			$customercperson = htmlspecialchars($getcustomer['contactperson']);
			$customername = htmlspecialchars($getcustomer['customername']);
			$customeraddr = htmlspecialchars($getcustomer['address'].$areaname);
			$customertelp = htmlspecialchars($getcustomer['phone']);
			
			$headersale['trtype'] = $_POST['trtype'];
			$terms = $_POST['terms'];
			$invoiceduedate = $_POST['duedate'];
			
			$headersale['description'] = $_POST['description'];
			$headersale['totals'] = $_POST['totals'];
			$ftotals = number_format($_POST['totals'],2,",",".");
			$fdisc = number_format($_POST['disc'],2,",",".");
			$ftax = number_format($_POST['tax'],2,",",".");
			$headersale['totalsale'] = $_POST['totalsale'];
			$ftotal = number_format($_POST['totalsale'],2,",",".");
		}
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_sale']){
		//print_r($_POST);
		if (!empty($_POST['id'])){
			$sale->setId($_POST['id']);
			
			$db->beginTransaction();
			$saledate = strtotime($_POST['saledate']);
			$duedate = strtotime($_POST['duedate']);
			$startyear =  strtotime('01-01-'.date("Y",$saledate));
			$endyear =  strtotime('31-12-'.date("Y",$saledate).' 23:59:59');
			if ($_POST['customeraddrid'] == '-1'){
				$customer->setCode($_POST['customercode']);
				$getccode = $customer->getcustomerDetail('partial');
				$customer->setId($getccode['customerid']);
				$getcaddr = $customer->getcustomeraddrdetail('all');
				if (sizeof($getcaddr) > 0){
					foreach ($getcaddr as $gca){
						$_POST['customeraddrid'] = $gca['detailcustid'];
						break;
					}
				}
				
				$supplier->setCode($_POST['customercode']);
				$getccode = $supplier->getsupplierDetail('partial');
				$supplier->setId($getccode['supplierid']);
				$getcaddr = $supplier->getsupplieraddrdetail('all');
				if (sizeof($getcaddr) > 0){
					foreach ($getcaddr as $gca){
						$_POST['supplieraddrid'] = $gca['detailsplid'];
						break;
					}
				}
				
			}
			else{
			$detailcustomerad = $db->fetch_one("SELECT * FROM detailcustomer where detailcustid = '".$_POST['customeraddrid']."' ");
			$detailsupplierad = $db->fetch_one("SELECT * FROM detailsupplier where address = '".$detailcustomerad['address']."' ");
			$_POST['supplieraddrid'] = $detailsupplierad['detailsplid'];
			}
			
			$getoldheadersale = $sale->getHeaderSale();
			
			$sale->updateHeaderSale($_POST['saleno'],$saledate,$duedate,$_POST['customercode'],$_POST['customeraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['totalsale'],$_POST['trtype'],$userid);
			
			
			if ( $getoldheadersale['customercode'] == $_POST['customercode'] )
			{
			
			if ($saledate == $getoldheadersale['saledate']){
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
			
			$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");
			
			$dbcustomer = $db->fetch_one("SELECT * FROM customer WHERE customercode = '".$_POST['customercode']."' ");
			
			//jika pembelian dilakukan scr kredit 
			if ($_POST['trtype'] == "credit"){
			
			

			
			$checkdebt = $payment->getallHeaderPaymentByMonth($saledate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			//jika blm ada data hutang di periode bln ini
			if (empty($checkdebt['hpid'])){
			
			
			$lastmonth = $payment->getDetailLastPaymentByMonth($saledate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
			
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
			
			$grandtotals = $_POST['totalsale'] + $remainingprevioush;
			}
			else if (!empty($checknulfremainingprevious[0])){
			
			$grandtotals = $_POST['totalsale'] - $remainingprevious;
			
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
			
			$grandtotals = $_POST['totalsale'];
			}
			
			if ($grandtotals <=0 ){
			$complete = 1;
			$completedate = $saledate;
			}
			else{
			$complete = 0;
			$completedate = 0;
			}
			
			$invstartdate = strtotime('01-'.date("m-Y",$saledate));
			$invenddate = strtotime(date('t-m-Y',$saledate).' 23:59:59');
			
			
			
			$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$saledate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST['totalsale'],$grandtotals,$userid,1,$remainingprevioush,$remainingnowh);
			
			$payment->setId($lastidpaym);
			$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
			$payment->updateDebtCreditnotlive($_POST['customercode'],0);
			
			
			}
			
			//jika ada data hutang di periode bln ini
			else{
				
				$payment->setId($checkdebt['hpid']);
				$toldestheader = $payment->getHeaderPayment();
				
				$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$_POST['id']."' AND types = 'sale' ");
				
				 
				
				//jika belum ada data di detailpayment
				if (empty($getdetail['dpid']))
				{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
				
				$pheader = $payment->getHeaderPayment();
				
				if ($pheader['status'] == 1){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( $totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				
				$grandtotals = 0;

				$totalpayment = abs($ttlfolpaym);
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
				
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
				
				}
				
				//jika ada data di detailpayment
				else
				{
			
				$payment->setId($checkdebt['hpid']);
				
				$payment->setDetailId($getdetail['dpid']);
				
				$olddetails = $payment->getDetailPaymentFromSale($_POST['id'],"sale");
				$payment->updateDetailPayment($_POST['id'],$_POST['totalsale'],0,"","sale","bb",0,$olddetails );
				$oldestheader = $payment->getHeaderPayment();
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldestheader);
				$payment->setId($checkdebt['hpid']);
				
				$oldheader = $payment->getHeaderPayment();
				
				if ($oldheader['status'] == 1){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				

				$grandtotals = 0;
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				
				//$ttlfolpaym = ($oldheader['totalforsale']+$_POST['totalsale']) - $oldheader['totalforbuy'];
				
				
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
				$grandtotals = 0;

				if (!empty($checknulfremainingprevious[0])){

				
				$grandtotals = $totalpayment ;
				$grandtotals = $totalpayment+$oldheader['remainingprevious'];

				
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
				
				
				
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$oldheader['complete'],$oldheader);
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
		$checkdebt = $payment->getallHeaderPaymentByMonth($saledate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
		$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$_POST['id']."' AND types = 'sale' ");
		
		if (!empty($getdetail['dpid'])){
		
		$payment->setDetailId($getdetail['dpid']);
		$payment->deleteDetailPayment();
		
		$payment->setId($checkdebt['hpid']);
		
		$oldestheader = $payment->getHeaderPayment();
		$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldestheader);
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
		
		
		$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$oldheader['complete'],$oldheader);
		
		
		
		}
		
		/* else{
		
		$payment->setId($checkdebt['hpid']);
		$oldheader = $payment->getHeaderPayment();
		
		
		$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
		$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
		$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
		$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
		$ttlfolpaym = ( ($totalpaysale['totalpay']+$_POST['totalsale']) - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
		
		if ($ttlfolpaym > 0){
		
		$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
		$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
		
		
		$totalpayment = $ttlfolpaym;
		$grandtotals = $oldheader['grandtotals']+ $_POST['totalsale'];
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
		
		$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$remainingnow,$remainingnowh);
		
		$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
		$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
		}
		
		else{
		
		$payment->setId($checkdebt['hpid']);
		$oldheader = $payment->getHeaderPayment();
		$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
		$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
		$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
		$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
		$ttlfolpaym = ( ($totalpaysale['totalpay']+$_POST['totalsale']) - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
		
		$totalpayment = abs($ttlfolpaym);
		
		$remainingnow = $oldheader['remainingnow'];
		$remainingnowh = $oldheader['remainingnowh'];
		
		$grandtotals = abs($totalpayment - $oldheader['remainingprevious']);
		
		$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
		
		$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
		
		$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);

		
		}

		} */
		
		
		}
		
		}//jika sama header dulu & sekarang
		
		//jika tidak sama header dulu & sekarang
		else{
		
		//hapus data yang lama
		$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$getoldheadersale['customercode']."' ");

		$checkdebt = $payment->getallHeaderPaymentByMonth($getoldheadersale['saledate'],1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$getoldheadersale['customeraddrid']);
		$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$getoldheadersale['saleid']."' AND types = 'sale' ");
		
		if (!empty($getdetail['dpid'])){
		
		$payment->setDetailId($getdetail['dpid']);
		$payment->deleteDetailPayment();
		
		$payment->setId($checkdebt['hpid']);
		
		$oldestheader = $payment->getHeaderPayment();
		$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$oldestheader['complete'],$oldestheader);
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
		
		
		$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$oldheader['complete'],$oldheader);

		}//hapus data yang lama
		
		//tambah data baru
		if ($_POST['trtype'] == "credit"){
			
			$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");
			
			$checkdebt = $payment->getallHeaderPaymentByMonth($saledate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
			
			//jika blm ada data hutang di periode bln ini
			if (empty($checkdebt['hpid'])){
			
			
			$lastmonth = $payment->getDetailLastPaymentByMonth($saledate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
			
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
			
			$grandtotals = $_POST['totalsale'] + $remainingprevioush;
			}
			else if (!empty($checknulfremainingprevious[0])){
			
			$grandtotals = $_POST['totalsale'] - $remainingprevious;
			
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
			
			$grandtotals = $_POST['totalsale'];
			}
			
			if ($grandtotals <=0 ){
			$complete = 1;
			$completedate = $saledate;
			}
			else{
			$complete = 0;
			$completedate = 0;
			}
			
			$invstartdate = strtotime('01-'.date("m-Y",$saledate));
			$invenddate = strtotime(date('t-m-Y',$saledate).' 23:59:59');
			
			
			
			$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$saledate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST['totalsale'],$grandtotals,$userid,1,$remainingprevioush,$remainingnowh);
			
			$payment->setId($lastidpaym);
			$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
			$payment->updateDebtCreditnotlive($_POST['customercode'],0);
			
			
			}
			
			//jika ada data hutang di periode bln ini
			else{
			
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				
				$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
				$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
				$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
				$ttlfolpaym = ( ($totalpaysale['totalpay']+$_POST['totalsale']) - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				
				
				//$ttlfolpaym = ($oldheader['totalforsale']+$_POST['totalsale']) - $oldheader['totalforbuy'];
				if ($ttlfolpaym > 0){
				
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				
				
				$totalpayment = $ttlfolpaym;
				$grandtotals = $oldheader['grandtotals']+ $_POST['totalsale'];
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
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
				}
				
				else{
				
				$payment->setId($checkdebt['hpid']);
				$oldheader = $payment->getHeaderPayment();
				
				$ttlfolpaym = ( ($totalpaysale['totalpay']+$_POST['totalsale']) - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
				//$ttlfolpaym = ($oldheader['totalforsale']+$_POST['totalsale']) - $oldheader['totalforbuy'];
				$totalpayment = abs($ttlfolpaym);
				$remainingnow = $oldheader['remainingnow'];
				$remainingnowh = $oldheader['remainingnowh'];
				$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
				$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
				$grandtotals = 0;
				
				if (!empty($checknulfremainingprevious[0])){

				
				$grandtotals = $totalpayment ;
				$grandtotals = $totalpayment+$oldheader['remainingprevious'];

				
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
				
				$totalrepayment = $db->fetch_one("SELECT SUM(totals) AS totalrepay FROM detailrepayment WHERE hpid='".$checkdebt['hpid']."'");
				
				if (!empty($totalrepayment['totalrepay'])){
				if ($grandtotals < $totalrepayment['totalrepay'] ){
				$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
				}
				else{
				$remainingnowh = 0;
				}
				}
				}
				
				$payment->updateHeaderPaymentOnlycash(abs($totalpayment),abs($grandtotals),$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
				
				$payment->saveDetailPayment($_POST['id'],$_POST['totalsale'],$saledate,"","sale",0,0);
				
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);

				
				}
				
			
			}
			
			
		}
		
		
		
		
		}
			
			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailsalebox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$sale->setDetailId($arrpostdel[$x]);
						$olddetail = $sale->getDetailSaleIndv();
						$stock->setId("");
						$stock->setCode($olddetail['stockcode']);
						$getfs = $stock->getFirstStock();
						if ($getfs['assembly'] == 1){
							$assembly->setCode($olddetail['stockcode']);
							$getac = $assembly->getAssemblyComponent();
							if (sizeof($getac) > 0){
								foreach ($getac as $gac){
									$stock->setCode($gac['stockcodecomponent']);
									$sale->deleteDetailItem($gac['stockcodecomponent']);
									$stock->addStock($olddetail['quantity']*$gac['sccquantity']);
								}
							}
							$db->query("DELETE FROM detailsale WHERE dsid='".$sale->dtid."'");
						}
						else if ($getfs['assembly'] == 2){
							$sale->deleteDetailItemDeAssembly($olddetail['stockcode']);
							$db->query("DELETE FROM detailsale WHERE dsid='".$sale->dtid."'");
						}
						else{
							$sale->deleteDetailItem($olddetail['stockcode']);
							$db->query("DELETE FROM detailsale WHERE dsid='".$sale->dtid."'");
							$stock->addStock($olddetail['quantity']);
						}
					}
				}
			}
			
			//edited rows
			$arrpost = array_diff($arrpostt,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x])){
						$checkchars = strpos($_POST["detailsalebox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailsalebox_".$arrpost[$x]."_0"] = substr($_POST["detailsalebox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$sale->setDetailId($arrpost[$x]);
						$olddetail = $sale->getDetailSaleIndv();
						
						$_POST["detailsalebox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailsalebox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailsalebox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_8"],'calculate');
						$_POST["detailsalebox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_9"],'calculate');
												
						$stock->setId("");
						$stock->setCode($_POST["detailsalebox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						//$_POST["detailsalebox_".$arrpost[$x]."_3"] = $getstock['brandcode'];
						//$_POST["detailsalebox_".$arrpost[$x]."_4"] = $getstock['typecode'];
											
						$totals = $_POST["detailsalebox_".$arrpost[$x]."_5"] * $_POST["detailsalebox_".$arrpost[$x]."_7"];
						$salepricead = $_POST["detailsalebox_".$arrpost[$x]."_7"] - ($_POST["detailsalebox_".$arrpost[$x]."_8"] / 100 * $_POST["detailsalebox_".$arrpost[$x]."_7"]);
						$realsaleprice = $salepricead - ($_POST['disc'] / 100 * $salepricead);
						$realsaleprice = $realsaleprice + ($_POST['tax'] / 100 * $realsaleprice);

						if ($_POST["detailsalebox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailsalebox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realsaleprice = $realsaleprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailsalebox_".$arrpost[$x]."_5"];
							$_POST["detailsalebox_".$arrpost[$x]."_6"] = $getunit['lunit'];
						}
						
						if (($getstock['realremaining'] + $olddetail['quantity']) < $quantity && $getstock['assembly'] != 1){
							$errmsg .= '<div class="error">- Kode Barang : '.$getstock['stockcode'].' melebihi sisa stok sekarang : '.$getstock['realremaining'].'</div>';
						}
						
						$sale->updateDetailSale($_POST["detailsalebox_".$arrpost[$x]."_0"],$_POST["detailsalebox_".$arrpost[$x]."_1"],$_POST["detailsalebox_".$arrpost[$x]."_2"],$_POST["detailsalebox_".$arrpost[$x]."_3"],$_POST["detailsalebox_".$arrpost[$x]."_4"],$_POST["detailsalebox_".$arrpost[$x]."_5"],$_POST["detailsalebox_".$arrpost[$x]."_6"],$_POST["detailsalebox_".$arrpost[$x]."_7"],$_POST["detailsalebox_".$arrpost[$x]."_8"],$_POST["detailsalebox_".$arrpost[$x]."_9"],$_POST["detailsalebox_".$arrpost[$x]."_10"],$totals,$salepricead,$realsaleprice,$quantity,$unitquantity,$getstock['unitcode'],$olddetail);
						
						/* update if any return from this detail sale */
						$ones = $db->fetch_one("SELECT * FROM detailsaler WHERE dsid = '".$olddetail['dsid']."'");
						if (!empty($ones['dsrid'])){
							$headerdisc = $_POST['disc'];
							$headertax = $_POST['tax'];
							$salerpricead = $_POST["detailsalebox_".$arrpost[$x]."_7"] - ($_POST["detailsalebox_".$arrpost[$x]."_8"] / 100 * $_POST["detailsalebox_".$arrpost[$x]."_7"]);
							$salerpricead = $salerpricead - ($headerdisc / 100 * $salerpricead);
							$salerpricead = $salerpricead + ($headertax / 100 * $salerpricead);
							
							$updates['salerprice'] = $_POST["detailsalebox_".$arrpost[$x]."_7"];
							$updates['totals'] = $_POST["detailsalebox_".$arrpost[$x]."_7"] * $ones['quantity'];
							$updates['disc'] = $_POST["detailsalebox_".$arrpost[$x]."_8"];
							$updates['extdisc'] = $headerdisc;
							$updates['tax'] = $headertax;
							$updates['salerpricead'] = $salerpricead;
							$updates['totalsalerad'] = $salerpricead * $ones['quantity'];
							$updates['realsalerprice'] = $salerpricead;
							$db->update("detailsaler",$updates,"dsrid='".$ones['dsrid']."'");
							
							/* update headersaler */
							$onesh = $db->fetch_one("SELECT * FROM headersaler WHERE salerid='".$ones['salerid']."'");
							if (!empty($onesh['salerid'])){
								$gettotals = $db->fetch_one("SELECT SUM(totalsalerad) AS totals FROM detailsaler WHERE salerid='".$onesh['salerid']."'");
								$db->query("UPDATE headersaler SET totalsaler='".$gettotals['totals']."' WHERE salerid='".$onesh['salerid']."'");
							}
						}
						unset($updates);
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailsalebox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailsalebox_".$arrpost[$x]."_0"])){
						$checkchars = strpos($_POST["detailsalebox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailsalebox_".$arrpost[$x]."_0"] = substr($_POST["detailsalebox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						/* show on grid if any error occured */
						$cachesalebox .= '
							mygrid.addRow('.$arrpost[$x].',["'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_0"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_1"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_2"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_3"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_4"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_5"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_6"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_7"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_8"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_9"]).'","'.str_replace('"','\"',$_POST["detailsalebox_".$arrpost[$x]."_10"]).'"]);
						';
						
						$stock->setId("");
						$stock->setCode($_POST["detailsalebox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailsalebox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailsalebox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailsalebox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailsalebox_".$arrpost[$x]."_8"],'calculate');
						
						$totals = $_POST["detailsalebox_".$arrpost[$x]."_5"] * $_POST["detailsalebox_".$arrpost[$x]."_7"];
						$salepricead = $_POST["detailsalebox_".$arrpost[$x]."_7"] - ($_POST["detailsalebox_".$arrpost[$x]."_8"] / 100 * $_POST["detailsalebox_".$arrpost[$x]."_7"]);
						$realsaleprice = $salepricead - ($_POST['disc'] / 100 * $salepricead);
						$realsaleprice = $realsaleprice + ($_POST['tax'] / 100 * $realsaleprice);

						if ($_POST["detailsalebox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailsalebox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realsaleprice = $realsaleprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailsalebox_".$arrpost[$x]."_5"];
							$_POST["detailsalebox_".$arrpost[$x]."_6"] = $getunit['lunit'];
						}
					
						if ($getstock['realremaining'] < $quantity && $getstock['assembly'] != 1){
							$errmsg .= '<div class="error">- Kode Barang : '.$getstock['stockcode'].' melebihi sisa stok sekarang : '.$getstock['realremaining'].'</div>';
						}

						$sale->setSaleNo($_POST['saleno']);
						$sale->saveDetailSale($_POST["detailsalebox_".$arrpost[$x]."_0"],$_POST["detailsalebox_".$arrpost[$x]."_1"],$_POST["detailsalebox_".$arrpost[$x]."_2"],$_POST["detailsalebox_".$arrpost[$x]."_3"],$_POST["detailsalebox_".$arrpost[$x]."_4"],$_POST["detailsalebox_".$arrpost[$x]."_5"],$_POST["detailsalebox_".$arrpost[$x]."_6"],$_POST["detailsalebox_".$arrpost[$x]."_7"],$_POST["detailsalebox_".$arrpost[$x]."_8"],togglenumber($_POST["detailsalebox_".$arrpost[$x]."_9"],'calculate'),$_POST["detailsalebox_".$arrpost[$x]."_10"],$saledate,$totals,$salepricead,$realsaleprice,$quantity,$unitquantity,$getstock['unitcode']);
					}
				}
			}

			/* update sale date on detail sale */
			$db->query("UPDATE detailsale SET saledate='".$saledate."' WHERE saleno='".$_POST['saleno']."'");

			$db->endTransaction();
		}
		if (empty($errmsg)){
			redirecting("sale.php?no=".$_POST['saleno']);
		}
		else{
			$_GET['no'] = $_POST['saleno'];
			$_REQUEST['no'] = $_POST['saleno'];
		}
	}
	
	$saleno = $_GET['no'];
	
	if ($_GET['do'] == 'delete' && !empty($saleno) && $useraccess['delete_sale']){
		$db->beginTransaction();
		
		$sale->setSaleNo($saleno);
		$headersale = $sale->getHeaderSale();
		
		if ($headersale['trtype'] == "credit"){
				
				$dbcustomer = $db->fetch_one("SELECT * FROM customer WHERE customercode = '".$headersale['customercode']."' ");
				$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$headersale['customercode']."' ");
				$checkdebt = $payment->getallHeaderPaymentByMonth($headersale['saledate'],1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$headersale['customeraddrid']);
				$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$headersale['saleid']."' AND types = 'sale' ");
				
				if (!empty($getdetail['dpid'])){
				
				$payment->setDetailId($getdetail['dpid']);
				$payment->deleteDetailPayment();

				$payment->setId($checkdebt['hpid']);

				$oldestheader = $payment->getHeaderPayment();
				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldestheader);
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

				

				$payment->updateDebtCreditEdit($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
				




				}
				
				
				
			}

		
		
		//$customer->setCode($headersale['customercode']);
		//$customer->minCredit($headersale['totalsale']);
		
		$sale->deleteSale();
		
		$db->endTransaction();
		redirecting("sale.php?screen=list");		
	}
	
	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_sale'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'salelist';
	}
	else{
		
		if (empty($useraccess['add_sale']) && empty($useraccess['edit_sale'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'sale';
		if (!empty($saleno)){
			$sale->setId("");
			$sale->setSaleNo($saleno);
			$headersale = $sale->getHeaderSale();
			
			if (empty($headersale['saleid'])){
				redirecting('sale.php?screen=list');
			}
			
			$invoicedate = date("d-m-Y",$headersale['saledate']);
			$invoiceduedate = date("d-m-Y",$headersale['duedate']);
			$terms = floor(($headersale['duedate'] - $headersale['saledate']) / 86400);
			
			$customer->setCode($headersale['customercode']);
			$customer->setDetailId($headersale['customeraddrid']);
			$getcustomer = $customer->getCustomerDetail();
			$area->setCode($getcustomer['areacode']);
			$dbarea = $area->getareaDetail();
			if (!empty($dbarea['areaname'])){
				$areaname = ' '.$dbarea['areaname'];
			}
			$customercperson = htmlspecialchars($getcustomer['contactperson']);
			$customername = htmlspecialchars($getcustomer['customername']);
			$customeraddr = htmlspecialchars($getcustomer['address'].$areaname);
			$customertelp = htmlspecialchars($getcustomer['phone']);
			
			$alldtl = $sale->getDetailSale();
			$alldetailid = '';
			$allcannotdel = '';
			$allidreturn = '';
			
			$subtotalfk = 0;
			if (sizeof($alldtl) > 0){
				foreach ($alldtl as $aad){
					$stock->setId("");
					$stock->setCode($aad['stockcode']);
					$getds = $stock->getFirstStock();
					
					$alldetailid .= ',r-'.$aad['dsid'];
					if ($aad['returnsale'] > 0){
						$allcannotdel .= ',"r-'.$aad['dsid'].'"';
						$allidreturn .= ','.$aad['returnsale'];
					}
					
					$arrsaleprice .= 'arrsaleprice["r-'.$aad['dsid'].'"] = '.$getds['sellprice'].';';
					if ($getds['assembly'] == 1){
						$assembly->setCode($aad['stockcode']);
						$getac = $assembly->getAssemblyComponent();
						$tempmax = 0;
						if (sizeof($getac) > 0){
							foreach ($getac as $gac){
								$stock->setId("");
								$stock->setCode($gac['stockcodecomponent']);
								$stockcmpt = $stock->getFirstStock();
								if ($tempmax == 0){
									$tempmax = floor($stockcmpt['realremaining']/$gac['sccquantity']);
								}
								else{
									$tempmax = min($tempmax,floor($stockcmpt['realremaining']/$gac['sccquantity']));
								}
							}
						}
						$arrstockqty .= 'arrstockqty["r-'.$aad['dsid'].'"] = '.$tempmax.';';
					}
					else if ($getds['assembly'] == 2){
						$getac = $deassembly->getDeAssemblyParent($aad['stockcode']);
						if (sizeof($getac) > 0){
							$stock->setId("");
							$stock->setCode($getac['stockcode']);
							$parentfirststock = $stock->getFirstStock();
							$tempmax = $stockoption['realremaining'] + $parentfirststock['realremaining'] * $getac['sccquantity'];
						}
						$arrstockqty .= 'arrstockqty["r-'.$aad['dsid'].'"] = '.$tempmax.';';
					}
					else{
						$arrstockqty .= 'arrstockqty["r-'.$aad['dsid'].'"] = '.$getds['realremaining'].';';
					}
					$tempqtycode .= 'tempqtycode["r-'.$aad['dsid'].'"] = \''.$aad['stockcode'].'\';';
					$tempqtyforedit .= 'tempqtyforedit["r-'.$aad['dsid'].'"] = '.$aad['quantityf'].';';

					$arrunitscur .= 'tempunitsc["r-'.$aad['dsid'].'"] = \''.$aad['unitquantityf'].'\';';
					
					$units->setCode($getds['unitcode']);
					$getunit = $units->getunitDetail();
					$arrunits .= '
						arrunits["r-'.$aad['dsid'].'"] = new Array();
						arrunits["r-'.$aad['dsid'].'"][0] = "'.$getunit['funit'].'";
						arrunits["r-'.$aad['dsid'].'"][1] = "'.$getunit['lunit'].'";';
					$arrconversion .= '
						arrconversion["r-'.$aad['dsid'].'"] = new Array();
						arrconversion["r-'.$aad['dsid'].'"][0] = '.$getunit['cvalue'].';
						arrconversion["r-'.$aad['dsid'].'"][1] = 1;';
				
					$aad['quantityf'] = floor((100-$discount['extradisc'])/100 * $aad['quantityf']);
					if ($aad['quantityf'] < 1){
						$aad['quantityf'] = 1;
					}
						
					$totalsalefk = $aad['quantityf'] * $aad['saleprice'];
					$totaldiscfk = $aad['disc'] / 100 * $totalsalefk;
					$subtotalfk += ($totalsalefk - $totaldiscfk);
				}
				$alldetailid = substr($alldetailid,1);
				if (!empty($allcannotdel)){
					$allcannotdel = substr($allcannotdel,1);
					$allidreturn = substr($allidreturn,1);
				}
			}
			
			if ($statususer == 1){
				$headersale['totals'] = $subtotalfk;
				$totalgdiscfk = $headersale['disc'] / 100 * $subtotalfk;
				$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
				$totalgtaxfk = $headersale['tax'] / 100 * $totalafgdiscfk;
				$headersale['totalsale'] = $totalafgdiscfk + $totalgtaxfk;
			}
			
			$ftotals = number_format($headersale['totals'],2,",",".");	
			$fdisc = number_format($headersale['disc'],2,",",".");
			$ftax = number_format($headersale['tax'],2,",",".");
			$ftotal = number_format($headersale['totalsale'],2,",",".");
			
			$headersale = array_map("htmlspecialchars",$headersale);
		}
		else{
			//$lastids = $db->getlastid("headersale","saleid");
			if (empty($errmsg)){
				$dblastnumber = $db->fetch_one("SELECT * FROM stockyear WHERE year='".$yearsoftwarenow."'");
				if (!empty($dblastnumber['salenumber'])){
					$lastids = $dblastnumber['salenumber'];
				}
				else{
					$lastids = 1;
				}
				$headersale['saleno'] = str_pad($lastids,5,'0',STR_PAD_LEFT);
			}
		}
	}

	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>