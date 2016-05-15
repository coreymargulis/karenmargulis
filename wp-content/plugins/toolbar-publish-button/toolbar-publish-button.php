<?php
/*
Plugin Name: Toolbar Publish Button
Plugin URI: http://wpUXsolutions.com
Description: Save a lot of your time by scrolling less in WP admin! A small UX improvement that keeps Publish button within reach and retains the scrollbar position after saving in WordPress admin.
Version: 1.4.3
Author: wpUXsolutions
Author URI: http://wpUXsolutions.com
License: GPLv2 or later
Text Domain: tpb
Domain Path: /languages


Copyright 2013-2015  wpUXsolutions  (email : wpUXsolutions@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/




global $wpuxss_tpb_version;



$wpuxss_tpb_version = '1.4.3';




/**
 *  Load plugin text domain
 *
 *  @since    1.3
 *  @created  26/06/15
 */

add_action( 'plugins_loaded', 'wpuxss_tpb_on_plugins_loaded' );

if ( ! function_exists( 'wpuxss_tpb_on_plugins_loaded' ) ) {
    
    function wpuxss_tpb_on_plugins_loaded() {
        
      load_plugin_textdomain( 'tpb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
    }
}




/**
 *  wpuxss_tpb_on_admin_init
 *
 *  @since    1.4.3
 *  @created  15/10/15
 */
 
add_action( 'admin_init', 'wpuxss_tpb_on_admin_init' );

if ( ! function_exists( 'wpuxss_tpb_on_admin_init' ) ) {

    function wpuxss_tpb_on_admin_init() {
        
        wpuxss_tpb_on_activation();
        
        // register plugin settings
        register_setting( 
            'wpuxss_tpb_settings', //option_group
            'wpuxss_tpb_settings', //option_name
            'wpuxss_tpb_settings_validate' //sanitize_callback
        );
    }
}




/**
 *  wpuxss_tpb_admin_scripts
 *
 *  @since    1.4.3
 *  @created  15/10/15
 */

add_action( 'admin_enqueue_scripts', 'wpuxss_tpb_admin_scripts' );

if ( ! function_exists( 'wpuxss_tpb_admin_scripts' ) ) {

    function wpuxss_tpb_admin_scripts() {
        
       global $wpuxss_tpb_version,
              $hook_suffix;
              
       
       if ( 'index.php' == $hook_suffix )
           return;

        
        // styles for button
        wp_enqueue_style( 
            'wpuxss-tpb-admin-custom-style', 
            plugins_url( '/css/admin.css' , __FILE__ ),
            array(), 
            $wpuxss_tpb_version, 
            'all' 
        );
        
        // plugin scripts
        wp_enqueue_script(
            'wpuxss-tpb-script',
            plugins_url( '/js/tpb.js' , __FILE__ ),
            array('jquery'),
            $wpuxss_tpb_version,
            true
        );
        
        $options = get_option('wpuxss_tpb_settings');
        $wpuxss_tpb_background = isset( $options['wpuxss_tpb_background'] ) ? $options['wpuxss_tpb_background'] : '';
        
        wp_localize_script( 
            'wpuxss-tpb-script', 
            'button_bg',
            $wpuxss_tpb_background
        );    
        
        if ( $options['wpuxss_tpb_scrollbar_return'] )
        {
            wp_enqueue_script(
                'wpuxss-tpb-jquery-cookie',
                plugins_url( '/js/jquery.cookie.js' , __FILE__ ),
                array('jquery'),
                $wpuxss_tpb_version,
                true
            );

            wp_enqueue_script(
                'wpuxss-tpb-scrollbar',
                plugins_url( '/js/tpb-scrollbar.js' , __FILE__ ),
                array('jquery'),
                $wpuxss_tpb_version,
                true
            );
        }
    }
}




/**
 *  wpuxss_tpb_color_picker
 *
 *  @since    1.4
 *  @created  14/10/15
 */
 
add_action( 'admin_print_scripts-settings_page_toolbar-publish-button-settings', 'wpuxss_tpb_color_picker' );

if ( ! function_exists( 'wpuxss_tpb_color_picker' ) ) {

    function wpuxss_tpb_color_picker() {
        
        global $wpuxss_tpb_version;
        
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wpuxss-tpb-color-picker', 
            plugins_url('/js/tpb-color-picker.js', __FILE__ ), 
            array( 'wp-color-picker' ), 
            $wpuxss_tpb_version,
            true );
    }
}




/**
 *  wpuxss_tpb_admin_menu
 *
 *  Add plugin settings page
 *
 *  @since    1.1.0
 *  @created  15/07/13
 */

add_action('admin_menu', 'wpuxss_tpb_admin_menu');

if ( ! function_exists( 'wpuxss_tpb_admin_menu' ) ) {

    function wpuxss_tpb_admin_menu() {
        
        add_options_page( 
            __('Toolbar Publish Button Settings','tpb'), //page_title
            __('Toolbar Publish Button','tpb'), //menu_title
            'manage_options', //capability
            'toolbar-publish-button-settings', //page
            'wpuxss_tpb_print_options' //callback
        );
    }
}




/**
 *  wpuxss_tpb_print_options
 *
 *  Print plugin options page
 *
 *  @type     callback function
 *  @since    1.1.0
 *  @created  15/07/13
 */

if ( ! function_exists( 'wpuxss_tpb_print_options' ) ) {
    
    function wpuxss_tpb_print_options() {
        
        global $wpuxss_tpb_version;
        ?>  
        
        <div id="toolbar-publish-button-settings-wrap" class="wrap">    
            <?php screen_icon(); ?>
            <h2><?php esc_html_e('Toolbar Publish Button Settings','tpb'); ?></h2>  
            
            <div id="poststuff">
            
                <div id="post-body" class="metabox-holder columns-2">
    
                    <div id="postbox-container-2" class="postbox-container">
                    
                        <div class="postbox">
                            <form method="post" action="options.php">
    
                                <?php settings_fields( 'wpuxss_tpb_settings' ); ?>
                                
                                <h3><?php esc_html_e('Basic Settings','tpb'); ?></h3>
                                
                                <div class="inside">
                                
                                    <?php $options = get_option('wpuxss_tpb_settings');
                                    $wpuxss_tpb_background = isset( $options['wpuxss_tpb_background'] ) ? $options['wpuxss_tpb_background'] : ''; ?>
                               
                                    <ul class="wpuxss_tpb_settings_list">
                                    
                                        <li>
                                            <input id="wpuxss_tpb_scrollbar_return" name="wpuxss_tpb_settings[wpuxss_tpb_scrollbar_return]" type="checkbox" value="1" <?php checked( '1', $options['wpuxss_tpb_scrollbar_return'] ); ?> />
                                            <label>
                                                <?php esc_html_e('Remember scrollbar position','tpb'); ?><br />
                                                <span>
                                                    <?php esc_html_e('It will return an admin page (including code Editor page) scrollbar to its position after saving.','tpb'); ?>
                                                    <br />
                                                    <?php esc_html_e('Works for Plugins page after plugin actiation / deactivation.','tpb'); ?>
                                                </span>
                                            </label>
                                        </li>  
                                        <li>
                                            <label><?php esc_html_e('Button\'s Background','tpb'); ?></label>
                                            <input type="text" value="<?php echo $wpuxss_tpb_background; ?>" class="wpuxss-tpb-button-color" name="wpuxss_tpb_settings[wpuxss_tpb_background]" />
                                        </li> 
                                    </ul>
                                    
                                    <?php submit_button(); ?>
                                
                                </div>
    
                            </form>
                        </div>
                        
                    </div>
                    
                    <div id="postbox-container-1" class="postbox-container">
                    
                        <div class="postbox" id="wpuxss-credit">
                            
                            <h3>Toolbar Publish Button <?php echo $wpuxss_tpb_version; ?></h3>
                            
                            <div class="inside">
                            
                                <h4>Changelog</h4>
                                <p>What's new in <a href="http://wordpress.org/plugins/toolbar-publish-button/changelog/">version <?php echo $wpuxss_tpb_version; ?></a>.</p>
                                
                                <h4>Support</h4>
                                <p>Feel free to ask for support on the <a href="http://wordpress.org/support/plugin/toolbar-publish-button/">wordpress.org</a>.</p>
                                
                                <h4>Plugin rating</h4>
                                <p>Please <a href="http://wordpress.org/support/view/plugin-reviews/toolbar-publish-button/">vote for the plugin</a>. Thanks!</p>
                                
                                <h4>Other plugins you may find useful</h4>
                                <ul>
                                    <li><a href="http://wordpress.org/plugins/enhanced-media-library/">Enhanced Media Library</a></li>
                                </ul>
                                
                                <div class="author">
                                    <span><a href="http://www.wpuxsolutions.com/">wpUXsolutions</a> by <a class="logo-webbistro" href="http://twitter.com/webbistro"><span class="icon-webbistro">@</span>webbistro</a></span>
                                </div>
                            
                            </div>
        
                        </div>
                        
                    </div>
                
                </div>
                
            </div>
            
        </div>
     
        <?php
    }
}




/**
 *  wpuxss_tpb_settings_validate
 *
 *  @type     callback function
 *  @since    1.1.0
 *  @created  15/07/13
 */ 
 
if ( ! function_exists( 'wpuxss_tpb_settings_validate' ) ) {
    
    function wpuxss_tpb_settings_validate( $input ) {
        
        $input['wpuxss_tpb_scrollbar_return'] = isset( $input['wpuxss_tpb_scrollbar_return'] ) ? 1 : 0;
        $wpuxss_tpb_background = trim( $input['wpuxss_tpb_background'] );
        $wpuxss_tpb_background = strip_tags( stripslashes( $wpuxss_tpb_background ) );
        
        if( ! empty( $wpuxss_tpb_background ) && false === wpuxss_tpb_validate_color_format( $wpuxss_tpb_background ) ) {
         
            // $setting, $code, $message, $type
            add_settings_error( 'wpuxss_tpb_settings', 'wpuxss_tpb_settings_color_error', 'Please choose a valid color for background', 'error' ); 
            
            $options = get_option( 'wpuxss_tpb_settings' );
            $input['wpuxss_tpb_background'] = $options['wpuxss_tpb_background'];
         
        } else {
            $input['wpuxss_tpb_background'] = $wpuxss_tpb_background;
        }
 
        return $input;
    }
}




/**
 *  wpuxss_tpb_validate_color_format
 *
 *  @since    1.4
 *  @created  14/10/15
 */
 
if ( ! function_exists( 'wpuxss_tpb_validate_color_format' ) ) {
    
    function wpuxss_tpb_validate_color_format( $value ) { 
         
        if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {   
            return true;
        }
         
        return false;
    }
}




/**
 *  wpuxss_tpb_on_activation
 *
 *  Set default value for plugin settings during plugin activation
 *
 *  @since    1.1.0
 *  @created  15/07/13
 */ 

if ( ! function_exists( 'wpuxss_tpb_on_activation' ) ) {
    
    function wpuxss_tpb_on_activation() {
        
        global $wpuxss_tpb_version;
            
        $wpuxss_tpb_old_version = get_option('wpuxss_tpb_version', false); 
    
        if ( version_compare( $wpuxss_tpb_version, $wpuxss_tpb_old_version, '<>' ) ) 
        {
            update_option('wpuxss_tpb_version', $wpuxss_tpb_version );
            
            if ( empty( $wpuxss_tpb_old_version ) )
            {
                $wpuxss_tpb_settings = array(
                    'wpuxss_tpb_scrollbar_return' => 1
                );
                update_option( 'wpuxss_tpb_settings', $wpuxss_tpb_settings );
                
                return;
            }
        }
    }
}


/**
 *  wpuxss_tpb_settings_links
 *
 *  Add settings link to the plugin action links
 *
 *  @since    1.1.0
 *  @created  15/07/13
 */ 
 
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpuxss_tpb_settings_links' );

if ( ! function_exists( 'wpuxss_tpb_settings_links' ) ) {
    
    function wpuxss_tpb_settings_links( $links ) {
        
        return array_merge(
            array(
                'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=toolbar-publish-button-settings">' . __('Settings','tpb') . '</a>'
            ),
            $links
        );
    }
}


?>