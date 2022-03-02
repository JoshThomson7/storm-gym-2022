<?php
/*
FC Buttons
*/

$align = 'align-'.get_sub_field('buttons_align');
?>
<div class="buttons__wrap <?php echo $align; ?>">
    <?php
        while(have_rows('buttons')) : the_row();

        $button_label = get_sub_field('button_label');
        $button_url = get_sub_field('button_url');
        $button_colour = get_sub_field('button_colour');

        $button_target = '';
        if(get_sub_field('button_target')) {
            $button_target = ' target="_blank"';
        }
    ?>
        <a href="<?php echo $button_url; ?>"<?php echo $button_target; ?> class="button <?php echo $button_colour; ?>"><?php echo $button_label; ?></a>

    <?php endwhile; ?>
</div><!-- buttons__wrap -->
