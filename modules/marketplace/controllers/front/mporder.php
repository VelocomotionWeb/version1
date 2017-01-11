<?php
class MarketplaceMpOrderModuleFrontController extends ModuleFrontController 
{
	public function initContent() 
	{
		parent::initContent();
		$link = new Link();
		$id_lang = $this->context->cookie->id_lang;

		if (isset($this->context->cookie->id_customer))
		{
			$id_customer = $this->context->cookie->id_customer;
			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$obj_mp_customer = new MarketplaceCustomer();
				$obj_mp_shop = new MarketplaceShop();

				$mp_customer = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
				if ($mp_customer && $mp_customer['is_seller'] == 1)
				{
					$mp_shop = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
					if ($mp_shop)
					{
						$mporders = $this->mpSellerOrders($id_lang, $id_customer);
						if ($mporders)
						{
							foreach ($mporders as $key => $order)
							{
								$obj_buyer = new Customer($order['buyer_id_customer']);
								$mporders[$key]['buyer_info'] = $obj_buyer;
							}
						}

						$order_detail_link = $link->getModuleLink('marketplace', 'mporderdetails', array('shop' => $mp_shop['id']));
						$this->context->smarty->assign('order_detail_link', $order_detail_link);
						$this->context->smarty->assign('id_customer', $id_customer);
						$this->context->smarty->assign('id_shop', $mp_shop['id']);
						$this->context->smarty->assign('mporders', $mporders);
						$this->context->smarty->assign('is_seller', $mp_customer['is_seller']);
						$this->context->smarty->assign('logic', 4);
						$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
						$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
						$this->setTemplate('mporder.tpl');
					}
				}
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('marketplace', 'mporder')));
	}
	
	// changed as per marketplace commission calc table for preventing order data even if seller delete anyproduct which has ordered by any buyer
	public function mpSellerOrders($id_lang, $id_customer)
	{
		return Db::getInstance()->executeS("SELECT ordd.`id_order_detail`AS `id_order_detail`,
			ordd.`product_name` AS `ordered_product_name`,
			ordd.`product_price` AS product_price,
			ordd.`product_quantity` AS qty,
			ordd.`id_order` AS id_order,
			ord.`id_customer` AS buyer_id_customer,
			ord.`total_paid` AS total_paid,
			ord.`payment` AS payment_mode,
			ord.`reference` AS reference,
			cus.`firstname` AS seller_firstname,
			cus.`lastname` AS seller_lastname,
			cus.`email` AS seller_email,
			ord.`date_add`,ords.`name` AS order_status,
			ord.`id_currency` AS `id_currency`
			FROM `"._DB_PREFIX_."marketplace_commision_calc` mcc
			JOIN `"._DB_PREFIX_."order_detail` ordd ON (ordd.`product_id` = mcc.`product_id`)
			JOIN `"._DB_PREFIX_."orders` ord ON (ordd.`id_order` = ord.`id_order`)
			JOIN `"._DB_PREFIX_."marketplace_customer` mkc ON (mkc.`id_customer` = mcc.`customer_id`)
			JOIN `"._DB_PREFIX_."customer` cus ON (mkc.`id_customer` = cus.`id_customer`)
			JOIN `"._DB_PREFIX_."order_state_lang` ords ON (ord.`current_state` = ords.`id_order_state`)
			WHERE ords.id_lang = ".$id_lang." AND cus.`id_customer` = ".$id_customer."
			GROUP BY ordd.`id_order` ORDER BY ordd.`id_order` DESC");
	}

	/*
	public function mpSellerOrders($id_lang, $id_customer)
	{
		return Db::getInstance()->executeS("SELECT ordd.`id_order_detail`AS `id_order_detail`,
			ordd.`product_name` AS `ordered_product_name`,
			ordd.`product_price` AS product_price,
			ordd.`product_quantity` AS qty,
			ordd.`id_order` AS id_order,
			ord.`id_customer` AS buyer_id_customer,
			ord.`total_paid` AS total_paid,
			ord.`payment` AS payment_mode,
			ord.reference AS reference,
			cus.`firstname` AS seller_firstname,
			cus.`lastname` AS seller_lastname,
			cus.`email` AS seller_email,
			ord.`date_add`,ords.`name` AS order_status,
			ord.`id_currency` AS `id_currency`
			FROM `"._DB_PREFIX_."marketplace_shop_product` msp
			JOIN `"._DB_PREFIX_."order_detail` ordd ON (ordd.`product_id` = msp.`id_product`)
			JOIN `"._DB_PREFIX_."orders` ord ON (ordd.`id_order` = ord.`id_order`)
			JOIN `"._DB_PREFIX_."marketplace_seller_product` msep ON (msep.`id` = msp.`marketplace_seller_id_product`)
			JOIN `"._DB_PREFIX_."marketplace_customer` mkc ON (mkc.`marketplace_seller_id` = msep.`id_seller`)
			JOIN `"._DB_PREFIX_."customer` cus ON (mkc.`id_customer` = cus.`id_customer`)
			JOIN `"._DB_PREFIX_."order_state_lang` ords ON (ord.`current_state` = ords.`id_order_state`)
			WHERE ords.id_lang = ".$id_lang." AND cus.`id_customer` = ".$id_customer."
			GROUP BY ordd.`id_order` ORDER BY ordd.`id_order` DESC");
	}*/
	
	public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/imageedit.js');
		//data table file included
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/datatable_bootstrap.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/jquery.dataTables.min.js');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/dataTables.bootstrap.js');
    }
}
?>