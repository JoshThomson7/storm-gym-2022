<?php
/*
    Grid Boxes
*/

$grid_box_num = get_sub_field('grid_boxes_num');
$grid_box_img_align = ' '.get_sub_field('grid_box_image_alignment');
$grid_box_spacing = get_sub_field('grid_box_spacing') ? ' style="border: '.get_sub_field('grid_box_spacing').'px transparent solid;"' : '';
$grid_box_spacing_fix = get_sub_field('grid_box_spacing') ? ' style="margin: 0 -'.get_sub_field('grid_box_spacing').'px;"' : '';

$grid_boxes_carousel = get_sub_field('grid_boxes_carousel');
?>
<div class="grid__boxes__wrapper<?php echo $grid_boxes_carousel ? ' grid-boxes-carousel' : ''; ?>"<?php echo $grid_box_spacing_fix; ?>>
    <?php
        while(have_rows('grid_boxes')) : the_row();

        $link_open = '';
        $link_close = '';
        $overlay_link_open = '';
        $overlay_link_close = '';

        if(get_sub_field( 'grid_box_button_url' )) {
            if(get_sub_field('grid_box_overlay')) {
                $overlay_link_open = '<a href="'.get_sub_field("grid_box_button_url").'" class="grid__overlay__a">';
                $overlay_link_close = '</a>';
            } else {
                $link_open = '<a href="'.get_sub_field("grid_box_button_url").'">';
                $link_close = '</a>';
            }
        }

        // box image
        $attachment_id = get_sub_field('grid_box_image');
        $grid_box_img_arr = '';
        if(get_sub_field('grid_box_image')) {
            $grid_box_img_arr = vt_resize($attachment_id,'' , 800, 500, !get_sub_field('grid_box_image_no_crop'));
        }

        if(is_array($grid_box_img_arr)) {
            $grid_box_img = '<div class="grid__box__image">'.$link_open.'<img src="'.$grid_box_img_arr['url'].'">'.$link_close.'</div>';
        }

        // where content sits
        $content_placement = '';
        if(get_sub_field('grid_box_content_below_image')) {
            $content_placement = ' content_below';
        }

        // overlay
        $overlay = '';
        $overlay_opacity = '';
        $overlay_text_align = ' '.get_sub_field('grid_box_overlay_text_align');
        if(get_sub_field('grid_box_overlay')) {
            $overlay = ' overlay';
            $overlay_opacity = ' style="background:rgba(28, 28, 49, '.get_sub_field('grid_box_overlay_opacity').');"';
            if(is_array($grid_box_img_arr)) {
                $grid_box_img = '<div class="grid__box__image" style="background-image: url('.$grid_box_img_arr['url'].')">'.$link_open.$link_close.'</div>';
            }
            
            $grid_box_img_align = '';
        }
    ?>
        <article class="<?php echo $grid_box_num.$grid_box_img_align.$overlay.$content_placement; ?>"<?php echo $grid_box_spacing; ?>>
            <?php echo $overlay_link_open; ?>
            <?php echo $grid_box_img; ?>

            <div class="grid__box__content<?php echo $overlay_text_align; ?>">
                <?php echo $link_open; ?>
                    <?php if(get_sub_field('grid_box_top_label')): ?><h5><?php the_sub_field('grid_box_top_label'); ?></h5><?php endif; ?>
                    <?php if(get_sub_field('grid_box_heading')): ?><h3><?php the_sub_field('grid_box_heading'); ?></h3><?php endif; ?>
                <?php echo $link_close; ?>
                <?php if(get_sub_field('grid_box_caption')): ?><?php the_sub_field('grid_box_caption'); ?><?php endif; ?>

                <?php if(get_sub_field('grid_box_button_label') && !get_sub_field('grid_box_overlay')): ?>
                    <a href="<?php the_sub_field('grid_box_button_url'); ?>" class="button primary"><?php the_sub_field('grid_box_button_label'); ?></a>
                <?php endif; ?>
            </div><!-- grid__box__content -->

            <?php echo $overlay_link_close; ?>
        </article>
    <?php endwhile; ?>
</div><!-- grid__boxes__wrapper -->
