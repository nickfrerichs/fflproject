<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$fflp_baseurl = '';
$fflp_encryption_key = '';

$fflp_dbhost = '';
$fflp_dbname = '';
$fflp_dbuser = '';
$fflp_dbpass = '';

$fflp_salt = '';

// Set these if you want to use Google's reCAPTCHA
$fflp_use_recaptcha = False;
$fflp_recaptcha_public_key = '';
$fflp_recaptcha_private_key = '';

$fflp_site_title = '';
$fflp_admin_email = '';
$fflp_email_site_title = '';
$fflp_email_reply_to = '';

$fflp_smtp_host = 'ssl://smtp.gmail.com';
$fflp_smtp_user = '';
$fflp_smtp_pass = '';
$fflp_smtp_port = 465;

// Set this to smtp to use these settings, or sendmail to ue your local settings
$fflp_smtp_protocol = "smtp";
// Set this to "\r\n" "\r" or "\n", can cause auth issues if not correct for your OS.
$fflp_smtp_newline = "\r\n";

// Show debug and profiler data
$fflp_debug = False;
$fflp_log_threshold = 0;

// Log user out (expire session) after this many seconds of of inactivity (1 week default)
$fflp_session_expire = 60*60*24*7;
