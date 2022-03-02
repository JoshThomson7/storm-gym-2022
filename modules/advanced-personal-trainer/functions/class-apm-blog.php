<?php
/**
 * APM Blog
 *
 * Class in charge of single blog
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Blog {

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
     * Returns the exceprt
     * 
     * @param int trunc
     */
    public function excerpt($trunc = 30) {

        return trunc(get_the_excerpt($this->id()), $trunc);

    }

    /**
     * Returns date
     * 
     * @param string $format
     */
    public function date($format = 'M jS Y') {

        return get_the_time($format, $this->id());

    }

    /**
     * Returns blogger info.
     * 
     * @return object $blogger
     */
    public function blogger() {

        $blogger_type = get_field('post_blogger_type', $this->id());
        $blogger = new stdClass();

        switch ($blogger_type) {
            case 'team':
                $blogger_id = get_field('post_blogger_team', $this->id());

                if($blogger_id){
                    $practitioner = new APM_Practitioner($blogger_id);
                    $blogger->image = $practitioner->profile_picture(800, 800);
                    $blogger->name = $practitioner->title();
                    $blogger->bio = $practitioner->short_bio();
                    $blogger->job_title = $practitioner->job_title();
                    $blogger->url = $practitioner->url();
                    $blogger->locations = $practitioner->locations();
                    $blogger->services = $practitioner->services();
                }


                break;
            
            case 'custom':
                $get_blogger = get_field('blogger_details', $this->id());

                if(!empty($get_blogger)) {
                    $blogger->image = $get_blogger['post_blogger_image'];
                    $blogger->name = $get_blogger['post_blogger_name'];
                    $blogger->bio = $get_blogger['post_blogger_bio'];
                    $blogge->job_title = $get_blogger['post_blogger_job_title'];
                    $blogger->url = $get_blogger['post_blogger_permalink'];
                }
                break;
        }

        return $blogger;

    }

    /**
     * Returns blog image.
     * 
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @see vt_resize() in modules/wp-image-resize.php
     */
    public function image($width = 900, $height = 500, $crop = true) {

        $attachment_id = get_post_thumbnail_id($this->id());

        if($attachment_id) {
            return vt_resize($attachment_id,'' , $width, $height, $crop);
        }

        return false;

    }

    /**
     * Returns the post categories.
     * 
     * @param string $return | Accepts: all | all_with_object_id | ids | tt_ids | slugs | count | id=>parent | id=>name | id=>slug
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
     */
    public function categories($return = null) {

        $args = $return ? array('fields' => $return) : array();        
        $post_terms = wp_get_object_terms($this->id(), 'category', $args);

        if(!empty($post_terms) && !is_wp_error($post_terms)) {
            return $post_terms;
        }

        return null;

    }

    /**
     * Returns the main category (first in array, index [0] via reset()).
     * 
     * @see https://www.php.net/manual/en/function.reset.php
     * @param string $return | See $this->categories() above
     */
    public function main_category($return = null) {

        $post_cats = $this->categories($return);
        
        if(!empty($post_cats) && !is_wp_error($post_cats)) {
            return reset($post_cats);
        }

        return null;
        
    }

}

