<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
class MpSellerListviewmorelistModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $obj_seller = new SellerInfoDetail();
        $obj_mp_shop = new MarketplaceShop();
        $param = array('flag' => 1);
        $shop_store_link = $link->getModuleLink('marketplace', 'shopstore', $param);
        $viewmorelist_link = $link->getModuleLink('mpsellerlist', 'viewmorelist', $param);
        $ajaxsearch_link = $link->getModuleLink('mpsellerlist', 'ajaxsellersearch');
        $alp = Tools::getValue('alp');
        $orderby = trim(Tools::getValue('orderby'));
        $key = trim(Tools::getValue('name'));

        if ($alp) {
            $this->context->smarty->assign('alph', $alp);
            $all_active_seller = $obj_seller->findAllActiveSellerInfoByLimit(false, false, true, false, $alp);
        } else {
            if ($orderby && $key) {
                if ($orderby == 'address' || $orderby == 'shop_name' || $orderby == 'seller_name') {
                    $sellerlisthelper = new SellerListHelper();
                    $all_active_seller = $sellerlisthelper->findAllActiveSellerBySearch($orderby, $key);
                    $this->context->smarty->assign('alph', '0');
                } else {
                    Tools::redirect($viewmorelist_link);
                }
            } else {
                $all_active_seller = $obj_seller->findAllActiveSellerInfoByLimit(false, false, true, true, '');
                $this->context->smarty->assign('alph', '0');
            }
        }
        if ($all_active_seller) {
            $total_active_seller = count($all_active_seller);
            $shop_img = array();
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
            $this->context->smarty->assign('shop_img', $shop_img);
            $this->context->smarty->assign('all_active_seller', $all_active_seller);
        } else {
            $total_active_seller = 0;
        }
        $this->context->smarty->assign('shop_store_link', $shop_store_link);
        $this->context->smarty->assign('viewmorelist_link', $viewmorelist_link);
        $this->context->smarty->assign('total_active_seller', $total_active_seller);
        $this->context->smarty->assign('ajaxsearch_url', $ajaxsearch_link);
        $this->setTemplate('mpallsellerlist.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'mpsellerlist/views/css/sellerlist.css');
        $this->context->controller->addJs(dirname(dirname(dirname(__FILE__))).'/views/js/sellerlist.js');
    }
}
