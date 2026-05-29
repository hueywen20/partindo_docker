<?php
	require_once "global.php";
	
	require_once "class/supplier.php";
	require_once "class/purchase.php";
	$supplier = new supplier();
	$purchase = new Purchase();
	
	if (empty($useraccess['report_unpaidbuy'])){
		redirecting('index.php');
	}
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_POST['printit'] == 'prints'){
		$printtemplate = 'reportunpaidbuy';
		
		$sqls = array();
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			
			array_push($sqls,"buydate >= '".$startdate."' AND buydate <= '".$enddate."'");
		}
		
		$sql = '';
		array_push($sqls,"paid = 0");
		if (sizeof($sqls) > 0){
			$sql = ' WHERE '.implode(' AND ',$sqls);
		}
		
		if (!empty($_POST['suppliercode'])){
			$alldata = $db->fetch_all("SELECT * FROM supplier WHERE suppliercode='".$_POST['suppliercode']."'");
		}
		else{
			$alldata = $supplier->getListsupplier('partial');
		}
		
		$trx = 0;
		$total = 0;		
		if (sizeof($alldata) > 0){
			foreach ($alldata as $adt){
				$allpaydebt = $db->fetch_all("SELECT * FROM headerbuy".$sql." AND suppliercode='".str_replace("'","\'",$adt['suppliercode'])."'");
				$temptrx = 0;
				$temptotal = 0;
				$list = '';
				if (sizeof($allpaydebt) > 0){
					foreach ($allpaydebt as $apd){
						if ($statususer == 1){
							$purchase->setBuyNo($apd['buyno']);
							$alldetailp = $purchase->getDetailBuy();
							if (sizeof($alldetailp) > 0){
								$apd['totalbuy'] = 0;
								foreach ($alldetailp as $adp){
									$adp['quantityf'] = discq($adp['quantityf']);
									$totalbuyfk = $adp['quantityf'] * $adp['buyprice'];
									$totaldiscfk = $adp['disc'] / 100 * $totalbuyfk;
									$adp['totalbuyad'] = $totalbuyfk - $totaldiscfk;
									$apd['totalbuy'] += $adp['totalbuyad'];
								}
								$totalgdiscfk = $apd['disc'] / 100 * $apd['totalbuy'];
								$totalafgdiscfk = $apd['totalbuy'] - $totalgdiscfk;
								$totalgtaxfk = $apd['tax'] / 100 * $totalafgdiscfk;
								$apd['totalbuy'] = intval($totalafgdiscfk + $totalgtaxfk);
							}
						}
						
						$temptrx++;
						$temptotal += $apd['totalbuy'];
						$trx++;
						$total += $apd['totalbuy'];
						
						if ($apd['claims'] == 0){
							$statusbuy = 'Belum Proses';
						}
						else if ($apd['claims'] == 1){
							if ($apd['paid'] == 0){
								$statusbuy = 'Proses';
							}
							else{
								$statusbuy = 'Lunas';
							}
						}
						$list .= '
								<tr>
									<td align="left">'.htmlspecialchars($apd['orderno']).'</td>
									<td align="center">'.date("d-m-Y",$apd['buydate']).'</td>
									<td align="center">'.date("d-m-Y",$apd['duedate']).'</td>
									<td align="right">'.number_format($apd['totalbuy'],0,",",".").'</td>
									<td align="center">'.$statusbuy.'</td>
								</tr>';
					}
										
					$listall .= '
						<div align="right" style="width: 100%; padding-top: 10px">
						<span style="float: left">Supplier : <b>'.htmlspecialchars($adt['suppliercode'].' - '.$adt['suppliername']).'</b></span>
						Tanggal Cetak : '.$printdate.'</div>
						<div align="center" style="width: 100%; padding-bottom: 20px; border-bottom: 1px dotted #000; clear: both">
						<table border="1" cellpadding="3" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="30%" bgcolor="#DEDEDE">NO BON PEMBELIAN</th>
							<th align="center" width="15%" bgcolor="#DEDEDE">TGL PEMBELIAN</th>
							<th align="center" width="15%" bgcolor="#DEDEDE">JATUH TEMPO</th>
							<th align="center" width="25%" bgcolor="#DEDEDE">TOTAL PEMBELIAN</th>
							<th align="center" width="15%" bgcolor="#DEDEDE">STATUS</th>
						</tr>
						'.$list.'
						<tr>
							<td align="center"><b>'.$temptrx.'</b></td>
							<td align="left" colspan="2">&nbsp;<b>TOTAL</b></td>
							<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
							<td align="right" height="30"></td>
						</tr>
						</table></div>
					';
				}
			}
			
			if (!empty($listall)){
				$listall .= '
					<div align="left" style="width: 100%; padding: 10px 0">
					<table border="0" cellpadding="3" cellspacing="0" width="100%">
					<tr>
						<td align="center" width="30%"><b>'.$trx.'</b></td>
						<td align="left" width="30%">&nbsp;<b>GRAND TOTAL</b></td>
						<td align="right" width="25%"><b>'.number_format($total,2,",",".").'</b></td>
						<td align="right" width="15%"></td>
					</tr>
					</table></div>
				';
			}
		}
	}
	else{
		$printtemplate = 'reportunpaidbuyinit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>