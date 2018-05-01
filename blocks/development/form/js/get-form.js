export default function yikes_easy_forms_fetch_form( form_id ) {
	let data = {
		action: 'yikes_get_form',
		form_id: form_id,
		nonce: ez_forms_gb_data.fetch_form_nonce
	}

	let form = $.post( ez_forms_gb_data.ajax_url, data );

	return form;
}