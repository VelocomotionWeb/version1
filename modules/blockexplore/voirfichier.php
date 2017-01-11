<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($fichier,"./config.css");
?>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<CENTER><B CLASS="Fichier"><? echo $fichier; ?></B></CENTER>

<?

switch($type)
{
	case "Source"  : ?>
					 <A HREF="./editerfichier.php?chemin=<? echo $chemin; ?>&fichier=<? echo $fichier; ?>&type=<? echo $type; ?>"><? echo $strEditer; ?></A> 
					 <A HREF="<? echo $cheminrelatif; ?>/<? echo $chemin; ?>/<? echo $fichier; ?>" TARGET="_blank"><? echo $strTester; ?></A><BR>
					 <HR NOSHADE><BR>	
					 <?
					 show_source("$cheminrelatif/$chemin/$fichier");	
					 break;						 	

	case "Image"   : ?>
			 		 <HR NOSHADE><BR>	
					 <CENTER><IMG SRC="<? echo $cheminrelatif; ?>/<? echo $chemin; ?>/<? echo $fichier; ?>"><CENTER>
					 <?  
					 break;
}	
?>

<BR>&nbsp;
<HR NOSHADE><BR>
</BODY>
</HTML>		