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
		// check if our option is already set
		if( get_option( 'api_validation' ) ) {
			return;
		} else {
			add_option('api_validation' , 'invalid_api_key');
		}
		
	}
public function deactivate()
	{
	// delete_option(YKSEME_OPTION);
	}
public function uninstall()
	{
	delete_option(YKSEME_OPTION);
	delete_option('api_validation');
	}

/***** INITIAL SETUP
 ****************************************************************************************************/
public function initialize()
	{
	// If it's not already set up, initialize our plugin session
	if(session_id() == '') session_start();
	if(!is_array($_SESSION[$this->sessName]))
	 {
	 $_SESSION[$this->sessName]	= array();
	 }
	// Add the CSS/JS files
	add_action('admin_print_styles',		array(&$this, 'addStyles'));
	add_action('admin_print_scripts',		array(&$this, 'addScripts'));
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
	}
public function createShortcodes()
	{
	add_shortcode('yks-mailchimp-list', array(&$this, 'processShortcode'));
	}
public function getOptionValue()
	{
	$defaultVals	= array(
									'version'	=> YKSEME_VERSION_CURRENT,
									'api-key'	=> '',
									'flavor'	=> '0',
									'debug'	=> '0',
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
	
	return $fields;
	}
	
public function getImportedFieldsArray($lid, $mv)
	{
		if(empty($mv)) {
			return false;
		} else {
			$fields = array();
			
			// Problem adding and storing fields
			// TODO
			foreach($mv['data'][0]['merge_vars'] as $field)
				{
				// Add Field
				$name	= $this->slugify($field['label'].'-'.$field['tag']);
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
								'defalt'	=> $field['default'],
								'choices'	=> (isset($field['choices']) ? $field['choices'] : '')
								);
				$fields[$addField['id']] = $addField;
				}
			return $fields;
		}
	}
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
public function updateOptions($p)
	{
	if(!empty($p['form_data']))
		{
		parse_str($p['form_data'], $fd);
		$this->optionVal['api-key']	= $fd['yks-mailchimp-api-key'];
		$this->optionVal['flavor']	= $fd['yks-mailchimp-flavor'];
		$this->optionVal['debug']	= $fd['yks-mailchimp-debug'];
		return update_option(YKSEME_OPTION, $this->optionVal);
		}
	return false;
	}
public function updateApiKey($k)
	{
	$this->optionVal['api-key']	= $k; 
	return update_option(YKSEME_OPTION, $this->optionVal);
	}
public function updateVersion($k)
	{
	$this->optionVal['version']	= $k; 
	return update_option(YKSEME_OPTION, $this->optionVal);
	}


/********Mailchimp Error Codes
*****************************************************************************************************/

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
 // update list/merge-vars to 2.0
 // now get the fields correct for 2.0
 // fields not populated correctly
public function addList($lid='' , $name='')
	{
	if($lid == '' || isset($this->optionVal['lists'][$list['id']])) return false;
	$api	= new wpyksMCAPI($this->optionVal['api-key']);
	
	// test
	// $fields = $api->call('lists/list', '');
	
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
		if(update_option(YKSEME_OPTION, $this->optionVal))
			{
			return $this->generateListContainers(array($list));
			}
		}
	return false;

	}
	
public function getLists()
	{
	$api	= new wpyksMCAPI($this->optionVal['api-key']);
	$lists	= $this->getListsData();
	$listArr	= ($listArr == false ? $this->optionVal['lists'] : $listArr);
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
	
public function getListsData()
	{
	$theListItems = get_transient('yks-mcp-listdata-retrieved');
	if (!$theListItems)
		{
		$api	= new wpyksMCAPI($this->optionVal['api-key']);
		$lists	= $api->call('lists/list', array( 'limit' => 100 ));
		if($lists)
			{
			foreach ($lists['data'] as $list)
				{
					$theListItems[$list['id']] =  $list['name'];			
				}
			}
			set_transient( 'yks-mcp-listdata-retrieved', $theListItems, 60/4 ); //cache lists for 15 seconds for testing, originally 5 mins 60*5 
		}
	return $theListItems;
	}	
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
public function updateList($p)
	{
	if(!empty($p['form_data']))
		{
		parse_str($p['form_data'], $fd);
		if(!empty($fd['yks-mailchimp-unique-id']))
			{
			foreach($this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'] as $k => $v)
				{
				// Only proceed if the field is  not locked
				if($v['require'] == 0)
					{
					// Make sure this field was included in the update
					$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'][$k]['active']	= (isset($fd[$v['name']]) ? '1' : '0');
					}
				}
			return update_option(YKSEME_OPTION, $this->optionVal);
			}
		}
	return false;
	}
	
public function deleteList($i=false)
	{
	if($i == false) return false;
	else
		{
		unset($this->optionVal['lists'][$i]);
		return update_option(YKSEME_OPTION, $this->optionVal);
		}
	}
public function importList($i=false)
	{
	if($i == false) return false;
	else
		{
		$lid	= $this->optionVal['lists'][$i]['list-id'];
		$name	= $this->optionVal['lists'][$i]['name'];
		$api	= new wpyksMCAPI($this->optionVal['api-key']);
		$mv	= $api->call('lists/merge-vars', array(
 				'id' => array( $lid )
			)
		);
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





/***** SCRIPTS/STYLES
 ****************************************************************************************************/
public function addStyles()
	{
	// Register Styles
	wp_register_style('ykseme-css-base', 				YKSEME_URL.'css/style.ykseme.css', 											array(), '1.0.0', 'all');
	// Enqueue Styles
	wp_enqueue_style('thickbox');
	wp_enqueue_style('ykseme-css-base');
	}
	
public function addStyles_frontend()
	{
	// Register Styles
	wp_register_style('ykseme-css-base', 				YKSEME_URL.'css/style.ykseme.css', 											array(), '1.0.0', 'all');
	wp_register_style('ykseme-css-smoothness', 	YKSEME_URL.'css/jquery-ui-1.10.4.smoothness.css', 			array(), '1.0.0', 'all');
	// Enqueue Styles
	wp_enqueue_style('ykseme-css-base');
	wp_enqueue_style('ykseme-css-smoothness');
	}
	
public function addScripts()
	{		
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('ykseme-base',				  		YKSEME_URL.'js/script.ykseme.js',											array('jquery'));
	}
	
public function addScripts_frontend()
	{
		global $wp_scripts;
        $version ='1.9.0';
        if ( ( version_compare( $wp_scripts -> registered[jquery] -> ver, $version ) >= 0 ) && jQuery && !is_admin() )
         {   
            wp_enqueue_script( 'jquery' );
        }
        else
        {	
			wp_deregister_script('jquery');
            wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js', false, $version );
            wp_enqueue_script( 'jquery' );		
        }
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');
	}



/***** SHORTCODES
 ****************************************************************************************************/
public function processShortcode($p)
	{
	ob_start();
	if($this->optionVal['api-key'] != ''
	&& (is_array($this->optionVal['lists'][$p['id']]) && !empty($this->optionVal['lists'][$p['id']]['list-id'])))
		{
		// Setup this list
		$list		= $this->optionVal['lists'][$p['id']];
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
	else
		{
		include YKSEME_PATH.'templates/shortcode_error_data.php';
		}
	$shortcode = ob_get_contents();
	ob_end_clean();
	return $shortcode;
	}
public function processSnippet($list=false)
	{
	$p	= array('id' => $list);
	return $this->processShortcode($p);
	}





/***** ADMINISTRATION MENUS
 ****************************************************************************************************/
public function addAdministrationMenu()
	{
	// Top Level Menu
	add_menu_page('MailChimp Forms', 'MailChimp Forms', 'manage_options', 'yks-mailchimp-form', array(&$this, 'generatePageOptions'), YKSEME_URL.'images/ykseme_16px.png', 400);
	// Sub Items
	add_submenu_page('yks-mailchimp-form', 'MailChimp Forms', 'MailChimp Settings', 'manage_options', 'yks-mailchimp-form', array(&$this, 'generatePageOptions'));
	add_submenu_page('yks-mailchimp-form', 'Manage List Forms', 'Manage List Forms', 'manage_options', 'yks-mailchimp-form-lists', array(&$this, 'generatePageLists'));
	add_submenu_page('yks-mailchimp-form', 'About YIKES, Inc.', 'About YIKES, Inc.', 'manage_options', 'yks-mailchimp-about-yikes', array(&$this, 'generatePageAboutYikes'));
	}





/***** ADMINISTRATION PAGES
 ****************************************************************************************************/
public function generatePageOptions()
	{
	require_once YKSEME_PATH.'pages/options.php';
	}
public function generatePageLists()
	{
	require_once YKSEME_PATH.'pages/lists.php';
	}
public function generatePageAboutYikes()
	{
	require_once YKSEME_PATH.'pages/about.php';
	}
	




/***** FORM DATA
 ****************************************************************************************************/	
 
public function validateAPIkeySettings()
	{		
		$apiKey = $_POST['api_key'];
		$dataCenter = $_POST['data_center'];	
		
		$api	= new wpyksMCAPI($apiKey);
		
		
		// if there is an error with the $resp variable
		// display the error
		
		// need to add an exception for mailchimp HTTP error
		// not sur ehow to yet.
		try {
			//First try getting our user numero uno
			$resp = $api->call('helper/ping', array('apikey' => $apiKey));
			echo $resp['msg'];
			update_option('api_validation', 'valid_api_key');
		} catch( Exception $e ) {
			$errorMessage = str_replace('API call to helper/ping failed:', '', $e->getMessage());
			echo $errorMessage;
			update_option('api_validation', 'invalid_api_key');
		}
		
		
		wp_die();
 }
 
public function addUserToMailchimp($p)
	{
	if(!empty($p['form_data']))
		{
		parse_str($p['form_data'], $fd);
		if(!empty($fd['yks-mailchimp-list-id']))
			{
			$email	= false;
			$lid		= $fd['yks-mailchimp-list-id'];
			$api		= new wpyksMCAPI($this->optionVal['api-key']);
			$mv 		= array();
			
			
			
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
			
			// If no email, fail
			$noemail = "The email address is blank";
			if($email === false) return $noemail;
			
			// By default this sends a confirmation email - you will not see new members
			// until the link contained in it is clicked!
			$retval = $api->call('lists/subscribe', array(
				  'id'              => $lid,
				  'email'             => array( 'email' => $email ),
				  'merge_vars'        => $mv
			));
			
			if($api->errorCode)
				{
				return $this->YksMCErrorCodes ($api->errorCode);
				}
			else return "done";
			}
		}
	return "One or more fields are empty";
	}
	
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
	
public function generateListContainers($listArr=false)
	{
	$listArr	= ($listArr == false ? $this->optionVal['lists'] : $listArr);
	$thelistdata = $this->getListsData(); //Get list names from API

	if(count($listArr) > 0)
		{
		
		ob_start();
		foreach($listArr as $list)
			{
			?>
			<div class="yks-list-container" id="yks-list-container_<?php echo $list['id']; ?>">
				<div class="yks-status" id="yks-status" style="display: none;">
					<div class="yks-success" style="padding:.25em;">Your List Was Successfully Saved!</div>
				</div>
				<span class="yikes-lists-error" style="display:none;">I'm sorry there was an error with your request.</span>
				<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
					<input type="hidden" name="yks-mailchimp-unique-id" id="yks-mailchimp-unique-id_<?php echo $list['id']; ?>" value="<?php echo $list['id']; ?>" />
					<table class="form-table  yks-admin-form">
						<tbody>
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key">MailChimp List name</label></th>
								<td class="yks-mailchimp-listname"><?php
								if ($list['name'])
									{
										$thename = $list['name'];
										echo $thename;
									}
								else
									{
										foreach ($thelistdata as $lkey => $lval)
											{
											if ($lkey == $list['id'])
												{
												$thename = $lval;
												echo $thename;
												}
											}
									}
									?></td>
							</tr>		
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key">MailChimp List ID</label></th>
								<td><?php echo $list['list-id'];  ?>
								</td>
							</tr>				
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key">Shortcode</label></th>
								<td>
									[yks-mailchimp-list id="<?php echo $list['id']; ?>"]
									<span class="description yks-margin-left">Paste this shortcode into whatever page or post you want to add this form to</span>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key">PHP Snippet</label></th>
								<td>
									<?php echo htmlentities('<?php echo yksemeProcessSnippet(\''.$list['id'].'\'); ?>'); ?>
									<span class="description yks-margin-left">Use this code to add this form to a template file</span>
								</td>
							</tr>
							<tr valign="top">
								<td scope="row">
									<label for="api-key"><strong>Form Fields</strong></label>
									<p class="description">
										Check the fields you want included in your form (Email Address is required).
									</p> 
									<p class="description">
										Use the green arrows to drag-and-drop the fields and rearrange their order.
										<span class="yks-mailchimp-sorthandle-img"></span>
									</p>
								</th>
								<td class="yks-mailchimp-fields-td" id="yks-mailchimp-fields-td_<?php echo $list['id']; ?>">
									<fieldset class="yks-mailchimp-fields-container" id="yks-mailchimp-fields-container_<?php echo $list['id']; ?>">
										<legend class="screen-reader-text"><span>Active Fields</span></legend>
										<div class="yks-mailchimp-fields-list" id="yks-mailchimp-fields-list_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
											
											<!-- create sortable rows populated with mailchimp data -->
											<?php foreach($list['fields'] as $field) { ?>
											
											<div class="yks-mailchimp-fields-list-row">
												<label title="<?php echo $field['name']; ?>" rel="<?php echo $field['id']; ?>">
													<span class="yks-mailchimp-sorthandle">Drag &amp; drop</span>
													<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo ($field['active'] == 1 ? 'checked="checked"' : ''); ?><?php echo ($field['require'] == 1 ? 'disabled="disabled"' : ''); ?> />
													&nbsp;
													<div class="yks-mailchimp-field-name"><?php echo $field['label']; ?></div>
												</label>
												<span class="yks-mailchimp-field-merge"><span class="description">Merge field:</span> &nbsp; *|<input type="text" name="<?php echo $field['name']; ?>-merge" id="<?php echo $field['id']; ?>-merge" value="<?php echo $field['merge']; ?>"<?php echo (($field['locked'] == 1 || $field['merge'] == false) ? ' disabled="disabled"' : ''); ?> />|*</span>
											</div>
											<?php } ?>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<input type="submit" name="submit" class="yks-mailchimp-list-update button-primary" value="Save Form Settings" rel="<?php echo $list['id']; ?>" />
									<input type="button" name="delete" class="yks-mailchimp-delete button-primary" value="Delete Form" rel="<?php echo $list['id']; ?>" data-title="<?php echo $thename; ?>" />
									<input type="button" name="import" class="yks-mailchimp-import button-primary" value="Re-Import Form Fields from MailChimp" rel="<?php echo $list['id']; ?>" />
								</td>
							</tr>
						</tbody>
					</table>
			
					
				</form>
			</div>
			<?php
			}
		}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
	}
	
public function getFrontendFormJavascript($list='')
	{
	if($list === '') return false;
	
	$js	= false;
	foreach($list['fields'] as $field) : if($field['active'] == 1) :	
		// Setup javascript
		if($field['require'] == '1') :
		$prefix = "$ymce";
			$js .= "\n";
			switch($field['type'])
				{
				default:
				$prefixa = "ymce";
					$js .= "if ($".$prefixa."('#{$field[id]}').val() == '')";
					$js .= <<<JSC

	{
	msg += '* {$field[label]}'+"\\n";
	err++;
	}
JSC;
					break;
				case 'address':
					$js .= <<<JSC
if($prefix('#{$field[id]}').val() == '')
	{
	msg += '* {$field[label]}: Street Address'+"\\n";
	err++;
	}
if($prefix('#{$field[id]}-city').val() == '')
	{
	msg += '* {$field[label]}: City'+"\\n";
	err++;
	}
if($prefix('#{$field[id]}-state').val() == '')
	{
	msg += '* {$field[label]}: State'+"\\n";
	err++;
	}
if($prefix('#{$field[id]}-zip').val() == '')
	{
	msg += '* {$field[label]}: Zip Code'+"\\n";
	err++;
	}
JSC;
					break;
				case 'radio':
					$js .= <<<JSC
if($prefix('.{$field[name]}:checked').length <= 0)
	{
	msg += '* {$field[label]}'+"\\n";
	err++;
	}
JSC;
					break;
				}
			$js .= "\n";
		endif;
	endif; endforeach;
	return $js;
	}
	
public function getFrontendFormDisplay($list='')
	{
	if($list === '') return false;
	ob_start();	
	switch($this->optionVal['flavor'])
		{
		default:
		case '0':
			?>
			<table class="yks-mailchimpFormTable">
				<tbody>
					<?php foreach($list['fields'] as $field) : if($field['active'] == 1) : ?>
					<?php 
					if ($field['require'] == 1) 
						{ 
							$reqindicator 	= " <span class='yks-required-label'>*</span>";
							$reqlabel		= " yks-mailchimpFormDivRowLabel-required";
						}
					else
						{
							$reqindicator  = "";
							$reqlabel		= "";
						}
						?>
					<tr class="yks-mailchimpFormTableRow">
						<td class="prompt yks-mailchimpFormTableRowLabel"><label class="yks-mailchimpFormTdLabel<?php echo $reqlabel; ?>" for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label><?php echo $reqindicator; ?></td>
						<td class="yks-mailchimpFormTableRowField">
							<?php echo $this->getFrontendFormDisplay_field($field); ?>
						</td>
					</tr>
					<?php endif; endforeach; ?>
					<tr>
						<td colspan="2" class="yks-mailchimpFormTableSubmit">
							<p>
								<input type="submit" class="ykfmc-submit" id="ykfmc-submit_<?php echo $list['id']; ?>" value="Submit" />
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
				break;
				
				case '1':
			?>
			<div class="yks-mailchimpFormDiv">
				<?php foreach($list['fields'] as $field) : if($field['active'] == 1) : ?>
					<?php 
					if ($field['require'] == 1) 
						{ 
							$reqindicator 	= " <span class='yks-required-label'>*</span>";
							$reqlabel		= " yks-mailchimpFormDivRowLabel-required";
						}
					else
						{
							$reqindicator  = "";
							$reqlabel		= "";
						}
						?>
					<div class="yks-mailchimpFormDivRow">
						<label class="prompt yks-mailchimpFormDivRowLabel<?php echo $reqlabel; ?>" for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?><?php echo $reqindicator; ?></label>
						<div class="yks-mailchimpFormDivRowField">
							<?php echo $this->getFrontendFormDisplay_field($field); ?>
						</div>
					</div>
				<?php endif; endforeach; ?>
				<div class="yks-mailchimpFormDivRow">
					<div class="yks-mailchimpFormDivSubmit">
						<p><input type="submit" class="ykfmc-submit" id="ykfmc-submit_<?php echo $list['id']; ?>" value="Submit" /></p>
					</div>
				</div>
			</div>
			<?php
			break;
		}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
	}

private function getFrontendFormDisplay_field($field=false)
	{
	if($field === false) return false;
	$o = '';
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
			$o	.= '<input type="text" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'" value="" />';
			break;
		case 'dropdown':
			$o	.= '<select name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'">';
				if(count($field['choices']) > 0) : foreach($field['choices'] as $ok => $ov) :
						$o	.= '<option value="'.htmlentities($ov, ENT_QUOTES).'">'.$ov.'</option>';
				endforeach; endif;
			$o	.= '</select>';
			break;
		case 'address':
			
			$o	.= '<input type="text" name="'.$field['name'].'" class="'.$field['name'].($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'" value="" /><span class="yks-mailchimp-form-tooltip">Street Address</span><br />';
			$o	.= '<input type="text" name="'.$field['name'].'-add2" class="'.$field['name'].'-add2'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-add2" value="" /><span class="yks-mailchimp-form-tooltip">Apt/Suite</span><br />';
			$o	.= '<input type="text" name="'.$field['name'].'-city" class="'.$field['name'].'-city'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-city" value="" /><span class="yks-mailchimp-form-tooltip">City</span><br />';
			$o	.= '<input type="text" name="'.$field['name'].'-state" class="'.$field['name'].'-state'.($field['require'] == 1 ? ' yks-require' : '').'" id="'.$field['id'].'-state" value="" /><span class="yks-mailchimp-form-tooltip">State</span><br />';
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
			
		
		
		
		
		}
	}
?>