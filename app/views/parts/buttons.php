<?php
/*
	0 type	(submit|button|link)
	1 class (ok|cancel|misc)
	2 text
	3 tabindex
	4 url (for cancel)
*/
?><tr>
	<td class="caption">&nbsp;</td>
	<td class="action"><?php
		foreach($buttons as $button){
			
			$type = $button[0];
			$class = $button[1];
			$text = $button[2];
			$tabindex = $button[3];
			
			$id = (isset($button[5])) ? ' id="' . $button[5] . '"' : '';
			
			switch($type){
				
				case 'submit':
					$html = '<input type="submit" class="btn btn_%s" tabindex="%d" value="%s"%s />';
					echo sprintf($html, $class, $tabindex, $text, $id) . "\n";
				break;
				
				case 'button':
					$html = '<button type="button" name="%s" value="%s" class="btn btn_%s" tabindex="%d"%s>%s</button>';
					echo sprintf($html, $text, $text, $class, $tabindex, $id, $text);
				break;
				
				case 'link':
					$url = $button[4];
					$html = '<a href="%s" class="btn btn_%s" tabindex="%d"%s>%s</a>';
					echo sprintf($html, $url, $class, $tabindex, $id, $text) . "\n";
				break;

			}
			
		}
	?></td>
</tr>