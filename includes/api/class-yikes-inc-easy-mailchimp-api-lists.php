<?php

/**
 *
 */
class Yikes_Inc_Easy_MailChimp_API_Lists {

	/**
	 * Our API object.
	 *
	 * @var Yikes_Inc_Easy_MailChimp_API
	 */
	protected $api;

	/**
	 * The base API path.
	 *
	 * @var string
	 */
	protected $base_path = 'lists';

	/**
	 * Yikes_Inc_Easy_MailChimp_API_Lists constructor.
	 *
	 * @param Yikes_Inc_Easy_MailChimp_API $api
	 */
	public function __construct( Yikes_Inc_Easy_MailChimp_API $api ) {
		$this->api = $api;
	}

	/**
	 * Get all of the lists from the API.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $limit_fields Array of fields to limit the results. The fields should be keys in the array.
	 *
	 * @return array The array of lists, indexed by list ID.
	 */
	public function get_lists( $limit_fields = array() ) {
		// Ensure the ID and total_items are always present in the limit fields
		if ( ! empty( $limit_fields ) ) {
			if ( ! isset( $limit_fields['lists.id'] ) ) {
				$limit_fields['lists.id'] = true;
			}
			if ( ! isset( $limit_fields['total_items'] ) ) {
				$limit_fields['total_items'] = true;
			}
		}

		// Build the relative query
		$query = add_query_arg( 'fields', join( ',', array_keys( $limit_fields ) ), $this->base_path );

		// Set some initial variables.
		$lists  = array();
		$offset = 0;
		$total  = 0;

		// Retrieve lists, looping if needed.
		do {
			$query    = add_query_arg( 'offset', $offset, $query );
			$response = $this->get_from_api( $query );
			if ( isset( $response['error'] ) || empty( $response['lists'] ) ) {
				break;
			}

			if ( 0 === $total ) {
				$total = intval( $response['total_items'] );
			}

			foreach ( $response['lists'] as $list ) {
				$lists[ $list['id'] ] = $list;
			}

			$offset += 10;
		} while ( ( $offset - 1 ) < $total );

		return $lists;
	}

	/**
	 * Get a single list from the API.
	 *
	 * @author Jeremy Pry
	 *
	 * @param $list_id
	 *
	 * @return array|WP_Error
	 */
	public function get_list( $list_id ) {
		$path     = "{$this->base_path}/{$list_id}";
		$response = $this->get_from_api( $path );

		if ( isset( $response['error'] ) ) {
			return new WP_Error( $response['title'], $response['detail'] );
		}

		return $response;
	}

	/**
	 * Get an array of list IDs from the API.
	 *
	 * @author Jeremy Pry
	 * @return array Array of list IDs.
	 */
	public function get_list_ids() {
		return array_keys( $this->get_lists( array( 'lists.id' => true ) ) );
	}

	/**
	 * Get data from the API
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $path The relative API path. Leading slash not required.
	 *
	 * @return array
	 */
	protected function get_from_api( $path ) {
		$response = $this->api->get( $path );
		$headers  = wp_remote_retrieve_headers( $response );

		if ( is_wp_error( $response ) ) {
			return array();
		}

		// JSON-decode the body.
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		// MailChimp uses the application/problem+json type for errors
		if ( isset( $headers['Content-Type'] ) && 'application/problem+json' == $headers['Content-Type'] ) {
			$body['error'] = true;
		}

		return $body;
	}
}
