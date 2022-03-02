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

class APM_REST_Session_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'session';

	/**
	 * Register REST route.
	 */
	public function register_rest_route() {

        register_rest_route( APM_REST_API_NAMESPACE, '/'.$this->route.'/', array(
            'methods' => WP_REST_Server::READABLE, // GET
            'callback' => array( $this, 'getData' ),
            'permission_callback' => array($this, 'validate_request'),
            'args' => array(
                'id' => array(
                    'description'       => 'Query one user only.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
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

        $appts = array();

        // Params
        $include = $request['id'] ? array((int)$request['id']) : array();

        // User query
        $user_args = array(
            'role' => 'customer',
            'fields' => array('ID'),
            'include' => $include,
            'number' => 10
        );

        global $wpdb;

        // Create a new instance of the Lumeon API
        $lumeon = new LumeonAPI;
        $lumeon->wpdb = $wpdb;

        // Kick off a new Logging Session
        $session_id = $lumeon->NewSession();
        $session_status = '';

        if (!$lumeon->authenticateUser())
        {
            $session_status = $lumeon->last_error;
            $session_status = 'apparently the login went TU, which is nice!';
        }
        else{
            $session_status = 'OK';
        }
        
        $fields = array(
            'session_id'        => $session_id,
            'session_start'     => date("Y-m-d H:i:s"),  
            'status'            => $session_status,
            'session_lm_auth'   => $lumeon->getSessionLMAuthKey(),
        );
            
        return $fields;
    }

    /**
     * Validate request.
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
    */
    public function validate_request( $request ) {

        //if(!is_user_logged_in()) { return false; }

        return $request;

    }

}