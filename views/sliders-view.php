<?php $admin_url = get_admin_url(); ?>
<?php $page = ( isset( $_GET['pg'] ) )? (int) $_GET['pg'] : 1; ?>
<?php $page = ( !empty( $page ) )? $page : 1; ?>
<?php $limit = 10; ?>
<?php $offset = ( $page - 1 ) * $limit; ?>
<?php $sliders = wpoc_read_sliders( array(), $limit, $offset ) ?>

<div class="bootstrap-wrapper">	
	<div class="container-fluid wpoc-plugin-logo">
		<div class="row">
			<div style="padding: 0 10px;">
				<h3><?php _e( '&nbsp;&nbsp;Sliders List', 'wpoc_slider' ); ?>&nbsp;&nbsp;</h3>
			</div>
		</div>
	
	<div class="container-fluid">
		<div class="row" style="margin-top: 20px;">
			<div class="col-md-8 col-sm-12">
				<div class="row">
					<div class="col-sm-10">
                    
						<div class="row">
							<div class="col-sm-3">
								<a class="btn btn-success" href="<?php echo $admin_url; ?>admin.php?page=wpoc_edit"><i class="fa fa-plus-square"></i>&nbsp;
								<?php _e('Create New Slider', 'wpoc_slider'); ?></a><p></p>
                                
							</div>
                            
						</div>
					</div>
				</div>
			</div>
		</div>
        
    
		<div class="row">
			<div class="col-sm-12">
				<table class="table table-bordered table-striped table-hover wpoc-sliders-table">
					<thead>
						<tr>
							<th width="25%"><?php _e('Name', 'wpoc_slider') ?></th>
							<th width="20%"><?php _e('Shortcode', 'wpoc_slider') ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
				<?php if( count( $sliders['data'] ) > 0 ): ?>
					<?php foreach( $sliders['data'] as $s ): ?>
						<tr id="slider_<?php echo $s->sdr_id; ?>">
							<td><a href="<?php echo $admin_url; ?>admin.php?page=wpoc_edit&id=<?php echo $s->sdr_id; ?>"><?php echo $s->sdr_name; ?></a></td>
							<td><?php echo '[wpoc_slider id="' . $s->sdr_id . '"]'; ?></td>
							<td>
								<a class="btn btn-xs btn-default" href="<?php echo $admin_url; ?>admin.php?page=wpoc_edit&id=<?php echo $s->sdr_id; ?>">
									<i class="fa fa-pencil-square-o"></i>&nbsp;<?php _e( 'Edit', 'wpoc_slider' ); ?>
								</a>&nbsp;&nbsp;
								<a class="btn btn-xs btn-danger" href="javascript:void(0)" onclick="deleteSlider(<?php echo $s->sdr_id; ?>)">
									<i class="fa fa-times-circle"></i>&nbsp;<?php _e( 'delete', 'wpoc_slider' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="4"><?php _e( 'No sliders found', 'wpoc_slider' ); ?></td>
					</tr>
				<?php endif; ?>
					</tbody>
				</table>
				<?php if( $sliders['total'] > $limit ): ?>
					<?php echo wpoc_pagination( $admin_url . 'admin.php?page=wpoc_sliders&pg=', $sliders['total'], $limit, $page); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>