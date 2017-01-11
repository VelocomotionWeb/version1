<?php
class MpcombinationViewAttributeGroupValueModuleFrontController extends ModuleFrontController
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
				$id_group = Tools::getValue('id_group');
				$group_attribute = AttributeGroup::getAttributes($this->context->language->id, $id_group);

				$i = 0;
				$value_set = array();
				foreach ($group_attribute as $group_attribute_each)
				{
					$value_set[$i]['id'] = $group_attribute_each['id_attribute'];
					$value_set[$i]['name'] = $group_attribute_each['name'];
					if (Mpproductattribute::ifColorAttributegroup($id_group))
						$value_set[$i]['color'] = $group_attribute_each['color'];

					if(Mpproductattributecombination::checkCombination($group_attribute_each['id_attribute']))
						$value_set[$i]['editable'] = 0;
					else
						$value_set[$i]['editable'] = $group_attribute_each['id_attribute'];

					$i++;
				}

				if (Mpproductattribute::ifColorAttributegroup($id_group))
					$this->context->smarty->assign("is_color", 1);				

				$this->context->smarty->assign("value_set", $value_set);
				$this->context->smarty->assign("shop", $shop_id);				
				$this->context->smarty->assign('logic', 'mp_prod_attribute');
				$this->context->smarty->assign('is_seller', $is_seller);
				$this->context->smarty->assign('id_group', $id_group);
				$this->setTemplate('viewattributegroupvalue.tpl');
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