<?php
require_once(__DIR__."/bootstrap-core.php");

$statement = $db->getPDO()->prepare(
  "SELECT name, description, price, image FROM pizzapp_products ORDER BY product_id ASC"
);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$products = array();
foreach($result as $product){
  $productdata = array();
  $productdata["key"]=$product['name'];
  $productdata["desc"]=$product['description'];
  $productdata["price"]=$product['price'];
  $productdata["image"]=$product['image'];
  array_push($products,$productdata);
}

echo_json($products);