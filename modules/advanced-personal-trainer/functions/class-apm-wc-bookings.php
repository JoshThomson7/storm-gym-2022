<?php
/**
 * APM WooCommerce Bookings
 *
 * Class in charge of WooCommerce's APM Bookings
 * action and hook overrides
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_WC_Bookings {

    public function init() {

        add_filter('wc_session_expiring', array($this, 'session_expiring'));
        add_filter('wc_session_expiration', array($this, 'session_expired'));

        add_filter('woocommerce_add_cart_item_data', array($this, 'hold_booking'), 10, 3);
        add_action('woocommerce_remove_cart_item', array($this, 'remove_cart_item'), 10, 2);
        add_action('woocommerce_cleanup_sessions', array($this, 'cancelled_cart'), 10, 1);
        add_action('woocommerce_cancelled_order', array($this, 'cancelled_booking'), 10, 1);
        add_action('woocommerce_order_status_completed', array($this, 'confirmed_booking'), 10, 1);
        add_action('woocommerce_payment_complete', array($this, 'block_booking'), 10, 2);
        //add_filter('woocommerce_cart_item_name', array($this, 'show_block_booking_qty'), 10, 3);
        add_action('woocommerce_before_calculate_totals', array($this, 'check_for_credit'));
        add_action('woocommerce_after_checkout_validation', array($this, 'force_stripe_save_card'));
        add_action('woocommerce_checkout_before_terms_and_conditions', array($this, 'card_storage_message'));
        
    }

    /**
     * Log booking
     * 
     * @param array $cart_item_data
     * @param array $product_id
     * @param int $variation_id
     */
    public function hold_booking($cart_item_data, $product_id, $variation_id) {
        global $wpdb;
        //error_log(print_r($cart_item_data), true);
        if(isset($cart_item_data['apm_slot']) && $cart_item_data['apm_slot'] != ''){
            $added = new DateTime();
            $added = $added->format('Y-m-d H:i:s');
            $booking_data = array(
                'start' => $cart_item_data['apm_slot_start'],
                'end' => $cart_item_data['apm_slot_end'],
                'practitioner_id' => $cart_item_data['apm_practitioner_id'],
                'added' => $added,
                'status' => 'Pending'
            );
            $wpdb->insert('booked_appointments', $booking_data);
        }
        return $cart_item_data;
    }

    /**
     * Cart expiry times
     */
    public function session_expiring($seconds) {
        return 60 * 14; // 9 Min
    }

    public function session_expired($seconds) {
        return 60 * 15; // 10 Min
    }

    /** 
     * Remove booking from custom table on item removal
     * 
     * @param string $cart_item_key
     */
    public function remove_cart_item($cart_item_key){

        global $wpdb;
        global $woocommerce;

        $cart_items = $woocommerce->cart->get_cart();

        if(isset($cart_items[$cart_item_key]['apm_slot']) && $cart_items[$cart_item_key]['apm_slot'] != ''){
            $booking_data = array(
                'start' => $cart_items[$cart_item_key]['apm_slot_start'],
                'end' => $cart_items[$cart_item_key]['apm_slot_end'],
                'practitioner_id' => $cart_items[$cart_item_key]['apm_practitioner_id']
            );
            $wpdb->delete('booked_appointments', $booking_data);
        }
    }
    

    /**
     * Remove all bookings from custom table on cart expiry
     * 
     * @param array $param
     */
    public function cancelled_cart($array){

        global $wpdb;
        global $woocommerce;

        // $cart_items = $woocommerce->cart->get_cart();
        // foreach($cart_items as $cart_item){
        foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if(isset($cart_item['apm_slot']) && $cart_item['apm_slot'] != ''){
                $booking_data = array(
                    'start' => $cart_item['apm_slot_start'],
                    'end' => $cart_item['apm_slot_end'],
                    'practitioner_id' => $cart_item['apm_practitioner_id']
                );
                $wpdb->delete('booked_appointments', $booking_data);
            }
        }
    }


    /**
     * Remove booking on order cancellation
     * 
     * @param int $order_id
     */ 
    public function cancelled_booking($order_id){

        global $wpdb;

        $order = new WC_Order( $order_id );
        $order_items = $order->get_items(); //to get info about product
        foreach($order_items as $item_id => $item){
            if(apm_is_appointment_product($item->get_product_id())){
                $booking_data = array(
                    'start' => $item['_apm_slot_start'],
                    'end' => $item['_apm_slot_end'],
                    'practitioner_id' => $item['_apm_practitioner_id']
                );
                $wpdb->delete('booked_appointments', $booking_data);
            }
        }
    }

    /**
     * Mark booking as confirmed on payment complete
     * 
     * @param int $order_id
     */ 
    public function confirmed_booking($order_id){

        global $wpdb;

        // Get current user
        $current_user = wp_get_current_user();

        $order = new WC_Order( $order_id );
        $order_items = $order->get_items(); //to get info about product
        foreach($order_items as $item_id => $item){

            if(apm_is_appointment_product($item->get_product_id())) {

                $booking_data = array(
                    'start' => $item['_apm_slot_start'],
                    'end' => $item['_apm_slot_end'],
                    'practitioner_id' => $item['_apm_practitioner_id']
                );
                $wpdb->update('booked_appointments', array('status' => 'Confirmed'), $booking_data);
                
                // Add booking to remote database
                $added = new DateTime();
                $added = $added->format('Y-m-d H:i:s');
                $booking_data = array(
                    'start' => $item['apm_slot_start'],
                    'end' => $item['apm_slot_end'],
                    'practitioner_id' => $item['apm_practitioner_id'],
                    'added' => $added,
                    'status' => 'Confirmed'
                );
                $remote_db = new wpdb('capitalp_dbusr07','vMJCA{h#p,2bgJGLDb','capitalp_db2017','localhost');
                $remote_db->insert('booked_appointments', $booking_data);
                
                // Get clinic ID
                $clinic_id = wc_get_order_item_meta($item_id, '_apm_clinic_id', true);
                // Get tier ID;
                $tier = wp_get_post_terms($clinic_id, 'pricing_tier');
                $tier_id = $tier[0]->term_id;
                // Get any existing pre-paid appointments for this user
                $pre_paid_appointments = get_field('pre_paid_appointments', 'user_'.$current_user->ID);
                // Loop through the user's credit repeater and add the purchased credits
                if(isset($pre_paid_appointments) && !empty($pre_paid_appointments)){
                    $row = 1;
                    foreach($pre_paid_appointments as $pre_paid_appointment){
                        if($pre_paid_appointment['tier_id'] == $tier_id){
                            $new_total = $pre_paid_appointment['credits'] - 1;
                            $row_data = array('tier_id' => $pre_paid_appointment['tier_id'], 'credits' => $new_total);
                            update_row('pre_paid_appointments', $row, $row_data, 'user_'.$current_user->ID);
                        }
                        $row++;
                    }
                }
            }
        }
    }

    /**
     * For block bookings log the credit on the user account
     * 
     * @param int $order_id
     */
    public function block_booking($order_id){

        global $wpdb;
        global $woocommerce;

        $current_user = wp_get_current_user();

        $order = new WC_Order( $order_id );
        $order_items = $order->get_items(); //to get info about product

        $total_purchased_appointments = 0;

        foreach($order_items as $item_id => $item_obj){

            // Check if it is the 'block booking' product
            if($item_obj->get_product_id() === 5441) {

                // Get purchased credits and tier ID
                $credits = wc_get_order_item_meta($item_id, '_apm_block_booking_credits', true);
                $purchased_appointments = ($credits != '' ? $credits : 6);
                $tier_id = wc_get_order_item_meta($item_id, '_apm_price_tier_id', true);
                $block_booking_product_id = wc_get_order_item_meta($item_id, '_apm_block_booking_product_id', true);

                // Loop through the user's credit repeater and add the purchased credits
                if(isset($pre_paid_appointments) && !empty($pre_paid_appointments)) {
                    $row = 1;
                    foreach($pre_paid_appointments as $pre_paid_appointment) {
                        if($pre_paid_appointment['tier_id'] == $tier_id){
                            $new_total = $pre_paid_appointment['credits'] + $purchased_appointments;
                            $row_data = array('tier_id' => $pre_paid_appointment['tier_id'], 'credits' => $new_total, 'product' => $block_booking_product_id);
                            update_row('pre_paid_appointments', $row, $row_data, 'user_'.$current_user->ID);
                            $updated = 'yes';
                        }
                        $row++;
                    }
                    if(!isset($updated) || $updated != 'yes'){
                        $row_data = array('tier_id' => $tier_id, 'credits' => $purchased_appointments, 'product' => $block_booking_product_id);
                        add_row('pre_paid_appointments', $row_data, 'user_'.$current_user->ID);
                    }

                } else {
                    $row_data = array('tier_id' => $tier_id, 'credits' => $purchased_appointments, 'product' => $block_booking_product_id);
                    add_row('pre_paid_appointments', $row_data, 'user_'.$current_user->ID);
                }
            }

        }
        if(isset($pre_paid_appointments) && $pre_paid_appointments > 0 && $total_purchased_appointments > 0){
            // Send an email to the accounts people
            $mailer = $woocommerce->mailer();
            $message_body = '
            The user '.$current_user->display_name.' ('.$current_user->user_email.') has purchased '.$total_purchased_appointments.' appointments, which means they now have a total of '.$pre_paid_appointments.' pre-paid appointments on their account.
            ';
            $message = $mailer->wrap_message('New Block Booking', $message_body);
            //$mailer->send('joshua.catlett@capitalphysio.com', 'New Block Booking', $message); //LIVE
            $mailer->send('alex@fl1.digital', 'New Block Booking', $message); //TESTING
        }
    }

    /**
     * Show number of appointments selected, in the cart for block bookings
     * 
     * @param string $name
     * @param object $cart_item
     * @param string $cart_item
     */
    public function show_block_booking_qty($name, $cart_item, $cart_item_key){
        if($cart_item['product_id'] === 5441){
            $name .= "<br/><br/>x".$cart_item['variation']['attribute_appointments']." appointments";
        }
        return $name;
    }

    /**
     * Check for bookings 'credits' on account when adding to the cart
     * 
     * @param object $cart_object
     */
    public function check_for_credit($cart_object) {

        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

        global $current_user;

        /**
        *
        *  Block Booking upsells - what a faff!
        *
        */
            $is_block_booking_upsell = false;
            $_offers = array();
            $offer_counter = 0;
            foreach ($cart_object->cart_contents as $item) {
                if($item['product_id'] === 5441) {
                    if($item['apm_block_booking_upsell'] === 'yes') {
                        $is_block_booking_upsell = true;
                        $booklet_product_id = $item['apm_block_booking_product_id'];
                    }

                } elseif(apm_is_appointment_product($item['product_id'])) {
                
                    $clinic_id = $item['apm_clinic_id'];

                    $product_tier = wp_get_post_terms($clinic_id, 'pricing_tier');
                    $product_tier_id = $product_tier[0]->term_id;

                    if($product_tier_id) {
                        $_offers[$offer_counter] = $product_tier_id;
                        $offer_counter++;
                    }

                    // DEBUG
                    // if(current_user_can('administrator')) {
                    //     $item['data']->set_price(0.00);
                    // }
                }
            }

            // for multiple appointments, check that all are at the SAME clinic
            $offers_same_clinic = true;
            if (count(array_unique($_offers)) !== 1 && end($_offers) !== 'true') {
                $offers_same_clinic = false;
            }

            if($offers_same_clinic == true && !empty($_offers)) {
                // store occurences (how many of each)
                $offer_occurences = array_count_values($_offers);
                print_r($_offers);

                // delete duplicates
                $offers = array_unique($_offers);

                // work out quantity from occurences after removing duplicates
                $offer_quantity = $offer_occurences[$offers[0]];

                if($is_block_booking_upsell) {
                    $slots_count = 1;
                    foreach ($cart_object->cart_contents as $item) {
                        if(apm_is_appointment_product($item['product_id']) && $booklet_product_id == $item['product_id']) {
                            if($slots_count <= 7) {
                                $item['data']->set_price(0.00);
                            }

                            add_action('woocommerce_cart_totals_before_order_total', 'wc_cart_upsell_notice');
                        }

                        if($item['product_id'] === 5441) {
                            $item['apm_block_booking_credits'] = $offer_quantity;

                            // DEBUG
                            // if(current_user_can('administrator')) {
                            //     $item['data']->set_price(0.00);
                            // }
                        }
                        $slots_count++;
                    }
                }
            }

        /**
        *
        *  Handle normal (not upselling) cart block bookings
        *
        */
        // Get any existing pre-paid appointments for this user
        $pre_paid_appointments = get_field('pre_paid_appointments', 'user_'.$current_user->ID);

        // Loop through the cart items
        $used_credits = 0;
        $product_ids = array();
        foreach ($cart_object->cart_contents as $item) {

            $product_ids[] = $item['product_id'];

            // Check we have a logged in user
            if(is_user_logged_in()) {

                // If they have credit and if this is an appointment product
                if(isset($pre_paid_appointments) && !empty($pre_paid_appointments) && apm_is_appointment_product($item['product_id'])) {
                    
                    // Get tier ID
                    $pricing_tier = $item['apm_price_tier']; // grab pricing tier ID

                    // Loop through credits array and check if they have credits for this pricing tier
                    foreach($pre_paid_appointments as $pre_paid_appointment) {
                        $current_credits = intval($pre_paid_appointment['credits']) - $used_credits;
                        
                        if($pre_paid_appointment['tier_id'] == $pricing_tier && $pre_paid_appointment['product'] == $item['product_id'] && $current_credits > 0) {
                            $item['data']->set_price(0.00);
                            $used_credits++;
                        }
                    }
                }
            }
            
            // is it a block booking?
            if($item['product_id'] === 5441) {
                $pricing_tier = $item['apm_price_tier']; // grab pricing tier ID
                $pricing_tier_product_id = $item['apm_block_booking_product_id'];

                if(isset($pricing_tier) && $pricing_tier != '') {
                    $pricing_tier_prices = get_field('pricing_tier_prices', 'pricing_tier_'.$pricing_tier); // get pricing tier price
                    
                    if(isset($pricing_tier_prices) && !empty($pricing_tier_prices)) {
                    
                        foreach($pricing_tier_prices as $price_entry) {
                            if(apm_is_appointment_product($price_entry['pricing_tier_product']) && $pricing_tier_product_id == $price_entry['pricing_tier_product']) {
                                $pricing_tier_price = $price_entry['pricing_tier_price'];
                            }
                        }
                    }

                    // Get block booking data.
                    $block_booking = apm_get_product_block_booking($pricing_tier_product_id);

                    // discount
                    $block_booking_discount = $block_booking['discount'];
                    $block_booking_credits = $block_booking['credits'];

                    $discount_price = ($pricing_tier_price * $block_booking_credits) * ((100-$block_booking_discount) / 100); // calculate new price based on tier
                    
                    $item['data']->set_name('Block Booking: '.$block_booking_credits.' x '.get_the_title($pricing_tier_product_id));
                    $item['data']->set_price($discount_price); // set new price

                    // DEBUG
                    // if(current_user_can('administrator')) {
                    //     $item['data']->set_price(0.00);
                    // }
                }
            }
        }

        // notices (only show if physio product is in cart)
        if(isset($pre_paid_appointments) && !empty($pre_paid_appointments) && apm_product_is_in_the_cart()) {
            add_action('woocommerce_proceed_to_checkout', 'wc_cart_prepaid_notice');
        }

        return $cart_object;

    }

    /**
     * Force stripe to store card
     * 
     * @param array $posted
     */
    public function force_stripe_save_card($posted) {
        $_POST['wc-stripe-new-payment-method'] = 'true';
    }

    /**
     * Add a message to warn about storing card details
     */
    public function card_storage_message(){
        echo '<p class="card-storage-message">For your convenience, payment details will be securely stored and repeat bookings charged after each session.</p>';
    }
    
}

