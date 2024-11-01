<?php
/*
 * Plugin Name: OWL Carousel Slider
 * Plugin URI: https://www.wp-buy.com/
 * Description: OWL Carousel Slider is a free responsive image and content slider
 * Version: 2.2
 * Author: wp-buy
 * Author URI: https://www.wp-buy.com/
 * License: GPL2
 */

if ( !defined( 'ABSPATH' ) ) exit;

define( 'WPOC_DS', DIRECTORY_SEPARATOR );
define( 'WPOC_PLUGIN_ROOT_DIR', dirname( __FILE__ ) );
define( 'WPOC_PLUGIN_MAIN_FILE', __FILE__ );


require_once( 'functions.php' );
require_once( WPOC_PLUGIN_ROOT_DIR . WPOC_DS . 'options' . WPOC_DS . 'wpoc_options.php' );
require_once( 'widget.php' );

wpoc_init_plugin();
?>