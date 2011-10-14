<?php
add_action('wp_ajax_yks_mailchimp_form', 'yksmf_ajaxActions');
function yksmf_ajaxActions()
	{
	global $yksmfBase;
	require_once YKSMF_PATH.'process/ajax.php';
	exit;
	}
?>