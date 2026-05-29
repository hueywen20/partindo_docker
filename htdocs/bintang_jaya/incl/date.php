<?php

$buydate = 1328720400;
$completedate = 1331053200;

$printbuydate =  date("d-m-Y",$buydate);
$printcompletedate = date("d-m-Y",$completedate);

	function getdifferentdate($tgl1,$tgl2,$mode='day'){

	// memecah tanggal untuk mendapatkan bagian tanggal, bulan dan tahun
	// dari tanggal pertama

	$pecah1 = explode("-", $tgl1);
	$date1 = $pecah1[0];
	$month1 = $pecah1[1];
	$year1 = $pecah1[2];

	
	// memecah tanggal untuk mendapatkan bagian tanggal, bulan dan tahun
	// dari tanggal kedua
	$pecah2 = explode("-", $tgl2);
	$date2 = $pecah2[0];
	$month2 = $pecah2[1];
	$year2 =  $pecah2[2];

	// menghitung JDN dari masing-masing tanggal

	$jd1 = GregorianToJD($month1, $date1, $year1);
	$jd2 = GregorianToJD($month2, $date2, $year2);

	$jd3 = GregorianToJD($month1, $date1, $year1);
	$jd4 = GregorianToJD($month2, $date2, $year2);
	
	// hitung selisih hari kedua tanggal

	$selisihhari = $jd2 - $jd1;
	
	$selisihbln = ceil($selisih/30);
	if ($mode == 'day'){
	return $selisihhari;
	}
	else{
	return $selisihbln;
	}
	
	}
	
	
$diftime = getdifferentdate($printbuydate,$printcompletedate);



echo $printbuydate.' - '.$printcompletedate.' selisihnya '.$diftime.' Hari';

?>