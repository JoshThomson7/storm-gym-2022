<?php
/*
    Masonry
*/
?>

    <div class="masonry__wrapper">
        <?php if(have_rows('masonry', $reuse_id)): ?>
            <div class="masonry" data-isotope='{ "itemSelector": ".masonry__item" }'>
                <?php
                    while(have_rows('masonry', $reuse_id)) : the_row();

                    $row_layout = get_row_layout();

                    if(get_row_layout() === 'blog'):

                        $orderby = 'date';
                        $post_in = '';

                        if(get_sub_field('ms_blog_type') == 'latest') {
                            $orderby = 'date';

                        } elseif(get_sub_field('ms_blog_type') == 'featured') {
                            $post_in = array(get_sub_field('ms_blog_featured'));

                        } elseif(get_sub_field('ms_blog_type') == 'random') {
                            $orderby = 'rand';
                        }

                        $args = array(
                            'post_type'         => 'post',
 		        	        'post_status'       => 'publish',
 		        	        'posts_per_page'    => 1,
                            'post__in'          => $post_in,
                            'orderby'           => $orderby,
                            'order'             => 'desc'
                        );

                        $ms_blog = new WP_Query($args);
                ?>
                        <div class="masonry__item <?php echo $row_layout; ?>">

                            <div class="masonry__item__wrapper">
                                <?php
                                    while($ms_blog->have_posts()) : $ms_blog->the_post();

                                    if(get_field('page_banner')) { 
                                        $attachment_id = get_field('page_banner');
                                    } elseif(has_post_thumbnail()) {
                                        $attachment_id = get_post_thumbnail_id();
                                    }

                                    $blog_img = vt_resize($attachment_id,'' , 900, 576, true);
                                ?>
                                    <a href="<?php the_permalink(); ?>" class="blog__img" style="background-image:url(<?php echo $blog_img['url']; ?>)">
                                        <div class="blog__img__gradient"></div>
                                    </a>

                                    <div class="blog__content">
                                        <h3>
                                            <span><?php the_category( ', ' ); ?></span>
                                            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <p><?php echo trunc(get_the_excerpt(), 30); ?></p>

                                        <div class="blog__meta">
                                            <date><?php the_time('j M Y'); ?></date>
                                            <a href="<?php the_permalink(); ?>" title="Full article" class="arrow__link">Full article <i class="fa fa-arrow-right"></i></a>
                                        </div><!-- blog__meta -->
                                    </div><!-- blog__content -->
                                <?php endwhile; wp_reset_postdata(); ?>

                            </div><!-- masonry__item__wrapper -->

                        </div><!-- masonry__item -->

                <?php
                    elseif(get_row_layout() === 'property'):

                        $orderby = 'date';
                        $post_in = '';

                        if(get_sub_field('ms_property_type') == 'latest') {
                            $orderby = 'date';

                        } elseif(get_sub_field('ms_property_type') == 'featured') {
                            $post_in = array(get_sub_field('ms_property_featured'));

                        } elseif(get_sub_field('ms_property_type') == 'random') {
                            $orderby = 'rand';
                        }

                        $args = array(
                            'post_type'         => 'property',
                            'post_status'       => 'publish',
                            'posts_per_page'    => 1,
                            'post__in'          => $post_in,
                            'orderby'           => $orderby,
                            'order'             => 'desc'
                        );

                        $ms_property = new WP_Query($args);
                ?>
                        <div class="masonry__item <?php echo $row_layout; ?>">
                            
                            <div class="masonry__item__wrapper">
                                <?php while($ms_property->have_posts()) : $ms_property->the_post(); ?>
                                    <a href="<?php the_permalink(); ?>" class="property__img" style="background-image:url(<?php apf_the_property_image(); ?>)"></a>

                                    <div class="property__content">
                                        <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php apf_the_property_price(); ?></a></h2>
                                        <h3><?php apf_the_property_seo_title(); ?></h3>
                                        <p><?php the_title(); ?></p>
                                    </div><!-- blog__content -->
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div><!-- masonry__item__wrapper -->

                        </div><!-- masonry__item -->

                <?php
                    elseif(get_row_layout() === 'team'):

                    $team_id = get_sub_field('ms_team_member');
                    $member_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", get_the_title($team_id)));

                    $attachment_id = get_post_thumbnail_id($team_id);
                    $team_img = vt_resize($attachment_id,'' , 800, 800, true);
                ?>
                        <div class="masonry__item <?php echo $row_layout; ?>">

                            <div class="masonry__item__wrapper">
                                <a href="#<?php echo $member_id; ?>" title="<?php echo get_the_title($team_id); ?>" class="team__img team__modal" style="background-image:url(<?php echo $team_img['url']; ?>)"></a>

                                <div class="team__content">
                                    <h5>In the spotlight with</h5>
                                    <h2><a href="#<?php echo $member_id; ?>" title="<?php echo get_the_title($team_id); ?>" class="team__modal"><?php echo get_the_title($team_id); ?></a></h2>
                                    <p><?php the_sub_field('ms_team_short_bio'); ?></p>
                                </div><!-- team__content -->

                                <div class="team__popup__holder">
                                    <?php
                                        $attachment_id = get_post_thumbnail_id($team_id);
                                        $team_img = vt_resize($attachment_id,'' , 900, 900, true);

                                        $member_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", get_the_title($team_id)));

                                        $get_post_object = get_post($team_id);
                                    ?>
                                    <div id="<?php echo $member_id; ?>" class="team__popup">

                                        <div class="team__popup__img">
                                            <?php if(get_field('team_video_id', $team_id)): ?>
                                                <a href="http://www.youtube.com/watch?v=<?php the_field('team_video_id', $team_id); ?>" class="team__video"><span class="ion-play"></span> Watch video</a>
                                            <?php endif; ?>

                                            <img src="<?php echo $team_img['url']; ?>" alt="<?php echo get_the_title($team_id); ?>" />
                                        </div><!-- team__popup__img -->

                                        <div class="team__popup__content">
                                            <div class="team__popup__nav">
                                                <ul>
                                                    <li><a href="#" class="team__close"><i class="ion-android-close"></i></a></li>
                                                </ul>
                                            </div><!-- team__popup__nav -->

                                            <h3><?php echo get_the_title($team_id); ?> <span><?php the_field('team_job_title', $team_id); ?></span></h3>

                                            <?php if(get_field('team_email', $team_id)): ?>
                                                <div class="team__popup__icon">
                                                    <span class="icon ion-ios-paperplane-outline"></span>
                                                    <?php echo hide_email(get_field('team_email', $team_id)); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if(get_field('team_phone', $team_id)): ?>
                                                <div class="team__popup__icon">
                                                    <span class="icon ion-ios-telephone-outline"></span>
                                                    <a href="tel:<?php the_field('team_phone', $team_id); ?>" target="_blank"><?php the_field('team_phone', $team_id); ?></a>
                                                </div>
                                            <?php endif; ?>

                                            <p><?php echo $get_post_object->post_content; ?></p>
                                        </div><!-- team__popup__content -->
                                    </div><!-- team__popup -->
                                </div><!-- team__popup__holder -->
                            </div><!-- masonry__item__wrapper -->
                                
                        </div><!-- masonry__item -->

                <?php
                    elseif(get_row_layout() === 'testimonial'):

                        $orderby = 'date';
                        $post_in = '';

                        if(get_sub_field('ms_testimonial_type') == 'latest') {
                            $orderby = 'date';

                        } elseif(get_sub_field('ms_testimonial_type') == 'featured') {
                            $post_in = array(get_sub_field('ms_testimonial_featured'));

                        } elseif(get_sub_field('ms_testimonial_type') == 'random') {
                            $orderby = 'rand';
                        }

                        $args = array(
                            'post_type'         => 'testimonial',
                            'post_status'       => 'publish',
                            'posts_per_page'    => 1,
                            'post__in'          => $post_in,
                            'orderby'           => $orderby,
                            'order'             => 'desc'
                        );

                        $ms_testim = new WP_Query($args);
                ?>
                        <div class="masonry__item <?php echo $row_layout; ?>">
                            <div class="masonry__item__wrapper">
                                <?php while($ms_testim->have_posts()) : $ms_testim->the_post(); ?>
                                    <div class="testim__top">
                                        <figure><i class="ion-heart"></i></figure>
                                        <a href="<?php echo esc_url(home_url()); ?>/testimonials/">Read all testimonials <i class="ion-arrow-right-c"></i></a>
                                    </div><!-- testim__top -->

                                    <div class="testim__content">
                                        <p><?php echo trunc(get_field('testim_quote'), 30); ?></p>
                                        <h6><?php the_field('testim_name'); ?></h6>
                                    </div><!-- testim__content -->
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div><!-- masonry__item__wrapper -->
                        </div><!-- masonry__item -->

                <?php
                    elseif(get_row_layout() === 'feature_tall'):

                    $attachment_id = get_sub_field('ms_feature_tall_image');
                    $feat_tall_img = vt_resize($attachment_id,'' , 800, 800, true);

                    $link = get_sub_field('masonry_link');
                    $target = '';
                    if($link['masonry_link'] && $link['masonry_link_target']) {
                        $target = ' target="_blank"';
                    }
                ?>
                        <div class="masonry__item <?php echo $row_layout; ?>">
                            <div class="masonry__item__wrapper">
                                <a href="<?php echo $link['masonry_link_url']; ?>" title="<?php the_sub_field('ms_feature_tall_heading'); ?>" style="background-image:url(<?php echo $feat_tall_img['url']; ?>)"<?php echo $target; ?>>
                                    <div class="feat__tall__content">
                                        <h4><?php the_sub_field('ms_feature_tall_sub_heading'); ?></h4>
                                        <h3><?php the_sub_field('ms_feature_tall_heading'); ?></h3>
                                        <?php the_sub_field('ms_feature_tall_caption'); ?>
                                    </div><!-- feat__tall__content -->
                                </a>
                            </div><!-- masonry__item__wrapper -->
                        </div><!-- masonry__item -->

                <?php
                    elseif(get_row_layout() === 'feature'):

                    $attachment_id = get_sub_field('ms_feature_image');
                    $feat_tall_img = vt_resize($attachment_id,'' , 800, 400, true);

                    $link = get_sub_field('masonry_link');

                    $target = '';
                    if($link['masonry_link_url'] && $link['masonry_link_target']) {
                        $target = ' target="_blank"';
                    }
                ?>
                        <div class="masonry__item <?php echo $row_layout; ?>">
                            <div class="masonry__item__wrapper">
                                <a href="<?php echo $link['masonry_link_url']; ?>" title="<?php the_sub_field('ms_feature_tall_heading'); ?>" style="background-image:url(<?php echo $feat_tall_img['url']; ?>)"<?php echo $target; ?>>
                                    <div class="feat__overlay" style="background-color:rgba(0,17,17, <?php the_sub_field('ms_feature_overlay_opacity'); ?>);">
                                        <h4><?php the_sub_field('ms_feature_sub_heading'); ?></h4>
                                        <h3><?php the_sub_field('ms_feature_heading'); ?></h3>
                                    </div><!-- feat__tall__content -->
                                </a>
                            </div><!-- masonry__item__wrapper -->
                        </div><!-- masonry__item -->

                <?php endif; ?>
                <?php endwhile; ?>
            </div><!-- masonry -->
        <?php endif; ?>
    </div><!-- masonry__wrapper -->
