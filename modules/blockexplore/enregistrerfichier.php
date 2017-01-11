<?
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
if(!$EstInitialise) include "./config.inc.php";

$file = fopen("$cheminrelatif/$chemin/$fichier", "w");
fputs($file,stripslashes($Contenu));
fclose($file);

$Message = $strEditerMessage;
require("./editerfichier.php");
?>