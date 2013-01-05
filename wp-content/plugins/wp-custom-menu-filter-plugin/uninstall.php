<?php
//If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

unregister_setting( 'wpcmfp_settings', 'wpcmfp_settings' );
delete_option('wpcmfp_settings');

?>