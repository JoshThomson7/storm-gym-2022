<?php
/**
 * APM Team
 *
 * Class in charge of team
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Practitioners {

    public function init() {

        add_action('wp_ajax_nopriv_apm_filter_practitioners', array($this, 'apm_filter_practitioners'));
        add_action('wp_ajax_apm_filter_practitioners', array($this, 'apm_filter_practitioners'));

    }

    /**
     * WP_Query
     * 
     * @param array $custom_args
     */
    public function get_practitioners($custom_args = array()) {

        $posts = array();

        $default_args = array(
            'post_type' => 'practitioner',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'ids'
        );

        $args = wp_parse_args($custom_args, $default_args);

        $posts = new WP_Query($args);
        return $posts->posts;

    }

    /**
     * Returns department terms
     */
    public function get_departments() {

        $terms = get_terms('department', array(
            'orderby'    => 'name',
            'order' => 'ASC',
            'hide_empty' => 1
        ));

        return $terms;

    }

    /**
     * Returns gender terms
     */
    public function get_genders() {

        $terms = get_terms('gender', array(
            'orderby'    => 'name',
            'order' => 'ASC',
            'hide_empty' => 1
        ));

        return $terms;

    }

    /**
     * Filter Team.
     *
     * @since	1.0
     */
    public function apm_filter_practitioners() {

        // Security check.
        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        parse_str($_POST['form_data'], $form_data);

        // Get data
        $department = isset($form_data['department']) && !empty($form_data['department']) ? $form_data['department'] : null;
        $gender = isset($form_data['gender']) && !empty($form_data['gender']) ? $form_data['gender'] : null;

        $args = array();

        if($department) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'department',
                    'terms' => $department,
                    'field' => 'term_id',
                    'operator' => 'IN'
                )
            );
        }

        $get_practitioners = $this->get_practitioners($args);
        
        include APM_PATH .'templates/practitioners/practitioners-loop.php';

        wp_die();

    }

}

