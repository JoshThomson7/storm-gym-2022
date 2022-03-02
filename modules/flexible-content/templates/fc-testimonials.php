<?php
/**
 * Testimonials
 */

 // format
$order_by = get_sub_field('testimonials_order_by');
$posts_per_page = get_sub_field('testimonials_number') ? get_sub_field('testimonials_number') : 9;
$custom = get_sub_field('testimonials_custom');

$args = array(
    'post_type'         => 'testimonial',
    'post_status'       => 'publish',
    'orderby'           => $order_by,
    'order'             => 'asc',
    'posts_per_page'    => $posts_per_page
);

if($order_by === 'custom') {
    $args['post__in'] = $custom;
    $args['orderby'] = 'post__in';
    unset($args['order']);
}

$testimonials = new WP_Query($args);
?>
<div class="testimonials__wrapper testimonials-carousel">
    <?php
        while($testimonials->have_posts()) : $testimonials->the_post();

        $stars = get_field('review_rating');
    ?>
        <article>
            <div class="inner">
                <div class="testimonial__meta">
                    <h3><?php the_title(); ?></h3>
                    <div class="stars">
                        <?php for($x = 1; $x <= $stars; $x++): ?>
                            <span>&#x2605;</span>
                        <?php endfor; ?>
                    </div>
                </div><!-- testimonial__meta -->

                <div class="testim__content">
                    <?php the_field('review_quote'); ?>
                </div><!-- testim__content -->
            </div>
        </article>
    <?php endwhile; wp_reset_query(); ?>
</idv>
