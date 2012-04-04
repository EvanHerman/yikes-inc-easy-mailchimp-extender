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
	}
public function deactivate()
	{
	delete_option(YKSEME_OPTION);
	}
public function uninstall()
	{
	delete_option(YKSEME_OPTION);
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
						'id'			=> $lid.'-'.$name,
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
	if(empty($mv)) return false;
	$fields		= array();
	
	foreach($mv as $field)
		{
		// Add Field
		$name	= $this->slugify($field['label'].'-'.$field['tag']);
		$addField	= array(
							'id'			=> $lid.'-'.$name,
							'name'		=> $lid.'-'.$field['tag'],
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
		$fields[$addField['id']]	= $addField;
		}
		
	return $fields;
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





/***** LIST ACTIONS
 ****************************************************************************************************/
public function addList($lid='')
	{
	if($lid == '' || isset($this->optionVal['lists'][$list['id']])) return false;
	$api	= new wpyksMCAPI($this->optionVal['api-key']);
	$mv	= $api->listMergeVars($lid);
	if($mv)
		{
		$list	= array(
						'id'			=> $lid,
						'list-id'	=> $lid,
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
		$api	= new wpyksMCAPI($this->optionVal['api-key']);
		$mv	= $api->listMergeVars($lid);
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
	wp_register_style('ykseme-css-smoothness', 	YKSEME_URL.'css/jquery-ui-1.8.16.smoothness.css', 			array(), '1.0.0', 'all');
	// Enqueue Styles
	wp_enqueue_style('ykseme-css-base');
	wp_enqueue_style('ykseme-css-smoothness');
	}
	
public function addScripts()
	{
	wp_enqueue_script('jquery');
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('ykseme-base',				  		YKSEME_URL.'js/script.ykseme.js',											array('jquery'));
	}
	
public function addScripts_frontend()
	{
	wp_enqueue_script('jquery');
	// Everything else
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker',			YKSEME_URL.'js/jquery-ui-1.8.16.datepicker.min.js',		array('jquery'), '1.8.16');
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
	add_menu_page('Mailchimp Form', 'Mailchimp Form', 'manage_options', 'yks-mailchimp-form', array(&$this, 'generatePageOptions'), YKSEME_URL.'images/ykseme_16px.png', 400);
	// Sub Items
	add_submenu_page('yks-mailchimp-form', 'Manage Lists', 'Manage Lists', 'manage_options', 'yks-mailchimp-form-lists', array(&$this, 'generatePageLists'));
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
			if($email === false) return false;
			
			// By default this sends a confirmation email - you will not see new members
			// until the link contained in it is clicked!
			$retval = $api->listSubscribe($lid, $email, $mv);
		
			if($api->errorCode)
				{
				return false;
				}
			else return true;
			}
		}
	return false;
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
								<td><strong><?php echo $list['list-id']; ?></strong></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="api-key">Active Fields</label></th>
								<td class="yks-mailchimp-fields-td" id="yks-mailchimp-fields-td_<?php echo $list['id']; ?>">
									<fieldset class="yks-mailchimp-fields-container" id="yks-mailchimp-fields-container_<?php echo $list['id']; ?>">
										<legend class="screen-reader-text"><span>Active Fields</span></legend>
										<div class="yks-mailchimp-fields-list" id="yks-mailchimp-fields-list_<?php echo $list['id']; ?>" rel="<?php echo $list['id']; ?>">
											<?php foreach($list['fields'] as $field){ ?>
											<div class="yks-mailchimp-fields-list-row">
												<label title="<?php echo $field['name']; ?>" rel="<?php echo $field['id']; ?>">
													<span class="yks-mailchimp-sorthandle">Drag &amp; drop</span>
													<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo ($field['active'] == 1 ? 'checked="checked"' : ''); ?><?php echo ($field['require'] == 1 ? 'disabled="disabled"' : ''); ?> />
													&nbsp;
													<span class="yks-mailchimp-field-name"><?php echo $field['label']; ?></span>
												</label>
												<span class="yks-mailchimp-field-merge">*|<input type="text" name="<?php echo $field['name']; ?>-merge" id="<?php echo $field['id']; ?>-merge" value="<?php echo $field['merge']; ?>"<?php echo (($field['locked'] == 1 || $field['merge'] == false) ? ' disabled="disabled"' : ''); ?> />|*</span>
											</div>
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
						<input type="button" name="import" class="yks-mailchimp-import button-primary" value="Import List Data" rel="<?php echo $list['id']; ?>" />
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
	
public function getFrontendFormJavascript($list='')
	{
	if($list === '') return false;
	$js	= false;
	foreach($list['fields'] as $field) : if($field['active'] == 1) :	
		// Setup javascript
		if($field['require'] == '1') :
		
			$js .= "\n";
			switch($field['type'])
				{
				default:
					$js .= <<<JSC
if($('#{$field[id]}').val() == '')
	{
	msg += '* {$field[label]}'+"\\n";
	err++;
	}
JSC;
					break;
				case 'address':
					$js .= <<<JSC
if($('#{$field[id]}').val() == '')
	{
	msg += '* {$field[label]}: Street Address'+"\\n";
	err++;
	}
if($('#{$field[id]}-city').val() == '')
	{
	msg += '* {$field[label]}: City'+"\\n";
	err++;
	}
if($('#{$field[id]}-state').val() == '')
	{
	msg += '* {$field[label]}: State'+"\\n";
	err++;
	}
if($('#{$field[id]}-zip').val() == '')
	{
	msg += '* {$field[label]}: Zip Code'+"\\n";
	err++;
	}
JSC;
					break;
				case 'radio':
					$js .= <<<JSC
if($('.{$field[name]}:checked').length <= 0)
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
						<tr class="yks-mailchimpFormTableRow">
							<td class="prompt yks-mailchimpFormTableRowLabel"><?php echo $field['label']; ?></td>
							<td class="yks-mailchimpFormTableRowField">
								<?php echo $this->getFrontendFormDisplay_field($field); ?>
							</td>
						</tr>
					<?php endif; endforeach; ?>
					<tr>
						<td colspan="2" class="yks-mailchimpFormTableSubmit">
							<p><input type="submit" class="ykfmc-submit" id="ykfmc-submit_<?php echo $list['id']; ?>" value="Submit" /></p>
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
					<div class="yks-mailchimpFormDivRow">
						<label class="prompt yks-mailchimpFormDivRowLabel" for="<?php echo $field['name']; ?>"><?php echo $field['label']; ?></label>
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
	if($this->optionVal['lists'])
		{
		foreach($this->optionVal['lists'] as $uid => $list)
			{
			unset($this->optionVal['lists'][$uid]);
			$this->addList($list['list-id']);
			}
		}
	$this->optionVal['version']	= '2.0.0';
	return true;
	}
			
		
		
		
		
		}
	}
?>