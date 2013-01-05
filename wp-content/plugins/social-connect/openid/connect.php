<?php
require_once(dirname(dirname(__FILE__)) . '/openid/openid.php');
require_once(dirname(dirname(__FILE__)) . '/utils.php' );

try {
  if(!isset($_GET['openid_mode']) || $_GET['openid_mode'] == 'cancel') {
    $openid = new LightOpenID;
    $openid->identity = urldecode($_GET['openid_url']);
    $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
    header('Location: ' . $openid->authUrl());
  } else {
    $openid = new LightOpenID;
    if($openid->validate()) {
      $open_id = $openid->identity;
      $attributes = $openid->getAttributes();
      $email = $attributes['contact/email'];
      $first_name = $attributes['namePerson/first'];
      $last_name = $attributes['namePerson/last'];
      $signature = social_connect_generate_signature($open_id);
      ?>
<html>
<head>
<script>
function init() {
  window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'openid', 
    'social_connect_openid_identity' : '<?php echo $open_id ?>',
    'social_connect_signature' : '<?php echo $signature ?>',
    'social_connect_email' : '<?php echo $email ?>',
    'social_connect_first_name' : '<?php echo $first_name ?>',
    'social_connect_last_name' : '<?php echo $last_name ?>'});
    
  window.close();
}
</script>
</head>
<body onload="init();">
</body>
</html>      
      <?php
    }
  }
} catch(ErrorException $e) {
  echo $e->getMessage();
}
?>
