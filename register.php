<?php
$config = require(__DIR__."/include/config.php");
require_once(__DIR__."/include/functions.php");
$db = include_once(__DIR__."/include/use_db.php");

$email = post_parameter('email');
$password = post_parameter('password');

$error=0;

if(!($email!=null && filter_var($email, FILTER_VALIDATE_EMAIL))){
  $error=1;
}else if(!($password!=null && strlen($password)>=8)){
  $error=2;
}else{
  $password_salt = unique_random_string(3);
  $password_hashed = hash('sha256', $password.$password_salt);
  
  try{
    $statement = $db->getPDO()->prepare(
      "INSERT INTO pizzapp_users (email, password, password_salt) VALUES (:email, :password, :password_salt)"
    );

    $db->pdo_multibindParams($statement, array(
      'email'=>$email,
      'password'=>$password_hashed,
      'password_salt'=>$password_salt
    ));
    if($statement->execute()){
      $error=0;
    }else{
      $error=3;
    }
  }catch(PDOException $e){
    echo $e->getMessage();
  }
}

$output=null;

switch($error){
  default:
  case 0:
    http_response_code(200);
    $output=array('status'=>true, 'message'=>'OK');
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
}

echo var_export($output,true);
