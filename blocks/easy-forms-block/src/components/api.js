export default class EasyFormsAPI {

	constructor() {
		this.ajaxurl = window.ajaxurl;
	}

	get_api_key_status() {
		let data = {
			action: 'yikes_get_api_key_status',
			nonce: ez_forms_gb_data.get_api_key_status,
		}

		let status = jQuery.post( this.ajaxurl, data );

		return status;
	}

	get_recaptcha() {
		let data = {
			action: 'yikes_get_recaptcha',
			nonce: ez_forms_gb_data.fetch_recaptcha_nonce
		}

		let recaptcha_data = jQuery.post( this.ajaxurl, data );

		return recaptcha_data;
	}

	get_forms() {
		let data = {
			action: 'yikes_get_forms',
			nonce: ez_forms_gb_data.fetch_forms_nonce,
		}

		let forms = jQuery.post( this.ajaxurl, data );

		return forms;
	}

	get_form( form_id ) {
		let data = {
			action: 'yikes_get_form',
			form_id: form_id,
			nonce: ez_forms_gb_data.fetch_form_nonce
		}

		let form = jQuery.post( this.ajaxurl, data );

		return form;
	}
}
