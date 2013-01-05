<?php
/**
 * fb_login()
 *
 * @package fb-connect
 * @since 1.0
 *
 * shortcode for FB connect button. this is used in widget so keep that in mind if you change anything here
 *
 */
function fb_login($atts){
	global $wpdb;
	extract(shortcode_atts(array(
		'size' => 'medium',
		'login_text' => __('Login', 'wp-facebook-connect'),
		'logout_text' => __('Logout', 'wp-facebook-connect'),
		'connect_text' => __('Connect', 'wp-facebook-connect')
	), $atts));
	
	$cookie = get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);
	$perms = apply_filters('fb_connect_perms', array('email'));
	//only show facebook connect when user is not logged in
	if( is_user_logged_in() ) {
		if( $cookie ) {
			do_action('fb_connect_button_fb_wp');
			?>
			<a class="fb_button fb_button_<?php echo $size; ?>" href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>">
				<span class="fb_button_text">
					<?php echo $logout_text; ?>
		    	</span>
		    </a>
		    <?php
		} else {
			do_action('fb_connect_button_nofb_wp');
			?>
			<fb:login-button perms="<?php echo implode(',', $perms); ?>" size="<?php echo $size; ?>" >
				<?php echo $connect_text; ?>
			</fb:login-button>
			<?php
		}
	} else {
		if( $cookie ) {
			//this should never happen, because there is login process on 
			//INIT and by this time you should either be loged in or have new user created and loged in
			do_action('fb_connect_button_fb_nowp');
			_e('Facebook Connect error: login process failed!', 'wp-facebook-connect');
		} else {
			do_action('fb_connect_button_nofb_nowp');
			?>
			<fb:login-button perms="<?php echo implode(',', $perms); ?>" size="<?php echo $size; ?>" >
				<?php echo $login_text; ?>
			</fb:login-button>
			<?php
		}
	}
}
?>