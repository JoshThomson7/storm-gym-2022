<?php
/**
 * APM WooCommerce Checkout
 *
 * Class in charge of WooCommerce's Checkout
 * action and hook overrides
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_WC_Checkout {

    public function init() {

        remove_action('woocommerce_order_details_after_order_table', 'woocommerce_order_again_button');
        add_action('init', array($this, 'remove_cart_notice_on_checkout'));

        // Checkout fields
        add_filter('woocommerce_customer_meta_fields', array($this, 'custom_customer_user_fields'));
        add_filter('woocommerce_checkout_fields', array($this, 'checkout_fields_order'));
        add_filter('woocommerce_checkout_fields', array($this, 'shipping_fields_order'));
        add_filter('woocommerce_checkout_fields', array($this, 'custom_comments_field'));

        add_filter('woocommerce_billing_fields', array($this, 'custom_billing_fields'));
        add_action('woocommerce_after_checkout_billing_form', array($this, 'after_billing_form_fields'));

        // Insurance stuff
        add_action( 'woocommerce_checkout_update_order_review', array($this, 'update_order_review'), 10, 1 );
        add_action( 'woocommerce_cart_calculate_fees', array($this, 'calculate_fees'), 20);

        // ORDER
        // Order meta
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'on_create_order_line_item'), 10, 4);
        add_action('woocommerce_checkout_update_order_meta', array($this, 'update_order_meta'));

        add_action('woocommerce_order_item_meta_start', array($this, 'order_item_display_appointment_details'), 10, 3); // Order (front-end)
        add_action('woocommerce_after_order_itemmeta', array($this, 'order_item_display_appointment_details'), 10, 3); // Order (back-end)    

        // Order overrides
        add_filter('woocommerce_order_formatted_billing_address' , array($this, 'add_title_field_to_order'), 10, 2);
        add_filter('woocommerce_formatted_address_replacements', array($this, 'custom_address_replacement_fields') , 10, 2);
        add_filter('woocommerce_localisation_address_formats', array($this, 'custom_address_formats'));
        add_filter('woocommerce_thankyou_order_received_text', array($this, 'thankyou_order_received_text'), 10, 2);

        // Validation
        add_action('woocommerce_checkout_process', array($this, 'apm_custom_checkout_validation'));

        // On Order complete
        add_action('woocommerce_thankyou', array($this, 'on_order_complete'));
        //add_action('woocommerce_payment_complete', array($this, 'on_order_complete'));

        // Card logos
        add_action('woocommerce_review_order_after_submit', array($this, 'checkout_card_logos'));

        //add_filter('woocommerce_email_recipient_new_order', 'extra_admin_email_recipients', 10, 2);

    }

    /**
     * Removes cart notices from the checkout page
     *
     * @package Advanced Physio Module
     * @version 1.0
    */
    public function remove_cart_notice_on_checkout() {
        if ( function_exists( 'wc_cart_notices' ) ) {
            remove_action( 'woocommerce_before_checkout_form', array( wc_cart_notices(), 'add_cart_notice' ) );
        }
    }

    /**
     * Add title field to user admin screen
     */
    public function custom_customer_user_fields( $fields ) {

        $fields['billing']['fields']['billing_title'] = array(
            'type'          => 'select',
            'label'         => __( 'Title', 'woocommerce' ),
            'class'         => 'select2',
            'description'   => '',
            'options'       => array(
                ''		    => 'Please select',
                'Mrs'       => 'Mrs',
                'Miss'      => 'Miss',
                'Ms'        => 'Ms',
                'Mr'        => 'Mr',
                'Dr'        => 'Dr'
            )
        );

        $fields['billing']['fields']['billing_title']['priority'] = 1;

        return $fields;
    }

    /**
     * Change order of checkout fields
     */
    public function checkout_fields_order($fields) {

        unset($fields['billing']['billing_company']);
        unset($fields['billing']['billing_address_2']);

        $fields['account']['account_username']['label'] = 'Account Email';

        $order = array(
            "billing_title",
            "billing_first_name",
            "billing_last_name",
            "billing_email",
            "billing_phone",
            "billing_address_1",
            "billing_city",
            "billing_postcode",
            "billing_country"
        );

        foreach($order as $field) {
            $ordered_fields[$field] = $fields["billing"][$field];
        }

        $fields["billing"] = $ordered_fields;

        $fields['billing']['billing_title']['priority'] = 10;
        $fields['billing']['billing_first_name']['priority'] = 10;
        $fields['billing']['billing_last_name']['priority'] = 20;
        $fields['billing']['billing_email']['priority'] = 30;
        $fields['billing']['billing_phone']['priority'] = 40;
        $fields['billing']['billing_address_1']['priority'] = 50;
        $fields['billing']['billing_city']['priority'] = 70;
        $fields['billing']['billing_postcode']['priority'] = 70;
        $fields['billing']['billing_country']['priority'] = 90;

        return $fields;

    }

    /**
     * Change order of shipping fields
    */
    public function shipping_fields_order($fields) {

        unset($fields['shipping']['shipping_company']);
        unset($fields['shipping']['shipping_address_2']);

        $order = array(
            "shipping_first_name",
            "shipping_last_name",
            "shipping_address_1",
            "shipping_city",
            "shipping_postcode",
            "shipping_country"
        );

        foreach($order as $field) {
            $ordered_fields[$field] = $fields["shipping"][$field];
        }

        $fields["shipping"] = $ordered_fields;

        $fields['shipping']['shipping_first_name']['priority'] = 10;
        $fields['shipping']['shipping_last_name']['priority'] = 20;
        $fields['shipping']['shipping_address_1']['priority'] = 50;
        $fields['shipping']['shipping_city']['priority'] = 70;
        $fields['shipping']['shipping_postcode']['priority'] = 70;
        $fields['shipping']['shipping_country']['priority'] = 90;

        return $fields;
    }

    /**
    * Override Woocommerce checkout fields
    *
    * @package Advanced Physio Module
    * @version 1.0
    */
    public function custom_comments_field( $fields ) {

        $cart_booking_data = APM_Helpers::get_cart_booking_data();

        if($cart_booking_data->has_appointment) {
            $fields['order']['order_comments']['label'] = '<small>Our therapists find it useful to know a little bit about your enjury in advance so they can spend more time treating you. If you would like to share any relevant information, please use the box below.</small>';
            $fields['order']['order_comments']['placeholder'] = 'Please enter any relevant information about youjr injury.';
        } else {
            $_SESSION['order_comments'] = '';
        }

        return $fields;
    }

    /**
     * Override billoing fields
     * 
     * @param array $fields
     */
    public function custom_billing_fields($fields) {

        $fields['billing_title'] = array(
            'type'          => 'select',
            'class'         => array('billing_title form-row-wide'),
            'label'         => __('Title'),
            'options'       => array(
                ''		=> 'Please select',
                'Mrs'       => 'Mrs',
                'Miss'      => 'Miss',
                'Ms'        => 'Ms',
                'Mr'        => 'Mr',
                'Dr'        => 'Mr'
            )
        );

        return $fields;
    }

    public function after_billing_form_fields( $checkout ) {

        $cart_booking_data = WC()->session->get('cart_booking_data');

        echo '<div class="wc__checkout__fields has-deps" data-deps=\'{"js":["apm-checkout"]}\'>';
            
            if($cart_booking_data->has_appointment && $cart_booking_data->can_pay_with_insurance) {

                echo '<div class="wc__checkout__fields--section-heading"><h3>Private health insurance</h3><p>We accept all major insurers and can bill some directly on your behalf. If you would like this service, please contact our client care team on 033 0333 0435 for more information. <strong>Please note you will be charged a 30p deposit at checkout in order to successfully process your online booking.</strong></p></div>';

                echo '<div class="wc__checkout__field__row">';

                    woocommerce_form_field( 'apm_insurance', array(
                        'type'          => 'select',
                        'class'         => array('apm__insurance form-row-wide', 'update_totals_on_change'),
                        'label'         => __('Do you have private health insurance?'),
                        'required'      => true,
                        'options'       => array(
                            ''		=> 'Please select',
                            'Yes' => 'Yes',
                            'No'  => 'No',
                        )
                    ), WC()->session->get('has_insurance') ? 'Yes' : 'No');

                echo '</div>';


                echo '<div class="wc__checkout__field__row wc__apm__checkout--if-insurance no">';

                    woocommerce_form_field( 'apm_insurer_name', array(
                        'type'          => 'text',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Name of insurer'),
                        'required'      => true,
                    ), $checkout->get_value( 'apm_insurer_name' ));

                    woocommerce_form_field( 'apm_policy_holder', array(
                        'type'          => 'text',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Policy Holder Name'),
                        'required'      => true,
                    ), $checkout->get_value( 'apm_policy_holder' ));

                echo '</div>';

                echo '<div class="wc__checkout__field__row wc__apm__checkout--if-insurance no">';

                    woocommerce_form_field( 'apm_policy_number', array(
                        'type'          => 'text',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Policy Number'),
                        'required'      => true,
                    ), $checkout->get_value( 'apm_policy_number' ));

                    woocommerce_form_field( 'apm_policy_expiration', array(
                        'type'          => 'text',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Policy Expiration date'),
                        'required'      => true,
                    ), $checkout->get_value( 'apm_policy_expiration' ));

                echo '</div>';

                echo '<div class="wc__checkout__field__row wc__apm__checkout--if-insurance no">';

                    woocommerce_form_field( 'apm_auth_sessions_num', array(
                        'type'          => 'text',
                        'class'         => array('form-row-wide'),
                        'label'         => __('No. of authorised sessions'),
                        'required'      => true,
                    ), $checkout->get_value( 'apm_auth_sessions_num' ));

                    woocommerce_form_field( 'apm_auth_code', array(
                        'type'          => 'text',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Authorisation Code'),
                        'required'      => true,
                    ), $checkout->get_value( 'apm_auth_code' ));

                echo '</div>';

                echo '<div class="wc__checkout__field__row wc__apm__checkout--if-insurance no">';

                    woocommerce_form_field( 'apm_policy_excess', array(
                        'type'          => 'text',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Policy Excess'),
                        'required'      => true,
                    ), $checkout->get_value( 'apm_policy_excess' ));

                echo '</div>';

            }

            // GDPR
            echo '<div class="wc__checkout__fields--section-heading"><h3>Stay up to date</h3><p>We offer our existing customers company news, tips from our expert physiotherapists, discounts and promotional offers once or twice a month. Would you like to be the first one to hear about them? If so, which way suits you best?</p></div>';

            $user_gdpr = get_user_meta($current_user_id, 'apm_gdpr', true );
            $user_gdpr = explode(', ', $user_gdpr);

            //print_r($user_gdpr);

            echo '<div class="wc__checkout__field__row">

                    <p class="form-row apm__gdpr form-row-wide" id="apm_gdpr_text_field">
                        <input id="gdpr_text" type="checkbox" class="input-checkbox " name="apm_gdpr[]" value="Text"'.(in_array('Text', $user_gdpr) ? ' checked' : '').'> 
                        <label for="gdpr_text" class="checkbox">Text</label>
                    </p>

                    <p class="form-row apm__gdpr form-row-wide">
                        <input id="gdpr_email" type="checkbox" class="input-checkbox " name="apm_gdpr[]" value="Email"'.(in_array('Email', $user_gdpr) ? ' checked' : '').'>
                        <label for="gdpr_email" class="checkbox">Email</label>
                    </p>

                    <p class="form-row apm__gdpr form-row-wide">
                        <input id="gdpr_tel" type="checkbox" class="input-checkbox " name="apm_gdpr[]" value="Telephone"'.(in_array('Telephone', $user_gdpr) ? ' checked' : '').'>
                        <label for="gdpr_tel" class="checkbox">Telephone</label>
                    </p>

                    <p class="form-row apm__gdpr form-row-wide">
                        <input id="gdpr_postal" type="checkbox" class="input-checkbox " name="apm_gdpr[]" value="Postal"'.(in_array('Postal', $user_gdpr) ? ' checked' : '').'> 
                        <label for="gdpr_postal" class="checkbox">Postal</label>
                    </p>

                    <p class="form-row apm__gdpr form-row-wide">
                        <input id="gdpr_opt_out" type="checkbox" class="input-checkbox " name="apm_gdpr[]" value="Not interested"'.(in_array('Not interested', $user_gdpr) ? ' checked' : '').'> 
                        <label for="gdpr_opt_out" class="checkbox">Not interested</label>
                    </p>
            ';

            echo '</div>';

        echo '</div><!-- apm__checkout__fields -->';

    }

     /**
     * Listens for changes on insurance dropdown
     * and sets has_insurance session variable accordingly
     * 
     * @param array $posted_data
     */
    public function update_order_review( $posted_data ) {

        if ( ! is_array( $posted_data ) ) {
            parse_str( $posted_data, $posted );
        } else {
            $posted = $posted_data;
        }

        WC()->session->set('has_insurance', false);

        if($posted['apm_insurance'] == 'Yes') {
            WC()->session->set('has_insurance', true);
        }

    }

    /**
     * Checks if insurance session variable is true
     * and applies cart fee
     * 
     * @param array $cart
     */
    public function calculate_fees( $cart ) {
    
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
            
        $has_insurance = WC()->session->get('has_insurance');
        $cart_booking_data = WC()->session->get('cart_booking_data');
            
        if ( $has_insurance && $cart_booking_data->can_pay_with_insurance) {
            $cart->add_fee( 'Insurance Deposit Fee (Refundable)', 0.30);
        }
    
    }

    /**
     * Add order line items.
     *
     * @param WC_Order_Item_Product $item
     * @param string                $cart_item_key
     * @param array                 $values
     * @param WC_Order              $order
    */
    public function on_create_order_line_item($item, $cart_item_key, $values, $order) {

        // Title
        if(!empty( $values['billing_title'])) {
            $item->update_meta_data('billing_title', $values['billing_title']);
        }
        
        // Appointment
        if(!empty($values['appointment'])) {
            $item->update_meta_data('appointment', $values['appointment']);
        }

        // Product
        $product = WC()->session->get('product');
        if(!empty($product)) {
            $item->update_meta_data('product', $product);
        }

        // Location
        $location = WC()->session->get('clinic');
        if(!empty($location)) {
            $item->update_meta_data('location', $location);
        }

        // Questions
        $questions = WC()->session->get('questions');
        if(!empty($questions)) {
            $item->update_meta_data('questions', $questions);
        }

        // Lumeon session
        $lumeon_session = WC()->session->get('lumeon_session');
        if(!empty($lumeon_session)) {
            $item->update_meta_data('lumeon_session', $lumeon_session);
        }

        // Car booking data
        $cart_booking_data = WC()->session->get('cart_booking_data');
        if(!empty($cart_booking_data)) {
            $item->update_meta_data('cart_booking_data', $cart_booking_data);
        }

    }

    /**
     * Update order with custom meta
     * 
     * @param int $order_id
     */
    public function update_order_meta( $order_id ) {

        $current_user = wp_get_current_user();

        if ( ! empty( $_POST['apm_gdpr'] ) ) {
            $gdpr = implode(', ', $_POST['apm_gdpr']);
            update_post_meta( $order_id, '_apm_gdpr', sanitize_text_field( $gdpr ) );
            update_user_meta( $current_user->ID, 'apm_gdpr', sanitize_text_field( $gdpr ) );
        }

        // Insurance
        if(!empty( $_POST['apm_insurance'])) {

            $insurance = array(
                'insurer' => !empty($_POST['apm_insurance']) ? $_POST['apm_insurance'] : '',
                'policy_holder' => !empty($_POST['apm_policy_holder']) ? $_POST['apm_policy_holder'] : '',
                'policy_number' => !empty($_POST['apm_policy_number']) ? $_POST['apm_policy_number'] : '',
                'policy_expiration' => !empty($_POST['apm_policy_expiration']) ? $_POST['apm_policy_expiration'] : '',
                'policy_excess' => !empty($_POST['apm_policy_excess']) ? $_POST['apm_policy_excess'] : '',
                'auth_sessions_num' => !empty($_POST['apm_auth_sessions_num']) ? $_POST['apm_auth_sessions_num'] : '',
                'auth_code' => !empty($_POST['apm_auth_code']) ? $_POST['apm_auth_code'] : ''
            );

            update_post_meta($order_id, 'insurance', $insurance);
        }

    }

    /**
     * Add title field to order
     * Fuck you WooCommerce, why don't you just include a bloody Title field by default?!?!?!
     */
    public function add_title_field_to_order( $fields, $order ) {
        $user_id = get_post_meta($order->id, '_customer_user', true);
        $name = get_user_meta( $user_id, 'billing_title', true ).' '.$fields['first_name'].' '.$fields['last_name'];
        $fields['name'] = $name;

        return $fields;
    }

    /**
     * Woo replacements
     * 
     * @param array $replacements
     * @param array $address
     */
    public function custom_address_replacement_fields( $replacements, $address ) {
        $replacements['{name}'] = isset($address['name']) ? $address['name'] : ' ';;
        return $replacements;
    }

    /**
     * Woo formats
     * 
     * @param array $formats
     */
    public function custom_address_formats( $formats ) {
        // Rearrange these fields how you need, each country has an entry in the array like this:
        $formats['PL'] = "{name}\n{company}\n{address_1}\n{address_2}\n{address_3}\n{city}, {state} {postcode}\n{country}";

        return $formats;
    }

    /**
     * thankyou_order_received_text()
     *
     * @package Advanced Physio Module
     * @version 1.0
    */
    public function thankyou_order_received_text( $thankyoutext, $order ) {
        if(APM_Helpers::order_has_product_type($order->id, 'appointment')) {
            $current_user_id = get_current_user_id();
            $patient = new APM_Patient($current_user_id);
            $added_text = '<h1>Thank you, '.$patient->get_first_name().'.</h1>';
            $added_text .= get_field('wc_checkout_thank_you_text', 'option');
            $added_text .= '<p>If we can be of any further help in the meantime, please get in touch by calling <strong>033 0333 0435</strong> or emailing '.hide_email("clientcare@bodyset.co.uk").'</p>';
            // $added_text .= '<h2>Refer a friend</h2>';
            // $added_text .= '<p>Below is your own unique <strong>Refer a friend</strong> link. Use it to send it to friends and you will both get 20% off your next booking.</p>';
            // $added_text .= do_shortcode('[WOO_GENS_RAF_ADVANCE guest_text="Create an account to get discounts when referring to a friend."]');

        } else {
            // $added_text = '<div class="wc__refer__friend"><h2>Refer a friend</h2>';
            // $added_text .= '<p>Below is your own unique <strong>Refer a friend</strong> link. Use it to send it to friends and you will both get 20% off your next booking.</p>';
            // $added_text .= do_shortcode('[WOO_GENS_RAF_ADVANCE guest_text="Create an account to get discounts when referring to a friend."]</div>');
        }

        return $added_text;

    }    

    /**
     * Validate checkout fields
     */
    public function apm_custom_checkout_validation() {

        global $woocommerce;
        $cart_booking_data = WC()->session->get('cart_booking_data');

        if( ($cart_booking_data->has_appointment && $cart_booking_data->can_pay_with_insurance) || APM_Helpers::order_has_product_type($order->id, 'appointment')) {

            if ( !$_POST['billing_title'])
                wc_add_notice( __( 'Please select a title.' ), 'error' );

            if ( !$_POST['apm_insurance'] ) {
                wc_add_notice( __( 'Please select whether you have private health insurance.' ), 'error' );   
            } else {

                if($_POST['apm_insurance'] === 'Yes') {
                    if ( !$_POST['apm_insurer_name'] )
                        wc_add_notice( __( 'Please select an insurer.' ), 'error' );

                    if ( !$_POST['apm_policy_holder'] )
                        wc_add_notice( __( 'Please enter a name.' ), 'error' );

                    if ( !$_POST['apm_policy_number'] )
                        wc_add_notice( __( 'Please enter a policy number.' ), 'error' );

                    if ( !$_POST['apm_policy_expiration'] )
                        wc_add_notice( __( 'Please enter a date.' ), 'error' );

                    if ( !$_POST['apm_auth_sessions_num'] )
                        wc_add_notice( __( 'Please enter number of sessions.' ), 'error' );

                    if ( !$_POST['apm_auth_code'] )
                        wc_add_notice( __( 'Please enter a code.' ), 'error' );

                    if ( !$_POST['apm_policy_excess'] )
                        wc_add_notice( __( 'Please enter excess.' ), 'error' );
                }

            }
        }

    }

    /**
     * Display field values on the order edit page and email
     */
    public function order_item_display_appointment_details($item_id, $item, $order) {

        $appt = $item->get_meta('appointment');

        if(is_array($appt) && !empty($appt)) {

            $questions = $item->get_meta('questions');
            $location = $item->get_meta('location');
            $product = $item->get_meta('product');

            ?>
                <div style="font-size: 14px; margin: 10px 0; color: #383c40">
                    <ul>
                        <li style="display: flex; align-items: center; padding: 3px 0;">
                            <strong style="font-weight: 700; min-width: 120px;">Date</strong> <span style="font-weight: 500;"><?php echo $appt['start']['date']; ?></span>
                        </li>
                        <li style="display: flex; align-items: center; padding: 3px 0;">
                            <strong style="font-weight: 700; min-width: 120px;">Time</strong> <span style="font-weight: 500;"><?php echo $appt['start']['time'].' - '.$appt['end']['time']; ?></span>
                        </li>
                        <?php
                            if(isset($appt['lm_practitioner_id']) && !empty($appt['lm_practitioner_id']) && is_numeric($appt['lm_practitioner_id'])):
                                $practitioner = new APM_Practitioner(null, $appt['lm_practitioner_id']);
                        ?>
                            <li style="display: flex; align-items: center; padding: 3px 0;">
                                <strong style="font-weight: 700; min-width: 120px;">Practitioner</strong> <span style="font-weight: 500;"><?php echo $practitioner->title(); ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if(isset($location['name']) && !empty($location['name'])): ?>
                            <li style="display: flex; align-items: center; padding: 3px 0;">
                                <strong style="font-weight: 700; min-width: 120px;">Location</strong> <span style="font-weight: 500;"><?php echo $location['name']; ?></span>
                            </li>
                        <?php endif; ?>

                        <li style="display: flex; align-items: center; padding: 3px 0;">
                            <strong style="font-weight: 700; min-width: 120px;">Service</strong> <span style="font-weight: 500;"><?php echo get_the_title($product['ID']); ?></span>
                        </li>
                    </ul>
                </div>
            <?php

        }

    }

    /**
     * Display field values on the order edit page and email
     */
    public function display_admin_order_meta($order) {

        if(APM_Helpers::order_has_product_type($order->id, 'appointment')) {

            echo '<div class="apm__patient__details">';
                echo '<h3>Patient Details</h3>';
                echo '<p><strong>'.__('Patient type').'</strong><br>' . get_post_meta( $order->id, '_apm_patient_type', true ) . '</p>';
                echo '<p><strong>'.__('Date of birth').'</strong><br>' . get_post_meta( $order->id, '_apm_dob', true ) . '</p>';
                echo '<p><strong>'.__('Gender').'</strong><br>' . get_post_meta( $order->id, '_apm_gender', true ) . '</p>';
                echo '<p><strong>'.__('New injury?').'</strong><br>' . get_post_meta( $order->id, '_apm_injury_type', true ) . '</p>';
                echo '<p><strong>'.__('Has private health insurance?').'</strong><br>' . get_post_meta( $order->id, '_apm_insurance', true ) . '</p>';
            
                if(get_post_meta( $order->id, '_apm_insurance', true )) {
                    echo '<p><strong>'.__('Name of insurer').'</strong><br>' . get_post_meta( $order->id, '_apm_insurer_name', true ) . '</p>';
                    echo '<p><strong>'.__('Policy Holder Name').'</strong><br>' . get_post_meta( $order->id, '_apm_policy_holder', true ) . '</p>';
                    echo '<p><strong>'.__('Policy Number').'</strong><br>' . get_post_meta( $order->id, '_apm_policy_number', true ) . '</p>';
                    echo '<p><strong>'.__('Policy Expiration date').'</strong><br>' . get_post_meta( $order->id, '_apm_policy_expiration', true ) . '</p>';
                    echo '<p><strong>'.__('No. of authorised sessions').'</strong><br>' . get_post_meta( $order->id, '_apm_auth_sessions_num', true ) . '</p>';
                    echo '<p><strong>'.__('Authorisation Code').'</strong><br>' . get_post_meta( $order->id, '_apm_auth_code', true ) . '</p>';
                    echo '<p><strong>'.__('Policy Excess').'</strong><br>' . get_post_meta( $order->id, '_apm_policy_excess', true ) . '</p>';
                }

                echo '<p><strong>'.__('Main area requiring treatment').'</strong><br>' . get_post_meta( $order->id, '_apm_area_treatment', true ) . '</p>';
                echo '<p><strong>'.__('Marketing contact methods').'</strong><br>' . get_post_meta( $order->id, '_apm_gdpr', true ) . '</p>';

            echo '</div>';

            if($order->customer_message) {

                echo '<div class="apm__patient__details">';
                    echo '<h3>Additional information</h3>';
                    echo '<p>'.$order->customer_message.'</p>';
                echo '</div>';

            }
        }
        
        // "Class" products
        if(APM_Helpers::order_has_product_type($order->id, 'class')) {

            echo '<div class="apm__patient__details">';
                echo '<h3>Participant Details</h3>';
                echo '<p><strong>'.__('Date of birth').'</strong><br>' . get_post_meta( $order->id, '_apm_dob', true ) . '</p>';
                echo '<p><strong>'.__('Gender').'</strong><br>' . get_post_meta( $order->id, '_apm_gender', true ) . '</p>';
            echo '</div>';

            if($order->customer_message) {
                echo '<div class="apm__patient__details">';
                    echo '<h3>Additional information</h3>';
                    echo '<p>'.$order->customer_message.'</p>';
                echo '</div>';
            }

        }

    }

    /**
     * Outputs payment logos after button
     */
    public function checkout_card_logos() {

        echo '<div class="wc__checkout__cards"><img src="'.esc_url(get_stylesheet_directory_uri()).'/img/cards.png" alt="All major cards accepted"></div>';

    }

    /**
     * Logic on order complete
     * 
     * @param int $order_id
     */
    public function on_order_complete($order_id) {

        global $wpdb;
        $order = wc_get_order($order_id);
        $customer_id = $order->get_customer_id();

        if($customer_id) {

            foreach ( $order->get_items() as $item_id => $item ) {

                $product_id = $item->get_product_id();

                if($product_id === 5441) { continue; }

                $_product = new APM_Product($product_id);

                if($_product->is_appointment()) {
                
                    $appt = $item->get_meta('appointment');
                    $product = $item->get_meta('product');
                    $lumeon_session = $item->get_meta('lumeon_session');

                    // Make SUPER sure we have everything we need
                    if(!empty($appt) && !empty($product) && !empty($lumeon_session)) {

                        $reservation_id = $appt['lm_reservation_id'];

                        // Book with Lumeon
                        if($reservation_id) {

                            $lumeon = new LumeonAPI;
                            $lumeon->wpdb = $wpdb;
                            $lumeon->SetSession($lumeon_session['ID']);

                            $lumeonData = array(
                                'reservation_id' => $reservation_id,
                                'status' => 'booked',
                            );

                            $lumeon_response = $lumeon->appointmentSetStatus($lumeonData);

                        }
                        
                    }

                }

            }

            /**
             * Maybe workout credits
             */
            foreach ( $order->get_items() as $item_id => $item ) {

                $product_id = $item->get_product_id();

                // Skip if block product
                if($product_id === 5441) { continue; }

                $product = new APM_Product($product_id);

                if($product->is_appointment()) {

                    $appt = $item->get_meta('appointment');
                    $cart_booking_data = $item->get_meta('cart_booking_data');

                    // Make SUPER sure we have everything we need
                    if(!empty($appt) && !empty($cart_booking_data)) {

                        $customer = new APM_Patient($customer_id);

                        // Maybe workout credits
                        $credits = $cart_booking_data->credits;

                        if($credits > 0) {
                            $customer->set_credits(
                                $credits,
                                $cart_booking_data->upsell_block_id,
                                $cart_booking_data->has_block
                            );
                        }

                        // Get out as soon as we've done this
                        break;

                    }

                }

            }

        }

    }

    /**
     * Adds additional recipients to email sent when an order is
     * containing products is received
     * 
     * @param string $recipient
     * @param object $order
     */
    public function extra_admin_email_recipients( $recipient, $order ) {

        // Bail on WC settings pages since the order object isn't yet set
        // Not sure why this is even a thing, but shikata ga nai
        $page = $_GET['page'] = isset( $_GET['page'] ) ? $_GET['page'] : '';
        if ( 'wc-settings' === $page ) {
            return $recipient; 
        }
        
        // just in case
        if ( ! $order instanceof WC_Order ) {
            return $recipient; 
        }
        
        $items = $order->get_items();

        if(!empty($items)) {
        
            $shop_products = APM_Helpers::get_shop_products();
    
            // check if a shipped product is in the order	
            foreach ( $items as $item ) {
                $product = $order->get_product_from_item( $item );
                
                // add our extra recipient if there's a shipped product - commas needed!
                // we can bail if we've found one, no need to add the recipient more than once
                if ( $product && in_array($product->get_id(), $shop_products)) {
                    //$recipient .= ', sales@eurekaphysiocare.com'; marcus.kay@bodyset.co.uk 
                    $recipient .= ', '.get_field('wc_checkout_emails', 'option');
                    return $recipient;
                }
            }

        }
        
        return $recipient;
    }

}

