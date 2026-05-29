<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	require_once "class/area.php";
	require_once "class/AdjustIn.php";
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	$area = new area();
	$ain = new AdjustIn();
	
	$headerain['totalain'] = 0;
	$aindate = date("d-m-Y");
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['id'])){
				$ain->setId($_GET['id']);
				$allain = $ain->getDetailAdjustIn();
				if (sizeof($allain) > 0){
					foreach ($allain as $ap){
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
							$totalainfk = $ap['quantityf'] * $ap['ainprice'];
							$totaldiscfk = $ap['disc'] / 100 * $totalainfk;
							$ap['totalainprice'] = $totalainfk - $totaldiscfk;
						}
						
						$lists .= '
							<row id="r-'.$ap['dainid'].'">
								<cell>'.htmlspecialchars($ap['stockcode']).'</cell>
								<cell>'.htmlspecialchars($ap['partno']).'</cell>
								<cell>'.htmlspecialchars($ap['stockname']).'</cell>
								<cell>'.htmlspecialchars($ap['brandcode']).'</cell>
								<cell>'.htmlspecialchars($ap['typecode']).'</cell>
								<cell>'.number_format($ap['quantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['unitquantityf']).'</cell>
								<cell>'.number_format($ap['ainprice'],2,",",".").'</cell>
								<cell>'.number_format($ap['totalainprice'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['description']).'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('adjustindetaillist');
		}
		else if ($_GET['list'] == 'general'){
			/*$listain = $ain->getListAdjustIn('all');
			$lists = '';
			if (sizeof($listain) > 0){
				foreach ($listain as $list){					
					$lists .= '
						<row id="'.$list['ainid'].'">
							<cell>'.htmlspecialchars($list['ainid']).'</cell>
							<cell>'.date("d-m-Y",$list['aindate']).'</cell>
							<cell>'.$list['totalain'].'</cell>
							<cell>'.$list['description'].'</cell>
							<cell>Ubah^adjustin.php?id='.$list['ainid'].'^_self</cell>
							<cell>Hapus^javascript:deleteitem("adjustin.php?do=delete&amp;id='.$list['ainid'].'")^_self</cell>
						</row>
					';
				}
			}*/
			/* if (isset($_GET['keyword'])){
				if ($_GET['keyword'] != ''){ */
					$allain = $ain->searchAdjustIn($_GET['keyword'],$_GET['field']);
					$totalrows = sizeof($allain);
					$totalpgs = ceil($totalrows / $general['showperpage']);
					$pgs = handlepage($_GET['p'],$totalpgs);
					
					$listain = $ain->searchAdjustIn($_GET['keyword'],$_GET['field'],$pgs);
				/* }
			} */
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listain) > 0){
				$ctr = 1;
				foreach ($listain as $list){
					$ain->setId($list['ainid']);
				
					$getdetailain = $ain->getDetailAdjustIn();
					$splits = sizeof($getdetailain);
					
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0;
						$subtotalfk = 0;
						foreach ($getdetailain as $gdb){
							$gdb['typecode'] = wordwrap($gdb['typecode'],15,"<br>",true);
							$gdb['brandcode'] = wordwrap($gdb['brandcode'],10,"<br>",true);
							
							$gdb['typecode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['typecode']));
							$gdb['brandcode'] = str_replace("&lt;br&gt;","<br>",htmlspecialchars($gdb['brandcode']));
							
							if ($statususer == 1){
								$gdb['quantityf'] = floor((100-$discount['extradisc'])/100 * $gdb['quantityf']);
								if ($gdb['quantityf'] < 1){
									$gdb['quantityf'] = 1;
								}
								
								$totalainfk = $gdb['quantityf'] * $gdb['ainprice'];
								$totaldiscfk = $gdb['disc'] / 100 * $totalainfk;
								$subtotalfk += ($totalainfk - $totaldiscfk);
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
									<td width="'.$cwarr[9].'" class="stufflist" id="ainprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['ainprice']).'</td>
								';
							}
							else{
								$listsplit2 .= '
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'adjustin.php?id='.$list['ainid'].'\',\'_self\')">
										<td width="'.$cwarr[2].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockcode']).'</td>
										<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($gdb['partno']).'</td>
										<td width="'.$cwarr[4].'" class="stufflist" align="left">'.htmlspecialchars($gdb['stockname']).'</td>
										<td width="'.$cwarr[5].'" class="stufflist" align="left">'.$gdb['brandcode'].'</td>
										<td width="'.$cwarr[6].'" class="stufflist" align="left">'.$gdb['typecode'].'</td>
										<td width="'.$cwarr[7].'" class="stufflist" align="right">'.number_format($gdb['quantityf'],2,",",".").'</td>
										<td width="'.$cwarr[8].'" class="stufflist" align="left">'.htmlspecialchars($gdb['unitquantityf']).'</td>
										<td width="'.$cwarr[9].'" class="stufflist" id="ainprice_'.$ctr.'-'.$io.'" align="right">'.$codest->convertcodes($gdb['ainprice']).'</td>
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
						$list['totalain'] = $totalafgdiscfk + $totalgtaxfk;
					}

					$lists .= '
							<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'adjustin.php?id='.$list['ainid'].'\',\'_self\')">
								<td class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.htmlspecialchars($list['ainid']).'</td>
								<td class="stufflist" width="'.$cwarr[1].'" align="center"'.$rstext.'>'.date("d-m-Y",$list['aindate']).'</td>
								'.$listsplit.'
								<td class="stufflist" id="total_'.$ctr.'" width="'.$cwarr[10].'" align="right"'.$rstext.'>'.$codest->convertcodes($list['totalain']).'</td>
								<td class="stufflist bgseparator" width="'.$cwarr[11].'" align="center"'.$rstext.'><span style="text-align: center; width: '.$actioneditwidth.'px; display: inline-block">'.
								(($useraccess['edit_adjustin'])?'<a href="adjustin.php?id='.$list['ainid'].'">Ubah</a>':'-').'</span><span style="text-align: center; margin-left: 3px; width: '.$actiondeletewidth.'px; display: inline-block">'.
								(($useraccess['delete_adjustin'])?'<a href="javascript:deleteitem(\'adjustin.php?do=delete&id='.$list['ainid'].'\')">Hapus</a>':'-').'</span></td>
							</tr>'.$listsplit2.'
					';					
					
					$ctr++;
				}
			}
			$ctrgo = $ctr-1;
			/* $pclist = gettemplate('adjustinlistdetail'); */
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
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_adjustin']){
		$db->beginTransaction();
		$aindate = strtotime($_POST['aindate']);
		$lastid = $ain->saveHeaderAdjustIn($aindate,$_POST['description'],$_POST['totalain'],$userid);
		
		$ain->setId($lastid);
		
		$arrpostdel = explode(",",$_POST['detailainbox_rowsdeleted']);
		$arrpost = explode(",",$_POST['detailainbox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["detailainbox_".$arrpost[$x]."_0"])){
					$checkchars = strpos($_POST["detailainbox_".$arrpost[$x]."_0"],"||");
					if ($checkchars !== false){
						$_POST["detailainbox_".$arrpost[$x]."_0"] = substr($_POST["detailainbox_".$arrpost[$x]."_0"],0,$checkchars);
					}

					$stock->setId("");
					$stock->setCode($_POST["detailainbox_".$arrpost[$x]."_0"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["detailainbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_5"],'calculate');
					$_POST["detailainbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_7"],'calculate');
					$_POST["detailainbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_8"],'calculate');

					if ($_POST["detailainbox_".$arrpost[$x]."_6"] == $getunit['funit']){
						$quantity = $_POST["detailainbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
						$realainprice = $_POST["detailainbox_".$arrpost[$x]."_7"] / $getunit['cvalue'];
					}
					else{
						$quantity = $_POST["detailainbox_".$arrpost[$x]."_5"];
						$_POST["detailainbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
						$realainprice = $_POST["detailainbox_".$arrpost[$x]."_7"];
					}
					
					$ain->saveDetailAdjustIn($_POST["detailainbox_".$arrpost[$x]."_0"],$_POST["detailainbox_".$arrpost[$x]."_1"],$_POST["detailainbox_".$arrpost[$x]."_2"],$_POST["detailainbox_".$arrpost[$x]."_3"],$_POST["detailainbox_".$arrpost[$x]."_4"],$_POST["detailainbox_".$arrpost[$x]."_5"],$_POST["detailainbox_".$arrpost[$x]."_6"],$_POST["detailainbox_".$arrpost[$x]."_7"],$_POST["detailainbox_".$arrpost[$x]."_8"],$_POST["detailainbox_".$arrpost[$x]."_9"],$aindate,$realainprice,$quantity,$unitquantity,$getstock['unitcode']);
				}
			}
		}
		$db->endTransaction();
		redirecting("adjustin.php?id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_adjustin']){
		//print_r($_POST);
		if (!empty($_POST['id'])){
			$ain->setId($_POST['id']);
			
			$db->beginTransaction();
			$aindate = strtotime($_POST['aindate']);
			$ain->updateHeaderAdjustIn($aindate,$_POST['description'],$_POST['totalain'],$userid);
			
			$arrpostt = explode(",",$_POST['detailid']);
			
			//deleted rows
			$arrpostdel = explode(",",$_POST['detailainbox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpostt)){
						$ain->setDetailId($arrpostdel[$x]);
						$olddetail = $ain->getDetailAdjustInIndv();
						$stock->setCode($olddetail['stockcode']);
						$ain->deleteDetailAInItem($olddetail['stockcode']);
						$db->query("DELETE FROM detailadjustin WHERE dainid='".$ain->dtid."'");
						$stock->minStock($olddetail['quantity']);
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
						$checkchars = strpos($_POST["detailainbox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailainbox_".$arrpost[$x]."_0"] = substr($_POST["detailainbox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$ain->setDetailId($arrpost[$x]);
						$olddetail = $ain->getDetailAdjustInIndv();
						
						$units->setCode($olddetail['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailainbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailainbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailainbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_8"],'calculate');

						if ($_POST["detailainbox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailainbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realainprice = $_POST["detailainbox_".$arrpost[$x]."_7"] / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailainbox_".$arrpost[$x]."_5"];
							$_POST["detailainbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
							$realainprice = $_POST["detailainbox_".$arrpost[$x]."_7"];
						}
						
						$ain->updateDetailAdjustIn($_POST["detailainbox_".$arrpost[$x]."_0"],$_POST["detailainbox_".$arrpost[$x]."_1"],$_POST["detailainbox_".$arrpost[$x]."_2"],$_POST["detailainbox_".$arrpost[$x]."_3"],$_POST["detailainbox_".$arrpost[$x]."_4"],$_POST["detailainbox_".$arrpost[$x]."_5"],$_POST["detailainbox_".$arrpost[$x]."_6"],$_POST["detailainbox_".$arrpost[$x]."_7"],$_POST["detailainbox_".$arrpost[$x]."_8"],$_POST["detailainbox_".$arrpost[$x]."_9"],$aindate,$realainprice,$quantity,$unitquantity,$getstock['unitcode'],$olddetail);
					}
				}
			}
			
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['detailainbox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["detailainbox_".$arrpost[$x]."_0"])){
						$checkchars = strpos($_POST["detailainbox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["detailainbox_".$arrpost[$x]."_0"] = substr($_POST["detailainbox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["detailainbox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["detailainbox_".$arrpost[$x]."_5"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_5"],'calculate');
						$_POST["detailainbox_".$arrpost[$x]."_7"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_7"],'calculate');
						$_POST["detailainbox_".$arrpost[$x]."_8"] = togglenumber($_POST["detailainbox_".$arrpost[$x]."_8"],'calculate');

						if ($_POST["detailainbox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["detailainbox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
							$realainprice = $_POST["detailainbox_".$arrpost[$x]."_7"] / $getunit['cvalue'];
						}
						else{
							$quantity = $_POST["detailainbox_".$arrpost[$x]."_5"];
							$_POST["detailainbox_".$arrpost[$x]."_6"] = $getunit['lunit'];
							$realainprice = $_POST["detailainbox_".$arrpost[$x]."_7"];
						}
						
						$ain->saveDetailAdjustIn($_POST["detailainbox_".$arrpost[$x]."_0"],$_POST["detailainbox_".$arrpost[$x]."_1"],$_POST["detailainbox_".$arrpost[$x]."_2"],$_POST["detailainbox_".$arrpost[$x]."_3"],$_POST["detailainbox_".$arrpost[$x]."_4"],$_POST["detailainbox_".$arrpost[$x]."_5"],$_POST["detailainbox_".$arrpost[$x]."_6"],$_POST["detailainbox_".$arrpost[$x]."_7"],$_POST["detailainbox_".$arrpost[$x]."_8"],$_POST["detailainbox_".$arrpost[$x]."_9"],$aindate,$realainprice,$quantity,$unitquantity,$getstock['unitcode']);
					}
				}
			}
			$db->endTransaction();
		}
		redirecting("adjustin.php?id=".$_POST['id']);
	}

	$ainid = $_GET['id'];

	if ($_GET['do'] == 'delete' && !empty($ainid) && $useraccess['delete_adjustin']){
		$db->beginTransaction();
		
		$ain->setId($ainid);
		$headerain = $ain->getHeaderAdjustIn();
		
		$ain->deleteAdjustIn();
		
		$db->endTransaction();
		redirecting("adjustin.php?screen=list");		
	}

	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_adjustin'])){
			redirecting('index.php');
		}
		
		$printtemplate = 'adjustinlist';
	}
	else{
		
		if (empty($useraccess['add_adjustin']) && empty($useraccess['edit_adjustin'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'adjustin';
		if (!empty($ainid)){
			$ain->setId($ainid);
			$headerain = $ain->getHeaderAdjustIn();
			
			if (empty($headerain['ainid'])){
				redirecting('adjustin.php?screen=list');
			}
			
			$aindate = date("d-m-Y",$headerain['aindate']);
			
			$alldtl = $ain->getDetailAdjustIn();
			$alldetailid = '';
			$subtotalfk = 0;
			if (sizeof($alldtl) > 0){
				foreach ($alldtl as $aad){
					$stock->setId("");
					$stock->setCode($aad['stockcode']);
					$getds = $stock->getFirstStock();
					
					$alldetailid .= ',r-'.$aad['dainid'];
					$alldetailidjs .= ',"r-'.$aad['dainid'].'"';
					$allidusedqty .= ','.($detailsale['quantity'] - $detailsale['usedqty']);
					
					$units->setCode($getds['unitcode']);
					$getunit = $units->getunitDetail();
					$arrunits .= '
						arrunits["r-'.$aad['dainid'].'"] = new Array();
						arrunits["r-'.$aad['dainid'].'"][0] = "'.$getunit['funit'].'";
						arrunits["r-'.$aad['dainid'].'"][1] = "'.$getunit['lunit'].'";';
					$arrconversion .= '
						arrconversion["r-'.$aad['dainid'].'"] = new Array();
						arrconversion["r-'.$aad['dainid'].'"][0] = '.$getunit['cvalue'].';
						arrconversion["r-'.$aad['dainid'].'"][1] = 1;';
				
					$aad['quantityf'] = floor((100-$discount['extradisc'])/100 * $aad['quantityf']);
					if ($aad['quantityf'] < 1){
						$aad['quantityf'] = 1;
					}
						
					$totalainfk = $aad['quantityf'] * $aad['ainprice'];
					$totaldiscfk = $aad['disc'] / 100 * $totalainfk;
					$subtotalfk += ($totalainfk - $totaldiscfk);
				}
				$alldetailid = substr($alldetailid,1);
				$alldetailidjs = substr($alldetailidjs,1);
				$allidusedqty = substr($allidusedqty,1);
			}
			
			if ($statususer == 1){
				$headerain['totals'] = $subtotalfk;
				$totalgdiscfk = $headerain['disc'] / 100 * $subtotalfk;
				$totalafgdiscfk = $subtotalfk - $totalgdiscfk;
				$totalgtaxfk = $headerain['tax'] / 100 * $totalafgdiscfk;
				$headerain['totalain'] = $totalafgdiscfk + $totalgtaxfk;
			}
						
			$ftotal = number_format($headerain['totalain'],2,",",".");
			
			$headerain = array_map("htmlspecialchars",$headerain);
		}
	}

	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>