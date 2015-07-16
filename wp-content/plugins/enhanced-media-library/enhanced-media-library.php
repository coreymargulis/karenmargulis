<?php
/*
Plugin Name: Enhanced Media Library
Plugin URI: http://wpUXsolutions.com
Description: This plugin will be handy for those who need to manage a lot of media files.
Version: 2.0.4.6
Author: wpUXsolutions
Author URI: http://wpUXsolutions.com
Text Domain: eml
Domain Path: /languages
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Copyright 2013-2015  wpUXsolutions  (email : wpUXsolutions@gmail.com)
*/




global $wp_version,
       $wpuxss_eml_version,
       $wpuxss_eml_dir,
       $wpuxss_eml_path;



$wpuxss_eml_version = '2.0.4.6';




include_once( 'core/mime-types.php' );
include_once( 'core/taxonomies.php' );

if( is_admin() ) { 

    include_once( 'core/options-pages.php' );
}
 




/**
 *  wpuxss_eml_on_init
 *
 *  @since    1.0
 *  @created  03/08/13
 */

add_action('init', 'wpuxss_eml_on_init', 12);

if ( ! function_exists( 'wpuxss_eml_on_init' ) ) {

    function wpuxss_eml_on_init() {
        
        global $wpuxss_eml_dir,
               $wpuxss_eml_path;
               
        
        $wpuxss_eml_dir = plugin_dir_url( __FILE__ );
        $wpuxss_eml_path = plugin_dir_path( __FILE__ );  
        
        load_textdomain( 'eml', $wpuxss_eml_path . 'languages/eml-' . get_locale() . '.mo' );
        wpuxss_eml_on_activation_update();


        $wpuxss_eml_taxonomies = get_option('wpuxss_eml_taxonomies');
        if ( empty($wpuxss_eml_taxonomies) ) $wpuxss_eml_taxonomies = array();
        
        // register eml taxonomies
        foreach ( $wpuxss_eml_taxonomies as $taxonomy => $params )
        {
            if ( $params['eml_media'] && !empty($params['labels']['singular_name']) && !empty($params['labels']['name']) )
            {
                register_taxonomy( 
                    $taxonomy, 
                    'attachment', 
                    array(
                        'labels' => $params['labels'],
                        'public' => true,
                        'show_admin_column' => $params['show_admin_column'],
                        'show_in_nav_menus' => $params['show_in_nav_menus'],
                        'hierarchical' => $params['hierarchical'],
                        'update_count_callback' => '_update_generic_term_count',
                        'sort' => $params['sort'],
                        'rewrite' => array( 
                            'slug' => $params['rewrite']['slug'], 
                            'with_front' => $params['rewrite']['with_front'] 
                        )
                    ) 
                );
            }
        }
    }
}




/**
 *  wpuxss_eml_on_wp_loaded
 *
 *  @since    1.0
 *  @created  03/11/13
 */


add_action( 'wp_loaded', 'wpuxss_eml_on_wp_loaded' );

if ( ! function_exists( 'wpuxss_eml_on_wp_loaded' ) ) {

    function wpuxss_eml_on_wp_loaded() {
    
        $wpuxss_eml_taxonomies = get_option('wpuxss_eml_taxonomies');
        if ( empty($wpuxss_eml_taxonomies) ) $wpuxss_eml_taxonomies = array();
        $taxonomies = get_taxonomies(array(),'object');
        
        // discover 'foreign' taxonomies
        foreach ( $taxonomies as $taxonomy => $params )
        {
            if ( !empty($params->object_type) && !array_key_exists($taxonomy,$wpuxss_eml_taxonomies) && !in_array('revision',$params->object_type) && !in_array('nav_menu_item',$params->object_type) && $taxonomy != 'post_format' )
            {
                $wpuxss_eml_taxonomies[$taxonomy] = array(
                    'eml_media' => 0,
                    'admin_filter' => 0,
                    'media_uploader_filter' => 0,
                    'media_popup_taxonomy_edit' => 0,
                    'show_admin_column' => isset($params->show_admin_column) ? $params->show_admin_column : 0,
                    'show_in_nav_menus' => isset($params->show_in_nav_menus) ? $params->show_in_nav_menus : 0,
                    'hierarchical' => $params->hierarchical ? 1 : 0,
                    'sort' => isset($params->sort) ? $params->sort : 0
                );
                
                if ( in_array('attachment',$params->object_type) )
                    $wpuxss_eml_taxonomies[$taxonomy]['assigned'] = 1;
                else 
                    $wpuxss_eml_taxonomies[$taxonomy]['assigned'] = 0;
            }
        }
    
        // assign/unassign taxonomies to atachment
        foreach ( $wpuxss_eml_taxonomies as $taxonomy => $params )
        {
            if ( $params['assigned'] )
                register_taxonomy_for_object_type( $taxonomy, 'attachment' );
            
            if ( ! $params['assigned'] )
                unregister_taxonomy_for_object_type( $taxonomy, 'attachment' );
        }
        
        global $wp_taxonomies;
        
        // update_count_callback for attachment taxonomies if needed
        foreach ( $taxonomies as $taxonomy => $params )
        {
            if ( in_array('attachment',$params->object_type) )
            {
                if ( !isset($wp_taxonomies[$taxonomy]->update_count_callback) || empty($wp_taxonomies[$taxonomy]->update_count_callback) )
                    $wp_taxonomies[$taxonomy]->update_count_callback = '_update_generic_term_count';
            }
        }
    
        update_option( 'wpuxss_eml_taxonomies', $wpuxss_eml_taxonomies );
    }
}





/**
 *  wpuxss_eml_admin_enqueue_scripts
 *
 *  @since    1.1.1
 *  @created  07/04/14
 */
 
add_action( 'admin_enqueue_scripts', 'wpuxss_eml_admin_enqueue_scripts' );

if ( ! function_exists( 'wpuxss_eml_admin_enqueue_scripts' ) ) {
    
    function wpuxss_eml_admin_enqueue_scripts() {
    
        global $wpuxss_eml_version,
               $wpuxss_eml_dir,
               $current_screen;
               
               
        $media_library_mode = get_user_option( 'media_library_mode', get_current_user_id() ) ? get_user_option( 'media_library_mode', get_current_user_id() ) : 'grid';
    
    
        // admin styles
        wp_enqueue_style( 
            'wpuxss-eml-admin-custom-style', 
            $wpuxss_eml_dir . 'css/eml-admin.css',
            false, 
            $wpuxss_eml_version,
            'all' 
        );
        
        // scripts for list view :: /wp-admin/upload.php    
        if ( isset( $current_screen ) && 'upload' === $current_screen->base && 'list' === $media_library_mode )
        {
            wp_enqueue_script(
                'wpuxss-eml-media-list-script',
                $wpuxss_eml_dir . 'js/eml-media-list.js',
                array('jquery'),
                $wpuxss_eml_version,
                true
            );
        }
    }
}




/**
 *  wpuxss_eml_enqueue_media
 *
 *  @since    2.0
 *  @created  04/09/14
 */
 
add_action( 'wp_enqueue_media', 'wpuxss_eml_enqueue_media' );

if ( ! function_exists( 'wpuxss_eml_enqueue_media' ) ) {

    function wpuxss_eml_enqueue_media() {
        
        global $wpuxss_eml_version,
               $wpuxss_eml_dir,
               $wp_version,
               $current_screen;
               
               
        if ( ! is_admin() ) {
            return;
        }
           
     
        $media_library_mode = get_user_option( 'media_library_mode', get_current_user_id() ) ? get_user_option( 'media_library_mode', get_current_user_id() ) : 'grid';
        
        $wpuxss_eml_tax_options = get_option('wpuxss_eml_tax_options');
    
        // taxonomies for passing to media uploader's filter
        $wpuxss_eml_taxonomies = get_option('wpuxss_eml_taxonomies');
        if ( empty($wpuxss_eml_taxonomies) ) $wpuxss_eml_taxonomies = array();
        
        $taxonomies_array = array();
        $compat_taxonomies_to_hide = array();
        $compat_taxonomies_to_show = array();
        $compat_taxonomies = array();
        
        foreach ( get_object_taxonomies('attachment','object') as $taxonomy ) 
        {
            $terms_array = array();
            $terms = array();
            
            if ( $wpuxss_eml_taxonomies[$taxonomy->name]['media_uploader_filter'] && function_exists( 'wp_terms_checklist' ) )
            {
                ob_start();
                
                    wp_terms_checklist( 0, array( 'taxonomy' => $taxonomy->name, 'checked_ontop' => false, 'walker' => new Walker_Media_Taxonomy_Uploader_Filter() ) );
                    
                    $html = '';
                    if ( ob_get_contents() != false )
                        $html = ob_get_contents();
                
                ob_end_clean();
                
                $terms = array_filter( explode('|', $html) );
                
                if ( !empty($terms) )
                {
                    foreach ($terms as $term)
                    {
                        $term = explode('>', $term);
                        array_push($terms_array, array('term_id' => $term[0], 'term_name' => $term[1]));
                    }
                    $taxonomies_array[$taxonomy->name] = array(
                        'singular_name' => $taxonomy->labels->singular_name,
                        'plural_name'      => $taxonomy->labels->name,
                        'term_list'       => $terms_array
                    );
                }
            }
            
            if ( ! $wpuxss_eml_taxonomies[$taxonomy->name]['media_popup_taxonomy_edit'] ) {
                $compat_taxonomies_to_hide[] = $taxonomy->name;
            }
            elseif ( $wpuxss_eml_tax_options['edit_all_as_hierarchical'] || $taxonomy->hierarchical ) {
                $compat_taxonomies_to_show[] = $taxonomy->name;
            }
            
            $compat_taxonomies[] = $taxonomy->name;
            
        } //endforeach
        
        
        // generic scripts 
        
        wp_enqueue_script(
            'wpuxss-eml-media-models-script',
            $wpuxss_eml_dir . 'js/eml-media-models.js',
            array('media-models'),
            $wpuxss_eml_version,
            true
        ); 
    
        wp_enqueue_script(
            'wpuxss-eml-media-views-script',
            $wpuxss_eml_dir . 'js/eml-media-views.js',
            array('media-views'),
            $wpuxss_eml_version,
            true
        );
    
    
        wp_localize_script( 
            'wpuxss-eml-media-models-script', 
            'wpuxss_eml_attachments_edit_nonce',
            wp_create_nonce( 'eml-attachments-edit-nonce' )
        );
        
        $media_views_l10n = array(
            'taxonomies' => $taxonomies_array,
            'compat_taxonomies' => $compat_taxonomies,
            'compat_taxonomies_to_hide' => $compat_taxonomies_to_hide,
            'is_tax_compat' => count( $compat_taxonomies_to_show ) ? 1 : 0,
            'force_filters' => $wpuxss_eml_tax_options['force_filters'],
            'wp_version' => $wp_version,
            'uncategorized' => __( 'All Uncategorized', 'eml' ),
            'filter_by'           => __( 'Filter by ', 'eml' ),
            'in'            => __( 'All ', 'eml' ),
            'not_in'        => __( 'Not in ', 'eml' ),
            'reset_filters'  => __( 'Reset All Filters', 'eml' )
        ); 
        
        wp_localize_script( 
            'wpuxss-eml-media-views-script', 
            'wpuxss_eml_media_views_l10n',
            $media_views_l10n
        );    
        
        // scripts for grid view :: /wp-admin/upload.php    
        if ( isset( $current_screen ) && 'upload' === $current_screen->base && 'grid' === $media_library_mode )
        {
            wp_enqueue_script(
                'wpuxss-eml-media-grid-script',
                $wpuxss_eml_dir . 'js/eml-media-grid.js',
                array('media'),
                $wpuxss_eml_version,
                true
            );
        }
        
        // scripts for Appearance -> Header
        if ( isset( $current_screen ) && 'appearance_page_custom-header' === $current_screen->base ) {
            
            wp_enqueue_script(
                'wpuxss-eml-custom-header-script',
                $wpuxss_eml_dir . 'js/eml-custom-header.js',
                array('custom-header'),
                $wpuxss_eml_version,
                true
            );
        }
        
        // scripts for Appearance -> Background
        if ( isset( $current_screen ) && 'appearance_page_custom-background' === $current_screen->base ) {
            
            wp_enqueue_script(
                'wpuxss-eml-custom-background-script',
                $wpuxss_eml_dir . 'js/eml-custom-background.js',
                array('custom-background'),
                $wpuxss_eml_version,
                true
            );
        }
        
        
        // scripts for /wp-admin/customize.php
        if ( isset( $current_screen ) && 'customize' === $current_screen->base ) 
        {
            wp_enqueue_script(
                'wpuxss-eml-customize-controls-script',
                $wpuxss_eml_dir . 'js/eml-customize-controls.js',
                array('customize-controls'),
                $wpuxss_eml_version,
                true
            );
        }
    }
}





/**
 *  wpuxss_eml_on_activation_update
 *
 *  @since    2.0.4
 *  @created  30/01/15
 */

if ( ! function_exists( 'wpuxss_eml_on_activation_update' ) ) {
       
    function wpuxss_eml_on_activation_update() {
        
        global $wpuxss_eml_version;
        
        $wpuxss_eml_old_version = get_option('wpuxss_eml_version', false); 

        if ( version_compare( $wpuxss_eml_version, $wpuxss_eml_old_version, '<>' ) ) 
        {
            update_option('wpuxss_eml_version', $wpuxss_eml_version );

            if ( empty($wpuxss_eml_old_version) )
            {            
                $wpuxss_eml_taxonomies['media_category'] = array(
                    'assigned' => 1,
                    'eml_media' => 1,
                    'public' => 1,
                    
                    'labels' => array(
                        'name' => 'Media Categories',
                        'singular_name' => 'Media Category',
                        'menu_name' => 'Media Categories',
                        'all_items' => 'All Media Categories',
                        'edit_item' => 'Edit Media Category',
                        'view_item' => 'View Media Category',
                        'update_item' => 'Update Media Category',
                        'add_new_item' => 'Add New Media Category',
                        'new_item_name' => 'New Media Category Name',
                        'parent_item' => 'Parent Media Category',
                        'parent_item_colon' => 'Parent Media Category:',
                        'search_items' => 'Search Media Categories'
                    ),
                    
                    'hierarchical' => 1,
                    
                    'show_admin_column' => 1,
                    'admin_filter' => 1, // list view filter
                    'media_uploader_filter' => 1, // grid view filter
                    'media_popup_taxonomy_edit' => 0,
                    
                    'show_in_nav_menus' => 1,
                    'sort' => 0,
                    'rewrite' => array( 
                        'slug' => 'media_category', 
                        'with_front' => 1 
                    ) 
                );
                
                $wpuxss_eml_tax_options = array(
                    'tax_archives' => 1,
                    'edit_all_as_hierarchical' => 0,
                    'force_filters' => 0
                );
                
                $allowed_mimes = get_allowed_mime_types();
                
                foreach ( wp_get_mime_types() as $type => $mime )
                {
                    $wpuxss_eml_mimes[$type] = array(
                        'mime'     => $mime,
                        'singular' => $mime,
                        'plural'   => $mime,
                        'filter'   => 0,
                        'upload'   => isset($allowed_mimes[$type]) ? 1 : 0
                    );
                }
                
                $wpuxss_eml_mimes['pdf']['singular'] = 'PDF';
                $wpuxss_eml_mimes['pdf']['plural'] = 'PDFs';
                $wpuxss_eml_mimes['pdf']['filter'] = 1;
                
                update_option( 'wpuxss_eml_taxonomies', $wpuxss_eml_taxonomies );
                update_option( 'wpuxss_eml_tax_options', $wpuxss_eml_tax_options );
                
                update_option( 'wpuxss_eml_mimes', $wpuxss_eml_mimes );
                update_option( 'wpuxss_eml_mimes_backup', $wpuxss_eml_mimes );
                
                return;
            }
            
            if ( version_compare( $wpuxss_eml_old_version, '2.0.2', '<' ) ) 
            {     
                $wpuxss_eml_taxonomies = get_option('wpuxss_eml_taxonomies');
                
                foreach( (array) $wpuxss_eml_taxonomies as $taxonomy => $params ) 
                {  
                    if ( $params['eml_media'] ) 
                    {
                        $wpuxss_eml_taxonomies[$taxonomy]['rewrite']['with_front'] = 1;
                    }
                }
                
                update_option( 'wpuxss_eml_taxonomies', $wpuxss_eml_taxonomies );
            }
            
            if ( version_compare( $wpuxss_eml_old_version, '2.0.4', '<' ) )
            {
                $wpuxss_eml_taxonomies = get_option('wpuxss_eml_taxonomies');
                
                foreach( (array) $wpuxss_eml_taxonomies as $taxonomy => $params ) 
                { 
                    $wpuxss_eml_taxonomies[$taxonomy]['media_popup_taxonomy_edit'] = 1;
                }
                
                $wpuxss_eml_tax_options = array(
                    'tax_archives' => 1,
                    'edit_all_as_hierarchical' => 0,
                    'force_filters' => 0
                );
                
                update_option( 'wpuxss_eml_taxonomies', $wpuxss_eml_taxonomies );
                update_option( 'wpuxss_eml_tax_options', $wpuxss_eml_tax_options );
            }
        } // endif
    }
}

?>
