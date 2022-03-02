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

class APM_REST_Products_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'products';

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

        $id =  $request->get_param('id');
        $author = $request->get_param('author');
        $posts_per_page = $request->get_param('per_page');
        $order = $request->get_param('order');
        $orderby = $request->get_param('orderby');
        $exclude = $request->get_param('exclude');
        $include = $request->get_param('include');

        // WP_Query arguments
        $args = array(
            'post_type'        => 'product',
            'p'                => $id ? $id : '',
            'author'           => $author ? $author : '',
            'post_status'      => $status ? $status : 'publish',
            'posts_per_page'   => $posts_per_page ? $posts_per_page : -1,
            'order'            => $order ? $order : 'name',
            'orderby'          => $orderby ? $orderby : 'ASC',
            'post__in'         => $include ? $this->param_to_array($include) : '',
            'post__not_in'     => $exclude ? $this->param_to_array($exclude) : '',
            'fields'           => 'ids'
        );

        if(!empty($include)) {
            $args['orderby'] = 'post__in';
        }

        $getProducts = new WP_Query($args);
        $getProducts = $getProducts->posts;

        $posts = array();

        if(!empty($getProducts)) {
            foreach($getProducts as $product_id) {

                $product = new APM_Product($product_id);
                array_push($posts, $product->rest_api_data(true));

            }
        }
        
        return $posts;

    }

    /**
     * Checks if a string
     * 
     * @param string $param
     * @param string $delimiter
     */
    private function param_to_array($param, $delimiter = '|') {

        $return = $param;

        if(!empty($param)) {

            if(strpos($param, $delimiter) !== false) {
                $return = explode($delimiter, $param);
            }

        }

        return $return;

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