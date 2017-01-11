<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strCopierTitre,"./config.css");
?>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strCopierTitre;
include "./entete.inc.php";

function ExploreRepertoire($chemin,$niveau,$max,$tabniveau,$NbRepTotal,$cheminrelatif,$source)
{
	$NbRep   = GetNbRepertoire($chemin);
	$repind  = 0;
	$handle  = @opendir($chemin);
	$file    = @readdir($handle);      // repertoire .
	$file    = @readdir($handle);      // repertoire ..
	$niveau++;

	while ($file = @readdir($handle))
	{
		if(is_dir("$chemin/$file"))
		{
			$tabrep[$repind] = $file; 
			$repind++;
		}
	}

	if(count($tabrep)) usort($tabrep,TriRep);

	for($indice=0;$indice<$repind;$indice++)
	{
		$NbRepTotal++;
		?>
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR HEIGHT="26"><?

		for($i=0;$i<$niveau;$i++) 
		if($tabniveau[$i] == 0) { ?><TD WIDTH="22"><IMG SRC="./images/blanc.gif" WIDTH="22" HEIGHT="26"></TD><? }
		else { ?><TD WIDTH="22"><IMG SRC ="./images/barre.gif" WIDTH="22" HEIGHT="26"></TD><? }

		if($indice+1 == $NbRep) { ?> <TD WIDTH="22"><IMG SRC="./images/feuille.gif" WIDTH="22" HEIGHT="26"></TD><? }
		else { ?><TD WIDTH="22"><IMG SRC = "./images/croix.gif" WIDTH="22" HEIGHT="26"></TD><? }
			
		$NbCol = $max - $niveau;
		
		?>
			<TD WIDTH="22" VALIGN="top"><IMG SRC="./images/dir.gif" WIDTH="22" HEIGHT="22"></TD>
			<TD VALIGN="bottom" <? if($NbCol > 1) { ?>COLSPAN="<? echo $NbCol; ?>" <? } ?> >
				&nbsp; 
				<? 
					echo $tabrep[$indice];
					$destination = str_replace($cheminrelatif,".","$chemin/$tabrep[$indice]");
					if(substr($destination,0,strlen($source)) != $source) { ?><INPUT TYPE="checkbox" NAME="choix[<? echo $NbRepTotal; ?>]"><? }

				?>
				<INPUT TYPE="hidden" NAME="emplacement[<? echo $NbRepTotal; ?>]" VALUE="<? echo $chemin; ?>/<? echo $tabrep[$indice]; ?>">
			</TD>
		</TR>
		</TABLE>
		<?

		if($indice+1 < $NbRep) $tabniveau[$niveau] = 1; 
		else $tabniveau[$niveau] = 0; 

		$NbRepTotal = ExploreRepertoire("$chemin/$tabrep[$indice]",$niveau,$max,$tabniveau,$NbRepTotal,$cheminrelatif,$source);	
	}

	return $NbRepTotal;
}

$source = "$chemin/$fichier";
?>
<TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="left">

<TABLE><TR>
<TD BGCOLOR="<? echo $CouleurInfo; ?>"  ><? echo $strCopierChemin1; ?></TD>
<TD BGCOLOR="<? echo $CouleurChemin; ?>"><B><? echo $source; ?></B></TD>
<TD BGCOLOR="<? echo $CouleurInfo; ?>"><B><? echo $strCopierChemin2; ?></B></TD>
</TR></TABLE><P>

<? 

$max = GetNiveauMax($cheminbase,-1,0);
$maxplus1 = $max + 1;
$tabniveau[0] = 1;

?>

<FORM  ACTION="./index.php" METHOD="post">
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR HEIGHT="26">
	<TD WIDTH="22" VALIGN="top"><IMG SRC="./images/dir.gif" WIDTH="22" HEIGHT="22"></TD>
	<TD COLSPAN="<? echo $maxplus1 ?>" VALIGN="bottom">
		&nbsp; .
		<INPUT TYPE="checkbox" NAME="choix[0]">
		<INPUT TYPE="hidden"   NAME="emplacement[0]" VALUE="<? echo $cheminrelatif; ?>">
	</TD>
</TR>
</TABLE>
<?
	$NbRepTotal = ExploreRepertoire($cheminrelatif,-1,$max,$tabniveau,0,$cheminrelatif,$source);
	$NbRepTotal++;
?>
<P>
<INPUT TYPE="hidden" NAME="NbRepTotal" VALUE="<? echo $NbRepTotal; ?>">
<INPUT TYPE="hidden" NAME="fichier"    VALUE="<? echo $fichier; ?>">
<INPUT TYPE="hidden" NAME="chemin"     VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="action"     VALUE="<? echo $action; ?>">
<INPUT TYPE="hidden" NAME="tri"        VALUE="<? echo $tri; ?>">

<TABLE><TR>
<TD><INPUT TYPE="Submit" VALUE="<? echo $strCopier; ?>"></TD>
</FORM>
<FORM METHOD="post" ACTION="./index.php">
<TD><INPUT TYPE="Submit" VALUE="<? echo $strAnnuler; ?>" ></TD>
</TR></TABLE>
<INPUT TYPE="hidden" NAME="chemin"  VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="tri"     VALUE="<? echo $tri; ?>">
</FORM>

</TD></TR></TABLE>
<BR>
<? include "./basdepage.inc.php"; ?>
</BODY>
</HTML>