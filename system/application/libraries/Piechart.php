<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


////////////////////////////////////////////////////////////////
// PHP script made by Rasmus Petersen - http://www.peters1.dk //
////////////////////////////////////////////////////////////////


class Piechart {
	
	
	
	
	
	var $show_label;
	var $show_percent;
	var $show_text;
	var $show_parts;
	var $label_form;
	var $font;
	var $width;
	var $height;
	var $background_colour;
	var $text_colour;
	var $colours = array();
	var $shadow_height;
	var $shadow_dark;
	var $data = array();
	var $labels  = array();
	
	
	
	
	
	function Piechart(){
		$this->show_label = true;
		$this->show_percent = false;
		$this->show_text = true;
		$this->show_parts = true;
		$this->label_form = 'square';
		$this->font = 2;
		$this->font_size = 8;
		$this->width = 199;
		$this->background_colour = 'FFFFFF';
		$this->text_colour = '000000';
		$this->colours = array('003366', 'CCD6E0', '7F99B2','F7EFC6', 'C6BE8C', 'CC6600','990000','520000','BFBFC1','808080'); // colors of the slices.
		$this->shadow_height = 16;
		$this->shadow_dark = true;
	}
	
	
	
	
	
	function showLabel($showLabel){
		$this->show_label = $showLabel;
	}
	
	
	function showPercent($showPercent){
		$this->show_percent = $showPercent;
	}
	
	
	function showText($showText){
		$this->show_text = $showText;
	}
	
	
	function showParts($showParts){
		$this->show_parts = $showParts;
	}
	
	
	function setLegend($type){
		$types = array('square', 'round');
		if(in_array($type, $types)){
			$this->label_form = $type;
		} else {
			return false;
		}
	}
	
	
	function setFont($ttfpath, $ttfsize = 8){
		if(file_exists($ttfpath)){
			$this->font = $ttfpath;
			$this->font_size = $ttfsize;
		} else {
			$this->font = 2;
		}
	}
	
	
	function setWidth($width){
		$this->width = $width;
	}	
	
		
	function setData($arr_data){
		$this->data = $arr_data;
	}
		
	
	function setLabels($arr_labels){
		$this->labels = $arr_labels;
	}
	
	
	
	
	
	function Generate($outfile = NULL){
	
		if($this->data == NULL){
			return false;
		}
		
		$this->height = $this->width/2;
		
		$text_length = 0;
		
		for($i = 0; $i < count($this->labels); $i++){
			if ($this->data[$i]/array_sum($this->data) < 0.1){
				$number[$i] = ' '.number_format(($this->data[$i]/array_sum($this->data))*100,1,'.','.').'%';
			} else {
				$number[$i] = number_format(($this->data[$i]/array_sum($this->data))*100,1,'.','.').'%';
			}
			if (strlen($this->labels[$i]) > $text_length) $text_length = strlen($this->labels[$i]);
		}
		
		$xtra_width = 0;
		$xtra_height = 0;
		
		if(is_array($this->labels)){
			$antal_label = count($this->labels);
			$xtra = (5+15*$antal_label)-($this->height+ceil($this->shadow_height));
			if ($xtra > 0) $xtra_height = (5+15*$antal_label)-($this->height+ceil($this->shadow_height));
		
			$xtra_width = 5;
			if ($this->show_label) $xtra_width += 20;
			if ($this->show_percent) $xtra_width += 45;
			if ($this->show_text) $xtra_width += $text_length*8;
			if ($this->show_parts) $xtra_width += 35;
		}
		
		$img = ImageCreateTrueColor($this->width+$xtra_width, $this->height+ceil($this->shadow_height)+$xtra_height);
		
		ImageFill($img, 0, 0, $this->_colourHex($img, $this->background_colour));
		
		foreach($this->colours as $colorkode){
			$fill_colour[] = $this->_colourHex($img, $colorkode);
			$shadow_colour[] = $this->_colourHexshadow($img, $colorkode, $this->shadow_dark);
		}
		
		$label_place = 5;
		$label_output = '';
		
		if(is_array($this->labels)){
			for ($i = 0; $i < count($this->labels); $i++){
				if ($this->label_form == 'round' && $this->show_label){
					imagefilledellipse($img,$this->width+21,$label_place+5,10,10,$this->_colourHex($img, $this->colours[$i % count($this->colours)]));
					imageellipse($img,$this->width+21,$label_place+5,10,10,$this->_colourHex($img, $this->text_colour));
				} else if ($this->label_form == 'square' && $this->show_label){	
					imagefilledrectangle($img,$this->width+16,$label_place,$this->width+16,$label_place+10,$this->_colourHex($img, $this->colours[$i % count($this->colours)]));
					imagerectangle($img,$this->width+16,$label_place,$this->width+16,$label_place+10,$this->_colourHex($img, $this->text_colour));
				}
				
				// Generate text info
				if ($this->show_percent) $label_output = $number[$i].' ';
				if ($this->show_text) $label_output .= $this->labels[$i].' ';
				if ($this->show_parts) $label_output .= '('.$this->data[$i].')';
				
				if(is_int($this->font)){
					imagestring( $img, $this->font, $this->width+30, $label_place, $label_output, $this->_colourHex($img, $this->text_colour) );
				} else {
					imagettftext($img, $this->font_size, 0, $this->width+30, $label_place+10, $this->_colourHex($img, $this->text_colour), $this->font, $label_output);
				}
				$label_output = '';
				$label_place = $label_place + 17;
			}
		}
		
		$centerX = round($this->width/2);
		$centerY = round($this->height/2);
		$diameterX = $this->width-4;
		$diameterY = $this->height-4;
		
		$data_sum = array_sum($this->data);
		
		$start = 270;
		
		$value = '';
		$value_counter = 0;
		
		for ($i = 0; $i < count($this->data); $i++){
			$value += $this->data[$i];
			$end = ceil(($value/$data_sum)*360) + 270;
			$slice[] = array($start, $end, $shadow_colour[$value_counter % count($shadow_colour)], $fill_colour[$value_counter % count($fill_colour)]);
			$start = $end;
			$value_counter++;
		}
		
		for ($i=$centerY+$this->shadow_height; $i>$centerY; $i--){
			for ($j = 0; $j < count($slice); $j++){
				ImageFilledArc($img, $centerX, $i, $diameterX, $diameterY, $slice[$j][0], $slice[$j][1], $slice[$j][2], IMG_ARC_PIE);
			}
		}	
		
		for ($j = 0; $j < count($slice); $j++){
			ImageFilledArc($img, $centerX, $centerY, $diameterX, $diameterY, $slice[$j][0], $slice[$j][1], $slice[$j][3], IMG_ARC_PIE);
		}
		
		// Call function to save image
		$this->_OutputImage($img, $outfile);
		// GD function: get rid of image
		imagedestroy($img);
	}
	
	
	
	
	
	function _colourHex($img, $HexColorString){
		$R = hexdec(substr($HexColorString, 0, 2));
		$G = hexdec(substr($HexColorString, 2, 2));
		$B = hexdec(substr($HexColorString, 4, 2));
		return ImageColorAllocate($img, $R, $G, $B);
	}
	
	
	
	
	
	function _colourHexshadow($img, $HexColorString, $mork){
		$R = hexdec(substr($HexColorString, 0, 2));
		$G = hexdec(substr($HexColorString, 2, 2));
		$B = hexdec(substr($HexColorString, 4, 2));
		if($mork){
			($R > 99) ? $R -= 100 : $R = 0;
			($G > 99) ? $G -= 100 : $G = 0;
			($B > 99) ? $B -= 100 : $B = 0;
		} else {
			($R < 220) ? $R += 35 : $R = 255;
			($G < 220) ? $G += 35 : $G = 255;
			($B < 220) ? $B += 35 : $B = 255;
		}
		return ImageColorAllocate($img, $R, $G, $B);
	}
	
	
	
	
	
	function _OutputImage($img, $outfile = NULL){
		#header('Content-type: image/jpg');
		#ImageJPEG($img,NULL,100);
		#header('Content-type: image/png');
		if($outfile != NULL){
			imagepng($img, $outfile);
		} else {
			header('Content-type: image/png');
			imagepng($img);
		}
	}
	
	
	
	
	
}
?>
