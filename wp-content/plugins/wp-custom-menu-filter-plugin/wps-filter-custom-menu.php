<?php
/*
Plugin Name: WP Custom Menu Filter Plugin
Plugin URI: http://wpsmith.net/wordpress-plugins/wp-custom-menu-filter-plugin
Description: This filters the WP Custom Menu based on whether user is logged in or not.
Version: 0.7
Author: wpsmith
Author URI: http://wpsmith.net
*/

/*  Copyright 2011  Travis Smith  (email : travis@wpsmith.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Registration
register_activation_hook( __FILE__ , 'wps_install' );
function wps_install() {
	global $wp_version;
	
	if ( version_compare( $wp_version , "3.1" , "<" ) ) {
		deactivate_plugins( basename( __FILE__ ) ); //deactivate plugin
		wp_die( "This plugin requires WordPress version 3.1 or higher." );
	}
}

// define defaults
$wpcmfp_ = array ();

$menus = wp_get_nav_menus();
$count = count( $menus );

for ( $i=0; $i < $count; $i++ ) {
	$wpcmfp_defaults[ 'menu-' . $menus[$i]->term_taxonomy_id . '-loggedout' ] = 'loggedout';
	$wpcmfp_defaults[ 'menu-' . $menus[$i]->term_taxonomy_id . '-loggedin' ] = 'loggedin';
}

$wpcmfp_settings = get_option( 'wpcmfp_settings' );

// Fallback
$wpcmfp_settings = apply_filters( 'wpcmfp_defaults' , wp_parse_args( $wpcmfp_settings, $wpcmfp_defaults ) , $wpcmfp_defaults );

// Localization
define( "WPSFM_DOMAIN" , 'wps_cmfp' );

//	this function registers our settings in the db
add_action( 'admin_init', 'wps_register_settings' );
function wps_register_settings() {
	register_setting( 'wpcmfp_settings' , 'wpcmfp_settings' , 'wpcmfp_settings_validate' );
}

// execute our settings section function
add_action( 'admin_menu', 'wps_add_menu' );
function wps_add_menu() {
    add_theme_page( 'Custom Menu Filter Settings' , 'Custom Menu Filter Settings' , 'manage_options' , 'wps-filter-menu' , 'wps_settings_page' ); 
}

// add "Settings" link to plugin page
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wps_plugin_action_links' );
function wps_plugin_action_links( $links ) {
	$wpcmfp_settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'themes.php?page=wps-filter-menu' ), __('Settings') );
	array_unshift( $links , $wpcmfp_settings_link );
	return $links;
}

// settings section
function wps_settings_page() {

	echo '<div class="wrap">';
	
	//	the settings management form
	wpcmfp_settings_settings_admin();

	echo '</div>';
	
}

//	this function checks to see if we just updated the settings
//	if so, it displays the "updated" message.
function wpcmfp_settings_update_check() {
	global $wpcmfp_settings;
	
	if( isset( $wpcmfp_settings['update'] ) ) {
		_e( '<div class="updated fade" id="message"><p>Settings <strong>' . $wpcmfp_settings['update'] . '</strong></p></div>' , WPSFM_DOMAIN );
		unset( $wpcmfp_settings['update'] );
		update_option( 'wpcmfp_settings' , $wpcmfp_settings );
	}
}

function wpcmfp_settings_settings_admin() {
	global $wpcmfp_settings;
	// Array ( [loggedin_hidden] => wps_li_hidden [loggedout_hidden] => wps_lo_hidden ) 
	
	// Check to see if it was just updated
	wpcmfp_settings_update_check();
	
	// Check that the user has the required capability 
    if ( !current_user_can( 'manage_options' ) )
    {
      die( __( ' You do not have sufficient permissions to access this page.' ) );
    }
	
	// Get all menus
	$nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
	
	// Get Menu Locations
	//$menu_locations = get_nav_menu_locations();

	// Get number of menus
	$num_menus = count( array_keys( $nav_menus ) ); ?>
	
	<form method="post" action="options.php"> 
	<?php
	
	settings_fields('wpcmfp_settings');
	global $wpcmfp_settings; 
	
    // Now display the settings editing screen
    echo '<div class="wrap">';
	
    // header
    echo "<h2>" . __( 'WP Custom Menu Filter Plugin Settings', WPSFM_DOMAIN ) . "</h2>";

    // settings form
    ?>

		<form name="form1" method="post" action="">
			<?php 
			// Count used to remove <hr /> from last item
			$i = 0;
				foreach ( $nav_menus as $menu ) { 
					$i++;
				?>
				
			<h3><?php _e( 'Menu Title: ' . $menu -> name , WPSFM_DOMAIN ); ?></h3>
			<p><?php _e( 'ID: ' . $menu -> term_id , WPSFM_DOMAIN ); ?></p>
			
			<p><?php _e("Users Logged Out CSS Class Name:", WPSFM_DOMAIN ); ?> 
				<input type="text" name="wpcmfp_settings[<?php echo 'menu-' . $menu -> term_id . '-loggedout'; ?>]" value="<?php echo isset( $wpcmfp_settings[ 'menu-' . $menu -> term_id . '-loggedout' ] ) ? $wpcmfp_settings[ 'menu-' . $menu -> term_id . '-loggedout' ] : '' ; ?>" size="20"><br />
				<?php _e("<em>Enter the CSS class name for items that you want to be hidden from users not logged in (e.g., logged-out).</em>", WPSFM_DOMAIN ); ?> 
			</p>
			
			<p><?php _e("Users Logged In CSS Class Name:", WPSFM_DOMAIN ); ?> 
				<input type="text" name="wpcmfp_settings[<?php echo 'menu-' . $menu -> term_id . '-loggedin'; ?>]" value="<?php echo isset( $wpcmfp_settings[ 'menu-' . $menu -> term_id . '-loggedin' ] ) ? $wpcmfp_settings[ 'menu-' . $menu -> term_id . '-loggedin' ] : '' ; ?>" size="20"><br />
				<?php _e("<em>Enter the CSS class name for items that you want to be hidden from users logged in (e.g., logged-in).</em>", WPSFM_DOMAIN ); ?> 
			</p>
			
					<?php if ( $num_menus != $i ) : ?> <hr /> <?php endif;
				}
				
				if ( count( $nav_menus ) == 0 ) { ?>
					<p><em><?php _e('You first need to create a <a href="' . get_option( 'siteurl' ) . '/wp-admin/nav-menus.php">custom WordPress menu</a>' , WPSFM_DOMAIN ); ?></em></p>
			<?php }

			?>

			<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
			<input type="hidden" name="wpcmfp_settings[update]" value="Updated" />

		</form>
</div>
<?php

}

function wpcmfp_settings_validate( $input ) {
	
	// Remove any HTML, text only
	foreach ( $input as $key => $val ) {
		$input[ $key ] = wp_filter_nohtml_kses( $val );
	}
	return $input;
}

add_filter( 'wp_nav_menu_objects', 'wps_custom_nav_menu_items' , 90 , 2 );
function wps_custom_nav_menu_items( $sorted_menu_items, $args = array() ) {
	global $wpcmfp_settings;
	$args = (array) $args;
	
	// Get menu id
	if ( $args['menu_id'] )
		$menu_id = $args['menu_id'];
	elseif ( $args['theme_location'] ) {
		$menu_locations = get_nav_menu_locations();
		$menu_id = $menu_locations[$args['theme_location']];
	}
	else {
		$nav_item_db_id = $sorted_menu_items[1]->ID;
		$nav_menu = wp_get_object_terms( $nav_item_db_id, 'nav_menu' );
		$menu_id = $nav_menu[0]->term_id;
	}
	
	// Get the class to exclude
	if ( ! is_user_logged_in() ) {
		$exclusion_class = $wpcmfp_settings[ 'menu-' . $menu_id . '-loggedout' ];
	}
	else {
		$exclusion_class = $wpcmfp_settings[ 'menu-' . $menu_id . '-loggedin' ];
	}
	
	$modified_nav_items = array();
	
	// Cycle through all nav_items
	foreach ( $sorted_menu_items as $nav_item ) {	
		
		// Cycle through all classes
		for ( $i=0; $i < count( $nav_item->classes ); $i++ ) {
			$exclude = false;
			
			// if nothing is there set to add
			if ( strlen ( $nav_item->classes[ $i ] ) < 1 ) {
				$exclude = false;
			}
			else
			{	
				// if matches add to exclusion array & break loop
				if ( $nav_item->classes[ $i ] == $exclusion_class ) {
					$excluded_nav_items[] = $nav_item; 
					$exclude = true;
					break;
				}
			}
		}
		
		if ( $exclude != true )
			$modified_nav_items[] = $nav_item;
							
	}

	return $modified_nav_items;
}
