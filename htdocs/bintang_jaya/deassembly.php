<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	require_once "class/DeAssembly.php";

	$detailstock['status'] = 1;
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	$deassembly = new DeAssembly();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$deassembly->setId($_POST['id']);
		}
		$checkchars = strpos($_POST["stockcode"],"||");
		if ($checkchars !== false){
			$_POST["stockcode"] = substr($_POST["stockcode"],0,$checkchars);
		}
		echo $deassembly->checkcodeexist(trim($_POST['stockcode']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['id'])){
				$deassembly->setId($_REQUEST['id']);
				$deasm = $deassembly->getDeAssembly();
				
				$stock->setId("");
				$stock->setCode($deasm['stockcode']);
				$detailstock = $stock->getFirstStock();
				if (sizeof($detailstock) > 0){
					$deassembly->setCode($detailstock['stockcode']);
					$allcomponent = $deassembly->getDeAssemblyComponent();
					foreach ($allcomponent as $ap){
						$brand->setCode($ap['sccbrandcode']);
						$getbrandname = $brand->getBrandDetail();
						if (empty($getbrandname['brandname']))
							$getbrandname['brandname'] = $ap['sccbrandcode'];
						$type->setCode($ap['scctypecode']);
						$gettypename = $type->gettypeDetail();
						if (empty($gettypename['typename']))
							$gettypename['typename'] = $ap['scctypecode'];
						$units->setCode($ap['sccunitcode']);
						$getunitname = $units->getunitDetail();
						
						$stock->setId("");
						$stock->setCode($ap['stockcodecomponent']);
						$detailstockcnt = $stock->getFirstStock();
						
						$lists .= '
							<row id="r-'.$ap['dsdaid'].'">
								<cell>'.htmlspecialchars($ap['stockcodecomponent']).'</cell>
								<cell>'.htmlspecialchars($ap['sccpartno']).'</cell>
								<cell>'.htmlspecialchars($ap['sccname']).'</cell>
								<cell>'.htmlspecialchars($ap['sccbrandcode']).'</cell>
								<cell>'.htmlspecialchars($ap['scctypecode']).'</cell>
								<cell>'.number_format($ap['sccquantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['sccunitquantityf']).'</cell>
								<cell>'.number_format($detailstockcnt['buyprice'],2,",",".").'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('deassemblydetaillist');
			eval("\$pclist = \"$pclist\";");
		}
		else if ($_GET['list'] == 'general'){
			$keywords = $_GET['keyword'];
			$fields = $_GET['field'];
			
			$allassembly = $deassembly->searchDeAssembly($keywords,$fields,'data');
			$totalrows = sizeof($allassembly);
			$totalpgs = ceil($totalrows / $general['showperpage']);
			$pgs = handlepage($_GET['p'],$totalpgs);
			
			$listassembly = $deassembly->searchDeAssembly($keywords,$fields,'data',$pgs);
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listassembly) > 0){
				$ctr = ($pgs - 1) * $general['showperpage'] + 1;
				foreach ($listassembly as $list){
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					
					$deassembly->setCode($list['stockcode']);
					$allcomponent = $deassembly->getDeAssemblyComponent();
					$splits = sizeof($allcomponent);
					
					if ($splits > 0){
						if ($splits > 1){
							$rstext = ' rowspan="'.$splits.'"';
						}
						$io = 0; 
						foreach ($allcomponent as $apn){
							if ($io == 0){
								$listsplit .= '
									<td width="'.$cwarr[2].'" class="stufflist" align="left">'.htmlspecialchars($apn['stockcodecomponent']).'</td>
									<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($apn['sccname']).'</td>
								';
							}
							else{
								$listsplit2 .= '
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'deassembly.php?id='.$list['sdaid'].'\',\'_self\')">
										<td width="'.$cwarr[2].'" class="stufflist" align="left">'.htmlspecialchars($apn['stockcodecomponent']).'</td>
										<td width="'.$cwarr[3].'" class="stufflist" align="left">'.htmlspecialchars($apn['sccname']).'</td>
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
						';
					}
					
					$stock->setCode($list['stockcode']);
					$lists .= '
						<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'deassembly.php?id='.$list['sdaid'].'\',\'_self\')">
							<td id="row_'.$ctr.'_0" class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.htmlspecialchars($list['stockcode']).'</td>
							<td id="row_'.$ctr.'_1" class="stufflist" width="'.$cwarr[1].'" align="left"'.$rstext.'>'.htmlspecialchars($list['generalname']).'</td>
							'.$listsplit.'
							<td id="row_'.$ctr.'_5" class="stufflist" width="'.$cwarr[4].'" align="center"'.$rstext.'>'.(($useraccess['edit_deassembly'])?'<a href="deassembly.php?id='.$list['sdaid'].'"><b>Ubah</b></a>':'-').'</td>
							<td id="row_'.$ctr.'_6" class="stufflist" width="'.$cwarr[5].'" align="center"'.$rstext.'>'.(($useraccess['delete_deassembly'] && $stock->canDeleteFirstStock('deassembly'))?'<a href="javascript:deleteitem(\'deassembly.php?do=delete&amp;id='.$list['sdaid'].'\')"><b>Hapus</b></a>':'-').'</td>
						</tr>'.$listsplit2.'
					';
					$ctr++;
				}
			}
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
					<div align="left" style="padding: 10px 5px 0 5px">
					Halaman <b>'.$pgs.'</b> dari <b>'.$totalpgs.'</b>'.$pgslinks.'
					<span style="float: right">
					Record '.number_format($startrecord,0,",",".").' - '.number_format($endrecord,0,",",".").' dari total '.number_format($totalrows,0,",",".").'</span>
					</div>
				';
			}
		}
		echo $pclist;
		exit;
	}
	
	if (!empty($_REQUEST['id'])){
		$deassembly->setId($_REQUEST['id']);
		$deasm = $deassembly->getDeAssembly();
		
		if (empty($deasm['sdaid'])){
			redirecting('deassembly.php?screen=list');
		}
		
		$stock->setId("");
		$stock->setCode($deasm['stockcode']);
		$detailstock = $stock->getFirstStock();
	}
	
	if ($_POST['submits'] == 'Tambah' && $useraccess['add_deassembly']){
		$checkchars = strpos($_POST["stockcode"],"||");
		if ($checkchars !== false){
			$_POST["stockcode"] = substr($_POST["stockcode"],0,$checkchars);
		}
		
		$stock->setId("");
		$stock->setCode($_POST['stockcode']);
		$detailstock = $stock->getFirstStock();
		
		$deassembly->setCode($_POST['stockcode']);
		$lastid = $deassembly->saveDeAssembly();
		
		$arrpostdel = explode(",",$_POST['deassemblybox_rowsdeleted']);
		$arrpost = explode(",",$_POST['deassemblybox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["deassemblybox_".$arrpost[$x]."_0"])){
					$checkchars = strpos($_POST["deassemblybox_".$arrpost[$x]."_0"],"||");
					if ($checkchars !== false){
						$_POST["deassemblybox_".$arrpost[$x]."_0"] = substr($_POST["deassemblybox_".$arrpost[$x]."_0"],0,$checkchars);
					}
					
					$stock->setId("");
					$stock->setCode($_POST["deassemblybox_".$arrpost[$x]."_0"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["deassemblybox_".$arrpost[$x]."_5"] = togglenumber($_POST["deassemblybox_".$arrpost[$x]."_5"],'calculate');
											
					if ($_POST["deassemblybox_".$arrpost[$x]."_6"] == $getunit['lunit']){
						$quantity = $_POST["deassemblybox_".$arrpost[$x]."_5"];
					}
					else if ($_POST["deassemblybox_".$arrpost[$x]."_6"] == $getunit['funit']){
						$quantity = $_POST["deassemblybox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
					}
					
					//update price
					$_POST["deassemblybox_".$arrpost[$x]."_7"] = togglenumber($_POST["deassemblybox_".$arrpost[$x]."_7"],'calculate');
					$db->query("UPDATE stock SET buyprice='".$_POST["deassemblybox_".$arrpost[$x]."_7"]."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."'");
					$upd = $stock->getStockAll();
					$db->query("UPDATE stock SET buyminprice='".$upd['minp']."', buymaxprice='".$upd['maxp']."', minexpdate='".$upd['mexp']."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."'");
					
					//update deassembly price
					$db->query("UPDATE logdeassembly SET price='".$_POST["deassemblybox_".$arrpost[$x]."_7"]."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."' AND price=0");
					
					$deassembly->saveDetailDeAssembly($_POST["deassemblybox_".$arrpost[$x]."_0"],$_POST["deassemblybox_".$arrpost[$x]."_1"],$_POST["deassemblybox_".$arrpost[$x]."_2"],$_POST["deassemblybox_".$arrpost[$x]."_3"],$_POST["deassemblybox_".$arrpost[$x]."_4"],$_POST["deassemblybox_".$arrpost[$x]."_5"],$_POST["deassemblybox_".$arrpost[$x]."_6"],$quantity,$unitquantity,$getstock['unitcode']);
				}
			}
		}
		
		//direct deassembly
		if ($_POST['directs'] == 'yes' && !empty($_POST['deascount'])){
			$getdacomponent = $deassembly->getDeAssemblyComponent();
			$scparent = $_POST['stockcode'];
			$parentquantity = $_POST['deascount'];
			$quantitytor = $_POST['deascount'];
			if ($detailstock['remaining'] > 0){
				$valuetoinsert = 0;
				$valuetoupdate = 0;
				if ($quantitytor <= $detailstock['remaining']){
					$valuetoupdate = $detailstock['remaining'] - $quantitytor;
					$valuetoinsert = $quantitytor;
					$quantitytor = 0;
				}
				else{
					$valuetoupdate = 0;
					$valuetoinsert = $detailstock['remaining'];
					$quantitytor = $quantitytor - $fstk['remaining'];
				}
				$db->query("UPDATE stock SET remaining=".$valuetoupdate." WHERE stockcode='".$scparent."'");
			}
			if ($quantitytor > 0){
				$stock->setCode($_POST['stockcode']);
				$gstr = $stock->getStockToReduced();
				if (sizeof($gstr) > 0){
					foreach ($gstr as $str){
						if ($quantitytor == 0){
							break;
						}
						$valuetoinsert = 0;
						$valuetoupdate = 0;
						$stockrm = $str['quantity'] - $str['usedqty'];
						if ($quantitytor <= $stockrm){
							$valuetoupdate = $quantitytor;
							$valuetoinsert = $quantitytor;
							$quantitytor = 0;
						}
						else{
							$valuetoupdate = $stockrm;
							$valuetoinsert = $stockrm;
							$quantitytor = $quantitytor - $stockrm;
						}
					
						$db->query("UPDATE detailbuy SET usedqty=usedqty+".$valuetoupdate." WHERE dbid='".$str['dbid']."'");
					}
				}
			}
			
			if (sizeof($getdacomponent) > 0){
				$stock->setId("");
				$stock->setCode($scparent);
				$parentfirststock = $stock->getFirstStock();
			
				$stock->minStock($parentquantity);
				
				$units->setCode($parentfirststock['unitcode']);
				$unitdetail = $units->getunitDetail();
				
				$insertlogdap['logdate'] = time()-1;
				$insertlogdap['stockcode'] = $scparent;
				$insertlogdap['quantity'] = $parentquantity;
				$insertlogdap['unitquantity'] = $unitdetail['lunit'];
				$insertlogdap['unitcode'] = $parentfirststock['unitcode'];
				$insertlogdap['description'] = 'deassembly';
				$logparentid = $db->insert("logdeassemblyparent",$insertlogdap);
				
				foreach ($getdacomponent as $gdcpnt){
					$componentquantity = $gdcpnt['sccquantity'] * $parentquantity;
					
					$insertlogda['logdate'] = time()-1;
					$insertlogda['logparentid'] = $logparentid;
					$insertlogda['stockcode'] = $gdcpnt['stockcodecomponent'];
					$insertlogda['quantity'] = $componentquantity;
					$insertlogda['unitquantity'] = $gdcpnt['sccunitquantity'];
					$insertlogda['unitcode'] = $gdcpnt['sccunitcode'];
					$insertlogda['description'] = 'Pecahan dari : '.$scparent;
					$insertlogda['usedqty'] = 0;
					
					$stock->setId("");
					$stock->setCode($gdcpnt['stockcodecomponent']);
					$cpntfirststock = $stock->getFirstStock();
					$insertlogda['price'] = $cpntfirststock['buyprice'];
					$insertlogda['status'] = 1;
			
					$lastldaid = $db->insert("logdeassembly",$insertlogda);
					
					$stock->setCode($gdcpnt['stockcodecomponent']);
					$stock->addStock($componentquantity);
					$stock->addTotalStock($componentquantity);
				}
			}
		}
		
		redirecting("deassembly.php?id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_deassembly']){
		$stock->setId("");
		
		$checkchars = strpos($_POST["stockcode"],"||");
		if ($checkchars !== false){
			$_POST["stockcode"] = substr($_POST["stockcode"],0,$checkchars);
		}
		
		$deassembly->setCode($detailstock['stockcode']);
		$deassembly->updateDeAssembly($_POST['stockcode']);
		$allcomponent = $deassembly->getDeAssemblyComponent();
		$arrpost = array();
		if (sizeof($allcomponent) > 0){
			foreach ($allcomponent as $aad){
				array_push($arrpost,'r-'.$aad['dsdaid']);
			}
		}

		//deleted rows
		$arrpostdel = explode(",",$_POST['deassemblybox_rowsdeleted']);
		$arrpostdelsz = sizeof($arrpostdel);
		if ($arrpostdelsz > 0){
			for ($x = 0; $x < $arrpostdelsz; $x++){
				if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpost)){
					$deassembly->setDetailId($arrpostdel[$x]);
					$deassembly->deleteComponent();
				}
			}
		}		
		//edited rows
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["deassemblybox_".$arrpost[$x]."_0"])){
					$deassembly->setDetailId($arrpost[$x]);
					
					$checkchars = strpos($_POST["deassemblybox_".$arrpost[$x]."_0"],"||");
					if ($checkchars !== false){
						$_POST["deassemblybox_".$arrpost[$x]."_0"] = substr($_POST["deassemblybox_".$arrpost[$x]."_0"],0,$checkchars);
					}
					
					$stock->setId("");
					$stock->setCode($_POST["deassemblybox_".$arrpost[$x]."_0"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["deassemblybox_".$arrpost[$x]."_5"] = togglenumber($_POST["deassemblybox_".$arrpost[$x]."_5"],'calculate');
											
					if ($_POST["deassemblybox_".$arrpost[$x]."_6"] == $getunit['lunit']){
						$quantity = $_POST["deassemblybox_".$arrpost[$x]."_5"];
					}
					else if ($_POST["deassemblybox_".$arrpost[$x]."_6"] == $getunit['funit']){
						$quantity = $_POST["deassemblybox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
					}
					
					//update price
					$_POST["deassemblybox_".$arrpost[$x]."_7"] = togglenumber($_POST["deassemblybox_".$arrpost[$x]."_7"],'calculate');
					$db->query("UPDATE stock SET buyprice='".$_POST["deassemblybox_".$arrpost[$x]."_7"]."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."'");
					$upd = $stock->getStockAll();
					$db->query("UPDATE stock SET buyminprice='".$upd['minp']."', buymaxprice='".$upd['maxp']."', minexpdate='".$upd['mexp']."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."'");
					
					//update deassembly price
					$db->query("UPDATE logdeassembly SET price='".$_POST["deassemblybox_".$arrpost[$x]."_7"]."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."' AND price=0");
					
					$deassembly->updateDetailDeAssembly($_POST['stockcode'],$_POST["deassemblybox_".$arrpost[$x]."_0"],$_POST["deassemblybox_".$arrpost[$x]."_1"],$_POST["deassemblybox_".$arrpost[$x]."_2"],$_POST["deassemblybox_".$arrpost[$x]."_3"],$_POST["deassemblybox_".$arrpost[$x]."_4"],$_POST["deassemblybox_".$arrpost[$x]."_5"],$_POST["deassemblybox_".$arrpost[$x]."_6"],$quantity,$unitquantity,$getstock['unitcode']);
				}
			}
		}
		//added rows
		unset($arrpost);
		$arrpost = explode(",",$_POST['deassemblybox_rowsadded']);
		$arrpost = array_diff($arrpost,$arrpostdel);
		$arrpost = array_values($arrpost);
		$arrpostsz = sizeof($arrpost);
		if ($arrpostsz > 0){
			$deassembly->setCode($_POST['stockcode']);
			for ($x = 0; $x < $arrpostsz; $x++){
				if (!empty($arrpost[$x]) && !empty($_POST["deassemblybox_".$arrpost[$x]."_0"])){
					$checkchars = strpos($_POST["deassemblybox_".$arrpost[$x]."_0"],"||");
					if ($checkchars !== false){
						$_POST["deassemblybox_".$arrpost[$x]."_0"] = substr($_POST["deassemblybox_".$arrpost[$x]."_0"],0,$checkchars);
					}
					
					$stock->setId("");
					$stock->setCode($_POST["deassemblybox_".$arrpost[$x]."_0"]);
					$getstock = $stock->getFirstStock();
					$units->setCode($getstock['unitcode']);
					$getunit = $units->getunitDetail();
					$unitquantity = $getunit['lunit'];
					
					$_POST["deassemblybox_".$arrpost[$x]."_5"] = togglenumber($_POST["deassemblybox_".$arrpost[$x]."_5"],'calculate');
											
					if ($_POST["deassemblybox_".$arrpost[$x]."_6"] == $getunit['lunit']){
						$quantity = $_POST["deassemblybox_".$arrpost[$x]."_5"];
					}
					else if ($_POST["deassemblybox_".$arrpost[$x]."_6"] == $getunit['funit']){
						$quantity = $_POST["deassemblybox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
					}
					
					//update price
					$_POST["deassemblybox_".$arrpost[$x]."_7"] = togglenumber($_POST["deassemblybox_".$arrpost[$x]."_7"],'calculate');
					$db->query("UPDATE stock SET buyprice='".$_POST["deassemblybox_".$arrpost[$x]."_7"]."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."'");
					$upd = $stock->getStockAll();
					$db->query("UPDATE stock SET buyminprice='".$upd['minp']."', buymaxprice='".$upd['maxp']."', minexpdate='".$upd['mexp']."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."'");
					
					//update deassembly price
					$db->query("UPDATE logdeassembly SET price='".$_POST["deassemblybox_".$arrpost[$x]."_7"]."' WHERE stockcode='".$_POST["deassemblybox_".$arrpost[$x]."_0"]."' AND price=0");
					
					$deassembly->saveDetailDeAssembly($_POST["deassemblybox_".$arrpost[$x]."_0"],$_POST["deassemblybox_".$arrpost[$x]."_1"],$_POST["deassemblybox_".$arrpost[$x]."_2"],$_POST["deassemblybox_".$arrpost[$x]."_3"],$_POST["deassemblybox_".$arrpost[$x]."_4"],$_POST["deassemblybox_".$arrpost[$x]."_5"],$_POST["deassemblybox_".$arrpost[$x]."_6"],$quantity,$unitquantity,$getstock['unitcode']);
				}
			}
		}
		
		//remove deassembly from not related item
		$dballdas = $db->fetch_all("SELECT * FROM detailstockdeassembly GROUP BY stockcodecomponent");
		if (sizeof($dballdas) > 0){
			$iddas = '';
			foreach ($dballdas as $dads){
				$iddas .= ',\''.str_replace("'","\'",$dads['stockcodecomponent']).'\'';
			}
			$iddas = substr($iddas,1);
			
			$db->query("UPDATE stock SET assembly = 0 WHERE stockcode NOT IN (".$iddas.") AND assembly = 2");
		}
		
		//direct deassembly
		if ($_POST['directs'] == 'yes' && !empty($_POST['deascount'])){
			$getdacomponent = $deassembly->getDeAssemblyComponent();
			$scparent = $_POST['stockcode'];
			$parentquantity = $_POST['deascount'];
			$quantitytor = $_POST['deascount'];
			if ($detailstock['remaining'] > 0){
				$valuetoinsert = 0;
				$valuetoupdate = 0;
				if ($quantitytor <= $detailstock['remaining']){
					$valuetoupdate = $detailstock['remaining'] - $quantitytor;
					$valuetoinsert = $quantitytor;
					$quantitytor = 0;
				}
				else{
					$valuetoupdate = 0;
					$valuetoinsert = $detailstock['remaining'];
					$quantitytor = $quantitytor - $fstk['remaining'];
				}
				$db->query("UPDATE stock SET remaining=".$valuetoupdate." WHERE stockcode='".$scparent."'");
			}
			if ($quantitytor > 0){
				$stock->setCode($_POST['stockcode']);
				$gstr = $stock->getStockToReduced();
				if (sizeof($gstr) > 0){
					foreach ($gstr as $str){
						if ($quantitytor == 0){
							break;
						}
						$valuetoinsert = 0;
						$valuetoupdate = 0;
						$stockrm = $str['quantity'] - $str['usedqty'];
						if ($quantitytor <= $stockrm){
							$valuetoupdate = $quantitytor;
							$valuetoinsert = $quantitytor;
							$quantitytor = 0;
						}
						else{
							$valuetoupdate = $stockrm;
							$valuetoinsert = $stockrm;
							$quantitytor = $quantitytor - $stockrm;
						}
					
						$db->query("UPDATE detailbuy SET usedqty=usedqty+".$valuetoupdate." WHERE dbid='".$str['dbid']."'");
					}
				}
			}
			
			if (sizeof($getdacomponent) > 0){
				$stock->setId("");
				$stock->setCode($scparent);
				$parentfirststock = $stock->getFirstStock();
			
				$stock->minStock($parentquantity);
				
				$units->setCode($parentfirststock['unitcode']);
				$unitdetail = $units->getunitDetail();
				
				$insertlogdap['logdate'] = time()-1;
				$insertlogdap['stockcode'] = $scparent;
				$insertlogdap['quantity'] = $parentquantity;
				$insertlogdap['unitquantity'] = $unitdetail['lunit'];
				$insertlogdap['unitcode'] = $parentfirststock['unitcode'];
				$insertlogdap['description'] = 'deassembly';
				$logparentid = $db->insert("logdeassemblyparent",$insertlogdap);
				
				foreach ($getdacomponent as $gdcpnt){
					$componentquantity = $gdcpnt['sccquantity'] * $parentquantity;
					
					$insertlogda['logdate'] = time()-1;
					$insertlogda['logparentid'] = $logparentid;
					$insertlogda['stockcode'] = $gdcpnt['stockcodecomponent'];
					$insertlogda['quantity'] = $componentquantity;
					$insertlogda['unitquantity'] = $gdcpnt['sccunitquantity'];
					$insertlogda['unitcode'] = $gdcpnt['sccunitcode'];
					$insertlogda['description'] = 'Pecahan dari : '.$scparent;
					$insertlogda['usedqty'] = 0;
					
					$stock->setId("");
					$stock->setCode($gdcpnt['stockcodecomponent']);
					$cpntfirststock = $stock->getFirstStock();
					$insertlogda['price'] = $cpntfirststock['buyprice'];
					$insertlogda['status'] = 1;
			
					$lastldaid = $db->insert("logdeassembly",$insertlogda);
					
					$stock->setCode($gdcpnt['stockcodecomponent']);
					$stock->addStock($componentquantity);
					$stock->addTotalStock($componentquantity);
				}
			}
		}
		
		redirecting("deassembly.php?id=".$_REQUEST['id']);
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_deassembly']){
		$deassembly->setCode($detailstock['stockcode']);
		$deassembly->deleteDeAssembly();
		redirecting("deassembly.php?screen=list");
	}
	
	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_deassembly'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'deassemblylist';
	}
	else{
		
		if (empty($useraccess['add_deassembly']) && empty($useraccess['edit_deassembly'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'deassembly';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>