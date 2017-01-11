<?php
/**
 * module-dao_class.php file defines method of management of DATA ACCESS OBJECT
 */

class BT_FPCModuleDao
{
	/**
	 * Magic Method __construct
	 */
	private function __construct()
	{

	}


	/**
	 * updateCustomerEmail() method check if customer account already exists
	 *
	 * @param int $iCustomerId
	 * @param string $sEmail
	 * @return bool
	 */
	public static function updateCustomerEmail($iCustomerId, $sEmail)
	{
		$sQuery = 'UPDATE ' . _DB_PREFIX_ . 'customer SET email = "' . pSQL($sEmail) . '"'
			. ' WHERE `active` = 1 AND `id_customer` = ' . $iCustomerId
			. ' AND `deleted` = 0' . (version_compare(_PS_VERSION_, '1.4', '>')? ' AND `is_guest` =  0' : '');

		// execute
		return (
			Db::getInstance()->Execute($sQuery)
		);
	}

	/**
	 * updateCustomerName() method update the customer name
	 *
	 * @param int $iCustomerId
	 * @param string $sName
	 * @return bool
	 */
	public static function updateCustomerName($iCustomerId, $sName)
	{
		$sQuery = 'UPDATE ' . _DB_PREFIX_ . 'customer SET lastname = "' . pSQL($sName) . '"'
			. ' WHERE `active` = 1 AND `id_customer` = ' . $iCustomerId
			. ' AND `deleted` = 0' . (version_compare(_PS_VERSION_, '1.4', '>')? ' AND `is_guest` =  0' : '');

		// execute
		return (
			Db::getInstance()->Execute($sQuery)
		);
	}

	/**
	 * UpdateCustomerFirstName() method update the customer
	 *
	 * @param int $iCustomerId
	 * @param string $sFirstName
	 * @return bool
	 */
	public static function updateCustomerFirstName($iCustomerId, $sFirstName)
	{
		$sQuery = 'UPDATE ' . _DB_PREFIX_ . 'customer SET firstname = "' . pSQL($sFirstName) . '"'
			. ' WHERE `active` = 1 AND `id_customer` = ' . $iCustomerId
			. ' AND `deleted` = 0' . (version_compare(_PS_VERSION_, '1.4', '>')? ' AND `is_guest` =  0' : '');

		// execute
		return (
		Db::getInstance()->Execute($sQuery)
		);
	}

	/**
	 * UpdateCustomerFirstName() method update the customer
	 *
	 * @param int $iCustomerId
	 * @param string $sFirstName
	 * @return bool
	 */
	public static function updateCustomerPassword($iCustomerId, $sPassword)
	{
		$sPassword = Tools::encrypt($sPassword);
		$sQuery = 'UPDATE ' . _DB_PREFIX_ . 'customer SET passwd = "' . pSQL($sPassword) . '"'
			. ' WHERE `active` = 1 AND `id_customer` = ' . $iCustomerId
			. ' AND `deleted` = 0' . (version_compare(_PS_VERSION_, '1.4', '>')? ' AND `is_guest` =  0' : '');

		// execute
		return (
		Db::getInstance()->Execute($sQuery)
		);
	}



	/**
	 * addCustomerAssociationStatus() method add customer FB account association to deactivate the reminder
	 *
	 * @param int $iShopId
	 * @param int $iCustId
	 * @return bool
	 */
	public static function addCustomerAssociationStatus($iShopId, $iCustId)
	{
		$sQuery = 'INSERT INTO ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_customer_assoc (CCA_SHOP_ID, CCA_CUST_ID) '
			. 'VALUES("' . $iShopId . '","' . $iCustId . '")';

		return (
			Db::getInstance()->Execute($sQuery)
		);
	}

	/**
	 * addCustomerAssociationStatus() method add a callback for review
	 *
	 * @param int $iShopId
	 * @param int $iCustId
	 * @return bool
	 */
	public static function existCustomerAssociationStatus($iShopId, $iCustId)
	{
		$sQuery = 'SELECT COUNT(*) as nb FROM  ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_customer_assoc  '
			. 'WHERE CCA_CUST_ID = ' . $iCustId . ' AND CCA_SHOP_ID = ' . $iShopId . ' ';

		$aNb = Db::getInstance()->ExecuteS($sQuery);

		return (
			!empty($aNb[0]['nb']) ? true : false
		);
	}

	/**
	 * collectSocialData() method collect data about customer via social network
	 *
	 * @param int $iCustId
	 * @param int $sSocialId
	 * @param int $iShopId
	 * @param string $sSocialType
	 * @param string $sAction
	 * @param string $sType
	 * @param int $iObjId
	 * @return bool
	 */
	public static function collectSocialData($iCustId, $sSocialId, $iShopId, $sSocialType, $sAction, $sType, $iObjId)
	{
		$sQuery = 'INSERT INTO ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_collect (COL_CUST_ID, COL_SOCIAL_ID, COL_SHOP_ID, COL_SOCIAL_TYPE, COL_ACTION, COL_TYPE, COL_OBJ_ID, COL_DATE_ADD) '
			. 'VALUES(' . $iCustId . ', ' . $sSocialId . ', ' . $iShopId . ', "' . $sSocialType . '", "' . $sAction . '", "' . $sType . '", ' . $iObjId . ', now())';

		return (
			Db::getInstance()->Execute($sQuery)
		);
	}

	/**
	 * existSocialData() method detect if the object is already registered
	 *
	 * @param int $iShopId
	 * @param int $iCustId
	 * @param string $sSocialId
	 * @param string $sSocialType
	 * @param string $sAction
	 * @param string $sType
	 * @param int $iObjId
	 * @return bool
	 */
	public static function existSocialData($iShopId, $iCustId, $sSocialId, $sSocialType, $sAction, $sType, $iObjId)
	{
		$sQuery = 'SELECT count(*) as nb FROM ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_collect '
			.   ' WHERE (COL_CUST_ID = ' . $iCustId . ' OR COL_SOCIAL_ID = "' . $sSocialId . '" )'
			.   ' AND COL_SHOP_ID = ' . $iShopId
			.   ' AND COL_SOCIAL_TYPE = "' . pSQL($sSocialType) . '"'
			.   ' AND COL_ACTION = "' . pSQL($sAction) . '"'
			.   ' AND COL_TYPE = "' . pSQL($sType) . '"'
			.   ' AND COL_OBJ_ID = ' . $iObjId;

		$aNb = Db::getInstance()->ExecuteS($sQuery);

		return (
			!empty($aNb[0]['nb']) ? true : false
		);
	}
}