<?php
/**
 * APM Init
 *
 * Class in charge of initialising everything APM
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APT_CPT {

    public function init() {

        $post_types = array(
            'workout',
            'video',
            'collection'
        );

        foreach($post_types as $post_type) {
            $method = 'register_'.$post_type.'_cpt';

            if(method_exists($this, $method)) {
                $this->$method();
            }
        }
        
        //add_filter('term_link', 'filter_location_link', 10, 2);

        //add_filter('post_type_link', array('filter_location_link'), 20, 2);
        add_action('admin_menu', array($this, 'menu_page'));
        add_action('admin_menu', array($this, 'remove_duplicate_subpage'));
        add_filter('parent_file', array($this, 'highlight_current_menu'));

        //$this->register_enpoints();

    }

    function menu_page() {
        add_menu_page(
            __('Advanced PT', APT_SLUG),
            'Advanced PT',
            'manage_options',
            APT_SLUG,
            '',
            'dashicons-heart',
            30
        );

        $submenu_pages = array(
            array(
                'page_title'  => 'Workouts',
                'menu_title'  => 'Workouts',
                'capability'  => 'manage_options',
                'menu_slug'   => 'edit.php?post_type=workout',
                'function'    => null,
            ),
                array(
                    'page_title'  => '',
                    'menu_title'  => '&nbsp;- Level',
                    'capability'  => 'manage_options',
                    'menu_slug'   => 'edit-tags.php?taxonomy=workout_level&post_type=workout',
                    'function'    => null,
                ),
                array(
                    'page_title'  => '',
                    'menu_title'  => '&nbsp;- Intensity',
                    'capability'  => 'manage_options',
                    'menu_slug'   => 'edit-tags.php?taxonomy=workout_intensity&post_type=workout',
                    'function'    => null,
                ),
                array(
                    'page_title'  => '',
                    'menu_title'  => '&nbsp;- Good for',
                    'capability'  => 'manage_options',
                    'menu_slug'   => 'edit-tags.php?taxonomy=workout_good_for&post_type=workout',
                    'function'    => null,
                ),
                array(
                    'page_title'  => '',
                    'menu_title'  => '&nbsp;- Equipment',
                    'capability'  => 'manage_options',
                    'menu_slug'   => 'edit-tags.php?taxonomy=workout_equipment&post_type=workout',
                    'function'    => null,
                ),
            array(
                'page_title'  => 'Videos',
                'menu_title'  => 'Videos',
                'capability'  => 'manage_options',
                'menu_slug'   => 'edit.php?post_type=video',
                'function'    => null,
            ),
            array(
                'page_title'  => 'Collections',
                'menu_title'  => 'Collections',
                'capability'  => 'manage_options',
                'menu_slug'   => 'edit.php?post_type=collection',
                'function'    => null,
            )
        );

        foreach ( $submenu_pages as $submenu ) {

            add_submenu_page(
                APT_SLUG,
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['function']
            );

        }
    }

    public function highlight_current_menu( $parent_file ) {

        global $submenu_file, $current_screen, $pagenow;

        $cpts = APT_Helpers::registered_post_types();

        # Set the submenu as active/current while anywhere APM
        if (in_array($current_screen->post_type, $cpts)) {

            if ( $pagenow == 'post.php' ) {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }

            if ( $pagenow == 'edit-tags.php' ) {
                $submenu_file = 'edit-tags.php?taxonomy='.$current_screen->taxonomy.'&post_type=' . $current_screen->post_type;
            }

            $parent_file = APT_SLUG;

        }

        return $parent_file;

    }

    /**
     * Workout CPT
     */
    private function register_workout_cpt() {

        // CPT
        $cpt = new WP_CPT(
            array(
                'post_type_name' => 'workout',
                'plural' => 'Workouts',
                'menu_name' => 'Advanced PT' // Override main menu name
            ),
            array(
                'menu_position' => 21,
                'rewrite' => array( 'slug' => 'workout', 'with_front' => true ),
                'show_in_menu' => false
            )
        );

        // Taxonomies
        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'workout_level',
                'slug' => 'workout_level',
                'singular' => 'Level',
                'plural' => 'Levels'
            )
        );
        
        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'workout_intensity',
                'slug' => 'workout_intensity',
                'singular' => 'Intensity',
                'plural' => 'Intensities'
            )
        );

        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'workout_good_for',
                'slug' => 'workout_good_for',
                'singular' => 'Good for',
                'plural' => 'Good for'
            )
        );

        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'workout_equipment',
                'slug' => 'workout_equipment',
                'singular' => 'Equipment',
                'plural' => 'Equipment'
            )
        );

    }

    /**
     * Videos CPT
     */
    private function register_video_cpt() {

        // CPT
        $cpt = new WP_CPT(
            array(
                'singular' => 'Video',
                'plural' => 'Videos',
                'post_type_name' => 'video',
                'menu_name' => 'Videos'
            ),
            array(
                'show_in_menu' => false
            )
        );

    }

    /**
     * Collection CPT
     */
    private function register_collection_cpt() {

        // CPT
        $cpt = new WP_CPT(
            array(
                'singular' => 'Collection',
                'plural' => 'Collections',
                'post_type_name' => 'collection',
                'menu_name' => 'Collections'
            ),
            array(
                'show_in_menu' => false
            )
        );

    }

    /**
	 * Remove duplicate sub page
	 *
	 * @since 1.0
	 */
	public function remove_duplicate_subpage() {
        remove_submenu_page(APT_SLUG, APT_SLUG);
    }

    /**
	 * Register core endpoints.
	 *
	 * @since 1.0
	 */
	public function register_enpoints() {

        add_rewrite_endpoint('xml-map', EP_PAGES);
        add_rewrite_endpoint('add', EP_PAGES);

        add_filter( 'request', array($this, 'filter_var_request'));

    }

    /**
	 * Filter endpoint request.
	 *
	 * If nothing follows the endpoint, your query var will be empty (but set), so it will always evaluate as false when you try to catch it.
     * You can get around this by filtering 'request' and changing the value of your endpoint variables to true if they are set.
     *
     * @since 1.0
	 */
	public function filter_var_request($vars) {

        if( isset( $vars['xml-map'] ) ) $vars['xml-map'] = true;
        if( isset( $vars['add'] ) ) $vars['add'] = true;

        return $vars;

    }

}