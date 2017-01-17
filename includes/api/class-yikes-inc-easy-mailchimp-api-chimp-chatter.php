<?php

/**
 * Chimp Chatter handler.
 *
 * Note that this expects a V2 API object.
 *
 * @deprecated
 *
 * @author Jeremy Pry
 * @since  6.3.0
 */
class Yikes_Inc_Easy_MailChimp_API_Chimp_Chatter extends Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

	/**
	 * The base API path.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $base_path = 'helper/chimp-chatter.json';

	/**
	 * Whether a V2 API connection is required.
	 *
	 * @since 6.3.0
	 * @var bool
	 */
	protected $requires_v2 = true;

	/**
	 * Retrieve the Chimp Chatter
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
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
