<p>Choose the appropriate heading for each column you want to import.</p>

<div style="overflow:scroll;height:500px;">

	<table class="list" width="99%" cellpadding="0" cellspacing="0" border="0">
		
		<thead>
		<tr class="heading">
			<td class="h" width="16" title="Import/Not">&nbsp;</td>
			<?php
			$options['0'] = '(Ignore)';
			$options['username'] = 'Username';
			$options['password'] = 'Password';
			$options['display'] = 'Display name';
			$options['email'] = 'Email address';
			$options['groupname'] = 'Group name';
			
			$csvdata = fgetcsv($fhandle, filesize($csv['full_path']), ',');
			
			$col = 0;
			for($i = 0; $i<count($csvdata); $i++){
				$select = form_dropdown("col_{$col}", $options, '0');
				echo '<td class="h" title="column" width="100">' . $select . '</td>';
				$col++;
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
			$check['name'] = "row[{$row}][enable]";
			$check['id'] = "row[{$row}][enable]";
			$check['value'] = 1;
			$check['enabled'] = TRUE;
			
			echo '<td>' . form_checkbox($check) . '</td>';
			
			$col = 0;
			foreach($csvdata as $coldata){
				
				echo "<td>" . form_hidden("row[{$row}][$col]", $coldata) . "$coldata</td>";
				$col++;
				
			}
			echo '</tr>';
			
			$row++;
		}
		?>
		
		</tbody>
		
	</table>

</div>

<p>Total rows in the file: <?php echo $row + 1; ?></p>

<p>
	<label for="username_formula">Auto-generate username using this formula:</label>
	<br />
	<?php
	$input['name'] = 'username_formula';
	$input['id'] = 'username_formula';
	$input['size'] = '40';
	echo form_input($input);
	?>
</p>