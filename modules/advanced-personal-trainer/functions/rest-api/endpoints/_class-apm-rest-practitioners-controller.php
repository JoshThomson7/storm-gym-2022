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

class APM_REST_Practitioners_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'practitioners';

	/**
	 * Register REST route.
	 */
	public function register_rest_route() {

        register_rest_route( APM_REST_API_NAMESPACE, '/'.$this->route.'/', array(
            'methods' => WP_REST_Server::READABLE, // GET
            'callback' => array( $this, 'get_practitioners' ),
            'args' => array(
                'id' => array(
                    'description'       => 'Query one post only from collection by ID.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
                'lumeon_id' => array(
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
            )
        ));

	}

    /**
     * Returns team members
	 *
	 * @since 2.0
	 */
	public function get_practitioners(WP_REST_Request $request) {

        $practitioners = new APM_Practitioners();

        $id = $request['id'] ? $request['id'] : '';
        $lumeon_id = $request['lumeon_id'] ? $request['lumeon_id'] : '';

        // WP_Query arguments
        $meta_query = array();

        if($lumeon_id) {
            $meta_query[] = array(
                'key' => 'team_lumeon_id',
    			'value' => $lumeon_id,
    			'compare' => '='
            );
        }

        $args = array(
            'p'                => $id,
            'author'           => $author,
            'post_status'      => $status,
            'posts_per_page'   => $posts_per_page,
            'order'            => $order,
            'orderby'          => $orderby,
            'post__in'         => $include,
            'post__not_in'     => array($exclude),
            'meta_query'       => $meta_query
        );

        $team = $practitioners->get_team($args);

        $posts = array();

        if(!empty($team)) {
            foreach($team as $practitioner_id) {

                $thePractitioner = new stdClass();

                $practitioner = new APM_Practitioner($practitioner_id);

                $thePractitioner->ID = $practitioner_id;
                $thePractitioner->lumeonID = $practitioner->lumeon_id();
                $thePractitioner->name = $practitioner->title();
                $thePractitioner->jobTitle = $practitioner->job_title();
                $thePractitioner->permalink = $practitioner->url();
                $thePractitioner->picture = $practitioner->profile_picture(800, 800, true);
                $thePractitioner->shortBio = $practitioner->short_bio();
                $thePractitioner->workingHours = $practitioner->working_hours();
                $thePractitioner->products = $practitioner->products('ids');
                $thePractitioner->gender = 'male';

                array_push($posts, $thePractitioner);

            }
        }
        
        return $posts;

    }

    /**
     * Check for errors.
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
    */
    public function check_for_errors( $request ) {

        if(!is_user_logged_in()) { return false; }

        return $request;

    }

}