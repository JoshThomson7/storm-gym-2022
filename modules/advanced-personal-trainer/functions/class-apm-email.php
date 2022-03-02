<?php
/**
 * Class in charge of email.
 *
 * @since      1.0
 * @package    WP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH') ) exit;

class APM_Email {

    /**
	 * Where the email goes.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    private $recipients;

    /**
	 * The email subject.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    private $subject;

    /**
	 * The email body.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    private $body;

    /**
	 * Custom email headers.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      array
	 */
    private $headers;
    
    /**
     * The email attachments
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    private $attachments;

    /**
     * Init hooks
     */
    public function init() {

        // From name and email address
        add_filter('wp_mail_from', array($this, 'custom_from_address'));
        add_filter('wp_mail_from_name', array($this, 'custom_from_name'));

        // HTML emails
        add_filter('wp_mail_content_type', array($this, 'wp_mail_html_it_up'));

        // WP core emails
        add_filter('wp_new_user_notification_email', array($this, 'custom_new_user_notification_email'), 10, 3);
        add_filter('wp_new_user_notification_email_admin', array($this, 'custom_new_user_notification_email_admin'), 10, 3);
        add_filter('password_change_email', array($this, 'custom_password_change_email'), 10, 4);
        add_filter('retrieve_password_message', array($this, 'custom_retrieve_password_message'), 10, 4);
        add_filter('retrieve_password_title', array($this, 'custom_retrieve_password_title'));

    }

    /**
     * Set From header
     * 
     * @param string $from
     * @param string $name
     */
    public function From($from = null, $name = null) {

        if(!$from) {
            $from = 'no-reply@'.$_SERVER['HTTP_HOST'];
        }

        if(!$name) {
            $name = get_bloginfo('name');
        }

        $this->headers = 'From: '.$name.' <'.$from.'>';

    }
    
    /**
     * Set recipients
     * 
     * @param array $recipient
     */
    public function To($recipients = array()) {

        $this->recipients = $recipients;

    }

    /**
     * Set recipients
     * 
     * @param array $recipient
     */
    public function Recipients($recipients = array()) {

        $this->recipients = $recipients;

    }

    /**
     * Sets email subject
     * 
     * @param string $subject
     */
    public function Subject($subject = null) {

        if(!$subject) {
            $subject = 'Notification';
        }

        $this->subject = $subject;

    }

    public function Body($body) {

        $this->body = $body;

    }

    /**
	 * Global email styles.
	 *
	 * @since 1.0
	 */
    public function get_email_styles() {

        $styles = array(
            'background' => 'background: #f7f8fb; padding: 40px 0; width: 100%; height: 100%;',
            'wrapper' => 'max-width: 600px; margin: 0 auto; padding: 40px; background: #fff; border-radius: 3px; font-family: sans-serif;',
            'logo' => array(
                'a' => 'width: 60px; margin: 0 0 20px; display: block;',
                'img' => 'width: 130px;'
            ),
            'button' => 'background: #00aaa7; border-radius: 3px; color: #fff; display: inline-block; padding: 6px 25px; text-decoration: none; font-weight: bold;',
            'h2' => 'font-size: 17px; color: #30233f; font-weight: 600;',
            'p' => 'color: #383c40; font-size: 14px; margin-bottom: 14px;',
            'small' => 'color: #383c40; font-size: 11px;',
            'strong' => 'font-weight: 700;',
            'a' => 'color: #00aaa7',
            'ul' => 'padding-left: 20px;',
            'li' => 'color: #383c40; font-size: 14px;'
        );

        return $styles;
    }

    public function apply_styles($body) {

        $styles = $this->get_email_styles();

        $message = str_replace('<p>', '<p style="'.$styles['p'].'">', $body);
        $message = str_replace('<a', '<a style="'.$styles['a'].'" ', $message);
        $message = str_replace('<strong>', '<strong style="'.$styles['strong'].'">', $message);
        $message = str_replace('<small>', '<small style="'.$styles['small'].'">', $message);
        $message = str_replace('<ul>', '<ul style="'.$styles['ul'].'">', $message);
        $message = str_replace('<li>', '<li style="'.$styles['li'].'">', $message);

        return $message;
    }
    
    /**
	 * Email template.
	 *
	 * @since 1.0
	 */
    function email_template($message = null) {

        $styles = $this->get_email_styles();

        if(!$message) {
            $message = $this->body;
        }

        $template = '
            <div style="'.$styles['background'].'">
                <div style="'.$styles['wrapper'].'">
                    
                    <a href="'.esc_url(home_url()).'" style="'.$styles['logo']['a'].'">
                        <img src="'.esc_url(get_stylesheet_directory_uri().'ts-logo-email.png').'" style="'.$styles['logo']['img'].'">
                    </a>

                    <h2 style="'.$styles['h2'].'">'.$this->subject.'</h2>
                    '.$this->apply_styles($message).'
                    <p style="'.$styles['p'].'">
                        <strong>'.get_bloginfo('name').'</strong><br>
                        <small style="'.$styles['small'].'><a style="'.$styles['a'].'" href="'.esc_url(home_url()).'">'.esc_url(home_url()).'</a></small>
                    </p>
                </div>
            </div>
        ';

        return $template;
    
    }

    public function send() {

        wp_mail($this->recipients, $this->subject, $this->email_template(), $this->headers, $this->attachments);

    }

    /**
     * HTML email up
     */
    public function wp_mail_html_it_up() {
        return 'text/html';
    }

    /**
     * Custom from address
     * 
     * @param string $email
     */
    public function custom_from_address($email) {
        return 'no-reply@'.$_SERVER['HTTP_HOST'];
    }

    /**
     * Custom from email
     * 
     * @param string $email
     */
    public function custom_from_name($from_name) {
        return get_bloginfo('name');
    }

    /**
     * Custom retrieve password subject email
     * 
     */
    public function custom_retrieve_password_title() {
        return 'Reset your password';
    }

    /**
     * Reset password email override
     */
    public function custom_retrieve_password_message($message, $key, $user_login, $user_data) {

        $styles = $this->get_email_styles();

        $user_id = $user_data->data->ID;
        $user = new APM_Patient($user_id);

        if ( ! function_exists( 'is_woocommerce_activated' ) ) {
            $password_reset_url = get_permalink(get_option('woocommerce_myaccount_page_id')).'lost-password/?key='.$key.'&id='.$user_id;
        } else {
            $password_reset_url = network_site_url('wp-login.php?action=rp&key='.$key.'&login='.rawurlencode($user_login), 'login');
        }

        $body = $this->Body('
            <h2 style="'.$styles['h2'].'">Hi '.$user->get_first_name().'</h2>

            <p style="'.$styles['p'].'">You have requested to reset your password for the following account:</p>
            <p style="'.$styles['p'].'"><strong>'.$user_login.'</strong></p>
            
            <p style="'.$styles['p'].'">To reset it, please click on the button below.</p>
            <p style="'.$styles['p'].'"><a href="'.$password_reset_url.'" style="'.$styles['button'].'">Reset your password</a></p>
            
            <p style="'.$styles['p'].'">If you didn\'t make this request, simply ignore this email and nothing will happen.</p>
        ');

        $message = $this->email_template($body);

        return $message;

    }

    /**
     * Overrides core WP user notification email
     * 
     * @param array $email_data
     * @param object $user WP_User object
     * @param string $blogname
     */
    public function custom_new_user_notification_email($email_data, $user, $blogname) {

        // Parse message string into variables
        // so we can get the password reset key
        parse_str($email_data['message'], $message);

        // User data
        $user_login = $user->user_email;
        $key = $message['key'];
        $password_reset_url = network_site_url('wp-login.php?action=rp&key='.$key.'&login='.rawurlencode($user_login), 'login').'&redirect_to='.esc_url(get_permalink(get_page_by_path('book')));

        // Create user object
        $user = new APM_Patient($user->ID);

        /**
         * Prepare custom email
         */
        // Load styles
        $styles = $this->get_email_styles();

        // Custom body message
        $body = $this->Body('
            <h2 style="'.$styles['h2'].'">Hi '.$user->get_first_name().'</h2>

            <p style="'.$styles['p'].'">Your new account is almost ready.</p>
            
            <p style="'.$styles['p'].'">Simply click on the button below and you will be taken to a page to set your new password.</p>
            <p style="'.$styles['p'].'"><a href="'.$password_reset_url.'" style="'.$styles['button'].'">Set your new password</a></p>
            
            <p style="'.$styles['p'].'">If you think this email was not for you, simply ignore it and nothing will happen.</p>
        ');

        // Inject custom message body into template
        $message = $this->email_template($body);

        // Override email props
        $email_data['subject'] = 'Your new account';
        $email_data['message'] = $message;

        return $email_data;

    }

    /**
     * Overrides core WP user notification email
     * 
     * @param array $email_data
     * @param object $user WP_User object
     * @param string $blogname
     */
    public function custom_new_user_notification_email_admin($email_data, $user, $blogname) {

        // Parse message string into variables
        // so we can get the password reset key
        parse_str($email_data['message'], $message);

        // User data
        $user_login = $user->user_email;

        // Create user object
        $user = new APM_Patient($user->ID);

        /**
         * Prepare custom email
         */
        // Load styles
        $styles = $this->get_email_styles();

        // Custom body message
        $body = $this->Body('
            <h2 style="'.$styles['h2'].'">New user account</h2>

            <p style="'.$styles['p'].'">A new user account with email address <strong>'.$user_login.'</strong> has been created.</p>
            <p style="'.$styles['p'].'">If you think this is a mistake, please get in touch with the user in question: <strong>'.$user->get_full_name().'.</strong></p>
        ');

        // Inject custom message body into template
        $message = $this->email_template($body);

        // Override email props
        $email_data['subject'] = 'New user account';
        $email_data['message'] = $message;

        return $email_data;

    }

    public function custom_password_change_email($pass_change_email, $user, $userdata) {

        // Create user object
        $user = new APM_Patient($user->ID);

        /**
         * Prepare custom email
         */
        // Load styles
        $styles = $this->get_email_styles();

        // Custom body message
        $body = $this->Body('
            <h2 style="'.$styles['h2'].'">Password changed</h2>

            <p style="'.$styles['p'].'">This notice confirms that your password was changed on Bodyset. If you did not change your password, please contact the us on clientcare@capitalphysio.com.</p>
        ');

        // Inject custom message body into template
        $message = $this->email_template($body);

        // Override email props
        $email_data['subject'] = 'Password changed';
        $email_data['message'] = $message;

        return $email_data;

    }
    
}