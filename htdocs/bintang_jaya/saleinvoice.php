<?php
	require_once "global.php";
	
	require_once "class/Stock.php";
	require_once "class/units.php";
	require_once "class/customer.php";
	require_once "class/area.php";
	require_once "class/Sale.php";
	$stock = new Stock();
	$units = new unit();
	$customer = new customer();
	$area = new area();
	$sale = new Sale();

	if (empty($useraccess['view_sale'])){
		redirecting('index.php');
	}
	
	$saleno = $_GET['no'];
	$paper = $_GET['paper'];
	if (!empty($saleno)){
		$sale->setId($saleno);
		$headersale = $sale->getHeaderSale();
		$sale->setSaleNo($headersale['saleno']);
		$sale->setId("");
		$invoicedate = date("d-M-Y",$headersale['saledate']);
		$invoiceduedate = date("d-M-Y",$headersale['duedate']);
		
		$customer->setCode($headersale['customercode']);
		$customer->setDetailId($headersale['customeraddrid']);
		$getcustomer = $customer->getCustomerDetail();
		$area->setCode($getcustomer['areacode']);
		$dbarea = $area->getareaDetail();
		if (!empty($dbarea['areaname'])){
			$areaname = ' '.$dbarea['areaname'];
		}
		$customername = $getcustomer['customername'];
		$customeraddr = $getcustomer['address'].$areaname;
		$customertelp = $getcustomer['phone'];
		
		$totaldisc = $headersale['disc'] / 100 * $headersale['totals'];
		$totalafterdisc = $headersale['totals'] - $totaldisc;
		$totaltax = $headersale['tax'] / 100 * $totalafterdisc;
		$fdisc = number_format($totaldisc,2,",",".");
		$ftax = number_format($totaltax,2,",",".");
		$ftotal = number_format($headersale['totalsale'],2,",",".");
		
		$headersale = array_map("htmlspecialchars",$headersale);

		$headersale['saleno'] = $codest->convertcodes($headersale['saleno'],'replacements_sale',true);
		
		/* sale detail */
		if ($paper == 'contformbig'){
			$totalrow = 24;
		}
		else{
			$totalrow = 12;
		}
		$detailsale = $sale->getDetailSale();
		$totalsaledetail = sizeof($detailsale);
		$totalpage = ceil($totalsaledetail / $totalrow);
		if ($totalsaledetail > 0){
			$i = 0;
			$page = 0;
			if ($paper == 'contformsmall'){
				$sdatainvoice = gettemplate('saledatainvoicecontformsmall');
			}
			else if ($paper == 'contformbig'){
				$sdatainvoice = gettemplate('saledatainvoicecontformbig');
			}
			else{
				$sdatainvoice = gettemplate('saledatainvoice');
			}
			foreach ($detailsale as $ds){
				$units->setCode($ds['unitcode']);
				
				$ds = array_map("htmlspecialchars",$ds);
				
				$unitd = $units->getunitDetail();
				$inf = number_format(($i+1),0,",",".");
				$quantity = number_format($ds['quantityf'],2,",",".");
				$lunits = $ds['unitquantityf'];
				$salepartno = $ds['partno'];
				$salestockname = $ds['stockname'];
				$saleprice = number_format($ds['saleprice'],2,",",".");
				$saledisc = number_format($ds['disc'] / 100 * $ds['totals'],2,",",".");
				$totalsalead = number_format($ds['totalsalead'],2,",",".");
				if ($i % $totalrow == ($totalrow-1)){
					$page++;
				}
				eval("\$datainvoice .= \"$sdatainvoice\";");
				$i++;
			}
			$destrow = $totalpage * $totalrow;
			if ($i < $destrow){
				$inf = '&nbsp;';
				$quantity = '&nbsp;';
				$lunits = '&nbsp;';
				$salepartno = '&nbsp;';
				$salestockname = '&nbsp;';
				$saleprice = '&nbsp;';
				$saledisc = '&nbsp;';
				$totalsalead = '&nbsp;';
				for ($i; $i < $destrow; $i++){
					if ($i % $totalrow == ($totalrow-1)){
						$page++;
					}
					eval("\$datainvoice .= \"$sdatainvoice\";");
				}
			}
		}
	
		$headinclude = gettemplate('headinclude');
		eval("\$headinclude = \"$headinclude\";");

		$tmpl = gettemplate('saleinvoice');
		eval("\$template = \"$tmpl\";");
		echo $template;
	}
?>