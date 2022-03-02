<?php
/*
-----------------------------------------------------
    ___                            ___
   /   | ______________  _________/ (_)___  ____
  / /| |/ ___/ ___/ __ \/ ___/ __  / / __ \/ __ \
 / ___ / /__/ /__/ /_/ / /  / /_/ / / /_/ / / / /
/_/  |_\___/\___/\____/_/   \__,_/_/\____/_/ /_/

-----------------------------------------------------
Accordion
*/
?>


<?php while(have_rows('accordion')) : the_row(); ?>

    <div class="accordion__wrap">

        <h3 class="toggle"><?php the_sub_field('accordion_heading'); ?> <span class="fal fa-chevron-down"></h3>

        <div class="accordion__content">
            <?php echo apply_filters('the_content', get_sub_field('accordion_content')); ?>
        </div><!-- accordion__content -->

        <div class="accordion__bg"></div>

    </div><!-- accordion__wrap -->

<?php endwhile; ?>
