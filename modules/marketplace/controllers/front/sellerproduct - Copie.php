<?php
class MarketplacesellerproductModuleFrontController extends ModuleFrontController
{
    public function initContent() 
    {
        parent::initContent();
        $id_lang = $this->context->cookie->id_lang;
        
        $obj_mp_product = new SellerProductDetail();
        $obj_mp_shop = new MarketplaceShop();

        $shop_link_rewrite = Tools::getValue('mp_shop_name');
        $id_category = Tools::getValue('id_category');
        $quantite = Tools::getValue('quantite');
        $date_depart = Tools::getValue('date_depart');
        $date_fin = Tools::getValue('date_fin');
        $l_type_loc = Tools::getValue('l_type_loc').'-1';	


        if (Tools::getValue('shop') && $id_category) //if from category
            $id_shop = Tools::getValue('shop');
        else
            $id_shop = $obj_mp_shop->getIdShopByName($shop_link_rewrite); //if direct from menu

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
			$this->n = 99;
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
					
					if (/*$id_order==0 && $id_category!=0*/1)
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

				/* AJOUTER PAR SRDEV - assigne les variables pour le template */
                $this->context->smarty->assign(array('orderby'=> $orderby,
                                            'orderway'=> $orderway,
                                            'defaultorederby' => 'id',
                                            'name_shop' => $shop_link_rewrite,
                                            'id_store' => $id_store,
                                            'store' => $store,
                                            'mp_shop_product' => $mp_shop_product,
											'products' => $products,
                                            'id_shop' => $id_shop,
											'quantite'=> Tools::getValue('quantite'),
											'date_depart'=> Tools::getValue('date_depart'),
											'date_fin'=> Tools::getValue('date_fin'),
											'nameshop' => $name_shop
                                            ));
            }
            $this->setTemplate('sellerproduct.tpl');
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
        $this->addJS(_MODULE_DIR_.'marketplace/views/js/shop_collection.js');

        $this->addCSS(array(
            _MODULE_DIR_.'marketplace/views/css/sellerproduct.css',
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
	public function datefr($date_modif, $heurea="") // Entre=2000-12-30 sort=30/12/2000
	{
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
	}

}
?>