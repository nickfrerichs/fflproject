<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Need some variables from consolidated config file.
include(FCPATH.'config.php');

$config['basic_debug'] = $fflp_debug;
$config['use_recaptcha'] = $fflp_use_recaptcha;
