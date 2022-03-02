<?php
/**
 * AVB Vimeo
 *
 * @package advanced-video-banners/
 * @version 2.0
 */

$avb_banner_vimeo = new AVB_Banner_Vimeo($banner_data);
?>
<iframe class="avb-banner__medium embed-player hide-on-mobile" src="https://player.vimeo.com/video/<?php echo $avb_banner_vimeo->video_id(); ?>?api=1&byline=0&portrait=0&title=0&background=1&mute=1&loop=1&autoplay=0&id=<?php echo $avb_banner_vimeo->video_id(); ?>" width="980" height="520" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>