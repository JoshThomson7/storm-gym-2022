<?php
/**
 * Single Workout Template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
AVB::avb_banners();

global $post;
$workout = new APT_Workout($post->ID);
?>

<section class="apt-workout-single has-deps">
    <div class="max__width">
        
    </div><!-- max__width -->
</section><!-- apt-workout-single -->

<?php get_footer(); ?>