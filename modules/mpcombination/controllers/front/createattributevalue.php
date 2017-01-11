<?php
class MpcombinationCreateAttributeValueModuleFrontController extends ModuleFrontController
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
				if ($id_group)
					$this->context->smarty->assign('id_group', $id_group);

				$id_attribute = Tools::getValue('id_attribute');

				// Delete Attribute group
				if(Tools::getValue('delete_attr_value'))
				{
					if ($id_attribute != 0)
					{
						if (!Mpproductattributecombination::checkCombinationByattribute($id_attribute))
						{
							$obj_attribute = new Attribute($id_attribute);
							$obj_attribute->delete();				
							
							Tools::redirect($link->getModuleLink('mpcombination','viewattributegroupvalue',array('shop'=>$shop_id, 'id_group'=>$id_group, 'attr_value_delete_success'=>1)));
						}
						else
							Tools::redirect($link->getModuleLink('mpcombination', 'viewattributegroupvalue', array('shop'=>$shop_id, 'id_group'=>$id_group, 'error_attr'=>1)));
					}
					else
						Tools::redirect($link->getModuleLink('mpcombination', 'viewattributegroupvalue', array('shop'=>$shop_id, 'id_group'=>$id_group, 'error_attr'=>1)));
				}

				if ($id_attribute === '0') //if Attribute value is already in use
				{									
					$param = array('shop'=>$shop_id, 'id_group'=>Tools::getValue('id_group'), 'error_attr'=>1);			
					Tools::redirect($link->getModuleLink('mpcombination', 'viewattributegroupvalue', $param));
				}
				else if ($id_attribute)
				{					
					$att_group = AttributeGroup::getAttributesGroups($this->context->language->id);
					$attrib_grp = array();
					foreach ($att_group as $att_group_each)
					{
						if ($att_group_each['id_attribute_group'] == $id_group)
						{
							$attrib_grp['name'] = $att_group_each['name'];
							$attrib_grp['id'] = $att_group_each['id_attribute_group'];
						}	
					}
					$this->context->smarty->assign("attrib_grp", $attrib_grp);
					
					$group_attribute_set = AttributeGroup::getAttributes($this->context->language->id, $id_group);
					$attrib_valname = "";
					foreach ($group_attribute_set as $group_attribute_set_each)
					{
						if ($group_attribute_set_each['id_attribute'] == $id_attribute)
						{
							$attrib_valname = $group_attribute_set_each['name'];
							$attrib_color = $group_attribute_set_each['color'];
						}						
					}					
					$this->context->smarty->assign("attrib_valname", $attrib_valname);

					if (Mpproductattribute::ifColorAttributegroup($id_group))
						$this->context->smarty->assign("attrib_color", $attrib_color);

					
					$this->context->smarty->assign("id_attribute", $id_attribute);
					$this->context->smarty->assign("id_group", $id_group);					
				}
				else
				{								
					$grouptypecolor_link = $link->getModuleLink('mpcombination', 'grouptypecolor');				
					$att_group = AttributeGroup::getAttributesGroups($this->context->language->id);
					$attrib_set = "";
					foreach ($att_group as $att_group_each)
					{
						$i = $att_group_each['id_attribute_group'];
						$attrib_set[$i]['name'] = $att_group_each['name'];
						$attrib_set[$i]['id'] = $att_group_each['id_attribute_group'];
					}
					ksort($attrib_set);
					$this->context->smarty->assign('grouptypecolor_link', $grouptypecolor_link);
					$this->context->smarty->assign('attrib_set', $attrib_set);
				}

				$this->context->smarty->assign("shop", $shop_id);				
				$this->context->smarty->assign('logic', 'mp_prod_attribute');
				$this->context->smarty->assign('is_seller', $is_seller);
				$this->setTemplate('createattributevalue.tpl');
				
			}
			else
				Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}

	public function postProcess()
	{
		$link = new Link();
		$id_group = Tools::getValue('id_group');
		$shop_id = Tools::getValue('shop');
		$attrib_group = Tools::getValue('attrib_group');
		$attrib_value = Tools::getValue('attrib_value');

		if (Tools::isSubmit('attrib_value_add')) // add attribute value
		{
			$is_color = 0;
			if (!$attrib_value)
				$this->errors[] = Tools::displayError('Attribute Value is required');
			else if (!Validate::isGenericName($attrib_value))
				$this->errors[] = Tools::displayError('Attribute Value is invalid.');
			else if (Mpproductattribute::ifColorAttributegroup($attrib_group))
			{
				$is_color = 1;
				$attrib_value_color = Tools::getValue('attrib_value_color');
				if (!$attrib_value_color)
					$this->errors[] = Tools::displayError('Problem occured while adding data.');
			}

			if (!count($this->errors))
			{
				$obj_attribute = new Attribute();
				$obj_attribute->id_attribute_group = $attrib_group;
				foreach (Language::getLanguages(true) as $lang)
					$obj_attribute->name[$lang['id_lang']] = $attrib_value;

				if ($is_color)
					$obj_attribute->color = $attrib_value_color;

				$obj_attribute->add();
				Tools::redirect($link->getModuleLink('mpcombination', 'productattribute', array('shop' => $shop_id, 'attr_value_add_success' => 1)));	
			}
		}
		else if (Tools::isSubmit('attrib_value_update')) // edit attribute value
		{
			$id_attribute = Tools::getValue('id_attribute');
			$is_color = 0;
			if (!$attrib_value)
				$this->errors[] = Tools::displayError('Attribute Value is required');
			else if (!Validate::isGenericName($attrib_value))
				$this->errors[] = Tools::displayError('Attribute Value is invalid.');
			else if (Mpproductattribute::ifColorAttributegroup($attrib_group))
			{
				$is_color = 1;
				$attrib_value_color = Tools::getValue('attrib_value_color');
				if (!$attrib_value_color)
					$this->errors[] = Tools::displayError('Problem occured while adding data.');
			}

			if (!count($this->errors))
			{
				if (!Mpproductattributecombination::checkCombinationByattribute($id_attribute))
				{
					$obj_attribute = new Attribute($id_attribute);
					$obj_attribute->id_attribute_group = $attrib_group;
					foreach (Language::getLanguages(true) as $lang)
						$obj_attribute->name[$lang['id_lang']] = $attrib_value;
					if ($is_color)
						$obj_attribute->color = $attrib_value_color;
					
					$obj_attribute->update();					
					
					Tools::redirect($link->getModuleLink('mpcombination', 'viewattributegroupvalue', array('shop'=>$shop_id, 'id_group'=>$id_group, 'attr_value_update_success'=>1)));
				}
				else
					Tools::redirect($link->getModuleLink('mpcombination', 'viewattributegroupvalue', array('shop'=>$shop_id, 'id_group'=>$id_group, 'error_attr'=>1)));			
			}			
		}
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