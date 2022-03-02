<?php
/**
 * AVB Image
 *
 * @package advanced-video-banners/
 * @version 2.0
 */

$avb_banner_image = new AVB_Banner_Image($banner_data);
?>
<div class="avb-banner__medium image <?php if($banner->get_prop('image_mobile')): ?>hide-on-mobile<?php endif; ?>" style="background-image:url(<?php echo $avb_banner_image->image(); ?>);">
    <img data-lazy="<?php echo $avb_banner_image->image(); ?>">
</div>