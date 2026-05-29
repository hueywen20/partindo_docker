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
	require_once "class/Assembly.php";
	require_once "class/DeAssembly.php";
	require_once "class/purchase.php";
	require_once "class/Sale.php";
	require_once "class/PurchaseR.php";
	require_once "class/AdjustOut.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	$supplier = new supplier();
	$customer = new customer();
	$assembly = new Assembly();
	$deassembly = new DeAssembly();
	$purchase = new Purchase();
	$sale = new Sale();
	$purchaser = new PurchaseR();
	$aout = new AdjustOut();
	
	if (empty($useraccess['view_stocklist'])){
		redirecting('index.php');
	}
	
	if ($_GET['getlist'] == 'xml'){
		if (empty($_GET['id'])){
/* 			if (isset($_GET['keyword'])){
				$keywords = $_GET['keyword'];
				if (sizeof($keywords) > 0){
					if (!isset($_GET['asm'])){
						$_GET['asm'] = -1;
					}
					if (!isset($_GET['status'])){
						$_GET['status'] = -1;
					}
					$fields = $_GET['field'];
					$allstock = $stock->searchStock($keywords,$fields,$_GET['asm'],$_GET['status']);
					$totalrows = sizeof($allstock);
					$totalpgs = ceil($totalrows / $general['showperpage']);
					$pgs = handlepage($_GET['p'],$totalpgs);
					$liststock = $stock->searchStock($keywords,$fields,$_GET['asm'],$_GET['status'],$pgs);
				}
			}
			else{
				$allstock = $stock->getListStock();
				$totalrows = sizeof($allstock);
				$totalpgs = ceil($totalrows / $general['showperpage']);
				$pgs = handlepage($_GET['p'],$totalpgs);
				$liststock = $stock->getListStock($pgs);
			}
 */					
			$keywords = $_GET['keyword'];
			$fields = $_GET['field'];
			if (!isset($_GET['asm'])){
				$_GET['asm'] = -1;
			}
			if (!isset($_GET['status'])){
				$_GET['status'] = -1;
			}
			$allstock = $stock->searchStock($keywords,$fields,'data',$_GET['asm'],$_GET['status']);
			$totalrows = sizeof($allstock);
			$totalpgs = ceil($totalrows / $general['showperpage']);
			$pgs = handlepage($_GET['p'],$totalpgs);
			
			$liststock = $stock->searchStock($keywords,$fields,'data',$_GET['asm'],$_GET['status'],$pgs);
			
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($liststock) > 0){
				//$ctr = 1+$_GET['posStart'];
				$ctr = ($pgs - 1) * $general['showperpage'] + 1;
				foreach ($liststock as $list){
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					$stock->setCode($list['stockcode']);
					$allpartno = $stock->getAllPartNo();
					$splits = sizeof($allpartno);
					if ($splits > 0){
						if ($splits > 1){
							//$rstext = ' rowspan="'.$splits.'"';
							$rstext = ' rowspan="2"';
						}
						$io = 0; 
						foreach ($allpartno as $apn){
							if ($io == 0){
								$listsplit .= '<td width="'.$cwarr[12].'" class="stufflist" align="left">'.htmlspecialchars($apn['partno']);
								if ($splits > 2){
									$listsplit .= '<span style="float: right"><img src="img/arrow_expand.png" id="arrow_'.$ctr.'" border="0" style="cursor: pointer" onclick="expandall('.$ctr.')" title="Expand"></span>';
								}
								$listsplit .= '</td>';
								//$listsplit .= '<cell>'.htmlspecialchars($apn['partno']).'</cell>';
							}
							else{
								$listsplit2 .= '
									<tr'.(($io > 1)?' style="display: none"':'').' id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'stock.php?id='.$list['stockid'].'\',\'_self\')">
										<td width="'.$cwarr[12].'" class="stufflist" align="left">'.htmlspecialchars($apn['partno']).'</td>
									</tr>
								';
								/*$listsplit2 .= '
									<row id="'.$list['stockid'].'_'.$io.'">
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell></cell>
										<cell>'.htmlspecialchars($apn['partno']).'</cell>
									</row>';*/
							}
							$io++;
						}
					}
					else{
						$listsplit .= '<td width="'.$cwarr[12].'" class="stufflist" align="left"></td>';
					}
					//$stockstatus = $stock->getStockAll();
					$brand->setCode($list['brandcode']);
					$getbrandname = $brand->getBrandDetail();
					$type->setCode($list['typecode']);
					$gettypename = $type->gettypeDetail();
					$location->setCode($list['locationcode']);
					$getlocationname = $location->getlocationDetail();
					$units->setCode($list['unitcode']);
					$getlowunit = $units->getunitDetail();
					if ($list['minexpdate'] == 0){
						$mexdate = 0;
					}
					else{
						$mexdate = date("d - m - Y",$list['minexpdate']);
					}

					//$list['typecode'] = substr($list['typecode'],0,15);
					//$list['brandcode'] = substr($list['brandcode'],0,10);
					//$list['size'] = substr($list['size'],0,10);
					
					$list['typecode'] = wordwrap($list['typecode'],19,"<br>",true);
					$list['brandcode'] = wordwrap($list['brandcode'],15,"<br>",true);
					$list['size'] = wordwrap($list['size'],10,"<br>",true);
					
					$list['typecode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($list['typecode']));
					$list['brandcode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($list['brandcode']));
					$list['size'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($list['size']));
					
					$stock->setId($list['stockid']);
					$getmainphotos = $stock->getMainPhoto();
					$stock->setId("");

			/*$firststock = $stock->getFirstStock();

			$remainings = 0;
			if (sizeof($firststock) > 0){
				$remainings += $firststock['quantity'];
			}
			
			$getassemblyprt = $assembly->getAssemblyParent($firststock['stockcode']);
			
			$detailstock = $stock->getStockDetail($getassemblyprt);
			if (sizeof($detailstock) > 0){
				foreach ($detailstock as $dtls){						
					if ($dtls['status'] == 'purchase'){
						$remainings += $dtls['stockin'];
					}
					else if ($dtls['status'] == 'sale'){
						if ($firststock['assembly'] == 1){
							$remainings += $dtls['stockout'];
						}
						$remainings -= $dtls['stockout'];
					}
					else if ($dtls['status'] == 'saleasm'){
						$remainings -= $dtls['stockout'];
					}
					else if ($dtls['status'] == 'buyreturn'){
						$remainings -= $dtls['stockout'];
					}
					else if ($dtls['status'] == 'salereturn'){
						$remainings += $dtls['stockin'];
					}
					else if ($dtls['status'] == 'adjustin'){
						$remainings += $dtls['stockin'];
					}
					else if ($dtls['status'] == 'adjustout'){
						$remainings -= $dtls['stockout'];
					}
					else if ($dtls['status'] == 'logdeassembly' && $dtls['stockin'] > 0){
						$remainings += $dtls['stockin'];
					}
					else if ($dtls['status'] == 'logdeassembly' && $dtls['stockout'] > 0){
						$remainings -= $dtls['stockout'];
					}
					else if ($dtls['status'] == 'logdeassemblyparent' && $dtls['stockin'] > 0){
						$remainings += $dtls['stockin'];
					}
					else if ($dtls['status'] == 'logdeassemblyparent' && $dtls['stockout'] > 0){
						$remainings -= $dtls['stockout'];
					}
				}
			}
			$list['realremaining'] = $remainings;*/

					if ($statususer == 1){
						//get purchase
						$dbpurchase['q'] = 0;
						$dbpurch = $db->fetch_all("SELECT * FROM detailbuy WHERE stockcode='".$db->clean($list['stockcode'])."'");
						if (sizeof($dbpurch) > 0){
							foreach ($dbpurch as $dpch){
								$dpch['qty'] = floor((100-$discount['extradisc'])/100 * $dpch['quantity']);
								$dbpurchase['q'] += $dpch['qty'];
							}
						}
						//get sale
						$dbsale['q'] = 0;
						$dbfa = $db->fetch_all("SELECT * FROM detailsale WHERE stockcode='".$db->clean($list['stockcode'])."'");
						if (sizeof($dbfa) > 0){
							foreach ($dbfa as $rsfa){
								$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
								$dbsale['q'] += $rsfa['qty'];
							}
						}
						//get purchase return
						$dbpurchaser['q'] = 0;
						$dbfa = $db->fetch_all("SELECT * FROM detailbuyr WHERE stockcode='".$db->clean($list['stockcode'])."'");
						if (sizeof($dbfa) > 0){
							foreach ($dbfa as $rsfa){
								$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
								$dbpurchaser['q'] += $rsfa['qty'];
							}
						}
						//get sale return
						$dbsaler['q'] = 0;
						$dbfa = $db->fetch_all("SELECT * FROM detailsaler WHERE stockcode='".$db->clean($list['stockcode'])."'");
						if (sizeof($dbfa) > 0){
							foreach ($dbfa as $rsfa){
								$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
								$dbsaler['q'] += $rsfa['qty'];
							}
						}
						//get adjust in
						$dbain['q'] = 0;
						$dbfa = $db->fetch_all("SELECT * FROM detailadjustin WHERE stockcode='".$db->clean($list['stockcode'])."'");
						if (sizeof($dbfa) > 0){
							foreach ($dbfa as $rsfa){
								$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
								$dbain['q'] += $rsfa['qty'];
							}
						}
						//get adjust out
						$dbaout['q'] = 0;
						$dbfa = $db->fetch_all("SELECT * FROM detailadjustout WHERE stockcode='".$db->clean($list['stockcode'])."'");
						if (sizeof($dbfa) > 0){
							foreach ($dbfa as $rsfa){
								$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
								$dbaout['q'] += $rsfa['qty'];
							}
						}
						
						$getassemblyp = $assembly->getAssemblyParent($list['stockcode']);
						//get assembly detail
						$getassemblypsize = sizeof($getassemblyp);
						$assemblyscq = array();
						$assemblyscqt = array();
						$dbsasm['q'] = 0;
						$dbsrasm['q'] = 0;
						if ($getassemblypsize > 0){
							$idparentasm = '';
							foreach ($getassemblyp as $gasbp){
								$idparentasm .= ',\''.$db->clean($gasbp['stockcode']).'\'';
								$assemblyscq[$gasbp['stockcode']] = $gasbp['sccquantity'];
								
								$gettotalqty = $db->fetch_one("SELECT SUM(sccquantity) AS totalq FROM detailstockassembly WHERE stockcode='".$db->clean($gasbp['stockcode'])."'");
								$assemblyscqt[$gasbp['stockcode']] = $gettotalqty['totalq'];
							}
							$sql = ' WHERE a.stockcode IN ('.substr($idparentasm,1).')';
							//get sale
							$dbsaleasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsale a INNER JOIN headersale b ON a.saleno=b.saleno".$sql." ORDER BY a.saledate");
							if (sizeof($dbsaleasm) > 0){
								foreach ($dbsaleasm as $dbsa){
									$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
									$dbsasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
								}
							}
							
							//get sale return
							$dbsalerasm = $db->fetch_all("SELECT a.*, b.customercode FROM detailsaler a INNER JOIN headersaler b ON a.salerid=b.salerid".$sql." ORDER BY a.salerdate");
							if (sizeof($dbsalerasm) > 0){
								foreach ($dbsalerasm as $dbsa){
									$dbsa['quantity'] = floor((100-$discount['extradisc'])/100 * $dbsa['quantity']);
									$dbsrasm['q'] += $dbsa['quantity'] * $assemblyscq[$dbsa['stockcode']];
								}
							}
						}
						
						//get deassembly
						$dbdeasm['q'] = 0;
						$dbfa = $db->fetch_all("SELECT * FROM logdeassembly WHERE stockcode='".$db->clean($list['stockcode'])."'");
						if (sizeof($dbfa) > 0){
							foreach ($dbfa as $rsfa){
								$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
								$dbdeasm['q'] += $rsfa['qty'];
							}
						}
						
						$list['quantity'] = floor((100-$discount['extradisc'])/100 * $list['quantity']);
						
						$stocknow = $list['quantity']+$dbpurchase['q']-$dbsale['q']-$dbpurchaser['q']+$dbsaler['q']+$dbain['q']-$dbaout['q']+$dbdeasm['q']-$dbsasm['q']+$dbsrasm['q'];
						if ($stocknow < 0){
							$stocknow = 0;
						}
					}
					else{
						$stocknow = $list['realremaining'];
					}
					
					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'stock.php?id='.$list['stockid'].'\',\'_self\')">
								<td height="45" id="row_'.$ctr.'_0" class="stufflist" width="'.$cwarr[0].'" align="right"'.$rstext.'>'.number_format($ctr,0,",",".").'</td>
								<td id="row_'.$ctr.'_1" class="stufflist" width="'.$cwarr[1].'" align="left"'.$rstext.'>'.htmlspecialchars($list['stockcode']).'</td>
								<td id="row_'.$ctr.'_2" class="stufflist" width="'.$cwarr[2].'" align="left"'.$rstext.'>'.htmlspecialchars($list['generalname']).'</td>
								<td id="row_'.$ctr.'_3" class="stufflist" width="'.$cwarr[3].'" align="left"'.$rstext.'>'.$list['brandcode'].'</td>
								<td id="row_'.$ctr.'_4" class="stufflist" width="'.$cwarr[4].'" align="left"'.$rstext.'>'.$list['typecode'].'</td>
								<td id="row_'.$ctr.'_5" class="stufflist" width="'.$cwarr[5].'" align="left"'.$rstext.'>'.$list['size'].'</td>
								<td id="row_'.$ctr.'_6" class="stufflist" width="'.$cwarr[6].'" align="center"'.$rstext.'>'.htmlspecialchars($list['locationcode']).'</td>
								<td id="row_'.$ctr.'_7" class="stufflist" width="'.$cwarr[7].'" align="right"'.$rstext.'>'.number_format($stocknow,0,",",".").'</td>
								<td id="row_'.$ctr.'_8" class="stufflist" width="'.$cwarr[8].'" align="center"'.$rstext.'>'.htmlspecialchars($getlowunit['lunit']).'</td>
								<td class="stufflist" width="'.$cwarr[9].'" align="right"'.$rstext.' id="minprice_'.$ctr.'">'.$codest->convertcodes($list['buyminprice']).'</td>
								<td class="stufflist" width="'.$cwarr[10].'" align="right"'.$rstext.' id="maxprice_'.$ctr.'">'.$codest->convertcodes($list['buymaxprice']).'</td>
								<td id="row_'.$ctr.'_9" class="stufflist" width="'.$cwarr[11].'" align="center"'.$rstext.'>'.$mexdate.'</td>
								'.$listsplit.'
								<td id="row_'.$ctr.'_10" class="stufflist" width="'.$cwarr[13].'" align="center"'.$rstext.'>
								'.((!empty($getmainphotos['filename']))?'
								<a href="products/'.$getmainphotos['filename'].'" target="_blank"><img src="img/icon_view.png" border="0" width="15" height="15"></a>
								':'').'</td>
							</tr>'.$listsplit2.'
					';
					
					/*$lists .= '
						<row id="'.$list['stockid'].'">
							<cell'.$rstext.'>'.$ctr.'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['stockcode']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['generalname']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['brandcode']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['typecode']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['size']).'</cell>
							<cell'.$rstext.'>'.htmlspecialchars($list['locationcode']).'</cell>
							<cell'.$rstext.'>'.intval($list['realremaining']).'</cell>
							<cell'.$rstext.'>'.$codest->convertcodes($list['buyminprice']).'</cell>
							<cell'.$rstext.'>'.$codest->convertcodes($list['buymaxprice']).'</cell>
							<cell'.$rstext.'>'.$mexdate.'</cell>
							'.$listsplit.'
						</row>
					'.$listsplit2;*/
					$ctr++;
				}
			}
			if (!empty($lists)){
				$listheight = $_GET['hdv'] - 30;
				$stocklist = '
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
				
				$stocklist .= '
					<div align="left" style="padding: 10px 5px 0 5px">
					Halaman <b>'.$pgs.'</b> dari <b>'.$totalpgs.'</b>'.$pgslinks.'
					<span style="float: right">
					Record '.number_format($startrecord,0,",",".").' - '.number_format($endrecord,0,",",".").' dari total '.number_format($totalrows,0,",",".").'</span>
					</div>
				';
			}
			//$lists = '<rows>'.$lists.'</rows>';
		}
		else{
			header("Content-type: text/xml");

			$stock->setId($_REQUEST['id']);
			$firststock = $stock->getFirstStock();
			$brand->setCode($firststock['brandcode']);
			$getbrandname = $brand->getBrandDetail();
			$type->setCode($firststock['typecode']);
			$gettypename = $type->gettypeDetail();
			$stock->setCode($firststock['stockcode']);
			$stpn = $stock->getAllPartNo();
			$firstcreated = '';
			if (sizeof($stpn) > 0){
				foreach ($stpn as $stn){
					if (empty($firstcreated)){
						$firstcreated = $stn['partno'];
						break;
					}
				}
			}
			
			$rstextasb = '';
			$listsplitasb = '';
			$listsplit2asb = '';
			if ($firststock['assembly'] == 1){
				$assembly->setCode($firststock['stockcode']);
				$allcomponent = $assembly->getAssemblyComponent();
				$szarrscname = sizeof($allcomponent);
				if ($szarrscname > 0){
					if ($szarrscname > 1){
						$rstextasb = ' rowspan="'.$szarrscname.'"';
					}
					$io = 0; 
					foreach ($allcomponent as $arrscname){
						if ($io == 0){
							$listsplitasb = '<cell align="left">'.htmlspecialchars($arrscname['stockcodecomponent']).'</cell>';
						}
						else{
							$listsplit2asb .= '
								<row id="$dtls[dbid]_'.$io.'">
									<cell></cell>
									<cell></cell>
									<cell></cell>
									<cell></cell>
									<cell></cell>
									<cell></cell>
									<cell align="'.$align.'">'.htmlspecialchars($arrscname['stockcodecomponent']).'</cell>
									<cell></cell>
									<cell></cell>
									<cell></cell>
									<cell align="right">$thecapitals['.$io.']</cell>
									<cell></cell>
									<cell></cell>
									<cell></cell>
									<cell></cell>
								</row>';
						}
						$io++;
					}
				}
				$listsplitasb = addslashes($listsplitasb);
				$listsplit2asb = addslashes($listsplit2asb);
			}

			$ctr = 1;
			$remainings = 0;
			if (sizeof($firststock) > 0){
				$dbstockactiveyear = $db->fetch_one("SELECT * FROM stockanually WHERE year='".$general['yearactivestart']."' AND stockid='".$firststock['stockid']."'");
				if (!empty($dbstockactiveyear['quantity'])){
					$firststock['quantity'] = $dbstockactiveyear['quantity'];
					$firststock['createddate'] = strtotime('01-01-'.$general['yearactive']);
				}
				
				if ($statususer == 1){
					$firststock['quantity'] = floor((100-$discount['extradisc'])/100 * $firststock['quantity']);
				}
				
				$remainings += $firststock['quantity'];
				if ($firststock['expdate'] == 0){
					$mexdate = 0;
				}
				else{
					$mexdate = date("d-m-Y",$firststock['expdate']);
				}
				$lists .= '
					<row id="'.$firststock['stockid'].'">
						<cell>'.$ctr.'</cell>
						<cell>'.date("d-m-Y",$firststock['createddate']).'</cell>
						<cell>'.htmlspecialchars($firstcreated).'</cell>
						<cell>'.htmlspecialchars($firststock['generalname']).'</cell>
						<cell>'.htmlspecialchars($firststock['brandcode']).'</cell>
						<cell>'.htmlspecialchars($firststock['typecode']).'</cell>
						<cell>Stok Awal</cell>
						<cell>'.$firststock['quantity'].'</cell>
						<cell>0</cell>
						<cell>'.$remainings.'</cell>
						<cell>'.$codest->convertcodes($firststock['buyprice']).'</cell>
						<cell>'.$codest->convertcodes(0).'</cell>
						<cell>'.$mexdate.'</cell>
						<cell></cell>
						<cell></cell>
					</row>
				';
				$ctr++;
			}
			
			$getassemblyprt = $assembly->getAssemblyParent($firststock['stockcode']);
			$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
			$enddatetoshow = strtotime("31-12-".$general['yearactiveend']." 23:59:59");
			
			$detailstock = $stock->getStockDetail($getassemblyprt,$startdatetoshow,$enddatetoshow);
			if (sizeof($detailstock) > 0){
				foreach ($detailstock as $dtls){
					$brand->setCode($dtls['brandcode']);
					$getbrandname = $brand->getBrandDetail();
					if (empty($getbrandname['brandname']))
						$getbrandname['brandname'] = $dtls['brandcode'];
					$type->setCode($dtls['typecode']);
					$gettypename = $type->gettypeDetail();
					if (empty($gettypename['typename']))
						$gettypename['typename'] = $dtls['typecode'];
						
					if ($dtls['status'] == 'purchase'){
						$dtls['stockin'] = discq($dtls['stockin']);
						$remainings += $dtls['stockin'];
						$supplier->setCode($dtls['sc']);
						$getsuppliername = $supplier->getsupplierDetail();
						$getscname = $getsuppliername['suppliername'];
						if ($dtls['expdate'] == 0){
							$expdate = 0;
						}
						else{
							$expdate = date("d-m-Y",$dtls['expdate']);
						}
						$align = 'left';
					}
					else if ($dtls['status'] == 'sale'){
						if ($firststock['assembly'] == 1){
							$dtls['stockout'] = discq($dtls['stockout']);
							$remainings += $dtls['stockout'];
							
							//get total purchase
							$getdsids = substr($dtls['dbid'],strpos($dtls['dbid'],'-')+1);
							$dballsaleitem = $db->fetch_all("SELECT dsi.quantity, db.realbuyprice FROM detailbuy db INNER JOIN detailsaleitem dsi ON db.dbid = dsi.dbid WHERE dsi.dsid='".$getdsids."' ORDER BY db.stockcode");
							$totalpurchasm = 0;
							$thecapitals = array();
							if (sizeof($dballsaleitem) > 0){
								$ios = 0;
								foreach ($dballsaleitem as $dbasi){
									/* $totalpurchasm += $dbasi['quantity'] * $dbasi['realbuyprice']; */
									$thecapitals[$ios] = $codest->convertcodes($dbasi['realbuyprice']);
									$ios++;
								}
							}
							
							eval("\$listsplitasbtext = \"$listsplitasb\";");
							eval("\$listsplit2asbtext = \"$listsplit2asb\";");
							
							/* $getdsids = substr($dtls['dbid'],strpos($dtls['dbid'],'-')+1);
							$dballsaleitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$db->clean($getdsids)."'");
							$totalpurchasm = 0;
							$totalpurchasm = $dballsaleitem['price']; */
							
							$lists .= '
								<row id="'.$dtls['dbid'].'-as">
									<cell'.$rstextasb.'>'.$ctr.'</cell>
									<cell'.$rstextasb.'>'.date("d-m-Y",$dtls['date']).'</cell>
									<cell'.$rstextasb.'>'.htmlspecialchars($dtls['partno']).'</cell>
									<cell'.$rstextasb.'>'.htmlspecialchars($dtls['stockname']).'</cell>
									<cell'.$rstextasb.'>'.htmlspecialchars($dtls['brandcode']).'</cell>
									<cell'.$rstextasb.'>'.htmlspecialchars($dtls['typecode']).'</cell>
									'.$listsplitasbtext.'
									<cell'.$rstextasb.'>'.$dtls['stockout'].'</cell>
									<cell'.$rstextasb.'>0</cell>
									<cell'.$rstextasb.'>'.$remainings.'</cell>
									<cell>'.$codest->convertcodes($thecapitals[0]).'</cell>
									<cell'.$rstextasb.'>0</cell>
									<cell'.$rstextasb.'>'.$expdate.'</cell>
									<cell align="'.$align.'"'.$rstextasb.'>'.htmlspecialchars($dtls['invc']).'</cell>
									<cell'.$rstextasb.'>Assembly</cell>
								</row>
							'.$listsplit2asbtext;
						}
						$dtls['stockout'] = discq($dtls['stockout']);
						$remainings -= $dtls['stockout'];
						$customer->setCode($dtls['sc']);
						$getcustomername = $customer->getcustomerDetail();
						$getscname = $getcustomername['customername'];
						$expdate = '0';
						$align = 'right';
					}
					else if ($dtls['status'] == 'saleasm'){
						$dtls['stockout'] = discq($dtls['stockout']);
						$remainings -= $dtls['stockout'];
						$getscname = $dtls['sc'];
						$expdate = '0';
						$align = 'left';
						$dtls['buyprice'] = 0;
					}
					else if ($dtls['status'] == 'buyreturn'){
						$dtls['stockout'] = discq($dtls['stockout']);
						$remainings -= $dtls['stockout'];
						$customer->setCode($dtls['sc']);
						$getsuppliername = $supplier->getsupplierDetail();
						$getscname = $getsuppliername['suppliername'];
						$expdate = '0';
						$align = 'left';
					}
					else if ($dtls['status'] == 'salereturn'){
						$dtls['stockin'] = discq($dtls['stockin']);
						$remainings += $dtls['stockin'];
						$customer->setCode($dtls['sc']);
						$getcustomername = $customer->getcustomerDetail();
						$getscname = $getcustomername['customername'];
						$expdate = '0';
						$align = 'right';
					}
					else if ($dtls['status'] == 'salerasm'){
						$dtls['stockin'] = discq($dtls['stockin']);
						$remainings += $dtls['stockin'];
						$getscname = $dtls['sc'];
						$expdate = '0';
						$align = 'left';
					}
					else if ($dtls['status'] == 'adjustin'){
						$dtls['stockin'] = discq($dtls['stockin']);
						$remainings += $dtls['stockin'];
						$getscname = 'Penyesuaian(+)';
						$expdate = '0';
						$align = 'left';
					}
					else if ($dtls['status'] == 'adjustout'){
						$dtls['stockout'] = discq($dtls['stockout']);
						$remainings -= $dtls['stockout'];
						$getscname = 'Penyesuaian(-)';
						$expdate = '0';
						$align = 'right';
					}
					else if ($dtls['status'] == 'logdeassembly' && $dtls['stockin'] > 0){
						$dtls['stockin'] = discq($dtls['stockin']);
						$remainings += $dtls['stockin'];
						$getscname = $dtls['description'];
						$expdate = '0';
						$align = 'left';
						$dtls['description'] = '';
					}
					else if ($dtls['status'] == 'logdeassembly' && $dtls['stockout'] > 0){
						$dtls['stockout'] = discq($dtls['stockout']);
						$remainings -= $dtls['stockout'];
						$getscname = $dtls['description'];
						$expdate = '0';
						$align = 'left';
						$dtls['description'] = '';
					}
					else if ($dtls['status'] == 'logdeassemblyparent' && $dtls['stockin'] > 0){
						$dtls['stockin'] = discq($dtls['stockin']);
						$remainings += $dtls['stockin'];
						$getscname = $dtls['sc'];
						$expdate = '0';
						$align = 'left';
						$dtls['description'] = 'Re-Assembly';
					}
					else if ($dtls['status'] == 'logdeassemblyparent' && $dtls['stockout'] > 0){
						$dtls['stockout'] = discq($dtls['stockout']);
						$remainings -= $dtls['stockout'];
						$getscname = $dtls['sc'];
						$expdate = '0';
						$align = 'left';
						$dtls['description'] = 'De-Assembly';
					}
					
					if ($dtls['status'] == 'logdeassemblyparent'){
						$rstext = '';
						$listsplit = '';
						$listsplit2 = '';
						$arrscname = explode("|^|",$getscname);
						$szarrscname = sizeof($arrscname);
						if ($szarrscname > 0){
							if ($szarrscname > 1){
								$rstext = ' rowspan="'.$szarrscname.'"';
							}
							$io = 0; 
							for ($io = 0; $io < $szarrscname; $io++){
								if ($io == 0){
									$listsplit = '<cell align="'.$align.'">'.htmlspecialchars($arrscname[$io]).'</cell>';
								}
								else{
									$listsplit2 .= '
										<row id="'.$dtls['dbid'].'_'.$io.'">
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell align="'.$align.'">'.htmlspecialchars($arrscname[$io]).'</cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
											<cell></cell>
										</row>';
								}
							}
							$lists .= '
								<row id="'.$dtls['dbid'].'">
									<cell'.$rstext.'>'.$ctr.'</cell>
									<cell'.$rstext.'>'.date("d-m-Y",$dtls['date']).'</cell>
									<cell'.$rstext.'>'.htmlspecialchars($dtls['partno']).'</cell>
									<cell'.$rstext.'>'.htmlspecialchars($dtls['stockname']).'</cell>
									<cell'.$rstext.'>'.htmlspecialchars($dtls['brandcode']).'</cell>
									<cell'.$rstext.'>'.htmlspecialchars($dtls['typecode']).'</cell>
									'.$listsplit.'
									<cell'.$rstext.'>'.$dtls['stockin'].'</cell>
									<cell'.$rstext.'>'.$dtls['stockout'].'</cell>
									<cell'.$rstext.'>'.$remainings.'</cell>
									<cell'.$rstext.'>'.$codest->convertcodes($dtls['buyprice']).'</cell>
									<cell'.$rstext.'>'.$codest->convertcodes($dtls['saleprice']).'</cell>
									<cell'.$rstext.'>'.$expdate.'</cell>
									<cell align="'.$align.'"'.$rstext.'>'.htmlspecialchars($dtls['invc']).'</cell>
									<cell'.$rstext.'>'.htmlspecialchars($dtls['description']).'</cell>
								</row>
							'.$listsplit2;
						}
					}
					else{
						$lists .= '
							<row id="'.$dtls['dbid'].'">
								<cell>'.$ctr.'</cell>
								<cell>'.date("d-m-Y",$dtls['date']).'</cell>
								<cell>'.htmlspecialchars($dtls['partno']).'</cell>
								<cell>'.htmlspecialchars($dtls['stockname']).'</cell>
								<cell>'.htmlspecialchars($dtls['brandcode']).'</cell>
								<cell>'.htmlspecialchars($dtls['typecode']).'</cell>
								<cell align="'.$align.'">'.htmlspecialchars($getscname).'</cell>
								<cell>'.$dtls['stockin'].'</cell>
								<cell>'.$dtls['stockout'].'</cell>
								<cell>'.$remainings.'</cell>
								<cell>'.$codest->convertcodes($dtls['buyprice']).'</cell>
								<cell>'.$codest->convertcodes($dtls['saleprice']).'</cell>
								<cell>'.$expdate.'</cell>
								<cell align="'.$align.'">'.htmlspecialchars($dtls['invc']).'</cell>
								<cell>'.htmlspecialchars($dtls['description']).'</cell>
							</row>
						';
					}
					
					if ($dtls['status'] == 'salereturn' && $firststock['assembly'] == 1){
						$dtls['stockin'] = discq($dtls['stockin']);
						$remainings -= $dtls['stockin'];
						eval("\$listsplitasbtext = \"$listsplitasb\";");
						eval("\$listsplit2asbtext = \"$listsplit2asb\";");
						
						$getdsids = substr($dtls['dbid'],strpos($dtls['dbid'],'-')+1);
						$dballsaleitem = $db->fetch_one("SELECT * FROM logassembly WHERE dsid='".$db->clean($getdsids)."'");
						$totalpurchasm = 0;
						$totalpurchasm = $dballsaleitem['price'];
						
						$lists .= '
							<row id="'.$dtls['dbid'].'-as">
								<cell'.$rstextasb.'>'.$ctr.'</cell>
								<cell'.$rstextasb.'>'.date("d-m-Y",$dtls['date']).'</cell>
								<cell'.$rstextasb.'>'.htmlspecialchars($dtls['partno']).'</cell>
								<cell'.$rstextasb.'>'.htmlspecialchars($dtls['stockname']).'</cell>
								<cell'.$rstextasb.'>'.htmlspecialchars($dtls['brandcode']).'</cell>
								<cell'.$rstextasb.'>'.htmlspecialchars($dtls['typecode']).'</cell>
								'.$listsplitasbtext.'
								<cell'.$rstextasb.'>0</cell>
								<cell'.$rstextasb.'>'.$dtls['stockin'].'</cell>
								<cell'.$rstextasb.'>'.$remainings.'</cell>
								<cell'.$rstextasb.'>0</cell>
								<cell'.$rstextasb.'>0</cell>
								<cell'.$rstextasb.'>'.$expdate.'</cell>
								<cell align="'.$align.'"'.$rstextasb.'>'.htmlspecialchars($dtls['invc']).'</cell>
								<cell'.$rstextasb.'>Retur Jual &amp; De-Assembly</cell>
							</row>
						'.$listsplit2asbtext;
					}
					$ctr++;
				}
			}
			
			$lists = '<rows>'.$lists.'</rows>';
			$stocklist = gettemplate('stocklist');
			eval("\$stocklist = \"$stocklist\";");
		}
		echo $stocklist;
		exit;
	}
	else if (!empty($_REQUEST['id'])){
		
		if (empty($useraccess['view_stockhistory'])){
			redirecting('index.php');
		}
		
		$stock->setId($_REQUEST['id']);
		$firststock = $stock->getFirstStock();
		if (sizeof($firststock) == 0){
			header("Location: stock.php");
			exit;
		}
		$brand->setCode($firststock['brandcode']);
		$getbrandname = $brand->getBrandDetail();
		$type->setCode($firststock['typecode']);
		$gettypename = $type->gettypeDetail();
		$location->setCode($firststock['locationcode']);
		$getlocationname = $location->getlocationDetail();
		$stgr->setCode($firststock['stgrcode']);
		$getstgrname = $stgr->getstgrDetail();
		
		$stock->setCode($firststock['stockcode']);
		$stpn = $stock->getAllPartNo();
		$stprn = '';
		if (sizeof($stpn) > 0){
			$stprn = '<select size="5" multiple="multiple" style="width: 300px">';
			foreach ($stpn as $stn){
				$stprn .= '<option>'.$stn['partno'].'</option>';
			}
			$stprn .= '</select>';
		}
		$ctr = 1;
		$assets = 0;
		if ($firststock['assembly'] == 0){
			$dbstockactiveyear = $db->fetch_one("SELECT * FROM stockanually WHERE year='".$general['yearactive']."' AND stockid='".$firststock['stockid']."'");
			if (!empty($dbstockactiveyear['quantity'])){
				$firststock['quantity'] = $dbstockactiveyear['quantity'];
			}
			
			if (sizeof($firststock) > 0){
				$assets += $firststock['quantity'] * $firststock['buyprice'];
			}
			
			$getassemblyprt = $assembly->getAssemblyParent($firststock['stockcode']);
			
			//$startdatetoshow = strtotime("01-01-".$general['yearactivestart']);
			//$enddatetoshow = strtotime("31-12-".$general['yearactiveend']." 23:59:59");
			
			//$detailstock = $stock->getStockDetail($getassemblyprt,$startdatetoshow,$enddatetoshow);
			$detailstock = $stock->getStockDetail($getassemblyprt);
			if (sizeof($detailstock) > 0){
				foreach ($detailstock as $dtls){
					if ($dtls['status'] == 'purchase'){
						$dtls['stockin'] = discq($dtls['stockin']);
						$assets += $dtls['stockin'] * $dtls['buyprice'];
					}
					else if ($dtls['status'] == 'sale'){
						$sale->setDetailId(substr($dtls['dbid'],2));
						$detailsaleitem = $sale->getDetailSaleItem();
						if (sizeof($detailsaleitem) > 0){
							foreach ($detailsaleitem as $dsim){
								if ($dsim['dbid'] == -1){
									$stock->setId("");
									$stock->setCode($dsim['stockcode']);
									$firststocks = $stock->getFirstStock();
									$detailbuyitem['realbuyprice'] = $firststocks['buyprice'];
								}
								else{
									$purchase->setDetailId($dsim['dbid']);
									$detailbuyitem = $purchase->getDetailBuyIndv();
								}
								$dtls['stockout'] = discq($dtls['stockout']);
								$assets -= $dtls['stockout'] * $detailbuyitem['realbuyprice'];
							}
						}
					}
					else if ($dtls['status'] == 'saleasm'){
						$dtls['stockout'] = discq($dtls['stockout']);
						$assets -= $dtls['stockout'] * $dtls['buyprice'];
					}
					else if ($dtls['status'] == 'buyreturn'){
						$purchaser->setDetailId(substr($dtls['dbid'],3));
						$detailpurchaseritem = $purchaser->getDetailBuyRItem();
						if (sizeof($detailpurchaseritem) > 0){
							foreach ($detailpurchaseritem as $dsim){
								if ($dsim['dbid'] == -1){
									$stock->setId("");
									$stock->setCode($dsim['stockcode']);
									$firststocks = $stock->getFirstStock();
									$detailbuyitem['realbuyprice'] = $firststocks['buyprice'];
								}
								else{
									$purchase->setDetailId($dsim['dbid']);
									$detailbuyitem = $purchase->getDetailBuyIndv();
								}
								$dtls['stockout'] = discq($dtls['stockout']);
								$assets -= $dtls['stockout'] * $detailbuyitem['realbuyprice'];
							}
						}
					}
					else if ($dtls['status'] == 'salereturn'){
						$dtls['stockin'] = discq($dtls['stockin']);
						$assets += $dtls['stockin'] * $dtls['buyprice'];
					}
					else if ($dtls['status'] == 'adjustin'){
						$dtls['stockin'] = discq($dtls['stockin']);
						$assets += $dtls['stockin'] * $dtls['buyprice'];
					}
					else if ($dtls['status'] == 'adjustout'){
						$aout->setDetailId(substr($dtls['dbid'],5));
						$detailaoutitem = $aout->getDetailAdjustOutItem();
						if (sizeof($detailaoutitem) > 0){
							foreach ($detailaoutitem as $dsim){
								if ($dsim['dbid'] == -1){
									$stock->setId("");
									$stock->setCode($dsim['stockcode']);
									$firststocks = $stock->getFirstStock();
									$detailbuyitem['realbuyprice'] = $firststocks['buyprice'];
								}
								else{
									$purchase->setDetailId($dsim['dbid']);
									$detailbuyitem = $purchase->getDetailBuyIndv();
								}
								$dtls['stockout'] = discq($dtls['stockout']);
								$assets -= $dtls['stockout'] * $detailbuyitem['realbuyprice'];
							}
						}
					}
				}
				if ($assets < 0){
					$assets = 0;
				}
			}
		}
		else if ($firststock['assembly'] == 2){
			$dballdeas = $db->fetch_all("SELECT * FROM logdeassembly WHERE stockcode='".$db->clean($firststock['stockcode'])."' AND quantity > usedqty");
			if (sizeof($dballdeas) > 0){
				foreach ($dballdeas as $dads){
					$assets += ($dads['quantity'] - $dads['usedqty']) * $dads['price'];
				}
			}
		}
		//$assetsf = number_format($assets,0,",",".");

		$assetsf = $codest->convertcodes(exp_to_dec($assets));
	}

	$allstgrdb = $stgr->getListstgr('partial');
	$allgroups = '';
	if (sizeof($allstgrdb) > 0){
		foreach ($allstgrdb as $astdb){
			$allgroups .= '
				<option value="'.$astdb['stgrcode'].'">'.$astdb['stgrcode'].'</option>
			';
		}
	}
	
	$stocklist = gettemplate('stockdetail');
	eval("\$stocklist = \"$stocklist\";");
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('stock');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>