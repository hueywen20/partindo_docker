<?php
	require_once "global.php";
	
	if (empty($useraccess['settings'])){
		redirecting('index.php');
	}
	
	if ($_POST['submits'] == 'Ubah' && !empty($_POST['grp'])){
		//save setting
		$setgroup = $_POST['grp'];
		$updating = '';
		foreach ($_POST as $key => $value){
			$rsdata = $db->fetch_one("SELECT data_type FROM settings WHERE varkey='".$key."' AND grouping='".$setgroup."'");

			$update = false;
			switch ($rsdata['data_type']){
				case 'numeric'	:
					if (ctype_digit($value) && isset($value))
						$update = true;
					break;
				case 'free' 	:
					$update = true;
					break;
				case 'decimal'	:
					if (is_numeric($value) && isset($value))
						$update = true;
					break;
			}
			if ($update){
				$db->query("UPDATE settings SET value='".$value."' WHERE varkey='".$key."' AND grouping='".$setgroup."'");
			}
		}
		redirecting("settings.php?s=".$setgroup);
	}
	
	$s = $_REQUEST['s'];
	if (empty($s)){
		$s = 'general';
	}

	$dbset = $db->fetch_all("SELECT * FROM settings WHERE grouping='".$s."' ORDER BY setting_order ASC");
	if (sizeof($dbset) > 0){
		foreach ($dbset as $rsset){
			$contentset .= '
				<tr>
					<td width="50%" align="left" valign="top">'.$rsset['phrase'].'</td>
					<td width="2%"></td>
					<td width="48%" align="left" valign="top">
			';
			switch ($rsset['input_type']){
				case 'text'		:
					$contentset .= '
						<input type="text" class="validate[required]" id="'.$rsset['varkey'].'" name="'.$rsset['varkey'].'" size="50" value="'.htmlspecialchars($rsset['value']).'">
					';
					break;
				case 'radio_yes_no'	:
					$contentset .= '
						<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="30" align="left"><input type="radio" class="validate[required]" id="radio_'.$rsset['sid'].'_yes" name="'.$rsset['varkey'].'" value="1"'.(($rsset['value']==1)?' checked':'').'></td>
							<td width="100" align="left"><label for="radio_'.$rsset['sid'].'_yes">Ya</label></td>
							<td width="30" align="left"><input type="radio" class="validate[required]" id="radio_'.$rsset['sid'].'_no" name="'.$rsset['varkey'].'" value="0"'.(($rsset['value']==0)?' checked':'').'></td>
							<td width="100" align="left"><label for="radio_'.$rsset['sid'].'_no">Tidak</label></td>
						</tr>
						</table>
					';
					break;
				case 'textarea'	:
					$contentset .= '
						<textarea class="validate[required]" id="'.$rsset['varkey'].'" name="'.$rsset['varkey'].'" rows="5" cols="50">'.htmlspecialchars($rsset['value']).'</textarea>
					';
					break;
			}
			$contentset .= '
					</td>
				</tr>
				<tr>
					<td width="100%" height="21" colspan="3"></td>
				</tr>
			';
		}
	}

	$srs = $db->fetch_all("SELECT * FROM settings GROUP BY grouping ORDER BY group_order ASC");
	if (sizeof($srs) > 0){
		$gset = '<select name="grp" onchange="loadpagefromcombo(this,\'settings.php?s=\')">';
		foreach ($srs as $rsrs){
			$gset .= '<option value="'.$rsrs['grouping'].'"'.(($rsrs['grouping']==$s)?' selected':'').'>'.$rsrs['groupingname'].'</option>';
		}
		$gset .= '</select>';
	}
	
	$headinclude = gettemplate('headinclude');
	eval("\$headinclude = \"$headinclude\";");

	$tmpl = gettemplate('settings');
	eval("\$template = \"$tmpl\";");
	echo $template;
?>