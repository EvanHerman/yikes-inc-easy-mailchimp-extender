<?php

$integration_options = get_option( 'optin-checkbox-init' , '' );

?>
<br />
<p class="description"><?php _e( 'It looks like we found some interest groups! Pre-select interest groups for this integration below.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
<p class="description"><?php _e( '<strong>Note:</strong> the interest groups will not show up on the front end for your users to select from.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
<?php

/*
*	Loop over interest groups
*/
foreach( $interest_groupings as $id => $interest_group ) {

	$interest_group_type      = isset( $interest_group['type'] ) ? $interest_group['type'] : '';
	$interest_groups_fields   = isset( $interest_group['items'] ) ? $interest_group['items'] : array();
	$selected_interest_groups = isset( $integration_options[ $integration_type ]['interest-groups'] ) ? $integration_options[ $integration_type ]['interest-groups'] : array();
	?>
	<section class="interest-group-section">
		<strong class="interest-group-section-title"><?php echo ucwords( $interest_group['title'] ); ?></strong>
	<?php
	/*
	*	Loop over the interest group types, and return the appropriate type
	*/
	$checked = $selected = '';
	switch( $interest_group_type ) {
	
		default:
		case 'hidden':
		case 'checkboxes':
			foreach( $interest_groups_fields as $field_id => $field ) {
				if ( isset( $selected_interest_groups[ $id ] ) ) {
					$checked = checked( true, in_array( $field_id, $selected_interest_groups[ $id ] ), false );
				}
				?>
				<label>
					<input type="checkbox"
					       name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $id; ?>][]"
					       value="<?php echo $field_id; ?>" <?php echo $checked ?>>
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
					       name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $id; ?>][]"
					       value="<?php echo $field_id; ?>" <?php echo $checked; ?>>
					<?php echo $field['name']; ?>
				</label>
				<?php
			}

			break;
			
		case 'dropdown':
			if ( ! empty( $interest_groups_fields ) ) {
				?>
				<select name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $id; ?>][]">
				<?php foreach( $interest_groups_fields as $field_id => $field ) {
					if ( isset( $selected_interest_groups[ $id ] ) ) {
						$selected = selected( true, in_array( $field_id, $selected_interest_groups[ $id ] ), false );
					}
					?>
					<option value="<?php echo $field_id; ?>" <?php echo $selected; ?>>
						<?php echo $field['name']; ?>
					</option>
				<?php
				}
				?></select><?php
			}
			break;
	
	}
	
	?>
	</section>
	<?php
}
