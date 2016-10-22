<?php

/**
 *
 */
class Yikes_Inc_Easy_MailChimp_API_Account extends Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

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
		$transient = get_transient( 'yikes_eme_account' );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$response = $this->parse_response( $this->api->get( '' ) );
		$response = $this->maybe_return_error( $response );
		if ( ! is_wp_error( $response ) ) {
			set_transient( 'yikes_eme_account', $response, HOUR_IN_SECONDS );
		}

		return $response;
	}
}
