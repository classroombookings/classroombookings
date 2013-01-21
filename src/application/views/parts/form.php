<div class="grid_12 form">

	<?php foreach ($sections as $section_name => $section_title): ?>

	<div class="row section section-<?php echo $section_name ?>">
	
		<div class="grid_3">
			<h3 class="sub-heading"><?php echo $section_title ?></h3>
		</div>
		
		<div class="grid_9">
		
			<?php $current_inputs = element($section_name, $inputs, array()); ?>
			
			<?php foreach ($current_inputs as $input_name => $input): ?>
			
			<div class="row">
				<div class="grid_6">
					<?php if (element('label', $input)): ?>
					<label for="<?php echo $input_name ?>"><?php echo $input['label'] ?></label>
					<?php endif; ?>
					<?php echo $input['content'] ?>
				</div>
				<?php if (element('hint', $input)): ?>
				<div class="grid_3">
					<div class="hint">
						<h6><?php echo $input['label'] ?></h6>
						<p><?php echo $input['hint'] ?></p>
					</div>
				</div>
				<?php endif; ?>
			</div>
			
			<?php endforeach; ?>
			
		</div> <!-- / .grid_9 -->
		
	</div> <!-- / .row.form-section-....... -->
	
	<?php endforeach; ?>
	
	<div class="row submit">
		<div class="grid_9 offset_3">
			<?php foreach ($buttons as $button): ?>
			<?php echo $button; ?>
			<?php endforeach; ?>
		</div>
	</div>
	
</div> <!-- / .grid_12.form -->