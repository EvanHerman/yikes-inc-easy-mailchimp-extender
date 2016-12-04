<?php

/**
 * Get Account details.
 *
 * This uses the V2 API.
 *
 * @deprecated
 *
 * @author Jeremy Pry
 * @since  %VERSION%
 */
class Yikes_Inc_Easy_MailChimp_API_Account_Details extends Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

	/**
	 * The base API path.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $base_path = 'helper/account-details.json';

	/**
	 * Get the account details.
	 *
	 * This is meant to use the V2 API.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 *
	 * @param bool $use_transient Whether to use a transient in the response.
	 *
	 * @return array|WP_Error
	 */
	public function account_details( $use_transient = true ) {
		$transient_key = 'yikes_eme_account_details';
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
