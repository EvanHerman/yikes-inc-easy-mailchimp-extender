	<?php 
		// grab our global form_data;
		global $form_data;
	?>
	<label class="hidden-setting-label yikes-easy-mc-hidden" for="form" id=<?php esc_attr_e( $section_id ); ?>>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox yikes-easy-mc-postbox">
							<h3 class="edit-form-title"><span><?php echo $main_title; ?></span></h3>
								<div class="inside <?php esc_attr_e( $section_id ); ?>-container">
									<?php $class::$main_callback( $section_id, json_decode( $form_data['custom_fields'], true )  ); ?>
								</div>
							</div>
						</div>
					</div>
					<?php if( !empty( $sidebar_callback ) ) { ?>
						<div id="postbox-container-1" class="postbox-container">
							<div class="meta-box-sortables">
								<div class="postbox yikes-easy-mc-postbox">
									<h3 class="edit-form-title"><span><?php echo $sidebar_title; ?></span></h3>
									<div class="inside <?php esc_attr_e( $section_id ); ?>-sidebar-container"> 
										<?php $class::$sidebar_callback( $section_id, json_decode( $form_data['custom_fields'], true ) ); ?>	
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
			</div>

			<br class="clear">
		</div>
	</label>