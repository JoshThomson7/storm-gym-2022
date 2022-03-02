<?php
class LumeonAPI extends GenericAPI
{
    public $practitioner_lookup = [];
    public $clinic_lookup = [];

    private function lumeon_mode() {

        return get_field('lumeon_mode', 'option');

    }

    private function lumeon_credentials($field = '') {

        $credentials = get_field('lumeon_'.$this->lumeon_mode(), 'option');

        if($credentials) {
            return $credentials[$field];
        }

        return $credentials;

    }
    
     /**
     *
     * Validates the Lumeon Connection options to ensure we're in a fit state to attempt to connecto to and use Lumeon
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function validatieOptions() {
        $this->options = array(
            'baseURL' => $this->lumeon_credentials('base_url'),
            'client_id' => $this->lumeon_credentials('client_id'),
            'client_secret' => $this->lumeon_credentials('client_secret'),
            'grant_type' => 'password',
            'username' => $this->lumeon_credentials('username'),
            'password' => $this->lumeon_credentials('password')
        );

        $isValid = true;
        $isValid &= isset($this->options["baseURL"]);
        $isValid &= isset($this->options["client_id"]);
        $isValid &= isset($this->options["client_secret"]);
        $isValid &= isset($this->options["grant_type"]);
        $isValid &= isset($this->options["username"]);
        $isValid &= isset($this->options["password"]);

        return ($isValid);
    }

    /**
     *
     * Authenticates a User with Lumeon
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function authenticateUser($user_name = null, $user_pass = null){

        $success = false;

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions())
        {
            // Take a snapshot of what's happened for troubleshooting
            $this->LogEvent('authenticateUser', 'validatieOptions is false', 'api');

            return(false);
        }

        // If we've passed credentials, over ride the defaults
        if ($user_name == ''){
            $user_name  = $this->options["username"];
        }

        if ($user_pass == ''){
            $user_pass  = $this->options["password"];
        }

        $url = $this->options["baseURL"] .'oauth/v2/token';
        $fields = array(
            'client_id'     => urlencode($this->options["client_id"]),
            'client_secret' => urlencode($this->options["client_secret"]),
            'grant_type'    => urlencode($this->options["grant_type"]),
            'username'      => urlencode($user_name),
            'password'      => urlencode($user_pass)
        );

        // URLify the post options
        $fields_string = $this->Urlify($fields);

        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $fields_string, 'api');

        // open connection
        $ch = curl_init();

        // set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    
        // execute post
        $result = curl_exec($ch);
        
        $ret = json_decode($result);
        $this->LogEvent($url, $result, 'lumeon');

        // Pull out the Authentication Token
        if (isset($ret->{'error'}))
        {
            $this->auth_token = '';
            $this->last_error = $ret->{'error_description'};
        }
        else{
            $this->auth_token = $ret->{'access_token'};

            $this->setSessionLMAuthKey($this->auth_token);            
            $success = true;
        }
        
        //close connection
        curl_close($ch);
        return ($success);
    }

    /**
     *
     * Loads an individual Patient Record From Lumeon
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function patientGetById($id){

        $ret = null;

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions()){return(false);}

        $url = $this->options["baseURL"] .'api/fhir/dstu2/Patient/'.$id;
        $fields = array(
            'id'     => urlencode($id)
        );

        // URLify the post options
        $fields_string = $this->Urlify($fields);

        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $fields_string, 'api');

        // open connection
        $ch = curl_init();
        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute post
        $result = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode == 404) {
            // Patient ID doesn't exist!
            $this->LogEvent($url, 'Patient ID '.$id.' does not exist' , 'lumeon');
        }
        else
        {
            $success = true;
            $ret = json_decode($result);
        }
        
        //close connection
        curl_close($ch);
        return ($ret);
    }

    /**
     *
     * Searches for a Patient record within Lumeon
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function patientSearch($addr_pc = '', $dob ='', $family = '', $email = '', $gender = ''){

        $ret = null;

        // Post Codes need to be lower case and no space
        if ($addr_pc != ''){
            $addr_pc = strtolower($addr_pc);
            $addr_pc = str_replace($addr_pc, ' ', '');
        }

        // Check we can work with this the Date format, otherwise we'll get no results! Log this so we can diagnise this remotely if we need to!
        if ($dob != '')
        {
            if (!$this->isValidLumeonDate($dob)) {
                $this->LogEvent('', 'Date of Birth '.$dob.' not valid', 'api');
                return ($ret);
            }
        }

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions()){return(false);}

        $fields = array(
            '_query'                => 'mpi',
            'address-postalcode'    => urlencode($addr_pc),
            'birthdate'             => urlencode($dob),
            'family'                => urlencode($family),
            'email'                 => urlencode($email),
            'gender'                => urlencode($gender)
        );

        // URLify the post options
        $fields_string = $this->Urlify($fields);
        $url = $this->options["baseURL"] .'api/fhir/dstu2/Patient?'.$fields_string;

        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $fields_string, 'api');

        // open connection
        $ch = curl_init();

        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute post
        $result = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode != 200) {
            // Patient ID doesn't exist!
            $this->LogEvent($url, 'Unexpected Response Code ' .$httpCode, 'lumeon');
        }
        else
        {
            $ret = json_decode($result);
            $this->LogEvent($url, $result, 'lumeon');
        }
        
        //close connection
        curl_close($ch);
        return ($ret);
    }

    /**
     *
     * Retrieves all/a specific Location with Lumeon
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function locationSearch($location_name = '') {

        $ret = null;

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions()){return(false);}

        $fields = array(
            'name-postalcode'       => urlencode($location_name)
        );

        // URLify the post options
        $fields_string = $this->Urlify($fields);
        $url = $this->options["baseURL"] .'api/fhir/dstu2/Location?'.$fields_string;

        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $fields_string, 'api');

        // open connection
        $ch = curl_init();

        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute post
        $result = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode != 200) {
            // Patient ID doesn't exist!
            $this->LogEvent($url, 'Unexpected Response Code ' .$httpCode, 'lumeon');
        }
        else
        {
            $ret = json_decode($result);
            $this->LogEvent($url, $result, 'lumeon');
        }
        
        //close connection
        curl_close($ch);
        return ($ret);
    }

    /**
     *
     * Loads Appointment Types from Lumeon, these are in effect the Types of Product in Bodyset and determine the duration used in Booking
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function appointmentTypes($patient_id){

        $ret = null;

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions()){return(false);}
        
        $fields = array(
            'patient'       => urlencode($patient_id),
            'servicetype'   => 4,
            'payor'         => urlencode($patient_id)
        );

        // URLify the post options
        $fields_string = $this->Urlify($fields);
        $url = $this->options["baseURL"] .'api/fhir/dstu2/HealthcareService?'.$fields_string;

        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $fields_string, 'api');

        // open connection
        $ch = curl_init();

        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute post
        $result = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode != 200) {
            // Patient ID doesn't exist!
            $this->LogEvent($url, 'Unexpected Response Code ' .$httpCode, 'lumeon');
        }
        else
        {
            $ret = json_decode($result);
            $this->LogEvent($url, $result, 'lumeon');
        }
        
        //close connection
        curl_close($ch);
        return ($ret);
    }

    /**
     *
     * Quick Search Helper function that uses all defaults and searches by Patiet and Location(s)
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function appointmentQuickSlotSearch($appt_type, $patient_id, $location_ids)
    {
        return ($this->appointmentSlotSearch($appt_type, $patient_id, $location_ids, '', '', 'all', '1', '', '', '1'));
    }

     /**
     *
     * Validates Date format to ensure Compatibilitt with Lumeon Date handling
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function isValidLumeonDate($date)
    {
        return (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date));
    }

     /**
     *
     * Loads a Practioner record from Lumeon
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function getPractitionerByLumeonId($id)
    {
        $ret = null;

        // Do we already have this practioner in our lookup?
        if (isset($this->practitioner_lookup[''. $id .'']))
        {
            $ret = $this->practitioner_lookup[''. $id .''];
            return ($ret);
        }

        // open connection
        $ch = curl_init();
        $authorization = "Authorization: Bearer ";

        $url = APM_REST_API_URL.'practitioners/?lumeon_id='.$id;
        
        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute post
        $result = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode != 200) {
            // Patient ID doesn't exist!
            $this->LogEvent($url, 'Unexpected Response Code ' .$httpCode, 'WordPress');
        }
        else
        {
            $this->LogEvent($url, $result, 'WordPress');
            $ret = json_decode($result);

            // Add them to the Lookup array
            $this->practitioner_lookup[''. $id .''] = $ret;
        }
        
        //close connection
        curl_close($ch);
        return ($ret);
    }

    // function fillCalendarDates($start_date, $start_time, $end_time, $days_ahead = 5, $minute_interval= 30)
    // {
    //     $appts = array();
        
    //     $date = $start_date;
    //     for ($day = 0; $day < $days_ahead; $day++) {
        
    //         $time_string = date('Y-m-d H:i:s', strtotime("$date $start_time"));
    //         $end = date('Y-m-d H:i:s', strtotime("$date $end_time"));
            
    //         // Loop through incrementing by each time interval
    //         while ($time_string != $end)
    //         {
    //             $slot_end = date("Y-m-d H:i:s", strtotime($time_string . '+' . $minute_interval . ' minutes'));
    //             $id = date('YmdHis', strtotime($time_string));

    //             $appt = array(
    //                 'title' => date('H:i', strtotime($time_string)),
    //                 'start' =>$time_string,
    //                 'end' => $slot_end,
    //                 'lm_slot_id' => '',
    //                 'lm_location_id' => '',
    //                 'lm_location_name' => '',
    //                 'lm_practitioner_id' => '',
    //                 'lm_practitioner_name' => '',
    //                 'wp_practitioner_img' => '',
    //                 'wp_practitioner_id' => '',
    //                 'status' => 'booked',
    //                 'id' => $id,
    //                 'eventBackgroundColor' => "",
    //                 'eventBorderColor' => "",
    //                 'eventTextColor' => "",
    //                 'selected' => false,
    //             );

    //             array_push($appts, $appt);
    //             $time_string = date("Y-m-d H:i:s", strtotime($time_string . '+' . $minute_interval . ' minutes')); 
    //         }

    //         $date = date("Y-m-d", strtotime($date . '+1 day'));
    //     }

    //     return $appts;
    // }

    

    /**
     *
     * Helper Function: Loads JSON data from a File
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function JSONFromFile($file_name)
    {
        $data = null;
        $path = APM_URL.'functions/rest-api/endpoints/lumeon/'.$file_name;
        try {
            // $data_file = fopen($url, "r");
            // $data = fread($data_file, filesize($file_name));
            // fclose($data_file);
            $data = file_get_contents($path);

        } catch (Exception $e) {
            $this->LogEvent('$function JSONFromFile: lumeon.php', 'Caught exception '. $e->getMessage(), 'WordPress');
        }
        return ($data);
    }

    /**
     * Creates calendar slot skeleton
     * 
     * @param string $start
     * @param string $end
     */
    function calendarSlotSkeleton($start, $end, $slot_length) {

        $slots = array();

        $dates_between = APM_Helpers::get_dates_between($start, $end, '+1 day', 'Y-m-d');

        if(!empty($dates_between)) {

            foreach($dates_between as $date) {

                $times_between = APM_Helpers::get_dates_between('08:00', '18:00', '+'.$slot_length.' min', 'H:i');
                
                if(!empty($times_between)) {
                
                    foreach($times_between as $time) {

                        $startString = $date.' '.$time;

                        $startDate = new DateTime($startString, wp_timezone());

                        $endDate = new DateTime($date.' '.$time, wp_timezone());
                        $endDate->modify('+30 min');

                        $slot = array(
                            'id' => $startDate->format('YmdHi'),
                            'title' => $time,
                            'start' => $startDate->format('Y-m-d H:i'),
                            'end' => $endDate->format('Y-m-d H:i'),
                            'status' => 'booked'
                        );

                        array_push($slots, $slot);

                    }

                }

            }

        }

        return $slots;

    }

    /**
     *
     * Takes appointments from Lumeon and parses them to keep only the data needed and in a simplied format
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function processAppointments($data, $slot_length)
    {
        $appts = array();
        $now = new DateTime('now', wp_timezone());
        
        //$json = $this->JSONFromFile("appointments.json");
        //$data = json_decode($json);
        if (isset($data))
        {
            $last_lm_practitioner_id = 0;
            $wp_practitioner_img = '';
            $wp_practitioner_id = 0;

            foreach($data->entry as $appt) {

                // Get our start DateTime going
                $startDate = new DateTime($appt->resource->start, wp_timezone());

                // Skip past, quarter past and quarter to the hour slots
                if($now->getTimestamp() >= $startDate->getTimestamp() || $startDate->format('i') === '15' || $startDate->format('i') === '45') { 
                    continue;
                }

                // Work out a new end date from $slot_length
                $endDate = new DateTime($startDate->format('Y-m-d H:i'), wp_timezone());
                $endDate->modify('+'.$slot_length.' minutes');

                $lm_practitioner_id = str_replace('Practitioner/', '', $appt->resource->extension[3]->valueReference->reference);

                if ($last_lm_practitioner_id != $lm_practitioner_id) {
                    $practitioner = $this->getPractitionerByLumeonId($lm_practitioner_id);
                    $wp_practitioner_img = isset($practitioner) ? $practitioner[0]->picture->url : '';
                    $wp_practitioner_id = isset($practitioner) ? $practitioner[0]->ID : '';
                    $last_lm_practitioner_id = $lm_practitioner_id;
                }

                // Build our appointment array
                $appt = array(
                    'id'                    => $startDate->format('YmdHi'),
                    'title'                 => $startDate->format('H:i'),
                    'start'                 => $startDate->format('Y-m-d H:i'),
                    'end'                   => $endDate->format('Y-m-d H:i'),
                    'status'                => 'available',
                    'selected'              => false,
                    'lm_slot_id'            => $appt->resource->id,
                    'lm_location_id'        => str_replace('Location/', '', $appt->resource->extension[1]->valueReference->reference),
                    'lm_location_name'      => $appt->resource->extension[1]->valueReference->display,
                    'lm_practitioner_id'    => $lm_practitioner_id,
                    'lm_practitioner_name'  => $appt->resource->extension[3]->valueReference->display,
                    'wp_practitioner_img'   => $wp_practitioner_img,
                    'wp_practitioner_id'    => $wp_practitioner_id,
                    'eventBackgroundColor'  => '',
                    'eventBorderColor'      => '',
                    'eventTextColor'        => ''
                );

                array_push($appts, $appt);
            }
        }

        return $appts;
    }

    /**
     * Intersects arrays and if matches are found,
     * replace slot array with Lumeon's. Also Deals
     * with multiple availability.
     * 
     * @param array $appts
     * @param array $slotSkeleton
     */ 
    function calendarSlots($appts, $slotSkeleton) {

        if(empty($slotSkeleton) && empty($appts)) { return null; }

        foreach($slotSkeleton as $key => $slot) {
            
            $prevSlot = null;

            foreach($appts as $appt) {
                
                if($slot['id'] == $appt['id']) {
                    // Have we got multiple availability?
                    if(!empty($prevSlot) && isset($prevSlot['id']) && $prevSlot['id'] == $appt['id']) {
                        $slotSkeleton[$key]['multiple'][] = $appt;
                    } else {
                        $slotSkeleton[$key] = $appt;
                    }
                }

                $prevSlot = $appt;

            }

        }

        return $slotSkeleton;

    }

    /**
     *
     * Loads available slots from Lumeon, then processes them and parses them to return an array of booked and available slots
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function appointmentSlotSearch($appt_type = '', $patient_id, $location_ids, $date_from = '', $date_to = '', $search_type = 'all', $weeks = '1', $practitioner = '', $gender = '', $page = '1', $slot_length = 30) {

        $ret = null;
        $calSlots = array();
        
        // Post Codes need to be lower case and no space
        if ($date_from === ''){
            $date_from = date('Y-m-d', strtotime('monday this week'));
        }

        if (!$this->isValidLumeonDate($date_from)) {
            $this->LogEvent('', 'Date from not valid '.$date_from.' not valid', 'api');
            return ($ret);
        }

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions()){return(false);} 

        $fields = array(
            'appointment_type'      => urlencode($appt_type),
            'upgrade_to'            => '3',
            'patient'               => urlencode($patient_id),
            'location[]'            => urlencode($location_ids),
            'date_from'             => $date_from,
            'show'                  => $search_type,                // Search Types are 'all' or 'best'
            'weeks'                 => urlencode($weeks),           // How many weeks ahead to search
            'practitioner'          => urlencode($practitioner),
            'practitioner_sex'      => $gender,
            'page'                  => urlencode($page),
            'payor'                 => $patient_id,
        );

        // URLify the post options
        $fields_string = $this->Urlify($fields);
        $url = $this->options["baseURL"] .'api/fhir/dstu2/Slot?'.$fields_string;

        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $fields_string, 'api');

        // open connection
        $ch = curl_init();
        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute post
        $result = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode != 200) {
            // Patient ID doesn't exist!
            $this->LogEvent($url, 'Unexpected Response Code ' .$httpCode, 'lumeon');
        }
        else
        {
            $this->LogEvent($url, $result, 'lumeon');
            $ret = json_decode($result);

            $appts = $this->processAppointments($ret, $slot_length);
            $slotSkeleton = $this->calendarSlotSkeleton($date_from, $date_to, $slot_length);
            $calSlots = $this->calendarSlots($appts, $slotSkeleton);

        }
        
        $this->LogEvent($url, 'Appointment Processing Finished', 'api');

        //close connection
        curl_close($ch);

        return $calSlots;
    }

    /**
     *
     * Returns an addresses formatted corrected in single line format for lumeon
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function createAdderessLine($address_1, $address_2, $address_posttown, $address_county, $address_postcode, $address_country)
    {
        $address = '';
        if (isset($address_1) && ($address_1 != '')){
            $address.= $address_1.' ';
        }
        if (isset($address_2) && ($address_2 != '')){
            $address.= $address_2.' ';
        }
        if (isset($address_posttown) && ($address_posttown != '')){
            $address.= $address_posttown.' ';
        }
        if (isset($address_county) && ($address_county != '')){
            $address.= $address_county.' ';
        }
        if (isset($address_postcode) && ($address_postcode != '')){
            $address.= $address_postcode.' ';
        }
        if (isset($address_country) && ($address_country != '')){
            $address.= $address_country.' ';
        }
        rtrim($address, ' ');
    }

    /**
     *
     * Creates a Patient Record in Lumeon format
     *
     * @param    object  $object The object to convert
     * @return      boolean
     *
     */
    function createPatient ($patient_data)
    {
        $success = false;

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions())
        {
            return(false);
        }

        // Load in the JSON Template for the new patient post
        $JSON = $this->JSONFromFile("lumeon_patient.json");
        
        // List of fieldds we need to check against
        $required_fields = array("patient_surname", "patient_firstname", "patient_email", "patient_dob");

        $has_errors = false;
        foreach($patient_data as $key=>$value) 
        { 
            // Check required fields
            if (in_array($key, $required_fields, true))
            {
                // Check to see if thes
                if (!isset($value) || ($value === ''))
                {
                    $this->LogEvent('', 'Patient  '.$key.' not entered', 'api');
                    $has_errors = true;
                    break;
                }
            }

            // If this is a date field, check for a valid format
            if ($key === 'patient_dob')
            {
                if (!$this->isValidLumeonDate($value)) {
                    $this->LogEvent('', 'Date of Birth '.$value.' not valid', 'api');
                    $has_errors = true;
                    break;
                }
            }
            
            // Parse out the variable with the new value
            $JSON = str_replace('{'.$key.'}', $value, $JSON);
        }

        // If there are errors, don't bother continuing
        if ($has_errors)
            return ($success);
        
        $url = $this->options["baseURL"] .'api/fhir/dstu2/Patient';
        
        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $JSON, 'api');

        // open connection
        $ch = curl_init();

        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        
        // set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $JSON);
    
        // execute post
        $result = curl_exec($ch);

        $info = curl_getinfo($ch);
        $ret = json_decode($result);
        $this->LogEvent($url, $result, 'lumeon');

        // Test the Response to see if this worked
        $this->LogEvent('function createPatient', $ret->issue[0]->diagnostic, 'lumeon');
        if (($ret) && ($ret->issue[0]->diagnostic == "Patient created")) {
            // Now we're going to go find the Patient to get the ID!
            $patientSearch = $this->patientSearch(
                    $patient_data["patient_addr_postcode"], 
                    $patient_data["patient_dob"], 
                    $patient_data["patient_surname"], 
                    $patient_data["patient_email"], 
                    $patient_data["patient_gender"]);

            if ((!empty($patientSearch)) && ($patientSearch->total == 1)) {
                $lumeon_id = $patientSearch->entry[0]->resource->identifier[0]->value;
                $this->LogEvent('function createPatient', 'Patient '. $patient_data->patient_firstname . ' '. $patient_data->patient_surname . ' Created as ID '. $lumeon_id, 'WordPress');
                return ($lumeon_id);
            }
        }
        
        //close connection
        curl_close($ch);
        return ($success);
    }

    function appointmentCreate($appt_data)
    {
        $success = false;

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions())
        {
            return(false);
        }

        // Load in the JSON Template for the new patient post
        $JSON = $this->JSONFromFile("lumeon_booking.json");
        
        // List of fieldds we need to check against
        $required_fields = array("lumeon_patient_id", "lumeon_service_id", "lumeon_slot_id", "lumeon_type_code");

        $has_errors = false;
        foreach($appt_data as $key=>$value) 
        { 
            // Check required fields
            if (in_array($key, $required_fields, true))
            {
                // Check to see if thes
                if (!isset($value) || ($value === ''))
                {
                    $this->LogEvent('', 'Booking Data  '.$key.' not entered', 'api');
                    $has_errors = true;
                    break;
                }
            }
            
            // Parse out the variable with the new value
            $JSON = str_replace('{'.$key.'}', $value, $JSON);
        }

        // If there are errors, don't bother continuing
        if ($has_errors)
            return ($success);
        
        $url = $this->options["baseURL"] .'api/fhir/dstu2/Appointment';
        
        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $JSON, 'api');

        // open connection
        $ch = curl_init();

        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        
        // set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $JSON);
    
        // execute post
        $result = curl_exec($ch);

        $info = curl_getinfo($ch); 
        $ret = json_decode($result);
        $this->LogEvent($url, $result, 'lumeon');
        
        //close connection
        curl_close($ch);
        return ($ret);
    }

    function appointmentSetStatus($appt_data)
    {
        $success = false;

        // Check we have all the necessary parameters to continue
        if (!$this->validatieOptions())
        {
            return(false);
        }

        // Load in the JSON Template for the new patient post
        $JSON = $this->JSONFromFile("lumeon_booking_status.json");
        
        // List of fieldds we need to check against
        $required_fields = array("reservation_id");

        $reservation_id = $appt_data['reservation_id'];

        $has_errors = false;
        foreach($appt_data as $key=>$value) 
        { 
            // Check required fields
            if (in_array($key, $required_fields, true))
            {
                // Check to see if thes
                if (!isset($value) || ($value === ''))
                {
                    $this->LogEvent('', 'Booking Data  '.$key.' not entered', 'api');
                    $has_errors = true;
                    break;
                }
            }
            
            // Parse out the variable with the new value
            $JSON = str_replace('{'.$key.'}', $value, $JSON);
        }

        // If there are errors, don't bother continuing
        if ($has_errors)
            return ($success);
        
        $url = $this->options["baseURL"] .'api/fhir/dstu2/Appointment/'.$reservation_id;
        
        // Take a snapshot of what's happened for troubleshooting
        $this->LogEvent($url, $JSON, 'api');

        // open connection
        $ch = curl_init();

        $authorization = "Authorization: Bearer ". $this->getAuthToken();

        // set the url, This is a GET request so nice and simple
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        
        // set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $JSON);

        // execute post
        $result = curl_exec($ch);

        $info = curl_getinfo($ch); 
        $ret = json_decode($result);
        $this->LogEvent($url, $result, 'lumeon');
        
        //close connection
        curl_close($ch);
        return ($ret);
    }
}

// global $wpdb;

// // Create a new instance of the Lumeon API
// $lumeon = new LumeonAPI;
// $lumeon->wpdb = $wpdb;
// $lumeon->SetSession(593);

// $data = $lumeon->appointmentSlotSearch(92533, 83, '2020-08-03', '2020-08-08');
// pretty_print($data);