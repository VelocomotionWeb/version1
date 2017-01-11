<?php
class mpcombinationmpattributemanageModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();
		$link = new Link();
		if (isset($this->context->customer->id))
		{
			$id_customer = $this->context->customer->id;
			$obj_mpcustomer = new MarketplaceCustomer();
			$mp_customer = $obj_mpcustomer->findMarketPlaceCustomer($id_customer);
			if ($is_seller = $mp_customer['is_seller'])
			{
				$this->context->smarty->assign('path_edit_attr', $link->getModuleLink('mpcombination', 'getattributevalue'));

				$attributes = Attribute::getAttributes($this->context->language->id, true);
				$attribute_js = array();
				foreach ($attributes as $attribute)
					$attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];

				$att_group = AttributeGroup::getAttributesGroups($this->context->language->id);

				$mp_product_attr_id = Tools::getValue('mp_product_attr_id');
				if ($mp_product_attr_id) //edit combination
				{				
					$obj_mp_product_attr = new Mpproductattribute($mp_product_attr_id);
					$mp_id_product = $obj_mp_product_attr->mp_id_product;

					$is_in_map = Mpcombinationmap::isInMap($mp_product_attr_id);
					if ($is_in_map)
					{
						$id_product = $is_in_map['main_product_id'];
						$id_product_attribute = $is_in_map['id_ps_product_attribute'];
						$quantity = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);
						
						$product = new Product((int)$id_product);
						$this->context->smarty->assign('id_product', $id_product);

						$images = Image::getImages($this->context->language->id, $product->id);
						$i = 0;
						foreach ($images as $k => $image)
						{
							$images[$k]['obj'] = new Image($image['id_image']);
							++$i;
						}

						$ps_atrribute_images = $obj_mp_product_attr->getPsAttributeImages($id_product_attribute);
						$this->context->smarty->assign('ps_atrribute_images', $ps_atrribute_images);

						if(!$images)
							$this->context->smarty->assign('img_available', 1);

						$this->context->smarty->assign('mp_pro_image', $images);
					}
					else
					{
						$quantity = $obj_mp_product_attr->mp_quantity;
						$this->context->smarty->assign('mp_pro_image', 0);
					}
									
					$i = 0;
					$attribute_ids_set = Mpproductattributecombination::getAttributeId($mp_product_attr_id);
					$attribute_box = array();
					$attribute_box_group_id = array();
					foreach ($attributes as $attributess1)
					{ 	
						foreach ($attribute_ids_set as $attribute_ids_set1)
						{
							if ($attributess1['id_attribute'] == $attribute_ids_set1['id_ps_attribute'])
							{
								$attribute_box[$i]['groupid'] = $attributess1['id_attribute_group'];
								$attribute_box[$i]['id'] = $attributess1['id_attribute'];
								$attribute_box[$i]['name'] = $attributess1['attribute_group'].' : '.$attributess1['name'];
								$attribute_box_group_id[$i] = $attributess1['id_attribute_group'];
								$i++;
							}
						}
					}								

					$attribute_box_group_id_json = Tools::jsonEncode($attribute_box_group_id);				
					$this->context->smarty->assign('attribute_box_group_id_json', $attribute_box_group_id_json);
					$this->context->smarty->assign('attribute_box', $attribute_box);
					$this->context->smarty->assign('mp_id_product_attribute', $obj_mp_product_attr->mp_id_product_attribute);
					$this->context->smarty->assign('att_group', $att_group);				
					$this->context->smarty->assign('mp_reference', $obj_mp_product_attr->mp_reference);
					$this->context->smarty->assign('mp_ean13', $obj_mp_product_attr->mp_ean13);
					$this->context->smarty->assign('mp_upc', $obj_mp_product_attr->mp_upc);
					$this->context->smarty->assign('mp_wholesale_price', $obj_mp_product_attr->mp_wholesale_price);
					$this->context->smarty->assign('mp_price', $obj_mp_product_attr->mp_price);
					$this->context->smarty->assign('mp_weight', $obj_mp_product_attr->mp_weight);
					$this->context->smarty->assign('mp_unit_price_impact', $obj_mp_product_attr->mp_unit_price_impact);
					$this->context->smarty->assign('mp_minimal_quantity', $obj_mp_product_attr->mp_minimal_quantity);
					$this->context->smarty->assign('mp_available_date', $obj_mp_product_attr->mp_available_date);				
					$this->context->smarty->assign('mp_id_product', $mp_id_product);
					//$this->context->smarty->assign('obj_mp_product_attr', $obj_mp_product_attr);
					$this->context->smarty->assign('quantity', $quantity);

					$after_save_reload_url = $link->getModuleLink('marketplace', 'productupdate', array('id'=>$mp_id_product, 'editproduct'=>1));
					$this->context->smarty->assign('after_save_reload_url', $after_save_reload_url);

					$this->context->smarty->assign('edit', 1);
					$this->context->smarty->assign('is_seller',$is_seller);
					$this->context->smarty->assign('logic','edit_comb');
					$this->setTemplate('mpattributeedit.tpl');
				}
				else //add combination
				{
					$mp_product_id = Tools::getValue('mp_product_id');

					
					$attr_error = Tools::getValue('attr_error');
					if ($attr_error)
						$this->context->smarty->assign('attr_error', $attr_error);

					/*$obj_seller_prod_det = new SellerProductDetail();
					$mp_prod_det = $obj_seller_prod_det->getMarketPlaceShopProductDetail($mp_product_id);
					$id_shop = $mp_prod_det['id_shop'];
					$mp_seller_id = $obj_seller_prod_det->getSellerIdByProduct($mp_product_id);
					$customer_id = $obj_seller_prod_det->getCustomerIdBySellerId($mp_seller_id);*/			
					
					// image section START				
					$obj_mp_shop_prod = new MarketplaceShopProduct();
					$ps_prod_id = $obj_mp_shop_prod->findMainProductIdByMppId($mp_product_id);
					if ($ps_prod_id)
					{
						$product = new Product((int)$ps_prod_id['id_product']);
						$images = Image::getImages($this->context->language->id, $product->id);
						$i = 0;
						foreach ($images as $k => $image)
						{
							$images[$k]['obj'] = new Image($image['id_image']);
							++$i;
						}
						
						$this->context->smarty->assign('mp_pro_image', $images);
						if(!$images)
							$this->context->smarty->assign('img_available', 1);
					}
					else
						$this->context->smarty->assign('mp_pro_image', 0);

					// image section END

					$this->context->smarty->assign('mp_product_id', $mp_product_id);				
					$this->context->smarty->assign('att_group', $att_group);
					$this->context->smarty->assign('mp_price', 0);
					$this->context->smarty->assign('mp_weight', 0);
					$this->context->smarty->assign('mp_unit_price_impact', 0);
					$this->context->smarty->assign("is_seller",$is_seller);
					$this->context->smarty->assign("logic",'create_comb');				
					$this->setTemplate('mpattributeadd.tpl');
				}
			}
			else
				Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}

	public function postProcess()
	{
		if (Tools::isSubmit('range_submit'))
		{
			$link = new Link();
			$mp_reference = Tools::getValue('mp_reference');
			$mp_ean13 = Tools::getValue('mp_ean13');
			$mp_id_product = (int)Tools::getValue('mp_id_product');
			$product_att_list = Tools::getValue('attribute_combination_list');
			$mp_quantity = Tools::getValue('mp_quantity');
			$mp_upc = Tools::getValue('mp_upc');
			$mp_wholesale_price = Tools::getValue('mp_wholesale_price');
			$attribute_price_impact = Tools::getValue('attribute_price_impact');
			$attribute_weight_impact = Tools::getValue('attribute_weight_impact');
			$attribute_weight = Tools::getValue('attribute_weight');
			$mp_price = Tools::getValue('mp_price');
			$attribute_unit_impact = Tools::getValue('attribute_unit_impact');
			$attribute_unity = Tools::getValue('attribute_unity');
			$attribute_minimal_quantity = Tools::getValue('attribute_minimal_quantity');
			$available_date_attribute = Tools::getValue('available_date_attribute');
			$id_images = Tools::getValue('id_image_attr');			

			if (!$product_att_list)
				$this->errors[] = Tools::displayError('Combination attribute cannot be blank.');
			else if (!Validate::isUnsignedId($mp_quantity)) 
				$this->errors[] = Tools::displayError('Quantity should be integer.');
			else if ($mp_ean13 != '')
			{
				if (!Validate::isEan13($mp_ean13))
					$this->errors[] = Tools::displayError('Field EAN13 is not valid.');
			}
			else if ($mp_upc != '')
			{
				if (!Validate::isUpc($mp_upc))
					$this->errors[] = Tools::displayError('Field UPC is not valid.');
			}
			else if (!Validate::isPrice($mp_wholesale_price))
				$this->errors[] = Tools::displayError('Wholesale price must be numeric');

			if ($attribute_price_impact != 0)
			{
				if ($mp_price == '')
					$mp_price = 0;
				if (!Validate::isPrice($mp_price))
				{
					$this->errors[] = Tools::displayError('Impact price must be numeric.');
				}
			}
			else 
				$mp_price = 0;
				
			if ($attribute_weight_impact != 0)
			{
				if ($attribute_weight == '')
					$attribute_weight = 0;
				if (!Validate::isFloat($attribute_weight))
					$this->errors[] = Tools::displayError('Impact on weight must be numeric.');
			}
			else
				$attribute_weight = 0.00;
			
			if ($attribute_unit_impact != 0)
			{
				if ($attribute_unity == '')
					$attribute_unity = 0;
				if (!Validate::isPrice($attribute_unity))
					$this->errors[] = Tools::displayError('Impact on unit price must be numeric.');
			} 
			else 
				$attribute_unity = 0;					
			
			if (!Validate::isUnsignedId($attribute_minimal_quantity))
				$this->errors[] = Tools::displayError('Minimum quantity  should be integer and greater than 0.');

			if (!Validate::isDateFormat($available_date_attribute)) 
				$this->errors[] = Tools::displayError('Must be valid date format.');

			if ($attribute_price_impact < 0)
				$mp_price = -$mp_price;
						
			if ($attribute_weight_impact < 0)
				$attribute_weight = -$attribute_weight;
			
			if ($attribute_unit_impact < 0)
				$attribute_unity = -$attribute_unity;

			$mp_id_product_attribute = Tools::getValue('mp_product_attr_id');
			if ($product_att_list)
			{
				if (Mpproductattributecombination::isProductCombinationExists($mp_id_product, $product_att_list, $mp_id_product_attribute))		
					$this->errors[] = Tools::displayError('This Combination is already exists for this product.');
			}

			if (!count($this->errors))
			{
				$obj_seller_product = new SellerProductDetail($mp_id_product);
				$mp_shop_product = $obj_seller_product->getMarketPlaceShopProductDetailBYmspid($mp_id_product);
				$mp_id_shop = $obj_seller_product->id_shop;

				if ($mp_id_product_attribute) //edit combination
				{
					$edit_combi = 1;
					$obj_mp_pro_attr = new Mpproductattribute($mp_id_product_attribute);					
				}
				else
				{
					$edit_combi = 0;
					$obj_mp_pro_attr = new Mpproductattribute();
				}

				$product_has_combi = Mpproductattribute::getProductAttributesIds($mp_id_product);
				if (!$product_has_combi)
					$obj_mp_pro_attr->mp_default_on = 1;
				$obj_mp_pro_attr->mp_id_product = $mp_id_product;
				$obj_mp_pro_attr->mp_reference = $mp_reference;
				$obj_mp_pro_attr->mp_ean13 = $mp_ean13;
				$obj_mp_pro_attr->mp_quantity = $mp_quantity;
				$obj_mp_pro_attr->mp_upc = $mp_upc;
				$obj_mp_pro_attr->mp_wholesale_price = $mp_wholesale_price;
				$obj_mp_pro_attr->mp_price = $mp_price;
				$obj_mp_pro_attr->mp_minimal_quantity = $attribute_minimal_quantity;
				$obj_mp_pro_attr->mp_weight = $attribute_weight;
				$obj_mp_pro_attr->mp_unit_price_impact = $attribute_unity;
				$obj_mp_pro_attr->mp_available_date = $available_date_attribute;
				$obj_mp_pro_attr->save();
				$attribute_list = array();

				if ($mp_id_product_attribute)
				{					
					$mp_ecotax = $obj_mp_pro_attr->mp_ecotax;

					Db::getInstance()->delete('mp_product_attribute_combination', '`mp_id_product_attribute` = '.(int)$mp_id_product_attribute);

					foreach ($product_att_list as $group)
					{
						$attribute_list[] = array(
							'mp_id_product_attribute' => (int)$mp_id_product_attribute,
							'id_ps_attribute' => (int)$group
						);
					}
					Db::getInstance()->insert('mp_product_attribute_combination', $attribute_list);

					if (!$product_has_combi)
						Mpproductattributeshop::updateValue($mp_id_product_attribute, $mp_wholesale_price, $mp_price,$attribute_weight, $attribute_unity, $attribute_minimal_quantity, $available_date_attribute,1);
					else
						Mpproductattributeshop::updateValue($mp_id_product_attribute, $mp_wholesale_price, $mp_price,$attribute_weight, $attribute_unity, $attribute_minimal_quantity, $available_date_attribute);
				}
				else
				{
					$mp_id_product_attribute = $obj_mp_pro_attr->id;
					$mp_ecotax = $obj_mp_pro_attr->mp_ecotax;
					foreach ($product_att_list as $group)
					{
						$attribute_list[] = array(
							'mp_id_product_attribute' => (int)$mp_id_product_attribute,
							'id_ps_attribute' => (int)$group
						);
					}

					Db::getInstance()->insert('mp_product_attribute_combination', $attribute_list);

					if (!$product_has_combi)
						Mpproductattributeshop::insertProductAttributeShop($mp_id_product_attribute, $mp_id_shop, $mp_wholesale_price, $mp_price,$mp_ecotax, $attribute_weight, $attribute_unity, $attribute_minimal_quantity, $available_date_attribute, 1);
					else
						Mpproductattributeshop::insertProductAttributeShop($mp_id_product_attribute, $mp_id_shop, $mp_wholesale_price, $mp_price,$mp_ecotax, $attribute_weight, $attribute_unity, $attribute_minimal_quantity, $available_date_attribute);
				}

				//Set Mp combination mp images
				$obj_mp_pro_attr->setMpImages($id_images, $mp_id_product_attribute);
				
				if ($mp_shop_product)
				{					
					$is_in_map = Mpcombinationmap::isInMap($mp_id_product_attribute);					
					$id_ps_product_attribute = $is_in_map['id_ps_product_attribute'];
					if ($id_ps_product_attribute)
						$obj_comb = new Combination($id_ps_product_attribute);
					else
						$obj_comb = new Combination();
					
					$main_product_id = $mp_shop_product['id_product'];
					$obj_comb->id_product = $main_product_id;
					$obj_comb->reference = $mp_reference;
					$obj_comb->ean13 = $mp_ean13;
					$obj_comb->upc = $mp_upc;
					$obj_comb->wholesale_price = $mp_wholesale_price;
					$obj_comb->price = $mp_price;
					$obj_comb->weight = $attribute_weight;
					$obj_comb->unit_price_impact = $attribute_unity;
					$obj_comb->minimal_quantity = $attribute_minimal_quantity;
					$obj_comb->available_date = $available_date_attribute;

					$ps_product_has_combi = Mpproductattribute::getPsProductDefaultAttributesIds($main_product_id);
					if (!$ps_product_has_combi)
						$obj_comb->default_on = 1;

					$obj_comb->save();

					if($edit_combi)
					{
						//if admin delete combination from catalog then another combination will automatially created when seller update the combination of product.
						
						Db::getInstance()->delete('product_attribute_combination', '`id_product_attribute` = '.(int)$id_ps_product_attribute);
						Db::getInstance()->delete('mp_combination_map', '`id_ps_product_attribute` = '.(int)$id_ps_product_attribute);

						$id_ps_product_attribute = $obj_comb->id;

						foreach ($product_att_list as $group)
							$obj_mp_pro_attr->insertIntoPsProductCombination($group, $id_ps_product_attribute);
					
						Mpcombinationmap::insertIntoMpCombinationMap($id_ps_product_attribute, $mp_id_product_attribute, $mp_id_product, $main_product_id);						
					}
					else
					{
						$id_ps_product_attribute = $obj_comb->id;
						foreach ($product_att_list as $group)
							$obj_mp_pro_attr->insertIntoPsProductCombination($group, $id_ps_product_attribute);

						Mpcombinationmap::insertIntoMpCombinationMap($id_ps_product_attribute, $mp_id_product_attribute, $mp_id_product, $main_product_id);
					}
					
					Mpstockavailable::setQuantity($mp_id_product, $mp_id_product_attribute, $mp_quantity);
					StockAvailable::setQuantity($main_product_id, $id_ps_product_attribute, $mp_quantity);

					//mp combination ps Images
					$obj_comb->setImages($id_images);
				}

				Tools::redirect($link->getModuleLink('marketplace', 'productupdate', array('flag' => 1,
																				'id' => $mp_id_product,
																				'edited_conf' => 1)));
			}
		}
	}

	public function setMedia()
	{
		parent::setMedia();

		$this->addCSS(array(
			_MODULE_DIR_.'mpcombination/views/css/mp_combination.css',
			_MODULE_DIR_.'marketplace/views/css/marketplace_account.css'
			));

		$this->addJS(_MODULE_DIR_.'mpcombination/views/js/attributesBack.js');
		$this->addJS(_MODULE_DIR_.'mpcombination/views/js/mpattribute_impact.js');

		//datepicker
		$this->addJS(array(
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui.js',
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-sliderAccess.js',
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.js',
				));
		$this->addCSS(_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.css');
	}			
}
?>