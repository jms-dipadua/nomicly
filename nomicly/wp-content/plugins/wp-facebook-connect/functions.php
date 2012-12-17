<?php
/**
 * facebook_header()
 *
 * @package fb-connect
 * @since 1.0
 *
 * javascript for header
 * taken from here: http://developers.facebook.com/docs/guides/web
 *
 */
function facebook_header(){
	wp_enqueue_script( 'facebook_connect_js_functions', 'http://connect.facebook.net/en_US/all.js', array('jquery') );
}

/**
 * wpfbc_load_textdomain()
 *
 * @package fb-connect
 * @since 1.0
 *
 * localization
 *
 */
function wpfbc_load_textdomain($plugin_path) {
	load_plugin_textdomain( 'wp-facebook-connect', false, dirname( plugin_basename( __FILE__ ) ) );
}

/**
 * fb_logout_url()
 *
 * @package fb-connect
 * @since 1.0
 *
 * logout url for people who are logged in with FB
 *
 */
function fb_logout_url($url){
	$cookie = get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);
	if($cookie)
		return "javascript:FB.logout(function(){location.href='" . $url . "'})";
	else
		return $url;
}

/**
 * fb_footer()
 *
 * @package fb-connect
 * @since 1.0
 *
 * markup for footer
 * taken from here: http://developers.facebook.com/docs/guides/web
 *
 */
function fb_footer(){
?> 
<script type="text/javascript">
jQuery(document).ready(function(){
	  FB.init({appId: '<?php echo FACEBOOK_APP_ID; ?>', status: true,
	           cookie: true, xfbml: true});
	  FB.Event.subscribe('auth.sessionChange', function(response) {
	    if (response.session) {
	    jQuery('body').html('');
	      window.location.href=window.location.href;
	    } else {
	    jQuery('body').html('');
	      window.location.href=window.location.href;
	    }
	  });
});
</script>
<div id="fb-root"></div>
<?php
}

/**
 * get_facebook_cookie()
 *
 * @package fb-connect
 * @since 1.0
 *
 * gets facebook cookie (yummy thing that is created when user is authenticated with FB in your website)
 * taken from here: http://developers.facebook.com/docs/guides/web
 *
 * @return array|null
 *
 */
function get_facebook_cookie($app_id, $application_secret) {
  $args = array();
  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  ksort($args);
  $payload = '';
  foreach ($args as $key => $value) {
    if ($key != 'sig') {
      $payload .= $key . '=' . $value;
    }
  }
  if (md5($payload . $application_secret) != $args['sig']) {
    return null;
  }
  return $args;
}

/**
 * fb_login_user()
 *
 * @package fb-connect
 * @since 1.0
 *
 * this is the main function that performs the login or user creation process
 *
 * @return true
 */
function fb_login_user(){
	global $wpdb;
	//@todo: investigate: does this gets included doing regular request?
	require_once( ABSPATH . 'wp-includes/registration.php' );
	//mmmm, cookie
	$cookie = get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);
	//if we have cookie, then try to get user data
	if ($cookie) {
		//get user data
	    $user = json_decode(@file_get_contents('https://graph.facebook.com/me?access_token=' . $cookie['access_token']));
	    //if user data is empty, then nothing will happen
	    if( !empty($user) ){
	    	//this should never happen, since email address is required to register in FB
	    	//I put it here just in case of API changes or some other disaster, like wrong API key or secret
		    if( !isset($user->email) || empty($user->email) ){
		    	do_action('fb_connect_get_email_error');
		    }

	    	//if user is logged in, then we just need to associate FB account with WordPress account
	    	if( is_user_logged_in() ){
    			global $current_user;
				get_currentuserinfo();
				$fb_uid = get_user_meta($current_user->ID, 'fb_uid', true);
	
				if($fb_uid == $user->id)
					return true;
					
				if( $user->email == $current_user->user_email ) {
					//if FB email is the same as WP email we don't need to do anything.
					do_action('fb_connect_wp_fb_same_email');
					$fb_uid = get_user_meta($current_user->ID, 'fb_uid', true);
					if( !$fb_uid )
						update_user_meta( $current_user->ID, 'fb_uid', $user->id );
					return true;
				} else {
					//else we need to set fb_uid in user meta, this will be used to identify this user
					do_action('fb_connect_wp_fb_different_email');
					$fb_uid = get_user_meta($current_user->ID, 'fb_uid', true);
					if( !$fb_uid )
						update_user_meta( $current_user->ID, 'fb_uid', $user->id );
					$fb_email = get_user_meta($current_user->ID, 'fb_email', true);
					if( !$fb_uid )	
						update_user_meta( $current_user->ID, 'fb_email', $user->email );
					//that's it, we don't need to do anything else, because the user is already logged in.
					return true;
				}
	    	}else{
			    //check if user has account in the website. get id
			    $existing_user = $wpdb->get_var( 'SELECT DISTINCT `u`.`ID` FROM `' . $wpdb->users . '` `u` JOIN `' . $wpdb->usermeta . '` `m` ON `u`.`ID` = `m`.`user_id`  WHERE (`m`.`meta_key` = "fb_uid" AND `m`.`meta_value` = "' . $user->id . '" ) OR user_email = "' . $user->email . '" OR (`m`.`meta_key` = "fb_email" AND `m`.`meta_value` = "' . $user->email . '" )  LIMIT 1 ' );
			    //if the user exists - set cookie, do wp_login, redirect and exit
			    if( $existing_user > 0 ){
			    	$fb_uid = get_user_meta($existing_user, 'fb_uid', true);
			    	if( !$fb_uid )
			    		update_user_meta( $new_user, 'fb_uid', $user->id );
			    	$user_info = get_userdata($existing_user);
			    	do_action('fb_connect_fb_same_email');
			    	wp_set_auth_cookie($existing_user, true, false);
			    	do_action('wp_login', $user_info->user_login);
			    			    if (wp_get_referer()) {
	wp_redirect(wp_get_referer());
} else {
	wp_redirect( $_SERVER['REQUEST_URI'] );
}
			    	exit();
			    //if user don't exist - create one and do all the same stuff: cookie, wp_login, redirect, exit
				} else {
					do_action('fb_connect_fb_new_email');
					//sanitize username
					$username = sanitize_user($user->first_name, true);
	
					//check if username is taken
					//if so - add something in the end and check again
					$i='';
					while(username_exists($username . $i)){
						$i=absint($i);
						$i++;
					}
					
					//this will be new user login name
					$username = $username . $i;
					
					//put everything in nice array
					$userdata = array(
						'user_pass'		=>	wp_generate_password(),
						'user_login'	=>	$username,
						'user_nicename'	=>	$username,
						'user_email'	=>	$user->email,
						'display_name'	=>	$user->name,
						'nickname'		=>	$username,
						'first_name'	=>	$user->first_name,
						'last_name'		=>	$user->last_name,
						'role'			=>	'subscriber'
					);
					$userdata = apply_filters('fb_connect_new_userdata', $userdata, $user);
					//create new user
					$new_user = absint(wp_insert_user($userdata));
					do_action('fb_connect_new_user', $new_user);
					//if user created succesfully - log in and reload
					if( $new_user > 0 ){
						update_user_meta( $new_user, 'fb_uid', $user->id );
						$user_info = get_userdata($new_user);
						wp_set_auth_cookie($new_user, true, false);
						do_action('wp_login', $user_info->user_login);
				    	wp_redirect(wp_get_referer());
				    	exit();
					} else {
						echo('Facebook Connect: Error creating new user!');
					}
				}	    	
	    	}
		}
    }
}
?>