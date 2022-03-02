<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Dashboard ACF
 *
 * Advanced Custom Fields functions.
 *
 * @author  Multiple Authors
 * @package modules/dashboard
 * @version 1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function apf_acf_init() {
	acf_update_setting('google_api_key', 'AIzaSyDArEWe_hBgt9OR105_Sj6GQTDQOG1ZHME');

    if( function_exists('acf_add_options_page') ) {
        /*acf_add_options_page(array(
            'page_title'  => 'Theme General Settings',
            'menu_title'  => 'Theme Settings',
            'menu_slug'   => 'theme-general-settings',
            'capability'  => 'edit_posts',
            'redirect'    => false
        ));*/

        acf_add_options_sub_page(array(
            'page_title'  => 'Header Options',
            'menu_title'  => 'Header',
            'parent_slug' => 'themes.php'
        ));

        acf_add_options_sub_page(array(
            'page_title'  => 'Footer Options',
            'menu_title'  => 'Footer',
            'parent_slug' => 'themes.php',
        ));
    }

}
add_action('acf/init', 'apf_acf_init');
