<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
 *	GF AJAX Functions
 */

function gf_ajax_form() {

    /*
	/* Security check
	*/
	check_ajax_referer('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'security');

    $gf_id = $_GET['gf_id'];
    $event_id = $_GET['event_id'];

    // event data
    if( isset($_GET['event_id']) ) {
        $event_id = $_GET['event_id'];

        $_GET['gf_event_id'] = $event_id;
        $_GET['gf_event_date'] = the_event_date('j M Y', 'start', false, $event_id).' @ '.the_event_date('H:i', 'start_end', false, $event_id);
    }

    gravity_form( $gf_id, true, true, false, false, true );

    die();
}

add_action('wp_ajax_nopriv_gf_ajax_form', 'gf_ajax_form');
add_action('wp_ajax_gf_ajax_form' , 'gf_ajax_form');
