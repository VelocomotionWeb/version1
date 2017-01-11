<?
$NomFichier = basename($fichier);

@set_time_limit(600);

header("Content-Type: application/force-download; name=\"$NomFichier\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $taille");
header("Content-Disposition: attachment; filename=\"$NomFichier\"");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

require("./config.inc.php");
readfile("$cheminrelatif/$chemin/$fichier");
exit();

?>