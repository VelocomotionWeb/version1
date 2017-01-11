<?php
class mpcombinationgeneratecombinationModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();
		$link = new Link();
		if ($this->context->customer->id)
		{			
			$id_customer = $this->context->customer->id;
			$obj_mpcustomer = new MarketplaceCustomer();
			$mp_customer = $obj_mpcustomer->findMarketPlaceCustomer($id_customer);
			if ($is_seller = $mp_customer['is_seller'])
			{
				$message = Tools::getValue('msg');
				if ($message)
					$this->context->smarty->assign('message', $message);
				else
					$this->context->smarty->assign('message', 0);
								
				$mp_product_id = Tools::getValue('mp_product_id');
				$mp_attirbute_com = Configuration::get('MP_ATTRIBUTE_COMBINATION');
				if ($mp_attirbute_com == 1)
					Hook::exec('actionAttibuteDisplayBySeller', array('mp_product_id' => $mp_product_id));
				else if ($mp_attirbute_com == 2)
					Hook::exec('actionAttibuteDisplayByBoth', array('mp_product_id' => $mp_product_id));
				else 
				{
					//only by admin
					$attribute_array = array();
					$attribute_deatil = AttributeGroup::getAttributesGroups($this->context->language->id);
					foreach ($attribute_deatil as $attr_de) 
					{
						$name = $attr_de['name'];
						$id_attribute_group = $attr_de['id_attribute_group'];
						$attribute_value_info = AttributeGroup::getAttributes($this->context->language->id, $id_attribute_group);
						$attribute_array[] = array('attibute_group_name'=>$name, 'id_attribute_group'=>$id_attribute_group, 'attribute_value'=>$attribute_value_info);
					}					
				}				
				$js_attributes = $this->displayAndReturnAttributeJs();
				$this->context->smarty->assign('attribute_js', $js_attributes);

				$attribute_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
				$this->context->smarty->assign('attribute_groups', $attribute_groups);
				$this->context->smarty->assign('currency_sign', $this->context->currency->sign);
				$this->context->smarty->assign('weight_unit', Configuration::get('PS_WEIGHT_UNIT'));				
								
				//$this->context->smarty->assign('my_account_link', $link->getPageLink('my-account'));
				$this->context->smarty->assign('tax_rates', 0);				
				$this->context->smarty->assign('mp_product_id', $mp_product_id);
				$this->context->smarty->assign('attribute_array', $attribute_array);				
				$this->context->smarty->assign('mp_attirbute_com', $mp_attirbute_com);	

				$combinations_groups = $this->groupTable($this->context->language->id, $this->context->shop->id, $mp_product_id);				
				$attributes = array();
				$obj_mpattr = new Mpproductattribute();
				$impacts = $obj_mpattr->getAttributesImpacts($mp_product_id);
				foreach ($combinations_groups as &$combination)
				{
					$target = &$attributes[$combination['id_attribute_group']][$combination['id_attribute']];
					$target = $combination;				
					if (isset($impacts[$combination['id_attribute']]))
					{
						$target['price'] = $impacts[$combination['id_attribute']]['price'];
						$target['weight'] = $impacts[$combination['id_attribute']]['weight'];
					}
				}
				
				$this->context->smarty->assign('attributes', $attributes);
				$this->context->smarty->assign('is_seller', $is_seller);
				$this->setTemplate('generatecombination.tpl');
			}
			else
				Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		} 
		else 
			Tools::redirect($link->getPageLink('my-account'));
	}	

	protected static function displayAndReturnAttributeJs()
	{
		$attributes = Attribute::getAttributes(Context::getContext()->language->id, true);
		$attribute_js = array();
		foreach ($attributes as $attribute)
			$attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
		echo '
		<script type="text/javascript">
			var attrs = new Array();
			attrs[0] = new Array(0, \'---\');';
		foreach ($attribute_js as $idgrp => $group)
		{
			echo '
				attrs['.$idgrp.'] = new Array(0, \'---\' ';
			foreach ($group as $idattr => $attrname)
				echo ', '.$idattr.', \''.addslashes(($attrname)).'\'';
			echo ');';
		}
		echo '
		</script>';
		return $attribute_js;
	}

	public function groupTable($id_lang, $ps_id_shop, $mp_id_product)
	{
		$obj_mpattr = new Mpproductattribute();
		$combinations_groups = $obj_mpattr->getMpAttributesGroups($id_lang, $ps_id_shop, $mp_id_product);
		return $combinations_groups;
	}	

	public function setMedia() 
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'mpcombination/views/css/mp_combination.css');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');

		$this->addJS(_MODULE_DIR_.'mpcombination/views/js/attributesBack.js');
		$this->addJS(_MODULE_DIR_.'mpcombination/views/js/mpattribute_impact.js');
		
		$this->addJS(array(
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui.js',
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-sliderAccess.js',
					_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.js',
				));
		$this->addCSS(_MODULE_DIR_.'marketplace/views/js/jquerydatepicker/jquery-ui-timepicker-addon.css');
	}
}
?>