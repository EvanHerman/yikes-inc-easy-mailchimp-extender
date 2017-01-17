<?php

/**
 * Get Account details.
 *
 * This uses the V2 API.
 *
 * @deprecated
 *
 * @author Jeremy Pry
 * @since  6.3.0
 */
class Yikes_Inc_Easy_MailChimp_API_Account_Details extends Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

	/**
	 * The base API path.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $base_path = 'helper/account-details.json';

	/**
	 * Whether a V2 API connection is required.
	 *
	 * @since 6.3.0
	 * @var bool
	 */
	protected $requires_v2 = true;

	/**
	 * Get the account details.
	 *
	 * This is meant to use the V2 API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param bool $use_transient Whether to use a transient in the response.
	 *
	 * @return array|WP_Error
	 */
	public function account_details( $use_transient = true ) {
		$transient_key = 'yikes_eme_account_details';

		return $this->post_base_path( $transient_key, $use_transient );
	}
}
