<?php
/**
 * Landing banner
 */
?>
<section class="landing__banner">
    <div class="landing__banner--mask">
        <div class="landing__banner--mask-bk"></div>
    </div>
    <div class="landing__banner--content">
        <div class="max__width">
            <div class="landing__banner--content-wrapper">
                <div class="caption">
                    <h1><?php the_field('banner_landing_heading_top'); ?></h1>
                    <h2><?php the_field('banner_landing_heading_bottom'); ?></h2>
                    <p><?php the_field('banner_landing_caption'); ?></p>

                    <?php postcode_form(); ?>
                </div>

                <figure>
                    <img src="<?php the_field('banner_landing_image'); ?>" />
                </figure>
            </div>
        </div>
    </div>
</section>