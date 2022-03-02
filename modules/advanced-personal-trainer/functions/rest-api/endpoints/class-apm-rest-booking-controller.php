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

class APM_REST_Booking_Controller extends WP_REST_Controller {

    /**
	 * Declare REST API route.
	 */
    protected $route = 'booking';

	/**
	 * Register REST route.
     * 
     * @see https://www.shawnhooper.ca/2017/02/15/wp-rest-secrets-found-reading-core-code/
	 */
	public function register_rest_route() {

        register_rest_route( APM_REST_API_NAMESPACE, $this->route, array(
            'methods' => 'POST', // POST
            'callback' => array( $this, 'booking' ),
            'args' => array(
                'session_id' => array(
                    'description'       => 'Set the ID of the API Session',
                    'type'              => 'integer',
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
                // 'lumeon_patient_id' => array(
                //     'description'       => 'Lumeon Patient ID.',
                //     'type'              => 'integer',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_numeric( $param );
                //     },
                //     'sanitize_callback' => 'absint',
                // ), 
                // 'lumeon_patient_name' => array(
                //     'description'       => 'Name of the Patient being booked',
                //     'type'              => 'string',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_string( $param );
                //     },
                //     'sanitize_callback' => 'sanitize_text_field',
                // ),
                // 'lumeon_service_id' => array(
                //     'description'       => 'Lumeon Service ID.',
                //     'type'              => 'integer',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_numeric( $param );
                //     },
                //     'sanitize_callback' => 'absint',
                // ),
                // 'lumeon_slot_id' => array(
                //     'description'       => 'Lumeon Booking Slot ID.',
                //     'type'              => 'integer',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_numeric( $param );
                //     },
                //     'sanitize_callback' => 'absint',
                // ),
                // 'lumeon_type_code' => array(
                //     'description'       => 'Lumeon Booking Type Code.',
                //     'type'              => 'string',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_string( $param );
                //     },
                //     'sanitize_callback' => 'sanitize_text_field',
                // ),
                // 'lumeon_service_name' => array(
                //     'description'       => 'Name of the Service being booked',
                //     'type'              => 'string',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_string( $param );
                //     },
                //     'sanitize_callback' => 'sanitize_text_field',
                // ),
                // 'product' => array(
                //     'description'       => 'WP Product ID.',
                //     'type'              => 'integer',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_numeric( $param );
                //     },
                //     'sanitize_callback' => 'absint',
                // ),
                // 'clinic' => array(
                //     'description'       => 'Clinic data.',
                //     'type'              => 'string',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_string( $param );
                //     },
                // ),
                // 'appointments' => array(
                //     'description'       => 'Appointments.',
                //     'type'              => 'string',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_string( $param );
                //     },
                // ),
                // 'questions' => array(
                //     'description'       => 'Questions.',
                //     'type'              => 'string',
                //     'validate_callback' => function( $param, $request, $key ) {
                //         return is_string( $param );
                //     },
                // ),
            )
        ));
	}

    /**
     * Returns team members
	 *
	 * @since 2.0
	 */
	public function booking($request = null) {

        $response = array(
            'code' => 400,
            'message' =>  __('There was an error processing the request.', 'wp-rest-user'),
            'data' => array(
                'endpoint' => $this->route,
                'tag' => 'error_at_start'
            )
        );

        $session_id             = $request->get_param('session_id');
        $lumeonData             = $request->get_param('lumeon');
        $product                = $request->get_param('product');
        $product_id             = isset($product['ID']) && !empty($product['ID']) ? $product['ID'] : null;
        $_product               = new APM_Product($product_id);
        $upsell_block_id        = null;
        $appointments           = $request->get_param('appointments');
        $questions              = $request->get_param('questions');
        $clinic                 = $request->get_param('clinic');
        $quantity               = 1;

        if(empty($session_id)) {
            $response['message'] = __('No Session ID provided.', 'wp-rest-user');
            $response['data']['tag'] = 'no_session_id';
        }

        global $wpdb;

        // Create a new instance of the Lumeon API
        $lumeon = new LumeonAPI;
        $lumeon->wpdb = $wpdb;
        $lumeon->SetSession($session_id);
                
        /**
         * WooCommerce
         * - Add to Cart
         */
        if(class_exists('WC_Customer')) {

            if($appointments > 0) {

                WC()->session->set('appointments', $appointments);

                /**
                 * Product
                 */
                if(!empty($product) && is_array($product)) {
                    
                    WC()->session->set('product', $product);
                    
                    $upsell_block_id = isset($product['upsell_block_id']) && !empty($product['upsell_block_id']) && is_numeric($product['upsell_block_id']) ? $product['upsell_block_id'] : null;

                    if($upsell_block_id) {
                        WC()->session->set('upsell_block_id', $upsell_block_id);
                    }
                }

                /**
                 * Questions
                 */
                if(!empty($questions) && is_array($questions)) {
                    
                    WC()->session->set('questions', $questions);

                    $purpose = isset($questions['purpose']) && !empty($questions['purpose']) ? $questions['purpose'] : '';
                    $bookingType = isset($questions['bookingType']) && !empty($questions['bookingType']) ? $questions['bookingType'] : '';

                    if($bookingType === 'block' && $upsell_block_id) {
                        $this->addBlockToCart();
                    }
                }

                /**
                 * Session
                 */
                if(!empty($clinic) && is_array($clinic)) {
                    
                    WC()->session->set('clinic', $clinic);

                }

                /**
                 * Appointments
                 */
                $lumeonPosted = array();
                $lumeonReservationIDs = array();
                $lumeon_responses = array();
                
                foreach($appointments as $appointment) {

                    $lumeon_data = array(
                        'lumeon_patient_id'         => $lumeonData['patientID'],
                        'lumeon_patient_name'       => $lumeonData['patientName'],
                        'lumeon_service_id'         => $product['lumeon_id'],
                        'lumeon_service_name'       => html_entity_decode($_product->get_name()),
                        'lumeon_slot_id'            => $appointment['lm_slot_id'],
                        'lumeon_type_code'          => '3',
                    );

                    array_push($lumeonPosted, $lumeon_data);

                    // Reserve appointment                    
                    $lumeon_response = $lumeon->appointmentCreate($lumeon_data);

                    if(!empty($lumeon_response) && isset($lumeon_response->text) && strpos($lumeon_response->text, 'Successfully reserved Appointment') !== false) {

                        array_push($lumeon_responses, $lumeon_response);

                        $lumeonReservationID = str_replace('Successfully reserved Appointment ', '', $lumeon_response->text);

                        if($lumeonReservationID) {

                            array_push($lumeonReservationIDs, $lumeonReservationID);

                            // filter validation rules
                            $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);

                            /**
                             * Build our unique cart item meta data
                             */

                            $start = $appointment['start'];
                            $startDate = new DateTime($start, wp_timezone());

                            $end = $appointment['end'];
                            $endDate = new DateTime($end, wp_timezone());

                            $cart_item_data = array(
                                'appointment' => $appointment

                            );
                            
                            $cart_item_data['appointment']['start'] = array(
                                'date' => $startDate->format('D j F'),
                                'time' => $startDate->format('H:i')
                            );

                            $cart_item_data['appointment']['end'] = array(
                                'date' => $endDate->format('D j F'),
                                'time' => $endDate->format('H:i')
                            );

                            $cart_item_data['appointment']['lm_reservation_id'] = $lumeonReservationID;

                            $response['redirect_url'] = null;

                            // Validate
                            if($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, null, null, $cart_item_data)) {

                                // add to cart
                                do_action('woocommerce_ajax_added_to_cart', $product_id);

                                WC()->cart->calculate_totals();
                                WC()->cart->set_session();
                                WC()->cart->maybe_set_cart_cookies();

                            }

                        }

                    }

                }

                /**
                 * Response
                 */
                if(!empty($lumeonReservationIDs)) {

                    // Cart expiry time
                    $minutes = get_field('wc_appointment_expiry', 'option') ? get_field('wc_appointment_expiry', 'option') : 15;
                    $time = new DateTime('now', wp_timezone());
                    $time->modify('+'.$minutes.' minutes');
                    $expiry = $time->getTimestamp();

                    $lumeonSessionData = array(
                        'ID' => $session_id,
                        'posted' => $lumeonPosted,
                        'responses' => $lumeon_responses,    
                        'reservationIDs' => $lumeonReservationIDs,
                        'cartExpiry' => $expiry
                    );

                    WC()->session->set('lumeon_session', $lumeonSessionData);

                    $response = array(
                        'code' => 200,
                        'message' =>  __('Success.', 'wp-rest-user'),
                        'data' => array(
                            'endpoint' => $this->route,
                            'tag' => 'success',
                            'lumeon' => $lumeonSessionData
                        ),
                        'redirect_url' => WC()->cart->get_cart_url()
                    );
                }

            }

        }
        
        return $response;

    }

    private function addBlockToCart() {

        $block_id = 5441;
        $block_validation = apply_filters('woocommerce_add_to_cart_validation', true, $block_id, 1);
        
        if($block_validation && WC()->cart->add_to_cart($block_id, 1)) {
            do_action('woocommerce_ajax_added_to_cart', $block_id);
            WC()->cart->calculate_totals();
            WC()->cart->set_session();
            WC()->cart->maybe_set_cart_cookies();
        }

    }

}   