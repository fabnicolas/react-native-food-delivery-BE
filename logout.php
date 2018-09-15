<?php
require_once(__DIR__.'/bootstrap.php');

// Inputs.
$email = post_parameter('email');
$password = post_parameter('password');

$error=0; // This flag will be used to determine the right message to send to the client.
$error_extra=null;

list($error,$error_extra)=$user->logout($email, $password);