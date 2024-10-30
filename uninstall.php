<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
function hiper_cookie_consent_delete_plugin() {
	global $wpdb;
	delete_option('hiper_cookie_consent_on');
	delete_option('hiper_cookie_consent_message');
}
hiper_cookie_consent_delete_plugin();