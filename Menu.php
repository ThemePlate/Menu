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

		$items = wp_get_nav_menu_items( $this->object );

		$this->items = $this->prepare( $items );

	}


	private function prepare( $items, $parent = 0 ) {

		$prepared = [];

		foreach ( $items as $item ) {
			if ( (int) $item->menu_item_parent === $parent ) {
				$children = $this->prepare( $items, (int) $item->ID );

				$prepared[] = [
					'id'       => $item->ID,
					'slug'     => $item->post_name,
					'parent'   => $item->menu_item_parent,
					'label'    => $item->title,
					'url'      => $item->url,
					'target'   => $item->target,
					'title'    => $item->attr_title,
					'info'     => $item->description,
					'classes'  => $item->classes,
					'xfn'      => $item->xfn,
					'children' => $children,
				];
			}
		}

		return $prepared;

	}


	public function get() {

		return $this->object;

	}


	public function get_items() {

		return $this->items;

	}

}
