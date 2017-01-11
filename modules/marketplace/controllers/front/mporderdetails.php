<?php
class MarketplaceMpOrderDetailsModuleFrontController extends ModuleFrontController 
{
	public function initContent()
	{
		parent::initContent();
		$id_lang = $this->context->cookie->id_lang;
		$obj_mp_customer = new MarketplaceCustomer();

		if (isset($this->context->cookie->id_customer))
		{
			$id_customer = $this->context->cookie->id_customer;
			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$mp_customer = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
				if ($mp_customer && $mp_customer['is_seller'] == 1)
				{
					$id_order = Tools::getValue('id_order');
					$id_shop = Tools::getValue('shop');
					$mp_order_details = $this->mpOrderDetails($id_order, $id_lang);
					if ($mp_order_details)
					{
						$order_products = $this->mpOrderProductDetails($id_order, $id_customer);
			           	if ($order_products)
							$this->context->smarty->assign('order_products', $order_products);

						// Order Messages by buyer
						$this->mpCustomerMessages($mp_order_details['buyer_id_customer'], $id_order);

						// get addresses
						$this->mpOrderAddressDetails($id_order);

						// get order status and add shipping tracking number
						$this->mpShippingDetails();
						
						$this->context->smarty->assign('mp_order_details', $mp_order_details);
						$this->context->smarty->assign('id_shop', $id_shop);
						$this->context->smarty->assign('is_seller', $mp_customer['is_seller']);
						$this->context->smarty->assign('logic', 4);
						$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
						$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
						$this->setTemplate('mporderdetails.tpl');
					}
				}
			}
			else
				Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect($this->context->link->getPageLink('my-account'));
	}

	public function mpShippingDetails()
	{
		$shop = Tools::getValue('shop');
		$id_order = Tools::getValue('id_order');
		$is_order_state_updated = Tools::getValue('is_order_state_updated');
		$id_lang = $this->context->language->id;
		$link = new Link();

		$states = OrderState::getOrderStates($id_lang);
		$img_url = _PS_IMG_;

		$order = new Order($id_order);
		$history = $order->getHistory($this->context->language->id);

		foreach ($history as &$order_state)
			$order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white !important' : 'black !important';

		$currentState = $order->getCurrentOrderState();

		$params = array('shop' => $shop, 'id_order' => $id_order);
		$update_url_link = $link->getModuleLink('marketplace', 'updateorderstatusprocess', $params);
		$this->context->smarty->assign(array(
										'update_url_link' => $update_url_link,
										'states' => $states,
										'current_id_lang' => $id_lang,
										'order' => $order,
										'history' => $history,
										'currentState' => $currentState,
										'img_url' => $img_url,
										'is_order_state_updated' => $is_order_state_updated
									));

		$obj_shipping_detail = new MarketplaceShippingInfo();
		$shipping_details = $obj_shipping_detail->getShippingDetailsByOrderId($id_order);
		if ($shipping_details)
		{
			$this->context->smarty->assign('shipping_date', $shipping_details['shipping_date']);
			$this->context->smarty->assign('shipping_description', $shipping_details['shipping_description']);
		}

		$obj_delivery_detail = new MarketplaceDeliveryInfo();
		$delivery_details = $obj_delivery_detail->getDeliveryDetailsByOrderId($id_order);

		if ($delivery_details)
		{
			$this->context->smarty->assign('delivery_date', $delivery_details['delivery_date']);
			$this->context->smarty->assign('received_by', $delivery_details['received_by']);
		}
	}

	public function mpOrderAddressDetails($id_order)
	{
		$id_lang = Context::getContext()->language->id;
		$order = new Order($id_order);
		$customer = new Customer($order->id_customer);
		$addressInvoice = new Address($order->id_address_invoice, $id_lang);
		if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state)
			$invoiceState = new State((int)$addressInvoice->id_state);

		if ($order->id_address_invoice == $order->id_address_delivery)
		{
			$addressDelivery = $addressInvoice;
			if (isset($invoiceState))
				$deliveryState = $invoiceState;
		}
		else
		{
			$addressDelivery = new Address($order->id_address_delivery, $id_lang);
			if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state)
				$deliveryState = new State((int)($addressDelivery->id_state));
		}

		$this->context->smarty->assign(array(
			'customer_addresses' => $customer->getAddresses($id_lang),
			'addresses' => array(
				'delivery' => $addressDelivery,
				'deliveryState' => isset($deliveryState) ? $deliveryState : null,
				'invoice' => $addressInvoice,
				'invoiceState' => isset($invoiceState) ? $invoiceState : null
				)
			));
	}

	public function mpCustomerMessages($id_customer, $id_order)
	{
		$messages = CustomerThread::getCustomerMessages($id_customer, null, $id_order);
		if ($messages)
		{
			foreach ($messages as $key => $msg)
			{
				$obj_customer = new Customer($msg['id_customer']);
				$messages[$key]['firstname'] = $obj_customer->firstname;
				$messages[$key]['lastname'] = $obj_customer->lastname;

				$obj_product = new Product($msg['id_product'], false, $msg['id_lang']);
				$messages[$key]['product_name'] = $obj_product->name;
			}
			$this->context->smarty->assign('messages', array_reverse($messages));
		}
	}

	public function mpOrderDetails($id_order, $id_lang)
	{
		return Db::getInstance()->getRow("SELECT cntry.`name` AS `country`,
			ads.`postcode` AS `postcode`,
			ads.`city` AS `city`,
			ads.`phone` AS `phone`,
			ads.`phone_mobile` AS `mobile`,
			ordd.`id_order_detail` AS `id_order_detail`,
			ordd.`product_name` AS `ordered_product_name`,
			ordd.`product_price` AS total_price,
			ordd.`product_quantity` AS qty,
			ordd.`id_order` AS id_order,
			ord.`id_customer` AS buyer_id_customer,
			ord.`payment` AS payment_mode,
			ord.`current_state` AS current_state,
			cus.`firstname` AS name,
			cus.`lastname` AS lastname,
			ord.`date_add` AS `date`,
			ords.`name`AS order_status,
			ads.`address1` AS `address1`,
			ads.`address2` AS `address2`
			FROM  `"._DB_PREFIX_."order_detail` ordd
			JOIN `"._DB_PREFIX_."orders` ord ON (ord.`id_order` = ordd.`id_order`)
			JOIN `"._DB_PREFIX_."customer` cus ON (cus.`id_customer` = ord.`id_customer`)
			JOIN `"._DB_PREFIX_."order_state_lang` ords ON (ord.`current_state` = ords.`id_order_state`)
			JOIN `"._DB_PREFIX_."address` ads ON (ads.`id_customer`= cus.`id_customer`)
			JOIN `"._DB_PREFIX_."country_lang` cntry ON (cntry.`id_country` = ads.`id_country`)
			WHERE ordd.`id_order`=".$id_order." AND cntry.`id_lang` = ".$id_lang);
	}
	
	// changed as per marketplace commission calc table for preventing order data even if seller delete anyproduct which has ordered by any buyer
	public function mpOrderProductDetails($id_order, $id_customer)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'order_detail` od
			JOIN `'._DB_PREFIX_.'marketplace_commision_calc` mcc ON (mcc.`product_id`= od.`product_id`)
			WHERE od.`id_order` = '.$id_order.' AND mcc.customer_id = '.$id_customer);
	}

	/*
	public function mpOrderProductDetails($id_order, $id_customer)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'order_detail` od
						JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = od.product_id)
						JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
						JOIN `'._DB_PREFIX_.'marketplace_shop_product` msp ON (msp.`id_product`=p.`id_product`)
						JOIN `'._DB_PREFIX_.'marketplace_shop` ms ON (ms.`id`=msp.`id_shop`)
						WHERE od.`id_order` = '.$id_order.' AND ms.id_customer = '.$id_customer);
	}
	*/

	public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(array(
        	_MODULE_DIR_.'marketplace/views/css/marketplace_account.css',
        	_MODULE_DIR_.'marketplace/views/css/mporderdetails_shipping.css'
        	));
        $this->addJS(_MODULE_DIR_.'marketplace/views/js/mporderdetails_shipping.js');
    }
}
?>