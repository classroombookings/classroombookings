<?php

$lang['auth.log_in'] = 'Log in';
$lang['auth.log_out'] = 'Log out';

$lang['auth.bad_credentials'] = 'Incorrect username and/or password.';
$lang['auth.login_required'] = 'You must be logged in to access that page.';
$lang['auth.permission_required'] = 'You do not have required permissions to access that page.';

$lang['auth.authentication'] = 'Authentication';
$lang['auth.ldap.ldap'] = 'LDAP';
$lang['auth.ldap.connection'] = 'Connection';
$lang['auth.ldap.search'] = 'Search';
$lang['auth.ldap.user_attribute_mapping'] = 'User attribute mapping';
$lang['auth.ldap.user_assignments'] = 'Default user assignments';

$lang['auth.ldap.user_attribute_mapping.hint.1'] = 'When you use a search filter to find the authenticating user, you can populate the following classroombookings user details with attributes found in LDAP each time they log in.';
$lang['auth.ldap.user_attribute_mapping.hint.2'] = 'Combine multiple LDAP attributes by adding a colon before the attribute name, for example - ';
$lang['auth.ldap.user_attribute_mapping.hint.3'] = 'Leave these fields blank to disable automatic population.';

$lang['auth.ldap.demo_notice'] = "In the demo mode, the verification feature and ability to enable LDAP authentication are disabled to prevent account lock-outs and protect against abuse.";

$lang['auth.ldap.field.ldap_enabled'] = 'Enable';
$lang['auth.ldap.field.ldap_enabled.title'] = 'Use LDAP to authenticate users.';

$lang['auth.ldap.field.ldap_create_users'] = 'Create users';
$lang['auth.ldap.field.ldap_create_users.title'] = 'Automatically create user accounts on successful authentication.';
$lang['auth.ldap.field.ldap_create_users.hint.1'] = 'When enabled, any valid credentials returned from an LDAP authentication attempt will automatically create a classroombookings account with the Role and/or Department specified below.';
$lang['auth.ldap.field.ldap_create_users.hint.2'] = 'When not enabled, only users who have an existing account in classroombookings will be authenticated.';

$lang['auth.ldap.field.ldap_server'] = 'Server';
$lang['auth.ldap.field.ldap_server.hint'] = 'Hostname or IP address.';

$lang['auth.ldap.field.ldap_port'] = 'Port';
$lang['auth.ldap.field.ldap_port.hint'] = 'Standard ports are 389 (non-SSL) or 636 (SSL).';

$lang['auth.ldap.field.ldap_version'] = 'Protocol version';
$lang['auth.ldap.field.ldap_version.hint'] = 'Usually 3.';

$lang['auth.ldap.field.ldap_use_tls'] = 'Use TLS';
$lang['auth.ldap.field.ldap_ignore_cert'] = 'Ignore certificate';
$lang['auth.ldap.field.ldap_bind_dn_format'] = 'Bind DN format';
$lang['auth.ldap.field.ldap_bind_dn_format.hint'] = 'This will vary depending on your server and configuration. The tag `:user` will be replaced with the authenticating user. Some common formats are:';

$lang['auth.ldap.field.ldap_base_dn'] = 'Base DN';
$lang['auth.ldap.field.ldap_search_filter'] = 'Search filter';
$lang['auth.ldap.field.ldap_search_filter.hint'] = 'The tag `:user` will be replaced by the user logging in.';

$lang['auth.ldap.test.title'] = 'Verify settings';
$lang['auth.ldap.test.hint.1'] = "Configure the settings on the left then provide a username and password in this box to verify access. You do not need to click Save before verifying the settings here.";
$lang['auth.ldap.test.hint.2'] = "These credentials are passed directly to the LDAP server that you have specified and are never stored by classroombookings.";
$lang['auth.ldap.test.verify'] = "Verify credentials";
$lang['auth.ldap.test.verifying'] = "Testing connection";

$lang['auth.ldap.test.bind_dn'] = 'Bind DN';
$lang['auth.ldap.test.search_filter'] = 'Search filter';
$lang['auth.ldap.test.auth_success'] = 'Authentication success!';

$lang['auth.ldap.save.success'] = 'The LDAP settings have been updated.';

$lang['auth.ldap.error.demo_mode'] = 'Feature disabled in demo mode.';
$lang['auth.ldap.error.no_module'] = 'The PHP LDAP module is not installed or enabled.';
$lang['auth.ldap.error.no_server_or_port'] = 'No server and/or port supplied.';
$lang['auth.ldap.error.no_socket_connection'] = 'Connection error or timed out.';
$lang['auth.ldap.error.invalid_ldap_uri'] = 'Invalid LDAP connection URI.';
$lang['auth.ldap.error.no_username_or_password'] = 'No username and/or password.';
$lang['auth.ldap.error.bind_error'] = 'LDAP bind error or bad username and/or password.';
$lang['auth.ldap.error.search_error'] = 'LDAP search error.';
$lang['auth.ldap.error.search_num_results_error'] = 'LDAP search did not return exactly one result.';
$lang['auth.ldap.error.search_get_entry_error'] = 'LDAP get search result entry error.';
$lang['auth.ldap.error.search_get_attributes_error'] = 'LDAP get user attributes error.';
