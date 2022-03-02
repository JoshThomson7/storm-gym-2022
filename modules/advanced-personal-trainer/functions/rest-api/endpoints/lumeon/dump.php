
<?php

include_once('../../../wp-load.php' );
include_once('./lumeon.php' );

function wp_authenticate( $username, $password ) {
    $username = sanitize_user( $username );
    $password = trim( $password );
 
    $user = apply_filters( 'authenticate', null, $username, $password );
 
    if ( null == $user ) {
        $user = new WP_Error( 'authentication_failed', __( '<strong>Error</strong>: Invalid username, email address or incorrect password.' ) );
    }
 
    $ignore_codes = array( 'empty_username', 'empty_password' );
 
    if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
        $error = $user;
    }
 
    return $user;
}

global $wpdb;
//$post = $wpdb->get_row("SELECT * FROM $wpdb->posts");
 
$user_name = 'dave';
$user_pass = 'Letmetest123!';

$user = wp_authenticate($user_name, $user_pass);
if(is_wp_error($user)) {
    echo $user->get_error_message();
} else {
    echo 'Logged in as ' .$user->user_nicename .'<br />';
}

$user_id =  username_exists($user_name);
if (false === $user_id)
{
    echo 'user ' . $user_name . ' does not exist<br />';
    $user_id = wp_create_user($user_name, $user_pass, $user_name.'@fl1.digital');
    if(is_wp_error($user_id)) {
        echo 'Could not create user ' . $user_name . '<br />';   
    }
    else{
        echo 'User ' . $user_name . ' created<br />';   
    }
}
else 
{
    echo 'user ' . $user_name . ' exists! and the ID is '.$user_id.'<br />';
}

$url = get_option('lumeon_url', 'http://www.fl1digital.com');
update_option('lumeon_url', 'https://capitalphysiotest.lumeon.com/module/', false);

$options = array(
    'baseURL' =>'https://capitalphysiotest.lumeon.com/module/',
    'client_id' => '2_ouwdpauxtao88c4g08ggwg44s4sc0w8ogsgso8cs08gcc0kwk',
    'client_secret' => 'ysz5nvvdzfkg4k0o0ksg4gw0oo4o4s0c48sosggkc8w88gco0',
    'grant_type' => 'password',
    'username' => 'jason@fl1.digital',
    'password' => 'G1veMeFuck1ngData!'
);

$test_auth = true;
$test_patentGet = false;
$test_patentSearch = false;
$test_clinicSearch = false;
$test_apptSearch = true;
$test_patientCreate = false;
$test_appt_types = false;
$test_appt_fill = false;
$test_appt_new = false;


$lumeon = new LumeonAPI;
$lumeon->options = $options;
$lumeon->wpdb = $wpdb;

echo 'Started Session: '. $lumeon->NewSession() .'<br />';

// Display the options for debugging
//$lumeon->dumpOptions();
if ($test_auth)
{
    if (!$lumeon->authenticateUser())
    {
        echo $lumeon->last_error;
    }
    else{
        echo 'Yay, we good and all logged in!<br />';   
    }
    }

// Let's go get a patient!
if ($test_patentGet)
{
    $data = $lumeon->patientGetById(502397);
    if (isset($data))
    {
        $full_name = $data->name[0]->given[0] . ' ' . $data->name[0]->family[0];
        echo 'Lumeon has on record ' . $full_name;

        print_r($data);
    }
    else{
        echo 'Aww man they don\'t know this guy!';
    }
}

if ($test_patentSearch)
{
    // Let's see if we can find our Mr Sammon
    $data = $lumeon->patientSearch('CV35 7TS', '1973-09-17', 'Sammon');
    if (isset($data))
    {
        echo 'We found '.$data->total.' records for you in Lumeon';
    }
    else{
        echo 'Aww man they can\'t find this guy!';
    }
}

if ($test_clinicSearch)
{
    // OK, let's see if we can get some locations out of Lumeon!
    $data = $lumeon->locationSearch();
    if (isset($data))
    {
        echo 'We found '.$data->total.' records for you in Lumeon';

        foreach($data->entry as $location) 
        { 
            echo '<br />'. $location->resource->name . ' ID: '. $location->resource->id;
        }
    }
    else{
        echo 'Aww man they can\'t find this guy!';
    }
}

if ($test_apptSearch)
{
    // Test the Appointment Processing Routine
    $data = $lumeon->appointmentSlotSearch('', 502397, 114);
    if (isset($data))
    {
        $json = json_encode($data);
        echo $json;
        /*
        echo 'We found '.$data->total.' available slots for you in Lumeon';

        foreach($data->entry as $location) { 
            echo '<br />'. $location->resource->start . ' end: '. $location->resource->end;
        }
        */
    }
    else{
        echo 'Aww man they don\'t have any appointments';
    }
}
if ($test_patientCreate)
{
    $fields = array(
        'patient_title'         => 'Mr',
        'patient_firstname'     => 'Hermann',
        'patient_surname'       => 'Hauser',
        'patient_phone_work'    => '01727 739812',
        'patient_phone_mobile'  => '07977 123123',
        'patient_email'         => 'hermann@acorncomputers.com',  
        'patient_gender'        => 'male',           
        'patient_dob'           => '1948-10-23',
        'patient_addr_1'        => '10 High Street',
        'patient_addr_2'        => '',
        'patient_addr_city'     => 'Cambridge',
        'patient_addr_county'   => 'Cambridgeshire',
        'patient_addr_postcode' => 'CB1 6YT',
        'patient_addr_country'  => 'UK',
    );

    $data = $lumeon->createPatient($fields);
}

if ($test_appt_types)
{
    $data = $lumeon->appointmentTypes(502397);
    if (isset($data))
    {
        print_r($data);
        /*
        foreach($data->entry as $location) { 
            echo '<br />'. $location->resource->start . ' end: '. $location->resource->end;
        }
        */
    }
    else{
        echo 'Aww man they don\'t have any appointment types';
    }
}

//$test_appt_fill = false;
if ($test_appt_fill)
{
    // Fill up an array with empty time slots
    $empty_slots = $lumeon->fillCalendarDates(date('Y-m-d', strtotime('2020-07-21')), "09:00", "17:00");
    if (isset($empty_slots))
    {
        // Get Real time slots in the format we want
        $available_slots = $lumeon->processAppointments(/*$ret*/);
        if (isset($available_slots))
        {
            echo '<br /><br /><br /><br />';
            // Merge the two to give us a final list of free and booked appointment slots
            $appointments = array_merge($empty_slots, $available_slots);
            //print_r($appointments);
            echo json_encode($appointments);
        }
    }
}

if ($test_appt_new)
{
    $fields = array(
        'lumeon_patient_id'         => 502397,
        'lumeon_patient_name'       => 'Hermann Hauser',
        'lumeon_service_id'         => 'AT218',
        'lumeon_service_name'       => 'Physio',
        'lumeon_slot_id'            => '4834.220253',
        'lumeon_type_code'          => '17',
    );

    echo 'calling appointmentCreate<br/>';
    $data = $lumeon->appointmentCreate($fields);
    if (isset($data))
    {

        print_r($data);
        /*
        foreach($data->entry as $location) { 
            echo '<br />'. $location->resource->start . ' end: '. $location->resource->end;
        }
        */
    }
    else{
        echo 'Aww man they don\'t wanna book yo ass!';
    }
}
?>