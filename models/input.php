<?php
class Input{
  function __construct(){}
  
  function parseProductList($product_list){
    $products=array();
    $products_id=array();

    $product=explode("|", $product_list);
    for($i=0;$i<count($product);$i++){
      $product_info=explode(";", $product[$i]);
      if(!empty($product_info[0]) && !empty($product_info[1])){
        $id=$product_info[0];
        array_push($products_id,$id);
        $products[$id]['quantity']=$product_info[1];
      }
    }

    return array($products, $products_id);
  }


}

return new Input();
?>