<?php
/**
 * Handles all requests to the MailChimp API
 * @since 1.0
 */
class YIKES_MAILCHIMP_API {

	/**
	 * @var string The URL to the MailChimp API
	 */
	protected $api_url = 'https://api.mailchimp.com/3.0/';

	/**
	 * @var string The API key to use
	 */
	protected $api_key = '';

	/**
	 * @var string The error message of the latest API request (if any)
	 */
	protected $error_message = '';

	/**
	 * @var int The error code of the last API request (if any)
	 */
	protected $error_code = 0;

	/**
	 * @var boolean Boolean indicating whether the user is connected with MailChimp
	 */
	protected $connected;

	/**
	 * @var object The full response object of the latest API call
	 */
	protected $last_response;

	/**
	 * Constructor
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
		$dash_position = strpos( $api_key, '-' );
		if( $dash_position !== false ) {
			$this->api_url = 'https://' . substr( $api_key, $dash_position + 1 ) . '.api.mailchimp.com/3.0/';
		}
	}

	/**
	 * Show an error message to administrators
	 *
	 * @param string $message
	 *
	 * @return bool
	 */
	private function show_error( $message ) {

		if( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if( ! function_exists( 'add_settings_error' ) ) {
			return false;
		}

		add_settings_error( 'yikes-mailchimp-api', 'yikes-mailchimp-api-error', $message, 'error' );
		return true;
	}

	/**
	 * @param $message
	 *
	 * @return bool
	 */
	private function show_connection_error( $message ) {
		$message .= '<br /><br />' . sprintf( '<a href="%s">' . __( 'Read more about common connectivity issues.', 'yikes-inc-easy-mailchimp-extender' ) . '</a>', 'https://www.yikesplugins.com' );
		return $this->show_error( $message );
	}

	/**
	 * Pings the MailChimp API to see if we're connected
	 *
	 * The result is cached to ensure a maximum of 1 API call per page load
	 *
	 * @return boolean
	 */
	public function is_connected() {
		$result = $this->call( '/' );
		if( is_object( $result ) ) {
			// Msg key set? All good then!
			if( ! empty( $result->account_id ) ) {
				update_option( 'yikes-mc-api-validation', 'valid_api_key' );
				return;
			} else {
				// Uh oh. We got an error back.
				if( isset( $result ) ) {
					update_option( 'yikes-mc-api-invalid-key-response', $result->detail );
					update_option( 'yikes-mc-api-validation', 'invalid_api_key' );
					return;
				}
			}
		}
		update_option( 'yikes-mc-api-validation', 'invalid_api_key' );
	}

	/**
	 * Sends a subscription request to the MailChimp API
	*
	*	@param $merge_vars
	 */
	public function subscribe( $list_id, $merge_vars = array() ) {
		// if no merge vars, abort
		if( empty( $merge_vars ) ) {
			return false;
		}
		
		$this->empty_last_response();
		
		// do not make request when no api key was provided.
		if( empty( $this->api_key ) ) {
			return false;
		}
		
		$endpoint = 'lists/' . $list_id . '/members';
		
		$url = $this->api_url . $endpoint;
		$request_args = array(
			'timeout' => 10,
			'headers' => $this->get_headers(),
			'method' => 'POST',
			'body' => json_encode( $merge_vars )
		);
	
		$response = wp_remote_request( $url, $request_args );	
		
		try {
			$response = $this->parse_response( $response );;
		} catch( Exception $e ) {
			$this->error_message = $e->getMessage();
			$this->show_connection_error( $e->getMessage() );
			return false;
		}

		// store response
		$this->last_response = $response;
		
		// store error (if any)
		if( is_object( $response ) ) {
			if( ! empty( $response->status ) && $response->status != 200 ) {
				echo $response->detail;
				return false;
			}
			if( ! empty( $response->status ) && $response->status == 200 ) {
				return true;
			}
		}
	}

	/**
	 * Gets the Groupings for a given List
	 * @param int $list_id
	 * @return array|boolean
	 */
	public function get_list_groupings( $list_id ) {
		$result = $this->call( 'lists/interest-groupings', array( 'id' => $list_id ) );
		if( is_array( $result ) ) {
			return $result;
		}

		return false;
	}

	/**
	 * @param array $list_ids Array of ID's of the lists to fetch. (optional)
	 *
	 * @return bool
	 */
	public function get_lists( $list_ids = array() ) {
		$args = array(
			'limit' => 100,
			'sort_field' => 'web',
			'sort_dir' => 'ASC',
		);

		// set filter if the $list_ids parameter was set
		if( count( $list_ids ) > 0 ) {
			$args['filters'] = array(
				'list_id' => implode( ',', $list_ids )
			);
		}

		$result = $this->call( 'lists/list', $args );

		if( is_object( $result ) && isset( $result->data ) ) {
			return $result->data;
		}

		return false;
	}

	/**
	 * Get the lists an email address is subscribed to
	 *
	 * @param array|string $email
	 *
	 * @return array
	 */
	public function get_lists_for_email( $email ) {

		if( is_string( $email ) ) {
			$email = array(
				'email' => $email,
			);
		}

		$result = $this->call( 'helper/lists-for-email', array( 'email' => $email ) );

		if( ! is_array( $result ) ) {
			return false;
		}

		return $result;
	}

	/**
	 * Get lists with their merge_vars for a given array of list id's
	 * @param array $list_ids
	 * @return array|boolean
	 */
	public function get_lists_with_merge_vars( $list_ids ) {
		$result = $this->call( 'lists/merge-vars', array('id' => $list_ids ) );

		if( is_object( $result ) && isset( $result->data ) ) {
			return $result->data;
		}

		return false;
	}

	/**
	 * Gets the member info for one or multiple emails on a list
	 *
	 * @param string $list_id
	 * @param array $emails
	 * @return array
	 */
	public function get_subscriber_info( $list_id, array $emails ) {

		if( is_string( $emails ) ) {
			$emails = array( $emails );
		}

		$result = $this->call( 'lists/member-info', array(
				'id' => $list_id,
				'emails'  => $emails
			)
		);

		if( is_object( $result ) && isset( $result->data ) ) {
			return $result->data;
		}

		return false;
	}

	/**
	 * Checks if an email address is on a given list
	 *
	 * @param string $list_id
	 * @param string $email
	 * @return boolean
	 */
	public function list_has_subscriber( $list_id, $email ) {
		$member_info = $this->get_subscriber_info( $list_id, array( array( 'email' => $email ) ) );

		if( is_array( $member_info ) && isset( $member_info[0] ) ) {
			return ( $member_info[0]->status === 'subscribed' );
		}

		return false;
	}

	/**
	 * @param        $list_id
	 * @param array|string $email
	 * @param array  $merge_vars
	 * @param string $email_type
	 * @param bool   $replace_interests
	 *
	 * @return bool
	 */
	public function update_subscriber( $list_id, $email, $merge_vars = array(), $email_type = 'html', $replace_interests = false ) {

		// default to using email for updating
		if( ! is_array( $email ) ) {
			$email = array(
				'email' => $email
			);
		}

		$result = $this->call( 'lists/update-member', array(
				'id' => $list_id,
				'email'  => $email,
				'merge_vars' => $merge_vars,
				'email_type' => $email_type,
				'replace_interests' => $replace_interests
			)
		);

		if( is_object( $result ) ) {

			if( isset( $result->error ) ) {
				return false;
			} else {
				return true;
			}

		}

		return false;
	}

	/**
	 * Unsubscribes the given email or luid from the given MailChimp list
	 *
	 * @param string       $list_id
	 * @param array|string $struct
	 * @param bool         $delete_member
	 * @param bool         $send_goodbye
	 * @param bool         $send_notification
	 *
	 * @return bool
	 */
	public function unsubscribe( $list_id, $struct, $send_goodbye = true, $send_notification = false, $delete_member = false ) {

		if( ! is_array( $struct ) ) {
			// assume $struct is an email
			$struct = array(
				'email' => $struct
			);
		}

		$response = $this->call( 'lists/unsubscribe', array(
				'id' => $list_id,
				'email' => $struct,
				'delete_member' => $delete_member,
				'send_goodbye' => $send_goodbye,
				'send_notify' => $send_notification
			)
		);

		if( is_object( $response ) ) {

			if ( isset( $response->complete ) && $response->complete ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @see https://apidocs.mailchimp.com/api/2.0/ecomm/order-add.php
	 *
	 * @param array $order_data
	 *
	 * @return boolean
	 */
	public function add_ecommerce_order( array $order_data ) {
		$response = $this->call( 'ecomm/order-add', array( 'order' => $order_data ) );

		if( is_object( $response ) ) {

			// complete means success
			if ( isset( $response->complete ) && $response->complete ) {
				return true;
			}

			// 330 means order was already added: great
			if( isset( $response->code ) && $response->code == 330 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @see https://apidocs.mailchimp.com/api/2.0/ecomm/order-del.php
	 *
	 * @param string $store_id
	 * @param string $order_id
	 *
	 * @return bool
	 */
	public function delete_ecommerce_order( $store_id, $order_id ) {

		$data = array(
			'store_id' => $store_id,
			'order_id' => $order_id
		);

		$response = $this->call( 'ecomm/order-del', $data );

		if( is_object( $response ) ) {
			if ( isset( $response->complete ) && $response->complete ) {
				return true;
			}

			// Invalid order (order not existing). Good!
			if( isset( $response->code ) && $response->code == 330 ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Calls the MailChimp API
	 *
	 * @uses WP_HTTP
	 *
	 * @param string $method
	 * @param array $data
	 *
	 * @return object
	 */
	public function call( $method, array $data = array() ) {

		$this->empty_last_response();
		
		// do not make request when no api key was provided.
		if( empty( $this->api_key ) ) {
			update_option( 'yikes-mc-api-invalid-key-response', __( 'Enter your MailChimp API key in the field above.', 'yikes-inc-easy-mailchimp-extender' ) );
			return false;
		}
		
		$url = $this->api_url . $method . '.json';
		$request_args = array(
			'timeout' => 10,
			'headers' => $this->get_headers(),
		);
	
		$response = wp_remote_get( $url, $request_args );	
		
		try {
			$response = $this->parse_response( $response );
		} catch( Exception $e ) {
			$this->error_message = $e->getMessage();
			$this->show_connection_error( $e->getMessage() );
			if( $method == '/' ) { // used to validate api key
				update_option( 'yikes-mc-api-invalid-key-response' , $e->getMessage() );
				update_option( 'yikes-mc-api-validation', 'invalid_api_key' );
			}
			return false;
		}

		// store response
		$this->last_response = $response;

		// store error (if any)
		if( is_object( $response ) ) {
			if( ! empty( $response->error ) ) {
				$this->error_message = $response->error;
			}

			// store error code (if any)
			if( ! empty( $response->code ) ) {
				$this->error_code = (int) $response->code;
			}
		}

		return $response;
	}

	/**
	 * Checks if an error occured in the most recent request
	 * @return boolean
	 */
	public function has_error() {
		return ( ! empty( $this->error_message ) );
	}

	/**
	 * Gets the most recent error message
	 * @return string
	 */
	public function get_error_message() {
		return $this->error_message;
	}

	/**
	 * Gets the most recent error code
	 *
	 * @return int
	 */
	public function get_error_code() {
		return $this->error_code;
	}

	/**
	 * Get the most recent response object
	 *
	 * @return object
	 */
	public function get_last_response() {
		return $this->last_response;
	}

	/**
	 * Empties all data from previous response
	 */
	private function empty_last_response() {
		$this->last_response = null;
		$this->error_code = 0;
		$this->error_message = '';
	}

	/**
	 * Get the request headers to send to the MailChimp API
	 *
	 * @return array
	 */
	private function get_headers() {

		$headers = array(
			'Accept' => 'application/json',
			'Authorization' => 'apikey ' . $this->api_key, 
			'User-Agent' => 'Easy MailChimp by YIKES/6.0 (https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/)' 
		);

		// Copy Accept-Language from browser headers
		if( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$headers['Accept-Language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}

		return $headers;
	}

	/**
	 * @param array|WP_Error $response
	 * @return object
	 * @throws Exception
	 */
	private function parse_response( $response ) {

		if( is_wp_error( $response ) ) {
			throw new Exception( 'Error connecting to MailChimp. ' . $response->get_error_message(), (int) $response->get_error_code() );
		}

		// decode response body
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		if( ! is_null( $data ) ) {
			return $data;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$message = wp_remote_retrieve_response_message( $response );

		if( $code !== 200 ) {
			$message = sprintf( 'The MailChimp API server returned the following response: <em>%s %s</em>.', $code, $message );

			// check for Akamai firewall response
			if( $code === 403 ) {
				preg_match('/Reference (.*)/i', $body, $matches );

				if( ! empty( $matches[1] ) ) {
					$message .= '</strong><br /><br />' . sprintf( 'This usually means that your server is blacklisted by MailChimp\'s firewall. Please contact MailChimp support with the following reference number: %s </strong>', $matches[1] );
				}
			}
		}

		throw new Exception( $message, $code );
	}
}