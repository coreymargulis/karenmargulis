<?php
/*
Author: Eddie Machado
URL: htp://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, ect.
*/

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'library/bones.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
// require_once( 'library/admin.php' );

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function bones_ahoy() {

  //Allow editor style.
  add_editor_style();

  // let's get language support going, if you need it
  load_theme_textdomain( 'bonestheme', get_template_directory() . '/library/translation' );

  // USE THIS TEMPLATE TO CREATE CUSTOM POST TYPES EASILY
  //require_once( 'library/custom-post-type.php' );

  // launching operation cleanup
  add_action( 'init', 'bones_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'bones_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'bones_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  bones_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'bones_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'bones_filter_ptags_on_images' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'bones_excerpt_more' );

} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'bones-thumb-600', 600, 150, true );
add_image_size( 'bones-thumb-300', 300, 100, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 100 sized image,
we would use the function:
<?php the_post_thumbnail( 'bones-thumb-300' ); ?>
for the 600 x 150 image:
<?php the_post_thumbnail( 'bones-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'bones_custom_image_sizes' );

function bones_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'bones-thumb-600' => __('600px by 150px'),
        'bones-thumb-300' => __('300px by 100px'),
    ) );
}

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

/************* THEME CUSTOMIZE *********************/

/* 
  A good tutorial for creating your own Sections, Controls and Settings:
  http://code.tutsplus.com/series/a-guide-to-the-wordpress-theme-customizer--wp-33722
  
  Good articles on modifying the default options:
  http://natko.com/changing-default-wordpress-theme-customization-api-sections/
  http://code.tutsplus.com/tutorials/digging-into-the-theme-customizer-components--wp-27162
  
  To do:
  - Create a js for the postmessage transport method
  - Create some sanitize functions to sanitize inputs
  - Create some boilerplate Sections, Controls and Settings
*/

function bones_theme_customizer($wp_customize) {
  // $wp_customize calls go here.
  //
  // Uncomment the below lines to remove the default customize sections 

  // $wp_customize->remove_section('title_tagline');
  // $wp_customize->remove_section('colors');
  // $wp_customize->remove_section('background_image');
  // $wp_customize->remove_section('static_front_page');
  // $wp_customize->remove_section('nav');

  // Uncomment the below lines to remove the default controls
  // $wp_customize->remove_control('blogdescription');
  
  // Uncomment the following to change the default section titles
  // $wp_customize->get_section('colors')->title = __( 'Theme Colors' );
  // $wp_customize->get_section('background_image')->title = __( 'Images' );
}

add_action( 'customize_register', 'bones_theme_customizer' );

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function bones_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'bonestheme' ),
		'description' => __( 'The first (primary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	/*
	to add more sidebars or widgetized areas, just copy
	and edit the above sidebar code. In order to call
	your new sidebar just use the following code:

	Just change the name to whatever your new
	sidebar's id is, for example:

	register_sidebar(array(
		'id' => 'sidebar2',
		'name' => __( 'Sidebar 2', 'bonestheme' ),
		'description' => __( 'The second (secondary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	To call the sidebar in your template, you can just copy
	the sidebar.php file and rename it to your sidebar's name.
	So using the above example, it would be:
	sidebar-sidebar2.php

	*/
} // don't remove this bracket!


/************* COMMENT LAYOUT *********************/

// Comment Layout
function bones_comments( $comment, $args, $depth ) {
   $GLOBALS['comment'] = $comment; ?>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
    <article  class="cf">
      <header class="comment-author vcard">
        <?php
        /*
          this is the new responsive optimized comment image. It used the new HTML5 data-attribute to display comment gravatars on larger screens only. What this means is that on larger posts, mobile sites don't have a ton of requests for comment images. This makes load time incredibly fast! If you'd like to change it back, just replace it with the regular wordpress gravatar call:
          echo get_avatar($comment,$size='32',$default='<path_to_url>' );
        */
        ?>
        <?php // custom gravatar call ?>
        <?php
          // create variable
          $bgauthemail = get_comment_author_email();
        ?>
        <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>?s=40" class="load-gravatar avatar avatar-48 photo" height="40" width="40" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />
        <?php // end custom gravatar call ?>
        <?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'bonestheme' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'bonestheme' ),'  ','') ) ?>
        <time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__( 'F jS, Y', 'bonestheme' )); ?> </a></time>

      </header>
      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert alert-info">
          <p><?php _e( 'Your comment is awaiting moderation.', 'bonestheme' ) ?></p>
        </div>
      <?php endif; ?>
      <section class="comment_content cf">
        <?php comment_text() ?>
      </section>
      <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </article>
  <?php // </li> is added by WordPress automatically ?>
<?php
} // don't remove this bracket!


/*
This is a modification of a function found in the
twentythirteen theme where we can declare some
external fonts. If you're using Google Fonts, you
can replace these fonts, change it in your scss files
and be up and running in seconds.
*/
function bones_fonts() {
  wp_enqueue_style('googleFonts', 'http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic');
}

add_action('wp_enqueue_scripts ', 'bones_fonts');

// Remove caption from image upload
add_filter( 'disable_captions', create_function('$a', 'return true;') );






/************* WOOCOMMERCE *********************/


/*
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<section id="main" class="gallery clearfix">';
}

function my_theme_wrapper_end() {
  echo '</section>';
}
*/

// Disable Woocommerce CSS
add_filter( 'woocommerce_enqueue_styles', '__return_false' );


/*--------------------
HIDE THINGS 
--------------------*/

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
add_filter( 'wc_product_sku_enabled', '__return_false' );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
add_filter( 'wc_product_weight_enabled', '__return_false' );
add_filter( 'wc_product_dimensions_enabled', '__return_false' );
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

// Remove tabs 
function woo_remove_product_tabs( $tabs ) {
 
    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab
 
    return $tabs;
 
}
// Remove "buy" button from product list page
add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );

function remove_add_to_cart_buttons() {
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
}

// Hide default sorting drop-down from WooCommerce
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );



/*--------------------
TEXT CHANGES
--------------------*/

// Change "add to cart" text



// Out of stock message

add_filter('woocommerce_get_availability', 'availability_filter_func');

function availability_filter_func($availability)
{
$availability['availability'] = str_ireplace('Out of stock', 'Sold', $availability['availability']);
return $availability;
}

/*
if (!function_exists('woocommerce_template_loop_add_to_cart')) {
	function woocommerce_template_loop_add_to_cart() {
		global $product;
		if (!$product->is_in_stock()) return;
		woocommerce_get_template('loop/add-to-cart.php');
*/
/*
		else {
			echo 'Sold';
		}

	}
}
*/


/*--------------------
VISUAL/LAYOUT CHANGES
--------------------*/

// Thumbnails
function woocommerce_template_loop_product_thumbnail() {
  $image = get_field('painting');
  
  $url = $image['url'];
  $alt = $image['alt'];

  // thumbnail
  $size = 'medium';
  $thumb = $image['sizes'][ $size ];
  
  if( !empty($image) ):

    echo '<img src="' . $thumb . '" alt="' . $alt . '" />';

  endif;
}

// Single Product Image
function woocommerce_show_product_images() {
  $image = get_field('painting');
  
  $url = $image['url'];
  $alt = $image['alt'];

  // thumbnail
  $size = 'large';
  $thumb = $image['sizes'][ $size ];
  
  if( !empty($image) ):

    echo '<img id="painting" src="' . $thumb . '" alt="' . $alt . '" />';

  endif;
}

// Show images in cart






// Exclude demos from gallery page
add_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
 
function custom_pre_get_posts_query( $q ) {
 
	if ( ! $q->is_main_query() ) return;
	if ( ! $q->is_post_type_archive() ) return;
	
	if ( ! is_admin() && is_shop() ) {
 
		$q->set( 'tax_query', array(array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => array( 'demo' ), // Don't display demos on the gallery page
			'operator' => 'NOT IN'
		)));
	
	}
 
	remove_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
 
}

// Display # products per page
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 50;' ), 20 );

//Masonry
add_action( 'wp_enqueue_scripts', 'slug_masonry' );
function slug_masonry( ) {
	wp_enqueue_script( 'masonry' );
	wp_enqueue_script( 'masonry-init', get_template_directory_uri().'/js/masonry-min.js', array( 'masonry' ), null, true );
}

/*--------------------
CHECKOUT FORM CHANGES
--------------------*/

// Checkout Field Changes
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {

   // Change label text
   $fields['order']['order_comments']['placeholder'] = 'Notes or instructions (optional)';
   $fields['account']['account_password']['placeholder'] = '*******';
   $fields['billing']['billing_first_name']['placeholder'] = 'First name';
   $fields['billing']['billing_last_name']['placeholder'] = 'Last name';
   $fields['billing']['billing_email']['placeholder'] = 'Email address';
   $fields['billing']['billing_address_1']['placeholder'] = 'Address';
   $fields['billing']['billing_address_2']['placeholder'] = 'Apartment number, suite, unit, etc. (optional)';
   $fields['billing']['billing_city']['placeholder'] = 'City';
   $fields['billing']['billing_postcode']['placeholder'] = 'Zip / Postal code';
   $fields['billing']['billing_state']['placeholder'] = 'State / Province';

   // Remove labels above input fields
   unset ($fields['order']['order_comments']['label']);
   unset ($fields['account']['account_password']['label']);
   unset ($fields['billing']['billing_last_name']['label']);
   unset ($fields['billing']['billing_first_name']['label']);
   unset ($fields['billing']['billing_email']['label']);
   unset ($fields['billing']['billing_address_1']['label']);
   unset ($fields['billing']['billing_address_2']['label']);
   unset ($fields['billing']['billing_city']['label']);
   unset ($fields['billing']['billing_postcode']['label']);
   unset ($fields['billing']['billing_state']['label']);
   unset ($fields['billing']['billing_country']['label']);
   unset ($fields['billing']['billing_country']['required']);

    return $fields;
}

// Make phone number not required
add_filter( 'woocommerce_billing_fields', 'wc_npr_filter_phone', 10, 1 );

function wc_npr_filter_phone( $address_fields ) {
  $address_fields['billing_phone']['required'] = false;
  return $address_fields;
}


// Hook in
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

// Our hooked in function - $address_fields is passed via the filter!
function custom_override_default_address_fields( $address_fields ) {
     $address_fields['country']['placeholder'] = 'United States';

     return $address_fields;
}


/*--------------------
ADMIN CHANGES
--------------------*/

// Set default quantity to 1
add_filter( 'woocommerce_quantity_input_args', 'jk_woocommerce_quantity_input_args', 10, 2 );
function jk_woocommerce_quantity_input_args( $args, $product ) {
    $args['input_value']  = 1;  // Starting value
    $args['max_value']    = 1;   // Maximum value
    $args['min_value']    = 1;    // Minimum value
    $args['step']     = 1;    // Quantity steps
    return $args;
}

// Stock management default
add_action('save_post', 'myWoo_savePost', 10, 2);

function myWoo_savePost($postID, $post) {
    if (isset($post->post_type) && $post->post_type == 'product') {

        update_post_meta($post->ID, '_manage_stock', 'yes');

        update_post_meta($post->ID, '_stock', '1');
    }
}

// Sold individually
function default_no_quantities( $individually, $product ){
$individually = true;
return $individually;
}
add_filter( 'woocommerce_is_sold_individually', 'default_no_quantities', 10, 2 );



/* DON'T DELETE THIS CLOSING TAG */ ?>
