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
	 * @since %VERSION%
	 * @var Yikes_Inc_Easy_MailChimp_API
	 */
	protected $api;

	/**
	 * The base API path.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $base_path = 'helper/chimp-chatter.json';

	/**
	 * Retrieve the Chimp Chatter
	 *
	 * @author Jeremy Pry
	 * @since %VERSION%
	 *
	 * @param bool $use_transient Whether to use a transient in the response.
	 *
	 * @return array|WP_Error
	 */
	public function chimp_chatter( $use_transient = true ) {
		$transient_key = 'yikes_eme_chimp_chatter';

		return $this->post_base_path( $transient_key, $use_transient );
	}
}
