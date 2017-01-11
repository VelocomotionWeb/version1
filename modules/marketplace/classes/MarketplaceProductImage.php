<?php
class MarketplaceProductImage extends ObjectModel
{
	public $id;	
	public $seller_product_id;
	public $seller_product_image_id;
	public $active;
			

	public static $definition = array(
		'table' => 'marketplace_product_image',
		'primary' => 'id',
		'fields' => array(
			'seller_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'seller_product_image_id' => array('type' => self::TYPE_STRING),
			'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool')
		),
	);
	
	public function findProductImageByMpProId($id)
	{
		$product_image = Db::getInstance()->executeS('SELECT  * FROM '._DB_PREFIX_.'marketplace_product_image
								WHERE seller_product_id = '.(int)$id);
		if (!empty($product_image))
			return $product_image;

		return false;
	}

	public function uploadProductMainImage($image, $seller_product_id)
	{		
		if ($image['size'] > 0)
		{			
			if ($image['tmp_name'] != "")
			{
				$this->uploadProductImage($image["tmp_name"], $seller_product_id);
				return true;
			}
		}
		else
			return true;
	}
	
	public function uploadProductOtherImage($image_temp, $seller_product_id)
	{
		if ($image_temp)
		{
			foreach ($image_temp as $img_tmp)
				if ($img_tmp != "")
					$this->uploadProductImage($img_tmp ,$seller_product_id);
		}
	}
	
	public function uploadProductImage($img_tmp, $seller_product_id)
	{
		$rand = MpHelper::randomImageName();		

		Db::getInstance()->insert('marketplace_product_image', array(
			'seller_product_id' => (int) $seller_product_id,
			'seller_product_image_id' => pSQL($rand)
		));

		$image_name = $rand.'.jpg';
		$upload_path = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';
		ImageManager::resize($img_tmp, $upload_path.$image_name);
	}

	public function changeProductImageStatusBySellerProductId($seller_id_product, $status)
	{
		return Db::getInstance()->update('marketplace_product_image', array('active' => $status),'seller_product_id='.$seller_id_product);
	}
}
?>