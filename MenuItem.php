<?php

/**
 * ThemePlate menu iterator
 *
 * @package ThemePlate
 * @since 0.1.0
 */

namespace ThemePlate;

use WP_Post;

/**
 * @phpstan-type DecoratedMenuItem WP_Post&object{
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
class MenuItem {

	public int $id;
	public string $slug;
	public int $parent;
	public string $label;
	public string $url;
	public string $target;
	public string $title;
	public string $info;
	/** @var string[] */
	public array $classes;
	public string $xfn;
	/** @var MenuItem[] */
	public array $children;
	public bool $is_active;
	public bool $is_active_parent;
	public bool $is_active_ancestor;

	/** @param DecoratedMenuItem $post */
	public function __construct( WP_Post $post ) {

		$this->id                 = $post->ID;
		$this->slug               = $post->post_name;
		$this->parent             = (int) $post->menu_item_parent;
		$this->label              = $post->title;
		$this->url                = $post->url;
		$this->target             = $post->target;
		$this->title              = $post->attr_title;
		$this->info               = $post->description;
		$this->classes            = array_filter( $post->classes, array( $this, 'filter' ) );
		$this->xfn                = $post->xfn;
		$this->children           = array();
		$this->is_active          = $post->current;
		$this->is_active_parent   = $post->current_item_parent;
		$this->is_active_ancestor = $post->current_item_ancestor;

	}


	protected function filter( string $classname ): bool {

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

}
