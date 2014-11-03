<?php

/* Setup our Private Actions */
add_action('wp_ajax_yks_mailchimp_form', 'ykseme_ajaxActions');

/* Setup our Public actions */
add_action('wp_ajax_yks_mailchimp_form_submit', 'ykseme_publicAjaxActions');
add_action('wp_ajax_nopriv_yks_mailchimp_form_submit', 'ykseme_publicAjaxActions');

function ykseme_ajaxActions()
	{
		global $yksemeBase;
		require_once YKSEME_PATH.'process/ajax.php';
		exit;
	}
	
	
function ykseme_publicAjaxActions()
	{
		global $yksemeBase;
		require_once YKSEME_PATH.'process/public.ajax.php';
		exit;
	}
		
?>