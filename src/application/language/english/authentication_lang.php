<?php

$lang = array(
	
	'authentication_global' =>
	'Global',
		
		'authentication_save_success' =>
		'Authantication settings have been updated successfully.',
		
		'authentication_save_error' =>
		'The authentication settings could not be updated. Please check and try again.',
	
		'authentication_methods' =>
		'Authentication Methods',
		
			'authentication_methods_hint' =>
			'Select which methods users can use to log in to Classroombookings. New options will be available when saved.',
		
		'authentication_enable_ldap' =>
		'Enable LDAP',
		
		'authentication_enable_preauth' =>
		'Enable Pre-authentication',
		
		'authentication_anonymous' =>
		'Anonymous user',
		
		'authentication_anonymous_hint' =>
		'To enable anonymous mode, select a user whose permissions will be used for users who do not log in.',
	
	'authentication_ldap' =>
	'LDAP',
		
		'authentication_ldap_save_success' =>
		'LDAP settings have been updated successfully.',
		
		'authentication_ldap_save_error' =>
		'The LDAP settings could not be updated. Please check and try again.',
		
		'authentication_ldap_server' =>
		'LDAP server details',
		
		'authentication_ldap_server_hint' =>
		'If you enable LDAP authentication, users who attempt to login with LDAP credentials will be added to Classroombookings if successful.<br><br>Ensure you set the <span>Default Group</span> below to ensure they inherit the correct permissions.',
		
		'authentication_ldap_host' =>
		'Hostname or IP address',
		
		'authentication_ldap_port' =>
		'TCP port number',
		
		'authentication_ldap_base' =>
		'Search base DNs',
		
		'authentication_ldap_base_hint' =>
		'Users must be a direct member of a DN. Separate multiple search DNs with a <span>;</span> semicolon.',
		
		'authentication_ldap_filter' =>
		'User query filter',
		
		'authentication_ldap_filter_hint' =>
		'Use a filter to be more specific about LDAP user validaiton.<br><br><span>%u</span> will be replaced by the username logging in.',
		
		'authentication_ldap_test' =>
		'Test settings',
		
		'authentication_ldap_test_hint' =>
		'Enter the credentials of an account on the LDAP server to test the above settings.<br><br>Settings do not have to be saved first - the test will be carried out without leaving this page, and the credentials will not be saved.',
		
		'authentication_ldap_integration' =>
		'Integration',
		
		'authentication_ldap_default_group' =>
		'Default Classroombookings group',
		
		'authentication_ldap_default_group_hint' =>
		'Specify which group users should belong to if they are not automatically assigned via LDAP Groups to local group mappings.',
		
		'authentication_ldap_update' =>
		'User details update',
		
		'authentication_ldap_update_label' =>
		'Update user details on every login',
		
		'authentication_ldap_update_hint' =>
		'If this option is enabled, the user details (display name, group and department membership) will be updated with their LDAP details every time they login.<br><br>If you manually change any of these properties for a user, they may be un-done.',
	
	'authentication_ldap_groups' =>
	'LDAP Groups',
	
		'authentication_ldap_groups_hint' =>
		'Use this page to retrieve the user groups from your LDAP server so you 
			can automatically assign members of LDAP groups to groups and 
			departments within Classroombookings. This only needs to be done once, 
			or if your LDAP groups change.<br><br>This will use the settings you 
			configured in the <strong>LDAP</strong> tab, but you must supply a username 
			and password that has the appropriate permissions to retrieve all 
			the groups. The details you enter here are used once and 
			<span>will not be saved</span>.',
	
		'authentication_ldap_groups_current' =>
		'Current LDAP groups',
		
		'authentication_ldap_groups_hostport' =>
		'Hostname and port',
		
		'authentication_ldap_groups_mode' =>
		'Mode',
		
		'authentication_ldap_groups_mode_sync' =>
		'Synchronise (all existing memberships will be maintained)',
		
		'authentication_ldap_groups_mode_reload' =>
		'Reload (clear all existing LDAP groups first)',
		
		'authentication_ldap_groups_get' =>
		'Get LDAP groups',
		
		'authentication_ldap_groups_reload_success' =>
		'The groups have been updated successfully.',
		
		'authentication_ldap_groups_sync_success' =>
		'The groups have been updated successfully.',
	
	'authentication_preauth' =>
	'Pre-authentication',
	
		'authentication_preauth_your_key' =>
		'Your key',
		
		'authentication_preauth_new_key' =>
		'Generate new key',
		
		'authentication_preauth_defaults' =>
		'Defaults',
		
		'authentication_preauth_defaults_hint' =>
		'If you would like to automatically create new user accounts via pre-authentication, specify a default group and email domain suffix.',
		
		'authentication_preauth_default_group' =>
		'Classroombookings group',
		
		'authentication_preauth_no_create' =>
		'(do not create accounts)',
		
		'authentication_preauth_email_domain' =>
		'Email address domain',
		
		'authentication_preauth_email_domain_hint' =>
		'Email addresses for accounts created using pre-authentication will be generated by taking their username and the value you enter here.',
		
		'authentication_preauth_new_key_success' =>
		'A new pre-authentication key has been created. Remember to update applications that use the old one!',
		
		'authentication_preauth_new_key_error' =>
		'Failed to generate a new key.',
		
		'authentication_preauth_save_success' =>
		'The pre-authentication defaults have been updated.',
		
		'authentication_preauth_save_error' =>
		'Failed to update the pre-authentication defaults.',
	
);