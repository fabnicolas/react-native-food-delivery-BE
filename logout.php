<?php
require_once(__DIR__.'/bootstrap.php');

// Inputs.
$email = post_parameter('email');
$password = post_parameter('password');

// Variables to determine JSON data in response.
$json_status=0;
$json_extra=null;

list($error,$error_extra)=$user->logout($email, $password);