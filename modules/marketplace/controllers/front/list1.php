<?php
class MarketplaceList1ModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $id_lang = $this->context->cookie->id_lang;

        $obj_mp_product = new SellerProductDetail();
        $obj_mp_shop = new MarketplaceShop();

		$city = Tools::getValue('city');
		if (@ereg(",", $city))
		{
			$city = @split(",", $city);
			$city = $city[0];
		}
        $lng = Tools::getValue('lng');
        $lat = Tools::getValue('lat');
        $shop_link_rewrite = Tools::getValue('mp_shop_name');
        $shop_link_rewrite_ec = Tools::getValue('mp_shop_name');
        $id_category = Tools::getValue('id_category');
        $quantite = Tools::getValue('quantite');
        $distance = Tools::getValue('distance');
        $latitude = Tools::getValue('latitude');
        $longitude = Tools::getValue('longitude');
        $date_depart = Tools::getValue('date_depart');
        $date_fin = Tools::getValue('date_fin');
		if ( $date_depart == "") $date_depart = $this->dateadd('d', 1, date("d/m/Y"));
		//echo $date_depart."=". $this->datediff("d", $this->dateus($date_depart),  date("Y-m-d"), true);
		if ( $this->datediff("d",  $this->dateus($date_depart),  date("Y-m-d"), true)>0 )
		{
			$date_depart = $this->dateadd('d', 1, date("d/m/Y"));
			$date_fin = $this->dateadd('d', 1, date("d/m/Y"));
		}
		if ( $date_fin == "") $date_fin = $date_depart;
		//die("<li>$date_depart");
        $l_type_loc = Tools::getValue('l_type_loc').'-1';
		if ($distance==0) $distance = 100;
		if ($quantite==0) $quantite = 1;
		//echo "d=$distance";

		// liste des loueurs
		$seller_stores = StoreLocator::getAllStore(true);

		// tri par distance
		if ($seller_stores) {
			// get store location details
			$n=0;
			foreach ($seller_stores as $key => $store) {
				$obj_country = new Country($store['country_id'], $id_lang);
				$obj_state = new State($store['state_id']);
				$seller_stores[$key]['country_name'] = $obj_country->name;
				$seller_stores[$key]['state_name'] = $obj_state->name;
				$city_name = $seller_stores[$key]['city_name'];
				$city = $city;

				//  distance entre $city et $city_name

				$earth_radius = 6371;
				$LATB = $seller_stores[$key]['latitude'];
				$LONB = $seller_stores[$key]['longitude'];
				$lata = $latitude;
				$lona = $longitude;
				$rlo1 = deg2rad($lona);
				$rla1 = deg2rad($lata);
				$rlo2 = deg2rad($LONB);
				$rla2 = deg2rad($LATB);
				$dlo = ($rlo2 - $rlo1) / 2;
				$dla = ($rla2 - $rla1) / 2;
				$formule = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
				$d = 2 * atan2(sqrt($formule), sqrt(1 - $formule));
				$dist = round($earth_radius * $d,1);
				$seller_stores[$key]['distance_user'] = $dist;

				$sql_s = "SELECT * FROM "._DB_PREFIX_."marketplace_shop WHERE id = '".@ereg_replace("'", "''", $seller_stores[$key]['id_seller'])."'";
				$liste_s = Db::getInstance()->ExecuteS($sql_s);

				$obj_mpshop = new MarketplaceShop();
				$id_shop =@$liste_s[0]['id'];
				if ($id_shop>0)
				{
					$mp_shop_details = $obj_mpshop->getMarketPlaceShopDetail($id_shop);
					$shop_link_rewrite = $mp_shop_details['link_rewrite'];
					$seller_stores[$key]['link_rewrite'] = $shop_link_rewrite;
				}
				if(file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg')) {
					$seller_stores[$key]['img_exist'] = 1;
				} else {
					$seller_stores[$key]['img_exist'] = 0;
				}

			}
			/*
			*/
			// Tri croissant par distance
			foreach ($seller_stores as $key => $row) {
				$disttest[$key]  = @$row['distance_user'];
			}
			array_multisort($disttest, SORT_ASC, $seller_stores);
			foreach ($seller_stores as $key => $row)
			{
				//echo "<li>$shop_link_rewrite_ec=" . $row['link_rewrite'];
				if ($row['link_rewrite'] == $shop_link_rewrite_ec)
				{
					//if ($latitude<=0) $latitude = $row['latitude'];
					//if ($longitude<=0) $longitude = $row['longitude'];
					if ($city=="") $city = $row['city_name'];
					$refait = 1;
				}
			}
			// on refait le traitement si les données ont changées
			if ($refait==1)
			{
				foreach ($seller_stores as $key => $store) {
					$obj_country = new Country($store['country_id'], $id_lang);
					$obj_state = new State($store['state_id']);
					$seller_stores[$key]['country_name'] = $obj_country->name;
					$seller_stores[$key]['state_name'] = $obj_state->name;
					$city_name = $seller_stores[$key]['city_name'];
					$city = $city;

					$earth_radius = 6371;
					$LATB = $seller_stores[$key]['latitude'];
					$LONB = $seller_stores[$key]['longitude'];
					$lata = $latitude;
					$lona = $longitude;
					$rlo1 = deg2rad($lona);
					$rla1 = deg2rad($lata);
					$rlo2 = deg2rad($LONB);
					$rla2 = deg2rad($LATB);
					$dlo = ($rlo2 - $rlo1) / 2;
					$dla = ($rla2 - $rla1) / 2;
					$formule = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
					$d = 2 * atan2(sqrt($formule), sqrt(1 - $formule));
					$dist = round($earth_radius * $d,1);
					$seller_stores[$key]['distance_user'] = $dist;

					//$seller_stores[$key]['id_seller'] = $seller_stores[$key]['id_seller'];
					$sql_s = "SELECT * FROM "._DB_PREFIX_."marketplace_shop WHERE id = '".@ereg_replace("'", "''", $seller_stores[$key]['id_seller'])."'";
					$liste_s = Db::getInstance()->ExecuteS($sql_s);

					$obj_mpshop = new MarketplaceShop();
					$id_shop = @$liste_s[0]['id'];
					//die($id_shop);
					//$id_shop = 4;
					if ($id_shop>0)
					{
						$mp_shop_details = $obj_mpshop->getMarketPlaceShopDetail($id_shop);
						$shop_link_rewrite = $mp_shop_details['link_rewrite'];
						$seller_stores[$key]['link_rewrite'] = $shop_link_rewrite;
					}
					if(file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg')) {
						$seller_stores[$key]['img_exist'] = 1;
					} else {
						$seller_stores[$key]['img_exist'] = 0;
					}
					//if () $seller_stores[$key]['img_exist']

				}
				// Tri croissant par distance
				foreach ($seller_stores as $key => $row) {
					$disttest[$key]  = @$row['distance_user'];
				}
				array_multisort($disttest, SORT_ASC, $seller_stores);
			}
			//var_dump($seller_stores);
			$this->context->smarty->assign('manage_status', Configuration::get('MP_STORE_LOCATION_ACTIVATION'));
			$this->context->smarty->assign('store_locations', $seller_stores);
			//echo count($seller_stores);
		}
		if ($shop_link_rewrite_ec=="" )
		{
			// retrouve le shop le plus pret
			$shop_link_rewrite_ec = $seller_stores[0]["link_rewrite"];
		}

        if (Tools::getValue('shop') && $id_category) //if from category
            $id_shop = Tools::getValue('shop');
        else
            $id_shop = $obj_mp_shop->getIdShopByName($shop_link_rewrite_ec);

		if ($id_shop)
        {

			$shop = MarketplaceShop::getMarketPlaceShopDetail($id_shop);
			$name_shop = $shop["shop_name"];
            $orderby = Tools::getValue('orderby');
            $orderway = Tools::getValue('orderway');

            //default orderby and orderway
            if (!$orderby)
                $orderby = 'id';
            elseif ($orderby == 'name')
                $orderby = 'product_name';

			// force un tri par nom
			$orderby = 'product_name';
            if (!$orderway)
                $orderway = 'desc';

            // for creating pagination
            $id_seller = MarketplaceShop::findMpSellerIdByShopId($id_shop);
			$store = StoreLocator::getSellerStore($id_seller);
			$id_store = $store[0]['id'];
            $all_active_products = SellerProductDetail::getMpSellerProductDetails($id_seller, true);
            if ($all_active_products)
                $this->pagination(count($all_active_products));

            //get all marketplace product
			$id_category = false;
			$this->p = 1; $this->n = 99;
            if ($id_category)
                $mp_shop_product = $this->getMpProductByCategory($id_category, $id_shop, $this->p, $this->n, $orderby, $orderway);
            else
                $mp_shop_product = $obj_mp_product->findAllActiveProductInMarketPlaceShop($id_shop, $this->p, $this->n, $orderby, $orderway);

            if ($mp_shop_product)
            {
                //get category details
                $mp_product = $obj_mp_product->findAllActiveProductInMarketPlaceShop($id_shop, $this->p, $this->n, $orderby, $orderway);
                $catg_details = $this->getMpProductCategoryCount($mp_product);
                if ($catg_details)
                     $this->context->smarty->assign('catg_details', $catg_details);

                //Product array by category or by default
                foreach ($mp_shop_product as $key => $product)
                {
					/* AJOUTER PAR SRDEV - test si les jours sont dispo dans reserv */
					$id_product = $product['id_product'];
					// boucle sur le nombre de jour a partie du jour de depart
					//echo "quantite=$quantite date_depart=$date_depart product=$id_product $jour";

					// lecture si le produit est deja en panier
					$sql_r = "SELECT * FROM "._DB_PREFIX_."cart_product WHERE id_product = $id_product AND id_cart = '".$this->context->cart->id."' ";
					$result_r = Db::getInstance()->ExecuteS($sql_r);
					$product_in_cart = $result_r[0]["id_cart"]>0?1:0;
					$mp_shop_product[$key]['product_in_cart'] = $product_in_cart;
					//echo "$sql_r  $id_order $quantite";

					$id_order = 0;
					for($cpt=0; $cpt<=$quantite; $cpt++)
					{
						$jour = $this->dateadd('d', $cpt, $date_depart);
						$sql_r = "SELECT * FROM reserv WHERE id_product = $id_product AND dt_reserv = '$jour' ";
						$result_r = Db::getInstance()->ExecuteS($sql_r);
						$id_order = $result_r[0]["id_order"];
						if ($id_order!=0) $mp_shop_product[$key]['product_in_cart'] =2;
						//echo "<li>trouve d=$date_depart f=$quantite $cpt $jour :$id_order";
					}

					$sql = "";
					$obj_product = new Product($product['id_product'], true, $id_lang);
					/* AJOUTER PAR SRDEV - verif si categorie select */
					$sql_r = "SELECT * FROM "._DB_PREFIX_."category_product WHERE id_product = ".$product['id_product']." ";
					if ($l_type_loc!="-1") $sql_r .= " AND id_category IN ($l_type_loc) ";
					$result_r = Db::getInstance()->ExecuteS($sql_r);
					$id_category = $result_r[0]["id_category"];

					if ($id_order==0 && $id_category!=0&& !$product_in_cart/* Pour ne pas afficher les produits en panier */)
					{
						/* FIN SRDEV - test si les jours sont dispo dans reserv */
						$cover = Product::getCover($product['id_product']);
						if ($cover)
							$mp_shop_product[$key]['image'] = $product['id_product'].'-'.$cover['id_image'];
						else
							$mp_shop_product[$key]['image'] = 0;

						$mp_shop_product[$key]['product_name'] = $obj_product->name;
						$mp_shop_product[$key]['qty_available'] = StockAvailable::getQuantityAvailableByProduct($product['id_product']);
						$mp_shop_product[$key]['product'] = $obj_product;
						$mp_shop_product[$key]['link'] = $this->context->link->getProductLink($obj_product);

						if ($mp_shop_product[$key]['product_in_cart']==0) $mp_shop_product[md5($mp_shop_product[$key]['product_name'])]++;

						$mp_shop_product[$key]['count'] = $mp_shop_product[md5($mp_shop_product[$key]['product_name'])]*1;
						$mp_shop_product[$key]['product_cname'] = md5($obj_product->name);
						//echo "<li>".$mp_shop_product[$key]['product_name'] . "==>".$mp_shop_product[$key]['count'];
						//echo "<li>".$mp_shop_product[$key]['count'] . "/".md5($mp_shop_product[$key]['product_name']) .
						//"/".$mp_shop_product[md5($mp_shop_product[$key]['product_name'])];

						$mp_shop_product[$key]['lang_iso'] = $this->context->language->iso_code;
						$mp_shop_product[$key]['link_rewrite'] = $obj_product->link_rewrite;
						$mp_shop_product[$key]['available_for_order'] = $obj_product->available_for_order;
						$mp_shop_product[$key]['show_price'] = $obj_product->show_price;


						/* AJOUTER PAR SRDEV - rempli un autre tableau des produits valides */
						$products[] = $mp_shop_product[$key];
					}
                }
                foreach ($products as $key => $product)
                {
					foreach ($products as $key0 => $product0) if ($product0['product_name'] == $product['product_name'] )
					{
						$products[$key0]['count_max'] = $product['count'];
						$products[$key0]['count_id'][] = $product['id_product'];
					}
				}
			}

			/* AJOUTER PAR SRDEV - assigne les variables pour le template */
			$this->context->smarty->assign(array('orderby'=> $orderby,
				'orderway'=> $orderway,
				'defaultorederby' => 'id',
				'lng' => $lng,
				'lat' => $lat,
				'shop_link_rewrite' => $shop_link_rewrite_ec,
				'shop_link_rewrite_ec' => $shop_link_rewrite_ec,
				'id_store' => $id_store,
				'city' => $city,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'distance' => $distance,
				'store' => $store,
				'mp_shop_product' => $mp_shop_product,
				'products' => $products,
				'id_shop' => $id_shop,
				'quantite'=> $quantite,
				'date_depart'=> $date_depart,
				'date_fin'=> $date_fin,
				'nameshop' => $name_shop
				));
            $this->setTemplate('list1.tpl');
        }
    }

    public function getMpProductCategoryCount($mp_product)
    {
        $catg_details = array();
        $id_lang = $this->context->language->id;
        if ($mp_product)
        {
            foreach ($mp_product as $product)
            {
                if ($product['active'])
                {
                    $obj_prod = new Product($product['id_product'], false, $id_lang);
                    $catgs = $obj_prod->getCategories();
                    foreach ($catgs as $catg)
                    {
                        $obj_catg = new Category($catg, $id_lang);
                        if (!array_key_exists($catg, $catg_details))
                        {
                            if ($catg != Category::getRootCategory()->id)
                                $catg_details[$catg] = array('id_category' => $catg,
                                                            'Name' => $obj_catg->name,
                                                            'NoOfProduct' => 1);
                        }
                        else
                            $catg_details[$catg]['NoOfProduct'] += 1;

                    }
                }
            }
        }

        if ($catg_details)
            return $catg_details;

        return false;
    }

    public function getMpProductByCategory($id_category, $id_shop, $p, $n, $orderby, $orderway)
    {
        $obj_mp_product = new SellerProductDetail();
        $mp_product = $obj_mp_product->findAllActiveProductInMarketPlaceShop($id_shop, $p, $n, $orderby, $orderway);

        if ($mp_product)
            foreach ($mp_product as $key => $product)
            {
                $obj_prod = new Product($product['id_product'], false, $this->context->language->id);
                $catgs = $obj_prod->getCategories();
                if (!in_array($id_category, $catgs))
                    unset($mp_product[$key]);
            }

        return array_values($mp_product); //for ordering indexes
    }

    public function setMedia()
    {

        parent::setMedia();
		$this->context->controller->addJqueryPlugin('fancybox');
        //$this->addJS(_MODULE_DIR_.'marketplace/views/js/list.js');
		$this->addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'screen');
		$this->addJqueryPlugin('fancybox');
		$this->addCSS(array(
            _MODULE_DIR_.'blocksearchhome/blocksearchhome_top.css',
            _MODULE_DIR_.'marketplace/views/css/list.css',
            _MODULE_DIR_.'mpstorelocator/views/css/list_seller.css',
            _MODULE_DIR_.'marketplace/views/css/header.css'
        ));

       /* if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0)
            $this->addJS(_THEME_JS_DIR_ . 'products-comparison.js');*/
    }

	 // function dateadd
	public function DateAdd($interval, $number, $date) {
		if (@ereg("-", $date)) $date = $this->datefr($date, "NON"); // on met au format francais pour le traitement si contient -
		list($day, $month, $year)=split("/", $date); // recuperation des elements de la date
		switch ($interval) {

			case 'y':   // add year
				$year+=$number;
				break;

			case 'm':    // add month
				$month+=$number;
				break;

			case 'd':    // add days
				$day+=$number;
				break;

			case 'w':    // add week
				$day+=($number*7);
				break;

			case 'ww':    // add week
				$day+=($number*7);
				break;

			case 'h':    // add hour
				$hours+=$number;
				break;

			case 'n':    // add minutes
				$minutes+=$number;
				break;

			case 's':    // add seconds
				$seconds+=$number;
				break;

		}
		$dateplus = @date("d/m/Y", mktime(0, 0, 0, $month, $day,  $year ));// ajoute le délai à la date
		return $dateplus;
	}
	public function datefr($date_modif, $heurea="") {
		//echo  $date_modif.$heurea;
		$date_modif_retour = $date_modif;
		//echo  "retour=".$date_modif_retour;
		if ($date_modif_retour== "") return "";
		// si un tiret alors c'est pas un format americain donc pas de formatage
		//if (@ereg("-", $date_modif_retour)) return $date_modif_retour;
		if (@ereg("-", $date_modif)) $date_modif_retour = substr($date_modif,8,2)."/".substr($date_modif,5,2)."/".substr($date_modif,0,4);
		if ($heurea=="") if (@ereg(":", $date_modif)) $date_modif_retour = $date_modif_retour." ".substr($date_modif,11,2).":".substr($date_modif,14,2);
		//if (! is_date( $date_modif ) ) $date_modif = "";
		if (@ereg("//", $date_modif_retour)) $date_modif_retour = "";
		//if ($date_modif_retour= "") echo "date_modif_retour=$date_modif_retour";
		return substr($date_modif_retour,0,16);
	} // Entre=2000-12-30 sort=30/12/2000
	function datediff( $str_interval, $dt_menor, $dt_maior, $relative=false){
       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

       $diff = date_diff( $dt_menor, $dt_maior, ! $relative);

       switch( $str_interval){
           case "y":
               $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; break;
           case "m":
               $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
               break;
           case "d":
               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
               break;
           case "h":
               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
               break;
           case "i":
               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
               break;
           case "s":
               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
               break;
          }
       if( $diff->invert)
               return -1 * $total;
       else    return $total;
    }
	function dateus($date_modif){

		$date_modif_retour = $date_modif;
		if (@ereg("/", $date_modif))
		{
			$date_modif_retour =  substr($date_modif,6,4)."-".substr($date_modif,3,2)."-".substr($date_modif,0,2);
			if ($heurea=="") if (@ereg(":", $date_modif)) $date_modif_retour .= " ".substr($date_modif,11,2).":".substr($date_modif,14,2);
		}
		//echo substr($date_modif,6,4)."/".substr($date_modif,3,2)."/".substr($date_modif,0,2);
		return $date_modif_retour;
	} // Entre=30/12/2000 sort=2000-12-30


}
?>