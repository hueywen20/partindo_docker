<?php
	require_once "global.php";
	
	if (empty($useraccess['report_activity'])){
		redirecting('index.php');
	}
	
	$printdate = date("d-M-Y / H:i:s");
	if (!empty($_POST['datestart']) && !empty($_POST['dateend'])){
		$printtemplate = 'reportactivity';
		$startdate = strtotime($_POST['datestart']);
		$enddate = strtotime($_POST['dateend'].' 23:59:59');
		
		$addsql = ' WHERE a.lastedited >= \''.$startdate.'\' AND a.lastedited <= \''.$enddate.'\'';
		if (!empty($_POST['userid'])){
			$addsql .= ' AND a.lasteditedby=\''.$_POST['userid'].'\'';
		}
		if ($_POST['activitytype'] == 'create'){
			$addsql .= ' AND a.lastedited = a.createddate';
		}
		else if ($_POST['activitytype'] == 'edit'){
			$addsql .= ' AND a.lastedited != a.createddate';
		}
		$orders = ' ORDER BY a.lastedited';
		
		$activityarray = array();
		
		/* area */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'area'){
			$dbarea = $db->fetch_all("SELECT a.*, u.username FROM area a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbarea) > 0){
				foreach ($dbarea as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Kota';
					$thedata['notes'] = $rest['areacode'].' - '.$rest['areaname'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* brand */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'brand'){
			$dbbrand = $db->fetch_all("SELECT a.*, u.username FROM brand a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbbrand) > 0){
				foreach ($dbbrand as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Merek';
					$thedata['notes'] = $rest['brandcode'].' - '.$rest['brandname'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* codes */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'codes'){
			$dbcodes = $db->fetch_all("SELECT a.*, u.username FROM codes a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbcodes) > 0){
				foreach ($dbcodes as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Kode';
					$thedata['notes'] = $rest['replacements'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* country */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'country'){
			$dbcountry = $db->fetch_all("SELECT a.*, u.username FROM country a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbcountry) > 0){
				foreach ($dbcountry as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Negara';
					$thedata['notes'] = $rest['countrycode'].' - '.$rest['countryname'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* customer */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'customer'){
			$dbcustomer = $db->fetch_all("SELECT a.*, u.username FROM customer a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbcustomer) > 0){
				foreach ($dbcustomer as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Customer';
					$thedata['notes'] = $rest['customercode'].' - '.$rest['customername'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headeradjustin */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'adjustin'){
			$dbain = $db->fetch_all("SELECT a.*, u.username FROM headeradjustin a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbain) > 0){
				foreach ($dbain as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Penyesuaian Stok ( + )';
					$thedata['notes'] = $rest['ainid'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headeradjustout */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'adjustout'){
			$dbaout = $db->fetch_all("SELECT a.*, u.username FROM headeradjustout a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbaout) > 0){
				foreach ($dbaout as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Penyesuaian Stok ( - )';
					$thedata['notes'] = $rest['aoutid'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headerbuy */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'buy'){
			$dbbuy = $db->fetch_all("SELECT a.*, u.username FROM headerbuy a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbbuy) > 0){
				foreach ($dbbuy as $rest){
					$getsupplier = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode='".$rest['suppliercode']."'");
					
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Pembelian';
					$thedata['notes'] = $rest['orderno'].'<br>( '.$getsupplier['suppliercode'].' - '.$getsupplier['suppliername'].' )';
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headerbuyr */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'buyr'){
			$dbbuyr = $db->fetch_all("SELECT a.*, u.username FROM headerbuyr a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbbuyr) > 0){
				foreach ($dbbuyr as $rest){
					$getsupplier = $db->fetch_one("SELECT * FROM supplier WHERE suppliercode='".$rest['suppliercode']."'");
					
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Retur Pembelian';
					$thedata['notes'] = $rest['buyrid'].'<br>( '.$getsupplier['suppliercode'].' - '.$getsupplier['suppliername'].' )';
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headerpaydebt */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'paydebt'){
			$dbpaydebt = $db->fetch_all("SELECT a.*, u.username FROM headerpaydebt a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbpaydebt) > 0){
				foreach ($dbpaydebt as $rest){
					$getsupplier = $db->fetch_one("SELECT * FROM supplier WHERE supplierid='".$rest['supplierid']."'");
				
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Pelunasan Hutang';
					$thedata['notes'] = $getsupplier['suppliercode'].' - '.$getsupplier['suppliername'].'<br>( '.date("d-m-Y",$rest['startdate']).' s/d '.date("d-m-Y",$rest['enddate']).' )';
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headerpayment */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'payment'){
			$dbpayment = $db->fetch_all("SELECT a.*, u.username FROM headerpayment a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbpayment) > 0){
				foreach ($dbpayment as $rest){
					$getcustomer = $db->fetch_one("SELECT * FROM customer WHERE customerid='".$rest['customerid']."'");
				
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Pelunasan Piutang';
					$thedata['notes'] = $getcustomer['customercode'].' - '.$getcustomer['customername'].'<br>( '.date("d-m-Y",$rest['startdate']).' s/d '.date("d-m-Y",$rest['enddate']).' )';
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headersale */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'sale'){
			$dbsale = $db->fetch_all("SELECT a.*, u.username FROM headersale a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbsale) > 0){
				foreach ($dbsale as $rest){
					$getcustomer = $db->fetch_one("SELECT * FROM customer WHERE customercode='".$rest['customercode']."'");
					
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Penjualan';
					$thedata['notes'] = $rest['saleno'].'<br>( '.$getcustomer['customercode'].' - '.$getcustomer['customername'].' )';
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* headersaler */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'saler'){
			$dbsaler = $db->fetch_all("SELECT a.*, u.username FROM headersaler a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbsaler) > 0){
				foreach ($dbsaler as $rest){
					$getcustomer = $db->fetch_one("SELECT * FROM customer WHERE customercode='".$rest['customercode']."'");
					
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Retur Penjualan';
					$thedata['notes'] = $rest['salerid'].'<br>( '.$getcustomer['customercode'].' - '.$getcustomer['customername'].' )';
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* location */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'location'){
			$dblocation = $db->fetch_all("SELECT a.*, u.username FROM location a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dblocation) > 0){
				foreach ($dblocation as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Lokasi';
					$thedata['notes'] = $rest['locationcode'].' - '.$rest['locationname'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* state */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'state'){
			$dbstate = $db->fetch_all("SELECT a.*, u.username FROM state a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbstate) > 0){
				foreach ($dbstate as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Propinsi';
					$thedata['notes'] = $rest['statecode'].' - '.$rest['statename'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* stock */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'stock'){
			$dbstock = $db->fetch_all("SELECT a.*, u.username FROM stock a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbstock) > 0){
				foreach ($dbstock as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Stok / Barang';
					$thedata['notes'] = $rest['stockcode'].' - '.$rest['generalname'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* stockgroup */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'stockgroup'){
			$dbstockg = $db->fetch_all("SELECT a.*, u.username FROM stockgroup a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbstockg) > 0){
				foreach ($dbstockg as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Grup Stok';
					$thedata['notes'] = $rest['stgrcode'].' - '.$rest['stgrname'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* supplier */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'supplier'){
			$dbsupplier = $db->fetch_all("SELECT a.*, u.username FROM supplier a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbsupplier) > 0){
				foreach ($dbsupplier as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Supplier';
					$thedata['notes'] = $rest['suppliercode'].' - '.$rest['suppliername'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* type */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'type'){
			$dbtype = $db->fetch_all("SELECT a.*, u.username FROM type a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbtype) > 0){
				foreach ($dbtype as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Tipe';
					$thedata['notes'] = $rest['typecode'].' - '.$rest['typename'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* units */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'units'){
			$dbunits = $db->fetch_all("SELECT a.*, u.username FROM units a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbunits) > 0){
				foreach ($dbunits as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Satuan';
					$thedata['notes'] = $rest['unitcode'].' - '.$rest['unitname'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* user */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'user'){
			$dbuser = $db->fetch_all("SELECT a.* FROM user a".$addsql.$orders);
			if (sizeof($dbuser) > 0){
				foreach ($dbuser as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'User';
					$thedata['notes'] = $rest['username'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		/* usergroup */
		if ($_POST['activity'] == 'all' || $_POST['activity'] == 'usergroup'){
			$dbug = $db->fetch_all("SELECT a.*, u.username FROM usergroup a INNER JOIN user u ON a.lasteditedby = u.id".$addsql.$orders);
			if (sizeof($dbug) > 0){
				foreach ($dbug as $rest){
					$thedata['times'] = $rest['lastedited'];
					$thedata['username'] = $rest['username'];
					$thedata['activity'] = (($rest['createddate'] == $rest['lastedited'])?'Tambah ':'Edit ').'Grup User';
					$thedata['notes'] = $rest['title'];
					$activityarray[] = $thedata;
					unset($thedata);
				}
			}
		}
		
		$listall = '';
		if (sizeof($activityarray) > 0){
			$alldatas = multisort($activityarray,'times','username','activity','notes');
			
			$datego = '';
			$ik = 1;
			foreach ($alldatas as $adts){
				$datenow = date("d-M-Y",$adts['times']);
				if ($datego != $datenow){
					if (!empty($listall)){
						$listall .= '
							</table></div>
						';
					}
					$listall .= '
						<div align="center" style="width: 760px; padding-bottom: 20px; border-bottom: 1px dotted #000">
						<div align="left"><h2>'.$datenow.'</h2></div>
						<table border="1" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th align="center" width="6%" bgcolor="#DEDEDE">NO</th>
							<th align="center" width="19%" bgcolor="#DEDEDE">TANGGAL / WAKTU</th>
							<th align="center" width="20%" bgcolor="#DEDEDE">USERNAME</th>
							<th align="center" width="20%" bgcolor="#DEDEDE">AKTIVITAS</th>
							<th align="center" width="35%" bgcolor="#DEDEDE">KETERANGAN</th>
						</tr>
					';
					$ik = 1;
					$datego = $datenow;
				}
				$listall .= '
					<tr>
						<td align="right" height="35" class="detailitem">'.number_format($ik,0,",",".").'</td>
						<td align="center" height="35" class="detailitem">'.date("d-M-Y / H:i:s",$adts['times']).'</td>
						<td align="left" height="35" class="detailitem">'.htmlspecialchars($adts['username']).'</td>
						<td align="left" height="35" class="detailitem">'.htmlspecialchars($adts['activity']).'</td>
						<td align="left" height="35" class="detailitem">'.$adts['notes'].'</td>
					</tr>
				';
				$ik++;
			}
			$listall .= '
				</table></div>
			';
		}
	}
	else{
		$printtemplate = 'reportactivityinit';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate($printtemplate);
	eval("\$template = \"$tmpl\";");
	echo $template;
?>