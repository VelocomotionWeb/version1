<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strRenomerTitre,"./config.css");
?>
<SCRIPT LANGUAGE="JavaScript">
function SaisieCorrectNomFichier(form)
{
	taille = form.newfichier.value.length

	if(taille == 0) 
	{
		alert(<? echo $strRenomerAlert; ?>);  
		erreur = false;
	}
	else erreur = true;	

	return erreur;
}
</SCRIPT>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strRenomerTitre;
include "./entete.inc.php";

if(!empty($fichier)) $fichier = stripslashes($fichier);
?>

<TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="center">

<FORM  ACTION="./index.php" METHOD="POST"> 
<B><? echo $strRenomerOldFile?></B><BR>
<INPUT TYPE="text"   NAME="oldfichier" SIZE="40" MAXLENGTH="40" VALUE="<? echo $fichier; ?>" ONFOCUS="this.blur()"><P>
<B><? echo $strRenomerNewFile?></B><BR>
<INPUT TYPE="text"   NAME="newfichier" SIZE="40" MAXLENGTH="40" ONFOCUS="this.select();"><P>
<INPUT TYPE="hidden" NAME="fichier" VALUE="<? echo $fichier; ?>"><P>
<INPUT TYPE="hidden" NAME="chemin"  VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="action"  VALUE="<? echo $action; ?>">
<INPUT TYPE="hidden" NAME="tri"     VALUE="<? echo $tri; ?>">

<TABLE><TR>
<TD><INPUT TYPE="Submit" VALUE="<? echo $strRenomer; ?>" ONCLICK="return SaisieCorrectNomFichier(this.form)"></TD>
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