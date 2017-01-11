<?PHP
/*-- SRDEV création : 07/07/2016
		            \\|//
		            (0 0)
		•————oOOOo———(_)———oOOOo————•
		|         SRPROGFOU   	    |
		|       ROL  Stéphane       |
		•———————(   )———(   )———————• 
		         \ (     ) /                        
		          \_)   (_/       
*/

/* VARIABLE A DECLARER */
$simulation = 1; // mode simulmation = 1 ou production = 0
$action = 1; // mode action = 1 ou non = 0

$jour = $_POST["jour"];
$id_product = $_POST["id_product"];
$val = $_POST["val"];
$tax = $_POST["tax"];
if ($tax) $val = $val / (1 + ($tax/100));

/* VARIABLE GLOBALE */
$version = "1.0";  

/* Connexion */
include_once('../../config/config.inc.php');
include_once('../../config/settings.inc.php');

$sql = "SELECT * FROM "._DB_PREFIX_."specific_price WHERE id_product = $id_product";
$liste = Db::getInstance()->ExecuteS($sql);   
$sql = "SELECT * FROM "._DB_PREFIX_."tax WHERE rate = $tax";
$id_tax = Db::getInstance()->getValue($sql);   
//echo "$sql";
//var_dump($liste);
foreach($liste as $row)
{
	$j = $row["from_quantity"];
	//echo $j."<br>";
}
//echo "$sql $id_product / $jour / $tax";

// recupere le taux de TVA du loueur
// si pas affecté on prend par defaut = 1
//$tax_manager = TaxManagerFactory::getManager('', Product::getIdTaxRulesGroupByIdProduct((int)$id_product, $context));
       // $product_tax_calculator = $tax_manager->getTaxCalculator();
//echo $product_tax_calculator;
// si le jour est 1 le prix est affecté à la déclinaison journée
$sql = "SELECT pa.id_product_attribute FROM "._DB_PREFIX_."product_attribute_combination pac
		INNER JOIN "._DB_PREFIX_."product_attribute pa ON pa.id_product_attribute = pac.id_product_attribute 
  		WHERE pa.id_product = $id_product";
$id_product_attribute = Db::getInstance()->getValue($sql);   

if ($jour == 1)
{
	$update = "UPDATE "._DB_PREFIX_."product_attribute SET price = $val WHERE id_product_attribute=$id_product_attribute AND id_product = $id_product";
	$ret = Db::getInstance()->execute($update);   
	$update = "UPDATE "._DB_PREFIX_."product_attribute_shop SET price = $val WHERE id_product_attribute=$id_product_attribute AND id_product = $id_product";
	$ret = Db::getInstance()->execute($update);   
	//echo "$update";
}
else
{
	$sql = "SELECT price FROM "._DB_PREFIX_."product_attribute pa WHERE id_product_attribute = $id_product_attribute AND id_product = $id_product";
	$price = Db::getInstance()->getValue($sql);   
	$reduc = $price - ($val/$jour);
//echo "$price - ($val / $jour) = $reduc";	

	$id_product_attribute = 0; $id_tax = 0;
	$delete = "DELETE FROM "._DB_PREFIX_."specific_price WHERE id_product_attribute=$id_product_attribute AND id_product = $id_product AND from_quantity = $jour";
	$ret = Db::getInstance()->execute($delete);   
	$însert = "INSERT INTO "._DB_PREFIX_."specific_price (`id_product`, `id_shop`, `id_shop_group`, 
				`id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`) 
				VALUES (".$id_product.", 0, 0, ".$id_product_attribute.", '-1', ".$jour.", ".$reduc.", ".$id_tax.", 'amount');";
	$ret = Db::getInstance()->execute($însert);   
	echo "$însert";
}
echo "OK";