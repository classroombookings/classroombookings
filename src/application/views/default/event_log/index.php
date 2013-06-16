<?php $this->load->view('parts/filter-toggle') ?>

<div class="grid_12 filterable content">

	<?php if ($events): ?>

	<table class="list log" id="log">
		
		<thead>
			<th><?php echo lang('event_log_datetime') ?></th>
			<th><?php echo lang('username') ?></th>
			<th><?php echo lang('event_log_description') ?></th>
			<th><?php echo lang('event_log_type') ?></th>
			<th><?php echo lang('event_log_area') ?></th>
			<th></th>
		</thead>
		
		<tbody>
			
			<?php foreach ($events as $log): ?>
			
			<tr class="<?php echo alternator('odd', 'even') ?>">
				
				<td><?php echo $log['l_datetime'] ?></td>
				
				<td class="title"><?php echo $log['l_username'] ?></td>
				
				<td><?php echo $log['l_description'] ?></td>
				
				<td><?php echo $log['l_type'] ?></td>
				
				<td><?php echo $log['l_area'] ?></td>
				
				<td><?php echo anchor('event_log/view/' . $log['l_id'], lang('view')) ?></td>
				
			</tr>
			
			<?php endforeach; ?>
			
		</tbody>
		
	</table>
	
	<div class="pagination-wrapper">
		<?php echo $this->pagination->create_links() ?>
	</div>

	<?php else: ?>

	<p class="no-results"><?php echo lang('event_log_none') ?></p>
		
	<?php endif; ?>

</div>


<div class="grid_3 filterable filter hidden">
<?php $this->load->view('default/event_log/index/filter') ?>
</div>