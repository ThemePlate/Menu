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

		$this->object = wp_get_nav_menu_object( $locations[ $location ] ?? $location );

		$items = wp_get_nav_menu_items( $this->object );

		_wp_menu_item_classes_by_context( $items );

		$this->items = $this->prepare( $items ?: [] );

	}


	private function prepare( $items, $parent = 0 ) {

		$prepared = [];

		foreach ( $items as $item ) {
			if ( (int) $item->menu_item_parent === $parent ) {
				$children = $this->prepare( $items, (int) $item->ID );

				$prepared[] = (object) [
					'id'       => $item->ID,
					'slug'     => $item->post_name,
					'parent'   => (int) $item->menu_item_parent,
					'label'    => $item->title,
					'url'      => $item->url,
					'target'   => $item->target,
					'title'    => $item->attr_title,
					'info'     => $item->description,
					'classes'  => array_filter( $item->classes ),
					'xfn'      => $item->xfn,
					'children' => $children,

					'is_active'          => $item->current,
					'is_active_parent'   => $item->current_item_parent,
					'is_active_ancestor' => $item->current_item_ancestor,
				];
			}
		}

		return $prepared;

	}


	public function get() {

		return $this->object;

	}


	public function get_id() {

		return $this->object->term_id ?? 0;

	}


	public function get_name() {

		return $this->object->name ?? '';

	}


	public function get_slug() {

		return $this->object->slug ?? '';

	}


	public function get_count() {

		return $this->object->count ?? 0;

	}


	public function get_items() {

		return $this->items;

	}

}
