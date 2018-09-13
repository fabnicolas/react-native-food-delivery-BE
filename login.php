<?php
include_once(__DIR__.'/bootstrap.php');

// Inputs.
$email = post_parameter('email');
$password = post_parameter('password');

$error=0; // This flag will be used to determine the right message to send to the client.
$error_extra=null;

list($error,$error_extra)=$user->login($email, $password);

switch($error){
  default:
  case 0:
    http_response_code(200);
    $output=array('status'=>true, 'message'=>'LOGIN_OK');
    break;
  case 1:
  case 2:
  case 3:
  case 4:
    http_response_code(400);
    $output=array('status'=>false, 'message'=>'INVALID_DATA');
    break;
  case -1:
    http_response_code(401);
    $output=array('status'=>false, 'message'=>'DB ERROR: '.$error_extra);
    break;
}

echo_json($output);