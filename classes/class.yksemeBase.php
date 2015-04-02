<?php
if(!class_exists("yksemeBase")) {
	class yksemeBase {
		
			/**
			*	Variables
			*/
			private	$error = false;
			private	$errorMsg = '';
			public	$sessName = 'ykseme';
			public	$optionVal = false;
			public	$currentLists = false;
			public	$currentListsCt = false;

			/**
			*	Construct
			*/
			public function __construct() {
					yksemeBase::initialize();
					add_action('init', array(&$this, 'ykes_mc_apply_filters'));
				}

			/**
			*	Destruct
			*/
			public function __destruct() {
					unset($this);
				}

			/**
			*	Actions
			*	These are called when the plug in is initialized/deactivated/un-installed
			*/
			public function activate() {
					// redirect the user on plugin activation
					// to our MailChimp settings page
					add_option('yks_easy_mc_plugin_do_activation_redirect', true);
					
					// check if our option is already set
					// if it exists, return
					if( get_option( 'api_validation' ) ) {
						return;
					} else { // else create it
						add_option('api_validation' , 'invalid_api_key');
					}
										
				}
				
			public function deactivate() {
				
				}
				
			public function uninstall() { // delete options on plugin uninstall
					delete_option(YKSEME_OPTION);
					delete_option('api_validation');
					delete_option('imported_lists');
				}

			/***** INITIAL SETUP
			 ****************************************************************************************************/
			public function initialize() {
					// include our initialization file
					include YKSEME_PATH . 'lib/inc/yks-mc-init.php';
					
					/* 
						Conditionally Include the MailChimp Class File 
					*/
					if( !class_exists("Mailchimp") ) {
						if ( $this->optionVal['ssl_verify_peer'] == 'true' ) {
							require_once YKSEME_PATH.'classes/MCAPI_2.0.class.php';
						} else {
							require_once YKSEME_PATH.'classes/MCAPI_2.0.class.verify_false.php';
						}
					}
					
					/*
					* add our new ssl_verify_peer debug option, if it doesn't already exist
					* @since v5.2
					*/
					if ( !isset( $this->optionVal['ssl_verify_peer'] ) ) {
						$options = get_option( YKSEME_OPTION );
						$options['ssl_verify_peer'] = 'true';
						update_option( YKSEME_OPTION , $options );
					}
					
					/*
					* add our new hide 'required text' option, if it doesn't already exist
					* @since v5.3
					*/
					if ( !isset( $this->optionVal['yks-mailchimp-required-text'] ) ) {
						$options = get_option( YKSEME_OPTION );
						$options['yks-mailchimp-required-text'] = '0';
						update_option( YKSEME_OPTION , $options );
					}
					
				}
		
			// register and add our shortcodes
			public function createShortcodes() {
					add_shortcode( 'yks-mailchimp-list' , array( &$this, 'processShortcode' ) );
					add_shortcode( 'yks-mailchimp-subscriber-count' , array( &$this, 'displaySubscriberCount') );
				}

			/** Custom Filter To Alter User Submitted Data **/
			public function yikes_mc_get_form_data_filter( $mv ) {
					return $mv;
				}

			/** Custom Filter To Alter User Already Subscribed Error Message **/
			public function yikes_mc_user_already_subscribed_error_message_filter( $errorMessage , $email ) {
					return $errorMessage;
				}

			// Create and store our initial plugin options	
			public function getOptionValue() {
					$blog_title = get_bloginfo( 'name' );
					$defaultVals	= array(
						'version'	=> YKSEME_VERSION_CURRENT,
						'api-key'	=> '',
						'flavor'	=> '1',
						'debug'	=> '0',
						'optin' => 'true',
						'single-optin-message' => __('Thank You for subscribing!', 'yikes-inc-easy-mailchimp-extender'),
						'double-optin-message' => __('Thank You for subscribing! Check your email for the confirmation message.', 'yikes-inc-easy-mailchimp-extender'),
						'optIn-checkbox'	=> 'hide',
						'yks-mailchimp-optIn-default-list' => 'select_list',
						'yks-mailchimp-optin-checkbox-text'	=> 'Add me to the ' . $blog_title . ' mailing list',
						'recaptcha-setting' => '0',
						'yks-mailchimp-required-text' => '0',
						'recaptcha-api-key' => '',
						'recaptcha-private-api-key' => '',
						'ssl_verify_peer' => 'true',
						'lists'		=> array()
					);
					$ov	= get_option(YKSEME_OPTION, $defaultVals);
					$this->optionVal	= $ov;
					return $ov;
				}
			
			// run our update check to make sure the user is up to date
			private function runUpdateCheck() {
					if( ! isset( $this->optionVal['version'] ) || $this->optionVal['version'] < YKSEME_VERSION_CURRENT ) {
						$this->runUpdateTasks(); 
						}
				}


			/***** FUNCTIONS
			 ****************************************************************************************************/
			// check if were on the login page
			function is_login_page() {
					return in_array( $GLOBALS['pagenow'] , array( 'wp-login.php', 'wp-register.php' ) );
				}
				
			// create a slug like string, given some text (ie: this-is-the-name)
			public function slugify( $text ) { 
				  // replace non letter or digits by -
				  $text = preg_replace( '~[^\\pL\d]+~u', '-', $text );
				  // trim
				  $text = trim( $text, '-' );
				  // transliterate
				  $text = iconv( 'utf-8' , 'us-ascii//TRANSLIT' , $text );
				  // lowercase
				  $text = strtolower( $text);
				  // remove unwanted characters
				  $text = preg_replace( '~[^-\w]+~' , '' , $text );
				  if(empty($text))
					{
					return 'n-a';
					}
				  return $text;
				}
				
			// create an array for any fields left blank
			// not sure if still needed	 (CHECK)
			public function getBlankFieldsArray( $lid='' ) {
					$fields		= array();
					// Add Field
					$name	= $this->slugify('Email Address'.'-'.'EMAIL');
					$addField	= array(
						'id'		=> $lid.'-'.$name,
						'name'		=> $lid.'-'.$name,
						'merge'		=> 'EMAIL',
						'label'		=> 'Email Address',
						'require'	=> '1',
						'active'	=> '1',
						'locked'	=> '1',
						'sort'		=> '1',
						'type'		=> 'email',
						'help'		=> '',
						'defalt'	=> '',
						'choices'	=> ''
					);
					$fields[$addField['id']]	= $addField;
					// return our fields
					return $fields;
				}

			// Create an array of data for imported fields based on the Merge Varaibles passed back from MailChimp
			public function getImportedFieldsArray( $lid, $mv ) {
					if( empty( $mv ) ) {
						return false;
					} else {
						$fields = array();
						$num = 1;
						foreach($mv['data'][0]['merge_vars'] as $field) {
								// Add Field
								$name	= $this->slugify(isset($field['label']).'-'.$field['tag']);
								$addField	= array(
									'id'		=> $lid.'-'.$name,
									'name'		=> $lid.$field['tag'],
									'merge'		=> $field['tag'],
									'label'		=> $field['name'],
									'require'	=> $field['req'],
									'active'	=> '1',
									'locked'	=> '1',
									'sort'		=> $field['order'],
									'type'		=> $field['field_type'],
									'help'		=> $field['helptext'],
									'default'	=> $field['default'],
									'choices'	=> (isset($field['choices']) ? $field['choices'] : '')
								);
								$fields[$addField['id']] = $addField;
								$num++;
							}
						return $fields;
					}
				}
				
			// Get the current users browser information
			// Used specifically on the lists page	
			public function getBrowser() { 
					$u_agent	= $_SERVER['HTTP_USER_AGENT']; 
					$bname		= 'Unknown';
					$platform	= 'Unknown';
					$version	= "";
					//First get the platform?
					if( preg_match( '/linux/i', $u_agent ) ) {
						$platform = 'Linux';
					} elseif( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
						$platform = 'Mac';
					} elseif( preg_match( '/windows|win32/i' , $u_agent ) ) {
						$platform = 'Windows';
					}
					
					// Next get the name of the useragent yes seperately and for good reason
					if( preg_match( '/MSIE/i' , $u_agent ) && ! preg_match( '/Opera/i' , $u_agent ) ) { 
						$bname = 'Internet Explorer'; 
						$ub = "MSIE"; 
					} elseif( preg_match( '/Firefox/i' , $u_agent ) ) { 
						$bname = 'Mozilla Firefox'; 
						$ub = "Firefox"; 
					} elseif( preg_match( '/Chrome/i' , $u_agent ) ) { 
						$bname = 'Google Chrome'; 
						$ub = "Chrome"; 
					} elseif( preg_match( '/Safari/i' , $u_agent ) ) { 
						$bname = 'Apple Safari'; 
						$ub = "Safari"; 
					} elseif( preg_match( '/Opera/i' , $u_agent ) ) { 
						$bname = 'Opera'; 
						$ub = "Opera"; 
					} elseif( preg_match( '/Netscape/i' , $u_agent ) ) { 
						$bname = 'Netscape'; 
						$ub = "Netscape"; 
					} 
					
					// finally get the correct version number
					$known = array('Version', $ub, 'other');
					$pattern = '#(?<browser>' . join( '|' , $known ) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
					if( ! preg_match_all( $pattern, $u_agent, $matches) ) {
						// we have no matching number just continue
					}
					
					// see how many we have
					$i = count($matches['browser']);
					if( $i != 1 ) {
						//we will have two since we are not using 'other' argument yet
						//see if version is before or after the name
						if( strripos( $u_agent ,"Version" ) < strripos( $u_agent , $ub ) ) {
							$version= $matches['version'][0];
						} else {
							$version= $matches['version'][1];
						}
					} else {
						$version= $matches['version'][0];
					}
					
					// check if we have a number
					if( $version==null || $version=="" ) { $version="?"; }
					
					return array(
						'userAgent' => $u_agent,
						'name'      => $bname,
						'version'   => $version,
						'platform'  => $platform,
						'pattern'    => $pattern
					);
				}

			/***** Encryption/Decryption
			****** API Keys (MailChimp and reCaptcha)
			*****************************************************************************************************/
			function yikes_mc_encryptIt( $q ) {
					$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
					$qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
					return( $qEncoded );
				}
				
			function yikes_mc_decryptIt( $q ) {
					$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
					$qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
					return( $qDecoded );
				}	


			/***** CONFIGURATION
			 ****************************************************************************************************/
			// Update our plug in options
			// Runs when the user updates the settings page with new values 
			public function updateOptions( $p ) {
					if( ! empty( $p['form_data'] ) ) {
							parse_str( $p['form_data'] , $fd );
							// checking if the entered API key has copied out of the value field?
								if ( strlen( $fd['yks-mailchimp-api-key'] ) > 45 ) {
									$apiKey = $this->yikes_mc_decryptIt($fd['yks-mailchimp-api-key']);
								} else {
									$apiKey = $fd['yks-mailchimp-api-key'];
								}
							// check if the newly input API key differs from the previously stored one
							if ( $apiKey == $this->optionVal['api-key'] ) {
								$this->optionVal['api-key'] = $apiKey;
								$this->optionVal['flavor'] = $fd['yks-mailchimp-flavor'];
								$this->optionVal['optin'] = $fd['yks-mailchimp-optin'];
								$this->optionVal['single-optin-message'] = stripslashes($fd['single-optin-message']);
								$this->optionVal['double-optin-message'] = stripslashes($fd['double-optin-message']);
								$this->optionVal['optIn-checkbox'] = $fd['yks-mailchimp-optIn-checkbox'];
								$this->optionVal['yks-mailchimp-optIn-default-list'] = isset($fd['yks-mailchimp-optIn-default-list']) ? $fd['yks-mailchimp-optIn-default-list'] : null; // if its set, else set to null <- fixes save form settings bug
								$this->optionVal['yks-mailchimp-optin-checkbox-text'] = stripslashes($fd['yks-mailchimp-optin-checkbox-text']);
								$this->optionVal['yks-mailchimp-jquery-datepicker'] = isset( $fd['yks-mailchimp-jquery-datepicker'] ) ? '1' : '';
								$this->optionVal['yks-mailchimp-required-text'] = $fd['yks-mailchimp-required-text'];
								update_option('api_validation', 'valid_api_key');
								return update_option( YKSEME_OPTION , $this->optionVal );
							} else {
								$this->optionVal['api-key'] = $apiKey;
								$this->optionVal['flavor'] = $fd['yks-mailchimp-flavor'];
								$this->optionVal['optin'] = $fd['yks-mailchimp-optin'];
								$this->optionVal['single-optin-message'] = stripslashes($fd['single-optin-message']);
								$this->optionVal['double-optin-message']	= stripslashes($fd['double-optin-message']);
								$this->optionVal['optIn-checkbox'] = $fd['yks-mailchimp-optIn-checkbox'];
								$this->optionVal['yks-mailchimp-optIn-default-list'] = isset($fd['yks-mailchimp-optIn-default-list']) ? $fd['yks-mailchimp-optIn-default-list'] : null; // if its set, else set to null <- fixes save form settings bug
								$this->optionVal['yks-mailchimp-optin-checkbox-text'] = stripslashes($fd['yks-mailchimp-optin-checkbox-text']);
								$this->optionVal['yks-mailchimp-jquery-datepicker'] = isset( $fd['yks-mailchimp-jquery-datepicker'] ) ? '1' : '';
								$this->optionVal['yks-mailchimp-required-text'] = $fd['yks-mailchimp-required-text'];
								update_option('api_validation', 'valid_api_key');
								// if the new API key differs from the old one
								// we need to unset the previously set up widgets				
								// 1 - empty the lists array of imported lists
								$this->optionVal['lists'] = array();
								// 2 - unset our previously set up widgets
								update_option( 'widget_yikes_mc_widget' , '' );
								return update_option( YKSEME_OPTION , $this->optionVal );
							}
						}
					return false;
				}
					
			// Update our recaptcha options
			// Runs when the user updates the recaptcha settings page with new values
			public function updateRecaptchaOptions( $p ) {
					if( !empty( $p['form_data'] ) ) {
							parse_str($p['form_data'], $fd);
							$this->optionVal['recaptcha-setting'] = isset($fd['yks-mailchimp-recaptcha-setting']) ? $fd['yks-mailchimp-recaptcha-setting'] : '0';
							$this->optionVal['recaptcha-api-key'] = isset($fd['yks-mailchimp-recaptcha-api-key']) ? $fd['yks-mailchimp-recaptcha-api-key'] : '';
							$this->optionVal['recaptcha-private-api-key'] = isset($fd['yks-mailchimp-recaptcha-private-api-key']) ? $fd['yks-mailchimp-recaptcha-private-api-key'] : '';
							return update_option( YKSEME_OPTION , $this->optionVal );
						}
					return false;
				}

			// Update our debug plugin options
			// Runs when the user updates the debug settings page with new values 
			public function updateDebugOptions( $p ) {
					if( !empty( $p['form_data'] ) ) {
							parse_str($p['form_data'], $fd);
							$this->optionVal['debug']	= $fd['yks-mailchimp-debug'];
							$this->optionVal['ssl_verify_peer'] = $fd['yks-mailchimp-ssl-verify-peer'];
							return update_option(YKSEME_OPTION, $this->optionVal);
						}
					return false;
				}	
			
			// Update the API Key	
			public function updateApiKey( $k ) {
					$this->optionVal['api-key'] = $k; 
					return update_option( YKSEME_OPTION , $this->optionVal );
				}
					
			// Update the version number	
			public function updateVersion( $k ) {
					$this->optionVal['version'] = $k; 
					return update_option( YKSEME_OPTION , $this->optionVal );
				}

			/***** LIST ACTIONS
			 ****************************************************************************************************/
			// Import a list from MailChimp and add it to the lists page
			// Runs when user adds a list from the drop down on the list page
			// Sends a call to MailChimp api to retrieve list data
			 public function addList( $lid='' , $name='' ) {
				
					if( $lid == '' || isset( $this->optionVal['lists'][$lid] ) ) return false;
					
					$api	= new Mailchimp($this->optionVal['api-key']);
					
					$mv = $api->call('lists/merge-vars', array(
								'id' => array($lid)
							)
						);		
					
					// if merge variables are found
					if( $mv) {
						$list	= array(
							'id' => $lid,
							'list-id' => $lid,
							'name' => $name,
							'fields' => $this->getImportedFieldsArray( $lid , $mv )
						);
								
						$this->optionVal['lists'][$list['id']]	= $list;
						
						// store newly retreived list array in imported_list global option
						update_option( 'imported_lists' , $this->optionVal['lists'] );
						
						if( update_option( YKSEME_OPTION, $this->optionVal ) ) {
							return $this->generateListContainers( array( $list ) );
						}
					}
					
					return false;
				}
		

			/*		Get Interest Groups	*/
			// Send request to MailChimp API to retreive interest groups associated to a specific list
			public function getInterestGroups( $list_id ) {
					// store our API key
					$api = new Mailchimp($this->optionVal['api-key']);
					
					// setup switch for div/table
					$yikes_mc_flavor = $this->optionVal['flavor'];
					
					// try the request, and catch any errors
					try {
						
						$interest_groups = $api->call('lists/interest-groupings', array( 'id' => $list_id ));
						
						// if the list has an interest group
						if ($interest_groups) {
							// json_encode the data, and store it in optionVal['interest-groups']
							$this->optionVal['interest-groups'] = json_encode($interest_groups);
										
							$num = 0;			
									
							switch( $yikes_mc_flavor ) {
								
								// table flavor
								case '0':
					
									// loop over each interest group returned
									foreach($interest_groups as $interest_group) {
										
										?> <!-- pass interest group data in a hidden form field , required to pass the data back to the correct interest-group -->
										<input type='hidden' name='interest-group-data' value='<?php echo $this->optionVal["interest-groups"]; ?>' /> <?php
										
										// get form type
										$list_form_type = $interest_group['form_field'];
										$interestGroupID = $interest_group['id'];
										
										switch( $list_form_type ) {
																	
											// checkbox interest groups
											case 'checkboxes':
												echo '<tr class="yks_mc_table_interest_group_holder yks_mc_table_checkbox_holder">';
													echo '<td class="yks_mc_table_td">';
													// display the label
													echo '<label class="prompt yks_table_label yks-mailchimpFormTableRowLabel yks-mailchimpFormTableRowLabel-required font-secondary label-text">'.stripslashes( $interest_group['name'] ).'</label>'; // display the interest group name from MailChimp
													foreach ($interest_group['groups'] as $singleGrouping) {
														$checkboxValue = $interest_group['name'];
														echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="checkbox" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_checkbox" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'[]" value="'.$singleGrouping['name'].'"><span>'.$singleGrouping['name'].'</span></label>';
													}
													echo '</td>';
												echo '</tr>';					
												break;
																	
											// radiobuttons interest groups									
											case 'radio':
												echo '<tr class="yks_mc_table_interest_group_holder yks_mc_table_radio_holder">';
													echo '<td class="yks_mc_interest_radio_button_holder yks_mc_table_td">';
														// display the label
														echo stripslashes($user_set_interest_group_label);
														foreach ($interest_group['groups'] as $singleGrouping) {
															$radioValue = $interest_group['name'];
															echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="radio" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_radio" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" value="'.$singleGrouping['name'].'"><span>'.stripslashes($singleGrouping['name']).'</span></label>';
														}
													echo '</td>';	
												echo '</tr>';	
												break;
																	
											// drop down interest groups
											case 'dropdown':	
												echo '<tr class="yks_mc_table_interest_group_holder yks_mc_table_dropdown_holder">';	
													echo '<td class="yks_mc_table_dropdown_interest_group_holder yks_mc_table_td">';
														// display the label
														echo stripslashes($user_set_interest_group_label);
														echo '<select id="yks_mc_interest_dropdown"  name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" class="yks_mc_interest_group_select">';
															foreach ($interest_group['groups'] as $singleGrouping) {
																$dropDownValue = $interest_group['name'];
																echo '<option value="'.$singleGrouping['name'].'" name="'.$dropDownValue.'">'.$singleGrouping['name'].'</option>';
															}
														echo '</select>';	
													echo '</td>';
												echo '</tr>';			
												break;
												
											// hidden dropdown interest groups
											case 'hidden':	
												echo '<div class="yks_mc_interest_group_holder" style="display:none;">';	
													echo '<select id="yks_mc_interest_dropdown"  name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" class="yks_mc_interest_group_select">';
														foreach ($interest_group['groups'] as $singleGrouping) {
															$dropDownValue = $interest_group['name'];
															echo '<option value="'.$singleGrouping['name'].'" name="'.$dropDownValue.'">'.$singleGrouping['name'].'</option>';
														}
													echo '</select>';	
												echo '</div>';			
												break; //break dropdown interest group type
												
										}
										
										$num++;
									}
													
								break; // break case: 0;	
													
								// div flavor
								case '1':
									// loop over each interest group returned
									foreach($interest_groups as $interest_group) {
									
										// get form type
										$list_form_type = $interest_group['form_field'];
										$interestGroupID = $interest_group['id'];
										if ( $list_form_type == 'hidden' ) { $hidden = 'style="display:none;"'; } else { $hidden = ''; }
										
										echo '<b class="yks_mc_interest_group_text" ' . $hidden . '>'.$interest_group['name'].'</b>';
										?>
										
										<input type='hidden' name='interest-group-data' value='<?php echo $this->optionVal["interest-groups"]; ?>' />
										
										<?php
																			
										switch($list_form_type) {
											
											// checkbox interest groups
											case 'checkboxes':	
												echo '<div class="yks_mc_interest_group_holder">';
													foreach ($interest_group['groups'] as $singleGrouping) {
														$checkboxValue = $interest_group['name'];
														echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="checkbox" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_checkbox" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'[]" value="'.$singleGrouping['name'].'"><span></span>'.stripslashes($singleGrouping['name']).'</label>';
													}
												echo '</div>';					
												break; // break checkbox interest group type
																
											// radiobuttons interest groups									
											case 'radio':
												echo '<div class="yks_mc_interest_group_holder">';
													echo '<div class="yks_mc_interest_radio_button_holder">';
														echo stripslashes($user_set_interest_group_label);
														foreach ($interest_group['groups'] as $singleGrouping) {
															$radioValue = $interest_group['name'];
															echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="radio" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_radio" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" value="'.$singleGrouping['name'].'"><span></span>'.stripslashes($singleGrouping['name']).'</label>';
														}
													echo '</div>';	
												echo '</div>';	
												break; //break radio buttons interest group type
																
											// drop down interest groups
											case 'dropdown':	
												echo '<div class="yks_mc_interest_group_holder">';	
													echo '<select id="yks_mc_interest_dropdown"  name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" class="yks_mc_interest_group_select">';
														foreach ($interest_group['groups'] as $singleGrouping) {
															$dropDownValue = $interest_group['name'];
															echo '<option value="'.$singleGrouping['name'].'" name="'.$dropDownValue.'">'.$singleGrouping['name'].'</option>';
														}
													echo '</select>';	
												echo '</div>';			
												break; //break dropdown interest group type
												
											// hidden dropdown interest groups
											case 'hidden':	
												echo '<div class="yks_mc_interest_group_holder" style="display:none;">';	
													// display the label
													echo stripslashes($user_set_interest_group_label);
													echo '<select id="yks_mc_interest_dropdown"  name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" class="yks_mc_interest_group_select">';
														foreach ($interest_group['groups'] as $singleGrouping) {
															$dropDownValue = $interest_group['name'];
															echo '<option value="'.$singleGrouping['name'].'" name="'.$dropDownValue.'">'.$singleGrouping['name'].'</option>';
														}
													echo '</select>';	
												echo '</div>';			
												break; //break dropdown interest group type
												
										}
										$num++;
									}
									break; //break case: 1;
												
							}
						}
					// catch any errors if thrown	
					} catch( Exception $e ) {
						if ( $this->optionVal['debug'] == 1 ) {
							$this->writeErrorToErrorLog( $e );
						}
						return;
					}
				return false;
				} // end getInterestGroups();	
				
			// Send a call to the MailChimp API to retreive all lists on the account		
			public function getLists() {
					$api	= new Mailchimp($this->optionVal['api-key']);
					$lists	= $this->getListsData();
					$listArr	= (!isset($listArr) ? $this->optionVal['lists'] : $listArr);
					$theusedlist = array();
					if( count( $listArr ) > 0 ) {
						foreach( $listArr as $list ) {
							$theusedlist[] = $list['id'];
						}
					}
					if( $lists ) { // if list data is returned 
						echo "<select id='yks-list-select' name='yks-list-select'>";
						echo "<option value=''> Select List</option>";
						
						foreach ( $lists as  $lkey => $lvalue ) {
								if ( !in_array( $lkey , $theusedlist ) ) {
										echo "<option value='".$lkey."'>".$lvalue."</option>";		
									}
							}
							
						echo "</select>";
						echo '<input type="submit" name="submit" class="button-primary" id="yks-submit-list-add" value="' .__ ("Create a Form For This List" , "yikes-inc-easy-mailchimp-extender" ) .'"  >';
						
						// not currently possible to create new lists via API
						// echo '&nbsp;<a href="#" onclick="return false;" class="button-secondary"><span class="dashicons dashicons-plus" style="line-height:1.3"></span> Create New List</a>';
						
					} else {
						echo '<strong>' . __('Error - No Lists Found On Your Account. Please create at least one list on your MailChimp account.' , 'yikes-inc-easy-mailchimp-extender' ) . '</strong>';
					}
					return false;
				} // end getLists();
				
			// Get lists for the settings page
			// Used for default subscription list		
			public function getOptionsLists() {
					$api = new Mailchimp($this->optionVal['api-key']);
					$lists = $this->getListsData();
					$listArr = (!isset($listArr) ? $this->optionVal['lists'] : $listArr);
					if( $lists ) {
						echo "<select id='yks-mailchimp-optIn-default-list' name='yks-mailchimp-optIn-default-list'>";
						echo "<option value='select_list'> Select List</option>";
						foreach ( $lists as  $lkey => $list_name) {
							echo "<option ".selected( isset($this->optionVal['yks-mailchimp-optIn-default-list']) ? $this->optionVal['yks-mailchimp-optIn-default-list'] : "select_list", $lkey )." value='".$lkey."'>".$list_name."</option>";		
						}
						echo "</select>";
					}
					return false;
				} // end getOptionsLists()
		
			// Send a call to MailChimp API to get the data associated with a specific list (in this instance: the fields, and the subscriber count)	
			public function getListsData() {
					$api	= new Mailchimp($this->optionVal['api-key']);
					$lists	= $api->call('lists/list', array( 'limit' => 100 ));
					if( $lists ) {
						foreach ( $lists['data'] as $list ) {
							$theListItems[$list['id']] =  $list['name'];	
							$theListItems['subscriber-count']['subscriber-count-'.$list['id']] = $list['stats']['member_count'];
						}		
					}

					if ( isset ( $theListItems ) ) {
						return $theListItems;
					}
				} // end getListsData();

			/*
			 Send a call to MailChimp API to get the data associated with a specific list (in this instance: the fields, and the subscriber count)	
			 @since v5.2
			*/
			public function getInterstGroupInfo( $list_id ) {
					$api = new Mailchimp($this->optionVal['api-key']);
					try {
						$interest_groups = $api->call('lists/interest-groupings', array( 'id' => $list_id ));
						return $interest_groups;
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						return $e->getMessage();
						// write our error to the error log, when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
							$this->writeErrorToErrorLog( $e );
						}
						die();
					}
				}	

			/*
			 Get Interest Group Response
			 @sinve v5.2
			*/
			public function getListInterestGroups( $list_id ) {
					$interest_groups = $this->getInterstGroupInfo( $list_id  );
					if ( is_array( $interest_groups ) ) {
						foreach ( $interest_groups as $interest_group ) {
							$interest_group_id = $interest_group['id'];
							$interest_group_type = $interest_group['form_field'];
							$interest_group_name = $interest_group['name'];
							echo '<div class="yks-mailchimp-fields-list-row" alt="' . $interest_group_id . '">
										
										<span class="yks-mc-interest-group-delete" title="Delete ' . $interest_group_name . '">
											<span class="dashicons dashicons-no-alt"></span>
										</span>
										
										<span class="yks-mc-interest-group-edit" title="Edit ' . $interest_group_name . '">
											<span class="dashicons dashicons-edit"></span>
										</span>
										
										<span class="yks-mc-interest-group-name">' . $interest_group_name . '</span>
										
										<span class="yks-mc-interest-group-type">' . $interest_group_type . '</span>
										
									</div>';
						}
					} else {
					?>
					<style>
						#yks-mailchimp-interest-groups-container_<?php echo $list_id; ?> {
							background-color: transparent !important;
							border: none !important;
						}
					</style>
					<span class="no-interest-groups-found"><em><?php echo __( 'No interest groups have been setup for this form yet' , 'yikes-inc-easy-mailchimp-extender' ); ?></em></span>
					<?php
					}
				} // end getListInterestGroups();

			/*
			 Get Specific Interest Group Response
			 @since v5.2
			*/
			public function getSpecificInterestGroupData( $list_id , $mc_interest_group_id ) {	
					// get interest group info,
					$interest_groups = $this->getInterstGroupInfo( $list_id  );
					if ( $interest_groups ) {
						foreach ( $interest_groups as $key ) {
							if ( $key['id'] == $mc_interest_group_id ) {
								echo json_encode( $key );
							}
						}
					} else {
						echo 'error retreiving group information...please try again';
					}
				}
				
			// Send a call to MailChimp API to get the data associated with a specific list (in this instance: the fields, and the subscriber count)	
			public function getListsForStats() {
					$api	= new Mailchimp($this->optionVal['api-key']);
					$lists	= $this->getListsData();
					$listArr	= (!isset($listArr) ? $this->optionVal['lists'] : $listArr);
					$theusedlist = array();
					if( count( $listArr ) > 0 ) {
						foreach( $listArr as $list ) {
							$theusedlist[] = $list['id'];
						}
					}
					if( $lists ) {
						// Drop Down to switch form stats
						echo '<h3>Select list to view stats</h3>';
						echo '<div class="list_container_for_stats">';
							echo "<a alt='' href='#' class='stats_list_name' onclick='return false;'><input type='button' class='asbestos-flat-button active_button' value='".__( 'All Lists' , 'yikes-inc-easy-mailchimp-extender')."'></a>";
							foreach ($lists as  $lkey => $lvalue) {
								if ( is_array($lvalue) ) {
									continue;
								} else {
									// if ( is_array( $lvalue ) ) { $lvalue = '<em style="color:rgba(245, 79, 79, 0.74);">Error</em>'; } else { $lvalue = $lvalue; }
									echo "<a alt='".$lkey."' href='#' class='stats_list_name' onclick='return false;'><input type='button' class='asbestos-flat-button' value='".$lvalue."'></a>";	
								}
							}
						echo '</div>';	
					}
					return false;
				} // end getListsForStats()
		
			// Sort through the returned data	
			public function sortList( $p ) {
					if( empty( $p['update_string'] ) || empty( $p['list_id'] ) ) {
						return false;
					} else {
						// Setup fields
						$a = explode( ';' , $p['update_string'] );
						if( $a !== false ) {
							foreach( $a as $f ) {
								$d  = explode( ':' , $f );
								$this->optionVal['lists'][$p['list_id']]['fields'][$d[0]]['sort']	= $d[1];
							}
						}
						uasort($this->optionVal['lists'][$p['list_id']]['fields'], array(&$this, 'sortListFields'));
						return update_option(YKSEME_OPTION, $this->optionVal);
					}
					return false;
				} // end sortList();
		
			private function sortListfields( $a , $b ) {
					$a = $a['sort'];
					$b = $b['sort'];
					if( $a == $b ) {
						return 0;
					}
					return ( $a < $b ) ? -1 : 1;
				} // end sortListfields();
		
			// Update a single list on the lists page
			// This function fires when the user clicks 'save settings' for a specific form on the lists page	
			public function updateList( $p ) {
					if( !empty( $p['form_data'] ) ) {
						parse_str($p['form_data'], $fd);
						if( !empty( $fd['yks-mailchimp-unique-id'] ) ) {
							$num = 1;
							foreach( $this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'] as $k => $v ) {
								// Only proceed if the field is  not locked
								if( $v['require'] == 0 ) {
									// Make sure this field was included in the update
									$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['active'] = (isset($fd[$v['name']]) ? '1' : '0');
								}
								
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['placeholder-'.$fd['yks-mailchimp-unique-id'].'-'.$num] = $fd['placeholder-'.$fd['yks-mailchimp-unique-id'].'-'.$num];
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['custom-field-class-'.$fd['yks-mailchimp-unique-id'].'-'.$num] = $fd['custom-field-class-'.$fd['yks-mailchimp-unique-id'].'-'.$num];
								$num++;
									
								// redirect checkbox
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['yks_mailchimp_redirect_'.$fd['yks-mailchimp-unique-id']] = (isset($fd['yks_mailchimp_redirect_'.$fd['yks-mailchimp-unique-id']]) ? '1' : '');
								
								// send welcome checkbox
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['yks_mailchimp_send_welcome_'.$fd['yks-mailchimp-unique-id']] = (isset($fd['yks_mailchimp_send_welcome_'.$fd['yks-mailchimp-unique-id']]) ? '1' : '');
								
								if(isset($fd['yks_mailchimp_redirect_'.$fd['yks-mailchimp-unique-id']])) {
									$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['page_id_'.$fd['yks-mailchimp-unique-id']] = $fd['page_id_'.$fd['yks-mailchimp-unique-id']];
								}
									
								// custom style setting
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['active'] = (isset($fd['yks_mailchimp_custom_styles_'.$fd['yks-mailchimp-unique-id']]) ? '1' : '0');
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_template']['active'] = (isset($fd['yks_mailchimp_custom_template_'.$fd['yks-mailchimp-unique-id']]) ? '1' : '0');
							}
								
							// save the selected form template, if custom template was set
							if ( $this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_template']['active'] == 1 ) {
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_template']['template_file'] = $fd['yks-mc-template-file-selection'];
							}
								
							// save the color styles
							if ( $this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['active'] == 1 ) {
							
								// save the custom styles colors here!
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_submit_button_color'] = $fd['yks-mc-submit-button-color'];
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_submit_button_text_color'] = $fd['yks-mc-submit-button-text-color'];
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_background_color'] = $fd['yks-mc-background-color'];
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_text_color'] = $fd['yks-mc-text-color'];
								
								// if the form width is left blank,
								// we'll just set it to 100%
								if ( $fd['yks-mc-form-width'] != '' ) {
									$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_width'] = $fd['yks-mc-form-width'];
								} else {
									$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_width'] = '100%';
								}
									
								$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_alignment'] = $fd['yks-mc-form-alignment'];		
									
								$padding_matches = array();
								$matching_array = array( 'px' , 'em' , 'rem' , '%' );
										
								if ( preg_match( '/([0-9.]+)([^0-9]+)/', $fd['yks-mc-form-padding'], $padding_matches ) ) {
									// $padding_explosion = preg_split( '/[a-zA-Z]/' , $fd['yks-mc-form-padding'] );
									$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_padding'] = $padding_matches[1];
									if ( in_array( $padding_matches[2] , $matching_array ) ) {
										$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_padding_measurement'] = $padding_matches[2];
									} else {
										$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_padding_measurement'] = 'px';
									}
								} else {
									if ( trim( $fd['yks-mc-form-padding'] ) != '' ) {
										$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_padding'] = $fd['yks-mc-form-padding'];
									} else {
										$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_padding'] = '0';
									}
										$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['custom_styles']['yks_mc_form_padding_measurement'] = 'px';
								}							
							}
							
							return update_option(YKSEME_OPTION, $this->optionVal);
						}
					}
					return false;
				} // end updateList();
		
			// Remove a list from the lists page
			// Runs when a user clicks 'delete list' on the lists page	
			public function deleteList( $i=false ) {
					if( $i == false ) {
						return false;
					} else {
						unset( $this->optionVal['lists'][$i] );
						update_option( 'imported_lists' , $this->optionVal['lists'] );
						return update_option( YKSEME_OPTION , $this->optionVal );
					}
				} // end deleteList();
				
			// Import a list to the lists page
			// Runs when a user adds a list from the drop down on the lists page	
			public function importList( $i = false ) {
					if( $i == false ) {
						return false;
					} else {
						// create our variables
						$lid	= $this->optionVal['lists'][$i]['list-id'];
						$name	= $this->optionVal['lists'][$i]['name'];
						$api	= new Mailchimp($this->optionVal['api-key']);
						$mv	= $api->call('lists/merge-vars', array(
								'id' => array( $lid )
							)
						);
						// if merge variables are returned
						$mv	= $this->getImportedFieldsArray($lid, $mv);
						if( $mv ) {
							// Save the new list
							$this->optionVal['lists'][$i]['fields']	= $mv;
							if( update_option( YKSEME_OPTION , $this->optionVal ) ) {
								return $this->generateListContainers(array($this->optionVal['lists'][$i]));
							}
						}		
					}
					return false;
				} // end importList();
		
			// reImport a list to the lists page
			// Runs when a user adds a list from the drop down on the lists page	
			public function reImportMergeVariables( $i = false ) {
					if( $i == false ) {
						return false;
					} else {
						// create our variables
						$lid	= $this->optionVal['lists'][$i]['list-id'];
						$name	= $this->optionVal['lists'][$i]['name'];
						$api	= new Mailchimp($this->optionVal['api-key']);
						$mv	= $api->call('lists/merge-vars', array(
								'id' => array( $lid )
							)
						);
						// if merge variables are returned
						$mv	= $this->getImportedFieldsArray($lid, $mv);
						if( $mv ) {
							// Save the new list
							$this->optionVal['lists'][$i]['fields']	= $mv;
							// update the list with the new fields
							 if( update_option( YKSEME_OPTION , $this->optionVal ) ) {
								return $this->generateMergeVariableContainers(array($this->optionVal['lists'][$i]));
							}
						}	
					}
					return false;
				} // end reImportMergeVariables();

			// Make a call to the MailChimp API to retrieve all subscribers to a given list
			// Runs when the user clicks 'view' next to the subscriber count on the list page
			public function listAllSubscribers( $lid, $list_name ) {
					$api	= new Mailchimp($this->optionVal['api-key']);
					$subscribers_list	= $api->call('lists/members', 
						array(
							'id'	=>	$lid,
							'opts'	=>	array(				
								'limit'	=>	'100',
								'sort_field'	=>	'optin_time',
								'sort_dir'	=>	'DESC'
							)	
						)	
					);
					// if the subscriber count is greater than 0
					// display all subscribers in a table
					if( $subscribers_list['total'] > 0 ) { ?>
						<h2><?php echo $list_name; echo '   <span class="subscriber-count" style="font-size:11px;">(<span class="number">'.$subscribers_list['total'].'</span> '.__(" subscribers" , "yikes-inc-easy-mailchimp-extender").')</span>'; ?></h2>
						<p><?php _e( 'Click on a subscriber to see further information' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
							
						<table id="yikes-mailchimp-subscribers-table" class="yks-mailchimp-fields-list" style="width:100%;">
							<thead class="yikes-mailchimp-subscribers-table-head">
								<tr>
									<th width="50%"><?php _e( 'E-Mail' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th width="50%"><?php _e( 'Date Subscribed' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
								</tr>
							</thead>
						<?php foreach ( $subscribers_list['data'] as $subscriber  ) {
								$timeStamp = explode(' ', $subscriber['timestamp_opt'] );
								echo '<tr class="yks-mailchimp-subscribers-list-row" id="yks-mailchimp-subscribers-list-row">';
									echo '<td><a class="subscriber-mail-link" rel="'.$subscriber["email"].'" class="subscriberEmailAddress">'.$subscriber['email'].'</a></td>';
									// echo '<td>'.str_replace('-', ' ', date("M-jS-Y", strtotime($subscriber['timestamp_opt']))).'</td>';
									echo '<td>'.str_replace('-', ' ', date("M-jS-Y", strtotime($timeStamp[0]))).'</td></tr>';
							} ?>
						</table>

						<!-- display a single user profile in this div -->
						<div id="individual_subscriber_information" style="display:none;"></div>
						<?php
					} else { // else display an error of sorts
						?>
						<h2><?php echo $list_name; echo '   <span class="subscriber-count" style="font-size:11px;">(<span class="number">0</span> '.__(" subscribers" , "yikes-inc-easy-mailchimp-extender").')</span>'; ?></h2>
						<?php
						_e( "Sorry You Don't Currently Have Any Subscribers In This List!" , "yikes-inc-easy-mailchimp-extender" );
					}
					wp_die();
				} // end listAllSubscribers();

			// Make a call to the MailChimp API to retrieve information about a specific user
			// Runs when the user clicks a subscribers email address
			public function getSubscriberInfo($lid, $email) {
					$api = new Mailchimp($this->optionVal['api-key']);
					$subscriber_info = $api->call('lists/member-info', 
						array(
							'id' => $lid,
							'emails'	=> array(
								0 => array(
									'email' => $email,
								),
							)	
						)	
					);
					// if the subscriber count is greater than 0
					// display all subscribers in a table
					if($subscriber_info) {
						// store user data into variables
						$subscriber_data = $subscriber_info['data'][0];
						$member_rating = $subscriber_data['member_rating'];
						// firstname/lastname data inside of new array
						$subscriber_data_merges = $subscriber_data['merges'];
						// seperate date+time
						$subscriber_data_info_changed = explode(' ' , $subscriber_data['info_changed']);
						$subscriber_data_timestamp_opt = explode (' ', $subscriber_data['timestamp_opt']);
						// store date+time in seperate variables			
						$subscriber_data_info_changed_date = $subscriber_data_info_changed[0];
						$subscriber_data_info_changed_time = $subscriber_data_info_changed[1];
						// store optin-time+date in seperate variables
						$subscriber_data_info_optin_date = $subscriber_data_timestamp_opt[0];
						$subscriber_data_info_optin_time = $subscriber_data_timestamp_opt[1];	
						// create our language variable dependent on what is set in MailChimp
						include_once('set_language.php');
							
						// create star rating variable, based on returned member_rating value
						if(isset($member_rating)) {
							if ($member_rating == 1) {
								$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
							} else if ($member_rating == 2) {
								$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
							} else if ($member_rating == 3) {
								$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
							} else if ($member_rating == 4) {
								$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
							} else if ($member_rating == 5) {  
								$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span></div>';
							}
						} ?>	
						
						<div class="yks-mc-subscriber-top">
							<span class="button-primary dashicons dashicons-arrow-left-alt2 yks-mc-subscriber-go-back"><?php _e( 'Back to Subscriber List' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
								<h2 class="yks-mc-subscriber-header"><?php _e( 'Subscriber Details' , 'yikes-inc-easy-mailchimp-extender' ); ?> </h2>
								<!-- display users email address -->
								<h3 id="yks-subscriber-info-email-address"><?php echo $subscriber_data['email']; ?></h3>
								<!-- display member star rating pulled from MailChimp -->
								<?php echo $member_rating_stars; ?>
								<!-- user optin time and date -->
								<span class="yks-subscriber-info-optin-data">
									<?php echo 'Subscribed on : '.date('m/d/Y',strtotime($subscriber_data_info_optin_date)); ?><?php echo ' at '.date('g:i A',strtotime($subscriber_data_info_optin_time)); ?>
								</span>
						</div>
							
						<h2 class="yks-mc-subscriber-header"><?php _e( 'Overview' , 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
						<div class="yks-mc-subscriber-overview">		
							
							<div class="yks-mc-subscriber-left-container">		
										
								<label class="yks-mc-overview-title"><?php _e( 'First Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
								<p class="yks-mc-overview-info-value yks-mc-subscriber-firstName"><?php if(!empty($subscriber_data_merges['FNAME'])) { echo $subscriber_data_merges['FNAME']; } else { echo 'not provided'; } ?></p>	
										
								<label class="yks-mc-overview-title"><?php _e( 'Last Updated' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
								<p class="yks-mc-overview-info-value"><?php echo date('m/d/Y',strtotime($subscriber_data_info_changed_date)); ?><?php echo ' at '.date('g:i A',strtotime($subscriber_data_info_changed_time)); ?></p>						
										
								<label class="yks-mc-overview-title"><?php _e( 'Preferred Email Type' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
								<p class="yks-mc-overview-info-value"><?php if(!empty($subscriber_data['email_type'])) { echo $subscriber_data['email_type']; } else { echo 'No Preference.'; } ?></p>
										
							</div>

							<div class="yks-mc-subscriber-right-container">
									
								<label class="yks-mc-overview-title"><?php _e( 'Last Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
								<p class="yks-mc-overview-info-value yks-mc-subscriber-lastName"><?php if(!empty($subscriber_data_merges['LNAME'])) { echo $subscriber_data_merges['LNAME']; } else { echo 'not provided'; } ?></p>
													
								<label class="yks-mc-overview-title"><?php _e( 'Language' , 'yikes-inc-easy-mailchimp-extender' ); ?></label>
								<p class="yks-mc-overview-info-value"><?php echo $subscriber_data_language; ?></p>
																
							</div>
									
						</div>
						
						<?php
							// build our segment array to determine if the user is subscribed to any segments
							$segment_count = array();
							// check if groupings data is set (for segment and interest groups)
							// this avoids an error being thrown when no segment/interest groups have been selected
							if ( isset( $subscriber_data_merges['GROUPINGS'] ) ) {
								foreach ($subscriber_data_merges['GROUPINGS'] as $group1) {
									foreach ($group1['groups'] as $group2) {
										if ($group2['interested'] == 1) {
											array_push($segment_count, $group2['name']);
										}
									}	
								}	
							}
						?>
								
						<h2 class="yks-mc-subscriber-header"><?php _e( 'Groups Subscribed To' , 'yikes-inc-easy-mailchimp-extender' ); ?></h2>	
						<!-- display what groups/segments the subscriber is subscribed too -->
							<?php		
								// if the user is subscribed to some segment
								if(!empty($segment_count)) {
									$groups_array = array(
										'group_name'	=>	'',
										'segments'	=>	array()
									);
									$num = 0;
									foreach ($subscriber_data_merges['GROUPINGS'] as $group_name) {	
										$groups_array[$num]['group_name'] = $group_name['name'];
											foreach ($group_name['groups'] as $groupData ) {
												if ($groupData['interested'] == 1) {
													$groups_array[$num]['segments'] = $groupData['name'];
												}
											}
										$num++;
									}					
									// build up the segments array that the user is subscribed too					
									foreach ( $groups_array as $group_data ) {
										if(!empty($group_data['segments'])) { ?>
										<ul>						
										<?php
											echo '<li style="font-size:16px; color:#333;">'.$group_data['group_name'].'</li>';
												if(!empty($group_data['group_name']) && isset($group_data['segments'])) {
													echo 'Segments : '; echo implode(' ,', array($group_data['segments']));
												} elseif (count($group_data['segments']) == 0) {
													echo 'n/a';
												}
										?>
										</ul>
										<?php		
										}
									}	
								} else {
									// if there are no segments subscribed too
									// just print a message none specified
									echo 'none specified';
								}	
						
							// display the notes associated with a user if there are any returned
							if(!empty($subscriber_data['notes'])) { ?>
								<div class="yks-mc-subscriber-bottom">
									<h2 class="yks-mc-subscriber-header"><?php _e( 'Subscriber Notes' , 'yikes-inc-easy-mailchimp-extender' ); ?></h2>
										<?php
												foreach ( $subscriber_data['notes'] as $note ) {
												?>
													<textarea style="width:100%; height:100px; resize:none;" id="yks-mc-subscriber-notes" disabled><?php echo $note['note']; ?></textarea>
													<span style="font-size:11px; opacity:.75;"><?php _e( 'Written by' , 'yikes-inc-easy-mailchimp-extender' ); ?>: <?php echo $note['created_by_name']; ?> // <?php _e( 'Created on' , 'yikes-inc-easy-mailchimp-extender' ); ?>: <?php echo date('m/d/Y',strtotime($note['created'])); ?></span>
												<?php
											}	
											?>
								</div>
							<?php 
							}
					} 	
					wp_die();
				} // end getSubscriberInfo();

			// Make a call to the MailChimp API to remove a specified user from a given list
			// Runs when the user clicks the 'X' next to a subscriber when viewing all subscribers on the lists page
			public function yks_removeSubscriber($lid, $user_email) {
					$api	= new Mailchimp($this->optionVal['api-key']);
					$subscribers_list	= $api->call('lists/unsubscribe', array(
						'id'	=>	$lid,
						'email'	=>	array(	
							'email'	=>	$user_email
						)
					));
				} // end yks_removeSubscriber();

			/***** SCRIPTS/STYLES
			 ****************************************************************************************************/
			
			// enqueue dashboard styles
			public function addStyles() {
					include YKSEME_PATH . 'lib/inc/yks-mc-dashboard-styles.php';				
				} // end addStyles();
		
			// enqueue dashboard scripts
			public function addScripts() {		
					include YKSEME_PATH . 'lib/inc/yks-mc-dashboard-scripts.php';
				} // end addScripts();
		
			// enqueue our styles on the front end
			public function addStyles_frontend() {
					include YKSEME_PATH . 'lib/inc/yks-mc-frontend-styles.php';			
				} // end addStyles_frontend();
		
			// enqueue front end scripts
			public function addScripts_frontend() {	
				// enqueue jQuery datepicker only when the user has specified to do so
					if( isset( $this->optionVal['yks-mailchimp-jquery-datepicker'] ) && $this->optionVal['yks-mailchimp-jquery-datepicker']	== '1' ) {
						wp_enqueue_script('jquery-ui-datepicker');
					}
				} // addScripts_frontend();
				
			// redirect the user to the settings page on initial activation
			function yks_easy_mc_plugin_activation_redirect() {
					if (get_option('yks_easy_mc_plugin_do_activation_redirect', false)) {
						delete_option('yks_easy_mc_plugin_do_activation_redirect');
						// redirect to settings page
						wp_redirect(admin_url('/admin.php?page=yks-mailchimp-welcome'));
					}
				} // end yks_easy_mc_plugin_activation_redirect();
		

			/***** SHORTCODES
			 ****************************************************************************************************/
			 // Function to process the shortcode provided by the plugin
			 // $p is the data associated with the shortcode (ie: form id and submit button text)
			public function processShortcode( $p ) {
					ob_start();
					// check if the list is set, to prevent errors from being thrown
					if ( isset( $this->optionVal['lists'][$p['id']] ) ) {
					
						if( $this->optionVal['api-key'] != '' && ( is_array( $this->optionVal['lists'][$p['id']] ) && !empty( $this->optionVal['lists'][$p['id']] ) ) ) {
							// Setup this list
							$list		= $this->optionVal['lists'][$p['id']];
							$submit_text =  $p['submit_text'];
							// Which form are we on?
							if( !isset( $this->currentLists[$p['id']] ) ) {
								$this->currentLists[$p['id']]	= 0;
							} else {
								$this->currentLists[$p['id']]++;
							}
							$listCt = $this->currentLists[$p['id']];
							// Add the count to the list vars
							$list['id']	= $listCt.'-'.$list['id'];
							if( count( $list['fields'] ) ) {
								foreach( $list['fields'] as $fieldId => $field ) {
									$list['fields'][$fieldId]['id']	= $listCt.'-'.$field['id'];
								}
							}	
							// List form
							include YKSEME_PATH.'templates/shortcode_form.php';
						}	
								
						// if there is no api key entered, or it's an invalid api key
					} else if ( $this->optionVal['api-key'] == '' || get_option( 'api_validation' ) == 'invalid_api_key' ) {
						include YKSEME_PATH.'templates/shortcode_error_no_API_key.php';
						// else if the form was removed from the lists page
					} else {
						include YKSEME_PATH.'templates/shortcode_error_data.php';
					}
					$shortcode = ob_get_contents();
					ob_end_clean();
					return $shortcode;
				} // end processShortcode();
		
			/* New Function to Display Subscriber Count for a given list */
			public function displaySubscriberCount( $p ) {
					ob_start();
					if( $this->optionVal['api-key'] != '' && ( is_array( $this->optionVal['lists'][$p['id']] ) && !empty( $this->optionVal['lists'][$p['id']] ) ) ) {
						// Setup this list
						$list = $this->optionVal['lists'][$p['id']];
						$mailChimp = new yksemeBase();
						$list = $mailChimp->getListsData();
						$list_id = key($list);
						// return just the subscriber count so the user can easily customize it!
						echo $list['subscriber-count']['subscriber-count-'.$list_id];
					} else if ( $this->optionVal['api-key'] == '' || get_option( 'api_validation' ) == 'invalid_api_key' ) {
						include YKSEME_PATH.'templates/shortcode_error_no_API_key.php';
						// else if the form was removed from the lists page
					} else {
						include YKSEME_PATH.'templates/shortcode_error_data.php';
					}
					$shortcode = ob_get_contents();
					ob_end_clean();
					return $shortcode;
				} // end displaySubscriberCount();
		
			// Function to process the PHP snippet provided by the plugin
			// Again the data passed in, is the shortcode/php snippet paramaters (form id, submit button text)	
			public function processSnippet($list=false, $submit_text) {
					$p = array(
							'id' => $list,
							'submit_text'	=> $submit_text
						);
					return $this->processShortcode($p);
				} // end processSnippet();


			/***** ADMINISTRATION MENUS => Continue Editing Downward!
			 ****************************************************************************************************/
			public function addAdministrationMenu() {
					// Top Level Menu
					add_menu_page( __('MailChimp Forms','yikes-inc-easy-mailchimp-extender'), 'MailChimp Forms', apply_filters( 'yks_mailchimp_user_role' , 'manage_options' ), 'yks-mailchimp-form', array(&$this, 'generatePageOptions'), 'dashicons-welcome-write-blog', 400);
					// Sub Items
					add_submenu_page('yks-mailchimp-form', __('MailChimp Forms','yikes-inc-easy-mailchimp-extender'), __('MailChimp Settings','yikes-inc-easy-mailchimp-extender'), apply_filters( 'yks_mailchimp_user_role' , 'manage_options' ), 'yks-mailchimp-form', array(&$this, 'generatePageOptions'));
					// if the user has entered a VALID API key
					if ( get_option('api_validation') == 'valid_api_key') {
						add_submenu_page('yks-mailchimp-form', __('My MailChimp','yikes-inc-easy-mailchimp-extender'), __('My MailChimp','yikes-inc-easy-mailchimp-extender'), apply_filters( 'yks_mailchimp_user_role' , 'manage_options' ), 'yks-mailchimp-my-mailchimp', array(&$this, 'generateUserMailChimpPage'));
					}
					add_submenu_page('yks-mailchimp-form', __('Manage List Forms','yikes-inc-easy-mailchimp-extender'), __('Manage List Forms','yikes-inc-easy-mailchimp-extender'), apply_filters( 'yks_mailchimp_user_role' , 'manage_options' ), 'yks-mailchimp-form-lists', array(&$this, 'generatePageLists'));
					add_submenu_page('yks-mailchimp-form', __('About YIKES, Inc.','yikes-inc-easy-mailchimp-extender'), __('About YIKES, Inc.','yikes-inc-easy-mailchimp-extender'), apply_filters( 'yks_mailchimp_user_role' , 'manage_options' ), 'yks-mailchimp-about-yikes', array(&$this, 'generatePageAboutYikes'));
					add_submenu_page('options.php', __('Welcome','yikes-inc-easy-mailchimp-extender'), __('Welcome','yikes-inc-easy-mailchimp-extender'), apply_filters( 'yks_mailchimp_user_role' , 'manage_options' ), 'yks-mailchimp-welcome', array(&$this, 'generateWelcomePage'));
				} // end addAdministrationMenu();


			/***** ADMINISTRATION PAGES
			 ****************************************************************************************************/
			public function generatePageOptions() {
					require_once YKSEME_PATH.'pages/options.php'; // include our options page
				} // end generatePageOptions();
				
			public function generatePageLists() {
					require_once YKSEME_PATH.'pages/lists.php'; // include our lists page
				} // end generatePageLists();
				
			public function generatePageAboutYikes() {
					require_once YKSEME_PATH.'pages/about.php'; // include our about page
				} // end generatePageAboutYikes();
				
			public function registerMailChimpWidget() {	
					require_once YKSEME_PATH.'templates/yikes-mailchimp-widget.php'; // include our widget
				} // end registerMailChimpWidget();
				
			public function includeRECAPTCHAlib() {	
					require_once YKSEME_PATH.'lib/recaptchalib.php'; // include our widget
				} // end includeRECAPTCHAlib();
				
			public function generateUserMailChimpPage() {	
					require_once YKSEME_PATH.'pages/myMailChimp.php'; // include our widget
				} // end generateUserMailChimpPage();		
				
			public function generateWelcomePage() {	
					require_once YKSEME_PATH.'pages/welcome.php'; // include our widget
				} // end generateWelcomePage();		

			/***** FORM DATA
			 ****************************************************************************************************/
			public function yks_resetPluginSettings() {
					$blog_title = get_bloginfo( 'name' );
					// reset the plugin settings back to defaults
					$this->optionVal['api-key']	= '';
					$this->optionVal['flavor']	= '1';
					$this->optionVal['debug']	= '0';
					$this->optionVal['optin']	= 'true';
					$this->optionVal['single-optin-message']	= __('Thank You for subscribing!', 'yikes-inc-easy-mailchimp-extender');
					$this->optionVal['double-optin-message']	= __('Thank You for subscribing! Check your email for the confirmation message.', 'yikes-inc-easy-mailchimp-extender');
					$this->optionVal['optIn-checkbox']	= 'hide';
					$this->optionVal['yks-mailchimp-optIn-default-list']	= 'select_list';
					$this->optionVal['yks-mailchimp-optin-checkbox-text']	= 'Add me to the ' . $blog_title . ' mailing list';
					$this->optionVal['recaptcha-setting']	= '0';
					$this->optionVal['recaptcha-api-key']	= '';
					$this->optionVal['recaptcha-private-api-key']	= '';
					$this->optionVal['yks-mailchimp-jquery-datepicker']	= '';
					$this->optionVal['yks-mailchimp-required-text']	= '';
					$this->optionVal['version'] = YKSEME_VERSION_CURRENT;
					$this->optionVal['ssl_verify_peer'] = 'true';
					update_option('api_validation' , 'invalid_api_key');
					// we need to unset the previously set up widgets
					// and set up new erros if the API key doesn't exist 
							
					// 1 - empty the lists array of imported lists
					$this->optionVal['lists'] = array();
					// 2 - unset our previously set up widgets
					update_option( 'widget_yikes_mc_widget' , '' );
					// update our options	
					return update_option( YKSEME_OPTION, $this->optionVal );	
				} // end yks_resetPluginSettings();
	 
			// Make a call to MailChimp API to validate the provided API key - send request to helper/ping, returns a boolean
			public function validateAPIkeySettings() {		
				
					// right now we just check the length of the API key being passed in
					// mailchimp api keys are around 30-40 characters
					// we check if the string length is greater than 45...
					if ( strlen($_POST['api_key']) > 45 ) {
						// Create and store our variables to pass to MailChimp
						$apiKey = $this->yikes_mc_decryptIt($_POST['api_key']); // api key
						$apiKey_explosion = explode( "-" , $apiKey);
						$dataCenter = $apiKey_explosion[0]; // data center (ie: us3)	
						$api	= new Mailchimp($apiKey);
						// try the call, catch any errors that may be thrown
						try {
							$resp = $api->call('helper/ping', array('apikey' => $apiKey));
							echo $resp['msg'];
							$this->getOptionsLists();
						} catch( Exception $e ) {
							$errorMessage = str_replace('API call to helper/ping failed:', '', $e->getMessage());
							echo $errorMessage;
							// log our error message for display back to the user
							$this->writeErrorToErrorLog( $e );
						}
						return $apiKey;
						// always die or it will always return 1
						wp_die();
					} else {
						$apiKey = $this->yikes_mc_encryptIt($_POST['api_key']);
						// Create and store our variables to pass to MailChimp
						$decryped_api_key = $this->yikes_mc_decryptIt($apiKey); // api key
						$apiKey_explosion = explode( "-" , $decryped_api_key);
						$dataCenter = $apiKey_explosion[0]; // data center (ie: us3)	
						$api	= new Mailchimp($decryped_api_key);
						// try the call, catch any errors that may be thrown
						try {
							$resp = $api->call('helper/ping', array('apikey' => $decryped_api_key));
							echo $resp['msg'];
						} catch( Exception $e ) {
							$errorMessage = str_replace('API call to helper/ping failed:', '', $e->getMessage());
							echo $errorMessage;
							// log our error message for display back to the user
							$this->writeErrorToErrorLog( $e );
						}
						// always die or it will always return 1
						return $apiKey;
						wp_die();
					}
				} // end validateAPIkeySettings();
	 
			// Make a call to MailChimp API to get the current users PROFILE
			public function getUserProfileDetails() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					// try the call, catch any errors that may be thrown
					try {
						$profile_response = $api->call('users/profile', array('apikey' => $apiKey));
						include_once YKSEME_PATH.'templates/mailChimp-profile-template.php';
					} catch( Exception $e ) {
						$errorMessage = str_replace('API call to helper/ping failed:', '', $e->getMessage());
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
					}

					// try the call, catch any errors that may be thrown
					try {
						$account_details = $api->call('helper/account-details', array('apikey' => $apiKey));
						include_once YKSEME_PATH.'templates/mailChimp-account-overview.php';
					} catch( Exception $e ) {
						$errorMessage = str_replace('API call to helper/ping failed:', '', $e->getMessage());
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
					}
					// always die or it will always return 1
					wp_die();
				} // end getUserProfileDetails();
	 
			/*
				Send Update Profile Email
				@since v5.2
			*/
			public function sendUpdateProfileEmail( $user_email , $list_id ) {
				$api = new Mailchimp($this->optionVal['api-key']);
				$explode_key = explode( '-' , $this->optionVal['api-key'] );
				$data_center = $explode_key[1];
				$full_site_url = get_bloginfo('url');
				$extracted_url = str_replace( 'https://' , '' , str_replace( 'www.' , '' , str_replace( 'http://' , '' , $full_site_url ) ) );
				$site_name = str_replace( ' ' , '' , get_bloginfo('name') ) ;
					try {
						// get the site URL
						// not sure this needs its own API call
						$account_details = $api->call( 'helper/account-details', array('apikey' => $this->optionVal['api-key'] ) );
						// get the list details (default from name, default from email)
						$list_details = $api->call( 'lists/list', 
							array(
								'apikey' => $this->optionVal['api-key'],
								'filters' => array(
									'list_id' => $list_id
								)
							) 
						);
						// get the subscribers info
						$subscriber_account_details = $api->call('lists/member-info', 
							array(
								'id'	=>	$list_id,
								'emails'	=>	array(
									0 => array(
											  'email' => $user_email,
										  ),
								)	
							)	
						);
						$subscriber_id = $subscriber_account_details['data'][0]['id'];
						$explode_url = explode( '.' , $account_details['contact']['url'] );
						$subject = 'MailChimp Profile Update';
						$headers = 'From: ' . $list_details['data'][0]['default_from_name'] . ' <' . $list_details['data'][0]['default_from_email'] . '>' . "\r\n";
						$headers .= 'Content-type: text/html';
						$email_content = '<p>Dear user,</p> <p>A request has been made to update your account information. To do so use the following link: <a href="http://' . $explode_url[1] . '.' . $data_center . '.list-manage1.com/profile?u=' . $account_details['user_id'] . '&id=' . $list_id .'&e=' . $subscriber_id . '" title="Update MailChimp Profile">Update Profile Info.</a>';
						$email_content .= "<p>If you didn't request this update, please disregard this email.</p>";
						$email_content .= '<p>&nbsp;</p>';
						$email_content .= '<p>This email was sent from : ' . $full_site_url . '</p>'; 
						$email_content .= '<p>&nbsp;</p>'; 
						$email_content .= '<p>&nbsp;</p>';
						$email_content .= '<p style="font-size:13px;margin-top:5em;float:right;"><em>this email was generated by the <a href="http://www.wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/" target="_blank">YIKES Inc. Easy MailChimp Extender</a> plugin, created by <a href="http://www.yikesinc.com" target="_blank">YIKES Inc.</a></em></p>';
						if ( wp_mail( $user_email, $subject, $email_content, $headers ) ) {
							echo '<span class="preloader-confirmation-box success">' . __( 'Update email successfully sent. Please check your inbox for the message.' , 'yikes-inc-easy-mailchimp-extender' ) . '</span>';
						} else {
							echo '<span class="preloader-confirmation-box error">' . __( 'Email failed to send. Please contact the site administrator.' , 'yikes-inc-easy-mailchimp-extender' ) . '</span>';
						} 
						// print_r($account_details);	
					} catch( Exception $e ) {
						$errorMessage = '<span class="error">' . __( 'Error sending update profile email. Please contact the site administrator.' , 'yikes-inc-easy-mailchimp-extender' ) . '</span>';
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
					}
				} // end sendUpdateProfileEmail();
	 

			// Make a call to MailChimp API to validate the provided API key - api request to helper/chimp-chatter, returns Account Activity
			public function getMailChimpChatter() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $this->optionVal['api-key']; // api key
					$api	= new Mailchimp($apiKey);
					// try the call, catch any errors that may be thrown
					try {
						$resp = $api->call('helper/chimp-chatter', array('apikey' => $apiKey));
						include_once YKSEME_PATH.'templates/mailChimpChatter-template.php'; 
					} catch( Exception $e ) {
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}

					}
					// always die or it will always return 1
					wp_die();
				} // end getMailChimpChatter();
	 

			// Make a call to MailChimp API to validate the provided API key
			// calls the helper/chimp-chatter method, and returns Account Activity 
			public function getMailChimpChatterForWidget() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $this->optionVal['api-key']; // api key
					$api	= new Mailchimp($apiKey);
					// try the call, catch any errors that may be thrown
					try {
						$resp = $api->call('helper/chimp-chatter', array('apikey' => $apiKey));
						include_once YKSEME_PATH.'templates/mailChimpChatter-widget-template.php'; 
					} catch( Exception $e ) {
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}

					}
					// always die or it will always return 1
					wp_die();
				} // end getMailChimpChatterForWidget();
	 
	 
			// Make a call to MailChimp API to lists/growth history
			public function getListGrowthHistory() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					if ( isset($_POST['list_id']) ) { $listID = $_POST['list_id']; } else { $listID = NULL; }
					$api	= new Mailchimp($apiKey);
					// try the call, catch any errors that may be thrown
					try {
						$resp = $api->call('lists/growth-history', array( 'apikey' => $apiKey , 'id' => $listID ));
						// include our Stats Template
						include_once YKSEME_PATH.'templates/mailChimp-list-growth-template.php'; 
						// Working File
						 // date is returned out of order tho...
						 // include_once YKSEME_PATH.'templates/mailChimp-list-growth-template.php'; 
					} catch( Exception $e ) {
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}

					}
					// always die or it will always return 1
					wp_die();
				} // end getListGrowthHistory();
	  
			
			// Make a call to MailChimp API to 
			// get a specified all campaigns or specified list campaign data
			// used for both overall aggregate stats AND single list stats
			public function getCapmpaignData() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					// try the call, catch any errors that may be thrown
					try {
						$resp = $api->call('campaigns/list', array( 'apikey' => $apiKey , 'limit' => 1000 ));
						// include our Stats Template
						include_once YKSEME_PATH.'templates/mailChimp-campaign-stats-template.php'; 
					} catch( Exception $e ) {
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}

					}
					// always die or it will always return 1
					wp_die();
				} // end getCapmpaignData();
				

			// Make a call to MailChimp API to 
			// To get our piechart for the link stats page
			public function getPieChart() {		
					// Create and store our variables to pass to MailChimp
					$pie_chart_link_data_array = $_POST['pie_chart_data_array']; // link data
					// include our Stats Template
					include YKSEME_PATH.'templates/mailChimp-campaign-clicks-pie-chart.php'; 
					
					// always die or it will always return 1
					wp_die();
				}
	 
			// Make a call to MailChimp API to 
			// get a specified all campaigns or specified list campaign data
			// used for both overall aggregate stats AND single list stats
			public function getSpecificCapmpaignData() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					$campaign_id = $_POST['campaign_id'];
					$campaign_title = $_POST['campaign_title'];
					$campaign_email_subject = $_POST['campaign_subject'];
					$campaign_send_date = $_POST['campaign_send_date'];
					$campaign_send_time = $_POST['campaign_send_time'];
					$campaign_view_email_link = $_POST['campaign_view_email_link'];
					$campaign_web_Id = $_POST['campaign_web_Id'];
					// try the call, catch any errors that may be thrown
					try {
						$resp = $api->call('reports/summary', array( 'apikey' => $apiKey , 'cid' => $campaign_id ));
						// include our Stats Template
						include_once YKSEME_PATH.'templates/mailChimp-campaign-report.php'; 
						// print_r($resp);
					} catch( Exception $e ) {
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
					}
					// always die or it will always return 1
					wp_die();
				} // end getSpecificCapmpaignData();
	 
			// Send a call to MailChimp API to get the email recipients of a specific campaign
			public function getCampaignEmailToTable() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					$campaign_id = $_POST['campaign_id'];
					// try the call, catch any errors that may be thrown
					try {
						$resp = $api->call('reports/sent-to', array( 'apikey' => $apiKey , 'cid' => $campaign_id , 'opts' => array('limit' => '100') ));
						// include our Stats Template
						// include_once YKSEME_PATH.'templates/mailChimp-campaign-report.php'; 
						// print_r($resp);
						if ( $resp ) {
							?>
							<script type="text/javascript">
								jQuery(document).ready(function() {
									 jQuery('#yikes-mailchimp-emailed-subscribers').dataTable();
								});
							</script>
							
							<style>
								.subscriber_rating_star { font-size:15px; }
							</style>
							
							<table id="yikes-mailchimp-emailed-subscribers" class="yks-mailchimp-fields-list" style="width:100%;">
								<thead class="yikes-mailchimp-subscribers-table-head">
									<tr>
										<th style="width:20%;"><?php _e( 'E-Mail' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th style="width:19%;"><?php _e( 'First Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th style="width:18%;"><?php _e( 'Last Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th style="width:25%;"><?php _e( 'Member Rating' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
										<th style="width:18%;"><?php _e( 'Last Changed' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									</tr>
								</thead>
							<?php				
							foreach ( $resp['data'] as $recipient ) {	
							
								// create the name variable, dependant on first and last name being stored
								if ( isset ( $recipient['member']['merges']['FNAME'] ) && $recipient['member']['merges']['FNAME'] != '' ) { 
									$user_first_name = $recipient['member']['merges']['FNAME']; 
								} else {
									$user_first_name = '';
								}
								if ( isset ( $recipient['member']['merges']['LNAME'] ) && $recipient['member']['merges']['LNAME'] != '' ) { 
									$user_last_name = $recipient['member']['merges']['LNAME']; 
								} else {
									$user_last_name = '';
								}
								
								$user_email = $recipient['member']['email'];
								$user_id = $recipient['member']['id'];
								$email_type = $recipient['member']['email_type'];
								$user_rating = $recipient['member']['member_rating'];
								$user_last_changed = $recipient['member']['info_changed'];
									$exploded_change = explode( ' ' , $user_last_changed);
									$user_last_changed_date = date( 'm/d/Y' , strtotime( $exploded_change[0] ) );	
									$user_last_changed_time = date( 'g:i a' , strtotime( $exploded_change[1] ) );
									
									if(isset($user_rating)) {
										if ($user_rating == 1) {
											$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
										} else if ($user_rating == 2) {
											$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
										} else if ($user_rating == 3) {
											$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
										} else if ($user_rating == 4) {
											$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
										} else if ($user_rating == 5) {  
											$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span></div>';
										}
									}
								
								// $timeStamp = explode(' ', $subscriber['timestamp_opt'] );
								echo '<tr class="yks-mailchimp-subscribers-list-row" id="yks-mailchimp-subscribers-list-row">';
									echo '<td><a class="subscriber-mail-link" rel="'.$user_email.'" class="subscriberEmailAddress">'.$user_email.'</a></td>';
									echo '<td>'.ucfirst(strtolower($user_first_name)).'</td>';
									echo '<td>'.ucfirst(strtolower($user_last_name)).'</td>';
									echo '<td><span style="display:none;">'.$user_rating.'</span>'.$member_rating_stars.'</td>';
									echo '<td>'.$user_last_changed_date . ' at ' . $user_last_changed_time .'</td>';
								echo '</tr>';
								
							}
						?>
							</table>	
						<?php
						}
					} catch( Exception $e ) {
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}

					}
					// always die or it will always return 1
					wp_die();
				} // end getCampaignEmailToTable();
	 
	 
			// Send a call to MailChimp API to get the geo location of users who opened links
			public function getGeoDataForCampaignOpenLinks() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					$campaign_id = $_POST['campaign_id'];
					// try the call, catch any errors that may be thrown
					try {
						$resp = $api->call('reports/geo-opens', array( 'apikey' => $apiKey , 'cid' => $campaign_id ) );
						// include our Stats Template
						// include_once YKSEME_PATH.'templates/mailChimp-campaign-report.php'; 
						// print_r($resp);
						if ( $resp ) {
							include_once YKSEME_PATH.'templates/mailChimp-campaign-click-geo-map.php';
						}
					} catch( Exception $e ) {
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						?>
						<script>
							jQuery(document).ready(function() {
								setTimeout(function() {
									var mailChimpError = '<?php echo "<strong class=yks-mc-report-error>".$e->getMessage()."</strong>"; ?>';
									jQuery('#open_tracking_disabled_warning').fadeIn();
									jQuery('#geo_map_no_data').append('<p class="geo_map_no_data_error">'+mailChimpError+'</p>');
								}, 600);
							});
						</script>

						<section class="overview_information_section">
				
							<div class="overview_information">

								<h2>Campaign Activity Geo Map</h2>
								
									<div id="geo_map_no_data" style="max-width: 100%;min-width: 100%; background:#fff;">
										<div id="geo_map_no_data_overlay"></div>
										<img src="<?php echo plugins_url( '/../images/highcharts-worldmap-disabled.png' , __FILE__ ); ?>" alt="World Map Disabled" title="World Map Disabled">
									</div>
							</div>
						
						</section>
						
						<?php
					}
					// always die or it will always return 1
					wp_die();
				} // end getGeoDataForCampaignOpenLinks();
	 
	 
			// Make a call to MailChimp API to 
			// get link tracking information for a specified campaign
			// used in the world map on the campaign stats page
			public function getCampaignLinkStats() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					$campaign_id = $_POST['campaign_id'];
					// try the call, catch any errors that may be thrown
					try {
						$campaign_click_stats = $api->call( '/reports/clicks' , array( 'apikey' => $apiKey , 'cid' => $campaign_id ) );
						// include our Stats Template
						include_once YKSEME_PATH.'templates/mailChimp-campaign-click-report.php';
					} catch( Exception $e ) {
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
					}
					// always die or it will always return 1
					wp_die();
				} // end getCampaignLinkStats();
	 
	 
			// Make a call to MailChimp API to 
			// get users who opened a specific campaign 
			// used in the stats page modal
			public function getCampaignOpenedData() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					$campaign_id = $_POST['campaign_id'];
					// try the call, catch any errors that may be thrown
					try {
						// create our interactive data table
						// and initialize it here...
					
						$campaign_open_stats = $api->call( '/reports/opened' , array( 'apikey' => $apiKey , 'cid' => $campaign_id , 'opts' => array( 'sort_field' => 'opens' , 'sort_dir' => 'DESC' ) ) );
						
						// create an empty array
						$total_opens_array = array();
						// build our total opens array
						foreach ( $campaign_open_stats['data'] as $opened_data ) {			
							$total_opens_array[] = $opened_data['opens'];
						}
						
						// display total unique opens
						echo '<h2 style="float:left;">' . __( 'Unique Opens' , 'yikes-inc-easy-mailchimp-extender' ) . ' - ' . $campaign_open_stats["total"] . '</h2>';
						// display total opens
						echo '<h2 style="float:right;">' . __( 'Total Opens' , 'yikes-inc-easy-mailchimp-extender' ) . ' - ' . array_sum($total_opens_array) . '</h2>';
									
						// echo '<h2>' . __( 'Total Opens' 'yikes-inc-easy-mailchimp-extender' ) . $campaign_open_stats["total"] . '</h2>';
						
						?>
						<!-- initialize o ur data table -->
						<script type="text/javascript">
							jQuery(document).ready(function() {
								 jQuery('#yikes-mailchimp-subscribers-opens').dataTable({
									 "aaSorting": [[ 1, "desc" ]]
								 });
							});
						</script>
							
						<style>
							.subscriber_rating_star { font-size:15px; }
						</style>
						
						<!-- build our opened user table -->
						<table id="yikes-mailchimp-subscribers-opens" class="yks-mailchimp-fields-list" style="width:100%;">
							<thead class="yikes-mailchimp-subscribers-table-head">
								<tr>
									<th style="width:31%;"><?php _e( 'E-Mail' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:14%;"><?php _e( 'Opens' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:20%;"><?php _e( 'First Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:20%;"><?php _e( 'Last Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:15%;"><?php _e( 'Member Rating' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
								</tr>
							</thead>
						<?php
						// loop over to build create and store our user variables returned by mailchimp
						foreach ( $campaign_open_stats['data'] as $opened_data ) {
						
							// checking if FNAME is set as a merge variable
							// if not, we'll just make it an empty string
							if ( isset ( $opened_data['member']['merges']['LNAME'] ) ) {
								$opened_data['member']['merges']['LNAME'] = $opened_data['member']['merges']['LNAME'];
							} else {
								$opened_data['member']['merges']['LNAME'] = '';
							}
							
							// checking if LNAME is set as a merge variable
							// if not, we'll just make it an empty string
							if ( isset ( $opened_data['member']['merges']['FNAME'] ) ) {
								$opened_data['member']['merges']['FNAME'] = $opened_data['member']['merges']['FNAME'];
							} else {
								$opened_data['member']['merges']['FNAME'] = '';
							}
							
						
							if(isset($opened_data['member']['member_rating'])) {
								if ($opened_data['member']['member_rating'] == 1) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 2) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 3) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 4) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 5) {  
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span></div>';
								}
							}
							// build our table inside the loop
							echo '<tr>';
								echo '<td><a href="mailto:'.$opened_data['member']['email'].'">'.$opened_data['member']['email'].'</a></td>';
								echo '<td>'.$opened_data['opens'].'</td>';
								echo '<td>'.ucfirst(strtolower($opened_data['member']['merges']['FNAME'])).'</td>';
								echo '<td>'.ucfirst(strtolower($opened_data['member']['merges']['LNAME'])).'</td>';
								echo '<td><span style="display:none;">'.$opened_data['member']['member_rating'].'</span>'.$member_rating_stars.'</td>';
							echo '</tr>';
						}
						
					} catch( Exception $e ) {
						// if there is some error, lets return it
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
					}
					// always die or it will always return 1
					wp_die();
				} // end getCampaignOpenedData();
	 
	 
			// Make a call to MailChimp API to 
			// get bounced email addressed for this campaign
			// used in the stats page modal
			public function getCampaignBouncedEmailData() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					$campaign_id = $_POST['campaign_id'];
					// try the call, catch any errors that may be thrown
					try {
						// create our interactive data table
						// and initialize it here...
					
						$campaign_bounce_stats = $api->call( '/reports/bounce-messages' , array( 'apikey' => $apiKey , 'cid' => $campaign_id , 'opts' => array( 'limit' => '100' ) ) );
						
						// print_r($campaign_bounce_stats);
						
						// display total unique opens
						echo '<h2 style="float:left;">' . __( 'Total Bounced Emails' , 'yikes-inc-easy-mailchimp-extender' ) . ' - ' . $campaign_bounce_stats["total"] . '</h2>';
									
						// echo '<h2>' . __( 'Total Opens' 'yikes-inc-easy-mailchimp-extender' ) . $campaign_open_stats["total"] . '</h2>';
						
						?>
						<!-- initialize o ur data table -->
						<script type="text/javascript">
							jQuery(document).ready(function() {
								 jQuery('#yikes-mailchimp-subscribers-bounced').dataTable({
									 "aaSorting": [[ 1, "desc" ]]
								 });
							});
						</script>
							
						<style>
							.subscriber_rating_star { font-size:15px; }
						</style>
						
						<!-- build our opened user table -->
						<table id="yikes-mailchimp-subscribers-bounced" class="yks-mailchimp-fields-list" style="width:100%;">
							<thead class="yikes-mailchimp-subscribers-table-head">
								<tr>
									<th style="width:31%;"><?php _e( 'E-Mail' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:20%;"><?php _e( 'First Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:20%;"><?php _e( 'Last Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:15%;"><?php _e( 'Member Rating' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
								</tr>
							</thead>
						<?php
						// loop over to build create and store our user variables
						// returned by mailchimp
						foreach ( $campaign_bounce_stats['data'] as $opened_data ) {
						
							// checking if FNAME is set as a merge variable
							// if not, we'll just make it an empty string
							if ( isset ( $opened_data['member']['merges']['LNAME'] ) ) {
								$opened_data['member']['merges']['LNAME'] = $opened_data['member']['merges']['LNAME'];
							} else {
								$opened_data['member']['merges']['LNAME'] = '';
							}
							
							// checking if LNAME is set as a merge variable
							// if not, we'll just make it an empty string
							if ( isset ( $opened_data['member']['merges']['FNAME'] ) ) {
								$opened_data['member']['merges']['FNAME'] = $opened_data['member']['merges']['FNAME'];
							} else {
								$opened_data['member']['merges']['FNAME'] = '';
							}
							
						
							if(isset($opened_data['member']['member_rating'])) {
								if ($opened_data['member']['member_rating'] == 1) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 2) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 3) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 4) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 5) {  
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span></div>';
								}
							}
							// build our table inside the loop
							echo '<tr>';
								echo '<td><a href="mailto:'.$opened_data['member']['email'].'">'.$opened_data['member']['email'].'</a></td>';
								echo '<td>'.ucfirst(strtolower($opened_data['member']['merges']['FNAME'])).'</td>';
								echo '<td>'.ucfirst(strtolower($opened_data['member']['merges']['LNAME'])).'</td>';
								echo '<td><span style="display:none;">'.$opened_data['member']['member_rating'].'</span>'.$member_rating_stars.'</td>';
							echo '</tr>';
						}
						
						// print_r($campaign_open_stats);
						
					} catch( Exception $e ) {
						// if there is some error, lets return it
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
					}
					// always die or it will always return 1
					wp_die();
				} // end getCampaignBouncedEmailData(); 
	 
	 
			// Make a call to MailChimp API to 
			// get unsubscribed email addressed for this campaign
			// used in the stats page modal
			public function getCampaignUnsubscribeData() {		
					// Create and store our variables to pass to MailChimp
					$apiKey = $_POST['api_key']; // api key
					$api	= new Mailchimp($apiKey);
					$campaign_id = $_POST['campaign_id'];
					// try the call, catch any errors that may be thrown
					try {
						// create our interactive data table
						// and initialize it here...
					
						$campaign_unsubscribe_stats = $api->call( '/reports/unsubscribes' , array( 'apikey' => $apiKey , 'cid' => $campaign_id , 'opts' => array( 'limit' => '100' ) ) );
						
						// print_r($campaign_bounce_stats);
						
						// display total unique opens
						echo '<h2 style="float:left;">' . __( 'Total Unsubscribes' , 'yikes-inc-easy-mailchimp-extender' ) . ' - ' . $campaign_unsubscribe_stats["total"] . '</h2>';
									
						// echo '<h2>' . __( 'Total Opens' 'yikes-inc-easy-mailchimp-extender' ) . $campaign_open_stats["total"] . '</h2>';
						
						?>
						<!-- initialize o ur data table -->
						<script type="text/javascript">
							jQuery(document).ready(function() {
								 jQuery('#yikes-mailchimp-unsubscribe-table').dataTable({
									 "aaSorting": [[ 0, "desc" ]]
								 });
							});
						</script>
							
						<style>
							.subscriber_rating_star { font-size:15px; }
						</style>
						
						<!-- build our opened user table -->
						<table id="yikes-mailchimp-unsubscribe-table" class="yks-mailchimp-fields-list" style="width:100%;">
							<thead class="yikes-mailchimp-subscribers-table-head">
								<tr>
									<th style="width:31%;"><?php _e( 'E-Mail' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:20%;"><?php _e( 'First Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:20%;"><?php _e( 'Last Name' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:15%;"><?php _e( 'Member Rating' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
									<th style="width:15%;"><?php _e( 'Reason' , 'yikes-inc-easy-mailchimp-extender' ); ?></th>
								</tr>
							</thead>
						<?php
						// loop over to build create and store our user variables
						// returned by mailchimp
						foreach ( $campaign_unsubscribe_stats['data'] as $opened_data ) {
						
							// checking if FNAME is set as a merge variable
							// if not, we'll just make it an empty string
							if ( isset ( $opened_data['member']['merges']['LNAME'] ) ) {
								$opened_data['member']['merges']['LNAME'] = $opened_data['member']['merges']['LNAME'];
							} else {
								$opened_data['member']['merges']['LNAME'] = '';
							}
							
							// checking if LNAME is set as a merge variable
							// if not, we'll just make it an empty string
							if ( isset ( $opened_data['member']['merges']['FNAME'] ) ) {
								$opened_data['member']['merges']['FNAME'] = $opened_data['member']['merges']['FNAME'];
							} else {
								$opened_data['member']['merges']['FNAME'] = '';
							}
							
						
							if(isset($opened_data['member']['member_rating'])) {
								if ($opened_data['member']['member_rating'] == 1) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 2) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 3) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 4) {
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-empty"></span></div>';
								} else if ($opened_data['member']['member_rating'] == 5) {  
									$member_rating_stars = '<div class="yks-mc-subscriber-rating"><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span><span class="subscriber_rating_star dashicons dashicons-star-filled"></span></div>';
								}
							}
							// build our table inside the loop
							echo '<tr>';
								echo '<td><a href="mailto:'.$opened_data['member']['email'].'">'.$opened_data['member']['email'].'</a></td>';
								echo '<td>'.ucfirst(strtolower($opened_data['member']['merges']['FNAME'])).'</td>';
								echo '<td>'.ucfirst(strtolower($opened_data['member']['merges']['LNAME'])).'</td>';
								echo '<td><span style="display:none;">'.$opened_data['member']['member_rating'].'</span>'.$member_rating_stars.'</td>';
								echo '<td>'.strtolower($opened_data['reason']).'</td>';
							echo '</tr>';
						}
						
					} catch( Exception $e ) {
						// if there is some error, lets return it
						echo '<strong>'.$e->getMessage().'</strong>';
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
							$this->writeErrorToErrorLog( $e );
						}
					}
					// always die or it will always return 1
					wp_die();
				} // end getCampaignUnsubscribeData();
	 
	 
			// Make a call to MailChimp API to add a new subscriber to a specified list
			// Runs when a user fills out the form on the frontend of the site 
			public function addUserToMailchimp( $p , $update_existing ) {
				
				if( !empty( $p['form_data'] ) ) {
					
					parse_str($p['form_data'], $fd);
					
					// grab and store our nonce field
					// for security purposes
					$yks_mc_form_submission_nonce = $fd['_wpnonce'];

					// cross check our nonce
						// passing in the action used when we created the nonce field
						// if the nonce does not match, we need to die()
					if ( !wp_verify_nonce( $yks_mc_form_submission_nonce , 'yks_mc_front_end_form_'.$fd['yks-mailchimp-list-id'] ) ) { 	
						die( __( 'Failed nonce security check. Please reload the page and submit this form again.' , 'yikes-inc-easy-mailchimp-extender' ) );		
					} 	

							
					if( !empty( $fd['yks-mailchimp-list-id'] ) ) {
						
						// if reCAPTCHA is enabled
						if ( $this->optionVal['recaptcha-setting'] == '1' ) {
								
							if ( isset( $fd['g-recaptcha-response'] ) && $fd['g-recaptcha-response'] == '' ) {
								die( __( 'Please check off that you are not a robot.' , 'yikes-inc-easy-mailchimp-extender' ) );
							}
						
							$privatekey = $this->optionVal['recaptcha-private-api-key'];
							$response = $fd['g-recaptcha-response'];
							  
							  // if the CAPTCHA was entered properly
							  if ( $fd['g-recaptcha-response'] == '' ) {
									// if the response returns invalid,
									// lets add the animated shake and error fields
									// to the captcha fields
									?>
									<script>
										jQuery(document).ready(function() {
											jQuery('.g-recaptcha').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
												jQuery(this).removeClass('shake animated');
												next();
											});
										});
									</script>
									<?php

									// if it returns valid...
									// continue sending data to MailChimp
								} else {
								
									// Create and store the variables needed to add a new subscriber
									$email	= false;
									$lid			= $fd['yks-mailchimp-list-id'];
									$api		= new Mailchimp($this->optionVal['api-key']);
									$mv 		= array();
									$optin	= $this->optionVal['optin'];

									foreach($this->optionVal['lists'][$lid]['fields'] as $field) : if($field['active'] == '1') :
										
										// Check to see if the field is in the post
										if(isset($fd[$field['name']])) :
											
											// Grab the primary email
											if(strtolower($field['merge']) == 'email')
												$email	= $fd[$field['name']];
											
											// It exists, set the merge var that we send to mailchimp
											switch($field['type'])
												{
												default:
													$mv[$field['merge']]	= $fd[$field['name']];
													break;
													
												case 'address':
													$mv[$field['merge']]	= array(
																		'addr1'		=> $fd[$field['name']],
																		'addr2'		=> $fd[$field['name'].'-add2'],
																		'city'		=> $fd[$field['name'].'-city'],
																		'state'		=> $fd[$field['name'].'-state'],
																		'zip'			=> $fd[$field['name'].'-zip'],
																		'country'	=> 'US'
																	);
													break;
																									
												}
											
										
														
										endif;
									endif; endforeach;
								
								// Interest group loop to build the GROUPINGS array	
								// The GROUPINGS array passes our interest group, and values back to the specific form
								if ( isset($fd['interest-group-data']) ) {
									$interest_group_option = json_decode($fd['interest-group-data'], true);
								}
									// if interest groups exist, continue and form an array
									if ( isset($interest_group_option) ) {
									
										// print_r($interest_group_option);
										$mv['GROUPINGS'] = array();
										
										// loop over each interest group
										foreach ($interest_group_option as $group ) :
												
												switch($group['form_field'])
													{
														case 'radio':
														case 'dropdown':
															array_push($mv['GROUPINGS'], array(
																	'id'	=>	$group['id'],
																	// 'groups'	=>	array($fd['interest-group'])
																	'groups'	=>	array(isset($fd[$group['form_field'].'-'.$group['id']]) ? $fd[$group['form_field'].'-'.$group['id']] : '')
																)	
															);
														break;
														
														case 'checkboxes':
															array_push($mv['GROUPINGS'], array(
																	'id'	=>	$group['id'],
																	// 'groups'	=>	array($fd['interest-group'])
																	'groups'	=>	(isset($fd[$group['form_field'].'-'.$group['id']]) ? $fd[$group['form_field'].'-'.$group['id']] : '')
																	// $fd[$group['form_field'].'-'.$group['id']]
																	
																)	
															);
														break;
													}	
										endforeach; // end loop
										
									}
											
									// If no email provided, fail
									$noemail = "The email address is blank";
									if($email === false) return $noemail;
										
										// Add custom filter here, to capture user submitted 
										// data before it's sent off to MailChimp
										$form_data = apply_filters( 'yikes_mc_get_form_data' , $mv ); 
										
										$form_data = apply_filters( 'yikes_mc_get_form_data_'.$lid, $mv ); 
										
										// setup our welcome email variable
										if( isset( $this->optionVal['lists'][$lid]['fields'][$lid.'-email']['yks_mailchimp_send_welcome_'.$lid] ) && $this->optionVal['lists'][$lid]['fields'][$lid.'-email']['yks_mailchimp_send_welcome_'.$lid] == '1' ) {
											$welcome = false;
										} else {
											$welcome = true;
										}
										
										// try adding subscriber, catch any error thrown
										try {
											$retval = $api->call('lists/subscribe', array(
												  'id'              => $lid, // form id
												  'email'             => array( 'email' => $email ), // user email
												  'merge_vars'        => $form_data, // merge variables (ie: fields and interest groups)
												  'double_optin'	=> $optin, // double optin value (retreived from the settings page)
												  'send_welcome' => $welcome,
												  'update_existing' => $update_existing
											));
											return "done";
										} catch( Exception $e ) { // catch any errors returned from MailChimp
											$errorCode = $e->getCode();
											if ( $errorCode = '214' ) {
												$errorMessage = $e->getMessage();
												return json_encode( array( 'errorCode' => $errorCode , 'errorResponse' => apply_filters( 'yikes_mc_user_already_subscribed' , $errorMessage , $email ) ) );
												die();
											} else { 
												echo '<strong>'.$e->getMessage().'</strong>';
												die();
											}
										}
							 }
							 
						// if reCAPTCHA is not active, we will just send the request	
						// without further verification
						} else {
								
							// Create and store the variables needed to add a new subscriber
							$email	= false;
							$lid			= $fd['yks-mailchimp-list-id'];
							$api		= new Mailchimp($this->optionVal['api-key']);
							$mv 		= array();
							$optin	= $this->optionVal['optin'];

						foreach($this->optionVal['lists'][$lid]['fields'] as $field) : if($field['active'] == '1') :
							
							// Check to see if the field is in the post
							if(isset($fd[$field['name']])) :
								
								// Grab the primary email
								if(strtolower($field['merge']) == 'email')
									$email	= $fd[$field['name']];
								
								// It exists, set the merge var that we send to mailchimp
								switch($field['type'])
									{
									default:
										$mv[$field['merge']]	= $fd[$field['name']];
										break;
										
									case 'address':
										$mv[$field['merge']]	= array(
															'addr1'		=> $fd[$field['name']],
															'addr2'		=> $fd[$field['name'].'-add2'],
															'city'		=> $fd[$field['name'].'-city'],
															'state'		=> $fd[$field['name'].'-state'],
															'zip'			=> $fd[$field['name'].'-zip'],
															'country'	=> 'US'
														);
										break;
																						
									}
								
							
											
							endif;
						endif; endforeach;
						
						// Interest group loop to build the GROUPINGS array	
						// The GROUPINGS array passes our interest group, and values back to the specific form
						if ( isset($fd['interest-group-data']) ) {
							$interest_group_option = json_decode($fd['interest-group-data'], true);
						}
							// if interest groups exist, continue and form an array
							if ( isset($interest_group_option) ) {
							
								// print_r($interest_group_option);
								$mv['GROUPINGS'] = array();
								
								// loop over each interest group
								foreach ($interest_group_option as $group ) :
										
										switch($group['form_field'])
											{
												case 'radio':
												case 'dropdown':
													array_push($mv['GROUPINGS'], array(
															'id'	=>	$group['id'],
															// 'groups'	=>	array($fd['interest-group'])
															'groups'	=>	array(isset($fd[$group['form_field'].'-'.$group['id']]) ? $fd[$group['form_field'].'-'.$group['id']] : '')
														)	
													);
												break;
												
												case 'checkboxes':
													array_push($mv['GROUPINGS'], array(
															'id'	=>	$group['id'],
															// 'groups'	=>	array($fd['interest-group'])
															'groups'	=>	(isset($fd[$group['form_field'].'-'.$group['id']]) ? $fd[$group['form_field'].'-'.$group['id']] : '')
															// $fd[$group['form_field'].'-'.$group['id']]
															
														)	
													);
												break;
											}	
								endforeach; // end loop
								
							}
									
							// If no email provided, fail
							$noemail = "The email address is blank";
							if($email === false) return $noemail;
								
								// Add custom filter here, to capture user submitted 
								// data before it's sent off to MailChimp
								$form_data = apply_filters( 'yikes_mc_get_form_data' , $mv ); 
								$specific_form_data = apply_filters( 'yikes_mc_get_form_data_'.$lid, $lid, $mv ); 
								
								// setup our welcome email variable
								if( isset( $this->optionVal['lists'][$lid]['fields'][$lid.'-email']['yks_mailchimp_send_welcome_'.$lid] ) && $this->optionVal['lists'][$lid]['fields'][$lid.'-email']['yks_mailchimp_send_welcome_'.$lid] == '1' ) {
									$welcome = false;
								} else {
									$welcome = true;
								}
								
								// try adding subscriber, catch any error thrown
								try {
									$retval = $api->call('lists/subscribe', array(
										  'id'              => $lid, // form id
										  'email'             => array( 'email' => $email ), // user email
										  'merge_vars'        => $form_data, // merge variables (ie: fields and interest groups)
										  'double_optin'	=> $optin, // double optin value (retreived from the settings page)
										  'send_welcome' => $welcome,
										  'update_existing' => $update_existing
									));
									return "done";
								} catch( Exception $e ) { // catch any errors returned from MailChimp
									$errorCode = $e->getCode();
										if ( $errorCode = '214' ) {
											$errorMessage = $e->getMessage();
											return json_encode( array( 'errorCode' => $errorCode , 'errorResponse' => apply_filters( 'yikes_mc_user_already_subscribed' , $errorMessage , $email ) ) );
											die();
										} else { 
											echo '<strong>'.$e->getMessage().'</strong>';
											die();
										}
								}
							}

						}
					} else {
						return __('One or more fields are empty','yikes-inc-easy-mailchimp-extender'); // return an error if your leaving any necessary fields empty
					}
				} // end addUserToMailchimp();
		
		
			// Generate the lists containers on the lists page
			// This function gets any imported lists, and builds up the lists page	
			public function generateListContainers($listArr=false) {
					$listArr	= ($listArr == false ? $this->optionVal['lists'] : $listArr);
					$thelistdata = $this->getListsData(); //Get list names from API
					// if there are any imported lists in the array	
					if( count( $listArr ) > 0) {
						include YKSEME_PATH . 'lib/inc/yks-mc-manage-list-form-table.php';
					}
					$output = ob_get_contents();
					ob_end_clean();
					return $output;
				} // end generateListContainers();
		
		
			// Generate the Merge Variable containers on the lists page
			// we use this function to re-import merge variables from mailchimp
			public function generateMergeVariableContainers($listArr=false) {
					$listArr	= ($listArr == false ? $this->optionVal['lists'] : $listArr);
					$thelistdata = $this->getListsData(); //Get list names from API
					// if there are any imported lists in the array	
					if(count($listArr) > 0) {
						ob_start();
						// loop over each lists and build the page
						$i = 1;
						foreach($listArr as $list) {
							$get_list_data = $this->getListsData();
							?>
								<td class="yks-mailchimp-fields-td" id="yks-mailchimp-fields-td_<?php echo $list['id']; ?>">
									<fieldset class="yks-mailchimp-fields-container" id="yks-mailchimp-fields-container_<?php echo $list['id']; ?>">
										<legend class="screen-reader-text"><span><?php _e( 'Active Fields' , 'yikes-inc-easy-mailchimp-extender' ); ?></span></legend>
											<div class="yks-mailchimp-fields-list" id="yks-mailchimp-fields-list_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
											<!-- create sortable rows populated with mailchimp data -->
												<?php 
													$num = 1;
													foreach($list['fields'] as $field) { ?>
														<div class="yks-mailchimp-fields-list-row" alt="<?php echo $field['merge']; ?>" rel="<?php echo $field['id']; ?>">
															<!-- update labels on initial creation as well! to do-->
															<label title="Delete <?php echo $field['label']; ?> Field">
																<span class="yks-mc-delete-merge-var"><span class="dashicons dashicons-no-alt"></span></span>
															</label>
															<label title="Edit <?php echo $field['label']; ?> Field">
																<span class="yks-mc-merge-var-change"><span class="dashicons dashicons-edit"></span></span>
															</label>
															<label title="Reorder <?php echo $field['label']; ?>">
																<span class="yks-mailchimp-sorthandle"><?php _e( 'Drag' , 'yikes-inc-easy-mailchimp-extender' ); ?> &amp; <?php _e( 'drop' , 'yikes-inc-easy-mailchimp-extender' ); ?></span>
															</label>
															<label title="Toggle Visibility of <?php echo $field['label']; ?>">
																<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo ($field['active'] == 1 ? 'checked="checked"' : ''); ?><?php echo ($field['require'] == 1 ? 'disabled="disabled"' : ''); ?> />
															</label>	
																&nbsp;
															<label>
																<div class="yks-mailchimp-field-name"><?php echo $field['label']; ?></div>
															</label>
															
															<!-- <span class="yks-mailchimp-field-merge"><span class="description"><?php _e( 'Merge field' , 'yikes-inc-easy-mailchimp-extender' ); ?>:</span> &nbsp; <input type="text" class="merge_field_input" name="<?php echo $field['name']; ?>-merge" id="<?php echo $field['id']; ?>-merge" value="<?php echo $field['merge']; ?>"<?php echo (($field['locked'] == 1 || $field['merge'] == false) ? ' disabled="disabled"' : ''); ?> /></span> -->
															<span class="yks-mailchimp-field-placeholder"><span class="description"><?php _e( 'Placeholder' , 'yikes-inc-easy-mailchimp-extender' ); ?>:</span> &nbsp; <input type="text" class="custom-placeholder-field" name="placeholder-<?php echo $list['id'].'-'.$num; ?>" id="<?php echo $field['id']; ?>-placeholder" placeholder="<?php echo $field['label']; ?>" value="<?php if(isset($field['placeholder-'.$list['id'].'-'.$num])) { echo $field['placeholder-'.$list['id'].'-'.$num]; } ?>" /></span>
															<span class="yks-mailchimp-field-custom-field-class"><span class="description"><?php _e( 'Custom Class' , 'yikes-inc-easy-mailchimp-extender' ); ?>:</span> &nbsp; <input type="text" name="custom-field-class-<?php echo $list['id'].'-'.$num; ?>" id="<?php echo $field['id']; ?>-custom-field-class" value="<?php if(isset($field['custom-field-class-'.$list['id'].'-'.$num])) { echo $field['custom-field-class-'.$list['id'].'-'.$num]; } ?>" /></span>
														</div>
														<?php 
															$num++;
														} ?>
											</div>
									</fieldset>
								</td>
							<?php
								$i++;
							}
						}
					$output = ob_get_contents();
					ob_end_clean();
					return $output;
				} // end generateMergeVariableContainers();

			// Get list data
			public function getListDataRightMeow() {
					echo json_encode( $this->optionVal['lists'] );
				} // end getListDataRightMeow();

			// Generate our front end JavaScript , used to submit forms	
			public function getFrontendFormJavascript($list='') {
				if($list === '') return false;
					$js	= false;
					foreach($list['fields'] as $field) : if($field['active'] == 1) :	
						// Setup JavaScript
						if($field['require'] == '1') :
						$prefix = "ymce";
							$js .= "\n";
							switch($field['type']) {
								
								// default
								default:
								$prefixa = "ymce";
									$js .= "if ($".$prefixa."('#".$field['id']."').val() == '')";
									$js .= "{
												msg += '<li>".$field['label']."'+'\\n</li>';
												err++;
												$".$prefixa."('#".$field['id']."').addClass('yks_error_field_required shake animated').delay(1200).queue(function(next){
												$".$prefixa."(this).removeClass('shake animated');
												next();
											});
										} else {
											$".$prefixa."('#".$field['id']."').removeClass('yks_error_field_required');
										}";
										break;
										
									// address
									case 'address':					
										$js .= "if($".$prefix."('#".$field['id']."').val() == '') {
											msg += '<li>Street Address'+'\\n</li>';
											err++;
											$".$prefixa."('#".$field['id']."').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
												$".$prefixa."(this).removeClass('shake animated');
												next();
											});
										} else {
											$".$prefixa."('#".$field['id']."').removeClass('yks_error_field_required')
										}	
										
										if($".$prefix."('#".$field['id']."-city').val() == '') {
											msg += '<li>City'+'\\n</li>';
											err++;
											$".$prefixa."('#".$field['id']."-city').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
												$".$prefixa."(this).removeClass('shake animated');
												next();
											});
										} else {
											$".$prefixa."('#".$field['id']."-city').removeClass('yks_error_field_required')
										}
										if($".$prefix."('#".$field['id']."-state').val() == '') {
											msg += '<li>State'+'\\n</li>';
											err++;
											$".$prefixa."('#".$field['id']."-state').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
												$".$prefixa."(this).removeClass('shake animated');
												next();
											});
										} else {
											$".$prefixa."('#".$field['id']."-state').removeClass('yks_error_field_required')
										}
										if($".$prefix."('#".$field['id']."-zip').val() == '') {
											msg += '<li>Zip Code'+'\\n</li>';
											err++;
											$".$prefixa."('#".$field['id']."-zip').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
												$".$prefixa."(this).removeClass('shake animated');
												next();
											});
										} else {
											$".$prefixa."('#".$field['id']."-zip').removeClass('yks_error_field_required')
										}";
										break;
										
										// radio	
										case 'radio':
											$js .= "if($".$prefix."('.".$field['name'].":checked').length <= 0) { 
													msg += '<li>".$field['label']."\\n</li>';
													err++;
													$".$prefixa."('label[for=".$field['id']."]').next().find('input').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
														$".$prefixa."(this).removeClass('shake animated');
														next();
													});
												} else {
													$".$prefixa."('#".$field['id']."').removeClass('yks_error_field_required')
												}";
											break;
							}
							
						$js .= "\n";
						endif;
						endif; endforeach;
						return $js;			
				} // end getFrontendFormJavascript();
				
			// Generate the form on the front end of the site
			// this is what the user will see, and interact with	
			public function getFrontendFormDisplay($list='', $submit_text) {
					if($list === '') return false;
					ob_start();	
					include YKSEME_PATH . 'lib/inc/yks-mc-frontend-form-display.php';
					$output = ob_get_contents();
					ob_end_clean();
					return $output;
				}

			// Generate the input fields for the form on the front end of the site	
			// based on the $field['type'] that is returned from MailChimp
			private function getFrontendFormDisplay_field( $field = false , $num ) {
					if($field === false) return false;
					$o = '';
					$fieldID = $field['id'];
					$exploded_field_id = explode( '-' , $fieldID );
					$form_id = $exploded_field_id[1];
					// print_r($field);
					
					$class_title = 'yks-mc-input-' . sanitize_title( $field['label'] );
					
					// check to see if the custom class has been set up...
					if ( isset( $field['custom-field-class-'.$form_id.'-'.$num] ) ) {
						$custom_class =  $field['custom-field-class-'.$form_id.'-'.$num];
					} else {
						$custom_class = '';
					}
					
					// check to see if the placeholder value has been stored
					// setup the placeholder field
					if ( isset( $field['placeholder-'.$form_id.'-'.$num] ) ) {
						$placeholder = $field['placeholder-'.$form_id.'-'.$num];
					} else {
						$placeholder = '';
					}
					
					switch( $field['type'] ) {
							default:
							case 'email':
							case 'number':
							case 'zipcode':
							case 'phone':
							case 'website':
							case 'imageurl':
							// custom placeholder value goes here
								$o	.= '<input type="text" name="'.$field['name'].'" placeholder="'.$placeholder.'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '') . ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'" value="" />';
								break;
								
							case 'text':				
								if ( $field['default'] ) {
									
									$custom_tag_array = apply_filters( 'yikes_mailchimp_default_value_tag' , $custom_tag_array=array() );
								
									/* 
										Lets Setup The Default Merge Variables Here 
										example : {post_title} , {post_id} , {page_url} , {user_logged_in}, {blog_name}
									*/
									global $post;
										
										switch ( $field['default'] ) {
											
											case '{post_title}' :
												$field['default'] = get_the_title( $post->ID );
												break;
												
											case '{post_id}' :
												$field['default'] = $post->ID;
												break;

											case '{page_url}' :
												$field['default'] = get_bloginfo( 'url' ) . $_SERVER['REQUEST_URI'];
												break;

											case '{blog_name}' :
												$field['default'] = get_bloginfo( 'name' );
												break;

											case '{user_logged_in}' :
												if ( is_user_logged_in() ) {
													$field['default'] = 'Registered User'; 
												} else {
													$field['default'] = 'Guest';
												}
												break;

											case in_array( $field['default'] , $custom_tag_array ) :
												$field['default'] = apply_filters( 'yikes_mailchimp_process_default_value_tag' , $field );
												break;	
											
											default:
												$field['default'] = $field['default'];
												break;
												
										}
									
								}	
								$o	.= '<input type="text" placeholder="'.$placeholder.'" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '') . ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'" value="'.$field['default'].'" />';
								break;
								
							case 'dropdown':
								$o	.= '<select name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'">';
									if(count($field['choices']) > 0) : foreach($field['choices'] as $ok => $ov) :
											$o	.= '<option value="'.htmlentities($ov, ENT_QUOTES).'">'.$ov.'</option>';
									endforeach; endif;
								$o	.= '</select>';
								break;
								
							case 'address':
								$o	.= '<input type="text" placeholder="'.$placeholder.'" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'" value="" /><span class="yks-mailchimp-form-tooltip">Street Address</span>';
								$o	.= '<input type="text" name="'.$field['name'].'-add2" class="'.$field['name'].'-add2'.($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'-add2" value="" /><span class="yks-mailchimp-form-tooltip">Apt/Suite</span>';
								$o	.= '<input type="text" name="'.$field['name'].'-city" class="'.$field['name'].'-city'.($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'-city" value="" /><span class="yks-mailchimp-form-tooltip">City</span>';
								$o	.= '<input type="text" name="'.$field['name'].'-state" class="'.$field['name'].'-state'.($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'-state" value="" /><span class="yks-mailchimp-form-tooltip">State</span>';
								$o	.= '<input type="text" name="'.$field['name'].'-zip" class="'.$field['name'].'-zip'.($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'-zip" value="" /><span class="yks-mailchimp-form-tooltip">Zip</span>';
								break;
								
							case 'radio':
								if(count($field['choices']) > 0) : $ct=0; foreach($field['choices'] as $ok => $ov) :
									$ct++;
									$o	.= '<label class="yks_mc_interest_group_label" for="'.$field['id'].'-'.$ok.'">
													<input type="radio" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .' yikes_mc_interest_group_checkbox" id="'.$field['id'].'-'.$ok.'" value="'.htmlentities($ov, ENT_QUOTES).'" />
													<span>'.$ov.'</span>
												</label>';;
								endforeach; endif;
								break;
								
							case 'date':
							case 'birthday':
								$o	.= '<input placeholder="'.$placeholder.'" type="text" name="'.$field['name'].'" class="'.$field['name'].' yks-field-type-date'.($field['require'] == 1 ? ' yks-require' : ''). ' ' . $custom_class . ' ' . $class_title .'" id="'.$field['id'].'" value="" />';
								break;
						
						}
						
						return $o;
				}


			/***** UPDATES
			 ****************************************************************************************************/
			public function runUpdateTasks() {
					$currentVersion	= (!isset($this->optionVal['version']) || empty($this->optionVal['version']) ? '5.0.3' : $this->optionVal['version']);
					$latestVersion	= YKSEME_VERSION_CURRENT;
					if($currentVersion < $latestVersion)
						{	
						$updateFunction	= 'runUpdateTasks_'.str_replace('.', '_', $currentVersion);
						if(!method_exists($this, $updateFunction)) return false;
						else
							{
							if(call_user_func(array(&$this, $updateFunction)))
								{
								update_option( YKSEME_OPTION, YKSEME_VERSION_CURRENT );
								$this->runUpdateTasks();
								}
							}
						}
					else return false;
				}
		
			/**
			 * This update makes the first name and last name optional
			 * To do this we need to loop through the existing fields and
			 * change the 'require' key to 0
			 *
			 * 1.1.0 => 1.2.0
			 */
			private function runUpdateTasks_1_1_0() {
					if($this->optionVal['lists']) {
						foreach($this->optionVal['lists'] as $lid => $list) {
							foreach($list['fields'] as $fid => $field) {
								switch($field['name']) {
									case 'yks-mailchimp-field-name-first':
									case 'yks-mailchimp-field-name-last':
										$this->optionVal['lists'][$lid]['fields'][$fid]['locked'] = 0;
										break;
									}
								}
							}
						}
					$this->optionVal['version']	= '1.2.0';
					return true;
				}

			/**
			 * This update adds a merge_vars key to each
			 * field array so that users can specify their
			 * own merge_var names
			 *
			 * 1.2.0 => 1.3.0
			 */
			private function runUpdateTasks_1_2_0() {
					if($this->optionVal['lists']) {
						foreach($this->optionVal['lists'] as $lid => $list) {
							$fct = 1;
							foreach($list['fields'] as $fid => $field) {
								switch($field['name']) {
									
									case 'yks-mailchimp-field-email':
										$this->optionVal['lists'][$lid]['fields'][$fid]['merge']	= 'EMAIL';
										break;
										
									case 'yks-mailchimp-field-apt-suite':
									case 'yks-mailchimp-field-city':
									case 'yks-mailchimp-field-state':
									case 'yks-mailchimp-field-zip':
										$this->optionVal['lists'][$lid]['fields'][$fid]['merge']	= false;
										break;
										
									default:
										if(empty($this->optionVal['lists'][$lid]['fields'][$fid]['merge']) || !isset($this->optionVal['lists'][$lid]['fields'][$fid]['merge']))
											$this->optionVal['lists'][$lid]['fields'][$fid]['merge']	= 'MERGE'.$fct;
										$fct++;
										break;
									}
								}
							}
						}
					$this->optionVal['version']	= '1.3.0';
					return true;
				}
		
					/**
			 * This update needs to pull in all of the custom form
			 * data for each of the lists, unfortunately it has to replace
			 * just about all of the data with the new schema. We also
			 * add in the flavor key (for table/div usage)
			 *
			 * 4.3 => 5.0.4
			 */
			private function runUpdateTasks_4_3() {
					if ( !isset( $this->optionVal['recaptcha-setting'] ) ) {
						$this->optionVal['recaptcha-setting'] = '0';
					}
					if ( !isset( $this->optionVal['recaptcha-api-key'] ) ) {
						$this->optionVal['recaptcha-api-key'] = '';
					}
					if ( !isset( $this->optionVal['recaptcha-private-api-key'] ) ) {
						$this->optionVal['recaptcha-private-api-key'] = '';
					}
					$this->optionVal['version']	= '5.0.4';
					return true;
				}

			/** Update/Add New Merge Variables and Interest Group Containers */
		
			// generate a container to allow for creating new merge variables (form fields)
			function generateNewMergeVariableContainer() {
					include YKSEME_PATH . 'lib/inc/yks-mc-new-merge-variable-container.php';
				}
			
			// generate a container to allow for editing merge variables
			function generateMergeVariableUpdateContainer() {	
					$delete_icon = '<span class="dashicons dashicons-no-alt remove-radio-dropdown-option"></span>';
					include YKSEME_PATH . 'lib/inc/yks-mc-update-merge-variable-container.php';
				}
			
			// generate a container to allow for creating new interest groups
			public function generateCreateInterestGroupContainer() {
					include YKSEME_PATH . 'lib/inc/yks-mc-create-interest-group-container.php';
				}
		
			// end merge variable and interest group containers
			
			// generate a thickbox container
			// to display a preview of the form
			function generateFormPreviewContainer() {
					?>
						<div id="formPreviewContainer" style="display:none;">
							 <?php echo '<img src="' . admin_url() . '/images/wpspin_light.gif" alt="preloader" style="margin-left: 50%; margin-top: 25%;">'; ?>
						</div>
					<?php
				}
		
			// generate a thickbox container
			// to display a how to in using custom template files
			function generateUserTemplateHowTo() {
					?>
						<div id="user_template_how_to" style="display:none;">
							<a href="http://www.yikesinc.com" title="YIKES, Inc." target="_blank"><img style="float:left;margin-bottom:0;width:75px;" src="<?php echo YKSEME_URL; ?>/images/yikes_logo.png" alt="YIKES, Inc." id="yksme-yikes-logo" /></a>
							 <h4 class="user_template_how_to_title"><?php _e( 'Custom User Template Files'  , 'yikes-inc-easy-mailchimp-extender' ); ?></h4>
							 
							 <p style="margin-top: 2.5em;" ><?php _e( 'With the latest version of YIKES Inc. Easy MailChimp Extender you can now extend the plugin beyond what it can do out of the box. Now you can create your own MailChimp sign up template files and use them with any list , anywhere on your site. We have provided you with a few bundled templates, as well as two boilerplate template files for easy customization.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
							 
							 <p><?php _e( 'You can create your own templates in two ways.' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
							 <hr />
							 <ul>
								<li><h4><?php _e( 'Automatic Method :'  , 'yikes-inc-easy-mailchimp-extender' ); ?></h4><p><?php _e( "The easiest way is to import the template files from the plugin automatically. You can do this by clicking on the 'import boilerplate files' button. This will copy over the necessary files right into your theme. From there you can start editing the template files found in your theme root inside of the newly created 'yikes-inc-easy-mailchimp-extender' directory." , "yikes-inc-easy-mailchimp-extender" ); ?></p></li>
								<a href="#" onclick="return false;" class="button-secondary import_template_boilerplates" style="width:148px;display:block;margin:0 auto;"><?php _e( "import boilerplate files" , "yikes-inc-easy-mailchimp-extender" ); ?></a>
								<hr />
								<li><h4><?php _e( 'Manual Method' , 'yikes-inc-easy-mailchimp-extender' ); ?> :</h4><p><?php _e( "If the automatic method doesn't work for you, you can manually copy over the necessary files." , 'yikes-inc-easy-mailchimp-extender' ); ?></p> <p><?php _e( "Copy the following directory" , "yikes-inc-easy-mailchimp-extender" ); ?> :<br /> <em class="yks-mc-file-path"><?php echo YKSEME_PATH . 'templates/yikes-mailchimp-user-templates'; ?></em> <br /><?php _e( " into your theme root, found at " , "yikes-inc-easy-mailchimp-extender" ); ?><br /> <em class="yks-mc-file-path"><?php echo get_stylesheet_directory_uri(); ?></em></p></li>
								<hr />
								<li><h5><?php _e( "Notes" , "yikes-inc-easy-mailchimp-extender" ); ?></h5></li>
									<ul>
										<li><p><?php _e( "You can also copy over any of the default bundled themes into the 'yikes-mailchimp-user-templates' directory to customize the look and feel of a default bundled template file." , "yikes-inc-easy-mailchimp-extender" ); ?></p></li>
										<li><p><?php _e( "If you are having any difficulties copying over the template files, or need help using them please open a support ticket on our" , "yikes-inc-easy-mailchimp-extender" ); ?> <a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues" target="_blank"><?php _e( "github issue tracker" , "yikes-inc-easy-mailchimp-extender" ); ?></a>.</p></li>
									</ul>
						</div>
					<?php
				}
			
			// generate a thickbox container
			// to display a preview of the form
			function getFormPreview($shortcode,$form_title,$form_bg_color,$form_text_color,$form_submit_button_color,$form_submit_button_text_color,$form_padding,$form_width,$form_alignment) {
					?>
					<h4 style="width:100%;text-align:center;">"<?php echo trim( $form_title ); ?>" Form Preview</h4>
					<div class="yks_mc_preview_form_container"><?php echo do_shortcode( stripslashes( $shortcode ) ); ?></div>
					<!-- override the styles for our preview container -->
					<style>
						.yks-mailchimpFormContainer {
							background: <?php echo $form_bg_color; ?> !important;
							padding: <?php echo $form_padding; ?> !important;
							color: <?php echo $form_text_color; ?> !important;
							width: <?php echo $form_width; ?> !important;
							<?php	
								if ( $form_alignment == 'left' ) {
									?>
										display: block;
										float: left;
									<?php
								} elseif ( $form_alignment == 'center' ) {
									?>
										display: block;
										margin: 0 auto;
									<?php
								} elseif ( $form_alignment == 'right' ) {
									?>
										display: block;
										float: right;
									<?php
								}
							?>
						}
						.yks-mailchimpFormDivRowLabel, .yks_mc_interest_group_label, .yks-mailchimp-form-tooltip {
							color: <?php echo $form_text_color; ?> !important;
						}
						.ykfmc-submit {
							background: <?php echo $form_submit_button_color; ?> !important;
							color: <?php echo $form_submit_button_text_color; ?> !important;
						}
						.yks_mc_interest_group_holder input[type=radio], .yks_mc_interest_group_holder input[type=checkbox] {
							margin:0 5px 0 0 !important;
						}
						body.mailchimp-forms_page_yks-mailchimp-form-lists form[name="yks-mailchimp-form"] {
							padding: 0 !important;
						}
					</style>
					<?php
				}
			
			// Get the selected form template screenshot
			function getTemplateScreenshot( $template_name , $template_screenshot , $template_path ) {
				
					$default_headers = array(
						'YIKES Inc. MailChimp Template',
						'YIKES Inc. MailChimp Template Author',
						'YIKES Inc. MailChimp Template Description'
					);
					// end pre-packaged templates
					$template_file_data = get_file_data( $template_path , $default_headers );				
					
					?>
					<div id="selected_template_preview_container">
						<span class="selected_template_preview_header">
							<h3 class="template_name"><?php echo $template_name; ?></h3>
							
							<p class="template_author"><strong><?php _e( "Author " , "yikes-inc-easy-mailchimp-extender" ); ?> :</strong> <em> <?php echo $template_file_data[1]; ?></em></p>
						</span>
						
						<p><strong><?php _e( "Description" , "yikes-inc-easy-mailchimp-extender" ); ?> :</strong> <?php echo $template_file_data[2]; ?></p>
						
						<hr />
						
						<img class="template_screenshot preview_template_screenshot" style="max-width:100%;" src="<?php echo YKSEME_URL . 'templates/yikes-mailchimp-bundled-templates/' . str_replace( ' ' , '_' , $template_name ) . '/' . $template_screenshot; ?>">
					</div>
					<?php
				}
			
			/**
			 * This update needs to pull in all of the custom form
			 * data for each of the lists, unfortunately it has to replace
			 * just about all of the data with the new schema. We also
			 * add in the flavor key (for table/div usage)
			 *
			 * 1.3.0 => 2.0.0
			 */
			private function runUpdateTasks_1_3_0() {
					$this->optionVal['flavor']	= '0';
					$this->optionVal['debug']	= '0';
					if( $this->optionVal['lists'] ) {
						foreach($this->optionVal['lists'] as $uid => $list) {
							unset($this->optionVal['lists'][$uid]);
							$this->addList($list['list-id']);
						}
					}
					$this->optionVal['version']	= '2.2.1';
					return true;
				}

			// Copy the user template file from within the plugin
			// into the users theme root
			public function copyUserTemplatesToUserTheme() {
					$src = YKSEME_PATH . 'templates/yikes-mailchimp-user-templates';
					$dst = get_stylesheet_directory() . '/yikes-mailchimp-user-templates';
					
					function recurse_copy($src,$dst) { 	
						$dir = opendir($src); 
						// mkdir( $dst . '/yiks-mailchimp-user-templates/' ); 
						mkdir( $dst );
						while(false !== ( $file = readdir($dir)) ) { 
							if (( $file != '.' ) && ( $file != '..' )) { 
								if ( is_dir($src . '/' . $file) ) { 
									recurse_copy($src . '/' . $file,$dst . '/' . $file); 
								} 
								else { 
									copy($src . '/' . $file,$dst . '/' . $file); 
								} 
							} 
						} 
						closedir($dir);
					}
					recurse_copy( $src , $dst );	
				}

			/* 
			generateRandomString();
			@since v5.2
			Generate a random string of text and numbers for merge variable creation 
			*/
			public function randomMergeVarString($length = 5) {
				$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, strlen($characters) - 1)];
				}
				echo $randomString;
			}

			/* 
			deleteInterestGroupFromList();
			@since v5.2
			Delete an interest group from a given list 
			*/
			public function deleteInterestGroupFromList($mc_list_id,$interest_group_id) {
					$api	= new Mailchimp($this->optionVal['api-key']);
					try {
						$retval = $api->call('lists/interest-grouping-del', array(
							'id' => $mc_list_id,
							'grouping_id' => $interest_group_id
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						$errorMessage = $e->getMessage();
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						die();
					}
				}

			/* 
			createNewInterestGroup();
			@since v5.2
			Delete an interest group from a given list 
			*/
			public function createNewInterestGroup($p) {
				
					$api	= new Mailchimp($this->optionVal['api-key']);
					parse_str( $p['form_data'], $formData );
					$list_id	= $formData['mc-list-id'];
					$grouping_name = $formData['add-interest-group-name'];
					$grouping_type = $formData['add-interest-group-type'];
					$grouping_groups = $formData['radio-dropdown-option'];
					
					try {
						$retval = $api->call('lists/interest-grouping-add', array(
							'id' => $list_id,
							'name' => $grouping_name,
							'type' => $grouping_type,
							'groups' => $grouping_groups
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						$errorMessage = $e->getMessage();
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						die();
					}
				}

			/* 
			updateInterestGroup();
			@since v5.2
			Update an interest group from a given list 
			*/
			public function updateInterestGroup( $mailchimp_list_id , $grouping_id , $previous_value , $new_value ) {
				
					$api	= new Mailchimp($this->optionVal['api-key']);
					
					try {
						$retval = $api->call('lists/interest-group-update', array(
							'id' => $mailchimp_list_id,
							'old_name' => $previous_value,
							'new_name' => $new_value,
							'grouping_id' => $grouping_id
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						if ( $e->getCode() == 211 ) {
							return "done";
						} else {
							$errorMessage = $e->getMessage();
							echo $errorMessage;
							// write our error to the error log,
							// when advanced debug is enabled
							if ( $this->optionVal['debug'] == 1 ) {
									$this->writeErrorToErrorLog( $e );
								}
						}
						die();
					}
				}

			/* 
			updateInterestGroupingTitle();
			@since v5.2
			Update an interest group from a given list 
			*/
			public function updateInterestGroupingTitle( $mailchimp_list_id , $grouping_id , $value ) {
				
					$api	= new Mailchimp($this->optionVal['api-key']);
					
					try {
						$retval = $api->call('lists/interest-grouping-update', array(
							'grouping_id' => $grouping_id,
							'name' => 'name',
							'value' => $value
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						$errorMessage = $e->getMessage();
						$errorCode = $e->getCode();
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						die();
					}
				}

			/* 
			addInterestGroupOption();
			@since v5.2
			Update an interest group from a given list 
			*/
			public function addInterestGroupOption( $mailchimp_list_id , $group_name , $grouping_id ) {
				
					$api	= new Mailchimp($this->optionVal['api-key']);
					
					try {
						$retval = $api->call('lists/interest-group-add', array(
							'id' => $mailchimp_list_id,
							'group_name' => $group_name,
							'grouping_id' => $grouping_id
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						$errorMessage = $e->getMessage();
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						die();
					}
					
				}

			/* 
			deleteInterestGroupOption();
			@since v5.2
			Update an interest group from a given list 
			*/
			public function deleteInterestGroupOption( $mailchimp_list_id , $group_name , $grouping_id ) {
				
					$api	= new Mailchimp($this->optionVal['api-key']);
					
					try {
						$retval = $api->call('lists/interest-group-del', array(
							'id' => $mailchimp_list_id,
							'group_name' => $group_name,
							'grouping_id' => $grouping_id
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						$errorMessage = $e->getMessage();
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						die();
					}
				}

			/* 
			addNewFieldToList();
			@since v5.2
			MailChimp API Request to Add new field to a list 
			*/
			public function addNewFieldToList( $p ) {
					include YKSEME_PATH . 'lib/inc/yks-mc-add-new-field-to-list.php';	
				}

			/* 
			@since v5.2
			MailChimp API Request to Add new field to a list 
			*/
			public function deleteFieldFromList( $mailchimp_list_id , $merge_tag ) {
					$api	= new Mailchimp($this->optionVal['api-key']);
					try {
						$retval = $api->call('lists/merge-var-del', array(
							'id'              => $mailchimp_list_id, // list id to delete merge tag from
							'tag'             => $merge_tag // merge tag to be delete
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						$errorMessage = $e->getMessage();
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						die();
					}
				}

			/* 
			@since v5.2
			Change the interest group type
			*/
			public function changeListInterestGroupType( $grouping_id , $value ) {
					$api	= new Mailchimp($this->optionVal['api-key']);
					try {
						$retval = $api->call('lists/interest-grouping-update', array(
							'grouping_id' => $grouping_id, // list id to delete merge tag from
							'name' => 'type', // merge tag to be delete
							'value' => $value
						));
						return "done";
					} catch( Exception $e ) { // catch any errors returned from MailChimp
						$errorMessage = $e->getMessage();
						echo $errorMessage;
						// write our error to the error log,
						// when advanced debug is enabled
						if ( $this->optionVal['debug'] == 1 ) {
								$this->writeErrorToErrorLog( $e );
							}
						die();
					}
				}

			/* 
			@since v5.2
			MailChimp API Request to Update an existing field on a given list 
			*/
			public function updateListField( $p ) {
					include YKSEME_PATH . 'lib/inc/yks-mc-update-list-field.php';		
				}

			// recursive directory scanning
			// used to retreive template files from bundled+user template directories
			function buildCustomTemplateDropdown($list) {

					$bundled_template_directory = YKSEME_PATH . 'templates/yikes-mailchimp-bundled-templates/';
					$bundled_template_scan = scandir($bundled_template_directory);
					$custom_template_directory = get_stylesheet_directory() . '/yikes-mailchimp-user-templates/';
					
					// only scan the directory when files DO exist
					if( is_dir( $custom_template_directory ) ) {	
						$custom_template_scan = scandir($custom_template_directory);
					}
					
					echo '<select class="template-selection-dropdown" name="yks-mc-template-file-selection" >';
						
						/* Bundled Template Dropdown Loop */
						echo '<optgroup label="Bundled Templates">';
							foreach( $bundled_template_scan as $bundled_template ) {
								if( $bundled_template != '.' && $bundled_template != '..' ) {	
									
									if( is_dir( $bundled_template_directory . $bundled_template ) ) {
										$template_path = $this->getTemplateFilePath($bundled_template_directory.$bundled_template);
									}
										
									// set the selected option
									if ( $list['custom_template']['template_file'] == $template_path ) {
										echo '<option value="' . $template_path . '" selected="selected" >' . str_replace( '_' , ' ' , $bundled_template ) . '</option>';
									} else {
										echo '<option value="' . $template_path . '">' . str_replace( '_' , ' ' , $bundled_template )  . '</option>';
									}
											
								}
							}
						echo '</optgroup>';
						
						echo '<optgroup label="Custom Templates">';	
							/* Custom Template Dropdown Loop */
							if( is_dir( get_stylesheet_directory() . '/yikes-mailchimp-user-templates' ) && count( $custom_template_scan ) >= 1 ) {
									foreach( $custom_template_scan as $custom_template ) {
										if( $custom_template != '.' && $custom_template != '..' ) {	
											if( is_dir( $custom_template_directory . $custom_template ) ) {
												$custom_template_path = $this->getTemplateFilePath($custom_template_directory.$custom_template);
											}
												// set the selected option
												if ( $list['custom_template']['template_file'] == $custom_template_path ) {
													echo '<option value="' . $custom_template_path . '" selected="selected" >' . str_replace( '_' , ' ' , $custom_template ) . '</option>';
												} else {
													echo '<option value="' . $custom_template_path . '">' . str_replace( '_' , ' ' , $custom_template ) . '</option>';
												}
										}
									}
							} else {
								echo '<option value="" disabled="disabled">None Found</option>';
							}
						echo '</optgroup>';
					echo '</select>';	
				}
		
			// function to return our form template path
			function getTemplateFilePath($directory) {	
					$get_files = scandir($directory);
					$sub_files = array();
					foreach( $get_files as $file ) {
						if($file != '.' && $file != '..') {
							$explode_file =  explode( '.' , $file );
							$file_extension = $explode_file[1];
							if ( $file_extension == 'php' ) {
								$file_extension_path = $directory . '/' . $file;
							}
						}
					}
					return $file_extension_path;
				}
		
			// Adding Opt-In Checkbox to comment forms
			// submit the user to mailchimp on a successful comment submission
			function ymc_add_meta_settings($comment_id) {
					  add_comment_meta(
						$comment_id, 
						'mailchimp_subscribe', 
						$_POST['mailchimp_subscribe'], 
						true
					  );
				}

			// add the checkbox after the comment form
			function add_after_comment_form($arg) {
						$custom_text = trim($this->optionVal['yks-mailchimp-optin-checkbox-text']);
						if ( $custom_text == '' ) {
							$custom_text = __("Sign Me Up For MAILCHIMP-REPLACE-THIS-TEXT's Newsletter", "gettext");
						} else {
							$custom_text = $custom_text;
						}
						// set the default checked state here...
						$arg['comment_notes_after'] = '<label for="yikes_mailchimp_comment_subscribe">
								<input type="checkbox" name="mailchimp_subscribe" id="yikes_mailchimp_comment_subscribe" checked="checked" /> 
								 '.$custom_text.'
								</label>';
						return $arg;	
				}

			// Replacing 'MAILCHIMP-REPLACE-THIS-TEXT' text with sitename
			function yikes_mc_replace_this_text( $text ) {			
					$newtext = get_bloginfo('name');
					$text = str_replace( 'MAILCHIMP-REPLACE-THIS-TEXT', $newtext, $text );
					return $text;
				}
			
			// Add our commenter to the list, when comment is submitted
			function ymc_subscription_add( $cid, $comment='' ) {
					  $cid = (int) $cid;
					  $yikes_api_key = $this->optionVal['api-key'];
					  $exploded_api_key = explode('-',$yikes_api_key);
					  $yikes_data_center = $exploded_api_key[1];
					  
					  if ( !is_object($comment) )
						$comment = get_comment($cid);
							
					  if ( $comment->comment_karma == 0 ) {
						$subscribe = get_comment_meta($cid, 'mailchimp_subscribe', true);
							if ( $subscribe == 'on' ) {
								global $current_user;
								get_currentuserinfo();
								$commenter_first_name = trim($current_user->user_firstname);
								$commenter_last_name = trim($current_user->user_lastname);
								
								if( isset( $commenter_first_name ) && $commenter_first_name != '' ) { 
									$commenter_first_name = $commenter_first_name; // use the users first name set in the profile
								} else { 
									$commenter_first_name = $comment->comment_author; // if no first name is set in the user profile, we will use the account name
								}
								
								if( isset( $commenter_last_name ) && $commenter_last_name != '' ) { 
									$commenter_last_name = $commenter_last_name; // use the users last name set in the profile
								} else { 
									$commenter_last_name = 'n/a'; // if the user has not set a last name in their profile, we set it to n/a
								}
								
								// store our API key
								// on the settings page, if they have chosen to display the checkbox
								$api = new Mailchimp($this->optionVal['api-key']);
								
								$apikey   = $yikes_api_key;
								$listid   = $this->optionVal['yks-mailchimp-optIn-default-list']; // Need to set up a default list to subscribe all users to
								$endpoint   = 'https://api.mailchimp.com';
								$optin	= $this->optionVal['optin'];
								
								// setup our welcome email variable
								if( isset( $this->optionVal['lists'][$listid]['fields'][$listid.'-email']['yks_mailchimp_send_welcome_'.$listid] ) && $this->optionVal['lists'][$listid]['fields'][$listid.'-email']['yks_mailchimp_send_welcome_'.$listid] == '1' ) {
									$welcome = false;
								} else {
									$welcome = true; 
								}
								
								// try adding subscriber, catch any error thrown
								try {
									$retval = $api->call('lists/subscribe', array(
										  'id'              => $listid, // form id
										 'email'	=>	array(	
												'email'	=>	$comment->comment_author_email
											),
										  'merge_vars'        => array( 
											'FNAME'	=>	$commenter_first_name,
											'LNAME'	=>	$commenter_last_name,
											'NAME' => $commenter_first_name
										   ), 
										  'double_optin'	=> $optin, // double optin value (retreived from the settings page)
										  'send_welcome' => $welcome
									));
									return "done";
								} catch( Exception $e ) { // catch any errors returned from MailChimp
									$error_message = $e->getMessage();
									if (strpos($error_message,'Click here to update your profile.') !== false) {
										$errorMessage = str_replace('Click here to update your profile.', '', $e->getMessage());
										$errorMessage_explode = explode('to list', $errorMessage);
										echo $errorMessage_explode[0].'.';
										die();
									}
									if (strpos($error_message,'Invalid') !== false) {
										$display_errorMessage = str_replace('Invalid MailChimp List ID:', "Oops! The Webmaster hasn't set up the default MailChimp list to subscribe you too. Please contact them and let them know of this error. In the meantime, un-check the subscription checkbox in the comment form when submitting comments.", $error_message);
										echo $display_errorMessage;
										die();
									} else { 
										// str_replace('Invalid MailChimp List ID: .', 'The Webmaster hasn\t set up the default MailChimp list to subscribe you too. Please contact them and let them know of this error. In the meantime, un-check the subscription checkbox in the comment form when submitting comments.', $e->getMessage());
										echo $errorMessage;
										die();
									}
								}
							}
					  }
				}	
			
			// add our actions on initialize
			// inside of __construct()
			public function ykes_mc_apply_filters() {
					// if the optin checkbox setting is set to show
					// we wiill display the checkbox on the front end
					if ( $this->optionVal['optIn-checkbox'] == 1 ) {
						add_action('comment_post', array(&$this, 'ymc_add_meta_settings'), 10, 2);
						add_action('comment_approved_', array(&$this, 'ymc_subscription_add'), 60, 2);
						add_action('comment_post', array(&$this, 'ymc_subscription_add'));
						add_filter('gettext', array(&$this, 'yikes_mc_replace_this_text'));
						// only display the checkbox if the user is logged in, and the default list is set
						if( is_user_logged_in() && isset( $this->optionVal['yks-mailchimp-optIn-default-list'] ) && $this->optionVal['yks-mailchimp-optIn-default-list'] != 'select_value' ) {
							add_filter('comment_form_defaults', array(&$this, 'add_after_comment_form'));
						}
					}
				}

			
			/****
			**
			** Custom The_Content filter
			** used to prevent other plugins from hooking here
			**
			****/
			function yks_mc_content() {
					//Create our own version of the_content so that others can't accidentally loop into our output - Taken from default-filters.php, shortcodes.php, and media.php
					if ( !has_filter( 'yks_mc_content', 'wptexturize' ) ) {
						add_filter( 'yks_mc_content', 'wptexturize'        );
						add_filter( 'yks_mc_content', 'convert_smilies'    );
						add_filter( 'yks_mc_content', 'convert_chars'      );
						add_filter( 'yks_mc_content', 'wpautop'            );
						add_filter( 'yks_mc_content', 'shortcode_unautop'  );
						add_filter( 'yks_mc_content', 'prepend_attachment' );
						$vidembed = new WP_Embed();
						add_filter( 'yks_mc_content', array( &$vidembed, 'run_shortcode'), 8 );
						add_filter( 'yks_mc_content', array( &$vidembed, 'autoembed'), 8 );
						add_filter( 'yks_mc_content', 'do_shortcode', 11);
					} //end has_filter
				} //end yks_mc_content
			
			
			// Check if cURL is enabled at the server level
			// used on the options.php page
			public function yikes_curl_check() {
					if  (in_array  ('curl', get_loaded_extensions())) {
						return true;
					} else {
						return false;
					}
				}

			// check if php.ini exists in the site root
			function yks_check_if_php_ini_exists() {

					// get php ini path from the actively loaded php ini file
					$wordpress_site_root = str_replace('php.ini','',php_ini_loaded_file());
					// file name
					$filename = '/php.ini';
					
					$php_ini_location = php_ini_loaded_file();
					
					if (file_exists($wordpress_site_root.$filename)) {
						echo "<span class='yks_mc_no_phpini_success'>Good News </span>: We have located your <strong>".str_replace('/','',$filename)."</strong> file inside the directory <strong>".$wordpress_site_root."</strong>";
						$filename = '/php.ini';
					} else {
						echo "<span class='yks_mc_no_phpini_alert'>Alert </span>: No <strong>".str_replace('/','',$filename)."</strong> was located in <strong>".$wordpress_site_root.'/'."</strong>.";
						$filename = '/php.ini';
					}
					
				}
			
			// display the php.ini location to the user
			function yks_display_php_ini_location() {
				echo php_ini_loaded_file();
			}
				

			//	Add TinyMCE Buttons to the TinyMCE Editor
			//	We'll use the button to place form shortcodes!
			//  NOTE: This only runs on 3.9 or greater -> due to tinyMCE 4.0
				// Custom TinyMCE Button to insert form shortcodes onto pages and posts
			function yks_mc_add_tinyMCE() {			
					global $typenow;
					// only on Post Type: post and page
					if( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
						return ;
					}
				}

			// inlcude the js for tinymce
			function yks_mc_add_tinymce_plugin( $plugin_array ) {		
					$plugin_array['yks_mc_tinymce_button'] = plugins_url( '/../js/yks_mc_tinymce_button.js', __FILE__ );
					// Print all plugin js path
					// var_dump( $plugin_array );
					return $plugin_array;		
				}

			// Add the button key for address via JS
			function yks_mc_add_tinymce_button( $buttons ) {	
					array_push( $buttons, 'yks_mc_tinymce_button_key' );
					// Print all buttons
					return $buttons;
				}
						
			/**
			* Localize Script
			* Pass our imported list data, to the JS file
			* to build the dropdown list in the modal
			*/
			function yks_mc_js_admin_head() {			
					$yks_mc_imported_list_data = $this->optionVal['lists'];
					// our list data array, we will pass to the js file
					$list_value_array = array();
					$i = 0;
					// loop over the list data
					foreach ( $yks_mc_imported_list_data as $single_list ) {
						// store it temporarily and push it back to our array
						$list_value_array[$i]['text'] = $single_list['name'];
						$list_value_array[$i]['value'] = $single_list['id'];
						$i++;
					}
					?>
					<!-- TinyMCE Shortcode Plugin -->
					<script type='text/javascript'>
						var yks_mc_lists_data = {
							'lists': <?php echo json_encode($list_value_array); ?>
						};
					</script>
					<!-- TinyMCE Shortcode Plugin -->
					<?php
				}
						
						
			/* Checking if the user is on localhost */
			/* If they are we want to display a warning about SSL on localhost */
			function yks_mc_is_user_localhost() {
					$whitelist = array( '127.0.0.1', '::1' );
					if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) ) {
						return true;
					}
				}
						
			/*******************************************************/
			/*							Helper Functions						 */
			/******************************************************/
			/* 
			*
			* Helper function when testing user submitted data
			* to be used inside of the yikes_mc_get_form_data filter
			*
			*/
			function yks_mc_print_user_data( $form_ID, $merge_variables ) {
					echo '<h3>The Data Returned From This Form</h3>';
					echo '<strong>MailChimp List ID : </strong> '.$form_ID.' <br />';						
					echo '<hr />';						
					if ( isset( $merge_variables['FNAME'] ) && $merge_variables['FNAME'] != '' ) {
						echo '<strong>User\'s Name : </strong> '.$merge_variables['FNAME'].' <br />';							
						echo '<hr />';							
					}						
					if ( isset( $merge_variables['LNAME'] ) && $merge_variables['LNAME'] != '' ) {
						echo '<strong>User\'s Name : </strong> '.$merge_variables['LNAME'].' <br />';						
						echo '<hr />';						
					}					
					echo '<strong>Users Email : </strong>'.$merge_variables['EMAIL'].' <br />';						
					if ( isset( $merge_variables['GROUPINGS'] ) ) {								
						echo '<hr />';						
						echo '<strong>Interest Group Data : </strong><br /><br />';
						foreach ( $merge_variables['GROUPINGS'] as $grouping_variable ) {
							echo '<ol style="list-style:none;">Interest Group : '.$grouping_variable['id'].'</ol>';
							if ( !isset($grouping_variable['groups'][0]) || $grouping_variable['groups'][0] == '' ) {
								echo '<li style="list-style:none;">No interest groups selected</li><br />';
							} else {
								// loop over interest groups to build array
								$interest_group_array = array();
								foreach ( $grouping_variable['groups'] as $interest_group ) {
									array_push( $interest_group_array , $interest_group );
								}
								if ( count($interest_group_array)  > 0 ) {
									foreach ( $interest_group_array as $interest_group_label ) {
										echo '<li style="list-style:none;">'.$interest_group_label.'</li>';
									}
									echo '<br />';
								}		
							}
						}
					}
					echo '<em style="color:rgb(238, 93, 93);">Form data has not been sent to MailChimp</em><br />';
					die(); // die to prevent data being sent over to MailChimp
				}
						
			/* 
			* Helper function when testing user submitted data
			* print_r($merge_variables) is returned
			*/
			function yks_mc_dump_user_data( $form_ID, $merge_variables ) {
					echo '<strong>Form ID :</strong> '.$form_ID. '<br />';
					echo '<strong>Merge Variables :</strong><br />';
					print_r($merge_variables);
					echo '<br /><em style="color:rgb(238, 93, 93);">Form data has not been sent to MailChimp</em>';
					die(); // die to prevent data being sent over to MailChimp
				}
							
			/****************************************************************************************
			*			Begin Heartbeat API Code
			*			- Used on the Account Activity page for lilve updates
			****************************************************************************************/
				
			/*
				Client-side code. First we enqueue the Heartbeat API and our Javascript. 
				Our Javascript is then setup to always send the message 'marco' to the server.
				If a message comes back, the Javascript logs it (polo) to console.
			*/
				 
			//enqueue heartbeat.js and our Javascript
			function yks_mc_heartbeat_init() {   			 
					//enqueue the Heartbeat API
					wp_enqueue_script('heartbeat');
						
					//load our Javascript in the footer
					add_action("admin_print_footer_scripts", array( &$this ,"yks_mc_heartbeat_admin_footer" ) );
				}
				
				 
			//our Javascript to send/process from the client side
			function yks_mc_heartbeat_admin_footer() {
					include YKSEME_PATH . 'lib/inc/yks-mc-heartbeat-api.php';
				}
					
			/*
				Our server-side code. 
				------------------------------
				This hooks into the heartbeat_received filter. 
				It checks for a key 'client' in the data array. If it is set to 'get_chimp_chatter_data', 
				a key 'server' is set to 'Get MailChimp Chatter Data' in the response array.
			*/
			function yks_mc_heartbeat_received($response, $data) {	
					// if the client returns get chimp chatter data, popluate
					// the response with some data
					if( $data['yks_mc_chimp_chatter_heartbeat'] == 'get_chimp_chatter_data' ) {
						// populate the response with something
						$response['yks_mc_chimp_chatter_data'] = 'Get MailChimp Chatter Data';
					} else if ( $data['yks_mc_chimp_chatter_heartbeat'] == 'get_chimp_chatter_widget_data' ) {
						$response['yks_mc_chimp_chatter_data'] = 'Get MailChimp Chatter Widget Data';
					}	
					return $response;
				}
				
			/** Change Default HeartBeat API Pulse Time */
			function yks_mc_tweak_heartbeat_settings( $settings ) {
					$settings['interval'] = 45; //Anything between 15-60
					return $settings;
				}	

			/*******************************************************
			Custom Dashboard MailChimp Account Activity Widget
			********************************************************/
			/**
			 * Add a widget to the dashboard.
			 *
			 * This function is hooked into the 'wp_dashboard_setup' action below.
			 */
			function yks_mc_add_chimp_chatter_dashboard_widget() {
					wp_add_dashboard_widget(
						 'yks_mc_account_activity_widget',         // Widget slug.
						 'MailChimp Account Activity',         // Title.
						array( &$this , 'yks_mc_chimp_chatter_dashboard_widget_function' ) // Display function.
					);		
				}
				
			/**
			 * Create the function to output the contents of our Dashboard Widget.
			 */
			function yks_mc_chimp_chatter_dashboard_widget_function() {
					include YKSEME_PATH . 'lib/inc/yks-mc-heartbeat-widget-functions.php';
				} 
				
				
			// help , review container 
			public function help_review_container() {
					?>
					<div id="yks_mc_review_this_plugin_container">
						<a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues?state=open" target="_blank">
							<span class="yks_mc_need_support">
								<strong>
									<?php _e( 'Need Help?', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
									<?php _e( 'Get In Contact!', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
									<div class="dashicons dashicons-plus-alt"></div>
								</strong>
							</span>
						</a>
						<a href="http://wordpress.org/support/view/plugin-reviews/yikes-inc-easy-mailchimp-extender" target="_blank">
							<span class="yks_mc_leave_us_a_review">
								<strong>
									<?php _e( 'Loving the plugin?', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
									<?php _e( 'Leave us a nice review', 'yikes-inc-easy-mailchimp-extender' ); ?> <br />
									<div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div><div class="dashicons dashicons-star-filled"></div>
								</strong>
							</span>
						</a>
						<a href="http://www.yikesinc.com" target="_blank" class="yks_header_logo">
							<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes_logo.png'; ?>" alt="YIKES Inc. Logo" width=85 title="YIKES Inc. Logo" />
						</a>
					</div>
					<?php
				}
				
				
			/** 
				Write our errors to an error log for display to the user 
				- should help minimize number of requests we receive, or at least help us diagnose user issues better
			**/
			
			/* 
			* writeErrorToErrorLog() 
			* parameters: $errorResponse
			* writes a returned API error to our log for display
			*
			* @since 5.2
			*/
			public function writeErrorToErrorLog( $errorResponse ) {
					// make sure file_get_contents and file_put_contents are available
					if ( function_exists( 'file_get_contents' ) && function_exists( 'file_put_contents' ) ) {	
						$error_occurance_time = current_time( 'M' ) . '. ' . current_time( 'jS' ) . ', ' . current_time( 'Y' ) . ' - ' . current_time( 'g:i:sa' );
						$error_log_location = YKSEME_PATH . 'lib/error_log/yks_mc_error_log.php';
						$current_contents = file_get_contents( $error_log_location );
						// get total count of errors, we only want to limit to 8 latest errors
						$total_errors = explode( '<tr>' , $current_contents );
						$error_array = array();
						$i = 0;
						foreach( $total_errors as $error ) {	
							$error_array[] = $error;
							// limit the error log to the latest 10 errors
							if ( ++$i == 11 ) {
								break;
							}
						}
						$new_content = '<tr>
							<td>' . $errorResponse->getMessage() . '</td>
							<td>' . $error_occurance_time . '</td>
						</tr>' . implode( '<tr>' , $error_array );
						file_put_contents( $error_log_location , $new_content );
					}
				}
			
			/*
			*  ytks_mc_generate_error_log_table()
			*  generate our erorr log table on the options settings page
			*
			*  @since 5.2
			*/	
			public function yks_mc_generate_error_log_table() {					
					$error_log_contents = file_get_contents( YKSEME_PATH . 'lib/error_log/yks_mc_error_log.php' , true );							
					if ( $error_log_contents != '' ) {
						return $error_log_contents;
					}			
				}
					
			/*
			*  clearYksMCErrorLog()
			*  clear the error log of all errors
			*
			*  @since 5.2
			*/	
			public function clearYksMCErrorLog() {	
					echo 'running'; 
					try {
						$clear_contents = file_put_contents( YKSEME_PATH . 'lib/error_log/yks_mc_error_log.php' , '' );
					} catch ( Exception $e ) {
						return $e->getMessage();
						$this->writeErrorToErrorLog( $e );
					}			
				}	
		
		} // end class
	} // end class check
?>