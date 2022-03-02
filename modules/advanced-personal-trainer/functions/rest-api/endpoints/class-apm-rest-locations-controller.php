<?php
/**
 * Extends the WordPress REST API.
 *
 * Adds custom endpoint to the WordPress REST API.
 *
 * @package    APM
 * @subpackage apm/functions/rest-api
 * @author     FL1 Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class APM_REST_Locations_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'locations';

	/**
	 * Register REST route.
	 */
	public function register_rest_route() {

        register_rest_route( APM_REST_API_NAMESPACE, '/'.$this->route.'/', array(
            'methods' => WP_REST_Server::READABLE, // GET
            'callback' => array( $this, 'getData' ),
            'permission_callback' => array($this, 'check_for_errors'),
            'args' => array(
                'id' => array(
                    'description'       => 'Query one post only from collection by ID.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
                'author' =>  array(
                    'description'       => 'Query the collection by author ID.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'status' =>  array(
                    'description'       => 'Query the collection by post_status',
                    'type'              => 'string',
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'per_page' => array(
                    'description'       => 'Maxiumum number of items to show per page.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    //'sanitize_callback' => 'absint', absint converts to non-negative value
                ),
                'order' =>  array(
                    'description'       => 'Change order of the collection.',
                    'type'              => 'string',
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'orderby' =>  array(
                    'description'       => 'The sort order of the collection.',
                    'type'              => 'string',
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'include' =>  array(
                    'description'       => 'Include a post by ID.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint'
                ),
                'exclude' =>  array(
                    'description'       => 'Exclude a post by ID.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint'
                ),

                /**
                 * "By" and "by" args
                 */
                'by' =>  array(
                    'description'       => 'Get locations by different actions. Accepts: coords | practitioners | services',
                    'type'              => 'string',
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),

                // Coords
                'lat' =>  array(
                    'description'       => 'Get latitude.',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    }
                ),
                'lng' =>  array(
                    'description'       => 'Get longitude.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    }
                ),

                // Prctitioners
                'practitioners' =>  array(
                    'description'       => 'Get locations by different practitioners.',
                    'type'              => 'string',
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric($param);
                    },
                ),

                // Services
                'services' =>  array(
                    'description'       => 'Get locations by different services.',
                    'type'              => 'string',
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric($param);
                    },
                ),
            )
        ));

	}

    /**
     * Returns team members
	 *
	 * @since 2.0
	 */
	public function getData(WP_REST_Request $request) {

        $locations = new APM_Locations();

        $id = $request['id'] ? $request['id'] : '';
        $author = $request['author'] ? $request['author'] : '';
        $posts_per_page = $request['per_page'] ? $request['per_page'] : -1;
        $order = $request['order'] ? $request['order'] : 'asc';
        $orderby = $request['orderby'] ? $request['orderby'] : 'name';
        $exclude = $request['exclude'] ? $request['exclude'] : '';
        $include = $request['include'] ? array($request['include']) : '';

        /**
         * By params
         */
        $by = $request['by'] ? $request['by'] : '';

        // By coords
        $lat = $request['lat'] ? $request['lat'] : '';
        $lng = $request['lng'] ? $request['lng'] : '';

        // By practitioners
        $practitioners = $request['practitioners'] ? $request['practitioners'] : '';

        if($by) {
            switch ($by) {
                case 'coords':
                    $byCoords = $this->byCoords($lat, $lng);
                    $include = !empty($byCoords) ? array_keys($byCoords) : '';
                    break;

                case 'practitioners':
                    if($practitioners) {
                        $include = $this->byPractitioners($practitioners);
                    }
                    break;
                
                default:
                    break;
            }
        }

        // WP_Query arguments
        $args = array(
            'p'                => $id,
            'author'           => $author,
            'post_status'      => $status,
            'posts_per_page'   => $posts_per_page,
            'order'            => $order,
            'orderby'          => $orderby,
            'post__in'         => $include,
            'post__not_in'     => array($exclude)
        );

        if(!empty($include)) {
            $args['orderby'] = 'post__in';
        }

        $getLocations = $locations->get_locations($args);

        $posts = array();

        if(!empty($getLocations)) {
            foreach($getLocations as $location_id) {

                $theLocation = new stdClass();

                $location = new APM_Location($location_id);

                $theLocation->ID = $location->id();
                $theLocation->name = html_entity_decode($location->title());
                $theLocation->permalink = $location->url();
                $theLocation->address = $location->location('address');
                $theLocation->addressLines = $location->location('address', true);
                $theLocation->lat = $location->location('lat');
                $theLocation->lng = $location->location('lng');
                $theLocation->openingTimes = $location->opening_times();
                $theLocation->fees = $location->fees();
                $theLocation->lumeonid = $location->lumeonid();
                $theLocation->services = $location->services();
                $theLocation->products = $location->products();
                $theLocation->genders = $location->genders();

                // Include distance if by = coors
                if($by === 'coords') {
                    $theLocation->distance = round($byCoords[$location_id], 1);
                }

                array_push($posts, $theLocation);
            }
        }
        
        return $posts;

    }

    /**
     * Get posts by coordinates
     * 
     * @param $props
     */
    private function byCoords($lat, $lng) {

        // Collect array of IDs
        $ids = array();

        if(!empty($lat) && !empty($lng)) {

            global $wpdb;
        
            $geoSQL = $wpdb->prepare("SELECT clinics.*, fl1_posts.post_title FROM (SELECT WP_ID, SQRT(POW(69.1 * (lat - %s), 2) + POW(69.1 * (%s - lng) * COS(lat / 57.3), 2)) AS distance FROM clinic_locations HAVING distance < 500) as clinics INNER JOIN fl1_posts ON fl1_posts.ID = clinics.WP_ID ORDER BY distance", $lat, $lng);
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
                    $ids[$location->WP_ID] = $location->distance;
                }
            }
        
        }

        return $ids;

    }

    /**
     * Get posts by coordinates
     * 
     * @param $props
     */
    private function byPractitioners($practitioners) {

        // Collect array of IDs
        $ids = array();

        if(!empty($practitioners)) {

            // Do we have multiple?
            if(strpos($practitioners, ',') !== false) {

                $practitioners = explode($practitioners, ',');

            } else {
                $practitioners = array($practitioners);
            }

            if(!empty($practitioners) && is_array($practitioners)) {
                
                foreach($practitioners as $practitioner_id) {
                    $practitioner = new APM_Practitioner($practitioner_id);
                    $ids = $ids + $practitioner->locations();
                }

            }

        }

        return array_unique($ids);
        
    }

    /**
     * Helper function that checks
     * if all values in an array are numeric
     */
    private function array_is_numeric($array) {

        $is_numeric = true;

        foreach($array as $value) {
            if(!is_numeric($value)) {
                $is_numeric = false;
            }
        }

        return $is_numeric;
    }

    /**
     * Check for errors.
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
    */
    public function check_for_errors( $request ) {

        //if(!is_user_logged_in()) { return false; }

        return $request;

    }

}