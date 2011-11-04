<?php
if(!class_exists("yksemeBase"))
	{
  class yksemeBase
		{
		
		
		
/**
 *	Variables
 */
private	$error		    = false;
private	$errorMsg	    = '';
public	$sessName	    = 'ykseme';
public	$optionVal		= false;
public	$currentLists	= false;

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
	}
public function deactivate()
	{
	}
public function uninstall()
	{
	delete_option(YKSEME_OPTION);
	}





/***** INITIAL SETUP
 ****************************************************************************************************/
private function initialize()
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
	$this->currentLists	= array();
	}
public function createShortcodes()
	{
	add_shortcode('yks-mailchimp-list', array(&$this, 'processShortcode'));
	}
public function getOptionValue()
	{
	$defaultVals	= array(
									'api-key'	=> '',
									'lists'		=> array()
								);
	$ov	= get_option(YKSEME_OPTION, $defaultVals);
	$this->optionVal	= $ov;
	return $ov;
	}
	




/***** FUNCTIONS
 ****************************************************************************************************/
public function multidimensionalArraySearch($parents, $searched)
	{ 
  if(empty($searched) || empty($parents)) return false;
  foreach($parents as $key => $value)
  	{ 
    $exists	= true; 
    foreach($searched as $skey => $svalue)
    	$exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
    if($exists){ return $key; }
  } 
  return false; 
	}
public function getBlankFieldsArray()
	{
	$fields		= array();
	
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-name-first',
						'label'		=> 'First Name',
						'require'	=> '1',
						'active'	=> '1',
						'locked'	=> '1',
						'sort'		=> 0
						);
	$fields[$addField['id']]	= $addField;
	
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-name-last',
						'label'		=> 'Last Name',
						'require'	=> '1',
						'active'	=> '1',
						'locked'	=> '1',
						'sort'		=> 1
						);
	$fields[$addField['id']]	= $addField;
	
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-email',
						'label'		=> 'Email',
						'require'	=> '1',
						'active'	=> '1',
						'locked'	=> '1',
						'sort'		=> 2
						);
	$fields[$addField['id']]	= $addField;
	
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-address',
						'label'		=> 'Address',
						'require'	=> '0',
						'active'	=> '0',
						'locked'	=> '0',
						'sort'		=> 3
						);
	$fields[$addField['id']]	= $addField;
						
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-apt-suite',
						'label'		=> 'Apt/Suite',
						'require'	=> '0',
						'active'	=> '0',
						'locked'	=> '0',
						'sort'		=> 4
						);
	$fields[$addField['id']]	= $addField;
	
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-city',
						'label'		=> 'City',
						'require'	=> '0',
						'active'	=> '0',
						'locked'	=> '0',
						'sort'		=> 5
						);
	$fields[$addField['id']]	= $addField;
						
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-state',
						'label'		=> 'State',
						'require'	=> '0',
						'active'	=> '0',
						'locked'	=> '0',
						'sort'		=> 6
						);
	$fields[$addField['id']]	= $addField;
						
	// Add Field
	$addField	= array(
						'id'			=> uniqid(),
						'name'		=> 'yks-mailchimp-field-zip',
						'label'		=> 'Zip/Postal Code',
						'require'	=> '0',
						'active'	=> '0',
						'locked'	=> '0',
						'sort'		=> 7
						);
	$fields[$addField['id']]	= $addField;
	
	return $fields;
	}





/***** CONFIGURATION
 ****************************************************************************************************/
public function updateApiKey($k)
	{
	$this->optionVal['api-key']	= $k; 
	return update_option(YKSEME_OPTION, $this->optionVal);
	}





/***** LIST ACTIONS
 ****************************************************************************************************/
public function addList()
	{
	$list	= array(
					'id'			=> uniqid(),
					'list-id'	=> '',
					'fields'	=> $this->getBlankFieldsArray()
				);
	$this->optionVal['lists'][$list['id']]	= $list;
	if(update_option(YKSEME_OPTION, $this->optionVal))
		{
		return $this->generateListContainers(array($list));
		}
	else return false;
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
			$this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['list-id']	= $fd['yks-mailchimp-list-id'];
			foreach($this->optionVal['lists'][$fd['yks-mailchimp-unique-id']]['fields'] as $k => $v)
				{
				// Only proceed if the field is  not locked
				if($v['locked'] == 0)
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





/***** SCRIPTS/STYLES
 ****************************************************************************************************/
public function addStyles()
	{
	// Register Styles
	wp_register_style('ykseme-css-base', 				YKSEME_URL.'css/style.ykseme.css', 									array(), '1.0.0', 'all');
	// Enqueue Styles
	wp_enqueue_style('thickbox');
	wp_enqueue_style('ykseme-css-base');
	}
	
public function addStyles_frontend()
	{
	// Register Styles
	wp_register_style('ykseme-css-base', 				YKSEME_URL.'css/style.ykseme.css', 									array(), '1.0.0', 'all');
	// Enqueue Styles
	wp_enqueue_style('ykseme-css-base');
	}
	
public function addScripts()
	{
	wp_enqueue_script('jquery');
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('ykseme-base',				  		YKSEME_URL.'js/script.ykseme.js',										array('jquery'));
	}
	
public function addScripts_frontend()
	{
	wp_enqueue_script('jquery');
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	}





/***** SHORTCODES
 ****************************************************************************************************/
public function processShortcode($p)
	{
	ob_start();
	if($this->optionVal['api-key'] != ''
	&& (is_array($this->optionVal['lists'][$p['id']]) && !empty($this->optionVal['lists'][$p['id']]['list-id'])))
		{
		$list	= $this->optionVal['lists'][$p['id']];
		if(!in_array($list, $this->currentLists))
			{
			include YKSEME_PATH.'templates/shortcode_form.php';
			$this->currentLists[]	= $list;
			}
		else
			{
			require_once YKSEME_PATH.'templates/shortcode_error_exists.php';
			}
		}
	else
		{
		require_once YKSEME_PATH.'templates/shortcode_error_data.php';
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
	add_menu_page('Mailchimp Form', 'Mailchimp Form', 'manage_options', 'yks-mailchimp-form', array(&$this, 'generatePageOptions'), YKSEME_URL.'images/ykseme_16px.png', 400);
	// Sub Items
	add_submenu_page('yks-mailchimp-form', 'Manage Lists', 'Manage Lists', 'manage_options', 'yks-mailchimp-form-lists', array(&$this, 'generatePageLists'));
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
	




/***** FORM DATA
 ****************************************************************************************************/	
public function addUserToMailchimp($p)
	{
	if(!empty($p['form_data']))
		{
		parse_str($p['form_data'], $fd);
		if(!empty($fd['yks-mailchimp-list-id']))
			{
			$api	= new MCAPI($this->optionVal['api-key']);
			$mv = array();
			
			// First name
			if(isset($fd['yks-mailchimp-field-phone']))
				{
				$mv['FNAME']	= $fd['yks-mailchimp-field-name-first'];
				}
				
			// Last name
			if(isset($fd['yks-mailchimp-field-phone']))
				{
				$mv['LNAME']	= $fd['yks-mailchimp-field-name-last'];
				}
			
			// Address
			if(isset($fd['yks-mailchimp-field-address'])
			|| isset($fd['yks-mailchimp-field-apt-suite'])
			|| isset($fd['yks-mailchimp-field-city'])
			|| isset($fd['yks-mailchimp-field-state'])
			|| isset($fd['yks-mailchimp-field-zip']))
				{
				$mv['ADDR1']	= array(
												'addr1'=> $fd['yks-mailchimp-field-address'].(!empty($fd['yks-mailchimp-field-apt-suite']) ? ' '.$fd['yks-mailchimp-field-apt-suite'] : ''),
												'city'	=> $fd['yks-mailchimp-field-city'],
												'state'	=> $fd['yks-mailchimp-field-state'],
												'zip'		=> $fd['yks-mailchimp-field-zip']
											);
				}
				
			// Phone
			if(isset($fd['yks-mailchimp-field-phone']))
				{
				$mv['PHONE']	= $fd['yks-mailchimp-field-phone'];
				}
				

			
			// By default this sends a confirmation email - you will not see new members
			// until the link contained in it is clicked!
			$retval = $api->listSubscribe($fd['yks-mailchimp-list-id'], $fd['yks-mailchimp-field-email'], $mv);
		
			if($api->errorCode)
				{
				return false;
				}
			else return true;
			}
		}
	return false;
	}
	
public function generateListContainers($listArr=false)
	{
	$listArr	= ($listArr == false ? $this->optionVal['lists'] : $listArr);
	if(count($listArr) > 0)
		{
		ob_start();
		foreach($listArr as $list)
			{
			?>
			<div class="yks-list-container" id="yks-list-container_<?php echo $list['id']; ?>">
				<div class="yks-status" id="yks-status_<?php echo $list['id']; ?>"></div>
				<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
					<input type="hidden" name="yks-mailchimp-unique-id" id="yks-mailchimp-unique-id_<?php echo $list['id']; ?>" value="<?php echo $list['id']; ?>" />
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key">Shortcode</label></th>
								<td><strong>[yks-mailchimp-list id="<?php echo $list['id']; ?>"]</strong></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key">Snippet</label></th>
								<td><strong><?php echo htmlentities('<?php echo yksemeProcessSnippet(\''.$list['id'].'\'); ?>'); ?></strong></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="yks-mailchimp-api-key">Mailchimp List Id</label></th>
								<td><input name="yks-mailchimp-list-id" type="text" id="yks-mailchimp-list-id" value="<?php echo $list['list-id']; ?>" class="regular-text" /></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="api-key">Active Fields</label></th>
								<td class="yks-mailchimp-fields-td" id="yks-mailchimp-fields-td_<?php echo $list['id']; ?>">
									<fieldset class="yks-mailchimp-fields-container" id="yks-mailchimp-fields-container_<?php echo $list['id']; ?>">
										<legend class="screen-reader-text"><span>Active Fields</span></legend>
										<div class="yks-mailchimp-fields-list" id="yks-mailchimp-fields-list_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
											<?php foreach($list['fields'] as $field){ ?>
											<label title="<?php echo $field['name']; ?>" rel="<?php echo $field['id']; ?>">
												<span class="yks-mailchimp-sorthandle">Drag &amp; drop</span>
												<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo ($field['active'] == 1 ? 'checked="checked"' : ''); ?><?php echo ($field['locked'] == 1 ? 'disabled="disabled"' : ''); ?> />
												&nbsp;
												<span><?php echo $field['label']; ?></span>
											</label>
											<?php } ?>
										</div>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
			
					<p class="submit">
						<input type="submit" name="submit" class="yks-mailchimp-list-update button-primary" value="Update List Options" rel="<?php echo $list['id']; ?>" />
						<input type="button" name="delete" class="yks-mailchimp-delete button-primary" value="Delete List" rel="<?php echo $list['id']; ?>" />
					</p>
				</form>
			</div>
			<?php
			}
		}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
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
			
		
		
		
		
		}
	}
?>