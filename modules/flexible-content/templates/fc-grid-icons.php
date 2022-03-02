<?php
/*
---------------------------------------------------
    ____                    ______     _     __
   /  _/________  ____     / ____/____(_)___/ /
   / // ___/ __ \/ __ \   / / __/ ___/ / __  /
 _/ // /__/ /_/ / / / /  / /_/ / /  / / /_/ /
/___/\___/\____/_/ /_/   \____/_/  /_/\__,_/

---------------------------------------------------
Icon Grid
*/
// Iterms per row
$items_per_row = get_sub_field('icon_grid_items');

// Text align
$text_align = get_sub_field('icon_grid_text_align');
if(!$text_align) {
    $text_align = ' right';
}
?>

<div class="fc_icon_grid_wrapper <?php echo $text_align; ?>">
    <?php
        while(have_rows('icon_grid')) : the_row();
    
        $icon = '<span class="'.get_sub_field('icon_grid_icon').'"></span>';
        if(get_sub_field('icon_grid_icon_img')) {
            $icon = '<span><img src="'.get_sub_field('icon_grid_icon_img').'" /></span>';
        }
    ?>
        <article class="<?php echo $items_per_row; ?>">
            <?php if(get_sub_field('icon_grid_links_to')): ?><a href="<?php the_sub_field('icon_grid_links_to') ?>"><?php endif; ?>
                <?php if(get_sub_field('icon_grid_icon') || get_sub_field('icon_grid_icon_img')): ?>
                    <div class="icon-grid__icon"><?php echo $icon; ?></div>
                <?php endif; ?>

                <div class="icon-grid__content">
                    <?php if(get_sub_field('icon_grid_heading')): ?><h4><?php the_sub_field('icon_grid_heading'); ?></h4><?php endif; ?>
                    <?php if(get_sub_field('icon_grid_content')): ?><?php the_sub_field('icon_grid_content'); ?><?php endif; ?>
                </div><!-- icon-grid__content -->
            <?php if(get_sub_field('icon_grid_links_to')): ?></a><?php endif; ?>
        </article>
    <?php endwhile; ?>
</div><!-- fc_icon_grid_wrapper -->
