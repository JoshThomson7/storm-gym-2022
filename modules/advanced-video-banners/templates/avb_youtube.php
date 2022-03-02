<?php
/**
 * AVB YouTube
 *
 * @package advanced-video-banners/
 * @version 2.0
 */

$avb_banner_youtube = new AVB_Banner_YouTube($banner_data);
?>
<iframe class="avb-banner__medium embed-player hide-on-mobile" src="https://www.youtube.com/embed/<?php echo $avb_banner_youtube->video_id(); ?>?enablejsapi=1&controls=0&fs=0&iv_load_policy=3&rel=0&showinfo=0&loop=1&playlist=<?php echo $avb_banner_youtube->video_id(); ?>&start=1" frameborder="0" allowfullscreen></iframe>