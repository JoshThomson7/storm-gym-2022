<?php
/**
 * Generic API
 *
 * @package    APM
 * @subpackage apm/functions/rest-api
 * @author     FL1 Digital
 */

class GenericAPI {
    
    public $options = [];
    public $wpdb;
    public $logger = null;
    public $auth_token = '';
    public $session_id = 0;
    public $last_error = '';

    function __construct(){

    }
    function __construct1($options){
        $this->setOptions($options);
    }
    public function setOptions($options)
    {
        $this->options = $options; 
    }

    public function getAuthToken()
    {
        return ($this->getSessionLMAuthKey());
    }
    
    public function dumpOptions()
    {
        echo 'printing shiz<br />';
        $fields_string = '';
        foreach($this->options as $key=>$value) { 
            $fields_string .= $key.'='.$value.'<br />'; 
        }
        echo $fields_string;
    }

    public function Urlify($array)
    {
        //url-ify the data for the POST
        $fields_string = '';
        foreach($array as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        
        return ($fields_string);
    }

    public function SetSession($session_id)
    {
        $this->session_id = $session_id;
    }

    public function NewSession()
    {
        $wp = $this->wpdb;  
        if (isset($wp))
        {
            $wp->insert('fl1_api_session', array('id' => 'NULL'));     
            $this->session_id = $wp->insert_id;

            // Take a snapshot of what's happened for troubleshooting
            $this->LogEvent('', 'Started new session '. $this->session_id, 'api');
        }
        else{
            echo ('WPDB is NOT set');
        }
        return ($this->session_id);
    }

    protected function setSessionLMAuthKey($auth_key)
    {
        $wp = $this->wpdb;  
        $wp->update('fl1_api_session', array('lm_authkey' => $auth_key), array('id' => $this->session_id));
    }
    
    public function getSessionLMAuthKey()
    {
        $auth_key = null;

        $wp = $this->wpdb;  
        $auth_key = $wp->get_var( "SELECT lm_authkey FROM fl1_api_session WHERE id=" .$this->session_id);
        
        return ($auth_key);
    }

    public function LogEvent($url, $data, $source = '')
    {
        $date = new DateTime('now', wp_timezone());
        
        $wp = $this->wpdb;  
        if (isset($wp))
        {
            $wp->insert('fl1_api_log', array('session_id' => $this->session_id, 'time_stamp' => $date->format('Y/m/d h:i:s a'), 'url' => $url, 'data' => $data, 'source' => $source));
        }
        else{
            echo ('WPDB is NOT set');
        }
        return;
    }
}
?>