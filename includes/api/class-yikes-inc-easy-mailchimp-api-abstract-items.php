<?php

/**
 * Abstract class for retrieving items from the API.
 *
 * @author Jeremy Pry
 * @since  6.3.0
 */
abstract class Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

	/**
	 * Our API object.
	 *
	 * @since 6.3.0
	 * @var Yikes_Inc_Easy_MailChimp_API
	 */
	protected $api;

	/**
	 * The base API path.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $base_path = '';

	/**
	 * Whether a V2 API connection is required.
	 *
	 * @since 6.3.0
	 * @var bool
	 */
	protected $requires_v2 = false;

	/**
	 * Yikes_Inc_Easy_MailChimp_API_Lists constructor.
	 *
	 * @since 6.3.0
	 *
	 * @param Yikes_Inc_Easy_MailChimp_API $api The API connection.
	 *
	 * @throws \Exception When a V2 API connection is required and not received.
	 */
	public function __construct( Yikes_Inc_Easy_MailChimp_API $api ) {
		// If a V2 API is required, throw an exception if we don't have it.
		if ( $this->requires_v2 && ! version_compare( '3.0', $api->get_version(), '>' ) ) {
			throw new \Exception( sprintf( 'The %s class requires a V2 API instance.', get_class( $this ) ) );
		}

		$this->api = $api;
	}

	/**
	 * Retrieve items from the API, looping as needed.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $query        The relative path, including any query args.
	 * @param string $item_key     The item key to look for in results.
	 * @param string $index_field  The item field to use as an index.
	 * @param int    $offset_start The offset to start at.
	 *
	 * @return array|WP_Error
	 */
	protected function loop_items( $query, $item_key, $index_field = 'id', $offset_start = 0 ) {
		// Set some initial variables.
		$items  = array();
		$offset = $offset_start;
		$total  = 0;

		// Retrieve items, looping if needed.
		do {
			// Add the offset to the query.
			$query    = add_query_arg( 'offset', $offset, $query );
			$response = $this->get_from_api( $query );
			if ( is_wp_error( $response ) ) {
				return $response;
			}

			// If the API gave an error or there are no more lists, break.
			if ( isset( $response['error'] ) ) {
				return new WP_Error( $response['title'], $response['detail'] );
			}

			if ( empty( $response[ $item_key ] ) ) {
				break;
			}

			// Update the total number of items if it's still zero.
			if ( 0 === $total ) {
				$total = intval( $response['total_items'] );
			}

			// Store each new list.
			foreach ( $response[ $item_key ] as $item ) {
				$items[ $item[ $index_field ] ] = $item;
			}

			$offset += 10;
		} while ( $offset <= $total );

		return $items;
	}

	/**
	 * Get data from the API
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative API path. Leading slash not required.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  An array of additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	protected function get_from_api( $path = '', $headers = array(), $params = array() ) {
		$response = $this->api->get( $path, $headers, $params );

		return $this->parse_response( $response );
	}

	/**
	 * Send a PATCH request to the API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative API path. Leading slash not required.
	 * @param mixed  $body    The body data for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  Additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	protected function patch_to_api( $path, $body, $headers = array(), $params = array() ) {
		$params   = wp_parse_args( array( 'body' => $body ), $params );
		$response = $this->api->patch( $path, $headers, $params );

		return $this->parse_response( $response );
	}

	/**
	 * Send a POST request to the API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative API path. Leading slash not required.
	 * @param mixed  $body    The body data for the request.
	 * @param array  $headers The array of headers to send with the request.
	 * @param array  $params  Additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	protected function post_to_api( $path, $body, $headers = array(), $params = array() ) {
		$params   = wp_parse_args( array( 'body' => $body ), $params );
		$response = $this->api->post( $path, $headers, $params );

		return $this->parse_response( $response );
	}

	/**
	 * Send a PUT request to the API.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $path    The relative API path. Leading slash not required.
	 * @param mixed  $body    The body data for the request.
	 * @param array  $headers Array of headers to send with the request.
	 * @param array  $params  Additional parameters to pass to the request. See WP_Http::request().
	 *
	 * @return array|WP_Error
	 */
	protected function put_to_api( $path, $body, $headers = array(), $params = array() ) {
		$params   = wp_parse_args( array( 'body' => $body ), $params );
		$response = $this->api->put( $path, $headers, $params );

		return $this->parse_response( $response );
	}

	/**
	 * Return either the valid response, or a WP_Error.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param mixed $response The API response.
	 *
	 * @return array|WP_Error Array of data when there was no error, or a WP_Error.
	 */
	protected function maybe_return_error( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( isset( $response['error'] ) ) {
			$title  = isset( $response['title'] )  ? $response['title']  : $response['name'];
			$detail = isset( $response['detail'] ) ? $response['detail'] : $response['error'];
			$data   = isset( $response['errors'] ) ? $response['errors'] : array();

			return new WP_Error(
				$title,
				$detail,
				array(
					'status' => (int) $response['status'],
					'data'   => $data,
				)
			);
		}

		return $response;
	}

	/**
	 * Parse the response and retrieve the body.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param array|WP_Error $response
	 *
	 * @return array|WP_Error
	 */
	protected function parse_response( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// JSON-decode the body.
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		// MailChimp uses the application/problem+json type for errors
		$headers = wp_remote_retrieve_headers( $response );
		if ( isset( $headers['content-type'] ) ) {
			if ( false !== strpos( $headers['content-type'], 'application/problem+json' ) ) {
				$body['error'] = true;
			}
		}

		return $body;
	}

	/**
	 * Get a post response from the API.
	 *
	 * This is an abstracted method to retrieve a POST response from the API using only the base path.
	 *
	 * @author Jeremy Pry
	 * @since  6.3.0
	 *
	 * @param string $transient_key The string to use for returning a stored transient.
	 * @param bool   $use_transient Whether to use transients.
	 *
	 * @return array|WP_Error
	 */
	protected function post_base_path( $transient_key, $use_transient = true ) {
		$transient = get_transient( $transient_key );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$response = $this->maybe_return_error( $this->post_to_api( $this->base_path, array() ) );
		if ( ! is_wp_error( $response ) ) {
			set_transient( $transient_key, $response, HOUR_IN_SECONDS );
		}

		return $response;
	}
}
