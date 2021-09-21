<?php

echo $this->session->flashdata('saved');

echo '<h2>School setup</h2>';
dotable($school_menu);

echo '<h2>Management</h2>';
dotable($manage_menu);


function dotable($array){

	echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
	echo '<tbody>';
	$row = 0;

	foreach($array as $link){
		if($row == 0){ echo '<tr>'; }
		echo '<td width="33%">';
		echo '<h5 style="margin:14px 0px">';
		echo '<a href="'.$link['url'].'">';
		echo '<img src="' . base_url('assets/images/ui/'.$link['icon']) . '" alt="'.$link['label'].'" hspace="4" align="top" width="16" height="16" />';
		echo $link['label'];
		echo '</a>';
		echo '</h5>';
		echo '</td>';
		echo "\n";
		if($row == 2){ echo '</tr>'."\n\n"; $row = -1; }
		$row++;
	}

	echo '</tbody>';
	echo '</table>'."\n\n";
}
?>
