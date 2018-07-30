
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include(FCPATH.'config.php');

$config['recaptcha_sitekey']   = $fflp_recaptcha_public_key;
$config['recaptcha_secretkey'] = $fflp_recaptcha_private_key;
$config['lang']                = "en";


?>