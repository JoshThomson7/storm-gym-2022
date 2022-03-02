<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * WordPress Assests
 *
 * Enqueues the theme assests into
 *
 * @package modules/
 * @version 1.0
 * @see http://code.tutsplus.com/tutorials/loading-css-into-wordpress-the-right-way--cms-20402
*/

/*------------------------------------------------*/
/*	Enqueue Scripts and Style
/*------------------------------------------------*/
function enqueue_scripts_and_style() {

    // Needed for Widget Factory
    wp_register_script('jquery-ui-script', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array('jquery'), '1.12.1');
    wp_enqueue_script('jquery-ui-script');

	// scripts
	wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom-min.js', array('jquery'), '', false);

    // Ajax
    wp_localize_script('custom-js', 'fl1_ajax_object', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'ajaxNonce' => wp_create_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M'),
        'jsPath' => get_stylesheet_directory_uri().'/js/',
    ));

    wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?v=3&libraries=places&key=AIzaSyDArEWe_hBgt9OR105_Sj6GQTDQOG1ZHME');

    wp_enqueue_style('style-base', get_stylesheet_directory_uri() . '/style-base.css' );

}
add_action('wp_enqueue_scripts', 'enqueue_scripts_and_style');

/**
 * Login styles
 */
function wp_login_enqueue_scripts() {
    wp_enqueue_style('wp-login', get_stylesheet_directory_uri() . '/wp-login.min.css' );
}
add_action( 'login_enqueue_scripts', 'wp_login_enqueue_scripts', 10 );


/*------------------------------------------------*/
/*	Enqueue Favicon
/*------------------------------------------------*/

function enqueue_favicon() {
    // echo '<link rel="apple-touch-icon-precomposed" sizes="57x57" href="'.get_stylesheet_directory_uri().'/img/favicon/apple-touch-icon-57x57.png" />';
    // echo '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'.get_stylesheet_directory_uri().'/img/favicon/apple-touch-icon-114x114.png" />';
    // echo '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="'.get_stylesheet_directory_uri().'/img/favicon/apple-touch-icon-72x72.png" />';
    // echo '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="'.get_stylesheet_directory_uri().'/img/favicon/apple-touch-icon-144x144.png" />';
    // echo '<link rel="apple-touch-icon-precomposed" sizes="120x120" href="'.get_stylesheet_directory_uri().'/img/favicon/apple-touch-icon-120x120.png" />';
    // echo '<link rel="apple-touch-icon-precomposed" sizes="152x152" href="'.get_stylesheet_directory_uri().'/img/favicon/apple-touch-icon-152x152.png" />';
    // echo '<link rel="icon" type="image/png" href="'.get_stylesheet_directory_uri().'/img/favicon/favicon-32x32.png" sizes="32x32" />';
    // echo '<link rel="icon" type="image/png" href="'.get_stylesheet_directory_uri().'/img/favicon/favicon-16x16.png" sizes="16x16" />';
    // echo '<meta name="application-name" content="&nbsp;"/>';
    // echo '<meta name="msapplication-TileColor" content="#" />';
    // echo '<meta name="msapplication-TileImage" content="'.get_stylesheet_directory_uri().'/img/favicon/mstile-144x144.png" />';

}
add_action( 'wp_head', 'enqueue_favicon');
