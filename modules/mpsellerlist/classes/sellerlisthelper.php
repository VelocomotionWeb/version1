<?php

class SellerListHelper
{
    public function findAllActiveSellerProductOrderBy($start_point = 0, $limit_point = 8, $orderby = false, $orderway = false)
    {
        if (!$orderby) {
            $orderby = 'id';
        }

        if (!$orderway) {
            $orderway = 'DESC';
        }

        $seller_product = Db::getInstance()->executeS('select 
            mpsp.*,msp.`id_product` as main_id_product FROM `'._DB_PREFIX_.'marketplace_seller_product` mpsp 
            JOIN `'._DB_PREFIX_.'marketplace_shop_product` msp on (mpsp.`id`=msp.`marketplace_seller_id_product`) 
            JOIN `'._DB_PREFIX_.'product` p on (msp.`id_product`=p.`id_product`)
            where mpsp.`active`=1 order by `'.pSQL($orderby).'` '.pSQL($orderway).' LIMIT '.(int)$start_point.','.(int)$limit_point);
        if (empty($seller_product)) {
            return false;
        } else {
            return $seller_product;
        }
    }

    public function findAllActiveSellerBySearch($search_for, $key)
    {
        $sql = 'SELECT msi.*,ms.`id` AS mp_shop_id, ms.`link_rewrite` AS shop_link_rewrite, msi.`seller_name` AS mp_seller_name, msi.`shop_name` AS mp_shop_name, msi.`address` AS mp_shop_adr, mpc.`id_customer` FROM `'._DB_PREFIX_.'marketplace_seller_info` msi
            LEFT JOIN `'._DB_PREFIX_.'marketplace_customer` mpc ON (msi.`id` = mpc.`marketplace_seller_id`)
            INNER JOIN `'._DB_PREFIX_.'marketplace_shop` ms ON (ms.`shop_name` = msi.shop_name AND ms.`is_active` = 1) WHERE msi.'.pSQL($search_for)." LIKE '%".pSQL($key)."%'";

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
}
