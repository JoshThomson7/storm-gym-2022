<?php
/**
 * Inner banner
 */

global $post;
$post_id = $post->ID;

if(is_shop()) {
    $post_id = get_option( 'woocommerce_shop_page_id' ); 
}

$attachment_id = get_field('page_banner', $post_id);
$banner_image = '';
if($attachment_id) {
    $banner_image = vt_resize($attachment_id,'' , 900, 500, true);
    $banner_image = ' style="background-image: url('.$banner_image['url'].')"';
}

$alt_title = get_field('page_banner_alt_title', $post_id);
$page_title = $alt_title ? $alt_title : get_the_title($post_id);

$page_caption = get_field('page_banner_caption', $post_id);

if(is_archive() && !is_shop()) {

    $page_caption = '<p>Blog</p>';

    if(is_category()) {
        $attachment_id = get_field('page_banner', 306);
        $page_title = single_cat_title('', false);

    } elseif(is_tag()) {
        $attachment_id = get_field('page_banner', 306);
        $page_title = single_tag_title('', false);

    } elseif(is_tax()) {
        $term_obj = get_queried_object();
        $page_title = $term_obj->name;
    }

    $attachment_id = get_post_thumbnail_id();
    $banner_image = '';
    if($attachment_id) {
        $banner_image = vt_resize($attachment_id,'' , 900, 500, true);
        $banner_image = ' style="background-image: url('.$banner_image['url'].')"';
    }

}
?>

<section class="banners inner">
    <div class="max__width">
        <div class="banner__content">
            <h1><?php echo $page_title; ?></h1>
            <?php if($page_caption): ?><?php echo $page_caption; ?><?php endif; ?>
        </div><!-- banner__content -->
        
        <?php if(!empty($attachment_id)): ?>
            <div class="banner__image"<?php echo $banner_image; ?>>
                <div class="banner__image__gradient"></div>
            </div>
        <?php endif; ?>
    </div><!-- max__width -->
</section><!-- home__banners -->