<?php
/*
This page gets included from lib.ajax and then processes
the post. This page should never get called by itself.
*/
if(!empty($_POST)
&& isset($_POST['form_action']))
	{	
	switch($_POST['form_action'])
		{
		
		
		
default:
	echo '-1';
	break;
	
case 'update_options':
	$action	= $yksemeBase->updateOptions($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	
case 'update_recptcha_options':
	$action	= $yksemeBase->updateRecaptchaOptions($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;

case 'update_debug_options':
	$action	= $yksemeBase->updateDebugOptions($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;	

case 'list_add':
	$list	= $yksemeBase->addList($_POST['list_id'], $_POST['name']);
	if($list)
		{
		echo json_encode($list);
		}
	else echo '-1';
	break;

	
case 'list_update':
	$action	= $yksemeBase->updateList($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	
case 'list_sort':
	$action	= $yksemeBase->sortList($_POST);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
	
case 'list_delete':
	$action	= $yksemeBase->deleteList($_POST['id']);
	if($action)
		{
		echo '1';
		}
	else echo '-1';
	break;
case 'list_import':
	$list	= $yksemeBase->importList($_POST['id']);
	if($list)
		{
		echo json_encode($list);
		}
	else echo '-1';
	break;
case 'merge_variables_reImport':
	$mv	= $yksemeBase->reImportMergeVariables($_POST['id']);
	if($mv)
		{
		echo json_encode($mv);
		}
	else echo '-1';
	break;
	
case 'notice_hide':
	setcookie('yks-mailchimp-notice-hidden', '1', time()+60*60*24*30);
	echo '1';
	break;
	
case 'validate_api_key':
	$validate_action = $yksemeBase->validateAPIkeySettings($_POST);
	break;

case 'get_profile_details':
	$validate_action = $yksemeBase->getUserProfileDetails($_POST);
	break;
	
case 'yks_mc_reset_plugin_settings':
	$validate_action = $yksemeBase->yks_resetPluginSettings($_POST);
	break;
	
case 'yks_get_list_subscribers':
	$get_subscribers = $yksemeBase->listAllSubscribers($_POST['list_id'], $_POST['list_name']);
	echo '1';
	break;	
	
case 'yks_remove_subscriber':
	$get_subscribers = $yksemeBase->yks_removeSubscriber($_POST['list_id'], $_POST['user_email']);
	echo '1';
	break;	
	
case 'yks_get_subscriberInfo':
	$get_subscribers = $yksemeBase->getSubscriberInfo($_POST['list_id'], $_POST['user_email']);
	echo '1';
	break;	

case 'yks_get_chimp_chatter':
	$validate_action = $yksemeBase->getMailChimpChatter($_POST);
	break;
	
case 'yks_get_widget_chimp_chatter':
	$validate_action = $yksemeBase->getMailChimpChatterForWidget($_POST);
	break;	
	
case 'yks_get_growth_data':
	$validate_action = $yksemeBase->getListGrowthHistory($_POST);
	break;		
		
case 'yks_get_campaign_data':
	$validate_action = $yksemeBase->getCapmpaignData($_POST);
	break;	

case 'yks_get_specific_campaign_data':
	$validate_action = $yksemeBase->getSpecificCapmpaignData($_POST);
	break;	
	
case 'yks_get_specific_campaign_link_data':
	$validate_action = $yksemeBase->getCampaignLinkStats($_POST);
	break;	
	
case 'yks_get_piechart':
	$validate_action = $yksemeBase->getPieChart($_POST);
	echo '1';
	break;	
	
case 'yks_get_campaign_emailed_to':
	$validate_action = $yksemeBase->getCampaignEmailToTable($_POST);
	echo '1';
	break;	
	
case 'yks_get_campaign_links_geo_opens':
	$get_geo_opens = $yksemeBase->getGeoDataForCampaignOpenLinks($_POST);
	echo '1';
	break;		
	
case 'yks_get_campaign_opened_data':
	$get_geo_opens = $yksemeBase->getCampaignOpenedData($_POST);
	echo '1';
	break;		
	
case 'yks_get_bounced_email_data':
	$get_geo_opens = $yksemeBase->getCampaignBouncedEmailData($_POST);
	echo '1';
	break;
	
case 'yks_get_unsubscribed_email_data':
	$get_geo_opens = $yksemeBase->getCampaignUnsubscribeData($_POST);
	echo '1';
	break;			
	
case 'yks_mc_get_form_preview':
	$get_form_preview = $yksemeBase->getFormPreview($_POST['shortcode'],$_POST['form_title'],$_POST['form_bg_color'],$_POST['form_text_color'],$_POST['form_submit_button_color'],$_POST['form_submit_button_text_color'],$_POST['form_padding'],$_POST['form_width'],$_POST['form_alignment']);
	echo $get_form_preview;
	break;		
	
case 'yks_mc_get_custom_template_preview':
	$get_template_screenshot_preview = $yksemeBase->getTemplateScreenshot($_POST['template_name'] , $_POST['selected_form_screenshot'] , $_POST['template_path'] );
	echo $get_template_screenshot_preview;
	break;	
	
case 'copy_user_templates_to_theme':
	$create_user_mailchimp_boilerplate = $yksemeBase->copyUserTemplatesToUserTheme();
	break;		
	
case 'add_new_field_to_list':
	$create_user_mailchimp_boilerplate = $yksemeBase->addNewFieldToList( $_POST );
	break;		
	
case 'delete_new_list_field':
	$delete_field_from_list = $yksemeBase->deleteFieldFromList( $_POST['mailchimp_list_id'] , $_POST['merge_tag'] );
	break;	
	
case 'update_list_field':
	$update_list_field = $yksemeBase->updateListField( $_POST );
	break;		

case 'get_list_data':
	$get_list_data = $yksemeBase->getListDataRightMeow();
	break;
	
case 'generate_random_merge_var_name':
	$random_merge_var_name = $yksemeBase->randomMergeVarString();
	break;
	
case 'delete_interest_group_from_list':
	$random_merge_var_name = $yksemeBase->deleteInterestGroupFromList( $_POST['mailchimp_list_id'] , $_POST['interest_group_id'] );
	break;
	
case 'add_new_interest_group':
	$random_merge_var_name = $yksemeBase->createNewInterestGroup( $_POST );
	break;
	
case 'update_interest_group':
	$random_merge_var_name = $yksemeBase->updateInterestGroup( $_POST['mailchimp_list_id'] , $_POST['grouping_id'] , $_POST['previous_value'] , $_POST['new_value'] );
	break;
	
case 'update_interest_grouping_title':
	$random_merge_var_name = $yksemeBase->updateInterestGroupingTitle( $_POST['mailchimp_list_id'] , $_POST['grouping_id'] , $_POST['value'] );
	break;
	
case 'add_interest_group_option':
	$add_interest_group_option = $yksemeBase->addInterestGroupOption( $_POST['mailchimp_list_id'] , $_POST['group_name'] , $_POST['grouping_id'] );
	break;
	
case 'delete_interest_group_option':
	$random_merge_var_name = $yksemeBase->deleteInterestGroupOption( $_POST['mailchimp_list_id'] , $_POST['group_name'] , $_POST['grouping_id'] );
	break;
	
case 'get_interest_group_data':
	$list_interest_groups = $yksemeBase->getListInterestGroups( $_POST['mailchimp_list_id'] );
	break;

case 'get_specific_interest_group_data':
	$list_interest_groups = $yksemeBase->getSpecificInterestGroupData( $_POST['mailchimp_list_id'] , $_POST['mc_interest_group_id'] );
	break;
	
case 'clear_yks_mc_error_log':
	$clear_error_log = $yksemeBase->clearYksMCErrorLog();
	break;
	
case 'change_yikes_mc_interest_group_type':
	$change_interest_group_type = $yksemeBase->changeListInterestGroupType( $_POST['grouping_id'] , $_POST['value'] );
	break;
	
		}
	}
?>