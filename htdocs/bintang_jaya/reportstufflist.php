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
	
	if (empty($useraccess['report_stufflist'])){
		redirecting('index.php');
	}
	
	$printdate = date("d-M-Y / H:i:s");
	if ($_POST['printit'] == 'prints'){
		$printtemplate = 'reportstufflist';
		$stockcodestart = trim($_POST['stockcodestart']);
		$stockcodeend = trim($_POST['stockcodeend']);
		$generalname = trim($_POST['generalname']);
		$brandname = trim($_POST['brandname']);
		$typename = trim($_POST['typename']);
		$size = trim($_POST['size']);
		$locationname = trim($_POST['locationname']);
		$realremaining = trim($_POST['realremaining']);
		$unitname = trim($_POST['unitname']);
		$buyminprice = trim($_POST['buyminprice']);
		$buymaxprice = trim($_POST['buymaxprice']);
		$minexpdate = trim($_POST['minexpdate']);
		$partno = trim($_POST['partno']);
		$stockgroup = trim($_POST['stockgroup']);
		
		$keywords = array();
		$fields = array();
		if (!empty($stockcodestart) && !empty($stockcodeend)){
			array_push($fields,'stockcoderange');
			
			$strlength = strlen($stockcodeend);
			$lastchar = substr($stockcodeend,-1);
			$beforelastchar = substr($stockcodeend,0,$strlength - 1);
			$asciinumber = ord($lastchar);
			if (($asciinumber >= 65 && $asciinumber <= 89) || ($asciinumber >= 97 && $asciinumber <= 121)){
				if ($strlength == 1){
					$stockcodeend = chr($asciinumber + 1);
				}
				else{
					$stockcodeend = $beforelastchar.chr($asciinumber + 1);
				}
				array_push($keywords,'s.stockcode >= \''.$db->clean($stockcodestart).'\' AND s.stockcode < \''.$db->clean($stockcodeend).'\'');
			}
			else if ($asciinumber == 90 || $asciinumber == 122){
				if ($strlength == 1){
					array_push($keywords,'s.stockcode >= \''.$db->clean($stockcodestart).'\'');
				}
				else{
					$strlengthi = $strlength;
					$beforelastchari = $stockcodeend;
					while ($strlengthi > 1){
						$strlengthi = strlen($beforelastchari);
						$lastchari = substr($beforelastchari,-1);
						$beforelastchari = substr($beforelastchari,0,$strlengthi - 1);
						
						$strlengthi = strlen($beforelastchari);
						
						$asciinumber = ord($lastchari);
						if (($asciinumber >= 65 && $asciinumber <= 89) || ($asciinumber >= 97 && $asciinumber <= 121)){
							$stockcodeend = $beforelastchari.chr($asciinumber + 1);
							array_push($keywords,'s.stockcode BETWEEN \''.$db->clean($stockcodestart).'\' AND \''.$db->clean($stockcodeend).'\'');
							break;
						}
						else if ($asciinumber == 90 || $asciinumber == 122){
							if ($strlengthi == 1){
								$asciinumber = ord($beforelastchari);
								if (($asciinumber >= 65 && $asciinumber <= 89) || ($asciinumber >= 97 && $asciinumber <= 121)){
									$stockcodeend = chr($asciinumber + 1);
									array_push($keywords,'s.stockcode BETWEEN \''.$db->clean($stockcodestart).'\' AND \''.$db->clean($stockcodeend).'\'');
								}
								else if ($asciinumber == 90 || $asciinumber == 122){
									array_push($keywords,'s.stockcode >= \''.$db->clean($stockcodestart).'\'');
								}
								else{
									$stockcodeend = $beforelastchari;
									array_push($keywords,'s.stockcode BETWEEN \''.$db->clean($stockcodestart).'\' AND \''.$db->clean($stockcodeend).'\'');
								}
							}
							continue;
						}
						else{
							$stockcodeend = $beforelastchari.$lastchari;
							array_push($keywords,'s.stockcode BETWEEN \''.$db->clean($stockcodestart).'\' AND \''.$db->clean($stockcodeend).'\'');
							break;
						}
					}
				}
			}
			else{
				array_push($keywords,'s.stockcode BETWEEN \''.$db->clean($stockcodestart).'\' AND \''.$db->clean($stockcodeend).'\'');
			}
		}
		else if (!empty($stockcodestart)){
			array_push($fields,'stockcode');
			array_push($keywords,$stockcodestart);
		}
		else if (!empty($stockcodeend)){
			array_push($fields,'stockcode');
			array_push($keywords,$stockcodeend);
		}
		if (!empty($generalname)){
			array_push($fields,'generalname');
			array_push($keywords,$generalname);
		}
		if (!empty($brandname)){
			array_push($fields,'brandname');
			array_push($keywords,$brandname);
		}
		if (!empty($typename)){
			array_push($fields,'typename');
			array_push($keywords,$typename);
		}
		if (!empty($size)){
			array_push($fields,'size');
			array_push($keywords,$size);
		}
		if (!empty($locationname)){
			array_push($fields,'locationname');
			array_push($keywords,$locationname);
		}
		if (!empty($realremaining)){
			array_push($fields,'realremaining');
			array_push($keywords,$realremaining);
		}
		if (!empty($unitname)){
			array_push($fields,'unitname');
			array_push($keywords,$unitname);
		}
		if (!empty($buyminprice)){
			array_push($fields,'buyminprice');
			array_push($keywords,$buyminprice);
		}
		if (!empty($buymaxprice)){
			array_push($fields,'buymaxprice');
			array_push($keywords,$buymaxprice);
		}
		if (!empty($minexpdate)){
			array_push($fields,'minexpdate');
			array_push($keywords,$minexpdate);
		}
		if (!empty($partno)){
			array_push($fields,'partno');
			array_push($keywords,$partno);
		}
		if (!empty($stockgroup)){
			array_push($fields,'stockgroup');
			array_push($keywords,$stockgroup);
		}

		$limits = -1;
		$ctr = 1;
		if (!empty($_POST['startlimit']) && !empty($_POST['manys'])){
			if (ctype_digit($_POST['startlimit']) && ctype_digit($_POST['manys'])){
				$limits = ($_POST['startlimit'] - 1).','.$_POST['manys'];
				$ctr = $_POST['startlimit'];
			}
		}
		
		if (sizeof($keywords) > 0){
			$liststock = $stock->searchStock($keywords,$fields,'data',-1,-1,-1,$limits);
		}
		else{
			$liststock = $stock->getListStock(-1,-1,$limits);
		}
				
		$lists = '';
		$listthediv = '';
		$heightdiv = '1220px';
		$heightlimit = 1150;
		$onerowheight = 12;
		$padtb = 6;
		$countrow = 0;
				
		$ctrwrap = 3;
		$codewrap = 11;
		$namewrap = 30;
		$brandwrap = 10;
		$typewrap = 10;
		$sizewrap = 8;
		$locwrap = 7;
		$remainunitwrap = 7;
		$minmaxwrap = 10;
		$expwrap = 5;
		
		if (sizeof($liststock) > 0){
			foreach ($liststock as $list){
				$listsplit = '';
				$listsplit2 = '';
				$rstext = '';
				$stock->setCode($list['stockcode']);
				$allpartno = $stock->getAllPartNo();
				$splits = sizeof($allpartno);
				
				$heightparts = 0;
				
				if ($splits > 0){
					if ($splits > 1){
						$rstext = ' rowspan="'.$splits.'"';
					}
					$io = 0;
					$partrow = 0;
					$partwrap = 15;
					foreach ($allpartno as $apn){
						$partnowrap = wordwrap($apn['partno'],$partwrap,"<br>",true);
						if ($io == 0){
							$listsplit .= '<td align="left">'.$partnowrap.'</td>';
						}
						else{
							$listsplit2 .= '
								<tr>
									<td align="left">'.$partnowrap.'</td>
								</tr>
							';
						}
						$io++;
						
						$partrow += substr_count($partnowrap,"<br>") + 1;
					}
					$heightparts = ($partrow * $onerowheight) + ($padtb * $partrow);
				}
				else{
					$listsplit .= '<td align="left"></td>';
					$partrow = 1;
				}
				
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
					$mexdate = date("d-m-Y",$list['minexpdate']);
				}
				
				if ($statususer == 1){
					//get purchase
					$dbpurchase['q'] = 0;
					$dbpurch = $db->fetch_all("SELECT * FROM detailbuy WHERE stockcode='".$list['stockcode']."'");
					if (sizeof($dbpurch) > 0){
						foreach ($dbpurch as $dpch){
							$dpch['qty'] = floor((100-$discount['extradisc'])/100 * $dpch['quantity']);
							$dbpurchase['q'] += $dpch['qty'];
						}
					}
					//get sale
					$dbsale['q'] = 0;
					$dbfa = $db->fetch_all("SELECT * FROM detailsale WHERE stockcode='".$list['stockcode']."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							$dbsale['q'] += $rsfa['qty'];
						}
					}
					//get purchase return
					$dbpurchaser['q'] = 0;
					$dbfa = $db->fetch_all("SELECT * FROM detailbuyr WHERE stockcode='".$list['stockcode']."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							$dbpurchaser['q'] += $rsfa['qty'];
						}
					}
					//get sale return
					$dbsaler['q'] = 0;
					$dbfa = $db->fetch_all("SELECT * FROM detailsaler WHERE stockcode='".$list['stockcode']."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							$dbsaler['q'] += $rsfa['qty'];
						}
					}
					//get adjust in
					$dbain['q'] = 0;
					$dbfa = $db->fetch_all("SELECT * FROM detailadjustin WHERE stockcode='".$list['stockcode']."'");
					if (sizeof($dbfa) > 0){
						foreach ($dbfa as $rsfa){
							$rsfa['qty'] = floor((100-$discount['extradisc'])/100 * $rsfa['quantity']);
							$dbain['q'] += $rsfa['qty'];
						}
					}
					//get adjust out
					$dbaout['q'] = 0;
					$dbfa = $db->fetch_all("SELECT * FROM detailadjustout WHERE stockcode='".$list['stockcode']."'");
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
							$idparentasm .= ',\''.$gasbp['stockcode'].'\'';
							$assemblyscq[$gasbp['stockcode']] = $gasbp['sccquantity'];
							
							$gettotalqty = $db->fetch_one("SELECT SUM(sccquantity) AS totalq FROM detailstockassembly WHERE stockcode='".$gasbp['stockcode']."'");
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
					$dbfa = $db->fetch_all("SELECT * FROM logdeassembly WHERE stockcode='".$list['stockcode']."'");
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
				
				$ctrtext = number_format($ctr,0,",",".");
				
				$counterwrap = wordwrap($ctrtext,$ctrwrap,"<br>",true);
				$stockcodewrap = wordwrap($list['stockcode'],$codewrap,"<br>",true);
				$generalnamewrap = wordwrap($list['generalname'],$namewrap,"<br>",true);
				$brandcodewrap = wordwrap($list['brandcode'],$brandwrap,"<br>",true);
				$typecodewrap = wordwrap($list['typecode'],$typewrap,"<br>",true);
				$sizeswrap = wordwrap($list['size'],$sizewrap,"<br>",true);
				$locationwrap = wordwrap($list['locationcode'],$locwrap,"<br>",true);
				
				$ctrrow = substr_count($counterwrap,"<br>") + 1;
				$coderow = substr_count($stockcodewrap,"<br>") + 1;
				$namerow = substr_count($generalnamewrap,"<br>") + 1;
				$brandrow = substr_count($brandcodewrap,"<br>") + 1;
				$typerow = substr_count($typecodewrap,"<br>") + 1;
				$sizerow = substr_count($sizeswrap,"<br>") + 1;
				$locrow = substr_count($locationwrap,"<br>") + 1;
				
				$remaintext = number_format($stocknow,0,",",".");
				$remainwrap = wordwrap($remaintext,$remainunitwrap,"<br>",true);
				$unitwrap = wordwrap($getlowunit['lunit'],$remainunitwrap,"<br>",true);
				
				$remainrow = substr_count($remainwrap,"<br>") + 1 + substr_count($unitwrap,"<br>") + 1;
				
				$mintext = number_format($list['buyminprice'],0,",",".");
				$maxtext = number_format($list['buymaxprice'],0,",",".");
				$minwrap = wordwrap($mintext,$minmaxwrap,"<br>",true);
				$maxwrap = wordwrap($maxtext,$minmaxwrap,"<br>",true);
				
				$minmaxrow = substr_count($minwrap,"<br>") + 1 + substr_count($maxwrap,"<br>") + 1;
				
				$expiredwrap = wordwrap($mexdate,$expwrap,"<br>",true);
				$exprow = substr_count($expiredwrap,"<br>") + 1;
				
				$thismaxrow = max($ctrrow,$coderow,$namerow,$brandrow,$typerow,$sizerow,$locrow,$remainrow,$minmaxrow,$exprow,$partrow);
				$thismaxrowpx = ($thismaxrow * $onerowheight) + $padtb;
				if ($heightparts > $thismaxrowpx){
					$thismaxrowpx = $heightparts;
				}
				
				if (($countrow + $thismaxrowpx) > $heightlimit){
					$listthediv .= '
						<div style="height: '.$heightdiv.'" align="center">
							<div align="center" class="reporttitle" style="width: 100%">LAPORAN DAFTAR BARANG</div><br>
							<div align="right" style="width: 100%">Tanggal Cetak : '.$printdate.'</div>
							<table border="1" cellpadding="2" cellspacing="0" width="100%">
							<tr>
								<th align="center" bgcolor="#DEDEDE">NO</th>
								<th align="center" bgcolor="#DEDEDE" width="10%">KODE<br />BARANG</th>
								<th align="center" bgcolor="#DEDEDE" width="20%">NAMA<br />UMUM</th>
								<th align="center" bgcolor="#DEDEDE">MEREK</th>
								<th align="center" bgcolor="#DEDEDE">TIPE</th>
								<th align="center" bgcolor="#DEDEDE">UKURAN</th>
								<th align="center" bgcolor="#DEDEDE">LKS</th>
								<th align="center" bgcolor="#DEDEDE">SISA</th>
								<th align="center" bgcolor="#DEDEDE">MODAL<br />MIN / MAX</th>
								<th align="center" bgcolor="#DEDEDE">EXP DATE</th>
								<th align="center" bgcolor="#DEDEDE">NO<br />PART</th>
							</tr>
							'.$lists.'
							</table>
						</div>
					';
					$lists = '';
					$countrow = $thismaxrowpx;
				}
				else{
					$countrow += $thismaxrowpx;
				}
				
				$lists .= '
						<tr>
							<td align="right"'.$rstext.'>'.$counterwrap.'</td>
							<td align="left"'.$rstext.'>'.$stockcodewrap.'</td>
							<td align="left"'.$rstext.'>'.$generalnamewrap.'</td>
							<td align="left"'.$rstext.'>'.$brandcodewrap.'</td>
							<td align="left"'.$rstext.'>'.$typecodewrap.'</td>
							<td align="left"'.$rstext.'>'.$sizeswrap.'</td>
							<td align="center"'.$rstext.'>'.$locationwrap.'</td>
							<td align="right"'.$rstext.'>'.$remainwrap.'<br />'.$unitwrap.'</td>
							<td align="right"'.$rstext.'><div align="left">'.$minwrap.'</div><div align="right">'.$maxwrap.'</div></td>
							<td align="center"'.$rstext.'>'.$expiredwrap.'</td>
							'.$listsplit.'
						</tr>'.$listsplit2.'
				';
				
				$ctr++;
			}
			$listthediv .= '
				<div style="height: '.$heightdiv.'" align="center">
					<div align="center" class="reporttitle" style="width: 100%">LAPORAN DAFTAR BARANG</div><br>
					<div align="right" style="width: 100%">Tanggal Cetak : '.$printdate.'</div>
					<table border="1" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<th align="center" bgcolor="#DEDEDE">NO</th>
						<th align="center" bgcolor="#DEDEDE" width="10%">KODE<br />BARANG</th>
						<th align="center" bgcolor="#DEDEDE" width="20%">NAMA UMUM</th>
						<th align="center" bgcolor="#DEDEDE">MEREK</th>
						<th align="center" bgcolor="#DEDEDE">TIPE</th>
						<th align="center" bgcolor="#DEDEDE">UKURAN</th>
						<th align="center" bgcolor="#DEDEDE">LOKASI</th>
						<th align="center" bgcolor="#DEDEDE">SISA</th>
						<th align="center" bgcolor="#DEDEDE">MODAL<br />MIN / MAX</th>
						<th align="center" bgcolor="#DEDEDE">EXP DATE</th>
						<th align="center" bgcolor="#DEDEDE">NO PART</th>
					</tr>
					'.$lists.'
					</table>
				</div>
			';
		}
	}
	else{
		$printtemplate = 'reportstufflistinit';

		$allstgrdb = $stgr->getListstgr('partial');
		$allgroups = '';
		if (sizeof($allstgrdb) > 0){
			foreach ($allstgrdb as $astdb){
				$allgroups .= '
					<option value="'.$astdb['stgrcode'].'">'.$astdb['stgrcode'].'</option>
				';
			}
		}
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>