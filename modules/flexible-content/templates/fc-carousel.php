<?php
/*
------------------------------------------------
   ______                                 __
  / ____/___ __________  __  __________  / /
 / /   / __ `/ ___/ __ \/ / / / ___/ _ \/ /
/ /___/ /_/ / /  / /_/ / /_/ (__  )  __/ /
\____/\__,_/_/   \____/\__,_/____/\___/_/

------------------------------------------------
Carousel
*/
?>


<div class="carousel_images">
    <?php
        while(have_rows('carousel_images')) : the_row();

        // URL
        $link_start = '';
        $link_end = '';

        if(get_sub_field('carousel_item_url')) {
            $target = '';

            if(get_sub_field('carousel_item_target')) {
                $target = ' target="_blank"';
            }

            $link_start = '<a href="'.get_sub_field('carousel_item_url').'" title="'.get_sub_field('carousel_item_name').'"'.$target.'>';
            $link_end = '</a>';
        }

        $attachment_id = get_sub_field('carousel_item_image');
        $carousel_image = vt_resize($attachment_id,'' , 400, 100, false);
    ?>
        <div class="carousel_image">
            <?php echo $link_start; ?>
                <img src="<?php echo $carousel_image['url']; ?>" alt="<?php the_sub_field('carousel_item_name'); ?>">
            <?php echo $link_end; ?>
        </div><!--  -->
    <?php endwhile; ?>
</div><!-- carousel_images -->
