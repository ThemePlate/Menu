<?php

/**
 * ThemePlate menu iterator
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

class Menu {

	private $object;
	private $items = [];


	public function __construct( $location ) {

		$locations = get_nav_menu_locations();

		$this->object = wp_get_nav_menu_object( $locations[ $location ] ?: $location );

		$this->items = wp_get_nav_menu_items( $this->object );

	}


	public function get() {

		return $this->object;

	}


	public function get_items() {

		return $this->items;

	}

}
