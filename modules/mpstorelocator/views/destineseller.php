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
$destine = $_GET["destine"];

/* VARIABLE GLOBALE */
$version = "1.0";  

/* Connexion */
include_once('../../../config/config.inc.php');
include_once('../../../config/settings.inc.php');

$sql = "SELECT * FROM "._DB_PREFIX_."store_locator WHERE destine1='$destine' OR  destine2='$destine' OR destine1='' GROUP BY city_name";
$liste = Db::getInstance()->ExecuteS($sql);   
//echo "$sql";
//var_dump($liste);
foreach($liste as $city)
{
	$city_name = ucfirst(strtolower($city["city_name"]));
	echo $city_name."<br>";
}


