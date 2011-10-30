<table class="list2 middle" summary="Academic years list" id="years">
	
	<thead>
		<tr>
			<th scope="col" width="20">Current</th>
			<th scope="col">Name</th>
			<th scope="col">Start date</th>
			<th scope="col">End date</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	
	<tbody>
	
		<?php foreach ($years as $year): ?>
	
		<tr>
		
			<td align="center" width="20">
				<?php if ($year->current == 1): ?>
					<img src="img/ico/checkmark2.png" width="16" height="16" class="remove-bottom" style="margin:0">
				<?php endif; ?>
			</td>
			
			<td class="title">
				<?php echo anchor('academic/years/edit/' . $year->year_id, $year->name) ?>
			</td>
			
			<td><?php echo date("l jS F Y", todate($year->date_start)) ?></td>
			<td><?php echo date("l jS F Y", todate($year->date_end)) ?></td>
			
			<?php echo form_open('academic/years/make_current', null, array('year_id' => $year->year_id)); ?>
			
			<td class="actions">
				<a href="<?php echo site_url(sprintf('academic/years/delete/%d', $year->year_id)) ?>" class="button red small">Delete</a>
				<?php if ($year->current != 1): ?>
					<input type="submit" class="small green button makecurrent" value="Make current">
				<?php endif; ?>
			</td>
			
			<?php echo form_close() ?>
		
		</tr>
	
		<?php endforeach; ?>
	
	</tbody>
	
</table>