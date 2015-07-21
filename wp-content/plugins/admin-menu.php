<?php 
/*
Plugin Name: Admin Menu Dashboard
Plugin URI: http://coreymargulis.com
Description: This plugin supports the tutorial in wptutsplus. It customizes the WordPress dashboard. Modified
Version: 1.0
Author: Corey Margulis
Author URI: http://coreymargulis.com
License: GPLv2
*/

// Rename Posts to News in Menu
function wptutsplus_change_post_menu_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'News';
    $submenu['edit.php'][5][0] = 'News Items';
    $submenu['edit.php'][10][0] = 'Add News Item';
}
add_action( 'admin_menu', 'wptutsplus_change_post_menu_label' );

// Remove menu items for all but Administrators
function wptutsplus_remove_comments_menu_item() {
     $user = wp_get_current_user();
     if ( ! $user->has_cap( 'manage_options' ) ) 
    {
        remove_menu_page( 'edit-comments.php' );
        remove_menu_page( 'tools.php' );
        remove_menu_page( 'index.php' );
        remove_submenu_page( 'themes.php','themes.php' );
        remove_submenu_page( 'themes.php','customize.php' );
        remove_submenu_page( 'themes.php','widgets.php' );
        remove_submenu_page( 'themes.php','options-general.php' );
        remove_submenu_page( 'themes.php','theme-editor.php' );
    }
}
add_action( 'admin_menu', 'wptutsplus_remove_comments_menu_item' );

// Move Pages above Media
function wptutsplus_change_menu_order( $menu_order ) {
    return array(
        'index.php',
        'edit.php?post_type=page',
        'edit.php?post_type=staff',
        'edit.php?post_type=services',
        'edit.php?post_type=towerorganizations',
        'edit.php',
        'upload.php',
        'nav-menus.php',
    );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'wptutsplus_change_menu_order' );


//------------------------------------------------------------//


// // remove columns from pages listing screen
// function wptutsplus_remove_pages_listing_tags( $columns ) {
//     unset( $columns[ 'comments' ] );
//     return $columns;
// }
// add_action( 'manage_pages_columns', 'wptutsplus_remove_pages_listing_tags' );






?>