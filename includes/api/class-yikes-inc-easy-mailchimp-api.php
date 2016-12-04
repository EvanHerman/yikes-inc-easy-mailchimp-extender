<?php

/**
 * A class to handle requests to the MailChimp API.
 *
 * @author Jeremy Pry
 * @since  %VERSION%
 */
class Yikes_Inc_Easy_MailChimp_API {

	/**
	 * The API key.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * The URL for the API.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $api_url = '';

	/**
	 * The API version.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $api_version = '';

	/**
	 * The datacenter where the MailChimp account is located.
	 *
	 * This is typically part of the API Key.
	 *
	 * @since %VERSION%
	 * @var string
	 */
	protected $datacenter = '';

	/**
	 * Yikes_Inc_Easy_MailChimp_API constructor.
	 *
	 * @since %VERSION%
	 *
	 * @param string $datacenter  The datacenter string where the MailChimp account is located.
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
	 * Send a DELETE request to the MailChimp API.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
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
	 * Send a GET request to the MailChimp API.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
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
	 * Send a PATCH request to the MailChimp API.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function patch( $path = '', $headers = array(), $params = array() ) {
		if ( ! isset( $params['body'] ) || empty( $params['body'] ) ) {
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
	 * Send a POST request to the MailChimp API.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function post( $path = '', $headers = array(), $params = array() ) {
		if ( ! isset( $params['body'] ) || empty( $params['body'] ) ) {
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
	 * Send a PUT request to the MailChimp API.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 *
	 * @param string $path    The relative path for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	public function put( $path = '', $headers = array(), $params = array() ) {
		if ( ! isset( $params['body'] ) || empty( $params['body'] ) ) {
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
	 * Send a request to the MailChimp API.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 *
	 * @param string $path    The relative path for the request.
	 * @param string $method  The method to use for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	protected function send_request( $path, $method, $headers = array(), $params = array() ) {
		// Ensure our method is uppercase
		$method = strtoupper( $method );

		// Remove leading slashes from $path, as we'll add that ourselves later.
		$path = ltrim( $path, '/' );

		// If we have body data, maybe convert it to JSON.
		if ( isset( $params['body'] ) && ( is_array( $params['body'] ) || is_object( $params['body'] ) ) ) {
			$params['body']          = json_encode( $params['body'] );
			$headers['Content-Type'] = 'application/json';
		}

		// Build the args for the request.
		$args = array(
			'method'     => $method,
			/**
			 * Filter the headers used for a request to the MailChimp API.
			 *
			 * @since %VERSION%
			 *
			 * @param array  $headers The array of headers to send with the request.
			 * @param string $path    The relative path for the request.
			 * @param string $method  The method used for the request.
			 * @param array  $params  The array of additional parameters passed to the request.
			 */
			'headers'    => apply_filters( 'yikesinc_eme_api_headers', $this->combine_headers( $headers ), $path, $method, $params ),
			'user-agent' => $this->get_user_agent(),
			'timeout'    => 15,
			/**
			 * Filter whether our requests should verify the SSL certificate.
			 *
			 * @param bool $sslverify
			 */
			'sslverify'  => apply_filters( 'yikes-mailchimp-sslverify', true ),
		);

		/**
		 * Filter the args used for a request to the MailChimp API.
		 *
		 * @since %VERSION%
		 *
		 * @param array  $args   The arguments for the request.
		 * @param string $path   The relative path for the request.
		 * @param string $method The method used for the request.
		 * @param array  $params The array of additional params passed to the request.
		 */
		$args = apply_filters( 'yikesinc_eme_api_args', wp_parse_args( $params, $args ), $path, $method, $params );

		/**
		 * Filter the URL used for a request to the MailChimp API.
		 *
		 * @since %VERSION%
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
	 * @since  %VERSION%
	 * @return string The user agent string.
	 */
	protected function get_user_agent() {
		global $wp_version;

		$user_agent = 'WordPress/' . $wp_version . '; Yikes Easy MailChimp Extender; ' . get_bloginfo( 'url' );
		/**
		 * Filter the User Agent used in API requests.
		 *
		 * @since %VERSION%
		 *
		 * @param string $user_agent The user agent to send with API requests.
		 */
		$user_agent = apply_filters( 'yikesinc_eme_api_user_agent', $user_agent );

		return $user_agent;
	}

	/**
	 * Combine the headers for the request with Auth Headers.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 *
	 * @param array $headers The array of headers to use for the request.
	 *
	 * @return array The array of headers, including auth headers, to use for the request.
	 */
	protected function combine_headers( $headers = array() ) {
		$headers = wp_parse_args( $headers, $this->get_auth_headers() );

		return $headers;
	}

	/**
	 * Get the Auth Headers for an API requests.
	 *
	 * @author Jeremy Pry
	 * @since  %VERSION%
	 * @return array The array of auth headers for an API request.
	 */
	protected function get_auth_headers() {
		/*
		 * According to the MailChimp API docs, you can use any string you want, and the API
		 * key as the password. We're just going to use "yikesmailchimp" as the user.
		 */
		$user_pass    = base64_encode( "yikesmailchimp:{$this->api_key}" );
		$auth_headers = array(
			'Authorization' => "Basic {$user_pass}",
		);

		/**
		 * Filter the Auth Headers used for an API request.
		 *
		 * @since %VERSION%
		 *
		 * @param array $auth_headers The array of auth headers for an API request.
		 */
		return apply_filters( 'yikesinc_eme_api_auth_headers', $auth_headers );
	}
}
