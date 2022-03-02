<?php
/**
 * AVB Banner YouTube
 * Class in charge of the YouTube type banner
 * 
 * @package AVB
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AVB_Banner_YouTube extends AVB_Banner {

    public function video_id() {

        return $this->get_prop('video_id') ?? null;

    }

}
