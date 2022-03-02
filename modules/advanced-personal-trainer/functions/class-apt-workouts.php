<?php
/**
 * APT Workouts
 *
 * Class in charge of workouts
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APT_Workouts {

    public function init() {

        //add_action('acf/prepare_field/key=field_6152e4e639c41', array($this, 'prepare_video_field'));

        add_action('wp_ajax_nopriv_apt_filter_workouts', array($this, 'apt_filter_workouts'));
        add_action('wp_ajax_apt_filter_workouts', array($this, 'apt_filter_workouts'));

        add_action('acf/save_post', array($this, 'on_save_workout'), 20);
        add_filter('acf/prepare_field/name=workout_json', array($this, 'workout_json_acf'));
        add_filter('acf/prepare_field/name=workout_duration', array($this, 'workout_duration_acf'));

    }

    /**
     * WP_Query
     * 
     * @param array $custom_args
     */
    public function get_workouts($custom_args = array()) {

        $posts = array();

        $default_args = array(
            'post_type' => 'workout',
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
     * Returns all the available taxonomy terms
     * 
     * @param string $taxonomy
     * @param array $custom_args
     */
    public function get_tax_terms($taxonomy, $custom_args = array()) {

        if(!$taxonomy) { return false; }

        $default_args = array(
            'hide_empty' => 0
        );

        $args = wp_parse_args($custom_args, $default_args);
        $terms = get_terms($taxonomy, $args);

        return $terms;

    }

    public function prepare_video_field($field) {

        // Lock-in the value "Example".
        $field = array(
            'id' => $field['value'],
            'url' => 'https://player.vimeo.com/video/'.$field['value'],
            'platform' => 'vimeo'
        );

        return $field;
    }

    /**
     * Prepares field to display its REST API JSON data
     * 
     * @param array $field
     * @see https://github.com/bitterendio/acf-field-type-json
     */
    public function workout_json_acf($field) {

        if(is_admin()) { 
            global $post;
            $workout_id = $post->ID;
            if(get_post_type($workout_id) === 'workout'){

                $workout = new APT_Workout($workout_id);
                $json = json_encode($workout->rest_api_data());
                $field['value'] = $json;
            }
        }

        return $field;
    }

    /**
     * Prepares field as read-only
     * 
     * @param array $field
     */
    public function workout_duration_acf($field) {

        $field['readonly'] = 1;
        return $field;

    }

    /**
     * Hooks on the save_post action to empty the workout JSON data
     * 
     * @param array $post_id
     */
    public function on_save_workout($post_id) {

        if(get_post_type($post_id) === 'workout') {
            // Blank out field
            update_field('workout_json', '', $post_id);

            // Autocalulate workout duration
            $workout = new APT_Workout($post_id);
            update_field('workout_duration', $workout->get_workout('totalTimeMinutes'), $post_id);
        }

        return $post_id;
    }

    /**
     * AJAX Filter.
     *
     * @since	1.0
     */
    public function apt_filter_workouts() {

        // Security check.
        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        $form_data = $_POST['formData'];

        // Get data
        $workout_level = isset($form_data['workout_level']) && !empty($form_data['workout_level']) ? $form_data['workout_level'] : null;
        $workout_intensity = isset($form_data['workout_intensity']) && !empty($form_data['workout_intensity']) ? $form_data['workout_intensity'] : null;
        $workout_good_for = isset($form_data['workout_good_for']) && !empty($form_data['workout_good_for']) ? $form_data['workout_good_for'] : null;
        $workout_equipment = isset($form_data['workout_equipment']) && !empty($form_data['workout_equipment']) ? $form_data['workout_equipment'] : null;
        $workout_duration = isset($form_data['workout_duration']) && !empty($form_data['workout_duration']) ? explode('_', $form_data['workout_duration']) : 0;

        $args = array();
        $args['tax_query'] = array();
        $args['meta_query'] = array();

        if($workout_level) {
            $args['tax_query'][] = array(
                'taxonomy' => 'workout_level',
                'terms' => $workout_level,
                'field' => 'term_id',
                'operator' => 'IN'
            );
        }

        if($workout_intensity) {
            $args['tax_query'][] = array(
                'taxonomy' => 'workout_intensity',
                'terms' => $workout_intensity,
                'field' => 'term_id',
                'operator' => 'IN'
            );
        }

        if($workout_good_for) {
            $args['tax_query'][] = array(
                'taxonomy' => 'workout_good_for',
                'terms' => $workout_good_for,
                'field' => 'term_id',
                'operator' => 'IN'
            );
        }

        if($workout_equipment) {
            $args['tax_query'][] = array(
                'taxonomy' => 'workout_equipment',
                'terms' => $workout_equipment,
                'field' => 'term_id',
                'operator' => 'IN'
            );
        }

        // Filter by duration
        if(is_array($workout_duration)) {
            $args['meta_query'][] = array(
                'key'       => 'workout_duration',
                'value'     => $workout_duration,
                'compare'   => 'BETWEEN',
                'type'      => 'NUMERIC'
            );
        }

        $get_workouts = $this->get_workouts($args);

        include APT_PATH .'templates/workouts/workouts-loop.php';

        wp_die();

    }

}

