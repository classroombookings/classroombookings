<div class="colmask leftmenu"> 
	
	<div class="colright">

		<div class="col1wrap">
			
			<div class="col1">
				
				<?php $alert = (isset($alert)) ? $alert : $this->session->flashdata('flash') ?>
				
				<?php if (!empty($alert)): ?>
				
				<div class="add-bottom"><?php echo $alert ?></div>
				
				<?php endif; ?>
				
				<!-- start page body -->
				<?php echo $this->layout->get('content') ?>
				<!-- end page body -->
				
			</div> <!-- / .col1 .container -->
			
		</div> <!-- / .col1wrap -->
		
		<div class="col2" id="sidebar">
			<!-- start sidebar -->
			<?php echo $this->layout->get('sidebar') ?>
			<!-- end sidebar -->
		</div> 
		<!-- / .col2 #sidebar -->
		
	</div> <!-- / .colright -->
	
</div> <!-- / .colmask .leftmenu -->