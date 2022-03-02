<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

/*
General Configuration File
*/

$db_server = "localhost";
$db_username = "wpdev2_bodyset";
$db_password = "6;OM%;(P8xQn";
$db_name = "wpdev2_bodysetv2";


$json = file_get_contents('php://input');
$req = json_decode($json);

if (!isset($json)){
    $res = [
        "status"  => -1,
        "message" => "No JSON Request supplied",
        "token" => "",
    ];
    echo json_encode($res);    
    die;
}

//include_once('init.php');
$action = "";
if (isset($req->action))
{
    $action = $req->action;
}
else
{
    $res = [
        "status"  => -1,
        "message" => "No Action set",
        "token" => "",
    ];
    echo json_encode($res);
}

switch ($action) {
    case "login":
        doLogin($req);
        break;
    case "register":
        doRegister($req);
        break;
    case "searchlink":
        doSearchLink($req);
        break;
    case "linkaccount":
        doLinkAccount($req);
        break;
    case "servicesbytype":
        doServicesByType($req);
        break;
    case "helpforscreen":
        doGetHelpForScreen($req);
        break;
    case "productsbyservice":
        doProductsByService($req);
        break;
    case "clinicsbyarea":
        $conn = databaseOpen($db_server, $db_username, $db_password, $db_name);
        doClinicsByArea($conn, $req);
        break;
    case "appointments":
        doAppointments($req);
        break;
    case "makebooking":
        doMakeBooking($req);
        break;
    default:
        break;
}

function databaseOpen($db_server, $db_username, $db_password, $db_name) {

    $conn = null;
    //echo("<br />connecting as <br />server: " .$db_server . "<br />user:" . $db_username . "<br />database:" . $db_name . "<br />password:" . $db_password . "<br />");
    
    // Create connection
    $conn = new mysqli($db_server, $db_username, $db_password, $db_name);
    
    // Check connection
    if ($conn->connect_error) {
        echo ("Connection failed: " . $conn->connect_error);
        return ($conn);
    }
    return ($conn);
}


// Standard Login to WordPress
function doLogin($req) {

    $user_name = $req->user_email;
    $user_psw = $req->user_psw;

    if (($user_name == 'alex@somewhere.com') && ($user_psw == 'letmein'))
    {
        $res = [
            "status"  => 0,
            "message" => "Login successful",
            "token" => "thisisamassivelongtoken.withaviewdotsin.andshit",
        ];
    }

    if (($user_name == 'dave@somewhere.com') && ($user_psw == 'letmein'))
    {
        $res = [
            "status"  => 1,
            "message" => "Login exists but not succesful",
            "token" => "",
        ];
    }

    if (($user_name == 'jason@somewhere.com') && ($user_psw == 'letmein'))
    {
        $res = [
            "status"  => 2,
            "message" => "Login successful but not linked with Lumeon",
            "token" => "thisisamassivelongtoken.withaviewdotsin.andshit",
        ];
    }

    if (($user_name == 'phil@somewhere.com') && ($user_psw == 'letmein'))
    {
        $res = [
            "status"  => 3,
            "message" => "Account does not exist, please register",
            "token" => "",
        ];
    }

    // Have we hit one of the conditions above? If not, we just say everything's good!
    if (!$res)
    {
        $res = [
            "status"  => 0,
            "message" => "Login successful",
            "token" => "thisisamassivelongtoken.withaviewdotsin.andshit",
        ];
    }

    echo json_encode($res);
}

// Run a Registration
function doRegister($req) {

    $user_name = $req->user_email;
    $user_psw = $req->user_psw;

    if ($user_name === "")
    {
        $res = [
            "status"  => -1,
            "message" => "User Name must be entered",
            "token" => "",
        ];
    }

    if ($user_psw == '')
    {
        $res = [
            "status"  => -1,
            "message" => "Password must be entered",
            "token" => "",
        ];
    }

    if ($user_name == "fail@somewhere.com")
    {
        $res = [
            "status"  => -1,
            "message" => "Account creation failed",
            "token" => "",
        ];
    }
    else
    {
        $res = [
            "status"  => 0,
            "message" => "Account created successfully",
            "token" => "",
        ];
    }
    echo json_encode($res);
}

function doSearchLink($req) {

    $address_pc = $req->pc;
    $birthdate = $req->birthdate;
    $email = $req->email;
    $family = strtolower($req->family);
    $gender = $req->gender;

    if ($family == "jones")
    {
        $res = [
            "status"  => 1,
            "message" => "Match found",
            "patient_id" => 76768762,
        ];
    }

    if ($family == "smith")
    {
        $res = [
            "status"  => 2,
            "message" => "Matches found",
            "patient_id" => 0,
        ];
    }

    if (!$res)
    {
        $res = [
            "status"  => 0,
            "message" => "No Matches found",
            "patient_id" => 0,
        ];
    }
    echo json_encode($res);
}

function doLinkAccount($req) {

}

function doServicesByType($req) {

    if ($req->service_type == "primary")
    {
        $res  = [
            ["name" => "Physio"], 
            ["name" => "chiropractic"], 
            ["name" => "osteothrapy"]  
         ]; 
    }
    else
    {
        $res  = [
            ["name" => "pilates"], 
            ["name" => "massage"], 
            ["name" => "bike assessment"],
            ["name" => "running assessment"] 
         ]; 
    }
    echo json_encode($res);
}


function doGetHelpForScreen($req){


}

function doProductsByService($req){


}

function doClinicsByArea($conn, $req) {

    $images = array(
        "https://www.bodyset.co.uk/wp-content/uploads/2017/10/iStock-489845146-e1580298038856.jpg", 
        "https://www.bodyset.co.uk/wp-content/uploads/2016/10/Chiswick-Physio.jpg", 
        "https://www.bodyset.co.uk/wp-content/uploads/2014/05/iStock-1146290684.jpg",
        "https://www.bodyset.co.uk/wp-content/uploads/2020/03/Bodyset-Physitrack-Telehealth-Consultation-scaled-1800x600.png"
    );

    // look for clinics within the selected radius
    $sql = "SELECT clinics.*, fl1_posts.post_title FROM (SELECT WP_ID, SQRT(POW(69.1 * (lat - $req->lat), 2) + POW(69.1 * ($req->long - lng) * COS(lat / 57.3), 2)) AS distance FROM clinic_locations HAVING distance < 10000) as clinics INNER JOIN fl1_posts ON fl1_posts.ID = clinics.WP_ID ORDER BY distance";
    $clinic_locations = $conn->query($sql);
    if ($clinic_locations->num_rows == 0) {
        echo("error executing " .$sql);
    }
    
    // if no clinics are found
    if(empty($clinic_locations)){

        // get the nearest clinic
        $nearest = 1;
        $sql = "SELECT WP_ID, SQRT(POW(69.1 * (lat - $req->lat), 2) + POW(69.1 * ($req->long - lng) * COS(lat / 57.3), 2)) AS distance FROM clinic_locations ORDER BY distance LIMIT 1";
        $clinic_locations = $conn->query($sql);
    }

    
    // split data into clinic IDs and distances
    $clinics = array();

    while($row = $clinic_locations->fetch_assoc()) {
      $clinics[] = array(
        'ID' => $row["WP_ID"],
        'name' => $row["post_title"],
        'image_url' => $images[rand(0,3)],
        'distance' => round($row["distance"], 1)
    );
    }
    /*
    foreach($clinic_locations as $clinic_location) {

        // get clinic services
        $clinic_products = get_field('clinic_products', $clinic_location['WP_ID']);

        if(!in_array($product_id, $clinic_products)) { continue; }

        $clinics[] = array(
            'ID' => $clinic_location['WP_ID'],
            'post_title' => get_the_title($clinic_location['WP_ID']),
            'distance' => round($clinic_location['distance'], 1)
        );
    }

    // PHP 7's usort magic!
    usort($clinics, function ($item1, $item2) {
        return $item1['post_title'] <=> $item2['post_title'];
    });
    return $clinics;
    */
    echo json_encode($clinics);
    //}
}

function doAppointments($req) {

}

function doMakeBooking($req) {


}

?>