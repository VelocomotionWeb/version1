<SCRIPT LANGUAGE="JavaScript">
//vérifie si compatible
compat=false; if(parseInt(navigator.appVersion)>=3.0){compat=true}

//préload les images
if(compat)
{
	b_supp_on      = new Image;		b_supp_on.src      = "./images/icone_supp_on.gif";
	b_supp_off     = new Image;		b_supp_off.src     = "./images/icone_supp.gif";
	b_ren_on       = new Image;		b_ren_on.src       = "./images/icone_ren_on.gif";
	b_ren_off      = new Image;		b_ren_off.src      = "./images/icone_ren.gif";
	b_move_on      = new Image;		b_move_on.src      = "./images/icone_move_on.gif";
	b_move_off     = new Image;		b_move_off.src     = "./images/icone_move.gif";
	b_copy_on      = new Image;		b_copy_on.src      = "./images/icone_copy_on.gif";
	b_copy_off     = new Image;		b_copy_off.src     = "./images/icone_copy.gif";
	b_download_on  = new Image;		b_download_on.src  = "./images/icone_download_on.gif";
	b_download_off = new Image;		b_download_off.src = "./images/icone_download.gif";
}

//fonction pour faire changer d'image
function change(x,y) { if(compat) {document.images[x].src=eval(y+'.src');} }
</SCRIPT>

<?
foreach($_POST as $key => $var) {$$key = $var;} foreach($_GET as $key => $var) {$$key = $var;} 
//if ($user!="stephane") die("Restricted Access");

require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strExplorateurTitre,"./config.css");
?>
<BODY BACKGROUND="<? echo $ImageFond; ?>" BGPROPERTIES="fixed" BGCOLOR="<? echo $strColorFond ?>" >
<?

// ------------------------------ Initialisation des variables ----------------------------------------------- //

if(!empty($newfichier)) $newfichier = stripslashes($newfichier);
if(!empty($chemin))     $chemin     = stripslashes($chemin); else $chemin = ".";
if(!empty($fichier))    $fichier    = stripslashes($fichier);
if(!empty($place))      $place      = stripslashes($place);
if(!empty($rep))        $rep        = stripslashes($rep);
if(empty($tri))         $tri        = "NomASC";

// ----------------------------------- Sécurité navigation -------------------------------------------------- //


if(@ereg("\.\.",$chemin))
{
	//$chemin = ".";
	unset($action);
	unset($rep);
}
if($cheminrelatif=="")
{
	$cheminrelatif = "../";
}

$strTitre = $strExplorateurTitre;
include "entete.inc.php";
$chemintotal = $cheminrelatif."/".$chemin;
//$chemintotal = "../";
//echo "$chemintotal = $cheminrelatif --- $chemin";
// ----------------------------------- Gestion des actions -------------------------------------------------- //

switch($action)
{
	//  ------------------------------------ Renomer un fichier ou répertoire --------------------------------   //

	case "Renomer"          : if(file_exists("$chemintotal/$newfichier")) Message("$strExplorateurFichier$newfichier$strExplorateurAlertDeja");
							  else if(rename("$chemintotal/$fichier","$chemintotal/$newfichier")) Message("$strExplorateurFichier$fichier$strExplorateurMsgRenomer$newfichier");
						 	 	   else Message("$strExplorateurErreur");
					 		  break;

	//  ------------------------------------- Créer un répertoire --------------------------------------------   //

	case "CreerRep"		    : if(file_exists("$chemintotal/$rep")) Message("$strExplorateurRepertoire$rep$strExplorateurAlertDeja");
							  else if(mkdir("$chemintotal/$rep", 0777)) Message("$strExplorateurRepertoire$rep$strExplorateurMsgCreerRep");
								   else Message("$strExplorateurErreur");
							  break;

	//  ------------------------------------ Supprimer un fichier --------------------------------------------   //

	case "SupprimerFichier" : if(unlink("$chemintotal/$fichier")) Message("$strExplorateurFichier$fichier$strExplorateurMsgSupprimer");
							  else Message("$strExplorateurErreur");
							  break;


	//  ------------------------------------- Supprimer un répertoire ----------------------------------------   //

	case "SupprimerRep"     : if(rmdir("$chemintotal/$rep")) Message("$strExplorateurRepertoire$rep$strExplorateurMsgSupprimer");
							  else Message("$strExplorateurErreur");
							  break;

	//  ------------------------------------- Supprimer un répertoire non-vide -------------------------------  //

	case "SupprimerRepNV"   : if(EffacerRepertopireRecursif("$chemintotal/$fichier")) Message("$strExplorateurRepertoire$fichier$strExplorateurMsgSupprimer");
							  else Message("$strExplorateurErreur");
							  break;

	//  ------------------------------------- Télécharger un fichier -----------------------------------------   //

	case "Telecharger"      : for($i=0;$i<$NbFiles;$i++)
							  {
								if(copy("$fichiers[$i]","$chemintotal/$fichiers_name[$i]")) MessageBR("$strExplorateurFichier$fichiers_name[$i]$strExplorateurTelechargerSize$fichiers_size[$i]$strExplorateurMsgTelecharger");
								else Message("$strExplorateurErreur");
								$retouralaligne = true;
							  }
							   if($retouralaligne) { ?><BR><? }
							  break;

	// -------------------------------------- Déplacer un fichier --------------------------------------------  //

	case "DeplacerFichier"  : if("$chemin/$fichier" != RecupereEmplacement($cheminrelatif,"$place/$fichier"))
							  {
								 if(copy("$chemintotal/$fichier","$place/$fichier"))
								 {
								 	if(unlink("$chemintotal/$fichier")) 
									{
										$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$place/$fichier");
										Message("$strExplorateurFichier$chemin/$fichier$strExplorateurMsgDeplacer$NouvelEmplacement");
									}
									else Message("$strExplorateurErreur");
								 }
								 else Message("$strExplorateurErreur");
							  }
							  else Message("$strExplorateurAlertSD");
							  break;

	//  ------------------------------------- Copier un fichier ----------------------------------------------  //

	case "CopierFichier"    : for($i=0;$i<$NbRepTotal;$i++)
							  {
								if($choix[$i] == "on")
								{
									if("$chemin/$fichier" != RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier"))
									{
										if(copy("$chemintotal/$fichier","$emplacement[$i]/$fichier")) 
										{ 
											$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier");
											?><B CLASS="Important"><? echo $strExplorateurFichier; ?><? echo $chemin; ?>/<? echo $fichier; ?><? echo $strExplorateurMsgCopier; ?><? echo $NouvelEmplacement; ?></B><BR><? 
											$retouralaligne = true;
										}
										else Message("$strExplorateurErreur");
									}
									else Message("$strExplorateurAlertSD");
								}				
							  }

							  if($retouralaligne) { ?><BR><? }
							  break;

	// -------------------------------------- Déplacer un répertoire --------------------------------------------  //

	case "DeplacerRep"     : $Message[0] = $strExplorateurRepertoire;
							 $Message[1] = $strExplorateurMsgDeplacer;
							 $Message[2] = $NouvelEmplacement;
							 $Message[3] = $strExplorateurErreur;
							 $Message[4] = $strExplorateurAlertSD;

							 DeplacerRep($cheminrelatif,$chemin,$fichier,$place,$Message);
							 break;

	// -------------------------------------- Copier un répertoire --------------------------------------------  //

	case "CopierRep"       : $Message[0] = $strExplorateurRepertoire;
							 $Message[1] = $strExplorateurMsgDeplacer;
							 $Message[2] = $NouvelEmplacement;
							 $Message[3] = $strExplorateurErreur;
							 $Message[4] = $strExplorateurAlertSD;

							 CopierRep($cheminrelatif,$chemin,$fichier,$emplacement,$NbRepTotal,$choix,$Message);
							 break;
// -------------------------------------- modifier les permissions --------------------------------------------  //

	case "ModifierPerms"   : $mode = ($ar+$aw+$ax).($gr+$gw+$gx).($pr+$pw+$px); 
							 /*if(chmod("$chemintotal/$Item",OctDec($mode))) Message("ok");
							 else Message("$strExplorateurErreur");*/
						     echo "$chemintotal/$Item";
							 if(chmod("$chemintotal/$Item",0755)) Message("ok");
							 else Message("$strExplorateurErreur");
							 break;
}

// ------------------------------- Récupération des fichiers et répertoires ------------------------------ //


$handle  = @opendir($chemintotal);
$file    = @readdir($handle);      // repertoire .
$file    = @readdir($handle);      // repertoire ..
$repind  = 0;
$fileind = 0;

while ($file = @readdir($handle))
{
	if(is_dir("$chemintotal/$file"))
	{
		$reptab[$repind]["nom"]           = $file;
		$reptab[$repind]["taille"]        = filesize("$chemintotal/$file");
		$reptab[$repind]["date"]          = GetDateStr(filemtime("$chemintotal/$file"));
		$reptab[$repind]["datetri"]       = FormatDate(filemtime("$chemintotal/$file"));
		$reptab[$repind]["permissions"]   = FormatePermissions(fileperms("$chemintotal/$file"));
		$repind++;
	}
	else
	{
		$filetab[$fileind]["nom"]         = $file;
		$filetab[$fileind]["taille"]      = filesize("$chemintotal/$file");
		$filetab[$fileind]["date"]        = GetDateStr(filemtime("$chemintotal/$file"));
		$filetab[$fileind]["datetri"]     = FormatDate(filemtime("$chemintotal/$file"));
		$filetab[$fileind]["permissions"] = FormatePermissions(fileperms("$chemintotal/$file"));
		$fileind++;
	}
}

@closedir($handle);

// --------------------------------------- Gestion des tris -------------------------------------------- //

switch($tri)
{
	// Tri par nom
	case "NomASC"      : if(count($reptab))  usort($reptab,TriNomASC);
					     if(count($filetab)) usort($filetab,TriNomASC);
					     break;
	case "NomDESC"     : if(count($reptab))  usort($reptab,TriNomDESC);
					     if(count($filetab)) usort($filetab,TriNomDESC);
					     break;			

	// Tri par taille
	case "TailleASC"   : if(count($reptab))  usort($reptab,TriTailleASC);
					     if(count($filetab)) usort($filetab,TriTailleASC);
					     break;
	case "TailleDESC"  : if(count($reptab))  usort($reptab,TriTailleDESC);
					     if(count($filetab)) usort($filetab,TriTailleDESC);
					     break;

	// Tri par date
	case "TriDateASC"  : if(count($reptab))  usort($reptab,TriDateASC);
						 if(count($filetab)) usort($filetab,TriDateASC);
			             break;
	case "TriDateDESC" : if(count($reptab))  usort($reptab,TriDateDESC);
						 if(count($filetab)) usort($filetab,TriDateDESC);
			             break;
}

$cheminencode = rawurlencode($chemin);
$CheminDecompose = DecomposerChemin($chemin,$action,$tri);

// --------------------------------------- Affichage -------------------------------------- //
?>

<TABLE BORDER="0">
<TR>
	<TD BGCOLOR="<? echo $CouleurInfo; ?>" ALIGN="right"><? echo $strExplorateurChemin; ?></TD>
	<TD BGCOLOR="<? echo $CouleurChemin; ?>"><? echo $CheminDecompose; ?></font></TD>
</TR>
</TABLE> 

<P>
<TABLE WIDTH="790" BORDER="0" CELLPADDING="1" CELLSPACING="1">
<TR>
	<TD BGCOLOR="#C0DBEC">&nbsp;</TD>
	<TD BGCOLOR="#C0DBEC" ALIGN="center"><A HREF="./index.php?chemin=<? echo $cheminencode; ?>&tri=<? if($tri == "NomASC")     echo "NomDESC";     else echo "NomASC"; ?>"><? echo $strExplorateurNom; ?></A></TD>
	<TD BGCOLOR="#C0DBEC" ALIGN="center"><A HREF="./index.php?chemin=<? echo $cheminencode; ?>&tri=<? if($tri == "TailleASC")  echo "TailleDESC";  else echo "TailleASC"; ?>"><? echo $strExplorateurTaille; ?></A></TD>
	<TD BGCOLOR="#C0DBEC" ALIGN="center"><A HREF="./index.php?chemin=<? echo $cheminencode; ?>&tri=<? if($tri == "TriDateASC") echo "TriDateDESC"; else echo "TriDateASC"; ?>"><? echo $strExplorateurDate; ?></A></TD>
	<TD BGCOLOR="#C0DBEC" ALIGN="center"><B><? echo $strExplorateurPermissions; ?></B></TD>
	<TD BGCOLOR="#C0DBEC" ALIGN="center" COLSPAN="5"><B><? echo $strExplorateurActions; ?></B></TD>
</TR>
<TR><TD COLSPAN="10"><HR NOSHADE></TD></TR>

<? 

if($chemin != ".")
{
	$cheminretour = ModifChemin($chemin);
	$cheminretour = rawurlencode($cheminretour);

	?>
	<TR>
		<TD ALIGN="center"><A HREF="./index.php?chemin=<? echo $cheminretour; ?>&tri=<? echo $tri; ?>"><IMG SRC="./images/back.gif" BORDER="0"></A></TD>
		<TD ALIGN="left"  ><A HREF="./index.php?chemin=<? echo $cheminretour; ?>&tri=<? echo $tri; ?>">..</A></TD>
	</TR>
	<?
}

$cheminencode = rawurlencode($chemin);

// -------------------------------------- Affichage des répertoires --------------------------------------- //

for($i=0;$i<$repind;$i++)
{
	$nomrep      = $reptab[$i]["nom"];
	$cheminrep   = rawurlencode($chemin."/".$nomrep);
	$repencode   = rawurlencode($nomrep);
	$Permissions = $reptab[$i]["permissions"];
	$IndiceImage = $i;

	?>
	<TR>
		<TD ALIGN="center"><A HREF="./index.php?chemin=<? echo $cheminrep; ?>&tri=<? echo $tri; ?>"><IMG SRC="./images/dir.gif" BORDER="0"></A></TD>
		<TD ALIGN="left"  ><A HREF="./index.php?chemin=<? echo $cheminrep; ?>&tri=<? echo $tri; ?>"><? echo $nomrep; ?></A></TD>
		<TD ALIGN="right" ><? echo FormatTailleFichier($reptab[$i]["taille"],$strOctetAbrevation); ?></TD>
		<TD ALIGN="right" ><? echo $reptab[$i]["date"];        ?></TD>
		<TD ALIGN="center"><? echo $Permissions; ?></TD>

		<?
			if(EstVide("$chemintotal/$nomrep"))
			{
				if($ConfirmerEfface == "1") 
				{ ?><TD ALIGN="center"><A HREF="./confirmer.php?rep=<? echo $repencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=SupprimerRep"  onMouseOver="change('b_supp<? echo $IndiceImage;?>','b_supp_on')" onMouseOut="change('b_supp<? echo $IndiceImage;?>','b_supp_off')"><IMG SRC="./images/icone_supp.gif" NAME="b_supp<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurEffacer; ?>"></A></TD><? }
				else { ?><TD ALIGN="center"><A HREF="./index.php?rep=<? echo $repencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=SupprimerRep"	onMouseOver="change('b_supp<? echo $IndiceImage;?>','b_supp_on')"	onMouseOut="change('b_supp<? echo $IndiceImage;?>','b_supp_off')"><IMG SRC="./images/icone_supp.gif" NAME="b_supp<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurEffacer; ?>"></A></TD><? }
			}
			else { ?> <TD ALIGN="center"><A HREF="./confirmer.php?fichier=<? echo $repencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=SupprimerRepNV" onMouseOver="change('b_supp<? echo $IndiceImage;?>','b_supp_on')" onMouseOut="change('b_supp<? echo $IndiceImage;?>','b_supp_off')"><IMG SRC="./images/icone_supp.gif" NAME="b_supp<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurEffacer; ?>"></A></TD> <? }
		?>

		<TD ALIGN="center"><A HREF="./renomer.php?fichier=<? echo $repencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=Renomer"	     onMouseOver="change('b_ren<? echo $IndiceImage;?>','b_ren_on')"   onMouseOut="change('b_ren<? echo $IndiceImage;?>','b_ren_off')"  ><IMG SRC="./images/icone_ren.gif"  NAME="b_ren<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurRenomer; ?>"></A></TD>
		<TD ALIGN="center"><A HREF="./deplacer.php?fichier=<? echo $repencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=DeplacerRep" onMouseOver="change('b_move<? echo $IndiceImage;?>','b_move_on')" onMouseOut="change('b_move<? echo $IndiceImage;?>','b_move_off')"><IMG SRC="./images/icone_move.gif" NAME="b_move<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurDeplacer; ?>"></A></TD>
		<TD ALIGN="center"><A HREF="./copier.php?fichier=<? echo $repencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=CopierRep"     onMouseOver="change('b_copy<? echo $IndiceImage;?>','b_copy_on')" onMouseOut="change('b_copy<? echo $IndiceImage;?>','b_copy_off')"><IMG SRC="./images/icone_copy.gif" NAME="b_copy<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurCopier; ?>"></A></TD>
	 </TR>
	<?
}

// --------------------------------------- Affichage des fichiers ----------------------------------------- //

$IndiceImage++;


for($i=0;$i<$fileind;$i++)
{
	$nomfic      = $filetab[$i]["nom"];
	$ficencode   = rawurlencode($nomfic);
	$ext         = GetExtension($nomfic);
	$ext         = strtolower($ext);
	$icone       = GetIcone($ext);
	$affichage   = GetTypeAffichageFichier($ext);
	$type        = $affichage["Type"];
	$lien        = $affichage["Lien"];
	$Permissions = $filetab[$i]["permissions"];
	$IndiceImage += $i;


	?>
	<TR>
	<?
	if($EditionActive == "1")
	{  
		?>
		<TD ALIGN="center"><A HREF="#" ONCLICK="res = window.open('./<? echo $lien; ?>?fichier=<? echo $ficencode; ?>&chemin=<? echo $chemin; ?>&type=<? echo $type; ?>','voirfichier','resizable=yes,scrollbars=yes,statue=yes,width=<? echo $LargeurFenetre; ?>,height=<? echo $HauteurFenetre; ?>');"><IMG SRC ="./images/<? echo $icone ?>" BORDER="0"></A></TD>
		<TD ALIGN="left"  ><A HREF="#" ONCLICK="res = window.open('./<? echo $lien; ?>?fichier=<? echo $ficencode; ?>&chemin=<? echo $chemin; ?>&type=<? echo $type; ?>','voirfichier','resizable=yes,scrollbars=yes,statue=yes,width=<? echo $LargeurFenetre; ?>,height=<? echo $HauteurFenetre; ?>');"><? echo $nomfic; ?></A></TD>
		<?
	}
	else { ?><TD ALIGN="center"><IMG SRC ="./images/<? echo $icone ?>"></TD><TD ALIGN="left"  ><? echo $nomfic; ?></TD><? }
	?>
		<TD ALIGN="right" ><? echo FormatTailleFichier($filetab[$i]["taille"],$strOctetAbrevation); ?></TD>
		<TD ALIGN="right" ><? echo $filetab[$i]["date"]; ?></TD>
		<TD ALIGN="center"><? echo $Permissions; ?></TD>
		<?
			if($ConfirmerEfface == "1") { ?><TD ALIGN="center">
			<A HREF="./confirmer.php?fichier=<? echo $ficencode; ?>&action=2&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=SupprimerFichier" onMouseOver="change('b_supp<? echo $IndiceImage;?>','b_supp_on')" onMouseOut="change('b_supp<? echo $IndiceImage;?>','b_supp_off')"><IMG SRC="./images/icone_supp.gif" NAME="b_supp<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurEffacer; ?>"></A></TD><? }
			else { ?><TD ALIGN="center"><A HREF="./index.php?fichier=<? echo $ficencode; ?>&action=2&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=SupprimerFichier" onMouseOver="change('b_supp<? echo $IndiceImage;?>','b_supp_on')" onMouseOut="change('b_supp<? echo $IndiceImage;?>','b_supp_off')"><IMG SRC="./images/icone_supp.gif" NAME="b_supp<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurEffacer; ?>"></A></TD><? }
		?>
		<TD ALIGN="center"><A HREF="./renomer.php?fichier=<? echo $ficencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=Renomer"          onMouseOver="change('b_ren<? echo $IndiceImage;?>','b_ren_on')"   onMouseOut="change('b_ren<? echo $IndiceImage;?>','b_ren_off')"  ><IMG SRC="./images/icone_ren.gif"  NAME="b_ren<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurRenomer; ?>"></A></TD>
		<TD ALIGN="center"><A HREF="./deplacer.php?fichier=<? echo $ficencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=DeplacerFichier" onMouseOver="change('b_move<? echo $IndiceImage;?>','b_move_on')" onMouseOut="change('b_move<? echo $IndiceImage;?>','b_move_off')"><IMG SRC="./images/icone_move.gif" NAME="b_move<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurDeplacer; ?>"></A></TD>
		<TD ALIGN="center"><A HREF="./copier.php?fichier=<? echo $ficencode; ?>&chemin=<? echo $cheminencode; ?>&tri=<? echo $tri; ?>&action=CopierFichier"  	 onMouseOver="change('b_copy<? echo $IndiceImage;?>','b_copy_on')" onMouseOut="change('b_copy<? echo $IndiceImage;?>','b_copy_off')"><IMG SRC="./images/icone_copy.gif" NAME="b_copy<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurCopier; ?>"></A></TD>
		<TD ALIGN="center"><A HREF="./download.php?fichier=<? echo $ficencode; ?>&chemin=<? echo $cheminencode; ?>&taille=<? echo $filetab[$i]["taille"]; ?> " onMouseOver="change('b_download<? echo $IndiceImage;?>','b_download_on')" onMouseOut="change('b_download<? echo $IndiceImage;?>','b_download_off')"><IMG SRC="./images/icone_download.gif"  NAME="b_download<? echo $IndiceImage;?>" BORDER="0" ALT="<? echo $strExplorateurDownload ?>"></A></TD>
	</TR>
	<?
}

if(($repind == "0") && ($fileind == "0")) { ?><TR><TD COLSPAN="9" ALIGN="center"><B CLASS="Important"><? echo $strExplorateurPasDeFichier; ?></B></TD></TR><? }

// ------ fin du tableau ---- //

?>
<TR><TD COLSPAN="10"><HR NOSHADE></TD></TR>
</TABLE><BR>

<? $AfficherNbFileAndNbRep = 1; ?>
<? include "./basdepage.inc.php"; ?>
</BODY>
</HTML>