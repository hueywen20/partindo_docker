<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	require_once "class/customer.php";
	require_once "class/supplier.php";
	require_once "class/area.php";
	require_once "class/Sale.php";
	require_once "class/SaleR.php";
	require_once "class/Assembly.php";
	require_once "class/Payment.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$supplier = new supplier();
	$stgr = new stgr();
	$units = new unit();
	$customer = new customer();
	$area = new area();
	$payment = new Payment();
	$sale = new Sale();
	$saler = new SaleR();
	$assembly = new Assembly();
	
	$headersaler['totals'] = 0;
	$headersaler['disc'] = 0;
	$headersaler['tax'] = 0;
	$headersaler['totalsaler'] = 0;
	$invoicedate = date("d-m-Y");
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['id'])){
				$saler->setId($_GET['id']);
				$allsaler = $saler->getDetailSaleR();
				if (sizeof($allsaler) > 0){
					foreach ($allsaler as $ap){
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
						
						$getdbdetail = $db->fetch_one("SELECT saleno FROM detailsale WHERE dsid='".$ap['dsid']."'");
						
						if ($statususer == 1){
							$ap['quantityf'] = floor((100-$discount['extradisc'])/100 * $ap['quantityf']);
							if ($ap['quantityf'] < 1){
								$ap['quantityf'] = 1;
							}
							$totalsalerfk = $ap['quantityf'] * $ap['salerprice'];
							$totaldiscfk = $ap['disc'] / 100 * $totalsalerfk;
							$ap['totalsalerad'] = $totalsalerfk - $totaldiscfk;
							$ap['totalsalerad'] = $ap['totalsalerad'] - $ap['extdisc'] / 100 * $ap['totalsalerad'];
							$ap['totalsalerad'] = $ap['totalsalerad'] + $ap['tax'] / 100 * $ap['totalsalerad'];
						}
						
						$lists .= '
							<row id="'.$ap['dsid'].'">
								<cell>'.htmlspecialchars($getdbdetail['saleno']).'</cell>
								<cell>'.htmlspecialchars($ap['stockcode']).'</cell>
								<cell>'.htmlspecialchars($ap['partno']).'</cell>
								<cell>'.htmlspecialchars($ap['stockname']).'</cell>
								<cell>'.htmlspecialchars($ap['brandcode']).'</cell>
								<cell>'.htmlspecialchars($ap['typecode']).'</cell>
								<cell>'.number_format($ap['quantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['unitquantityf']).'</cell>
								<cell>'.number_format($ap['salerprice'],2,",",".").'</cell>
								<cell>'.number_format($ap['disc'],2,",",".").'</cell>
								<cell>'.number_format($ap['extdisc'],2,",",".").'</cell>
								<cell>'.number_format($ap['tax'],2,",",".").'</cell>
								<cell>'.number_format($ap['totalsalerad'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['description']).'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('salerdetaillist');
		}
		else if ($_GET['list'] == 'general'){
			/*$listsaler = $saler->getListSaleR('all');
			$lists = '';
			if (sizeof($listsaler) > 0){
				foreach ($listsaler as $list){
					$customer->setCode($list['customercode']);
					$getcustomername = $customer->getcustomerDetail();
					
					$lists .= '
						<row id="'.$list['salerid'].'">
							<cell>'.htmlspecialchars($list['salerid']).'</cell>
							<cell>'.htmlspecialchars($list['saleno']).'</cell>
							<cell>'.date("d-m-Y",$list['salerdate']).'</cell>
							<cell>'.htmlspecialchars($getcustomername['customername']).'</cell>
							<cell>'.$list['totalsaler'].'</cell>
							<cell>Ubah^saler.php?id='.$list['salerid'].'^_self</cell>
							<cell>Hapus^javascript:deleteitem("saler.php?do=delete&amp;id='.$list['salerid'].'")^_self</cell>
						</row>
					';
				}
			}*/
			/* if (isset($_GET['keyword'])){
				if ($_GET['keyword'] != ''){ */
					$allsaler = $saler->searchSaleR($_GET['keyword'],$_GET['field']);
					$totalrows = sizeof($allsaler);
					$totalpgs = ceil($totalrows / $general['showperpage']);
					$pgs = handlepage($_GET['p'],$totalpgs);
					
					$listsaler = $saler->searchSaleR($_GET['keyword'],$_GET['field'],$pgs);
				/* }
			} */
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listsaler) > 0){
				$ctr = 1;
				foreach ($listsaler as $list){
					$customer->setCode($list['customercode']);
					$getcustomername = $customer->getcustomerDetail();
					
					$saler->setId($list['salerid']);
				
					$getdetailsaler = $saler->getDetailSaleR();
					$splits = sizeof($getdetailsaler);
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0;
						$subtotalfk = 0;
						foreach ($getdetailsaler as $gdb){
							$gdb['typecode'] = wordwrap($gdb['typecode'],15,"<br>",true);
							$gdb['brandcode'] = wordwrap($gdb['brandcode'],10,"<br>",true);
							
							$gdb['typecode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['typecode']));
							$gdb['brandcode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['brandcode']));
							
							if ($statususer == 1){
								$gdb['quantityf'] = floor((100-$discount['extradisc'])/100 * $gdb['quantityf']);
								if ($gdb['quantityf'] < 1){
									$gdb['quantityf'] = 1;
								}
								
								$totalsalerfk = $gdb['quantityf'] * $gdb['salerprice'];
								$totaldiscfk = $gdb['disc'] / 100 * $totalsalerfk;
								$tempstd = $tempstd - $gdb['extdisc'] / 100 * $tempstd;
								$tempstd = $tempstd + $gdb['tax'] / 100 * $tempstd;
								$subtotalfk += $tempstd;
							}
							
							$getdbdetail = $db->fetch_one("SELECT saleno FROM detailsale WHERE dsid='".$gdb['dsid']."'");

							if ($io == 0){
								$listsplit .= '
									<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($getdbdetail['saleno']).'</td>
									<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
									<td width="'.$cwarr[5].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
									<td width="'.$cwarr[6].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
									<td width="'.$cwarr[7].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
									<td width="'.$cwarr[8].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
									<td width="'.$cwarr[9].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
									<td width="'.$cwarr[10].'" class="stufflist" align="left">'.htmlspecialchars($gdb['unitquantityf']).'</td>
									<td width="'.$cwarr[11].'" class="stufflist" id="price_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['salerprice']).'</td>
								';
							}
							else{
								$listsplit2 .= '
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'saler.php?id='.$list['salerid'].'\',\'_self\')">
										<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($getdbdetail['saleno']).'</td>
										<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
										<td width="'.$cwarr[5].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
										<td width="'.$cwarr[6].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
										<td width="'.$cwarr[7].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
										<td width="'.$cwarr[8].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
										<td width="'.$cwarr[9].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
										<td width="'.$cwarr[10].'" class="stufflist" align="left">'.htmlspecialchars($gdb['unitquantityf']).'</td>
										<td width="'.$cwarr[11].'" class="stufflist" id="price_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['salerprice']).'</td>
									</tr>
								';
							}
							$io++;
						}
					}
					else{
						$listsplit .= '
							<td width="'.$cwarr[3].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[4].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[5].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[6].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[7].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[8].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[9].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[10].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[11].'" class="stufflist" align="right"></td>
						';
					}

					$actioneditwidth = floor(51 / 100 * $cwarr[13]);
					$actiondeletewidth = $cwarr[12]-$actioneditwidth-3;
					
					if ($statususer == 1){
						$totalgdiscfk = $list['disc'] / 100 * $subtotalfk;
						$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
						$totalgtaxfk = $list['tax'] / 100 * $totalafgdiscfk;
						$list['totalsaler'] = $totalafgdiscfk + $totalgtaxfk;
					}

					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'saler.php?id='.$list['salerid'].'\',\'_self\')">
								<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.htmlspecialchars($list['salerid']).'</td>
								<td class="stufflist" width="'.$cwarr[1].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['salerdate']).'</td>
								<td class="stufflist" width="'.$cwarr[2].'" align="left"'.$rstext.'>'.htmlspecialchars($getcustomername['customername']).'</td>
								'.$listsplit.'
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[12].'" align="right"'.$rstext.'>'.$codest->convertcodes($list['totalsaler']).'</td>
								<td class="stufflist bgseparator" width="'.$cwarr[13].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
								(($useraccess['edit_saler'])?'<a href="saler.php?id='.$list['salerid'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
								(($useraccess['delete_saler'])?'<a href="javascript:deleteitem(\'saler.php?do=delete&id='.$list['salerid'].'\')">Hapus</a>':'-').'</span></td>
							</tr>'.$listsplit2.'
					';					
					
					$ctr++;
				}
			}
			$ctrgo = $ctr-1;
			/* $pclist = gettemplate('salerlistdetail'); */
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
		else if ($_GET['list'] == 'getcustomerstuff'){
			header("Content-type: text/xml");
			if (!empty($_GET['code'])){
				$limitsale = $nwtm - $salesetting['salereturnlimit'] * 86400;
			
				$liststuff = $db->fetch_all("SELECT ds.*, hs.saleno, hs.disc AS extdisc, hs.tax FROM detailsale ds INNER JOIN headersale hs ON ds.saleno = hs.saleno WHERE hs.customercode='".$_GET['code']."' AND ds.returnsale < ds.quantity AND ds.saledate >= ".$limitsale." ORDER BY ds.saledate");
				$lists = '';
				if (sizeof($liststuff) > 0){
					foreach ($liststuff as $list){
						$conversionvalue = $list['quantity'] / $list['quantityf'];
						if ($statususer == 1){
							$list['quantity'] = floor((100-$discount['extradisc'])/100 * $list['quantity']);
							if ($list['quantity'] < 1){
								$list['quantity'] = 1;
							}
						}
						
						$stuffremaining = $list['quantity'] - $list['returnsale'];
						if ($stuffremaining <= 0){
							continue;
						}
						$listsaleprice = $list['saleprice'] / $conversionvalue;
						$totalsalebdisc = $stuffremaining * $listsaleprice;
						$totalsaleadisc = $totalsalebdisc - ($list['disc'] / 100 * $totalsalebdisc);
						$totalsaleadisc = $totalsaleadisc - ($list['extdisc'] / 100 * $totalsaleadisc);
						$totalsaleadisc = $totalsaleadisc + ($list['tax'] / 100 * $totalsaleadisc);
						$lists .= '
							<row id="'.$list['dsid'].'">
								<cell>'.date("d-m-Y",$list['saledate']).'</cell>
								<cell>'.htmlspecialchars($list['saleno']).'</cell>
								<cell>'.htmlspecialchars($list['stockcode']).'</cell>
								<cell>'.htmlspecialchars($list['partno']).'</cell>
								<cell>'.htmlspecialchars($list['stockname']).'</cell>
								<cell>'.htmlspecialchars($list['brandcode']).'</cell>
								<cell>'.htmlspecialchars($list['typecode']).'</cell>
								<cell>'.number_format($stuffremaining,2,",",".").'</cell>
								<cell>'.htmlspecialchars($list['unitquantity']).'</cell>
								<cell>'.number_format($listsaleprice,2,",",".").'</cell>
								<cell>'.number_format($list['disc'],2,",",".").'</cell>
								<cell>'.number_format($list['extdisc'],2,",",".").'</cell>
								<cell>'.number_format($list['tax'],2,",",".").'</cell>
								<cell>'.number_format($totalsaleadisc,2,",",".").'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('salercustomerstuff');
		}
		eval("\$pclist = \"$pclist\";");
		echo $pclist;
		exit;
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_saler']){
		$db->beginTransaction();
		$salerdate = strtotime($_POST['salerdate']);
		$startyear =  strtotime('01-01-'.date("Y",$salerdate));
		$endyear =  strtotime('31-12-'.date("Y",$salerdate).' 23:59:59');
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
		$lastid = $saler->saveHeaderSaleR($salerdate,$_POST['customercode'],$_POST['customeraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['totalsaler'],$userid);
		
		
		
			
			
		
		//$saler->setSaleNo($_POST['saleno']);
		$saler->setId($lastid);
		
		$arrpostdel = explode(",",$_POST['detailsalerbox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailsalerbox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["detailsalerbox_".$arrpost[$x]."_1"])){
					$checkchars = strpos($_POST["detailsalerbox_".$arrpost[$x]."_1"],"||");
					if ($checkchars !== false){
						$_POST["detailsalerbox_".$arrpost[$x]."_1"] = substr($_POST["detailsalerbox_".$arrpost[$x]."_1"],0,$checkchars);
					}
					
					$stock->setId("");
					$stock->setCode($_POST["detailsalerbox_".$arrpost[$x]."_1"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["detailsalerbox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_6"],'calculate');
					$_POST["detailsalerbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_8"],'calculate');
					$_POST["detailsalerbox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_9"],'calculate');
					$_POST["detailsalerbox_".$arrpost[$x]."_10"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_10"],'calculate');
					$_POST["detailsalerbox_".$arrpost[$x]."_11"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_11"],'calculate');
					
					$totals = $_POST["detailsalerbox_".$arrpost[$x]."_6"] * $_POST["detailsalerbox_".$arrpost[$x]."_8"];
					$salerpricead = $_POST["detailsalerbox_".$arrpost[$x]."_8"] - ($_POST["detailsalerbox_".$arrpost[$x]."_9"] / 100 * $_POST["detailsalerbox_".$arrpost[$x]."_8"]);
					$salerpricead = $salerpricead - ($_POST["detailsalerbox_".$arrpost[$x]."_10"] / 100 * $salerpricead);
					$salerpricead = $salerpricead + ($_POST["detailsalerbox_".$arrpost[$x]."_11"] / 100 * $salerpricead);
					$realsalerprice = $salerpricead - ($_POST['disc'] / 100 * $salerpricead);
					$realsalerprice = $realsalerprice + ($_POST['tax'] / 100 * $realsalerprice);

					if ($_POST["detailsalerbox_".$arrpost[$x]."_7"] == $getunit['funit']){
						$quantity = $_POST["detailsalerbox_".$arrpost[$x]."_6"] * $getunit['cvalue'];
						$realsalerprice = $realsalerprice / $getunit['cvalue'];
					}
					else{
						$quantity = $_POST["detailsalerbox_".$arrpost[$x]."_6"];
						$_POST["detailsalerbox_".$arrpost[$x]."_7"] = $getunit['lunit'];
					}
					
					//get detail sale
					//$sale->setDetailId($arrpost[$x]);
					//$detailsale = $sale->getDetailsaleIndv();
					
					$lastdetid = $saler->saveDetailSaleR($_POST["detailsalerbox_".$arrpost[$x]."_1"],$_POST["detailsalerbox_".$arrpost[$x]."_2"],$_POST["detailsalerbox_".$arrpost[$x]."_3"],$_POST["detailsalerbox_".$arrpost[$x]."_4"],$_POST["detailsalerbox_".$arrpost[$x]."_5"],$_POST["detailsalerbox_".$arrpost[$x]."_6"],$_POST["detailsalerbox_".$arrpost[$x]."_7"],$_POST["detailsalerbox_".$arrpost[$x]."_8"],$_POST["detailsalerbox_".$arrpost[$x]."_9"],$_POST["detailsalerbox_".$arrpost[$x]."_10"],$_POST["detailsalerbox_".$arrpost[$x]."_11"],togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_12"],'calculate'),$_POST["detailsalerbox_".$arrpost[$x]."_13"],$salerdate,$totals,$salerpricead,$realsalerprice,$quantity,$unitquantity,$getstock['unitcode'],$arrpost[$x]);
					
					
					///hutang/piutang
		

					$_POST['id'] = $lastdetid;
					$_POST["detailsalerbox_".$arrpost[$x]."_12"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_12"],'calculate');
					
					$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");
					$checkdebt = $payment->getHeaderPaymentByMonth($salerdate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
					
					//jika blm ada data hutang di periode bln ini
					if (empty($checkdebt['hpid'])){
					
					
					$lastmonth = $payment->getDetailLastPaymentByMonth($salerdate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
					
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
					
					
					
					if (!empty($checknulfremainingprevious[0])){
					
					$grandtotals = $_POST["detailsalerbox_".$arrpost[$x]."_12"] + $remainingprevious;
					}
					else if (!empty($checknulfremainingprevioush[0])){
					
					$grandtotals = $_POST["detailsalerbox_".$arrpost[$x]."_12"] - $remainingprevioush;
					
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
					
					$grandtotals = $_POST["detailsalerbox_".$arrpost[$x]."_12"];
					}
					
					if ($grandtotals <=0 ){
					$complete = 1;
					$completedate = $salerdate;
					}
					else{
					$complete = 0;
					$completedate = 0;
					}
					
					$invstartdate = strtotime('01-'.date("m-Y",$salerdate));
					$invenddate = strtotime(date('t-m-Y',$salerdate));
					
					
					
					$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$salerdate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST["detailsalerbox_".$arrpost[$x]."_12"],$grandtotals,$userid,1,$remainingprevioush,$remainingnowh);
					
					$payment->setId($lastidpaym);
					$payment->saveDetailPayment($_POST['id'],$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
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
						$ttlfolpaym = ($totalpaysale['totalpay'] - ($totalpaysaler['totalpay']+$_POST["detailsalerbox_".$arrpost[$x]."_12"])) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);

						if ($ttlfolpaym > 0){
						
						$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
						$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
						
						
						$totalpayment = $ttlfolpaym;
						$grandtotals = $oldheader['grandtotals']+ $_POST["detailsalerbox_".$arrpost[$x]."_12"];
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
						
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);
						
						$payment->saveDetailPayment($_POST['id'],$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
						}
						
						else{
						
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						
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
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
						
						$payment->saveDetailPayment($_POST['id'],$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
						
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);

						
						}
						
					
					}
					
					/* echo "Sub Total (Rp.) : ".abs($_POST["detailsalerbox_".$arrpost[$x]."_12"]);
					echo "<br>Grand Total (Rp.) :".abs($grandtotals);
					echo "<br>Rprev h : ".abs($remainingprevioush);
					echo "<br>Rprev   : ".abs($remainingprevious);
					echo "<br>Rnow h : ".abs($remainingnowh);
					echo "<br>now   : ".abs($remainingnow); */
					
				}
			}
		}
		$db->endTransaction();
		redirecting("saler.php?id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_saler']){
		//print_r($_POST);
		if (!empty($_POST['id'])){
			$saler->setId($_POST['id']);
			$getoldheadersaler = $saler->getHeaderSaleR();
			$db->beginTransaction();
			$salerdate = strtotime($_POST['salerdate']);
			$startyear =  strtotime('01-01-'.date("Y",$salerdate));
			$endyear =  strtotime('31-12-'.date("Y",$salerdate).' 23:59:59');
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
			
			
			
			
			
			if ( $getoldheadersaler['customercode'] == $_POST['customercode'] )
			{
			
			if ($salerdate == $getoldheadersaler['salerdate']){
			$statusedit = 0;
			}
			else{
			$statusedit = 1;
			}
			
			}
			else{
			$statusedit = 1;
			}
			
			
			$saler->updateHeaderSaleR($salerdate,$_POST['customercode'],$_POST['customeraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['totalsaler'],$userid);
				
			$arrpostt = explode(",",$_POST['detailid']);

			//deleted rows
			$arrpostdel = explode(",",$_POST['detailsalerbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$getdsrid = $saler->getDetailIdFromItem($arrpostdel[$x]);
						
						$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$getoldheadersaler['customercode']."' ");

						//hutang
						$checkdebt = $payment->getallHeaderPaymentByMonth($getoldheadersaler['salerdate'],2,$dbcustsup['supplierid'],$headersaler['supplieraddrid'],$dbcustsup['customerid'],$getoldheadersaler['customeraddrid']);
						
						$payment->setId($checkdebt['hpid']);
						
						$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$getdsrid."' AND types = 'return' ");
				
		
						if (!empty($getdetail['dpid'])){
						
						$payment->setDetailId($getdetail['dpid']);
						$payment->deleteDetailPayment();
						$payment->setId($checkdebt['hpid']);

						$oldestheader = $payment->getHeaderPayment();
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$oldestheader['complete'],$oldestheader);
						$payment->setId($checkdebt['hpid']);

						if ($oldheader['status'] == 1){

						$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
						$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);

						$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
						$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
						//$ttlfolpaym = $totalpaysale['totalpay'] - $oldheader['totalforbuy'];
						$grandtotals = 0;
						$totalpayment = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
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
						$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
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
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);



						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$oldestheader['complete'],$oldheader);
						}
						}
						
						$saler->setDetailId($getdsrid);
						$olddetail = $saler->getDetailSaleRIndv();
						$stock->setId("");
						$stock->setCode($olddetail['stockcode']);
						$db->query("UPDATE detailsale SET returnsale=returnsale-".$olddetail['quantity']." WHERE dsid='".$arrpostdel[$x]."'");				
						$getfs = $stock->getFirstStock();
						if ($getfs['assembly'] == 1){
							$assembly->setCode($olddetail['stockcode']);
							$getac = $assembly->getAssemblyComponent();
							if (sizeof($getac) > 0){
								foreach ($getac as $gac){
									$stock->setCode($gac['stockcodecomponent']);
									$this->deleteDetailRItem($gac['stockcodecomponent'],$arrpostdel[$x]);
									$stock->minStock($olddetail['quantity']*$gac['sccquantity']);
								}
							}
						}
						else{							
							$saler->deleteDetailRItem($olddetail['stockcode'],$arrpostdel[$x]);
							$db->query("DELETE FROM detailsaler WHERE dsrid='".$getdsrid."'");
							$stock->minStock($olddetail['quantity']);
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
					if (!empty($arrpost[$x]) && !empty($_POST["detailsalerbox_".$arrpost[$x]."_1"])){
						$checkchars = strpos($_POST["detailsalerbox_".$arrpost[$x]."_1"],"||");
						if ($checkchars !== false){
							$_POST["detailsalerbox_".$arrpost[$x]."_1"] = substr($_POST["detailsalerbox_".$arrpost[$x]."_1"],0,$checkchars);
						}
						
						$saler->setDetailId($saler->getDetailIdFromItem($arrpost[$x]));
						$olddetail = $saler->getDetailSaleRIndv();
						
						$_POST["detailsalerbox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_6"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_8"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_9"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_10"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_10"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_11"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_11"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_12"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_12"],'calculate');
						
						$stock->setId("");
						$stock->setCode($_POST["detailsalerbox_".$arrpost[$x]."_1"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];

						$_POST["detailsalerbox_".$arrpost[$x]."_4"] = $getstock['brandcode'];
						$_POST["detailsalerbox_".$arrpost[$x]."_5"] = $getstock['typecode'];
						
						$totals = $_POST["detailsalerbox_".$arrpost[$x]."_6"] * $_POST["detailsalerbox_".$arrpost[$x]."_8"];
						$salerpricead = $_POST["detailsalerbox_".$arrpost[$x]."_8"] - ($_POST["detailsalerbox_".$arrpost[$x]."_9"] / 100 * $_POST["detailsalerbox_".$arrpost[$x]."_8"]);
						$salerpricead = $salerpricead - ($_POST["detailsalerbox_".$arrpost[$x]."_10"] / 100 * $salerpricead);
						$salerpricead = $salerpricead + ($_POST["detailsalerbox_".$arrpost[$x]."_11"] / 100 * $salerpricead);
						$realsalerprice = $salerpricead - ($_POST['disc'] / 100 * $salerpricead);
						$realsalerprice = $realsalerprice + ($_POST['tax'] / 100 * $realsalerprice);

						if ($_POST["detailsalerbox_".$arrpost[$x]."_7"] == $getunit['funit']){
							$quantity = $_POST["detailsalerbox_".$arrpost[$x]."_6"] * $getunit['cvalue'];
							$realsalerprice = $realsalerprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailsalerbox_".$arrpost[$x]."_6"];
							$_POST["detailsalerbox_".$arrpost[$x]."_7"] = $getunit['lunit'];
						}
						
						$saler->updateDetailSaleR($_POST["detailsalerbox_".$arrpost[$x]."_1"],$_POST["detailsalerbox_".$arrpost[$x]."_2"],$_POST["detailsalerbox_".$arrpost[$x]."_3"],$_POST["detailsalerbox_".$arrpost[$x]."_4"],$_POST["detailsalerbox_".$arrpost[$x]."_5"],$_POST["detailsalerbox_".$arrpost[$x]."_6"],$_POST["detailsalerbox_".$arrpost[$x]."_7"],$_POST["detailsalerbox_".$arrpost[$x]."_8"],$_POST["detailsalerbox_".$arrpost[$x]."_9"],$_POST["detailsalerbox_".$arrpost[$x]."_10"],$_POST["detailsalerbox_".$arrpost[$x]."_11"],$_POST["detailsalerbox_".$arrpost[$x]."_12"],$_POST["detailsalerbox_".$arrpost[$x]."_13"],$salerdate,$totals,$salerpricead,$realsalerprice,$quantity,$unitquantity,$getstock['unitcode'],$arrpost[$x],$olddetail);
						
						
						//jika sama header dulu & sekarang
						if ( $statusedit == 0 ) {
						//hutang
						$lastdsidthis = $saler->getDetailIdFromItem($arrpost[$x]);
						
						$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");
				
						$checkdebt = $payment->getallHeaderPaymentByMonth($salerdate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
						
						$payment->setId($checkdebt['hpid']);
						$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$lastdsidthis."' AND types = 'return' ");
						$payment->setDetailId($getdetail['dpid']);
						
						$olddetails = $payment->getDetailPaymentFromSale($lastdsidthis,"return");
						$payment->updateDetailPayment($lastdsidthis,$_POST["detailsalerbox_".$arrpost[$x]."_12"],0,"","return","bb",0,$olddetails );
						$oldestheader = $payment->getHeaderPayment();
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldestheader);
						$payment->setId($checkdebt['hpid']);
						
						$oldheader = $payment->getHeaderPayment();
						
						if ($oldheader['status'] == 1){
						
						$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
						$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
						
						$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
						$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
						//$ttlfolpaym = $totalpaysale['totalpay'] - $oldheader['totalforbuy'];
						$grandtotals = 0;
						$totalpayment = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
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
						$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
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
						
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);
						
						
						
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
						}
						
						}//jika sama header dulu & sekarang
						
						
						//jika tidak sama header dulu & sekarang
						else{
						$lastdsidthis = $saler->getDetailIdFromItem($arrpost[$x]);
						
						$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$getoldheadersaler['customercode']."' ");
						
						//hapus data yang lama
						$checkdebt = $payment->getallHeaderPaymentByMonth($getoldheadersaler['salerdate'],2,$dbcustsup['supplierid'],$headersaler['supplieraddrid'],$dbcustsup['customerid'],$getoldheadersaler['customeraddrid']);
						
						$payment->setId($checkdebt['hpid']);
						
						$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$lastdsidthis."' AND types = 'return' ");
				
		
						if (!empty($getdetail['dpid'])){
						
						$payment->setDetailId($getdetail['dpid']);
						$payment->deleteDetailPayment();
						$payment->setId($checkdebt['hpid']);

						$oldestheader = $payment->getHeaderPayment();
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$oldestheader['complete'],$oldestheader);
						$payment->setId($checkdebt['hpid']);
						
						$oldheader = $payment->getHeaderPayment();
						if ($oldheader['status'] == 1){

						$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
						$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);

						$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
						$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
						//$ttlfolpaym = $totalpaysale['totalpay'] - $oldheader['totalforbuy'];
						$grandtotals = 0;
						$totalpayment = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
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
						$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);
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
						//echo $remainingnowh;
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);



						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$oldestheader['complete'],$oldheader);
						}
						}
						//hapus data lama
						
						//tambah data baru
						$dbcustsup = $db->fetch_one("SELECT * FROM customer c LEFT JOIN supplier s ON c.customercode = s.suppliercode WHERE customercode = '".$_POST['customercode']."' ");
			
						$checkdebt = $payment->getallHeaderPaymentByMonth($salerdate,1,$dbcustsup['supplierid'],0,$dbcustsup['customerid'],$_POST['customeraddrid']);
						

					
					//jika blm ada data hutang di periode bln ini
						if (empty($checkdebt['hpid'])){
						
						
						$lastmonth = $payment->getDetailLastPaymentByMonth($salerdate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
						
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
						
						
						
						if (!empty($checknulfremainingprevious[0])){
						
						$grandtotals = $_POST["detailsalerbox_".$arrpost[$x]."_12"] + $remainingprevious;
						}
						else if (!empty($checknulfremainingprevioush[0])){
						
						$grandtotals = $_POST["detailsalerbox_".$arrpost[$x]."_12"] - $remainingprevioush;
						
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
						
						$grandtotals = $_POST["detailsalerbox_".$arrpost[$x]."_12"];
						}
						
						if ($grandtotals <=0 ){
						$complete = 1;
						$completedate = $salerdate;
						}
						else{
						$complete = 0;
						$completedate = 0;
						}
						
						$invstartdate = strtotime('01-'.date("m-Y",$salerdate));
						$invenddate = strtotime(date('t-m-Y',$salerdate));
						
						
						
						$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$salerdate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST["detailsalerbox_".$arrpost[$x]."_12"],$grandtotals,$userid,1,$remainingprevioush,$remainingnowh);
						
						$payment->setId($lastidpaym);
						$payment->saveDetailPayment($lastdsidthis,$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
						$payment->updateDebtCreditnotlive($_POST['customercode'],0);
						
						
						}
						
					//jika ada data hutang di periode bln ini
					else{
					
						
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						
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
						
						
						
						
						$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
						$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
						$ttlfolpaym = ($totalpaysale['totalpay'] - ($totalpaysaler['totalpay']+$_POST["detailsalerbox_".$arrpost[$x]."_12"])) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);

						if ($ttlfolpaym > 0){
						
						$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
						$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
						
						
						$totalpayment = $ttlfolpaym;
						$grandtotals = $oldheader['grandtotals']+ $_POST["detailsalerbox_".$arrpost[$x]."_12"];
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
						
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);
						
						$payment->saveDetailPayment($lastdsidthis,$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
						}
						
						else{
						
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						
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
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
						
						$payment->saveDetailPayment($lastdsidthis,$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
						
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);

						
						}
						
					
					}
					
						
						
						
						}
						
						
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailsalerbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailsalerbox_".$arrpost[$x]."_1"])){
						$checkchars = strpos($_POST["detailsalerbox_".$arrpost[$x]."_1"],"||");
						if ($checkchars !== false){
							$_POST["detailsalerbox_".$arrpost[$x]."_1"] = substr($_POST["detailsalerbox_".$arrpost[$x]."_1"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["detailsalerbox_".$arrpost[$x]."_1"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailsalerbox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_6"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_8"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_9"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_10"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_10"],'calculate');
						$_POST["detailsalerbox_".$arrpost[$x]."_11"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_11"],'calculate');
						
						$totals = $_POST["detailsalerbox_".$arrpost[$x]."_6"] * $_POST["detailsalerbox_".$arrpost[$x]."_8"];
						$salerpricead = $_POST["detailsalerbox_".$arrpost[$x]."_8"] - ($_POST["detailsalerbox_".$arrpost[$x]."_9"] / 100 * $_POST["detailsalerbox_".$arrpost[$x]."_8"]);
						$salerpricead = $salerpricead - ($_POST["detailsalerbox_".$arrpost[$x]."_10"] / 100 * $salerpricead);
						$salerpricead = $salerpricead + ($_POST["detailsalerbox_".$arrpost[$x]."_11"] / 100 * $salerpricead);
						$realsalerprice = $salerpricead - ($_POST['disc'] / 100 * $salerpricead);
						$realsalerprice = $realsalerprice + ($_POST['tax'] / 100 * $realsalerprice);

						if ($_POST["detailsalerbox_".$arrpost[$x]."_7"] == $getunit['funit']){
							$quantity = $_POST["detailsalerbox_".$arrpost[$x]."_6"] * $getunit['cvalue'];
							$realsalerprice = $realsalerprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailsalerbox_".$arrpost[$x]."_6"];
							$_POST["detailsalerbox_".$arrpost[$x]."_7"] = $getunit['lunit'];
						}
						
						$lastdtid = $saler->saveDetailSaleR($_POST["detailsalerbox_".$arrpost[$x]."_1"],$_POST["detailsalerbox_".$arrpost[$x]."_2"],$_POST["detailsalerbox_".$arrpost[$x]."_3"],$_POST["detailsalerbox_".$arrpost[$x]."_4"],$_POST["detailsalerbox_".$arrpost[$x]."_5"],$_POST["detailsalerbox_".$arrpost[$x]."_6"],$_POST["detailsalerbox_".$arrpost[$x]."_7"],$_POST["detailsalerbox_".$arrpost[$x]."_8"],$_POST["detailsalerbox_".$arrpost[$x]."_9"],$_POST["detailsalerbox_".$arrpost[$x]."_10"],$_POST["detailsalerbox_".$arrpost[$x]."_11"],togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_12"],'calculate'),$_POST["detailsalerbox_".$arrpost[$x]."_13"],$salerdate,$totals,$salerpricead,$realsalerprice,$quantity,$unitquantity,$getstock['unitcode'],$arrpost[$x]);
						
						
						//hutang 
						$_POST["detailsalerbox_".$arrpost[$x]."_12"] = togglenumber($_POST["detailsalerbox_".$arrpost[$x]."_12"],'calculate');
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();

						$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
						$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
						$ttlfolpaym = ($totalpaysale['totalpay'] - ($totalpaysaler['totalpay']+$_POST["detailsalerbox_".$arrpost[$x]."_12"])) - ($totalpaybuy['totalpay'] - $totalpaybuyr['totalpay']);

						if ($ttlfolpaym > 0){
						
						$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
						$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
						
						
						$totalpayment = $ttlfolpaym;
						$grandtotals = $oldheader['grandtotals']+ $_POST["detailsalerbox_".$arrpost[$x]."_12"];
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
						
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);
						
						$payment->saveDetailPayment($lastdtid,$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);
						}
						
						else{
						
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						
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
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
						
						$payment->saveDetailPayment($lastdtid,$_POST["detailsalerbox_".$arrpost[$x]."_12"],$salerdate,"","return",0,0);
						
						$payment->updateDebtCreditEdit4($dbcustsup['customerid'],$dbcustsup['customercode'],$_POST['complete'],$oldheader);

						
						}
						
						
						
						
						
					}
				}
			}
			$db->endTransaction();
		}
		redirecting("saler.php?id=".$_POST['id']);
	}

	$salerid = $_GET['id'];
	if ($_GET['do'] == 'delete' && !empty($salerid) && $useraccess['delete_saler']){
		$db->beginTransaction();
		
		$saler->setId($salerid);
		$headersaler = $saler->getHeaderSaleR();

		
		$customer->setCode($headersaler['customercode']);
		$customer->addCredit($headersaler['totalsaler']);
		
		$saler->deleteSaleR();
		
		$db->endTransaction();
		redirecting("saler.php?screen=list");		
	}

	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_saler'])){
			redirecting('index.php');
		}
		
		$printtemplate = 'salerlist';
	}
	else{
		
		if (empty($useraccess['add_saler']) && empty($useraccess['edit_saler'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'saler';
		if (!empty($salerid)){
			$saler->setId($salerid);
			$headersaler = $saler->getHeadersaleR();
			
			if (empty($headersaler['salerid'])){
				redirecting('saler.php?screen=list');
			}
			
			$saledate = date("d-m-Y",$headersaler['saledate']);
			$invoicedate = date("d-m-Y",$headersaler['salerdate']);
			$selsaleno = $headersaler['saleno'];
			
			$customer->setCode($headersaler['customercode']);
			$customer->setDetailId($headersaler['customeraddrid']);
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
			
			$alldtl = $saler->getDetailSaleR();
			$alldetailid = '';
			$arrmaxqty = '';
			$subtotalfk = 0;
			if (sizeof($alldtl) > 0){
				foreach ($alldtl as $aad){
					$stock->setId("");
					$stock->setCode($aad['stockcode']);
					$getds = $stock->getFirstStock();
					
					$alldetailid .= ','.$aad['dsid'];
					$alldetailidjs .= ',\''.$aad['dsid'].'\'';
					
					$getdsid = $db->fetch_one("SELECT * FROM detailsale WHERE dsid='".$aad['dsid']."'");
					
					$units->setCode($getds['unitcode']);
					$getunit = $units->getunitDetail();
					$arrmaxqty .= 'arrmaxr["'.$aad['dsid'].'"] = '.($getdsid['quantity']-$getdsid['returnsale']+$aad['quantity']);
					$arrunits .= '
						arrunits["'.$aad['dsid'].'"] = new Array();
						arrunits["'.$aad['dsid'].'"][0] = "'.$getunit['funit'].'";
						arrunits["'.$aad['dsid'].'"][1] = "'.$getunit['lunit'].'";';
					$arrconversion .= '
						arrconversion["'.$aad['dsid'].'"] = new Array();
						arrconversion["'.$aad['dsid'].'"][0] = '.$getunit['cvalue'].';
						arrconversion["'.$aad['dsid'].'"][1] = 1;';
				
					$aad['quantityf'] = floor((100-$discount['extradisc'])/100 * $aad['quantityf']);
					if ($aad['quantityf'] < 1){
						$aad['quantityf'] = 1;
					}
						
					$totalsalefk = $aad['quantityf'] * $aad['salerprice'];
					$totaldiscfk = $aad['disc'] / 100 * $totalsalefk;
					$subtotalfk += ($totalsalefk - $totaldiscfk);
				}
				$alldetailid = substr($alldetailid,1);
				$alldetailidjs = substr($alldetailidjs,1);
			}
			
			if ($statususer == 1){
				$headersaler['totals'] = $subtotalfk;
				$totalgdiscfk = $headersaler['disc'] / 100 * $subtotalfk;
				$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
				$totalgtaxfk = $headersaler['tax'] / 100 * $totalafgdiscfk;
				$headersaler['totalsaler'] = $totalafgdiscfk + $totalgtaxfk;
			}
			
			$ftotals = number_format($headersaler['totals'],2,",",".");
			$fdisc = number_format($headersaler['disc'],2,",",".");
			$ftax = number_format($headersaler['tax'],2,",",".");
			$ftotal = number_format($headersaler['totalsaler'],2,",",".");
			
			$headersaler = array_map("htmlspecialchars",$headersaler);
		}
	}

	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>