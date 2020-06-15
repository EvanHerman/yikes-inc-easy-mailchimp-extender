<?php

/**
 * A class to handle requests to the Mailchimp API.
 *
 * @author Jeremy Pry
 * @since  6.3.0
 */
class Yikes_Inc_Easy_Mailchimp_API {

	/**
	 * The API key.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * The URL for the API.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $api_url = '';

	/**
	 * The API version.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $api_version = '';

	/**
	 * The datacenter where the Mailchimp account is located.
	 *
	 * This is typically part of the API Key.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $datacenter = '';

	/**
	 * Yikes_Inc_Easy_Mailchimp_API constructor.
	 *
	 * @since 6.3.0
	 *
	 * @param string $datacenter  The datacenter string where the Mailchimp account is located.
	 * @param string $api_key     The base API key, without the datacenter appended.
	 * @param string $api_version The API version to use.
	 */
	public function __construct( $datacenter, $api_key, $api_version ) {
		$this->datacenter  = $datacenter;
		$this->api_key     = $api_key;
		$this->api_version = $api_version;
		$this->api_url     = "https://{$this->datacenter}.api.mailchimp.com/{$this->api_version}";
	}

	/**
	 * Send a DELETE request to the Mailchimp API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function delete( $path = '', $headers = array(), $params = array() ) {
		return $this->send_request( $path, 'DELETE', $headers, $params );
	}

	/**
	 * Send a GET request to the Mailchimp API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function get( $path = '', $headers = array(), $params = array() ) {
		return $this->send_request( $path, 'GET', $headers, $params );
	}

	/**
	 * Send a PATCH request to the Mailchimp API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function patch( $path = '', $headers = array(), $params = array() ) {
		if ( ! isset( $params['body'] ) ) {
			return new WP_Error(
				'yikesinc_eme_missing_body',
				sprintf(
					/* translators: %s refers to the request method. */
					__( '%s requests require a body as one of the parameters.', 'yikes-inc-easy-mailchimp-extender' ),
					'PATCH'
				)
			);
		}

		return $this->send_request( $path, 'PATCH', $headers, $params );
	}

	/**
	 * Send a POST request to the Mailchimp API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function post( $path = '', $headers = array(), $params = array() ) {
		if ( ! isset( $params['body'] ) ) {
			return new WP_Error(
				'yikesinc_eme_missing_body',
				sprintf(
					/* translators: %s refers to the request method. */
					__( '%s requests require a body as one of the parameters.', 'yikes-inc-easy-mailchimp-extender' ),
					'POST'
				)
			);
		}

		return $this->send_request( $path, 'POST', $headers, $params );
	}

	/**
	 * Send a PUT request to the Mailchimp API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function put( $path = '', $headers = array(), $params = array() ) {
		if ( ! isset( $params['body'] ) ) {
			return new WP_Error(
				'yikesinc_eme_missing_body',
				sprintf(
					/* translators: %s refers to the request method. */
					__( '%s requests require a body as one of the parameters.', 'yikes-inc-easy-mailchimp-extender' ),
					'PUT'
				)
			);
		}

		return $this->send_request( $path, 'PUT', $headers, $params );
	}

	/**
	 * Send a request to the Mailchimp API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative path for the request.
	 * @param string $method  The method to use for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	protected function send_request( $path, $method, $headers = array(), $params = array() ) {
		$headers = apply_filters( 'yikesinc_eme_mailchimp_headers', $headers );

		// Remove leading slashes from $path, as we'll add that ourselves later.
		$path = ltrim( $path, '/' );

		$args = $this->build_request_args( $path, $method, $headers, $params );

		/**
		 * Filter the URL used for a request to the Mailchimp API.
		 *
		 * @since 6.3.0
		 *
		 * @param string $url  The URL to use.
		 * @param string $path The relative path for the request.
		 */
		$url = apply_filters( 'yikesinc_eme_api_url', sprintf( '%s/%s', $this->api_url, $path ), $path );

		return wp_remote_request( $url, $args );
	}

	/**
	 * Get the user agent to use with a request to the API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return string The user agent string.
	 */
	protected function get_user_agent() {
		global $wp_version;

		$user_agent = 'WordPress/' . $wp_version . '; Yikes Easy Mailchimp Extender; ' . get_bloginfo( 'url' );
		/**
		 * Filter the User Agent used in API requests.
		 *
		 * @since 6.3.0
		 *
		 * @param string $user_agent The user agent to send with API requests.
		 */
		$user_agent = apply_filters( 'yikesinc_eme_api_user_agent', $user_agent );

		return $user_agent;
	}

	/**
	 * Get the Auth Headers for an API requests.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 * @return array The array of auth headers for an API request.
	 */
	protected function get_auth_headers() {
		/*
		 * According to the Mailchimp API docs, you can use any string you want, and the API
		 * key as the password. We're just going to use "yikesmailchimp" as the user.
		 */
		$user_pass    = base64_encode( "yikesmailchimp:{$this->api_key}" );
		$auth_headers = array(
			'Authorization' => "Basic {$user_pass}",
		);

		/**
		 * Filter the Auth Headers used for an API request.
		 *
		 * @since 6.3.0
		 *
		 * @param array  $auth_headers The array of auth headers for an API request.
		 * @param string $api_version  The version of the API being used.
		 */
		return apply_filters( 'yikesinc_eme_api_auth_headers', $auth_headers, $this->api_version );
	}

	/**
	 * Get a body array with authorization included.
	 *
	 * @author Jeremy Pry
	 * @return array
	 */
	protected function get_auth_body() {
		return array(
			'apikey' => $this->api_key,
		);
	}

	/**
	 * Build the arguments for the request.
	 *
	 * @author Jeremy Pry
	 * @since 6.3.0
	 *
	 * @param string $path    The relative path for the request.
	 * @param string $method  The method to use for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array
	 */
	protected function build_request_args( $path, $method, $headers = array(), $params = array() ) {
		// Ensure our method is uppercase
		$method = strtoupper( $method );

		// Get the authorized array
		$authorized_args = $this->get_authorized_args();

		// If we have body data, maybe convert it to JSON.
		if ( isset( $params['body'] ) && ( is_array( $params['body'] ) || is_object( $params['body'] ) ) ) {
			$params['body']          = json_encode( wp_parse_args( $authorized_args['body'], $params['body'] ) );
			$headers['Content-Type'] = 'application/json';
		}

		// Combine the given headers and auth headers
		$headers = wp_parse_args( $authorized_args['headers'], $headers );
		/**
		 * Filter the headers used for a request to the Mailchimp API.
		 *
		 * @since 6.3.0
		 *
		 * @param array  $headers The array of headers to send with the request.
		 * @param string $path    The relative path for the request.
		 * @param string $method  The method used for the request.
		 * @param array  $params  The array of additional parameters passed to the request.
		 */
		$headers = apply_filters( 'yikesinc_eme_api_headers', $headers, $path, $method, $params );

		// Build the args for the request.
		$args = array(
			'method'     => $method,
			'headers'    => $headers,
			'user-agent' => $this->get_user_agent(),
			/**
			 * Filter the timeout used when sending an API request.
			 *
			 * @since 6.3.0
			 *
			 * @param int $timeout The number of seconds after which the request will time out.
			 */
			'timeout'    => apply_filters( 'yikesinc_eme_api_timeout', 15 ),
			/**
			 * Filter whether our requests should verify the SSL certificate.
			 *
			 * @since 6.3.0
			 *
			 * @param bool $sslverify
			 */
			'sslverify'  => apply_filters( 'yikes-mailchimp-sslverify', true ),
		);

		/**
		 * Filter the args used for a request to the Mailchimp API.
		 *
		 * @since 6.3.0
		 *
		 * @param array  $args   The arguments for the request.
		 * @param string $path   The relative path for the request.
		 * @param string $method The method used for the request.
		 * @param array  $params The array of additional params passed to the request.
		 */
		return apply_filters( 'yikesinc_eme_api_args', wp_parse_args( $params, $args ), $path, $method, $params );
	}

	/**
	 * Get an authorized request based on the API version.
	 *
	 * @author Jeremy Pry
	 * @since 6.3.0
	 * @return array
	 */
	protected function get_authorized_args() {
		$args = array(
			'body'    => array(),
			'headers' => array(),
		);

		// Version 2.0 uses body authorization
		if ( version_compare( '3.0', $this->api_version, '>' ) ) {
			$args['body'] = $this->get_auth_body();
		}

		// Version 3.0 uses authorization headers.
		if ( version_compare( '3.0', $this->api_version, '<=' ) ) {
			$args['headers'] = $this->get_auth_headers();
		}

		return $args;
	}

	/**
	 * Get the API version for this instance.
	 *
	 * @author Jeremy Pry
	 * @since 6.3.0
	 * @return string The API version.
	 */
	public function get_version() {
		return $this->api_version;
	}
}
