<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Need some variables from consolidated config file.
include(FCPATH.'config.php');

$config['basic_debug'] = $fflp_debug;
$config['use_recaptcha'] = $fflp_use_recaptcha;
$config['fflp_email_reply_to'] = $fflp_email_reply_to;
$config['fflp_email_site_title'] = $fflp_email_site_title;
$config['fflp_recaptcha_public_key'] = $fflp_recaptcha_public_key;
$config['fflp_recaptcha_private_key'] = $fflp_recaptcha_private_key;
