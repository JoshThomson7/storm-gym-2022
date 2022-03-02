<?php
/**
 * APT Helper functions
 *
 * Module's helper functions
 *
*/

class APT_Helpers {

    /**
     * Check if we're looking at a custom
     * endpoint registered via add_rewrite_endpoint.
     *
     * @since 2.0
     */
    public static function is_endpoint($endpoint = null) {
        
        // Bail early if no endpoint
        if(!$endpoint) { return false; }

        global $wp_query;

        if(isset($wp_query->query_vars[$endpoint])) {
            return true;
        }

        return false;

    }

    /**
     * Handles GET params in the URL
     * 
     * @param string $param
     * 
     */
    public static function get_param($param, $type = null) {

        if(isset($_GET[$param]) && !empty($_GET[$param]) ) {
            
            $value = $_GET[$param];

            if($type) {

                switch ($type) {
                    case 'integer':
                        $validate = is_numeric($value);
                        break;

                    case 'string':
                        $validate = is_string($value);
                        break;
                    
                    default:
                        $validate = false;
                        break;
                }

            }
        }

        return $validate ? $value : null;

    }

    /**
     * Converts delimited string to array
     * 
     * @param string $param
     * @param string $delimiter
     */
    public static function param_to_array($param, $delimiter = '|') {

        $return = $param;

        if(!empty($param)) {

            if(strpos($param, $delimiter) !== false) {
                $return = explode($delimiter, $param);
            }

        }

        return $return;

    }

    /**
     * Returns all post types added by the plugin.
     *
     * @since 1.0
     * @param string $src
     */
    public static function get_dates_between($start = null, $end = null, $interval = '+1 day', $format = null) {

        $all_dates = array();
    
        if($start && $end && $interval) {
    
            $start = new DateTime($start, wp_timezone());
            $end = new DateTime($end, wp_timezone());
            $end->modify($interval);
    
            $interval = DateInterval::createFromDateString($interval);
            $period = new DatePeriod($start, $interval, $end);
    
            // Loop through period
            if(!empty($period)) {
            
                foreach ($period as $date) {
    
                    if($format) {
                        $date = $date->format($format);
                    }
    
                    array_push($all_dates, $date);
                }
    
            }
    
        }
    
        return $all_dates;
    
    }

    public static function is_number_in_range($number, $range) {
        $range = explode('_', $range);
        $min = $range[0];
        $max = $range[1];
        return $number >= $min && $number <= $max;
    }

    /**
     * Converts minutes to seconds or seconds to minutes
     *
     * @param string $type
     * @param int|string $time
     */
    public static function convert_time_to($type = 'minutes', $time = null) {

        if(!$time) { return false; }

        switch ($type) {
            case 'seconds':
                $converted = $time * 60;
                break;
            
            case 'minutes':
                $converted = ceil($time/60);
                break;
            
            default:
                $converted = 0;
                break;
        }
        

        return $converted;

    }
    

    /**
     * Returns all post types added by the plugin.
     *
     * @since 1.0
     * @param string $src
     */
    public static function registered_post_types($exclude = array()) {

        $post_types = array();
        
        // Filter by generator.
        $args = array(
            'generator' => APT_SLUG
        );

        // Get smart post types.
        $get_post_types = get_post_types($args, 'names');

        if(!empty($get_post_types)) {

            if($exclude) {
                $get_post_types = array_diff($exclude, $get_post_types);
            }
        
            // Iterate through post types.
            foreach($get_post_types as $post_type ) {
                $post_types[] = $post_type;
            }

        }

        return $post_types;

    }

    /**
     * Geocode address
     *
     * @param  string $type
     * @param  int $item_id
     * @return array
     */
    public static function geocode($full_address, $action = null){

        // bail early if no address passed
        if(empty($full_address)) {
            return false;
        }
    
        // hacky hack
        if($action != 'geolocate') {
            $uk = '+United+Kingdom';
        }
    
        if($full_address === 'sw1' || $full_address === 'SW1') {
            $full_address = 'london sw1';
    
        } elseif($full_address === 'st pauls' || $full_address === 'st paul\'s') {
            $full_address = 'st pauls london';

        } elseif($full_address === 'St martin\'s' || $full_address === 'St Martin\'s') {
            $full_address = 'st martins';
    
        } elseif($full_address === 'farringdon') {
            $full_address = 'farringdon london';
        }
    
        // url encode the address
        $full_address = str_replace(" ", "+", urlencode($full_address)).$uk;
    
        if($full_address) {
            $url = 'https://maps.google.com/maps/api/geocode/json?&address='.$full_address.'&key='.APT_GOOGLE_GEOCODE_API_KEY;
    
            // get the json response
            $resp_json = file_get_contents($url);
    
            // decode the json
            $resp = json_decode($resp_json, true);
    
            // response status will be 'OK', if able to geocode given address
            if($resp['status'] === 'OK') {
    
                // get the important data
                $lati = $resp['results'][0]['geometry']['location']['lat'];
                $longi = $resp['results'][0]['geometry']['location']['lng'];
                $formatted_address = $resp['results'][0]['formatted_address'];
    
                // verify if data is complete
                if($lati && $longi && $formatted_address){
    
                    // put the data in the array
                    $data_arr = array();
    
                    array_push(
                        $data_arr,
                        $lati,
                        $longi,
                        $formatted_address
                    );
    
                    return $data_arr;
    
                }else{
                    return false;
                }
    
            }else{
                return false;
            }
        } else {
            return false;
        }

    }

    /** ------------------------------------------------------
     * 
     * WooCommerce Helpers
     * 
     *-------------------------------------------------------*/

}