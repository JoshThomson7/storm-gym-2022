<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * WordPress Sidebars
 *
 * @package modules/
 * @version 1.0
 *
*/

if ( function_exists('register_sidebar') ) {

	// Default Sidebar
	register_sidebar(array(
		'name' => 'Sidebar',
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div>',
	));

}
