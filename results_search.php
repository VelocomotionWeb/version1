<?php include ("config/settings.inc.php");?>
<?php include ("config/config.inc.php");?>
<style>

.bloc.haut {
    width: 96%;
}
.bloc.left {
    width: 61.6%;
}
.bloc.right {
    float: left;
}
.bloc {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    line-height: 1.4;
	margin-top: 30px;
    padding: 20px;
    height: 200px !important;
}
.bloc.left, .bloc.right {
    height: 288px;
}
.bloc.left {
    float: left;
    margin-right: 17px;
    width: 61%;
}
.bloc.left img {
    border: 1px solid #ddd;
    box-shadow: 0 0 24px #666;
    float: right;
    height: 160px;
}

#lienboutique{
	border-radius: 9px;
	background-color: #84BB25;
	color: white;
	font-weight: bold;
	border: 1px solid #84BB25;
	box-shadow: none;
	border: none;
	padding: 2px 10px;
	transition: background-color, color, 0.5s;
}

#lienboutique:hover{
	background-color: white;
	color: #84BB25;
}

#link_seller {
  width: auto !important;
	margin-top: 20px;
}

#lienboutique, #link_seller{
	padding: 5px 10px;
}
</style>
<?
define('_PS_MODE_DEV_', true);
$simulation = 1; 

$ville = $_POST["ville"];
$date_depart = $_POST["date_depart"];
$date_fin = $_POST["date_fin"];
$adresse = $_POST["adresse"];
$id_seller = $_GET["id_seller"];
$trouve = 0;

// sépare la ville de la region / pays
$ville = @split(",", $ville);
$ville = $ville[0];
?>
<div class="bloc haut" style="display:none">
    Votre recherche : <br>
    <li>Ville : <b><?=$ville;?></b>
    <li>du : <b><?=$date_depart;?></b> au <b><?=$date_fin;?></b>
</div>    
<?			

// lecture dans la base des produits piur ces criteres
	$sql_ds = "SELECT * FROM "._DB_PREFIX_."store_locator WHERE city_name = '$ville' OR id_seller = '$id_seller'";
	//if ($id_product>0) $sql_ds .= " WHERE id_product=$id_product";
	//$sql_ds .= " )";
	$liste_ds = Db::getInstance()->ExecuteS($sql_ds);
	if (count($liste_ds)<1)
	{
		$sql_ds = "SELECT * FROM "._DB_PREFIX_."store_locator ";
		//$liste_ds = Db::getInstance()->ExecuteS($sql_ds);
	}
	//if ($simulation) echo "<br><small>$sql_ds<li>1.  " . date("h:i:s") . "</small>";
	foreach ($liste_ds as $row)
	{
		$trouve = 1;
		$sql_s = "SELECT * FROM "._DB_PREFIX_."marketplace_seller_info WHERE id = '".$row["id_seller"]."'";
		$liste_s = Db::getInstance()->ExecuteS($sql_s);
		//var_dump($liste_s);		
		$name_seller = $liste_s[0]["seller_name"];
		//if ($simulation) echo "<br><small>$sql_s<li> ..... $name_seller 1.  " . date("h:i:s") . "</small>";
		$sql_shop = "SELECT * FROM "._DB_PREFIX_."marketplace_shop WHERE id = '".$liste_s[0]["id"]."'";
		$liste_shop = Db::getInstance()->ExecuteS($sql_shop);
		//var_dump($liste_shop);		
		//$name_seller = $liste_shop[0]["seller_name"];
		?>
        <div class="bloc left">
            <!--Vous cherchez : <?=$row["city_name"];?>+<?=$row["street"];?><br>-->
            <img  class="left_img" src="/modules/marketplace/views/img/shop_img/<?=$row["id_seller"];?>-<?=$liste_shop[0]["shop_name"];?>.jpg" alt="{l s='Seller Image' mod='marketplace'}"/>
            
            Vendeur : <b><?=$liste_shop[0]["shop_name"];?></b><br>
            Boutique : <b><?=$row["name"];?></b><br>
            Adresse  : <?=$row["street"];?>, <?=$row["city_name"];?> <br>
            Tel  : <?=$row["phone"];?><br>           
            <? $url = @ereg_replace(" ", "-", strtolower($name_seller)); ?>
            <br>
            <a target="_blank" id="lienboutique" href="/?mp_shop_name=<?=$url;?>&fc=module&module=marketplace&controller=shopcollection">Lien vers la boutique</a>
            <br>
            <a target="_blank" href="/module/marketplace/shopdetail?flag=1&mp_shop_name=<?=$url;?>">
           		<input type="button" id="link_seller" value="Détail vendeur" class="submit">
            </a>
            <br>        
            <?
            $sql_ds = "SELECT * FROM "._DB_PREFIX_."store_locator WHERE city_name = '$ville'";
            //if ($id_product>0) $sql_ds .= " WHERE id_product=$id_product";
            //$sql_ds .= " )";
            $liste_ds = Db::getInstance()->ExecuteS($sql_ds);
            //if ($simulation) echo "<br><small>$sql_ds<li>1.  " . date("h:i:s") . "</small>";
            foreach ($liste_ds as $row)
            {
                /*
				?>
                Vendeur : <?=$row["id_seller"];?><br>
                Nom  : <?=$row["name"];?><br>
                <?
				*/
            }
            ?>
        </div>
        <div class="bloc right">
            <iframe
              width="300"
              height="160"
              frameborder="0" style="border:0"
              src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDAAx0cPh2MP4DbPzmVs_V2HslsSAoUSaQ&q=<?=$row["city_name"];?>+<?=$row["street"];?>" allowfullscreen>
            </iframe>
        </div>
        <br clear="all">
        <?
		
	}
	if (!$trouve) 
	{ 
		?>
		<div class='bloc haut'>
			Pas de boutique trouvée !
            <a target="_blank" href="/module/mpsellerlist/viewmorelist">
           		<input type="button" id="link_seller" value="Tous les vendeurs" class="submit">
            </a>
		</div>
        <?
	}
	if (!$trouve)
	{
		//include("http://www.velocomotion.com/module/mpsellerlist/viewmorelist?content_only=1");
	/*
		?>
        <div class="bloc right">
        <div class="bloc haut">
            Votre recherche : <br>
				<?
				$sql_ds = "SELECT * FROM "._DB_PREFIX_."store_locator";
                $liste_ds = Db::getInstance()->ExecuteS($sql_ds);
                //if ($simulation) echo "<br><small>$sql_ds<li>1.  " . date("h:i:s") . "</small>";
                foreach ($liste_ds as $row)
				{
					$sql_s = "SELECT * FROM "._DB_PREFIX_."marketplace_seller_info WHERE id = '".$row["id_seller"]."'";
					$liste_s = Db::getInstance()->ExecuteS($sql_s);
					//var_dump($liste_s);		
					$name_seller = $liste_s[0]["seller_name"];
					//if ($simulation) echo "<br><small>$sql_s<li> ..... $name_seller 1.  " . date("h:i:s") . "</small>";
					$sql_shop = "SELECT * FROM "._DB_PREFIX_."marketplace_shop WHERE id = '".$liste_s[0]["id"]."'";
					$liste_shop = Db::getInstance()->ExecuteS($sql_shop);
					//var_dump($liste_shop);		
					//$name_seller = $liste_shop[0]["seller_name"];
                	?>
					<li>Ville : <b><?=$ville;?></b>
					<li>du : <b><?=$date_depart;?></b> au <b><?=$date_fin;?></b>
					<?
				}
                ?>
        </div>    
        
        <?
	*/ 
	}
?>
