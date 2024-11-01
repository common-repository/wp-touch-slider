<?php

class WPOC_Slider_Widget extends WP_Widget{
	
	
	public function __construct(){
		$w_options = array(
			'classname' => 'WPOC_Slider_Widget',
			'description' => __( 'Displays selected Slider' ),
			'name' => 'WPOC Carousel Slider'
		);
		parent::__construct( 'wpoc_slider_widget', 'WPOC Carousel Slider', $w_options );
	}
	
	public function form( $instance ){
		extract( $instance );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><b><?php _e( 'Title', 'wpoc_slider' ); ?></b></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
			name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if( isset( $title ) ) echo esc_attr( $title ); ?>" />
		</p>
		
		<?php $sliders = wpoc_sliders_list(); ?>
		<?php 
		$results = (isset($results)) ? $results : '';
		if( !is_wp_error( $results ) ): ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'slider_id' ); ?>"><b><?php _e( 'Slider', 'wpoc_slider' ); ?></b></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'slider_id' ); ?>" name="<?php echo $this->get_field_name('slider_id'); ?>">
				<?php foreach( $sliders as $s ): ?>
				<option value="<?php echo $s->sdr_id; ?>" <?php selected( $s->sdr_id, $slider_id ) ?>><?php echo $s->sdr_name ?></option>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
		</p>
		<?php
	}
	
	
	public function widget( $args, $instance ){
		extract( $args );
		extract( $instance );
		
		$title = apply_filters( 'widget_title', $title );
		
		echo $before_widget;
		
		if( !empty( $title ) ) echo $before_title.$title.$after_title;
		
		echo wpoc_render_slider( $slider_id );
		
		echo $after_widget;
	}
	
	
	public function update( $new_instance, $old_instance ){
		return $new_instance;
	}
	
	
}
?>