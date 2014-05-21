<?php
if(!class_exists("yksemeBase"))
	{
  class yksemeBase
		{
		
/**
 *	Variables
 */
private	$error		    	= false;
private	$errorMsg	    	= '';
public	$sessName	    	= 'ykseme';
public	$optionVal			= false;
public	$currentLists		= false;
public	$currentListsCt	= false;

/**
 *	Construct
 */
public function __construct()
	{
	yksemeBase::initialize();
	add_action('init', array(&$this, 'ykes_mc_apply_filters'));
	}

/**
 *	Destruct
 */
public function __destruct()
	{
	unset($this);
	}

/**
 *	ACTIONS
 *	These are called when the plugin is initialized/deactivated/uninstalled
 */
public function activate()
	{
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
public function deactivate()
	{
	
	}
public function uninstall()
	{ // delete options on plugin uninstall
	delete_option(YKSEME_OPTION);
	delete_option('api_validation');
	delete_option('imported_lists');
	}

/***** INITIAL SETUP
 ****************************************************************************************************/
public function initialize()
	{
	// If it's not already set up, initialize our plugin session
	if(session_id() == '') session_start();
	if(!is_array(@$_SESSION[$this->sessName]))
	 {
	 $_SESSION[$this->sessName]	= array();
	 }
	// Add the CSS/JS files
	add_action('admin_print_styles',		array(&$this, 'addStyles'));
	add_action('admin_print_scripts',		array(&$this, 'addScripts'));
	add_action('admin_init', array( &$this, 'yks_easy_mc_plugin_activation_redirect' ) );
	// tinymce buttons
	// only add filters and actions on wp 3.9 and above
	if ( get_bloginfo( 'version' ) >= '3.9' ) {
		add_action( 'admin_head', array(&$this, 'yks_mc_add_tinyMCE') );
		add_filter( 'mce_external_plugins', array(&$this, 'yks_mc_add_tinymce_plugin') );
		add_filter( 'mce_buttons', array(&$this, 'yks_mc_add_tinymce_button') );
		// pass our lists data to tinyMCE button for use
		foreach( array('post.php','post-new.php') as $hook )
		 add_action( "admin_head-$hook", array(&$this, 'yks_mc_js_admin_head') );
	} else { 
		// if the WordPress is older than 3.9
		// load jQuery UI 1.10 CSS for dialogs
		wp_enqueue_style('yks_easy_mc_extender-admin-ui-css',
                '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css'
            );
		wp_enqueue_style('yks_easy_mc_wp_3.8', plugin_dir_url(__FILE__).'../css/yks_easy_mc_wp_3point8.css');
	}
	if(!is_admin())
		{
		add_action('wp_print_styles', array(&$this, 'addStyles_frontend'));
		add_action('wp_print_scripts', array(&$this, 'addScripts_frontend'));
		}
	// Setup the administration menus
	add_action('admin_menu', array(&$this, 'addAdministrationMenu'));
	// Make sure the option exists
	if(!$this->optionVal)		$this->getOptionValue();
	// Setup outfit shortcodes
	$this->createShortcodes();
	// Initialize current list array
	$this->currentLists		= array();
	$this->currentListsCt	= array();
	// Do any update tasks if needed
	$this->runUpdateCheck();
	// Register Our Widget
	$this->registerMailChimpWidget($this->optionVal['lists']);
	}
public function createShortcodes()
	{
	add_shortcode('yks-mailchimp-list', array(&$this, 'processShortcode'));
	}
// Create and store our initial plugin options	
public function getOptionValue()
	{
	$defaultVals	= array(
									'version'	=> YKSEME_VERSION_CURRENT,
									'api-key'	=> '',
									'flavor'	=> '1',
									'debug'	=> '0',
									'optin' => 'true',
									'single-optin-message' => __('Thank You for subscribing!', 'yikes-inc-easy-mailchimp-extender'),
									'double-optin-message' => __('Thank You for subscribing! Check your email for the confirmation message.', 'yikes-inc-easy-mailchimp-extender'),
									'interest-group-label'	=>	__('Select Your Area of Interest', 'yikes-inc-easy-mailchimp-extender'),
									'yks-mailchimp-optIn-checkbox'	=> 'hide',
									'yks-mailchimp-optIn-default-list' => array(),
									'yks-mailchimp-optin-checkbox-text'	=> 'SIGN ME UP!',
									'lists'		=> array()
								);
	$ov	= get_option(YKSEME_OPTION, $defaultVals);
	$this->optionVal	= $ov;
	return $ov;
	}
	
private function runUpdateCheck()
	{
	if(!isset($this->optionVal['version'])
	|| $this->optionVal['version'] < YKSEME_VERSION_CURRENT)
		{
		$this->runUpdateTasks();
		}
	}


/***** FUNCTIONS
 ****************************************************************************************************/
 // check if were on the login page
 function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}
 // create a slug like string, given some text (ie: this-is-the-name)
public function slugify($text)
	{ 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
  // trim
  $text = trim($text, '-');
  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  // lowercase
  $text = strtolower($text);
  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);
  if(empty($text))
  	{
    return 'n-a';
  	}
  return $text;
	}
// create an array for any fields left blank
// not sure if still needed	
public function getBlankFieldsArray($lid='')
	{
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
public function getImportedFieldsArray($lid, $mv)
	{
		if(empty($mv)) {
			return false;
		} else {
			$fields = array();
			
			$num = 1;
			foreach($mv['data'][0]['merge_vars'] as $field)
				{
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
								'placeholder' => (isset($field['placeholder-'.$lid.'-'.$num]) ? $field['placeholder-'.$lid.'-'.$num] : ''),
								'redirect_checkbox'	=>	(isset($field['yks_mailchimp_redirect_'.$lid]) ? $field['yks_mailchimp_redirect_'.$lid] : ''),
								'selected_page'	=>	(isset($field['page_id_'.$lid]) ? $field['page_id_'.$lid] : ''),
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
public function getBrowser()
	{ 
	$u_agent	= $_SERVER['HTTP_USER_AGENT']; 
	$bname		= 'Unknown';
	$platform	= 'Unknown';
	$version	= "";
	//First get the platform?
	if(preg_match('/linux/i', $u_agent))
		{
		$platform = 'Linux';
		}
	elseif(preg_match('/macintosh|mac os x/i', $u_agent))
		{
		$platform = 'Mac';
		}
	elseif(preg_match('/windows|win32/i', $u_agent))
		{
		$platform = 'Windows';
		}
	
	// Next get the name of the useragent yes seperately and for good reason
	if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
		{ 
		$bname = 'Internet Explorer'; 
		$ub = "MSIE"; 
		} 
	elseif(preg_match('/Firefox/i',$u_agent)) 
		{ 
		$bname = 'Mozilla Firefox'; 
		$ub = "Firefox"; 
		} 
	elseif(preg_match('/Chrome/i',$u_agent)) 
		{ 
		$bname = 'Google Chrome'; 
		$ub = "Chrome"; 
		} 
	elseif(preg_match('/Safari/i',$u_agent)) 
		{ 
		$bname = 'Apple Safari'; 
		$ub = "Safari"; 
		} 
	elseif(preg_match('/Opera/i',$u_agent)) 
		{ 
		$bname = 'Opera'; 
		$ub = "Opera"; 
		} 
	elseif(preg_match('/Netscape/i',$u_agent)) 
		{ 
		$bname = 'Netscape'; 
		$ub = "Netscape"; 
		} 
	
	// finally get the correct version number
	$known = array('Version', $ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if(!preg_match_all($pattern, $u_agent, $matches))
		{
		// we have no matching number just continue
		}
	
	// see how many we have
	$i = count($matches['browser']);
	if($i != 1)
		{
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if(strripos($u_agent,"Version") < strripos($u_agent,$ub))
			{
			$version= $matches['version'][0];
			}
		else
			{
			$version= $matches['version'][1];
			}
		}
	else
		{
		$version= $matches['version'][0];
		}
	
	// check if we have a number
	if($version==null || $version=="") {$version="?";}
	
	return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
	);
	}
// Store google analytics tracking information
// Generated on the lists page	
public function getTrackingGif($page='')
	{
	?>
	<script type="text/javascript">
  	var _gaq = _gaq || [];
  	_gaq.push(['_setAccount', 'UA-3024863-1']);
  	_gaq.push(['_trackPageview', '/virtual/wordpress/plugin/yikes-inc-easy-mailchimp-extender/<?php echo $this->slugify($page); ?>']);

  	(function() {
    	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  	})();
	</script>
	<?php
	}


/***** CONFIGURATION
 ****************************************************************************************************/
// Update our plugin options
// Runs when the user updates the settings page with new values 
public function updateOptions($p)
	{
	if(!empty($p['form_data']))
		{
		parse_str($p['form_data'], $fd);
		$this->optionVal['api-key']	= $fd['yks-mailchimp-api-key'];
		$this->optionVal['flavor']	= $fd['yks-mailchimp-flavor'];
		$this->optionVal['debug']	= $fd['yks-mailchimp-debug'];
		$this->optionVal['optin']	= $fd['yks-mailchimp-optin'];
		$this->optionVal['single-optin-message']	= $fd['single-optin-message'];
		$this->optionVal['double-optin-message']	= $fd['double-optin-message'];
		$this->optionVal['interest-group-label']	= $fd['interest-group-label'];
		$this->optionVal['yks-mailchimp-optIn-checkbox']	= $fd['yks-mailchimp-optIn-checkbox'];
		$this->optionVal['yks-mailchimp-optIn-default-list']	= isset($fd['yks-mailchimp-optIn-default-list']) ? $fd['yks-mailchimp-optIn-default-list'] : null; // if its set, else set to null <- fixes save form settings bug
		$this->optionVal['yks-mailchimp-optin-checkbox-text']	= $fd['yks-mailchimp-optin-checkbox-text'];
		update_option('api_validation', 'valid_api_key');
		return update_option(YKSEME_OPTION, $this->optionVal);
		}
	return false;
	}
// Update the API Key	
public function updateApiKey($k)
	{
	$this->optionVal['api-key']	= $k; 
	return update_option(YKSEME_OPTION, $this->optionVal);
	}
// Update the version number	
public function updateVersion($k)
	{
	$this->optionVal['version']	= $k; 
	return update_option(YKSEME_OPTION, $this->optionVal);
	}


/********Mailchimp Error Codes
*****************************************************************************************************/
// not sure these are used any more
// maybe remove
public function YksMCErrorCodes ($error) 
{
//Server Errors	
$errorcode['-32601'][1] = 'ServerError_MethodUnknown';
$errorcode['-32602'][1] = 'ServerError_InvalidParameters';
$errorcode['-99'][1] = 'Unknown_Exception';
$errorcode['-98'][1] = 'Request_TimedOut';
$errorcode['-92'][1] = 'Zend_Uri_Exception';
$errorcode['-91'][1] = 'PDOException';
$errorcode['-91'][1] = 'Avesta_Db_Exception';
$errorcode['-90'][1] = 'XML_RPC2_Exception';
$errorcode['-90'][1] = 'XML_RPC2_FaultException';
$errorcode['-50'][1] = 'Too_Many_Connections';
$errorcode['0'][1] = 'Parse_Exception';

$errormessage[1] = "Sorry, we can't connect to MailChimp at this time. Please come back later and try again.";

//API User or API Key error
$errorcode['100'][2] = 'User_Unknown';
$errorcode['101'][2] = 'User_Disabled';
$errorcode['102'][2] = 'User_DoesNotExist';
$errorcode['103'][2] = 'User_NotApproved';
$errorcode['104'][2] = 'Invalid_ApiKey';
$errorcode['105'][2] = 'User_UnderMaintenance';
$errorcode['106'][2] = 'Invalid_AppKey';
$errorcode['107'][2] = 'Invalid_IP';
$errorcode['108'][2] = 'User_DoesExist';
$errorcode['109'][2] = 'User_InvalidRole';
$errorcode['120'][2] = 'User_InvalidAction';
$errorcode['121'][2] = 'User_MissingEmail';
$errorcode['122'][2] = 'User_CannotSendCampaign';
$errorcode['123'][2] = 'User_MissingModuleOutbox';
$errorcode['124'][2] = 'User_ModuleAlreadyPurchased';
$errorcode['125'][2] = 'User_ModuleNotPurchased';
$errorcode['126'][2] = 'User_NotEnoughCredit';
$errorcode['127'][2] = 'MC_InvalidPayment';

$errormessage[2] = "Sorry, this MailChimp account does not exist.";

// List errors 
$errorcode['200'][3] = 'List_DoesNotExist';
$errorcode['210'][3] = 'List_InvalidInterestFieldType';
$errorcode['211'][3] = 'List_InvalidOption';
$errorcode['212'][3] = 'List_InvalidUnsubMember';
$errorcode['213'][3] = 'List_InvalidBounceMember';

$errormessage[3] = "Sorry,  this list does not exist.";

//Already subscribed or unsubscribed
$errorcode['214'][4] = 'List_AlreadySubscribed';
$errorcode['215'][4] = 'List_NotSubscribed';
$errorcode['220'][4] = 'List_InvalidImport';
$errorcode['221'][4] = 'MC_PastedList_Duplicate';
$errorcode['222'][4] = 'MC_PastedList_InvalidImport';
$errorcode['230'][4] = 'Email_AlreadySubscribed';
$errorcode['231'][4] = 'Email_AlreadyUnsubscribed';
$errorcode['232'][4] = 'Email_NotExists';
$errorcode['233'][4] = 'Email_NotSubscribed';

$errormessage[4] = "Sorry, you are already subscribed to this list.";

// General Message 
$errorcode['250'][5] = 'List_MergeFieldRequired';
$errorcode['251'][5] = 'List_CannotRemoveEmailMerge';
$errorcode['252'][5] = 'List_Merge_InvalidMergeID';
$errorcode['253'][5] = 'List_TooManyMergeFields';
$errorcode['254'][5] = 'List_InvalidMergeField';
$errorcode['270'][5] = 'List_InvalidInterestGroup';
$errorcode['271'][5] = 'List_TooManyInterestGroups';
$errorcode['300'][5] = 'Campaign_DoesNotExist';
$errorcode['301'][5] = 'Campaign_StatsNotAvailable';
$errorcode['310'][5] = 'Campaign_InvalidAbsplit';
$errorcode['311'][5] = 'Campaign_InvalidContent';
$errorcode['312'][5] = 'Campaign_InvalidOption';
$errorcode['313'][5] = 'Campaign_InvalidStatus';
$errorcode['314'][5] = 'Campaign_NotSaved';
$errorcode['315'][5] = 'Campaign_InvalidSegment';
$errorcode['316'][5] = 'Campaign_InvalidRss';
$errorcode['317'][5] = 'Campaign_InvalidAuto';
$errorcode['318'][5] = 'MC_ContentImport_InvalidArchive';
$errorcode['319'][5] = 'Campaign_BounceMissing';
$errorcode['330'][5] = 'Invalid_EcommOrder';
$errorcode['350'][5] = 'Absplit_UnknownError';
$errorcode['351'][5] = 'Absplit_UnknownSplitTest';
$errorcode['352'][5] = 'Absplit_UnknownTestType';
$errorcode['353'][5] = 'Absplit_UnknownWaitUnit';
$errorcode['354'][5] = 'Absplit_UnknownWinnerType';
$errorcode['355'][5] = 'Absplit_WinnerNotSelected';

$errormessage[5] = 'Sorry, MailChimp could not process your signup.';

// Validation errors
$errorcode['500'][6] = 'Invalid_Analytics';
$errorcode['503'][6] = 'Invalid_SendType';
$errorcode['504'][6] = 'Invalid_Template';
$errorcode['505'][6] = 'Invalid_TrackingOptions';
$errorcode['506'][6] = 'Invalid_Options';
$errorcode['507'][6] = 'Invalid_Folder';
$errorcode['550'][6] = 'Module_Unknown';
$errorcode['551'][6] = 'MonthlyPlan_Unknown';
$errorcode['552'][6] = 'Order_TypeUnknown';
$errorcode['553'][6] = 'Invalid_PagingLimit';
$errorcode['554'][6] = 'Invalid_PagingStart';
$errorcode['555'][6] = 'Max_Size_Reached';
$errorcode['556'][6] = 'MC_SearchException';

$errormessage[6] = "Sorry, MailChimp doesn't like the data you are trying to send.";

// Validate date and time field
$errorcode['501'][7] = 'Invalid_DateTimel';

$errormessage[7] = "Sorry, that date and time is invalid. Please try again.";

//Validate Email
$errorcode['502'][8] = 'Invalid_Email';

$errormessage[8] = "Sorry, that email address is invalid. Please try again.";

// Validate URL fields
$errorcode['508'][9] = 'Invalid_URL';

$errormessage[9] = "Sorry, that URL is invalid. Please try again.";

// Get error message
foreach ($errorcode as $eid => $value )
	{
	if ($eid == $error)
		{
		foreach ($value as  $key => $mssg)
			{
				$Message = $errormessage[$key];
				if ( $this->optionVal['debug']	== '1')
					{
					$Message .= '<br /><strong>Error Code: '.$error.' - '.$mssg.'</strong><br />';
					$Message .= 'For more info, <a href="http://apidocs.mailchimp.com/api/1.3/exceptions.field.php">visit the MailChimp website</a> or <a href="http://kb.mailchimp.com/home">contact MailChimp</a>';	
					}
			}			
		}
	}	
	return $Message;

}


/***** LIST ACTIONS
 ****************************************************************************************************/
// Import a list from MailChimp and add it to the lists page
// Runs when user add's a list from the drop down on the list page
// Sending a call to MailChimp api to retrieve list data
 public function addList($lid='' , $name='')
	{
	if($lid == '' || isset($this->optionVal['lists'][$lid])) return false;
	$api	= new wpyksMCAPI($this->optionVal['api-key']);
	
	$mv = $api->call('lists/merge-vars', array(
 				'id' => array($lid)
			)
		);		
				
	if($mv)
		{
		$list	= array(
						'id'			=> $lid,
						'list-id'	=> $lid,
						'name'	=> $name,
						'fields'	=> $this->getImportedFieldsArray($lid, $mv)
					);
				
		$this->optionVal['lists'][$list['id']]	= $list;
		
		// store newly retreived list array in imported_list global option
		update_option('imported_lists', $this->optionVal['lists']);
		
		if(update_option(YKSEME_OPTION, $this->optionVal))
			{
			return $this->generateListContainers(array($list));
			}
		}
	return false;
	}
	
///////////////////////////////////////////////////
/*		Get Interest Groups	*/
///////////////////////////////////////////////////	
// Send request to MailChimp API to retreive interest groups associated to a sepcific list
public function getInterestGroups($list_id)
	{
	// store our API key
	$api = new wpyksMCAPI($this->optionVal['api-key']);
	
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
							
							switch($yikes_mc_flavor)
									{
								// table flavor
								case '0':
									// loop over each interest group returned
									foreach($interest_groups as $interest_group) {
											// if the interest group label is set to '' on the settings page
											if ( $this->optionVal['interest-group-label'] == '' ) {
												$user_set_interest_group_label = '<label class="prompt yks_table_label yks-mailchimpFormTableRowLabel yks-mailchimpFormTableRowLabel-required font-secondary label-text">'.$interest_group['name'].'</label>'; // display the interest group name from MailChimp
											} else { 
												$user_set_interest_group_label =  '<label class="prompt yks_table_label yks-mailchimpFormTableRowLabel yks-mailchimpFormTableRowLabel-required font-secondary label-text">'.$this->optionVal['interest-group-label'].'</label>'; // else display the custom name set in the settings page
											}
											?>
											<!-- pass interest group data in a hidden form field , required to pass the data back to the correct interest-group -->
											<input type='hidden' name='interest-group-data' value='<?php echo $this->optionVal["interest-groups"]; ?>' />
											<?php
											// get form type
											$list_form_type = $interest_group['form_field'];
											$interestGroupID = $interest_group['id'];

											switch($list_form_type)
											{
												
											// checkbox interest groups
											case 'checkboxes':
													echo '<tr class="yks_mc_table_interest_group_holder yks_mc_table_checkbox_holder">';
														echo '<td class="yks_mc_table_td">';
														// display the label
														echo $user_set_interest_group_label;
															foreach ($interest_group['groups'] as $singleGrouping) {
																$checkboxValue = $interest_group['name'];
																echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="checkbox" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_checkbox" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'[]" value="'.$singleGrouping['name'].'">'.$singleGrouping['name'].'</label>';
															}
														echo '</td>';
													echo '</tr>';					
											break;
												
											// radiobuttons interest groups									
											case 'radio':
												echo '<tr class="yks_mc_table_interest_group_holder yks_mc_table_radio_holder">';
													echo '<td class="yks_mc_interest_radio_button_holder yks_mc_table_td">';
														// display the label
														echo $user_set_interest_group_label;
														foreach ($interest_group['groups'] as $singleGrouping) {
															$radioValue = $interest_group['name'];
															echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="radio" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_radio" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" value="'.$singleGrouping['name'].'">'.$singleGrouping['name'].'</label>';
														}
													echo '</td>';	
												echo '</tr>';	
											break;
												
											// drop down interest groups
											case 'dropdown':	
												echo '<tr class="yks_mc_table_interest_group_holder yks_mc_table_dropdown_holder">';	
													echo '<td class="yks_mc_table_dropdown_interest_group_holder yks_mc_table_td">';
														// display the label
														echo $user_set_interest_group_label;
														echo '<select id="yks_mc_interest_dropdown"  name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" class="yks_mc_interest_group_select">';
															foreach ($interest_group['groups'] as $singleGrouping) {
																$dropDownValue = $interest_group['name'];
																echo '<option value="'.$singleGrouping['name'].'" name="'.$dropDownValue.'">'.$singleGrouping['name'].'</option>';
															}
														echo '</select>';	
													echo '</td>';
												echo '</tr>';			
											break;
										}
										$num++;
									}
									
								break;	
									
								// div flavor
								case '1':
									// loop over each interest group returned
									foreach($interest_groups as $interest_group) {
											// if the interest group label is set to '' on the settings page
											if ( $this->optionVal['interest-group-label'] == '' ) {
												echo '<b class="yks_mc_interest_group_text">'.$interest_group['name'].'</b>'; // display the interest group name from MailChimp
											} else { 
												echo '<b class="yks_mc_interest_group_text">'.$this->optionVal['interest-group-label'].'</b>'; // else display the custom name set in the settings page
											}
											?>
											<!-- pass interest group data in a hidden form field , required to pass the data back to the correct interest-group -->
											<input type='hidden' name='interest-group-data' value='<?php echo $this->optionVal["interest-groups"]; ?>' />
											<?php
											// get form type
											$list_form_type = $interest_group['form_field'];
											$interestGroupID = $interest_group['id'];

											switch($list_form_type)
											{
												
											// checkbox interest groups
											case 'checkboxes':
												echo '<div class="yks_mc_interest_group_holder">';
													foreach ($interest_group['groups'] as $singleGrouping) {
														$checkboxValue = $interest_group['name'];
														echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="checkbox" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_checkbox" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'[]" value="'.$singleGrouping['name'].'">'.$singleGrouping['name'].'</label>';
													}
												echo '</div>';					
											break;
												
											// radiobuttons interest groups									
											case 'radio':
												echo '<div class="yks_mc_interest_group_holder">';
													echo '<div class="yks_mc_interest_radio_button_holder">';
														foreach ($interest_group['groups'] as $singleGrouping) {
															$radioValue = $interest_group['name'];
															echo '<label class="yks_mc_interest_group_label" for="'.$singleGrouping['name'].'"><input type="radio" id="'.$singleGrouping['name'].'" class="yikes_mc_interest_group_radio" name="'.$interest_group['form_field'].'-'.$interest_group['id'].'" value="'.$singleGrouping['name'].'">'.$singleGrouping['name'].'</label>';
														}
													echo '</div>';	
												echo '</div>';	
											break;
												
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
											break;
										}
										$num++;
									}
								break;
								
					}
				}
			} catch( Exception $e ) {
				return;
			}
		return false;
	}	
// Send a call to the MailChimp API to retreive all lists on the account		
public function getLists()
	{
	$api	= new wpyksMCAPI($this->optionVal['api-key']);
	$lists	= $this->getListsData();
	$listArr	= (!isset($listArr) ? $this->optionVal['lists'] : $listArr);
	$theusedlist = array();
	if(count($listArr) > 0)
		{
		foreach($listArr as $list)
			{
				$theusedlist[] = $list['id'];
			}
		}
	if($lists)
		{
		echo "<select id='yks-list-select' name='yks-list-select'>";
		echo "<option value=''> Select List</option>";
		
		foreach ($lists as  $lkey => $lvalue)
			{
				if (!in_array($lkey, $theusedlist))
					{
						echo "<option value='".$lkey."'>".$lvalue."</option>";		
					}
			}
		echo "</select>";
		}
	return false;
	}	
// Get lists for the settings page
// Used for default subscription list		
public function getOptionsLists()
	{
	$api	= new wpyksMCAPI($this->optionVal['api-key']);
	$lists	= $this->getListsData();
	$listArr	= (!isset($listArr) ? $this->optionVal['lists'] : $listArr);
	if($lists)
		{
		echo "<select id='yks-mailchimp-optIn-default-list' name='yks-mailchimp-optIn-default-list'>";
		echo "<option value='select_list'> Select List</option>";
		foreach ($lists as  $lkey => $list_name)
			{
				echo "<option ".selected( isset($this->optionVal['yks-mailchimp-optIn-default-list']) ? $this->optionVal['yks-mailchimp-optIn-default-list'] : "select_list", $lkey )." value='".$lkey."'>".$list_name."</option>";		
			}
		echo "</select>";
		}
	return false;
	}	
// Send a call to MailChimp API to get the data associated with a specific list (in this instance: the fields, and the subscriber count)	
public function getListsData()
	{
	/*
	$theListItems = get_transient('yks-mcp-listdata-retrieved');
	if (!$theListItems)
		{
	*/	
		$api	= new wpyksMCAPI($this->optionVal['api-key']);
		$lists	= $api->call('lists/list', array( 'limit' => 100 ));
		if($lists)
			{
			foreach ($lists['data'] as $list)
				{
					$theListItems[$list['id']] =  $list['name'];	
					$theListItems['subscriber-count']['subscriber-count-'.$list['id']] = $list['stats']['member_count'];
				}
				
			}
			/* set_transient( 'yks-mcp-listdata-retrieved', $theListItems, 60/60 ); //cache lists for 15 seconds for testing, originally 5 mins 60*5 */
		/* } */
	return $theListItems;
	}	
// Sort through the returned data	
public function sortList($p)
	{
	if(empty($p['update_string'])
	|| empty($p['list_id'])) return false;
	else
		{
		// Setup fields
		$a  = explode(';', $p['update_string']);
		if($a !== false)
			{
			foreach($a as $f)
				{
				$d  = explode(':', $f);
				$this->optionVal['lists'][$p['list_id']]['fields'][$d[0]]['sort']	= $d[1];
				}
			}
		uasort($this->optionVal['lists'][$p['list_id']]['fields'], array(&$this, 'sortListFields'));
		return update_option(YKSEME_OPTION, $this->optionVal);
		}
	return false;
	}
	
private function sortListfields($a,$b)
	{
	$a	= $a['sort'];
	$b	= $b['sort'];
  if($a == $b)
  	{
    return 0;
    }
  return ($a < $b) ? -1 : 1;
	}
	
// Update a single list on the lists page
// This function fires when the user clicks 'save settings' for a specific form on the lists page	
public function updateList($p)
	{
	if(!empty($p['form_data']))
		{
		parse_str($p['form_data'], $fd);
		if(!empty($fd['yks-mailchimp-unique-id']))
			{
			$num = 1;
			foreach($this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'] as $k => $v)
				{
				// Only proceed if the field is  not locked
				if($v['require'] == 0)
					{
					// Make sure this field was included in the update
					$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['active'] = (isset($fd[$v['name']]) ? '1' : '0');
					}
					// $name	= $this->slugify($field['label'].'-'.$field['tag']);
					$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['placeholder-'.$fd['yks-mailchimp-unique-id'].'-'.$num] = $fd['placeholder-'.$fd['yks-mailchimp-unique-id'].'-'.$num];
					
					$num++;
					
					$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['yks_mailchimp_redirect_'.$fd['yks-mailchimp-unique-id']] = $fd['yks_mailchimp_redirect_'.$fd['yks-mailchimp-unique-id']];
				
					if(isset($fd['yks_mailchimp_redirect_'.$fd['yks-mailchimp-unique-id']])) {
						$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['page_id_'.$fd['yks-mailchimp-unique-id']] = $fd['page_id_'.$fd['yks-mailchimp-unique-id']];
					}
				
				}
			return update_option(YKSEME_OPTION, $this->optionVal);
			}
		}
	return false;
	}
// Remove a list from the lists page
// Runs when a user clicks 'delete list' on the lists page	
public function deleteList($i=false)
	{
	if($i == false) return false;
	else
		{
		unset($this->optionVal['lists'][$i]);
		update_option('imported_lists', $this->optionVal['lists']);
		return update_option(YKSEME_OPTION, $this->optionVal);
		}
	}
// Import a list to the lists page
// Runs when a user adds a list from the drop down on the lists page	
public function importList($i=false)
	{
	if($i == false) return false;
	else
		{
		// create our variables
		$lid	= $this->optionVal['lists'][$i]['list-id'];
		$name	= $this->optionVal['lists'][$i]['name'];
		$api	= new wpyksMCAPI($this->optionVal['api-key']);
		$mv	= $api->call('lists/merge-vars', array(
 				'id' => array( $lid )
			)
		);
		// if merge variables are returned
		if($mv)
			{
			$mv	= $this->getImportedFieldsArray($lid, $mv);
			if($mv)
				{
				// Save the new list
				$this->optionVal['lists'][$i]['fields']	= $mv;
				if(update_option(YKSEME_OPTION, $this->optionVal))
					{
					return $this->generateListContainers(array($this->optionVal['lists'][$i]));
					}
				}
			}
		}
	return false;
	}
// Make a call to the MailChimp API to retrieve all subscribers to a given list
// Runs when the user clicks 'view' next to the subscriber count on the list page
public function listAllSubscribers($lid, $list_name) {
		$api	= new wpyksMCAPI($this->optionVal['api-key']);
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
		if($subscribers_list['total'] > 0) {
			?>
				<h2><?php echo $list_name; echo '   <span class="subscriber-count" style="font-size:11px;">(<span class="number">'.$subscribers_list['total'].'</span> subscribers)</span>'; ?></h2>
				<p><?php _e('Click on a subscriber to see further information','yikes-inc-easy-mailchimp-extender'); ?></p>
				
				<table id="yikes-mailchimp-subscribers-table" class="yks-mailchimp-fields-list" style="width:100%;">
					<thead class="yikes-mailchimp-subscribers-table-head">
						<tr>
							<th width="50%"><?php _e('E-Mail','yikes-inc-easy-mailchimp-extender'); ?></th>
							<th width="50%"><?php _e('Date Subscribed','yikes-inc-easy-mailchimp-extender'); ?></th>
						</tr>
					</thead>
					<?php
						foreach ( $subscribers_list['data'] as $subscriber  ) {
							$timeStamp = explode(' ', $subscriber['timestamp_opt'] );
							echo '<tr class="yks-mailchimp-subscribers-list-row" id="yks-mailchimp-subscribers-list-row">';
								echo '<td><a class="subscriber-mail-link" rel="'.$subscriber["email"].'" class="subscriberEmailAddress">'.$subscriber['email'].'</a></td>';
								// echo '<td>'.str_replace('-', ' ', date("M-jS-Y", strtotime($subscriber['timestamp_opt']))).'</td>';
								echo '<td>'.str_replace('-', ' ', date("M-jS-Y", strtotime($timeStamp[0]))).'</td></tr>';
						}
					?>
				</table>

				<!-- display a single user profile in this div -->
				<div id="individual_subscriber_information" style="display:none;"></div>
			<?php
		} else { // else display an error of sorts
			?>
			<h2><?php echo $list_name; echo '   <span class="subscriber-count" style="font-size:11px;">(<span class="number">0</span> subscribers)</span>'; ?></h2>
			<?php
			_e('Sorry You Don\'t Currently Have Any Subscribers In This List!','yikes-inc-easy-mailchimp-extender');
		}
		wp_die();
}

// Make a call to the MailChimp API to retrieve information about a specific user
// Runs when the user clicks a subscribers email address
public function getSubscriberInfo($lid, $email) {
		$api	= new wpyksMCAPI($this->optionVal['api-key']);
		$subscriber_info	= $api->call('lists/member-info', 
			array(
				'id'	=>	$lid,
				'emails'	=>	array(
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
			}
		?>	
			
			<div class="yks-mc-subscriber-top">
				<span class="button-primary dashicons dashicons-arrow-left-alt2 yks-mc-subscriber-go-back"><?php _e('Back to Subscriber List', 'yikes-inc-easy-mailchimp-extender'); ?></span>
					<h2 class="yks-mc-subscriber-header"><?php _e('Subscriber Details', 'yikes-inc-easy-mailchimp-extender'); ?> </h2>
					<!-- display users email address -->
					<h3 id="yks-subscriber-info-email-address"><?php echo $subscriber_data['email']; ?></h3>
					<!-- display member star rating pulled from MailChimp -->
					<?php echo $member_rating_stars; ?>
					<!-- user optin time and date -->
					<span class="yks-subscriber-info-optin-data">
						<?php echo 'Subscribed on : '.date('m/d/Y',strtotime($subscriber_data_info_optin_date)); ?><?php echo ' at '.date('g:i A',strtotime($subscriber_data_info_optin_time)); ?>
					</span>
			</div>
			
			<h2 class="yks-mc-subscriber-header"><?php _e('Overview', 'yikes-inc-easy-mailchimp-extender'); ?></h2>
				<div class="yks-mc-subscriber-overview">
				
					<div class="yks-mc-subscriber-left-container">		
					
						<label class="yks-mc-overview-title"><?php _e('First Name', 'yikes-inc-easy-mailchimp-extender'); ?></label>
						<p class="yks-mc-overview-info-value yks-mc-subscriber-firstName"><?php if(!empty($subscriber_data_merges['FNAME'])) { echo $subscriber_data_merges['FNAME']; } else { echo 'not provided'; } ?></p>
						
						<label class="yks-mc-overview-title"><?php _e('Last Updated', 'yikes-inc-easy-mailchimp-extender'); ?></label>
						<p class="yks-mc-overview-info-value"><?php echo date('m/d/Y',strtotime($subscriber_data_info_changed_date)); ?><?php echo ' at '.date('g:i A',strtotime($subscriber_data_info_changed_time)); ?></p>
					
						<label class="yks-mc-overview-title"><?php _e('Preferred Email Type', 'yikes-inc-easy-mailchimp-extender'); ?></label>
						<p class="yks-mc-overview-info-value"><?php if(!empty($subscriber_data['email_type'])) { echo $subscriber_data['email_type']; } else { echo 'No Preference.'; } ?></p>
						
					</div>

					<div class="yks-mc-subscriber-right-container">
					
						<label class="yks-mc-overview-title"><?php _e('Last Name', 'yikes-inc-easy-mailchimp-extender'); ?></label>
						<p class="yks-mc-overview-info-value yks-mc-subscriber-lastName"><?php if(!empty($subscriber_data_merges['LNAME'])) { echo $subscriber_data_merges['LNAME']; } else { echo 'not provided'; } ?></p>
											
						<label class="yks-mc-overview-title"><?php _e('Language', 'yikes-inc-easy-mailchimp-extender'); ?></label>
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
					// test $segment_count array
					// print_r($segment_count);
			?>
			<!-- returns true always, groupings gets stored, but segments do not // need to check if there are any segments stored in the groupings array -->			
			<h2 class="yks-mc-subscriber-header"><?php _e('Groups Subscribed To', 'yikes-inc-easy-mailchimp-extender'); ?></h2>	
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
						if(!empty($group_data['segments'])) {
						?>
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
				<h2 class="yks-mc-subscriber-header"><?php _e('Subscriber Notes', 'yikes-inc-easy-mailchimp-extender'); ?></h2>
					<?php
							foreach ( $subscriber_data['notes'] as $note ) {
							?>
								<textarea style="width:100%; height:100px; resize:none;" id="yks-mc-subscriber-notes" disabled><?php echo $note['note']; ?></textarea>
								<span style="font-size:11px; opacity:.75;"><?php _e('Written by', 'yikes-inc-easy-mailchimp-extender'); ?>: <?php echo $note['created_by_name']; ?> // <?php _e('Created on', 'yikes-inc-easy-mailchimp-extender'); ?>: <?php echo date('m/d/Y',strtotime($note['created'])); ?></span>
							<?php
						}	
						?>
			</div>
			<?php } ?>
			
			<!-- <h2>Var Dump</h2> -->
			<?php /* print_r($subscriber_info);  // testing the returned subscriber info data */ 
	
		} 	
	wp_die();
}

// Make a call to the MailChimp API to remove a specified user from a given list
// Runs when the user clicks the 'X' next to a subscriber when viewing all subscribers on the lists page
public function yks_removeSubscriber($lid, $user_email) {
	$api	= new wpyksMCAPI($this->optionVal['api-key']);
		$subscribers_list	= $api->call('lists/unsubscribe', array(
			'id'	=>	$lid,
			'email'	=>	array(	
				'email'	=>	$user_email
			)
		));
}

/***** SCRIPTS/STYLES
 ****************************************************************************************************/
public function addStyles()
	{
	// Register Styles
	wp_register_style('ykseme-css-base', 				YKSEME_URL.'css/style.ykseme.css', 											array(), '1.0.0', 'all');
	wp_register_style('jquery-datatables-pagination', 				YKSEME_URL.'css/jquery.dataTables.css', 											array(), '1.0.0', 'all');	
	// Enqueue Styles
	wp_enqueue_style('thickbox');
	wp_enqueue_style('ykseme-css-base');	
	wp_enqueue_style('jquery-datatables-pagination');
	}
	
public function addStyles_frontend()
	{
	// Register Styles
	wp_register_style('ykseme-css-base', 				YKSEME_URL.'css/style.ykseme.css', 											array(), '1.0.0', 'all');
	wp_register_style('ykseme-css-smoothness', 	'//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css', 			array(), '1.0.0', 'all');
	wp_register_style('ykseme-animate-css', 				YKSEME_URL.'css/animate.css', 											array(), '1.0.0', 'all');
	// Enqueue Styles
	wp_enqueue_style('ykseme-css-base');
	wp_enqueue_style('ykseme-css-smoothness');
	wp_enqueue_style('ykseme-animate-css');
	}
	
public function addScripts()
	{		
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('ykseme-base',				  		YKSEME_URL.'js/script.ykseme.js',											array('jquery'));
	wp_enqueue_script('jquery-datatables-pagination',				  		YKSEME_URL.'js/jquery.dataTables.js',											array('jquery'));
	}
	
	// redirect the user to the settings page on initial activation
	function yks_easy_mc_plugin_activation_redirect() {
		if (get_option('yks_easy_mc_plugin_do_activation_redirect', false)) {
			delete_option('yks_easy_mc_plugin_do_activation_redirect');
			// redirect to settings page
			wp_redirect(admin_url('/admin.php?page=yks-mailchimp-form'));
		}
	}
	
public function addScripts_frontend()
	{
		global $wp_scripts;
		
        $version ='1.9.0';
		// compare the registered version of jQuery with 1.9.0
        if ( ( version_compare( @$wp_scripts->registered['jquery']->ver, $version ) >= 0 ) && !is_admin() )
         {   
            wp_enqueue_script( 'jquery' );
        }
        else
        {	
			if ( !is_admin() && !$this->is_login_page() ) {
				// if its older, or non-existent, load the newest version from google CDN
				 wp_deregister_script('jquery');
				 wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js', false, $version );
				 wp_enqueue_script( 'jquery' );	
			}
        }
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');
	}


/***** SHORTCODES
 ****************************************************************************************************/
 // Function to process the shortcode provided by the plugin
 // $p is the data associated with the shortcode (ie: form id and submit button text)
public function processShortcode($p)
	{
	ob_start();
	if($this->optionVal['api-key'] != ''
	&& (is_array($this->optionVal['lists'][$p['id']]) && !empty($this->optionVal['lists'][$p['id']])))
		{
		// Setup this list
		$list		= $this->optionVal['lists'][$p['id']];
		$submit_text =  $p['submit_text'];
		// Which form are we on?
		if(!isset($this->currentLists[$p['id']]))
			$this->currentLists[$p['id']]	= 0;
		else
			$this->currentLists[$p['id']]++;
		$listCt	= $this->currentLists[$p['id']];
		// Add the count to the list vars
		$list['id']		= $listCt.'-'.$list['id'];
		if(count($list['fields']))
			{
			foreach($list['fields'] as $fieldId => $field)
				{
				$list['fields'][$fieldId]['id']		= $listCt.'-'.$field['id'];
				}
			}
		// List form
		include YKSEME_PATH.'templates/shortcode_form.php';
		}
		// if there is no api key entered, or it's an invalid api key
	else if ( $this->optionVal['api-key'] == '' || get_option( 'api_validation' ) == 'invalid_api_key' )
		{
		include YKSEME_PATH.'templates/shortcode_error_no_API_key.php';
		// else if the form was removed from the lists page
		} else {
			include YKSEME_PATH.'templates/shortcode_error_data.php';
		}
	$shortcode = ob_get_contents();
	ob_end_clean();
	return $shortcode;
	}
// Function to process the PHP snippet provided by the plugin
// Again the data passed in, is the shortcode/php snippet paramaters (form id, submit button text)	
public function processSnippet($list=false, $submit_text)
	{
	$p	= array(
			'id' => $list,
			'submit_text'	=> $submit_text
		);
	return $this->processShortcode($p);
	}


/***** ADMINISTRATION MENUS
 ****************************************************************************************************/
public function addAdministrationMenu()
	{
	// Top Level Menu
	add_menu_page( __('MailChimp Forms','yikes-inc-easy-mailchimp-extender'), __('MailChimp Forms','yikes-inc-easy-mailchimp-extender'), 'manage_options', 'yks-mailchimp-form', array(&$this, 'generatePageOptions'), 'dashicons-welcome-write-blog', 400);
	// Sub Items
	add_submenu_page('yks-mailchimp-form', __('MailChimp Forms','yikes-inc-easy-mailchimp-extender'), __('MailChimp Settings','yikes-inc-easy-mailchimp-extender'), 'manage_options', 'yks-mailchimp-form', array(&$this, 'generatePageOptions'));
	add_submenu_page('yks-mailchimp-form', __('Manage List Forms','yikes-inc-easy-mailchimp-extender'), __('Manage List Forms','yikes-inc-easy-mailchimp-extender'), 'manage_options', 'yks-mailchimp-form-lists', array(&$this, 'generatePageLists'));
	add_submenu_page('yks-mailchimp-form', __('About YIKES, Inc.','yikes-inc-easy-mailchimp-extender'), __('About YIKES, Inc.','yikes-inc-easy-mailchimp-extender'), 'manage_options', 'yks-mailchimp-about-yikes', array(&$this, 'generatePageAboutYikes'));
	}


/***** ADMINISTRATION PAGES
 ****************************************************************************************************/
public function generatePageOptions()
	{
	require_once YKSEME_PATH.'pages/options.php'; // include our options page
	}
public function generatePageLists()
	{
	require_once YKSEME_PATH.'pages/lists.php'; // include our lists page
	}
public function generatePageAboutYikes()
	{
	require_once YKSEME_PATH.'pages/about.php'; // include our about page
	}
public function registerMailChimpWidget()
	{	
	require_once YKSEME_PATH.'templates/yikes-mailchimp-widget.php'; // include our widget
	}		


/***** FORM DATA
 ****************************************************************************************************/
public function yks_resetPluginSettings() {
	// reset the plugin settings back to defaults
	$this->optionVal['api-key']	= '';
	$this->optionVal['flavor']	= '1';
	$this->optionVal['debug']	= '0';
	$this->optionVal['optin']	= 'true';
	$this->optionVal['single-optin-message']	= __('Thank You for subscribing!', 'yikes-inc-easy-mailchimp-extender');
	$this->optionVal['double-optin-message']	= __('Thank You for subscribing! Check your email for the confirmation message.', 'yikes-inc-easy-mailchimp-extender');
	$this->optionVal['interest-group-label']	= '';
	$this->optionVal['yks-mailchimp-optIn-checkbox']	= 'hide';
	$this->optionVal['yks-mailchimp-optIn-default-list']	= array();
	$this->optionVal['yks-mailchimp-optin-checkbox-text']	= 'SIGN ME UP!';
	update_option('api_validation' , 'invalid_api_key');
		return update_option(YKSEME_OPTION, $this->optionVal);
		
}
 
// Make a call to MailChimp API to validate the provided API key
// calls the helper/ping method, and returns true or false 
public function validateAPIkeySettings()
	{		
		// Create and store our variables to pass to MailChimp
		$apiKey = $_POST['api_key']; // api key
		$dataCenter = $_POST['data_center']; // data center (ie: us3)	
		$api	= new wpyksMCAPI($apiKey);
		// try the call, catch any errors that may be thrown
		try {
			$resp = $api->call('helper/ping', array('apikey' => $apiKey));
			echo $resp['msg'];
			$this->getOptionsLists();
		} catch( Exception $e ) {
			$errorMessage = str_replace('API call to helper/ping failed:', '', $e->getMessage());
			_e($errorMessage,'yikes-inc-easy-mailchimp-extender');
			update_option('api_validation', 'invalid_api_key');
		}
		// always die or it will always return 1
		wp_die();
 }
 
// Make a call to MailChimp API to add a new subscriber to a specified list
// Runs when a user fills out the form on the frontend of the site 
public function addUserToMailchimp($p)
	{
	if(!empty($p['form_data']))
		{
		parse_str($p['form_data'], $fd);
		if(!empty($fd['yks-mailchimp-list-id']))
			{
			// Create and store the variables needed to add a new subscriber
			$email	= false;
			$lid			= $fd['yks-mailchimp-list-id'];
			$api		= new wpyksMCAPI($this->optionVal['api-key']);
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
			$interest_group_option = json_decode($fd['interest-group-data'], true);
				
				// if interest groups exist, continue and form an array
				if ( $interest_group_option ) {
				
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
				$form_data = apply_filters( 'yikes_mc_get_form_data', $lid, $mv ); 
				$specific_form_data = apply_filters( 'yikes_mc_get_form_data_'.$lid, $lid, $mv ); 
				
				
				// try adding subscriber, catch any error thrown
				try {
					$retval = $api->call('lists/subscribe', array(
						  'id'              => $lid, // form id
						  'email'             => array( 'email' => $email ), // user email
						  'merge_vars'        => $mv, // merge variables (ie: fields and interest groups)
						  'double_optin'	=> $optin // double optin value (retreived from the settings page)
					));
					return "done";
				} catch( Exception $e ) { // catch any errors returned from MailChimp
					$errorCode = $e->getCode();
					if ( $errorCode = '214' ) {
						$errorMessage = str_replace('Click here to update your profile.', '', $e->getMessage());
						$error_catch = explode('to list', $errorMessage);
						echo $error_catch[0].'.';
						die();
					} else { 
						echo $e->getMessage();
						die();
					}
				}
					
			}
		}
	return __('One or more fields are empty','yikes-inc-easy-mailchimp-extender'); // return an error if your leaving any necessary fields empty
	}
	
	
// get our merge variables associated with a sepcific list	
// may not need any more
// maybe remove
/*
private function getFieldMergeVar($fn, $lid)
	{
	$mk	= '_YKS_UNKNOWN';
	switch($fn)
		{
		case 'yks-mailchimp-field-name-first':
		case 'yks-mailchimp-field-name-last':
		case 'yks-mailchimp-field-email':
		case 'yks-mailchimp-field-address':
			foreach($this->optionVal['lists'] as $lud => $list)
				{
				if($list['list-id'] == $lid)
					{
					foreach($this->optionVal['lists'][$lud]['fields'] as $fud => $field)
						{
						if($field['name'] == $fn)
							$mk = $field['merge'];
						}
					}
				}
			break;
		}
	return $mk;
	}
*/
	
// Generate the lists containers on the lists page
// This function gets any imported lists, and builds up the lists page	
public function generateListContainers($listArr=false)
	{
	$listArr	= ($listArr == false ? $this->optionVal['lists'] : $listArr);
	$thelistdata = $this->getListsData(); //Get list names from API
	// if there are any imported lists in the array	
	if(count($listArr) > 0)
		{
		ob_start();
		// loop over each lists and build the page
		foreach($listArr as $list)
			{
			
			$get_list_data = $this->getListsData();
			?>
			<div class="yks-list-container" id="yks-list-container_<?php echo $list['id']; ?>">
				<div class="yks-status" id="yks-status" style="display: none;">
					<div class="yks-success" style="padding:.25em;"><p>&nbsp;<?php _e('Your List Was Successfully Saved!','yikes-inc-easy-mailchimp-extender'); ?></p></div>
				</div>
				<div class="yks-status-error" id="yks-status-error" style="display: none;">
					<div class="yks-error" style="padding:.25em;"><p>&nbsp;<?php _e('Your settings were not saved (or you did not change them).','yikes-inc-easy-mailchimp-extender'); ?></p></div>
				</div>
				<span class="yikes-lists-error" style="display:none;"><?php _e('I\'m sorry there was an error with your request.','yikes-inc-easy-mailchimp-extender'); ?></span>
				<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
					<input type="hidden" name="yks-mailchimp-unique-id" id="yks-mailchimp-unique-id_<?php echo $list['id']; ?>" value="<?php echo $list['id']; ?>" />
					<table class="form-table  yks-admin-form">
						<tbody>
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key"><?php _e('MailChimp List name','yikes-inc-easy-mailchimp-extender'); ?></label></th>
								<td class="yks-mailchimp-listname"><?php
								if ($list['name'])
									{
										$thename = $list['name'];
										//echo $thename;
										printf( __( '%1$s', 'yikes-inc-easy-mailchimp-extender' ), $thename );
									}
								else
									{
										foreach ($thelistdata as $lkey => $lval)
											{
											if ($lkey == $list['id'])
												{
												$thename = $lval;
												//echo $thename;
												printf( __( '%1$s', 'yikes-inc-easy-mailchimp-extender' ), $thename );
												}
											}
									}
									?></td>
							</tr>	
								<!-- display the specific MailChimp list ID back to the user -->							
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key"><?php _e('MailChimp List ID','yikes-inc-easy-mailchimp-extender'); ?></label></th>
								<td><?php echo $list['list-id'];  ?>
								</td>
							</tr>				
							<!-- display the shortcode with the specific list ID -->
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key"><?php _e('Shortcode','yikes-inc-easy-mailchimp-extender'); ?></label></th>
								<td>
									[yks-mailchimp-list id="<?php echo $list['id']; ?>" submit_text="Submit"]
									<span class="description yks-margin-left"><?php _e('Paste this shortcode into whatever page or post you want to add this form to','yikes-inc-easy-mailchimp-extender'); ?></span>
								</td>
							</tr>
							<!-- display the PHP snippet with the specific list ID -->
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key"><?php _e('PHP Snippet','yikes-inc-easy-mailchimp-extender'); ?></label></th>
								<td>
									<?php echo htmlentities('<?php echo yksemeProcessSnippet(\''.$list['id'].'\', \'Submit\'); ?>'); ?>
									<span class="description yks-margin-left"><?php _e('Use this code to add this form to a template file','yikes-inc-easy-mailchimp-extender'); ?></span>
								</td>
							</tr>
							<!-- display subscriber count here -->
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key"><?php _e('Number of Subscribers','yikes-inc-easy-mailchimp-extender'); ?></label></th>
								<td>
									<!-- rel contains list id that we pass along to our function to ajax retreive all subscribers -->
									<span class="number-of-subscribers-<?php echo $list['id']; ?>"><?php echo $get_list_data['subscriber-count']['subscriber-count-'.$list['id']]; ?>&nbsp;</span><a href="#TB_inline?width=600&height=550&inlineId=yikes-mailchimp-subscribers-box" class="thickbox displayListSubscribers" rel="<?php echo $list['id']; ?>">View</a>	
								</td>
							</tr>
							<!-- display the forms fields, with options to customize -->
							<tr valign="top">
								<td scope="row">
									<label for="api-key"><strong><?php _e('Form Fields','yikes-inc-easy-mailchimp-extender'); ?></strong></label>
									<p class="description">
										<?php _e('Check the fields you want included in your form (Email Address is required).','yikes-inc-easy-mailchimp-extender'); ?>
									</p> 
									<p class="description">
										<?php _e('Use the green arrows to drag-and-drop the fields and rearrange their order.','yikes-inc-easy-mailchimp-extender'); ?>
										<span class="yks-mailchimp-sorthandle-img"></span>
									</p>
								</th>
								<td class="yks-mailchimp-fields-td" id="yks-mailchimp-fields-td_<?php echo $list['id']; ?>">
									<fieldset class="yks-mailchimp-fields-container" id="yks-mailchimp-fields-container_<?php echo $list['id']; ?>">
										<legend class="screen-reader-text"><span><?php _e('Active Fields','yikes-inc-easy-mailchimp-extender'); ?></span></legend>
										<div class="yks-mailchimp-fields-list" id="yks-mailchimp-fields-list_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
											
											<!-- create sortable rows populated with mailchimp data -->
											<?php 
											$num = 1;
											foreach($list['fields'] as $field) { ?>
											<div class="yks-mailchimp-fields-list-row">
												<label title="<?php echo $field['name']; ?>" rel="<?php echo $field['id']; ?>">
													<span class="yks-mailchimp-sorthandle"><?php _e('Drag','yikes-inc-easy-mailchimp-extender'); ?> &amp; <?php _e('drop','yikes-inc-easy-mailchimp-extender'); ?></span>
													<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo ($field['active'] == 1 ? 'checked="checked"' : ''); ?><?php echo ($field['require'] == 1 ? 'disabled="disabled"' : ''); ?> />
													&nbsp;
													<div class="yks-mailchimp-field-name"><?php echo $field['label']; ?></div>
												</label>
												<span class="yks-mailchimp-field-merge"><span class="description"><?php _e('Merge field','yikes-inc-easy-mailchimp-extender'); ?>:</span> &nbsp; *|<input type="text" name="<?php echo $field['name']; ?>-merge" id="<?php echo $field['id']; ?>-merge" value="<?php echo $field['merge']; ?>"<?php echo (($field['locked'] == 1 || $field['merge'] == false) ? ' disabled="disabled"' : ''); ?> />|*</span>
												<span class="yks-mailchimp-field-placeholder"><span class="description"><?php _e('Placeholder','yikes-inc-easy-mailchimp-extender'); ?>:</span> &nbsp; *|<input type="text" name="placeholder-<?php echo $list['id'].'-'.$num; ?>" id="<?php echo $field['id']; ?>-placeholder" placeholder="<?php echo $field['label']; ?>" value="<?php if(isset($field['placeholder-'.$list['id'].'-'.$num])) { echo $field['placeholder-'.$list['id'].'-'.$num]; } ?>" />|*</span>											
											</div>
											<?php 
											$num++;
											} ?>
										</div>
										<!-- display redirect checkbox here -->
										<tr valign="top">
											<th scope="row"><label for="yks-mailchimp-url-redirect"><?php _e('Redirect User On Submission','yikes-inc-easy-mailchimp-extender'); ?></label></th>
											<td>
												<span class="yks-mailchimp-redirect-checkbox-holder">
													<input type="checkbox" name="yks_mailchimp_redirect_<?php echo $list['id']; ?>" class="yks_mailchimp_redirect" id="yks-mailchimp-redirect-<?php echo $list['id']; ?>" value="1" <?php if(isset($field['yks_mailchimp_redirect_'.$list['id']])) { echo ($field['yks_mailchimp_redirect_'.$list['id']] == 1 ? 'checked="checked"' : ''); } ?> />
													<span class="description yks-margin-left"><?php _e('choose a page to redirect the user to after they submit the form.', 'yikes-inc-easy-mailchimp-extender'); ?></span>
														<!-- drop down of registered posts/pages -->
														<li id="pages" class="yks_mc_pages_dropdown_<?php echo $list['id']; ?>"  <?php if(!isset($field['yks_mailchimp_redirect_'.$list['id']])) { echo 'style="list-style:none;display:none;"'; } else { echo 'style="list-style:none;"'; } ?> >
															<h3><?php _e('Select A Post/Page', 'yikes-inc-easy-mailchimp-extender'); ?></h3>
																	<form action="<? bloginfo('url'); ?>" method="get">
																		 <select id="page_id" name="page_id_<?php echo $list['id']; ?>" >
																			 <?php
																				// set up variables for the queries
																				 global $post;
																				 global $page;
																				 $args_posts = array( 'numberposts' => -1);
																				 $args_pages = array(
																					'sort_order' => 'ASC',
																					'sort_column' => 'post_title',
																					'hierarchical' => 1,
																					'exclude' => '',
																					'include' => '',
																					'meta_key' => '',
																					'meta_value' => '',
																					'authors' => '',
																					'child_of' => 0,
																					'parent' => -1,
																					'exclude_tree' => '',
																					'number' => '',
																					'offset' => 0,
																					'post_type' => 'page',
																					'post_status' => 'publish'
																				); 
																				$pages = get_pages($args_pages);
																				// print_r($pages);
																				
																				 $posts = get_posts($args_posts);
																				// print_r($posts);
																				?>
																				<optgroup label="Posts"><?php
																				
																				// throwing error -> must resolve
																				 foreach( $posts as $post ) : setup_postdata($post); ?>
																						<option <?php if(isset($field['page_id_'.$list['id']])) { selected( $field['page_id_'.$list['id']], $post->ID ); } ?> value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
																				 <?php endforeach; ?>
																				 
																				 </optgroup>
																				 <optgroup label="Pages">
																				  <?php 
																				  foreach( $pages as $page ) : ?>
																						<option <?php if(isset($field['page_id_'.$list['id']])) { selected( $field['page_id_'.$list['id']], $page->ID ); } ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
																				 <?php endforeach; ?>
																				 </optgroup>
																		 </select>
																	 </form>
														</li>
												</span>														
											</td>
										</tr>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<input type="submit" name="submit" class="yks-mailchimp-list-update button-primary" value="<?php _e('Save Form Settings', 'yikes-inc-easy-mailchimp-extender'); ?>" rel="<?php echo $list['id']; ?>" />
									<input type="button" name="delete" class="yks-mailchimp-delete button-primary" value="<?php _e('Delete Form', 'yikes-inc-easy-mailchimp-extender'); ?>" rel="<?php echo $list['id']; ?>" data-title="<?php echo $thename; ?>" />
									<input type="button" name="import" class="yks-mailchimp-import button-primary" value="<?php _e('Re-Import Form Fields from MailChimp', 'yikes-inc-easy-mailchimp-extender'); ?>" rel="<?php echo $list['id']; ?>" />
								</td>
							</tr>
						</tbody>
					</table>
				
				</form>
				
				<!-- run loop to display content here -->
				<!-- thickbox for our hidden content, we will display subscribed peoples here based on which link is clicked -->
				<?php add_thickbox(); ?>
				<div id="yikes-mailchimp-subscribers-box" style="display:none;">
					<img class="mailChimp_get_subscribers_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" >
						<div class="yks_mc_subscribers">
						</div>
				</div>

			</div>
			<?php
			}
		}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
	}
// Generate our front end JavaScript , used to submit forms	
public function getFrontendFormJavascript($list='')
	{
	if($list === '') return false;
	$js	= false;
	foreach($list['fields'] as $field) : if($field['active'] == 1) :	
		// Setup JavaScript
		if($field['require'] == '1') :
		$prefix = "ymce";
			$js .= "\n";
			switch($field['type'])
				{
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
					$js .= "if($".$prefix."('#".$field['id']."').val() == '')
						{
						msg += '<li>Street Address'+'\\n</li>';
						err++;
						$".$prefixa."('#".$field['id']."').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
								$".$prefixa."(this).removeClass('shake animated');
								next();
							});
						} else {
							$".$prefixa."('#".$field['id']."').removeClass('yks_error_field_required')
						}	
					if($".$prefix."('#".$field['id']."-city').val() == '')
						{
						msg += '<li>City'+'\\n</li>';
						err++;
						$".$prefixa."('#".$field['id']."-city').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
								$".$prefixa."(this).removeClass('shake animated');
								next();
							});
						} else {
							$".$prefixa."('#".$field['id']."-city').removeClass('yks_error_field_required')
						}
					if($".$prefix."('#".$field['id']."-state').val() == '')
						{
						msg += '<li>State'+'\\n</li>';
						err++;
						$".$prefixa."('#".$field['id']."-state').addClass('yks_error_field_required shake animated').delay(800).queue(function(next){
								$".$prefixa."(this).removeClass('shake animated');
								next();
							});
						} else {
							$".$prefixa."('#".$field['id']."-state').removeClass('yks_error_field_required')
						}
					if($".$prefix."('#".$field['id']."-zip').val() == '')
						{
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
					$js .= 	"if($".$prefix."('.{".$field['name']."}:checked').length <= 0)
						{
						msg += '* {".$field['label']."}'+'\\n';
						err++;
						}";
					break;
				}
			$js .= "\n";
		endif;
	endif; endforeach;
	return $js;
	}
// Generate the form on the front end of the site
// this is what the user will see, and interact with	
public function getFrontendFormDisplay($list='', $submit_text)
	{
	if($list === '') return false;
	ob_start();	
	switch($this->optionVal['flavor'])
		{
		default:
		// Display the form inside of a table
		// if the user has selected table as their flavor on the settings page
		// make sure this matches exactly with the div flavor below (currently does not)
		case '0':
			?>
				<!-- BEGIN TABLE FLAVOR -->
				<table class="yks-mailchimpFormTable">
					<?php 
					$num = 1;				
					foreach($list['fields'] as $field) : if($field['active'] == 1) : 
					// get field placeholders
					$form_id = explode( '-', $field['id']);
					$field_placeholder_ = (isset($field['placeholder-'.$form_id[1].'-'.$num]) ? $field['placeholder-'.$form_id[1].'-'.$num] : '');
					echo '<input type="hidden" class="'.$field['name'].'_placeholder_value" value="'.$field_placeholder_.'">';
					?>
						<!-- javascript to populate the correct form fields, with the specified place-holder value, on the lists page -->
						<script>
							jQuery(document).ready(function() {	
								var hiddenInputClass = '<?php echo $field['name']; ?>';
								// alert('<?php echo $num; ?>');
								var hiddenInputClassValue = jQuery('.'+hiddenInputClass+'_placeholder_value').val();
								jQuery('input[name="'+hiddenInputClass+'"]').attr("placeholder", hiddenInputClassValue);
							});
						</script>
						<?php 
						if ($field['require'] == 1)  // if the field is required (set in MailChimp), display the red required star
							{ 
								$reqindicator 	= " <span class='yks-required-label'>*</span>";
								$reqlabel		= " yks-mailchimpFormTableRowLabel-required";
							}
						else  // else don't
							{
								$reqindicator  = "";
								$reqlabel		= "";
							}
							?>
						<tr class="yks-mailchimpFormTableRow">
							<td class="prompt yks-mailchimpFormTableRowLabel"><label class="prompt yks-mailchimpFormTableRowLabel<?php echo $reqlabel; ?>" for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?><?php echo $reqindicator; ?></label>
								<!-- run our function to generate the input fields for the form, passing in the field -->
								<?php echo $this->getFrontendFormDisplay_field($field); ?>
							</td>
						</tr>	
						<?php 
							$num++;
							endif; endforeach; 
						?>
					<tr class="yks-mailchimpFormTableRow">
						<!-- run our function to generate the interest group fields for the form, passing in the form id -->
						<?php echo $this->getInterestGroups($form_id[1]); ?>
						<td class="yks-mailchimpFormTableSubmit">
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
							var formID = '<?php echo $form_id[1] ?>';
							jQuery('#yks-mailchimp-form_0-'+formID).submit(function() {
								var i = 0;
								 setInterval(function(){
									if ( jQuery('.yks-success').is(':visible') ) {
										i++;
										if ( i == 1 ) {
											window.location.replace(formRedirectPage);	
										}
									}
								 }, 1000);
							});
						});
				</script>
				<?php
				}		
			break;
				
			// END TABLE FLAVOR	
				
				// Display the form inside of a div
				// if the user has selected div as their flavor on the settings page
				case '1':
			?>
			<div class="yks-mailchimpFormDiv">
				<?php 
				$num = 1;				
				foreach($list['fields'] as $field) : if($field['active'] == 1) : 
				// get field placeholders
				$form_id = explode( '-', $field['id']);
				$field_placeholder_ = (isset($field['placeholder-'.$form_id[1].'-'.$num]) ? $field['placeholder-'.$form_id[1].'-'.$num] : '');
				echo '<input type="hidden" class="'.$field['name'].'_placeholder_value" value="'.$field_placeholder_.'">';
				?>
					<!-- javascript to populate the correct form fields, with the specified place-holder value, on the lists page -->
					<script>
						jQuery(document).ready(function() {	
							var hiddenInputClass = '<?php echo $field['name']; ?>';
							// alert('<?php echo $num; ?>');
							var hiddenInputClassValue = jQuery('.'+hiddenInputClass+'_placeholder_value').val();
							jQuery('input[name="'+hiddenInputClass+'"]').attr("placeholder", hiddenInputClassValue);
						});
					</script>
					<?php 
					if ($field['require'] == 1)  // if the field is required (set in MailChimp), display the red required star
						{ 
							$reqindicator 	= " <span class='yks-required-label'>*</span>";
							$reqlabel		= " yks-mailchimpFormDivRowLabel-required";
						}
					else  // else don't
						{
							$reqindicator  = "";
							$reqlabel		= "";
						}
						?>
					<div class="yks-mailchimpFormDivRow">
						<label class="prompt yks-mailchimpFormDivRowLabel<?php echo $reqlabel; ?>" for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?><?php echo $reqindicator; ?></label>
						<div class="yks-mailchimpFormDivRowField">
							<!-- run our function to generate the input fields for the form, passing in the field -->
							<?php echo $this->getFrontendFormDisplay_field($field); ?>
						</div>
					</div>	
					<?php 
						$num++;
						endif; endforeach; 
					?>
				<div class="yks-mailchimpFormDivRow">
					<!-- run our function to generate the interest group fields for the form, passing in the form id -->
					<?php $this->getInterestGroups($form_id[1]); ?>
					<div class="yks-mailchimpFormDivSubmit">
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
							var formID = '<?php echo $form_id[1] ?>';
							jQuery('#yks-mailchimp-form_0-'+formID).submit(function() {
								var i = 0;
								 setInterval(function(){
									if ( jQuery('.yks-success').is(':visible') ) {
										i++;
										if ( i == 1 ) {
											window.location.replace(formRedirectPage);	
										}
									}
								 }, 1000);	
							});
						});
				</script>
				<?php
				}		
			break;
		}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
	}

// Generate the input fields for the form on the front end of the site	
// based on the $field['type'] that is returned from MailChimp
private function getFrontendFormDisplay_field($field=false)
	{
	if($field === false) return false;
	$o = '';
	$num = 1;
	$fieldID = $field['id'];
	
	switch($field['type'])
		{
		default:
		case 'email':
		case 'text':
		case 'number':
		case 'zipcode':
		case 'phone':
		case 'website':
		case 'imageurl':
		// custom placeholder value goes here
			$o	.= '<input type="text" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'" value="" />';
			$num++;
			break;
		case 'dropdown':
			$o	.= '<select name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'">';
				if(count($field['choices']) > 0) : foreach($field['choices'] as $ok => $ov) :
						$o	.= '<option value="'.htmlentities($ov, ENT_QUOTES).'">'.$ov.'</option>';
				endforeach; endif;
			$o	.= '</select>';
			break;
		case 'address':
			
			$o	.= '<input type="text" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'" value="" /><span class="yks-mailchimp-form-tooltip">Street Address</span>';
			$o	.= '<input type="text" name="'.$field['name'].'-add2" class="'.$field['name'].'-add2'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-add2" value="" /><span class="yks-mailchimp-form-tooltip">Apt/Suite</span>';
			$o	.= '<input type="text" name="'.$field['name'].'-city" class="'.$field['name'].'-city'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-city" value="" /><span class="yks-mailchimp-form-tooltip">City</span>';
			$o	.= '<input type="text" name="'.$field['name'].'-state" class="'.$field['name'].'-state'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-state" value="" /><span class="yks-mailchimp-form-tooltip">State</span>';
			$o	.= '<input type="text" name="'.$field['name'].'-zip" class="'.$field['name'].'-zip'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-zip" value="" /><span class="yks-mailchimp-form-tooltip">Zip</span>';
			break;
		case 'radio':
			if(count($field['choices']) > 0) : $ct=0; foreach($field['choices'] as $ok => $ov) :
				$ct++;
				$o	.= '<input type="radio" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-'.$ok.'" value="'.htmlentities($ov, ENT_QUOTES).'" />'.$ov;
				if($ct < count($field['choices']))
					$o	.= '<br />';
			endforeach; endif;
			break;
		case 'date':
		case 'birthday':
			$o	.= '<input type="text" name="'.$field['name'].'" class="'.$field['name'].' yks-field-type-date'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'" value="" />';
			break;
		}
	return $o;
	}


/***** DROPDOWNS
 ****************************************************************************************************/
 // not sure we need these any more
 // generate some dropdowns (not sure where)
public function generateDropdown($name, $html='', $sel='', $type=false, $zopt=false)
	{
	switch($type)
		{
		case 'yes_no':
			return yksemeBase::yesNoDropdown($name, $html, $sel);
			break;
		case 'and_or':
			return yksemeBase::andOrDropdown($name, $html, $sel);
			break;
		case 'wpsc_products':
			return yksemeBase::wpscProductDropdown($name, $html, $sel, $zopt);
			break;
			
		default:
			break;
		}
	}
private function yesNoDropdown($name, $html, $sel)
	{
	// Options
	$ddo	= '<option value="0"'.($sel == '0' ? ' selected' : '').'>No</option>';
	$ddo	.= '<option value="1"'.($sel == '1' ? ' selected' : '').'>Yes</option>';
	// Dropdown
	$dd	= '<select name="'.$name.'" id="'.$name.'"'.(!empty($html) ? ' '.$html : '').'>'.$ddo.'</select>';
	return $dd;
	}
private function andOrDropdown($name, $html, $sel)
	{
	// Options
	$ddo	= '<option value="0"'.($sel != '0' ? ' selected' : '').'>Or</option>';
	$ddo	.= '<option value="1"'.($sel == '1' ? ' selected' : '').'>And</option>';
	// Dropdown
	$dd	= '<select name="'.$name.'" id="'.$name.'"'.(!empty($html) ? ' '.$html : '').'>'.$ddo.'</select>';
	return $dd;
	}


/***** UPDATES
 ****************************************************************************************************/
public function runUpdateTasks()
	{
	$currentVersion	= (!isset($this->optionVal['version']) || empty($this->optionVal['version']) ? '1.1.0' : $this->optionVal['version']);
	$latestVersion	= YKSEME_VERSION_CURRENT;
	if($currentVersion < $latestVersion)
		{
		$updateFunction	= 'runUpdateTasks_'.str_replace('.', '_', $currentVersion);
		if(!method_exists($this, $updateFunction)) return false;
		else
			{
			if(call_user_func(array(&$this, $updateFunction)))
				{
				update_option(YKSEME_OPTION, $this->optionVal);
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
private function runUpdateTasks_1_1_0()
	{
	if($this->optionVal['lists'])
		{
		foreach($this->optionVal['lists'] as $lid => $list)
			{
			foreach($list['fields'] as $fid => $field)
				{
				switch($field['name'])
					{
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
private function runUpdateTasks_1_2_0()
	{
	if($this->optionVal['lists'])
		{
		foreach($this->optionVal['lists'] as $lid => $list)
			{
			$fct = 1;
			foreach($list['fields'] as $fid => $field)
				{
				switch($field['name'])
					{
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
 * 1.3.0 => 2.0.0
 */
private function runUpdateTasks_1_3_0()
	{
	$this->optionVal['flavor']	= '0';
	$this->optionVal['debug']	= '0';
	if($this->optionVal['lists'])
		{
		foreach($this->optionVal['lists'] as $uid => $list)
			{
			unset($this->optionVal['lists'][$uid]);
			$this->addList($list['list-id']);
			}
		}
	$this->optionVal['version']	= '2.2.1';
	return true;
	}


		//
		// Adding Opt-In Checkbox to comment forms
		// To Do : Add setting to show/hide checkbox on frontend
		//
		// display a checkbox in the comment form
		// submit the user to mailchimp on a successful comment submission
		function ymc_add_meta_settings($comment_id) {
			  add_comment_meta(
				$comment_id, 
				'mailchimp_subscribe', 
				$_POST['mailchimp_subscribe'], 
				true
			  );
		}

		function add_after_comment_form($arg) {
			$custom_text = trim($this->optionVal['yks-mailchimp-optin-checkbox-text']);
			if ( $custom_text == '' ) {
				$custom_text = __("Sign Me Up For MAILCHIMP-REPLACE-THIS-TEXT's Newsletter", "gettext");
			} else {
				$custom_text = $custom_text;
			}
			
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
						$api = new wpyksMCAPI($this->optionVal['api-key']);
						
						$apikey   = $yikes_api_key;
						$listid   = $this->optionVal['yks-mailchimp-optIn-default-list']; // Need to set up a default list to subscribe all users to
						$endpoint   = 'https://api.mailchimp.com';
						$optin	= $this->optionVal['optin'];
						
						// try adding subscriber, catch any error thrown
						try {
							$retval = $api->call('lists/subscribe', array(
								  'id'              => $listid, // form id
								 'email'	=>	array(	
										'email'	=>	$comment->comment_author_email
									),
								  'merge_vars'        => array( 
									'FNAME'	=>	$commenter_first_name,
									'LNAME'	=>	$commenter_last_name
								   ), 
								  'double_optin'	=> $optin, // double optin value (retreived from the settings page)
								  'update_existing' => true // used to avoid the error thrown when user is already subscribed
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
			if ( $this->optionVal['yks-mailchimp-optIn-checkbox'] == 1 ) {
				add_action('comment_post', array(&$this, 'ymc_add_meta_settings'), 10, 2);
				add_action('comment_approved_', array(&$this, 'ymc_subscription_add'), 60, 2);
				add_action('comment_post', array(&$this, 'ymc_subscription_add'));
				add_filter('gettext', array(&$this, 'yikes_mc_replace_this_text'));
				add_filter('comment_form_defaults', array(&$this, 'add_after_comment_form'));
			}
		}

		
		// Check if cURL is enabled at the server level
		// used on the options.php page
		public function yikes_curl_check() {
				if  (in_array  ('curl', get_loaded_extensions())) {
					return true;
				}
				else {
					return false;
				}
		}

		// check if php.ini exists in the site root
		function yks_check_if_php_ini_exists() {

			// get php ini path from
			// the actively loaded php ini file
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
			

		//
		//	Add TinyMCE Buttons to the TinyMCE Editor
		//	We'll use the button to place form shortcodes!
		//  NOTE: This only runs on 3.9 or greater -> due to tinyMCE 4.0
		//		
			// Custom TinyMCE Button to insert form shortcodes onto pages and posts
					function yks_mc_add_tinyMCE() {
						
							global $typenow;
							// only on Post Type: post and page
							if( ! in_array( $typenow, array( 'post', 'page' ) ) )
								return ;
						
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
							// var_dump( $buttons );
							return $buttons;
						
					}
					
					 /**
					 * Localize Script
					 * Pass our imported list data, to the JS file
					 * to build the dropdown list in the modal
					 */
					function yks_mc_js_admin_head() 
					{
						
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
						if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) )
							return true;
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
					*
					* Helper function when testing user submitted data
					* print_r($merge_variables) is returned
					*
					*/
					function yks_mc_dump_user_data( $form_ID, $merge_variables ) {
						echo '<strong>Form ID :</strong> '.$form_ID. '<br />';
						echo '<strong>Merge Variables :</strong><br />';
						print_r($merge_variables);
						echo '<br /><em style="color:rgb(238, 93, 93);">Form data has not been sent to MailChimp</em>';
						die(); // die to prevent data being sent over to MailChimp
					}
			
		}
	}
?>