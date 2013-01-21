<dl class="htabs">
	
	<?php foreach($tabs as $tab): ?>
	
	<?php $class = ($active_tab === $tab['id']) ? 'class="active"' : ''; ?>
	<dd><a href="<?php echo current_url() ?>#<?php echo $tab['id'] ?>" <?php echo $class ?> data-tab="<?php echo $tab['id'] ?>"><?php echo $tab['title'] ?></a></dd>
	
	<?php endforeach; ?>
	
</dl>


<ul class="htabs-content">
	
	<?php foreach($tabs as $tab): ?>
		
	<!-- Tab: <?php echo $tab['title'] ?> -->
	<?php $class = ($active_tab === $tab['id']) ? 'class="active"' : ''; ?>
	<li <?php echo $class ?> id="<?php echo $tab['id'] ?>Tab">
		<?php $this->load->view($tab['view']) ?>
	</li>
		
	<?php endforeach; ?>
	
</ul>