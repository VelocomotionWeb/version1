<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
if(!$EstInitialise) include "./config.inc.php";
require("./fonctions.inc.php");
AfficherEntete($fichier,"./config.css");

if(!isset($NbCols)) $NbCols = 120;
if(!isset($NbRows)) $NbRows = 30;
?>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<SCRIPT LANGUAGE="javaScript">

function Valider()
{
	var valide = true;

	if(document.Etat.EtatFichier.value == <? echo "\"".$strEditerModifie."\""; ?>)
		valide = confirm(<? echo $strEditerNonEnregistre; ?>);

	return valide;
}

</SCRIPT>
<CENTER><B CLASS="Fichier"><? echo $fichier; ?></B></CENTER>
<HR NOSHADE><BR>

<!-- ------------------------------------ Affichage de l'état du fichier ----------------------------------------- -->

<FORM NAME="Etat">
<? if(isset($Message)) $EtatFichier = $Message; else $EtatFichier = $strEditerNonModifie; ?>
<B><? echo $strEditerEtatFichier; ?></B><INPUT TYPE="texte" NAME="EtatFichier" VALUE="<? echo $EtatFichier; ?>" SIZE="40">
</FORM>
<CENTER>

<!-- --------------------------- Définition de la taille de la zone de texte ------------------------------------- -->

<FORM ACTION="./editerfichier.php?fichier=<? echo $fichier; ?>&chemin=<? echo $chemin; ?>&type=<? echo $type; ?>" METHOD="post">
<TABLE WIDTH="100%"><TR>
<TD><B><? echo $strEditerRedimensionner; ?></B></TR>
<TD>
	<? echo $strEditerNbCols; ?>
	<SELECT NAME="NbCols">
	<? for($i=50;$i<210;$i+=10) { ?><OPTION VALUE="<? echo $i; ?>" <? if($i==$NbCols) { ?>SELECTED<? } ?> ><? echo $i; } ?>
	</SELECT>
	<? echo $strEditerNbRows; ?>
	<SELECT NAME="NbRows">
	<? for($i=20;$i<85;$i+=5) { ?><OPTION VALUE="<? echo $i; ?>" <? if($i==$NbRows) { ?>SELECTED<? } ?> ><? echo $i;  } ?>
	</SELECT>
</TD>
<TD><INPUT TYPE="submit" VALUE="<? echo $strValider; ?>"></TD>
</TR></TABLE>
</FORM>

<!-- ---------------------------------- Affichage de la zone de texte ------------------------------------------- -->

<FORM ACTION="./enregistrerfichier.php?fichier=<? echo $fichier; ?>&chemin=<? echo $chemin; ?>&type=<? echo $type; ?>" METHOD="post">
<TEXTAREA NAME="Contenu" ROWS="<? echo $NbRows; ?>" COLS="<? echo $NbCols; ?>" ONFOCUS="document.Etat.EtatFichier.value='<? echo $strEditerModifie; ?>'">
<? readfile("$cheminrelatif/$chemin/$fichier"); ?>
</TEXTAREA><P>

<!-- ------------------------------------ Affichage des bouton submit ------------------------------------------- -->

<TABLE><TR>
<TD><INPUT TYPE="submit" VALUE="<? echo $strEnregistrer; ?>"></TD>
<INPUT TYPE="hidden" NAME="NbCols" VALUE="<? echo $NbCols; ?>">
<INPUT TYPE="hidden" NAME="NbRows" VALUE="<? echo $NbRows; ?>">
</FORM>
<FORM METHOD="post" ACTION="./voirfichier.php?fichier=<? echo $fichier; ?>&chemin=<? echo $chemin; ?>&type=<? echo $type; ?>">
<TD><INPUT TYPE="submit" VALUE="<? echo $strAnnuler; ?>" ONCLICK="return Valider()"></TD>
</TR></TABLE>
</FORM>


</CENTER>
<HR NOSHADE><BR>
</BODY>
</HTML>		