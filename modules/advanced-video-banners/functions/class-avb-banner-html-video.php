<?php
/**
 * AVB Banner HTML Video
 * Class in charge of the HTML Video type banner
 * 
 * @package AVB
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AVB_Banner_HTML_Video extends AVB_Banner {

    public function file() {

        return $this->get_prop('video_file') ?? null;

    }
    
    public function poster($return = 'url', $width = 1200, $height = 800, $crop = true) {

        $image_id = $this->get_prop('video_poster');
        $image_url = '';
        if($image_id) { 
            $image_url = vt_resize($image_id, '', $width, $height, $crop);
            return isset($image_url[$return]) ? $image_url[$return] : null;
        }

        return null;

    }

}
