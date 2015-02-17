<?php 
/*
Plugin Name: Custom Admin Menu
Plugin URI: http://rachelmccollin.co.uk
Description: This plugin supports the tutorial in wptutsplus. It customizes the WordPress dashboard.
Version: 1.0
Author: Rachel McCollin
Author URI: http://rachelmccollin.com
License: GPLv2
*/

// Rename Posts to News in Menu
// function wptutsplus_change_post_menu_label() {
//     global $menu;
//     global $submenu;
//     $menu[5][0] = 'News';
//     $submenu['edit.php'][5][0] = 'News Items';
//     $submenu['edit.php'][10][0] = 'Add News Item';
// }
// add_action( 'admin_menu', 'wptutsplus_change_post_menu_label' );

// Remove Comments menu item for all but Administrators
function wptutsplus_remove_comments_menu_item() {
    // $user = wp_get_current_user();
    // if ( ! $user->has_cap( 'manage_options' ) ) 
    {
        //remove_menu_page( 'edit-comments.php' );
        //remove_menu_page( 'tools.php' );
    }
}
add_action( 'admin_menu', 'wptutsplus_remove_comments_menu_item' );

// Move Pages above Media
function wptutsplus_change_menu_order( $menu_order ) {
    return array(
        'index.php',
        'edit.php',
        'edit.php?post_type=page',
        'upload.php',
        'edit-comments.php',
    );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'wptutsplus_change_menu_order' );


//------------------------------------------------------------//


// remove columns from pages listing screen
function wptutsplus_remove_pages_listing_tags( $columns ) {
    unset( $columns[ 'comments' ] );
    return $columns;
}
add_action( 'manage_pages_columns', 'wptutsplus_remove_pages_listing_tags' );






?>