<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Order extends OrderCore
{
    public function setProductPrices(&$row)
    {
        $tax_calculator = OrderDetail::getTaxCalculatorStatic((int)$row['id_order_detail']);
        $row['tax_calculator'] = $tax_calculator;
        $row['tax_rate'] = $tax_calculator->getTotalRate();

        $row['product_price'] = Tools::ps_round($row['unit_price_tax_excl'], 12);
        $row['product_price_wt'] = Tools::ps_round($row['unit_price_tax_incl'], 12);

        $group_reduction = 1;
        if ($row['group_reduction'] > 0) {
            $group_reduction = 1 - $row['group_reduction'] / 100;
        }

        $row['product_price_wt_but_ecotax'] = $row['product_price_wt'] - $row['ecotax'];

        $row['total_wt'] = $row['total_price_tax_incl'];
        $row['total_price'] = $row['total_price_tax_excl'];
    }

}
