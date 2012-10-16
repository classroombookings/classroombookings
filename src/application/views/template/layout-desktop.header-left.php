<ul class="horiz">

<li class="name">Test School</li>
		
<?php

foreach($menu as $item)
{
	$href = $item[0];
	$text = $item[1];
	$permission = $item[2];
	$class = $item[3];
	if ($this->auth->check($permission, true))
	{
		echo '<li>' . anchor(site_url($href), $text, 'class=" ' . $class . '"') . '</li>';
	}
}

?>

</ul>