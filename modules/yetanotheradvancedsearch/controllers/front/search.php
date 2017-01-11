<?php
/**
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*/

include_once(dirname(__FILE__).'/../../yetanotheradvancedsearchConfig.php');

class YetanotheradvancedsearchSearchModuleFrontController extends ModuleFrontController {

	/**
	 * Main Entry point.
	 */
	public function initContent()
	{
		parent::initContent();

		// default values
		$orderby = 'price';
		$orderway = 'asc';
		$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
		$c = array();
				$df = null;
		$p = 0;
		$range = 3;

		// default ordering
		if (Tools::getValue('orderby') != null) $orderby = Tools::getValue('orderby');
		if (Tools::getValue('orderway') != null) $orderway = Tools::getValue('orderway');

		// number of products per page
		if (Tools::getValue('n') != null) $n = Tools::getValue('n');

		// => c = list of criteria
		if (Tools::getValue('c') != null) $c = Tools::getValue('c');

		// => df = default filter
		if (Tools::getValue('df') != null) $df = Tools::getValue('df');

		// page
		if (Tools::getValue('p') != null) $p = Tools::getValue('p');

		// do the search
		$this->doSearch($df, $c, $p, $n, $range, $orderby, $orderway);
	}

	/**
	 * Do the search.
	 *
	 * @param type $df default filter
	 * @param type $c
	 * @param type $p
	 * @param type $range
	 * @param type $orderby
	 * @param type $orderway
	 */
	public function doSearch($df, $c, $p, $n, $range, $orderby, $orderway)
	{
		// we can't just make a count, because to update the menu
		// we need all the ids
		$all_simplified_products = YetAnotherAdvancedSearchManager::doSimplifiedRequest($df, $c);
		$nb_products = count($all_simplified_products);

		// page
		if ($p > ($nb_products / $n)) $p = ceil($nb_products / $n);
		if ($p < 1) $p = 1;

		// number of pages 'around' current page
		$pages_nb = ceil($nb_products / (int)$n);
		$start = (int)$p - $range;
		if ($start < 1) $start = 1;
		$stop = (int)$p + $range;
		if ($stop > $pages_nb) $stop = (int)$pages_nb;

		$products = YetAnotherAdvancedSearchManager::doRequest($df, $c, $orderby, $orderway, $p, $n);

		// Add pagination variable
		$n_array = array();
		if ((int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10)
			$n_array = array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 30);
		else $n_array = array(10, 20, 30);
			$n_array = array_unique($n_array);
		asort($n_array);

		$order_by_values = array(
			0 => 'name', 1 => 'price', 2 => 'date_add',
			3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name',
			6 => 'quantity', 7 => 'reference');
		$order_way_values = array(0 => 'asc', 1 => 'desc');

		$this->context->smarty->assign(
			array(
				'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				'nb_products' => $nb_products,
				'category' => (object)array('id' => Tools::getValue('id_category_layered', 1)),
				'pages_nb' => (int)$pages_nb,
				'p' => (int)$p,
				'n' => (int)$n,
				'range' => (int)$range,
				'start' => (int)$start,
				'stop' => (int)$stop,
				'n_array' => $n_array,
				'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
				'products' => $products,
				'products_per_page' => $n,
				'static_token' => Tools::getToken(false),
				'page_name' => 'Advanced Search',
				'nArray' => $n_array,
				'orderby' => $orderby,
				'orderway' => $orderway,
				'orderbydefault' => $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')],
				'orderwayposition' => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')], // Deprecated: orderwayposition
				'orderwaydefault' => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')],
				'theme_dir' => _PS_THEME_DIR_,
			)
		);

		$this->cleanUpCompare();
		$tojson = array(
			'html' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'yetanotheradvancedsearch/views/templates/front/search_execution.tpl'),
			'menu' => YetAnotherAdvancedSearchManager::updateMenu($all_simplified_products),
			'nb_products' => $nb_products
		);

		// Automatic reindexing
		if (YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::UPDATE_TIMESTAMP) == 'true')
		{
			$last_update = YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::LAST_UPDATE);
			$current = time();
			$aday = 86400; // 24 x 60 x 60 (s)
			if (($current - $last_update) > $aday) $this->startReindexAsync();
		}

		echo Tools::jsonEncode($tojson);
		die();
	}

	/**
	 * Async reindexing..
	 */
	private function startReindexAsync($rw_timeout = 86400)
	{
		$errno = '';
		$errstr = '';

		// URL
		$s_url = $this->context->link->getModuleLink('yetanotheradvancedsearch', 'reindex');
		if (strpos($s_url, 'token=') === false)
		{
			$sep = '?';
			if (strpos($s_url, '?') !== false) $sep = '&';
			$s_url .= $sep.'token='.YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::URL_UPDATE);
		}
		$a_url_parts = parse_url($s_url);
		set_time_limit(0);
		$fp = fsockopen(
		$a_url_parts['host'], array_key_exists('port', $a_url_parts) && Tools::getIsset($a_url_parts['port']) ?
			$a_url_parts['port'] : 80, $errno, $errstr, 30
		);
		if (!$fp)
		{
			echo "$errstr ($errno)<br />\n";
			return;
		}
		$s_header = "GET {$a_url_parts['path']}?{$a_url_parts["query"]} HTTP/1.1\r\n";
		$s_header .= "Host: {$a_url_parts['host']}\r\n";
		$s_header .= "Connection: Close\r\n\r\n";
		stream_set_blocking($fp, false);
		stream_set_timeout($fp, $rw_timeout);
		fwrite($fp, $s_header);
		fclose($fp);
	}

	/**
	 * Clean Up compare
	 * @return null
	 */
	private function cleanUpCompare()
	{
		$id_compare = $this->context->cookie->id_compare;
		if ($id_compare != null)
		{
			return Db::getInstance()->execute('
				DELETE cp FROM `'._DB_PREFIX_.'compare_product` cp, `'._DB_PREFIX_.'compare` c
				WHERE cp.`id_compare`=c.`id_compare`
				AND c.`id_compare` = '.(int)$id_compare);
		}
		return null;
	}

}

?>
