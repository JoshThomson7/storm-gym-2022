<?php
/**
 * Extends the WordPress REST API.
 *
 * Adds custom endpoints to the WordPress REST API.
 *
 * @package    APT
 * @author     FL1 Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class APT_REST_API {

    public function register_routes() {

        // Settings.
        // $settings_controller = new APT_REST_Settings_Controller();
        // add_action('rest_api_init',	array($settings_controller, 'register_rest_route'));

        // Register.
        $workout_controller = new APT_REST_Workout_Controller();
        add_action('rest_api_init',	array($workout_controller, 'register_rest_route'));

        // add_filter('jwt_auth_token_before_dispatch', array($this, 'jwt_before_dispatch'), 10, 2 );
        // //add_action('wp_authenticate', 'customcode', 30, 2);

        // add_action( 'wp_loaded', array($this, 'maybe_load_cart'), 5);
        // add_filter('jwt_auth_expire', array($this, 'filter_jwt_auth_expire'));
    }

    /**
     * We have to tell WC that this should not be handled as a REST request.
     * Otherwise we can't use the product loop template contents properly.
     * Since WooCommerce 3.6
     *
     * @param bool $is_rest_api_request
     * @return bool
     */
    public function maybe_load_cart( $is_rest_api_request ) {
        
        if ( version_compare( WC_VERSION, '3.6.0', '>=' ) && WC()->is_rest_api_request() ) {
            if ( empty( $_SERVER['REQUEST_URI'] ) ) {
                return;
            }
    
            $rest_prefix = 'bodyset/booking';
            $req_uri     = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
    
            $is_my_endpoint = ( false !== strpos( $req_uri, $rest_prefix ) );
    
            if ( ! $is_my_endpoint ) {
                return;
            }
    
            require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
            require_once WC_ABSPATH . 'includes/wc-notice-functions.php';
    
            if ( null === WC()->session ) {
                $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    
                // Prefix session class with global namespace if not already namespaced
                if ( false === strpos( $session_class, '\\' ) ) {
                    $session_class = '\\' . $session_class;
                }
    
                WC()->session = new $session_class();
                WC()->session->init();
            }
    
            /**
             * For logged in customers, pull data from their account rather than the
             * session which may contain incomplete data.
             */
            if ( is_null( WC()->customer ) ) {
                if ( is_user_logged_in() ) {
                    WC()->customer = new WC_Customer( get_current_user_id() );
                } else {
                    WC()->customer = new WC_Customer( get_current_user_id(), true );
                }
    
                // Customer should be saved during shutdown.
                add_action( 'shutdown', array( WC()->customer, 'save' ), 10 );
            }
    
            // Load Cart.
            if ( null === WC()->cart ) {
                WC()->cart = new WC_Cart();
            }
        }
        
    }


    /**
	 * REST API authentication.
	 *
	 * Disables the REST API unless logged in.
	 *
	 * @since    1.4.3
	 */
	public function rest_api_authentication() {

        /*
         *	Disable REST API link in HTTP headers.
         *	Link: <https://example.com/wp-json/>; rel="https://api.w.org/"
        */
        remove_action('template_redirect', 'rest_output_link_header', 11);

        /*
         *	Disable REST API links in HTML <head>
         *	<link rel='https://api.w.org/' href='https://example.com/wp-json/' />
        */
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');

        // Add CORS support
        add_action('init', array($this, 'add_cors_http_header'));

    }

    /**
     * Add headers
     */
    public function add_cors_http_header() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");
    }

    /**
     * Logs user into WordPress after REST API login
     * 
     * @param array $data
     * @param object user
     */
    public function jwt_before_dispatch($data, $user) {

        $user_id = $user->ID; 
        $user = get_userdata($user_id);
        $user_login = $user->user_login;

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id); 
        do_action('wp_login', $user_login, $user);
        
        return $data;

    }

    /**
     * Custom token expiration time
     */
    public function filter_jwt_auth_expire() {
        return time() + 3540; // Days in sec * 365 days
    }
    

}
