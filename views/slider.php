
 <div class="bootstrap-wrapper">
	 <br />
	 
	 
	 <div class="alert alert-info" style="width:95%; margin-left:20px;">
  <p style="font-size:15px;">if you need more fetures you can <a href="http://www.wp-buy.com/product/responsive-metro-slider-pro/" target="_blank">upgrade to professional version</a> now, The premium version is completely different from the free version as there are a lot more features included. <span style="color:red"><b>Now 30% off</b></span></p>
	 
</div>



	<div style="padding: 0 10px;">
				<h3><?php _e( '&nbsp;&nbsp;New Slider', 'wpoc_slider' ); ?>&nbsp;&nbsp;</h3>
			</div>
<br />
        
	<!-- START loading -->
	<div id="wpoc-loading" class="wpoc-loading hidden">
		<div>
			<i class="fa fa-spinner fa-pulse fa-4x"></i>
		</div>
	</div>
	<!-- END loading -->
	<br />
	<form method="post" action="<?php echo get_admin_url( NULL, 'admin.php?page=wpoc_save' ); ?>">
		<div class="container-fluid">
			<div class="row wpoc-submit">
				<div class="col-lg-2">
					<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i>&nbsp;<?php _e( 'Save & Preview', 'wpoc_lang' ); ?></button>
				</div>
			</div>
			<div class="row" id="wpoc_slider_main_info">
				
				
				<div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<label for="sdr_name"><?php _e( 'Slider name', 'wpoc_slider'); ?></label>
					<input type="text" name="sdr_name" id="sdr_name" value="<?php echo $sdr_name; ?>" class="form-control" autocomplete="off" />
				</div>
				
				<div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<label class="control-label"><?php _e( 'shortcode', 'wpoc_lang' ); ?></label>
					<input class="form-control wpoc-code-field" type="text" 
					value='<?php echo ( !empty( $sdr_id ) )? '[wpoc_slider id="' . $sdr_id . '"]' : ''; ?>' readonly>
				</div>
				
				<div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<label class="control-label"><?php _e( 'PHP function call', 'wpoc_lang' ); ?></label>
					<input class="form-control wpoc-code-field" type="text" 
					value="<?php echo ( !empty( $sdr_id ) )? 'wpoc_render_slider(' . $sdr_id . ');' : ''; ?>" readonly>
				</div>
                <div class="col-lg-1 col-md-2 col-sm-3 wpoc-btn-by-fld">
					<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i>&nbsp;<?php _e( 'Save & Preview', 'wpoc_lang' ); ?></button>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default" style="margin-top: 20px;">
						<div class="panel-heading">
							<h3 class="panel-title"><?php _e( 'Slider Preview', 'wpoc_lang' ); ?></h3>
						</div>
						<div class="panel-body">
						<?php include("slider-preview.php"); ?>
						</div>
					</div>	
				</div>	
			</div>
			
			<div class="row">
				<div class="col-lg-7 col-md-8 col-sm-12 col-xs-12">				
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?php _e( 'Slides', 'wpoc_lang' ); ?></h3>
						</div>
						<div class="panel-body">
						<?php include("slider-slides.php"); ?>
						</div>
					</div>
				</div>
				
				<div class="col-lg-5 col-md-4 col-sm-12 col-xs-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?php _e( 'Slider Options', 'wpoc_lang' ); ?></h3>
						</div>
						<div class="panel-body">
						<?php include("slider-options.php"); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div style="background-color:white; width:100%"><a href="http://www.wp-buy.com/product/responsive-metro-slider-pro/" target="_blank"><img src="<?php echo plugins_url( 'images/upgradenow-button.png', WPOC_PLUGIN_MAIN_FILE ); ?>"></a><?php echo touch_rate_us('https://wordpress.org/support/view/plugin-reviews/wp-touch-slider?rate=5#postform', '#191E23');?></div>
<script type="text/javascript">
	var wpoc_main_info = jQuery('#wpoc_slider_main_info'),
	wpoc_main_info_position = wpoc_main_info.offset().top + wpoc_main_info.height(),
	wpoc_submit = jQuery('.wpoc-submit');
	jQuery(window).on('scroll', function(){
		if(jQuery(window).scrollTop() > wpoc_main_info_position ) {
			wpoc_submit.width(wpoc_main_info.parent('div').width() + 30)
			wpoc_submit.addClass('wpoc-fixed');	
		} else {
			wpoc_submit.removeClass('wpoc-fixed');
		}
	});
</script>