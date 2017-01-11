<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
class MpSellerListviewmoreproductModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $id_lang = $this->context->language->id;
        $id_customer = $this->context->cookie->id_customer;
        $obj_mp_shop = new MarketplaceShop();
        $sortby = trim(Tools::getValue('orderby'));
        $orderby = trim(Tools::getValue('orderway'));
        if ($sortby == 'price' && $orderby == 'asc') {
            $sort_orderby = '1';
        } elseif ($sortby == 'price' && $orderby == 'desc') {
            $sort_orderby = '2';
        } elseif ($sortby == 'name' && $orderby == 'asc') {
            $sortby = 'product_name';
            $sort_orderby = '3';
        } elseif ($sortby == 'name' && $orderby == 'desc') {
            $sortby = 'product_name';
            $sort_orderby = '4';
        } else {
            $sort_orderby = '0';
        }
        if ($id_customer) {
            $mp_shop_info = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
            if ($mp_shop_info) {
                //$shop_link = $mp_shop_info['link_rewrite'];
                //$param = array('mp_shop_name' => $shop_link);
                $gotoshop_link = $link->getModuleLink('marketplace', 'dashboard');
            } else {
                $gotoshop_link = $link->getPageLink('my-account');
            }
        } else {
            $gotoshop_link = $link->getPageLink('my-account');
        }
        $sellerlisthelper = new SellerListHelper();
        $seller_product_info = $sellerlisthelper->findAllActiveSellerProductOrderBy(0, 8, $sortby, $orderby);
        if ($seller_product_info) {
            $active_seller_product = count($seller_product_info);
            $product_img_info = array();
            $i = 0;
            foreach ($seller_product_info as $active_pro) {
                $product = new Product($active_pro['main_id_product'], false, $id_lang);
                $cover_image_id = Product::getCover($product->id);
                $product_img_info[$i]['link_rewrite'] = $product->link_rewrite;
                $product_img_info[$i]['lang_iso'] = Context::getContext()->language->iso_code;
                if ($cover_image_id) {
                    $ids = $product->id.'-'.$cover_image_id['id_image'];
                    $product_img_info[$i]['image'] = $ids;
                    ++$i;
                } else {
                    $product_img_info[$i]['image'] = 0;
                    ++$i;
                }
            }
            $params = array('flag' => 1);
            $product_link = $link->getPageLink('product', $params);
            $ajaxsort_url = $link->getModuleLink('mpsellerlist', 'viewmoreproduct');
            $this->context->smarty->assign(array(
                'product_link' => $product_link,
                'seller_product_info' => $seller_product_info,
                'product_img_info' => $product_img_info,
                'ajaxsort_url' => $ajaxsort_url, ));
        } else {
            $active_seller_product = 0;
        }
        $this->context->smarty->assign(array(
            'active_seller_product' => $active_seller_product,
            'gotoshop_link' => $gotoshop_link,
            'path' => _MODULE_DIR_,
            'orderby' => $sort_orderby,
            'sortby' => $sortby,
            'orderway' => $orderby, ));
        $this->setTemplate('mpallsellerproduct.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'mpsellerlist/views/css/sellerlist.css');
        $this->context->controller->addJs(dirname(dirname(dirname(__FILE__))).'/views/js/sellerlist.js');
    }
}
