<?php
/*
--------------------------------------------
    ______           __
   / ____/__  ____ _/ /___  __________
  / /_  / _ \/ __ `/ __/ / / / ___/ _ \
 / __/ /  __/ /_/ / /_/ /_/ / /  /  __/
/_/    \___/\__,_/\__/\__,_/_/   \___/

-------------------------------------------
Feature
*/
// Feature image
$attachment_id = get_sub_field('feature_image');
$feature_img = vt_resize($attachment_id,'' , 800, 500, true);

// Feature image expand
$feature_img_expand = '';
$feature_img_expand_bg = '';
if(get_sub_field('feature_img_expand')) {
    $feature_img_expand = ' fc-feature-img-expand';
    $feature_img_expand_bg = ' style="background-image: url('.$feature_img['url'].')"';
}

// Feature image align
$feature_img_align = '';
if(get_sub_field('feature_image_align') == 'right') {
    $feature_img_align = ' right';
}

// Feature text width
$feature_text_width = get_sub_field('feature_text_width').'%';

// Video
$feature_img_video = '';
if(get_sub_field('feature_video_id')) {
    $feature_img_video = '<a href="#" class="do-modal-video" data-video-id="'.get_sub_field('feature_video_id').'"><figure><i class="fa fa-play"></i></figure></a>';
}
?>

<div class="fc_feature_wrapper<?php echo $feature_img_align; ?><?php echo $feature_img_expand; ?>">
    <div class="feature__image"<?php echo $feature_img_expand_bg; ?>>
        <?php echo $feature_img_video; ?>
        <?php if(!$feature_img_expand_bg): ?>
            <img src="<?php echo $feature_img['url']; ?>" alt="" />
        <?php endif; ?>
        <?php if(get_sub_field('feature_video_id')): ?><div class="feature__video-overlay"></div><?php endif; ?>
    </div><!-- feature__image -->

    <div class="feature__text" style="width: <?php echo $feature_text_width; ?>">
        <?php if(get_sub_field('feature_top_heading')): ?><h5><?php the_sub_field('feature_top_heading') ?></h5><?php endif; ?>
        <?php if(get_sub_field('feature_heading')): ?><h3><?php the_sub_field('feature_heading') ?></h3><?php endif; ?>
        <?php the_sub_field('feature_text'); ?>

        <?php if(get_sub_field('feature_link_text') && get_sub_field('feature_link_url')): ?>
            <div class="feature__action">
                <a href="<?php the_sub_field('feature_link_url'); ?>" class="button primary">
                    <span><?php the_sub_field('feature_link_text'); ?></span>
                </a>
            </div><!-- feature__action -->
        <?php endif; ?>
    </div><!-- feature__text -->
</div><!-- fc_feature_wrapper -->
