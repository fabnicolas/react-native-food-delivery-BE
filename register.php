<?php
require_once(__DIR__."/bootstrap.php");

// Inputs.
$email = post_parameter('email');
$password = post_parameter('password');

// Variables to determine JSON data in response.
$json_status=0;
$json_extra=null;

list($json_status,$json_extra)=$user->register($email, $password);

$output=null;

switch($json_status){
  default:
  case 0:
    http_response_code(200);
    $output=array('status'=>true, 'message'=>'REGISTRATION_OK');
    break;
  case 1:
    http_response_code(400);
    $output=array('status'=>false, 'message'=>'INVALID_EMAIL');
    break;
  case 2:
    http_response_code(400);
    $output=array('status'=>false, 'message'=>'INVALID_PASSWORD');
    break;
  case 3:
    http_response_code(401);
    $output=array('status'=>false, 'message'=>'USER_EXISTING');
    break;
  case -1:
    http_response_code(400);
    $output=array('status'=>false, 'message'=>'DB ERROR: '.$json_extra);
    break;
}

echo_json($output);
