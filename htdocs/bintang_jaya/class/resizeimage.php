<?php
	class photo{
		var $filename = array();
		var $image_dest = array();
		var $imagetypes = array(IMAGETYPE_JPEG=>'jpg', IMAGETYPE_PNG=>'png', IMAGETYPE_GIF=>'gif');
		var $type = array();
		var $fn = array();
		var $isOri = array();
		var $isMultiple = false;
		var $counter = 1;

		function photo($fname){
			$filename = $fname;
			for ($i = 0; $i < sizeof($filename); $i++){
				if (!file_exists($filename[$i])){
					$filename[$i] = 'img/no_image_available.png';
				}

				$info = getimagesize($filename[$i]);
				if ( $info ){
					if (!isset($this->imagetypes[$info[2]]) ){
						$filename[$i] = 'img/no_image_available.png';
					}
				}

				$this->setFile($filename[$i]);
			}
		}

		function setFile($fname){
			array_push($this->filename,$fname);
		}

		function imagecreatefrombmpfile($filename)
		{
		   if (! $f1 = fopen($filename,"rb")) return FALSE;

		   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
		   if ($FILE['file_type'] != 19778) return FALSE;

		   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
						 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
						 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
		   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
		   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
		   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
		   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
		   $BMP['decal'] = 4-(4*$BMP['decal']);
		   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

		   $PALETTE = array();
		   if ($BMP['colors'] < 16777216)
		   {
			$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
		   }

		   $IMG = fread($f1,$BMP['size_bitmap']);
		   $VIDE = chr(0);

		   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
		   $P = 0;
		   $Y = $BMP['height']-1;
		   while ($Y >= 0)
		   {
			$X=0;
			while ($X < $BMP['width'])
			{
			 if ($BMP['bits_per_pixel'] == 24)
				$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
			 elseif ($BMP['bits_per_pixel'] == 16)
			 {
				$COLOR = unpack("n",substr($IMG,$P,2));
				$COLOR[1] = $PALETTE[$COLOR[1]+1];
			 }
			 elseif ($BMP['bits_per_pixel'] == 8)
			 {
				$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
				$COLOR[1] = $PALETTE[$COLOR[1]+1];
			 }
			 elseif ($BMP['bits_per_pixel'] == 4)
			 {
				$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
				if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
				$COLOR[1] = $PALETTE[$COLOR[1]+1];
			 }
			 elseif ($BMP['bits_per_pixel'] == 1)
			 {
				$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
				if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
				elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
				elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
				elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
				elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
				elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
				elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
				elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
				$COLOR[1] = $PALETTE[$COLOR[1]+1];
			 }
			 else
				return FALSE;
			 imagesetpixel($res,$X,$Y,$COLOR[1]);
			 $X++;
			 $P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P+=$BMP['decal'];
		   }

		   fclose($f1);

		   return $res;
		}

		function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct, $type=''){ 
			if(!isset($pct)){ 
				return false; 
			} 
			
			if ($type != 'gif'){
				$pct /= 100; 
				// Get image width and height 
				$w = imagesx( $src_im ); 
				$h = imagesy( $src_im ); 
				// Turn alpha blending off 
				imagealphablending( $src_im, false ); 
				// Find the most opaque pixel in the image (the one with the smallest alpha value) 
				$minalpha = 127; 
				for( $x = 0; $x < $w; $x++ ) 
				for( $y = 0; $y < $h; $y++ ){ 
					$alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF; 
					if( $alpha < $minalpha ){ 
						$minalpha = $alpha; 
					} 
				} 
				//loop through image pixels and modify alpha for each 
				for( $x = 0; $x < $w; $x++ ){ 
					for( $y = 0; $y < $h; $y++ ){ 
						//get current alpha value (represents the TANSPARENCY!) 
						$colorxy = imagecolorat( $src_im, $x, $y ); 
						$alpha = ( $colorxy >> 24 ) & 0xFF; 
						//calculate new alpha 
						if( $minalpha !== 127 ){ 
							$alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha ); 
						} else { 
							$alpha += 127 * $pct; 
						} 
						//get the color index with new alpha 
						$alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha ); 
						//set pixel with the new color + opacity 
						if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){ 
							return false; 
						} 
					} 
				}
			}
			// The image copy 
			imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
		}
		
		function resizeImageP($w, $h, $merge = false, $bgimg=array(), $bgpositioning=array(), $attachimg=array(), $positioning=array(), $attw=0, $atth=0){
			for ($i = 0; $i < sizeof($this->filename); $i++){
				$info = @getimagesize($this->filename[$i]);
				if ( $info ){
					if ( isset($this->imagetypes[$info[2]]) ){
						if ($this->imagetypes[$info[2]] == 'jpg' || $this->imagetypes[$info[2]] == 'jpeg' || $this->imagetypes[$info[2]] == 'jpe') {
							$image = imagecreatefromjpeg($this->filename[$i]);
							array_push($this->type,'jpg');
						} else if ($this->imagetypes[$info[2]] == 'gif') {
							$image = imagecreatefromgif($this->filename[$i]);
							array_push($this->type,'gif');
						} else if ($this->imagetypes[$info[2]] == 'png') {
							$image = imagecreatefrompng($this->filename[$i]);
							array_push($this->type,'png');
						} else {
							$image = imagecreatefromgif('img/no_image_available.png');
							array_push($this->type,'gif');
						}
					}
					// Get new dimensions
					list($width_orig, $height_orig) = $info;
				}

				if ($width_orig <= $w && $height_orig <= $h){
					if (!$merge)
						array_push($this->image_dest,$image);
					array_push($this->fn,fopen($this->filename[$i],"r"));
					array_push($this->isOri,true);
				}
				else{
					$percentw = $w/$width_orig;
					$percenth = $h/$height_orig;
					if ($percentw > $percenth){
						$percent = $percenth;
						$rszwhat = 'height';
					}
					else{
						$percent = $percentw;
						$rszwhat = 'width';
					}
					$newwidth = $width_orig * $percent;
					$newheight = $height_orig * $percent;
					$image = $this->resizeImagePnew($image,$percent,$width_orig,$height_orig,$merge);
					
					if ($rszwhat == 'height'){
						if ($newwidth > $w){
							$imagetemp = @imagecreatetruecolor($newwidth, $newheight);
							imagecopy($imagetemp,$image,0,0,0,0,$newwidth,$newheight);
							$percentw = $w/$newwidth;
							//array_pop($this->image_dest[$i]);
							$image = $this->resizeImagePnew($imagetemp,$percentw,$newwidth,$newheight,$merge);
						}
					}
					else if ($rszwhat == 'width'){
						if ($newheight > $h){
							$imagetemp = @imagecreatetruecolor($newwidth, $newheight);
							imagecopy($imagetemp,$image,0,0,0,0,$newwidth,$newheight);
							$percenth = $h/$newheight;
							//array_pop($this->image_dest[$i]);
							$image = $this->resizeImagePnew($imagetemp,$percenth,$newwidth,$newheight,$merge);
						}
					}
					array_push($this->fn,null);
					array_push($this->isOri,false);
				}
				
				if ($merge){
					if (sizeof($bgimg) > 0){
						for ($m = 0; $m < sizeof($bgimg); $m++){
							$infobg = @getimagesize($bgimg[$m]);
							if ( $infobg ){
								if ( isset($this->imagetypes[$infobg[2]]) ){
									if ($this->imagetypes[$infobg[2]] == 'jpg' || $this->imagetypes[$infobg[2]] == 'jpeg' || $this->imagetypes[$infobg[2]] == 'jpe') {
										$imagebg = imagecreatefromjpeg($bgimg[$m]);
									} else if ($this->imagetypes[$infobg[2]] == 'gif') {
										$imagebg = imagecreatefromgif($bgimg[$m]);
									} else if ($this->imagetypes[$infobg[2]] == 'png') {
										$imagebg = imagecreatefrompng($bgimg[$m]);
									}
								}
							}
							if (!empty($imagebg)){
								switch ($bgpositioning[$m]){
									
									case 'top left' : $this->imagecopymerge_alpha($imagebg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image),100); break;
									case 'top center' : $this->imagecopymerge_alpha($imagebg, $image, (imagesx($imagebg)-imagesx($image))/2, 0, 0, 0, imagesx($image), imagesy($image),100); break;
									case 'top right' : $this->imagecopymerge_alpha($imagebg, $image, (imagesx($imagebg)-imagesx($image)), 0, 0, 0, imagesx($image), imagesy($image),100); break;
									case 'middle left' : $this->imagecopymerge_alpha($imagebg, $image, 0, (imagesy($imagebg)-imagesy($image))/2, 0, 0, imagesx($image), imagesy($image),100); break;
									case 'middle center' : $this->imagecopymerge_alpha($imagebg, $image, (imagesx($imagebg)-imagesx($image))/2, (imagesy($imagebg)-imagesy($image))/2, 0, 0, imagesx($image), imagesy($image),100); break;
									case 'middle right' : $this->imagecopymerge_alpha($imagebg, $image, (imagesx($imagebg)-imagesx($image)), (imagesy($imagebg)-imagesy($image))/2, 0, 0, imagesx($image), imagesy($image),100); break;
									case 'bottom left' : $this->imagecopymerge_alpha($imagebg, $image, 0, (imagesy($imagebg)-imagesy($image)), 0, 0, imagesx($image), imagesy($image),100); break;
									case 'bottom center' : $this->imagecopymerge_alpha($imagebg, $image, (imagesx($imagebg)-imagesx($image))/2, (imagesy($imagebg)-imagesy($image)), 0, 0, imagesx($image), imagesy($image),100); break;
									case 'bottom right' : $this->imagecopymerge_alpha($imagebg, $image, (imagesx($imagebg)-imagesx($image)), (imagesy($imagebg)-imagesy($image)), 0, 0, imagesx($image), imagesy($image),100); break;
									default : 
										$absbgpos = explode(" ",$bgpositioning[$m]);
										$this->imagecopymerge_alpha($imagebg, $image, $absbgpos[1], $absbgpos[0], 0, 0, imagesx($image), imagesy($image),100);
										break;
								}
								imagedestroy($image);
								$image = @imagecreatetruecolor($infobg[0], $infobg[1]);
								imagecopy($image,$imagebg,0,0,0,0,$infobg[0],$infobg[1]);								
								//echo 'a';
								imagedestroy($imagebg);
							}
						}
					}
					/*$imagebgtemp = @imagecreatetruecolor($infobg[0], $infobg[1]);
					imagecopy($imagebgtemp,$imagebg,0,0,0,0,$infobg[0],$infobg[1]);					
					$this->imagecopymerge_alpha($imagebgtemp, $image, $left+(imagesx($imagebgtemp)-imagesx($image))/2, (imagesy($imagebgtemp)-imagesy($image))/2, 0, 0, imagesx($image), imagesy($image),100); */
					
					if (sizeof($attachimg) > 0){
						//print_r($imagebg);
						//if (empty($imagebg)){
							$imagebg = @imagecreatetruecolor(imagesx($image), imagesy($image));
							imagecopy($imagebg,$image,0,0,0,0,$info[0],$info[1]);
						//}
						$infoatt = @getimagesize($attachimg[$i]);
						if ( $infoatt ){
							if ( isset($this->imagetypes[$infoatt[2]]) ){
								if ($this->imagetypes[$infoatt[2]] == 'jpg' || $this->imagetypes[$infoatt[2]] == 'jpeg' || $this->imagetypes[$infoatt[2]] == 'jpe') {
									$imageatt = imagecreatefromjpeg($attachimg[$i]);
								} else if ($this->imagetypes[$infoatt[2]] == 'gif') {
									$imageatt = imagecreatefromgif($attachimg[$i]);
								} else if ($this->imagetypes[$infoatt[2]] == 'png') {
									$imageatt = imagecreatefrompng($attachimg[$i]);
								}
							}
							list($width_origatt, $height_origatt) = $infoatt;
						}
						if (!empty($imageatt)){
							//resize attachment
							if ($width_origatt > $attw || $height_origatt > $atth){
								$percentwatt = $attw/$width_origatt;
								$percenthatt = $atth/$height_origatt;
								if ($percentwatt > $percenthatt){
									$percentatt = $percenthatt;
									$rszwhatatt = 'height';
								}
								else{
									$percentatt = $percentwatt;
									$rszwhatatt = 'width';
								}
								$newwidthatt = $width_origatt * $percentatt;
								$newheightatt = $height_origatt * $percentatt;
								$imageatt = $this->resizeImagePnew($imageatt,$percentatt,$width_origatt,$height_origatt,true);
								
								if ($rszwhatatt == 'height'){
									if ($newwidthatt > $attw){
										$imagetempatt = @imagecreatetruecolor($newwidthatt, $newheightatt);
										imagecopy($imagetempatt,$imageatt,0,0,0,0,$newwidthatt,$newheightatt);
										$percentwatt = $attw/$newwidthatt;
										$imageatt = $this->resizeImagePnew($imagetempatt,$percentwatt,$newwidthatt,$newheightatt,true);
									}
								}
								else if ($rszwhatatt == 'width'){
									if ($newheightatt > $atth){
										$imagetempatt = @imagecreatetruecolor($newwidthatt, $newheightatt);
										imagecopy($imagetempatt,$imageatt,0,0,0,0,$newwidthatt,$newheightatt);
										$percenthatt = $atth/$newheightatt;
										$imageatt = $this->resizeImagePnew($imagetempatt,$percenthatt,$newwidthatt,$newheightatt,true);
									}
								}
							}
							
							$abspos = explode(" ",$positioning[$m]);
							if (strstr($positioning[$i],'left')){
								$halign = 0;
							}
							else if (strstr($positioning[$i],'center')){
								$halign = (imagesx($imagebg)-imagesx($imageatt))/2;
							}
							else if (strstr($positioning[$i],'right')){
								$halign = imagesx($imagebg)-imagesx($imageatt);
							}
							else{
								if ($abspos[1] < 0)
									$halign = imagesx($imagebg)-imagesx($imageatt)+$abspos[1];
								else
									$halign = $abspos[1];
							}
						
							if (strstr($positioning[$i],'top')){
								$valign = 0;
							}
							else if (strstr($positioning[$i],'middle')){
								$valign = (imagesy($imagebg)-imagesy($imageatt))/2;
							}
							else if (strstr($positioning[$i],'bottom')){
								$valign = imagesy($imagebg)-imagesy($imageatt);
							}
							else{
								if ($abspos[0] < 0)
									$valign = imagesy($imagebg)-imagesy($imageatt)+$abspos[0];
								else
									$valign = $abspos[0];
							}
							/*switch ($positioning[$m]){
								case 'top left' : $this->imagecopymerge_alpha($imagebg, $imageatt, 0, 0, 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'top center' : $this->imagecopymerge_alpha($imagebg, $imageatt, (imagesx($imagebg)-imagesx($imageatt))/2, 0, 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'top right' : $this->imagecopymerge_alpha($imagebg, $imageatt, (imagesx($imagebg)-imagesx($imageatt)), 0, 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'middle left' : $this->imagecopymerge_alpha($imagebg, $imageatt, 0, (imagesy($imagebg)-imagesy($imageatt))/2, 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'middle center' : $this->imagecopymerge_alpha($imagebg, $imageatt, (imagesx($imagebg)-imagesx($imageatt))/2, (imagesy($imagebg)-imagesy($imageatt))/2, 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'middle right' : $this->imagecopymerge_alpha($imagebg, $imageatt, (imagesx($imagebg)-imagesx($imageatt)), (imagesy($imagebg)-imagesy($imageatt))/2, 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'bottom left' : $this->imagecopymerge_alpha($imagebg, $imageatt, 0, (imagesy($imagebg)-imagesy($imageatt)), 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'bottom center' : $this->imagecopymerge_alpha($imagebg, $imageatt, (imagesx($imagebg)-imagesx($imageatt))/2, (imagesy($imagebg)-imagesy($imageatt)), 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								case 'bottom right' : $this->imagecopymerge_alpha($imagebg, $imageatt, (imagesx($imagebg)-imagesx($imageatt)), (imagesy($imagebg)-imagesy($imageatt)), 0, 0, imagesx($imageatt), imagesy($imageatt),100); break;
								default : 
									$abspos = explode(" ",$positioning[$m]);
									$this->imagecopymerge_alpha($imagebg, $imageatt, $abspos[1], $abspos[0], 0, 0, imagesx($imageatt), imagesy($imageatt),100);
									break;
							}*/
							
							$this->imagecopymerge_alpha($imagebg, $imageatt, $halign, $valign, 0, 0, imagesx($imageatt), imagesy($imageatt),100,$this->imagetypes[$infoatt[2]]);
							
							imagedestroy($imageatt);
						}
					}
					$this->type[$i] = 'jpeg';
					$this->isOri[$i] = false;
					array_push($this->image_dest,$imagebg);
				}
			}
		}

		//resizing image into percentage of original size
		function resizeImagePnew($image,$percent,$width,$height,$merge){
			$newwidth = $width*$percent;
			$newheight = $height*$percent;
		
			$image_dest = @imagecreatetruecolor($newwidth, $newheight);

			$transparencyIndex = imagecolortransparent($image);
			$transparencyColor = array('red' => 0, 'green' => 0, 'blue' => 0);
			
			if ($transparencyIndex == 0) {
				$transparencyColor = imagecolorsforindex($image, $transparencyIndex);
			}
			
			$transparencyIndex = imagecolorallocate($image_dest, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
			imageSaveAlpha($image_dest, true);
			ImageAlphaBlending($image_dest, false);
			imagefill($image_dest, 0, 0, $transparencyIndex);
			imagecolortransparent($image_dest, $transparencyIndex);

			imagecopyresampled($image_dest, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			
			if (!$merge)
				array_push($this->image_dest,$image_dest);
				
			return $image_dest;
		}
		
		function mergeimage($secondimg,$top,$left){
			$info = getimagesize($secondimg);
			if ( $info ){
				if ( isset($this->imagetypes[$info[2]]) ){
					if ($this->imagetypes[$info[2]] == 'jpg' || $this->imagetypes[$info[2]] == 'jpeg' || $this->imagetypes[$info[2]] == 'jpe') {
						$imagebgo = imagecreatefromjpeg($secondimg);
						$imagebg = imagecreatefromjpeg($secondimg);
					} else if ($this->imagetypes[$info[2]] == 'gif') {
						$imagebgo = imagecreatefromgif($secondimg);
						$imagebg = imagecreatefromgif($secondimg);
					} else if ($this->imagetypes[$info[2]] == 'png') {
						$imagebgo = imagecreatefrompng($secondimg);
						$imagebg = imagecreatefrompng($secondimg);
					}
					for ($i = 0; $i < sizeof($this->filename); $i++){
						$imagebg = $imagebgo;
						$this->imagecopymerge_alpha($imagebg, $this->image_dest[$i], $left+(imagesx($imagebg)-imagesx($this->image_dest[$i]))/2, (imagesy($imagebg)-imagesy($this->image_dest[$i]))/2, 0, 0, imagesx($this->image_dest[$i]), imagesy($this->image_dest[$i]),100); 
						$this->image_dest[$i] = $imagebg;
						$this->type[$i] = 'jpeg';
						$this->isOri[$i] = false;
						//imagedestroy($imagebg);
					}
				}
			}			
		}
		
		function combineimage($totalimage,$width,$height,$fixedframe,$halign='',$valign=''){
			if (isset($this->image_dest)){
				$image_dest = @imagecreatetruecolor($width, $height);

				$transparencyIndex = imagecolortransparent($this->image_dest[0]);
				$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

				if ($transparencyIndex == 0) {
					$transparencyColor = imagecolorsforindex($image, $transparencyIndex);
				}

				$transparencyIndex = imagecolorallocate($image_dest, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
				imagefill($image_dest, 0, 0, $transparencyIndex);
				imagecolortransparent($image_dest, $transparencyIndex);
				if (!$fixedframe){
					$startpos = 0;
					for ($i = 0; $i < sizeof($this->filename); $i++){
						$thisimgwidth = imagesx($this->image_dest[$i]);
						$thisimgheight = imagesy($this->image_dest[$i]);
						imagecopy($image_dest, $this->image_dest[$i], $startpos, 0, 0, 0, $thisimgwidth, $thisimgheight); 
						$startpos += $thisimgwidth;
					}
				}
				else{
					$framewidth = $width/$totalimage;
					$frameheight = $height;
					$positions = $valign.' '.$halign;
					for ($i = 0; $i < sizeof($this->filename); $i++){
						$thisimgwidth = imagesx($this->image_dest[$i]);
						$thisimgheight = imagesy($this->image_dest[$i]);
						switch ($positions){
							case 'top left'		: imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth, 0, 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'top center'	: imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth+($framewidth-$thisimgwidth)/2, 0, 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'top right'	: imagecopy($image_dest, $this->image_dest[$i], ($i+1)*$framewidth-$thisimgwidth, 0, 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'middle left'	: imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth, ($frameheight-$thisimgheight)/2, 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'middle center': imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth+($framewidth-$thisimgwidth)/2, ($frameheight-$thisimgheight)/2, 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'middle right'	: imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth+($framewidth-$thisimgwidth), ($frameheight-$thisimgheight)/2, 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'bottom left'	: imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth, ($frameheight-$thisimgheight), 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'bottom center': imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth+($framewidth-$thisimgwidth)/2, ($frameheight-$thisimgheight), 0, 0, $thisimgwidth, $thisimgheight);break;
							case 'bottom right'	: imagecopy($image_dest, $this->image_dest[$i], $i*$framewidth+($framewidth-$thisimgwidth), ($frameheight-$thisimgheight), 0, 0, $thisimgwidth, $thisimgheight);break;
							default : 
								$abspos = explode(" ",$positions);
								imagecopy($image_dest, $this->image_dest[$i], $abspos[1], $abspos[0], 0, 0, $thisimgwidth, $thisimgheight);
								break;
						}
					}					
				}
				$this->image_dest[0] = $image_dest;
			}
		}
		
		function addwatermark($text_gen,$font,$color,$bgcolor,$fontSize,$align,$opacity,$widthw,$heightw,$top,$pad){
			$angle = 0;

			$sizeT = imagettfbbox($fontSize, $angle, $font, $text_gen);
			$widthT = abs($sizeT[2]) + abs($sizeT[0]);
			$heightT = abs($sizeT[7]) + abs($sizeT[1]);

			//$img_b = imagecreatetruecolor($widthT, $heightT);
			$img_b = imagecreatetruecolor($widthw,$heightw);
			imageSaveAlpha($img_b, true);
			ImageAlphaBlending($img_b, false);
			 
			$transparentColor = imagecolorallocatealpha($img_b, hexdec('0x' . $bgcolor{0} . $bgcolor{1}), hexdec('0x' . $bgcolor{2} . $bgcolor{3}), hexdec('0x' . $bgcolor{4} . $bgcolor{5}),$opacity);
			imagefill($img_b, 0, 0, $transparentColor);

			$textColor = imagecolorallocate($img_b, hexdec('0x' . $color{0} . $color{1}), hexdec('0x' . $color{2} . $color{3}), hexdec('0x' . $color{4} . $color{5}));
			if ($align=='left')
				imagettftext($img_b, $fontSize, 0, $pad, abs($sizeT[5]), $textColor, $font, $text_gen);
			else if ($align=='center')
				imagettftext($img_b, $fontSize, 0, ($widthw-$widthT)/2, abs($sizeT[5]), $textColor, $font, $text_gen);
			else if ($align=='right')
				imagettftext($img_b, $fontSize, 0, $widthw-$widthT-$pad, abs($sizeT[5]), $textColor, $font, $text_gen);
			/*if ($align=='left')
				$this->imagecopymerge_alpha($this->image_dest[0], $img_b, 0, 200, 0, 0, imagesx($img_b), imagesy($img_b),100);
			else if ($align=='center')
				$this->imagecopymerge_alpha($this->image_dest[0], $img_b, (imagesx($this->image_dest[0])-imagesx($img_b))/2, 200, 0, 0, imagesx($img_b), imagesy($img_b),100);
			else if ($align=='right')
				$this->imagecopymerge_alpha($this->image_dest[0], $img_b, imagesx($this->image_dest[0])-imagesx($img_b), 200, 0, 0, imagesx($img_b), imagesy($img_b),100);*/
			$this->imagecopymerge_alpha($this->image_dest[0], $img_b, 0, $top, 0, 0, imagesx($img_b), imagesy($img_b),100);
			$this->isOri[0] = false;
			$this->type[0] = 'jpg';
		}

		//save resized image
		function saveToFile($fname){
			for ($i = 0; $i < sizeof($fname); $i++){
				if (isset($this->type[$i])){
					if ($this->type[$i] == 'jpg' || $this->type[$i] == 'jpeg' || $this->type[$i] == 'jpe')
						imagejpeg($this->image_dest[$i], $fname[$i], 100);
					else if ($this->type[$i] == 'gif')
						imagegif($this->image_dest[$i], $fname[$i]);
					else if ($this->type[$i] == 'png')
						imagepng($this->image_dest[$i], $fname[$i]);
				}
			}
		}

		function outputimage(){
			//print_r($this->image_dest);
	    	header("Content-type: image/".$this->type[0]);
	    	if ($this->isOri[0]){
				fpassthru($this->fn[0]);
	    	}
	    	else{
				if ($this->type[0] == 'jpg' || $this->type[0] == 'jpeg' || $this->type[0] == 'jpe'){
					imagejpeg($this->image_dest[0], NULL,100); 
				}
				else if ($this->type[0] == 'gif')
					imagegif($this->image_dest[0]);
				else if ($this->type[0] == 'png')
					imagepng($this->image_dest[0]);
				//imagedestroy($this->image_dest[0]);
				unset($this->image_dest);
			}
		}

	}
?>