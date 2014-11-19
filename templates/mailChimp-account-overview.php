<?php

// create and store our account detail variables
$current_date = date('Y-m-d' /* testing future date, strtotime("+30 days") */ );

// Username
$account_username = $account_details['username'];
// Member Since
$member_since_explosion = explode( ' ' , $account_details['member_since'] );
	$member_since_date = date( 'm/d/Y' , strtotime($member_since_explosion[0]) );
	
// when emails will reset
$email_reset_date =  date('m/d/Y', strtotime($member_since_date. ' + 30 days'));
	
	// loop over to figure out when our reset date will be
	// happens every 30 days!
	if ( strtotime($current_date) > strtotime($email_reset_date) ) {
		while ( strtotime($current_date) > strtotime($email_reset_date) ) {
			$email_reset_date =  date('m/d/Y', strtotime($email_reset_date. ' + 30 days'));
		}
	}
	
// Is Account Activated
$is_activated = $account_details['has_activated'];

// Industry
$industry = $account_details['industry'];

// plan type
$plan_type = $account_details['plan_type'];
	if ( $plan_type = 'free' ) {
		$plan_type = __( 'Free Forever', 'yikes-inc-easy-mailchimp-extender');
		// if the user is a free user,
		// store the number of emails left
		$emails_left  = $account_details['emails_left'];
	} elseif ( $plan_type = 'payasyougo' ) {
		$plan_type = __( 'Pay As You Go', 'yikes-inc-easy-mailchimp-extender');
		// if the user is a free user,
		// store the number of emails left
		$emails_left  = $account_details['emails_left'];
	} else { 
		$plan_type = __( 'Premium Chimp', 'yikes-inc-easy-mailchimp-extender');
	}

// set the styles for the activated indicator div
if ( $is_activated == '1' ) {
	$activated_id = 'yks-mc-account-activated';
} else {
	$activated_id = 'yks-mc-account-not-activated';
}
	
?>

<style>
span.profile_info_span {
	display:block;
	float:left;
	margin: 2.5em;
	width:10%;
}
	span.profile_info_span:first-child {
		margin: 2.5em 2.5em 2.5em 0 !important;
	}
</style>

<div class="wrap">

<div id="is_account_active" style="float:right;text-align:center;">
	<div id="<?php echo $activated_id; ?>"></div>
	<span style="float:right;"><?php if ( $is_activated == 0 ) { _e( 'Account Not Yet Activated', 'yikes-inc-easy-mailchimp-extender'); } else { _e( 'Account Active', 'yikes-inc-easy-mailchimp-extender'); } ?></span>
</div>

<span class="profile_info_span"><h3><?php _e( 'Company', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3><?php echo $account_username; ?></span>
<span class="profile_info_span"><h3><?php _e( 'Member Since', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3><?php echo $member_since_date; ?></span>

<span class="profile_info_span"><h3><?php _e( 'Account Type', 'yikes-inc-easy-mailchimp-extender'); ?> :  </h3><?php echo $plan_type; ?></span>

<?php 
	if ( $plan_type == __( 'Free Forever', 'yikes-inc-easy-mailchimp-extender') || $plan_type == __( 'Pay As You Go', 'yikes-inc-easy-mailchimp-extender') ) {
		?>
		<div id="yks-mc-emails-left">
			<span class="profile_info_span"><h3><?php _e( 'Emails Left', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3><?php echo number_format ($emails_left); ?></span>
			<span class="profile_info_span"><h3><?php _e( 'Emails will reset on', 'yikes-inc-easy-mailchimp-extender'); ?> </h3><em><?php echo $email_reset_date; ?></em></span>
		</div>
	<?php 
		}
	?>
	
	<span class="profile_info_span"><h3><?php _e( 'Industry', 'yikes-inc-easy-mailchimp-extender'); ?> : </h3><?php if ( $industry != '' ) { echo $industry; } else { echo '<em>n/a</em>'; } ?></span>
	
</div>