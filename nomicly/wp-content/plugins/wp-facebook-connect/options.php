<?php
/**
 * fb_connect_settings_missing()
 *
 * @package fb-connect
 * @since 1.0
 *
 * notification to set the settings before using plugin
 *
 */
function fb_connect_settings_missing(){ 
	?>
	<div class="error">
		<p><?php printf( __( 'Facebook Connect plugin is almost ready. To start using Facebook Connect <strong>you need to set your Facebook Application API ID and Faceook Application secret</strong>. You can do that in <a href="%1s">Facebook Connect settings page</a>.', 'wpsc' ), admin_url( 'options-general.php?page=fb_connect_options' ) ) ?></p>
	</div> 
	<?php
}

/**
 * fb_connect_menu()
 *
 * @package fb-connect
 * @since 1.0
 *
 * add options page and register settings
 *
 */
function fb_connect_menu() {
	add_options_page('FB Connect options', 'FB Connect', 'manage_options', 'fb_connect_options', 'fb_connect_options');
	add_action( 'admin_init', 'register_fb_connect_mysettings' );
}


/**
 * register_fb_connect_mysettings()
 *
 * @package fb-connect
 * @since 1.0
 *
 * register settings
 *
 */
function register_fb_connect_mysettings(){
	register_setting( 'fb_connect_settings', 'fbconnect_api_id' );
	register_setting( 'fb_connect_settings', 'fbconnect_secret' );
}

/**
 * fb_connect_options()
 *
 * @package fb-connect
 * @since 1.0
 *
 * options page
 *
 */
function fb_connect_options() {
  ?>
	<div class="wrap">
		<h2><?php _e('Facebook Connect'); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'fb_connect_settings' ); ?>
			
			<table class="form-table">
			
				<tr valign="top">
					<th scope="row"><?php _e('Facebook Application API ID:'); ?></th>
					<td><input type="text" name="fbconnect_api_id" value="<?php echo get_option('fbconnect_api_id'); ?>" /></td>
				</tr>
				 
				<tr valign="top">
					<th scope="row"><?php _e('Faceook Application secret:'); ?></th>
					<td><input type="text" name="fbconnect_secret" value="<?php echo get_option('fbconnect_secret'); ?>" /></td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		
		</form>
	</div>
  <?php
}
?>