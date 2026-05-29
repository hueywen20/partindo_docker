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
	require_once "class/PurchaseR.php";
	require_once "class/Payment.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$customer = new customer();
	$units = new unit();
	$supplier = new supplier();
	$area = new area();
	$purchase = new purchase();
	$purchaser = new PurchaseR();
	$payment = new Payment();
	
	$headerbuyr['totals'] = 0;
	$headerbuyr['disc'] = 0;
	$headerbuyr['tax'] = 0;
	$headerbuyr['totalbuyr'] = 0;
	$invoicedate = date("d-m-Y");
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['id'])){
				$purchaser->setId($_GET['id']);
				$allpurchaser = $purchaser->getDetailBuyR();
				if (sizeof($allpurchaser) > 0){
					foreach ($allpurchaser as $ap){
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
						
						$purchaser->setDetailId($ap['dbrid']);
						$getdbid = $purchaser->getDetailBuyRItem();
						$idrows = '';
						if (sizeof($getdbid) > 0){
							foreach ($getdbid as $gdbid){
								$idrows = $gdbid['dbid'];
								$getdbdetail = $db->fetch_one("SELECT hb.orderno FROM headerbuy hb INNER JOIN detailbuy dby ON dby.buyno = hb.buyno WHERE dby.dbid='".$idrows."'");
								break;
							}
						}
						
						if ($statususer == 1){
							$ap['quantityf'] = floor((100-$discount['extradisc'])/100 * $ap['quantityf']);
							if ($ap['quantityf'] < 1){
								$ap['quantityf'] = 1;
							}
							$totalbuyrfk = $ap['quantityf'] * $ap['buyrprice'];
							$totaldiscfk = $ap['disc'] / 100 * $totalbuyrfk;
							$ap['totalbuyrad'] = $totalbuyrfk - $totaldiscfk;
							$ap['totalbuyrad'] = $ap['totalbuyrad'] - $ap['extdisc'] / 100 * $ap['totalbuyrad'];
							$ap['totalbuyrad'] = $ap['totalbuyrad'] + $ap['tax'] / 100 * $ap['totalbuyrad'];
						}
						
						$lists .= '
							<row id="'.$idrows.'">
								<cell>'.htmlspecialchars($getdbdetail['orderno']).'</cell>
								<cell>'.htmlspecialchars($ap['stockcode']).'</cell>
								<cell>'.htmlspecialchars($ap['partno']).'</cell>
								<cell>'.htmlspecialchars($ap['stockname']).'</cell>
								<cell>'.htmlspecialchars($ap['brandcode']).'</cell>
								<cell>'.htmlspecialchars($ap['typecode']).'</cell>
								<cell>'.number_format($ap['quantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['unitquantityf']).'</cell>
								<cell>'.number_format($ap['buyrprice'],2,",",".").'</cell>
								<cell>'.number_format($ap['disc'],2,",",".").'</cell>
								<cell>'.number_format($ap['extdisc'],2,",",".").'</cell>
								<cell>'.number_format($ap['tax'],2,",",".").'</cell>
								<cell>'.number_format($ap['otherpays'],2,",",".").'</cell>
								<cell>'.number_format($ap['totalbuyrad'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['description']).'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('purchaserdetaillist');
		}
		else if ($_GET['list'] == 'general'){
			/*$listpurchaser = $purchaser->getListPurchaseR('all');
			$lists = '';
			if (sizeof($listpurchaser) > 0){
				foreach ($listpurchaser as $list){
					$supplier->setCode($list['suppliercode']);
					$getsuppliername = $supplier->getsupplierDetail();
					
					$lists .= '
						<row id="'.$list['buyrid'].'">
							<cell>'.htmlspecialchars($list['buyrid']).'</cell>
							<cell>'.htmlspecialchars($list['buyno']).'</cell>
							<cell>'.date("d-m-Y",$list['buyrdate']).'</cell>
							<cell>'.htmlspecialchars($getsuppliername['suppliername']).'</cell>
							<cell>'.$list['totalbuyr'].'</cell>
							<cell>Ubah^purchaser.php?id='.$list['buyrid'].'^_self</cell>
							<cell>Hapus^javascript:deleteitem("purchaser.php?do=delete&amp;id='.$list['buyrid'].'")^_self</cell>
						</row>
					';
				}
			}*/
			/* if (isset($_GET['keyword'])){
				if ($_GET['keyword'] != ''){ */
					$allbuyr = $purchaser->searchBuyR($_GET['keyword'],$_GET['field']);
					$totalrows = sizeof($allbuyr);
					$totalpgs = ceil($totalrows / $general['showperpage']);
					$pgs = handlepage($_GET['p'],$totalpgs);
					
					$listbuyr = $purchaser->searchBuyR($_GET['keyword'],$_GET['field'],$pgs);
				/* }
			} */
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listbuyr) > 0){
				$ctr = 1;
				foreach ($listbuyr as $list){
					$supplier->setCode($list['suppliercode']);
					$getsuppliername = $supplier->getsupplierDetail();
					
					$purchaser->setId($list['buyrid']);
				
					$getdetailbuyr = $purchaser->getDetailBuyR();
					$splits = sizeof($getdetailbuyr);
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0;
						$subtotalfk = 0;
						foreach ($getdetailbuyr as $gdb){
							$gdb['typecode'] = wordwrap($gdb['typecode'],15,"<br>",true);
							$gdb['brandcode'] = wordwrap($gdb['brandcode'],10,"<br>",true);
							
							$gdb['typecode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['typecode']));
							$gdb['brandcode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['brandcode']));
							
							if ($statususer == 1){
								$gdb['quantityf'] = floor((100-$discount['extradisc'])/100 * $gdb['quantityf']);
								if ($gdb['quantityf'] < 1){
									$gdb['quantityf'] = 1;
								}
								
								$totalbuyrfk = $gdb['quantityf'] * $gdb['buyrprice'];
								$totaldiscfk = $gdb['disc'] / 100 * $totalbuyrfk;
								$tempstd = $totalbuyrfk - $totaldiscfk;
								$tempstd = $tempstd - $gdb['extdisc'] / 100 * $tempstd;
								$tempstd = $tempstd + $gdb['tax'] / 100 * $tempstd;
								$subtotalfk += $tempstd;
							}
							
							$purchaser->setDetailId($gdb['dbrid']);
							$getdbid = $purchaser->getDetailBuyRItem();
							$idrows = '';
							if (sizeof($getdbid) > 0){
								foreach ($getdbid as $gdbid){
									$idrows = $gdbid['dbid'];
									$getdbdetail = $db->fetch_one("SELECT hb.orderno FROM headerbuy hb INNER JOIN detailbuy dby ON dby.buyno = hb.buyno WHERE dby.dbid='".$idrows."'");
									break;
								}
							}
							
							if ($io == 0){
								$listsplit .= '
									<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($getdbdetail['orderno']).'</td>
									<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
									<td width="'.$cwarr[5].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
									<td width="'.$cwarr[6].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
									<td width="'.$cwarr[7].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
									<td width="'.$cwarr[8].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
									<td width="'.$cwarr[9].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
									<td width="'.$cwarr[10].'" class="stufflist" align="left">'.htmlspecialchars($gdb['unitquantityf']).'</td>
									<td width="'.$cwarr[11].'" class="stufflist" id="price_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['buyrprice']).'</td>
								';
							}
							else{
								$listsplit2 .= '
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'purchaser.php?id='.$list['buyrid'].'\',\'_self\')">
										<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($getdbdetail['orderno']).'</td>
										<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
										<td width="'.$cwarr[5].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
										<td width="'.$cwarr[6].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
										<td width="'.$cwarr[7].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
										<td width="'.$cwarr[8].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
										<td width="'.$cwarr[9].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
										<td width="'.$cwarr[10].'" class="stufflist" align="left">'.htmlspecialchars($gdb['unitquantityf']).'</td>
										<td width="'.$cwarr[11].'" class="stufflist" id="price_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['buyrprice']).'</td>
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
					$actiondeletewidth = $cwarr[13]-$actioneditwidth-3;
					
					if ($statususer == 1){
						$totalgdiscfk = $list['disc'] / 100 * $subtotalfk;
						$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
						$totalgtaxfk = $list['tax'] / 100 * $totalafgdiscfk;
						$list['totalbuyr'] = $totalafgdiscfk + $totalgtaxfk;
					}

					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'purchaser.php?id='.$list['buyrid'].'\',\'_self\')">
								<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.htmlspecialchars($list['buyrid']).'</td>
								<td class="stufflist" width="'.$cwarr[1].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['buyrdate']).'</td>
								<td class="stufflist" width="'.$cwarr[2].'" align="left"'.$rstext.'>'.htmlspecialchars($getsuppliername['suppliername']).'</td>
								'.$listsplit.'
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[12].'" align="right"'.$rstext.'>'.$codest->convertcodes($list['totalbuyr']).'</td>
								<td class="stufflist bgseparator" width="'.$cwarr[13].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
								(($useraccess['edit_purchaser'])?'<a href="purchaser.php?id='.$list['buyrid'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
								(($useraccess['delete_purchaser'])?'<a href="javascript:deleteitem(\'purchaser.php?do=delete&id='.$list['buyrid'].'\')">Hapus</a>':'-').'</span></td>
							</tr>'.$listsplit2.'
					';					
					
					$ctr++;
				}
			}
			$ctrgo = $ctr-1;
			/* $pclist = gettemplate('purchaserlistdetail'); */
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
		else if ($_GET['list'] == 'getsupplierstuff'){
			header("Content-type: text/xml");
			if (!empty($_GET['code'])){
				$liststuff = $db->fetch_all("SELECT db.*, hb.orderno, hb.disc AS extradisc, hb.tax FROM detailbuy db INNER JOIN headerbuy hb ON db.buyno = hb.buyno WHERE hb.suppliercode='".$_GET['code']."' AND db.usedqty < db.quantity ORDER BY db.buydate");
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
						
						$stuffremaining = $list['quantity'] - $list['usedqty'];
						if ($stuffremaining <= 0){
							continue;
						}
						$listbuyprice = $list['buyprice'] / $conversionvalue;
						$totalbuybdisc = $stuffremaining * $listbuyprice;
						$totalbuyadisc = $totalbuybdisc - ($list['disc'] / 100 * $totalbuybdisc);
						$totalbuyadisc = $totalbuyadisc - ($list['extradisc'] / 100 * $totalbuyadisc);
						$totalbuyadisc = $totalbuyadisc + ($list['tax'] / 100 * $totalbuyadisc);
						
						$totalrealbuy = $stuffremaining * $list['realbuyprice'];
						$otherpays = ($totalrealbuy - $totalbuyadisc) / $stuffremaining;
						if (empty($list['expdate'])){
							$mexp = 0;
						}
						else{
							$mexp = date("d-m-Y",$list['expdate']);
						}
						$lists .= '
							<row id="'.$list['dbid'].'">
								<cell>'.date("d-m-Y",$list['buydate']).'</cell>
								<cell>'.htmlspecialchars($list['orderno']).'</cell>
								<cell>'.htmlspecialchars($list['stockcode']).'</cell>
								<cell>'.htmlspecialchars($list['partno']).'</cell>
								<cell>'.htmlspecialchars($list['stockname']).'</cell>
								<cell>'.htmlspecialchars($list['brandcode']).'</cell>
								<cell>'.htmlspecialchars($list['typecode']).'</cell>
								<cell>'.number_format($stuffremaining,2,",",".").'</cell>
								<cell>'.htmlspecialchars($list['unitquantity']).'</cell>
								<cell>'.number_format($listbuyprice,2,",",".").'</cell>
								<cell>'.number_format($list['disc'],2,",",".").'</cell>
								<cell>'.number_format($list['extradisc'],2,",",".").'</cell>
								<cell>'.number_format($list['tax'],2,",",".").'</cell>
								<cell>'.number_format($otherpays,2,",",".").'</cell>
								<cell>'.number_format($totalrealbuy,2,",",".").'</cell>
								<cell>'.$mexp.'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('purchasersupplierstuff');
		}
		eval("\$pclist = \"$pclist\";");
		echo $pclist;
		exit;
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_purchaser']){
		$db->beginTransaction();
		$buyrdate = strtotime($_POST['buyrdate']);
		$startyear =  strtotime('01-01-'.date("Y",$buyrdate));
		$endyear =  strtotime('31-12-'.date("Y",$buyrdate).' 23:59:59');
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
			
		$lastid = $purchaser->saveHeaderBuyR($buyrdate,$_POST['suppliercode'],$_POST['supplieraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['totalbuyr'],$userid);
		

			
		
		//$purchaser->setBuyNo($_POST['buyno']);
		$purchaser->setId($lastid);
		
		$arrpostdel = explode(",",$_POST['detailpchrbox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailpchrbox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["detailpchrbox_".$arrpost[$x]."_1"])){
					$checkchars = strpos($_POST["detailpchrbox_".$arrpost[$x]."_1"],"||");
					if ($checkchars !== false){
						$_POST["detailpchrbox_".$arrpost[$x]."_1"] = substr($_POST["detailpchrbox_".$arrpost[$x]."_1"],0,$checkchars);
					}
					
					$stock->setId("");
					$stock->setCode($_POST["detailpchrbox_".$arrpost[$x]."_1"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["detailpchrbox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_6"],'calculate');
					$_POST["detailpchrbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_8"],'calculate');
					$_POST["detailpchrbox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_9"],'calculate');
					$_POST["detailpchrbox_".$arrpost[$x]."_10"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_10"],'calculate');
					$_POST["detailpchrbox_".$arrpost[$x]."_11"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_11"],'calculate');
					$_POST["detailpchrbox_".$arrpost[$x]."_13"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_13"],'calculate');
					
					$totals = $_POST["detailpchrbox_".$arrpost[$x]."_6"] * $_POST["detailpchrbox_".$arrpost[$x]."_8"];
					$buyrpricead = $_POST["detailpchrbox_".$arrpost[$x]."_8"] - ($_POST["detailpchrbox_".$arrpost[$x]."_9"] / 100 * $_POST["detailpchrbox_".$arrpost[$x]."_8"]);
					$buyrpricead = $buyrpricead - ($_POST["detailpchrbox_".$arrpost[$x]."_10"] / 100 * $buyrpricead);
					$buyrpricead = $buyrpricead + ($_POST["detailpchrbox_".$arrpost[$x]."_11"] / 100 * $buyrpricead);
					$realbuyrprice = $buyrpricead - ($_POST['disc'] / 100 * $buyrpricead);
					$realbuyrprice = $realbuyrprice + ($_POST['tax'] / 100 * $realbuyrprice);

					if ($_POST["detailpchrbox_".$arrpost[$x]."_7"] == $getunit['funit']){
						$quantity = $_POST["detailpchrbox_".$arrpost[$x]."_6"] * $getunit['cvalue'];
						$realbuyrprice = $realbuyrprice / $getunit['cvalue'];
					}
					else{
						$quantity = $_POST["detailpchrbox_".$arrpost[$x]."_6"];
						$_POST["detailpchrbox_".$arrpost[$x]."_7"] = $getunit['lunit'];
					}
					
					$realbuyrprice = $_POST["detailpchrbox_".$arrpost[$x]."_13"] / $quantity;
					
					//get detail buy
					//$purchase->setDetailId($arrpost[$x]);
					//$detailbuy = $purchase->getDetailBuyIndv();
	
					$lastdbrid = $purchaser->saveDetailBuyR($_POST["detailpchrbox_".$arrpost[$x]."_1"],$_POST["detailpchrbox_".$arrpost[$x]."_2"],$_POST["detailpchrbox_".$arrpost[$x]."_3"],$_POST["detailpchrbox_".$arrpost[$x]."_4"],$_POST["detailpchrbox_".$arrpost[$x]."_5"],$_POST["detailpchrbox_".$arrpost[$x]."_6"],$_POST["detailpchrbox_".$arrpost[$x]."_7"],$_POST["detailpchrbox_".$arrpost[$x]."_8"],$_POST["detailpchrbox_".$arrpost[$x]."_9"],$_POST["detailpchrbox_".$arrpost[$x]."_10"],$_POST["detailpchrbox_".$arrpost[$x]."_11"],togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_12"],'calculate'),$_POST["detailpchrbox_".$arrpost[$x]."_13"],$_POST["detailpchrbox_".$arrpost[$x]."_14"],$buyrdate,$totals,$buyrpricead,$realbuyrprice,$quantity,$unitquantity,$getstock['unitcode'],$arrpost[$x]);
					
					
					
					
					//hutang/piutang
					$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$_POST['suppliercode']."' ");
					
					
					$checkdebt = $payment->getHeaderPaymentByMonth($buyrdate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],0);
					
					$_POST['id'] = $lastdbrid;
					
					//jika blm ada data hutang di periode bln ini
					if (empty($checkdebt['hpid'])){
					
					$_POST["detailpchrbox_".$arrpost[$x]."_12"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_12"],'calculate');
					$lastmonth = $payment->getDetailLastPaymentByMonth($buyrdate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
					
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
					
					
					//jika remainingnowh ada
					if (!empty($checknulfremainingprevioush[0])){
					$grandtotals = $_POST["detailpchrbox_".$arrpost[$x]."_12"] + $remainingprevioush;
					
					}
					
					//jika remainingnow ada
					else if (!empty($checknulfremainingprevious[0])){
					
					$grandtotals = $_POST["detailpchrbox_".$arrpost[$x]."_12"] - $remainingprevious;
					if ($grandtotals < 0){
					
					$remainingnow = abs($grandtotals);
					$grandtotals = 0;
					}	
					else{
					
					$remainingnow = 0;
					$grandtotals = abs($grandtotals);
					}
					
					}
					else {
					$grandtotals =$_POST["detailpchrbox_".$arrpost[$x]."_12"];
					}
					
					if ($grandtotals <=0 ){
					$complete = 1;
					$completedate = $buyrdate;
					}
					else{
					$complete = 0;
					$completedate = 0;
					}
					
					$invstartdate = strtotime('01-'.date("m-Y",$buyrdate));
					$invenddate = strtotime(date('t-m-Y',$buyrdate));
					
					
					$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$buyrdate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST["detailpchrbox_".$arrpost[$x]."_12"],abs($grandtotals),$userid,2,$remainingprevioush,$remainingnowh);
					
					$payment->setId($lastidpaym);
					$payment->saveDetailPayment($_POST['id'],$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
					
					$payment->updateDebtCreditnotlive($_POST['suppliercode'],0);
					}
			
			//jika ada data hutang di periode bln ini
			else{
						
					
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						$_POST["detailpchrbox_".$arrpost[$x]."_12"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_12"],'calculate');
						
						$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
						$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
						$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - ($totalpaybuyr['totalpay']+$_POST["detailpchrbox_".$arrpost[$x]."_12"]));
						
						
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
						$grandtotals = abs($ttlfolpaym - $_POST["detailpchrbox_".$arrpost[$x]."_12"]);
						$remainingnow = 0;
						}
						
						}
						else{
						$grandtotals = abs($totalpayment+$oldheader['remainingprevioush']);
						}
						
						//echo $grandtotals;
						
						$payment->updateHeaderPaymentOnlycash(abs($totalpayment),$grandtotals,$userid,$remainingnow,$remainingnowh);
						
						$payment->saveDetailPayment($_POST['id'],$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						
						}
						
						else{
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						
						$totalpayment = abs($ttlfolpaym);
						
						$grandtotals = $totalpayment + $oldheader['remainingprevious'];
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
					
						
						
						$payment->saveDetailPayment($_POST['id'],$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						}
					}
					/* echo "Sub Total (Rp.) : ".abs($_POST["detailpchrbox_".$arrpost[$x]."_12"]);
					echo "<br>Grand Total (Rp.) :".abs($grandtotals);
					echo "<br>Rprev h : ".abs($remainingprevioush);
					echo "<br>Rprev   : ".abs($remainingprevious);
					echo "<br>Rnow h : ".abs($remainingnowh);
					echo "<br>now   : ".abs($remainingnow); */
					//hutang/piutang
				}
			}
		}
		$db->endTransaction();
		redirecting("purchaser.php?id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_purchaser']){
		//print_r($_POST);
		if (!empty($_POST['id'])){
			$purchaser->setId($_POST['id']);
			$oldheaderbuyr = $purchaser->getHeaderBuyR();
			$db->beginTransaction();
			$buyrdate = strtotime($_POST['buyrdate']);
			$startyear =  strtotime('01-01-'.date("Y",$buyrdate));
			$endyear =  strtotime('31-12-'.date("Y",$buyrdate).' 23:59:59');
			
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
			

			
			$purchaser->updateHeaderBuyR($buyrdate,$_POST['suppliercode'],$_POST['supplieraddrid'],$_POST['description'],$_POST['totals'],$_POST['disc'],$_POST['tax'],$_POST['totalbuyr'],$userid);
			
			
			if ( $oldheaderbuyr['suppliercode'] == $_POST['suppliercode'] )
			{
			
			if ($buyrdate == $oldheaderbuyr['buyrdate']){
			$statusedit = 0;
			}
			else{
			$statusedit = 1;
			}
			
			}
			else{
			$statusedit = 1;
			}
			
			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailpchrbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$getdbrid = $purchaser->getDetailIdFromItem($arrpostdel[$x]);
						$purchaser->setDetailId($getdbrid);
						$olddetail = $purchaser->getDetailBuyRIndv();
						
						$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$oldheaderbuyr['suppliercode']."' ");
						
						$checkdebt = $payment->getallHeaderPaymentByMonth($oldheaderbuyr['buyrdate'],2,$dbcustsup['supplierid'],$dbcustsup['supplieraddrid'],$dbcustsup['customerid'],0);
						$payment->setId($checkdebt['hpid']);
						$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$getdbrid."' AND types = 'returnby' ");
						
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
						
						
						
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						}
						
						}
						
						$stock->setCode($olddetail['stockcode']);
						
						$purchaser->deleteDetailRItem($olddetail['stockcode']);
						$db->query("DELETE FROM detailbuyr WHERE dbrid='".$getdbrid."'");
						$stock->addStock($olddetail['quantity']);
					}
				}
			}
			
			//edited rows
			$arrpost = array_diff($arrpostt,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailpchrbox_".$arrpost[$x]."_1"])){
						$checkchars = strpos($_POST["detailpchrbox_".$arrpost[$x]."_1"],"||");
						if ($checkchars !== false){
							$_POST["detailpchrbox_".$arrpost[$x]."_1"] = substr($_POST["detailpchrbox_".$arrpost[$x]."_1"],0,$checkchars);
						}
						
						$purchaser->setDetailId($purchaser->getDetailIdFromItem($arrpost[$x]));
						$olddetail = $purchaser->getDetailBuyRIndv();
						
						$_POST["detailpchrbox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_6"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_8"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_9"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_10"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_10"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_11"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_11"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_12"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_12"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_13"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_13"],'calculate');
						
						$stock->setId("");
						$stock->setCode($_POST["detailpchrbox_".$arrpost[$x]."_1"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];

						$_POST["detailpchrbox_".$arrpost[$x]."_4"] = $getstock['brandcode'];
						$_POST["detailpchrbox_".$arrpost[$x]."_5"] = $getstock['typecode'];
						
						$totals = $_POST["detailpchrbox_".$arrpost[$x]."_6"] * $_POST["detailpchrbox_".$arrpost[$x]."_8"];
						$purchaserpricead = $_POST["detailpchrbox_".$arrpost[$x]."_8"] - ($_POST["detailpchrbox_".$arrpost[$x]."_9"] / 100 * $_POST["detailpchrbox_".$arrpost[$x]."_8"]);
						$purchaserpricead = $purchaserpricead - ($_POST["detailpchrbox_".$arrpost[$x]."_10"] / 100 * $purchaserpricead);
						$purchaserpricead = $purchaserpricead + ($_POST["detailpchrbox_".$arrpost[$x]."_11"] / 100 * $purchaserpricead);
						$realbuyrprice = $purchaserpricead - ($_POST['disc'] / 100 * $purchaserpricead);
						$realbuyrprice = $realbuyrprice + ($_POST['tax'] / 100 * $realbuyrprice);

						if ($_POST["detailpchrbox_".$arrpost[$x]."_7"] == $getunit['funit']){
							$quantity = $_POST["detailpchrbox_".$arrpost[$x]."_6"] * $getunit['cvalue'];
							$realbuyrprice = $realbuyrprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailpchrbox_".$arrpost[$x]."_6"];
							$_POST["detailpchrbox_".$arrpost[$x]."_7"] = $getunit['lunit'];
						}
						
						$realbuyrprice = $_POST["detailpchrbox_".$arrpost[$x]."_13"] / $quantity;
						
						$purchaser->updateDetailBuyR($_POST["detailpchrbox_".$arrpost[$x]."_1"],$_POST["detailpchrbox_".$arrpost[$x]."_2"],$_POST["detailpchrbox_".$arrpost[$x]."_3"],$_POST["detailpchrbox_".$arrpost[$x]."_4"],$_POST["detailpchrbox_".$arrpost[$x]."_5"],$_POST["detailpchrbox_".$arrpost[$x]."_6"],$_POST["detailpchrbox_".$arrpost[$x]."_7"],$_POST["detailpchrbox_".$arrpost[$x]."_8"],$_POST["detailpchrbox_".$arrpost[$x]."_9"],$_POST["detailpchrbox_".$arrpost[$x]."_10"],$_POST["detailpchrbox_".$arrpost[$x]."_11"],togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_12"],'calculate'),$_POST["detailpchrbox_".$arrpost[$x]."_13"],$_POST["detailpchrbox_".$arrpost[$x]."_14"],$buyrdate,$totals,$purchaserpricead,$realbuyrprice,$quantity,$unitquantity,$getstock['unitcode'],$arrpost[$x],$olddetail);
						
						//jika sama header dulu & sekarang
						if ( $statusedit == 0 ) {
						
						//hutang / piutang
						$idthis = $purchaser->getDetailIdFromItem($arrpost[$x]);
						$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$_POST['suppliercode']."' ");
					
						$checkdebt = $payment->getallHeaderPaymentByMonth($buyrdate,2,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],0);
						$payment->setId($checkdebt['hpid']);
						$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$idthis."' AND types = 'returnby' ");
						$payment->setDetailId($getdetail['dpid']);
						
						$olddetails = $payment->getDetailPaymentFromSale($idthis,"returnby");
						$payment->updateDetailPayment($idthis,$_POST["detailpchrbox_".$arrpost[$x]."_12"],0,"","returnby","bb",0,$olddetails );
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
						
						
						
						$payment->updateDebtCreditEdit3($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						}
						}//jika sama header dulu & sekarang
						
						//jika tidak sama header dulu & sekarang
						else{
						$idthis = $purchaser->getDetailIdFromItem($arrpost[$x]);
						//hapus data yang lama
						$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$oldheaderbuyr['suppliercode']."' ");
						
						$checkdebt = $payment->getallHeaderPaymentByMonth($oldheaderbuyr['buyrdate'],2,$dbcustsup['supplierid'],$dbcustsup['supplieraddrid'],$dbcustsup['customerid'],0);
						$payment->setId($checkdebt['hpid']);
						$getdetail = $db->fetch_one("SELECT * FROM detailpayment WHERE hpid	= '".$checkdebt['hpid']."' AND hsid = '".$purchaser->getDetailIdFromItem($arrpost[$x])."' AND types = 'returnby' ");
						
						
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
						
						
						
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						}
						
						}
						//hapus data yang lama
						
						//tambah data baru
						//hutang/piutang
					$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$_POST['suppliercode']."' ");
					
					
					$checkdebt = $payment->getallHeaderPaymentByMonth($buyrdate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],0);

					
					//jika blm ada data hutang di periode bln ini
					if (empty($checkdebt['hpid'])){
					
					
					$lastmonth = $payment->getDetailLastPaymentByMonth($buyrdate,1,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid']);
					
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
					
					
					//jika remainingnowh ada
					if (!empty($checknulfremainingprevioush[0])){
					$grandtotals = $_POST["detailpchrbox_".$arrpost[$x]."_12"] + $remainingprevioush;
					
					}
					
					//jika remainingnow ada
					else if (!empty($checknulfremainingprevious[0])){
					
					$grandtotals = $_POST["detailpchrbox_".$arrpost[$x]."_12"] - $remainingprevious;
					if ($grandtotals < 0){
					
					$remainingnow = abs($grandtotals);
					$grandtotals = 0;
					}	
					else{
					
					$remainingnow = 0;
					$grandtotals = abs($grandtotals);
					}
					
					}
					else {
					$grandtotals =$_POST["detailpchrbox_".$arrpost[$x]."_12"];
					}
					
					if ($grandtotals <=0 ){
					$complete = 1;
					$completedate = $buyrdate;
					}
					else{
					$complete = 0;
					$completedate = 0;
					}
					
					$invstartdate = strtotime('01-'.date("m-Y",$buyrdate));
					$invenddate = strtotime(date('t-m-Y',$buyrdate));
					
					
					$lastidpaym = $payment->saveHeaderPayment($dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],$_POST['customeraddrid'],$buyrdate,togglenumber($_POST['cash'],'calculate'),togglenumber($_POST['transfer'],'calculate'),$_POST['bank'],$_POST['accname'],$_POST['accnumber'],$_POST['transfernotes'],togglenumber($_POST['cheque'],'calculate'),$_POST['chequenotes'],strtotime($_POST['chequedates']),strtotime($_POST['chequeduedate']),togglenumber($_POST['giro'],'calculate'),$_POST['gironotes'],strtotime($_POST['girodates']),strtotime($_POST['giroduedate']),$remainingprevious,$remainingnow,$complete,$completedate,$invstartdate,$invenddate,"",$_POST["detailpchrbox_".$arrpost[$x]."_12"],abs($grandtotals),$userid,2,$remainingprevioush,$remainingnowh);
					
					$payment->setId($lastidpaym);
					$payment->saveDetailPayment($idthis,$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
					
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
						$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - ($totalpaybuyr['totalpay']+$_POST["detailpchrbox_".$arrpost[$x]."_12"]));
						
						
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
						$grandtotals = abs($ttlfolpaym - $_POST["detailpchrbox_".$arrpost[$x]."_12"]);
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
						$remainingnow = $remainingnow;
						}
						}
						$payment->updateHeaderPaymentOnlycash(abs($totalpayment),$grandtotals,$userid,$remainingnow,$remainingnowh);
						
						$payment->saveDetailPayment($idthis,$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						
						}
						
						else{
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						
						$totalpayment = abs($ttlfolpaym);
						
						$grandtotals = $totalpayment + $oldheader['remainingprevious'];
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$oldheader['remainingnow'],$oldheader['remainingnowh']);
					
						
						
						$payment->saveDetailPayment($idthis,$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						}
					}
					
						
						//tambah data baru
						
						
						}
						
						
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailpchrbox_rowsadded']);
			/* $arrpost = array_diff($arrpost,$arrpostdel); */
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailpchrbox_".$arrpost[$x]."_1"])){
						$checkchars = strpos($_POST["detailpchrbox_".$arrpost[$x]."_1"],"||");
						if ($checkchars !== false){
							$_POST["detailpchrbox_".$arrpost[$x]."_1"] = substr($_POST["detailpchrbox_".$arrpost[$x]."_1"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["detailpchrbox_".$arrpost[$x]."_1"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailpchrbox_".$arrpost[$x]."_6"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_6"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_8"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_9"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_9"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_10"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_10"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_11"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_11"],'calculate');
						$_POST["detailpchrbox_".$arrpost[$x]."_13"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_13"],'calculate');
						
						$totals = $_POST["detailpchrbox_".$arrpost[$x]."_6"] * $_POST["detailpchrbox_".$arrpost[$x]."_8"];
						$buyrpricead = $_POST["detailpchrbox_".$arrpost[$x]."_8"] - ($_POST["detailpchrbox_".$arrpost[$x]."_9"] / 100 * $_POST["detailpchrbox_".$arrpost[$x]."_8"]);
						$buyrpricead = $buyrpricead - ($_POST["detailpchrbox_".$arrpost[$x]."_10"] / 100 * $buyrpricead);
						$buyrpricead = $buyrpricead + ($_POST["detailpchrbox_".$arrpost[$x]."_11"] / 100 * $buyrpricead);
						$realbuyrprice = $buyrpricead - ($_POST['disc'] / 100 * $buyrpricead);
						$realbuyrprice = $realbuyrprice + ($_POST['tax'] / 100 * $realbuyrprice);

						if ($_POST["detailpchrbox_".$arrpost[$x]."_7"] == $getunit['funit']){
							$quantity = $_POST["detailpchrbox_".$arrpost[$x]."_6"] * $getunit['cvalue'];
							$realbuyrprice = $realbuyrprice / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailpchrbox_".$arrpost[$x]."_6"];
							$_POST["detailpchrbox_".$arrpost[$x]."_7"] = $getunit['lunit'];
						}
						
						$realbuyrprice = $_POST["detailpchrbox_".$arrpost[$x]."_13"] / $quantity;
						
						$lastdbidthis = $purchaser->saveDetailBuyR($_POST["detailpchrbox_".$arrpost[$x]."_1"],$_POST["detailpchrbox_".$arrpost[$x]."_2"],$_POST["detailpchrbox_".$arrpost[$x]."_3"],$_POST["detailpchrbox_".$arrpost[$x]."_4"],$_POST["detailpchrbox_".$arrpost[$x]."_5"],$_POST["detailpchrbox_".$arrpost[$x]."_6"],$_POST["detailpchrbox_".$arrpost[$x]."_7"],$_POST["detailpchrbox_".$arrpost[$x]."_8"],$_POST["detailpchrbox_".$arrpost[$x]."_9"],$_POST["detailpchrbox_".$arrpost[$x]."_10"],$_POST["detailpchrbox_".$arrpost[$x]."_11"],togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_12"],'calculate'),$_POST["detailpchrbox_".$arrpost[$x]."_13"],$_POST["detailpchrbox_".$arrpost[$x]."_14"],$buyrdate,$totals,$buyrpricead,$realbuyrprice,$quantity,$unitquantity,$getstock['unitcode'],$arrpost[$x]);			

						
						$dbcustsup = $db->fetch_one("SELECT * FROM supplier s LEFT JOIN customer c ON c.customercode = s.suppliercode WHERE suppliercode = '".$_POST['suppliercode']."' ");
					
						$checkdebt = $payment->getallHeaderPaymentByMonth($buyrdate,2,$dbcustsup['supplierid'],$_POST['supplieraddrid'],$dbcustsup['customerid'],0);
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						$_POST["detailpchrbox_".$arrpost[$x]."_12"] = togglenumber($_POST["detailpchrbox_".$arrpost[$x]."_12"],'calculate');
						
						$totalpaysale = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='sale' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuy = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='buy' AND hpid='".$checkdebt['hpid']."'");
						$totalpaysaler = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='return' AND hpid='".$checkdebt['hpid']."'");
						$totalpaybuyr = $db->fetch_one("SELECT SUM(pays) AS totalpay FROM detailpayment WHERE types='returnby' AND hpid='".$checkdebt['hpid']."'");
						$ttlfolpaym = ($totalpaysale['totalpay'] - $totalpaysaler['totalpay']) - ($totalpaybuy['totalpay'] - ($totalpaybuyr['totalpay']+$_POST["detailpchrbox_".$arrpost[$x]."_12"]));
						
						
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
						$grandtotals = abs($ttlfolpaym - $_POST["detailpchrbox_".$arrpost[$x]."_12"]);
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
						
						$payment->updateHeaderPaymentOnlycash(abs($totalpayment),$grandtotals,$userid,$remainingnow,$remainingnowh);
						
						$payment->saveDetailPayment($lastdbidthis,$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						
						}
						
						else{
						$payment->setId($checkdebt['hpid']);
						$oldheader = $payment->getHeaderPayment();
						$checknulfremainingprevioush = explode(".",$oldheader['remainingprevioush']);
						$checknulfremainingprevious = explode(".",$oldheader['remainingprevious']);
						$remainingnow = $oldheader['remainingnow'];
						$remainingnowh = $oldheader['remainingnowh'];
						$totalpayment = abs($ttlfolpaym);
						
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
						
						if ($grandtotals < $totalrepayment['totalrepay'] ){
						$remainingnowh = $totalrepayment['totalrepay']-$grandtotals;
						}
						else{
						$remainingnowh = 0;
						}
									
						
						$payment->updateHeaderPaymentOnlycash($totalpayment,$grandtotals,$userid,$remainingnow,$remainingnowh);
					
						
						
						$payment->saveDetailPayment($lastdbidthis,$_POST["detailpchrbox_".$arrpost[$x]."_12"],$buyrdate,"","returnby",0,0);
						$payment->updateDebtCreditEdit2($dbcustsup['supplierid'],$dbcustsup['suppliercode'],$_POST['complete'],$oldheader);
						}

				
						
					}
				}
			}
			$db->endTransaction();
		}
		redirecting("purchaser.php?id=".$_POST['id']);
	}

	$buyrid = $_GET['id'];

	if ($_GET['do'] == 'delete' && !empty($buyrid) && $useraccess['delete_purchaser']){
		$db->beginTransaction();
		
		$purchaser->setId($buyrid);
		$headerbuyr = $purchaser->getHeaderBuyR();
		
		
		
		$supplier->setCode($headerbuyr['suppliercode']);
		$supplier->addDebt($headerbuyr['totalbuyr']);
		
		$purchaser->deleteBuyR();
		$db->endTransaction();
		redirecting("purchaser.php?screen=list");		
	}

	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_purchaser'])){
			redirecting('index.php');
		}
		
		$printtemplate = 'purchaserlist';
	}
	else{
		
		if (empty($useraccess['add_purchaser']) && empty($useraccess['edit_purchaser'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'purchaser';
		if (!empty($buyrid)){
			$purchaser->setId($buyrid);
			$headerbuyr = $purchaser->getHeaderBuyR();
			
			if (empty($headerbuyr['buyrid'])){
				redirecting('purchaser.php?screen=list');
			}
			
			$buydate = date("d-m-Y",$headerbuyr['buydate']);
			$invoicedate = date("d-m-Y",$headerbuyr['buyrdate']);
			$selbuyno = $headerbuyr['buyno'];
			
			$supplier->setCode($headerbuyr['suppliercode']);
			$supplier->setDetailId($headerbuyr['supplieraddrid']);
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
			
			$alldtl = $purchaser->getDetailBuyR();
			$alldetailid = '';
			$arrmaxqty = '';
			$subtotalfk = 0;
			if (sizeof($alldtl) > 0){
				foreach ($alldtl as $aad){
					$stock->setId("");
					$stock->setCode($aad['stockcode']);
					$getds = $stock->getFirstStock();
					
					$getdbid = $db->fetch_one("SELECT * FROM detailbuyritem WHERE dbrid='".$aad['dbrid']."'");
					
					$getdbdbid = $db->fetch_one("SELECT * FROM detailbuy WHERE dbid='".$getdbid['dbid']."'");
					
					$alldetailid .= ','.$getdbid['dbid'];
					$alldetailidjs .= ',\''.$getdbid['dbid'].'\'';
					
					$units->setCode($getds['unitcode']);
					$getunit = $units->getunitDetail();
					$arrmaxqty .= 'arrmaxr["'.$getdbid['dbid'].'"] = '.($getdbdbid['quantity']-$getdbdbid['usedqty']+$getdbid['quantity']);
					$arrunits .= '
						arrunits["'.$getdbid['dbid'].'"] = new Array();
						arrunits["'.$getdbid['dbid'].'"][0] = "'.$getunit['funit'].'";
						arrunits["'.$getdbid['dbid'].'"][1] = "'.$getunit['lunit'].'";';
					$arrconversion .= '
						arrconversion["'.$getdbid['dbid'].'"] = new Array();
						arrconversion["'.$getdbid['dbid'].'"][0] = '.$getunit['cvalue'].';
						arrconversion["'.$getdbid['dbid'].'"][1] = 1;';
				
					$aad['quantityf'] = floor((100-$discount['extradisc'])/100 * $aad['quantityf']);
					if ($aad['quantityf'] < 1){
						$aad['quantityf'] = 1;
					}
						
					$totalsalefk = $aad['quantityf'] * $aad['buyrprice'];
					$totaldiscfk = $aad['disc'] / 100 * $totalsalefk;
					$subtotalfk += ($totalsalefk - $totaldiscfk);
				}
				$alldetailid = substr($alldetailid,1);
				$alldetailidjs = substr($alldetailidjs,1);
			}
			
			if ($statususer == 1){
				$headerbuyr['totals'] = $subtotalfk;
				$totalgdiscfk = $headerbuyr['disc'] / 100 * $subtotalfk;
				$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
				$totalgtaxfk = $headerbuyr['tax'] / 100 * $totalafgdiscfk;
				$headerbuyr['totalbuyr'] = $totalafgdiscfk + $totalgtaxfk;
			}
			
			$ftotals = number_format($headerbuyr['totals'],2,",",".");
			$fdisc = number_format($headerbuyr['disc'],2,",",".");
			$ftax = number_format($headerbuyr['tax'],2,",",".");
			$ftotal = number_format($headerbuyr['totalbuyr'],2,",",".");
			
			$headerbuyr = array_map("htmlspecialchars",$headerbuyr);
		}
	}

	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>