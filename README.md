# ThemePlate Menu

```php
use ThemePlate\Menu;

$primary_menu = new Menu( 'primary' );

$primary_menu->get();
$primary_menu->get_id();
$primary_menu->get_name();
$primary_menu->get_slug();
$primary_menu->get_count();
$primary_menu->get_items();
```

```php
<ul id="menu-<?php echo $primary_menu->get_id(); ?>" class="<?php echo $primary_menu->get_slug(); ?>">
 <?php foreach ( $primary_menu->get_items() as $menu_item ) : ?>
  <li class="<?php echo implode( ' ', $menu_item->classes ); ?><?php echo $menu_item->is_active ? ' active' : ''; ?>">
   <a href="<?php echo $menu_item->url; ?>"><?php echo $menu_item->label; ?></a>
  </li>
 <?php endforeach; ?>
</ul>
```
