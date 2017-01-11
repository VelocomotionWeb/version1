<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strOptionTitre,"./config.css");
?>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strOptionTitre;
include "./entete.inc.php";
?>
<FORM METHOD="post" ACTION="./majconfig.php">

<TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="center">

<B CLASS="Option"><? echo $strOptionLangue; ?></B><P>
<TABLE>
<TR>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="francais.inc.php"   <? if(ereg("francais",$FichierLangue))   { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/francais.gif"		ALT="<? echo $srtLangueFrancais ?>"></TD>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="english.inc.php"    <? if(ereg("english",$FichierLangue))    { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/anglais.gif"		ALT="<? echo $srtLangueAnglais ?>"></TD>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="português.inc.php"  <? if(ereg("português",$FichierLangue))  { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/portugais.gif"	ALT="<? echo $srtLanguePortugais ?>"></TD>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="nederlands.inc.php" <? if(ereg("nederlands",$FichierLangue)) { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/neerlandais.gif"	ALT="<? echo $srtLangueNeerlandais ?>"></TD>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="spanish.inc.php"    <? if(ereg("spanish",$FichierLangue))    { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/espagnol.gif"		ALT="<? echo $srtLangueEspagnol ?>"></TD>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="deutsch.inc.php"    <? if(ereg("deutsch",$FichierLangue))    { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/allemand.gif"		ALT="<? echo $srtLangueAllemand ?>"></TD>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="italiano.inc.php"   <? if(ereg("italiano",$FichierLangue))   { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/italien.gif"		ALT="<? echo $srtLangueItalien ?>"></TD>
	<TD ALIGN="center" VALIGN="middle"><INPUT TYPE="radio" NAME="langage" VALUE="japanese.inc.php"   <? if(ereg("japanese",$FichierLangue))   { ?>CHECKED<? } ?> ></TD>
	<TD ALIGN="center" VALIGN="middle"><IMG SRC="./images/japon.gif"		ALT="<? echo $srtLangueJaponais ?>"></TD>
</TR>
</TABLE>
<P>

<B CLASS="Option"><? echo $strOptionDiversOptions; ?></B><P>
<TABLE WIDTH="90%" BORDER="0">
<TR>
	<TD ALIGN="left"  VALIGN="middle" WIDTH="80%"><B><? echo $strOptionVoirHeure; ?></B></TD>
	<TD ALIGN="right" VALIGN="middle" WIDTH="20%"><INPUT TYPE="checkbox" NAME="MajVoirHeure" <? if($VoirHeure == "1") { ?>CHECKED<? } ?> ></TD>
</TR>
<TR><TD COLSPAN="2"><HR></TD></TR>
<TR><TD ALIGN="left"  VALIGN="middle" COLSPAN="2"><B><? echo $strOptionImageFond; ?></B></TD></TR>
<TR><TD ALIGN="left" VALIGN="middle" COLSPAN="2"><B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $strOptionVotreImage; ?></B> : <INPUT TYPE="text" NAME="MajImageFond" SIZE="20" VALUE="<? echo $ImageFond; ?>"></TD></TR>
<TR><TD COLSPAN="2"><HR></TD></TR>
<TR>
	<TD ALIGN="left" COLSPAN="2">
		<B><? echo $strOptionCouleurFond; ?></B> : <INPUT TYPE="text" NAME="MajCouleurFond" SIZE="8" VALUE="<? echo $strColorFond; ?>"><BR>
		<B><? echo $strOptionCouleurChiffre; ?></B> : <INPUT TYPE="text" NAME="MajCouleurChiffre" SIZE="8" VALUE="<? echo $CouleurChiffre; ?>"><BR>
	</TD>
</TR>
<TR><TD COLSPAN="2"><HR></TD></TR>
<TR><TD ALIGN="left" VALIGN="middle" COLSPAN="2"><B><? echo $strOptionCheminRetour; ?></B> <I>"<? echo $strExplorateurRetourSite; ?>"</I> : <INPUT TYPE="text" NAME="MajstrCheminRetour" SIZE="20" VALUE="<? echo $strCheminRetour; ?>"> <? echo $strOptionCheminRetourDefaut; ?></TR>
<TR><TD COLSPAN="2"><HR></TD></TR>
<TR>
	<TD ALIGN="left" VALIGN="middle"><B><? echo $strOptionEditionActive; ?></B></TD>												
	<TD ALIGN="right" VALIGN="middle"><INPUT TYPE="checkbox" NAME="MajEditionActive" <? if($EditionActive == "1") { ?>CHECKED<? } ?> ></TD>
</TR>
<TR>
	<TD ALIGN="left" VALIGN="middle" COLSPAN="2"><B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $strOptionEditionResolution; ?></B>
	<SELECT NAME="resolution">
		<OPTION VALUE="600x480"   <? if(($LargeurFenetre == "600")  && ($HauteurFenetre == "480"))   { ?>SELECTED<? } ?> >600x480
		<OPTION VALUE="800x600"   <? if(($LargeurFenetre == "800")  && ($HauteurFenetre == "600"))   { ?>SELECTED<? } ?> >800x600
		<OPTION VALUE="1024x768"  <? if(($LargeurFenetre == "1024") && ($HauteurFenetre == "768"))   { ?>SELECTED<? } ?> >1024x768
		<OPTION VALUE="1280x1024" <? if(($LargeurFenetre == "1280") && ($HauteurFenetre == "1024")) { ?>SELECTED<? } ?> >1280x1024
	</SELECT>
</TR>
<TR><TD COLSPAN="2"><HR></TD></TR>
<TR><TD ALIGN="left" VALIGN="middle" COLSPAN="2"><B><? echo $strOptionCheminRelatif; ?></B><TD></TR>
<TR><TD ALIGN="left" VALIGN="middle" COLSPAN="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="text" NAME="MajCheminRelatif" SIZE="20" VALUE="<? echo $cheminrelatif; ?>"> <? echo $strOptionCheminRelatifDefaut; ?></TR>
<TR><TD COLSPAN="2"><HR></TD></TR>
<TR>
	<TD ALIGN="left" VALIGN="middle"><B><? echo $strOptionConfirmerEfface; ?></B></TD> 
	<TD ALIGN="right" VALIGN="middle"><INPUT TYPE="checkbox" NAME="MajConfirmerEfface" <? if($ConfirmerEfface == "1") { ?>CHECKED<? } ?> ></TD>
</TR>
<TR><TD COLSPAN="2"><HR></TD></TR>
</TABLE>
<P>

<INPUT TYPE="hidden" NAME="cheminreptofiles" VALUE="<? echo $cheminreptofiles; ?>">
<INPUT TYPE="hidden" NAME="chemin" VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="tri"    VALUE="<? echo $tri; ?>">

<TABLE><TR>
<TD><INPUT TYPE="submit" VALUE="<? echo $strValider; ?>"></TD>
</FORM>
<FORM METHOD="post" ACTION="./index.php">
<TD><INPUT TYPE="submit" VALUE="<? echo $strAnnuler; ?>"></TD>
</TR></TABLE>
<INPUT TYPE="hidden" NAME="chemin" VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="tri"    VALUE="<? echo $tri; ?>">
</FORM>

</TD></TR></TABLE>
<BR>

<? include "./basdepage.inc.php"; ?>
</BODY>
</HTML>