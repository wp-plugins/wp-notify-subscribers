<?php
/*
Plugin Name: WP Notify Subscribers
Plugin URI: https://github.com/tobias-redmann/wp-notify-subscribers
Description: A brief description of the Plugin.
Version: 0.1
Author: Tobias Redmann
Author URI: http://www.tricd.de
*/




function wpns_get_version() {
  
  return '0.1';
  
}

/**
 * Get the mail subject
 * 
 * @param WP_Post $post
 * @param WP_User $subscriber
 * @return string
 */
function wpns_get_mail_subject($post, $subscriber) {
  
  $options = wpns_get_options();
  
  $mail_subject = $options['wpns_message_subject'];
  
  return wpns_substitute($post, $subscriber, $mail_subject);
  
  
}

function wpns_get_placeholders() {
  
  $placeholders = array();
  $placeholders[0]     = '[post_url]';
  $placeholders[1]     = '[receiver_display_name]';
  $placeholders[2]     = '[receiver_email]';
  $placeholders[3]     = '[blog_name]';
  $placeholders[4]     = '[blog_url]';
  $placeholders[5]     = '[sender_name]';
  $placeholders[6]     = '[sender_email]';
  $placeholders[7]     = '[post_short_url]';
  
  return $placeholders;
  
}

/**
 * 
 * @param type $post
 * @param type $subscriber
 * @param type $text
 * @return type
 */
function wpns_substitute($post, $subscriber, $text) {
  
  $placeholders = wpns_get_placeholders();
   
  $substitutions = array();
  $substitutions[0]    = get_permalink($post->ID);
  $substitutions[1]    = $subscriber->display_name;
  $substitutions[2]    = $subscriber->user_email;
  $substitutions[3]    = get_bloginfo('name');
  $substitutions[4]    = home_url('/');
  $substitutions[5]    = wpns_get_sender_name();
  $substitutions[6]    = wpns_get_sender_email();
  $substitutions[7]    = wp_get_shortlink($post->ID, 'post');
  
  return str_replace($placeholders, $substitutions, $text);
  
}

/**
 * Get the options array we can make with the settings
 * 
 * @return Array
 */
function wpns_get_options() {
  
  return get_option('wpns_options');
  
}


/**
 * 
 * @param WP_Post $post
 * @param WP_User $subscriber
 * @return String
 */
function wpns_get_mail_text($post, $subscriber) {
  
  $options = wpns_get_options();
  
  $mail_text = $options['wpns_message_text'];
  
  return wpns_substitute($post, $subscriber, $mail_text);
  
}

function wpns_get_sender_email() {

  $options = wpns_get_options();
  
  return $options['wpns_sender_email'];
  
}

function wpns_get_sender_name() {
  
  $options = wpns_get_options();
  
  return $options['wpns_sender_name'];
  
}

/**
 * Send the mail to a specific subscriber
 * 
 * @param WP_Post $post
 * @param WP_User $subscriber
 */
function wpns_send_mail($post, $subscriber) {
  
  $headers = 'From: '. wpns_get_sender_name() .' <'. wpns_get_sender_email() .'>' . "\r\n";
  
  // FIXME: Just for test purposes
  //if ($subscriber->ID == 1 || $subscriber->ID == 2) {
  
    wp_mail(
      $subscriber->user_email, 
      wpns_get_mail_subject($post, $subscriber),
      wpns_get_mail_text($post, $subscriber),
      $headers
    );
  
  //}
  
}


/**
 * Get all blog subscribers
 * 
 * @return Array
 */
function wpns_get_subscribers() {
  
  #return get_users(array('role' => 'subscriber'));
  return get_users();
  
}


/**
 * Notify all subscribers when Post is published
 * 
 * @param int $post_id WordPress Post Id
 */
function wp_notify_subscribers($post_id) {
  
  // FIXME: It is possible, that the filter also excecutes when already was public
  
  $subscribers = wpns_get_subscribers();
  
  $post = get_post($post_id);
  
  foreach($subscribers as $subscriber) {
  
    wpns_send_mail($post, $subscriber);
  
  }
  
}

function wpns_install() {
  
  $initial_options = array();
  
  $initial_options['wpns_version']          = wpns_get_version();
  $initial_options['wpns_sender_name']      = get_bloginfo('name');
  $initial_options['wpns_sender_email']     = get_bloginfo('admin_email');
  $initial_options['wpns_message_subject']  = 'New message from Blog';
  $initial_options['wpns_message_text']     = "Hey [receiver_display_name],
    
There is a new post in [blog_name]. Check out the following url:

[url]

";
    
  update_option('wpns_options', $initial_options);
  
}

function wpns_uninstall() {
  
  delete_option('wpns_options');
  
}


$file_name = WP_PLUGIN_DIR . '/wp-notify-subscribers/wp-notify-subscribers.php';

error_log($file_name);

register_activation_hook( $file_name, 'wpns_install');
register_deactivation_hook($file_name, 'wpns_uninstall');

require_once('settings.php');

add_filter('publish_post', 'wp_notify_subscribers');


?>