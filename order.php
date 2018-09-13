<?php
include_once(__DIR__.'/bootstrap.php');

$json_status=0; // This flag will be used to determine the right message to send to the client.
$json_extra=null;

$json_status=0;
$json_extra=null;


if($session->tryAuthenticate()){
  // Can order
  $product_list=post_parameter('product_list');
  if($product_list!=null){
    $products=array();
    $products_id=array();

    // Parse product list
    $product=explode("|", $product_list);
    for($i=0;$i<count($product);$i++){
      $product_info=explode(";", $product[$i]);
      if(!empty($product_info[0]) && !empty($product_info[1])){
        $id=$product_info[0];
        array_push($products_id,$id);
        $products[$id]['quantity']=$product_info[1];
      }
    }

    // Assume products and counters are valid

    /*
      product[0]['product_id'] == 'Misto fritto'
      product[0]['product_quantity'] == '4'
    */
    $statement=$db->getPDO()->prepare(
      "SELECT * FROM pizzapp_products WHERE product_id IN ".($db->pdo_in_composer($products_id))
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
        $quantity = (double)$products[$product_id]['quantity'];

        $cost+=($price*$quantity);

        array_push($orders,array($user_id, $product_id, $quantity));
      }
      try{
        $statement=$db->getPDO()->prepare(
          "INSERT INTO pizzapp_orders (user_id, product_id, quantity) VALUES ".
          ($db->pdo_insertinto_composer($orders, '(?, ?, ?)'))
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