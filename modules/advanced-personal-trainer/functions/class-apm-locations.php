<?php
/**
 * APM Locations
 *
 * Class in charge of locations
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Locations {

    public $location_array = array();

    public function init() {

        add_action('wp_ajax_nopriv_apm_filter_locations', array($this, 'apm_filter_locations'));
        add_action('wp_ajax_apm_filter_locations', array($this, 'apm_filter_locations'));

        add_action('wp_ajax_nopriv_apm_filter_map', array($this, 'apm_filter_map'));
        add_action('wp_ajax_apm_filter_map', array($this, 'apm_filter_map'));

        add_action('acf/save_post', array($this, 'on_save_location'), 20);
        add_action('before_delete_post', array($this, 'on_delete_location'));
        add_action('wp_trash_post', array($this, 'on_delete_location'));

    }

    /**
     * Locations WP_Query
     * 
     * @param array $custom_args
     */
    public function get_locations($custom_args = array()) {

        $posts = array();

        $default_args = array(
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'ids'
        );

        $args = wp_parse_args($custom_args, $default_args);

        $posts = new WP_Query($args);
        return $posts->posts;

    }

    /**
     * gets location by service
     * 
     * @param int $product_id
     */
    public function get_locations_by_service($service_id) {

        // Bail early if not numeric
        if(!is_numeric($service_id)) { return false; }

        $args = array(
            'meta_query' => array(
                array(
                    'key'       => 'clinic_services',
                    'value'     => sprintf(':"%s";', $service_id),
                    'compare'   => 'LIKE'
                )
            )
        );

        return $this->get_locations($args);

    }

    /**
     * Returns all the clinics for a given area
     *
     * @param  int $area_id
     * @return array
     */
    public function get_locations_by_area($area_id) {

        // Bail early if not numeric
        if(!is_numeric($area_id)) { return false; }
        
        $args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'location_area',
                    'field'    => 'id',
                    'terms'    => $area_id,
                )
            )
        );

        return $this->get_locations($args);

    }

    /**
     * Works out the clinics where a product/service is available
     * 
     * @param int $product_id
     * @param int $tier_id (optional)
     * @param string $return Accepts comma_list | null (array of IDs)
     */
    public function get_locations_by_product($product_id, $tier_id = null, $return = null) {

        // Bail early if not numeric
        if(!is_numeric($product_id)) { return false; }

        $args = array(
            'meta_query' => array(
                array(
                    'key'       => 'clinic_products',
                    'value'     => sprintf(':"%s";', $product_id),
                    'compare'   => 'LIKE'
                )
            )
        );

        if($tier_id) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'pricing_tier',
                    'terms' => $tier_id,
                    'field' => 'term_id',
                    'operator' => 'IN'
                )
            );
        }

        return $this->get_locations($args);

    }

    /**
     * Returns all the clinics where a given practitioner works
     *
     * @param  int $practitioner_id
     * @return array
     */
    public function get_locations_by_practitioner($practitioner_id) {

        // Bail early if not numeric
        if(!is_numeric($practitioner_id)) { return false; }

        $practitioner = new APM_Practitioner($practitioner_id);
        return $practitioner->locations();

    }

    /**
     * Areas Taxonomy
     * 
     * @param array $custom_args
     */
    public function get_areas() {

        $terms = get_terms('location_area', array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1
        ));

        return $terms;

    }

    /**
     * Gets data in AJAX Callback
     */
    public function get_filter_data($posted_data) {

        parse_str($posted_data['form_data'], $form_data);

        // Get data
        $geolocate = isset($posted_data['geolocate']) && !empty($posted_data['geolocate']) ? $posted_data['geolocate'] : null;
        $radius = isset($form_data['apm_radius']) && !empty($form_data['apm_radius']) ? $form_data['apm_radius'] : 874;
        $service_id = isset($form_data['apm_service']) && !empty($form_data['apm_service']) ? $form_data['apm_service'] : null;
        $practitioner_id = isset($form_data['apm_practitioner']) && !empty($form_data['apm_practitioner']) ? $form_data['apm_practitioner'] : null;

        $locations = new APM_Locations();
        $get_locations = $locations->get_locations();

        if($geolocate) {
            $get_locations = $locations->geolocate($geolocate, $radius);
        }

        if($service_id) {
            $get_locations = $locations->get_locations_by_service($service_id);
        }

        if($practitioner_id) {
            $get_locations = $locations->get_locations_by_practitioner($practitioner_id);
        }

        return array(
            'locations' => $get_locations,
            'posted' => array(
                'geolocate' => $geolocate,
                'radius' => $radius,
                'service' => $service_id,
                'practitioner' => $practitioner_id
            )
        );
    }

    /**
     * 
     * AJAX Callbacks
     * 
     * apm_filter_locations()
     * apm_filter_map()
     * 
     */

    /**
     * Filter Locations.
     *
     * @since	1.0
     */
    public function apm_filter_locations() {

        // Security check.
        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        $filtered = $this->get_filter_data($_POST);

        include APM_PATH .'templates/locations/locations-map.php';
        include APM_PATH .'templates/locations/locations-filters.php';
        include APM_PATH .'templates/locations/locations-loop.php';

        wp_die();

    }

    /**
     * Filter Map.
     *
     * @since	1.0
     */
    public function apm_filter_map() {

        // Security check.
        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        $filtered = $this->get_filter_data($_POST);
        $locations = $filtered['locations'];

        if($filtered['posted']['geolocate']) {
            $locations = wp_list_pluck($filtered['locations'], 'ID');
        }

        wp_send_json($locations);
        wp_die();

    }

    /**
     * Get posts by coordinates
     * 
     * @param $props
     */
    private function geolocate($location, $radius = 874) {

        // Collect array of IDs
        $ids = array();

        // Geocode location.
        $geo_data = APM_Helpers::geocode($location);

        if(!empty($geo_data) && is_array($geo_data)) {
        
            $lat = $geo_data[0];
            $lng = $geo_data[1];

            if(!empty($lat) && !empty($lng)) {

                global $wpdb;
            
                $geoSQL = $wpdb->prepare("SELECT clinics.*, fl1_posts.post_title FROM (SELECT WP_ID, SQRT(POW(69.1 * (lat - %s), 2) + POW(69.1 * (%s - lng) * COS(lat / 57.3), 2)) AS distance FROM clinic_locations HAVING distance < %d) as clinics INNER JOIN fl1_posts ON fl1_posts.ID = clinics.WP_ID ORDER BY distance", $lat, $lng, $radius);
                $geoLocations = $wpdb->get_results($geoSQL);
                
                // If no clinics are found, find nearest one.
                if(empty($geoLocations)) {
                    $nearest = 1;
                    $nearestSQL = $wpdb->prepare("SELECT WP_ID, SQRT(POW(69.1 * (lat - %s), 2) + POW(69.1 * (%s - lng) * COS(lat / 57.3), 2)) AS distance FROM clinic_locations ORDER BY distance LIMIT 1", $lat, $lng);
                    $geoLocations = $wpdb->get_results($nearestSQL);
                }
                
                // Collect array WP_ID => distance
                if(!empty($geoLocations)) {
                    foreach($geoLocations as $location) {
                        array_push($ids, array(
                            'ID' => $location->WP_ID,
                            'distance' => $location->distance,
                        ));
                    }
                }
            
            }

        }

        return $ids;

    }

    /**
     * In order to use radius searching, save the lat/long data into
     * separate custom fields when location data is saved
     * 
     * @param int $post_id
     */
    public function on_save_location($post_id) {

        global $wpdb;
        
        $screen = get_current_screen();
    
        if(get_post_type($post_id) === 'location') {
    
            $clinic_address = get_field('clinic_address', $post_id);

            if(!empty($clinic_address)) {
             
                $clinic_lat = $clinic_address['lat'];
                $clinic_lng = $clinic_address['lng'];
        
                update_field('clinic_lat', $clinic_lat, $post_id);
                update_field('clinic_lng', $clinic_lng, $post_id);
        
                $check_exists = $wpdb->get_row("SELECT WP_ID FROM clinic_locations WHERE WP_ID = ".$post_id);
        
                if(isset($check_exists->WP_ID) && $check_exists->WP_ID != ''){
                    $wpdb->update('clinic_locations', array('lat' => $clinic_lat, 'lng' => $clinic_lng), array('WP_ID' => $post_id));
                } else {
                    $wpdb->insert('clinic_locations', array('WP_ID' => $post_id, 'lat' => $clinic_lat, 'lng' => $clinic_lng));
                }

            }
    
            // Connect Feefo APi and update rating.
            $feefo = get_field('feefo_clinics', $post_id);
            $feefo_data = get_feefo(array($feefo));
            $feefo_rating_average = $feefo_data['rating']['average'];
            $feefo_rating_max = $feefo_data['rating']['max'];
            $feefo_rating_count = $feefo_data['rating']['count'];
    
            update_field('clinic_feefo_rating_average', $feefo_rating_average, $post_id);
            update_field('clinic_feefo_rating_max', $feefo_rating_max, $post_id);
            update_field('clinic_feefo_rating_count', $feefo_rating_count, $post_id);
            
            // Create Feefo Cron.
            apm_create_feefo_cron($post_id); // See apm-feefo.php
    
        } elseif(strpos($screen->id, 'theme-general-settings') == true) { 
    
            $feefo_data = get_feefo();
    
            $feefo_total_reviews = $feefo_data['rating']['count'];
            $feefo_total_average = $feefo_data['rating']['average'];
    
            update_field('feefo_total_average', $feefo_total_average, 'option');
            update_field('feefo_total_reviews', $feefo_total_reviews, 'option');
    
            apm_create_feefo_cron('option'); // See apm-feefo.php
    
        }

    }

    public function on_delete_location($post_id){

        // check it's a clinic
        global $post_type;   
        if($post_type != 'clinic') return;
    
        // delete entry in table
        global $wpdb;
        $wpdb->delete('clinic_locations', array('WP_ID' => $post_id));
    
    }

}

