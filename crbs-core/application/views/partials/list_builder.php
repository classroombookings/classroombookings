
<div
	not-class="block-group list-builder"
	class="row middle-xs list-builder"
	data-script="install ListBuilder"
	data-input-name="<?= $name ?>"
>
	<?php echo form_hidden($name, '') ?>
	<div class="col-xs">
		<h6><?= $available ?? 'Available' ?></h6>
		<?php
		echo form_input([
			'type' => 'search',
			'value' => '',
			'class' => 'filter-input',
			'placeholder' => 'Filter...',
			'data-filter' => '',
		]);
		echo form_dropdown([
			'options' => isset($alphabetise) && $alphabetise === true
				? alphabetise_assoc_results($available_options)
				: $available_options
				,
			'multiple' => '',
			'data-available' => '',
		]);
		?>
	</div>
	<div class="col-xs" style="flex-grow: 0">
		<div class="list-builder-buttons">
			<?php
			echo form_button([
				'type' => 'button',
				'class' => 'btn-block',
				'data-action' => 'select_one',
				'content' => img([
					'src' => asset_url('assets/images/ui/resultset_next.png'),
					'style' => 'vertical-align: middle; margin: 3px 0;',
					'width' => 16,
					'height' => 16,
					'alt' => '&rarr;',
				]),
			]);
			echo form_button([
				'type' => 'button',
				'class' => 'btn-block',
				'data-action' => 'select_all',
				'content' => img([
					'src' => asset_url('assets/images/ui/forward_green.png'),
					'style' => 'vertical-align: middle; margin: 3px 0;',
					'width' => 16,
					'height' => 16,
					'alt' => '&ldquo;',
				]),
			]);
			echo "<br>";
			echo form_button([
				'type' => 'button',
				'class' => 'btn-block',
				'data-action' => 'remove_one',
				'content' => img([
					'src' => asset_url('assets/images/ui/resultset_previous.png'),
					'style' => 'vertical-align: middle; margin: 3px 0;',
					'width' => 16,
					'height' => 16,
					'alt' => '&rarr;',
				]),
			]);
			echo form_button([
				'type' => 'button',
				'class' => 'btn-block',
				'data-action' => 'remove_all',
				'content' => img([
					'src' => asset_url('assets/images/ui/rewind_green.png'),
					'style' => 'vertical-align: middle; margin: 3px 0;',
					'width' => 16,
					'height' => 16,
					'alt' => '&ldquo;',
				]),
			]);
			?>
		</div>
	</div>
	<div class="col-xs">
		<h6><?= $selected ?? 'Selected' ?></h6>
		<?php
		echo form_input([
			'type' => 'search',
			'value' => '',
			'class' => 'filter-input',
			'placeholder' => 'Filter...',
			'data-filter' => '',
		]);
		echo form_dropdown([
			'options' => $selected_options,
			'multiple' => '',
			'data-selected' => '',
		]);
		?>
	</div>
</div>
