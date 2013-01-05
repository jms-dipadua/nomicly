<?php
/*
Plugin Name:	Facebook Registration Tool 
Version:	0.3
Author:		iEntry, Inc.
Author URI:	http://www.beyondwp.com
License: 	GPLv2
*/

/* Copyright 2010  iEntry, Inc.  (email : mmarr@ientry.com )

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


if (!class_exists('fbregister')) {
	class fbregister {
		protected $configured = false, $connected = false, $app_id, $app_secret;

		function add_settings_page() {
			add_options_page('Facebook Registration', 'Facebook Registration Plugin Settings', 'manage_options', 'fbregister', array(&$this, 'settings_page'));
		}

		function settings_page() {
 		?>
			<h2>Facebook Registration Plugin Settings</h2>
			<form action="options.php" method="post">
			<?php settings_fields('fbregister_options'); ?>
			<?php do_settings_sections('fbregister'); ?>
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Submit" /></p>
			</form>
		<?php
		}

		function admin_init() {
			$options = get_option('fbregister_options');
			if ( !$this->test_settings() ) {
				add_action( 'admin_notices', create_function( '', "echo '<div class=\"error\"><p>".sprintf('Facebook Registration is not properly configured. Please go to the <a href="%s">settings</a> page to resolve.', admin_url('options-general.php?page=fbregister'))."</p></div>';" ) );
			} 
			register_setting( 'fbregister_options', 'fbregister_options' );
			add_settings_section( 'fbregister_default', null, array(&$this, 'fbregister_settings_text'), 'fbregister');
			add_settings_field( 'app_id', 'Facebook Application ID', array(&$this, 'fbregister_app_id'), 'fbregister', 'fbregister_default' );
			add_settings_field( 'app_secret', 'Facebook Application Secret', array(&$this, 'fbregister_app_secret'), 'fbregister', 'fbregister_default' );
		}

		function fbregister_settings_text() {
			if (!$this->configured) {
				// not configured, so show some helpful information about facebook app creation
			?>
				<div style="border: 1px solid black; padding: 10px; margin: 25px;">
				<h3>What's this Facebook App ID/Secret?</h3>
				<p>In order to use the Facebook Registration Tool Plugin for Wordpress, a Facebook application is required. A Facebook application is required in order to use Facebook's tools, such as the Registration Tool this plugin integrates. It's easy to create, just check out the tutorial <a href="http://beyondwp.com/2010/12/23/whats-the-big-facebook-app-secret/">here.</a></p>
				<p>Already have a Facebook app? Click <a href="http://www.facebook.com/developers/apps.php" target="_blank">here</a> to find your ID and Secret.</p>
				</div>
			<?php
			} else {
				$this->test_fb_connection(true);
			}
		}

		protected function test_fb_connection($debug = false) {
			if ($this->configured) {

			$stime = microtime(true);
			// is configured, so lets make sure the App ID and Secret are valid
			include_once('libs/facebook.php');
			$fb = new Facebook(array('appId' => $this->app_id, 'secret' => $this->app_secret));

			$error = false;
			try {
				$resp = $fb->api(array('method'=>'admin.getAppProperties','properties'=>'connect_url'));
			} catch (Exception $e) {
				$error = true;
				echo ($debug) ? '<p class="error">There is an error with your Facebook settings: "' : '';
				echo ($debug) ? "Error #{$e->getCode()}: {$e->getMessage()}\"." : '';
				switch ($e->getCode()) {
				case 104:
					echo ($debug) ? ' This probably means your <strong>Application ID</strong> is incorrect.' : '';
					break;
				case 190:
					echo ($debug) ? ' This probably means your <strong>Application Secret</strong> is incorrect.' : '';
					break;
				}
				echo ($debug) ? '</p>' : '';
			}
			$etime = microtime(true);
			$time = $etime - $stime;
#			echo "Facebook check took $time seconds\n";

			$this->connected = !$error;

			return !$error;

			} else {
				 return false;	# not configured, so let's not even try to connect!
			}
		}

		protected function test_settings() {
			$options = get_option('fbregister_options');
			if ( empty($options['app_id']) || empty($options['app_secret']) ) {
				return false;
			} else {
				$this->configured = true;
				$this->app_id = $options['app_id'];
				$this->app_secret = $options['app_secret'];
			}
			return true;
		}

		function fbregister_app_id() {
			$options = get_option('fbregister_options');
			echo "<input id='fbregister_app_id' name='fbregister_options[app_id]' class='regular-text' value='{$options['app_id']}' />";
		}

		function fbregister_app_secret() {
			$options = get_option('fbregister_options');
			echo "<input id='fbregister_app_secret' name='fbregister_options[app_secret]' class='regular-text' value='{$options['app_secret']}' />";
		}

		function init() {
			$options = get_option('fbregister_options');
			if ($this->test_settings() && $this->test_fb_connection()) {
				wp_enqueue_script('fbSDK', 'http://connect.facebook.net/en_US/all.js');
				wp_enqueue_script('fbreg', plugins_url('js/fbreg.js.php', __FILE__), array('jquery'));
				if (defined('MULTISITE') && MULTISITE) {
					// do special things for multisite?
				} else {
					add_action('login_head', array(&$this, 'login_head'));
					add_action('register_form', array(&$this, 'register_form'));
					wp_enqueue_style('fbreg_css', plugins_url('css/fbreg.css', __FILE__));
				}
			}
		}

		function login_head() {
			global $wp_scripts;
			$wp_scripts->print_scripts();
			wp_print_styles();
		}
	}
}

if (class_exists('fbregister')) {
	$fbregister = new fbregister();
}

if (isset($fbregister)) {
	add_action('admin_init', array(&$fbregister, 'admin_init'));
	add_action('admin_menu', array(&$fbregister, 'add_settings_page'));
	add_action('init', array(&$fbregister, 'init'));

}



?>
