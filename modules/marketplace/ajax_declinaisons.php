<?php
require(dirname(__FILE__).'/../../config/config.inc.php');

$product = new Product($_GET['id_product']);
$attributesGroups = $product->getAttributesGroups(intval($cookie->id_lang));
//var_dump($attributesGroups);
$ret_attrs = array();
if (count($attributesGroups)!=0) {
	foreach ($attributesGroups as $attr) {
		if (!key_exists($attr['id_attribute'], $ret_attrs) && $attr['id_attribute_group'] == 12) 
		{
				$ret_attrs[$attr['id_attribute']]['id'] = $attr['id_product_attribute'];
				$ret_attrs[$attr['id_attribute']]['position'] = $attr['position'];
				$ret_attrs[$attr['id_attribute']]['name'] = $attr['attribute_name'];
				$ret_attrs[$attr['id_attribute']]['group_name'] = $attr['public_group_name'];
		}
	}
}
// on change l'ordre /* SRDEV - change l'ordre par position */
usort($ret_attrs, function($a, $b) {
	return $a['position'] - $b['position'];
});

echo json_encode($ret_attrs);
?>