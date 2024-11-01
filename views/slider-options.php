
<input type="hidden" name="sdr_id" id="sdr_id" value="<?php echo $sdr_id; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="responsive"><?php _e( 'Responsive', 'wpoc_slider'); ?></label>
		<input type="checkbox" class="" name="options[responsive]" id="responsive" value="1" <?php checked( '1', $responsive ); ?> autocomplete="off" />
	</div>

	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="autoPlay"><?php _e( 'Autoplay', 'wpoc_slider'); ?></label>
		<input type="checkbox" class="" name="options[autoPlay]" id="autoPlay" onchange="disableElementsById(['autoPlayTime', 'autoPlayTime_slider'], false, this.checked, true)" value="1" autocomplete="off" <?php checked( '1', $autoPlay ); ?> />
	</div>
</div>

<div class="row">
	<div class="form-group col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label id="autoPlayTime_value"><?php _e( 'Time interval', 'wpoc_slider' ); ?> - <span class="wpoc-slider-value"><?php echo $autoPlayTime; ?> <?php _e( 'sec.', 'wpoc_slider' ); ?></span></label>
		<div id="autoPlayTime_slider"></div>
		<input type="hidden" name="options[autoPlayTime]" id="autoPlayTime" value="<?php echo $autoPlayTime; ?>" <?php disabled( '0', $autoPlay ); ?> autocomplete="off" />
		<span class="littleNote"><?php _e( 'playing speed in seconds', 'wpoc_slider' ); ?></span>
	</div>
	</div>
    <div class="row">
	<div class="form-group col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label id="paginationSpeed_value"><?php _e( 'Pagination speed', 'wpoc_slider' ); ?> - <span class="wpoc-slider-value"><?php echo $paginationSpeed; ?> <?php _e( 'sec.', 'wpoc_slider' ); ?></span></label>
		<div id="paginationSpeed_slider"></div>
		<input type="hidden" name="options[paginationSpeed]" id="paginationSpeed" value="<?php echo $paginationSpeed; ?>" autocomplete="off" />
	</div>
</div>

<div class="row">
	<div class="form-group col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label id="slideSpeed_value"><?php _e( 'Slide speed', 'wpoc_slider' ); ?> - <span class="wpoc-slider-value"><?php echo $slideSpeed; ?> <?php _e( 'sec.', 'wpoc_slider' ); ?></span></label>
		<div id="slideSpeed_slider"></div>
		<input type="hidden" name="options[slideSpeed]" id="slideSpeed" value="<?php echo $slideSpeed; ?>" autocomplete="off" />
	</div>
	</div>
    <div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="pagination"><?php _e( 'Show pagination', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[pagination]" id="pagination" onchange="disableElementsById(['paginationNumbers'], false, this.checked, true)" value="1" <?php checked( '1', $pagination ); ?> autocomplete="off" />
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="paginationNumbers"><?php _e( 'Pagination numbers', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[paginationNumbers]" id="paginationNumbers" value="1" <?php disabled( '0', $pagination ); ?> autocomplete="off" <?php checked( '1', $paginationNumbers ); ?> />
		<br />
		<span class="littleNote"><?php _e( 'Show numbers inside pagination buttons', 'wpoc' ); ?></span>
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="lazyLoad"><?php _e( 'Lazy load', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[lazyLoad]" id="lazyLoad" value="1" onchange="disableElementsById(['lazyFollow', 'lazyEffect'], false, this.checked, true)" <?php checked( '1', $lazyLoad ); ?> autocomplete="off" />
		<br />
		<span class="littleNote"><?php _e( 'Delays loading of images', 'wpoc' ); ?></span>
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="lazyFollow"><?php _e( 'Lazy follow', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[lazyFollow]" id="lazyFollow" value="1" <?php disabled( '0', $lazyLoad ); ?> <?php checked( '1', $lazyFollow ); ?> autocomplete="off" />
		<br />
		<span class="littleNote"><?php _e( 'Only loads the images that get displayed in viewport', 'wpoc' ); ?></span>
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="lazyEffect"><?php _e( 'Use fade-in effect with lazy load', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[lazyEffect]" id="lazyEffect" value="1" <?php disabled( '0', $lazyLoad ); ?> <?php checked( '1', $lazyEffect ); ?> autocomplete="off" />
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="dragBeforeAnimFinish"><?php _e( 'drag before animation finish', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[dragBeforeAnimFinish]" id="dragBeforeAnimFinish" value="1" <?php checked( '1', $dragBeforeAnimFinish ); ?> autocomplete="off" />
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="mouseDrag"><?php _e( 'Mouse drag', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[mouseDrag]" id="mouseDrag" value="1" <?php checked( '1', $mouseDrag ); ?> autocomplete="off" />
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="touchDrag"><?php _e( 'Touch drag', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[touchDrag]" id="touchDrag" value="1" <?php checked( '1', $touchDrag ); ?> autocomplete="off" />
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="navigation"><?php _e( 'Navigation', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[navigation]" id="navigation" value="1" onchange="disableElementsById(['navigationPrev', 'navigationNext'], false, this.checked, true)" <?php checked( '1', $navigation ); ?> autocomplete="off" />
	</div>
</div>

<div class="row">
	<div class="form-group col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label class="control-label"><?php _e( 'Previous text', 'wpoc_slider' ); ?></label>
		<input type="text" class="form-control" name="options[navigationPrev]" id="navigationPrev" value="<?php echo $navigationPrev; ?>" autocomplete="off" <?php disabled( '0', $navigation ); ?> />
	</div>
	
	<div class="form-group col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label class="control-label"><?php _e( 'Next text', 'wpoc_slider' ); ?></label>
		<input type="text" class="form-control" name="options[navigationNext]" id="navigationNext" value="<?php echo $navigationNext; ?>" autocomplete="off" <?php disabled( '0', $navigation ); ?> />
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="scrollPerPage"><?php _e( 'Scroll per page', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[scrollPerPage]" id="scrollPerPage" value="1" <?php checked( '1', $scrollPerPage ); ?> autocomplete="off" />
		<br />
		<span class="littleNote"><?php _e( 'This affect next/prev buttons and mouse/touch dragging', 'wpoc' ); ?></span>
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="rewindNav"><?php _e( 'Rewind and slide to the first', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[rewindNav]" id="rewindNav" value="1" onchange="disableElementsById(['rewindSpeed_slider', 'rewindSpeed'], false, this.checked, true)" <?php checked( '1', $rewindNav ); ?> autocomplete="off" />
	</div>
</div>

<div class="row">
	<div class="form-group col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label id="rewindSpeed_value"><?php _e( 'Rewind speed', 'wpoc_slider' ); ?> - <span class="wpoc-slider-value"><?php echo $rewindSpeed; ?> <?php _e( 'sec.', 'wpoc_slider' ); ?></span></label>
		<div id="rewindSpeed_slider"></div>
		<input type="hidden" name="options[rewindSpeed]" id="rewindSpeed" value="<?php echo $rewindSpeed; ?>" <?php disabled( '0', $rewindNav ); ?> autocomplete="off" />
	</div>
    </div>
    <div class="row">
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="stopOnHover"><?php _e( 'Stop on hover', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[stopOnHover]" id="stopOnHover" value="1" autocomplete="off" <?php checked( '1', $stopOnHover ); ?> />
	</div>
</div>

<div class="row">
	<div class="form-group col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label class="control-label"><?php _e( 'Height', 'wpoc_slider' ); ?></label>
		<input type="text" class="form-control" name="options[height]" id="height" value="<?php echo $height; ?>" autocomplete="off" />
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="itemsScaleUp"><?php _e( 'Scale slides up', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[itemsScaleUp]" id="itemsScaleUp" value="1" autocomplete="off" <?php checked( '1', $itemsScaleUp ); ?> />
		<br />
		<span class="littleNote"><?php _e( 'stretch slides if they less than the required', 'wpoc_slider' ); ?></span>
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12 wpoc-field">
		<label for="singleItem"><?php _e( 'Display only one slide', 'wpoc_slider' ); ?></label>
		<input type="checkbox" class="" name="options[singleItem]" id="singleItem" value="1" autocomplete="off" onchange="disableElementsById(['autoHeight'], false, this.checked, true, ['itemsDesktop', 'itemsDesktopSmall', 'itemsTablet', 'itemsMobile'])" <?php checked( '1', $singleItem ); ?> />
	</div>
</div>

<br /><br />
<div class="row">
	<div class="col-lg-12">
		<label class="wpoc-label"><?php _e( 'Number of slides visible with each device screen', 'wpoc_slider' ); ?></label>
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12">
		<span class="wpoc-image">
			<img src="<?php echo plugins_url( 'images/desktop_64.png', WPOC_PLUGIN_MAIN_FILE ); ?>" />
		</span>
		<span class="wpoc-title"><?php _e( 'Desktop', 'wpoc_slider' ); ?></span>
		<span class="wpoc-device">
			<input type="text" class="form-control" name="options[itemsDesktop]" id="itemsDesktop" value="<?php echo $itemsDesktop; ?>" <?php disabled( '1', $singleItem ); ?> autocomplete="off" />
		</span>
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12">
		<span class="wpoc-image">
			<img src="<?php echo plugins_url( 'images/labtop_64.png', WPOC_PLUGIN_MAIN_FILE ); ?>" />
		</span>
		<span class="wpoc-title"><?php _e( 'Labtop', 'wpoc_slider' ); ?></span>
		<span class="wpoc-device">
			<input type="text" class="form-control" name="options[itemsDesktopSmall]" id="itemsDesktopSmall" value="<?php echo $itemsDesktopSmall; ?>" <?php disabled( '1', $singleItem ); ?> autocomplete="off" />
		</span>
	</div>
</div>

<div class="row">
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12">
		<span class="wpoc-image">
			<img src="<?php echo plugins_url( 'images/tablet_64.png', WPOC_PLUGIN_MAIN_FILE ); ?>" />
		</span>
		<span class="wpoc-title"><?php _e( 'Tablet', 'wpoc_slider' ); ?></span>
		<span class="wpoc-device">
			<input type="text" class="form-control" name="options[itemsTablet]" id="itemsTablet" value="<?php echo $itemsTablet; ?>" <?php disabled( '1', $singleItem ); ?> autocomplete="off" />
		</span>
	</div>
	
	<div class="col-lg-6 col-md-12 col-sm-6 col-xs-12">
		<span class="wpoc-image">
			<img src="<?php echo plugins_url( 'images/mobile_64.png', WPOC_PLUGIN_MAIN_FILE ); ?>" />
		</span>
		<span class="wpoc-title"><?php _e( 'Mobile', 'wpoc_slider' ); ?></span>
		<span class="wpoc-device">
			<input type="text" class="form-control" name="options[itemsMobile]" id="itemsMobile" value="<?php echo $itemsMobile; ?>" <?php disabled( '1', $singleItem ); ?> autocomplete="off" />
		</span>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#autoPlayTime_slider').slider({
		max: 15,
		min: 2,
		step: 0.5,
		value: parseFloat(<?php echo $autoPlayTime ?>),
		stop: function(evt, ui){
			jQuery('#autoPlayTime').val(ui.value);
			jQuery('#autoPlayTime_value').html("<?php _e( 'Time interval', 'wpoc_slider' ); ?> - <span class=\"wpoc-slider-value\">" + ui.value + " <?php _e( 'sec.', 'wpoc_slider' ); ?></span>");
		}
	});
	<?php echo ( $autoPlay )? '' : 'jQuery("#autoPlayTime_slider").slider("disable");'; ?>
	
	jQuery('#slideSpeed_slider').slider({
		max: 2.5,
		min: 0.1,
		step: 0.1,
		value: parseFloat(<?php echo $slideSpeed ?>),
		stop: function(evt, ui){
			jQuery('#slideSpeed').val(ui.value);
			jQuery('#slideSpeed_value').html("<?php _e( 'Slide speed', 'wpoc_slider' ); ?> - <span class=\"wpoc-slider-value\">" + ui.value + " <?php _e( 'sec.', 'wpoc_slider' ); ?></span>");
		}
	});
	
	jQuery('#paginationSpeed_slider').slider({
		max: 5,
		min: 0.1,
		step: 0.1,
		value: parseFloat(<?php echo $paginationSpeed ?>),
		stop: function(evt, ui){
			jQuery('#paginationSpeed').val(ui.value);
			jQuery('#paginationSpeed_value').html("<?php _e( 'Pagination speed', 'wpoc_slider' ); ?> - <span class=\"wpoc-slider-value\">" + ui.value + " <?php _e( 'sec.', 'wpoc_slider' ); ?></span>");
		}
	});
	
	jQuery('#rewindSpeed_slider').slider({
		max: 3,
		min: 0.5,
		step: 0.5,
		value: parseFloat(<?php echo $rewindSpeed ?>),
		stop: function(evt, ui){
			jQuery('#rewindSpeed').val(ui.value);
			jQuery('#rewindSpeed_value').html("<?php _e( 'Rewind speed', 'wpoc_slider' ); ?> - <span class=\"wpoc-slider-value\">" + ui.value + " <?php _e( 'sec.', 'wpoc_slider' ); ?></span>");
		}
	});
	<?php echo ( $rewindNav )? '' : 'jQuery("#rewindSpeed_slider").slider("disable");'; ?>
	
	
	jQuery("#responsive").checkboxpicker();
	jQuery("#autoPlay").checkboxpicker();
	jQuery("#pagination").checkboxpicker();
	jQuery("#paginationNumbers").checkboxpicker();
	jQuery("#lazyLoad").checkboxpicker();
	jQuery("#lazyFollow").checkboxpicker();
	jQuery("#lazyEffect").checkboxpicker();
	jQuery("#dragBeforeAnimFinish").checkboxpicker();
	jQuery("#mouseDrag").checkboxpicker();
	jQuery("#touchDrag").checkboxpicker();
	jQuery("#navigation").checkboxpicker();
	jQuery("#scrollPerPage").checkboxpicker();
	jQuery("#rewindNav").checkboxpicker();
	jQuery("#stopOnHover").checkboxpicker();
	jQuery("#itemsScaleUp").checkboxpicker();
	jQuery("#singleItem").checkboxpicker();
	jQuery("#autoHeight").checkboxpicker();
});
</script>