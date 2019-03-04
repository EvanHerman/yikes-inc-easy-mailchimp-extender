<?php

/**
 *
 */
class Yikes_Inc_Easy_Mailchimp_API_Lists extends Yikes_Inc_Easy_Mailchimp_API_Abstract_Items {

	/**
	 * Our API object.
	 *
	 * @var Yikes_Inc_Easy_Mailchimp_API
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
	 * @param array $limit_fields  Array of fields to limit the results. The fields should be keys in the array.
	 * @param bool  $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error The array of lists, indexed by list ID, or WP_Error if the API gave us an error.
	 */
	public function get_lists( $limit_fields = array(), $use_transient = true ) {
		// Ensure the ID and total_items are always present in the limit fields
		$limit_fields = $this->compute_limit_fields(
			$limit_fields,
			array(
				'lists.id'    => true,
				'total_items' => true,
			)
		);

		$joined_fields = join( ',', array_keys( $limit_fields ) );
		$transient_key = empty( $limit_fields ) ? 'yikes_eme_lists' : "yikes_eme_lists_{$joined_fields}";
		$transient     = get_transient( $transient_key );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		// Add the limiting fields to the query.
		$query = add_query_arg( 'fields', $joined_fields, $this->base_path );
		$lists = $this->maybe_return_error( $this->loop_items( $query, 'lists' ) );

		if ( is_wp_error( $lists ) ) {
			return $lists;
		}

		set_transient( $transient_key, $lists, HOUR_IN_SECONDS );

		return $lists;
	}

	/**
	 * Get a single list from the API.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id       The list ID in Mailchimp.
	 * @param array  $limit_fields  Array of fields to limit the results. The fields should be keys in the array.
	 * @param bool   $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error
	 */
	public function get_list( $list_id, $limit_fields = array(), $use_transient = true ) {
		$transient_key = "yikes_eme_list_{$list_id}";
		$limit_fields  = $this->compute_limit_fields( $limit_fields, array() );
		$joined_fields = join( ',', array_keys( $limit_fields ) );
		
		if ( ! empty( $limit_fields ) ) {
			$transient_key .= "_{$joined_fields}";
		}

		$transient = get_transient( $transient_key );

		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$path     = "{$this->base_path}/{$list_id}";
		$response = $this->maybe_return_error( $this->get_from_api( $path ) );

		if ( ! is_wp_error( $response ) ) {
			set_transient( $transient_key, $response, HOUR_IN_SECONDS );
		}

		return $response;
	}

	/**
	 * Get an array of list IDs from the API.
	 *
	 * Utilizes a transient for caching.
	 *
	 * @author Jeremy Pry
	 *
	 * @param bool $use_transient Whether to use the transient.
	 *
	 * @return array|WP_Error Array of list IDs or WP_Error object.
	 */
	public function get_list_ids( $use_transient = true ) {
		$transient = get_transient( 'yikesinc_eme_list_ids' );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$lists = $this->get_lists( array( 'lists.id' => true ) );
		if ( is_wp_error( $lists ) ) {
			return $lists;
		}

		$list_ids = array_keys( $lists );
		set_transient( 'yikesinc_eme_list_ids', $list_ids, HOUR_IN_SECONDS );

		return $list_ids;
	}

	/**
	 * Get the merge fields for a particular list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id       The list ID in Mailchimp.
	 * @param bool   $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error
	 */
	public function get_merge_fields( $list_id, $use_transient = true ) {
		$transient = get_transient( "yikes_eme_merge_variables_{$list_id}" );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$path         = "{$this->base_path}/{$list_id}/merge-fields";
		$merge_fields = $this->loop_items( $path, 'merge_fields', 'merge_id' );

		if ( is_wp_error( $merge_fields ) ) {
			return $merge_fields;
		}

		// Get the whole merge object, minus the fields we already retrieved.
		$path         = add_query_arg( 'exclude_fields', 'merge_fields', $path );
		$merge_object = $this->get_from_api( $path );

		if ( is_wp_error( $merge_object ) ) {
			return $merge_object;
		}

		// The API doesn't give us the email field, so let's create that ourselves.
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

		$email_field = apply_filters( 'yikes-easy-mailchimp-email-address-field', $email_field, $list_id );

		array_unshift( $merge_fields, $email_field );
		$merge_object['merge_fields'] = $merge_fields;
		set_transient( "yikes_eme_merge_variables_{$list_id}", $merge_object, HOUR_IN_SECONDS );

		return $merge_object;
	}

	/**
	* Update a merge field for a particular list
	*
	* @author Kevin Utz
	*
	* @since 6.3.3
	*
	* @param string | $list_id			| The ID of the Mailchimp list
	* @param string | $field_id			| The ID of the merge field
	* @param array  | $field_data		| An array of field data constituting the body of our API request
	* @param bool	| $clear_transient	| Flag whether we should delete the transients associated with this list
	*
	* @return array | WP_Error
	*/
	public function update_merge_field( $list_id, $field_id, $field_data, $clear_transient = true ) {
		$path	= "{$this->base_path}/{$list_id}/merge-fields/{$field_id}";
		$field	= $this->patch_to_api( $path, $field_data );

		if ( is_wp_error( $field ) ) {
			return $field;
		}

		if ( $clear_transient === true ) {
			delete_transient( "yikes_eme_merge_variables_{$list_id}" );
		}

		return $field;
	}


	/**
	 * Get the Interest Categories for a particular list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id       The list ID.
	 * @param bool   $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error
	 */
	public function get_interest_categories( $list_id, $use_transient = true ) {
		$transient = get_transient( "yikes_eme_interest_categories_{$list_id}" );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

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

		set_transient( "yikes_eme_interest_categories_{$list_id}", $categories, HOUR_IN_SECONDS );

		return $categories;
	}

	/**
	 * Get segments for a list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id       The list ID.
	 * @param string $type          The segment type. Valid types are saved, static, or fuzzy.
	 * @param bool   $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error
	 */
	public function get_segments( $list_id, $type = 'saved', $use_transient = true ) {
		$transient = get_transient( "yikes_eme_segments_{$list_id}_{$type}" );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		// @todo: Include members in the segments?
		$base_path = "{$this->base_path}/{$list_id}/segments";
		$base_path = add_query_arg( 'type', $type, $base_path );
		$segments  = $this->maybe_return_error( $this->loop_items( $base_path, 'segments' ) );

		if ( is_wp_error( $segments ) ) {
			return $segments;
		}

		set_transient( "yikes_eme_segments_{$list_id}_{$type}", $segments, HOUR_IN_SECONDS );

		return $segments;
	}

	/**
	 * Get the members associated with a list.
	 *
	 * The members will be keyed to their email address.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id       The list ID.
	 * @param string $status        The subscriber status.
	 * @param bool   $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error
	 */
	public function get_members( $list_id, $status = 'subscribed', $use_transient = true ) {
		$transient = get_transient( "yikes_eme_members_{$list_id}" );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$base_path = add_query_arg( 'status', $status, "{$this->base_path}/{$list_id}/members" );
		$members   = $this->maybe_return_error( $this->loop_items( $base_path, 'members', 'email_address' ) );

		if ( is_wp_error( $members ) ) {
			return $members;
		}

		set_transient( "yikes_eme_members_{$list_id}", $members, HOUR_IN_SECONDS );

		return $members;
	}

	/**
	 * Get data for an individual member.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id       The list ID.
	 * @param string $member_id     The member ID. This is the MD5 hash of the email address.
	 * @param bool   $use_transient Whether to use a transient.
	 *
	 * @return array|WP_Error
	 */
	public function get_member( $list_id, $member_id, $use_transient = true ) {
		$transient = get_transient( "yikes_eme_member_{$list_id}_{$member_id}" );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$path   = "{$this->base_path}/{$list_id}/members/{$member_id}";
		$member = $this->maybe_return_error( $this->get_from_api( $path ) );

		if ( is_wp_error( $member ) ) {
			return $member;
		}

		set_transient( "yikes_eme_member_{$list_id}_{$member_id}", $member, HOUR_IN_SECONDS );

		return $member;
	}

	/**
	 * Get the lists that a member belongs to.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $member_id     The unique member ID. This is the MD5 hash of the email address.
	 * @param bool   $use_transient Whether to use a transient.
	 *
	 * @return array
	 */
	public function get_members_lists( $member_id, $use_transient = true ) {
		$transient = get_transient( "yikes_eme_member_lists_{$member_id}" );
		if ( false !== $transient && $use_transient ) {
			return $transient;
		}

		$member_lists = array();
		$list_ids     = $this->get_list_ids();
		if ( is_wp_error( $list_ids ) ) {
			return array();
		}

		foreach ( $list_ids as $list_id ) {
			$member = $this->get_member( $list_id, $member_id );
			if ( is_wp_error( $member ) ) {
				continue;
			}
			$member_lists[ $list_id ] = $member;
		}

		set_transient( "yikes_eme_member_lists_{$member_id}", $member_lists, HOUR_IN_SECONDS );

		return $member_lists;
	}

	/**
	 * Subscribe a member to the list.
	 *
	 * For keys to include in the $member_data array, see the Mailchimp API documentation (link below).
	 *
	 * @author Jeremy Pry
	 * @see    http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#edit-put_lists_list_id_members_subscriber_hash
	 *
	 * @param string $list_id     The list ID.
	 * @param string $member_id   The unique member ID. This is the MD5 hash of the email address.
	 * @param array  $member_data Array of member data.
	 *
	 * @return array|WP_Error
	 */
	public function member_subscribe( $list_id, $member_id, $member_data ) {
		$path     = "{$this->base_path}/{$list_id}/members/{$member_id}";
		$response = $this->put_to_api( $path, $member_data );

		// Clear the list members transient
		delete_transient( "yikes_eme_members_{$list_id}" );

		return $this->maybe_return_error( $response );
	}

	/**
	 * Unsubscribe a member from the list.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $list_id   The list ID.
	 * @param string $member_id The member ID. This is the MD5 hash of the email address.
	 *
	 * @return array|WP_Error
	 */
	public function member_unsubscribe( $list_id, $member_id ) {
		$path     = "{$this->base_path}/{$list_id}/members/{$member_id}";
		$body     = array( 'status' => 'unsubscribed' );
		$response = $this->patch_to_api( $path, $body );

		// Clear the list members transient
		delete_transient( "yikes_eme_members_{$list_id}" );

		return $this->maybe_return_error( $response );
	}

	/**
	 * Add a note to a member's profile
	 *
	 * @author Kevin Utz
	 *
	 * @param string $list_id    The list ID.
	 * @param string $member_id  The member ID. This is the MD5 hash of the email address.
	 * @param array  $notes_data The data for the user's note
	 *
	 * @return array|WP_Error
	 */
	public function create_member_note( $list_id, $member_id, $notes_data ) {
		$path     = "{$this->base_path}/{$list_id}/members/{$member_id}/notes";
		$response = $this->post_to_api( $path, $notes_data );

		return $this->maybe_return_error( $response );
	}

	/**
	 * Add a tag to a subscriber.
	 *
	 * @author Kevin Utz
	 *
	 * @param string $list_id The list ID.
	 * @param string $tag_id  The tag ID.
	 * @param array  $email   The user's email, in the format array( 'email_address' => 'theemail' ).
	 *
	 * @return array|WP_Error
	 */
	public function create_member_tags( $list_id, $tag_id, $email ) {
		$path     = "{$this->base_path}/{$list_id}/segments/{$tag_id}/members";
		$response = $this->post_to_api( $path, $email );

		return $this->maybe_return_error( $response );
	}

	/**
	 * Ensure that an array of limit fields includes defaults.
	 *
	 * Both the $fields and $required arrays should be key-based arrays.
	 *
	 * @author Jeremy Pry
	 *
	 * @param array $fields   The limit fields.
	 * @param array $required The required fields.
	 *
	 * @return array
	 */
	protected function compute_limit_fields( $fields, $required ) {
		// Don't add required fields if there aren't any limits at all.
		if ( empty( $fields ) || empty( $required ) ) {
			return $fields;
		}

		foreach ( $required as $key => $value ) {
			if ( ! isset( $fields[ $key ] ) ) {
				$fields[ $key ] = true;
			}
		}

		return $fields;
	}
}
