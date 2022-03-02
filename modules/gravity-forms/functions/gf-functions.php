<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
/* Gravity Forms custom functions
*/

require_once('inc/gf-ajax.php');
//require_once('inc/gf-fields.php');

function gf_ajax_form_html() {

    gravity_form_enqueue_scripts(2, true);
    ?>

    <div class="gf__modal__form__overlay">
        <div class="gf__modal__form">
            <a href="#" class="close"><span class="ion-android-close"></span></a>

            <?php wp_spinner(true); ?>

            <div id="gf_ajax"></div>
        </div><!-- gf__modal__form -->
    </div><!-- gf__modal__form__overlay -->

    <?php
}

// This filter can be used to change the default AJAX spinner image
add_filter( 'gform_ajax_spinner_url', 'gf_spinner_url', 10, 2 );
function gf_spinner_url( $image_src, $form ) {
    return get_stylesheet_directory_uri().'/modules/gravity-forms/img/gf-loader.svg';
}
