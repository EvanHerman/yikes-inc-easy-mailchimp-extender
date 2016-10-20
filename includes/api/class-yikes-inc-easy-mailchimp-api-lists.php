<?php

/**
 *
 */
class Yikes_Inc_Easy_MailChimp_API_Lists extends Yikes_Inc_Easy_MailChimp_API_Abstract_Items {

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
	 * Get all of the lists from the API.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $limit_fields Array of fields to limit the results. The fields should be keys in the array.
	 *
	 * @return array|WP_Error The array of lists, indexed by list ID, or WP_Error if the API gave us an error.
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

		// Add the limiting fields to the query.
		$query = add_query_arg( 'fields', join( ',', array_keys( $limit_fields ) ), $this->base_path );

		return $this->loop_items( $query, 'lists' );
	}

	/**
	 * Get a single list from the API.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id The list ID in MailChimp.
	 *
	 * @return array|WP_Error
	 */
	public function get_list( $list_id ) {
		$path     = "{$this->base_path}/{$list_id}";
		$response = $this->get_from_api( $path );

		return $this->maybe_return_error( $response );
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
	 * Get the merge fields for a particular list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id The list ID in MailChimp.
	 *
	 * @return array|WP_Error
	 */
	public function get_merge_fields( $list_id ) {
		$path     = "{$this->base_path}/{$list_id}/merge-fields";
		$response = $this->get_from_api( $path );

		// The API doesn't give us the email field, so let's create that ourselves.
		$response = $this->maybe_return_error( $response );
		if ( ! is_wp_error( $response ) && isset( $response['merge_fields'] ) ) {
			$email_field = array(
				'merge_id'      => 0,
				'tag'           => 'EMAIL',
				'name'          => __( 'Email Address' ),
				'type'          => 'email',
				'required'      => true,
				'default_value' => '',
				'public'        => true,
				'display_order' => 1,
				'options'       => array(
					'size' => 25,
				),
				'list_id'       => $list_id,
				'_links'        => array(),
			);

			array_unshift( $response['merge_fields'], $email_field );
		}

		return $response;
	}
}
