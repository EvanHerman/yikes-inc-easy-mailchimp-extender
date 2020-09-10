<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package YIKES\EasyForms
 * @author  Freddie Mixell
 * @license GPL2
 */

namespace YIKES\EasyForms;

/**
 * Class Container
 *
 * @since   %VERSION%
 * @package YIKES\EasyForms
 */
final class Container {

	/**
	 * The registered services for the container.
	 *
	 * @since %VERSION%
	 * @var array
	 */
	protected $services = [];

	/**
	 * Container constructor.
	 *
	 * @param array $services Services to register with the container.
	 */
	public function __construct( array $services = [] ) {
		$this->services = $services ?: [];
	}

	/**
	 * Get the services from the container.
	 *
	 * @since %VERSION%
	 * @return array
	 */
	public function get_services() {
		return $this->services;
	}

	/**
	 * Add a service to the container.
	 *
	 * @since %VERSION%
	 *
	 * @param string $service Service class name.
	 */
	public function add_service( $service ) {
		$this->services[ $service ] = true;
	}

	/**
	 * Remove a service from the container.
	 *
	 * @since %VERSION%
	 *
	 * @param string $service Service class name.
	 */
	public function remove_service( $service ) {
		unset( $this->services[ $service ] );
	}
}