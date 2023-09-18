<?php
if (!empty($edit)) {
	$img = img('assets/images/ui/edit.png', FALSE, "hspace='3' border='0' alt='Edit'");
	echo anchor("{$edit}", $img, 'title="Edit"');
}

$img = img('assets/images/ui/delete.png', FALSE, "hspace='3' border='0' alt='Delete'");
echo anchor("{$delete}", $img, 'title="Delete"');
