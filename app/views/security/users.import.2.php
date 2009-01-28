<?php
$errors = validation_errors();
if($errors){
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

echo form_open('security/users/import/2', NULL, array('stage' => 2));

// Start tabindex
$t = 1;
?>

<p>Choose the appropriate heading for each column you want to import. Untick the rows that you do not want to import.</p>

<p>
	(<a href="#" onclick="$('.check').attr('checked', 'checked'); return false;">Tick all rows</a> | 
	<a href="#" onclick="$('.check').attr('checked', false); return false;">Untick all rows</a>)
</p>

<div style="overflow:scroll;max-height:500px;">

	<table class="list" width="99%" cellpadding="0" cellspacing="0" border="0">
		
		<thead>
		<tr class="heading">
			<td class="h" width="16" title="Import/Not">&nbsp;</td>
			<?php
			$options['ignore'] = '(Ignore)';
			$options['username'] = 'Username';
			$options['password'] = 'Password';
			$options['display'] = 'Display name';
			$options['email'] = 'Email address';
			$options['groupname'] = 'Group name';
			
			$csvdata = fgetcsv($fhandle, filesize($csv['full_path']), ',');
			#echo count($csvdata);
			$col = 0;
			for($i = 0; $i<count($csvdata); $i++){
				$select = form_dropdown("col[{$col}]", $options, '0', 'tabindex="'.$t.'"');
				echo '<td class="h" title="column" width="100">' . $select . '</td>';
				$col++;
				$t++;
			}
			?>
		</tr>
		</thead>
		<tbody>

		<?php
		$row = 0;
		while(($csvdata = fgetcsv($fhandle, filesize($csv['full_path']), ',')) !== FALSE){
			
			echo '<tr>';
			
			unset($check);
			$check['name'] = "row[{$row}][import]";
			$check['id'] = "row[{$row}][import]";
			$check['value'] = 1;
			$check['enabled'] = TRUE;
			$check['class'] = 'check';
			$check['tabindex'] = $t;
			
			echo '<td>' . form_checkbox($check) . '</td>';
			
			$col = 0;
			foreach($csvdata as $coldata){
				
				echo '<td>';
				echo form_hidden("row[{$row}][$col]", $coldata);
				echo '<label for="row['.$row.'][import]">'.$coldata.'</label>';
				echo '</td>';
				$col++;
				
			}
			echo '</tr>';
			echo "\n";
			
			$row++;
		}
		?>
		
		</tbody>
		
	</table>

</div>

<p>Total rows in the file: <?php echo $row + 1; ?></p>

<?php
unset($buttons);
$buttons[] = array('submit', 'positive', 'Preview import', 'magnifier-arr.gif', $t);
$buttons[] = array('cancel', 'negative', 'Cancel', 'arr-left.gif', $t+1, site_url('security/users/import'));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?>

</form>

<script type="text/javascript">
$('.check').attr('checked', 'checked');
</script>