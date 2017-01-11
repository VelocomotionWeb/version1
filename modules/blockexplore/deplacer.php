<?	
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strDeplacerTitre,"./config.css");
?>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strDeplacerTitre;
include "./entete.inc.php";

function ExploreRepertoire($chemin,$niveau,$max,$tabniveau,$cheminrelatif,$source)
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
		?>
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR><?

		for($i=0;$i<$niveau;$i++) 
			if($tabniveau[$i] == 0) { ?> <TD WIDTH="22"><IMG SRC="./images/blanc.gif" WIDTH="22" HEIGHT="26"></TD><? }
			else { ?><TD WIDTH="22"><IMG SRC="./images/barre.gif" WIDTH="22" HEIGHT="26"></TD><? }

		if($indice+1 == $NbRep) { ?> <TD WIDTH="22"><IMG SRC="./images/feuille.gif" WIDTH="22" HEIGHT="26"></TD><? }
		else { ?><TD WIDTH="22"><IMG SRC="./images/croix.gif" WIDTH="22" HEIGHT="26"></TD><? }
			
		$NbCol = $max - $niveau;
		?>
			<TD WIDTH="22" VALIGN="top"><IMG SRC="./images/dir.gif" WIDTH="22" HEIGHT="22"></TD>
			<TD VALIGN="bottom" COLSPAN="<? echo $NbCol; ?>">
				&nbsp; 
				<? 
					echo $tabrep[$indice];
					$destination = str_replace($cheminrelatif,".","$chemin/$tabrep[$indice]");
					if(substr($destination,0,strlen($source)) != $source) { ?><INPUT TYPE="radio" NAME="place" VALUE="<? echo $chemin; ?>/<? echo $tabrep[$indice]; ?>"><? }				
				?>
			</TD>
		</TR>
		</TABLE>
		<?

		if($indice+1 < $NbRep) $tabniveau[$niveau] = 1; 
		else $tabniveau[$niveau] = 0; 

		ExploreRepertoire("$chemin/$tabrep[$indice]",$niveau,$max,$tabniveau,$cheminrelatif,$source);	
	}
}


$source = "$chemin/$fichier";
?>
<TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="left">

<TABLE><TR>
<TD BGCOLOR="<? echo $CouleurInfo; ?>"  ><? echo $strDeplacerChemin1; ?></TD>
<TD BGCOLOR="<? echo $CouleurChemin; ?>"><B><? echo $source; ?></B></TD>
<TD BGCOLOR="<? echo $CouleurInfo; ?>"><B><? echo $strDeplacerChemin2; ?></B></TD>
</TR></TABLE><P>

<?

$max = GetNiveauMax($cheminrelatif,-1,0);
$maxplus1 = $max + 1;
$tabniveau[0] = 1;

?>

<FORM  ACTION="./index.php" METHOD="post">
<TABLE BORDER="0" cellpadding="0" cellspacing="0">
<TR>
	<TD HEIGHT="26" VALIGN="top"><IMG SRC="./images/dir.gif" WIDTH="22" HEIGHT="22"></TD>
	<TD COLSPAN="<? echo $maxplus1 ?>" VALIGN="bottom">
		&nbsp; .
		<INPUT TYPE="radio" NAME="place" VALUE="<? echo $cheminrelatif; ?>" CHECKED>
	</TD>
</TR>
</TABLE>
<? ExploreRepertoire($cheminrelatif,-1,$max,$tabniveau,$cheminrelatif,$source); ?>
<P>

<INPUT TYPE="hidden" NAME="fichier" VALUE="<? echo $fichier; ?>">
<INPUT TYPE="hidden" NAME="chemin"  VALUE="<? echo $chemin; ?>">
<INPUT TYPE="hidden" NAME="action"  VALUE="<? echo $action; ?>">
<INPUT TYPE="hidden" NAME="tri"     VALUE="<? echo $tri; ?>">

<TABLE><TR>
<TD><INPUT TYPE="Submit" VALUE="<? echo $strDeplacer; ?>"></TD>
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