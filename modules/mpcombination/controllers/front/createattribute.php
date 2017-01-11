<?php
class MpcombinationCreateAttributeModuleFrontController extends ModuleFrontController
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

				// Delete Attribute group
				if(Tools::getValue('delete_attr'))
				{
					if ($id_group != 0)
					{
						if (!(Mpproductattributecombination::checkCombinationByGroup($this->context->language->id, $id_group)))
						{
							$obj_attr_group = new AttributeGroup($id_group);
							$obj_attr_group->delete();
							Tools::redirect($link->getModuleLink('mpcombination', 'productattribute',array('shop'=>$shop_id, 'attr_delete_success'=>1)));
						}
						else
							Tools::redirect($link->getModuleLink('mpcombination', 'productattribute',array('shop'=>$shop_id, 'error_attr'=>1)));
					}
					else
						Tools::redirect($link->getModuleLink('mpcombination', 'productattribute',array('shop'=>$shop_id, 'error_attr'=>1)));
				}

				if ($id_group === '0') //if Attribute group is already in use
				{
					Tools::redirect($link->getModuleLink('mpcombination', 'productattribute',array('shop'=>$shop_id, 'error_attr'=>1)));
				}
				else if ($id_group) // edit attribute
				{	
					$group_details = $this->getAttributeGroupDetails($id_group);
					$name = "";
					$public_name = "";
					if ($group_details)
					{
						$name = $group_details['name'];
						$public_name = $group_details['public_name'];
						$group_type = $group_details['group_type'];
					}					

					$this->context->smarty->assign("id_group", $id_group);
					$this->context->smarty->assign('name', $name);
					$this->context->smarty->assign('public_name', $public_name);
					$this->context->smarty->assign('group_type', $group_type);					
				}
								
				$this->context->smarty->assign("shop", $shop_id);				
				$this->context->smarty->assign('logic', 'mp_prod_attribute');
				$this->context->smarty->assign('is_seller', $is_seller);

				$this->setTemplate('createattribute.tpl');					
			}
			else
				Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}

	public function postProcess()
	{
		$link = new link();
		$shop_id = Tools::getValue('shop');		
		$attrib_name = Tools::getValue('attrib_name');
		$attrib_public_name = Tools::getValue('attrib_public_name');
		$attrib_type = Tools::getValue('attrib_type');		
					
		if (Tools::isSubmit('attrib_add')) // add attribute
		{
			if (!$attrib_name)
				$this->errors[] = Tools::displayError('Attribute Name is required');
			else if (!Validate::isGenericName($attrib_name))
				$this->errors[] = Tools::displayError('Attribute Name is invalid.');

			if (!$attrib_public_name)
				$this->errors[] = Tools::displayError('Public Name is required.');
			else if (!Validate::isGenericName($attrib_public_name))
				$this->errors[] = Tools::displayError('Public Name is invalid.');

			if (!count($this->errors))
			{
				$is_color = 0;
				$obj_attr_group = new AttributeGroup();
				foreach (Language::getLanguages(true) as $lang)
				{
					$obj_attr_group->name[$lang['id_lang']] = $attrib_name;
					$obj_attr_group->public_name[$lang['id_lang']] = $attrib_public_name;
				}
				
				$obj_attr_group->group_type = $attrib_type;
				if ($attrib_type == 'color')
					$is_color = 1;
				$obj_attr_group->is_color_group = $is_color;
				$obj_attr_group->add();
				$id = $obj_attr_group->id;
				// To update Table layered_indexable_attribute_group
				$this->setIndexableValue(array('id_attribute_group'=>$id,'indexable'=>1));
				Tools::redirect($link->getModuleLink('mpcombination', 'productattribute', array('shop' => $shop_id, 'attr_add_success' => 1)));
			}
		}		
		else if (Tools::isSubmit('attrib_update')) // edit attribute
		{
			$id_group = Tools::getValue('id_group');

			if (!$attrib_name)
				$this->errors[] = Tools::displayError('Attribute Name is required');
			else if (!Validate::isGenericName($attrib_name))
				$this->errors[] = Tools::displayError('Attribute Name is invalid.');

			if (!$attrib_public_name)
				$this->errors[] = Tools::displayError('Public Name is required.');
			else if (!Validate::isGenericName($attrib_public_name))
				$this->errors[] = Tools::displayError('Public Name is invalid.');

			if (!count($this->errors))
			{		
				if (!Mpproductattributecombination::checkCombinationByGroup($this->context->language->id, $id_group))
				{
					$is_color = 0;
					$obj_attr_group = new AttributeGroup($id_group);
					foreach (Language::getLanguages(true) as $lang)
					{
						$obj_attr_group->name[$lang['id_lang']] = $attrib_name;
						$obj_attr_group->public_name[$lang['id_lang']] = $attrib_public_name;
					}
					$obj_attr_group->group_type = $attrib_type;
					if ($attrib_type == 'color')
						$is_color = 1;

					$obj_attr_group->color = $is_color;
					$obj_attr_group->update();
					
					Tools::redirect($link->getModuleLink('mpcombination','productattribute',array('shop'=>$shop_id, 'attr_update_success'=>1)));
				}
				else
					Tools::redirect($link->getModuleLink('mpcombination', 'productattribute',array('shop'=>$shop_id, 'error_attr'=>1)));
			}				
		}		
	}

	private function setIndexableValue($data)
	{
		Db::getInstance()->insert('layered_indexable_attribute_group',$data);
	}

	private function getAttributeGroupDetails($group_id)
	{
		$res = array();
		$all_group = AttributeGroup::getAttributesGroups($this->context->language->id);
		foreach($all_group as $each_group)
		{
			if ($each_group['id_attribute_group'] == $group_id)
			{
				$res['name'] = $each_group['name'];
				$res['public_name'] = $each_group['public_name'];	
				$res['group_type'] = $each_group['group_type'];	
			}
		}
		if (count($res) > 1)
			return $res;
		return 0;
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