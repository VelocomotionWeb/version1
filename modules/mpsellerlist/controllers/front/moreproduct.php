<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
class MpSellerListmoreproductModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $id_lang = $this->context->language->id;
        $nextid = Tools::getValue('nextid');
        $orderby = Tools::getValue('orderby');
        $orderway = Tools::getValue('orderway');

        $sellerlisthelper = new SellerListHelper();
        $seller_product_info = $sellerlisthelper->findAllActiveSellerProductOrderBy($nextid, 8, $orderby, $orderway);
        if (!empty($seller_product_info)) {
            foreach ($seller_product_info as $active_pro) {
                $product = new Product($active_pro['main_id_product'], false, $id_lang);
                $product_link = $this->context->link->getProductLink($active_pro['main_id_product']);
                $product_name = $active_pro['product_name'];
                $cover_image_id = Product::getCover($product->id);
                if ($cover_image_id) {
                    $ids = $product->id.'-'.$cover_image_id['id_image'];
                    //$prduct_img_link = "http://".$link->getImageLink($product->link_rewrite,$ids,'home_default');
                    //$prduct_img_link = $this->context->link->getImageLink($product->link_rewrite,$ids,'home_default');
                    $prduct_img_link = Tools::getShopProtocol().$link->getImageLink($product->link_rewrite, $ids, $product->getType());
                } else {
                    $prduct_img_link = _MODULE_DIR_.'mpsellerlist/views/img/defaultproduct.jpg';
                }

                echo "<div class='col-lg-3 col-md-4 col-xs-6 thumb' id=''>
					<a class='thumbnail' href='$product_link'>
					<img class='img-responsive' src='$prduct_img_link' title='$product_name' style='height:240px;'>
					</a>
					<div class='wk_seller_details'>
					<p class='wk_seller_name'>$product_name</p>
					<div><strong>$product->price</strong></div>
					<a href='$product_link' class='btn btn-default btn_product_shop'>View</a>
					</div>
					</div>";
            }
            die;
        } else {
            die('0');
        }
    }
}
