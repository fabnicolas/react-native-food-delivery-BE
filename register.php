<?php
$config = require(__DIR__."/include/config.php");
require_once(__DIR__."/include/functions.php");
$db = include_once(__DIR__."/include/use_db.php");

// Inputs.
$email = post_parameter('email');
$password = post_parameter('password');

$error=0; // This flag will be used to determine the right message to send to the client.
$error_extra=null;

// Use model "user".
$user = include_once(__DIR__."/models/user.php");
$user->linkDB($db);

list($error,$error_extra)=$user->register($email, $password);

$output=null;

switch($error){
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
    $output=array('status'=>false, 'message'=>'DB ERROR: '.$error_extra);
    break;
}

echo_json($output);
