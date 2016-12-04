<?php

/**
 * User Profile handler.
 *
 * Note: This needs the V2 endpoint.
 *
 * @author Jeremy Pry
 * @since  %VERSION%
 */
class Yikes_Inc_Easy_MailChimp_API_Profile extends Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

	/**
	 * The base API path.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $base_path = 'users/profile.json';

	/**
	 * Get profile data from the API.
	 *
	 * @author Jeremy Pry
	 * @since %VERSION%
	 *
	 * @param bool $use_transients Whether to use a transient in the response.
	 *
	 * @return array|WP_Error
	 */
	public function get_profile( $use_transients = true ) {
		$transient_key = 'yikes_eme_user_profile';
		$transient     = get_transient( $transient_key );

		if ( false !== $transient && $use_transients ) {
			return $transient;
		}

		$response = $this->maybe_return_error( $this->post_to_api( $this->base_path, array() ) );

		if ( ! is_wp_error( $response ) ) {
			set_transient( $transient_key, $response, HOUR_IN_SECONDS );
		}

		return $response;
	}
}
