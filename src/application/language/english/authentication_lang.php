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
	
	'authentication_preauth' =>
	'Pre-authentication',
	
);