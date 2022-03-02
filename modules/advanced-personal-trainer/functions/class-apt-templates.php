<?php
/**
 * APT Templates
 *
 * Class in charge of APT Templates
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APT_Templates {

    public function init() {

        add_filter('page_template', array($this, 'pages'));
        add_filter('single_template', array($this, 'single'));

        //add_action('template_redirect', array($this, 'block_access'));

    }

    public function pages($page_template) {
    
        // Clinic search
        if(is_page(array('workouts'))) {

            $page_template = APT_PATH . 'templates/workouts/workouts.php';

        }
    
        return $page_template;
    
    }

    public function single($single_template) {

        global $post;
        $post_type = $post->post_type;
        
        switch ($post_type) {
            case 'workout':
                $single_template = APT_PATH . 'templates/workouts/single-workout.php';
                break;
        }
    
        return $single_template;

    }

    // /**
    //  * Block access to some pages
    //  */
    // public function block_access() {

    //     if(is_singular('product')) {

    //         global $post;
    //         $product_id = $post->ID;
    //         $product = new APT_Product($product_id);

    //         if($product->is_appointment()) {
    //             wp_redirect(home_url() );
    //             exit();
    //         }

    //     }

    // }

}

