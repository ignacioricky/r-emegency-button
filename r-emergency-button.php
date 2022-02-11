<?php
/*
Plugin Name: R Emergency Button
Plugin URI: 
Description: R Emergency Button is a plugin which allows to create a custom button and upon clicking depending of number of clicks, it will email the user geolocation and data to provided email address 
Version: 1.0.0
Author URI: 
*/

define( 'EEB_VERSION', '1.0.0' );
define( 'EEB_PATH', plugin_dir_path( __FILE__ ) );
define( 'EEB_BASEPATH', plugin_basename(dirname(__FILE__)) );
require_once( ABSPATH . '/wp-includes/pluggable.php' );

class r_emergency_button
{
    public function __construct()
    {
        register_activation_hook( __FILE__, array( __class__, 'eeb_page_install' ) );
        add_action('admin_menu', array(__class__, 'eeb_option_page'));
        add_shortcode( 'eeb_button', array(&$this, 'eeb_display' ) );
        add_action( 'wp_head', array(__class__, 'eeb_head'));
    }

    /*
    * Activation Hook
    */
    public function eeb_page_install()
    {
        $defaultSettings = array(
            'eeb_text' => 'Text',
            'eeb_no_of_clicks' => 3,
            'eeb_email_to' => '',
            'eeb_detect_location' => true,
            'eeb_send_interval' => 5,
            'eeb_text' => '',
            'eeb_no_of_clicks' =>'',
            'eeb_email_to' =>'',
            'eeb_detect_location' =>'',
            'eeb_email_subject' => '',
            'eeb_email_template' =>''
        );

        $opt = get_option('eeb_page_options');

        if(!$opt) {
            update_option('eeb_page_options', $defaultSettings);
        }  
    }

    public function eeb_head()
    {
        $is_emergency = get_user_meta(
            get_current_user_id(),
            'use_emergency'
        )[0];

        $opt = get_option('eeb_page_options');
        $cont .= '<script type="text/javascript" >';
        $cont .= '
        var _eeb = {
            _runEmergency:function(){},
            is_running:false,
            _is_emergency:'.(($is_emergency == 1) ? 'true' : 'false').',
            _is_notify:'.(($_REQUEST['is_notify'] == true) ? 'true' : 'false').',
             _emergency_send_interval:'.(($opt['eeb_send_interval'] == 0) ? '0' : $opt['eeb_send_interval']).',
            _emergency_no_of_clicks:'.$opt['eeb_no_of_clicks'].'
        }';
        $cont .= '</script >';
        echo $cont;
        $content = '';

        if($is_emergency == 1){

            $content .= '

            function _sendEmergency(){

                navigator.geolocation.getCurrentPosition(function(position, html5Error) {
                    var http = new XMLHttpRequest();
                    var url = "get_data.php";
                    var params = "latitude="+position["coords"]["latitude"]+"&longitude="+position["coords"]["longitude"]+"&use_emergency=true&is_notify=true";
                    http.open("GET", window.location.href + "?" + params, true);

                    //Send the proper header information along with the request
                    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    console.log("asdasd");
                    http.onreadystatechange = function() {//Call a function when the state changes.
                        _eeb._runEmergency();
                        console.log("asd");
                    }
                    http.send();

                });
            }

            _eeb._runEmergency = function(x){

                setTimeout(function () {

                    _sendEmergency();
                    _eeb.is_running = true;
               }, '.$opt['eeb_send_interval'].'000);
                
            }

            !_eeb.is_running && !_eeb._is_notify && _sendEmergency(); ';
        }
        echo '<script type="text/javascript">
            var a = !1;

            function eeb_emergency_button(b) {
                if ( b.detail === 3) {
                    setTimeout(function () {
                        if(!_eeb._created){
                            var form = document.createElement("form");
                            var element1 = document.createElement("input");  
                            form.method = "POST";
                            form.name = "eeb-button";
                            element1.type="hidden";
                            element1.name="cancel_emergency";
                            element1.value=true;
                            form.appendChild(element1);  
                            document.body.appendChild(form);
                            _eeb._created = true;
                            form.submit();
                        }
                        
                    }, 1000);
                }
            };

            function cancel_emergency_button(b) {
                if(!_eeb._created){
                    var form = document.createElement("form");
                    var element1 = document.createElement("input");  
                    form.method = "POST";
                    form.name = "eeb-button";
                    element1.type="hidden";
                    element1.name="cancel_emergency";
                    element1.value=true;
                    form.appendChild(element1);  
                    document.body.appendChild(form);
                    _eeb._created = true;
                    form.submit();
                }
                
            };


            document.onreadystatechange = () => {
            if (document.readyState === "complete") {
                document.getElementById("eeb-button") && document.getElementById("eeb-button").addEventListener ("click", eeb_emergency_button);
                document.getElementById("_cancel_emergency") && document.getElementById("_cancel_emergency").addEventListener ("click", cancel_emergency_button);
                '.$content.'
            }
            };
            
        </script>';
    }

    public function eeb_option_page()
    {  
        add_options_page( __( 'R Emergency Button Page', 'r-emergency-button-page' ), __( 'R Emergency Button Page', 'r-emergency-button-page'), 'manage_options', 'eeb_page_settings', array(__class__, 'eeb_page_settings'));
    }

    /*
    * Admin Settings
    */
    public function eeb_page_settings()
    {
        if(current_user_can( 'manage_options' )){
           include('inc/page-settings.php');
        }
    }

        
    public static function eeb_display()
    {
        $opt = get_option('eeb_page_options');
        $button_txt = $opt['eeb_text'];
        $base = EEB_BASEPATH;
        $uiid = uniqid();
        $is_emergency = get_user_meta(
            get_current_user_id(),
            'use_emergency'
        )[0];

        $content = '<div style="clear: both;">';
        $content .= '<div style="padding:8px 0;">';
        $content .= '<button class="button" id= '.($is_emergency == 1 ? '_cancel_emergency' :  'eeb-button').' data-eebtoken="'.$uiid.'" style="'.($is_emergency == 1 ? 'background:red' : '').'">'.($is_emergency == 1 ? 'Cancel Emergency' : $button_txt).'</button>';
        $content .= '</div>';
        $content .= '</div>';

        return $content;
    }

    public static function is_emergency()
    {
        return get_user_meta(
            get_current_user_id(),
            'use_emergency'
        )[0] == 1;
    }

    public static function use_emergency($is_emergency = true)
    {
        update_user_meta(
            get_current_user_id(),
            'use_emergency',
            $is_emergency
        );
    }

    public static function cancel_emergency()
    {
        update_user_meta(
            get_current_user_id(),
            'use_emergency',
            false
        );
    }

    public function email($sub = 'Emergency')
    {
        $opt = get_option('eeb_page_options');
        $user = wp_get_current_user()->data;

        $email = $user->user_email ? $user->user_email : get_user_meta(get_current_user_id())['user_email'][0];
        $subject = $sub.' - ' . $email;

        
        $message =  $opt['eeb_email_template'];

        $message .= '<br/>';
        $message .= '<br/> <b>User details:</b><br/>';
        $message .= '<br/>';
        $message .= 'User : '.$user->display_name .'<br/>';
        $message .= 'Firstname : '.get_user_meta(get_current_user_id())['first_name'][0] .'<br/>';
        $message .= 'Lastname : '.get_user_meta(get_current_user_id())['last_name'][0] .'<br/>';
        $message .= 'Email : '.$email .'<br/>';
        $message .= 'Phone #(via Billing Phone) : '.get_user_meta(get_current_user_id())['billing_phone'][0] .'<br/>';
        $message .= 'Phone #(via Emergency Info) : '.get_user_meta(get_current_user_id())['CUSTOM_FIELD_emergency_phone'][0] .'<br/>';
        $message .= 'Emergency Address : '.get_user_meta(get_current_user_id())['CUSTOM_FIELD_emergency_address'][0] .'<br/>';
        
        
        if($opt['eeb_detect_location']){
            $message .= 'latitude : '.$_REQUEST['latitude'] .'<br/>';
            $message .= 'longitude : '.$_REQUEST['longitude'] .'<br/>';
        }

        add_action( 'phpmailer_init', array(__class__, 'configure_smtp') );
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        //add_filter( 'wp_mail_content_type', 'set_html_content_type' );
        $mail = wp_mail($opt['eeb_email_to'],$subject,$message, $headers);
        //remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
        
        echo $mail ? '1' : '2';
    }


    private function configure_smtp( PHPMailer $phpmailer )
    {
        $opt = get_option('eeb_page_options');

        $phpmailer->isSMTP(); //switch to smtp
        $phpmailer->Host = $opt['eeb_smtp_host'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $opt['eeb_smtp_port'];
        $phpmailer->Username = $opt['eeb_smtp_username'];
        $phpmailer->Password = $opt['eeb_smtp_password'];
        $phpmailer->SMTPSecure = $opt['eeb_smtp_secure'];
        $phpmailer->From = $opt['eeb_smtp_username'];
        $phpmailer->FromName=$opt['eeb_smtp_name'];
        
    }

    public static function redirect()
    {
        echo '<script>window.location.href="'.$url.'"</script>';
    }

    function set_html_content_type()
    {
        return 'text/html';
    }
    
}
