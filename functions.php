<?php

/**
 * Called when plugin is activated or upgraded
 *
 * @return void
 */
function wpoc_activate_plugin() {
	WPOC_Options::set_options();
	set_time_limit(120);
	wpoc_create_tables();
}
//---------------------------------------------------
/**
 * Hooks functions to certain actions
 *
 * @uses register_activation_hook()
 * @uses add_action()
 * @uses add_shortcode()
 *
 * @return void
 */
function wpoc_init_plugin() {
	register_activation_hook( WPOC_PLUGIN_MAIN_FILE, 'wpoc_activate_plugin' );
	add_action( 'admin_menu', 'wpoc_add_menu_pages' );
	add_action( 'admin_enqueue_scripts', 'wpoc_admin_scripts' );
	add_action( 'admin_head', 'wpoc_admin_head' );
	add_action( 'widgets_init', 'wpoc_register_widget' );
	add_action( 'init', 'wpoc_plugin_ini' );
	add_shortcode( 'wpoc_slider', 'wpoc_do_shortcode' );
	add_action( 'wp_ajax_wpoc_create_slide', 'wpoc_create_new_slide' );
	add_action( 'wp_ajax_wpoc_delete_slider', 'wpoc_delete_slider' );
	
}
//---------------------------------------------------
/**
 * Creates plugin's tables
 *
 * @uses wpdb::query()
 *
 * @return void
 */
function wpoc_create_tables( $call_times = 0 ) {
	global $wpdb;
	
	$queries = array();
	$queries[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wpoc_sliders` (
					`sdr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`sdr_name` varchar(200) NOT NULL,
					`sdr_options` TEXT NOT NULL,
					PRIMARY KEY (`sdr_id`)
				) ENGINE = InnoDB DEFAULT CHARSET=utf8";
				
	$queries[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wpoc_slides` (
					`sld_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					`sdr_id` int(11) unsigned NOT NULL,
					`sld_options` TEXT,
					PRIMARY KEY (`sld_id`)
				) ENGINE = InnoDB DEFAULT CHARSET=utf8";
				
	foreach( $queries as $sql ) {
		if( false === $wpdb->query( $sql ) ) {
			if( 0 === $call_times ) {
				return wpoc_create_tables( 1 );
			} else {
				return false;
			}
		}
	}
}


//---------------------------------------------------
/**
 * Add rate us box
 *
 * @uses add_menu_page()
 * @uses add_submenu_page()
 *
 * @return HTML
 */

function touch_rate_us($plugin_url, $box_color='#1D1F21'){
	
	$ret = '
	<style type="text/css">
	
	.rate_box{
		width:90%;
		
		margin:10px;
		-webkit-border-radius: 7px;
		-moz-border-radius: 7px;
		border-radius: 7px;
		padding:5px;
		text-align:left !important;
		
	}
	.rating > span {
	  display: inline-block;
	  position: relative;
	  width: 1.1em;
	  font-size:22px;
	}
	.rating {
	  unicode-bidi: bidi-override;
	  direction: rtl;
	  
	  
	}
	.link_wp{
		
		color:#EDAE42 !important
	}
	
	.rating > span:hover:before,
	.rating > span:hover ~ span:before {
	   content: "\2605";
	   position: absolute;
	   color:yellow;
	}
	</style>';
	
	$ret .= '<div class="row rate_box">
<div class="col-md-7">
<p>
<strong>Do you like this plugin?</strong><br /> Please take a few seconds to <a class="link_wp" href="'.$plugin_url.'" target="_blank">rate it on WordPress.org!</a></p>
</div>
<div class="col-md-3">
<div class="rating">';

	for($r=1; $r<=5; $r++)
	{
		
		$ret .= '<span onclick="window.open(\''.$plugin_url.'\',\'_blank\')">☆</span>';
	}

$ret .= '</div>
</div>
</div>';
return $ret;
}



//---------------------------------------------------
/**
 * Adds admin menu pages
 *
 * @uses add_menu_page()
 * @uses add_submenu_page()
 *
 * @return void
 */
function wpoc_add_menu_pages() {
	add_menu_page( 'All Sliders', 'OWL Carousel', WPOC_Options::get_options()->privilege, 'wpoc_sliders', 'wpoc_create_main_page', 'dashicons-format-gallery', '60.1253' );
	add_submenu_page( 'wpoc_sliders', 'Add\Edit Slider', 'Add New', WPOC_Options::get_options()->privilege, 'wpoc_edit', 'wpoc_create_edit_page' );
	add_submenu_page( NULL, NULL, NULL, WPOC_Options::get_options()->privilege, 'wpoc_save', 'wpoc_save_slider' );
}
//---------------------------------------------------
/**
 * Creates main page
 *
 * @uses wp_die()
 *
 * @return void
 */
function wpoc_create_main_page() {
	if( !current_user_can( WPOC_Options::get_options()->privilege ) ) {
		wp_die( __( 'You do not have permissions to access this page!', 'wpoc_slider' ) );
	}
	include_once( 'views' . WPOC_DS . 'sliders-view.php' );
}
//---------------------------------------------------
/**
 * Creates slider page
 *
 * @uses is_wp_error()
 *
 * @return void
 */
function wpoc_create_edit_page() {
	if( !current_user_can( WPOC_Options::get_options()->privilege ) ) {
		wp_die( __( 'You do not have permissions to access this page!', 'wpoc_slider' ) );
	}
	
	$sdr_id = ( isset( $_GET['id'] ) )? $_GET['id'] : 0;
	$slider = $slides = NULL;
	if( !empty( $sdr_id ) ) {
		$slider = wpoc_read_slider( $sdr_id );
		$slides = wpoc_read_slides( $sdr_id );
	}
	
	$options = array();
	$sdr_name = '';
	if( is_object( $slider ) && !is_wp_error( $slider ) && !is_wp_error( $slides ) && is_array( $slides ) ) {
		$options = unserialize( $slider->sdr_options );
		$sdr_name = $slider->sdr_name;
	}
	$options = wp_parse_args( $options, WPOC_Options::get_slider_defaults() );
	extract( $options );
	
	include_once( 'views' . WPOC_DS . 'slider.php' );
}
//---------------------------------------------------
/**
 * Retrieves a slider
 *
 * @uses wpdb::get_row()
 * @uses wpdb::prepare()
 *
 * @return object
 */
function wpoc_read_slider( $sdr_id ) {
	global $wpdb;
	$sql = "SELECT * FROM `{$wpdb->prefix}wpoc_sliders` WHERE sdr_id = %d";
	return $wpdb->get_row( $wpdb->prepare( $sql, $sdr_id ), OBJECT );
}
//---------------------------------------------------
/**
 * Enqueues slider pages scripts
 *
 * @uses wp_enqueue_script()
 * @uses wp_enqueue_style()
 * @uses plugins_url()
 *
 * @return void
 */
function wpoc_admin_scripts() {
	global $current_screen;
	
	$sliders = preg_match( '/^.*wpoc_sliders$/', $current_screen->id );
	$edit = preg_match( '/^.*wpoc_edit$/', $current_screen->id );
	
	if( $sliders || $edit ) {
		wp_enqueue_style( 'bootstrap', plugins_url( 'lib/bootstrap/css/bootstrap-wrapper.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'bootstrap-theme', plugins_url( 'lib/bootstrap/css/bootstrap-theme-wrapper.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'font-awesome4.5', plugins_url( 'lib/font-awesome/css/font-awesome.min.css', WPOC_PLUGIN_MAIN_FILE ) );
		
		if( is_rtl() ) {
			wp_enqueue_style( 'bootstrap-rtl', plugins_url( 'lib/bootstrap-rtl/dist/css/bootstrap-rtl-wrapper.css', WPOC_PLUGIN_MAIN_FILE ) );
		}
		
		if( is_rtl() ) {
			wp_enqueue_style( 'wpoc-admin-rtl', plugins_url( 'css/admin-rtl.css', WPOC_PLUGIN_MAIN_FILE ) );
		} else {
			wp_enqueue_style( 'wpoc-admin', plugins_url( 'css/admin.css', WPOC_PLUGIN_MAIN_FILE ) );
		}
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'bootstrap', plugins_url( 'lib/bootstrap/js/bootstrap.min.js', WPOC_PLUGIN_MAIN_FILE ), array('jquery') );
		
		wp_enqueue_script( 'wpoc-admin-js', plugins_url( 'js/admin.js', WPOC_PLUGIN_MAIN_FILE ) );
	}
	
	if( $edit ) {
		wp_enqueue_style( 'IcoMoon-icons', plugins_url( 'lib/IcoMoon/IcoMoon.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'jquery-ui', plugins_url( 'lib/jquery-ui.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'colpick', plugins_url( 'lib/colpick/css/colpick.css', WPOC_PLUGIN_MAIN_FILE ) );
		
		wp_enqueue_script( 'jquery-effects-core' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		wp_enqueue_script( 'bootstrap-checkbox', plugins_url( 'lib/bootstrap-checkbox/bootstrap-checkbox.min.js', WPOC_PLUGIN_MAIN_FILE ) );

		wp_enqueue_style( 'owl-css', plugins_url( 'lib/owl-carousel/owl.carousel.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'owl-theme', plugins_url( 'lib/owl-carousel/owl.theme.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'codemirror', plugins_url( 'lib/codemirror/lib/codemirror.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'codemirror-theme', plugins_url( 'lib/codemirror/theme/neat.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_script( 'owl-js', plugins_url( 'lib/owl-carousel/owl.carousel.js', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_script( 'IconMoon-liga', plugins_url( 'lib/IcoMoon/liga.js', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_script( 'colpick', plugins_url( 'lib/colpick/js/colpick.js', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_script( 'codemirror', plugins_url( 'lib/codemirror/lib/codemirror.js', WPOC_PLUGIN_MAIN_FILE, array(), '5.1.1' ) );
		wp_enqueue_script( 'codemirror-mode', plugins_url( 'lib/codemirror/mode/css/css.js', WPOC_PLUGIN_MAIN_FILE, array('codemirror') ) );
		wp_enqueue_script( 'codemirror-selection', plugins_url( 'lib/codemirror/addon/selection/active-line.js', WPOC_PLUGIN_MAIN_FILE, array('codemirror') ) );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_media();
	}
}
//---------------------------------------------------
/**
 * called when plugin initialize
 *
 * @uses load_theme_textdomain()
 *
 * @return void
 */
function wpoc_plugin_ini(){
	wpoc_add_sliders_list_to_editor();
	load_theme_textdomain( 'wpoc_slider', WPOC_PLUGIN_ROOT_DIR . WPOC_DS . 'languages' );
}
//---------------------------------------------------
/**
 * Reads sliders for sliders table
 *
 * @uses wpdb::get_var()
 * @uses wpdb::get_results()
 *
 * @param $filters array
 * @param $limit integer
 * @param $offset integer
 *
 * @return array
 */
function wpoc_read_sliders( $filters = '', $limit = 0, $offset = 0 ) {
	global $wpdb;
	
	$wheCnd = " WHERE 1 = 1";
	$wheCnd .= ( isset( $filters['sdr_name'] ) )? " AND sdr_name = {$filters['sdr_name']}" : "";
	
	$response = array('data' => array());
	
	$conSql = "SELECT COUNT(sdr_id) cnt FROM {$wpdb->prefix}wpoc_sliders {$wheCnd}";
	$response['total'] =  $wpdb->get_var( $conSql );
	
	$sql = "SELECT sdr_id, sdr_name  
			FROM {$wpdb->prefix}wpoc_sliders {$wheCnd}";
	$sql .= ( !empty( $limit ) )? " LIMIT {$limit} OFFSET {$offset}" : "";
	
	$results = $wpdb->get_results( $sql );
	
	if( false !== $results ) {
		$response['data'] = $results;
	}
	return $response;
}
//---------------------------------------------------
/**
 * Puts general js variables in the admin head
 *
 * @uses admin_url()
 * @uses get_bloginfo()
 * @uses current_user_can()
 * @uses plugins_url()
 *
 * @return void
 */
function wpoc_admin_head() {
	global $pagenow;
	?>
	<style type="text/css">
		i.mce-i-wpoc-slider-icon{
			background-image: url(<?php echo plugins_url( 'images/slider_icon_36.png', WPOC_PLUGIN_MAIN_FILE ); ?>);
		}
	</style>
	<script type="text/javascript">
		var wpoc_admin_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		var wpoc_wordpress_ver = '<?php echo get_bloginfo( 'version' ); ?>';
		var wpoc_you_sure = '<?php _e( 'Are you sure?', 'wpoc_slider' ); ?>';
		var wpoc_empty_background = '<?php echo plugins_url( 'images/empty_background.png', WPOC_PLUGIN_MAIN_FILE ); ?>';
	<?php
	if( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) && current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
		$sliders = wpoc_sliders_list();
		$arr = array();
		if( get_bloginfo( 'version' ) >= 3.9 ) {
			foreach( $sliders as $s ) {
				$arr[] = array(
					'name' => $s->sdr_name,
					'shortcode' => '[wpoc_slider id="' . $s->sdr_id . '"]',
				);
			}
		} else{
			foreach( $sliders as $s ) {
				$arr[] = '[wpoc_slider id="' . $s->sdr_id . '"]';
			}
		}
		if( !empty( $arr ) ) {
		?>
		var wpoc_slider_shortcode = <?php echo json_encode( $arr ) ?>;
		<?php
		}
	}
	?>
	</script>
	<?php
}
//---------------------------------------------------
/**
 * pagination
 *
 * @param string $link
 * @param integer $total
 * @param integer $per_page
 * @param integer $current_page
 * @param integer $total_links
 *
 * @return string
 */
function wpoc_pagination( $link, $total, $per_page, $current_page, $total_links = 7 ) {
	$total_pages = (int) ceil( $total / $per_page );
	$half_links = (int) ceil( ( $total_links - 1 ) / 2 );
	$links = array();
	
	if( $total_pages <= $total_links ) {
		for( $c = 1; $c <= $total_pages; $c++ ){
			$links[] = $c;
		}
	} else {
		$start = $current_page - $half_links;
		$end = $current_page + $half_links;
		
		if( $start < 1 ) {
			$end += ( - $start ) + 1;
			$start = 1;
		}
		else if( $end > $total_pages ) {
			$start -= $total_links - $end;
			$end = $total_links;
		}
		
		if( $start > 1 ) {
			$links[] = '';
		}
		
		for( $c = $start; $c <= $end; $c++ ) {
			$links[] = $c;
		}
		
		if( $end < $total_pages ) {
			$links[] = '';
		}
	}
	
	$out = '<nav>
		<ul class="pagination">
			<li ' . ( ( $current_page == 1 )? ' class="disabled"' : '' ) . '>
				<a href="' . ( ( $current_page == 1 )? 'javascript:void(0)' : $link . ( $current_page - 1 ) ) . '" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>';
			
    foreach( $links as $l ) {
		$out .= '<li ' . ( ( $current_page == $l )? ' class="active"' : '' ) . '>';
		if( !empty( $l ) ) {
			$out .= '<a href="' . $link . $l .'">' . $l . ( ( $current_page == $l )? ' <span class="sr-only">(current)</span>' : '' ) . '</a>';
		} else {
			$out .= '<a href="javascript:void(0);">..</span></a>';
		}
		$out .= '</li>';
	}
	
    $out .= '<li' . ( ( $current_page == $total_pages )? ' class="disabled"' : '' ) . '>
			  <a href="' . ( ( $current_page == $total_pages )? 'javascript:void(0)' : $link . ( $current_page + 1 ) ) . '" aria-label="Next">
				<span aria-hidden="true">&raquo;</span>
			  </a>
			</li>
		</ul>
	</nav>';
	
	$out .= '<div>' . __( 'Page', 'shs_lang' ) . ' ' . $current_page . ' ' . __( 'of', 'shs_lang' ) . ' ' . $total_pages . ' ' . __( 'pages', 'shs_lang' ) . '</div>';
	
	return $out;
}
//---------------------------------------------------
/**
 * Retrieves a slider slides
 *
 * @uses wpdb::get_results()
 * @uses wpdb::prepare()
 *
 * @param $sdr_id integer
 *
 * @return object
 */
function wpoc_read_slides( $sdr_id ) {
	global $wpdb;
	$sql = "SELECT * FROM `{$wpdb->prefix}wpoc_slides` WHERE sdr_id = %d";
	return $wpdb->get_results( $wpdb->prepare( $sql, $sdr_id ), OBJECT );
}
//---------------------------------------------------
/**
 * Retrieves single slide
 *
 * @uses wpdb::get_row()
 * @uses wpdb::prepare()
 *
 * @param $sld_id integer
 *
 * @return object
 */
function wpoc_read_slide( $sld_id ) {
	global $wpdb;
	$sql = "SELECT * FROM `{$wpdb->prefix}wpoc_slides` WHERE sld_id = %d";
	return $wpdb->get_row( $wpdb->prepare( $sql, $sld_id ), OBJECT );
}
//---------------------------------------------------
/**
 * Renders html slide options
 *
 * @uses wp_parse_args()
 * @uses selected()
 *
 * @param $slide_index integer
 * @param $slide_order integer
 * @param $slide object
 *
 * @return string
 */
function wpoc_render_slide_options( $slide_index, $slide_order, $slide = NULL ) {
	$options = array();
	if( is_object( $slide ) ) {
		$options = $slide->sld_options;
		$options = unserialize( $options );
	}
	$options = wp_parse_args( $options, WPOC_Options::get_slide_defaults() );
	extract( $options );
	
	$title  = '&nbsp;';
	$iconClass  = '';
	switch( $type ) {
		case 'default':
		$title = __( 'Default', 'wpoc_slider' );
		$iconClass = 'fa-picture-o';
		break;
		
		case 'video':
		$title = __( 'Video', 'wpoc_slider' );
		$iconClass = 'fa-youtube-play';
		break;
		
		case 'text':
		$title = __( 'Custom text', 'wpoc_slider' );
		$iconClass = 'fa-file-text-o';
		break;
	}
	
	$out = '<li id="slide_element_' . $slide_index . '">
		<div class="collapsible-panel">
			<div class="header"><span class="icon colapsingToggle"><i class="fa ' . $iconClass . '"></i></span><span class="colapsingToggle title">' . $title . '</span>
			<a class="action delete" href="javascript:void(0)" onclick="wpoc_removeSlide(' . $slide_index . ', event)"><i class="fa fa-times"></i></a>';
	if( is_object( $slide ) ) {
		//$out .= '<a class="action duplicate" href="javascript:void(0)" onclick="wpoc_duplicateSlide(' . $slide->sld_id . ', event)"><i class="fa fa-files-o"></i></a>';
	} else {
		$out .= '<span class="action disabled"><i class="fa fa-files-o"></i></span>';
	}
	$out .= '</div>
			<div class="body" onclick="set_default_preview(' . $slide_index . ')">
				<input type="hidden" name="slides[' . $slide_index . '][order]" id="slides_order_' . $slide_index . '" value="' . $slide_order . '" />
				<div class="row">
					<div class="form-group col-lg-6 col-sm-4 col-xs-12 wpoc-field">
						<label for="slides_type_' . $slide_index . '">' . __( 'Type', 'wpoc_slider' ) . '</label>
						<select name="slides[' . $slide_index . '][type]" id="slides_type_' . $slide_index . '" onchange="wpoc_slideTypeChange(this)">
							<option value="default" ' . selected( $type, 'default', false ) . '>' . __( 'Default (Icon or Image) Slide', 'wpoc_slider' ) . '</option>
							<option value="video" ' . selected( $type, 'video', false ) . '>' . __( 'Video Slide', 'wpoc_slider' ) . '</option>
							<option value="text" ' . selected( $type, 'text', false ) . '>' . __( 'Custom text Slide', 'wpoc_slider' ) . '</option>
			
						</select>
					</div>
				</div>' .
				wpoc_render_default_container( $options, $slide_index, ( 'default' == $type ) ) .
				wpoc_render_video_container( $options, $slide_index, ( 'video' == $type ) ) .
				wpoc_render_text_container( $options, $slide_index, ( 'text' == $type ) ) .
			'</div>
		</div>
		<div class="cleaner"></div>
	</li>';
	return $out;
}
//---------------------------------------------------
/**
 * Renders html default slide options
 *
 * @uses selected()
 *
 * @param $options array
 * @param $index integer
 * @param $display boolean
 *
 * @return string
 */
function wpoc_render_default_container( $options, $index, $display ) {
	$display = ( $display )? 'block' : 'none';
	extract( $options );
	
	$largeTitleStyles = (!empty($largeTitleStyles)) ? $largeTitleStyles : 'font-size: 30px;
font-weight: 300;
margin: 25px 0 15px;';

	$smallTitleStyles = (!empty($smallTitleStyles)) ? $smallTitleStyles : 'margin: 5px 0 0;
font-size: 18px;';



	$out = '<section class="row" id="default_container_' . $index . '" style="display: ' . $display . ';">
				<div class="col-lg-12">' .
				wpoc_get_icons_tabs( $options, $index ) .
				'</div>
				
				<div class="col-lg-12">
					<div class="row">
						<div class="col-lg-11 col-lg-offset-1 col-md-11 col-md-offset-1 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1">
							<div class="wpoc-default-preview" id="default_preview_' . $index . '" 
							style="background-image: url(' . plugins_url( 'images/empty_background.png', WPOC_PLUGIN_MAIN_FILE ) . ');"></div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-6 wpoc-field">
							<label>' . __( 'Background color', 'wpoc_slider' ) . '</label>
							<div id="bgcl_picker_' . $index . '" class="wpoc-color-picker"></div>
							<input type="hidden" name="slides[' . $index . '][backgroundColor]" id="slides_backgroundColor_' . $index . '" value="' .
							$backgroundColor . '" autocomplete="off" />
						</div>
						
						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 wpoc-field">
							<label>' . __( 'Selected icon', 'wpoc_slider' ) . '</label>
							<div class="wpoc-selected-icon" id="selected_icon_' . $index . '">' .
								( ( !empty( $icon ) )? '<i class="fa fa-' . $icon . '"></i>' : '') .
							'</div>
							<input type="hidden" name="slides[' . $index . '][icon]" id="slides_icon_' . $index . '" value="' . $icon . '" 
							autocomplete="off" />
							
							<input type="hidden" name="slides[' . $index . '][iconType]" id="slides_iconType_' . $index . '" value="' . $iconType . '" 
							autocomplete="off" />
						</div>
						
						<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 wpoc-field">
							<label>' . __( 'Icon color', 'wpoc_slider' ) . '</label>
							<div id="icocl_picker_' . $index . '" class="wpoc-color-picker"></div>
							<input type="hidden" name="slides[' . $index . '][iconColor]" id="slides_iconColor_' . $index . '" value="' . $iconColor . '" 
							autocomplete="off" />
						</div>
						
						<div class="form-group col-lg-6 col-md-6 col-sm-7 col-xs-12 wpoc-field">
							<label id="slides_iconSize_' . $index . '_value">' . __( 'Icon\Image size', 'wpoc_slider' ) . ' - <span class="wpoc-slider-value"> ' . $iconSize . __( 'px.', 'wpoc_slider' ) . '</span></label>
							<div id="slides_iconSize_' . $index . '_slider"></div>
							<input type="hidden" name="slides[' . $index . '][iconSize]" id="slides_iconSize_' . $index . '" value="' . $iconSize . '" 
							autocomplete="off" />
							<br/>
							
						</div>
						
					</div>
					
				</div>
				
				<div class="col-lg-12">
					<br />
					<div class="row">
						
						<div class="form-group col-lg-4 col-md-5 col-sm-5 col-xs-12 wpoc-field">
							<label>' . __( 'Image instead of icon', 'wpoc_slider' ) . '</label>
							<input type="text" class="form-control wpoc-full-width" name="slides[' . $index . '][iconImageUrl]" 
							id="slides_iconImageUrl_' . $index . '" value="' . $iconImageUrl . '" autocomplete="off" />
						</div>
						
						<div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-6 wpoc-no-label">
							<button type="button" class="btn btn-default" id="add_icon_img_btn_' . $index . '" 
							onclick="wpoc_add_image(\'wpoc_select_icon_img\', ' . $index . ')" 
							style="' . ( ( !empty( $iconImageUrl ) )? 'display: none;' : '' ) . '"><i class="fa fa-picture-o"></i>&nbsp;' .
							__( 'select', 'wpoc_slider' ) . '</button>
							
							<button type="button" class="btn btn-danger" id="remove_icon_img_btn_' . $index . '" 
							onclick="wpoc_remove_icon_image(' . $index . ')" style="' . ( ( empty( $iconImageUrl ) )? 'display: none;' : '' ) . '">
							<i class="fa fa-times"></i>&nbsp;' . __( 'remove', 'wpoc_slider' ) . '</button>
						</div>
						
						
						<div class="form-group col-lg-4 col-md-4 col-md-offset-1 col-sm-5 col-xs-12 wpoc-field">
							<span id="icon_image_wrapper_' . $index . '" class="wpoc-icon-image">' .
							( ( !empty( $iconImageUrl ) )? '<img src="' . $iconImageUrl . '" />' : '' ) .
							'</span>
						</div>
					</div>
				</div>
				
				<div class="col-lg-12">
					<div class="row">
						
						
						<div class="form-group col-lg-5 col-md-5 col-sm-6 col-xs-12 wpoc-field">
							<label>' . __( 'Large title', 'wpoc_slider' ) . '</label>
							<input type="text" class="form-control wpoc-full-width" name="slides[' . $index . '][largeTitle]" id="slides_largeTitle_' . $index .
							'" value="' . $largeTitle . '" onblur="set_default_preview(' . $index . ')" autocomplete="off" />
						</div>
						
						<div class="form-group col-lg-3 col-md-4 col-sm-4 col-xs-8 wpoc-field">
							<label>' . __( 'Large title color', 'wpoc_slider' ) . '</label>
							<div id="ltcl_picker_' . $index . '" class="wpoc-color-picker"></div>
							<input type="hidden" name="slides[' . $index . '][largeTitleColor]" id="slides_largeTitleColor_' . $index . '" 
							value="' . $largeTitleColor . '" autocomplete="off" />
						</div>
						
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 wpoc-field">
							<label>' . __( 'Large title styles', 'wpoc_slider' ) . '</label>
							<textarea name="slides[' . $index . '][largeTitleStyles]" id="slides_largeTitleStyles_' . $index . '">' .
							$largeTitleStyles . '</textarea>
						</div>
					</div>
				</div>
				
				<div class="col-lg-12">
					<div class="row">						
						<div class="form-group col-lg-5 col-md-5 col-sm-6 col-xs-12 wpoc-field">
							<label>' . __( 'Small title', 'wpoc_slider' ) . '</label>
							<input type="text" class="form-control wpoc-full-width" name="slides[' . $index . '][smallTitle]" id="slides_smallTitle_' . $index . '" 
							value="' . $smallTitle . '" onblur="set_default_preview(' . $index . ')" autocomplete="off" />
						</div>
						
						<div class="form-group col-lg-3 col-md-4 col-sm-4 col-xs-8 wpoc-field">
							<label>' . __( 'Small title color', 'wpoc_slider' ) . '</label>
							<div id="stcl_picker_' . $index . '" class="wpoc-color-picker"></div>
							<input type="hidden" name="slides[' . $index . '][smallTitleColor]" id="slides_smallTitleColor_' . $index . '" value="' .
							$smallTitleColor . '" autocomplete="off" />
						</div>
						
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 wpoc-field">
							<label>' . __( 'Small title styles', 'wpoc_slider' ) . '</label>
							<textarea name="slides[' . $index . '][smallTitleStyles]" id="slides_smallTitleStyles_' . $index . '">' .
							$smallTitleStyles . '</textarea>
						</div>

					</div>
				</div>
			</section>
			<script type="text/javascript">
				jQuery(function(){
					jQuery("#slides_iconSize_' . $index . '_slider").slider({
						max: 400,
						min: 32,
						step: 2,
						value: ' . $iconSize . ',
						stop: function(evt, ui){
							jQuery("#slides_iconSize_' . $index . '").val(ui.value);
							set_default_preview(' . $index . ');
							jQuery("#slides_iconSize_' . $index . '_value").html("' . __( 'Icon size', 'wpoc_slider' ) . ' - <span class=\"wpoc-slider-value\">" + ui.value + " ' . __( 'px.', 'wpoc_slider' ) . '</span>");
						}
					});
					
					init_color_picker("bgcl_picker_' . $index . '", "slides_backgroundColor_' . $index . '");
					init_color_picker("icocl_picker_' . $index . '", "slides_iconColor_' . $index . '");
					init_color_picker("ltcl_picker_' . $index . '", "slides_largeTitleColor_' . $index . '");
					init_color_picker("stcl_picker_' . $index . '", "slides_smallTitleColor_' . $index . '");
					
					var lts_codemirror_' . $index . ' = CodeMirror.fromTextArea(document.getElementById("slides_largeTitleStyles_' . $index . '"), {
						mode: "css",
						lineNumbers: true,
						theme: "neat",
						styleActiveLine: true,
						matchBrackets: true
					});
					lts_codemirror_' . $index . '.on("blur", function(instance){
							jQuery("#slides_largeTitleStyles_' . $index . '").val(instance.getValue());
							set_default_preview(' . $index . ');
					});
					
					var sts_codemirror_' . $index . ' = CodeMirror.fromTextArea(document.getElementById("slides_smallTitleStyles_' . $index . '"), {
						mode: "css",
						lineNumbers: true,
						theme: "neat",
						styleActiveLine: true,
						matchBrackets: true,
						blur: function(instance){
							jQuery("#slides_smallTitleStyles_' . $index . '").val(instance.getValue());
							set_default_preview(' . $index . ');
						}
					});
					sts_codemirror_' . $index . '.on("blur", function(instance){
							jQuery("#slides_smallTitleStyles_' . $index . '").val(instance.getValue());
							set_default_preview(' . $index . ');
					});
					
					set_default_preview(' . $index . ');
				});
			</script>';
	return $out;
}
//---------------------------------------------------
/**
 * Renders html video slide options
 *
 * @uses selected()
 *
 * @param $options array
 * @param $index integer
 * @param $display boolean
 *
 * @return string
 */
function wpoc_render_video_container( $options, $index, $display ) {
	extract( $options );
	$display = ( $display )? 'block' : 'none';
	
	
	
	$out = '<section class="row" id="video_container_' . $index . '" style="display: ' . $display . ';">
				<div style="padding:10px;"><img src="'.plugins_url( 'images/video-pro.jpg', WPOC_PLUGIN_MAIN_FILE ).'"><br />
				<p>please <b style="color:red"><a target="_blank" href="http://www.wp-buy.com/product/responsive-metro-slider-pro/">upgrade</a></b> to use this feature</p>
				</div>
			</section>
			<script type="text/javascript">
				jQuery(function(){
					jQuery("#slides_videoAutoplay_' . $index . '").checkboxpicker();
				});
			</script>';
	return $out;
}
//---------------------------------------------------
/**
 * Renders html text slide options
 *
 * @param $options array
 * @param $index integer
 * @param $display boolean
 *
 * @return string
 */
function wpoc_render_text_container( $options, $index, $display ) {
	$display = ( $display )? 'block' : 'none';
	extract( $options );
	$out = '<section class="row" id="text_container_' . $index . '" style="display: ' . $display . ';">
				<div class="form-group col-lg-12 wpoc-field">
					<label>' . __( 'Custom text', 'wpoc_slider' ) . '</label>
					<textarea name="slides[' . $index . '][customText]" id="slides_customText_' . $index . '" rows="5" 
					class="wpoc-full-width">' . $customText . '</textarea>
					<span class="littleNote">' . __( 'put here any text, html, shortcode', 'wpoc_slider' ) . '</span>
				</div>
			</section>';
	return $out;
}
//---------------------------------------------------
/**
 * Renders icons' tabs
 *
 * @param $options array
 * @param $index integer
 *
 * @return string
 */
function wpoc_get_icons_tabs( $options, $index ) {
	extract( $options );
	$fontawesome = wpoc_get_fontawesome_fonts();
	$icomoon = wpoc_get_icomoon_fonts();
	$out = '<div role="tabpanel">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="' . ( ( 'font-awesome' == $iconType )? 'active' : '' ) . '"><a href="#font_awesome_' . $index . '" aria-controls="font_awesome_' . $index . '" role="tab" data-toggle="tab">font awesome</a></li>
			<li role="presentation" class="' . ( ( 'ico-moon' == $iconType )? 'active' : '' ) . '"><a href="#ico_moon_' . $index . '" aria-controls="ico_moon_' . $index . '" role="tab" data-toggle="tab">ico moon</a></li>
		</ul>
		
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane ' . ( ( 'font-awesome' == $iconType )? 'active' : '' ) . ' wpoc-tab-pane" id="font_awesome_' . $index . '">
				<div class="wpoc-fonts-container">';
			
	foreach( $fontawesome as $ico ) {
		$out .= '<div class="wpoc-font-icon" title="' . $ico . '" onclick="setFontawesomeIcon(\'' . $ico . '\', ' . $index . ')">
					<i class="fa fa-' . $ico . '"></i>
				</div>';
	}
	
	$out .= '</div>
			</div>
			
			<div role="tabpanel" class="tab-pane ' . ( ( 'ico-moon' == $iconType )? 'active' : '' ) . ' wpoc-tab-pane" id="ico_moon_' . $index . '">
				<div class="wpoc-fonts-container">';
			
	foreach( $icomoon as $ico ) {
		$out .= '<div class="wpoc-font-icon" title="' . $ico . '" onclick="setIcomoonIcon(\'' . $ico . '\', ' . $index . ')">
					<span class="icon icon-' . $ico . '"></span>
				</div>';
	}
	
	$out .= '</div>
			</div>
		</div>
	</div>';
	return $out;
}
//---------------------------------------------------
/**
 * Creates a new slide
 *
 * @return void
 */
function wpoc_create_new_slide() {
	$index = ( isset( $_POST['index'] ) )? $_POST['index'] : 0;
	$order = ( isset( $_POST['order'] ) )? $_POST['order'] : 1;
	$sld_id = ( isset( $_POST['sld_id'] ) )? $_POST['sld_id'] : 0;
	
	$slide = NULL;
	if( !empty( $sld_id ) ) {
		$slide = wpoc_read_slide( $sld_id );
	}
	echo wpoc_render_slide_options( $index, $order, $slide );
	exit;
}
//---------------------------------------------------
/**
 * Saves a slider options
 *
 * @uses wp_parse_args()
 * @uses wpdb:insert()
 * @uses wpdb:update()
 * @uses wpdb:query()
 * @uses plugins_url()
 * @uses get_admin_url()
 *
 * @return void
 */
function wpoc_save_slider() {
	if( !isset( $_POST['submit'] ) ) {
		return;
	}
	global $wpdb;
	$slider = array();
	$sdr_id = ( isset( $_POST['sdr_id'] ) )? (int) $_POST['sdr_id'] : 0;
	$newSlider = ( empty( $sdr_id ) );
	$slider['sdr_name'] = ( isset( $_POST['sdr_name'] ) && !empty( $_POST['sdr_name'] ) )? $_POST['sdr_name'] : wpoc_generate_slider_name();
	$slider['sdr_options'] = ( isset( $_POST['options'] ) )? $_POST['options'] : array();
	$slider['sdr_options']['responsive'] = ( isset( $_POST['options']['responsive'] ) )? $_POST['options']['responsive'] : '0';
	$slider['sdr_options']['pagination'] = ( isset( $_POST['options']['pagination'] ) )? $_POST['options']['pagination'] : '0';
	$slider['sdr_options']['paginationNumbers'] = ( isset( $_POST['options']['paginationNumbers'] ) )? $_POST['options']['paginationNumbers'] : '0';
	$slider['sdr_options']['lazyLoad'] = ( isset( $_POST['options']['lazyLoad'] ) )? $_POST['options']['lazyLoad'] : '0';
	$slider['sdr_options']['lazyFollow'] = ( isset( $_POST['options']['lazyFollow'] ) )? $_POST['options']['lazyFollow'] : '0';
	$slider['sdr_options']['lazyEffect'] = ( isset( $_POST['options']['lazyEffect'] ) )? $_POST['options']['lazyEffect'] : '0';
	$slider['sdr_options']['dragBeforeAnimFinish'] = ( isset( $_POST['options']['dragBeforeAnimFinish'] ) )? $_POST['options']['dragBeforeAnimFinish'] : '0';
	$slider['sdr_options']['mouseDrag'] = ( isset( $_POST['options']['mouseDrag'] ) )? $_POST['options']['mouseDrag'] : '0';
	$slider['sdr_options']['touchDrag'] = ( isset( $_POST['options']['touchDrag'] ) )? $_POST['options']['touchDrag'] : '0';
	$slider['sdr_options']['navigation'] = ( isset( $_POST['options']['navigation'] ) )? $_POST['options']['navigation'] : '0';
	$slider['sdr_options']['scrollPerPage'] = ( isset( $_POST['options']['scrollPerPage'] ) )? $_POST['options']['scrollPerPage'] : '0';
	$slider['sdr_options']['stopOnHover'] = ( isset( $_POST['options']['stopOnHover'] ) )? $_POST['options']['stopOnHover'] : '0';
	$slider['sdr_options']['rewindNav'] = ( isset( $_POST['options']['rewindNav'] ) )? $_POST['options']['rewindNav'] : '0';
	$slider['sdr_options']['rewindSpeed'] = ( isset( $_POST['options']['rewindSpeed'] ) )? $_POST['options']['rewindSpeed'] : '0';
	$slider['sdr_options']['itemsScaleUp'] = ( isset( $_POST['options']['itemsScaleUp'] ) )? $_POST['options']['itemsScaleUp'] : '0';
	$slider['sdr_options']['singleItem'] = ( isset( $_POST['options']['singleItem'] ) )? $_POST['options']['singleItem'] : '0';
	$slider['sdr_options']['autoHeight'] = ( isset( $_POST['options']['autoHeight'] ) )? $_POST['options']['autoHeight'] : '0';
	$slider['sdr_options'] = wp_parse_args( $slider['sdr_options'], WPOC_Options::get_slider_defaults() );
	$slider['sdr_options'] = serialize( $slider['sdr_options'] );
	
	$success = false;
	$wpdb->query( "START TRANSACTION" );
	if( $sdr_id > 0 ) {
		$success = ( false !== $wpdb->update( $wpdb->prefix . 'wpoc_sliders', $slider, array('sdr_id' => $sdr_id), array('%s', '%s'), array('%d') ) );
	} else {
		$success = $wpdb->insert( $wpdb->prefix . 'wpoc_sliders', $slider, array('%s', '%s') );
		$sdr_id = $wpdb->insert_id;
	}
	
	if( $success ) {
		$success = wpoc_save_slides( $sdr_id );
	}
	
	$idParam = "";
	if( $success ) {
		$wpdb->query( "COMMIT" );
		echo '<div style="padding: 10px 10px 10px 40px; margin: 20px 10px; background: #CCEBC9 url(' .
									plugins_url( 'images/success_24.png', WPOC_PLUGIN_MAIN_FILE ) .
									') no-repeat 10px 10px; border: solid 1px #B0DEA9; color: #508232;">' .
									__( 'slider has been saved successfully' ) . '</div>';
									
		$idParam = "&id=" . $sdr_id;
	} else {
		$wpdb->query( "ROLLBACK" );
		echo '<div style="padding: 10px 10px 10px 40px; margin: 20px 10px; background: #EACBC9 url(' .
									plugins_url( 'images/error_24.png', WPOC_PLUGIN_MAIN_FILE ) .
									') no-repeat 10px 10px; border: solid 1px #DDB0AA; color: #7F3331;">' .
									__( 'Saving process has failed' ) . '</div>';
									
		$idParam = ( $newSlider )? "" : "&id=" . $sdr_id;
	}
	echo '<script type="text/javascript">
			window.location.href = "' . get_admin_url( NULL, "admin.php?page=wpoc_edit" . $idParam ) . '";
		</script>';
}
//---------------------------------------------------
/**
 * Returns list of font-awesome icons
 *
 * @return array
 */
function wpoc_get_fontawesome_fonts() {
	return include( WPOC_PLUGIN_ROOT_DIR . WPOC_DS . 'options' . WPOC_DS . 'font_awesome_icons.php' );
}
//---------------------------------------------------
/**
 * Returns list of IcoMoon icons
 *
 * @return array
 */
function wpoc_get_icomoon_fonts() {
	return include( WPOC_PLUGIN_ROOT_DIR . WPOC_DS . 'options' . WPOC_DS . 'ico_moon_icons.php' );
}
//---------------------------------------------------
/**
 * Saves a slider options
 *
 * @uses wp_parse_args()
 * @uses wpdb:insert()
 * @uses wpdb:delete()
 *
 * @param $sdr_id integer
 *
 * @return boolean
 */
function wpoc_save_slides( $sdr_id ) {
	global $wpdb;
	$slides = ( isset( $_POST['slides'] ) )? $_POST['slides'] : array();
	
	$success = ( false !== $wpdb->delete( $wpdb->prefix . 'wpoc_slides', array('sdr_id' => $sdr_id), array('%d') ) );
	
	if( $success ) {
		foreach( $slides as $slide ) {
			$slide['videoAutoplay'] = ( isset( $slide['videoAutoplay'] ) )? '1' : '0';
			$sld_options = wp_parse_args( $slide, WPOC_Options::get_slide_defaults() );
			$sld_options = serialize( $sld_options );
			$success = $wpdb->insert( $wpdb->prefix . 'wpoc_slides', array('sdr_id' => $sdr_id, 'sld_options' => $sld_options), array('%d', '%s') );
			if( !$success ) {
				break;
			}
		}
	}
	
	return $success;
}
//---------------------------------------------------
/**
 * generates a slider name
 *
 * @uses wpdb::get_var()
 *
 * @return string
 */
function wpoc_generate_slider_name() {
	global $wpdb;
	$sql = "SELECT IFNULL(MAX(ord), 0) + 1 mx 
			FROM (
				SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(sdr_name, ' ', 2), ' ', -1) as ord
				FROM wp_wpoc_sliders 
				WHERE sdr_name REGEXP '^Slider[[:space:]][1-9][0-9]*$'
			) myTable";
			
	$max = $wpdb->get_var( $sql );
	return 'Slider ' . $max;
}
//---------------------------------------------------
/**
 * Renders a slider
 *
 * @uses is_admin()
 * @uses wp_enqueue_style()
 * @uses wp_enqueue_script()
 * @uses plugins_url()
 * @uses is_wp_error()
 *
 * @param $sdr_id integer
 *
 * @return string
 */
function wpoc_render_slider( $sdr_id ) {
	$sdr_id = (int) $sdr_id;
	
	if( !is_admin() ) {
		wp_enqueue_style( 'font-awesome4.5', plugins_url( 'lib/font-awesome/css/font-awesome.min.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'IcoMoon-icons', plugins_url( 'lib/IcoMoon/IcoMoon.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'owl-css', plugins_url( 'lib/owl-carousel/owl.carousel.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'owl-theme', plugins_url( 'lib/owl-carousel/owl.theme.css', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_style( 'wpoc-client', plugins_url( 'css/client.css', WPOC_PLUGIN_MAIN_FILE ) );
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'owl-js', plugins_url( 'lib/owl-carousel/owl.carousel.js', WPOC_PLUGIN_MAIN_FILE ) );
		wp_enqueue_script( 'IconMoon-liga', plugins_url( 'lib/IcoMoon/liga.js', WPOC_PLUGIN_MAIN_FILE ) );
	}
	
	$slider = $slides = NULL;
	if( !empty( $sdr_id ) ) {
		$slider = wpoc_read_slider( $sdr_id );
		$slides = wpoc_read_slides( $sdr_id );
	}
	
	$options = array();
	if( !is_object( $slider ) || is_wp_error( $slider ) || is_wp_error( $slides ) || !is_array( $slides ) ) {
		return;
	}
	$options = unserialize( $slider->sdr_options );
	//extract( $options );
	
	$id = 'wpoc_carousel_' . uniqid();
	
	$out = '<div id="' . $id . '" class="owl-carousel">';
	foreach( $slides as $slide ) {
		$slide_options = unserialize( $slide->sld_options );
		$out .= '<div class="wpoc-item" style="height: ' . $options['height'] . 'px;">';
		if( 'default' == $slide_options['type'] ) {
			$out .= wpoc_render_default_slide( $slide_options );
		}
		elseif( 'video' == $slide_options['type'] ) {
			$out .= wpoc_render_video_slide( $slide_options );
		}
		elseif( 'text' == $slide_options['type'] ) {
			$out .= wpoc_render_text_slide( $slide_options );
		}
		$out .= '</div>';
	}
	$out .= '</div>';
	
	$out .= '<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery("#' . $id  . '").owlCarousel({
						responsive: ' . ( ( '1' == $options['responsive'] )? 'true' : 'false' ) . ',
						responsiveRefreshRate: 200,
						autoPlay: ' . ( ( '1' == $options['autoPlay'] )? $options['autoPlayTime'] * 1000 : 'false' ) . ',
						paginationSpeed: ' . $options['paginationSpeed'] * 1000 . ',
						slideSpeed: ' . $options['slideSpeed'] * 1000 . ',
						pagination: ' . ( ( '1' == $options['pagination'] )? 'true' : 'false' ) . ',
						paginationNumbers: ' . ( ( '1' == $options['paginationNumbers'] )? 'true' : 'false' ) . ',
						lazyLoad: ' . ( ( '1' == $options['lazyLoad'] )? 'true' : 'false' ) . ',
						lazyFollow: ' . ( ( '1' == $options['lazyFollow'] )? 'true' : 'false' ) . ',
						lazyEffect: ' . ( ( '1' == $options['lazyEffect'] )? 'true' : 'false' ) . ',
						dragBeforeAnimFinish: ' . ( ( '1' == $options['dragBeforeAnimFinish'] )? 'true' : 'false' ) . ',
						mouseDrag: ' . ( ( '1' == $options['mouseDrag'] )? 'true' : 'false' ) . ',
						touchDrag: ' . ( ( '1' == $options['touchDrag'] )? 'true' : 'false' ) . ',
						navigation: ' . ( ( '1' == $options['navigation'] )? 'true' : 'false' ) . ',
						navigationPrev: "' . $options['navigationPrev'] . '",
						navigationNext: "' . $options['navigationNext'] . '",
						scrollPerPage: ' . ( ( '1' == $options['scrollPerPage'] )? 'true' : 'false' ) . ',
						stopOnHover: ' . ( ( '1' == $options['stopOnHover'] )? 'true' : 'false' ) . ',
						rewindNav: ' . ( ( '1' == $options['rewindNav'] )? 'true' : 'false' ) . ',
						rewindSpeed: ' . ( ( '1' == $options['rewindSpeed'] )? 'true' : 'false' ) . ',
						itemsScaleUp: ' . ( ( '1' == $options['itemsScaleUp'] )? 'true' : 'false' ) . ',
						singleItem: ' . ( ( '1' == $options['singleItem'] )? 'true' : 'false' ) . ',
						autoHeight: false,
						itemsDesktop: [1199, ' . $options['itemsDesktop'] . '],
						itemsDesktopSmall: [979, ' . $options['itemsDesktopSmall'] . '],
						itemsTablet: [768, ' . $options['itemsTablet'] . '],
						itemsMobile: [479, ' . $options['itemsMobile'] . ']
					});
				});
			</script>';
	return $out;
}
//---------------------------------------------------
/**
 * Renders a default slide
 *
 * @param $options array
 *
 * @return string
 */
function wpoc_render_default_slide( $options ) {
	$out = '';
	if( 'slide' === $options['urlPlace'] && !empty( $options['slideUrl'] ) ) {
		$out .= '<a href="' . $options['slideUrl'] . '" target="' . $options['slideUrlTarget'] . '" style="text-decoration: none;">';
	}
	
	$out .= '<div class="wpoc-default-slide" style="' . ( ( !empty( $options['backgroundColor'] ) )? 'background-color: #' . $options['backgroundColor'] . ';' : 'background-color: transparent;' ) . '">';
	
	$img = '';
	if( !empty( $options['iconImageUrl'] ) ) {
		$img = '<img src="' . $options['iconImageUrl'] . '" width="' . $options['iconSize'] . '" height="' . $options['iconSize'] . '" />';
	}
	else if( !empty( $options['icon'] ) ) {
		switch( $options['iconType'] ) {
			case 'font-awesome':
			$img = '<i class="fa fa-' . $options['icon'] . '"></i>';
			break;
			
			case 'ico-moon':
			$img = '<i class="icon icon-' . $options['icon'] . '"></i>';
			break;
		}
	}
	
	if( !empty( $img ) ) {
		$out .= '<span style="' .
					( ( !empty( $options['iconColor'] ) )? 'color: #' .  $options['iconColor'] . ';' : '') . ' ' .
					( ( !empty( $options['iconSize'] ) )? 'font-size: ' . $options['iconSize'] . 'px;' : '') .
					'">' . $img . '</span>';
	}
	
	$out .= '<div class="wpoc-titles-wrapper">';
	if( !empty( $options['largeTitle'] ) ) {
		$out .= '<h3 style="' . $options['largeTitleStyles'] . ' color: #' . $options['largeTitleColor'] . ';">';
		if( 'title' === $options['urlPlace'] && !empty( $options['slideUrl'] ) ) {
			$out .= '<a href="' . $options['slideUrl'] . '" target="' . $options['slideUrlTarget'] . '" 
					style="text-decoration: none; ' . $options['largeTitleStyles'] . ' color: #' . $options['largeTitleColor'] . ';">';
		}
		$out .= $options['largeTitle'];
		if( 'title' === $options['urlPlace'] && !empty( $options['slideUrl'] ) ) {
			$out .= '</a>';
		}
		$out .= '</h3>';
	}
	if( !empty( $options['smallTitle'] ) ) {
		$out .= '<h4 style="color: #' . $options['smallTitleColor'] . '; ' . $options['smallTitleStyles'] . '">' . $options['smallTitle'] . '</h4>';
	}
	$out .= '</div>';
	
	$out .= '</div>';
	
	if( 'slide' === $options['urlPlace'] && !empty( $options['slideUrl'] ) ) {
		$out .= '</a>';
	}
	
	return $out;
}
//---------------------------------------------------
/**
 * Renders a video slide
 *
 * @param $options array
 *
 * @return string
 */
function wpoc_render_video_slide( $options ) {
	extract( $options );
	$id = uniqid();
	$out = '';
	if( 'youtube' == $videoType ) {
		$videoId = wpoc_get_youtube_id_from_url( $videoId );
		$url = 'http://www.youtube.com/embed/' . $videoId . ( ( '1' === $videoAutoplay )? '?autoplay=1' : '' );
		
		$out = '<iframe src="' . $url . '" width="100%" height="100%" frameborder="0" 
				webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}
	elseif( 'vimeo' == $videoType ) {
		$videoId = wpoc_get_vimeo_id_from_url( $videoId );
		$url = 'http://player.vimeo.com/video/' . $videoId . ( ( '1' === $videoAutoplay )? '?autoplay=1' : '' );
		$out = '<iframe src="' . $url . '" width="100%" height="100%" frameborder="0" 
				autoplay="' . $videoAutoplay . '" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}
	return $out;
}
//---------------------------------------------------
/**
 * returns youtube video id from url
 *
 * @return string
 */
function wpoc_get_youtube_id_from_url($url){
	if(!filter_var($url, FILTER_VALIDATE_URL)) {
		return $url;
	}
	$parsedUrl = parse_url($url);
	
	$video_id = '';
	if(strpos($parsedUrl['host'], 'youtube.com') !== false){
		if(strpos($parsedUrl['path'], '/watch') !== false){
			parse_str($parsedUrl['query'], $parsedStr);
			if(isset($parsedStr['v']) and !empty($parsedStr['v'])){
				$video_id = $parsedStr['v'];
			}
		}
		else if(strpos($parsedUrl['path'], '/v/') !== false){
			$vidId = str_replace('/v/', '', $parsedUrl['path']);
			if(!empty($vidId)){
				$video_id = $vidId;
			}
		}
		else if(strpos($parsedUrl['path'], '/embed/') !== false){
			$video_id = str_replace('/embed/', '', $parsedUrl['path']);
		}
	}
	else if(strpos($parsedUrl['host'], 'youtu.be') !== false){
		$video_id = str_replace('/', '', $parsedUrl['path']);
	}
	
	$video_id = explode('/', $video_id);
	$video_id = wpoc_remove_empty_elements_from_array($video_id);
	if(!empty($video_id)){
		return $video_id[count($video_id) - 1];
	}
	return NULL;
}
//---------------------------------------------------
/**
 * returns vimeo video id from url
 *
 * @return string
 */
function wpoc_get_vimeo_id_from_url($url){
	if(!filter_var($url, FILTER_VALIDATE_URL)) {
		return $url;
	}
	$video_id = '';
	$parsedUrl = parse_url($url);
	if($parsedUrl['host'] == 'vimeo.com'){
		$video_id = ltrim($parsedUrl['path'], '/');
	}
	
	$video_id = explode('/', $video_id);
	$video_id = wpoc_remove_empty_elements_from_array($video_id);
	if(!empty($video_id)){
		return $video_id[count($video_id) - 1];
	}
	return NULL;
}
//---------------------------------------------------
/**
 * Renders a text slide
 *
 * @param $options array
 *
 * @return string
 */
function wpoc_render_text_slide( $options ) {
	return do_shortcode( $options['customText'] );
}
//---------------------------------------------------
/**
 * Called when shortcode is used
 *
 * @param $options atts
 *
 * @return string
 */
function wpoc_do_shortcode( $atts ) {
	return wpoc_render_slider( $atts['id'] );
}
//---------------------------------------------------
/**
 * Deletes a slider
 *
 * @uses wpdb::query()
 * @uses wpdb::delete()
 *
 * @return void
 */
function wpoc_delete_slider() {
	global $wpdb;
	$sdr_id = ( isset( $_POST['sdr_id'] ) )? $_POST['sdr_id'] : 0;
	$wpdb->query( "START TRANSACTION" );
	$success = ( $wpdb->delete( $wpdb->prefix . 'wpoc_sliders', array('sdr_id' => $sdr_id), array('%d') ) > 0 );
	if( $success ) {
		$success = ( false !== $wpdb->delete( $wpdb->prefix . 'wpoc_slides', array('sdr_id' => $sdr_id), array('%d') ) );
	}
	
	if( $success ) {
		$wpdb->query( "COMMIT" );
		echo '1';
	} else {
		$wpdb->query( "ROLLBACK" );
		echo '0';
	}
	exit;
}
//---------------------------------------------------
/**
 * adds sliders combobox to editor
 *
 * @uses current_user_can()
 * @uses add_filter()
 *
 * @return void
 */
function wpoc_add_sliders_list_to_editor() {
	if( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
		add_filter( 'mce_external_plugins', 'wpoc_mce_plugin' );
		add_filter( 'mce_buttons', 'wpoc_mce_button' );
	}
}
//---------------------------------------------------
/**
 * includes mce plugins
 *
 * @uses get_bloginfo()
 * @uses plugins_url()
 *
 * @param $plgs array
 *
 * @return array
 */
function wpoc_mce_plugin( $plgs ) {
	if( get_bloginfo( 'version' ) < 3.9 ) {
		$plgs['wpocshortcode'] = plugins_url( 'lib/mceplugin/mceplugin.js', WPOC_PLUGIN_MAIN_FILE );
	} else{
		$plgs['wpocshortcode'] = plugins_url( 'lib/mceplugin/mceplugin_x4.js', WPOC_PLUGIN_MAIN_FILE );
	}
	return $plgs;
}
//---------------------------------------------------
/**
 * adds sliders combo to mce editor
 *
 * @uses get_bloginfo()
 * @uses plugins_url()
 *
 * @param $plgs array
 *
 * @return array
 */
function wpoc_mce_button( $btns ) {
	array_push( $btns, 'separator', 'wpocslidercombo' );
	return $btns;
}
//---------------------------------------------------
/**
 * Registers widget
 *
 * @uses register_widget()
 *
 * @return void
 */
function wpoc_register_widget(){
	register_widget('WPOC_Slider_Widget');
}
//---------------------------------------------------
/**
 * returns sliders list
 *
 * @uses wpdb::get_results()
 *
 * @return array
 */
function wpoc_sliders_list() {
	global $wpdb;
	$sql = "SELECT sdr_id, sdr_name from {$wpdb->prefix}wpoc_sliders";
	return $wpdb->get_results( $sql );
}
//---------------------------------------------------
/**
 * used to remove out elements that have null values
 *
 * @return array
 */
function wpoc_remove_empty_elements_from_array($array){
	foreach($array as $key => $value){
		if($value == NULL || $value == ''){
			unset($array[$key]);
		}
	}
	return $array;
}
//---------------------------------------------------


