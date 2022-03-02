<?php
// Resets
$banner_class = '';
$banner_img = '';
$banner_video = '';
$target = '';

if(get_sub_field('home_banner_url_target')) {
    $target = ' target="_blank"';
}

$opacity = 'opacity-'.get_sub_field('home_banner_overlay_opacity');

// button play?
$button_class = '';
if(get_sub_field('home_banner_button_play')) {
    $button_url = '#';
    $button_class = ' class="video__play"';
    $target = '';
} else {
    $button_url = get_sub_field('home_banner_button_url');
}

if(get_row_layout() == 'video') { // Video

    $banner_class = ' banner__video';

    if(get_sub_field('home_banner_video_altimg')) {
        $attachment_id = get_sub_field('home_banner_video_altimg');
        $banner_altimg = vt_resize($attachment_id,'' , 2000, 1200, true);
        $banner_altimg = ' style="background-image:url('.$banner_altimg['url'].');"';
        $banner_img = '<div class="banner__video__altimg"'.$banner_altimg.'></div>';
    }

    if(get_sub_field('home_banner_video_id')) {
        $banner_video = '<div class="banner__video__player"><div class="apf__video" id="player'.$banner_counter.'"></div></div>';
        $banner_video .= '<div class="banner__video__controls"><a href="#" class="video__stop"><i class="ion-stop"></i></a><a href="#" class="video__control video__mute"><i class="ion-volume-mute"></i></a></div>';
    }

} elseif(get_row_layout() == 'standard') { // Standard

    $banner_class = ' banner__standard';

    if(get_sub_field('home_banner_image')) {
        $attachment_id = get_sub_field('home_banner_image');
        $banner_img_url = vt_resize($attachment_id, '', 2000, 1200, true);
        $banner_img_url = ' style="background-image:url('.$banner_img_url['url'].');"';
        $banner_img = '<div class="banner__bg__img"'.$banner_img_url.'></div>';
    }

}
