<?php
/*
    Grid Links
*/
$grid_box_num = get_sub_field('boxes_per_row');
?>

<div class="grid">
    <?php
        $grid_links = get_sub_field('grid_links');

        foreach($grid_links as $grid_link):

        $attachment_id = get_field('page_banner', $grid_link->ID);
        $grid_link_img = vt_resize( $attachment_id, '', 800, 800, true);
    ?>
        <article class="<?php echo $grid_box_num; ?>">
            <a href="<?php echo get_permalink($grid_link->ID); ?>" style="background-image:url(<?php echo $grid_link_img['url']; ?>);">

                <div class="grid__link__content">
                    <h3><?php echo get_the_title($grid_link->ID); ?></h3>
                </div><!-- grid__link__content -->
            </a>
        </article>
    <?php endforeach; ?>
</div><!-- grid -->
