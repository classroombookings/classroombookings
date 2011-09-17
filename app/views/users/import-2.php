<?php
$errors = validation_errors();
if ($errors)
{
	echo $this->msg->err('<ul>' . $errors . '</ul>', 'Please check the following invalid item(s) and try again.');
}

if ($lasterr){ echo $lasterr . '<br class="clear">'; }

echo form_open('users/import/2', null, array('step' => 2));

// Start tabindex
$t = 1;
?>

<p>Choose the appropriate heading for each column you want to import. Untick the rows that you do not want to import.</p>

<p><button id="toggle" class="button black small">Toggle all rows</button></p>

<!-- <div style="overflow:scroll;max-height:500px;"> -->

	<table class="list" width="100%" cellpadding="0" cellspacing="0" border="0" id="userimport">
		
		<thead>
		<tr class="heading">
			<td class="h" width="16">&nbsp;</td>
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
				$select = form_dropdown("col[{$col}]", $options, '0', 'tabindex="'.$t.'" class="remove-bottom"');
				echo '<td class="h" title="column">' . $select . '</td>';
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

<!-- </div> -->

<br><br>

<p><strong>Total entries:</strong> <?php echo $row + 1; ?></p>

<hr>

<?php
unset($buttons);
$buttons[] = array('submit', 'blue', "Preview import &rarr;", $t);
$buttons[] = array('link', '', 'Cancel', $t + 1, site_url('users'));
$this->load->view('parts/buttons', array('buttons' => $buttons));
?>


</form>

<script type="text/javascript">
_jsQ.push(function(){
	$("button#toggle").click(function(e){
		e.preventDefault();
		var checkbox = $("table#userimport").find(':checkbox');
		checkbox.prop('checked', !checkbox[0].checked);
	});
});
</script>