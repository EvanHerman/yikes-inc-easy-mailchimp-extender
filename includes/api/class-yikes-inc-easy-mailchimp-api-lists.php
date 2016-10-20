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
	 * @return array|WP_Error
	 */
	public function get_lists() {
		$response = $this->get_from_api( $this->base_path );
		if ( isset( $response['error'] ) ) {
			return new WP_Error( $response['title'], $response['detail'] );
		}

		$lists       = array();
		$total_lists = $response['total_items'];
		foreach ( $response['lists'] as $list ) {
			$lists[ $list['id'] ] = $list;
		}

		// Maybe get more items.
		if ( $total_lists > 10 ) {
			$offset = 0;
			do {
				$offset += 10;
				$response = $this->get_from_api( $this->base_path . "?offset={$offset}" );
				if ( isset( $response['error'] ) || empty( $response['lists'] ) ) {
					break;
				}

				foreach ( $response['lists'] as $list ) {
					$lists[ $list['id'] ] = $list;
				}

			} while ( true );
		}

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
