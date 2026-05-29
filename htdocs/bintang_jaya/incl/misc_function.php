<?php
	function checkBrowser($input) {
		$browsers = "mozilla msie gecko firefox ";
		$browsers.= "konqueror safari netscape navigator ";
		$browsers.= "opera mosaic lynx amaya omniweb";

		$browsers = split(" ", $browsers);

		$userAgent = strToLower( $_SERVER['HTTP_USER_AGENT']);

		$l = strlen($userAgent);
		for ($i=0; $i<count($browsers); $i++){
			$browser = $browsers[$i];
			$n = stristr($userAgent, $browser);
			if(strlen($n)>0){
				$version = "";
				$navigator = $browser;
				$j=strpos($userAgent, $navigator)+$n+strlen($navigator)+1;
				for (; $j<=$l; $j++){
					$s = substr ($userAgent, $j, 1);
					if (is_numeric($version.$s))
						$version .= $s;
					else
						break;
				}
			}
		}

		if (strpos($userAgent, 'linux')) {
			$platform = 'linux';
		}
		else if (strpos($userAgent, 'macintosh') || strpos($userAgent, 'mac platform x')) {
			$platform = 'mac';
		}
		else if (strpos($userAgent, 'windows') || strpos($userAgent, 'win32')) {
			$platform = 'windows';
		}

		if ($input==true) {
			return array(
			"browser"      => $navigator,
			"version"      => $version,
			"platform"     => $platform,
			"userAgent"    => $userAgent);
		}else{
			return "$navigator $version";
		}
	}

	function currentURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") 
		{
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	function checkstr($inputstr){
		if (empty($inputstr)){
			return '-';
		}
		return $inputstr;
	}
	
	function gettemplate($templatename){
		global $db;
		include "template/".$templatename.".php";
		
		$theme = parsingcode(addslashes($html));
		return $theme;
	}

	function parsingcode($theme){
		$theme = preg_replace("/<if criteria=\\\\\"(.*)\\\\\">/U","\".(($1)?(\"",$theme);
		$theme = preg_replace("/<else>/","\"):(\"",$theme);
		$theme = preg_replace("/<\/if>/","\")).\"",$theme);
		$theme = preg_replace("/<\/endif>/","\"):(\"\")).\"",$theme);

		$theme = preg_replace("/\\\\\'/","'",$theme);

		return $theme;
	}

	function redirecting($url){
		header("Location: ".$url);
		exit();
	}

	function formatnumber($number,$thsep,$decsep){
		$separator = $thsep;
		if (strpos($number,'.')){
			$decimals = substr($number,strpos($number,'.')+1);
			$number = substr($number,0,strpos($number,'.'));
		}
		if (strlen($number) > 3) {
			$mod = strlen($number) % 3;
			$output = ($mod > 0 ? (substr($number,0,$mod)) : '');
			for ($i=0 ; $i < floor(strlen($number) / 3); $i++) {
				if (($mod == 0) && ($i == 0))
					$output .= substr($number,$mod + 3 * $i, 3);
				else
					$output .= $separator.substr($number,$mod + 3 * $i, 3);
			}
			if (!empty($decimals))
				$output .= $decsep.$decimals;
			return $output;
		}
		else{
			if (!empty($decimals))
				$number .= $decsep.$decimals;
			return $number;
		}
	}
	
	function togglenumber($strnumber,$mode){
		if ($mode == 'print'){
			$strnumber = str_replace('.','*',$strnumber);
			$strnumber = str_replace(',','.',$strnumber);
			$strnumber = str_replace('*',',',$strnumber);
		}
		else if ($mode == 'calculate'){
			$strnumber = str_replace('.','',$strnumber);
			$strnumber = str_replace(',','.',$strnumber);
		}
		return $strnumber;
	}
	
	function checkdecimal($strnumber){
		if (strstr($strnumber,'.')){
			$behinddecimal = substr($strnumber,strpos($strnumber,'.')+1);
			$isdec = false;
			for ($i = 0; $i < strlen($behinddecimal); $i++){
				if ($behinddecimal[$i] != '0'){
					$isdec = true;
					break;
				}
			}
			if ($isdec){
				$strnumber = rtrim(rtrim($strnumber, '0'), '.');
			}
			else{
				$strnumber = str_replace('.'.$behinddecimal,'',$strnumber);
			}
		}
		return $strnumber;
	}

	function generatepagelink($page,$counts){
		if ($counts > 1){
			$navp = '<a class="pagenavs" href="javascript:ajaxfromcookie('.($page-1).')" title="Halaman Sebelumnya">&lt;</a>&nbsp;';
			$navn = '<a class="pagenavs" href="javascript:ajaxfromcookie('.($page+1).')" title="Halaman Selanjutnya">&gt;</a>';
			$navfirst = '<a class="pagenavs" href="javascript:ajaxfromcookie(1)" title="Halaman Pertama">&lt;&lt;</a>&nbsp;&nbsp;';
			$navlast = '&nbsp;&nbsp;<a class="pagenavs" href="javascript:ajaxfromcookie('.($counts).')" title="Halaman Terakhir">&gt;&gt;</a>';
			if ($page == 1){
				$navp = '';
				$navfirst = '';
			}
			else if ($page == $counts){
				$navn = '';
				$navlast = '';
			}

			$pagelink .= $navfirst;
			$pagelink .= $navp;
			$left = $page-5;
			$right = $page+5;
			if ($left<1)
			{
				$right += abs($left)+1;
				$left=1;
			}
			if ($right>$counts)
			{
				$left += ($counts-$right);
				if ($left<1)
					$left=1;
				$right=$counts;
			}
			if ($left > 1)
				$pagelink .= '...&nbsp;';
			for ($l = $left; $l <= $right; $l++)
			{
				if ($l==$page)
					$pagelink .= '
						<span class="activepagenavs">'.$l.'</span>&nbsp;
					';
				else{
					$pagelink .= '
						<a class="pagenavs" href="javascript:ajaxfromcookie('.$l.')""><b>'.$l.'</b></a>&nbsp;
					';					
				}
			}
			if ($right < $counts)
				$pagelink .= '... &nbsp;&nbsp;';
			$pagelink .= $navn;
			$pagelink .= $navlast;
			
			$pagelink = '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;'.$pagelink;
		}
		else{
			$pagelink = '';
		}

		return $pagelink;
	}

	function _getip($var)
	{
		global $REMOTE_ADDR, $HTTP_X_FORWARDED_FOR, $HTTP_X_FORWARDED, $HTTP_FORWARDED_FOR, $HTTP_FORWARDED, $HTTP_VIA, $HTTP_X_COMING_FROM, $HTTP_COMING_FROM, $HTTP_SERVER_VARS, $HTTP_ENV_VARS;
		global ${$var};
		if (empty(${$var}))
		{
			if (!empty($_SERVER) && isset($_SERVER[$var])) ${$var} = $_SERVER[$var];
			else if (!empty($_ENV) && isset($_ENV[$var]))	${$var} = $_ENV[$var];
			else if (!empty($HTTP_SERVER_VARS) && isset($HTTP_SERVER_VARS[$var]))	${$var} = $HTTP_SERVER_VARS[$var];
			else if (!empty($HTTP_ENV_VARS) && isset($HTTP_ENV_VARS[$var]))	${$var} = $HTTP_ENV_VARS[$var];
			else if (@getenv($var))	${$var} = getenv($var);
		}
	}

	function ip2int($ip)
	{
	  $a = explode(".", $ip);
	  return $a[0] * 256 * 256 * 256+$a[1] * 256 * 256+$a[2] * 256+$a[3];
	}

	function ip()
	{
		global $REMOTE_ADDR, $HTTP_X_FORWARDED_FOR, $HTTP_X_FORWARDED, $HTTP_FORWARDED_FOR, $HTTP_FORWARDED, $HTTP_VIA, $HTTP_X_COMING_FROM, $HTTP_COMING_FROM, $HTTP_SERVER_VARS, $HTTP_ENV_VARS;
		_getip('REMOTE_ADDR');
		_getip('HTTP_X_FORWARDED_FOR');
		_getip('HTTP_X_FORWARDED');
		_getip('HTTP_FORWARDED_FOR');
		_getip('HTTP_FORWARDED');
		_getip('HTTP_VIA');
		_getip('HTTP_X_COMING_FROM');
		_getip('HTTP_COMING_FROM');
		if (!empty($REMOTE_ADDR))	$direct_ip = $REMOTE_ADDR;
		$proxy_ip = '';
		if (!empty($HTTP_X_FORWARDED_FOR)) $proxy_ip = $HTTP_X_FORWARDED_FOR;
		else if (!empty($HTTP_X_FORWARDED))	$proxy_ip = $HTTP_X_FORWARDED;
		else if (!empty($HTTP_FORWARDED_FOR))	$proxy_ip = $HTTP_FORWARDED_FOR;
		else if (!empty($HTTP_FORWARDED))	$proxy_ip = $HTTP_FORWARDED;
		else if (!empty($HTTP_VIA))	$proxy_ip = $HTTP_VIA;
		else if (!empty($HTTP_X_COMING_FROM))	$proxy_ip = $HTTP_X_COMING_FROM;
		else if (!empty($HTTP_COMING_FROM))	$proxy_ip = $HTTP_COMING_FROM;

		if (empty($proxy_ip))	return $direct_ip;
		else
		{
			$is_ip = ereg('^([0-9]{1,3}\.){3,3}[0-9]{1,3}',$proxy_ip,$regs);
			if ($is_ip && (count($regs) > 0))	return $regs[0];
			else return '0.0.0.0';
		}
	}

	function getIP(){
		//return ip2int(ip());
		return ip();
	}
	
	function handlepage($p,$totalp){
		if ($p == 'last' || $p > $totalp){
			$page = $totalp;
		}
		else if (empty($p) || $p < 1){
			$page = 1;
		}
		else{
			$page = $p;
		}
		if (empty($page)){
			$page = 1;
		}
		return $page;
	}
	
	function prepareHTML($html){
		$html = addslashes($html);
	   $html = preg_replace("/\\\\\'/","'",$html);
		return $html;
	}
	
	function getSession($id){
		session_start();
		return $_SESSION[$id];
	}
	
	function checkexist($uniq,$field,$table,$condition = ''){
		global $db;
		$dbcheck = $db->fetch_one("SELECT ".$field." FROM ".$table." WHERE ".$field."='".$uniq."'".$condition);
		if (sizeof($dbcheck) > 0)
			return true;
		else
			return false;
	}
   
   function condition($condition, $true, $false){
      return $condition ? $true : $false;
   }
   
   # $data => array of database result
   # $header => array of table's header
   # $width => array of the width of the table's component
   # $align => array of the align of the table's component
   # $column => array of the db column to be shown
   function generateTable($data, $header, $width, $align, $column, $numberPerPage, $message, $id = '1', $javascriptId = '', $action = ''){
      if (empty($data)){
         $html = "<div class='simple_warning_message'>$message</div>";
      }
      else{
         $numberPerPage = ($numberPerPage < 1 ? 10 : $numberPerPage);
         
         $w = array();
         
         $html .= '
            <style type="text/css">';
               
         for ($i = 0; $i < sizeof($width); $i++){
            $html .= '
               .width_' . $width[$i] . '{
                  width: ' . $width[$i] . 'px;
               }
               ';
            $w[$i] = 'width_' . $width[$i];
         }
         
         
         $html .= '
            </style>
            ';
            
         $totalData = sizeof($data);
         
         $totalPage = $totalData % $numberPerPage > 0 ? ((int)($totalData / $numberPerPage)) + 1 : $totalData / $numberPerPage;
         
         $txtPage = 'txt_page';
         $pageTextField = "<input id='$txtPage' class='simple_textbox width_50' type='text' />";
         
         $previousPage = '<< Sebelumnya';
         $nextPage = 'Selanjutnya >>';
         
         $txtShowButton = 'btn_show';
         $showButton = "<input id='$txtShowButton' class='simple_button' value='Tampil' type='button' onclick='" . (!empty($javascriptId) ? $javascriptId . '.' : '') . "gotoPage(\"$txtPage\")' />";
         
         $html .= '
            <div class="height_30">
               Halaman <span id="current_page">1</span> dari <span id="total_page">' . $totalPage . '</span>
               <a id="previous_page" href="javascript:' . (!empty($javascriptId) ? $javascriptId . '.' : '') . 'previousPage()">' . $previousPage . '</a>  <a id="next_page" href="javascript:' . (!empty($javascriptId) ? $javascriptId . '.' : '') . 'nextPage()">' . $nextPage . '</a>
               &nbsp;&nbsp;&nbsp;|
               Halaman ' . $pageTextField . ' &nbsp; ' . $showButton . '
            </div>
            <div class="clear_both"></div>
            ';
            
         $tableNo = 1;
         $row = 0;
         
         for ($i = 0; $i < sizeof($data); $i++){
            $j = 0;
            
            if ($i % $numberPerPage == 0){
               $row = 0;
               
               if ($i != 0){
                  $html .= '
                  </div>';
               }
               
               $html .= '
                  <div id="table_' . $id . '_' . ($tableNo++) . '">
                     <div class="table_header">';
                  
               for ($k = 0; $k < sizeof($header); $k++){
                  $html .= '
                        <div class="table_th ' . $w[$k] . '">
                           ' . strtoupper($header[$k]) . '
                        </div>
                        ';
               }
                  
               $html .= '
                     </div>
                     <div class="clear_both"></div>
                  ';
            }
            
            
            // set colour and number of the row
            $html .= '
               <div class="' . ((++$row) % 2 == 0 ? 'table_td_even' : 'table_td_odd') . '">
                  <div class="' . $w[$j] . ' text_' . $align[$j++] . '">
                     ' . ($i + 1) . '
                  </div>
               ';

            $key = 0;
            for ($key = 0; $key < sizeof($column); $key++){
               
               if (strpos($column[$key], 'date') > -1) {
                  $dt = date('d-m-Y', $data[$i][$column[$key]]);
               }
               elseif (strpos($column[$key], 'amount') > -1 || strpos($column[$key], 'price') > -1){
                  $dt = 'Rp. ' . number_format($data[$i][$column[$key]], 2, ',', '.');
               }
               else{
                  $dt = $data[$i][$column[$key]];
               }
               
               $html .= '
                  <div class="' . $w[$j] . ' text_' . $align[$j++] . '">
                     ' . $dt . '
                  </div>
                  ';
            }
            
            if ($action != ''){
               $keyAction = array('action', 'onclick', 'arguments');
               $l = 0;
               
               foreach ($action as $key => $dt){
                  switch ($key){
                     case $keyAction[0]:
                        $text = $dt;
                        break;
                     case $keyAction[1]:
                        $javascript = $dt;
                        break;
                     case $keyAction[2]:
                        $javascript .= '(\'' . $data[$i][$dt] . '\');';
                        break;
                  }
               }
               
               if (strpos($javascript, ')') < 0){
                  $javascript .= '()';
               }
               
               $html .= '
                  <div class="' . $w[$j] . ' text_' . $align[$j++] . '">
                     <a href="javascript:' . $javascript . '">' . $text . '</a>
                  </div>
                  ';
            }
            
/*
            foreach ($data[$i] as $key => $dt){
               $html .= '
                  <div class="' . $w[$j] . ' text_' . $align[$j++] . '">
                     ' . (strpos($key, 'date') > -1 ? date('d-m-Y', $dt) : $dt) . '
                  </div>
                  ';
            }
*/

            $html .= '
                </div>
                <div class="clear_both"></div>
                   ';
         }
         
         $html .= '
            </div>
            ';
      }
      return $html;
   }
   
   function getClassVariable($text, $splitter){
      for ($i = 1; $i < strlen($text); $i++){
         if ($text[$i] == $splitter){
            $text[$i + 1] = strtoupper($text[$i + 1]);
         }
      }
      $text = str_replace($splitter, '', $text);
      
      return $text;
   }
   
   function getPrice($price){
      return number_format($price , 2, ',' , '.');
   }
   
   function getClassFunction($text, $splitter){
      $text = strtoupper($text[0]) . getClassVariable(substr($text, 1), $splitter);
      
      return $text;
   }

	function multisort($array, $sort_by, $key1, $key2=NULL, $key3=NULL, $key4=NULL, $key5=NULL, $key6=NULL, $key7=NULL, $key8=NULL, $key9=NULL, $key10=NULL, $key11=NULL, $key12=NULL, $key13=NULL, $key14=NULL){
		// sort by ?
		foreach ($array as $pos =>  $val)
			$tmp_array[$pos] = $val[$sort_by];
		asort($tmp_array);
	   
		// display however you want
		foreach ($tmp_array as $pos =>  $val){
			$return_array[$pos][$sort_by] = $array[$pos][$sort_by];
			$return_array[$pos][$key1] = $array[$pos][$key1];
			if (isset($key2)){
				$return_array[$pos][$key2] = $array[$pos][$key2];
				}
			if (isset($key3)){
				$return_array[$pos][$key3] = $array[$pos][$key3];
				}
			if (isset($key4)){
				$return_array[$pos][$key4] = $array[$pos][$key4];
				}
			if (isset($key5)){
				$return_array[$pos][$key5] = $array[$pos][$key5];
				}
			if (isset($key6)){
				$return_array[$pos][$key6] = $array[$pos][$key6];
				}
			if (isset($key7)){
				$return_array[$pos][$key7] = $array[$pos][$key7];
				}
			if (isset($key8)){
				$return_array[$pos][$key8] = $array[$pos][$key8];
				}
			if (isset($key9)){
				$return_array[$pos][$key9] = $array[$pos][$key9];
				}
			if (isset($key10)){
				$return_array[$pos][$key10] = $array[$pos][$key10];
				}
			if (isset($key11)){
				$return_array[$pos][$key11] = $array[$pos][$key11];
				}
			if (isset($key12)){
				$return_array[$pos][$key12] = $array[$pos][$key12];
				}
			if (isset($key13)){
				$return_array[$pos][$key13] = $array[$pos][$key13];
				}
			if (isset($key14)){
				$return_array[$pos][$key14] = $array[$pos][$key14];
				}
		}
		return $return_array;
    }
	
	function terbilang($x) {
		$x = abs($x);
		$angka = array("", "satu", "dua", "tiga", "empat", "lima",
		"enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$result = "";
		if ($x <12) {
			$result = " ". $angka[$x];
		} else if ($x <20) {
			$result = terbilang($x - 10). " belas";
		} else if ($x <100) {
			$result = terbilang($x/10)." puluh". terbilang($x % 10);
		} else if ($x <200) {
			$result = " seratus" . terbilang($x - 100);
		} else if ($x <1000) {
			$result = terbilang($x/100) . " ratus" . terbilang($x % 100);
		} else if ($x <2000) {
			$result = " seribu" . terbilang($x - 1000);
		} else if ($x <1000000) {
			$result = terbilang($x/1000) . " ribu" . terbilang($x % 1000);
		} else if ($x <1000000000) {
			$result = terbilang($x/1000000) . " juta" . terbilang($x % 1000000);
		} else if ($x <1000000000000) {
			$result = terbilang($x/1000000000) . " milyar" . terbilang(fmod($x,1000000000));
		} else if ($x <1000000000000000) {
			$result = terbilang($x/1000000000000) . " trilyun" . terbilang(fmod($x,1000000000000));
		}      
			return $result;
	}
	
	function discq($quantity){
		global $statususer,$discount;
		
		if ($statususer == 1){
			$quantity = floor((100-$discount['extradisc'])/100 * $quantity);
			if ($quantity < 1){
				$quantity = 1;
			}
		}
		
		return $quantity;
	}
	
	function getdaysinmonth($month,$year){
		if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12){
			return 31;
		}
		else if ($month == 4 || $month == 6 || $month == 9 || $month == 11){
			return 30;
		}
		else{
			if ($year % 4 == 0){
				return 29;
			}
			else{
				return 28;
			}
		}
	}
	
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
	
	function exp_to_dec($float_str)
	// formats a floating point number string in decimal notation, supports signed floats, also supports non-standard formatting e.g. 0.2e+2 for 20
	// e.g. '1.6E+6' to '1600000', '-4.566e-12' to '-0.000000000004566', '+34e+10' to '340000000000'
	// Author: Bob
	{
		// make sure its a standard php float string (i.e. change 0.2e+2 to 20)
		// php will automatically format floats decimally if they are within a certain range
		$float_str = (string)((float)($float_str));

		// if there is an E in the float string
		if (($pos = strpos(strtolower($float_str), 'e')) !== false)
		{
			// get either side of the E, e.g. 1.6E+6 => exp E+6, num 1.6
			$exp = substr($float_str, $pos+1);
			$num = substr($float_str, 0, $pos);

			// strip off num sign, if there is one, and leave it off if its + (not required)
			if((($num_sign = $num[0]) === '+') || ($num_sign === '-')) $num = substr($num, 1);
			else $num_sign = '';
			if($num_sign === '+') $num_sign = '';

			// strip off exponential sign ('+' or '-' as in 'E+6') if there is one, otherwise throw error, e.g. E+6 => '+'
			if((($exp_sign = $exp[0]) === '+') || ($exp_sign === '-')) $exp = substr($exp, 1);
			else trigger_error("Could not convert exponential notation to decimal notation: invalid float string '$float_str'", E_USER_ERROR);

			// get the number of decimal places to the right of the decimal point (or 0 if there is no dec point), e.g., 1.6 => 1
			$right_dec_places = (($dec_pos = strpos($num, '.')) === false) ? 0 : strlen(substr($num, $dec_pos+1));
			// get the number of decimal places to the left of the decimal point (or the length of the entire num if there is no dec point), e.g. 1.6 => 1
			$left_dec_places = ($dec_pos === false) ? strlen($num) : strlen(substr($num, 0, $dec_pos));

			// work out number of zeros from exp, exp sign and dec places, e.g. exp 6, exp sign +, dec places 1 => num zeros 5
			if($exp_sign === '+') $num_zeros = $exp - $right_dec_places;
			else $num_zeros = $exp - $left_dec_places;

			// build a string with $num_zeros zeros, e.g. '0' 5 times => '00000'
			$zeros = str_pad('', $num_zeros, '0');

			// strip decimal from num, e.g. 1.6 => 16
			if($dec_pos !== false) $num = str_replace('.', '', $num);

			// if positive exponent, return like 1600000
			if($exp_sign === '+') return $num_sign.$num.$zeros;
			// if negative exponent, return like 0.0000016
			else return $num_sign.'0.'.$zeros.$num;
		}
		// otherwise, assume already in decimal notation and return
		else return $float_str;
	}
	
	function wraptheword($strings,$limits,$separators){
		$returnstr = '';
		if (strlen($strings) <= $limits){
			$returnstr = $strings;
		}
		else{
			while (true){
				if (strlen($strings) <= $limits){
					$returnstr .= $strings;
					break;
				}
				else{
					$subs = substr($strings,0,$limits);
					$strings = substr($strings,$limits);
					
					$returnstr .= $subs.$separators;
				}
			}
		}
		
		return $returnstr;
	}
?>