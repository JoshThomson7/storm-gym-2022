<?php
/**
 * AVB Banner Vimeo
 * Class in charge of the Vimeo type banner
 * 
 * @package AVB
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AVB_Banner_Vimeo extends AVB_Banner {

    public function video_id() {

        return $this->get_prop('video_id') ?? null;

    }

}
