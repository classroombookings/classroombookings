<div class="grid_12">

	<h3 class="sub-heading">Event ID #<?php echo $event['l_id'] ?></h3>
	
</div>
<div class="grid_8">
	
	<dl class="event-info">
		<dt><?php echo lang('username') ?></dt>
		<dd><?php echo anchor('users/set/' . $event['l_u_id'], element('l_username', $event, $event['u_username'])) ?> (<?php echo $event['u_display'] ?>)</dd>
		
		<dt><?php echo lang('event_log_description') ?></dt>
		<dd><?php echo $event['l_description'] ?></dd>
		
		<dt><?php echo lang('event_log_ip') ?></dt>
		<dd><?php echo $event['l_ip'] ?></dd>
		
		<dt><?php echo lang('event_log_browser') ?></dt>
		<dd><?php echo $event['l_browser'] ?></dd>
		
		<dt><?php echo lang('event_log_ua') ?></dt>
		<dd><?php echo $event['l_ua'] ?></dd>
	</dl>
	
</div>

<div class="grid_4">
	
	<dl class="event-info">
		<dt><?php echo lang('event_log_datetime') ?></dt>
		<dd><?php echo date_fmt($event['l_datetime'], 'l jS F Y H:i'); ?></dd>
		
		<dt><?php echo lang('event_log_area') ?></dt>
		<dd><?php echo $event['l_area'] ?></dd>
		
		<dt><?php echo lang('event_log_type') ?></dt>
		<dd><?php echo $event['l_type'] ?></dd>
		
		<dt><?php echo lang('event_log_uri') ?></dt>
		<dd><?php echo $event['l_uri'] ?></dd>
	</dl>
	
</div>


<div class="grid_12">
	
	<hr>
	
	<dl class="event-info">
		<dt>Data</dt>
		<dd><pre><code><?php echo json_encode(json_decode($event['l_data']), JSON_PRETTY_PRINT) ?></code></pre></dd>
	</dl>
</div>