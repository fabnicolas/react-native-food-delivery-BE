<?php
$config = require(__DIR__."/include/config.php");
require_once(__DIR__."/include/functions.php");
$db = include_once(__DIR__."/include/use_db.php");

$statement = $db->getPDO()->prepare(
  "SELECT name, description, price, image FROM pizzapp_products ORDER BY product_id ASC"
);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$text='[';
foreach($result as $product){
  $text.='{'.
    '"key": "'.$product["name"].'", '.
    '"desc": "'.$product["description"].'", '.
    '"price": "'.$product["price"].'", '.
    '"image": "'.$product["image"].'"'.
    '},';
}
$text=substr($text, 0, -1);
$text.="]";

echo ($text);