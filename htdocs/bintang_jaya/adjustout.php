<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	require_once "class/area.php";
	require_once "class/AdjustOut.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	$area = new area();
	$aout = new AdjustOut();
	
	$headeraout['totalaout'] = 0;
	$aoutdate = date("d-m-Y");
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['id'])){
				$aout->setId($_GET['id']);
				$allaout = $aout->getDetailAdjustOut();
				if (sizeof($allaout) > 0){
					foreach ($allaout as $ap){
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
						
						if ($statususer == 1){
							$ap['quantityf'] = floor((100-$discount['extradisc'])/100 * $ap['quantityf']);
							if ($ap['quantityf'] < 1){
								$ap['quantityf'] = 1;
							}
							$totalaoutfk = $ap['quantityf'] * $ap['aoutprice'];
							$totaldiscfk = $ap['disc'] / 100 * $totalaoutfk;
							$ap['totalaoutprice'] = $totalaoutfk - $totaldiscfk;
						}
						
						$lists .= '
							<row id="r-'.$ap['daoutid'].'">
								<cell>'.htmlspecialchars($ap['stockcode']).'</cell>
								<cell>'.htmlspecialchars($ap['partno']).'</cell>
								<cell>'.htmlspecialchars($ap['stockname']).'</cell>
								<cell>'.htmlspecialchars($ap['brandcode']).'</cell>
								<cell>'.htmlspecialchars($ap['typecode']).'</cell>
								<cell>'.number_format($ap['quantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['unitquantityf']).'</cell>
								<cell>'.number_format($ap['aoutprice'],2,",",".").'</cell>
								<cell>'.number_format($ap['totalaoutprice'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['description']).'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('adjustoutdetaillist');
		}
		else if ($_GET['list'] == 'general'){
			/*$listaout = $aout->getListAdjustOut('all');
			$lists = '';
			if (sizeof($listaout) > 0){
				foreach ($listaout as $list){					
					$lists .= '
						<row id="'.$list['aoutid'].'">
							<cell>'.htmlspecialchars($list['aoutid']).'</cell>
							<cell>'.date("d-m-Y",$list['aoutdate']).'</cell>
							<cell>'.$list['totalaout'].'</cell>
							<cell>'.$list['description'].'</cell>
							<cell>Ubah^adjustout.php?id='.$list['aoutid'].'^_self</cell>
							<cell>Hapus^javascript:deleteitem("adjustout.php?do=delete&amp;id='.$list['aoutid'].'")^_self</cell>
						</row>
					';
				}
			}*/
			/* if (isset($_GET['keyword'])){
				if ($_GET['keyword'] != ''){ */
					$allaout = $aout->searchAdjustOut($_GET['keyword'],$_GET['field']);
					$totalrows = sizeof($allaout);
					$totalpgs = ceil($totalrows / $general['showperpage']);
					$pgs = handlepage($_GET['p'],$totalpgs);
					
					$listaout = $aout->searchAdjustOut($_GET['keyword'],$_GET['field'],$pgs);
				/* }
			} */
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listaout) > 0){
				$ctr = 1;
				foreach ($listaout as $list){
					$aout->setId($list['aoutid']);
				
					$getdetailaout = $aout->getDetailAdjustOut();
					$splits = sizeof($getdetailaout);
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0;
						$subtotalfk = 0;
						foreach ($getdetailaout as $gdb){
							$gdb['typecode'] = wordwrap($gdb['typecode'],15,"<br>",true);
							$gdb['brandcode'] = wordwrap($gdb['brandcode'],10,"<br>",true);
							
							$gdb['typecode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['typecode']));
							$gdb['brandcode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['brandcode']));
							
							if ($statususer == 1){
								$gdb['quantityf'] = floor((100-$discount['extradisc'])/100 * $gdb['quantityf']);
								if ($gdb['quantityf'] < 1){
									$gdb['quantityf'] = 1;
								}
								
								$totalaoutfk = $gdb['quantityf'] * $gdb['aoutprice'];
								$totaldiscfk = $gdb['disc'] / 100 * $totalaoutfk;
								$subtotalfk += ($totalaoutfk - $totaldiscfk);
							}

							if ($io == 0){
								$listsplit .= '
									<td width="'.$cwarr[2].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
									<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
									<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
									<td width="'.$cwarr[5].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
									<td width="'.$cwarr[6].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
									<td width="'.$cwarr[7].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
									<td width="'.$cwarr[8].'" class="stufflist" align="left">'.htmlspecialchars($gdb['unitquantityf']).'</td>
									<td width="'.$cwarr[9].'" class="stufflist" id="aoutprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['aoutprice']).'</td>
								';
							}
							else{
								$listsplit2 .= '
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'adjustout.php?id='.$list['aoutid'].'\',\'_self\')">
										<td width="'.$cwarr[2].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
										<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
										<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
										<td width="'.$cwarr[5].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
										<td width="'.$cwarr[6].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
										<td width="'.$cwarr[7].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
										<td width="'.$cwarr[8].'" class="stufflist" align="left">'.htmlspecialchars($gdb['unitquantityf']).'</td>
										<td width="'.$cwarr[9].'" class="stufflist" id="aoutprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['aoutprice']).'</td>
									</tr>
								';
							}
							$io++;
						}
					}
					else{
						$listsplit .= '
							<td width="'.$cwarr[2].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[3].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[4].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[5].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[6].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[7].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[8].'" class="stufflist" align="left"></td>
							<td width="'.$cwarr[9].'" class="stufflist" align="right"></td>
						';
					}

					$actioneditwidth = floor(51 / 100 * $cwarr[11]);
					$actiondeletewidth = $cwarr[11]-$actioneditwidth-3;
					
					if ($statususer == 1){
						$totalgdiscfk = $list['disc'] / 100 * $subtotalfk;
						$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
						$totalgtaxfk = $list['tax'] / 100 * $totalafgdiscfk;
						$list['totalaout'] = $totalafgdiscfk + $totalgtaxfk;
					}

					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'adjustout.php?id='.$list['aoutid'].'\',\'_self\')">
								<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.htmlspecialchars($list['aoutid']).'</td>
								<td class="stufflist" width="'.$cwarr[1].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['aoutdate']).'</td>
								'.$listsplit.'
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[10].'" align="right"'.$rstext.'>'.$codest->convertcodes($list['totalaout']).'</td>
								<td class="stufflist bgseparator" width="'.$cwarr[11].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
								(($useraccess['edit_adjustout'])?'<a href="adjustout.php?id='.$list['aoutid'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
								(($useraccess['delete_adjustout'])?'<a href="javascript:deleteitem(\'adjustout.php?do=delete&id='.$list['aoutid'].'\')">Hapus</a>':'-').'</span></td>
							</tr>'.$listsplit2.'
					';					
					
					$ctr++;
				}
			}
			$ctrgo = $ctr-1;
			/* $pclist = gettemplate('adjustoutlistdetail'); */
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
		eval("\$pclist = \"$pclist\";");
		echo $pclist;
		exit;
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_adjustout']){
		$db->beginTransaction();
		$aoutdate = strtotime($_POST['aoutdate']);
		$lastid = $aout->saveHeaderAdjustOut($aoutdate,$_POST['description'],$_POST['totalaout'],$userid);
		
		$aout->setId($lastid);
		
		$arrpostdel = explode(",",$_POST['detailaoutbox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailaoutbox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["detailaoutbox_".$arrpost[$x]."_0"])){
					$checkchars = strpos($_POST["detailaoutbox_".$arrpost[$x]."_0"],"||");
					if ($checkchars !== false){
						$_POST["detailaoutbox_".$arrpost[$x]."_0"] = substr($_POST["detailaoutbox_".$arrpost[$x]."_0"],0,$checkchars);
					}

					$stock->setId("");
					$stock->setCode($_POST["detailaoutbox_".$arrpost[$x]."_0"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["detailaoutbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_5"],'calculate');
					$_POST["detailaoutbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_7"],'calculate');
					$_POST["detailaoutbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_8"],'calculate');

					if ($_POST["detailaoutbox_".$arrpost[$x]."_6"] == $getunit['funit']){
						$quantity = $_POST["detailaoutbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
						$realaoutprice = $_POST["detailaoutbox_".$arrpost[$x]."_7"] / $getunit['cvalue'];
					}
					else{
						$quantity = $_POST["detailaoutbox_".$arrpost[$x]."_5"];
						$_POST["detailaoutbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
						$realaoutprice = $_POST["detailaoutbox_".$arrpost[$x]."_7"];
					}
					
					$aout->saveDetailAdjustOut($_POST["detailaoutbox_".$arrpost[$x]."_0"],$_POST["detailaoutbox_".$arrpost[$x]."_1"],$_POST["detailaoutbox_".$arrpost[$x]."_2"],$_POST["detailaoutbox_".$arrpost[$x]."_3"],$_POST["detailaoutbox_".$arrpost[$x]."_4"],$_POST["detailaoutbox_".$arrpost[$x]."_5"],$_POST["detailaoutbox_".$arrpost[$x]."_6"],$_POST["detailaoutbox_".$arrpost[$x]."_7"],$_POST["detailaoutbox_".$arrpost[$x]."_8"],$_POST["detailaoutbox_".$arrpost[$x]."_9"],$aoutdate,$realaoutprice,$quantity,$unitquantity,$getstock['unitcode']);
				}
			}
		}
		$db->endTransaction();
		redirecting("adjustout.php?id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_adjustout']){
		//print_r($_POST);
		if (!empty($_POST['id'])){
			$aout->setId($_POST['id']);
			
			$db->beginTransaction();
			$aoutdate = strtotime($_POST['aoutdate']);
			$aout->updateHeaderAdjustOut($aoutdate,$_POST['description'],$_POST['totalaout'],$userid);
			
			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailaoutbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$aout->setDetailId($arrpostdel[$x]);
						$olddetail = $aout->getDetailAdjustOutIndv();
						$stock->setCode($olddetail['stockcode']);
						$aout->deleteDetailAOutItem($olddetail['stockcode']);
						$db->query("DELETE FROM detailadjustout WHERE daoutid='".$aout->dtid."'");
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
					if (!empty($arrpost[$x])){
						$checkchars = strpos($_POST["detailaoutbox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailaoutbox_".$arrpost[$x]."_0"] = substr($_POST["detailaoutbox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$aout->setDetailId($arrpost[$x]);
						$olddetail = $aout->getDetailAdjustOutIndv();
						
						$units->setCode($olddetail['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailaoutbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailaoutbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailaoutbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_8"],'calculate');

						if ($_POST["detailaoutbox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailaoutbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realaoutprice = $_POST["detailaoutbox_".$arrpost[$x]."_7"] / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailaoutbox_".$arrpost[$x]."_5"];
							$_POST["detailaoutbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
							$realaoutprice = $_POST["detailaoutbox_".$arrpost[$x]."_7"];
						}
						
						$aout->updateDetailAdjustOut($_POST["detailaoutbox_".$arrpost[$x]."_0"],$_POST["detailaoutbox_".$arrpost[$x]."_1"],$_POST["detailaoutbox_".$arrpost[$x]."_2"],$_POST["detailaoutbox_".$arrpost[$x]."_3"],$_POST["detailaoutbox_".$arrpost[$x]."_4"],$_POST["detailaoutbox_".$arrpost[$x]."_5"],$_POST["detailaoutbox_".$arrpost[$x]."_6"],$_POST["detailaoutbox_".$arrpost[$x]."_7"],$_POST["detailaoutbox_".$arrpost[$x]."_8"],$_POST["detailaoutbox_".$arrpost[$x]."_9"],$aoutdate,$realaoutprice,$quantity,$unitquantity,$getstock['unitcode'],$olddetail);
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailaoutbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailaoutbox_".$arrpost[$x]."_0"])){
						$checkchars = strpos($_POST["detailaoutbox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailaoutbox_".$arrpost[$x]."_0"] = substr($_POST["detailaoutbox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["detailaoutbox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailaoutbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailaoutbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailaoutbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailaoutbox_".$arrpost[$x]."_8"],'calculate');

						if ($_POST["detailaoutbox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailaoutbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realaoutprice = $_POST["detailaoutbox_".$arrpost[$x]."_7"] / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailaoutbox_".$arrpost[$x]."_5"];
							$_POST["detailaoutbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
							$realaoutprice = $_POST["detailaoutbox_".$arrpost[$x]."_7"];
						}
						
						$aout->saveDetailAdjustOut($_POST["detailaoutbox_".$arrpost[$x]."_0"],$_POST["detailaoutbox_".$arrpost[$x]."_1"],$_POST["detailaoutbox_".$arrpost[$x]."_2"],$_POST["detailaoutbox_".$arrpost[$x]."_3"],$_POST["detailaoutbox_".$arrpost[$x]."_4"],$_POST["detailaoutbox_".$arrpost[$x]."_5"],$_POST["detailaoutbox_".$arrpost[$x]."_6"],$_POST["detailaoutbox_".$arrpost[$x]."_7"],$_POST["detailaoutbox_".$arrpost[$x]."_8"],$_POST["detailaoutbox_".$arrpost[$x]."_9"],$aoutdate,$realaoutprice,$quantity,$unitquantity,$getstock['unitcode']);
					}
				}
			}
			$db->endTransaction();
		}
		redirecting("adjustout.php?id=".$_POST['id']);
	}

	$aoutid = $_GET['id'];

	if ($_GET['do'] == 'delete' && !empty($aoutid) && $useraccess['delete_adjustout']){
		$db->beginTransaction();
		
		$aout->setId($aoutid);
		$headeraout = $aout->getHeaderAdjustOut();
		
		$aout->deleteAdjustOut();
		
		$db->endTransaction();
		redirecting("adjustout.php?screen=list");		
	}

	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_adjustout'])){
			redirecting('index.php');
		}
		
		$printtemplate = 'adjustoutlist';
	}
	else{
		
		if (empty($useraccess['add_adjustout']) && empty($useraccess['edit_adjustout'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'adjustout';
		if (!empty($aoutid)){
			$aout->setId($aoutid);
			$headeraout = $aout->getHeaderAdjustOut();
			
			if (empty($headeraout['aoutid'])){
				redirecting('adjustout.php?screen=list');
			}
			
			$aoutdate = date("d-m-Y",$headeraout['aoutdate']);
						
			$ftotal = number_format($headeraout['totalaout'],2,",",".");
			
			$alldtl = $aout->getDetailAdjustOut();
			$alldetailid = '';
			$subtotalfk = 0;
			if (sizeof($alldtl) > 0){
				foreach ($alldtl as $aad){
					$stock->setId("");
					$stock->setCode($aad['stockcode']);
					$getds = $stock->getFirstStock();
					
					$alldetailid .= ',r-'.$aad['daoutid'];
					$alldetailidjs .= ',"r-'.$aad['daoutid'].'"';
					$allidusedqty .= ','.($detailsale['quantity'] - $detailsale['usedqty']);
					
					$units->setCode($getds['unitcode']);
					$getunit = $units->getunitDetail();
					$arrunits .= '
						arrunits["r-'.$aad['daoutid'].'"] = new Array();
						arrunits["r-'.$aad['daoutid'].'"][0] = "'.$getunit['funit'].'";
						arrunits["r-'.$aad['daoutid'].'"][1] = "'.$getunit['lunit'].'";';
					$arrconversion .= '
						arrconversion["r-'.$aad['daoutid'].'"] = new Array();
						arrconversion["r-'.$aad['daoutid'].'"][0] = '.$getunit['cvalue'].';
						arrconversion["r-'.$aad['daoutid'].'"][1] = 1;';
				
					$aad['quantityf'] = floor((100-$discount['extradisc'])/100 * $aad['quantityf']);
					if ($aad['quantityf'] < 1){
						$aad['quantityf'] = 1;
					}
						
					$totalaoutfk = $aad['quantityf'] * $aad['aoutprice'];
					$totaldiscfk = $aad['disc'] / 100 * $totalaoutfk;
					$subtotalfk += ($totalaoutfk - $totaldiscfk);
				}
				$alldetailid = substr($alldetailid,1);
				$alldetailidjs = substr($alldetailidjs,1);
				$allidusedqty = substr($allidusedqty,1);
			}
			
			if ($statususer == 1){
				$headeraout['totals'] = $subtotalfk;
				$totalgdiscfk = $headeraout['disc'] / 100 * $subtotalfk;
				$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
				$totalgtaxfk = $headeraout['tax'] / 100 * $totalafgdiscfk;
				$headeraout['totalaout'] = $totalafgdiscfk + $totalgtaxfk;
			}
						
			$ftotal = number_format($headeraout['totalaout'],2,",",".");
			
			$headeraout = array_map("htmlspecialchars",$headeraout);
		}
	}

	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>