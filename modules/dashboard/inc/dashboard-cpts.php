<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
	CUSTOM POST TYPES AND TAXONOMIES
	Author: FL1 Group - www.fl1group.com
	Version: 2.0

	// DOCUMENTATION:
	// --------------------------------------------------------------------------------
	// Post Types: http://codex.wordpress.org/Function_Reference/register_post_type
	// Taxonomies: http://codex.wordpress.org/Function_Reference/register_taxonomy

	INDEX:

		1. POST TYPES
		2. TAXONOMIES
		3. URL REWRITING


	CPT Dashboard Menu Position
		5 - below Posts
		10 - below Media
		15 - below Links
		20 - below Pages
		25 - below comments
		60 - below first separator
		65 - below Plugins
		70 - below Users
		75 - below Tools
		80 - below Settings
		100 - below second separator
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('init', 'fl1_set_admin_menu_separators');

function fl1_set_admin_menu_separators() {
    if (!is_admin())
        return;

    fl1_add_menu_separator(21);
    fl1_add_menu_separator(31);
}

function fl1_add_menu_separator($pos) {
	global $menu;

	if (!isset($menu[$pos])) {
	    $menu[21] = array(
	        0  =>  '',
	        1  =>  'read',
	        2  =>  'separator' . $pos,
	        3  =>  '',
	        4  =>  'wp-menu-separator'
	    );
	}
}

function admin_separators() {
   echo '<style type="text/css">
	   		#adminmenu li.wp-menu-separator {margin: 0;}
	   		.admin-color-fresh #adminmenu li.wp-menu-separator {background:#32373c; height:10px;}
         </style>';
}
add_action('admin_head', 'admin_separators');

/*================================================================================================================*/
/*                                                                                                                */
/*                                                1. POST TYPES                                                   */
/*                                                                                                                */
/*================================================================================================================*/
/*--------------------------------------------------------------------------*/
/*                                CONTRACTS                                 */
/*--------------------------------------------------------------------------*/
add_action('init', 'create_contract_posttype', 4);
function create_contract_posttype()
{
  $labels = array(
		'name' => __( 'Contracts' ),
		'singular_name' => __( 'Contract' ),
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Create Contract' ),
		'edit' => __( 'Edit' ),
		'edit_item' => __( 'Edit Contract' ),
		'new_item' => __( 'New Contract' ),
		'view' => __( 'View Contract' ),
		'view_item' => __( 'View Contract' ),
		'search_items' => __( 'Search Contracts' ),
		'not_found' => __( 'No contracts found' ),
		'not_found_in_trash' => __( 'No contracts found in trash' ),
		'parent' => __( 'Parent Contract' )
	  );

	$args = array(
		'labels' => $labels,
		'description' => __( 'This is where you can add contracts' ),
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'publicly_queryable' => true,
		'exclude_from_search' => true,
		'menu_icon' => 'dashicons-format-aside',
		'menu_position' => 21,
		'hierarchical' => true,
		'_builtin' => false, // It's a custom post type, not built in!
		'rewrite' => array( 'slug' => 'contract', 'with_front' => true ),
		'query_var' => true,
		'supports' => array( 'title')
		//'taxonomies' => array('country', 'region', 'client_type', 'work_sector', 'work_category', 'work_type')
	  );

	register_post_type('contract', $args);
}


/*================================================================================================================*/
/*                                                                                                                */
/*                                                2. TAXONOMIES                                                   */
/*                                                                                                                */
/*================================================================================================================*/
/*--------------------------------------------------------------------------*/
/*                             feature                                */
/*--------------------------------------------------------------------------*/
/* ================ Create taxonomy "feature" ================ */

// Hook into the init action and call create_feature_taxonomies when it fires
//add_action( 'init', 'create_feature_taxonomies', 0 );

function create_feature_taxonomies() {
    $labels = array(
        'name' => _x( 'Treatments', 'taxonomy general name' ),
        'singular_name' => _x( 'Treatment', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Treatments' ),
        'all_items' => __( 'All Treatments' ),
        'parent_item' => __( 'Parent Treatments' ),
        'parent_item_colon' => __( 'Parent Treatments:' ),
        'edit_item' => __( 'Edit Treatment' ),
        'update_item' => __( 'Update Treatment' ),
        'add_new_item' => __( 'Add New Treatment' ),
        'new_item_name' => __( 'New Treatment' ),
        'menu_name' => __( 'Treatments' )
    );

    register_taxonomy('feature',array('clinic'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'public' => true,
        'rewrite' => array( 'slug' => 'clinic/treatment', 'with_front' => true )
    ));
}


/*--------------------------------------------------------------------------*/
/*                            REWRITE RULES                                 */
/*--------------------------------------------------------------------------*/
// Rewrite rule to display hierarchical URLs
// IMPORTANT: Every time you make a change to any rewrite rule in the code below, or above,
// remember to visit the Permalinks page in Wordpress to flush the rules. No need to click
// on Update, simply visit it.

// clinic
// function filter_clinic_link($link, $post) {
//     if ($post->post_type != 'clinic')
//         return $link;
//
//     if ($cats = get_the_terms($post->ID, 'clinic_area'))
//         $link = str_replace('%clinic_area%', array_pop($cats)->slug, $link);
//
//     //if ($cats = get_the_terms($post->ID, 'property_area'))
//     //    $link = str_replace('%property_area%', array_pop($cats)->slug, $link);
//
//     return $link;
// }
//
// add_filter('post_type_link', 'filter_clinic_link', 10, 2);
