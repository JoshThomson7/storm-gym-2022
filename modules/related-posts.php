<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Function: getRelatedPosts
@Paramaters: $post_id the ID of the post
@Returns: a string unordered list of links of posts related to the ID."
*/


function wc_related_products($post_id) {
	// Use the global variable to query the wordpress database
	global $wp_query;

	$post_type = get_post_type();

	// Get the tags of the post passed to the function
	$product_cats = wp_get_post_terms($post_id, 'product_cat', array("fields" => "ids"));
	$product_tags = wp_get_post_terms($post_id, 'product_tag', array("fields" => "ids"));

	// Now set up the query.
	// We will be showing 4 related posts other than the current post and have categories and tags we set up previously.
	// We will be ordering the related posts randomly.
	$args = array (
		'post_type' => 'product',
        'post__not_in' => array($post_id),
        'product_mode' => 'shop',
		'posts_per_page'=> 4,
		'orderby'=>'rand'
	);

    // Pass the query
	$related = new wp_query($args);

	// If the query is successful and returns posts loop through them.
	?>

    <?php if($related->have_posts()): ?>
        <?php
            while($related->have_posts()) : $related->the_post();
            
            if(get_post_thumbnail_id(get_the_ID())) {
                $attachment_id = get_post_thumbnail_id(get_the_ID());
                $prod_image = vt_resize($attachment_id,'' , 700, 700, true);

            } else {
                $prod_image = ' style="background-image:url('.get_stylesheet_directory_uri().'/img/product-holding.png;);"';
            }
        ?>
            <?php include wc_path().'templates/woo-loop-grid.php'; ?>
        <?php endwhile; ?>
	<?php endif; ?>

	<?php // If something goes wrong, there are no related posts, return false.
	return false;
}
