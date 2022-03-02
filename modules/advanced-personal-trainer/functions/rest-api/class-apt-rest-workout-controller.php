<?php
/**
 * Extends the WordPress REST API.
 *
 * Adds custom endpoint to the WordPress REST API.
 *
 * @package    APT
 * @author     FL1 Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class APT_REST_Workout_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'workouts';

	/**
	 * Register REST route.
	 */
	public function register_rest_route() {

        register_rest_route( APT_REST_API_NAMESPACE, '/'.$this->route.'/', array(
            'methods' => WP_REST_Server::READABLE, // GET
            'callback' => array( $this, 'getData' ),
            //'permission_callback' => array($this, 'check_for_errors'),
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
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'exclude' =>  array(
                    'description'       => 'Exclude a post by ID.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'cat' =>  array(
                    'description'       => 'Filter services by service category.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));

    }

    /**
     * Returns team members
	 *
	 * @since 2.0
	 */
	public function getData(WP_REST_Request $request) {

        $workouts = new APT_Workouts();

        $id =  $request->get_param('id');
        $author = $request->get_param('author');
        $status = $request->get_param('status');
        $posts_per_page = $request->get_param('per_page');
        $order = $request->get_param('order');
        $orderby = $request->get_param('orderby');
        $exclude = $request->get_param('exclude');
        $include = $request->get_param('include');
        $cat = $request->get_param('cat');

        // WP_Query arguments
        $tax_query = array();
        $args = array(
            'p'                => $id,
            'author'           => $author,
            'post_status'      => $status,
            'posts_per_page'   => $posts_per_page ? $posts_per_page : -1,
            'order'            => $order ? $order : 'ASC',
            'orderby'          => $orderby ? $orderby : 'name',
            'post__in'         => $include ? APT_Helpers::param_to_array($include) : '',
            'post__not_in'     => $exclude ? APT_Helpers::param_to_array($exclude) : ''
        );

        if(!empty($include)) {
            $args['orderby'] = 'post__in';
        }

        // Filter by service category
        if(!empty($cat)) {
            $cat = APT_Helpers::param_to_array($cat);

            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'service_cat',
                    'terms' => is_array($cat) ? $cat : array($cat),
                    'field' => 'term_id',
                    'operator' => 'IN'
                )
            );
        }

        $getWorkouts = $workouts->get_workouts($args);

        $posts = array();

        if(!empty($getWorkouts)) {
            foreach($getWorkouts as $workout_id) {

                $workout = new APT_Workout($workout_id);
                array_push($posts, $workout->rest_api_data(true));
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

        //if(!is_user_logged_in()) { return false; }

        return $request;

    }

}