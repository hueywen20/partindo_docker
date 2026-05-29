<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/Brand.php";
	require_once "class/type.php";
	require_once "class/location.php";
	require_once "class/stockgroup.php";
	require_once "class/units.php";
	require_once "class/Assembly.php";

	$detailstock['status'] = 1;
	$stock = new Stock();
	$brand = new Brand();
	$type = new type();
	$location = new location();
	$stgr = new stgr();
	$units = new unit();
	$assembly = new Assembly();
	
	if ($_POST['check'] == 'code'){
		if (!empty($_POST['id'])){
			$stock->setId($_POST['id']);
		}
		echo $stock->checkStockCodeNoExist(trim($_POST['code']));
		exit;
	}
	
	if ($_GET['getlist'] == 'xml'){
		if ($_GET['list'] == 'detail'){
			header("Content-type: text/xml");
			if (!empty($_GET['id'])){
				$stock->setId($_REQUEST['id']);		
				$detailstock = $stock->getFirstStock();
				if (sizeof($detailstock) > 0){
					$assembly->setCode($detailstock['stockcode']);
					$allcomponent = $assembly->getAssemblyComponent();
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
						
						$lists .= '
							<row id="r-'.$ap['dsaid'].'">
								<cell>'.htmlspecialchars($ap['stockcodecomponent']).'</cell>
								<cell>'.htmlspecialchars($ap['sccpartno']).'</cell>
								<cell>'.htmlspecialchars($ap['sccname']).'</cell>
								<cell>'.htmlspecialchars($ap['sccbrandcode']).'</cell>
								<cell>'.htmlspecialchars($ap['scctypecode']).'</cell>
								<cell>'.number_format($ap['sccquantityf'],2,",",".").'</cell>
								<cell>'.htmlspecialchars($ap['sccunitquantityf']).'</cell>
							</row>
						';
					}
				}
			}
			$pclist = gettemplate('assemblydetaillist');
			eval("\$pclist = \"$pclist\";");
		}
		else if ($_GET['list'] == 'general'){
			$keywords = $_GET['keyword'];
			$fields = $_GET['field'];
			
			$allassembly = $assembly->searchAssembly($keywords,$fields,'data');
			$totalrows = sizeof($allassembly);
			$totalpgs = ceil($totalrows / $general['showperpage']);
			$pgs = handlepage($_GET['p'],$totalpgs);
			
			$listassembly = $assembly->searchAssembly($keywords,$fields,'data',$pgs);
			$lists = '';
			$cwarr = explode(",",$_GET['cwidth']);
			if (sizeof($listassembly) > 0){
				$ctr = ($pgs - 1) * $general['showperpage'] + 1;
				foreach ($listassembly as $list){
					$listsplit = '';
					$listsplit2 = '';
					$rstext = '';
					
					$assembly->setCode($list['stockcode']);
					$allcomponent = $assembly->getAssemblyComponent();
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
									<tr id="row_'.$ctr.'-'.$io.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'assembly.php?id='.$list['stockid'].'\',\'_self\')">
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
						<tr id="row_'.$ctr.'" onclick="changebgcolor(this)" bgcolor="'.(($ctr % 2 == 0)?'#EEEEFF':'#EEFFEE').'" class="tdhovertable" onmouseover="mouseovertable(this.id)" onmouseout="mouseouttable(this.id)" ondblclick="window.open(\'assembly.php?id='.$list['stockid'].'\',\'_self\')">
							<td id="row_'.$ctr.'_0" class="stufflist" width="'.$cwarr[0].'" align="left"'.$rstext.'>'.htmlspecialchars($list['stockcode']).'</td>
							<td id="row_'.$ctr.'_1" class="stufflist" width="'.$cwarr[1].'" align="left"'.$rstext.'>'.htmlspecialchars($list['generalname']).'</td>
							'.$listsplit.'
							<td id="row_'.$ctr.'_5" class="stufflist" width="'.$cwarr[4].'" align="center"'.$rstext.'>'.(($useraccess['edit_assembly'])?'<a href="assembly.php?id='.$list['stockid'].'"><b>Ubah</b></a>':'-').'</td>
							<td id="row_'.$ctr.'_6" class="stufflist" width="'.$cwarr[5].'" align="center"'.$rstext.'>'.(($useraccess['delete_assembly'] && $stock->canDeleteFirstStock())?'<a href="javascript:deleteitem(\'assembly.php?do=delete&amp;id='.$list['stockid'].'\')"><b>Hapus</b></a>':'-').'</td>
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
		$stock->setId($_REQUEST['id']);		
		$detailstock = $stock->getFirstStock();
		
		if (empty($detailstock['stockid'])){
			redirecting('assembly.php?screen=list');
		}
		
		$detailstock['quantity'] = number_format($detailstock['quantity'],2,",",".");
		$detailstock['minqty'] = number_format($detailstock['minqty'],2,",",".");
		$detailstock['buyprice'] = number_format($detailstock['buyprice'],2,",",".");
		$detailstock['sellprice'] = number_format($detailstock['sellprice'],2,",",".");
		$detailstock['remaining'] = number_format($detailstock['remaining'],2,",",".");
		if (!empty($detailstock['expdate'])){
			$detailstock['expdate'] = date("d-m-Y",$detailstock['expdate']);
		}
		$stock->setCode($detailstock['stockcode']);
		$dballpartno = $stock->getAllPartNo();
		$arrpartno = array();
		if (sizeof($dballpartno) > 0){
			foreach ($dballpartno as $dbap){
				array_push($arrpartno,$dbap['partno']);
			}
			$allpartno = implode("\r\n",$arrpartno);
		}
		$selbrand = urlencode($detailstock['brandcode']);
		$seltype = urlencode($detailstock['typecode']);
		$sellocation = urlencode($detailstock['locationcode']);
		$selunit = urlencode($detailstock['unitcode']);
		$selstgr = urlencode($detailstock['stgrcode']);
		
		$detailstock = array_map("htmlspecialchars",$detailstock);
	}

	if ($_POST['submits'] == 'Tambah' && $useraccess['add_assembly']){
		$_POST['quantity'] = togglenumber($_POST['quantity'],'calculate');
		$_POST['minqty'] = togglenumber($_POST['minqty'],'calculate');
		$_POST['buyprice'] = togglenumber($_POST['buyprice'],'calculate');
		$_POST['sellprice'] = togglenumber($_POST['sellprice'],'calculate');
		$lastid = $stock->saveStock($_POST['stockcode'],$_POST['standardname'],$_POST['generalname'],$_POST['brandcode'],$_POST['typecode'],$_POST['partno'],$_POST['size'],$_POST['locationcode'],$_POST['stgrcode'],$_POST['quantity'],$_POST['minqty'],$_POST['buyprice'],$_POST['sellprice'],$_POST['unitcode'],$_POST['expdate'],1,$_POST['stockstatus'],$userid);

		if ($lastid != false){
			$assembly->setCode($_POST['stockcode']);
			$assembly->saveAssembly();
			
			$arrpostdel = explode(",",$_POST['assemblybox_rowsdeleted']);
			$arrpost = explode(",",$_POST['assemblybox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				$stock->setId("");
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["assemblybox_".$arrpost[$x]."_0"])){
						$checkchars = strpos($_POST["assemblybox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["assemblybox_".$arrpost[$x]."_0"] = substr($_POST["assemblybox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["assemblybox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["assemblybox_".$arrpost[$x]."_5"] = togglenumber($_POST["assemblybox_".$arrpost[$x]."_5"],'calculate');
												
						if ($_POST["assemblybox_".$arrpost[$x]."_6"] == $getunit['lunit']){
							$quantity = $_POST["assemblybox_".$arrpost[$x]."_5"];
						}
						else if ($_POST["assemblybox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["assemblybox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
						}
						
						$assembly->saveDetailAssembly($_POST["assemblybox_".$arrpost[$x]."_0"],$_POST["assemblybox_".$arrpost[$x]."_1"],$_POST["assemblybox_".$arrpost[$x]."_2"],$_POST["assemblybox_".$arrpost[$x]."_3"],$_POST["assemblybox_".$arrpost[$x]."_4"],$_POST["assemblybox_".$arrpost[$x]."_5"],$_POST["assemblybox_".$arrpost[$x]."_6"],$quantity,$unitquantity,$getstock['unitcode']);
					}
				}
			}
		}
		
		redirecting("assembly.php?id=".$lastid);
	}
	else if ($_POST['submits'] == 'Ubah' && $useraccess['edit_assembly']){
		$_POST['quantity'] = togglenumber($_POST['quantity'],'calculate');
		$_POST['minqty'] = togglenumber($_POST['minqty'],'calculate');
		$_POST['buyprice'] = togglenumber($_POST['buyprice'],'calculate');
		$_POST['sellprice'] = togglenumber($_POST['sellprice'],'calculate');
		$results = $stock->updateStock($_POST['stockcode'],$_POST['standardname'],$_POST['generalname'],$_POST['brandcode'],$_POST['typecode'],$_POST['partno'],$_POST['size'],$_POST['locationcode'],$_POST['stgrcode'],$_POST['quantity'],$_POST['minqty'],$_POST['buyprice'],$_POST['sellprice'],$_POST['unitcode'],$_POST['expdate'],$_POST['stockstatus'],$userid);
		$assembly->setCode($detailstock['stockcode']);
		$assembly->updateAssembly($_POST['stockcode']);
		if ($results != false){
			$allcomponent = $assembly->getAssemblyComponent();
			$arrpost = array();
			if (sizeof($allcomponent) > 0){
				foreach ($allcomponent as $aad){
					array_push($arrpost,'r-'.$aad['dsaid']);
				}
			}

			//deleted rows
			$arrpostdel = explode(",",$_POST['assemblybox_rowsdeleted']);
			$arrpostdelsz = sizeof($arrpostdel);
			if ($arrpostdelsz > 0){
				for ($x = 0; $x < $arrpostdelsz; $x++){
					if (!empty($arrpostdel[$x]) && in_array($arrpostdel[$x],$arrpost)){
						$assembly->setDetailId($arrpostdel[$x]);
						$assembly->deleteComponent();
					}
				}
			}		
			//edited rows
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				$stock->setId("");
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["assemblybox_".$arrpost[$x]."_0"])){
						$assembly->setDetailId($arrpost[$x]);
						
						$checkchars = strpos($_POST["assemblybox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["assemblybox_".$arrpost[$x]."_0"] = substr($_POST["assemblybox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["assemblybox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["assemblybox_".$arrpost[$x]."_5"] = togglenumber($_POST["assemblybox_".$arrpost[$x]."_5"],'calculate');
												
						if ($_POST["assemblybox_".$arrpost[$x]."_6"] == $getunit['lunit']){
							$quantity = $_POST["assemblybox_".$arrpost[$x]."_5"];
						}
						else if ($_POST["assemblybox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["assemblybox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
						}
						
						$assembly->updateDetailAssembly($_POST['stockcode'],$_POST["assemblybox_".$arrpost[$x]."_0"],$_POST["assemblybox_".$arrpost[$x]."_1"],$_POST["assemblybox_".$arrpost[$x]."_2"],$_POST["assemblybox_".$arrpost[$x]."_3"],$_POST["assemblybox_".$arrpost[$x]."_4"],$_POST["assemblybox_".$arrpost[$x]."_5"],$_POST["assemblybox_".$arrpost[$x]."_6"],$quantity,$unitquantity,$getstock['unitcode']);
					}
				}
			}
			//added rows
			unset($arrpost);
			$arrpost = explode(",",$_POST['assemblybox_rowsadded']);
			$arrpost = array_diff($arrpost,$arrpostdel);
			$arrpost = array_values($arrpost);
			$arrpostsz = sizeof($arrpost);
			if ($arrpostsz > 0){
				$assembly->setCode($_POST['stockcode']);
				$stock->setId("");
				for ($x = 0; $x < $arrpostsz; $x++){
					if (!empty($arrpost[$x]) && !empty($_POST["assemblybox_".$arrpost[$x]."_0"])){
						$checkchars = strpos($_POST["assemblybox_".$arrpost[$x]."_0"],"||");
						if ($checkchars !== false){
							$_POST["assemblybox_".$arrpost[$x]."_0"] = substr($_POST["assemblybox_".$arrpost[$x]."_0"],0,$checkchars);
						}
						
						$stock->setId("");
						$stock->setCode($_POST["assemblybox_".$arrpost[$x]."_0"]);
						$getstock = $stock->getFirstStock();
						$units->setCode($getstock['unitcode']);
						$getunit = $units->getunitDetail();
						$unitquantity = $getunit['lunit'];
						
						$_POST["assemblybox_".$arrpost[$x]."_5"] = togglenumber($_POST["assemblybox_".$arrpost[$x]."_5"],'calculate');
												
						if ($_POST["assemblybox_".$arrpost[$x]."_6"] == $getunit['lunit']){
							$quantity = $_POST["assemblybox_".$arrpost[$x]."_5"];
						}
						else if ($_POST["assemblybox_".$arrpost[$x]."_6"] == $getunit['funit']){
							$quantity = $_POST["assemblybox_".$arrpost[$x]."_5"] * $getunit['cvalue'];
						}
						
						$assembly->saveDetailAssembly($_POST["assemblybox_".$arrpost[$x]."_0"],$_POST["assemblybox_".$arrpost[$x]."_1"],$_POST["assemblybox_".$arrpost[$x]."_2"],$_POST["assemblybox_".$arrpost[$x]."_3"],$_POST["assemblybox_".$arrpost[$x]."_4"],$_POST["assemblybox_".$arrpost[$x]."_5"],$_POST["assemblybox_".$arrpost[$x]."_6"],$quantity,$unitquantity,$getstock['unitcode']);
					}
				}
			}
		}
		redirecting("assembly.php?id=".$_REQUEST['id']);
	}
	
	if ($_GET['do'] == 'delete' && $useraccess['delete_assembly']){
		$stock->deleteStock();
		
		$assembly->setCode($detailstock['stockcode']);
		$assembly->deleteAssembly();
		if ($_GET['from'] == 'firststock'){
			redirecting("firststock.php");
		}
		else{
			redirecting("assembly.php?screen=list");
		}
	}
	
	if ($_GET['screen'] == 'list'){
		
		if (empty($useraccess['view_assembly'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'assemblylist';
	}
	else{
		
		if (empty($useraccess['add_assembly']) && empty($useraccess['edit_assembly'])){
			redirecting('index.php');
		}
			
		$printtemplate = 'assembly';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>