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

class APM_REST_Patients_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'patients';

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

        register_rest_route(APM_REST_API_NAMESPACE, '/'.$this->route.'/', array(
            'methods' => 'POST',
            'callback' => array( $this, 'postData' ),
            'permission_callback' => array($this, 'validate_request'),
            'args' => array(
                'id' => array(
                    'description'       => 'Usert to be updated.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
                'lumeon_id' => array(
                    'description'       => 'Lumeon ID.',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
            )
        ));

    }

    /**
     * GET
     * Returns patients
	 *
	 * @since 2.0
	 */
	public function getData(WP_REST_Request $request) {

        $users = array();

        // Params
        $include = $request['id'] ? array((int)$request['id']) : array();

        // User query
        $user_args = array(
            'role__in' => array('customer', 'administrator'),
            'fields' => array('ID'),
            'include' => $include,
            'number' => 10
        );
        
        $user_query = new WP_User_Query($user_args);

        if($user_query->get_results()) {

            foreach($user_query->get_results() as $patientData) {

                $thePatient = new stdClass();

                $patient_id = $patientData->ID;
                $patient = new APM_Patient($patient_id);

                $thePatient->ID = $patient_id;
                $thePatient->username = $patient->get_username();
                $thePatient->email = $patient->get_email();
                $thePatient->fullName = $patient->get_full_name();
                $thePatient->firstName = $patient->get_first_name();
                $thePatient->lastName = $patient->get_last_name();
                $thePatient->dateRegistered = $patient->get_date_created();
                $thePatient->billing = $patient->get_billing();
                $thePatient->shipping = $patient->get_shipping();
                $thePatient->dob = $patient->get_dob();
                $thePatient->gender = $patient->get_gender();
                $thePatient->credits = $patient->get_credits();

                $thePatient->lumeon = new stdClass();
                $thePatient->lumeon->patientID = $patient->get_lumeon_id();
                $thePatient->lumeon->firstName = 'FN';
                $thePatient->lumeon->lastName = 'FN';

                array_push($users, $thePatient);
            }

        }
        
        return $users;

    }

    /**
     * POST
     * Updates patients
	 *
	 * @since 2.0
	 */
	public function postData(WP_REST_Request $request) {

        $response = array(
            'code' => 400,
            'message' => __('There was an error processing the request.', 'wp-rest-user'),
            'data' => array(
                'tag' => 'error_at_beginning'
            )
        );

        // ID
        $id = $request->get_param('id');

        // Bail early if no ID
        if(!$id) {
            $response['message'] = __('User ID is required.', 'wp-rest-user');
            $response['data'] = array(
                'tag' => 'no_user_id'
            );    
        }

        // Params
        $lumeon_id = $request->get_param('lumeon_id');

        // Instantiate patient
        $patient = new APM_Patient($id);

        if($lumeon_id) {
            $patient->set_lumeon_id($lumeon_id);

            $response['code'] = 200;
            $response['message'] = __('Lumeon ID updated successfully.', 'wp-rest-user');
            $response['data'] = array(
                'tag' => 'lumeon_id_updated'
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