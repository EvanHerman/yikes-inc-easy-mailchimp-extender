<?php

/* 
Main Template file for generating our front end form fields
based on the setting in the dashboard, table vs div
*/

		switch($this->optionVal['flavor']) {
		
				default:
					break;
					
				// Display the form inside of a table
				// if the user has selected table as their flavor on the settings page
				// make sure this matches exactly with the div flavor below (currently does not)
				case '0':	
					?>
						<!-- BEGIN TABLE FLAVOR -->
						<table class="yks-mailchimpFormTable">
							<?php 
							/* if reCAPTCHA is enabled, we want to display the CAPTCHA form */
							if ( $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-api-key'] != '' && $this->optionVal['recaptcha-private-api-key'] != '' ) {									
									// if on localhost , we should replace backward slash (\) with forward slashes (/) to prevent escaped characters
									if ( $this->yks_mc_is_user_localhost() ) {
										$reCAPTCHA_lib_path = str_replace( '\\' , '/' , YKSEME_PATH."lib/recaptchalib.php" );
									} else {
										$reCAPTCHA_lib_path = YKSEME_PATH."lib/recaptchalib.php.";
									}
								
							} 
							
							$num = 1;	
							
							foreach($list['fields'] as $field) : 
							// get field placeholders
							$form_id = explode( '-', $field['id']);
							
							// add our nonce field for security purposes
							?>
							<tr class="yks-mailchimpFormTableRow">
								<?php wp_nonce_field( 'yks_mc_front_end_form_'.$form_id[1] ); ?>
							</tr>

								<?php 
									if ($field['require'] == 1) { // if the field is required (set in MailChimp), display the red required star	
										$reqindicator 	= " <span class='yks-required-label'>*</span>";
										$reqlabel		= " yks-mailchimpFormTableRowLabel-required";
									} else {  // else don't
										$reqindicator  = "";
										$reqlabel		= "";
									}
								?>
								
								<tr class="yks-mailchimpFormTableRow yks-mc-tr-<?php echo sanitize_title( $field['label'] ); ?>" <?php if($field['active'] == 0) { echo 'style="display:none;"'; } ?>>
									<td class="prompt yks-mailchimpFormTableRowLabel">
										<label class="prompt yks-mailchimpFormTableRowLabel<?php echo $reqlabel; ?> yks-mc-label-<?php echo sanitize_title( $field['label'] ); ?>" for="<?php echo $field['id']; ?>"><?php echo apply_filters( 'yikes_mc_field_label' , stripslashes( $field['label'] ) ); ?><?php echo $reqindicator; ?></label>
										<!-- run our function to generate the input fields for the form, passing in the field -->
										<?php echo $this->getFrontendFormDisplay_field($field,$num); ?>
									</td>
								</tr>	
								<?php 
									$num++;
									endforeach; 
								?>
							<tr class="yks-mailchimpFormTableRow">
								<!-- run our function to generate the interest group fields for the form, passing in the form id -->
								<?php echo $this->getInterestGroups( $form_id[1] ); ?>
								<td class="yks-mailchimpFormTableSubmit">	
									<?php 
									if ( $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-api-key'] != '' && $this->optionVal['recaptcha-private-api-key'] != '' ) { 
										$this->includeRECAPTCHAlib();
									} else if ( $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-api-key'] == '' || $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-private-api-key'] == '') { 
										echo '<div class="yks_mc_recaptcha_api_key_error">'. __('reCAPTCHA API Key Error. Please double check your API Keys.' , 'yikes-inc-easy-mailchimp-extender') . '</div>';
									}
									?>
									<input type="submit" class="ykfmc-submit" id="ykfmc-submit_<?php echo $list['id']; ?>" value="<?php if($submit_text != '') { echo $submit_text; } else {  echo 'Sign Up'; } ?>" />
								</td>
							</tr>
						</table>
					<?php 
					// Create and store our variables for the redirection
					$form_id = explode('-', $field['id']); // get the form ID
					$redirect_value = (isset($field['yks_mailchimp_redirect_'.$form_id[1]]) ? $field['yks_mailchimp_redirect_'.$form_id[1]] : ''); // get the redirect value from the lists page redirect checkbox
					$redirect_page = (isset($field['page_id_'.$form_id[1]]) ? $field['page_id_'.$form_id[1]] : '') ; // get the redirect page that was set in the pages dropdown on the lists page
					$site_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // grab and store the current sites URL
					$redirect_url = get_permalink($redirect_page); // get the permalink of the page we are going to redirect too
						// if redirection was set up for this form, print out our javascript to complete the redirect
						if ($redirect_value == 1) { // only redirect if successful form submission <-----
						?>				
						<script>
								jQuery(document).ready(function() {								
									// jquery redirect on form submission
									var formRedirectPage = '<?php echo $redirect_url ?>';
									var formID = '<?php echo $form_id[0].'-'.$form_id[1]; ?>';
									jQuery('#yks-mailchimp-form_'+formID).submit(function() {
										var interval = setInterval(function() {
											if ( jQuery('.yks-success').is(':visible') ) {
												window.location.replace(formRedirectPage);	
												clearInterval(interval);
												return;
											}
											//do whatever here..
										}, 2000); 
									});
								});
						</script>
						<?php
						}		
					break; // end case 0;
						
					// END TABLE FLAVOR	
						
						
						
						// Display the form inside of a div
						// if the user has selected div as their flavor on the settings page
						case '1':
						?>
						<div class="yks-mailchimpFormDiv">
							<?php 
								/* if reCAPTCHA is enabled, we want to display the CAPTCHA form */
							if ( $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-api-key'] != '' && $this->optionVal['recaptcha-private-api-key'] != ''  ) {
									// if on localhost , we should replace backward slash (\) with forward slashes (/) to prevent escaped characters
									if ( $this->yks_mc_is_user_localhost() ) {
										$reCAPTCHA_lib_path = str_replace( '\\' , '/' , YKSEME_PATH."lib/recaptchalib.php" );
									} else {
										$reCAPTCHA_lib_path = YKSEME_PATH."lib/recaptchalib.php.";
									}
									
							}

							$num = 1;		

							foreach($list['fields'] as $field) :

								// get field placeholders
								$form_id = explode( '-', $field['id']);				
								 
								if ($field['require'] == 1) { // if the field is required (set in MailChimp), display the red required star 
									$reqindicator 	= " <span class='yks-required-label'>*</span>";
									$reqlabel		= " yks-mailchimpFormDivRowLabel-required";
								} else { // else don't
									$reqindicator  = "";
									$reqlabel		= "";
								}
								?>
								<div class="yks-mailchimpFormDivRow yks-mc-form-row-<?php echo sanitize_title( $field['label'] ); ?>" <?php if($field['active'] == 0) { echo 'style="display:none;"'; } ?>>
									<label class="prompt yks-mailchimpFormDivRowLabel<?php echo $reqlabel; ?> yks-mc-label-<?php echo sanitize_title( $field['label'] ); ?>" for="<?php echo $field['id']; ?>"><?php echo apply_filters( 'yikes_mc_field_label' , stripslashes( $field['label'] ) ); ?><?php echo $reqindicator; ?></label>
									<div class="yks-mailchimpFormDivRowField yks-mc-input-field-row-<?php echo sanitize_title( $field['label'] ); ?>">
										<!-- run our function to generate the input fields for the form, passing in the field -->
										<?php echo $this->getFrontendFormDisplay_field($field,$num); ?>
									</div>
								</div>	
								<?php 
									$num++;
									endforeach; 
							?>
							<!-- add our nonce field for security purposes -->
							<div class="yks-mailchimpFormDivRow">
								<?php wp_nonce_field( 'yks_mc_front_end_form_'.$form_id[1] ); ?>
							</div>	
									
							<div class="yks-mailchimpFormDivRow">
								<!-- run our function to generate the interest group fields for the form, passing in the form id -->
								<?php $this->getInterestGroups($form_id[1]); ?>
								<div class="yks-mailchimpFormDivSubmit">
									<?php 	
									if ( $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-api-key'] != '' && $this->optionVal['recaptcha-private-api-key'] != '' ) { 
										$this->includeRECAPTCHAlib();
									} else if ( $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-api-key'] == '' || $this->optionVal['recaptcha-setting'] == '1' && $this->optionVal['recaptcha-private-api-key'] == '') { 
										echo '<div class="yks_mc_recaptcha_api_key_error">'. __('reCAPTCHA API Key Error. Please double check your API Keys.' , 'yikes-inc-easy-mailchimp-extender') . '</div>';
									}
									?>
									<p><input type="submit" class="ykfmc-submit" id="ykfmc-submit_<?php echo $list['id']; ?>" value="<?php if($submit_text != '') { echo $submit_text; } else {  echo 'Sign Up'; } ?>" /></p>
								</div>
							</div>
						</div>
						<?php 
						// Create and store our variables for the redirection
						$form_id = explode('-', $field['id']); // get the form ID
						$redirect_value = (isset($field['yks_mailchimp_redirect_'.$form_id[1]]) ? $field['yks_mailchimp_redirect_'.$form_id[1]] : ''); // get the redirect value from the lists page redirect checkbox
						$redirect_page = (isset($field['page_id_'.$form_id[1]]) ? $field['page_id_'.$form_id[1]] : '') ; // get the redirect page that was set in the pages dropdown on the lists page
						$site_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // grab and store the current sites URL
						$redirect_url = get_permalink($redirect_page); // get the permalink of the page we are going to redirect too
						// if redirection was set up for this form, print out our javascript to complete the redirect
						if ($redirect_value == 1) {
						?>			
						<script>
								jQuery(document).ready(function() {								
									// jquery redirect on form submission
									var formRedirectPage = '<?php echo $redirect_url ?>';
									var formID = '<?php echo $form_id[0].'-'.$form_id[1]; ?>';
									jQuery('#yks-mailchimp-form_'+formID).submit(function() {
										var interval = setInterval(function() {
											if ( jQuery('.yks-success').is(':visible') ) {
												window.location.replace(formRedirectPage);	
												clearInterval(interval);
												return;
											}
											//do whatever here..
										}, 2000); 
									});
								});
						</script>
						<?php
						}		
					break; // end case: 1
			}

?>