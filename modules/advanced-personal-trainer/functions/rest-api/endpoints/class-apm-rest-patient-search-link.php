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

class APM_REST_PatientSearchLink_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'search-link';

	/**
	 * Register REST route.
	 */
	public function register_rest_route() {

        register_rest_route( APM_REST_API_NAMESPACE, '/'.$this->route.'/', array(
            'methods' => WP_REST_Server::READABLE, // GET
            'callback' => array( $this, 'getData' ),
            'permission_callback' => array($this, 'validate_request'),
            'args' => array(
                'session_id' => array(
                    'description'       => 'Sets the API Session',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
                'email' => array(
                    'description'       => 'Must be email address.',
                    'type'              => 'string',
                    'format'            => 'email'
                ),
                'last_name' => array(
                    'description'       => 'Last name.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'dob' => array(
                    'description'       => 'Date of birth (YYYY-MM-DM).',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'gender' => array(
                    'description'       => 'Password.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        if($param === 'male' || $param === 'female') {
                            return true;
                        }
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'postcode' => array(
                    'description'       => 'Password.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
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

        $response = array();

        // Params
        $session_id     = $request['session_id'] ? $request['session_id'] : '';
        $email          = $request->get_param('email');
        $last_name      = $request->get_param('lastName');
        $dob            = $request->get_param('dob');
        $gender         = $request->get_param('gender');
        $postcode       = $request->get_param('postcode');

        global $wpdb;

        // Create a new instance of the Lumeon API
        $lumeon = new LumeonAPI;
        $lumeon->wpdb = $wpdb;

        // Set up the Session
        $lumeon->SetSession($session_id);

        // Look them up in Lumeon
        $data = $lumeon->patientSearch($postcode, $dob, $last_name, $email, $gender);
        if ($data->total > 0)
        {
            $lumeon_id = $data->entry[0]->resource->identifier[0]->value;
            $response = array(
                'found'                 => $data->total,
                'id'                    => $data->entry[0]->resource->identifier[0]->value,
            );
        }
        else{
            $response = array(
                'found'                 => 0,
                'id'                    => 0,
            );
        }
        return $response;
    }

    /**
     * Validate request.
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
    */
    public function validate_request( $request ) {

        if(!is_user_logged_in()) { return false; }

        return $request;

    }

}