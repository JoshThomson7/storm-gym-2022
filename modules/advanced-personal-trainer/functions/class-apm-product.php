<?php
/**
 * APM Product
 *
 * Class in charge of single service
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(class_exists('WC_Product')) {

    class APM_Product extends WC_Product {
        
        /**
         * Returns product_lumeon_id ACF
         */
        public function lumeon_id() {

            return get_field('product_lumeon_id', $this->get_id());

        }

        /**
         * Returns product_slot_length ACF
         */
        public function slot_length() {

            return get_field('product_slot_length', $this->get_id());

        }

        /**
         * Returns product_button_label ACF
         */
        public function button_label() {

            return get_field('product_button_label', $this->get_id());

        }

        /**
         * Returns product_terms
         */
        public function product_terms($taxonomy = 'product_cat', $key = '') {

            $terms = get_the_terms($this->get_id(), $taxonomy);

            if(!empty($terms)) {
                if($key) {
                    $terms = wp_list_pluck($terms, $key);
                }

                return $terms;
            }

            return null;

        }

        /**
         * Returns true if product mode is appointment
         */
        public function is_appointment() {

            if(has_term('appointment', 'product_mode', $this->get_id())) {
                return true;
            }
            
            return false;

        }

        /**
         * If the current product has been assigned with
         * a Block Booking upsell product, return its ID
         */
        public function upsell_block_id() {

            $product_id = get_field('product_block_upsell', $this->get_id());

            if($product_id) {
                return $product_id;
            }

            return null;
            
        }

        /**
         * Gets the number of credits a customer will get with a block booking of this product.
         */
        public function block_credits() {

            if($this->upsell_block_id()) {
                return get_field('product_block_credits', $this->upsell_block_id());
            }

            return null;
            
        }

        /**
         * Generate Block product title.
         */
        public function block_title() {

            if($this->upsell_block_id()) {
                $_block = new APM_Product($this->upsell_block_id());
                return $this->block_credits().' &times; '.$_block->get_title();
            }

            return null;
            
        }

        /**
         * Gets the price a customer will pay for a Block Booking.
         */
        public function block_price() {

            if($this->upsell_block_id()) {
                return get_field('product_block_price', $this->upsell_block_id());
            }

            return null;
            
        }

        /**
         * Returns true if product mode is follow-up
         */
        public function is_follow_up() {

            if(has_term('follow-up', 'product_mode', $this->get_id())) {
                return true;
            }
            
            return false;
            
        }

        /**
         * Returns true if product mode is assessment
         */
        public function is_assessment() {

            if(has_term('assessment', 'product_mode', $this->get_id())) {
                return true;
            }
            
            return false;
            
        }

        /**
         * Returns true if product mode is insurance
         */
        public function with_insurance() {

            if(has_term('insurance', 'product_mode', $this->get_id())) {
                return true;
            }
            
            return false;
            
        }

        /**
         * Returns services this product belongs to
         * 
         * @return array
         */
        public function services() {

            $services = new WP_Query(array(
                'post_type' => 'service',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'name',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'service_products',
                        'value' => '"' . $this->get_id() . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
                        'compare' => 'LIKE'
                    )
                ),
                'fields' => 'ids'

            ));
            
            return $services->posts;
            
        }

        /**
         * Rest API Data output
         * 
         * @return object $data
         */
        public function rest_api_data($services = false) {

            $data = new stdClass();
    
            $data->ID = $this->get_id();
            $data->name = html_entity_decode($this->get_name());
            $data->permalink = $this->get_permalink();
            $data->lumeon_id = $this->lumeon_id();
            $data->slot_length = $this->slot_length();
            $data->button_label = $this->button_label();
            $data->is_appointment = $this->is_appointment();
            $data->upsell_block_id = $this->upsell_block_id();
            $data->block_credits = $this->block_credits();
            $data->block_price = $this->block_price();
            $data->is_follow_up = $this->is_follow_up();
            $data->is_assessment = $this->is_assessment();
            $data->with_insurance = $this->with_insurance();

            if($services) {
                $getServices = $this->services();

                $data->services = array();

                if(!empty($getServices)) {
                    foreach($getServices as $service_id) {
                        $service = new APM_Service($service_id);
                        array_push($data->services, $service->rest_api_data());
                    }
                }
            }

            return $data;
    
        }

    }

}