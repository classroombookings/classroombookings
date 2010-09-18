<?php
/*
	0: url
	1: text
	2: class for icon or icon filename in /img/ico/
	3: title
	4: current page true/false
	5: other
*/
$items = $_ci_vars;
$items_count = count($items);

$html = "";
$html .= '<ul class="linkbar">';
$i = 0;
foreach($items as $item){
	
	if(!is_array($item)){
		
		// Not array - treat as blank
		$html .= '<li class="blank"><a> </a></li>';
	
	} else {
	
		$url = $item[0];
		$text = $item[1];
		
		// Icon - class name or image filename?
		if(strpos($item[2], '.') === FALSE){
			// Not an image filename, treat as class name
			$iconcls = (isset($item[2])) ? ' class="i i-' . $item[2] . '"' : '';
		} else {
			// Apply icon class, but specify filename in style instead of class
			$iconcls = sprintf(' class="i" style="background-image:url(img/ico/%s)"', $item[2]);
		}
		
		// Title
		$title = (isset($item[3])) ? $item[3] : '';
		
		// Current or not
		$currentcls = (isset($item[4])) ? ' class="current"' : '';
		
		// Other tags?
		$other = (isset($item[5])) ? $item[5] : '';
		
		// Template for link
		$format = '<li%s><a href="%s" title="%s"%s %s>%s</a></li>';
		
		// Append to variable
		$html .= sprintf($format, $currentcls, site_url($item[0]), $title, $iconcls, $other, $text) . "\n";
		$i++;
		
	}

}

$html .= '</ul>';

echo $html;
?>
