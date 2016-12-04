<?php

/**
 * Chimp Chatter handler.
 *
 * Note that this expects a V2 API object.
 *
 * @deprecated
 *
 * @author Jeremy Pry
 * @since  %VERSION%
 */
class Yikes_Inc_Easy_MailChimp_API_Chimp_Chatter extends Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

	/**
	 * Our API object.
	 *
	 * @var Yikes_Inc_Easy_MailChimp_API
	 */
	protected $api;

	/**
	 * The base API path.
	 *
	 * @var string
	 */
	protected $base_path = 'helper/chimp-chatter.json';

	/**
	 * Retrieve the Chimp Chatter
	 *
	 * @author Jeremy Pry
	 *
	 * @param bool $use_transient Whether to use a transient in the response.
	 *
	 * @return array|WP_Error
	 */
	public function chimp_chatter( $use_transient = true ) {
		$transient_key = 'yikes_eme_chimp_chatter';
		$transient     = get_transient( $transient_key );

		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$response = $this->maybe_return_error( $this->post_to_api( $this->base_path, array() ) );

		if ( ! is_wp_error( $response ) ) {
			set_transient( $transient_key, $response, HOUR_IN_SECONDS );
		}

		return $response;
	}
}
