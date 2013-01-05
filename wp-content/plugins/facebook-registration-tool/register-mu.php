<?php
require('../../../wp-load.php');

function parse_signed_request($signed_request, $secret) {
	list($encoded_sig, $payload) = explode('.', $signed_request, 2);

	// decode the data
	$sig = base64_url_decode($encoded_sig);
	$data = json_decode(base64_url_decode($payload), true);

	if (strtoupper($data['algorithm']) != 'HMAC-SHA256') {
		error_log('Unknown algorithm. Expected HMAC-SHA256');
		return null;
	}

	// check sig
	$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	if ($sig !== $expected_sig) {
		error_log('Bad Signed JSON signature!');
		return null;
	}

	return $data;
}

function base64_url_decode($input) {
	return base64_decode(strtr($input, '-_', '+/'));
}
$url = get_bloginfo('wpurl') . '/wp-signup.php';
if ($_REQUEST) {
	$options = get_option('fbregister_options');
	$response = parse_signed_request($_REQUEST['signed_request'], $options['app_secret']); 
	$user_login = $response['registration']['username'];
	$user_email = $response['registration']['email'];
	$signup_for = isset($response['registration']['signup_for']) ? $response['registration']['signup_for'] : 'user';
} else {
	header("location: $url");
}
?>
<html>
<head>
<title>Processing your request...</title>
</head>
<body OnLoad="OnLoadEvent();">
<form name="redirectForm" action="<?=$url?>" method="POST">
<noscript>
<br/><br/><div align="center">
<h1>Processing your Registration</h1>
<h2>JavaScript is currently disabled or is not supported by your browser</h2><br/>
<h3>Please click Submit to continue the processing of your registration.</h3>
<input type="submit" value="Submit" />
</div>
</noscript>
<input type="hidden" name="user_name" value="<?=$user_login?>" />
<input type="hidden" name="user_email" value="<?=$user_email?>" />
<!-- nonce stuff? -->
<?php do_action( "signup_hidden_fields" ); ?>
<!-- end nonce stuff -->
<input type="hidden" name="signup_for" value="<?= $signup_for ?>" /> 
<input type="hidden" name="stage" value="validate-user-signup" />
</form>
<script type="text/javascript">
<!--
function OnLoadEvent() {
	document.redirectForm.submit();
}
//-->
</script>
</body>
</html>

