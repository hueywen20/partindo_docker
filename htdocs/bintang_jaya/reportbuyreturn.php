<?php
	require_once "global.php";
	
	if (empty($useraccess['report_purchaserpc'])){
		redirecting('index.php');
	}
	
	require_once "class/supplier.php";
	require_once "class/PurchaseR.php";
	$supplier = new supplier();
	$purchaser = new PurchaseR();
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_GET['view'] == 'periodsupplier'){
		if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
			$printtemplate = 'reportbuyreturnpc';
			$startdate = strtotime($_POST['datestart']);
			$enddate = strtotime($_POST['dateend'].' 23:59:59');
			$sql = '';
			if (!empty($_POST['suppliercode'])){
				$sql = " AND suppliercode='".$_POST['suppliercode']."'";
			}
			/*if ($statususer == 1){
				$getalltr = $db->fetch_one("SELECT SUM(totalbuyr) AS totalbuyrs FROM headerbuyr WHERE (buyrdate >= '".$startdate."' AND buyrdate <= '".$enddate."')".$sql." ORDER BY buyrdate");
				$totaltransaction = $getalltr['totalbuyrs'];
				$getsets = (100-$discount['extradisc'])/100 * $totaltransaction;
				
				$getalltr = $db->fetch_all("SELECT * FROM headerbuyr WHERE (buyrdate >= '".$startdate."' AND buyrdate <= '".$enddate."')".$sql." ORDER BY totalbuyr");
				$arrhbid = array();
				if (sizeof($getalltr) > 0){
					$tempforsets = 0;
					foreach ($getalltr as $gatr){
						$tempforsets += $gatr['totalbuyr'];
						if ($tempforsets > $getsets){
							break;
						}
						array_push($arrhbid,$gatr['buyrid']);
					}
					if (sizeof($arrhbid) > 0){
						$dbbuy = $db->fetch_all("SELECT * FROM headerbuyr WHERE buyrid IN (".implode(",",$arrhbid).")".$sql." ORDER BY buyrdate");
					}
				}
			}
			else{*/
				$dbbuy = $db->fetch_all("SELECT * FROM headerbuyr WHERE (buyrdate >= '".$startdate."' AND buyrdate <= '".$enddate."')".$sql." ORDER BY buyrdate");
			//}
			$datego = '';
			$temptrx = 0;
			$tempdisc = 0;
			$temptax = 0;
			$temptotal = 0;
			$trx = 0;
			$disc = 0;
			$tax = 0;
			$total = 0;
			if (sizeof($dbbuy) > 0){
				foreach ($dbbuy as $ds){
					$supplier->setCode($ds['suppliercode']);
					$getsupplier = $supplier->getsupplierDetail();
					
					$datenow = date("d-M-Y",$ds['buyrdate']);
					$datenowdt = date("d-M-Y",$ds['buyrdate']);
					if ($datego != $datenow){
						if ($total != 0){
							$list .= '
								<tr>
									<td align="center"><b>'.$temptrx.'</b></td>
									<td align="left" colspan="2">&nbsp;<b>TOTAL</b></td>
									<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
								</tr>
							';
						}
						$list .= '
							<tr>
								<td align="left" colspan="4" valign="bottom" height="30">
								&nbsp;<b>'.$datenow.'</b></td>
							</tr>
						';
						$temptrx = 0;
						$tempdisc = 0;
						$temptax = 0;
						$temptotal = 0;
						$datego = $datenow;
					}
					$discvalue = ($ds['disc'] / 100) * $ds['totals'];
					$taxvalue = ($ds['tax'] / 100) * ($ds['totals'] - $discvalue);
				
					$listinner = '';
					//get detail purchase return
					$purchaser->setId($ds['buyrid']);
					$dbdetailbuyr = $purchaser->getDetailBuyR();
					$listinner = '';
					if (sizeof($dbdetailbuyr) > 0){
						$listinner .= '
							<tr>
								<td width="10%">&nbsp;</td>
								<td colspan="3" align="left" width="90%" style="border: 0px solid #000">
								<table border="1" cellpadding="0" width="100%" cellspacing="0">
								<tr>
									<th align="center" width="8%" bgcolor="#EFEFEF">No. Bon</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Tgl Faktur</th>
									<th align="center" width="13%" bgcolor="#EFEFEF">Kode</th>
									<th align="center" width="13%" bgcolor="#EFEFEF">Nama</th>
									<th align="center" width="9%" bgcolor="#EFEFEF">Merek</th>
									<th align="center" width="9%" bgcolor="#EFEFEF">Tipe</th>
									<th align="center" width="9%" bgcolor="#EFEFEF">Part No</th>
									<th align="center" width="7%" bgcolor="#EFEFEF">Qty</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Harga</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Diskon</th>
									<th align="center" width="8%" bgcolor="#EFEFEF">Sub Total</th>
								</tr>
						';
						$subtotalfk = 0;
						foreach ($dbdetailbuyr as $dbds){
							if ($statususer == 1){
								$dbds['quantityf'] = floor((100-$discount['extradisc'])/100 * $dbds['quantityf']);
								if ($dbds['quantityf'] < 1){
									$dbds['quantityf'] = 1;
								}
								
								$totalbuyfk = $dbds['quantityf'] * $dbds['buyrprice'];
								$totaldiscfk = $dbds['disc'] / 100 * $totalbuyfk;
								$tempstd = $totalbuyfk - $totaldiscfk;
								$tempstd = $tempstd - $dbds['extdisc'] / 100 * $tempstd;
								$tempstd = $tempstd + $dbds['tax'] / 100 * $tempstd;
								$subtotalfk += $tempstd;								
								$dbds['totalbuyrad'] = $tempstd;
							}
							
							//get buy no							
							$purchaser->setDetailId($dbds['dbrid']);
							$getdbid = $purchaser->getDetailBuyRItem();
							$idrows = '';
							if (sizeof($getdbid) > 0){
								foreach ($getdbid as $gdbid){
									$idrows = $gdbid['dbid'];
									$getdbdetail = $db->fetch_one("SELECT hb.orderno, hb.buydate FROM headerbuy hb INNER JOIN detailbuy dby ON dby.buyno = hb.buyno WHERE dby.dbid='".$idrows."'");
									break;
								}
							}
							
							$listinner .= '
								<tr>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($getdbdetail['orderno']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.date("d-M-Y",$getdbdetail['buydate']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['stockname']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['brandcode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['typecode']).'</td>
									<td align="left" height="30" class="detailitem" bgcolor="#EFEFEF">'.htmlspecialchars($dbds['partno']).'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['quantityf'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['buyrprice'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['disc'],2,",",".").'</td>
									<td align="right" height="30" class="detailitem" bgcolor="#EFEFEF">'.number_format($dbds['totalbuyrad'],2,",",".").'</td>
								</tr>
							';
						}
						
						$listinner .= '
								</table></td>
							</tr>
						';
					}
				
					if ($statususer == 1){
						$discvalue = $ds['disc'] / 100 * $subtotalfk;
						$totalafgdiscfk = $subtotalfk - $discvalue;
						$taxvalue = $ds['tax'] / 100 * $totalafgdiscfk;
						$ds['totalbuyr'] = $totalafgdiscfk + $taxvalue;
					}
					
					$list .= '
						<tr>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($ds['buyrid']).'</td>
							<td align="center" height="30" class="detailitem">'.$datenowdt.'</td>
							<td align="left" height="30" class="detailitem">'.htmlspecialchars($getsupplier['suppliername']).'</td>
							<td align="right" height="30" class="detailitem">'.number_format($ds['totalbuyr'],2,",",".").'</td>
						</tr>
					'.$listinner;
					$temptrx++;
					$tempdisc += $discvalue;
					$temptax += $taxvalue;
					$temptotal += $ds['totalbuyr'];
					$trx++;
					$disc += $discvalue;
					$tax += $taxvalue;
					$total += $ds['totalbuyr'];
				}
				$list .= '
					<tr>
						<td align="center"><b>'.$temptrx.'</b></td>
						<td align="left" colspan="2">&nbsp;<b>TOTAL</b></td>
						<td align="right" height="30"><b>'.number_format($temptotal,2,",",".").'</b></td>
					</tr>
					<tr>
						<td align="center"><b>'.$trx.'</b></td>
						<td align="left" colspan="2">&nbsp;<b>GRAND TOTAL</b></td>
						<td align="right" height="30"><b>'.number_format($total,2,",",".").'</b></td>
					</tr>
				';
			}
		}
		else{
			$printtemplate = 'reportbuyreturnpcinit';
		}
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>