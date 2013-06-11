<?php $this->load->view('parts/filter-toggle') ?>

<div class="grid_12 filterable content">

	<?php if ($departments): ?>

	<table class="list departments" id="departments">
		
		<thead>
			<th></th>
			<th><?php echo lang('departments_department_name') ?></th>
			<th><?php echo lang('departments_department_description') ?></th>
			<th><?php echo ucfirst(lang('departments_members_plural')) ?></th>
			<th></th>
		</thead>
		
		<tbody>
			
			<?php foreach ($departments as $d): ?>
			
			<tr class="<?php echo alternator('odd', 'even') ?>">
				
				<td class="one icon">
					<?php echo department_block($d) ?>
				</td>
				
				<td class="title">
					<?php echo anchor('departments/set/' . $d['d_id'], $d['d_name']) ?>
				</td>
				
				<td><?php echo $d['d_description'] ?></td>
				
				<td class="text-right" style="width: 30px"><?php echo $d['user_count'] ?></td>
				
				<td class="text-right"><?php echo department_delete($d) ?></td>
				
			</tr>
			
			<?php endforeach; ?>
			
		</tbody>
		
	</table>
	
	<div class="pagination-wrapper">
		<?php echo $this->pagination->create_links() ?>
	</div>

	<?php else: ?>

	<p class="no-results"><?php echo lang('departments_none') ?></p>
		
	<?php endif; ?>

</div>


<div class="grid_3 filterable filter hidden">
<?php $this->load->view('default/departments/index/filter') ?>
</div>