<?php
/**
 * APM Location
 *
 * Class in charge of single location/clinic
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Location {

    /**
	 * The post ID.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    protected $id;
    
    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @access public
	 * @param int $id
	 */
    public function __construct($id = null) {
        
        $this->id = $id;

    }

    /**
     * Gets post ID.
     * If not set, use global $post
     */
    public function id() {

        if($this->id) {

            return $this->id;

        } else {

            global $post;
            
            if(isset($post->ID)) {
                return $post->ID;
            }

        }

        return null;

    }

    /**
     * Returns post title
     */
    public function title() {

        $alt_title = get_field('menu_title', $this->id());
        $page_title = $alt_title ? $alt_title : get_the_title($this->id());

        return $page_title;

    }

    /**
     * Returns permalink
     */
    public function url() {

        return get_permalink($this->id());

    }

    /**
     * Returns location address, lat and lng
     * 
     * @return array
     */
    public function location($key = null, $break = false) {

        $location = get_field('clinic_address', $this->id());

        if(!empty($location) && is_array($location)) {

            if(isset($location[$key]) && !empty($location[$key])) {

                if($key === 'address' && $break) {
                    return str_replace(', ', '<br>', $location[$key]);
                }

                return $location[$key];
            }

        }

        return $location;

    }

    /**
     * Checks for a location
     * 
     * @return array
     */
    public function has_location() {

        if(!empty($this->location())) { 
            return true;
        }

        return false;

    }

    /**
     * has_times()
     * 
     * @param int $post_id
     * @return array
     */
    public function has_times() {

        if(have_rows('clinic_times', $this->id())) {
            return true;
        }

        return false;

    }

    /**
     * Returns an array of opening times
     * 
     * @param int $post_id
     * @return array
     */
    public function opening_times() {

        // Bail early if no times have been set.
        if(!$this->has_times()) { return; }

        $opening_times = array();
        
        // Loop through times.
        while(have_rows('clinic_times', $this->id())) {
            the_row();

            // Set up object.
            $todays_times = new stdClass();

            // Weekday.
            $weekday = get_sub_field('clinic_times_day');

            // Is it today?
            $today = new DateTime('now', wp_timezone());
            $today = $today->format('l');
            $is_today = '';
            if($today === $weekday) {
                $is_today = 'today ';
            }

            // Set weekday.
            $todays_times->weekday = array(
                'day' => $weekday,
                'is_today' => $is_today
            );
    
            // Get manually input time
            $times = get_sub_field('clinic_times_time');
            
            if(strpos($times, ' - ') !== false) {

                $times = explode(' - ', $times);
                $opening_time = $times[0];
                $closing_time = $times[1];

                $open_obj = new DateTime($opening_time, wp_timezone());
                $close_obj = new DateTime($closing_time, wp_timezone());
            
                // Convert times to timestamp for comparison.
                $open_stamp = $open_obj->getTimestamp();
                $close_stamp = $close_obj->getTimestamp();
                $close_obj->modify('-1 hour');
                $about_to_close = $close_obj->getTimestamp();
        
                // Handle past midnight.
                if($close_stamp < $open_stamp) {
                    $new_closing = new DateTime($open_obj->format('l '.$closing_time), wp_timezone());
                    $new_closing->modify('tomorrow');

                    $close_stamp = $new_closing->getTimestamp();
                    $new_closing->modify('-1 hour');
                    $about_to_close = $new_closing->getTimestamp();
                    
                    //$new_closing = date('l H:i', strtotime('tomorrow '.$today.' '.$closing_time));
                    // $close_stamp = strtotime($new_closing);
                    // $about_to_close = strtotime($new_closing.'- 1 hour');
                }

                // Set up opens array.
                $todays_times->opens = array(
                    'display_time' => $opening_time,
                    'timestamp' => $open_stamp
                );

                // Set up closes array.
                $todays_times->closes = array(
                    'display_time' => $closing_time,
                    'timestamp' => $close_stamp
                );
                
                // Get current time
                $time = time();

                // check it up
                if($opening_time === '' && $closing_time === '') {
                    $is_open = false;
        
                    $todays_times_today = $weekday;
        
                    $todays_times->status = array(
                        'text' => 'Closed',
                        'class' => 'closed'
                    );
        
                } else {
        
                    if( ($time <= $close_stamp) && ($time >= $open_stamp) ) {
        
                        $is_open = true;
        
                        if( ($time >= $about_to_close) && ($time < $close_stamp) ) {
        
                            $todays_times->status = array(
                                'text' => 'About to close',
                                'class' => 'about-to-close'
                            );
        
                        } else {
                            $todays_times->status = array(
                                'text' => 'Open',
                                'class' => 'open'
                            );
                        }
        
                    } else {
                        $todays_times->status = array(
                            'text' => 'Closed',
                            'class' => 'closed'
                        );
                    }

                }

                // Set up display array.
                $todays_times->display = '<span class="weekday">'.$weekday.'</span> <span class="times">'.$opening_time.' - '.$closing_time.'</span>';

                // Push the post to the main $post array
                array_push($opening_times, $todays_times);

            }

        }

        return $opening_times;
    
    }

    /**
     * Returns fees
     * 
     * @return array
     */
    public function fees() {

        return get_field('clinic_fees', $this->id());

    }

    /**
     * Returns lumeonid
     * 
     * @return array
     */
    public function lumeonid() {

        return get_field('clinic_lumeon_id', $this->id());
    }

    /**
     * Returns array of practitioner IDs
     * that work at this location
     * 
     * @return array
     */
    public function practitioners($location_id = null) {

        $practitioners = new APM_Practitioners();

        $location_id = $location_id ? $location_id : $this->id();

        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'team_locations',
                    'value' => '"' . $location_id . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
                    'compare' => 'LIKE'
                )
            )
        );

        return $practitioners->get_practitioners($args);


    }

    /**
     * Returns array of service IDs
     * attached to this location
     * 
     * @return array
     */
    public function services() {

        return get_field('clinic_services', $this->id());

    }

    /**
     * Returns array of product IDs
     * attached to this location
     * 
     * @return array
     */
    public function products() {

        return get_field('clinic_products', $this->id());

    }
    
    /**
     * Returns gender-related data
     * for this location
     * 
     * @return array
     */
    public function genders() {

        $genderData = array(
            'types' => array(),
            'count' => 0,
        );

        $practitioners = $this->practitioners();

        if(!empty($practitioners)) {

            $genders = array();

            foreach($practitioners as $practitioner_id) {
                
                $_practitioner = new APM_Practitioner($practitioner_id);
                $gender = $_practitioner->gender();

                if(!empty($gender)) {
                    $gender = reset($gender);
                    $gender = $gender->slug;
                    array_push($genders, $gender);
                }

            }
            
            if(!empty($genders)) {
                $genders = array_values(array_unique($genders));
                $genderData['types'] = $genders;
                $genderData['count'] = count($genders);
            }

        }

        return $genderData;

    }

}

