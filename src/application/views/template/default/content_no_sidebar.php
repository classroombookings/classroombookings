<br class="clear">

<div class="container_skel container">
	
	<?php $alert = (isset($alert)) ? $alert : $this->session->flashdata('flash') ?>
	
	<?php if (!empty($alert)): ?>
	
	<div class="row remove-bottom" style="padding-top: 20px;">
		<?php echo $alert ?>
	</div>
	
	<?php else: ?>
	
	<br class="clear">
	
	<?php endif; ?>
	
	<!-- page body -->
	<?php echo $this->layout->get('content') ?>
	<!-- end page body -->

</div> <!-- / .container_skel .container -->