<?php
/*
---------------------------
  ______      __
 /_  __/___ _/ /_  _____
  / / / __ `/ __ \/ ___/
 / / / /_/ / /_/ (__  )
/_/  \__,_/_.___/____/

---------------------------
Tabs
*/

$layout = get_sub_field('tabs_layout');
$alignment = ' '.get_sub_field('tabs_alignment');
?>

<div class="tabbed-wrapper <?php echo $layout.$alignment; ?>">
    <ul class="tabbed">
        <?php
            while(have_rows('tabs')) : the_row();
            $tabbed_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", get_sub_field('tab_heading')));
        ?>
            <li><a href="#" data-id="<?php echo $tabbed_id; ?>_tabbed" title="<?php the_sub_field('tab_heading'); ?>"><?php the_sub_field('tab_heading'); ?></a></li>
        <?php endwhile; ?>
    </ul>

    <?php
        while(have_rows('tabs')) : the_row();
        $tabbed_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", get_sub_field('tab_heading')));
    ?>
        <div class="tab__content <?php echo $tabbed_id; ?>_tabbed">
            <h3><?php the_sub_field('tab_heading'); ?></h3>
            <?php echo apply_filters('the_content', get_sub_field('tab_content')); ?>
        </div><!-- tab-content -->
    <?php endwhile; ?>
</div>