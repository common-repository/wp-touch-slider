<?php
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
} else {
	if( false !== get_option( 'wpoc_slider_settings' ) ) {
		delete_option( 'wpoc_slider_settings' );
	}
}
?>