<?php
class mpcombinationGetattributevalueModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		$this->display_header = false;
		$this->display_footer = false;
	}
	public function initContent()
	{
		$attribute_id = Tools::getValue('attr_id');
		$attributes = Attribute::getAttributes($this->context->language->id, true);
		$i = 0;
		$attribute_val = array();
		foreach ($attributes as $attribute)
		if ($attribute_id == $attribute['id_attribute_group'])
		{
			$attribute_val[$i]['id'] = $attribute['id_attribute'];
			$attribute_val[$i]['name'] = $attribute['name'];
			$i++;
		}
		$jsondata = Tools::jsonEncode($attribute_val);
		echo $jsondata;
	}
}
?>