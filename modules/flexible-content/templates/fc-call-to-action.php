<?php
/*
    Call to action
*/

// image
$cta_bk = get_sub_field('cta_bk');
$bk_img = '';
if($cta_bk) {
    $attachment_id = get_sub_field('cta_bk');
    $bk_img = vt_resize($attachment_id, '', 2000, 600, true);
    $bk_img = ' style="background-image:url('.$bk_img['url'].');"';
}

$scroll = '';
if (substr(get_sub_field('cta_button_link'), 0, 1) === '#') {
    $scroll = 'scroll ';
}

$parallax = '';
if(get_sub_field('cta_parallax')) {
    $parallax = ' cta__parallax';
}

// styles
$styles = get_sub_field('fc_styles');
$full_width = $styles['fc_full_width'] == true ? true : false;
?>

    <div class="cta__wrapper"<?php echo $bk_img; ?>>
        <?php if($full_width): ?><div class="max__width"><?php endif; ?>
        <article>
            <h2><?php the_sub_field('cta_heading'); echo $padding; ?></h2>
            <p><?php the_sub_field('cta_caption'); ?></p>
        </article>

        <?php if(get_sub_field('cta_button_link')): ?>
            <a href="<?php the_sub_field('cta_button_link'); ?>" class="button <?php the_sub_field('cta_button_colour'); ?>"><?php the_sub_field('cta_button_label'); ?></a>
        <?php endif; ?>

        <div class="cta__overlay" style="background:rgba(0, 0, 0, <?php the_sub_field('cta_overlay_opacity'); ?>)"></div>
        <div class="cta__image<?php echo $parallax; ?>"<?php echo $bk_img; ?>></div>
        <?php if($full_width): ?></div><?php endif; ?>
    </div><!-- cta__wrapper -->
