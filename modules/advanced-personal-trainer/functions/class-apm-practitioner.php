<?php
/**
 * APM Practitioner
 *
 * Class in charge of single practitioner
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Practitioner {

    /**
	 * The post ID.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    protected $id;
    protected $lumeon_id;
    
    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @access public
	 * @param int $id
	 */
    public function __construct($id = null, $lumeon_id = null) {
        
        $this->id = $id;

        if($lumeon_id) {
            $this->id = $this->get_by_lumeon_id($lumeon_id);
        }

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
     * Returns the practitioner's Lumeon ID
     */
    public function lumeon_id() {

        return get_field('team_lumeon_id', $this->id());

    }

    /**
     * Returns profile picture
     *
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @see vt_resize() in modules/wp-image-resize.php
     */
    public function profile_picture($width = null, $height = null, $crop = true) {

        $attachment_id = get_field('team_member_photo', $this->id());

        if($attachment_id) {
            if($width || $height) {
                return vt_resize($attachment_id, '', $width, $height, $crop);
            } else {
                return $attachment_id;
            }
        }

        return false;

    }

    /**
     * Returns job title
     */
    public function job_title() {

        return get_field('team_member_title', $this->id());

    }

    /**
     * Returns short bio
     */
    public function short_bio() {

        return get_field('team_member_short_bio', $this->id());

    }

    /**
     * Returns short bio
     */
    public function working_hours() {

        return get_field('working_hours', $this->id());

    }

    /**
     * Returns iCal feed URL
     */
    public function ical_feed_url() {

        return get_field('ical_feed', $this->id());

    }

    /**
     * Returns array of products
     * offered by practitioner
     * 
     * @param string $return
     */
    public function products($return = '') {

        $getProducts = get_field('team_products', $this->id());
        $products = array();

        switch ($return) {
            case 'ids':
                if(!empty($getProducts)) {
                    foreach($getProducts as $product) {
                        array_push($products, $product->ID);
                    }
                }
                break;
            
            default:
                $products = $getProducts;
                break;
        }

        return $products;

    }

    /**
     * Returns array of location IDs
     * where practitioner works.
     */
    public function locations() {

        return get_field('team_locations', $this->id());

    }   

    /**
     * Returns array of service IDs.
     */
    public function services() {

        return get_field('team_services', $this->id());

    }   

    /**
     * Returns gender WP_Term object.
     */
    public function gender() {

        return wp_get_post_terms($this->id(), 'gender', array( 'fields' => 'all' ));

    }

    /**
     * Gets practitioner by Lumeon ID.
     */
    private function get_by_lumeon_id($lumeon_id) {

        if(!$lumeon_id) { return null; }

        $practitioners = new APM_Practitioners();

        $practitioner = $practitioners->get_practitioners(array(
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'team_lumeon_id',
                    'value' => $lumeon_id,
                    'compare' => '='
                )
            )
        ));
        
        if(!empty($practitioner)) { 
            return reset($practitioner);
        }

        return null;

    }
}

