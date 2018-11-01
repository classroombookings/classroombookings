<div class="submit" style="border-top:0px;">
	<?php
	if (isset($submit)) {
		echo form_submit(array('value' => $submit[0], 'tabindex' => $submit[1]));
	}
	echo "&nbsp;&nbsp;";
	if (isset($cancel)) {
		echo anchor($cancel[2], $cancel[0], array('tabindex' => $cancel[1]));
	}
	?>
</div>
