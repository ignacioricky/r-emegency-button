<?php if ( ! defined( 'ABSPATH' ) ) exit; 
$current_user = wp_get_current_user();


//$vle_nonce = wp_create_nonce( 'verify-duplicatepage-email' );
?>
<script>
//var vle_nonce = "<?php echo $vle_nonce;?>";
</script>
<?php
//$this->load_custom_assets();
?>
<div class="wrap">
<h1><?php _e('R Emergency Button Page Settings ', 'r-emergency-button-page')?></h1>

<?php 
  $duplicatepageoptions = array();
  $opt = get_option('eeb_page_options');
  $msg = isset($_GET['msg']) ? $_GET['msg'] : '';

  if(isset($_POST['submit_eeb_page']) && wp_verify_nonce( $_POST['eebpage_nonce_field'], 'eebpage_action' )):
  	_e("<strong>Saving Please wait...</strong>", 'r-emergency-button-page');

  	$needToUnset = array('submit_eeb_page');//no need to save in Database

  	foreach($needToUnset as $noneed):
  	   unset($_POST[$noneed]);
  	endforeach;

  	foreach($_POST as $key => $val):
  		$duplicatepageoptions[$key] = $val;
  	endforeach;

		$saveSettings = update_option('eeb_page_options', $duplicatepageoptions );

		if($saveSettings){
			r_emergency_button::redirect('options-general.php?page=eeb_page_settings&msg=1');
		}
		else{
			r_emergency_button::redirect('options-general.php?page=eeb_page_settings&msg=2');
		}
  endif;

  //msg upon save
  if(!empty($msg) && $msg == 1):
    _e( '<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated"> 
  <p><strong>Settings saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'r-emergency-button-page');	
  elseif(!empty($msg) && $msg == 2):
    _e( '<div class="error settings-error notice is-dismissible" id="setting-error-settings_updated"> 
  <p><strong>Settings not saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'r-emergency-button-page');
  endif;


?> 
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content" style="position: relative;">
      <form action="" method="post" name="eeb_page_form">
        <?php  wp_nonce_field( 'eebpage_action', 'eebpage_nonce_field' ); ?>
        <table class="form-table">
          <tbody>
            <tr>
              <th scope="row"><label for="eeb_text"><?php _e('Emergency Button Text', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_text" name="eeb_text" type="text" value="<?php echo $opt['eeb_text']; ?>" />
                <p><?php _e('Text for the Emergency Button.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <tr>
              <th scope="row"><label for="eeb_no_of_clicks"><?php _e('Number of Clicks', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_no_of_clicks" name="eeb_no_of_clicks" type="number" value="<?php echo $opt['eeb_no_of_clicks']; ?>" />
                <p><?php _e('Number of Clicks to Email Emergency-Email.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <tr>
              <th scope="row"><label for="eeb_email_to"><?php _e('Emergency Email To', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_email_to" name="eeb_email_to" type="email" value="<?php echo $opt['eeb_email_to']; ?>" />
                <p><?php _e('Upon clicking the button with respective number of clicks this will be email to inputed email.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <tr>
              <th scope="row"><label for="eeb_detect_location"><?php _e('Detect Location', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_detect_location" name="eeb_detect_location" type="checkbox" <?php echo ($opt['eeb_detect_location'] ? 'checked' : '') ?> >Enabled</input>
                <p><?php _e('This will send user location, make sure the location is on.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <tr>
              <th scope="row"><label for="eeb_send_interval"><?php _e('Email Interval', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_send_interval" name="eeb_send_interval" type="number" value=<?php echo $opt['eeb_send_interval']  ?> >Sec/s</input>
                <p><?php _e('This will continue to send GPS coordinates every input second/s.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

          </tbody>
        </table>

       <!--  <table class="form-table">
          <tbody>

            <tr ><strong style="font-size:15px">Email template</strong><br/>Please setup your email template here, after the template user information will be included</tr>
            <tr>
              <th scope="row"><label for="eeb_email_subject"><?php //_e('Email Subject', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_email_subject" name="eeb_email_subject" type="text" value=<?php //echo $opt['eeb_email_subject']  ?> ></input>
                <p><?php //_e('Enter email eubject.', 'r-emergency-button-page')?></p>
              </td>
            </tr>
            <tr>
              <th scope="row"><label for="eeb_email_template"><?php //_e('Email Template', 'r-emergency-button-page')?></label></th>
              <td>
                <?php //wp_editor(  $opt['eeb_email_template'], 'eeb_email_template' );?>
                <p><?php //_e('Enter email template.', 'r-emergency-button-page')?></p>
              </td>
            </tr>


          </tbody>
        </table> -->

        <table class="form-table">
          <tbody>

            <tr ><strong style="font-size:15px">SMTP SETUP</strong><br/>Setup your smtp for mailing purposes</tr>
            <tr>
              <th scope="row"><label for="eeb_secure_smtp"><?php _e('Secured SMTP', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_secure_smtp" name="eeb_secure_smtp" type="checkbox" <?php echo ($opt['eeb_secure_smtp'] ? 'checked' : '') ?> >Enabled</input>
                <p><?php _e('SMTP connection is secured.', 'r-emergency-button-page')?></p>
              </td>
            </tr>
            <tr>
              <th scope="row"><label for="eeb_smtp_host"><?php _e('SMTP Host', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_smtp_host" name="eeb_smtp_host" type="text" value="<?php echo $opt['eeb_smtp_host']; ?>" />
                <p><?php _e('Your smtp host name.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <tr>
              <th scope="row"><label for="eeb_smtp_port"><?php _e('SMPT Port', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_smtp_port" name="eeb_smtp_port" type="text" value="<?php echo $opt['eeb_smtp_port']; ?>" />
                <p><?php _e('Your smtp port.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <tr>
              <th scope="row"><label for="eeb_smtp_username"><?php _e('Username', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_smtp_username" name="eeb_smtp_username" type="text" value="<?php echo $opt['eeb_smtp_username']; ?>" />
                <p><?php _e('Your smtp username.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <tr>
              <th scope="row"><label for="eeb_smtp_password"><?php _e('Password', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_smtp_password" name="eeb_smtp_password" type="password" value="<?php echo $opt['eeb_smtp_password']; ?>" />
                <p><?php _e('Your smtp password', 'r-emergency-button-page')?></p>
              </td>
            </tr>

            <!-- <tr>
              <th scope="row"><label for="eeb_smtp_email"><?php //_e('From Email', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_smtp_email" name="eeb_smtp_email" type="email" value="<?php //echo $opt['eeb_smtp_email']; ?>" />
                <p><?php //_e('Your smtp email.', 'r-emergency-button-page')?></p>
              </td>
            </tr> -->

            <tr>
              <th scope="row"><label for="eeb_smtp_name"><?php _e('From Name', 'r-emergency-button-page')?></label></th>
              <td>
                <input id="eeb_smtp_name" name="eeb_smtp_name" type="text" value="<?php echo $opt['eeb_smtp_name']; ?>" />
                <p><?php _e('Your smtp name or nickname.', 'r-emergency-button-page')?></p>
              </td>
            </tr>

          </tbody>
        </table>
        <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit_eeb_page"></p>
      </form>
      <div style=''>
        <p>Copy the below shortcode and place into your WordPress site.</p>
        <textarea cols='60' rows='0' onclick='this.select();this.focus();'>[eeb_button]</textarea>
      </div>
      </div>
    </div>
  </div>
</div>