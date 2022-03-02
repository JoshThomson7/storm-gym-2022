<?php
/*
*	FC Tab Scrollbar
*
*	@package Flexible Content
*	@version 1.0
*/
?>
<section class="fc_tab_scrollbar">
    <div class="max__width">
        <ul>
            <?php
                $fc_count = 0;
                while(have_rows('fc_content_types')) : the_row();
            ?>
                <?php
                    if(get_field('fc_content_types_'.$fc_count.'_fc_options_tab_name')):
                        $tab_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", get_field('fc_content_types_'.$fc_count.'_fc_options_tab_name')));
                ?>
                        <li><a href="#<?php echo $tab_id; ?>_section" class="scroll"><?php the_field('fc_content_types_'.$fc_count.'_fc_options_tab_name'); ?></a></li>
                <?php endif; ?>
            <?php $fc_count++; endwhile; ?>
        </ul>
    </div><!-- max__width -->
</section><!-- fc_tab_scrollbar -->
