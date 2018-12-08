<h5>
	<a href="<?php echo site_url('reports') ?>">
		<img src="webroot/images/ui/school_manage_reports.png" alt="Reports" hspace="4" align="top" width="16" height="16" />
		View reports
	</a>
</h5>

<h5>
	<a href="<?php echo site_url('bookings') ?>">
		<img src="webroot/images/ui/school_manage_bookings.png" alt="Bookings" hspace="4" align="top" width="16" height="16" />
		Manage bookings
	</a>
</h5>

<h5>
	<a href="<?php echo site_url('remoteaccess') ?>">
		<img src="webroot/images/ui/school_manage_xmlrpc.png" alt="Remote Access" hspace="4" align="top" width="16" height="16" />
		Configure remote access
	</a>
</h5>

<h5>
	<a href="<?php echo site_url('rooms') ?>">
		<img src="webroot/images/ui/school_manage_rooms.png" alt="Rooms" hspace="4" align="top" width="16" height="16" />
		Edit rooms
	</a>
</h5>

<h5>
	<a href="<?php echo site_url('profile') ?>">
		<?php
		if ($this->userauth->is_level(ADMINISTRATOR)) { $icon = 'user_administrator.png'; } else { $icon = 'user_teacher.png'; }
		?>
		<img src="webroot/images/ui/<?php echo $icon ?>" alt="Profile" hspace="4" align="top" width="16" height="16" />
		My profile
	</a>
</h5>
