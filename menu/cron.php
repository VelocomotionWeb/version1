<!-- SRDEV création : 26/06/2016
		            \\|//
		            (0 0)
		•————oOOOo———(_)———oOOOo————•
		|         SRPROGFOU   	    |
		|    Codeur : srdevinfo   	 |
		|       ROL   Stéphane      |
		|       06.22.72.49.40      |
		|     Skype : srprogfou     |
		|      contact@srdev.fr     |
		•———————(   )———(   )———————• 
		         \ (     ) /                        
		          \_)   (_/       
-->
<meta charset="utf-8">
<? 
$version = "1.0";
$site = "VELOCOMOTION";
error_reporting(1);
ini_set('display_errors', 1);
// Chargement de l'appel PRESTASHOP
include_once('../config/config.inc.php');
include_once('../config/settings.inc.php');

// Test de la connexion au BO
$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
$employee = new Employee((int)$cookie->id_employee);
if (Validate::isLoadedObject($employee) && $employee->checkPassword((int)$cookie->id_employee, $cookie->passwd)
&& (!isset($cookie->remote_addr) || $cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP')))
{
	// Teste si le profil employé à le droit !
	if ($employee->id_profile == 1 || $employee->id_profile == 2 || $employee->id_profile == 3) 
	{
        echo('Connecté sous : ' . $employee->lastname . ' ' . $employee->firstname . ' (' . $employee->id_profile . ')<br>');
		  $non_connecte = true;
	}
} 
// Si pas de droit on affiche le message et on quitte
//if (!$non_connecte) die("Vous n'avez pas l'autorisation pour ce menu !");
/* Alimente les mouvements si mal fait par Prestashop et en plus ça le prend si commande valide donc plus de reserv */

// lecture des commandes valide
$sql ="
	SELECT o.id_order oid_order, o.*, od.*, c.* FROM "._DB_PREFIX_."order_detail od
	INNER JOIN "._DB_PREFIX_."orders o ON od.id_order = o.id_order
	INNER JOIN "._DB_PREFIX_."customization cz ON od.product_id = cz.id_product AND od.product_attribute_id = cz.id_product_attribute AND o.id_cart = cz.id_cart
	INNER JOIN "._DB_PREFIX_."customized_data c ON cz.id_customization = c.id_customization
	LEFT  JOIN reserv r ON od.product_id = r.id_product AND od.id_order = r.id_order
	WHERE o.valid = 1 AND 
	ifnull(r.id_order, '') = ''
	ORDER BY o.id_order DESC
";
//echo "<br>$sql<li>1.  " . date("h:i:s");
$result = Db::getInstance()->ExecuteS($sql);
foreach($result as $row) // boucle sur les packs
{
	$id_order = $row["oid_order"];
	$id_product = $row["product_id"];
	$qty = $row["product_quantity"];
	$id_cart = $row["id_cart"];
	$id_product_attribute = $row["id_product_attribute"];
	// lecture de la date de depart pour cet achat
	$date_depart = $row["value"];

	$sql ="";
	echo "<br> $id_order $id_product $id_cart $date_depart q=$qty     ";
	//$result = Db::getInstance()->ExecuteS($sql);
	
	// boucle sur le nombre de jour a partie du jour de depart
	for($cpt=0; $cpt<$qty; $cpt++)
	{
		$jour = dateadd('d', $cpt, $date_depart);
		$sql_r = "INSERT INTO reserv (id_product, dt_reserv, id_order) VALUES ($id_product, '$jour', $id_order)";
		$result_r = Db::getInstance()->Execute($sql_r);
		echo "<li>Inserre $sql_r $cpt $jour";
	}
	echo "<br>$sql_ds<li>1.  " . date("h:i:s");
} 

// lecture des commandes dont unit_price_tax_incl = 0
$sql ="
	SELECT o.id_order oid_order, o.*, od.* FROM "._DB_PREFIX_."order_detail od
	INNER JOIN "._DB_PREFIX_."orders o ON od.id_order = o.id_order
	WHERE od.unit_price_tax_incl = 0
	ORDER BY od.id_order DESC
";
echo "<br>$sql<li>1.  " . date("h:i:s");
$result = Db::getInstance()->ExecuteS($sql);
foreach($result as $row) // boucle 
{
	$id_order_detail = $row["id_order_detail"];
	$sql_r = "UPDATE "._DB_PREFIX_."order_detail SET unit_price_tax_excl = total_price_tax_excl / product_quantity
	WHERE id_order_detail = $id_order_detail";
	$result_r = Db::getInstance()->Execute($sql_r);
	$sql_r = "UPDATE "._DB_PREFIX_."order_detail SET unit_price_tax_incl = total_price_tax_incl / product_quantity
	WHERE id_order_detail = $id_order_detail";
	$result_r = Db::getInstance()->Execute($sql_r);
	echo "<li>96. Update $sql_r $id_order_detail";
} 

/**************************** Création des déclinaisons manquantes *************************/
$sql = "SELECT * FROM "._DB_PREFIX_."product WHERE id_product NOT IN 
(SELECT id_product FROM "._DB_PREFIX_."product_attribute)";
$result = Db::getInstance()->ExecuteS($sql);
foreach($result as $row) // boucle sur les packs
{
	$id_product = $row["id_product"];
	$sql = "SELECT max(id_product_attribute) FROM "._DB_PREFIX_."product_attribute";
	$id_product_attribute = Db::getInstance()->getValue($sql)+1;
	
	$sql_r = "INSERT INTO "._DB_PREFIX_."product_attribute (id_product_attribute, id_product) 
	VALUES ($id_product_attribute, $id_product)";
	$result_r = Db::getInstance()->Execute($sql_r);
	echo "<li>$sql_r;";

	$sql_r = "INSERT INTO "._DB_PREFIX_."product_attribute_shop (id_product_attribute, id_product, id_shop) 
	VALUES ($id_product_attribute, $id_product, 1)";
	$result_r = Db::getInstance()->Execute($sql_r);
	echo "<li>$sql_r;";
	
	$sql_r = "INSERT INTO "._DB_PREFIX_."product_attribute_combination (id_product_attribute, id_attribute) 
	VALUES ($id_product_attribute, 256)";
	$result_r = Db::getInstance()->Execute($sql_r);
	echo "<li>$sql_r;";
}

/* Effacement les Réservations qui n'ont pas de commandes rattachées */
$sql="DELETE FROM reserv WHERE id_order NOT IN(SELECT id_order FROM "._DB_PREFIX_."orders o WHERE o.valid = 1)";
$result = Db::getInstance()->Execute($sql);

/* Création d'un champ de customization pour tous les produits */
$sql="INSERT IGNORE INTO "._DB_PREFIX_."customization_field SELECT 0, id_product, 1, 0 FROM ps_product";
$result = Db::getInstance()->Execute($sql);
$sql="INSERT IGNORE INTO "._DB_PREFIX_."customization_field_lang SELECT id_customization_field, 1, 0, 'Date de location' FROM ps_customization_field";
$result = Db::getInstance()->Execute($sql);
$sql="INSERT IGNORE INTO "._DB_PREFIX_."customization_field_lang SELECT id_customization_field, 3, 0, 'Rent date' FROM ps_customization_field";
$result = Db::getInstance()->Execute($sql);
$sql="UPDATE `ps_product` SET customizable = '1', text_fields=1";
$result = Db::getInstance()->Execute($sql);
$sql="UPDATE `ps_product_shop` SET customizable = '1', text_fields=1";
$result = Db::getInstance()->Execute($sql);


/******** LES FONCTIONS UTILES *************/
function DateAdd($interval, $number, $date) {
	if (@ereg("-", $date)) $date = datefr($date, "NON"); // on met au format francais pour le traitement si contient -
	list($day, $month, $year)=split("/", $date); // recuperation des elements de la date
    switch ($interval) {
    
        case 'y':   // add year
            $year+=$number;
            break;

        case 'm':    // add month
            $month+=$number;
            break;

        case 'd':    // add days
            $day+=$number;
            break;

        case 'w':    // add week
            $day+=($number*7);
            break;

        case 'ww':    // add week
            $day+=($number*7);
            break;
			
        case 'h':    // add hour
            $hours+=$number;
            break;

        case 'n':    // add minutes
            $minutes+=$number;
            break;

        case 's':    // add seconds
            $seconds+=$number; 
            break;            

    }
	$dateplus = @date("d/m/Y", mktime(0, 0, 0, $month, $day,  $year ));// ajoute le délai à la date 
	return $dateplus;
}
function datefr($date_modif, $heurea="") // Entre=2000-12-30 sort=30/12/2000
{
	//echo  $date_modif.$heurea;
	$date_modif_retour = $date_modif;
	//echo  "retour=".$date_modif_retour;
	if ($date_modif_retour== "") return "";
	// si un tiret alors c'est pas un format americain donc pas de formatage
	//if (@ereg("-", $date_modif_retour)) return $date_modif_retour;
	if (@ereg("-", $date_modif)) $date_modif_retour = substr($date_modif,8,2)."/".substr($date_modif,5,2)."/".substr($date_modif,0,4);
	if ($heurea=="") if (@ereg(":", $date_modif)) $date_modif_retour = $date_modif_retour." ".substr($date_modif,11,2).":".substr($date_modif,14,2);
	//if (! is_date( $date_modif ) ) $date_modif = "";
	if (@ereg("//", $date_modif_retour)) $date_modif_retour = "";
	//if ($date_modif_retour= "") echo "date_modif_retour=$date_modif_retour";
    return substr($date_modif_retour,0,16);
}

?>
<script>
setTimeout(function(){ 
	window.location.reload(); 
	}, 20000);
</script>
