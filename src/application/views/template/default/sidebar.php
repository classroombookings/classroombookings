<?php if ($nav['secondary']): ?>
<ul class="clearfix">
	<?php
	foreach ($nav['secondary'] as $uri => $item)
	{
		$href = $item;
		$text = $data['label'];
		$class = $data['class'];
		echo '<li>' . anchor(site_url($href), $text, 'class=" ' . $class . '"') . '</li>';
	}
	?>
</ul>
<?php endif; ?>

<?php
/*
$ulclass = (isset($ulclass)) ? $ulclass : '';
?>

<ul class="<?php echo $ulclass ?> clearfix">
		
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
*/
?>