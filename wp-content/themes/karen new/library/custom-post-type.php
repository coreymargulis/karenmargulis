<?php
/* Bones Custom Post Type Example
This page walks you through creating
a custom post type and taxonomies. You
can edit this one or copy the following code
to create another one.

I put this in a separate file so as to
keep it organized. I find it easier to edit
and change things if they are concentrated
in their own file.

Developed by: Eddie Machado
URL: http://themble.com/bones/
*/

// Flush rewrite rules for custom post types
add_action( 'after_switch_theme', 'bones_flush_rewrite_rules' );

// Flush your rewrite rules
function bones_flush_rewrite_rules() {
	flush_rewrite_rules();
}

// let's create the function for the custom type
function custom_post_example() {
	// creating (registering) the custom type
	register_post_type( 'Workshops', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		// let's now add all the options for this post type
		array( 'labels' => array(
			'name' => __( 'Workshops & Classes', 'bonestheme' ), /* This is the Title of the Group */
			'singular_name' => __( 'Workshop or Class', 'bonestheme' ), /* This is the individual type */
			'all_items' => __( 'All Workshops & Classes', 'bonestheme' ), /* the all items menu item */
			'add_new' => __( 'Add New', 'bonestheme' ), /* The add new menu item */
			'add_new_item' => __( 'Add New Workshop or Class', 'bonestheme' ), /* Add New Display Title */
			'edit' => __( 'Edit', 'bonestheme' ), /* Edit Dialog */
			'edit_item' => __( 'Edit Workshops & Classes', 'bonestheme' ), /* Edit Display Title */
			'new_item' => __( 'New Workshop or Class', 'bonestheme' ), /* New Display Title */
			'view_item' => __( 'View Workshop or Class', 'bonestheme' ), /* View Display Title */
			'search_items' => __( 'Search Workshops & Classes', 'bonestheme' ), /* Search Custom Type Title */
			'not_found' =>  __( 'Nothing found in the Database.', 'bonestheme' ), /* This displays if there are no entries yet */
			'not_found_in_trash' => __( 'Nothing found in Trash', 'bonestheme' ), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'This is a Workshop or Class', 'bonestheme' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => 'dashicons-megaphone', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'workshops', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'workshops', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky')
		) /* end of options */
	); /* end of register post type */

	/* this adds your post categories to your custom post type */
	register_taxonomy_for_object_type( 'category', 'workshops' );
	/* this adds your post tags to your custom post type */
	register_taxonomy_for_object_type( 'post_tag', 'workshops' );

	// creating (registering) the custom type
	register_post_type( 'Paintings', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		// let's now add all the options for this post type
		array( 'labels' => array(
			'name' => __( 'Paintings', 'bonestheme' ), /* This is the Title of the Group */
			'singular_name' => __( 'Painting', 'bonestheme' ), /* This is the individual type */
			'all_items' => __( 'All Paintings', 'bonestheme' ), /* the all items menu item */
			'add_new' => __( 'Add New', 'bonestheme' ), /* The add new menu item */
			'add_new_item' => __( 'Add New Painting', 'bonestheme' ), /* Add New Display Title */
			'edit' => __( 'Edit', 'bonestheme' ), /* Edit Dialog */
			'edit_item' => __( 'Edit Painting', 'bonestheme' ), /* Edit Display Title */
			'new_item' => __( 'New Painting', 'bonestheme' ), /* New Display Title */
			'view_item' => __( 'View Painting', 'bonestheme' ), /* View Display Title */
			'search_items' => __( 'Search Paintings', 'bonestheme' ), /* Search Custom Type Title */
			'not_found' =>  __( 'Nothing found in the Database.', 'bonestheme' ), /* This displays if there are no entries yet */
			'not_found_in_trash' => __( 'Nothing found in Trash', 'bonestheme' ), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'This is a Painting', 'bonestheme' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => 'dashicons-art', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'paintings', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'paintings', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky')
		) /* end of options */
	); /* end of register post type */

	/* this adds your post categories to your custom post type */
	// register_taxonomy_for_object_type( 'category', 'paintings' );
	/* this adds your post tags to your custom post type */
	register_taxonomy_for_object_type( 'post_tag', 'paintings' );

}


	// adding the function to the Wordpress init
	add_action( 'init', 'custom_post_example');

	/*
	for more information on taxonomies, go here:
	http://codex.wordpress.org/Function_Reference/register_taxonomy
	*/

	// now let's add custom categories (these act like categories)
	register_taxonomy( 'subject',
		array('paintings'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => true,     /* if this is true, it acts like categories */
			'labels' => array(
				'name' => __( 'Subjects', 'bonestheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Subject', 'bonestheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Search Subjects', 'bonestheme' ), /* search title for taxomony */
				'all_items' => __( 'All Subjects', 'bonestheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent Subject', 'bonestheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Subject:', 'bonestheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Edit Subject', 'bonestheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Update Subjects', 'bonestheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Add New Subject', 'bonestheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'New Subject', 'bonestheme' ) /* name title for taxonomy */
			),
			'show_admin_column' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'subject' ),
		)
	);

	register_taxonomy( 'region',
		array('paintings'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => true,     /* if this is true, it acts like categories */
			'labels' => array(
				'name' => __( 'Regions', 'bonestheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Region', 'bonestheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Search Region', 'bonestheme' ), /* search title for taxomony */
				'all_items' => __( 'All regions', 'bonestheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent Region', 'bonestheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Region:', 'bonestheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Edit Region', 'bonestheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Update Regions', 'bonestheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Add New Region', 'bonestheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'New Region', 'bonestheme' ) /* name title for taxonomy */
			),
			'show_admin_column' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'region' ),
		)
	);

	// now let's add custom tags (these act like categories)
	register_taxonomy( 'medium',
		array('paintings'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => false,    /* if this is false, it acts like tags */
			'labels' => array(
				'name' => __( 'Mediums', 'bonestheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Custom medium', 'bonestheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Search mediums', 'bonestheme' ), /* search title for taxomony */
				'all_items' => __( 'All mediums', 'bonestheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent medium', 'bonestheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Custom Tag:', 'bonestheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Edit Custom Tag', 'bonestheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Update Custom Tag', 'bonestheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Add New Custom Tag', 'bonestheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'New Custom Tag Name', 'bonestheme' ) /* name title for taxonomy */
			),
			'show_admin_column' => true,
			'show_ui' => true,
			'query_var' => true,
		)
	);

	register_taxonomy( 'season',
		array('paintings'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => false,    /* if this is false, it acts like tags */
			'labels' => array(
				'name' => __( 'Seasons', 'bonestheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Custom season', 'bonestheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Search seasons', 'bonestheme' ), /* search title for taxomony */
				'all_items' => __( 'All seasons', 'bonestheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent season', 'bonestheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Custom Tag:', 'bonestheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Edit Custom Tag', 'bonestheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Update Custom Tag', 'bonestheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Add New Custom Tag', 'bonestheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'New Custom Tag Name', 'bonestheme' ) /* name title for taxonomy */
			),
			'show_admin_column' => true,
			'show_ui' => true,
			'query_var' => true,
		)
	);

	register_taxonomy( 'colors',
		array('paintings'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => false,    /* if this is false, it acts like tags */
			'labels' => array(
				'name' => __( 'Colors', 'bonestheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Color', 'bonestheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Search colors', 'bonestheme' ), /* search title for taxomony */
				'all_items' => __( 'All colors', 'bonestheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent color', 'bonestheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Color:', 'bonestheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Edit Color', 'bonestheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Update Color', 'bonestheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Add New Color', 'bonestheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'New Color', 'bonestheme' ) /* name title for taxonomy */
			),
			'show_admin_column' => true,
			'show_ui' => true,
			'query_var' => true,
		)
	);

	/*
		looking for custom meta boxes?
		check out this fantastic tool:
		https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
	*/


?>
