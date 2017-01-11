<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strConfirmer,"./config.css");
?>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strConfirmer;
include "./entete.inc.php";

?><TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="left"><?

switch($action)
{
	case "SupprimerFichier" : Message("$strConfirmerMessageFichier<B>$fichier</B> ?<BR>"); 
							  break;

	case "SupprimerRep"     : Message("$strConfirmerMessageRep<B>$rep</B> ?<BR>");  
						      break;

	case "SupprimerRepNV"   : Message("$strExplorateurRepertoire<B>$fichier</B>$strConfirmerMessagePasVide<BR>$strConfirmerMessageSure<BR>"); 
						      break;
}
?>



<FORM  ACTION="./index.php" METHOD="POST">
<INPUT TYPE="hidden" NAME="fichier" VALUE="<? echo $fichier; ?>">
<INPUT TYPE="hidden" NAME="action"  VALUE="<? echo $action; ?>">
<INPUT TYPE="hidden" NAME="chemin"  VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="rep"     VALUE="<? echo $rep; ?>">
<INPUT TYPE="hidden" NAME="tri"     VALUE="<? echo $tri; ?>">

<TABLE><TR>
<TD><INPUT TYPE="Submit" VALUE="<? echo $strConfirmer; ?>"></TD>
</FORM>
<FORM METHOD="post" ACTION="./index.php">
<TD><INPUT TYPE="Submit" VALUE="<? echo $strAnnuler; ?>" ></TD>
</TR></TABLE>
<INPUT TYPE="hidden" NAME="chemin" VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="tri"    VALUE="<? echo $tri; ?>">
</FORM>

</TD></TR></TABLE>
<BR>
<? include "./basdepage.inc.php"; ?>
</BODY>
</HTML>