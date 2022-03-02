<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Dashboard Functions
 *
 * Contains useful functions for the WordPress Dashboard.
 *
 * @author  Multiple Authors
 * @package modules/dashboard
 * @version 1.0
*/

include('inc/dashboard-acf.php');
include('inc/dashboard-security.php');

add_theme_support('nav-menus');

/* ----------------------------------------------------------------------*/
/* 	Robots Blocked Alert
/*
/*	In WordPress, under Settings > Reading, if the option
/*  "Discourage search engines from indexing this site"
/*  is checked, it will
/* ----------------------------------------------------------------------*/
function addAlert() {
	if(get_option('blog_public') == 0):
?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				jQuery('.wrap > h2').parent().prev().after('<div class="update-nag notice notice-warning" style="display: block;"><strong>Robots blocked</strong>. Remember to change this setting when going live under <strong>Settings > Reading</strong> by unchecking the <strong>Discourage search engines from indexing this site</strong> option.</div>');
			});
		</script>
<?php
	endif;
} add_action('admin_head','addAlert');



/*-----------------------------------------------------------------------------------------------------*/
/* 	Hide Stuff
/*
/*	Useful functions to hide certain areas in WordPress from clients
/* 	@see http://support.advancedcustomfields.com/forums/topic/hide-menu-item-not-working-in-acf-5/
/* ----------------------------------------------------------------------------------------------------*/
add_filter('acf/settings/show_admin', 'remove_acf_menu');

function remove_acf_menu($show) {

	$admins = array('fl1admin', 'alex@fl1.digital'); // users that CAN edit ACF

	// get the current user
	$current_user = wp_get_current_user();

	return (in_array($current_user->user_login, $admins));
}

/*
*	Hide plugins from list of plugins
*	Docs: http://www.trickspanda.com/2014/02/hide-wordpress-plugin-plugin-list/
*/
// function hide_plugins() {
// 	$admins = array('fl1admin'); // users that CAN see plugins
// 	$current_user = wp_get_current_user();

// 	if(!in_array($current_user->user_login, $admins)) {

// 	  	global $wp_list_table;
// 	  	$hidearr = array('wp-all-import-pro/wp-all-import-pro.php');
// 	  	$myplugins = $wp_list_table->items;

// 		foreach ($myplugins as $key => $val) {
// 	    	if (in_array($key,$hidearr)) {
// 	      		unset($wp_list_table->items[$key]);
// 	    	}
// 	  	}
// 	}
// }

// add_action('pre_current_active_plugins', 'hide_plugins');



/* -------------------------------------------------------------------------*/
/*  Remove WP Logo from admin header
* --------------------------------------------------------------------------*/
function annointed_admin_bar_remove() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
}

add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);


/* -------------------------------------------------------------------------*/
/*  Change Wordpress logo URL to the website's Home Page
/* --------------------------------------------------------------------------*/
function custom_loginlogo_url($url) {
	return get_bloginfo('url');
}

add_filter( 'login_headerurl', 'custom_loginlogo_url' );

/* -------------------------------------------------------------------------*/
/*  Change admin footer text
* --------------------------------------------------------------------------*/
function fl1_footer_admin()  {
    echo '<span style="display:flex; align-items:center;"><i class="dashicons dashicons-editor-code" style="margin:0 3px;font-size:16px;line-height:21px;" title="Developed"></i> with <i class="dashicons dashicons-heart" style="margin:0 3px;font-size:16px;line-height:21px;" title="love"></i> by <a href="https://fl1.digital" title="FL1 Digital" target="_blank"><img src="'.get_stylesheet_directory_uri().'/img/fl1-icon.svg" style="width:22px; margin:3px 3px 0 5px;"></a> using <i class="dashicons dashicons-wordpress-alt" style="margin:0 3px;font-size:16px;line-height:21px;" title="WordPress"></i></span>';
}

add_filter('admin_footer_text', 'fl1_footer_admin');


/* -------------------------------------------------------------------------*/
/*  Hide comments
* --------------------------------------------------------------------------*/
function remove_menus(){
    remove_menu_page( 'edit-comments.php' ); //Comments
}

add_action( 'admin_menu', 'remove_menus' );


/* -------------------------------------------------------------------------*/
/*  Move Yoast to bottom
* --------------------------------------------------------------------------*/
function yoasttobottom() {
	return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');


/* ----------------------------------------------------------------------*/
/* 	User functions
/*
/*	Change username label with "Email"
/* ----------------------------------------------------------------------*/
add_filter( 'gettext', 'wpse6096_gettext', 10, 2 );
function wpse6096_gettext( $translation, $original ) {
    if ( 'Username' == $original ) {
        return 'E-mail';
    }
    if ( 'E-mail' == $original ) {
        return 'Confirm Email';
    }
    return $translation;
}
