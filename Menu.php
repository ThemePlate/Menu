<?php

/**
 * ThemePlate menu iterator
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use WP_Term;

/** @phpstan-import-type DecoratedMenuItem from MenuItem */
class Menu {

	private ?WP_Term $object = null;

	/** @var MenuItem[] */
	private array $items = array();


	public function __construct( string $location ) {

		$locations = get_nav_menu_locations();

		$object = wp_get_nav_menu_object( $locations[ $location ] ?? $location );

		if ( ! $object ) {
			return;
		}

		$this->object = $object;

		$items = wp_get_nav_menu_items( $this->object );

		if ( ! $items ) {
			return;
		}

		_wp_menu_item_classes_by_context( $items );

		$this->items = $this->prepare( $items );

	}


	/**
	 * @param DecoratedMenuItem[] $items
	 * @return MenuItem[]
	 */
	private function prepare( array $items, int $parent_id = 0 ): array {

		$prepared = array();

		foreach ( $items as $item ) {
			if ( (int) $item->menu_item_parent === $parent_id ) {
				$item = new MenuItem( $item );

				$item->children = $this->prepare( $items, (int) $item->id );

				$prepared[] = $item;
			}
		}

		return $prepared;

	}


	public function get(): ?WP_Term {

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


	/** @return MenuItem[] */
	public function get_items(): array {

		return $this->items;

	}

}
