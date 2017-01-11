<?php include ("config/settings.inc.php");?>
<?php include ("config/config.inc.php");?>
<style>
body {
    width: 1000px;
}
.bloc.haut {
    width: 96%;
}
.bloc.left {
    width: 61.6%;
}
.bloc {
    border: 1px solid #ddd;
    border-radius: 10px;
    float: left;
/*     margin-bottom: 10px; */
	margin-top: 30px;
    padding: 20px;
    height: 200px !important;
}
.bloc.left, .bloc.right {
    height: 244px;
}

.bloc.left {
    width: 61.6%;
}
.bloc.left img {
    border: 1px solid #ddd;
    /*border-radius: 20px;*/
    box-shadow: 0 0 24px #666;
    float: right;
/*     width: 250px; */
	height: 160px;
	width: auto;
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
?>
<div class="bloc haut">
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
            Url  : <?=$liste_shop[0]["link_rewrite"];?><br>
            
            Plan maps : <br>
            <?
			$url = @ereg_replace(" ", "-", strtolower($name_seller));
			?>
            <a target="_blank" href="/module/marketplace/shopdetail?flag=1&mp_shop_name=<?=$url;?>">Lien vers la page vendeur</a>
            <br>
            <a target="_blank" href="/?mp_shop_name=<?=$url;?>&fc=module&module=marketplace&controller=shopcollection">Lien vers la boutique</a>
            <br>
            Liste des produit dispo pour cette période : <br>
                    
            <?
            $sql_ds = "SELECT * FROM "._DB_PREFIX_."store_locator WHERE city_name = '$ville'";
            //if ($id_product>0) $sql_ds .= " WHERE id_product=$id_product";
            //$sql_ds .= " )";
            $liste_ds = Db::getInstance()->ExecuteS($sql_ds);
            //if ($simulation) echo "<br><small>$sql_ds<li>1.  " . date("h:i:s") . "</small>";
            foreach ($liste_ds as $row)
            {
                ?>
                Vendeur : <?=$row["id_seller"];?><br>
                Nom  : <?=$row["name"];?><br>
                <?
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
			<a href="/module/mpsellerlist/viewmorelist" target="_blank">Tous les vendeurs</a>
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
