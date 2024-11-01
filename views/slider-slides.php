
<ul id="sortable-slides">
	<?php
	$i = 0;
	$o = 1;
	if( is_array( $slides ) ) {
		foreach($slides as $slide){
			$i++;
			$o++;
			echo wpoc_render_slide_options( $i, $o, $slide );
		}
	}
	?>
</ul>
<input type="hidden" id="slides_index" value="<?php echo $i ?>" />
<input type="hidden" id="slides_order" value="<?php echo $o ?>" />

<input type="hidden" id="imageFormIndex" value="" />

<br /><br /><br />
<button type="button" class="btn btn-success" id="add_new_slide" onclick="wpoc_addNewSlide()"><i class="fa fa-plus-square"></i>&nbsp;<?php _e('Add slide', 'rc_slider') ?></button>
<script type="text/javascript">
var IN_SORTING = false;
var PREVENT_SORTING = false;
jQuery(function(){

	/* begin slides */
	
    jQuery('.collapsible-panel div.body').hide();
	
    jQuery('.collapsible-panel div.header').click(function(e){;
		if(!IN_SORTING){
			jQuery(this).next('.collapsible-panel div.body').slideToggle(400);
			jQuery(this).toggleClass('active');
			e.preventDefault();
		} else{
			IN_SORTING = false;
		}
    });
	
    jQuery('.collapsible-panel div.body').hover(
		function(e){
			jQuery("#sortable-slides").sortable("disable");
		}, function(e){
			jQuery("#sortable-slides").sortable("enable");
	});
	
	jQuery("#sortable-slides").sortable({
		axis: 'y',
		cursor: 'move',
		stop: function(evt, ui){
			var o = 0;
			jQuery('#sortable-slides li').each(function(index){
				var i = (new String(jQuery(this).attr('id'))).split('_').reverse()[0];
				jQuery('#slides_order_' + i).val(++o);
			});
			jQuery('#slides_order').val(o);
			IN_SORTING = true;
		}
	});
});
</script>




