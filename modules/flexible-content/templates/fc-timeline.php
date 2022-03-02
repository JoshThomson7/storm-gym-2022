<?php
/*
    Timeline
*/
?>

<div class="fc_timeline_wrapper">
    <?php
    $step = 1;
    while(have_rows('timeline')) : the_row();
    ?>
        <div class="timeline__row">
            <article>
                <div class="timeline__caption">
                    <i class="<?php the_sub_field('timeline_icon'); ?>"></i>

                    <h3><?php the_sub_field('timeline_heading'); ?></h3>
                    <?php the_sub_field('timeline_caption'); ?>
                </div><!-- timeline__caption -->

                <div class="line"></div>
                <figure><?php echo $step; ?></figure>
            </article>
        </div><!-- timeline__row -->
    <?php $step++; endwhile; ?>
</div><!-- fc_timeline_wrapper -->
