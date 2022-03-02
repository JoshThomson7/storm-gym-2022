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

class APM_REST_Appointments_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'appointments';

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

                'patient_id' => array(
                    'description'       => 'Sets the Lumeon Patient ID',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),

                'location_ids' => array(
                    'description'       => 'One of more Location Lumeon IDs',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),

                'date_from' => array(
                    'description'       => 'Sets the Date to start searching appointments from, defaults to today (optional)',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),

                'date_to' => array(
                    'description'       => 'End date to stop appointment search',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),

                'appt_type' => array(
                    'description'       => 'The appointment type ID in Lumeon (WC product Lumeon ID)',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),

                'search_type' => array(
                    'description'       => 'Sets the Type of Search to be performed, defaults to all (optional)',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),

                'weeks' => array(
                    'description'       => 'Number of weeks in the future to search for, defaults to 1 (optional)',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),

                'practitioner' => array(
                    'description'       => 'The Practitioner the appointments should be filtered for (optional)',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),

                'gender' => array(
                    'description'       => 'Preferred Gender of the Practitioner, if any - (optional)',
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

        $appts = array();

        // Params
        $session_id     = $request->get_param('session_id');
        $appt_type      = $request->get_param('appt_type');
        $patient_id     = $request->get_param('patient_id');
        $location_ids   = $request->get_param('location_ids');
        $date_from      = $request->get_param('date_from');
        $date_to        = $request->get_param('date_to');
        $slot_length    = $request->get_param('slot_length') ? $request->get_param('slot_length') : 30;
        $search_type    = $request['search_type'] ? $request['search_type'] : 'all';
        $weeks          = $request['weeks'] ? $request['weeks'] : '1';
        $practitioner   = $request['practitioner'] ? $request['practitioner'] : '';
        $gender         = $request['gender'] ? $request['gender'] : '';
        $page           = $request['page'] ? $request['page'] : '1';

        global $wpdb;

        // Create a new instance of the Lumeon API
        $lumeon = new LumeonAPI;
        $lumeon->wpdb = $wpdb;

        // Set up the Session
        $lumeon->SetSession($session_id);

        //$lumeon->LogEvent("APM_REST_Appointments_Controller::getData", $location_ids, "api");

        // Run an Appointment search
        $data = $lumeon->appointmentSlotSearch($appt_type, $patient_id, $location_ids, $date_from, $date_to, $search_type, $weeks, $practitioner, $gender, $page, $slot_length);

        /*
        if (!isset($data))
        {
            $data = array(
                'status'         => 'Went TU :-(',
            );
        }*/
 
        return $data;
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