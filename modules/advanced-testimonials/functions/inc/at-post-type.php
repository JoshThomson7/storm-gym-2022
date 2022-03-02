<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * AT Post Type
 *
 * Register testimonials post type
 *
 * @package Advanced Testimonials
 * @version 1.0
*/

add_action( 'init', 'at_post_type', 4 );
function at_post_type() {
  	$labels = array(
		'name' => __( 'Testimonials' ),
		'singular_name' => __( 'Testimonial' ),
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Create Testimonial' ),
		'edit' => __( 'Edit' ),
		'edit_item' => __( 'Edit Testimonial' ),
		'new_item' => __( 'New Testimonial' ),
		'view' => __( 'View Testimonial' ),
		'view_item' => __( 'View Testimonial' ),
		'search_items' => __( 'Search Testimonial' ),
		'not_found' => __( 'No testimonials found' ),
		'not_found_in_trash' => __( 'No testimonials found in trash' ),
		'parent' => __( 'Parent Testimonials' ),
	  );

	$args = array(
		'labels' => $labels,
		'description' => __( 'This is where you can create testimonials.' ),
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'publicly_queryable' => false,
		'exclude_from_search' => false,
		'menu_icon' => 'dashicons-format-status',
		'menu_position' => 22,
		'hierarchical' => true,
		'_builtin' => false, // It's a custom post type, not built in!
		'rewrite' => array( 'slug' => 'testimonial', 'with_front' => true ),
		'query_var' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
	  );

	register_post_type('testimonial', $args);
}
