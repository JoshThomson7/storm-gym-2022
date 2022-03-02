<?php
/**
 * APM Auth
 *
 * Class in charge of auth bits
 * 
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Auth {

    public function init() {

        add_action('wp_ajax_nopriv_apm_set_token', array($this, 'apm_set_token'));
        add_action('wp_ajax_apm_set_token', array($this, 'apm_set_token'));

        add_action('after_password_reset', array($this, 'after_password_reset_redirect'), 10, 2);

        add_filter('gettext', array($this, 'filter_gettext'), 10, 3 );

    }

    /**
     * Clear user token via AJAX
     * Called by on_user_logout()
     * 
     * @see on_user_logout()
     */
    public function apm_set_token() {

        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        parse_str($_POST['form_data'], $form_data);

        $username = isset($form_data['username']) && !empty($form_data['username']) ? $form_data['username'] : null;
        $password = isset($form_data['password']) && !empty($form_data['password']) ? $form_data['password'] : null;

        $token_url = str_replace('http', 'https', esc_url(home_url()).'/wp-json/jwt-auth/v1/token');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, 1);

        # Admin credentials here
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password);

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($ch);
        
        if(empty($json_response)) {
            return null;
        
        } else {

            $json_response = json_decode($json_response);

            // If we have a token, programatically log in user
            if(isset($json_response->token) && !empty($json_response->token)) {
                $creds = array(
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => true
                );
                $user = wp_signon($creds, false);

                // Check for errors
                $err = is_wp_error($user);
                if($err) {
                    $json_response = $err;
                } else {
                    $user_data = get_userdata($user->ID);

                    wp_set_auth_cookie($user->ID);
                    wp_set_current_user($user->ID);
                    do_action('wp_login', $user->user_login, $user);

                    $user_roles = $user->roles;
                    
                    if (in_array('administrator', $user_roles, true )) {
                        $json_response->redirect_url = admin_url();
                    } else {
                        $json_response->redirect_url = wc_get_page_permalink('myaccount');
                    }
                }
            }
        }

        // return false;
        curl_close ($ch);

        wp_send_json($json_response);
        wp_die();

    }

    /**
     * Redirect the user after resetting password
     */
    public function after_password_reset_redirect($user, $new_pass) {

        $user_data = get_userdata($user->ID);
        $user_roles = $user->roles;

        if(in_array('customer', $user_roles, true)) {
            wp_redirect(wc_get_page_permalink('myaccount').'?password-reset=true');
            exit;
        }

    }

    /**
     * Changes reset password button text
     */
    public function filter_gettext( $translated, $original, $domain ) {

        if(isset($_GET['action']) && $_GET['action'] === 'rp') {
            $translated = str_ireplace( 'Reset password',  'Set password',  $translated );
        }
    
        return $translated;
    }
    

}