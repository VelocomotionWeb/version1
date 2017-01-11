<?
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php"); 


$file = fopen("./config.inc.php", "w");
fputs($file,"<? \n \n ");

fputs($file,"\$EstInitialise     = true; \n \n ");

fputs($file,"// Image de fond \n \n ");
fputs($file,"\$ImageFond         = \"$ImageFond\"; // defaut \"\" \n \n ");

fputs($file,"// Chemin de base \n \n ");
if(!empty($MajCheminRelatif)) $valeur = $MajCheminRelatif; else $valeur = "./.."; fputs($file,"\$cheminrelatif   = \"$valeur\";   // defaut \"./..\" \n \n ");

fputs($file,"// Configuration du fichier de langue \n \n \$FichierLangue     = \"./langues/$langage\"; \n \n ");

fputs($file,"// Configuration des couleurs \n \n ");
if(!empty($MajCouleurChiffre)) $valeur = $MajCouleurChiffre; else $valeur = "#9CBDFF"; fputs($file,"\$CouleurChiffre    = \"$valeur\"; // defaut \"#9CBDFF\" \n ");
if(!empty($MajCouleurFond))    $valeur = $MajCouleurFond;    else $valeur = "White"; fputs($file,"\$strColorFond      = \"$valeur\"; // defaut \"White\" \n\n ");

fputs($file,"// Configuration visuelle \n \n ");
if($MajVoirHeure == "on")       $valeur = "1"; else $valeur = "0"; fputs($file,"\$VoirHeure         = \"$valeur\";   // Afficher l'heure : \"1\" visible, \"0\" invisible \n ");
if(!empty($MajstrCheminRetour)) $valeur = $MajstrCheminRetour; else $valeur = "/"; fputs($file,"\$strCheminRetour   = \"$valeur\";   // chemin du lien \"retour au site\" \n ");
fputs($file,"\$ImageFond         = \"$MajImageFond\";   // chemin de l'image de fond \n \n");

fputs($file,"// Configuration diverse \n \n ");
if($MajEditionActive == "on")  $valeur = "1"; else $valeur = "0"; fputs($file,"\$EditionActive     = \"$valeur\";             // Activer l'édition des fichiers  : \"1\" actif, \"0\" inactif \n ");

switch($resolution)
{
	case "600x480"   : $largeur = "600";  $hauteur = "480";  break;
	case "800x600"   : $largeur = "800";  $hauteur = "600";  break;
	case "1024x768"  : $largeur = "1024"; $hauteur = "768";  break;
	case "1280x1024" : $largeur = "1280"; $hauteur = "1024"; break;
}
fputs($file,"\$LargeurFenetre    = \"$largeur\";          // largeur de la fenêtre d'édition \n ");
fputs($file,"\$HauteurFenetre    = \"$hauteur\";          // Hauteur de la fenêtre d'édition \n ");
if($MajConfirmerEfface == "on") $valeur = "1"; else $valeur = "0"; fputs($file,"\$ConfirmerEfface   = \"$valeur\";             // confirmation de suppresion : \"1\" actif, \"0\" inactif \n \n");



fputs($file,"// ------------------------- Ne pas modifier / Don't Modify ------------------------------------------ \n \n ");
fputs($file,"\$strExplorateurVersion = \"$strExplorateurVersion\"; \n ");
fputs($file,"\$CouleurChemin         = \"#C0DBEC\"; \n ");
fputs($file,"\$CouleurInfo           = \"#A9DEFF\"; \n ");
fputs($file,"require(\"./\$FichierLangue\"); \n \n ?>");

fclose($file);

?><META HTTP-EQUIV="refresh" CONTENT="0; URL=./index.php?chemin=<? echo $chemin; ?>&tri=<? echo $tri; ?>"><?

?>