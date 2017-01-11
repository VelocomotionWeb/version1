<?php

class MpSellerListAjaxSellerSearchModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        // parent::initContent();
        $this->display_header = false;
        $this->display_footer = false;

        $key = Tools::getValue('key');
        $search_type = (int) Tools::getValue('search_type');

        if ($search_type == 1) {
            $search_for = 'seller_name';
        } elseif ($search_type == 2) {
            $search_for = 'shop_name';
        } elseif ($search_type == 3) {
            $search_for = 'address';
        }

        $sql = 'SELECT  ms.id AS mp_id_shop, ms.link_rewrite AS shop_link_rewrite, msi.seller_name AS mp_seller_name, msi.shop_name AS mp_shop_name, msi.address AS mp_shop_adr FROM '._DB_PREFIX_.'marketplace_seller_info AS msi
				INNER JOIN '._DB_PREFIX_.'marketplace_shop AS ms ON (ms.shop_name = msi.shop_name AND ms.is_active = 1)
				WHERE msi.'.$search_for." LIKE '%".$key."%'";

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            echo Tools::jsonEncode($result);
        } else {
            echo false;
        }
    }
}
