<div class="configure-menu">

	<div class="grid_3">
		<h4 class="sub-heading"><?php echo lang('configure_general') ?></h4>
		<ul>
			
			<?php if ($this->auth->check('crbs.configure.settings')): ?>
			<li>
				<?php echo anchor('configure/settings', lang('configure_general_settings'), 'class="i configure-settings"') ?>
				<p><?php echo lang('configure_general_settings_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('crbs.configure.settings')): ?>
			<li>
				<?php echo anchor('configure/quota', lang('configure_quota'), 'class="i configure-quota"') ?>
				<p><?php echo lang('configure_quota_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('crbs.configure.settings')): ?>
			<li>
				<?php echo anchor('configure/style', lang('configure_look_and_feel'), 'class="i configure-style"') ?>
				<p><?php echo lang('configure_look_and_feel_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('crbs.configure.settings')): ?>
			<li>
				<?php echo anchor('email', lang('configure_email'), 'class="i configure-email"') ?>
				<p><?php echo lang('configure_email_hint') ?></p>
			</li>
			<?php endif; ?>
			
		</ul>
	</div>

	<div class="grid_3">
		<h4 class="sub-heading"><?php echo lang('configure_security') ?></h4>
		<ul>
			<?php if ($this->auth->check('crbs.configure.authentication')): ?>
			<li>
				<?php echo anchor('authentication', lang('configure_authentication'), 'class="i configure-authentication"') ?>
				<p><?php echo lang('configure_authentication_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('users.view')): ?>
			<li>
				<?php echo anchor('users', lang('configure_users'), 'class="i configure-users"') ?>
				<p><?php echo lang('configure_users_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('groups.view')): ?>
			<li>
				<?php echo anchor('groups', lang('configure_groups'), 'class="i configure-groups"') ?>
				<p><?php echo lang('configure_groups_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('permissions.view')): ?>
			<li>
				<?php echo anchor('roles', lang('configure_permissions'), 'class="i configure-permissions"') ?>
				<p><?php echo lang('configure_permissions_description') ?></p>
			</li>
			<?php endif; ?>
		</ul>
	</div>

	<div class="grid_3">
		<h4 class="sub-heading"><?php echo lang('configure_academic') ?></h4>
		<ul>
			<?php if ($this->auth->check('years.view')): ?>
			<li>
				<?php echo anchor('academic/years', lang('configure_years'), 'class="i configure-years"') ?>
				<p><?php echo lang('configure_years_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('terms.view')): ?>
			<li>
				<?php echo anchor('academic/terms', lang('configure_terms'), 'class="i configure-terms"') ?>
				<p><?php echo lang('configure_terms_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('weeks.view')): ?>
			<li>
				<?php echo anchor('academic/weeks', lang('configure_weeks'), 'class="i configure-weeks"') ?>
				<p><?php echo lang('configure_weeks_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('holidays.view')): ?>
			<li>
				<?php echo anchor('academic/holidays', lang('configure_holidays'), 'class="i configure-holidays"') ?>
				<p><?php echo lang('configure_holidays_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('periods.view')): ?>
			<li>
				<?php echo anchor('academic/priods', lang('configure_periods'), 'class="i configure-periods"') ?>
				<p><?php echo lang('configure_periods_description') ?></p>
			</li>
			<?php endif; ?>
		</ul>
	</div>

	<div class="grid_3">
		<h4 class="sub-heading"><?php echo lang('configure_school') ?></h4>
		<ul>
			<?php if ($this->auth->check('rooms.view')): ?>
			<li>
				<?php echo anchor('rooms/manage', lang('configure_rooms'), 'class="i configure-rooms"') ?>
				<p><?php echo lang('configure_rooms_description') ?></p>
			</li>
			<?php endif; ?>
			
			<?php if ($this->auth->check('departments.view')): ?>
			<li>
				<?php echo anchor('departments', lang('configure_departments'), 'class="i configure-departments"') ?>
				<p><?php echo lang('configure_departments_description') ?></p>
			</li>
			<?php endif; ?>
		</ul>
	</div>
	
</div>