<?php
/**
 * APM Search
 *
 * Class in charge of single clinic
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Search {

    public function init() {

        add_action('wp_ajax_nopriv_appointment_finder', array($this, 'appointment_finder'));
        add_action('wp_ajax_appointment_finder', array($this, 'appointment_finder'));

    }

    // public static function popup() {
    //     require_once APM_PATH . 'templates/global-search/global-search.php';
    // }

    /**
     * iCal AJAX Search
     *
     * Used for Ajaxifying booking dates. Yeah!
     *
     * @author  Various
     * @package Advanced Physio Module
     *
    */
    public function appointment_finder() {

        // Security check
        check_ajax_referer('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ical_security');

        global $woocommerce;

        $action = isset($_POST['ical_search_action']) && !empty($_POST['ical_search_action']) ? $_POST['ical_search_action'] : null;
        $product_id = isset($_POST['ical_search_product_id']) && !empty($_POST['ical_search_product_id']) ? $_POST['ical_search_product_id'] : null;
        $slot_length = get_field('product_slot_length', $product_id);

        $date = isset($_POST['ical_search_date']) && !empty($_POST['ical_search_date']) ? $_POST['ical_search_date'] : null;

        $geocode = isset($_POST['ical_search_geocode']) && !empty($_POST['ical_search_geocode']) ? $_POST['ical_search_geocode'] : null;
        $radius = isset($_POST['ical_search_radius']) && !empty($_POST['ical_search_radius']) ? $_POST['ical_search_radius'] : 5;

        //$tier_id = isset($_POST['ical_search_tier_id']) && !empty($_POST['ical_search_tier_id']) ? $_POST['ical_search_tier_id'] : null;
        $practitioner_gender = isset($_POST['ical_search_gender']) && !empty($_POST['ical_search_gender']) ? $_POST['ical_search_gender'] : null;
        $clinic_ids = isset($_POST['ical_search_clinic_ids']) && !empty($_POST['ical_search_clinic_ids']) ? $_POST['ical_search_clinic_ids'] : null;
        $practitioner_ids = isset($_POST['ical_search_practitioner_ids']) && !empty($_POST['ical_search_practitioner_ids']) ? $_POST['ical_search_practitioner_ids'] : null;

        // dates
        if($date) {
            $slot_start = date('Ymd\THis\Z', strtotime('-1 day'. $date));
        } else {
            $slot_start = isset($_POST['ical_search_slot_start']) && !empty($_POST['ical_search_slot_start']) ? $_POST['ical_search_slot_start'] : null;
        }

        $slot_prev = isset($_POST['ical_search_slot_prev']) && !empty($_POST['ical_search_slot_prev']) ? $_POST['ical_search_slot_prev'] : null;
        $first_slot = isset($_POST['ical_search_first_slot']) && !empty($_POST['ical_search_first_slot']) ? $_POST['ical_search_first_slot'] : null;

        $filters_open = '';

        // Geocode location.
        $geo_data = APM_Helpers::geocode($geocode, $action);

        if(!empty($geo_data) && is_array($geo_data)) {
        
            $lat = $geo_data[0];
            $lng = $geo_data[1];

            /**
             *
             * Geocode
             *
             */
            if(isset($action) && ( $action === 'geocode' || $action === 'geolocate' || $action === 'filter' )) {

                // Get appointments.
                // $variable_id = $tier_id;
                // $practitioner_func = 'apm_get_tier_practitioners';
                //
                // keep filters open
                if(isset($action) && $action === 'filter') {
                    $filters_open = ' on';
                }

                require_once APM_PATH . 'templates/global-search/global-search-filters.php';
                require_once APM_PATH . 'templates/global-search/global-search-loop.php';

            } elseif(isset($action) && $action === 'load_more') {

                require_once APM_PATH . 'templates/global-search/global-search-loop.php';

            /**
             *
             * Filter clinics dropdown.
             *
             */
            } elseif(isset($action) && $action === 'clinic') {

                $html = '<option value="all">Any practitioner</option>';

                if($clinic_id == 'all') {
                    $practitioners = APM_Helpers::get_practitioners_by('tier', $tier_id);
                } else {
                    $practitioners = APM_Helpers::get_practitioners_by('clinic', $clinic_id);
                }

                foreach($practitioners as $practitioner) {

                    $selected = '';
                    if(isset($practitioner_id) && $practitioner_id == $practitioner['ID']) {
                        $selected = ' selected="selected"';
                    }

                    $html .= '<option value="'.$practitioner['ID'].'"'.$selected.' data-gender="'.$practitioner['gender'].'">'.$practitioner['post_title'].'</option>';
                }

                echo $html;

            }

        } // end geodata

        wp_die();
    }

}

