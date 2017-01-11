<?	
require("./config.inc.php");
require("./fonctions.inc.php");
AfficherEntete($strAproposTitre,"./config.css");
?>
<BODY BGCOLOR="<? echo $strColorFond ?>" BGPROPERTIES="fixed" BACKGROUND="<? echo $ImageFond; ?>">
<?
$strTitre = $strAproposTitre;
include "./entete.inc.php";
?>

<TABLE WIDTH="790" BORDER="0" CELLPADDING="5"><TR><TD ALIGN="center">

<BR><BR>
<TABLE><TR><TD>
<? echo $strAproposAuteur; ?><A HREF="mailto:elegac@free.fr"><B>Erlé LE GAC</B></A><BR>
<? echo $srtAproposSite1; ?><B><? echo $strExplorateurTitre; ?></B><? echo $srtAproposSite2; ?><A HREF="http://elegac.free.fr" TARGET="_blank"><B>PhpMyBisKote</B></A><P>
<? echo $srtAproposInterface1; ?><A HREF="mailto:fguibert@free.fr"><B>Fabien Guibert</B></A><? echo $srtAproposInterface2; ?><A HREF="http://fguibert.free.fr" TARGET="_blank"><B>Befa Site</B></A><P>
<? echo $srtAproposMerci1; ?><A HREF="mailto:franck.poupelin@free.fr"><B>Franck Poupelin</B></A><? echo $srtAproposMerci2; ?><P>
</TD></TR></TABLE>
<P>
<TABLE CELLPADDING="1" CELLSPACING="0">
<TR><TH COLSPAN="3"><H3><? echo $srtAproposTraduction; ?></H3></TH></TR>
<TR><TD><IMG SRC="./images/portugais.gif"   ALT="<? echo $srtLanguePortugais ?>"></TD>  <TD WIDTH="15">&nbsp;</TD><TD><B><A HREF="mailto:amc@ip.pt">Alexandre Cunha</A></B></TD></TR>
<TR><TD><IMG SRC="./images/neerlandais.gif" ALT="<? echo $srtLangueNeerlandais ?>"></TD><TD WIDTH="15">&nbsp;</TD><TD><B><A HREF="mailto:zipkid@yournet.be">Stefan 'Zipkid' Goethals</A></B></TD></TR>
<TR><TD><IMG SRC="./images/espagnol.gif"    ALT="<? echo $srtLangueEspagnol ?>"></TD>   <TD WIDTH="15">&nbsp;</TD><TD><B><A HREF="mailto:fsam@aleph.org.mx">Francisco Sam Castillo</A></B></TD></TR>
<TR><TD><IMG SRC="./images/allemand.gif"    ALT="<? echo $srtLangueAllemand ?>"></TD>   <TD WIDTH="15">&nbsp;</TD><TD><B><A HREF="mailto:b.tenhumberg@web.de">Berthold Tenhumberg</A></B></TD></TR>
<TR><TD><IMG SRC="./images/italien.gif"     ALT="<? echo $srtLangueItalien ?>"></TD>    <TD WIDTH="15">&nbsp;</TD><TD><B><A HREF="mailto:f.malfatto@fmal.com">Fulvio Malfatto</A></B></TD></TR>
<TR><TD><IMG SRC="./images/japon.gif"       ALT="<? echo $srtLangueJaponais ?>"></TD>   <TD WIDTH="15">&nbsp;</TD><TD><B><A HREF="mailto:mileston@po.baywell.ne.jp">Masanori Komagamine</A></B></TD></TR>


</TABLE>

</TD></TR></TABLE>
<P><BR>

<? include "./basdepage.inc.php"; ?>

</BODY>
</HTML>