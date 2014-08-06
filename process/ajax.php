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

case 'frontend_submit_form':
	$action	= $yksemeBase->addUserToMailchimp($_POST);
	if($action == "done")
		{
			echo '1';
		} else {
			echo $action;
		}
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
	
		}
	}
?>