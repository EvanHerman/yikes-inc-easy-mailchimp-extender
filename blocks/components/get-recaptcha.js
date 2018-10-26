export default function get_recaptcha() {

	let data = {
		action: 'yikes_get_recaptcha',
		nonce: ez_forms_gb_data.fetch_recaptcha_nonce
	}

	let recaptcha_data = $.post( ez_forms_gb_data.ajax_url, data );

	return recaptcha_data;
}