<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Advanced Testimonials
 *
 * AT Functions
 *
 * @package Advanced Testimonials
 * @version 1.0
*/

require_once('inc/at-post-type.php');

/*--------------------------------------------------------------------------*/
/*    function at_path()
/*    returns the full AT path
/*--------------------------------------------------------------------------*/
function at_path($at_url = false) {

    if($at_url) {
        $at_path = get_stylesheet_directory_uri()  . '/modules/advanced-testimonials/';
    } else {
        $at_path = get_stylesheet_directory()  . '/modules/advanced-testimonials/';
    }

    return $at_path;
}
?>
