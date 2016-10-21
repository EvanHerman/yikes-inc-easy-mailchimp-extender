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
		$path         = "{$this->base_path}/{$list_id}/merge-fields";
		$merge_fields = $this->loop_items( $path, 'merge_fields', 'merge_id' );

		// The API doesn't give us the email field, so let's create that ourselves.
		$merge_fields = $this->maybe_return_error( $merge_fields );
		if ( ! is_wp_error( $merge_fields ) && ! empty( $merge_fields ) ) {
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

			array_unshift( $merge_fields, $email_field );
		}

		return $merge_fields;
	}

	/**
	 * Get the Interest Categories for a particular list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id The list ID.
	 *
	 * @return array|WP_Error
	 */
	public function get_interest_categories( $list_id ) {
		$base_path  = "{$this->base_path}/{$list_id}/interest-categories";
		$categories = $this->loop_items( $base_path, 'categories' );

		// Check for Error, and maybe return early.
		if ( is_wp_error( $categories ) ) {
			return $categories;
		}

		// Loop through each interest category and attach the items.
		foreach ( $categories as $id => &$category ) {
			$path      = "{$base_path}/{$id}/interests";
			$interests = $this->loop_items( $path, 'interests' );

			// Check for Error, and maybe return early.
			if ( is_wp_error( $interests ) ) {
				return $interests;
			}

			$category['items'] = $interests;
		}

		return $categories;
	}

	/**
	 * Get segments for a list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id The list ID.
	 *
	 * @return array|WP_Error
	 */
	public function get_segments( $list_id ) {
		$base_path = "{$this->base_path}/{$list_id}/segments";
		$base_path = add_query_arg( 'type', 'saved', $base_path );
		$segments  = $this->loop_items( $base_path, 'segments' );

		// @todo: Include members in the segments?
		return $this->maybe_return_error( $segments );
	}

	/**
	 * Get the members associated with a list.
	 *
	 * The members will be keyed to their email address.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id The list ID.
	 *
	 * @return array|WP_Error
	 */
	public function get_members( $list_id ) {
		$base_path = "{$this->base_path}/{$list_id}/members";
		$members   = $this->loop_items( $base_path, 'members', 'email_address' );

		return $this->maybe_return_error( $members );
	}
}
