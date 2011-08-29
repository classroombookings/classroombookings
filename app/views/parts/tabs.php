<ul class="tabs">
	<?php foreach($tabs as $tab): ?>
		<?php $class = ($active_tab == $tab['id']) ? 'class="active"' : ''; ?>
		<li><a <?php echo $class ?> data-tab="<?php echo $tab['id'] ?>"><?php echo $tab['title'] ?></a></li>
	<?php endforeach; ?>
</ul>


<ul class="tabs-content">
	
	<?php foreach($tabs as $tab): ?>
		
		<!-- Tab: <?php echo $tab['title'] ?> -->
		<?php $class = ($active_tab == $tab['id']) ? 'class="active"' : ''; ?>
		<li <?php echo $class ?> id="<?php echo $tab['id'] ?>">
			<?php echo $tab['view'] ?>
		</li>
		
	<?php endforeach; ?>
	
</ul>