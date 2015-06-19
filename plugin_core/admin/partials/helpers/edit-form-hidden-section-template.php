	<?php 
		// grab our global form_data;
		global $form_data;
	?>
	<label class="hidden-setting-label yikes-easy-mc-hidden" for="form" id=<?php esc_attr_e( $section_data['id'] ); ?>>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox yikes-easy-mc-postbox">
							<h3 class="edit-form-title"><span><?php echo $section_data['main_title']; ?></span></h3>
								<div class="inside <?php esc_attr_e( $section_data['id'] ); ?>-container">
									<?php 
										if( isset( $section_data['main_description'] ) && $section_data['main_description'] != '' ) {
											?>
												<p><?php echo $section_data['main_description']; ?></p>
											<?php
										}
										$main_field_data = $section_data['main_fields'];
										foreach( $main_field_data as $field ) { 
											// include our field files
											include( YIKES_MC_PATH . 'admin/partials/helpers/fields/yikes-mailchimp-' . $field['type'] . '-field.php' );
										}
									?>
								</div>
							</div>
						</div>
					</div>
					<!-- begin sidebar -->
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox yikes-easy-mc-postbox">
								<h3 class="edit-form-title"><span><?php echo $section_data['sidebar_title']; ?></span></h3>
								<div class="inside <?php esc_attr_e( $section_data['id'] ); ?>-sidebar-container"> 
									<?php
										if( isset( $section_data['sidebar_description'] ) && $section_data['sidebar_description'] != '' ) {
											?>
												<p><?php echo $section_data['sidebar_description']; ?></p>
											<?php
										}
										$sidebar_field_data = $section_data['sidebar_fields'];
										foreach( $sidebar_field_data as $field ) { 
											// include our field files
											include( YIKES_MC_PATH . 'admin/partials/helpers/fields/yikes-mailchimp-' . $field['type'] . '-field.php' );
										}
									?>
								</div>
							</div>
						</div>
					</div>
			</div>

			<br class="clear">
		</div>
	</label>