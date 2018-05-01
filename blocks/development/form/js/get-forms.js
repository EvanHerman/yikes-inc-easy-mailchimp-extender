export default function yikes_easy_forms_fetch_forms() {

	let data = {
		action: 'yikes_get_forms',
		nonce: ez_forms_gb_data.fetch_forms_nonce,
	}

	let forms = $.post( ez_forms_gb_data.ajax_url, data );

	return forms;
}