<?php

/**
 * The Mailchimp API manager.
 *
 * Used to retrieve API functionality for use elsewhere.
 *
 * @author Jeremy Pry
 * @since  6.3.0
 */
class Yikes_Inc_Easy_Mailchimp_API_Manager {

	/**
	 * The account manager instance.
	 *
	 * @since 6.3.0
	 * @var Yikes_Inc_Easy_Mailchimp_API_Account
	 */
	protected $account_manager = null;

	/**
	 * Our API instance.
	 *
	 * @since 6.3.0
	 * @var Yikes_Inc_Easy_Mailchimp_API[]
	 */
	protected $api = array();

	/**
	 * The whole API key.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * The Datacenter for the Mailchimp account.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $dc = '';

	/**
	 * The API key with the datacenter portion removed.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $key = '';

	/**
	 * The list manager instance.
	 *
	 * @since 6.3.0
	 * @var Yikes_Inc_Easy_Mailchimp_API_Lists
	 */
	protected $list_manager = null;

	/**
	 * Yikes_Inc_Easy_Mailchimp_API_Manager constructor.
	 *
	 * @since 6.3.0
	 *
	 * @param string $api_key The full API key from Mailchimp.
	 */
	public function __construct( $api_key = '' ) {
		if ( empty( $api_key ) ) {
			$api_key = yikes_get_mc_api_key();
		}

		$this->api_key = $api_key;
		$parts         = $this->get_api_key_parts();
		$this->key     = $parts['key'];
		$this->dc      = $parts['dc'];
	}

	/**
	 * Get the API key.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return string The API key with the datacenter portion removed.
	 */
	public function get_api_key() {
		return $this->key;
	}

	/**
	 * Get the array of API Key parts.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return array Associative array of API key parts.
	 */
	protected function get_api_key_parts() {
		$parts = explode( '-', $this->api_key );

		return array(
			'key' => $parts[0],
			'dc'  => isset( $parts[1] ) ? $parts[1] : '',
		);
	}

	/**
	 * Get the Datacenter for the Mailchimp account.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return string The datacenter for the Mailchimp Account.
	 */
	public function get_datacenter() {
		return $this->dc;
	}

	/**
	 * Get the default version of the API to use.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return string
	 */
	public function get_default_api_version() {
		/**
		 * Filter the default Mailchimp API version.
		 *
		 * @since 6.3.0
		 *
		 * @param string $version The default Mailchimp API version.
		 */
		return apply_filters( 'yikesinc_eme_default_api_version', '3.0' );
	}

	/**
	 * Get the API instance.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $version The API version instance to retrieve.
	 *
	 * @return Yikes_Inc_Easy_Mailchimp_API
	 */
	public function get_api( $version = '' ) {
		$version = $version ?: $this->get_default_api_version();

		if ( ! array_key_exists( $version, $this->api ) || null === $this->api[ $version ] ) {
			$this->api[ $version ] = new Yikes_Inc_Easy_Mailchimp_API( $this->get_datacenter(), $this->get_api_key(), $version );
		}

		return $this->api[ $version ];
	}

	/**
	 * Get the List Manager instance.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return Yikes_Inc_Easy_Mailchimp_API_Lists
	 */
	public function get_list_handler() {
		if ( null == $this->list_manager ) {
			$this->list_manager = new Yikes_Inc_Easy_Mailchimp_API_Lists( $this->get_api() );
		}

		return $this->list_manager;
	}

	/**
	 * Get the Account Manager instance.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return Yikes_Inc_Easy_Mailchimp_API_Account
	 */
	public function get_account_handler() {
		if ( null === $this->account_manager ) {
			$this->account_manager = new Yikes_Inc_Easy_Mailchimp_API_Account( $this->get_api() );
		}

		return $this->account_manager;
	}
}
