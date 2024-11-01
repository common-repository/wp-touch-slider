<?php
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
} else {
	if( false !== get_option( 'wpoc_slider_settings' ) ) {
		delete_option( 'wpoc_slider_settings' );
	}		global $wpdb;		$queries = array();	$queries[] = "DROP TABLE IF NOT EXISTS `{$wpdb->prefix}wpoc_sliders`";	$queries[] = "DROP TABLE IF NOT EXISTS `{$wpdb->prefix}wpoc_slides`";	foreach( $queries as $sql ) {		$wpdb->query( $sql );	}
}
?>