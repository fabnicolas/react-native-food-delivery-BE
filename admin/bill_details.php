<?php
require_once(__DIR__.'/../bootstrap.php');

// Variables to determine JSON data in response.
$json_status=0;
$json_extra=null;

if($session->tryAuthenticate()){
  $current_user_id = $user->getLoggedUserID();
  $current_user = $user->getUser($current_user_id);
  if($current_user!=null){
    $statement = $db->getPDO()->prepare("
      SELECT x.user_id FROM 
        pizzapp_users AS x
        NATURAL JOIN
        pizzapp_user_assigned_roles AS y
        NATURAL JOIN
        pizzapp_user_roles AS z
      WHERE 
        x.user_id = ?
        AND
        z.name = 'admin'
      LIMIT 1;
    ");
    $statement->execute(array($current_user_id));
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if($result){
      // The user is an admin.

      $bill_id = post_parameter('bill_id');

      $statement = $db->getPDO()->prepare("
        SELECT
          y.name,
          x.quantity,
          y.price
        FROM
          pizzapp_orders AS x
          NATURAL JOIN
          pizzapp_products AS y
        WHERE x.bill_id = ?
      ");
      $statement->execute(array($bill_id));
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
      
      if($result){
        $json_status=0;
        $json_extra=array('data'=>$result);
      }else $json_status=4;
    }else $json_status=3; // This user does not have role 'admin' assigned to himself
  }else $json_status=2; // User not existing
}else $json_status=1; // Cannot authenticate

switch($json_status){
  default:
  case 0:
    http_response_code(200);
    $output=array('status'=>true, 'message'=>'BILL_DETAILS', 'extra'=>$json_extra);
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
    $output=array('status'=>false, 'message'=>'DB ERROR: '.$json_extra);
    break;
}

echo_json($output);
?>