<?php
class MpcombinationProductAttributeModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();
		$link = new link();
		if (isset($this->context->customer->id))
		{
			$id_customer = $this->context->customer->id;
			$obj_mpcustomer = new MarketplaceCustomer();
			$mp_customer = $obj_mpcustomer->findMarketPlaceCustomer($id_customer);
			if ($is_seller = $mp_customer['is_seller'])
			{
				$shop_id = Tools::getValue('shop');
				$id_lang = $this->context->language->id;
				$att_group = AttributeGroup::getAttributesGroups($id_lang);
				$attrib_set = array();
				foreach ($att_group as $att_group_each)
				{
					$count_value = 0;
					$i = $att_group_each['id_attribute_group'];
					$attrib_set[$i]['name'] = $att_group_each['name'];
					$attrib_set[$i]['id'] = $att_group_each['id_attribute_group'];
					$count_value = count(AttributeGroup::getAttributes($id_lang, $att_group_each['id_attribute_group']));
					$attrib_set[$i]['count_value'] = $count_value;
					if (Mpproductattributecombination::checkCombinationByGroup($id_lang, $att_group_each['id_attribute_group']))
						$attrib_set[$i]['editable'] = 0;
					else
						$attrib_set[$i]['editable'] = $att_group_each['id_attribute_group'];
				}
				ksort($attrib_set);				

				$this->context->smarty->assign("shop", $shop_id);				
				$this->context->smarty->assign('logic', 'mp_prod_attribute');
				$this->context->smarty->assign('is_seller', $is_seller);
				$this->context->smarty->assign('attrib_set', $attrib_set);
				$this->setTemplate('productattribute.tpl');
			}
			else
				Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}
	
	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(array(
			_MODULE_DIR_.'marketplace/views/css/marketplace_account.css',
			_MODULE_DIR_.'mpcombination/views/css/productattribute.css'
		));
		$this->addJS(array(
			_MODULE_DIR_.'mpcombination/views/js/createattribute.js'
		));
	}
}
?>