<ul class="nav">

<?php
$active = (isset($subnav_active)) ? $subnav_active : '';

foreach ($subnav as $item)
{
	if ($item['test'] === FALSE) continue;
	
	$class = ($item['uri'] === $active) ? 'current' : '';
	
	echo '<li class="' . $class . '">';
	if ($item['uri'] !== $active)
	{
		echo anchor($item['uri'], $item['text']);
	}
	else
	{
		echo '<span>' . $item['text'] . '</span>';
	}
	echo '</li>';
	
}
?>

</ul>