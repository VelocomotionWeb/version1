<?php
class mpstorelocatorlist_sellerModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {
		parent::initContent();
		
		$this->context->controller->addJqueryPlugin('fancybox');
		
		if (Configuration::get('MP_STORE_ALL_SELLER')) {
			$id_lang = $this->context->language->id;
			$seller_stores = StoreLocator::getAllStore(true);
			
			
			if ($seller_stores) {
				// get store location details
				$n=0;
				foreach ($seller_stores as $key => $store) {
					$obj_country = new Country($store['country_id'], $id_lang);
					$obj_state = new State($store['state_id']);
					$seller_stores[$key]['country_name'] = $obj_country->name;
					$seller_stores[$key]['state_name'] = $obj_state->name;   
					$city_name = $seller_stores[$key]['city_name'];
					$city = Tools::getValue('city');
					
					//  distance entre $city et $city_name
					/*$sql_d = "SELECT * FROM "._DB_PREFIX_."store_locator WHERE city_name = '".$city_name."'";
					//$infos_city = Db::getInstance()->ExecuteS($sql_d);
					$name = @$infos_city[0]['name'];*/
					
					$earth_radius = 6371;
					/*$LATB =$infos_city[0]['latittude'];
					$LONB =$infos_city[0]['longitude'];*/
					$LATB = $seller_stores[$key]['latitude'];
					$LONB = $seller_stores[$key]['longitude'];
					$lata = Tools::getValue('latitude');
					$lona = Tools::getValue('longitude');
					$rlo1 = deg2rad($lona);
					$rla1 = deg2rad($lata);
					$rlo2 = deg2rad($LONB);
					$rla2 = deg2rad($LATB);
					$dlo = ($rlo2 - $rlo1) / 2;
					$dla = ($rla2 - $rla1) / 2;
					$formule = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
					$d = 2 * atan2(sqrt($formule), sqrt(1 - $formule));
					$dist = round($earth_radius * $d,0);
					$seller_stores[$key]['distance_user'] = $dist;
					
					//$seller_stores[$key]['id_seller'] = $seller_stores[$key]['id_seller'];
					$sql_s = "SELECT * FROM "._DB_PREFIX_."marketplace_shop WHERE id = '".@ereg_replace("'", "''", $seller_stores[$key]['id_seller'])."'";
					$liste_s = Db::getInstance()->ExecuteS($sql_s);
					
					$obj_mpshop = new MarketplaceShop();
					$id_shop =@$liste_s[0]['id']; 
					//$id_shop = $obj_mpshop->getIdShopByName(@ereg_replace("'", "''", $liste_s[0]['shop_name']));
//echo "<li>$id_shop  ".$liste_s[0]['id'] . " "  ;				
					if ($id_shop>0) 
					{
//echo "<pre>".$n++;var_dump($id_shop);echo	 "</pre>";
						$mp_shop_details = $obj_mpshop->getMarketPlaceShopDetail($id_shop);
						$shop_link_rewrite = $mp_shop_details['link_rewrite'];
						$seller_stores[$key]['link_rewrite'] = $shop_link_rewrite;
//echo $seller_stores[$key]['link_rewrite'];				
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
				$this->context->smarty->assign('manage_status', Configuration::get('MP_STORE_LOCATION_ACTIVATION'));
				$this->context->smarty->assign('store_locations', $seller_stores);
			}
			if(Tools::getValue('date_depart')=="")
			{
				if($this->context->cookie->date_depart == ""){// 3e cas par defaut
					 $date_depart = date("d/m/Y");
					 $date_fin = date("d/m/Y");
					 $quantite = 1;
					$this->context->cookie->date_depart = $date_depart;
					$this->context->cookie->date_fin = $date_fin;
					$this->context->cookie->quantite = $quantite;
				}
				else { //2e cas avec cookie
					$date_depart = $this->context->cookie->date_depart;
					$date_fin = $this->context->cookie->date_fin;
					$quantite = $this->context->cookie->quantite;
				}
			}
			else{//1e cas avec getvalue
				$date_depart = Tools::getValue('date_depart');
				$date_fin = Tools::getValue('date_fin');

				$quantite = Tools::getValue('quantite');
				$this->context->cookie->date_depart = $date_depart;
				$this->context->cookie->date_fin = $date_fin;
				$this->context->cookie->quantite = $quantite;
			}
			
			//$date_depart = Tools::getValue('date_depart')==""?date("d/m/Y"):Tools::getValue('date_depart');
			//$date_fin = Tools::getValue('date_fin')==""?date("d/m/Y"):Tools::getValue('date_fin');
			$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
			$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
			$this->context->smarty->assign('city', Tools::getValue('city'));
			$this->context->smarty->assign('latitude', Tools::getValue('latitude'));
			$this->context->smarty->assign('longitude', Tools::getValue('longitude'));
			$this->context->smarty->assign('distance', Tools::getValue('distance')==''?100:Tools::getValue('distance'));
			$this->context->smarty->assign('date_depart', $date_depart);
			$this->context->smarty->assign('date_fin', $date_fin);
			$this->context->smarty->assign('quantite', $quantite);
			$this->context->smarty->assign('l_type_loc', Tools::getValue('l_type_loc'));
			$this->context->smarty->assign('id_store', Tools::getValue('id_store'));
			
			
			// recherche des attributs pour type loc			
			$sql = "SELECT * FROM ps_category_lang cl 
					INNER JOIN ps_category c ON cl.id_category = c.id_category WHERE c.id_parent = 23 and cl.id_lang = 1
					ORDER BY position ";			
			$type_locs = Db::getInstance()->ExecuteS($sql);
			$this->context->smarty->assign('type_locs', $type_locs);
			$this->context->smarty->assign('nbtype_locs', count($type_locs));
		
			
			$this->setTemplate('list_seller.tpl');
		}
	}

	public function setMedia()
	{
		parent::setMedia();

       // $this->addJS(_MODULE_DIR_.'marketplace/views/js/shop_collection.js');
       // $this->addJS(_MODULE_DIR_.'/themes/jms_travel/js/modules/blockcart/ajax-cart.js');

		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/store_details.css');
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.js');
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.css');
		$this->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places");
		//$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/storedetails.js');
      /*  $this->addCSS(array(
            _MODULE_DIR_.'marketplace/views/css/shop_collection.css',
            _MODULE_DIR_.'marketplace/views/css/header.css'
        ));
		*/
	}
}
?>