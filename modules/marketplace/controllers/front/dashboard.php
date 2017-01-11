<?php
class MarketplaceDashboardModuleFrontController extends ModuleFrontController 
{
	public function initContent() 
	{
		parent::initContent();
		$link = new Link();
		$seller_stores = StoreLocator::getAllStore(true);
		
		if (isset($this->context->cookie->id_customer))
		{
			$id_customer = $this->context->cookie->id_customer;

			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$obj_mp_customer = new MarketplaceCustomer();
				$obj_mp_seller = new SellerInfoDetail();

				$mp_customer = $obj_mp_customer->findMarketPlaceCustomer($id_customer); // Get mp seller details 

				//if seller is approved/active
				if ($mp_customer['marketplace_seller_id'] && $mp_customer['is_seller'] == 1)
				{
					$mp_seller_id = $mp_customer['marketplace_seller_id'];
					$id_shop = MarketplaceShop::getMpShopIdByCustomerId($id_customer);
					$mp_seller_info = $obj_mp_seller->sellerDetail($mp_seller_id);
					if ($id_shop)
					{
						$obj_mpid_shop = new MarketplaceShop($id_shop);
						$name_shop = $obj_mpid_shop->link_rewrite;
						$smarty_var = array('customer_id' => $id_customer,
											'id_shop' => $id_shop,
											'name_shop' => $name_shop,
											'logic' => 1,
											'seller_name' => $mp_seller_info['seller_name']);

					
						$mp_ordered_product = $this->getMpCustomerOrders();
						
						if ($mp_ordered_product)
						{
							foreach ($mp_ordered_product as $key => $order)
							{
								$obj_customer = new Customer($order['order_by_cus']);
								$mp_ordered_product[$key]['buyer_name'] = $obj_customer->firstname.' '.$obj_customer->lastname;
							}
							$smarty_var['ordered_products'] = $mp_ordered_product;
						}

						//SRDEV récupèration paramètre pour l'aperçu des produits
						foreach ($seller_stores as $key => $store) {
							$id_seller = $seller_stores[$key]['id_seller'];
							if ($id_seller == $mp_seller_id) {
								$city_name = $seller_stores[$key]['city_name'];
								$city_name = ucfirst(strtolower($city_name));
								$lat = $seller_stores[$key]['latitude'];
								$lon = $seller_stores[$key]['longitude'];
								break;
							}
						}
						$this->context->smarty->assign('city_name', $city_name);
						$this->context->smarty->assign('latitude', $lat);
						$this->context->smarty->assign('longitude', $lon);	
							
						// for statics - Asia/Kolkata - Time Zone Information - Daylight ...
						date_default_timezone_set('Asia/Calcutta');
						if (Tools::getIsset('from_date') && Tools::getIsset('to_date'))
						{
							if(Tools::getValue('from_date') == '' || Tools::getValue('to_date') == '')
							{
								$time_stamp = time();
								$dat = getdate($time_stamp);
								$j = 29;
							}
							else
							{
								$end_date = Tools::getValue('to_date');
								$from_date = strtotime(Tools::getValue('from_date'));

								$todate = strtotime($end_date);
								if($todate < $from_date)
								{
									$error = "To date must be greater than From date";
									$this->context->smarty->assign("error", $error);
									$time_stamp = time();
									$dat = getdate($time_stamp);
									$j = 29;
								}
								else
								{
									$time_stamp=$todate;
									$dat = getdate($time_stamp);
									$total_difffernce_btwn_date = ($todate-$from_date)/86400;
									$j = (int)$total_difffernce_btwn_date;							 
								}
							}
						}
						else
						{
							$time_stamp = time()+86400;
							$dat = getdate($time_stamp);
							$j = 29;
						}

						$newdate = array();
						$time_stamp_date = array();
						for($i = $j; $i >= 0; $i--)
						{
							$time_stamp_date[$i] = $time_stamp-$i*86400;
							$dat = getdate($time_stamp-$i*86400);
							$newdate[$i] = $dat['year'].'-'.$dat['mon'].'-'.$dat['mday'];
						}
						$todate = $newdate[0];
						$from_date = $newdate[$j];
						$l= $j;

						$this->context->smarty->assign("newdate", $newdate);
						$product_price_detail = array();
						$count_order_detail = array();
						for($i = $l; $i > 0; $i--)
						{
							$prev= $newdate[$i];
							$j = $i-1;
							$next = $newdate[$j];
							

							$total_price = $this->getOrderTotalPrice($prev, $next);
							$count_order = $this->getTotalOrderCount($prev, $next);
							
							$product_price_detail[$i] = Tools::ps_round($total_price[0]['total_price'], 2);
							$count_order_detail[$i] = $count_order[0]['total_order'];
						}

						$smarty_var = array_merge($smarty_var, array('product_price_detail' => $product_price_detail,
																	 'count_order_detail' => $count_order_detail,
																	 'to_date' => $todate,
																	 'from_date' => $from_date,
																	 'title_text_color' => Configuration::get('MP_TITLE_TEXT_COLOR'),
																	 'title_bg_color' => Configuration::get('MP_TITLE_BG_COLOR')
																	 ));
					}
					
					$smarty_var['is_seller'] = $mp_customer['is_seller'];
					$this->context->smarty->assign($smarty_var);
					$this->setTemplate('dashboard.tpl');
				}
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('marketplace', 'dashboard')));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
		$this->addJqueryUI(array('ui.datepicker'));
		
		//datepicker
		$this->addJS(array(
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui.js',
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-sliderAccess.js',
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.js',
				));
		$this->addCSS(_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.css');
    }

    public function getMpCustomerOrders()
    {
    	return Db::getInstance()->executeS("SELECT ordd.`product_price` as total_price,
    		ordd.`product_quantity` as qty,
    		ordd.`id_order` as id_order,
    		ord.`id_customer` as order_by_cus,
    		ord.`payment` as payment_mode,
    		cus.`firstname` as seller_name,
    		ord.`date_add`,
    		ords.`name` as order_status,
    		ord.`id_currency` as id_currency
    		FROM `"._DB_PREFIX_."marketplace_shop_product` msp
    		JOIN `"._DB_PREFIX_."order_detail` ordd on (ordd.`product_id` = msp.`id_product`)
    		JOIN `"._DB_PREFIX_."orders` ord on (ordd.`id_order` = ord.`id_order`)
    		JOIN `"._DB_PREFIX_."marketplace_seller_product` msep on (msep.`id` = msp.`marketplace_seller_id_product`)
    		JOIN `"._DB_PREFIX_."marketplace_customer` mkc on (mkc.`marketplace_seller_id` = msep.`id_seller`)
    		JOIN `"._DB_PREFIX_."customer` cus on (mkc.`id_customer` = cus.`id_customer`)
    		JOIN `"._DB_PREFIX_."order_state_lang` ords on (ord.`current_state` = ords.`id_order_state`)
    		WHERE ords.id_lang=".$this->context->language->id." AND cus.`id_customer`=".$this->context->customer->id."
    		ORDER BY ordd.`id_order` DESC LIMIT 5");
    }

    public function getOrderTotalPrice($prev, $next)
    {
    	Db::getInstance()->executeS("SELECT IFNULL(SUM(ordd.`product_price`),0) as total_price,
    		ordd.`product_quantity` as qty,
    		ordd.`id_order` as id_order,
    		ord.`id_customer` as order_by_cus,
    		ord.`payment` as payment_mode,
    		cus.`firstname` as name,
    		ord.`date_add`,ords.`name`as order_status
    		FROM `"._DB_PREFIX_."marketplace_shop_product` msp
    		JOIN `"._DB_PREFIX_."order_detail` ordd on (ordd.`product_id`=msp.`id_product`)
    		JOIN `"._DB_PREFIX_."orders` ord on (ordd.`id_order`=ord.`id_order`)
    		JOIN `"._DB_PREFIX_."marketplace_seller_product` msep on (msep.`id` = msp.`marketplace_seller_id_product`)
    		JOIN `"._DB_PREFIX_."marketplace_customer` mkc on (mkc.`marketplace_seller_id` = msep.`id_seller`)
    		JOIN `"._DB_PREFIX_."customer` cus on (mkc.`id_customer`=cus.`id_customer`)
    		JOIN `"._DB_PREFIX_ ."order_state_lang` ords on (ord.`current_state`=ords.`id_order_state`)
    		WHERE ords.id_lang=".$this->context->language->id." AND cus.`id_customer`=".$this->context->customer->id."
    		AND ord.`date_add` BETWEEN '".$prev."' AND '".$next."'");
    }

    public function getTotalOrderCount($prev, $next)
    {
    	return Db::getInstance()->executeS("SELECT IFNULL(count(ord.`id_order`),0) as total_order
    		FROM `"._DB_PREFIX_."marketplace_shop_product` msp
    		JOIN `"._DB_PREFIX_."order_detail` ordd on (ordd.`product_id`=msp.`id_product`)
    		JOIN `"._DB_PREFIX_."orders` ord on (ordd.`id_order`=ord.`id_order`)
    		JOIN `"._DB_PREFIX_."marketplace_seller_product` msep on (msep.`id` = msp.`marketplace_seller_id_product`)
    		JOIN `"._DB_PREFIX_."marketplace_customer` mkc on (mkc.`marketplace_seller_id` = msep.`id_seller`)
    		JOIN `"._DB_PREFIX_."customer` cus on (mkc.`id_customer`=cus.`id_customer`)
    		JOIN `"._DB_PREFIX_."order_state_lang` ords on (ord.`current_state`=ords.`id_order_state`)
    		WHERE ords.id_lang=".$this->context->language->id." AND cus.`id_customer`=".$this->context->customer->id."
    		AND ord.`date_add` BETWEEN '".$prev."' AND '".$next."'");
    }
}
?>