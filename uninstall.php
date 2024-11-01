<?php 

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if(  is_admin() ) {

	global $wpdb;
	$plugin_name = 'alti_tlg';

	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . $plugin_name . '_links' );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . $plugin_name . '_mediums' );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . $plugin_name . '_sources' );

	delete_option( $plugin_name . '_version' );
	delete_option( $plugin_name . '_bitly_token' );

}