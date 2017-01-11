<?php
require(dirname(__FILE__).'/../../config/config.inc.php');


$product = new Product($_GET['id_product']);
$id_product = $_GET['id_product'];
$id_product_attribute = $_GET['id_product_attribute'];
$quantite = $_GET['quantite'];

$price = $product->getPriceStatic($id_product, true, $id_product_attribute, 2, 0, false, true, $quantite, false, null,  $cookie->id_cart);

$price = number_format($price*$quantite, 2) . " &euro;";

echo $price;
?>