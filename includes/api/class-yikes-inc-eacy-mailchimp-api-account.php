<?php

/**
 *
 */
class Yikes_Inc_Easy_Mailchimp_API_Account extends Yikes_Inc_Easy_Mailchimp_API_Abstract_Items {

	/**
	 * Get general account info.
	 *
	 * @author Jeremy Pry
	 *
	 * @param bool $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error
	 */
	public function get_account( $use_transient = true ) {
		$transient_key = 'yikes_eme_account';
		$transient     = get_transient( $transient_key );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$response = $this->maybe_return_error( $this->get_from_api() );
		if ( ! is_wp_error( $response ) ) {
			set_transient( $transient_key, $response, HOUR_IN_SECONDS );
		}

		return $response;
	}
}
