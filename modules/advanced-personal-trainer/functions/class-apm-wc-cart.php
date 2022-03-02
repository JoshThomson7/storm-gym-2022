<?php
/**
 * APM WooCommerce Cart
 *
 * Class in charge of WooCommerce's Cart
 * action and hook overrides
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_WC_Cart {

    public static function onLoad() {

        // add_filter('wc_session_expiring', function() { 
        //     return MINUTE_IN_SECONDS * get_field('wc_appointment_expiry', 'option');
        // });
        // add_filter('wc_session_expiration', function() { 
        //     return MINUTE_IN_SECONDS * get_field('wc_appointment_expiry', 'option');
        // });
        add_filter('woocommerce_persistent_cart_enabled', '__return_false');

    }

    public function init() {

        add_action('init', array($this, 'wc_clear_cart_url'));

        add_filter('woocommerce_persistent_cart_enabled', '__return_false');
        add_filter('woocommerce_cart_item_permalink', array($this, 'cart_item_permalink'), 10, 3);
        add_filter('woocommerce_order_item_permalink', '__return_false');
        
        add_action('woocommerce_before_calculate_totals', array($this, 'before_calculate_totals'), 10 ); // Custom calculations
        add_action('woocommerce_before_cart_totals', array($this, 'before_cart_totals')); // Display notices before cart totals (credits)

        // Notices
        add_action('woocommerce_checkout_before_customer_details', array($this, 'cart_notices'));
        add_action('woocommerce_before_cart', array($this, 'cart_notices'));

        //add_action('woocommerce_after_cart', array($this, 'but_whats_in_the_cart')); // Dump cart data

        // Handle cart data
        add_filter('woocommerce_get_item_data', array($this, 'wc_apm_get_item_data'), 10, 2 ); // Cart
        add_action('woocommerce_cart_item_removed', array($this, 'on_cart_item_removed'), 10, 2); // On delete cart item
        add_action('woocommerce_cancelled_order', array($this, 'on_cancel_order')); // On cancelling order
        add_action('woocommerce_cart_loaded_from_session', array( $this, 'maybe_expire_cart_items'), 10);


        // Disable persistent cart
        add_filter('get_user_metadata', array($this, 'wc_remove_persistent_cart'), 10, 3); 
        add_filter('update_user_metadata', array($this, 'wc_remove_persistent_cart'), 10, 3); 
        add_filter('add_user_metadata', array($this, 'wc_remove_persistent_cart'), 10, 3);

        add_action('woocommerce_cart_is_empty', array($this, 'cart_is_empty'), 10);

    }    

    /**
     * Little helper hook to clear WC cart
     */
    public function wc_clear_cart_url() {
        
        if(isset($_GET['clear-cart'])) { 
            WC()->cart->empty_cart();
        }

    }

    /**
     * Little helper hook to see what's in the cart
     */
    public function but_whats_in_the_cart() {

        if(function_exists('pretty_print')) {
            //pretty_print(WC()->cart->get_cart());
            pretty_print(WC()->session);
        }

    }

    /**
     * Cart: display cart meta.
     * 
     * @param array $data
     * @param array $cartItem
     */
    public function wc_apm_get_item_data( $data, $cartItem ) {

        $this->wc_apm_display_item_data($cartItem);
        //pretty_print($cartItem);
        
    }    

    /**
     * Cart: displays configuration as cart meta.
     * 
     * @param array $item
     */
    private function wc_apm_display_item_data($item) {

        if(isset($item['appointment']) && !empty($item['appointment'])) {

            $appt = $item['appointment'];
            $product = WC()->session->get('product');
            $location = WC()->session->get('clinic');
            $questions = WC()->session->get('questions');

            ?>  
                <div class="wc__cart__product--meta">
                    <ul>
                        <li><strong>Date</strong> <span><?php echo $appt['start']['date']; ?></span></li>
                        <li><strong>Time</strong> <span><?php echo $appt['start']['time'].' - '.$appt['end']['time']; ?></span></li>
                        <?php
                            if(isset($appt['lm_practitioner_id']) && !empty($appt['lm_practitioner_id']) && is_numeric($appt['lm_practitioner_id'])):
                                $practitioner = new APM_Practitioner(null, $appt['lm_practitioner_id']);
                        ?>
                            <li><strong>Practitioner</strong> <span><?php echo $practitioner->title(); ?></span></li>
                        <?php endif; ?>
                        <?php if(isset($location['name']) && !empty($location['name'])): ?><li><strong>Location</strong> <span><?php echo $location['name']; ?></span></li><?php endif; ?>
                        <li><strong>Service</strong> <span><?php echo get_the_title($product['ID']); ?></span></li>
                    </ul>
                </div><!-- wc__cart__product--meta -->
            <?php

        }
        
    }

    /**
     * Custom cart product permalink
     * 
     * @param string $permalink
     * @param string $cart_item
     * @param string $cart_item_key
     */
    public function cart_item_permalink($permalink, $cart_item, $cart_item_key) { 

        $product_id = $cart_item['product_id'];
        $_product = new APM_Product($product_id);

        if(!$_product->is_appointment()) {
            return $permalink;
        }

        return false;
        
    }

    /**
     * Before calculate totals hook
     */
    public function before_calculate_totals() {

        // Bail
        if(is_admin() && ! defined( 'DOING_AJAX' ) ) { return; }
        if(!is_user_logged_in()) { return false; }
        
        /**
         * Session items
         */
        $product = WC()->session->get('product');
        $upsell_block_id = WC()->session->get('upsell_block_id');
        $questions = WC()->session->get('questions');
        $clinic = WC()->session->get('clinic');
        $appointments = WC()->session->get('appointments');
        $appointments_no = count($appointments);

        $_product_id = $product['ID'];
        $_product = new APM_Product($_product_id);

        $customer = new APM_Patient(get_current_user_id());
        $available_credits = $customer->get_credits($_product_id); // Get credits for THIS product

        $cart_booking_data = APM_Helpers::get_cart_booking_data();

        foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item) {

            /**
             * Block Booking magic
             */
            if($cart_booking_data->has_block) {

                $_block = new APM_Product($upsell_block_id);

                if($cart_item['product_id'] === 5441) { // Block WC Product

                    $cart_item['data']->set_name('BLOCK: '.$_block->block_title());
                    $cart_item['data']->set_price($_block->block_price());

                } else {

                    if($_product->get_id() === $cart_item['product_id'] && $_product->is_appointment() ) {

                        /**
                         * Follow-up.
                         * Block booking.
                         */
                        if($questions['purpose'] === 'follow-up') {

                            $cart_booking_data->credits = $appointments_no;
                            $cart_booking_data->upsell_block_id = $upsell_block_id;
                        
                        /**
                         * Everything else.
                         * Block booking.
                         */
                        } else {

                            $cart_item['data']->set_price(0);

                            $customer_credits = $_block->block_credits();
                            $cart_booking_data->credits = $customer_credits;
                            $cart_booking_data->upsell_block_id = $upsell_block_id;

                        }

                    }
                }
                
            } else {
                
                // Follow-up with existing credits for chosen appointment type
                if($cart_booking_data->has_appointment && $available_credits > 0 && $questions['purpose'] === 'follow-up' && $_product->get_id() === $cart_item['product_id']) {
                    $cart_item['data']->set_price(0);

                    $customer_credits = $available_credits - $appointments_no;
                    $cart_booking_data->credits = $customer_credits;
                    $cart_booking_data->upsell_block_id = $upsell_block_id;
                    $cart_booking_data->can_pay_with_insurance = false;
                }

            }
            
            /**
             * Set appointment to zero (0) if insurance session variable is set to yes
             */
            if($_product->get_id() === $cart_item['product_id'] && $_product->is_appointment() && $_product->with_insurance() && $cart_booking_data->can_pay_with_insurance && $cart_booking_data->has_insurance) {
                $cart_item['data']->set_price(0);
            }

        } // endforeach

        // Finally add cart_booking_data to session
        WC()->session->set('cart_booking_data', $cart_booking_data);
    
    }

    /**
     * Removes appointment from Lumeon via API
     * when removing an item from the cart
     * 
     * @param string $cart_item_key
     * @param array $cart_instance
     */
    public function on_cart_item_removed($cart_item_key, $cart_instance) {

        $line_item = $cart_instance->removed_cart_contents[$cart_item_key];
        
        $appt = $line_item['appointment'];
        $lumeonSession = WC()->session->get('lumeon_session');

        if(!empty($appt) && !empty($lumeonSession)) {

            $reservation_id = $appt['lm_reservation_id'];

            if($reservation_id) {

                $this->cancelLumeonAppt($lumeonSession['ID'], $reservation_id);

            }

        }

    }

    /**
     * Removes appointment from Lumeon via API
     * when cancelling an order
     * 
     * @param int $order_id
     */
    public function on_cancel_order($order_id) {

        $order = wc_get_order($order_id);

        foreach ( $order->get_items() as $item_id => $item ) {

            $appt = $item->get_meta('appointment');
            $lumeonSession = $item->get_meta('lumeon_session');

            if(!empty($appt) && !empty($lumeonSession)) {

                $reservation_id = $appt['lm_reservation_id'];

                if($reservation_id) {

                    $this->cancelLumeonAppt($lumeonSession['ID'], $reservation_id);

                }

            }
            
        }

    }

    /**
     * Expires appointments from cart
     */
    public function maybe_expire_cart_items() {

        $lumeonSession = WC()->session->get('lumeon_session');
        $reservationIDs = $lumeonSession['reservationIDs'];
        $sessionExpiry = $lumeonSession['cartExpiry'];

        $now = new DateTime('now', wp_timezone());
        $now = $now->getTimestamp();

        if($sessionExpiry <= $now && !empty($reservationIDs)) {

            foreach($reservationIDs as $reservationID) {
                $this->cancelLumeonAppt($lumeonSession['ID'], $reservationID);
            }
        
            foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item) {

                $product_id = $cart_item['product_id'];
                $_product = new APM_Product($product_id);

                if($_product->is_appointment()) {
                    WC()->cart->remove_cart_item( $cart_item_key );
                }

            }

        }

    }

    /**
     * Lumeon REST API call to cancel appointment
     * 
     * @param int $sessionID
     * @param int $reservationID
     */
    private function cancelLumeonAppt($sessionID, $reservationID) {

        global $wpdb;

        $lumeon = new LumeonAPI;
        $lumeon->wpdb = $wpdb;
        $lumeon->SetSession($sessionID);

        $lumeonData = array(
            'reservation_id' => $reservationID,
            'status' => 'cancelled',
        );

        $lumeon_response = $lumeon->appointmentSetStatus($lumeonData);

    }

    /**
     * Display notice before cart totals
     * 
     * @return bool
     */
    public function before_cart_totals() {

        $cart_booking_data = APM_Helpers::get_cart_booking_data();

        $customer = new APM_Patient(get_current_user_id());

        $product = WC()->session->get('product');
        $_product_id = $product['ID'];
        $_product = new APM_Product($_product_id);
        $customer_credits = $customer->get_credits($_product_id);

        $questions = WC()->session->get('questions');
        $upsell_block_id = WC()->session->get('upsell_block_id');
        $appointments = WC()->session->get('appointments');

        $show_notice = false;

        if($cart_booking_data->has_appointment) {

            if($cart_booking_data->has_block) {
                
                $show_notice = true;

                if($questions['purpose'] === 'initial-assessment') {
                    $notice_msg = 'Because you are purchasing a Block, this will add <strong>'.$_product->block_title().' credits</strong> to your account.';
                } else {
                    $notice_msg = 'Appointments in your cart that are discounted to &pound;0.00 will be paid for using pre-paid credits in your account.';
                    $notice_msg .= '<br><br>Because you are purchasing a Block of '.$_product->block_title().' and you are booking '.$cart_booking_data->appointment_count.' appointment(s), <strong>'.($_product->block_credits() - $cart_booking_data->appointment_count).' credit(s) will be added to your account.</strong>';
                }

            } else {
                
                if($customer_credits > 0) {

                    $show_notice = true;

                    $notice_msg = '<strong>You have '.$customer_credits.' credit(s) for this booking</strong>.<br>';
                    $notice_msg .= 'Appointments in your cart that are discounted to &pound;0.00 will be paid for using pre-paid credits in your account.';
                    $notice_msg .= '<br><br>Because you are booking '.$cart_booking_data->appointment_count.' appointment(s), you will be left with <strong>'.($customer_credits - $cart_booking_data->appointment_count).' credit(s) in your account.</strong>';

                }

            }
            
            if($show_notice) {

                $notice = array(
                    'bg' => '',
                    'icon' => 'fa-ticket',
                    'width' => '',
                    'heading' => 'Credits',
                    'notice' => $notice_msg
                );

                ?>
                <div class="apm__wc__notice <?php echo $notice['bg']; ?> <?php echo $notice['width']; ?>">
                    <figure><i class="fa <?php echo $notice['icon']; ?>"></i></figure>
                    <div>
                        <?php echo $notice['heading'] ? '<h5>'.$notice['heading'].'</h5>' : ''; ?>
                        <p><?php echo $notice['notice']; ?></p>
                    </div>
                </div>
                <?php

            }

        }

    }

    /*
     * Page templates
     */
    public function cart_notices() {

        $cart_booking_data = APM_Helpers::get_cart_booking_data();

        if($cart_booking_data->has_appointment) {

            $lumeonSession = $cart_booking_data->lumeonSession;
            $expiry = new DateTime('now', wp_timezone());
            $expiry = $expiry->setTimestamp($lumeonSession['cartExpiry']);
            $can_pay_with_insurance = $cart_booking_data->can_pay_with_insurance;

            $notices = array(
                array(
                    'bg' => 'orange',
                    'icon' => 'fa-head-side-mask',
                    'width' => 'half',
                    'heading' => 'Covid-19',
                    'notice' => '<strong>If your appointment is in person, please don\'t forget to bring a face mask.</strong>'
                ),
                array(
                    'bg' => 'orange',
                    'icon' => 'fa-clock',
                    'width' => 'half',
                    'heading' => 'Your booking',
                    'notice' => '<strong>We can only hold appointments in your basket for 15 minutes.</strong><br>Please complete your order before '.$expiry->format('H:i')
                ),
                array(
                    'bg' => '',
                    'icon' => 'fa-history',
                    'width' => 'half',
                    'heading' => 'Cancellations and refunds',
                    'notice' => 'Please note we have a 24 hr cancellation policy and charge in full for missed or late cancellations. Also note that all booklet purchases are non-refundable. These are valid for six months from the date of purchase.'
                ),
                array(
                    'bg' => '',
                    'icon' => 'fa-file-medical',
                    'width' => 'half',
                    'heading' => 'Private health insurance',
                    'notice' => 'We offer Private Medical Insurance on selected services. If you have private medical insurance, you will be able to enter your policy details at checkout. The cost of the session will be wavered and you will be charged a 30p insurance deposit booking fee which will be refunded.'
                ),
            );

            foreach($notices as $notice) {
                ?>
                <div class="apm__wc__notice <?php echo $notice['bg']; ?> <?php echo $notice['width']; ?>">
                    <figure><i class="fa <?php echo $notice['icon']; ?>"></i></figure>
                    <div>
                        <?php echo $notice['heading'] ? '<h5>'.$notice['heading'].'</h5>' : ''; ?>
                        <p><?php echo $notice['notice']; ?></p>
                    </div>
                </div>
                <?php
            }

        } else {
            return false;
        }

    }

    /**
     * Custom empty cart message
     */
    public function cart_is_empty() {
        $html = '<div class="wc__empty__basket">';
        $html .= '<div class="message"><figure><i class="fas fa-shopping-basket"></i></figure>';
        $html .= '<p>'.wp_kses_post( apply_filters( 'wc_empty_cart_message', __( 'Your basket is currently empty. Why not check out the items below?', 'woocommerce' ) ) ).'</p></div>';
        $html .= '<div class="wc__empty__basket--buttons">';
        $html .= '<a href="'.esc_url(home_url()).'" class="button primary_solid"><span>Back to Bodyset</span></a>';
        $html .= '<a href="'.esc_url(get_permalink(get_page_by_path('book'))).'" class="button orange_solid"><span>Book</span></a></div>';
        $html .= '</div>';

        echo $html;

        $cart_empty_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 4,
            'cache_results' => false,
            'orderby' => 'rand',
            'product_mode' => 'shop',
            'no_found_rows' => true
        );
        
        $cart_empty_query = new WP_Query($cart_empty_args);

        ?>

        <div class="wc__content">
            <div class="wc__products wc__products__grid">
                <?php
                    while($cart_empty_query->have_posts()) : $cart_empty_query->the_post();

                    if(get_post_thumbnail_id(get_the_ID())) {
                        $attachment_id = get_post_thumbnail_id(get_the_ID());
                        $prod_image = vt_resize($attachment_id,'' , 700, 700, true);

                    } else {
                        $prod_image = ' style="background-image:url('.get_stylesheet_directory_uri().'/img/product-holding.png;);"';
                    }

                    // loop
                    include(wc_path().'templates/woo-loop-grid.php');

                    endwhile; woocommerce_reset_loop(); wp_reset_postdata();
                ?>

                <?php pagination($cart_empty_query->max_num_pages); ?>
            </div><!-- wc__products -->
        </div>

        <?php

        //require(wc_path().'templates/woo-cart-empty.php');  
    }

    /**
     * Disable the persistent cart
     */
    public function wc_remove_persistent_cart($value, $id, $key) { 
        if ($key == '_woocommerce_persistent_cart') { 
            return false; 
        } 
        return $value; 
    } 

}

