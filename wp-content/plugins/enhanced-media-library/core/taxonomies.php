<?php




/**
 *  unregister_taxonomy_for_object_type
 *
 *  Unassign taxonomy (duplicates native WP(Since: WordPress 3.7.0) for back compatibility)
 *  Based on /wp-includes/taxonomy.php
 *
 *  @since    2.0
 *  @created  05/08/14
 */

if( ! function_exists( 'unregister_taxonomy_for_object_type' ) ) { 

    function unregister_taxonomy_for_object_type( $taxonomy, $object_type ) {
        
        global $wp_taxonomies;
        
        if ( ! isset( $wp_taxonomies[ $taxonomy ] ) )
            return false;
        
        if ( ! get_post_type_object( $object_type ) )
            return false;
        
        $key = array_search( $object_type, $wp_taxonomies[ $taxonomy ]->object_type, true );
        if ( false === $key )
            return false;
        
        unset( $wp_taxonomies[ $taxonomy ]->object_type[ $key ] );
        return true;
    }
}




/**
 *  wpuxss_eml_taxonomies_validate
 *
 *  @type     callback function
 *  @since    1.0
 *  @created  28/09/13
 */ 

if( ! function_exists( 'wpuxss_eml_taxonomies_validate' ) ) { 

    function wpuxss_eml_taxonomies_validate($input) {
        		
        if ( !$input ) $input = array();
    
        foreach ( $input as $taxonomy => $params )
        {	
            $sanitized_taxonomy = sanitize_key($taxonomy);
            
            if ( $sanitized_taxonomy !== $taxonomy ) 
            {
                $input[$sanitized_taxonomy] = $input[$taxonomy];
                unset($input[$taxonomy]);
                $taxonomy = $sanitized_taxonomy;
            }
            
            $input[$taxonomy]['hierarchical'] = isset($params['hierarchical']) ? 1: 0;	
            $input[$taxonomy]['sort'] = isset($params['sort']) ? 1: 0;	
            $input[$taxonomy]['show_admin_column'] = isset($params['show_admin_column']) ? 1: 0;
            $input[$taxonomy]['show_in_nav_menus'] = isset($params['show_in_nav_menus']) ? 1: 0;
            $input[$taxonomy]['assigned'] = isset($params['assigned']) ? 1: 0;
            $input[$taxonomy]['admin_filter'] = isset($params['admin_filter']) ? 1: 0;
            $input[$taxonomy]['media_uploader_filter'] = isset($params['media_uploader_filter']) ? 1: 0;
            $input[$taxonomy]['media_popup_taxonomy_edit'] = isset($params['media_popup_taxonomy_edit']) ? 1: 0;
            $input[$taxonomy]['rewrite']['with_front'] = isset($params['rewrite']['with_front']) ? 1: 0;
            $input[$taxonomy]['rewrite']['slug'] = isset($params['rewrite']['slug']) ? wpuxss_eml_sanitize_slug( $params['rewrite']['slug'], $taxonomy ) : '';
            
            if ( isset($params['labels']) )
            {
                $default_labels = array(
                    'menu_name' => $params['labels']['name'],
                    'all_items' => 'All ' . $params['labels']['name'],
                    'edit_item' => 'Edit ' . $params['labels']['singular_name'],
                    'view_item' => 'View ' . $params['labels']['singular_name'], 
                    'update_item' => 'Update ' . $params['labels']['singular_name'],
                    'add_new_item' => 'Add New ' . $params['labels']['singular_name'],
                    'new_item_name' => 'New ' . $params['labels']['singular_name'] . ' Name',
                    'parent_item' => 'Parent ' . $params['labels']['singular_name'],
                    'search_items' => 'Search ' . $params['labels']['name']
                );
                
                foreach ( $params['labels'] as $label => $value )
                {
                    $input[$taxonomy]['labels'][$label] = sanitize_text_field($value);
                    
                    if ( empty($value) && isset($default_labels[$label]) ) 
                    {
                        $input[$taxonomy]['labels'][$label] = sanitize_text_field($default_labels[$label]);
                    }
                }
            }
        }
            
        return $input;
    }
}





/**
 *  wpuxss_eml_sanitize_slug
 *
 *  @since    2.0.4
 *  @created  07/02/15
 */ 

if( ! function_exists( 'wpuxss_eml_sanitize_slug' ) ) { 

    function wpuxss_eml_sanitize_slug( $slug, $fallback_slug = '' ) {
        
        $slug_array = explode ( '/', $slug );
        $slug_array = array_filter( $slug_array );
        $slug_array = array_map ( 'remove_accents', $slug_array );
        $slug_array = array_map ( 'sanitize_title_with_dashes', $slug_array );
        
        $slug = implode ( '/', $slug_array );
     
        if ( '' === $slug || false === $slug )
            $slug = $fallback_slug;
     
        return $slug;
    }
}






/**
 *  wpuxss_eml_tax_options_validate
 *
 *  @type     callback function
 *  @since    2.0.4
 *  @created  28/01/15
 */ 

if( ! function_exists( 'wpuxss_eml_tax_options_validate' ) ) {
    
    function wpuxss_eml_tax_options_validate( $input ) {
    
        foreach ( (array)$input as $key => $option ) {
            $input[$key] = intval( $option );
        }
    
        return $input;
    }
}




/**
 *  wpuxss_eml_ajax_query_attachments
 *
 *  Based on /wp-admin/includes/ajax-actions.php
 *
 *  @since    1.0
 *  @created  03/08/13
 */

add_action( 'wp_ajax_query-attachments', 'wpuxss_eml_ajax_query_attachments', 0 );

if( ! function_exists( 'wpuxss_eml_ajax_query_attachments' ) ) {
        
    function wpuxss_eml_ajax_query_attachments() {
        
        global $wp_version;
        
        if ( ! current_user_can( 'upload_files' ) )
            wp_send_json_error();
            
        $query = isset( $_REQUEST['query'] ) ? (array) $_REQUEST['query'] : array();
        
        $uncategorized = ( isset( $query['uncategorized'] ) && $query['uncategorized'] ) ? 1 : 0;
        
        if ( version_compare( $wp_version, '4.1', '<' ) ) {
    
            if ( isset( $query['year'] ) && $query['year'] && 
                 isset( $query['monthnum'] ) && $query['monthnum'] ) {
                     
                $query['m'] = $query['year'] . $query['monthnum'];
            } else {
                
                $query['m'] = '';
            }
            
            $query = array_intersect_key( $query, array_flip( array(
                's', 'order', 'orderby', 'posts_per_page', 'paged', 'post_mime_type',
                'post_parent', 'post__in', 'post__not_in', 'm'
            ) ) );
        } else {
        
            $query = array_intersect_key( $query, array_flip( array(
                's', 'order', 'orderby', 'posts_per_page', 'paged', 'post_mime_type',
                'post_parent', 'post__in', 'post__not_in', 'year', 'monthnum'
            ) ) );
        }
        
        foreach ( get_object_taxonomies( 'attachment', 'names' ) as $taxonomy )
        {		
            if ( $uncategorized ) 
            {
                $terms = get_terms( $taxonomy, array('fields'=>'ids','get'=>'all') );
                
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $terms,
                    'operator' => 'NOT IN',
                );	
            } 
            else       
            {
                if ( isset( $_REQUEST['query'][$taxonomy] ) && $_REQUEST['query'][$taxonomy] ) 
                {
                    if( is_numeric( $_REQUEST['query'][$taxonomy] ) ) 
                    {
                        $tax_query[] = array(
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => array( $_REQUEST['query'][$taxonomy] )
                        );	
                    }
                    elseif ( 'not_in' === $_REQUEST['query'][$taxonomy] )
                    {
                        $terms = get_terms( $taxonomy, array('fields'=>'ids','get'=>'all') );
                
                        $tax_query[] = array(
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => $terms,
                            'operator' => 'NOT IN',
                        );	
                    }
                    elseif ( 'in' === $_REQUEST['query'][$taxonomy] )
                    {
                        $terms = get_terms( $taxonomy, array('fields'=>'ids','get'=>'all') );
                
                        $tax_query[] = array(
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => $terms,
                            'operator' => 'IN',
                        );	
                    }
                }
            }
        } // endforeach
            
        if ( ! empty( $tax_query ) )
        {
            $query['tax_query'] = $tax_query;
        }

        $query['post_type'] = 'attachment';
        
        if ( MEDIA_TRASH
            && ! empty( $_REQUEST['query']['post_status'] )
            && 'trash' === $_REQUEST['query']['post_status'] ) {
            $query['post_status'] = 'trash';
        } else {
            $query['post_status'] = 'inherit';
        }
    
        if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) )
            $query['post_status'] .= ',private';
    
        $query = apply_filters( 'ajax_query_attachments_args', $query );
        $query = new WP_Query( $query );
    
        $posts = array_map( 'wp_prepare_attachment_for_js', $query->posts );
        $posts = array_filter( $posts );
    
        wp_send_json_success( $posts );
    }
}





/**
 *  wpuxss_eml_restrict_manage_posts
 *
 *  Adds taxonomy filters to Media Library List View
 *
 *  @since    1.0
 *  @created  11/08/13
 */

add_action('restrict_manage_posts','wpuxss_eml_restrict_manage_posts');

if( ! function_exists( 'wpuxss_eml_restrict_manage_posts' ) ) {
    
    function wpuxss_eml_restrict_manage_posts() {
        
        global $current_screen,
               $wp_query;
               
            
        $media_library_mode = get_user_option( 'media_library_mode'  ) ? get_user_option( 'media_library_mode'  ) : 'grid';
    
    
        if ( isset( $current_screen ) && 'upload' === $current_screen->base && 'list' === $media_library_mode )
        {
            $wpuxss_eml_taxonomies = get_option('wpuxss_eml_taxonomies');
            
            $uncategorized = ( isset( $_REQUEST['attachment-filter'] ) && 'uncategorized' === $_REQUEST['attachment-filter'] ) ? 1 : 0;
            
            foreach ( get_object_taxonomies( 'attachment', 'object' ) as $taxonomy ) 
            {
                if ( $wpuxss_eml_taxonomies[$taxonomy->name]['admin_filter'] )
                {   
                    echo "<label for='{$taxonomy->name}' class='screen-reader-text'>" . __('Filter by ','eml') . "{$taxonomy->labels->singular_name}</label>";
               
                    $selected = ( ! $uncategorized && isset( $wp_query->query[$taxonomy->name] ) ) ? $wp_query->query[$taxonomy->name] : 0;
                    
                    wp_dropdown_categories(
                        array(
                            'show_option_all'    =>  __( 'Filter by ', 'eml' ) . $taxonomy->labels->singular_name,
                            'show_option_in'     =>  '— ' . __( 'All ', 'eml' ) . $taxonomy->labels->name . ' —',
                            'show_option_not_in' =>  '— ' . __( 'Not in ', 'eml' ) . $taxonomy->labels->singular_name . ' —',
                            'taxonomy'           =>  $taxonomy->name,
                            'name'               =>  $taxonomy->name,
                            'orderby'            =>  'name',
                            'selected'           =>  $selected,
                            'hierarchical'       =>  true,
                            'show_count'         =>  false,
                            'hide_empty'         =>  false,
                            'hide_if_empty'      =>  true,
                            'class'              =>  'eml-attachment-filters'
                        )
                    );
                }
            } // endforeach
        }
    }
}





/**
 *  wpuxss_eml_dropdown_cats
 *
 *  Modifies taxonomy filters in Media Library List View
 *
 *  @since    2.0.4.5
 *  @created  19/04/15
 */
 
add_filter( 'wp_dropdown_cats', 'wpuxss_eml_dropdown_cats', 10, 2 );

if( ! function_exists( 'wpuxss_eml_dropdown_cats' ) ) {

    function wpuxss_eml_dropdown_cats( $output, $r ) {
        
        global $current_screen; 
        
        if ( ! is_admin() ) {
            return $output;
        }
        
        $media_library_mode = get_user_option( 'media_library_mode' ) ? get_user_option( 'media_library_mode' ) : 'grid';
        
        if ( isset( $current_screen ) && 'upload' === $current_screen->base && 'list' === $media_library_mode )
        {
            $whole_select = $output;
            
            $options_array = array();
            while ( strlen( $whole_select ) >= 7 && false !== ( $option_pos = strpos( $whole_select, '<option', 7 ) ) )
            {
                $options_array[] = substr($whole_select, 0, $option_pos);
                $whole_select = substr($whole_select, $option_pos);
            }
            $options_array[] = $whole_select;
            
            $new_output = ''; 
            
            if ( isset( $r['show_option_in'] ) && $r['show_option_in'] ) 
            {
                $show_option_in = $r['show_option_in'];
                $selected = ( 'in' === strval($r['selected']) ) ? " selected='selected'" : '';
                $new_output .= "\t<option value='in'$selected>$show_option_in</option>\n";
            }
        
            if ( isset( $r['show_option_not_in'] ) && $r['show_option_not_in'] ) 
            {
                $show_option_not_in = $r['show_option_not_in'];
                $selected = ( 'not_in' === strval($r['selected']) ) ? " selected='selected'" : '';
                $new_output .= "\t<option value='not_in'$selected>$show_option_not_in</option>\n";
            }
            
            array_splice( $options_array, 2, 0, $new_output );
            
            $output = implode('', $options_array);
        }
        
        return $output;
    }
}





/**
 *  wpuxss_eml_custom_media
 *
 *  Replaces upload.php with the custom one
 * 
 *  @since    2.0.4
 *  @created  21/02/15
 */
 
add_action( 'load-upload.php', 'wpuxss_eml_custom_media' );

if( ! function_exists( 'wpuxss_eml_custom_media' ) ) {
    
    function wpuxss_eml_custom_media() {
        
        global $wpdb;
        
        require_once( ABSPATH . 'wp-admin/includes/class-wp-media-list-table.php' );
        require_once( 'class-eml-media-list-table.php' );
        require_once( 'eml-upload.php' );
    
        exit();
    }
}





/**
 *  wpuxss_eml_parse_tax_query
 *
 *  @since    2.0.4
 *  @created  19/02/15
 */
 
add_action( 'parse_tax_query', 'wpuxss_eml_parse_tax_query' );

if( ! function_exists( 'wpuxss_eml_parse_tax_query' ) ) {
    
    function wpuxss_eml_parse_tax_query( $query ) {
        
        global $current_screen; 
        
        
        if ( ! is_admin() ) {
            return;
        }
        
        /**
         *  actually just the fix for 
         *
         *  Fatal error: Call to undefined function get_userdata() 
         *  in /wp-includes/user.php on line 360
         *
         *  caused by, in particular, "Woocommerce Product Tabs" plugin
         *  /admin/class-woocommerce-product-tabs-admin.php
         *  line 56
         *  get_posts()!!!
         *
         *  very strange error! test it more carefully!
         */
        require_once( ABSPATH . 'wp-includes/pluggable.php' );
        
        
        $media_library_mode = get_user_option( 'media_library_mode' ) ? get_user_option( 'media_library_mode' ) : 'grid'; 
        
        
        if ( isset( $current_screen ) && 'upload' === $current_screen->base && 'list' === $media_library_mode )
        {
            $uncategorized = ( isset( $_REQUEST['attachment-filter'] ) && 'uncategorized' === $_REQUEST['attachment-filter'] ) ? 1 : 0;
            
            if ( isset( $_REQUEST['category'] ) )
            {
                $query->query['category'] = $query->query_vars['category'] = $_REQUEST['category'];
            }
            
            if ( isset( $_REQUEST['post_tag'] ) )
            {
                $query->query['post_tag'] = $query->query_vars['post_tag'] = $_REQUEST['post_tag'];
            }
            
            if ( isset( $query->query_vars['taxonomy'] ) && isset( $query->query_vars['term'] ) )
            {
                $tax = $query->query_vars['taxonomy'];
                $term = get_term_by( 'slug', $query->query_vars['term'], $tax );
                
                if ( $term )
                {	
                    $query->query_vars[$tax] = $term->term_id;
                    $query->query[$tax] = $term->term_id;
                    
                    unset( $query->query_vars['taxonomy'] );
                    unset( $query->query_vars['term'] );
                    
                    unset( $query->query['taxonomy'] );
                    unset( $query->query['term'] );
                }
            }
            
            foreach ( get_object_taxonomies( 'attachment','names' ) as $taxonomy ) 
            {
                if ( $uncategorized )
                {
                    $terms = get_terms( $taxonomy, array('fields'=>'ids','get'=>'all') );
                    
                    $tax_query[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $terms,
                        'operator' => 'NOT IN',
                    );	 
                    
                    if ( isset( $query->query[$taxonomy] ) ) unset( $query->query[$taxonomy] );
                    if ( isset( $query->query_vars[$taxonomy] ) ) unset( $query->query_vars[$taxonomy] );
                }
                else
                {                    
                    if ( isset( $query->query[$taxonomy] ) && $query->query[$taxonomy] ) 
                    {
                        if( is_numeric( $query->query[$taxonomy] ) ) 
                        {
                            $tax_query[] = array(
                                'taxonomy' => $taxonomy,
                                'field' => 'term_id',
                                'terms' => array( $query->query[$taxonomy] )
                            );	
                        }
                        elseif ( 'not_in' === $query->query[$taxonomy] )
                        {
                            $terms = get_terms( $taxonomy, array('fields'=>'ids','get'=>'all') );
                    
                            $tax_query[] = array(
                                'taxonomy' => $taxonomy,
                                'field' => 'term_id',
                                'terms' => $terms,
                                'operator' => 'NOT IN',
                            );	
                        }
                        elseif ( 'in' === $query->query[$taxonomy] )
                        {
                            $terms = get_terms( $taxonomy, array('fields'=>'ids','get'=>'all') );
                    
                            $tax_query[] = array(
                                'taxonomy' => $taxonomy,
                                'field' => 'term_id',
                                'terms' => $terms,
                                'operator' => 'IN',
                            );	
                        }
                    }
                }
            } // endforeach
            
            if ( ! empty( $tax_query ) )
            {
                $query->tax_query = new WP_Tax_Query( $tax_query );
            }
            
        } // endif
    }
}





/**
 *  wpuxss_eml_attachment_fields_to_edit
 *
 *  Based on /wp-admin/includes/media.php
 * 
 *  @since    1.0
 *  @created  14/08/13
 */
 
add_filter( 'attachment_fields_to_edit', 'wpuxss_eml_attachment_fields_to_edit', 10, 2 );

if( ! function_exists( 'wpuxss_eml_attachment_fields_to_edit' ) ) {
    
    function wpuxss_eml_attachment_fields_to_edit( $form_fields, $post ) {
        
        $wpuxss_eml_tax_options = get_option('wpuxss_eml_tax_options');
        
        foreach ( get_attachment_taxonomies($post) as $taxonomy ) {
            
            $t = (array) get_taxonomy($taxonomy);
            if ( ! $t['show_ui'] )
                continue;
            if ( empty($t['label']) )
                $t['label'] = $taxonomy;
            if ( empty($t['args']) )
                $t['args'] = array();
    
            $terms = get_object_term_cache($post->ID, $taxonomy);
            if ( false === $terms )
                $terms = wp_get_object_terms($post->ID, $taxonomy, $t['args']);
                
            $values = array();
            
            foreach ( $terms as $term )
                $values[] = $term->slug;
                
            $t['value'] = join(', ', $values);
            $t['show_in_edit'] = false;
            
            if ( ( $wpuxss_eml_tax_options['edit_all_as_hierarchical'] || $t['hierarchical'] ) && function_exists( 'wp_terms_checklist' ) )
            {
                ob_start();
                
                    wp_terms_checklist( $post->ID, array( 'taxonomy' => $taxonomy, 'checked_ontop' => false, 'walker' => new Walker_Media_Taxonomy_Checklist() ) );
                    
                    if ( ob_get_contents() != false )
                        $html = '<ul class="term-list">' . ob_get_contents() . '</ul>';
                    else
                        $html = '<ul class="term-list"><li>No ' . $t['label'] . ' found.</li></ul>';
                
                ob_end_clean();
                
                unset( $t['value'] );
                
                $t['input'] = 'html';
                $t['html'] = $html; 
            }
        
            $form_fields[$taxonomy] = $t;
        }
    
        return $form_fields;
    }
}




/**
 *  Walker_Media_Taxonomy_Checklist
 *
 *  Based on /wp-includes/category-template.php
 *
 *  @since    1.0
 *  @created  09/09/13
 */

if( ! class_exists('Walker_Media_Taxonomy_Checklist') ) {
    
    class Walker_Media_Taxonomy_Checklist extends Walker {
        
        var $tree_type = 'category';
        var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); 
    
        function start_lvl( &$output, $depth = 0, $args = array() ) 
        {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent<ul class='children'>\n";
        }
    
        function end_lvl( &$output, $depth = 0, $args = array() ) 
        {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }
    
        function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) 
        {
            extract($args);
            
            if ( empty($taxonomy) )
                $taxonomy = 'category';
    
            $class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
            $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . "<label class='selectit'><input value='0' type='hidden' name='tax_input[{$taxonomy}][{$category->term_id}]' /><input value='1' type='checkbox' name='tax_input[{$taxonomy}][{$category->term_id}]' id='in-{$taxonomy}-{$category->term_id}'" . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . " />" . esc_html( apply_filters('the_category', $category->name )) . "</label>";
        }
    
        function end_el( &$output, $category, $depth = 0, $args = array() ) 
        {
            $output .= "</li>\n";
        }
    }
}




/**
 *  Walker_Media_Taxonomy_Uploader_Filter
 *
 *  Based on /wp-includes/category-template.php
 *
 *  @since    1.0.1
 *  @created  05/11/13
 */

if( ! class_exists('Walker_Media_Taxonomy_Uploader_Filter') ) {
     
    class Walker_Media_Taxonomy_Uploader_Filter extends Walker {
        
        var $tree_type = 'category';
        var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); 
    
        function start_lvl( &$output, $depth = 0, $args = array() ) 
        {
            $output .= "";
        }
    
        function end_lvl( &$output, $depth = 0, $args = array() ) 
        {
            $output .= "";
        }
    
        function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) 
        {
            extract($args);
    
            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $depth); 
            $output .= $category->term_id . '>' . $indent . esc_html( apply_filters('the_category', $category->name )) . '|';
        }
    
        function end_el( &$output, $category, $depth = 0, $args = array() ) 
        {
                $output .= "";
        }
    }
}





/** 
 *  wpuxss_eml_save_attachment_compat
 *
 *  Based on /wp-admin/includes/ajax-actions.php
 *
 *  @since    1.0.6
 *  @created  06/14/14
 */

add_action( 'wp_ajax_save-attachment-compat', 'wpuxss_eml_save_attachment_compat', 0 );

if( ! function_exists('wpuxss_eml_save_attachment_compat') ) {
        
    function wpuxss_eml_save_attachment_compat() {	
    
        if ( ! isset( $_REQUEST['id'] ) )
            wp_send_json_error();
    
        if ( ! $id = absint( $_REQUEST['id'] ) )
            wp_send_json_error();
    
        if ( empty( $_REQUEST['attachments'] ) || empty( $_REQUEST['attachments'][ $id ] ) )
            wp_send_json_error();
        $attachment_data = $_REQUEST['attachments'][ $id ];
    
        check_ajax_referer( 'update-post_' . $id, 'nonce' );
    
        if ( ! current_user_can( 'edit_post', $id ) )
            wp_send_json_error();
    
        $post = get_post( $id, ARRAY_A );
    
        if ( 'attachment' != $post['post_type'] )
            wp_send_json_error();
    
        /** This filter is documented in wp-admin/includes/media.php */
        $post = apply_filters( 'attachment_fields_to_save', $post, $attachment_data );
    
        if ( isset( $post['errors'] ) ) {
            $errors = $post['errors']; // @todo return me and display me!
            unset( $post['errors'] );
        }
    
        wp_update_post( $post );
    
        foreach ( get_attachment_taxonomies( $post ) as $taxonomy ) 
        {    
            if ( isset( $attachment_data[ $taxonomy ] ) )
            {
                wp_set_object_terms( $id, array_map( 'trim', preg_split( '/,+/', $attachment_data[ $taxonomy ] ) ), $taxonomy, false );
            }
            elseif ( isset( $_REQUEST['tax_input'] ) && isset( $_REQUEST['tax_input'][ $taxonomy ] ) ) 
            {
                $term_ids = array_keys( $_REQUEST['tax_input'][ $taxonomy ], 1 );
                $term_ids = array_map( 'intval', $term_ids );
                
                wp_set_object_terms( $id, $term_ids, $taxonomy, false );
            }
        }
    
        if ( ! $attachment = wp_prepare_attachment_for_js( $id ) )
            wp_send_json_error();
        
        wp_send_json_success( $attachment );
    }
}





/**
 *  wpuxss_eml_pre_get_posts
 *
 *  Taxonomy archive specific query (front-end)
 *
 *  @since    1.0
 *  @created  03/08/13
 */

add_action( 'pre_get_posts', 'wpuxss_eml_pre_get_posts', 99 );

if( ! function_exists('wpuxss_eml_pre_get_posts') ) {
        
    function wpuxss_eml_pre_get_posts( $query ) { 
    
        $wpuxss_eml_tax_options = get_option('wpuxss_eml_tax_options');
        
        if ( $wpuxss_eml_tax_options['tax_archives'] ) 
        { 
            $wpuxss_eml_taxonomies = get_option('wpuxss_eml_taxonomies');
            
            foreach ( (array) $wpuxss_eml_taxonomies as $taxonomy => $params )
            {
                if ( $params['assigned'] && $params['eml_media'] && $query->is_main_query() && is_tax($taxonomy) && !is_admin() )
                {
                    $query->set( 'post_type', 'attachment' );
                    $query->set( 'post_status', 'inherit' );
                }	
            }
        }
    }
}

?>