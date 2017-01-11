<?php
class StoreLocator extends ObjectModel
{
	public $name;
	public $id_seller;
	public $active;
	public $street;
	public $map_address;
	public $map_address_text;
	public $country_id;
	public $state_id;
	public $city_name;
	public $latitude;
	public $longitude;
	public $zip_code;
	public $phone;
	public $date_add;
	public $date_upd;
	public $destine1;
	public $destine2;
	public $bic;

	public static $definition = array(
		'table' => 'store_locator',
		'primary' => 'id',
		'fields' => array(
				'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
				'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
				'street' => array('type' => self::TYPE_HTML,  'size' => 255, 'required' => true),
				'map_address' => array('type' => self::TYPE_HTML, 'size' => 255),
				'map_address_text' => array('type' => self::TYPE_STRING, 'size' => 255),
				'city_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 64),
				'country_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'state_id' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
				'latitude' => array('type' => self::TYPE_FLOAT, 'required' => true),
				'longitude' => array('type' => self::TYPE_FLOAT, 'required' => true),
				'zip_code' => array('type' => self::TYPE_STRING, 'validate' => 'isPostCode', 'size' => 12, 'required' => true),
				'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32),
				'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
				'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
				'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
				'destine1' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
				'destine2' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
				'bic' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
				
		)
	);

	public function delete()
	{
	    $image_path_logo = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$this->id.".jpg";
	    @unlink($image_path_logo);

	    if (!$this->mpDeleteStoreProduct($this->id) || !parent::delete())
	    	return false;
	    return true;
	}

	public function mpDeleteStoreProduct($id_store)
	{
		$delete_store_products = Db::getInstance()->delete('store_products', 'id_store = '.(int)$id_store);
		if (!$delete_store_products)
			return false;

		return true;
	}

	public static function getStoreById($id)
	{
		$stores = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'store_locator`
									WHERE id = '.$id);
		if ($stores && !empty($stores))
			return $stores;
		return false;
	}

	public static function getAllStore($active = true)
	{
		$stores = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_locator` WHERE active = '.($active ? '1' : '0'));
		if ($stores && !empty($stores))
			return $stores;
		return false;
	}

	/**
	 * [getSellerStore get store by id_seller]
	 * @param  [type]  $id_seller [mp seller id]
	 * @param  boolean $active    [true: if active, false or no need: if all]
	 * @return [array]
	 */
	public static function getSellerStore($id_seller, $active = false)
	{
		if ($active)
			$stores = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_locator` WHERE `id_seller` = '.$id_seller.($active ? ' AND active = '.$active : ''));
		else
			$stores = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_locator` WHERE `id_seller` = '.$id_seller);
		if ($stores && !empty($stores))
			return $stores;
		return false;
	}

	public function activeStoreLocator($status,$id)
	{
		$is_update = Db::getInstance()->update('store_locator', array('active' => (int)$status), 'id='.$id);
		if (!$is_update)
			return false;
		return true;
	}
	 
	public function findStoreLocatorStatus($id)
	{
		$current_status = Db::getInstance()->getRow('SELECT `active`
								FROM '._DB_PREFIX_.'store_locator WHERE `id`='.(int)$id);
		if (!$current_status)
			return false;
		return $current_status;
	}

	/**
	 * [getMoreStoreDetails get store details with state and country name]
	 * @param  [array] $stores  [store array]
	 * @param  [int] $id_lang [language id]
	 * @return [array/false]
	 */
	public static function getMoreStoreDetails($stores, $id_lang)
    {
    	if (!is_array($stores))
    		return false;

        foreach ($stores as $key => $data)
        {
            $obj_country = new Country($data['country_id'], $id_lang);
            $obj_state = new State($data['state_id']);
            $stores[$key]['country_name'] = $obj_country->name;    
            $stores[$key]['state_name'] = $obj_state->name;    
        }
        return $stores;
    }

    public static function getStoreByCity($city_name)
    {
    	$stores = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_locator`
									WHERE city_name = "'.$city_name.'"');
		if ($stores && !empty($stores))
			return $stores;
		return false;
    }

    public static function getActiveStoreByCityAndIdSeller($city_name, $id_seller)
    {
    	$stores = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_locator`
									WHERE city_name = "'.$city_name.'" AND active = 1 AND id_seller = '.$id_seller);
		if ($stores && !empty($stores))
			return $stores;
		return false;
    }

    public static function getStoreByCountry($id_country)
    {
    	$stores = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_locator`
									WHERE country_id = '.(int)$id_country);
		if ($stores && !empty($stores))
			return $stores;
		return false;
    }

    public static function getStoreByState($id_state)
    {
    	$stores = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_locator`
									WHERE state_id = '.(int)$id_state);
		if ($stores && !empty($stores))
			return $stores;
		return false;
    }
}
?>