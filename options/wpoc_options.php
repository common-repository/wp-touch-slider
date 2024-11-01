<?php

final class WPOC_Options {
	
	private static $options = NULL;
	private static $default_slider_options = array(
		'responsive'              => '1',
		'autoPlay'                => '0',
		'autoPlayTime'            => 5,
		'paginationSpeed'         => 0.8,
		'slideSpeed'              => 0.2,
		'pagination'              => '1',
		'paginationNumbers'       => '0',
		'lazyLoad'                => '0',
		'lazyFollow'              => '1',
		'lazyEffect'              => '1',
		'dragBeforeAnimFinish'    => '1',
		'mouseDrag'               => '1',
		'touchDrag'               => '1',
		'navigation'              => '0',
		'navigationPrev'          => 'prev',
		'navigationNext'          => 'next',
		'scrollPerPage'           => '0',
		'stopOnHover'             => '0',
		'rewindNav'               => '1',
		'rewindSpeed'             => '1',
		'height'                  => 280,
		'itemsScaleUp'            => '0',
		'singleItem'              => '0',
		'autoHeight'              => '0',
		'itemsDesktop' 	          => 4, // 1199
		'itemsDesktopSmall' 	  => 3, // 979
		'itemsTablet' 	          => 2, // 768
		'itemsMobile' 	          => 1, // 479
	);
	
	
	private static $default_slide_options = array(
		'type'               => 'default',
		'backgroundColor'    => '',
		'iconType'           => 'font-awesome',
		'icon'               => '',
		'iconSize'           => 86,
		'iconColor'          => '000000',
		'largeTitle'         => '',
		'largeTitleColor'    => '000000',
		'smallTitle'         => '',
		'smallTitleColor'    => '000000',
		'slideUrl'           => '',
		'urlPlace'           => 'slide',
		'slideUrlTarget'     => '_self',
		'iconImageUrl'       => '',
		'largeTitleStyles'   => '',
		'smallTitleStyles'   => '',
		'customText'         => '',
		'videoType'          => 'youtube',
		'videoId'            => '',
		'videoAutoplay'      => '0',
	);
			
	/**
	 * Returns the plugin options
	 *
	 * @uses get_option()
	 *
	 * @return object
	 */
	public static function get_options() {
		if( is_null( static::$options ) ) {
			static::$options = json_decode( json_encode( get_option( 'wpoc_slider_settings' ) ), false );
		}
		return static::$options;
	}
	
	/**
	 * Called when plugin is activated or upgraded
	 *
	 * @uses add_option()
	 * @uses get_option()
	 * @uses update_option()
	 *
	 * @return void
	 */
	public static function set_options( $options = array() ) {
		$defaults = array(
			'version' => '1.0',
			'privilege' => 'manage_options',
		);
		
		$options = wp_parse_args( $options, $defaults );
		
		if( false == get_option( 'wpoc_slider_settings' ) ) {
			add_option( 'wpoc_slider_settings', $options );
		} else {
			update_option( 'wpoc_slider_settings', $options );
		}
	}
	
	
	public static function get_slider_defaults(){
		return static::$default_slider_options;
	}
	
	public static function get_slide_defaults(){
		return static::$default_slide_options;
	}
}