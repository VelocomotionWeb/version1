<?php
class MarketplaceProductSeo extends ObjectModel
{
	public $id;	
	public $mp_product_id;
	public $meta_title;
	public $meta_description;
	public $friendly_url;			
	public $date_add;
	public $date_upd;	

	public static $definition = array(
			'table' => 'mp_product_seo',
			'primary' => 'id',
			'fields' => array(
				'mp_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt' ,'required' => true),
				'meta_title' => array('type' => self::TYPE_STRING),
				'meta_description' =>array('type' => self::TYPE_STRING),
				'friendly_url' => array('type' => self::TYPE_STRING),
				'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
				'date_upd' =>array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			),
		);

	public function getMetaInfo($mp_product_id)
	{
		$meta_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_product_seo` WHERE `mp_product_id`='.$mp_product_id);
		if(empty($meta_info))
			return false;
		else
			return $meta_info;
	}
}
?>