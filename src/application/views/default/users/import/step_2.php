<?php echo form_open(current_url(), array('id' => 'user_import_2_form')) ?>

	<div class="grid_12 form">

		<div class="row section section-headers">
		
			<div class="grid_3">
				<h3 class="sub-heading"><?php echo lang('users_import_csv_headers') ?></h3>
				<div class="hint">
					<p><?php echo lang('users_import_csv_headers_hint') ?></p>
				</div>
			</div>
			
			<div class="grid_9 inputs">
				
				<table class="list">
					
					<thead>
						<th><?php echo lang('users_import_header_source') ?></th>
						<th>&nbsp;</th>
						<th><?php echo lang('users_import_header_dest') ?></th>
					</thead>
					
					<tbody>
						<?php foreach ($headers as $index => $column): ?>
						<tr>
							<td><?php echo $column ?></td>
							<td>&nbsp;</td>
							<td class="text-right">
								<?php echo form_dropdown(
									'fields[' . $index . ']',
									$destination_fields,
									'',
									'tabindex="' . tab_index() . '" class="ftext-input" style="margin: 0"'
								); ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					
				</table>
				
			</div>
			
		</div>
		
		<div class="row submit">
			<div class="grid_9 offset_3 text-right">
				<?php echo form_button(array(
					'type' => 'submit',
					'class' => 'blue right',
					'text' => lang('next'),
					'tab_index' => tab_index(),
				)) ?>
				<?php echo form_button(array(
					'type' => 'link',
					'url' => 'users/import/cancel',
					'class' => 'grey right',
					'text' => lang('cancel'),
					'tab_index' => tab_index(),
				)) ?>
			</div>
		</div>
		
	</div>

</form>
