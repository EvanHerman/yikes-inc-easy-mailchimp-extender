<?php

// print_r( $interest_groupings );

$integration_options = get_option( 'optin-checkbox-init' , '' );	

// print_r( $integration_options );

// confirm interest groups are set, else bail

/*
* Confirm the interest groups is an array and not empty or else bail
*/
if( ! is_array( $interest_groupings ) || empty( $interest_groupings ) ) {
	echo '<p class="description no-interest-groupings-enabled-message">' . $interest_groupings . '</p>';
	return;
}

?>
<br />
<p class="description"><?php _e( 'It looks like we found some interest groups! Pre-select interest groups for this integration below.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
<p class="description"><?php _e( 'note: the interest groups will not show up on the front end for your users to select from.', 'yikes-inc-easy-mailchimp-extender' ); ?></p>
<?php

/*
*	Loop over interest groups
*/
foreach( $interest_groupings as $interest_group ) {
	
	$interest_group_id = $interest_group['id'];
	$interest_group_type = ( isset( $interest_group['form_field'] ) ) ? $interest_group['form_field'] : '';
	$interest_groups_fields = ( isset( $interest_group['groups'] ) ) ? $interest_group['groups'] : false; // if not set return false
	$selected_interest_groups = ( isset( $integration_options[$integration_type]['interest-groups'] ) ) ? $integration_options[$integration_type]['interest-groups'] : array();
	?>
	<section class="interest-group-section">
		<strong class="interest-group-section-title"><?php echo ucwords( $interest_group['name'] ); ?></strong>
	<?php
	/*
	*	Loop over the interest group types, and return the appropriate type
	*/	
	switch( $interest_group_type ) {
	
		default:
		case 'hidden':
		case 'checkboxes':
			if( $interest_groups_fields ) {
				foreach( $interest_groups_fields as $field ) {
				?>
					<label>
						<input type="checkbox" name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $interest_group_id; ?>][]" value="<?php echo $field['name']; ?>" <?php if( isset( $selected_interest_groups[$interest_group_id] ) ) {  if( in_array( $field['name'], $selected_interest_groups[$interest_group_id] ) ) { ?> checked="checked" <?php } } ?>>
						<?php echo $field['name']; ?>
					</label>
				<?php
				}
			}
			break;
			
		case 'radio':
			if( $interest_groups_fields ) {
				$x = 1; //  used to decide which is pre-checked
				foreach( $interest_groups_fields as $field ) {
				?>
					<label>
						<input type="radio" name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $interest_group_id; ?>][]" value="<?php echo $field['name']; ?>" <?php if( isset( $selected_interest_groups[$interest_group_id] ) ) {  if( in_array( $field['name'], $selected_interest_groups[$interest_group_id] ) ) { ?> checked="checked" <?php } } elseif( $x == 1 ) { ?> checked="checked" <?php }?>>
						<?php echo $field['name']; ?>
					</label>
				<?php
					$x++;
				}
			}
			break;
			
		case 'dropdown':
			if( $interest_groups_fields ) {
				?><select name="optin-checkbox-init[<?php echo $integration_type; ?>][interest-groups][<?php echo $interest_group_id; ?>][]"><?php
				foreach( $interest_groups_fields as $field ) {
				?>
					<option  value="<?php echo $field['name']; ?>" <?php if( isset( $selected_interest_groups[$interest_group_id] ) ) {  if( in_array( $field['name'], $selected_interest_groups[$interest_group_id] ) ) { ?> selected="selected" <?php } } ?>>
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

?>