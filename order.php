<?php
require_once(__DIR__.'/bootstrap.php');

$json_status=0; // This flag will be used to determine the right message to send to the client.
$json_extra=null;

if($session->tryAuthenticate()){
  // Can order
  $product_list=post_parameter('product_list');
  if($product_list!=null){
    $input = include_once(__DIR__.'/models/input.php');
    list($products,$products_id) = $input->parseProductList($product_list);

    $statement=$db->getPDO()->prepare(
      "SELECT product_id, price FROM pizzapp_products WHERE product_id IN ".($db->pdo_in_composer($products_id))
    );
    $statement->execute($products_id);
    $result=$statement->fetchAll();
    if($result){
      $user_id=$user->getLoggedUserID();

      $orders=array();
      $cost=0;

      foreach($result as $row){
        $product_id = $row['product_id'];

        $price = (double)$row['price'];
        $quantity = (int)$products[$product_id]['quantity'];

        $cost+=round($price*$quantity);

        array_push($orders,array($user_id, $product_id, null, $quantity));
      }

      $statement=$db->getPDO()->prepare(
        "INSERT INTO pizzapp_bill (cost) VALUES (?)"
      );
      $statement->execute(array($cost));
      $bill_id=(int)$db->getPDO()->lastInsertId();

      for($i=0;$i<count($orders);$i++){
        $orders[$i][2]=$bill_id;
      }

      try{
        $statement=$db->getPDO()->prepare(
          "INSERT INTO pizzapp_orders (user_id, product_id, bill_id, quantity) VALUES ".
          ($db->pdo_insertinto_composer($orders, '(?, ?, ?, ?)'))
        );
        $statement->execute($db->pdo_insertinto_executeparams($orders));

        $json_status=0;
        $json_extra=array('cost'=>$cost);
      }catch(PDOException $e){
        if($e->getCode()==23000){
          // Duplicate value
          $json_status=3;
        }else{
          // DB error (And/Or PDOException)
          $json_status=-1;
          $json_extra=$e->getMessage();
        }
      }
    }
  }else{$json_status=2;}
}else{$json_status=1;} // Cannot order

switch($json_status){
  default:
  case 0:
    http_response_code(200);
    $output=array('status'=>true, 'message'=>'ORDER_OK', 'extra'=>$json_extra);
    break;
  case 1:
    http_response_code(400);
    $output=array('status'=>false, 'message'=>'ERROR_NOT_LOGGED_IN');
    break;
  case 2:
    http_response_code(400);
    $output=array('status'=>false, 'message'=>'ERROR_NO_PRODUCTS');
    break;
  case 3:
    http_response_code(400);
    $output=array('status'=>false, 'message'=>'ERROR_DUPLICATE_ORDER');
    break;
  case -1:
    http_response_code(401);
    $output=array('status'=>false, 'message'=>'DB ERROR: '.$json_extra);
    break;
}

echo_json($output);