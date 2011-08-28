<ul class="nav">
		
<?php

foreach($menu as $item)
{
	$href = $item[0];
	$text = $item[1];
	$permission = $item[2];
	$class = $item[3];
	if (stristr($this->uri->uri_string(), $href))
	{
		$class .= ' active';
	}
	if ($this->auth->check($permission, true))
	{
		echo '<li>' . anchor(site_url($href), $text, 'class="i ' . $class . '"') . '</li>';
	}
}

?>

</ul>