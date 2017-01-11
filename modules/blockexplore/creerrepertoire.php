<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strCreerRepTitre,"./config.css");
?>
<SCRIPT LANGUAGE="JavaScript">
function PasVideRepertoire(form)
{
	taille = form.rep.value.length;

	if(taille == 0)
	{
		alert(<? echo $strCreerRepAlertChamps; ?>);
		erreur = false;
	}
	else erreur = true;

	return erreur;
}
</SCRIPT>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strCreerRepTitre;
include "./entete.inc.php";
?>

<TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="left">

<TABLE><TR>
<TD BGCOLOR="<? echo $CouleurInfo; ?>"  ><? echo $strCreerRepChemin; ?></TD>
<TD BGCOLOR="<? echo $CouleurChemin; ?>"><B><? echo $chemin; ?></B></TD>
</TR></TABLE><P>

<CENTER>
<FORM  ACTION="./index.php" METHOD="POST">
<B><? echo $strCreerRepNomRep ?></B><BR>
<INPUT TYPE="text"	 NAME="rep"    SIZE="20" MAXLENGTH="40"><P>
<INPUT TYPE="hidden" NAME="chemin" VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="action" VALUE="<? echo $action; ?>">
<INPUT TYPE="hidden" NAME="tri"    VALUE="<? echo $tri; ?>">

<TABLE><TR>
<TD><INPUT TYPE="Submit" VALUE="<? echo $strCreerRep; ?>" ONCLICK="return PasVideRepertoire(this.form)"></TD>
</FORM>
<FORM METHOD="post" ACTION="./index.php">
<TD><INPUT TYPE="Submit" VALUE="<? echo $strAnnuler; ?>" ></TD>
</TR></TABLE>
<INPUT TYPE="hidden" NAME="chemin" VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="tri"    VALUE="<? echo $tri; ?>">
</FORM>
</CENTER>

</TD></TR></TABLE>
<BR>
<? include "./basdepage.inc.php"; ?>
</BODY>
</HTML>