<?php
$img = img('assets/images/ui/edit.gif', FALSE, "hspace='2' border='0' alt='Edit'");
echo anchor("{$edit}", $img, 'title="Edit"');

$img = img('assets/images/ui/delete.gif', FALSE, "hspace='2' border='0' alt='Delete'");
echo anchor("{$delete}", $img, 'title="Delete"');
