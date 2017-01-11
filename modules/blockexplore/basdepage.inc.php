<SCRIPT language="JavaScript">
//vérifie si compatible
compat=false; if(parseInt(navigator.appVersion)>=3.0){compat=true}

//préload les images
if(compat)
{
	b_mailon  = new Image;   b_mailon.src  = "./images/int_bas_mailon.gif";
	b_mailoff = new Image;   b_mailoff.src = "./images/int_bas_mailoff.gif";
}

//fonction pour faire changer d'image
function change(x,y) { if(compat) {document.images[x].src=eval(y+'.src');} }
</SCRIPT>

<?
	if($repind > 1)  $NbRepertoires = $srtAproposRepertoires; else $NbRepertoires = $srtAproposRepertoire;
	if($fileind > 1) $NbFichiers    = $srtAproposFichiers;    else $NbFichiers    = $srtAproposFichier; 
?>

<TABLE BORDER="0" WIDTH="790" cellpadding="0" cellspacing="0">
<TR>
	<TD ALIGN="center" VALIGN="middle" WIDTH="308" BACKGROUND="./images/int_bas_version.gif">&nbsp;<? if($AfficherNbFileAndNbRep) { ?><B><? echo $repind; ?> <? echo $NbRepertoires; ?> - <? echo $fileind; ?> <? echo $NbFichiers; ?></B> <? } ?></TD>
	<TD ALIGN="center" VALIGN="middle" WIDTH="406" BACKGROUND="./images/int_bas_vide.gif"><A CLASS="Divers" HREF="./apropos.php?&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>"><? echo $strAproposTitre; ?></A> <B CLASS="Divers"><? echo $strExplorateurTitre." ".$strExplorateurVersion; ?></B></TD>
	<TD ALIGN="center" VALIGN="middle" WIDTH="76"><A HREF='mailto:elegac@free.fr' onMouseOver="change('b_mail','b_mailon')" onMouseOut="change('b_mail','b_mailoff')"><IMG SRC="./images/int_bas_mailoff.gif" BORDER=0 NAME="b_mail" ALT="<? echo $srtAproposEcrire; ?>"></A></TD>
</TR>
</TABLE>
<BR>