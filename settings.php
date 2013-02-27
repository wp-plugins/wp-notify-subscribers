<?php

// register the settings
add_action('admin_menu', 'wpns_add_admin_page');

function wpns_add_admin_page() {
  
  add_options_page('WP Notify Subscribers Settings', 'Notify Subscribers', 'manage_options', 'wpns_admin', 'wpns_admin_page');

  
}

add_action('admin_init', 'wpns_admin_init');

/**
 * 
 * Add the settings to the form
 * 
 */
function wpns_admin_init(){
  
  register_setting( 'wpns_settings', 'wpns_options', 'wpns_validate' );
  
  // the sender sections
  add_settings_section('wpns_settings_sender', 'Sender Settings', 'wpns_settings_sender_text' , 'wpns_admin');
  
  // add the fields for this section
  add_settings_field('wpns_sender_name', 'Sender Name', 'wpns_input_sender_name', 'wpns_admin', 'wpns_settings_sender');
  add_settings_field('wpns_sender_email', 'Sender Email', 'wpns_input_sender_email', 'wpns_admin', 'wpns_settings_sender');
  
  // the message sections
  add_settings_section('wpns_settings_message', 'Message Settings', 'wpns_settings_message_text' , 'wpns_admin');
  
  add_settings_field('wpns_message_subject', 'Subject', 'wpns_input_message_subject', 'wpns_admin', 'wpns_settings_message');
  add_settings_field('wpns_message_text', 'Message Body', 'wpns_input_message_text', 'wpns_admin', 'wpns_settings_message');
  
  
}




function wpns_admin_page() {
  ?>
  <div class="wrap">
    <h2>WordPress Notify Subscribers</h2>
    
    <form method="post" action="options.php">
      
      <?php settings_fields('wpns_settings'); ?>

      <?php do_settings_sections('wpns_admin'); ?>
      
      
      <?php submit_button(); ?>
    </form>
    
  </div>
  <?php
}

function wpns_settings_sender_text() {
  
  echo '<p>Set the sender name and email address</p>';
  
}

function wpns_settings_message_text() {
  
  echo '<p>Set the message subject and text';
  
  echo 'You can use the following placeholdes:<br/><br/>';
  
  echo '<code>'.implode(', ',wpns_get_placeholders()). '</code>';
  
  #echo '<code>[post_url]</code>, <code>[receiver_display_name]</code>, <code>[receiver_email]</code>, <code>[blog_name]</code>, <code>[sender_name]</code>';
    
  echo '</p>';
  
}

function wpns_input_sender_name() {
  
  $options = get_option('wpns_options');
  
  $value = '';
  
  $value = @$options['wpns_sender_name'];
 
  echo "<input id='wpns_sender_name' name='wpns_options[wpns_sender_name]' size='40' type='text' value='$value' />";
  
  
}

function wpns_input_sender_email() {
  
  $options = get_option('wpns_options');
  
  $value = '';
  
  $value = @$options['wpns_sender_email'];
 
  echo "<input id='wpns_sender_email' name='wpns_options[wpns_sender_email]' size='40' type='text' value='$value' />";
  
  
}

function wpns_input_message_subject() {
  
  $options = get_option('wpns_options');
  
  $value = '';
  
  $value = @$options['wpns_message_subject'];
 
  echo "<input style='width:50%' id='wpns_message_subject' name='wpns_options[wpns_message_subject]' size='40' type='text' value='$value' />";
  
  
}

function wpns_input_message_text() {
  
  $options = get_option('wpns_options');
  
  $value = '';
  
  $value = @$options['wpns_message_text'];
 
  echo "<textarea style='width:50%; height: 400px' id='wpns_message_text' name='wpns_options[wpns_message_text]'>$value</textarea>";
  
  
}

// FIXME: The validation do not anything by now
function wpns_validate($input) {
  
  return $input;
  
}


?>
