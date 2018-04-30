<?php
/**
 * Template for rendering interest groups
 *
 * Page template that houses all of the interest groups for checkbox settings.
 *
 * @since 6.0.0
 *
 * @package WordPress
 * @subpackage Component
 */

$integration_options = get_option( 'optin-checkbox-init', '' );
?>

<div class="integration-checkbox-interest-groups">
	<span class="dashicons dashicons-arrow-down-alt2 yikes-mailchimp-toggle-ig"></span> 
	<h4 class="integreation-checkbox-interest-groups-header">Interest Groups</h4>
	<br />

	<div class="integration-checkbox-interest-groups-interior">
		<p class="description"><?php _e( 'Select the interest groups users will be automatically added to. These will not be displayed on the form.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
		<?php

		/*
		*	Loop over interest groups
		*/
		foreach ( $interest_groupings as $id => $interest_group ) {

			$interest_group_type      = isset( $interest_group['type'] ) ? $interest_group['type'] : '';
			$interest_groups_fields   = isset( $interest_group['items'] ) ? $interest_group['items'] : array();
			$selected_interest_groups = isset( $integration_options[ $integration_type ]['interest-groups'] ) ? $integration_options[ $integration_type ]['interest-groups'] : array();
			$selected_interest_groups = isset( $selected_interest_groups[$list_id] ) ? $selected_interest_groups[$list_id] : $selected_interest_groups;
			?>
			<section class="interest-group-section">
				<strong class="interest-group-section-title"><?php echo ucwords( $interest_group['title'] ); ?></strong>
			<?php

			/*
			*	Loop over the interest group types, and return the appropriate type
			*/

			$checked = $selected = '';
			switch ( $interest_group_type ) {

				default:
				case 'hidden':
				case 'checkboxes':
					foreach ( $interest_groups_fields as $field_id => $field ) {
						if ( isset( $selected_interest_groups[ $id ] ) ) {
							$checked = checked( true, in_array( $field_id, $selected_interest_groups[ $id ] ), false );
						}
						?>
						<label>
							<input type="checkbox"
								name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $list_id; ?>][<?php echo $id; ?>][]"
								value="<?php echo $field_id; ?>" <?php echo $checked; ?>>
							<?php echo $field['name']; ?>
						</label>
						<?php
					}
					break;

				case 'radio':
					foreach ( $interest_groups_fields as $field_id => $field ) {
						if ( isset( $selected_interest_groups[ $id ] ) ) {
							$checked = checked( true, in_array( $field_id, $selected_interest_groups[ $id ] ), false );
						}
						?>
						<label>
							<input type="radio"
								name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $list_id; ?>][<?php echo $id; ?>][]"
								value="<?php echo $field_id; ?>" <?php echo $checked; ?>>
							<?php echo $field['name']; ?>
						</label>
						<?php
					}

					break;

				case 'dropdown':
					if ( ! empty( $interest_groups_fields ) ) {
						?>
						<select name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $list_id; ?>][<?php echo $id; ?>][]">
						<?php
						foreach ( $interest_groups_fields as $field_id => $field ) {
							if ( isset( $selected_interest_groups[ $id ] ) ) {
								$selected = selected( true, in_array( $field_id, $selected_interest_groups[ $id ] ), false );
							}
						?>
							<option value="<?php echo $field_id; ?>" <?php echo $selected; ?>>
								<?php echo $field['name']; ?>
							</option>
						<?php
						}
						?>
					</select>
					<?php
					}
					break;
			}
			?>
			</section>
			<?php
		}
		?>
	</div>
</div>
