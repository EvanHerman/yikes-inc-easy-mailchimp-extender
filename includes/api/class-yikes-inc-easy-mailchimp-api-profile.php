<?php

/**
 * User Profile handler.
 *
 * Note: This needs the V2 endpoint.
 *
 * @deprecated
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

		return $this->post_base_path( $transient_key, $use_transients );
	}
}
