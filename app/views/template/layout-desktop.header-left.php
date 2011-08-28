<ul class="horiz">
		
<?php

foreach($menu as $item)
{
	$href = $item[0];
	$text = $item[1];
	$permission = $item[2];
	$class = $item[3];
	if ($this->auth->check($permission, true))
	{
		echo '<li>' . anchor(site_url($href), $text, 'class="i ' . $class . '"') . '</li>';
	}
}

?>

</ul>