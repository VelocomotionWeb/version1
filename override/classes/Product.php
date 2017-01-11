<?php
class Product extends ProductCore
{
    public static function getAllCustomizedDatas($id_cart, $id_lang = null, $only_in_cart = true, $id_shop = null)
    {
        if (!Customization::isFeatureActive()) {
            return false;
        }
        if (!$id_cart) {
            return false;
        }
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (Shop::isFeatureActive() && !$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        if (!$result = Db::getInstance()->executeS('
			SELECT cd.`id_customization`, c.`id_address_delivery`, c.`id_product`, cfl.`id_customization_field`, c.`id_product_attribute`,
				cd.`type`, cd.`index`, cd.`value`, cfl.`name`
			FROM `'._DB_PREFIX_.'customized_data` cd
			NATURAL JOIN `'._DB_PREFIX_.'customization` c
			LEFT JOIN `'._DB_PREFIX_.'customization_field_lang` cfl ON (cfl.id_customization_field = cd.`index`
			AND id_shop = '.(int)$id_shop. '
			AND id_lang = '.(int)$id_lang.
                ($id_shop ? ' AND cfl.`id_shop` = '.$id_shop : '').')
			WHERE c.`id_cart` = '.(int)$id_cart.
            ($only_in_cart ? ' AND c.`in_cart` = 1' : '').'
			ORDER BY `id_product`, `id_product_attribute`, `type`, `index`
			')) {
            return false;
        }
		
			
        $customized_datas = array();
        foreach ($result as $row) {
            $customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['datas'][(int)$row['type']][] = $row;
        }
        if (!$result = Db::getInstance()->executeS(
            'SELECT `id_product`, `id_product_attribute`, `id_customization`, `id_address_delivery`, `quantity`, `quantity_refunded`, `quantity_returned`
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart` = '.(int)$id_cart.($only_in_cart ? '
			AND `in_cart` = 1' : ''))) {
            return false;
        }
        foreach ($result as $row) {
            $customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['quantity'] = (int)$row['quantity'];
            $customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['quantity_refunded'] = (int)$row['quantity_refunded'];
            $customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['quantity_returned'] = (int)$row['quantity_returned'];
        }
        return $customized_datas;
    }
    public function checkAccess($id_customer)
    {
        $context = Context::getContext();
        if (!$id_customer
            && isset($context->cookie)
            && isset($context->cookie->pc_groups)) {
            $groups = explode(',', $context->cookie->pc_groups);
            if ($groups !== false && count($groups) > 0) {
                return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                    SELECT ctg.`id_group`
                    FROM `'._DB_PREFIX_.'category_product` cp
                    INNER JOIN `'._DB_PREFIX_.'category_group` ctg ON (ctg.`id_category` = cp.`id_category`)
                    WHERE cp.`id_product` = '.(int)$this->id.' AND ctg.`id_group` IN(' . implode(',', $groups) . ')');
            }
        }
        return parent::checkAccess($id_customer);
    }
    public static function priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
        $id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, &$specific_price, $use_group_reduction,
        $id_customer = 0, $use_customer_price = true, $id_cart = 0, $real_quantity = 0)
    {
        static $address = null;
        static $context = null;
        if ($address === null) {
            $address = new Address();
        }
        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }
        if ($id_shop !== null && $context->shop->id != (int)$id_shop) {
            $context->shop = new Shop((int)$id_shop);
        }
        if (!$use_customer_price) {
            $id_customer = 0;
        }
        if ($id_product_attribute === null) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }
        $cache_id = (int)$id_product.'-'.(int)$id_shop.'-'.(int)$id_currency.'-'.(int)$id_country.'-'.$id_state.'-'.$zipcode.'-'.(int)$id_group.
            '-'.(int)$quantity.'-'.(int)$id_product_attribute.
            '-'.(int)$with_ecotax.'-'.(int)$id_customer.'-'.(int)$use_group_reduction.'-'.(int)$id_cart.'-'.(int)$real_quantity.
            '-'.($only_reduc?'1':'0').'-'.($use_reduc?'1':'0').'-'.($use_tax?'1':'0').'-'.(int)$decimals;
        $specific_price = SpecificPrice::getSpecificPrice(
            (int)$id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );
        if (isset(self::$_prices[$cache_id])) {
            
            if (isset($specific_price['price']) && $specific_price['price'] > 0) {
                $specific_price['price'] = self::$_prices[$cache_id];
            }
            return self::$_prices[$cache_id];
        }
        $cache_id_2 = $id_product.'-'.$id_shop;
        if (!isset(self::$_pricesLevel2[$cache_id_2])) {
            $sql = new DbQuery();
            $sql->select('product_shop.`price`, product_shop.`ecotax`');
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = '.(int)$id_shop.')');
            $sql->where('p.`id_product` = '.(int)$id_product);
            if (Combination::isFeatureActive()) {
                $sql->select('IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_product = p.id_product AND product_attribute_shop.id_shop = '.(int)$id_shop.')');
            } else {
                $sql->select('0 as id_product_attribute');
            }
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (is_array($res) && count($res)) {
                foreach ($res as $row) {
                    $array_tmp = array(
                        'price' => $row['price'],
                        'ecotax' => $row['ecotax'],
                        'attribute_price' => (isset($row['attribute_price']) ? $row['attribute_price'] : null)
                    );
                    self::$_pricesLevel2[$cache_id_2][(int)$row['id_product_attribute']] = $array_tmp;
                    if (isset($row['default_on']) && $row['default_on'] == 1) {
                        self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                    }
                }
            }
        }
        if (!isset(self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute])) {
            return;
        }
        $result = self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute];
        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float)$result['price'];
        } else {
            $price = (float)$specific_price['price'];
        }
		if ($id_cart) {
			$hasBookingInCart = Db::getInstance()->getRow('
				SELECT * FROM `' . _DB_PREFIX_ . 'bestkit_booking_order`
				WHERE `id_cart` = ' . (int)$id_cart . '
				AND `id_product` = ' . (int)$id_product . '
				AND `id_product_attribute` = ' . (int)$id_product_attribute . '
			');
			if ($hasBookingInCart) {
				$from = $hasBookingInCart['from'];
				$to = $hasBookingInCart['to'];
				$booking = Module::getInstanceByName('bestkit_booking');
				$price = $booking->getBookingPrice($id_product, $from, $to);
			}
		}
        if (!$specific_price || !($specific_price['price'] >= 0 && $specific_price['id_currency'])) {
            $price = Tools::convertPrice($price, $id_currency);
            if (isset($specific_price['price'])) {
                $specific_price['price'] = $price;
            }
        }
        if (is_array($result) && (!$specific_price || !$specific_price['id_product_attribute'] || $specific_price['price'] < 0)) {
            $attribute_price = Tools::convertPrice($result['attribute_price'] !== null ? (float)$result['attribute_price'] : 0, $id_currency);
            if ($id_product_attribute !== false) {
                $price += $attribute_price;
            }
        }
        $address->id_country = $id_country;
        $address->id_state = $id_state;
        $address->postcode = $zipcode;
        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$id_product, $context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }
        if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax) {
            $ecotax = $result['ecotax'];
            if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0) {
                $ecotax = $result['attribute_ecotax'];
            }
            if ($id_currency) {
                $ecotax = Tools::convertPrice($ecotax, $id_currency);
            }
            if ($use_tax) {
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    (int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID')
                );
                $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
                $price += $ecotax_tax_calculator->addTaxes($ecotax);
            } else {
                $price += $ecotax;
            }
        }
        $specific_price_reduction = 0;
        if (($only_reduc || $use_reduc) && $specific_price) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];
                if (!$specific_price['id_currency']) {
                    $reduction_amount = Tools::convertPrice($reduction_amount, $id_currency);
                }
                $specific_price_reduction = $reduction_amount;
                if (!$use_tax && $specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->removeTaxes($specific_price_reduction);
                }
                if ($use_tax && !$specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->addTaxes($specific_price_reduction);
                }
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
        }
        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }
        if ($use_group_reduction) {
            $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
            if ($reduction_from_category !== false) {
                $group_reduction = $price * (float)$reduction_from_category;
            } else { // apply group reduction if there is no group reduction for this category
                $group_reduction = (($reduc = Group::getReductionByIdGroup($id_group)) != 0) ? ($price * $reduc / 100) : 0;
            }
            $price -= $group_reduction;
        }
        if ($only_reduc) {
            return Tools::ps_round($specific_price_reduction, $decimals);
        }
			
		
		$total = $price * $quantity; 
		$total = Tools::ps_round($total, 20);
		$arr = (number_format($total,2) - floor($total));
		if ($arr > 0.01 && $arr <= 0.50) $total = floor($total) + 0.5;
		if ($arr > 0.50) $total = floor($total)+1;
		
		$price = $total	/ $quantity;
        if ($price < 0) {	
            $price = 0;
        }
        self::$_prices[$cache_id] = $price;
        return self::$_prices[$cache_id];
    }		
	
	public static function getPrice50($price, $quantity)
	{
		$price = $price * $quantity ;
		$arr = (number_format($price,2) - floor($price));
		
		return $price;
	}
    /*
    * module: lyopageeditor
    * date: 2016-12-12 16:43:02
    * version: 1.1.5
    */
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        unset(self::$definition['fields']['description']['validate']);
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
