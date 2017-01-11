<?php
if (!defined('_PS_VERSION_'))
	exit;
include_once 'classes/PsBookingAttribute.php';
class Booking extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
  	public function __construct()
	{
		$this->name = 'booking';
		$this->tab = 'front_office_features';
		$this->version = '1.6.0';
		$this->author = 'Webkul';
		$this->bootstrap = true;
		$this->need_instance = 0;
	    $context = Context::getContext();
		parent::__construct();
		$this->displayName = $this->l('Booking System');
		$this->description = $this->l('Online Booking System');
	}

	public function hookDisplayBackOfficeHeader() 
	{
		if (Tools::getValue('controller') == 'AdminAttributesGroups')
			$this->context->controller->addJS(($this->_path).'views/js/disable_date_attribute.js');
	}
	
	public function hookDisplayAddProductAttributeGrouphook($params)
	{
		$id_product = (int) Tools::getValue('id_product');
        $id_product_attribute = Db::getInstance()->executeS("SELECT `id_product_attribute`  from `" . _DB_PREFIX_ . "product_attribute` where `id_product`=" . $id_product . "");
        $i = 0;
        $attr_name = array();
        foreach ($id_product_attribute as $data)
        {
            $id_attribute = Db::getInstance()->executeS("SELECT `id_attribute`  from `" . _DB_PREFIX_ . "product_attribute_combination` where `id_product_attribute`=" . $data['id_product_attribute'] . "");
            foreach ($id_attribute as $data1)
            {
                $id_attribute_group = Db::getInstance()->getRow("SELECT *  from `" . _DB_PREFIX_ . "attribute` where `id_attribute`=" . $data1['id_attribute'] . "");
                $group_type	= Db::getInstance()->getRow("SELECT `group_type`  from `" . _DB_PREFIX_ . "attribute_group` where `id_attribute_group`=" . $id_attribute_group['id_attribute_group'] . "");
                if ($group_type['group_type'] == 'date')
                {
                    $attribute_name = Db::getInstance()->getRow("SELECT `name`  from `" . _DB_PREFIX_ . "attribute_lang` where `id_attribute`=" . $data1['id_attribute'] . " and `id_lang`=".$this->context->language->id);
                    $attr_name[$i]  = $attribute_name['name'];
                    $i++;
                }
            }
        }
        $selected_dates[] = array();
        if ($i > 0)
        {
            $attr_name = array_combine($attr_name, array_map('strtotime', $attr_name));
            foreach ($attr_name as $key=>$value)
                $selected_dates[] = $key;

            arsort($attr_name);
            end($attr_name);
            reset($attr_name);
            $end_date = key($attr_name);
        }
        $this->context->controller->addJS(array(
                _MODULE_DIR_.'booking/views/js/disable_date_attribute.js',
            ));
        $this->context->smarty->assign('end_date',$end_date);     
        $this->context->smarty->assign('selected_dates', Tools::jsonEncode($selected_dates));
		$this->context->smarty->assign('id_attribute_group',$params['key_attribute']);
		$this->context->smarty->assign('group',$params['group']);
		return $this->display(__FILE__, 'addDateAttribute.tpl');
	}

	public function hookActionAssignAttributesGroups()
	{
		$id_product_attribute = Db::getInstance()->executeS("SELECT `id_product_attribute`  from `" . _DB_PREFIX_ . "product_attribute` where `id_product`=" . Tools::getValue('id_product') . "");
        $i = 0;
        $date = array();
        foreach ($id_product_attribute as $data)
        {
            $id_attribute = Db::getInstance()->executeS("SELECT `id_attribute`  from `" . _DB_PREFIX_ . "product_attribute_combination` where `id_product_attribute`=" . $data['id_product_attribute'] . "");
            foreach ($id_attribute as $data1)
            {
                $id_attribute_group = Db::getInstance()->getRow("SELECT *  from `" . _DB_PREFIX_ . "attribute` where `id_attribute`=" . $data1['id_attribute'] . "");
                $group_type = Db::getInstance()->getRow("SELECT `group_type`  from `" . _DB_PREFIX_ . "attribute_group` where `id_attribute_group`=" . $id_attribute_group['id_attribute_group'] . "");
                if ($group_type['group_type'] == 'date')
                {
                    $attribute_name = Db::getInstance()->getRow("SELECT `name`  from `" . _DB_PREFIX_ . "attribute_lang` where `id_attribute`=" . $data1['id_attribute'] . " and `id_lang`=".$this->context->language->id);
                    $entered = true;
                    foreach ($date as $value)
                    {
                    	if ($value['id']==$data1['id_attribute'])
                    		$entered = false;
                    }
                    if ($entered)
                    {
                        $date[$i]['date'] = $attribute_name['name'];
                        $date[$i]['id'] = $data1['id_attribute'];
                        $i++;
                    }
                }
            }
        }
        $this->context->controller->addJS(array(
                _MODULE_DIR_.'booking/plugins/js/jquery-ui-1.10.3.custom.js',
                _MODULE_DIR_.'booking/views/js/datepicker_include.js',
            ));
        $this->context->controller->addCSS(_MODULE_DIR_.'booking/plugins/css/ui-lightness/jquery-ui-1.10.3.custom.css');
        $this->context->smarty->assign('date',$date);
	}

	/*public function insertAtrribute()
	{
		$id_shop = $this->context->shop->id;
		$attr_val = array(
			'0' => array(
				'is_color_group' => 0,
				'group_type' => 'date',
				'position' => 0),
			'1' => array(
				'is_color_group' => 0,
				'group_type' => 'shows',
				'position' => 0),
			'2' => array(
				'is_color_group' => 0,
				'group_type' => 'class',
				'position' => 0)
			);

		$i = false;
		foreach($attr_val as $key => $val)
		{
			$obj_attr_group = '';
			$obj_booking_attr = '';
			
			$obj_attr_group = $obj_attr_group.'_'.$key;
			$obj_booking_attr = $obj_booking_attr.'_'.$key;
			
			$obj_attr_group = new AttributeGroup();
			$obj_booking_attr = new PsBookingAttribute();
			
			$obj_attr_group->is_color_group = $val['is_color_group'];
			$obj_attr_group->group_type = $val['group_type'];
			$obj_attr_group->position = $val['position'];
			foreach (Language::getLanguages(true) as $lang)
			{
				$obj_attr_group->name[$lang['id_lang']] = $val['group_type'];
				$obj_attr_group->public_name[$lang['id_lang']] = $val['group_type'];
			}
			if ($obj_attr_group->save())
			{
				$obj_booking_attr->id_attribute_group = $obj_attr_group->id;
				$obj_booking_attr->save();		// ps booking table
				Db::getInstance()->insert('attribute_group_shop', array(
					'id_attribute_group' => (int)$obj_attr_group->id,
					'id_shop' => 0)
				);
			$i = true;
			}
		}

		if (!$i)
			return false;
		return true;
	}*/

	// delete attribute from attribute table which was created during installation of module
	public function deleteAttributeData()
	{
		$err = array();
		$obj_booking_attr = new PsBookingAttribute();
		$id_attr = $obj_booking_attr->getAttributeGroupId();
		if ($id_attr)
		{
			foreach($id_attr as $id_attr_val)
			{
				$obj_attr_group = new AttributeGroup($id_attr_val['id_attribute_group']);
				if ($obj_attr_group->delete())
				{
					$obj_booking_attr = new PsBookingAttribute($id_attr_val['id']);
					if (!$obj_booking_attr->delete())
						$err[] = 'error';
				}
			}
		}
		if (empty($err))
			return true;
		else
			return false;
	}

	// create new attribute date shows and class
	public function insertAtrribute()
	{
		$obj_attr_group1 = new AttributeGroup();
		$obj_booking_attr1 = new PsBookingAttribute();
		$obj_attr_group1->is_color_group = 0;
		$obj_attr_group1->group_type = 'date';
		$obj_attr_group1->position = 0;
		foreach (Language::getLanguages(true) as $lang)
		{
			$obj_attr_group1->name[$lang['id_lang']] = 'date';
			$obj_attr_group1->public_name[$lang['id_lang']] = 'date';
		}
		$obj_attr_group_id1 = $obj_attr_group1->save();
		/*$insert_attribute_group_date_shop = Db::getInstance()->insert('attribute_group_shop', array('id_attribute_group' =>$obj_attr_group1->id,'id_shop'=>$id_shop));*/
		$obj_booking_attr1->id_attribute_group = $obj_attr_group1->id;
		$obj_booking_attr1->save();		// ps booking table


		$obj_attr_group2 = new AttributeGroup();
		$obj_booking_attr2 = new PsBookingAttribute();
		$obj_attr_group2->is_color_group = 0;
		$obj_attr_group2->group_type = 'select';
		$obj_attr_group2->position = 0;
		foreach (Language::getLanguages(true) as $lang)
		{
			$obj_attr_group2->name[$lang['id_lang']] = 'class';
			$obj_attr_group2->public_name[$lang['id_lang']] = 'class';
		}
		$obj_attr_group_id2 = $obj_attr_group2->save();
		/*$insert_attribute_group_class_shop = Db::getInstance()->insert('attribute_group_shop', array('id_attribute_group' =>$obj_attr_group2->id,'id_shop'=>$id_shop));*/
		$obj_booking_attr2->id_attribute_group = $obj_attr_group2->id;
		$obj_booking_attr2->save();		// ps booking table

		$obj_attr_group3 = new AttributeGroup();
		$obj_booking_attr3 = new PsBookingAttribute();
		$obj_attr_group3->is_color_group = 0;
		$obj_attr_group3->group_type = 'select';
		$obj_attr_group3->position = 0;
		foreach (Language::getLanguages(true) as $lang)
		{
			$obj_attr_group3->name[$lang['id_lang']] = 'shows';
			$obj_attr_group3->public_name[$lang['id_lang']] = 'shows';
		}
		$obj_attr_group_id3 = $obj_attr_group3->save();
		/*$insert_attribute_group_class_shows = Db::getInstance()->insert('attribute_group_shop', array('id_attribute_group' =>$obj_attr_group3->id,'id_shop'=>$id_shop));*/
		$obj_booking_attr3->id_attribute_group = $obj_attr_group3->id;
		$obj_booking_attr3->save();		// ps booking table

		if (!$obj_attr_group_id1 || !$obj_attr_group_id2 || !$obj_attr_group_id3)
			return false;
		return true;
	}

	public function install()
	{
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql as $query)
		{
			if($query)
				if(!Db::getInstance()->execute(trim($query)))
					return false;
		}
		if (!parent::install()
			|| !$this->insertAtrribute()
			|| !$this->registerHook('displayAddProductAttributeGrouphook')
			|| !$this->registerHook('displayBackOfficeHeader')
			|| !$this->registerHook('actionAssignAttributesGroups'))
			return false;     
		return true;	
	}

	public function uninstall() 
	{
		if (!parent::uninstall()		
			|| !$this->deleteAttributeData())
			return false;     
		else
			return true;	
	}
}
?>