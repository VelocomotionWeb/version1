<?php
/**
* 2007-2014 PrestaShop
*
* Jms Deals
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2014 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

class JmsDeal extends ObjectModel
{
	public $id_product;
	public $expire_time;
	
	public $active;	
	public $ordering;

	public static $definition = array(
		'table' => 'jmsdeals_items',
		'primary' => 'id_deal',		
		'fields' => array(			
			'id_product' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
			'active' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'ordering' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),						
			'expire_time' =>	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
		)
	);

	public	function __construct($product_id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($product_id, $id_lang, $id_shop);
	}

	public function add($autodate = true, $null_values = false)
	{
		$res = true;
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		
		$res = parent::add($autodate, $null_values);
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'jmsdeals` (`id_deal`,`id_shop` )
			VALUES('.(int)$this->id.','.(int)$id_shop.')'
		);
		
		return $res;
	}
	
	public function delete()
	{
		$res = true;
		
		$res &= $this->reOrderPositions();

		$res &= Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'jmsdeals`
			WHERE `id_deal` = '.(int)$this->id
		);
		$res &= parent::delete();
		return $res;
	}

	public function reOrderPositions()
	{
		$id_deal = $this->id;
		$context = Context::getContext();
		$id_shop = $context->shop->id;

		$max = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT MAX(hss.`ordering`) as ordering
			FROM `'._DB_PREFIX_.'jmsdeals_items` hss, `'._DB_PREFIX_.'jmsdeals` hs
			WHERE hss.`id_product` = hs.`id_product` AND hs.`id_shop` = '.(int)$id_shop
		);

		if ((int)$max == (int)$id_deal)
			return true;

		$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hss.`ordering` as ordering, hss.`id_product` as id_product
			FROM `'._DB_PREFIX_.'jmsbrands_logos` hss
			LEFT JOIN `'._DB_PREFIX_.'jmsdeals` hs ON (hss.`id_product` = hs.`id_product`)
			WHERE hs.`id_shop` = '.(int)$id_shop.' AND hss.`ordering` > '.(int)$this->ordering
		);

		foreach ($rows as $row)
		{
			$current_slide = new JmsDeal($row['id_deal']);
			--$current_slide->position;
			$current_slide->update();
			unset($current_slide);
		}

		return true;
	}
}
