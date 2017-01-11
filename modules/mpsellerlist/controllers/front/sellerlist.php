<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
class MpSellerListsellerlistModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $id_lang = $this->context->language->id;
        $id_customer = $this->context->cookie->id_customer;
        $obj_mp_shop = new MarketplaceShop();
        if ($id_customer) {
            $mp_shop_info = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
            if ($mp_shop_info) {
                $shop_link = $mp_shop_info['link_rewrite'];
                $param = array('mp_shop_name' => $shop_link);
                $gotoshop_link = $link->getModuleLink('marketplace', 'dashboard');
            } else {
                $gotoshop_link = $link->getPageLink('my-account');
            }
        } else {
            $gotoshop_link = $link->getPageLink('my-account');
        }

        $obj_seller = new SellerInfoDetail();
        $all_active_seller = $obj_seller->findAllActiveSellerInfoByLimit();

        if ($all_active_seller) {
            $total_active_seller = count($all_active_seller);
            $param = array('flag' => 1);
            $shop_img = array();
            $shop_store_link = $link->getModuleLink('marketplace', 'shopstore', $param);
            foreach ($all_active_seller as $seller_key => $act_seller) {
                $img_file = 'modules/marketplace/views/img/shop_img/'.$act_seller['id'].'-'.$act_seller['shop_name'].'.jpg';
                if (file_exists($img_file)) {
                    $shop_img[] = $act_seller['id'].'-'.$act_seller['shop_name'].'.jpg';
                } else {
                    $shop_img[] = 'defaultshopimage.jpg';
                }

                $mp_shop_infobycustomer = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($act_seller['id_customer']);
                $all_active_seller[$seller_key]['link_rewrite'] = $mp_shop_infobycustomer['link_rewrite'];
            }

            $viewmorelist_link = $link->getModuleLink('mpsellerlist', 'viewmorelist');
            $viewmoreproduct_link = $link->getModuleLink('mpsellerlist', 'viewmoreproduct');
            $this->context->smarty->assign('viewmorelist_link', $viewmorelist_link);
            $this->context->smarty->assign('viewmoreproduct_link', $viewmoreproduct_link);
            $this->context->smarty->assign('shop_img', $shop_img);
            $this->context->smarty->assign('shop_store_link', $shop_store_link);
            $this->context->smarty->assign('all_active_seller', $all_active_seller);
        } else {
            $total_active_seller = 0;
        }

        $obj_seller_product = new SellerProductDetail();
        $seller_product_info = $obj_seller_product->findAllActiveSellerProductByLimit(0, 8);

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
            $this->context->smarty->assign('product_link', $product_link);
            $this->context->smarty->assign('seller_product_info', $seller_product_info);
            $this->context->smarty->assign('product_img_info', $product_img_info);
        } else {
            $active_seller_product = 0;
        }

        $mp_seller_text = Configuration::getGlobalValue('MP_SELLER_TEXT');
        $this->context->smarty->assign('mp_seller_text', $mp_seller_text);
        $this->context->smarty->assign('active_seller_product', $active_seller_product);
        $this->context->smarty->assign('total_active_seller', $total_active_seller);
        $this->context->smarty->assign('gotoshop_link', $gotoshop_link);
        $this->context->smarty->assign('path', _MODULE_DIR_);
        $this->setTemplate('mpsellerlist.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'mpsellerlist/views/css/sellerlist.css');
    }
}
