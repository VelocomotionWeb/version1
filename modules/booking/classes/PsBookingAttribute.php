<?php
class PsBookingAttribute extends ObjectModel
{
	public $id;
	public $id_attribute_group;
	public $date_add;
	public $date_upd;
		
	public static $definition = array(
		'table' => 'wk_booking_attr_group',
		'primary' => 'id',
		'fields' => array(
			'id_attribute_group' => array('type' => self::TYPE_INT, 'required' => true),
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
			)
	);

	public function getAttributeGroupId()
	{
		$id = Db::getInstance()->executeS('SELECT * From '._DB_PREFIX_.'wk_booking_attr_group');
		if ($id)
			return $id;
		else
			false;
	}
}