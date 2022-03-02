<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Dashboard Security Functions
 *
 * Contains useful security functions for WordPress.
 *
 * @author  Multiple Authors
 * @package modules/dashboard
 * @version 1.0
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* -------------------------------------------------------------------------*/
/*  Remove wp version meta tag and from rss feed
* --------------------------------------------------------------------------*/

add_filter('the_generator', '__return_false');

/* -------------------------------------------------------------------------*/
/*  Remove wp version param from any enqueued scripts
* --------------------------------------------------------------------------*/

function at_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'at_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'at_remove_wp_ver_css_js', 9999 );

/* -------------------------------------------------------------------------*/
/*  Disable ping back scanner and complete xmlrpc class
* --------------------------------------------------------------------------*/

add_filter( 'wp_xmlrpc_server_class', '__return_false' );
add_filter('xmlrpc_enabled', '__return_false');

/* -------------------------------------------------------------------------*/
/*  Disable various feeds
* --------------------------------------------------------------------------*/

function go_disable_feed() {
  wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>') );
}

add_action('do_feed', 'go_disable_feed', 1);
add_action('do_feed_rdf', 'go_disable_feed', 1);
add_action('do_feed_rss', 'go_disable_feed', 1);
add_action('do_feed_rss2', 'go_disable_feed', 1);
add_action('do_feed_atom', 'go_disable_feed', 1);
add_action('do_feed_rss2_comments', 'go_disable_feed', 1);
add_action('do_feed_atom_comments', 'go_disable_feed', 1);

/* -------------------------------------------------------------------------*/
/*  Disable redirect to login page
* --------------------------------------------------------------------------*/
// http://wordpress.stackexchange.com/questions/85529/how-to-disable-multisite-sign-up-page

function prevent_multisite_signup()
{
    wp_redirect( site_url() );
    die();
}
add_action( 'signup_header', 'prevent_multisite_signup' );


/* -------------------------------------------------------------------------*/
/*  Remove Xpingback header
* --------------------------------------------------------------------------*/

function remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_x_pingback');


/* -------------------------------------------------------------------------*/
/*  Remove Wordpress Emoji Support
* --------------------------------------------------------------------------*/

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );


/* ----------------------------------------------------------------------*/
/*  Strip out login errors and return custom messgae
/* ----------------------------------------------------------------------*/

function no_wordpress_errors(){
  return 'Oops! Incorrect email or password.';
}
add_filter( 'login_errors', 'no_wordpress_errors' );


/* ----------------------------------------------------------------------*/
/*  Clean header output
/* ----------------------------------------------------------------------*/

function remove_header_info() {
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'start_post_rel_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head',10,0); // for WordPress >= 3.0
}
add_action('init', 'remove_header_info');


/* ----------------------------------------------------------------------*/
/*  Comments Secutiry
/*
/*  The comment box in WordPress is like a basic HTML editor and
/*  people can use HTML tags like <b>, <a>, <i>, etc to highlight
/*  certain words in their comment or add live links. For more
/*  security, we remove this functionality.
/* ----------------------------------------------------------------------*/
add_filter( 'pre_comment_content', 'wp_specialchars' );

// Removes comment classes that show user name
function remove_comment_author_class( $classes ) {
    foreach( $classes as $key => $class ) {
		if(strstr($class, "comment-author-")) {
			unset( $classes[$key] );
		}
	}

	return $classes;
}
add_filter( 'comment_class' , 'remove_comment_author_class' );

/* -----------------------------------------------------------------------------------------------------------*/
/*  Change the author URL slug so it doesn't show the actual username but the nickname
/*  @see http://wordpress.stackexchange.com/questions/5742/change-the-author-slug-from-username-to-nickname
/* -----------------------------------------------------------------------------------------------------------*/

function wpse5742_request( $query_vars ) {
    if ( array_key_exists( 'author_name', $query_vars ) ) {
        global $wpdb;
        $author_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='nickname' AND meta_value = %s", $query_vars['author_name'] ) );
        if ( $author_id ) {
            $query_vars['author'] = $author_id;
            unset( $query_vars['author_name'] );
        }
    }

    return $query_vars;
}
add_filter( 'request', 'wpse5742_request' );

function wpse5742_author_link( $link, $author_id, $author_nicename ) {
    $author_nickname = get_user_meta( $author_id, 'nickname', true );
    if ( $author_nickname ) {
        $link = str_replace( $author_nicename, $author_nickname, $link );
    }
    return $link;
}
add_filter( 'author_link', 'wpse5742_author_link', 10, 3 );

function wpse5742_set_user_nicename_to_nickname( &$errors, $update, &$user ) {
    if ( ! empty( $user->nickname ) ) {
        $user->user_nicename = sanitize_title( $user->nickname, $user->display_name );
    }
}
add_action( 'user_profile_update_errors', 'wpse5742_set_user_nicename_to_nickname', 10, 3 );
