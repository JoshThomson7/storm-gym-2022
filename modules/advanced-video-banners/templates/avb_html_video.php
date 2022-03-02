<?php
/**
 * AVB Video HTML
 *
 * @package advanced-video-banners/
 * @version 2.0
 */

$avb_banner_html_video = new AVB_Banner_HTML_Video($banner_data);
?>
<video class="avb-banner__medium html-video <?php if($banner->get_prop('image_mobile')): ?>hide-on-mobile<?php endif; ?>" loop muted autoplay preload="metadata" poster="<?php echo $avb_banner_html_video->poster(); ?>">
    <source src="<?php echo $avb_banner_html_video->file(); ?>" type="video/mp4" />
</video>