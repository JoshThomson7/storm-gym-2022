<?php
/**
 * AVB Home Banners
 *
 * @package advanced-video-banners/
 * @version 1.0
 * @dependencies
 *      ACF PRO: https://www.advancedcustomfields.com/pro/
 *      Lighslider: http://sachinchoolur.github.io/lightslider/examples.html
 *      @see lib/lightslider
 *      YouTube API: https://developers.google.com/youtube/iframe_api_reference
 */

global $post;

$banner_height = get_field('avb_height');
$banner_dots_position = get_field('avb_dots_position');
$banner_down_arrow = get_field('avb_down_arrow');
$banners = get_field('avb');

$avb_page = is_front_page() ? 'avb-home' : 'avb-inner';

if(!empty($banners)):
?>

    <section class="avb">

        <div class="avb-banners avb-<?php echo $banner_height.'vh'; ?> avb-dots-<?php echo $banner_dots_position; ?> <?php echo $avb_page; ?>">
            <?php
                $avb_count = 1;
                foreach($banners as $banner_data):
                
                $banner_data['index'] = $avb_count;
                $banner = new AVB_Banner($banner_data);
            ?>
                
                <div class="avb-banner avb-<?php echo $banner_height.'vh'; ?>" data-type="<?php echo $banner->layout(); ?>">

                    <div class="avb-banner__caption">
                        <div class="max__width">
                            <div class="avb-banner__caption-wrap">
                                <?php if($banner->headingTop()): ?><?php echo $banner->headingTop(); ?><?php endif; ?>
                                <?php if($banner->heading()): ?><?php echo $banner->heading(); ?><?php endif; ?>
                                <?php if($banner->caption()): ?><p><?php echo $banner->caption(); ?></p><?php endif; ?>

                                <?php if($banner->button_label()): ?>
                                    <div class="avb-banner__caption-actions">
                                        <a href="<?php echo $banner->button_url(); ?>"<?php echo $banner->button_url_target(); ?> title="<?php echo $banner->button_label(); ?>" class="button">
                                            <span><?php echo $banner->button_label(); ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                        
                    <figure class="avb-banner__overlay <?php echo $banner->overlay_opacity(); ?>"></figure>

                    <div class="avb-banner__media">
                        <?php include AVB_PATH.'templates/'.$banner->layout().'.php'; ?>

                        <?php if($banner->get_prop('image_mobile')): ?>
                            <div class="avb-banner__medium show-on-mobile image" style="background-image:url(<?php echo $banner->image_mobile(); ?>);">
                                <img data-lazy="<?php echo $banner->image_mobile(); ?>">
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            <?php $avb_count++; endforeach; ?>

        </div><!-- avb-banners -->

        <?php if($banner_down_arrow): ?>
            <div class="avb__down-arrow">
                <figure>
                    <?php echo file_get_contents(AVB_PATH.'img/avb-chevron-down.svg'); ?>
                </figure>
            </div>
        <?php endif; ?>

    </section><!-- avb -->

<?php else: ?>
    
    <section class="avb">
        <div class="avb-banners avb-empty">
            <div class="avb-banner">
                <div class="avb-banner__caption">
                    <div class="max__width">
                        <div class="avb-banner__caption-wrap">
                            <h1><?php echo get_the_title($post->ID); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>
