<?php
class AdminAttributesGroupsController extends AdminAttributesGroupsControllerCore
{	
	public function renderRange()
	{
		$id_lang = $this->context->language->id;
		if (Tools::getValue('addrange'))
		{
			$group_type = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'attribute_group_lang` where `id_attribute_group` IN(select `id_attribute_group` from `'._DB_PREFIX_.'attribute_group` where `group_type`="date") AND id_lang ='.(int)$id_lang);
			$this->fields_form = array(
				'legend' => array(
				'title' => $this->l('Values'),
				'image' => '../img/admin/asterisk.gif',
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Attribute Type'),
					'name' => 'att_type',
					'required' => true,
					'options' => array(
					'query' => $group_type,
					'id' => 'id_attribute_group',
					'name' => 'name'
					)
					),
				array(
					'type' => 'date',
					'label' => $this->l('Start Date'),
					'name' => 'start_date',
					'id' => 'start_date',
					'size' => 33,
					'required' => true,
				),
				array(
					'type' => 'date',
					'label' => $this->l('End  Date'),
					'name' => 'end_date',
					'id' => 'end_date',
					'size' => 33,
					'required' => true,
				)
			),
			'submit' => array(
	                'title' => $this->l('Save'),
	                'name' => 'submit_date_form',
	                'id'=>'submit_date_form'
	                    )
			);
			return AdminController::renderForm();
		}
	}
		/*
	* module: booking
	* date: 2015-06-26 16:33:07
	* version: 0.1
	*/
	/*
	* module: booking
	* date: 2015-07-14 08:31:29
	* version: 1.6
	*/
	/*
	* module: booking
	* date: 2015-09-15 19:21:25
	* version: 1.6
	*/
	public function initContent()
	{
		if (!Combination::isFeatureActive())
		{
			$url = '<a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.
					$this->l('Performance').'</a>';
			$this->displayWarning(sprintf($this->l('This feature has been disabled. You can activate it here: %s.'), $url));
			return;
		}
		$this->initTabModuleList();
		$this->initToolbar();
		$this->initPageHeaderToolbar();
		if ($this->display == 'edit' || $this->display == 'add')
		{
			if (!($this->object = $this->loadObject(true)))
				return;
			$this->content .= $this->renderForm();
		}
		elseif ($this->display == 'editAttributes')
		{
			if (!$this->object = new Attribute((int)Tools::getValue('id_attribute')))
				return;
			$this->content .= $this->renderFormAttributes();
		}
		elseif(Tools::getValue('addrange'))
		{
		  	$this->content .= $this->renderRange();
		}
		elseif ($this->display != 'view' && !$this->ajax)
		{
			$this->content .= $this->renderList();
			$this->content .= $this->renderOptions();
		}
		elseif ($this->display == 'view' && !$this->ajax)
			$this->content = $this->renderView();
		$this->context->smarty->assign(array(
			'table' => $this->table,
			'current' => self::$currentIndex,
			'token' => $this->token,
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
			'show_page_header_toolbar' => $this->show_page_header_toolbar,
			'page_header_toolbar_title' => $this->page_header_toolbar_title,
			'page_header_toolbar_btn' => $this->page_header_toolbar_btn
		));
	}
		/*
	* module: booking
	* date: 2015-06-26 16:33:07
	* version: 0.1
	*/
	/*
	* module: booking
	* date: 2015-07-14 08:31:29
	* version: 1.6
	*/
	/*
	* module: booking
	* date: 2015-09-15 19:21:25
	* version: 1.6
	*/
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'add':
			case 'edit':
			case 'editAttributes':
					$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
				if ($this->display == 'editAttributes' && !$this->id_attribute)
					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#',
						'desc' => $this->l('Save then add another value'),
						'force_desc' => true,
					);
				$back = self::$currentIndex.'&token='.$this->token;
				$this->toolbar_btn['back'] = array(
					'href' => $back,
					'desc' => $this->l('Back to list')
				);
				break;
			default: 				
				$this->toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
				'desc' => $this->l('Add New Attributes')
				);
				$this->toolbar_btn['newAttributes'] = array(
					'href' => self::$currentIndex.'&amp;updateattribute&amp;token='.$this->token,
					'desc' => $this->l('Add New Values'),
					'class' => 'toolbar-new'
				);
				$this->toolbar_btn['newRange'] = array(
					'href' => self::$currentIndex.'addrange=1&token='.$this->token,
					'desc' => $this->l('Add New Range'),
					'class' => 'toolbar-new'
				);
		}
	}
	/*
	* module: booking
	* date: 2015-07-14 08:31:29
	* version: 1.6
	*/
	/*
	* module: booking
	* date: 2015-09-15 19:21:25
	* version: 1.6
	*/
	public function initPageHeaderToolbar()
	{
		if (empty($this->display))
		{
			$this->page_header_toolbar_btn['new_attribute_group'] = array(
				'href' => self::$currentIndex.'&addattribute_group&token='.$this->token,
				'desc' => $this->l('Add new attribute', null, null, false),
				'icon' => 'process-icon-new'
			);
			$this->page_header_toolbar_btn['new_value'] = array(
				'href' => self::$currentIndex.'&updateattribute&id_attribute_group='.(int)Tools::getValue('id_attribute_group').'&token='.$this->token,
				'desc' => $this->l('Add new value', null, null, false),
				'icon' => 'process-icon-new'
			);
			$this->page_header_toolbar_btn['newRange'] = array(
					'href' => self::$currentIndex.'&addrange=1&token='.$this->token,
					'desc' => $this->l('Add New Range'),
					'icon' => 'process-icon-new'
				);
		}
		if ($this->display == 'view')
			$this->page_header_toolbar_btn['new_value'] = array(
				'href' => self::$currentIndex.'&updateattribute&id_attribute_group='.(int)Tools::getValue('id_attribute_group').'&token='.$this->token,
				'desc' => $this->l('Add new value', null, null, false),
				'icon' => 'process-icon-new'
			);
		parent::initPageHeaderToolbar();
	}
	/*
	* module: booking
	* date: 2015-07-14 08:31:29
	* version: 1.6
	*/
	/*
	* module: booking
	* date: 2015-09-15 19:21:25
	* version: 1.6
	*/
	public function postProcess()
	{
		if (Tools::getValue('error'))
		{
			if (Tools::getValue('error') == 1)
				$msg = Tools::displayError('Invalid Start date.');
			else if (Tools::getValue('error') == 2)
				$msg = Tools::displayError('Invalid End Date.');
			$this->errors[] = Tools::displayError($msg);
			$this->context->smarty->assign("errors", $this->errors);
		}
		if(Tools::isSubmit('submit_date_form'))
		{
			$dates = array();
			$step = '+1 day';
			$id_attribute_gp = Tools::getValue('att_type');
			$current = Tools::getValue('start_date');
			$last = Tools::getValue('end_date');
			if (empty($current) || !Validate::isDateFormat($current))
				$error = 1;
			elseif (empty($last) || !Validate::isDateFormat($last))
				$error = 2;
			if (!isset($error))
			{
				$current = strtotime(Tools::getValue('start_date'));
				$last = strtotime(Tools::getValue('end_date'));
				$i=0;
				while( $current <= $last ) 
				{
					$dates[$i] = date('Y-m-d', $current);
					$current = strtotime($step, $current);
					$i++;
				}
				for($i=0;$i<count($dates);$i++)
				{
					$position = Db::getInstance()->getRow('SELECT `position`+1 as position FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.$id_attribute_gp.' ORDER BY position DESC');
					
					$name_exists = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."attribute_lang` WHERE `name` = '$dates[$i]'");
					if($position)
						$pos = $position['position'];
					else
						$pos = 0;
					if (!$name_exists)
					{
						$obj_attr_group1 = new Attribute();
						$obj_attr_group1->id_attribute_group = $id_attribute_gp;
						$obj_attr_group1->position = $pos;
						foreach (Language::getLanguages(true) as $lang)
							$obj_attr_group1->name[$lang['id_lang']] = $dates[$i];
						$obj_attr_group_id1 = $obj_attr_group1->add();
					}
				}
				$date_grp_id = Db::getInstance()->getValue('SELECT `id_attribute_group` FROM `'._DB_PREFIX_.'attribute_group` WHERE `group_type` = "date"');
				$attributes_data = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'attribute_lang` GROUP BY name ORDER BY name');
				$p=0;
				foreach ($attributes_data as $attr_data)
				{
					Db::getInstance()->update('attribute',array('position'=>$p),'id_attribute='.$attr_data['id_attribute'].' AND id_attribute_group='.$date_grp_id);
					$p++;
				}
				Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminAttributesGroups'));
			}
			else
				Tools::redirectAdmin(self::$currentIndex.'&addrange=1&error='.$error.'&token='.Tools::getAdminTokenLite('AdminAttributesGroups'));	
		}
		if(Tools::isSubmit('submitAddattribute_group'))
		{
			$attr_name = trim(Tools::getValue('name_1'));
			if ($attr_name == 'date' || $attr_name == 'shows' || $attr_name == 'class')
				return;
		}
		parent::postProcess();
	}
}
?>