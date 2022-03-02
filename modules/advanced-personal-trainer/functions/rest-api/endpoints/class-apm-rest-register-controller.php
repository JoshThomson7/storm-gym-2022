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

class APM_REST_Register_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'register';

	/**
	 * Register REST route.
     * 
     * @see https://www.shawnhooper.ca/2017/02/15/wp-rest-secrets-found-reading-core-code/
	 */
	public function register_rest_route() {

        register_rest_route( APM_REST_API_NAMESPACE, $this->route, array(
            'methods' => 'POST', // POST
            'callback' => array( $this, 'register' ),
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
                'username' => array(
                    'description'       => 'Must be email address.',
                    'type'              => 'string',
                    'format'            => 'email'
                ),
                // 'password' => array(
                //     'description'       => 'Password.',
                //     'type'              => 'string',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_string( $param );
                //     },
                //     'sanitize_callback' => 'sanitize_text_field',
                // ),
                'title' => array(
                    'description'       => 'Title.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'first_name' => array(
                    'description'       => 'First name.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'last_name' => array(
                    'description'       => 'Last name.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'phone_work' => array(
                    'description'       => 'Work Phone Number.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'phone_mobile' => array(
                    'description'       => 'Mobile Phone Number.',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'address_1' => array(
                    'description'       => 'Address Line 1',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'address_2' => array(
                    'description'       => 'Address Line 2',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'address_city' => array(
                    'description'       => 'Address City/Post Town',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'address_county' => array(
                    'description'       => 'Address County',
                    'type'              => 'string',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_string( $param );
                    },
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'address_country' => array(
                    'description'       => 'Address Country',
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
                'lumeon_id' => array(
                    'description'       => 'Sets the Lumeon Patient ID, if known',
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
     * Returns team members
	 *
	 * @since 2.0
	 */
	public function register($request = null) {

        $response = array();

        // Get params
        $session_id         = $request['session_id'] ? $request['session_id'] : '';
        $lumeon_id          = $request['lumeon_id'] ? $request['lumeon_id'] : 0;
        $email              = $request->get_param('email');
        $username           = $request->get_param('username');
        $password           = wp_generate_password();
        $title              = $request->get_param('title');        
        $first_name         = $request->get_param('firstName');
        $last_name          = $request->get_param('lastName');

        $phone_work         = $request->get_param('phone_work');
        $phone_mobile       = $request->get_param('phone_mobile');

        $address_1          = $request->get_param('address_1');
        $address_2          = $request->get_param('address_2');
        $address_city       = $request->get_param('address_city');
        $address_county     = $request->get_param('address_county');
        $address_country    = $request->get_param('address_country');
        $address_country    = $address_country && $address_country == 'UK' ? 'GB' : $address_country;
        $postcode           = $request->get_param('postcode');

        $dob                = $request->get_param('dob');
        $gender             = $request->get_param('gender');

        // Start checks
        if (empty($username)) {
            $response['code'] = 400;
            $response['message'] = __('Please enter an email address.', 'wp-rest-user');
            $response['data'] = array(
                'tag' => 'no_email'
            );
        }

        // Does the user already exist?
        $user_id = username_exists($username);

        if (!$user_id && email_exists($email) == false) {

            // See if we can link them to a Lumeon Patient account
            if ($lumeon_id == 0)
            {
                global $wpdb;

                // Create a new instance of the Lumeon API
                $lumeon = new LumeonAPI;
                $lumeon->wpdb = $wpdb;
        
                // Set up the Session
                $lumeon->SetSession($session_id);

                // Look them up in Lumeon
                $patientSearch = $lumeon->patientSearch($postcode, $dob, $last_name, $email, $gender);
                if (!empty($patientSearch))
                {
                    if ($patientSearch->total > 0)
                    {
                        $lumeon_id = $patientSearch->entry[0]->resource->identifier[0]->value;
                    }
                    else
                    {
                        // They don't exist on Lumeon, so we'll create a Patient record
                        $fields = array(
                            'patient_title'         => $title,
                            'patient_firstname'     => $first_name,
                            'patient_surname'       => $last_name,
                            'patient_phone_work'    => $phone_work,
                            'patient_phone_mobile'  => $phone_mobile,
                            'patient_email'         => $email,
                            'patient_gender'        => $gender,
                            'patient_dob'           => $dob,
                            'patient_addr_1'        => $address_1,
                            'patient_addr_2'        => $address_2,
                            'patient_addr_city'     => $address_city,
                            'patient_addr_county'   => $address_county,
                            'patient_addr_postcode' => $postcode,
                            'patient_addr_country'  => $address_country,
                        );
                    
                        $newPatient = $lumeon->createPatient($fields);
                        if (!empty($newPatient)) {
                            $lumeon_id = $newPatient;
                        }
                    }
                }
            }            

            // Insert new user record in DB
            $user_id = wp_insert_user(array(
                'user_login' => $username,
                'user_pass' => $password,
                'display_name' => $first_name.' '.$last_name,
                'user_nicename' => sanitize_title($first_name.' '.$last_name)
            ));

            // All good?
            if (!is_wp_error($user_id)) {
                
                $user = get_user_by('id', $user_id);
                $patient = new APM_Patient($user_id);
                
                // WooCommerce-specific
                if (class_exists('WC_Customer')) {

                    $user->set_role('customer');

                    $patient->set_password($password);
                    $patient->set_email($username);
                    $patient->set_lumeon_id($lumeon_id);

                    $patient->set_first_name($first_name);
                    $patient->set_billing_first_name($first_name);
                    $patient->set_last_name($last_name);
                    $patient->set_billing_last_name($last_name);
                    
                    $patient->set_dob($dob);
                    $patient->set_gender($gender);
                    
                    $patient->set_billing_address_1($address_1);
                    $patient->set_billing_address_2($address_2);
                    $patient->set_billing_city($address_city);
                    $patient->set_billing_state($address_county);
                    $patient->set_billing_postcode($postcode);
                    $patient->set_billing_country($address_country);
                    $patient->set_billing_email($username);
                    $patient->set_billing_phone($phone_mobile);

                    // WC_Customer requires running the save() method to save and retain new data from "set_" methods
                    $patient->save();

                } else {

                    $user->set_role('subscriber');
                }

                /**
                 * Send email to user about their new account
                 * @see class-apm-email.php for "custom_new_user_notification_email" hook
                 */
                wp_new_user_notification($user_id, null, 'user');
                
                $response['code'] = 200;
                $response['message'] = __('Thank you for registering. Please follow the instructions sent to '.$username.' in order to access your new Bodyset account.', 'wp-rest-user');
                $response['data'] = array(
                    'tag' => 'registration_success'
                );

            } else {
                return $user_id;
            }

        } else {

            $response['code'] = 400;
            $response['message'] = __('Oops! That email already exists. Maybe try resetting your password?', 'wp-rest-user');
            $response['data'] = array(
                'tag' => 'email_exists'
            );

        }
        
        return $response;

    }

}