<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Need some variables from consolidated config file.
include(FCPATH.'config.php');

$config['smtp_host'] = $fflp_smtp_host;
$config['smtp_user'] = $fflp_smtp_user;
$config['smtp_pass'] = $fflp_smtp_pass;
$config['port'] = $fflp_smtp_port;
