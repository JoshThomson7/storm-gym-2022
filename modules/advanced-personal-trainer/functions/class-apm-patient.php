<?php
/**
 * APM Patient
 *
 * Class in charge of patient users
 * 
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Is WooCommerce installed?
if(class_exists('WC_Customer')) {

    /**
     * For WC methods:
     * 
     * @see https://docs.woocommerce.com/wc-apidocs/class-WC_Customer.html
     */
    class APM_Patient extends WC_Customer {

        /**
         * Returns the user full name
         */
        public function get_full_name() {

            return $this->get_first_name().' '.$this->get_last_name();

        }

        /**
         * Getter for DOB
         */
        public function get_dob() {

            return get_field('user_dob', 'user_'.'user_'.$this->get_id());

        }

        /**
         * Getter for gender
         */
        public function get_gender() {

            return get_field('user_gender', 'user_'.$this->get_id());

        }

        /**
         * Getter for Lumeon ID
         */
        public function get_lumeon_id() {

            return get_field('user_lumeon_id', 'user_'.$this->get_id());

        }

        /**
         * Getter for user_credits
         */
        public function get_credits($product_id = null) {

            $credits = get_field('user_credits', 'user_'.$this->get_id());

            if($product_id) {

                foreach($credits as $credit) {

                    if($credit['product'] == $product_id) {
                        return $credit['credits'];
                        break;
                    }

                }

            } else {
                return $credits;
            }

        }

        /**
         * 
         * Setters
         * 
         */

        /**
         * Setter for Lumeon ID
         * 
         * @param string value
         */
        public function set_lumeon_id($value) {

            update_user_meta($this->get_id(), 'user_lumeon_id', $value);
            
        }

        /**
         * Setter for DOB
         * 
         * @param string value
         */
        public function set_dob($value) {

            update_user_meta($this->get_id(), 'user_dob', $value);

        }

        /**
         * Setter for Gender
         * 
         * @param string value
         */
        public function set_gender($value) {

            update_user_meta($this->get_id(), 'user_gender', $value);

        }

        /**
         * Setter for user_credits
         */
        public function set_credits($value, $product_id, $has_block) {

            $credits = $this->get_credits() ? $this->get_credits() : array();

            if(!empty($credits) && !$has_block) {

                foreach($credits as $key => $credit) {

                    if($credit['product'] == $product_id) {
                        $credits[$key]['credits'] = $value;
                    }

                }

            }

            if($has_block) {
                $credits[] = array(
                    'product' => $product_id,
                    'credits' => $value
                );       
            }

            update_field('user_credits', $credits, 'user_'.$this->get_id());

        }


    }

}