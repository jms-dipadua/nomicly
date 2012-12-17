<?php 
/*
Plugin Name: Facebook Connect
Description: A beautifully crafted light weight Facebook Connect Plugin that uses the new Facebook API to create WordPress user accounts.
Version: 1.6
Author: valentinas
License: GPL2
*/

//set the plugin path
$plugin_path = plugin_dir_path(__FILE__);

//constants - Application API ID and Application Secret
define('FACEBOOK_APP_ID', get_option('fbconnect_api_id'));
define('FACEBOOK_SECRET', get_option('fbconnect_secret'));

//options page functions - fb connect settings - api key and secret settings under Settings menu
require_once($plugin_path . 'options.php');
//add options page, setup menu, etc.. - backend
add_action('admin_menu', 'fb_connect_menu');

//if API ID and Secret are not set the plugin will not work, so it shows notifications
if( FACEBOOK_SECRET == '' || FACEBOOK_APP_ID == '' ){
	add_action( 'admin_notices', 'fb_connect_settings_missing' );
}else{
	//include main functions
	require_once($plugin_path . 'functions.php');
	//do localization
	add_action('plugins_loaded', 'wpfbc_load_textdomain');
	//add javascript to header
	add_action('init', 'facebook_header');
	//perform login process
	add_action('init', 'fb_login_user');
	//add markup to footer
	add_action('wp_footer', 'fb_footer');
	add_action('admin_print_footer_scripts', 'fb_footer');
	//modify logout URL
	add_filter('logout_url', 'fb_logout_url');
	
	//shortcode functions
	require_once($plugin_path . 'shortcode.php');
	//setup shortcode
	add_shortcode('fb_login', 'fb_login');
	
	//facebook login widget functions
	require_once($plugin_path . 'widget.php');
	//setup widget
	add_shortcode('fb_login', 'fb_login');
	
	//include avatar functions
	require_once($plugin_path . 'avatar.php');
	//replace avatar
	add_filter('get_avatar', 'fb_connect_replace_avatar', 5, 10);
}

//uninstal function
function uninstall_facebook_connect(){
	delete_option('fbconnect_api_id');
	delete_option('fbconnect_secret');
}
//resister uninstall function
register_uninstall_hook(__FILE__, 'uninstall_facebook_connect');
?>