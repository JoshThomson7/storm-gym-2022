<?php
/**
 * APT Video
 *
 * Class in charge of single video
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APT_Video {

    /**
	 * The post ID.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    protected $id;
    
    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @access public
	 * @param int $id
	 */
    public function __construct($id = null) {

        $this->id = $id;

    }

    /**
     * Gets post ID.
     * If not set, use global $post
     */
    public function id() {

        if($this->id) {

            return $this->id;

        } else {

            global $post;
            
            if(isset($post->ID)) {
                return $post->ID;
            }

        }

        return null;

    }

    /**
     * Returns post title
     */
    public function title() {

        return get_the_title($this->id());

    }

    /**
     * Returns permalink
     */
    public function url() {

        return get_permalink($this->id());

    }

    /**
     * Returns video_platform meta
     */
    public function platform() {

        return get_field('video_platform', $this->id()) ?? null;

    }

    /**
     * Returns video_id meta
     */
    public function video_id() {

        return get_field('video_id', $this->id()) ?? null;

    }

    /**
     * Rest API Data output
     * 
     * @return object $data
     */
    public function rest_api_data() {

        $data = new stdClass();

        $data->ID = $this->id();
        $data->title = html_entity_decode($this->title());
        $data->permalink = $this->url();
        $data->platform = $this->platform();
        $data->video_id = $this->video_id();
        
        return $data;

    }

}

