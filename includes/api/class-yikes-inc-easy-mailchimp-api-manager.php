<?php

/**
 * The MailChimp API manager.
 *
 * Used to retrieve API functionality for use elsewhere.
 *
 * @author Jeremy Pry
 * @since  %VERSION%
 */
class Yikes_Inc_Easy_MailChimp_API_Manager {

	/**
	 * The account manager instance.
	 *
	 * @var Yikes_Inc_Easy_MailChimp_API_Account
	 */
	protected $account_manager = null;

	/**
	 * Our API instance.
	 *
	 * @var Yikes_Inc_Easy_MailChimp_API[]
	 */
	protected $api = array();

	/**
	 * The whole API key.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * Yikes_Inc_Easy_MailChimp_API_Chimp_Chatter instance.
	 *
	 * @since %VERSION%
	 * @var Yikes_Inc_Easy_MailChimp_API_Chimp_Chatter
	 */
	protected $chimp_chatter = null;

	/**
	 * The Datacenter for the MailChimp account.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $dc = '';

	/**
	 * The API key with the datacenter portion removed.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $key = '';

	/**
	 * The list manager instance.
	 *
	 * @var Yikes_Inc_Easy_MailChimp_API_Lists
	 */
	protected $list_manager = null;

	/**
	 * Yikes_Inc_Easy_MailChimp_API_Manager constructor.
	 *
	 * @since %VERSION%
	 *
	 * @param string $api_key The full API key from MailChimp.
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
	 * @since  %VERSION%
	 * @return string The API key with the datacenter portion removed.
	 */
	public function get_api_key() {
		return $this->key;
	}

	/**
	 * Get the array of API Key parts.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
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
	 * Get the Datacenter for the MailChimp account.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 * @return string The datacenter for the MailChimp Account.
	 */
	public function get_datacenter() {
		return $this->dc;
	}

	/**
	 * Get the default version of the API to use.
	 *
	 * @author Jeremy Pry
	 * @return string
	 */
	public function get_default_api_version() {
		/**
		 * Filter the default MailChimp API version.
		 *
		 * @param string $version The default MailChimp API version.
		 */
		return apply_filters( 'yikesinc_eme_default_api_version', '3.0' );
	}

	/**
	 * Get the API instance.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $version The API version instance to retrieve.
	 *
	 * @return Yikes_Inc_Easy_MailChimp_API
	 */
	public function get_api( $version = '' ) {
		$version = $version ?: $this->get_default_api_version();

		if ( ! array_key_exists( $version, $this->api ) || null === $this->api[ $version ] ) {
			$this->api[ $version ] = new Yikes_Inc_Easy_MailChimp_API( $this->get_datacenter(), $this->get_api_key(), $version );
		}

		return $this->api[ $version ];
	}

	/**
	 * Get the List Manager instance.
	 *
	 * @author Jeremy Pry
	 *
	 * @return Yikes_Inc_Easy_MailChimp_API_Lists
	 */
	public function get_list_handler() {
		if ( null == $this->list_manager ) {
			$this->list_manager = new Yikes_Inc_Easy_MailChimp_API_Lists( $this->get_api() );
		}

		return $this->list_manager;
	}

	/**
	 * Get the Account Manager instance.
	 *
	 * @author Jeremy Pry
	 * @return Yikes_Inc_Easy_MailChimp_API_Account
	 */
	public function get_account_handler() {
		if ( null === $this->account_manager ) {
			$this->account_manager = new Yikes_Inc_Easy_MailChimp_API_Account( $this->get_api() );
		}

		return $this->account_manager;
	}

	/**
	 * Get the chimp chatter instance.
	 *
	 * @author Jeremy Pry
	 * @since %VERSION%
	 * @return Yikes_Inc_Easy_MailChimp_API_Chimp_Chatter
	 */
	public function get_chimp_chatter() {
		if ( null === $this->chimp_chatter ) {
			$this->chimp_chatter = new Yikes_Inc_Easy_MailChimp_API_Chimp_Chatter( $this->get_api( '2.0' ) );
		}

		return $this->chimp_chatter;
	}
}
