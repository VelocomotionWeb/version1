<SCRIPT language="JavaScript">
//vérifie si compatible
compat=false; if(parseInt(navigator.appVersion)>=3.0){compat=true}

//préload les images
if(compat)
{
	b1_on     = new Image;	b1_on.src     = "./images/int_b1on.jpg";
	b1_off    = new Image;	b1_off.src    = "./images/int_b1off.jpg";
	b2_on     = new Image;	b2_on.src     = "./images/int_b2on.jpg";
	b2_off    = new Image;	b2_off.src    = "./images/int_b2off.jpg";
	b3_on     = new Image;	b3_on.src     = "./images/int_b3on.jpg";
	b3_off    = new Image;	b3_off.src    = "./images/int_b3off.jpg";
	bpref_on  = new Image;	bpref_on.src  = "./images/int_bprefon.jpg";
	bpref_off = new Image;	bpref_off.src = "./images/int_bpref.jpg";
}

//fonction pour faire changer d'image
function change(x,y) { if(compat) {document.images[x].src=eval(y+'.src');} }
</SCRIPT>

<? $cheminencode = rawurlencode($chemin);	?>

<TABLE BORDER="0" WIDTH="790" CELLPADDING="0" CELLSPACING="0"> 
<TR HEIGHT="44">
	<TD BACKGROUND="./images/int_titre1.jpg"    WIDTH="169">&nbsp;</TD>
	<TD BACKGROUND="./images/int_hautb1.jpg"    WIDTH="139" VALIGN="bottom" ALIGN="center" HEIGHT="44"><B CLASS="SousTitre"><? echo $strExplorateurRafraichir; ?></B></TD>
	<TD BACKGROUND="./images/int_hautb2.jpg"    WIDTH="162" VALIGN="bottom" ALIGN="center" HEIGHT="44"><B CLASS="SousTitre"><? echo $strExplorateurCreerRep; ?></B></TD>
	<TD BACKGROUND="./images/int_hautb3.jpg"    WIDTH="168" VALIGN="bottom" ALIGN="center" HEIGHT="44"><B CLASS="SousTitre"><? echo $strExplorateurTelecharger; ?></B></TD>
	<TD BACKGROUND="./images/int_hautevide.jpg" WIDTH="*"   VALIGN="bottom" ALIGN="right"  HEIGHT="44"><A CLASS="Divers" HREF="<? echo $strCheminRetour; ?>"><? echo $strExplorateurRetourSite; ?></A></TD>
	<TD BACKGROUND="./images/int_bretour.jpg"   WIDTH="59">&nbsp;</TD>
</TR>
<TR HEIGHT="54">
	<TD BACKGROUND="./images/int_titre2.jpg" WIDTH="169" VALIGN="bottom">
	<?
		if($VoirHeure == "1")
		{	?>
			<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
			<TR>
				<TD WIDTH="5"   BACKGROUND="">&nbsp;</TD>
				<TD WIDTH="120" BACKGROUND="" ALIGN="center"><B CLASS="Date"><? echo DateDuJour(); ?></B></TD>
			</TR>
			</TABLE>
			<?
		}		
	?>
	</TD>
	<TD WIDTH="139"><A  HREF="./index.php?chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>" onMouseOver="change('b1','b1_on')" onMouseOut="change('b1','b1_off')"><IMG SRC="./images/int_b1off.jpg" BORDER="0" NAME="b1" ALT="<? echo $strExplorateurRafraichir; ?>"></A></TD>
	<TD WIDTH="162"><A  HREF="./creerrepertoire.php?chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=CreerRep" onMouseOver="change('b2','b2_on')" onMouseOut="change('b2','b2_off')"><IMG SRC="./images/int_b2off.jpg" BORDER=0 NAME="b2" ALT="<? echo $strExplorateurCreerRep; ?>"></A></TD>
	<TD WIDTH="168"><A  HREF="./telecharger.php?chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=Telecharger"  onMouseOver="change('b3','b3_on')" onMouseOut="change('b3','b3_off')"><IMG SRC="./images/int_b3off.jpg" BORDER=0 NAME="b3" ALT="<? echo $strExplorateurTelecharger; ?>"></A></TD>
	<TD WIDTH="97" ><IMG SRC="./images/int_milieuvide.jpg" BORDER=0></TD>
	<TD WIDTH="59" ><A  HREF="./preferences.php?chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>" onMouseOver="change('bpref','bpref_on')" onMouseOut="change('bpref','bpref_off')"><IMG SRC="./images/int_bpref.jpg" BORDER=0 NAME="bpref" ALT="<? echo $strExplorateurOptions; ?>"></A></TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR HEIGHT="39">
	<TD BACKGROUND="./images/int_basheure.jpg" WIDTH="169" VALIGN="top">
	
	<?
	if($VoirHeure == "1")
	{
		?>
		<TABLE WIDTH=117 BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR><TD WIDTH=117 ALIGN=right VALIGN="top">
			<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="0"><TR><TD>
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" BGCOLOR="<? echo $CouleurChiffre; ?>" BACKGROUND=""><TR>
				<TD WIDTH="14" HEIGHT="19"><IMG NAME="DH" WIDTH="14" HEIGHT="19"></TD>
				<TD WIDTH="14" HEIGHT="19"><IMG NAME="UH" WIDTH="14" HEIGHT="19"></TD>
				<TD WIDTH="6"  HEIGHT="19"><IMG SRC="./images/deuxpts.gif" WIDTH="6" HEIGHT="19"></TD>
				<TD WIDTH="14" HEIGHT="19"><IMG NAME="DM" WIDTH="14" HEIGHT="19"></TD>
				<TD WIDTH="14" HEIGHT="19"><IMG NAME="UM" WIDTH="14" HEIGHT="19"></TD>
				<TD WIDTH="6"  HEIGHT="19"><IMG SRC="./images/deuxpts.gif" WIDTH="6" HEIGHT="19"></TD>
				<TD WIDTH="14" HEIGHT="19"><IMG NAME="DS" WIDTH="14" HEIGHT="19"></TD>
				<TD WIDTH="14" HEIGHT="19"><IMG NAME="US" WIDTH="14" HEIGHT="19"></TD>
			</TR></TABLE>
			</TD></TR></TABLE>
			<SCRIPT LANGUAGE="javaScript"> Getheure(); </SCRIPT>
		</TD></TR>
		</TABLE>
		<?
	}
	?>

	</TD>
	<TD BACKGROUND="./images/int_baspad.jpg" WIDTH="*"><B CLASS="Titre"><? echo $strTitre; ?></B></TD>
	<TD><IMG SRC="./images/int_basdroite.jpg" BORDER="0"></TD>
</TR>
</TABLE>
<BR>