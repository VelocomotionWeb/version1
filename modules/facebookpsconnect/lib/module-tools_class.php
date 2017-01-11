<?php
/**
 * module-tools_class.php file defines all tools method in module - transverse
 */

class BT_FPCModuleTools
{
	/**
	 * translateJsMsg() method returns good translated errors
	 */
	public static function translateJsMsg()
	{
		$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']['id'] = FacebookPsConnect::$oModule->l('You have not filled out the application ID', 'module-tools_class');
		$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']['secret'] = FacebookPsConnect::$oModule->l('You have not filled out the application Secret', 'module-tools_class');
		$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']['callback'] = FacebookPsConnect::$oModule->l('You have not filled out the application callback', 'module-tools_class');
		$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']['scope'] = FacebookPsConnect::$oModule->l('You have not filled out the scope of App permissions', 'module-tools_class');
		$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']['developerKey'] = FacebookPsConnect::$oModule->l('You have not filled out the developer Key', 'module-tools_class');
		$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']['socialEmail'] = FacebookPsConnect::$oModule->l('You have not filled out your e-mail', 'module-tools_class');
		$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']['delete'] = FacebookPsConnect::$oModule->l('Delete', 'module-tools_class');
	}

	/**
	 * updateConfiguration() method update new keys in new module version
	 */
	public static function updateConfiguration()
	{
		// check to update new module version
		foreach ($GLOBALS[_FPC_MODULE_NAME . '_CONFIGURATION'] as $sKey => $mVal) {
			// use case - not exists
			if (Configuration::get($sKey) === false) {
				// update key/ value
				Configuration::updateValue($sKey, $mVal);
			}
		}
	}

	/**
	 * getConfiguration() method set all constant module in ps_configuration
	 *
	 * @param int $iShopId
	 */
	public static function getConfiguration($iShopId = null)
	{
		// get configuration options
		if (null !== $iShopId && is_numeric($iShopId)) {
			FacebookPsConnect::$aConfiguration = Configuration::getMultiple(array_keys($GLOBALS[_FPC_MODULE_NAME . '_CONFIGURATION']), null, null, $iShopId);
		}
		else {
			FacebookPsConnect::$aConfiguration = Configuration::getMultiple(array_keys($GLOBALS[_FPC_MODULE_NAME . '_CONFIGURATION']));
		}
	}

	/**
	 * getConnectorData() method get unserialized data by connector
	 *
	 * @param bool $bGetHook
	 * @param bool $bIsWidgetSet
	 * @return bool
	 */
	public static function getConnectorData($bGetHook = false, $bIsConnectorSet = false)
	{
		// check one connector set minimum
		$bOneSet = false;

		// use case - check if some connectors are configured
		foreach ($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'] as $sName => &$aVal) {
			// detect if connector is configured and stored
			if (false !== $aVal['data']) {
				// unserialize datas
				$aVal['data'] = unserialize($aVal['data']);

				if (!empty($aVal['data']['activeConnector'])) {
					$bOneSet = true;
				}

				if ($bGetHook) {
					// get hooks
					$aVal['hooks'] = self::getHookByConnector($sName);
				}
			}
		}
		if ($bIsConnectorSet) {
			return $bOneSet;
		}
	}

	/**
	 * getHookData() method get unserialized data by hook
	 */
	public static function getHookData()
	{
		// use case - check if some connectors are configured
		foreach ($GLOBALS[_FPC_MODULE_NAME . '_ZONE'] as $sHookId => &$aVal) {
			// detect if connector is configured and stored
			if (false !== $aVal['data']) {
				// unserialize data
				$aVal['data'] = unserialize($aVal['data']);
			}
		}
	}

	/**
	 * getHookByConnector() method get all linked hooks by connector
	 *
	 * @param string $sConnectorId
	 * @return array
	 */
	public static function getHookByConnector($sConnectorId = null)
	{
		// set variable
		$aHooks = array();
		$aConnectors = (null !== $sConnectorId && array_key_exists($sConnectorId, $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']))? array($sConnectorId => $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'][$sConnectorId]) : $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'];

		// get hooks by connector
		foreach ($aConnectors as $sName => $aData) {
			// loop on each hook
			foreach ($GLOBALS[_FPC_MODULE_NAME . '_ZONE'] as $sHookId => $aHookData) {
				if (Configuration::get(_FPC_MODULE_NAME . '_' . strtoupper($sHookId)) !== false) {
					if (array_key_exists($sName, unserialize(Configuration::get(_FPC_MODULE_NAME . '_' . strtoupper($sHookId))))) {
						$aHooks[$sName][] = array('id' => $sHookId, 'title' => $aHookData['title']);
					}
				}
			}
		}
		return (
			(null !== $sConnectorId && !empty($aHooks[$sConnectorId]))? $aHooks[$sConnectorId] : $aHooks
		);
	}

	/**
	 * unserializeData() method unserialize data of connector or hook
	 *
	 * @param string
	 * @return mixed
	 */
	public static function unserializeData($sId, $sGlobalType = 'connector')
	{
		$sType = ($sGlobalType == 'connector')? 'CONNECTORS' : 'ZONE';

		if (false !== $GLOBALS[_FPC_MODULE_NAME . '_' . $sType][$sId]['data']) {
			// unserialize data
			$GLOBALS[_FPC_MODULE_NAME . '_' . $sType][$sId]['data'] = unserialize($GLOBALS[_FPC_MODULE_NAME . '_' . $sType][$sId]['data']);
		}
	}

	/**
	 * isActiveLang() method defines if the language is active
	 *
	 * @param mixed $mLang
	 * @return bool
	 */
	public static function isActiveLang($mLang)
	{
		if (is_numeric($mLang)) {
			$sField = 'id_lang';
		}
		else {
			$sField = 'iso_code';
			$mLang = strtolower($mLang);
		}

		$mResult = Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'lang` WHERE active = 1 AND `' . $sField . '` = "' . pSQL($mLang) . '"');

		return (
			!empty($mResult)? true : false
		);
	}


	/**
	 * getLangIso() method set good iso lang
	 *
	 * @return string
	 */
	public static function getLangIso()
	{
		// get iso lang
		$sIsoLang = Language::getIsoById(FacebookPsConnect::$iCurrentLang);

		if (false === $sIsoLang) {
			$sIsoLang = 'en';
		}
		return $sIsoLang;
	}

	/**
	 * getLangId() method return Lang id from iso code
	 *
	 * @param string $sIsoCode
	 * @return int
	 */
	public static function getLangId($sIsoCode, $iDefaultId = null)
	{
		// get iso lang
		$iLangId = Language::getIdByIso($sIsoCode);

		if (empty($iLangId) && $iDefaultId !== null) {
			$iLangId = $iDefaultId;
		}
		return $iLangId;
	}
	
	/**
	 * getCurrency() method returns current currency sign or id
	 *
	 * @param string $sField : field name has to be returned
	 * @param string $iCurrencyId : currency id
	 * @return mixed : string or array
	 */
	public static function getCurrency($sField = null, $iCurrencyId = null)
	{
		// set
		$mCurrency = null;

		// get currency id
		if (null === $iCurrencyId) {
			$iCurrencyId = Configuration::get('PS_CURRENCY_DEFAULT');
		}

		$aCurrency = Currency::getCurrency($iCurrencyId);

		if ($sField !== null) {
			switch ($sField) {
				case 'id_currency' :
					$mCurrency = $aCurrency['id_currency'];
					break;
				case 'name' :
					$mCurrency = $aCurrency['name'];
					break;
				case 'iso_code' :
					$mCurrency = $aCurrency['iso_code'];
					break;
				case 'iso_code_num' :
					$mCurrency = $aCurrency['iso_code_num'];
					break;
				case 'sign' :
					$mCurrency = $aCurrency['sign'];
					break;
				case 'conversion_rate' :
					$mCurrency = $aCurrency['conversion_rate'];
					break;
				default:
					$mCurrency = $aCurrency;
					break;
			}
		}

		return $mCurrency;
	}

	/**
	 * getTimeStamp() method returns timestamp
	 *
	 * @param string $sDate
	 * @param string $sType
	 * @return mixed : bool or int
	 */
	public static function getTimeStamp($sDate, $sType = 'en')
	{
		// set variable
		$iTimeStamp = false;

		// get date
		$aTmpDate = explode(' ', str_replace(array('-', '/', ':'), ' ', $sDate));

		if (count($aTmpDate) > 1) {
			if ($sType == 'en') {
				$iTimeStamp = mktime(0, 0, 0, $aTmpDate[0], $aTmpDate[1], $aTmpDate[2]);
			}
			elseif ($sType == 'db') {
				$iTimeStamp = mktime(0, 0, 0, $aTmpDate[1], $aTmpDate[2], $aTmpDate[0]);
			}
			else {
				$iTimeStamp = mktime(0, 0, 0, $aTmpDate[1], $aTmpDate[0], $aTmpDate[2]);
			}
		}
		// destruct
		unset($aTmpDate);

		return $iTimeStamp;
	}


	/**
	 * formatTimestamp() method returns a formatted date
	 *
	 * @param int $iTimestamp
	 * @param mixed $mLocale
	 * @return string
	 */
	public static function formatTimestamp($iTimestamp, $sTemplate = null, $mLocale = false)
	{
		// set
		$sDate = '';

		if ($mLocale !== false) {
			if (null === $sTemplate) {
				$sTemplate = '%d %h. %Y';
			}
			// set date with locale format
			$sDate = strftime($sTemplate, $iTimestamp);
		}
		else {
			switch ($sTemplate) {
				case 'connect' :
					$sDate = date('d', $iTimestamp)
						. ' '
						. (!empty($GLOBALS[_FPC_MODULE_NAME . '_MONTH'][FacebookPsConnect::$sCurrentLang])? $GLOBALS[_FPC_MODULE_NAME . '_MONTH'][FacebookPsConnect::$sCurrentLang]['long'][date('n', $iTimestamp)] : date('M', $iTimestamp))
						. ' '
						. date('Y', $iTimestamp);
					break;
				default:
					// set date with matching month or with default language
					$sDate = date('d', $iTimestamp)
						. ' '
						. (!empty($GLOBALS[_FPC_MODULE_NAME . '_MONTH'][FacebookPsConnect::$sCurrentLang])? $GLOBALS[_FPC_MODULE_NAME . '_MONTH'][FacebookPsConnect::$sCurrentLang]['short'][date('n', $iTimestamp)] : date('M', $iTimestamp))
						. '. '
						. date('Y', $iTimestamp);
					break;
			}
		}
		return $sDate;
	}


	/**
	 * getPageName() method returns formatted URI for page name type
	 *
	 * @return mixed
	 */
	public static function getPageName()
	{
		$sScriptName = '';

		// use case - script name filled
		if (!empty($_SERVER['SCRIPT_NAME'])) {
			$sScriptName = $_SERVER['SCRIPT_NAME'];
		}
		// use case - php_self filled
		elseif ($_SERVER['PHP_SELF']) {
			$sScriptName = $_SERVER['PHP_SELF'];
		}
		// use case - default script name
		else {
			$sScriptName = 'index.php';
		}
		return (
			substr(basename($sScriptName), 0, strpos(basename($sScriptName), '.'))
		);
	}


	/**
	 * getAccountPageLink() method returns account page link
	 *
	 * @return string link
	 */
	public static function getAccountPageLink()
	{
		$oLink = new Link();

		$sLink = $oLink->getPageLink('my-account.php');

		unset($oLink);

		return $sLink;
	}


	/**
	 * getTemplatePath() method returns template path
	 *
	 * @param string $sTemplate
	 * @return string
	 */
	public static function getTemplatePath($sTemplate)
	{
		// set
		$mTemplatePath = null;

		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$mTemplatePath = FacebookPsConnect::$oModule->getTemplatePath($sTemplate);
		}
		else {
			if (file_exists(_PS_THEME_DIR_ . 'modules/' . FacebookPsConnect::$oModule->name . '/' . $sTemplate)) {
				$mTemplatePath = _PS_THEME_DIR_ . 'modules/' . FacebookPsConnect::$oModule->name . '/' . $sTemplate;
			}
			elseif (file_exists(_PS_MODULE_DIR_ . FacebookPsConnect::$oModule->name . '/' . $sTemplate)) {
				$mTemplatePath = _PS_MODULE_DIR_ . FacebookPsConnect::$oModule->name . '/' . $sTemplate;
			}
		}

		return $mTemplatePath;
	}

	/**
	 * getLinkObj() method returns link object
	 *
	 * @return obj
	 */
	public static function getLinkObj()
	{
		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$link = Context::getContext()->link;
		}
		else {
			global $link;
		}
		return $link;
	}


	/**
	 * getCookieObj() method returns cookie object
	 *
	 * @return obj
	 */
	public static function getCookieObj()
	{
		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$cookie = Context::getContext()->cookie;
		}
		else {
			global $cookie;
		}
		return $cookie;
	}


	/**
	 * getCartObj() method returns cart object
	 *
	 * @return obj
	 */
	public static function getCartObj()
	{
		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$cart = Context::getContext()->cart;
		}
		else {
			global $cart;
		}
		return $cart;
	}

	/**
	 * getProductImage() method returns product image
	 *
	 * @param obj $oProduct
	 * @param string $sImageType
	 * @return obj
	 */
	public static function getProductImage(Product &$oProduct, $sImageType)
	{
		$sImgUrl = '';

		if (Validate::isLoadedObject($oProduct)) {
			// use case - get Image
			$aImage = Image::getCover($oProduct->id);

			if (!empty($aImage)) {
				// get image url
				$sImgUrl = self::getLinkObj()->getImageLink($oProduct->link_rewrite, $oProduct->id . '-' . $aImage['id_image'], $sImageType);

				// use case - get valid IMG URI before  Prestashop 1.4
				$sImgUrl = self::detectHttpUri($sImgUrl);
			}
		}
		return $sImgUrl;
	}

	/**
	 * detectHttpUri() method detects and returns available URI - resolve Prestashop compatibility
	 *
	 * @param string $sURI
	 * @param string $sForceDomain
	 * @return mixed
	 */
	public static function detectHttpUri($sURI, $sForceDomain = '')
	{
		// use case - only with relative URI
		if (!strstr($sURI, 'http')) {
			$sURI = 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'? 's' : '')  . '://' . ($sForceDomain != ''? $sForceDomain : $_SERVER['HTTP_HOST']) . $sURI;
		}
		return $sURI;
	}

	/**
	 * truncateUri() method truncate current request_uri in order to delete params : sAction and sType
	 * 
	 * @category tools collection
	 * @param mixed: string or array $mNeedle
	 * @return mixed
	 */
	public static function truncateUri($mNeedle = '&sAction')
	{
		// set tmp
		$aQuery = is_array($mNeedle)? $mNeedle : array($mNeedle);

		// get URI
		$sURI = $_SERVER['REQUEST_URI'];

		foreach ($aQuery as $sNeedle) {
			$sURI = strstr($sURI, $sNeedle)? substr($sURI, 0 , strpos($sURI, $sNeedle)) : $sURI;
		}
		return $sURI;
	}

	/**
	 * jsonEncode() method detects available method and apply json encode
	 *
	 * @return string
	 */
	public static function jsonEncode($aData)
	{
		if (function_exists('json_encode')) {
			$aData = json_encode($aData);
		}
		elseif (method_exists('Tools', 'jsonEncode')) {
			$aData = Tools::jsonEncode($aData);
		}
		else {
			if (is_null($aData)) {
				return 'null';
			}
			if ($aData === false) {
				return 'false';
			}
			if ($aData === true) {
				return 'true';
			}
			if (is_scalar($aData)) {
				$aData = addslashes($aData);
				$aData = str_replace("\n", '\n', $aData);
				$aData = str_replace("\r", '\r', $aData);
				$aData = preg_replace('{(</)(script)}i', "$1'+'$2", $aData);
				return "'$aData'";
			}
			$isList = true;
			for ($i=0, reset($aData); $i<count($aData); $i++, next($aData)) {
				if (key($aData) !== $i) {
					$isList = false;
					break;
				}
			}
			$result = array();

			if ($isList) {
				foreach ($aData as $v) {
					$result[] = self::json_encode($v);
				}
				$aData = '[ ' . join(', ', $result) . ' ]';
			}
			else {
				foreach ($aData as $k => $v) {
					$result[] = self::json_encode($k) . ': ' . self::json_encode($v);
				}
				$aData = '{ ' . join(', ', $result) . ' }';
			}
		}

		return $aData;
	}

	/**
	 * jsonDecode() method detects available method and apply json decode
	 *
	 * @return mixed
	 */
	public static function jsonDecode($aData)
	{

		if (method_exists('Tools', 'jsonDecode')) {
			$aData = Tools::jsonDecode($aData);
		}
		elseif (function_exists('json_decode')) {
			$aData = json_decode($aData);

		}
		return $aData;
	}

	/**
	 * isInstalled() method check if specific module and module's vars are available
	 *
	 * @param int $sModuleName
	 * @param array $aCheckedVars
	 * @param bool $bObjReturn
	 * @return mixed : true or false or obj
	 */
	public static function isInstalled($sModuleName, array $aCheckedVars = array(), $bObjReturn = false)
	{
		$mReturn = false;

		// use case - check module is installed in DB
		if (Module::isInstalled($sModuleName)) {
			$oModule = Module::getInstanceByName($sModuleName);

			if (!empty($oModule)) {
				// check if module is activated
				$aActivated = Db::getInstance()->ExecuteS('SELECT id_module as id, active FROM ' . _DB_PREFIX_ . 'module WHERE name = "' . pSQL($sModuleName)  .'" AND active = 1');

				if (!empty($aActivated[0]['active'])) {
					$mReturn = true;

					if (version_compare(_PS_VERSION_, '1.5', '>')) {
						$aActivated = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'module_shop WHERE id_module = ' . pSQL($aActivated[0]['id'])  .' AND id_shop = ' . Context::getContext()->shop->id);

						if (empty($aActivated)) {
							$mReturn = false;
						}
					}

					if ($mReturn) {
						if (!empty($aCheckedVars)) {
							foreach ($aCheckedVars as $sVarName) {
								$mVar = Configuration::get($sVarName);

								if (empty($mVar)) {
									$mReturn = false;
								}
							}
						}
					}
				}
			}
			if ($mReturn && $bObjReturn) {
				$mReturn = $oModule;
			}
			unset($oModule);
		}
		return $mReturn;
	}

	/**
	 * isProductObj() method check if the product is a valid obj
	 *
	 * @param int $iProdId
	 * @param int $iLangId
	 * @param bool $bObjReturn
	 * @param bool $bAllProperties
	 * @return mixed : true or false
	 */
	public static function isProductObj($iProdId, $iLangId, $bObjReturn = false, $bAllProperties = false)
	{
		// set
		$bReturn = false;

		$oProduct = new Product($iProdId, $bAllProperties, $iLangId);

		if (Validate::isLoadedObject($oProduct)) {
			$bReturn = true;
		}

		return (
			!empty($bObjReturn) && $bReturn? $oProduct : $bReturn
		);
	}

	/**
	 * getProductPath() method write breadcrumbs of product for category
	 * 
	 * @category tools collection
	 * @param int $iCatId
	 * @param int $iCatId
	 * @return string
	 */
	public static function getProductPath($iCatId, $iLangId)
	{
		$oCategory = new Category($iCatId);

		return (
			(Validate::isLoadedObject($oCategory)? str_replace('>', ' &gt; ', strip_tags(self::getPath((int)($oCategory->id), (int)($iLangId)))) : '')
		);
	}
	
	/**
	 * getPath() method write breadcrumbs of product for category
	 * 
	 * Forced to redo the function from Tools here as it works with cookie
	 * for language, not a passed parameter in the function
	 *
	 * @param int $iCatId
	 * @param int $iCatId
	 * @param string $sPath
	 * @return string
	 */
	public static function getPath($iCatId, $iLangId, $sPath = '')
	{
		$mReturn = '';

		if ($iCatId == 1) {
			$mReturn = $sPath;
		}
		else {		
			// get pipe
			$sPipe = Configuration::get('PS_NAVIGATION_PIPE');
			
			if (empty($sPipe)) {
				$sPipe = '>';
			}
	
			$sFullPath = '';
			
			/* Old way: v1.2 - v1.3 */
			if (version_compare(_PS_VERSION_, '1.4.1') == -1) {
				// instantiate
				$oCategory = new Category((int)($iCatId), (int)($iLangId));
				
				if (Validate::isLoadedObject($oCategory)) {
					$sCatName = Category::hideCategoryPosition($oCategory->name);
					
					// htmlentities because this method generates some view
					if ($sPath != $sCatName) {
						$sDisplayedPath = htmlentities($sCatName, ENT_NOQUOTES, 'UTF-8') . $sPipe . $sPath;
					}
					else {
						$sDisplayedPath = htmlentities($sPath, ENT_NOQUOTES, 'UTF-8');
					}
						
					$mReturn = self::getPath((int)($oCategory->id_parent), $iLangId, trim($sDisplayedPath, $sPipe));
				}
			}
			/* New way for v1.4 */
			else { 
				$aCurrentCategory = Db::getInstance()->getRow('
					SELECT id_category, level_depth, nleft, nright
					FROM '._DB_PREFIX_.'category
					WHERE id_category = '.(int)$iCatId
				);
	
				if (isset($aCurrentCategory['id_category'])) {
					$sQuery = '
						SELECT c.id_category, cl.name, cl.link_rewrite
						FROM '._DB_PREFIX_.'category c';

					// use case 1.5
					if (version_compare(_PS_VERSION_, '1.5', '>')) {
						Shop::addSqlAssociation('category', 'c', false);
					}

					$sQuery .= ' LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category AND cl.`id_lang` = ' . (int)($iLangId) . (version_compare(_PS_VERSION_, '1.5', '>') ? Shop::addSqlRestrictionOnLang('cl') : '') . ')';

					$sQuery .= '
						WHERE c.nleft <= '.(int)$aCurrentCategory['nleft'].' AND c.nright >= '.(int)$aCurrentCategory['nright'].' AND cl.id_lang = '.(int)($iLangId).' AND c.id_category != 1
						ORDER BY c.level_depth ASC
						LIMIT '.(int)$aCurrentCategory['level_depth'];

					$aCategories = Db::getInstance()->ExecuteS($sQuery);
	
					$iCount = 1;
					$nCategories = count($aCategories);

					foreach ($aCategories as $aCategory) {
						$sFullPath .=
							htmlentities($aCategory['name'], ENT_NOQUOTES, 'UTF-8').
								(($iCount++ != $nCategories OR !empty($sPath)) ? '<span class="navigation-pipe">' . $sPipe . '</span>' : '');
					}
					$mReturn = $sFullPath . $sPath;
				}
			}
		}
		return $mReturn;
	}


	/**
	 * recursiveCategoryTree() method process categories to generate tree of them
	 *
	 * @param array $aCategories
	 * @param array $aIndexedCat
	 * @param array $aCurrentCat
	 * @param int $iCurrentIndex
	 * @param int $iDefaultId
	 * @return array
	 */
	public static function recursiveCategoryTree(array $aCategories, array $aIndexedCat, $aCurrentCat, $iCurrentIndex = 1, $iDefaultId = null)
	{
		// set variables
		static $_aTmpCat;
		static $_aFormatCat;

		if ($iCurrentIndex == 1) {
			$_aTmpCat = null;
			$_aFormatCat = null;
		}

		if (!isset($_aTmpCat[$aCurrentCat['infos']['id_parent']])) {
			$_aTmpCat[$aCurrentCat['infos']['id_parent']] = 0;
		}
		$_aTmpCat[$aCurrentCat['infos']['id_parent']] += 1;

		// calculate new level
		$aCurrentCat['infos']['iNewLevel'] = $aCurrentCat['infos']['level_depth'] + (version_compare(_PS_VERSION_, '1.5.0') != -1? 0 : 1);

		// calculate type of gif to display - displays tree in good
		$aCurrentCat['infos']['sGifType'] = (count($aCategories[$aCurrentCat['infos']['id_parent']]) == $_aTmpCat[$aCurrentCat['infos']['id_parent']] ? 'f' : 'b');

		// calculate if checked
		if (in_array($iCurrentIndex, $aIndexedCat)) {
			$aCurrentCat['infos']['bCurrent'] = true;
		}
		else {
			$aCurrentCat['infos']['bCurrent'] = false;
		}

		// define classname with default cat id
		$aCurrentCat['infos']['mDefaultCat'] = ($iDefaultId === null)? 'default' : $iCurrentIndex;

		$_aFormatCat[] = $aCurrentCat['infos'];

		if (isset($aCategories[$iCurrentIndex])) {
			foreach ($aCategories[$iCurrentIndex] as $iCatId => $aCat) {
				if ($iCatId != 'infos') {
					self::recursiveCategoryTree($aCategories, $aIndexedCat, $aCategories[$iCurrentIndex][$iCatId], $iCatId);
				}
			}
		}
		return $_aFormatCat;
	}

	/**
	 * manageProductDesc() method detect priority order to fill description : long description / short description / meta description
	 *
	 * @param array $aData
	 * @return string
	 */
	public static function manageProductDesc(array $aData)
	{
		// set
		$sDesc = '';

		if (!empty(FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_SORT_DESC'])) {
			$aDescPosition = unserialize(FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_SORT_DESC']);
		}
		else {
			$aDescPosition = array('meta', 'short', 'long');
		}

		foreach ($aDescPosition as $sOrder) {
			switch ($sOrder) {
				case 'meta' :
					if (empty($sDesc) && !empty($aData['meta_description'])) {
						$sDesc = $aData['meta_description'];
					}
					break;
				case 'short' :
					if (empty($sDesc) && !empty($aData['description_short'])) {
						$sDesc = $aData['description_short'];
					}
					break;
				case 'long' :
					if (empty($sDesc) && !empty($aData['description'])) {
						$sDesc = $aData['description'];
					}
					break;
				default:
					break;
			}
		}
		$sDesc = strip_tags($sDesc);

		return $sDesc;
	}

	/**
	 * getCustomerId() method detect if customer is logged and returns the customer ID
	 *
	 * @return int
	 */
	public static function getCustomerId(){
		$iCustomerId = 0;

		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$cookie = Context::getContext()->customer;
		}
		else {
			global $cookie;
		}

		// Check if user is logged
		if ($cookie->isLogged()){
			if (!empty(self::getCookieObj()->id_customer)){
				$iCustomerId = self::getCookieObj()->id_customer;
			}
		}

		return $iCustomerId;
	}

	/**
	 * fileGetContent() method detect if cURL is installed or if file_get_contents is allowed with allow_url_fopen
	 *
	 * @param $sUrl
	 * @param $aParams
	 * @return int
	 */
	public static function fileGetContent($sUrl, array $aParams = null){
		$sContent = '';

		if ('curl' == FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_API_REQUEST_METHOD']) {
			$rCurl = curl_init();

			if (empty($aParams)) {
				$aParams = array(
					CURLOPT_URL => $sUrl,
					CURLOPT_SSL_VERIFYPEER => ((strstr($sUrl, 'https://'))? true : false),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HEADER => 0,
					CURLOPT_VERBOSE => false,
				);
			}
			curl_setopt_array($rCurl, $aParams);

			$sContent = @curl_exec($rCurl);

			curl_close($rCurl);
		}
		elseif ('fopen' == FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_API_REQUEST_METHOD']) {
			$sContent = @file_get_contents($sUrl);
		}

		return $sContent;
	}

	/**
	 * execHttpRequest() method do an HTTP request
	 *
	 * @param $sUrl
	 * @param $aParams
	 * @return int
	 */
	public static function execHttpRequest($sUrl, array $aParams = null) {
		// set HTTP options
		$aOptions = array(
			'http' => array(),
		);

		// define the method to use
		$aOptions['http']['method'] = !empty($aParams['method'])? $aParams['method'] : 'POST';

		// check all parameters
		if (!empty($aParams['header'])
			&& is_string($aParams['header'])
		) {
			$aOptions['http']['header'] = $aParams['header'];
		}
		if (!empty($aParams['user_agent'])
			&& is_string($aParams['user_agent'])
		) {
			$aOptions['http']['user_agent'] = $aParams['user_agent'];
		}
		if (!empty($aParams['content'])
			&& is_array($aParams['content'])
		) {
			$aOptions['http']['content'] = http_build_query($aParams['content']);
		}
		if (!empty($aParams['proxy'])
			&& is_string($aParams['proxy'])
		) {
			$aOptions['http']['proxy'] = $aParams['proxy'];
		}
		if (!empty($aParams['request_fulluri'])
			&& is_bool($aParams['request_fulluri'])
		) {
			$aOptions['http']['request_fulluri'] = $aParams['request_fulluri'];
		}
		if (!empty($aParams['follow_location'])
			&& is_integer($aParams['follow_location'])
			&& in_array($aParams['follow_location'], array(0,1))
		) {
			$aOptions['http']['follow_location'] = $aParams['follow_location'];
		}
		if (!empty($aParams['max_redirects'])
			&& is_integer($aParams['max_redirects'])
		) {
			$aOptions['http']['max_redirects'] = $aParams['max_redirects'];
		}
		if (!empty($aParams['protocol_version'])
			&& is_float($aParams['protocol_version'])
		) {
			$aOptions['http']['protocol_version'] = $aParams['protocol_version'];
		}
		if (!empty($aParams['timeout'])
			&& is_float($aParams['timeout'])
		) {
			$aOptions['http']['timeout'] = $aParams['timeout'];
		}
		if (!empty($aParams['ignore_errors'])
			&& is_bool($aParams['ignore_errors'])
		) {
			$aOptions['http']['ignore_errors'] = $aParams['ignore_errors'];
		}

		$cContext = stream_context_create($aOptions);

		return (
			file_get_contents($sUrl, false, $cContext)
		);
	}
}