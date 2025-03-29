<?php

/**
 * ThemePlate menu iterator
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use WP_Term;

/**
 * @phpstan-type DecoratedMenuItem \WP_Post&object{
 *      menu_item_parent: string,
 *      title: string,
 *      url: string,
 *      target: string,
 *      attr_title: string,
 *      description: string,
 *      classes: array<int, string>,
 *      xfn: string,
 *      current: bool,
 *      current_item_ancestor: bool,
 *      current_item_parent: bool,
 * }
 */
class Menu {

	private ?WP_Term $object = null;
	private array $items     = array();


	public function __construct( string $location ) {

		$locations = get_nav_menu_locations();

		$this->object = wp_get_nav_menu_object( $locations[ $location ] ?? $location );

		$items = wp_get_nav_menu_items( $this->object );

		_wp_menu_item_classes_by_context( $items );

		// phpcs:ignore Universal.Operators.DisallowShortTernary
		$this->items = $this->prepare( $items ?: array() );

	}


	private function filter( string $classname ): bool {

		if ( '' === $classname ) {
			return false;
		}

		$blacklist = array(
			'menu-item',
			'menu-item-home',
			'menu-item-privacy-policy',
			'current-menu-item',
			'current-menu-ancestor',
			'current-menu-parent',
			'page_item',
			'current_page_item',
			'current_page_parent',
			'current_page_ancestor',
		);

		if ( in_array( $classname, $blacklist, true ) ) {
			return false;
		}

		// menu-item-type-test menu-item-object-test page-item-2 current-test-ancestor current-test-parent
		$pattern = '^(menu-item-(type|object)-\w+|page-item-\d+|current-\w+-(parent|ancestor))$';

		return in_array( preg_match( '/' . $pattern . '/', $classname ), array( 0, false ), true );

	}


	/** @param DecoratedMenuItem[] $items */
	private function prepare( array $items, int $parent_id = 0 ): array {

		$prepared = array();

		foreach ( $items as $item ) {
			if ( (int) $item->menu_item_parent === $parent_id ) {
				$children = $this->prepare( $items, (int) $item->ID );

				$prepared[] = (object) array(
					'id'                 => $item->ID,
					'slug'               => $item->post_name,
					'parent'             => (int) $item->menu_item_parent,
					'label'              => $item->title,
					'url'                => $item->url,
					'target'             => $item->target,
					'title'              => $item->attr_title,
					'info'               => $item->description,
					'classes'            => array_filter( $item->classes, array( $this, 'filter' ) ),
					'xfn'                => $item->xfn,
					'children'           => $children,

					'is_active'          => $item->current,
					'is_active_parent'   => $item->current_item_parent,
					'is_active_ancestor' => $item->current_item_ancestor,
				);
			}
		}

		return $prepared;

	}


	public function get(): WP_Term {

		return $this->object;

	}


	public function get_id(): int {

		return $this->object->term_id ?? 0;

	}


	public function get_name(): string {

		return $this->object->name ?? '';

	}


	public function get_slug(): string {

		return $this->object->slug ?? '';

	}


	public function get_count(): int {

		return $this->object->count ?? 0;

	}


	public function get_items(): array {

		return $this->items;

	}

}
