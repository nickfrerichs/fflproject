<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Need some variables from consolidated config file.
include(FCPATH.'config.php');

$config['smtp_host'] = $fflp_smtp_host;
$config['smtp_user'] = $fflp_smtp_user;
$config['smtp_pass'] = $fflp_smtp_pass;
$config['smtp_port'] = $fflp_smtp_port;
$config['newline'] = $fflp_smtp_newline;
$config['protocol'] = $fflp_smtp_protocol;
