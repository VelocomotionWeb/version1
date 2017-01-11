<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strTelechargerTitre,"./config.css");
?>
<SCRIPT LANGUAGE="JavaScript">
function SaisieCorrectNomTelecharger(form,NbFiles)
{
	var valide = true
	for(i=1;i<NbFiles+1;i++) 
		if (form.elements[i].value.length == 0) valide = false;

	if(!valide) 
	{
		if(NbFiles == 1) alert(<? echo $strTelechargerAlertFichier; ?>);  
		else alert(<? echo $strTelechargerAlertFichiers; ?>);  
	}

	return valide;
}
</SCRIPT>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strTelechargerTitre;
include "./entete.inc.php";
if(!isset($NbFiles)) $NbFiles = 1;
?>

<TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="left">

<TABLE><TR>
<TD BGCOLOR="<? echo $CouleurInfo; ?>"  ><? echo $strTelechargerChemin; ?></TD>
<TD BGCOLOR="<? echo $CouleurChemin; ?>"><B><? echo $chemin; ?></B></TD>
</TR></TABLE><P>
<? echo $strTelechargerNbFichier ?>
<SELECT NAME="NbFiles" ONCHANGE="top.location.href='./telecharger.php?chemin=<? echo $chemin; ?>&tri=<? echo $tri; ?>&action=<? echo $action; ?>&NbFiles=' + this.value">
<? for($i=1;$i<6;$i++) { ?><OPTION VALUE="<? echo $i; ?>" <? if($i==$NbFiles) { ?>SELECTED<? } ?> > <? echo $i; ?> <? } ?>
</SELECT>

<CENTER>
<FORM ENCTYPE="multipart/form-data" ACTION="./index.php?chemin=<? echo $chemin; ?>&tri=<? echo $tri; ?>&action=<? echo $action; ?>&NbFiles=<? echo $NbFiles ?>" METHOD="post">
<INPUT TYPE="hidden" NAME="MAX_FILE_SIZE" VALUE="<? echo 1024*1024*2; ?>">
<B><? echo $strTelechargerFichier; ?></B><P>
<? for($i=0;$i<$NbFiles;$i++) { ?><INPUT NAME="fichiers[]" TYPE="file" SIZE="60"><BR><? } ?><P>
<TABLE><TR>
<TD><INPUT TYPE="Submit" VALUE="<? echo $strTelecharger; ?>" ONCLICK="return SaisieCorrectNomTelecharger(this.form,<? echo $NbFiles; ?>)"></TD>
</FORM>
<FORM METHOD="post" ACTION="./index.php?chemin=<? echo $chemin; ?>&tri=<? echo $tri; ?>">
<TD><INPUT TYPE="Submit" VALUE="<? echo $strAnnuler; ?>" ></TD>
</TR></TABLE>
</FORM>
</CENTER>

<B CLASS="Important"><? echo $strExplorateurInfo; ?></B><BR>
<? echo $strTelechargerInfo; ?><B><? echo FormatTailleFichier(get_cfg_var("upload_max_filesize"),$strOctetAbrevation); ?></B><BR>


</TD></TR></TABLE>
<BR>

<? include "./basdepage.inc.php"; ?>
</BODY>
</HTML>