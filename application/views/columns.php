<?php
$c1 = (isset($c1) ? $c1 : array());
$c2 = (isset($c2) ? $c2 : array());
?>

<div>
	<?php if ($c1): ?>
	<div style="float:left;width:<?php echo $c1['width'] ?>" class="column">
		<div class="c" id="c1"><?php echo $c1['content'] ?></div>
	</div>
	<?php endif; ?>

	<?php if ($c2): ?>
	<div style="float:right;width:<?php echo $c2['width'] ?>" class="column">
		<div class="c" id="c2"><?php echo $c2['content'] ?></div>
	</div>
	<?php endif; ?>
</div>
