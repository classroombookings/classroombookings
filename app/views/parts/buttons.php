<?php
/*
	0 type	(submit|button|link)
	1 class (colour/small/large)
	2 text
	3 tabindex
	4 url (for cancel)
*/

foreach($buttons as $button){

	$type = $button[0];
	$class = $button[1];
	$text = $button[2];
	$tabindex = $button[3];
	$url = (isset($button[4])) ? $button[4] : '';
	$id = (isset($button[5])) ? 'id="' . $button[5] . '"' : '';
	
	$find = array('{class}', '{text}', '{tab}', '{url}', '{id}');
	$replace = array($class, $text, $tabindex, $url, $id);

	switch($type)
	{
		case 'submit':
			$format = '<input type="submit" class="button {class}" tabindex="{tab}" value="{text}" {id}>';
			echo str_replace($find, $replace, $format);
		break;
		
		case 'button':
			$format = '<button type="button" name="{text}" value="{text}" class="button {class}" tabindex="{tab}" {id}>{text}</button>';
			echo str_replace($find, $replace, $format);
		break;

		case 'link':
			$format = '<a href="{url}" class="cancel" tabindex="{tab}"{id}>{text}</a>';
			echo str_replace($find, $replace, $format);
		break;
	}
	
	echo "\n";

}