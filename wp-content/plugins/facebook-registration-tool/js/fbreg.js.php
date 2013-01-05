<?php
	require('../../../../wp-load.php');
	$options = get_option('fbregister_options');
?>
jQuery(document).ready(function() {
	// add fb-root div element to top of BODY
	jQuery('<div>').attr('id', 'fb-root').prependTo(document.body);
<?php	if (defined('MULTISITE') && MULTISITE) : ?>
<?php
	// we're multisite, so let's first see if we are allowing signups
	$active_signup = get_site_option('registration');
	if ($active_signup == 'none') {
		echo 'return 0;';
		return 0;
	}

	// if we've made it this far, we allow signsups
?>
	// grab the current stage we're at
	var stage = jQuery('input[name="stage"]').attr('value');
	if (stage == 'validate-user-signup') {
		// only do any of this if we're on the validate-user-signup part
	
		// grab any errors we have so we can tack them back on after we delete the form
		var errors = jQuery('p.error');

		var fb_register = jQuery('<fb:registration></fb:registration>').attr('redirect_uri', '<?php echo plugins_url('/facebook-registration-tool/register-mu.php'); ?>');
		fb_register.attr('width', '575');
	<?php
		if ($active_signup == 'user') : ?>
		fb_register.attr('fields', "[{'name':'name'},{'name':'email'},{'name':'username','description':'Username','type':'text'}]");
		<?php elseif ($active_signup == 'all') : ?>
		fb_register.attr('fields', "[{'name':'name'},{'name':'email'},{'name':'username','description':'Username','type':'text'},{'name':'signup_for', 'description':'Register', 'type':'select', 'options':{'blog':'a username and site','user':'just a username'},'default':'user'}]");
		<?php endif; ?>
		jQuery('form#setupform').after(fb_register).detach();	
		fb_register.before(errors);

	} // endif (stage == 'validate-user-signup')
<?php	else : ?>
<?php		// not multisite	?>
	jQuery('p.register').detach();
	jQuery('form#registerform').after(jQuery('<p>').css('padding-top', '10px').html('A password will be e-mailed to you.'));
	var fb_register = jQuery('<fb:registration></fb:registration>').attr('fields', "[{'name':'name'},{'name':'email'},{'name':'username','description':'Username','type':'text'}]");
	fb_register.attr('redirect_uri', '<?php echo plugins_url('/facebook-registration-tool/register.php'); ?>');
	fb_register.attr('width', '530');
	jQuery('form#registerform').after(fb_register).detach();
<?php	endif; ?>
	FB.init({appId:'<?php echo $options['app_id']; ?>',xfbml:true});
});
